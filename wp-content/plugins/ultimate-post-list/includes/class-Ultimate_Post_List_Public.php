<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link	   https://www.kybernetik-services.com
 * @since	  1.0.0
 *
 * @package	Ultimate_Post_List
 * @subpackage Ultimate_Post_List/public
 * @author	 Kybernetik Services <wordpress@kybernetik.com.de>
 */
class Ultimate_Post_List_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var	  string	$plugin_name	The ID of this plugin.
	 */
	private static $plugin_name;

	/**
	 * The slug of this plugin.
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var	  string	$plugin_slug	The slug of this plugin.
	 */
	private static $plugin_slug;

	/**
	 * The version of this plugin.
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var	  string	$plugin_version	The current version of this plugin.
	 */
	private static $plugin_version;

	/**
	 * The custom width of the thumbnail
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var	  integer	$default_thumb_width	The custom width of the thumbnail
	 */
	private static $default_thumb_width;

	/**
	 * The custom height of the thumbnail
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var	  integer	$default_thumb_height	custom height of the thumbnail
	 */
	private static $default_thumb_height;

	/**
	 * The HTML code of the default thumbnail element
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var	  string	$default_thumbnail_html	The HTML code of the default thumbnail element
	 */
	private static $default_thumbnail_html;

	/**
	 * The indicator of the HTML element containing the list
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var	  string	$list_indicator	The indicator of the HTML element containing the list
	 */
	private static $list_indicator;

	/**
	 * The offset of a list
	 *
	 * @since	5.0.0
	 * @access   private
	 * @var	  integer	$post_offset	The offset of a list
	 */
	private static $post_offset;

	/**
	 * The displayed number of entries in a list 
	 *
	 * @since	5.0.0
	 * @access   private
	 * @var	  integer	$number_posts	The displayed number of entries in a list
	 */
	private static $number_posts;

	/**
	 * Host name of the current site
	 *
	 * @since     5.0.0
	 *
	 * @var      string
	 */
	private static $home_domain = null;
	
	/**
	 * HTTP type of the current site ('http' or 'https')
	 *
	 * @since     5.0.0
	 *
	 * @var      string
	 */
	private static $home_protocol = null;
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	1.0.0
	 * @param	  string	$plugin_name	   The name of this plugin.
	 * @param	  string	$plugin_slug	   The slug of this plugin.
	 * @param	  string	$plugin_version	The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_slug, $plugin_version ) {

		// set class properties
		self::$plugin_name = $plugin_name;
		self::$plugin_slug = $plugin_slug;
		self::$plugin_version = $plugin_version;
		
		self::$default_thumb_width		= absint( round( get_option( 'thumbnail_size_w', 110 ) / 2 ) );
		self::$default_thumb_height 	= absint( round( get_option( 'thumbnail_size_h', 110 ) / 2 ) );
		self::$list_indicator 			= 'upl-list';
		self::$post_offset	 			= 0;
		self::$number_posts	 			= (int) get_option( 'posts_per_page', 10 );

		// Domain name and protocol of WP site
		$parsed_url = parse_url( home_url() );
		self::$home_domain = $parsed_url[ 'host' ];
		self::$home_protocol = $parsed_url[ 'scheme' ];
		unset( $parsed_url );

		// register the shortcode handler
		add_shortcode( UPL_SHORTCODE_NAME, array( __CLASS__, 'upl_shortcode_handler' ) );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since	1.0.0
	 */
	public function enqueue_styles() {

		$is_available = true;
		
		// make sure the css file exists; if not available: generate it
		if ( ! file_exists( UPL_CSS_FILE_PATH ) ) {
			// make the file
			$is_available = self::make_css_file();
		}
		
		// enqueue the style if there is a file
		if ( $is_available ) {
			wp_enqueue_style(
				self::$plugin_slug . '-public-style',
				UPL_CSS_FILE_URL,
				array(),
				self::$plugin_version,
				'all' 
			);
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since	1.0.0
	 */
	public function enqueue_scripts() {

		// load JS script
		wp_enqueue_script(
			self::$plugin_slug,
            UPL_URL . 'public/js/ultimate-post-list-public.min.js',
			array( 'jquery' ),
			self::$plugin_version,
			false 
		);

		// load values for placeholders in JS script
		wp_localize_script(
			self::$plugin_slug,
			'upl_vars',
			array(
				'upl_nonce'	=> wp_create_nonce( 'upl-nonce' ),
				'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
			) 
		);

	}

	/**
	 * Print the post list if called by the shortcode
	 *
	 * @since	1.0.0
	 */
	public static function upl_shortcode_handler( $user_atts ) {
		
		// initialize shortcode attributes
		$valid_atts = array(
			'id' => 0,
			'list_title' => '',
			'included_categories' => '',
			//'excluded_categories' => '',
		);
	
		// set defaults for missing attributes
		$merged_atts = shortcode_atts( $valid_atts, $user_atts );
		
		// sanitize list id
		$list_id = absint( $merged_atts[ 'id' ] );
		
		// quit if id is 0 or list not published
		if ( ! ( $list_id and 'publish' == get_post_status( $list_id ) ) ) {
			return;
		}
		
		// get settings
		$list_settings = self::get_stored_settings( $list_id );
		
		// set params for list printer
		$args = array(
			'list_id'		=> $list_id,
			'before_widget'	=> '',
			'after_widget'	=> '',
			'before_title'	=> '<' . $list_settings[ 'list_title_element' ][ 0 ] . '>',
			'after_title'	=> '</' . $list_settings[ 'list_title_element' ][ 0 ] . '>',
		);

		// if set by user add sanitize attributes to arguments list
		foreach ( array_keys( $valid_atts ) as $key ) {
			if ( isset( $user_atts[ $key ] ) ) {
				$args = self::sanitize_shortcode_atts( $merged_atts, $args, $key );
			}
		}
		
		// print the list
		return self::get_html( $args );
		
	}

	/**
	 * Append further list items to referencing list
	 *
	 * @since	4.0
	 */
	public function upl_ajax_load_more() {
		// check if request is secure
		if ( ! ( isset( $_POST[ 'upl_nonce' ] ) and wp_verify_nonce( $_POST[ 'upl_nonce' ], 'upl-nonce' ) ) ) {
			// quit if security check not passed
			$text = 'Sorry, you are not allowed to do that.';
			die( sprintf( '<li>%s</li>', esc_html( __( $text ) ) ) );
		}
		
		// check if list id is provided
		$list_id = 0;
		if ( isset( $_POST[ 'list_id' ][ 0 ] ) ) {
			$list_id = absint( $_POST[ 'list_id' ][ 0 ] );
		} else {
			// quit if list id is not provided
			$text = 'No data supplied.';
			die( sprintf( '<li>%s</li>', esc_html( __( $text ) ) ) );
		}
		
		// check if any number_items is given, else use 0 as default
		$number_of_items = 0;
		if ( isset( $_POST[ 'number_items' ] ) ) {
			$number_of_items = absint( $_POST[ 'number_items' ] );
		}
		
		// print the list
		$args = array(
			'list_id'	=> $list_id,
			'number_items'	=> $number_of_items,
		);
		echo self::get_html( $args, false );
		
		// quit WP without any message
		die();
	}

	/**
	 * Set the arguments for the list query
	 *
	 * @since	1.0.0
	 */
	public static function get_query_args( $list_settings ) {
		

		// standard params
		$query_args = array(
			'no_found_rows'		=> true, // improves performance by omitting counting of found rows
			'offset'			=> $list_settings[ 'offset_posts' ],
			'order'				=> $list_settings[ 'posts_order_direction' ][ 0 ],
			'orderby'			=> $list_settings[ 'posts_order_by' ][ 0 ],
			'posts_per_page'	=> $list_settings[ 'number_posts' ],
            'post_type'         => $list_settings[ 'post_type' ]
		);
		
		/* 
		 * if selected set filters for 
		 * categories, tags and authors
		 */
		$tax_filters = array(
			'included_categories'	=> 'category__in',
			//'excluded_categories'	=> 'category__not_in',
		);
		// set operator for taxonomies with custom values
		foreach ( $tax_filters as $option_name => $filter_name ) {
			if ( isset( $list_settings[ $option_name ] ) and ! in_array( 0, $list_settings[ $option_name ] ) ) {
				$query_args[ $filter_name ] = $list_settings[ $option_name ];
			}
		}
		// change operator if AND is desired for categories
		if ( isset( $query_args[ 'category__in' ] ) and isset( $list_settings[ 'included_all_cats' ] ) and 1 == $list_settings[ 'included_all_cats' ] ) {
				$query_args[ 'category__and' ] = $query_args[ 'category__in' ];
				unset( $query_args[ 'category__in' ] );
		}
		
		// default: published and private posts (necessary to set due to wrong Ajax results)
		$query_args[ 'post_status' ] = array( 'publish', 'private' );
		
		// if set put sticky posts at top of list
		if ( $list_settings[ 'show_sticky_posts_on_top' ] ) {
			$query_args[ 'ignore_sticky_posts' ] = false;
		} else {
			$query_args[ 'ignore_sticky_posts' ] = true;
		}

		// if set exclude current displayed post
		if ( $list_settings[ 'hide_current_viewed_post' ] ) {
			global $post;
			if ( isset( $post->ID ) and is_singular() ) {
				$query_args[ 'post__not_in' ] = array( $post->ID );
			}
		}
		
		// if set filter by post author
		if ( isset(	$list_settings[ 'post_context' ][ 0 ] ) ) {
			switch ( $list_settings[ 'post_context' ][ 0 ] ) {
				case 'current_viewed_author':
					if ( isset( $post->post_author ) ) {
						$query_args[ 'author' ] = $post->post_author;
					}
					break;
			}
		}
		
		// return the query arguments
		return $query_args;

	}

	/**
	 * Print the post list
	 *
	 * @since	1.0.0
	 */
	public static function get_html( $args, $is_no_ajax = true ) {

		if ( empty( $args[ 'list_id' ] ) ) {
				return '';
		}
		
		// get settings of list
		$list_settings = self::get_stored_settings( $args[ 'list_id' ] );
	
		// overwrite stored settings with shortcode attributes values if available
		//foreach ( array( 'list_title', 'included_categories', 'excluded_categories' ) as $key ) {
		foreach ( array( 'list_title', 'included_categories' ) as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$list_settings[ $key ] = $args[ $key ];
			}
		}
		
		// recalculate offset for Ajax call
		if ( isset( $args[ 'number_items' ] ) ) {
			$list_settings[ 'offset_posts' ] = absint( $args[ 'number_items' ] ) + $list_settings[ 'offset_posts' ];
		}

		// if set add URL of title, else keep title unchanged
		if ( $list_settings[ 'list_title' ] and $list_settings[ 'url_list_title' ] ) {
			$list_settings[ 'list_title' ] = sprintf(
				'<a href="%s">%s</a>',
				$list_settings[ 'url_list_title' ],
				// link target?
				$list_settings[ 'list_title' ]
			);
		}

		// set query parameters
		$query_args = self::get_query_args( $list_settings );

        // run the query
		$r = new WP_Query( apply_filters( 'upl_list_args', $query_args ) );

		#printf( '<pre>found_posts: %s</pre>', var_export( $r->found_posts, true ) );
		#printf( '<pre>max_num_pages: %s</pre>', var_export( $r->max_num_pages, true ) );
		#printf( '<pre>post_count: %s</pre>', var_export( $r->post_count, true ) );
		#printf( '<pre>posts: %s</pre>', var_export( $r->posts, true ) );
		#printf( '<pre>query_args: %s</pre>', var_export( $query_args, true ) );
		#printf( '<pre>query_vars: %s</pre>', var_export( $r->query_vars, true ) );
		#printf( '<pre>request: %s</pre>', var_export( $r->request, true ) );
		
		$html = '';
		
		if ( $r->have_posts()) {
			
			// set the desired image dimensions
			if ( 'custom' == $list_settings[ 'size_thumbnail' ][ 0 ] ) {
				// set dimensions with specified size array
				$thumb_width  = absint( $list_settings[ 'width_thumbnail' ] );
				$thumb_height = absint( $list_settings[ 'height_thumbnail' ] );
				if ( ! $thumb_width or ! $thumb_height ) {
					$thumb_width  = self::$default_thumb_width;
					$thumb_height = self::$default_thumb_height;
				}
				// set image dimension array
				$list_settings[ 'dimensions_thumbnail' ] = array( $thumb_width, $thumb_height );
			} else {
				// overwrite thumb_width and thumb_height with closest size
				list( $thumb_width, $thumb_height ) = self::get_image_dimensions( $list_settings[ 'size_thumbnail' ][ 0 ] );
				// set dimensions with specified size name
				$list_settings[ 'dimensions_thumbnail' ] = $list_settings[ 'size_thumbnail' ][ 0 ];
			}

			// set default image code
			$default_attr = array(
				'src'		=> $list_settings[ 'url_thumbnail' ],
				'class'		=> sprintf( "attachment-%dx%d", $thumb_width, $thumb_height ),
				'alt'		=> '',
				'loading'	=> 'lazy',
			);
			self::$default_thumbnail_html = '<img ';
			self::$default_thumbnail_html .= rtrim( image_hwstring( $thumb_width, $thumb_height ) );
			foreach ( $default_attr as $attr_name => $attr_value ) {
				self::$default_thumbnail_html .= ' ' . $attr_name . '="' . $attr_value . '"';
			}
			self::$default_thumbnail_html .= ' />';
			
			// translate repeately used texts once (for more performance)
			$text = 'Continue reading %s';
			$translated[ 'read_more' ] = __( $text );
			$text = ', ';
			$translated[ 'comma' ] = __( $text );
			$text = 'By %s';
			$translated[ 'author' ] = _x( $text, 'theme author' );
			$text = 'Author:';
			$translated[ 'author_x' ] = __( $text );
			$text = 'Loading more results... please wait.';
			$translated[ 'please_wait' ] = __( $text );
			$text = 'There is no excerpt because this is a protected post.';
			$translated[ 'no_excerpt' ] = __( $text );
			$text = '(no title)';
			$translated[ 'no_title' ] = __( $text );
			
			$translated[ 'id' ] = __( 'Post ID', 'ultimate-post-list' );
			
			/*
			 * consider display settings
			 */
			
			// set order of post data sorted by key
			$positions = array( 'position_post_thumbnail', 'position_post_title', 'position_post_date', 'position_post_author','position_post_excerpt', 'position_post_comment_count', 'position_post_type', 'position_post_categories', 'position_post_tags', 'position_post_custom_taxonomies', 'position_post_read_more', 'position_post_format', /*'position_post_time','position_post_popularity',*/ );

			$pos_order = array();

			foreach ( $positions as $key ) {
				if ( isset( $list_settings[ $key ][ 0 ] ) ) {
					$i = intval( $list_settings[ $key ][ 0 ] );
				} else {
					$i = 1;
				}
				$pos_order[ $key ] = $i;
			}

			asort( $pos_order );
			
			// if desired set link target
			if ( $list_settings[ 'open_post_links_in_new_window' ] ) {
				$list_settings[ 'link_target' ] = ' target="_blank"';
			} else {
				$list_settings[ 'link_target' ] = '';
			}
			
			// if desired date format take it else default date format
			if ( preg_match( '@^[DdFjlMmnYy ,./-]+$@', $list_settings[ 'format_date' ][ 0 ] ) ) {
				$date_format = $list_settings[ 'format_date' ][ 0 ];
			} else {
				$date_format = get_option( 'date_format', 'Y-m-d' );
			}
			
			// since 4.4.0: ensure backward compatibility for the option ignore_post_content_excerpt
			if ( ! isset( $list_settings[ 'ignore_post_content_excerpt' ] ) ) {
				// set empty string as default
				$list_settings[ 'ignore_post_content_excerpt' ] = '';
			}
			
			// set available support of navigation in widgets (NAV element for more accessibility), since WP 5.5
			$is_nav_widget = current_theme_supports( 'html5', 'navigation-widgets' );

			/*
			 * create list markup
			 */
			
			// initialize avatar thumbnails cache
			$thumbs_cache = array();

			// start list if no AJAX call
			if ( $is_no_ajax ) {
				if ( $args[ 'before_widget' ] ) {
					$html .= $args[ 'before_widget' ];
					$html .= "\n";

				}
				if ( $list_settings[ 'list_css_class_name' ] ) {
					$html .= sprintf( '<div id="%s-%d" class="%s %s">', self::$list_indicator, $args[ 'list_id' ], self::$list_indicator, $list_settings[ 'list_css_class_name' ] );
				} else {
					$html .= sprintf( '<div id="%s-%d" class="%s">', self::$list_indicator, $args[ 'list_id' ], self::$list_indicator );
				}
				$html .= "\n";
				if ( $list_settings[ 'list_title' ] ) {
					$html .= $args[ 'before_title' ] . $list_settings[ 'list_title' ] . $args[ 'after_title' ] . "\n";
				}
				// open navigation element if supported
				if ( $is_nav_widget ) {
					$html .= sprintf( '<nav role="navigation" aria-label="%s">%s', esc_attr( self::get_aria_nav_label( $list_settings[ 'list_title' ] ) ), "\n" );
				}
				// open list
				$html .= "<ul>\n";
			} // if( $is_no_ajax )
			
			while ( $r->have_posts() ) { 
				$r->the_post();
				$escaped_permalink = esc_url( get_permalink() );
				$html .= '<li';
				if ( is_sticky() ) { $html .= ' class="upl-sticky"'; }
				$html .= '>';
				foreach ( $pos_order as $key => $pos ) {
					switch ( $key ) {
						// the post thumbnail
						case 'position_post_thumbnail':
							if ( $list_settings[ 'show_post_thumbnail' ] ) {
								$thumbnail = '';
								switch ( $list_settings[ 'source_thumbnail' ] ) {
									case 'featured_only':
										if ( has_post_thumbnail() ) {
											$thumbnail = get_the_post_thumbnail( null, $list_settings[ 'dimensions_thumbnail' ] );
										}
										break;
									case 'first_only':
										$thumbnail = self::the_first_post_image( $list_settings[ 'dimensions_thumbnail' ] );
										break;
									case 'first_or_featured':
										$thumbnail = self::the_first_post_image( $list_settings[ 'dimensions_thumbnail' ] );
										if ( ! $thumbnail and has_post_thumbnail() ) {
											$thumbnail = get_the_post_thumbnail( null, $list_settings[ 'dimensions_thumbnail' ] );
										}
										break;
									case 'featured_or_first':
										if ( has_post_thumbnail() ) {
											$thumbnail = get_the_post_thumbnail( null, $list_settings[ 'dimensions_thumbnail' ] );
										} else {
											$thumbnail = self::the_first_post_image( $list_settings[ 'dimensions_thumbnail' ] );
										}
										break;
									case 'use_author_avatar':
										$author_id = get_the_author_meta( 'ID' );
										// if avatar already retrieved
										if ( isset( $thumbs_cache[ $author_id ] ) ) {
											// use stored result (faster)
											$thumbnail =  $thumbs_cache[ $author_id ];
										} else {
											// retrieve and store result
											$thumbnail = $thumbs_cache[ $author_id ] = get_avatar(
												$author_id, // post author id
												$thumb_width, // width & height in px
												'', // default avatar
												sprintf( __( '%1$s %2$s', 'ultimate-post-list' ), $translated[ 'author_x' ], get_the_author_meta( 'display_name' ) ), // image alt text
												array(
													'size'  => $thumb_width,
													'height'=> $thumb_height,
													'width' => $thumb_width,
												)
											);
										}
										break;
								} // switch()
								
								// filter to set another image as thumbnail, e.g. the header image
								#$thumbnail = apply_filters( '', '' );
								
								// echo thumbnail if found, else default if desired
								if ( $thumbnail ) {
									// echo thumbnail, make it clickable if desired
									if ( $list_settings[ 'set_post_thumbnail_clickable' ] ) {
										$output = sprintf(
											'<a href="%s"%s>%s</a>',
											$escaped_permalink,
											$list_settings[ 'link_target' ],
											$thumbnail
										);
									} else {
										$output = $thumbnail;
									}
									// if alt text must be the same as the post title
									if ( $list_settings[ 'use_title_as_alt_text' ] ) {
										if ( $text = get_the_title() ) {
											$output = preg_replace( '/alt=(["\']).*?["\']/', "alt=$1$text$1", $output );
										}
									}
									$html .= sprintf( '<div class="upl-post-thumbnail">%s</div>', $output );
								} elseif ( $list_settings[ 'show_default_thumbnail' ] ) {
									// echo thumbnail, make it clickable if desired
									if ( $list_settings[ 'set_post_thumbnail_clickable' ] ) {
										$output = sprintf(
											'<a href="%s"%s>%s</a>',
											$escaped_permalink,
											$list_settings[ 'link_target' ],
											self::$default_thumbnail_html
										);
									} else {
										$output = self::$default_thumbnail_html;
									} 
									// if alt text must be the same as the post title
									if ( $list_settings[ 'use_title_as_alt_text' ] ) {
										if ( $text = get_the_title() ) {
											$output = preg_replace( '/alt=(["\']).*?["\']/', "alt=$1$text$1", $output );
										}
									}
									$html .= sprintf( '<div class="upl-post-thumbnail">%s</div>', $output );
								}
							}
							break;
						// the post title
						case 'position_post_title':
							if ( $list_settings[ 'show_post_title' ] ) {
								
								$len = $list_settings[ 'max_length_post_title' ];
							
								// get current post's post_title
								$text = get_the_title();

								// if text is longer than desired
								if ( mb_strlen( $text ) > $len ) {
									// get text in desired length
									$text = mb_substr( $text, 0, $len );
									// append 'more' text
									$text .= $list_settings[ 'text_after_shortened_title' ];
								}

								// set post ID as surrogate value if empty title
								if ( ! $text ) {
									$text = sprintf(
										'%s<span class="screen-reader-text"> %s %d</span>',
										$translated[ 'no_title' ],
										$translated[ 'id' ],
										get_the_ID() 
									);
								}
								
								// echo text, make text clickable if desired
								if ( $list_settings[ 'set_post_title_clickable' ] ) {
									$output = sprintf(
										'<a href="%s"%s>%s</a>',
										$escaped_permalink,
										$list_settings[ 'link_target' ],
										$text
									);
								} else {
									$output = $text;
								} 
								
								$html .= sprintf( '<div class="upl-post-title">%s</div>', $output );
							}
							break;
						// the post date
						case 'position_post_date':
							if ( $list_settings[ 'show_post_date' ] ) {
								
								// echo text, make text clickable if desired
								if ( $list_settings[ 'set_post_date_clickable' ] ) {
									$output = sprintf( '<a href="%s"%s>%s</a>',
										get_month_link( get_the_date( 'Y' ), get_the_date( 'm' ) ),
										$list_settings[ 'link_target' ],
										esc_html( get_the_date( $date_format ) )
									);
								} else {
									$output = esc_html( get_the_date( $date_format ) );
								} 
								
								$html .= sprintf( '<div class="upl-post-date">%s</div>', $output );
								
							}
							break;
						// the post author
						case 'position_post_author':
							if ( $list_settings[ 'show_post_author' ] ) {

								// get current post's author
								$author = get_the_author();

								// print nothing if no author
								if ( ! empty( $author ) ) {
									// echo text, make text clickable if desired
									if ( $list_settings[ 'set_post_author_clickable' ] ) {
										$output = sprintf(
											'<a href="%s"%s>%s</a>',
											get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ),
											$list_settings[ 'link_target' ],
											esc_html( sprintf( $translated[ 'author' ], $author ) )
										);
									} else {
										$output = esc_html( sprintf( $translated[ 'author' ], $author ) );
									}
								
									$html .= sprintf( '<div class="upl-post-author">%s</div>', $output );
								
								}
							}
							break;
						case 'position_post_excerpt':
							// the post excerpt
							if ( $list_settings[ 'show_post_excerpt' ] ) {
								$excerpt = '';
								$post = get_post();
								if ( ! empty( $post ) ) {
									if ( post_password_required( $post ) ) {
										$excerpt = $translated[ 'no_excerpt' ];
									} else {
										// select excerpt source:
										// take text in only post excerpt field if desired
										if ( ! $list_settings[ 'ignore_post_excerpt_field' ] ) {
											$excerpt = apply_filters( 'upl_the_excerpt', $post->post_excerpt, $post );
										}
										// create excerpt from post content automatically if no excerpt and if not ignored
										if ( empty( $excerpt ) and ! $list_settings[ 'ignore_post_content_excerpt' ] ) {
											// get current post's excerpt from post content
											$excerpt = get_the_content( '' );
											$excerpt = strip_shortcodes( $excerpt );
											$excerpt = apply_filters( 'the_excerpt', $excerpt );
											$excerpt = str_replace( ']]>', ']]&gt;', $excerpt );
											$excerpt = wp_trim_words( 
												$excerpt,
												$list_settings[ 'max_length_post_excerpt' ],
												$list_settings[ 'text_after_post_excerpt' ] 
											);
											// if excerpt is longer than desired shorten it
											if ( mb_strlen( $excerpt ) > $list_settings[ 'max_length_post_excerpt' ] ) {
												// get excerpt in desired length
												$sub_excerpt = mb_substr( $excerpt, 0, $list_settings[ 'max_length_post_excerpt' ] );
												// get array of shortened excerpt words
												$excerpt_words = explode( ' ', $sub_excerpt );
												// get the length of the last word in the shortened excerpt
												$excerpt_cut = - ( mb_strlen( $excerpt_words[ count( $excerpt_words ) - 1 ] ) );
												// if there is no empty string
												if ( $excerpt_cut < 0 ) {
													// get the shorter excerpt until the last word
													$excerpt = mb_substr( $sub_excerpt, 0, $excerpt_cut );
												} else {
													// get the shortened excerpt
													$excerpt = $sub_excerpt;
												}
											} // if length (excerpt) > max_length_post_excerpt
											// append 'more' text
											$excerpt .= $list_settings[ 'text_after_post_excerpt' ];
										} // if ( empty( $excerpt ) )
										// create text, make text clickable if desired
										if ( $excerpt and $list_settings[ 'set_post_excerpt_clickable' ] ) {
											$excerpt = sprintf(
												'<a href="%s"%s>%s</a>',
												$escaped_permalink,
												$list_settings[ 'link_target' ],
												$excerpt
											);
										}
									} // if ( post_password_required( $post ) )
								} // if ( ! empty( $post ) )
								if ( $excerpt ) {
									$html .= sprintf( '<div class="upl-post-excerpt">%s</div>', $excerpt );
								}
							} // if ( show_post_excerpt )
							break;
					} // switch( $key )
				} // foreach()
				$html .= "</li>\n";
			} // while();

			// end list if no AJAX call
			if ( $is_no_ajax ) {
				// close list
				$html .= "</ul>\n";
				// close navigation element if supported
				if ( $is_nav_widget ) {
					$html .= "</nav>\n";
				}
				
				// show "More" element if desired
				if ( $list_settings[ 'show_more_element' ] ) {
					// set list identifier
					$id = self::$list_indicator . '-' . $args[ 'list_id' ];
					// set element text
					if ( empty( $list_settings[ 'more_element_label' ] ) ) {
						$text = '(more&hellip;)'; // default text if no text
						$label = __( $text );
					} else {
						$label = $list_settings[ 'more_element_label' ];
					}
					// set text for empty result
					if ( empty( $list_settings[ 'no_more_label' ] ) ) {
						$text = 'No posts found.'; // default text if no posts
						$no_more_posts = __( $text );
					} else {
						$no_more_posts = $list_settings[ 'no_more_label' ];
					}
					// set feedback symbol
					if ( $list_settings[ 'show_more_spinner' ] ) {
						$spinner_img = sprintf( 
							'<img src="%s" alt="%s" id="%s-spinner" style="display: none;" />',
							includes_url( sprintf( 'images/%s.gif', $list_settings[ 'style_more_spinner' ][ 0 ] ) ),
							esc_attr( $translated[ 'please_wait' ] ),
							$id
						);
					} else {
						$spinner_img = ''; // nothing
					}
					
					// build "more" form structure
					if ( 'link' == $list_settings[ 'more_element_type' ] ) {
						// show link for loading further posts
						$html .= sprintf(
							'<form action="" method="POST" id="%s-form"><div><a href="" id="%s-button" class="%s-button">%s</a>%s<input type="hidden" name="no_more_label" id="%s-no-more-label" value="%s"  /></div></form>',
							$id,
							$id,
							self::$list_indicator,
							esc_html( $list_settings[ 'more_element_label' ] ),
							$spinner_img,
							$id,
							esc_attr( $list_settings[ 'no_more_label' ] )
						);
					} else {
						// show button for loading further posts
						$html .= sprintf(
							'<form action="" method="POST" id="%s-form"><div><input type="submit" name="more_submit" value="%s" id="%s-button" class="%s-button">%s<input type="hidden" name="no_more_label" id="%s-no-more-label" value="%s" /></div></form>',
							$id,
							esc_attr( $list_settings[ 'more_element_label' ] ),
							$id,
							self::$list_indicator,
							$spinner_img,
							$id,
							esc_attr( $list_settings[ 'no_more_label' ] )
						);
					}
					$html .= "\n";
				}
				
				// show rest of widget-title
				$html .= "</div>\n";
				if ( $args[ 'after_widget' ] ) {
					$html .= $args[ 'after_widget' ];
					$html .= "\n";
				}
				
			} // if( $is_no_ajax )
				
			// Reset the global $the_post as this query will have stomped on it
			wp_reset_postdata();
			
		// else if a notice should be displayed and if no Ajax call
		} elseif ( 'show' == $list_settings[ 'visibility_if_empty' ] and $is_no_ajax ) {

			// print message about empty list
			if ( $args[ 'before_widget' ] ) {
				$html .= $args[ 'before_widget' ];
				$html .= "\n";
			}
			$html .= sprintf( '<div id="%s-%d" class="%s">', self::$list_indicator, $args[ 'list_id' ], self::$list_indicator );
			$html .= "\n";
			if ( $list_settings[ 'list_title' ] ) {
				$html .= $args[ 'before_title' ] . $list_settings[ 'list_title' ] . $args[ 'after_title' ];
			}
			if ( $list_settings[ 'text_if_empty' ] ) {
				$html .= sprintf( "<p>%s</p>\n", $list_settings[ 'text_if_empty' ] );
			}
			$html .= "</div>\n";
			if ( $args[ 'after_widget' ] ) {
				$html .= $args[ 'after_widget' ];
				$html .= "\n";
			}
		} // if( show and no ajax )
		
		return $html;
		
	}
	
	/**
	 * Generate the CSS file with stored settings
	 *
	 * @since	1.0.0
	 * @access	private
	 */
	private static function make_css_file () {

		// set font family styles
		$font_families = array(
			'georgia'	=> 'Georgia, serif',
			'palatino'	=> '"Palatino Linotype", "Book Antiqua", Palatino, serif',
			'times'		=> '"Times New Roman", Times, serif',
			'arial'		=> 'Arial, Helvetica, sans-serif',
			'gadget'	=> '"Arial Black", Gadget, sans-serif',
			'comic'		=> '"Comic Sans MS", cursive, sans-serif',
			'impact'	=> 'Impact, Charcoal, sans-serif',
			'lucida'	=> '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
			'tahoma'	=> 'Tahoma, Geneva, sans-serif',
			'trebuchet'	=> '"Trebuchet MS", Helvetica, sans-serif',
			'verdana'	=> 'Verdana, Geneva, sans-serif',
			'courier'	=> '"Courier New", Courier, monospace',
			'monaco'	=> '"Lucida Console", Monaco, monospace',
		);
		
		$set_default = true;
		
		// get all published lists with their IDs
		$list_ids = array();

		$args = array(
			'posts_per_page'   => -1,
			'orderby'          => 'ID',
			'order'            => 'ASC',
			'post_type'        => UPL_POST_TYPE,
		);
		$lists = get_posts( $args );
		foreach ( $lists as $list ) {
		   $list_ids[] = $list->ID;
		}

		// generate CSS
		$css_code  = "div." . self::$list_indicator . " form, " . "div." . self::$list_indicator . " p { margin-bottom: 1em; }\n"; 
		$css_code .= "div." . self::$list_indicator . " form img { display: inline; padding-left: 1em; padding-right: 1em; box-shadow: none; vertical-align: middle; border: 0 none; }\n"; // spinner wheel image
		$css_code .= "div." . self::$list_indicator . " ul { list-style: none outside none; overflow: hidden; margin-left: 0; margin-right: 0; padding-left: 0; padding-right: 0; }\n"; 
		$css_code .= "div." . self::$list_indicator . " ul li { margin: 0 0 1.5em; clear: both; }\n"; 
		$css_code .= "div." . self::$list_indicator . " ul li:last-child { margin-bottom: 0; }\n";
		
		foreach ( $list_ids as $list_id ) {
			$settings = self::get_stored_settings( $list_id );
			if ( ! $settings ) {
				continue;
			}

			/**
			 * Thumbnail CSS
			 */
			$css_statement = '';

			// set thumbnail width and height
			$thumb_width = self::$default_thumb_width;
			$thumb_height = self::$default_thumb_height;
			$thumb_dimensions = $settings[ 'size_thumbnail' ][ 0 ];

			// if custom size: get size
			if ( 'custom' == $thumb_dimensions ) {
				$thumb_width  = absint( $settings[ 'width_thumbnail' ]  );
				$thumb_height = absint( $settings[ 'height_thumbnail' ] );
				if ( ! $thumb_width or ! $thumb_height ) {
					$thumb_width  = self::$default_thumb_width;
					$thumb_height = self::$default_thumb_height;
				}
			// else get registered size
			} else {
				list( $thumb_width, $thumb_height ) = self::get_image_dimensions( $thumb_dimensions );
			}
			
			// get aspect ratio option
			$keep_aspect_ratio = false;
			if ( $settings[ 'keep_aspect_ratio' ] ) {
				$keep_aspect_ratio = (bool) $settings[ 'keep_aspect_ratio' ];
			}
			if ( $keep_aspect_ratio ) {
				$css_statement .= sprintf(
					' max-width: %dpx; width: 100%%; height: auto;',
					$thumb_width 
				);
			} else {
				$css_statement .= sprintf(
					' width: %dpx; height: %dpx;',
					$thumb_width,
					$thumb_height
				);
			}
			
			// set thumbnail position
			switch ( $settings[ 'alignment_thumbnail' ][ 0 ] ) {
				case 'align_right':
					$css_statement .= sprintf(
						' display: inline; float: right; margin: %dpx %dpx %dpx %dpx;',
						$settings[ 'width_margin_top' ],
						$settings[ 'width_margin_right' ],
						$settings[ 'width_margin_bottom' ],
						$settings[ 'width_margin_left' ]
					);
					break;
				case 'align_left':
					$css_statement .= sprintf(
						' display: inline; float: left; margin: %dpx %dpx %dpx %dpx;',
						$settings[ 'width_margin_top' ],
						$settings[ 'width_margin_right' ],
						$settings[ 'width_margin_bottom' ],
						$settings[ 'width_margin_left' ]
					);
					break;
				default: // align center
					$css_statement .= sprintf(
						' display: block; float: none; margin: %dpx auto %dpx;',
						$settings[ 'width_margin_top' ],
						$settings[ 'width_margin_bottom' ]
					);
			}
			
			if ( $settings[ 'width_radius_thumbnail' ] ) {
				$css_statement .= sprintf(
					" -webkit-border-radius: %dpx; -moz-border-radius: %dpx; border-radius: %dpx;",
					$settings[ 'width_radius_thumbnail' ],
					$settings[ 'width_radius_thumbnail' ],
					$settings[ 'width_radius_thumbnail' ]
				);
			}

			// set the thumbnail CSS code
			$css_code .= sprintf(
				"#%s-%d ul li img {%s }\n",
				self::$list_indicator,
				$list_id,
				$css_statement
			);
			
			/**
			 * List CSS
			 */
			$css_statement = '';

			// set CSS for list title if any value
			if ( $css_statement ) {
				$css_code .= sprintf(
					"#%s-%d %s, #%s-%d .widget-title {%s }\n",
					self::$list_indicator,
					$list_id,
					$settings[ 'list_title_element' ][ 0 ],
					self::$list_indicator,
					$list_id,
					$css_statement
				);
			}
			
			/**
			 * Post text CSS
			 */
			$css_statement = '';
			
			// add list item margin
			$css_statement .= sprintf( " margin-top: %dpx;", $settings[ 'width_item_margin_top' ] );
			$css_statement .= sprintf( " margin-bottom: %dpx;", $settings[ 'width_item_margin_bottom' ] );
			$css_statement .= sprintf( " margin-left: %dpx;", $settings[ 'width_item_margin_left' ] );
			$css_statement .= sprintf( " margin-right: %dpx;", $settings[ 'width_item_margin_right' ] );

			
			/*
			 * Grid Style CSS if selected
			 */
			switch ( $settings[ 'type_list_layout' ][ 0 ] ) {
				case 'grid':
					// add grid statements
					$css_statement .= ' display: inline-block; vertical-align: top;';
					// add grid element width
					$width_grid_item = $settings[ 'width_grid_item' ];
					$width_grid_item = ( $width_grid_item > $thumb_width ) ? $width_grid_item : $thumb_width;
					$css_statement .= sprintf(
						" width: 100%%; max-width: %dpx;",
						$width_grid_item
					);
					// add list item minimal height if set
					if ( $settings[ 'height_min_grid_item' ] ) {
						$css_statement .= sprintf(
							" min-height: %spx;",
							$settings[ 'height_min_grid_item' ]
						);
					}
					break;
			}

			// set CSS for list item if any value
			if ( $css_statement ) {
				$css_code .= sprintf(
					"#%s-%d ul li {%s }\n",
					self::$list_indicator,
					$list_id,
					$css_statement
				);
			}
			
			/**
			 * Text/Image Circulation CSS
			 */
			$css_statement = array();
			
			$key = 'list_item_layout_type';
			if ( 'text_next_to_thumbnail' == $settings[ $key ][ 0 ] ) {
				// set thumbnail position
				switch ( $settings[ 'alignment_thumbnail' ][ 0 ] ) {
					case 'align_right':
						$css_statement[] = sprintf(
							' margin-right: %dpx;',
							$settings[ 'width_margin_right' ] + $thumb_width + $settings[ 'width_margin_left' ]
						);
						$css_statement[] = ' margin-right: 0;';
						break;
					case 'align_left':
						$css_statement[] = sprintf(
							' margin-left: %dpx;',
							$settings[ 'width_margin_right' ] + $thumb_width + $settings[ 'width_margin_left' ]
						);
						$css_statement[] = ' margin-left: 0;';
						break;
					default: // align center
						// do nothing
				}
			
				// set CSS for text circulation if any value
				if ( $css_statement ) {
					$elem = 'div';
					$css_code .= sprintf(
						"#%s-%d ul li %s {%s }\n",
						self::$list_indicator,
						$list_id,
						$elem,
						$css_statement[ 0 ]
					);
					$css_code .= sprintf(
						"#%s-%d ul li %s.upl-post-thumbnail {%s }\n",
						self::$list_indicator,
						$list_id,
						$elem,
						$css_statement[ 1 ]
					);
				}
			}
			
			// do not set default code
			$set_default = false;

		}
		
		// set at least this statement if no CSS was set
		if ( $set_default ) {
			$css_code .= sprintf( '.%s ul li img { width: %dpx; height: %dpx; }', self::$list_indicator, self::$default_thumb_width, self::$default_thumb_height );
			$css_code .= "\n"; 
		}
		
		// write file safely; print inline CSS on error
		$success = true;
		try {
			if ( false === @file_put_contents( UPL_CSS_FILE_PATH, $css_code ) ) {
				$success = false;
				throw new Exception();
			}
		} catch (Exception $e) {
			print "\n<!-- Ultimate Post List: Could not open the CSS file! Print inline CSS instead: -->\n";
			printf( "<style type='text/css'>%s</style>\n", $css_code );
		}
		return $success;
	}

	/**
	* Get default settings (very basic to minimize database requests)
	*
	* @since	10.6.1
	*/
	private static function get_default_settings () {
		$alignment_thumbnail = is_rtl() ? 'align_right' : 'align_left';
		$obj_datetime = new DateTime();
		// add simple translations
		$translated = array();
		foreach ( array(
			'categories' => 'Categories',
			'format' => 'Format',
			'more_label' => '(more&hellip;)',
			'no_posts' => 'No posts found.',
			'read_more' => 'Read more...',
			'tags' => 'Tags',
			) as $key => $label ) {
			$translated[ $key ] = __( $label );
		}
		// set defaults
		$defaults = array(
			'alignment_thumbnail' => array( $alignment_thumbnail ),
			'format_date' => array( 'Y-m-d' ),
			'height_min_grid_item' => 0,
			'height_thumbnail' => self::$default_thumb_height,
			'hide_current_viewed_post' => 0,
			'ignore_post_content_excerpt' => 0,
			'ignore_post_excerpt_field' => 0,
			'included_all_cats' => 0,
			'included_categories' => array( 0 ),
			'keep_aspect_ratio' => 0,
			'list_css_class_name' => '',
			'list_item_layout_type' => array( 'text_around_thumbnail' ),
			'list_title' => '',
			'list_title_element' => array( 'h3' ),
			'max_length_post_excerpt' => 55,
			'max_length_post_title' => 1000,
			'more_element_label' => $translated[ 'more_label' ],
			'more_element_type' => 'button',
			'no_more_label' => $translated[ 'no_posts' ],
			'number_posts' => self::$number_posts,
			'offset_posts' => self::$post_offset,
			'open_post_links_in_new_window' => 0,
			'position_post_author' => array( 5 ),
			'position_post_date' => array( 3 ),
			'position_post_excerpt' => array( 6 ),
			'position_post_thumbnail' => array( 1 ),
			'position_post_title' => array( 2 ),
			'post_type' => array( 'post' ),
			'posts_order_by' => array( 'post_date' ),
			'posts_order_direction' => array( 'DESC' ),
			'set_post_author_clickable' => 0,
			'set_post_date_clickable' => 0,
			'set_post_excerpt_clickable' => 0,
			'set_post_thumbnail_clickable' => 1,
			'set_post_title_clickable' => 1,
			'show_default_thumbnail' => 0,
			'show_more_element' => 0,
			'show_more_spinner' => 1,
			'show_post_author' => 0,
			'show_post_date' => 0,
			'show_post_excerpt' => 0,
			'show_post_thumbnail' => 1,
			'show_post_title' => 1,
			'show_sticky_posts_on_top' => 0,
			'size_thumbnail' => array( 'custom' ),
			'source_thumbnail' => 'featured_only',
			'style_more_spinner' => 'wpspin',
			'text_after_post_excerpt' => '&hellip;',
			'text_after_shortened_title' => '&hellip;',
			'text_if_empty' => $translated[ 'no_posts' ],
			'type_list_layout' => array( 'vertical' ),
			'url_list_title' => '',
			'url_thumbnail' => plugins_url( 'public/images/default_thumb.gif', dirname( __FILE__ ) ),
			'use_title_as_alt_text' => 0,
			'visibility_if_empty' => 'show',
			'width_grid_item' => 0,
			'width_item_margin_bottom' => 24,
			'width_item_margin_left' => 0,
			'width_item_margin_right' => 0,
			'width_item_margin_top' => 0,
			'width_margin_bottom' => 0,
			'width_margin_left' => 0,
			'width_margin_right' => 0,
			'width_margin_top' => 0,
			'width_radius_thumbnail' => 0,
			'width_thumbnail' => self::$default_thumb_width,
		);
		return $defaults;
	}

	/**
	* Get the settings
	*
	* @since	10.6.1
	*/
	private static function get_stored_settings ( $post_id ) {
		
		$result = get_post_meta( $post_id, UPL_OPTION_NAME );

		// quit with empty string if no settings found
		if ( empty( $result ) ) {
			return '';
		}
		
		$settings = $result[ 0 ];
		
		// todo: sanitize settings
		//$list_settings = self::sanitize_options( $settings );

		// set default settings for non-existing values
		foreach ( self::get_default_settings() as $option_name => $default_value ) {
			if ( ! isset( $settings[ $option_name ] ) ) {
				$settings[ $option_name ] = $default_value;
			}
		}
		
		return $settings;
		
	}
		
	/**
	 * Returns the id of the first image in the content, else 0
	 *
	 * @since	1.0.0
	 * @access	private
	 *
	 * @return	integer	the post id of the first content image
	 */
	private static function get_first_content_image_id () {
		// set variables
		global $wpdb;
		$post = get_post();
		if ( $post and isset( $post->post_content ) ) {
			// look for images in HTML code
			preg_match_all( '/<img[^>]+>/i', $post->post_content, $all_img_tags );
			if ( $all_img_tags ) {
				foreach ( $all_img_tags[ 0 ] as $img_tag ) {
					// find class attribute and catch its value
					preg_match( '/<img.*?class\s*=\s*[\'"]([^\'"]+)[\'"][^>]*>/i', $img_tag, $img_class );
					if ( $img_class ) {
						// Look for the WP image id
						preg_match( '/wp-image-([\d]+)/i', $img_class[ 1 ], $thumb_id );
						// if first image id found: check whether is image
						if ( $thumb_id ) {
							$img_id = absint( $thumb_id[ 1 ] );
							// if is image: return its id
							if ( wp_attachment_is_image( $img_id ) ) {
								return $img_id;
							}
						} // if(thumb_id)
					} // if(img_class)
					
					// else: try to catch image id by its url as stored in the database
					// find src attribute and catch its value
					preg_match( '/<img.*?src\s*=\s*[\'"]([^\'"]+)[\'"][^>]*>/i', $img_tag, $img_src );
					if ( $img_src ) {
						// delete optional query string in img src
						$url = preg_replace( '/([^?]+).*/', '\1', $img_src[ 1 ] );
						// delete image dimensions data in img file name, just take base name and extension
						$url = preg_replace( '/(.+)-\d+x\d+\.(\w+)/', '\1.\2', $url );
						// if path is protocol relative then set it absolute
						if ( 0 === strpos( $url, '//' ) ) {
							$url = self::$home_protocol . ':' . $url;
						// if path is domain relative then set it absolute
						} elseif ( 0 === strpos( $url, '/' ) ) {
							$url = sprintf( '%s://%s%s', self::$home_protocol, self::$home_domain, $url );
						}
						// look up its id in the db
						$thumb_id = self::get_image_id_by_url( $url );
						// if id is available: return it
						if ( $thumb_id ) {
							return $thumb_id;
						} // if(thumb_id)
					} // if(img_src)
				} // foreach(img_tag)
			} // if(all_img_tags)
		} // if (post content)
		
		// if nothing found: return 0
		return 0;
	}

	/**
	 * Returns width and height of a image size name, else default sizes
	 *
	 * @since	1.0.0
	 * @access	private
	 */
	private static function get_image_dimensions ( $size = 'thumbnail' ) {

		$width  = 0;
		$height = 0;
		// check if selected size is in registered images sizes
		if ( in_array( $size, get_intermediate_image_sizes() ) ) {
			// if in WordPress standard image sizes
			if ( in_array( $size, array( 'thumbnail', 'medium', 'large' ) ) ) {
				$width  = get_option( $size . '_size_w' );
				$height = get_option( $size . '_size_h' );
			} else {
				// custom image sizes, formerly added via add_image_size()
				global $_wp_additional_image_sizes;
				$width  = $_wp_additional_image_sizes[ $size ][ 'width' ];
				$height = $_wp_additional_image_sizes[ $size ][ 'height' ];
			}
		}
		// check if vars have true values, else use default size
		if ( ! $width )  $width  = self::$default_thumb_width;
		if ( ! $height ) $height = self::$default_thumb_height;
		
		// return sizes
		return array( $width, $height );
	}
	
	/**
	 * Returns the id of the image in the media library else 0
	 *
	 * @access   private
	 * @since    9.9
	 *
	 * @return    integer    the post id of the image or 0
	 */
	private static function get_image_id_by_url ( $guid ) {
		// set variables
		global $wpdb;
		// look up its ID in the db
		$thumb_id = $wpdb->get_var( $wpdb->prepare( "SELECT `ID` FROM $wpdb->posts WHERE `guid` = '%s'", $guid ) );
		// if first image id found: return it, else download it and return its id
		if ( $thumb_id ) {
			return absint( $thumb_id );
		} else {
			return 0;
		}
	}

	/**
	 * Returns the HTML of first post's image
	 *
	 * @since	1.0.0
	 * @access	private
	 *
	 * @return	bool	success on finding an image
	 */
	private static function the_first_post_image ( $size ) {
		// look for first image
		$thumb_id = self::get_first_content_image_id();
		// if there is first image then show first image
		if ( $thumb_id ) :
			return wp_get_attachment_image( $thumb_id, $size );
		else :
			return '';
		endif; // thumb_id
	}

	/**
	 * Returns the ARIA label for the NAV element
	 *
	 * @since 5.1.3
	 */
	private static function get_aria_nav_label( $title ) {
		// the title may be filtered: Strip out HTML
		$title = trim( strip_tags( $title ) );
		// and make sure the aria-label is never empty
		return $title ? $title : __( 'Post List', 'ultimate-post-list' );
	}
	
	/**
	 * Parse the shortcode attributes to build a correct list of query arguments
	 *
	 * @since	4.2.0
	 * @access	private
	 *
	 * @return	array	the sanitized arguments list for the query
	 */
	private static function sanitize_shortcode_atts ( $atts, $args, $key ) {

		// quit unchanged if key not available
		if ( ! isset( $atts[ $key ] ) ) {
			return $args;
		}
		
		// set taxonomy based on key name
		$taxonomy = '';
		switch ( $key ) {
			case 'included_categories':
			//case 'excluded_categories':
				$taxonomy = 'category';
				break;
		}
		
		// if taxonomy: get term id, else get sanitized attribute value
		if ( $taxonomy ) {
			$terms = explode( ',', $atts[ $key ] );
			foreach ( $terms as $term ) {
				// cast string to integer if it contains only digits, else keep it as string
				if ( ctype_digit( $term ) ) {
					$term = intval( $term );
				}
				$result = term_exists( $term, $taxonomy );
				if ( $result ) {
					$args[ $key ][] = $result[ 'term_id' ];
				}
			}
		} else {
			$args[ $key ] = sanitize_text_field( $atts[ $key ] );
		}

		// return sanitized value
		return $args;
	}

}

/**
 * Print the post list via public function
 *
 * @since	4.1
 */
if ( ! function_exists( 'upl_get_html' ) ) {
	function upl_get_html( $args = array() ) {
		// print list
		echo Ultimate_Post_List_Public::upl_shortcode_handler( $args );
	}
}