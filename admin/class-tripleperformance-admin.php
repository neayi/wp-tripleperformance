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

		// alternate action : action="<?php menu_page_url( 'triple-performance' ) ? >"
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				// output security fields for the registered setting "wporg_options"
				settings_fields( 'tripleperformance_options' );

				$existingvalues = get_option('smwquery');

				if (empty($existingvalues))
					$existingvalues = array();

				if (!isset($existingvalues['selector']))
					$existingvalues['selector'] = '';

				if (!isset($existingvalues['update']))
					$existingvalues['update'] = 1;

				?>
				<table class="form-table">
					<tr valign="top"><th scope="row">Selecteur</th>
						<td><textarea name="smwquery[selector]"
          							rows="5" cols="100" placeholder="[[Category:Fiches Dephy - Pratiques remarquables]][[A un type de page::Exemple de mise en œuvre]]"><?php echo $existingvalues['selector']; ?></textarea></td>
					</tr>
					<tr valign="top"><th scope="row">Mettre à jour les articles déjà importés</th>
						<td><input name="smwquery[update]" type="checkbox" value="1" <?php checked('1', $existingvalues['update']); ?> /></td>
					</tr>
				</table>
				<?php
				// output save settings button
				submit_button( __( 'Save Settings', 'textdomain' ) );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Get the value from the POST (if any), and deal with it
	 */
	public function options_page_html_submit() {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// get the value from the POST (if any), and deal with it

	}

	public function add_admin_submenu()
	{
		$hookname = add_options_page(
			'Triple Performance',
			'Triple Performance',
			'manage_options',
			'triple-performance',
			array($this, 'options_page_html')
		);

		add_action( 'load-' . $hookname, array($this, 'options_page_html_submit') );
	}


	// Sanitize and validate input. Accepts an array, return a sanitized array.
	public function settings_validate($input) {
		// Our first value is either 0 or 1
		$input['update'] = ( $input['update'] == 1 ? 1 : 0 );

		// Say our second option must be safe text with no HTML tags
		// $input['selector'] =  wp_filter_nohtml_kses($input['selector']);

		return $input;
	}

	public function register_setting()
	{
		// register_setting( string $option_group, string $option_name, array $args = array() )
		register_setting( 'tripleperformance_options', 'smwquery', array($this, 'settings_validate'));

	}

}
