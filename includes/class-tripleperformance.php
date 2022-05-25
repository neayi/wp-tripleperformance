<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://tripleperformance.fr/
 * @since      1.0.0
 *
 * @package    Tripleperformance
 * @subpackage Tripleperformance/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Tripleperformance
 * @subpackage Tripleperformance/includes
 * @author     Bertrand Gorge <bertrand.gorge@neayi.com>
 */
class Tripleperformance {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Tripleperformance_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'TRIPLEPERFORMANCE_VERSION' ) ) {
			$this->version = TRIPLEPERFORMANCE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'tripleperformance';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_import_action();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Tripleperformance_Loader. Orchestrates the hooks of the plugin.
	 * - Tripleperformance_i18n. Defines internationalization functionality.
	 * - Tripleperformance_Admin. Defines all hooks for the admin area.
	 * - Tripleperformance_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tripleperformance-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tripleperformance-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tripleperformance-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tripleperformance-public.php';

		$this->loader = new Tripleperformance_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Tripleperformance_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Tripleperformance_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Tripleperformance_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_submenu' );

		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_setting' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Tripleperformance_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}


	public function my_interval( $schedules ) {
		$schedules['tp_sync_interval'] = array(
			'interval' => 5, // seconds
			'display' => __( 'Triple Performance Sync Interval' )
		);

		return $schedules;
	}

	private function define_import_action() {
		// add a custom interval filter
		$this->loader->add_filter( 'cron_schedules', $this, 'my_interval' );

		$this->loader->add_action( 'tp_syncArticles', $this, 'syncArticles' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Tripleperformance_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}


	/**
	 * Where the magic happens. We fetch the list of pages then we create posts with it.
	 */
	static public function syncArticles()
	{
		$existingvalues = get_option('smwquery');

		if (empty($existingvalues))
			$existingvalues = array();

		if (!isset($existingvalues['selector']))
			$existingvalues['selector'] = '';

		if (!isset($existingvalues['update']))
			$existingvalues['update'] = 1;

		if (empty($existingvalues['selector']))
			return;

		$ask = $existingvalues['selector'] . "|?=Page|?A une photo=photo|?Page_ID=PageId|limit=100"; // TODO: rendre la limite paramétrable

		$parameters = ["action" => "ask", "api_version" => "3", "query" => $ask, "format" => "json"];

		$wikiURL = 'https://wiki.tripleperformance.fr/';
		$url = $wikiURL . "api.php?" . http_build_query($parameters);

		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$output = curl_exec( $ch );
		curl_close( $ch );

		$result = json_decode( $output, true );

		if (empty($result['query']['results']))
			return;

		foreach ($result['query']['results'] as $result)
		{
			$page = reset($result);
			$title = key($result);

			$pagesByType[$title] = ['PageId' => $page['printouts']['PageId'][0]];

			if (!empty($page['printouts']['photo']))
				$pagesByType[$title]['photo'] = $page['printouts']['photo'];
				// "fulltext":"Fichier:Carton.jpg"
				// "fullurl":"//wiki.tripleperformance.fr/wiki/Fichier:Carton.jpg"
		}

		// Make the subsequent calls by chunk of 10 titles:
		$calls = array_chunk($pagesByType, 10, true);

		foreach ($calls as $titles)
		{
			// Now get the page extracts:

			$parameters = [ "prop" => "extracts",
							"explaintext" => true,
							"exintro" => true,
							"exsectionformat" => "wiki",
							"action" => "query",
							"format" => "json",
							"titles" => implode('|', array_keys($titles)),
							"redirects" => true ];

			$url = $wikiURL . "api.php?" . http_build_query($parameters);

			$ch = curl_init( $url );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			$output = curl_exec( $ch );
			curl_close( $ch );

			$extracts = json_decode( $output, true );

			if (empty($extracts['query']['pages']))
				continue;

			foreach ($extracts['query']['pages'] as $pageId => $data)
			{
				foreach ($pagesByType as $title => $pageData)
				{
					if ($pageData['PageId'] == $pageId)
					{
						$pagesByType[$title]['extract'] = $data['extract'];
						break;
					}
				}
			}
		}

		foreach ($pagesByType as $title => $pageData)
		{
			/*
				'ID' (int) The post ID. If equal to something other than 0, the post with that ID will be updated. Default 0.
				'post_author' (int) The ID of the user who added the post. Default is the current user ID.
				'post_date' (string) The date of the post. Default is the current time.
				'post_date_gmt' (string) The date of the post in the GMT timezone. Default is the value of $post_date.
				'post_content' (string) The post content. Default empty.
				'post_content_filtered' (string) The filtered post content. Default empty.
				'post_title' (string) The post title. Default empty.
				'post_excerpt' (string) The post excerpt. Default empty.
				'post_status' (string) The post status. Default 'draft'.
				'post_type' (string) The post type. Default 'post'.
				'comment_status' (string) Whether the post can accept comments. Accepts 'open' or 'closed'. Default is the value of 'default_comment_status' option.
				'ping_status' (string) Whether the post can accept pings. Accepts 'open' or 'closed'. Default is the value of 'default_ping_status' option.
				'post_password' (string) The password to access the post. Default empty.
				'post_name' (string) The post name. Default is the sanitized post title when creating a new post.
				'to_ping' (string) Space or carriage return-separated list of URLs to ping. Default empty.
				'pinged' (string) Space or carriage return-separated list of URLs that have been pinged. Default empty.
				'post_modified' (string) The date when the post was last modified. Default is the current time.
				'post_modified_gmt' (string) The date when the post was last modified in the GMT timezone. Default is the current time.
				'post_parent' (int) Set this for the post it belongs to, if any. Default 0.
				'menu_order' (int) The order the post should be displayed in. Default 0.
				'post_mime_type' (string) The mime type of the post. Default empty.
				'guid' (string) Global Unique ID for referencing the post. Default empty.
				'import_id' (int) The post ID to be used when inserting a new post. If specified, must not match any existing post ID. Default 0.
				'post_category' (int[]) Array of category IDs. Defaults to value of the 'default_category' option.
				'tags_input' (array) Array of tag names, slugs, or IDs. Default empty.
				'tax_input' (array) An array of taxonomy terms keyed by their taxonomy name. If the taxonomy is hierarchical, the term list needs to be either an array of term IDs or a comma-separated string of IDs. If the taxonomy is non-hierarchical, the term list can be an array that contains term names or slugs, or a comma-separated string of names or slugs. This is because, in hierarchical taxonomy, child terms can have the same names with different parent terms, so the only way to connect them is using ID. Default empty.
				'meta_input' (array) Array of post meta values keyed by their post meta key. Default empty.
			*/
			$args = array(
				'meta_query' => array(
					array(
						'key'   => 'wikipageid',
						'value' => $pageData['PageId'],
					)
				)
			);
			$postslist = get_posts( $args );

			$existingPostId = 0;
			if (!empty($postslist))
				$existingPostId = $postslist[0]->ID;

			echo '<pre>';

			if ($existingvalues['update'] == 0 && $existingPostId > 0)
				continue; // Don't update existing posts

			$postData = array(
				'ID'		    => $existingPostId,
				'post_title'    => $title,
				'post_content'  => $pageData['extract'],
				'post_excerpt'  => $pageData['extract'],
				// 'post_date' => ...
				'post_author'   => 1,
				'comment_status' => 'closed',
				'meta_input' => array(
					'wikipageid' => $pageData['PageId']
				),
				//'post_category' => array( 8,39 ),
				'post_status'   => 'publish'
			  );


			  print_r($postData);
			$ret = wp_insert_post($postData, true);

		}
		// $to = 'bertrand.gorge@test.com';
		// $subject = 'The subject ' . date('H:i:s');
		// $body = 'The email body content<br>' . $existingvalues['selector'] . '<br>' . print_r(array_keys($pagesByType), true);
		// $headers = array('Content-Type: text/html; charset=UTF-8');

		// wp_mail( $to, $subject, $body, $headers );
	}
}
