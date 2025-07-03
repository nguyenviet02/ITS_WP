<?php
/**
 * Ultimate_Post_List_Widget widget class
 *
 * @since	1.0.0
 */
 
class Ultimate_Post_List_Widget extends WP_Widget {

	private $plugin_slug;  // identifier of this plugin for WP
	private $plugin_version; // number of current plugin version

	function __construct() {
		$this->plugin_slug				= 'ultimate-post-list';
		$this->plugin_version			= '5.2.7.1';
		
		$widget_ops = array( 'classname' => $this->plugin_slug, 'description' => __( 'List of your site&#8217;s posts as configured with Ultimate Post List.', 'ultimate-post-list' ) );
		parent::__construct( $this->plugin_slug, __( 'Ultimate Post List', 'ultimate-post-list' ), $widget_ops );

		add_action( 'admin_init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_style' ) );
		//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_style' ) );

	}

	/**
	 * Echoes the widget content.
	 *
	 * @since	1.0.0
	 * @access	public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	function widget( $args, $instance ) {
		
		$cache = array();
		if ( ! $this->is_preview() ) {
			$cache = wp_cache_get( $this->plugin_slug, 'widget' );
		}

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args[ 'widget_id' ] ) ) {
			$args[ 'widget_id' ] = $this->id;
		}

		if ( isset( $cache[ $args[ 'widget_id' ] ] ) ) {
			echo $cache[ $args[ 'widget_id' ] ];
			return;
		}

		ob_start();
		extract( $args );

		// get and sanitize values
		$list_id = ( ! empty( $instance[ 'list_id' ] ) )	? absint( $instance[ 'list_id' ] )	: 0;
		
		// show widget only if id: not 0, list: published and settings: available
		if ( ! (
			$list_id 
			and 'publish' == get_post_status( $list_id ) 
			and class_exists( 'Ultimate_Post_List_Public' ) 
			) ) {
			return false;
		}
		
		// set widget title
		$widget_title = ( ! empty( $instance[ 'title' ] ) )	? $instance[ 'title' ] : '';
		$widget_title = apply_filters( 'widget_title', $widget_title, $instance, $this->id_base );       

		// set params for list printer
		$args = array(
			'list_id'		=> $list_id,
			'widget_id'		=> $args[ 'widget_id' ],
			'list_title'	=> $widget_title,
			'before_widget'	=> $before_widget,
			'after_widget'	=> $after_widget,
			'before_title'	=> $before_title,
			'after_title'	=> $after_title,
		);
		
		// print the list
		echo Ultimate_Post_List_Public::get_html( $args );
		
		#printf( '<pre>%s</pre>', var_export( $args, true ) );
		#printf( '<pre>%s</pre>', var_export( $widget_settings[ 'post_password' ], true ) );
		
		if ( ! $this->is_preview() ) {
			$cache[ $args[ 'widget_id' ] ] = ob_get_flush();
			wp_cache_set( $this->plugin_slug, $cache, 'widget' );
		} else {
			ob_end_flush();
		}
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * @since	1.0.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	function update( $new_widget_settings, $old_widget_settings ) {
		$instance = $old_widget_settings;
		// sanitize user input before update
		$instance[ 'title' ] = ( isset( $new_widget_settings[ 'title' ] ) )	? strip_tags( $new_widget_settings[ 'title' ] )	: '';
		$instance[ 'list_id' ] = ( isset( $new_widget_settings[ 'list_id' ] ) )	? absint( $new_widget_settings[ 'list_id' ] )	: 0;
		// more values here....


		// empty widget cache
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions[ $this->plugin_slug ] ) ) {
			delete_option( $this->plugin_slug );
		}

		// return sanitized current widget settings
		return $instance;
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @since	1.0.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 * @return string Default return is 'noform'.
	 */
	function form( $instance ) {
		// get and sanitize values
		$widget_title	= ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ]	: '';
		$list_id = ( isset( $instance[ 'list_id' ] ) )	? absint( $instance[ 'list_id' ] )	: 0;
		// more values here....

		$lists = get_posts( 
			array(
				'posts_per_page'   => -1,
				'orderby'          => 'title',
				'order'            => 'ASC',
				'post_type'        => UPL_POST_TYPE,
				'post_status'      => 'publish',
			)
		);


		// print form in widgets page
		include dirname( dirname( __FILE__ ) ) . '/admin/partials/widget-form.php';

	}
	
	/**
	 * Flushes the WordPress widget cache
	 *
	 * @since	1.0.0
	 * @access public
	 */
	function flush_widget_cache() {
		wp_cache_delete( $this->plugin_slug, 'widget' );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( 'ultimate-post-list', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

}
