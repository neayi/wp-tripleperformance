<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://tripleperformance.fr/
 * @since      1.0.0
 *
 * @package    Tripleperformance
 * @subpackage Tripleperformance/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tripleperformance
 * @subpackage Tripleperformance/admin
 * @author     Bertrand Gorge <bertrand.gorge@neayi.com>
 */
class Tripleperformance_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tripleperformance_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tripleperformance_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tripleperformance-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tripleperformance_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tripleperformance_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tripleperformance-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function options_page_html() {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$existingvalues = get_option('tp_options');

		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post"><?php settings_fields( 'tripleperformance_options' ); ?>
				<table class="form-table">
					<tr valign="top"><th scope="row">URL de la plateforme</th>
						<td><input type="text" name="tp_options[wiki_url]" size="50"
          						   placeholder="https://wiki.tripleperformance.fr/" value="<?php echo htmlspecialchars($existingvalues['wiki_url']); ?>"></td>
					</tr>
					<tr valign="top"><th scope="row">Requête SMW <sup>[<a href="https://www.semantic-mediawiki.org/wiki/Help:Selecting_pages" target="_blank">?</a>]</sup></th>
						<td><textarea name="tp_options[selector]"
          							rows="5" cols="100" placeholder="[[Category:Fiches Dephy - Pratiques remarquables]][[A un type de page::Exemple de mise en œuvre]]"><?php echo $existingvalues['selector']; ?></textarea></td>
					</tr>
					<tr valign="top"><th scope="row">Mettre à jour les articles déjà importés</th>
						<td><input name="tp_options[update]" type="checkbox" value="1" <?php checked('1', $existingvalues['update']); ?> /></td>
					</tr>
					<tr valign="top"><th scope="row">Catégorie des articles importés</th>
						<td><?php wp_dropdown_categories(  ['name' => 'tp_options[category]',
															'selected' => $existingvalues['category'],
															'hide_empty' => 0] ); ?></td>
					</tr>
					<tr valign="top"><th scope="row">Auteur des articles importés</th>
						<td><?php wp_dropdown_users(  ['name' => 'tp_options[author]',
													   'selected' => $existingvalues['author']] ); ?></td>
					</tr>

				</table>
				<?php
				// output save settings button
				submit_button( __( 'Save Settings', 'textdomain' ) );
				?>
			</form>
			<p>NB : L'import des articles se fait toutes les heures.</p>
		</div>
		<?php

		if (!empty($existingvalues['selector']))
		{
			$this->showTemporaryPages($existingvalues['selector'], $existingvalues['wiki_url']);
		}
	}

	/**
	 * Preview what's going to be imported eventually
	 */
	private function showTemporaryPages($selector, $wikiURL)
	{
		$ask = $selector . "|?=Page|?Page_ID=PageId|limit=100|sort=Page_ID|order=desc";

		$parameters = ["action" => "ask", "api_version" => "3", "query" => $ask, "format" => "json"];

		$url = $wikiURL . "api.php?" . http_build_query($parameters);

		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$output = curl_exec( $ch );
		curl_close( $ch );

		$result = json_decode( $output, true );

		if (empty($result['query']['results']))
			return;

		$count = 0;

		$pagesCount = count($result['query']['results']);
		echo "<p>$pagesCount pages ont été trouvées et seront importées :</p>";

		echo "<ul style=\"list-style: disc;margin-left: 2em;\">";
		foreach ($result['query']['results'] as $result)
		{
			$page = reset($result);
			$title = key($result);
			$pageId = $page['printouts']['PageId'][0];

			$pagesByType[$title] = ['PageId' => $pageId, 'title' => $title];

			echo "<li><a href=\"".$wikiURL."index.php?curid=$pageId\" target=\"_blank\">$title</a></li>";

			$count++;

			if ($count > 5)
				break;
		}

		if ($count <= $pagesCount)
			echo "<li>...</li>";

		echo '</ul>';
	}

	public function add_admin_submenu()
	{
		add_options_page(
			'Triple Performance',
			'Triple Performance',
			'manage_options',
			'triple-performance',
			array($this, 'options_page_html')
		);
	}

	/**
	 * Explicitely sanitize each field. Note that if not specified in this function the
	 * field will not be saved at all.
	 *
	 * Accepts an array, returns a sanitized array.
	 */
	public function settings_validate($input)
	{
		$output = array();

		// Our first value is either 0 or 1
		$output['update'] = ( $input['update'] == 1 ? 1 : 0 );

		$urlParts = parse_url($input['wiki_url']);

		if (empty($urlParts['scheme']))
			$urlParts['scheme'] = 'https';
		if (empty($urlParts['host']))
			$urlParts['host'] = 'wiki.tripleperformance.fr';

		$output['wiki_url'] = $urlParts['scheme'] . '://' . $urlParts['host'] . '/';

		// Say our second option must be safe text with no HTML tags
		// $output['selector'] =  wp_filter_nohtml_kses($input['selector']);
		$output['selector'] = $input['selector'];

		$output['category'] = intval($input['category']);

		$output['author'] = intval($input['author']);

		return $output;
	}

	public function register_setting()
	{
		$args = ['type' => 'array',
				 'sanitize_callback' => array($this, 'settings_validate'),
				 'default' => array('update' => 1,
				 					'selector' => '',
									'wiki_url' => 'https://wiki.tripleperformance.fr/',
									'category' => 0,
									'author' => 1)];

		register_setting( 'tripleperformance_options', 'tp_options', $args);
	}

}
