<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.kybernetik-services.com
 * @since      1.0.0
 *
 * @package    Ultimate_Post_List
 * @subpackage Ultimate_Post_List/admin
 */


class Ultimate_Post_List_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The slug of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_slug    The slug of this plugin.
	 */
	private $plugin_slug;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_version    The current version of this plugin.
	 */
	private $plugin_version;

	/**
	 * Part of URL to 'Add New' page for the post type
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var     string
	 */
	private $post_type_new;

	/**
	 * Part of URL to the edit page for the post type
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var     string
	 */
	private $post_type_edit;

	/**
	 * Slug of the help page
	 *
	 * @since    3.3
	 * @access   private
	 * @var      string
	 */
	private $help_page_slug;

	/**
	 * Action name of nonce
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string
	 */
	private $nonce_action_name;
	
	/**
	 * Name of nonce
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string
	 */
	private $nonce_name;
	
	/**
	 * Key name of a list ID
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string
	 */
	private $upl_id_key;
	
	/**
	 * Format of the date
	 *
	 * @since    3.2
	 * @access   private
	 * @var      string
	 */
	private $verbose_date_format;
	

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $plugin_slug       The slug of this plugin.
	 * @param      string    $plugin_version    The version of this plugin.
	 */
	public function __construct( $args ) {

		$this->plugin_name						= $args['name'];
		$this->plugin_slug						= $args['slug'];
		$this->plugin_version					= $args['plugin_version'];
		$this->post_type_new					= 'post-new.php?post_type=' . UPL_POST_TYPE;
		$this->post_type_edit					= 'edit.php?post_type=' . UPL_POST_TYPE;
		$this->nonce_action_name				= 'upl_edd_nonce';
		$this->nonce_name						= 'upl_23198_nonce';
		$this->upl_id_key						= 'upl_id';
		$this->help_page_slug					= 'upl-help-page';

		// not in use, just for the po-editor to display the translation on the plugins overview list
		$foo = __( 'Make up custom-tailored preview lists of the contents easily and place them in widget areas and post contents.', 'ultimate-post-list' );
	}

	/**
	 * Initialize variables, types and more
	 *
	 * @since    1.0.0
	 *
	 */
	public function init() {

		// register post type
		$this->register_post_type();
		
		// get localized date format based on language
		/* translators: 1: Post date, 2: Post time. */
		$text = '%1$s at %2$s';
		$this->verbose_date_format = __( $text );

	}

	/**
	 * Initialize variables, types and more
	 *
	 * @since    4.0.1
	 *
	 */
	public function set_options() {
		// set Ultimate Post List options
		if ( class_exists( 'Ultimate_Post_List_Options' ) ) {
			Ultimate_Post_List_Options::set_options();
		}
	}

	/**
	 * Register the post type for RP lists
	 *
	 * @since    1.0.0
	 *
	 */
	public function register_post_type() {
		register_post_type(
			UPL_POST_TYPE,
			array(
				'label' => __( 'Ultimate Post List', 'ultimate-post-list' ),
				'description' => __( 'Make up custom-tailored preview lists of the website&#8217;s contents easily and place them in widget areas and post contents.', 'ultimate-post-list' ),
				'labels' => array(
					// 'add_new'				=> _x( 'Add New', UPL_POST_TYPE, 'ultimate-post-list'),
					'add_new_item'			=> __( 'Add New List', 'ultimate-post-list' ),
					'all_items'				=> __( 'All Lists', 'ultimate-post-list' ),
					'archives'				=> __( 'Post Lists Archives', 'ultimate-post-list' ),
					'attributes' 			=> __( 'List Attributes', 'ultimate-post-list' ),
					'edit_item'				=> __( 'Edit List', 'ultimate-post-list' ),
					'filter_items_list'		=> __( 'Filter Post Lists', 'ultimate-post-list' ),
					'insert_into_item' 		=> __( 'Insert into list', 'ultimate-post-list' ),
					'items_list'			=> __( 'List of Post Lists', 'ultimate-post-list' ),
					'items_list_navigation'	=> __( 'Post Lists Navigation', 'ultimate-post-list' ),
					'name'					=> __( 'Ultimate Post Lists', 'ultimate-post-list' ),
					'new_item'				=> __( 'New List', 'ultimate-post-list' ),
					'not_found'				=> __( 'No lists found.', 'ultimate-post-list' ),
					'not_found_in_trash'	=> __( 'No lists found in trash.', 'ultimate-post-list' ),
					'search_items'			=> __( 'Search Lists', 'ultimate-post-list' ),
					'singular_name'			=> __( 'Ultimate Post List', 'ultimate-post-list' ),
					'uploaded_to_this_item'	=> __( 'Uploaded to this list', 'ultimate-post-list' ),
					'view_item'				=> __( 'View List', 'ultimate-post-list' ),
					'view_items'			=> __( 'View Lists', 'ultimate-post-list' ),
				),
				'rewrite' => false,
				'query_var' => false,
				'supports' => array( 'title' ),
				'public' => true,
				'exclude_from_search' => true,
				'publicly_queryable' => false,
				'show_in_nav_menus' => false,
				'show_in_menu' => false,
				'show_in_admin_bar' => false,
				'register_meta_box_cb' => array( $this, 'add_metaboxes' ),
			)
		);
	}

	/**
	 * Register the metaboxes for the post edit page
	 *
	 * @since    1.0.0
	 *
	 */
	public function add_metaboxes ( $post ) {

		if ( ! class_exists( 'Ultimate_Post_List_Options' ) ) {
			echo '<p>Missing class Ultimate_Post_List_Options.</p>';
			return;
		}

		// set prefix for UPLP boxes
		$prefix = 'upl-';
		
		// get current screen once
		$current_screen = get_current_screen();

		// add metabox containing the list shortcode
		add_meta_box(
			$prefix . 'shortcode', // metabox id
			__( 'Shortcode', 'ultimate-post-list' ), // metabox title
			array( $this, 'print_shortcode' ), // metabox callback function to print the content
			$current_screen, // current screen
			'side', // metabox context
			'high' // metabox priority
			// no callback arguments
		);
		
		// add metabox containing helpful information
		add_meta_box(
			$prefix . 'infos', // metabox id
			__( 'Helpful Links', 'ultimate-post-list' ), // metabox title
			array( $this, 'print_infos' ), // metabox callback function to print the content
			$current_screen, // current screen
			'side', // metabox context
			'low' // metabox priority
			// no callback arguments
		);
		
		// add metabox containing the introduction
		$text = 'Welcome';
		add_meta_box(
			$prefix . 'intro', // metabox id
			_x( $text, 'Howdy' ), // metabox title
			array( $this, 'print_intro' ), // metabox callback function to print the content
			$current_screen, // current screen
			'normal', // metabox context
			'default' // metabox priority
			// no callback arguments
		);
		
		// set option values for the current list
		Ultimate_Post_List_Options::set_rendered_options( $post->ID );
		
		// add metaboxes containing the list options
		if ( empty( Ultimate_Post_List_Options::$options_set ) ) {
			Ultimate_Post_List_Options::set_options();
		}
		foreach( Ultimate_Post_List_Options::$options_set as $key => $set ) {
			/*
			add_meta_box() parameters:
			1: string to use as 'id' attribute for the metabox
			2: title of the metabox
			3: function that fills the box with the desired content
			4: null, // screen or screens on which to show the box, default: current screen
			5: 'normal', // context within the screen where the boxes should display ('normal', 'side', 'advanced')
			6: 'default', // priority within the context where the boxes should show ('high', 'low', 'default')
			7: null // WP_Post object? doc says: array of arguments to the callback function
			*/

			add_meta_box(
				$prefix . $key, // metabox id
				$set[ 'title' ], // metabox title
				array( $this, 'print_metabox' ), // metabox callback function to print the content
				$current_screen, // current screen
				'normal', // metabox context
				'default', // metabox priority
				array( 'name' => $key ) // callback arguments
			);
		}
		
		// remove 3rd-party boxes
		if ( UPL_POST_TYPE == $current_screen->id ) {
			global $wp_meta_boxes;
			foreach ( $wp_meta_boxes[ UPL_POST_TYPE ] as $context => $priorities ) {
				foreach ( $priorities as $priority => $boxes ) {
					foreach ( $boxes as $id => $args ) {
						// skip necessary boxes
						if ( 'submitdiv' == $id or 'slugdiv' == $id ) {
							continue;
						}
						// remove non-UPLP box
						if ( false === strpos( $id, $prefix ) ) {
							//remove_meta_box( $id, $current_screen, $context );
							//unset( $wp_meta_boxes[ UPL_POST_TYPE ][ $context ][ $priority ][ $id ] );
							$wp_meta_boxes[ UPL_POST_TYPE ][ $context ][ $priority ][ $id ] = false;
						}
					} // foreach (id)
				} // foreach (priority)
			} // foreach (context)
		} // if (upl == current screen)
	}

	/* ====================================
	 * Methods for displaying admin content
	 * ==================================== */
	 
	/**
	 * Print a metaboxe on the post edit page
	 *
	 * @since    1.0.0
	 *
	 */
	public function print_metabox ( $post, $vars ) {
		Ultimate_Post_List_Options::print_rendered_options( $vars['args']['name'] );
	}

	/**
	 * Print the introduction metaboxe on the post edit page
	 *
	 * @since    1.0.0
	 *
	 */
	public function print_intro () {
		$text = 'Widgets';
		$widgets_link = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'widgets.php^' ) ), esc_html__( $text ) ); 
		printf( "<h3>%s</h3>\n", esc_html__( 'How to use the list as a widget', 'ultimate-post-list' ) );
		printf( "<p>%s</p>\n", sprintf( esc_html__( 'Go to the page %s, move the widget "%s" into the desired area and select the list you want to see in the website.', 'ultimate-post-list' ), $widgets_link, esc_html__( 'Ultimate Post List', 'ultimate-post-list' ) ) );
		printf( "<h3>%s</h3>\n", esc_html__( 'How to use the shortcode', 'ultimate-post-list' ) );
		printf( "<p>%s</p>\n", esc_html__( 'Copy the shortcode in the Shortcode box and insert it at the desired place in the content.', 'ultimate-post-list' ) );
		printf(
			"<p>%s %s</p>\n",
			esc_html__( 'You can add some attributes to overwrite the respective settings of the list.', 'ultimate-post-list' ),
			sprintf(
				__( 'Read more about allowed shortcode attributes on page %s.', 'ultimate-post-list' ), 
				$this->get_help_page_link()
			) // sentence
		); // print paragraph
	}

	/**
	 * Print the shortcode metabox on the post edit page
	 *
	 * @since    1.0.0
	 *
	 */
	public function print_shortcode ( $post ) {
		printf(
			'<textarea id="uplshortcode" readonly>[%s id="%d"]</textarea><button type="button" class="button" onclick="upl_copy()">%s</button>',
			UPL_SHORTCODE_NAME,
			$post->ID,
			esc_html__( 'Copy shortcode to clipboard', 'ultimate-post-list' ) 
		);
		printf(
			"<p>%s</p>\n",
			sprintf(
				__( 'Read more about allowed shortcode attributes on page %s.', 'ultimate-post-list' ), 
				$this->get_help_page_link()
			) // sentence
		); // print paragraph
	}

	/**
	 * Print the informations metabox on the post edit page
	 *
	 * @since    1.0.0
	 *
	 */
	public function print_infos () {
		printf( "<h3>%s</h3>\n", esc_html__( 'Do you need more options?', 'ultimate-post-list' ) );
		printf( "<p><a href=\"https://www.kybernetik-services.com/shop/wordpress/plugin/ultimate-post-list-pro/?utm_source=wordpress_org&utm_medium=plugin&utm_campaign=ultimate-post-list&utm_content=update-notice\" target=\"_blank\">%s</a></p>\n", esc_html__( 'Get the Pro version.', 'ultimate-post-list' ) );

		$link = sprintf( '<a href="https://wordpress.org/support/view/plugin-reviews/ultimate-post-list" target=\"_blank\">%s</a>', esc_html__( 'Reviews', 'ultimate-post-list' ) );
		printf( "<h3>%s</h3>\n", esc_html__( 'Do you like the plugin?', 'ultimate-post-list' ) );
		printf( "<p>%s</p>\n", sprintf( esc_html__( 'Rate it on %s.', 'ultimate-post-list' ), $link ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook_suffix ) {

		// quit if $hook_suffix and current screen are not appropriate for the list edit page
		if ( UPL_POST_TYPE != get_current_screen()->post_type ) {
			return;
		}

		// stylesheets for the admin pages
		wp_enqueue_style( $this->plugin_slug, UPL_URL . '/admin/css/ultimate-post-list-admin.css', array(), $this->plugin_version, 'all' );
		
		// stylesheets for the color pickers
		wp_enqueue_style( 'wp-color-picker' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook_suffix ) {

		// quit if $hook_suffix and current screen are not appropriate for the list edit page
		if ( UPL_POST_TYPE != get_current_screen()->post_type ) {
			return;
		}

		// scripts for the admin pages
		wp_enqueue_script( $this->plugin_slug, UPL_URL . 'admin/js/ultimate-post-list-admin.min.js', array( 'jquery' ), $this->plugin_version, false );

		// scripts for the color pickers
		wp_enqueue_script( 'wp-color-picker' );

		// translations for admin.js
		$translations = array(
			'success' => __( 'The shortcode is now in the clipboard.', 'ultimate-post-list' ),
		);
		wp_localize_script( $this->plugin_slug, 'upl_i18n', $translations );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_plugin_admin_menu() {
		
		global $_wp_last_object_menu;

		$_wp_last_object_menu++;
		
		// Add a new menu item in the WP backend
		$menu_title = __( 'Ultimate Post List', 'ultimate-post-list' );
		// Add 'All Lists' sub page
		$submenu_1_title = __( 'All Lists', 'ultimate-post-list' );
		$page_title = sprintf( '%s: %s', $menu_title, $submenu_1_title );

		add_menu_page( 
			$page_title, // page title
			$menu_title, // menu title
			'manage_options', // capatibility
			$this->post_type_edit, // menu slug
			'', // callable function
			'dashicons-list-view', // icon url
			$_wp_last_object_menu // position
		);

		// Give first sub level menu link a different label than the top level menu link 
		// by calling the add_submenu_page function the first time with the parent_slug 
		// and menu_slug as same values
		add_submenu_page( 
			$this->post_type_edit, // parent slug
			sprintf( '%s: %s', $menu_title, $submenu_1_title ), // page title
			$submenu_1_title, // menu title
			'manage_options', // capatibility
			$this->post_type_edit
		);
		
		// Add 'Add New' sub page
		$text = 'Add New';
		$submenu_2_title = _x( $text, 'post' );

		add_submenu_page(
			$this->post_type_edit, // parent slug
			sprintf( '%s: %s', $menu_title, $submenu_2_title ), // page title
			$submenu_2_title, // menu title
			'manage_options', // capatibility
			$this->post_type_new
		);

		$text = 'Help';
		$submenu_3_title = __( $text );
		
		add_submenu_page( 
			$this->post_type_edit, // parent slug
			sprintf( '%s: %s', $menu_title, $submenu_3_title ), // page title
			$submenu_3_title, // menu title
			'manage_options', // capatibility
			$this->help_page_slug, // menu slug
			array( $this, 'print_help_page' ) // callable function
		);
		
	}

	/**
	 * Add action link to the plugins page
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_action_links( $links ) {
		$text = 'Help';
		return array_merge(
			array(
				'activation' => sprintf( '<a href="%s">%s</a>', menu_page_url( $this->help_page_slug, false ), esc_html__( $text ) )
			),
			$links
		);

	}

	/**
	 * Besides 'Title' and 'Date' show more data in the post list table
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_posts_list_columns ( $columns ) {

		// set columns to add
		$headers = array(
			'cb'			=> '<input type="checkbox" />',
			'title'			=> 'Title',
			'shortcode'		=> 'Shortcode',
			'author'		=> 'Author',
			'date'			=> 'Date',
			'last-modified'	=> 'Last Modified',
		);

		foreach ( $headers as $key => $label ) {
			switch ( $key ) {
				// add column, take available column, remove existing column from origin
				case 'cb':
					if ( isset( $columns[ $key ] ) ) {
						$new_columns[ $key ] = $columns[ $key ];
						unset( $columns[ $key ] );
					} else {
						$new_columns[ $key ] = $label;
					}
					break;
				// add column, take available column, remove existing column from origin, use translated label
				case 'title':
				case 'date':
					if ( isset( $columns[ $key ] ) ) {
						$new_columns[ $key ] = $columns[ $key ];
						unset( $columns[ $key ] );
					} else {
						$new_columns[ $key ] = __( $label );
					}
					break;
				case 'shortcode':
					$new_columns[ $key ] = __( 'Shortcode', 'ultimate-post-list' );
					break;
				// add column, use translated label
				default:
					$new_columns[ $key ] = __( $label );
			}
		}

		// if no original column left
		if ( empty( $columns ) ) {
			// return new columns
			return $new_columns;
		} else {
			// return remaining original columns appended at new columns
			return array_merge( $new_columns, $columns );
		}
	}

	/**
	 * Register sortable columns
	 *
	 * @since    3.2
	 * @access   public
	 */
	public function register_sortable_columns( $columns ) {
		// make column "Last Modified" sortable
		$columns[ 'last-modified' ] = 'modified';
		// make column "Author" sortable
		$columns[ 'author' ] = 'author';
		// return new columns set
	 	return $columns;
	}
	
	/**
	 * Build content for added column cells in post list table
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function print_posts_list_content ( $column_name, $post_id ) {
		switch ( $column_name ) {
			case 'shortcode':
				printf( '[%s id="%d"]', UPL_SHORTCODE_NAME, $post_id );
				break;
			case 'last-modified':
				$date_format = 'Y/m/d';
				$time_format = 'g:i a';
				printf(
					$this->verbose_date_format,
					get_the_modified_date( __( $date_format ), $post_id ),
					get_the_modified_date( __( $time_format ), $post_id )
				);
				break;
		}
	}

	/**
	 * Print a message about the location of the plugin in the WP backend
	 * 
	 * @since    1.0.0
	 * @access   public
	 */
	public function display_activation_message () {

		$text = esc_html__( 'Ultimate Post List', 'ultimate-post-list' );
		
		// set links
		$link_1 = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'edit.php?post_type=' . UPL_POST_TYPE ) ),
			$text
		);
		$link_2 = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( $this->post_type_new ) ),
			esc_html__( 'Create a new list.', 'ultimate-post-list' )
		);
		
		// set whole message
		printf(
			'<div class="updated notice is-dismissible"><p>%s %s %s</p></div>',
			sprintf( 
				esc_html__( 'Welcome to %s! You can find the plugin at %s.', 'ultimate-post-list' ),
				$text,
				$link_1
			),
			$link_2,
			sprintf( 
				esc_html__( 'A documentation is available on the page %s.', 'ultimate-post-list' ),
				$this->get_help_page_link()
			)
		);
	}

	/**
	 * Print the help page
	 *
	 * @since    3.3
	 * @access   public
	 */
	public function print_help_page() {

		// print page
		include_once( UPL_ROOT . 'admin/partials/page-overview.php' );

	}

	/**
	 * Build the link to the help page
	 *
	 * @since    3.3
	 * @access   private
	 */
	private function get_help_page_link() {
		$text = 'Help';
		return sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url(
				sprintf(
					'edit.php?post_type=%s&page=%s',
					UPL_POST_TYPE,
					$this->help_page_slug
				) // link URL parameters
			) ), // link URL
			esc_html__( $text ) // link text
		); // link element
	}

	/* ===================================
	 * Methods for managin RP list options
	 * =================================== */
	 
	/**
	 * Look for available RP list options, sanitize them
	 * and save them into the database
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function save_post_settings( $post_id, $post ) {
		
		// Store data in post meta table if present in post data
		if ( isset( $_POST[ UPL_OPTION_NAME ] ) 
		and is_array( $_POST[ UPL_OPTION_NAME ] ) 
		and class_exists( 'Ultimate_Post_List_Options' ) ) {
			update_post_meta( $post_id, UPL_OPTION_NAME, Ultimate_Post_List_Options::sanitize_options( $_POST[ UPL_OPTION_NAME ] ) );
		}
		
		// delete CSS file due to layout changes to let the widget generate a new one automatically
		if ( file_exists( UPL_CSS_FILE_PATH ) ) {
			// remove the file
			unlink( UPL_CSS_FILE_PATH );
		}
		
	}

	/* ============================
	 * Methods for cloning lists
	 * ============================ */
	 
	/**
	 *
	 * Display 'Copy' action link on UPL list page
	 *
	 * @since    3.0.0
	 * @access   public
	 */
	public function print_clone_links ( $actions, $post ) {
		// if Ultimate Post List list overview and user is allowed to edit posts
		if ( $post->post_type == UPL_POST_TYPE and current_user_can( 'edit_post', $post->ID ) ) {
			$text = 'Copy';
			// add action link with current post ID
			$actions[ 'uplp-clone' ] = sprintf(
				// template to fill with ...
				'<a href="%s" title="%s">%s</a>',
				// ... the link address
				wp_nonce_url(
					admin_url( 
						sprintf(
							'admin.php?action=%s&%s=%d',
							UPL_CLONE_ACTION_NAME,
							$this->upl_id_key,	
							$post->ID 
						)
					),
					$this->nonce_action_name . $post->ID,
					$this->nonce_name
				),
				// ... the link title
				esc_attr(
					sprintf(
						__( 'Duplicate &#8220;%s&#8221;', 'ultimate-post-list' ),
						$post->post_title
					)
				),
				// ... the link text
				esc_html__( $text ) 
			);
		}
		// return extended actions array
		return $actions;
	}

	/**
	 *
	 * Display message on admin page about successful cloning of a list
	 *
	 * @since    3.0.0
	 * @access   public
	 */
	public function print_clone_admin_notice () {
		$list_id = absint( $_GET[ 'list_id' ] );
		$post = get_post( $list_id );
		if ( $post ) {
			$text = 'Edit';
			printf(
				'<div class="updated notice is-dismissible"><p>%s %s</p></div>',
				sprintf(
					esc_html__( 'List %s successfully created.', 'ultimate-post-list' ),
					'<em>' . esc_html( $post->post_title ) . '</em>'
				),
				sprintf(
					'<a href="%s">%s</a>',
					esc_url( get_edit_post_link( $list_id ) ),
					esc_html__( $text )
				)
			);
		} else {
			$text = 'You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?';
			printf(
				'<div class="error notice is-dismissible"><p>%s</p></div>',
				esc_html__( $text )
			);
		}
	}
	
	/**
	 *
	 * Clone the list determined by ID
	 *
	 * @since    3.0.0
	 * @access   public
	 */
	public function clone_list () {
		$text = 'WordPress &rsaquo; Error';
		// check existence of list id
		if( empty( $_GET[ $this->upl_id_key ] ) ) {
			wp_die( 
				// message in browser viewport
				esc_html__( 'Ultimate Post List can not copy a list. No list to duplicate has been supplied.', 'ultimate-post-list' ),
				// title in title tag
				esc_html__( $text ),
				// arguments: display a back link
				array( 'back_link' => true )
			);
		}
		
		// check existence of nonce
		if( empty( $_GET[ $this->nonce_name ] ) ) {
			wp_die( 
				// message in browser viewport
				sprintf( '<p>%s</p>', esc_html__( 'Ultimate Post List can not copy a list. The security token is missing.', 'ultimate-post-list' ) ),
				// title in title tag
				esc_html__( $text ),
				// arguments: display a back link
				array( 'back_link' => true )
			);
		}
	
		// check correct call
		wp_verify_nonce( $_GET[ $this->nonce_name ], $this->nonce_action_name . $_GET[ $this->upl_id_key ] );
		
		// get original list
		$list_id = (int) $_GET[ $this->upl_id_key ];
		$list_object = get_post( $list_id );
		
		if( ! $list_object ) {
			wp_die( 
				// message in browser viewport
				sprintf( '<p>%s</p>', esc_html__( 'Ultimate Post List can not copy a list. There is no such list.', 'ultimate-post-list' ) ),
				// title in title tag
				esc_html__( $text ),
				// arguments: display a back link
				array( 'back_link' => true )
			);
		}
			
		// insert new list into the database
		$new_list_id = wp_insert_post( 
			array(
			  'post_title'		=> sprintf( '%s %s', $list_object->post_title, __( '- Copy', 'ultimate-post-list' ) ),
			  'post_content'	=> $list_object->post_content,
			  'post_type'		=> $list_object->post_type,
			  'post_status'		=> 'draft',
			  'post_author'		=> $list_object->post_author,
			)
		);

		if( ! $new_list_id ) {
			if ( is_wp_error( $new_list_id ) ) {
				wp_die( 
					// message in browser viewport
					sprintf( '<p>%s</p>', sprintf ( esc_html__( 'Ultimate Post List can not copy a list. Reason: %s', 'ultimate-post-list' ), $new_list_id->get_error_message() ) ),
					// title in title tag
					esc_html__( $text ),
					// arguments: display a back link
					array( 'back_link' => true )
				);
			} else {
				wp_die( 
					// message in browser viewport
					sprintf( '<p>%s</p>', esc_html__( 'Ultimate Post List can not copy a list. The new list could not be created.', 'ultimate-post-list' ) ),
					// title in title tag
					esc_html__( $text ),
					// arguments: display a back link
					array( 'back_link' => true )
				);
			}
		}
		
		// create meta data of the list
		$list_meta_data = get_post_meta( $list_id );
		if( ! $list_meta_data ) {
			wp_die( 
				// message in browser viewport
				sprintf( '<p>%s</p>', esc_html__( 'Ultimate Post List can not copy a list. There is no meta data of the list.', 'ultimate-post-list' ) ),
				// title in title tag
				esc_html__( $text ),
				// arguments: display a back link
				array( 'back_link' => true )
			);
		}
		// loop over returned metadata and assign them to the new list
		foreach( $list_meta_data as $meta_data => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $meta_value => $meta_text ) {
					if ( is_serialized( $meta_text ) ) {
						update_post_meta( $new_list_id, $meta_data,  unserialize( $meta_text ) );
					} else {
						update_post_meta( $new_list_id, $meta_data,  $meta_text );
					}
				}
			} else {
				update_post_meta( $new_list_id, $meta_data, $value );
			}
		}

		// mark task as done
		set_transient( UPL_TRANSIENT_LIST_CLONED, '1', 60 );

		// Redirect to where the function was called on the same server
		wp_safe_redirect( add_query_arg( array( 'upl_list_cloned' => 1, 'list_id' => $new_list_id ), wp_get_referer() ) );
		exit();
	}

}
