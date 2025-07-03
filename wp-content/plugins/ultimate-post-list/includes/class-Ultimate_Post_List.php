<?php

/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Ultimate_Post_List
 * @subpackage Ultimate_Post_List/includes
 * @author     Kybernetik Services <wordpress@kybernetik.com.de>
 */
class Ultimate_Post_List {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ultimate_Post_List_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	 * The slug of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_slug    The slug of this plugin.
	 */
	protected $plugin_slug;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_version    The current version of the plugin.
	 */
	protected $plugin_version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = UPL_NAME;
		$this->plugin_slug = sanitize_title( $this->plugin_name );
		$this->plugin_version = UPL_VERSION;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ultimate_Post_List_Loader. Orchestrates the hooks of the plugin.
	 * - Ultimate_Post_List_i18n. Defines internationalization functionality.
	 * - Ultimate_Post_List_Admin. Defines all hooks for the admin area.
	 * - Ultimate_Post_List_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

        /**
         * Register widget on init
         *
         * @since	1.0.0
         * @access	public
         */
        function register_ultimate_post_list_widget () {
            register_widget( 'Ultimate_Post_List_Widget' );
        }
        add_action( 'widgets_init', 'register_ultimate_post_list_widget', 1 );

        // initiate loader instance
		$this->loader = new Ultimate_Post_List_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ultimate_Post_List_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ultimate_Post_List_i18n();
		#$plugin_i18n->set_domain( $this->get_plugin_slug() );

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

		$plugin_admin = new Ultimate_Post_List_Admin( array(
			'name' => $this->plugin_name, 
			'slug' => $this->plugin_slug, 
			'plugin_version' => $this->plugin_version,
			)
		);

		// load javascripts and stylesheets
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// add the options page and menu item
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );

		// Add an action link pointing to the options page.
		$this->loader->add_filter( 'plugin_action_links_' . UPL_ROOT_FILE, $plugin_admin, 'add_action_links' );

		// initialize, register post type
		$this->loader->add_action( 'init', $plugin_admin, 'init' );

		// load RP list data when a post is saved
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_post_settings', 10, 2 );
		
		// add some columns in the post list table for the given post type
		$this->loader->add_filter( 'manage_' . UPL_POST_TYPE . '_posts_columns', $plugin_admin, 'add_posts_list_columns' );

		// make columns sortable
		$this->loader->add_filter( 'manage_edit-' . UPL_POST_TYPE . '_sortable_columns', $plugin_admin, 'register_sortable_columns' );

		// fill table cells with content
		$this->loader->add_action( 'manage_' . UPL_POST_TYPE . '_posts_custom_column', $plugin_admin, 'print_posts_list_content', 10, 2 );

		// hook on displaying a message after plugin activation (single or multisite activation)
		if ( isset( $_GET[ 'activate' ] ) or isset( $_GET[ 'activate-multi' ] ) ) {
			if ( false !== get_transient( UPL_TRANSIENT_PLUGIN_ACTIVATED ) ) {
				$this->loader->add_action( 'admin_notices', $plugin_admin, 'display_activation_message' );
				delete_transient( UPL_TRANSIENT_PLUGIN_ACTIVATED );
			}
		}
		
		// display 'clone' links in list page
		$this->loader->add_action( 'post_row_actions', $plugin_admin, 'print_clone_links', 10, 2 );
		$this->loader->add_action( 'admin_action_' . UPL_CLONE_ACTION_NAME, $plugin_admin, 'clone_list', 10, 2 );
		if ( isset( $_GET[ 'upl_list_cloned' ] ) and '1' == $_GET[ 'upl_list_cloned' ] ) {
			if ( false !== get_transient( UPL_TRANSIENT_LIST_CLONED ) ) {
				$this->loader->add_action( 'admin_notices', $plugin_admin, 'print_clone_admin_notice' );
				delete_transient( UPL_TRANSIENT_LIST_CLONED );
			}
		}

		// prevent plugin "WordPress Editorial calendar" to show "Calendar" subpage in UPL menu
		add_action( 'edcal_show_calendar_' . UPL_POST_TYPE, '__return_false' );
				
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ultimate_Post_List_Public( $this->get_plugin_name(), $this->plugin_slug, $this->get_plugin_version() );

		// load javascripts and stylesheets
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		// call ajax functions for loading more list items
		$this->loader->add_action( 'wp_ajax_nopriv_upl_ajax_load_more', $plugin_public, 'upl_ajax_load_more' ); // for not logged in users
		$this->loader->add_action( 'wp_ajax_upl_ajax_load_more', $plugin_public, 'upl_ajax_load_more' ); // for logged in users

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
	 * The slug of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The slug of the plugin.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ultimate_Post_List_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The plugin_version number of the plugin.
	 */
	public function get_plugin_version() {
		return $this->plugin_version;
	}

}
