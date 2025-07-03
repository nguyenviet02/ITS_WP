<?php

class Ultimate_Post_List_Options {

	public static $options_set;
	public static $options_rendered;
	
	/**
	* Define the options
	*
	* @since	1.0.0
	*/
	public static function set_options () {

		/*
		 * Set multiple used translations once
		 */
		$translated = array();
		
		// add simple translations
		foreach ( array(
			'align_center' => 'Center',
			'align_justify' => 'Justify',
			'align_left' => 'Align left',
			'align_right' => 'Align right',
			'all' => 'All',
			'asc' => 'Ascending',
			'author' => 'Author',
			'avatars' => 'Avatars',
			'comma' => ', ',
			'datetime' => 'Date and time',
			'desc' => 'Descending',
			'last_modified' => 'Last Modified',
			'more_label' => '(more&hellip;)',
			'no' => 'No',
			'no_posts' => 'No posts found.',
			'none' => 'None',
			'postname' => 'Post name',
			'pw_protected' => 'Password protected',
			'random' => 'Random',
			'read_more' => 'Read more...',
			'select' => '&mdash; Select &mdash;',
			'settings' => 'Settings',
			'slug' => 'Slug',
			'yes' => 'Yes',
			) as $key => $label ) {
			$translated[ $key ] = __( $label );
		}

		// add context translations
		$contextuals = array(
			// post types (generic and custom)
			'Posts' => 'post type general name',
			'Pages' => 'post type general name',
			'Post' => 'post type singular name',
			'Page' => 'post type singular name',
			// statuses
			'Private'	=> 'post status',
			'Published'	=> 'post status',
			'Scheduled'	=> 'post status',
			'Pending'	=> 'post status',
			'Draft'		=> 'post status',
			'Trash'		=> 'post status',
			// post types
			'Media'		=> 'post type general name',
		);
		foreach( $contextuals as $label => $context ) {
			$translated[ $label ] = _x( $label, $context );
		}
		
		// add complex strings
		$translated[ 'in_desc' ]			= sprintf( __( 'Use the CTRL key to select multiple entries.', 'ultimate-post-list' ), implode( $translated[ 'comma' ], wp_allowed_protocols() ) );
		$translated[ 'post_title' ]			= __( 'Post title', 'ultimate-post-list' );
		$translated[ 'url_desc' ]			= sprintf( __( 'An URL starting with %s is accepted, else http:// is prepended automatically.', 'ultimate-post-list' ), implode( $translated[ 'comma' ], wp_allowed_protocols() ) );
		$text_1 = 'Date Format';
		$text_2 = 'General Settings';
		$translated[ 'date-format-desc' ]	= sprintf( 
			__( 'Select the desired representation of the post date, as shown in the list exemplarily with the 1st of February in 2003. If not specified the date format as set at &#8220;%s&#8221; on the page &#8220;%s&#8221; is taken.', 'ultimate-post-list' ), 
			__( $text_1 ),
			__( $text_2 )
		);
		
		/*
		 * Set default values
		 */
		$default = array();

		// set custom post types selections
		$defaults[ 'post_types' ] = array(
			'post' => $translated[ 'Posts' ],
			'page' => $translated[ 'Pages' ],
		);
		// sort the result alphabetically
		natsort( $defaults[ 'post_types' ] );
		// set number of post types
		$defaults[ 'size_post_types' ] = 2;

		// set number of post types
		$defaults[ 'size_post_types' ] = self::get_selection_size( count( $defaults[ 'post_types' ] ) );

		// set category selections boxes based on stored categories
		$categories = self::get_categories();

		if ( empty( $categories ) ) {
			$defaults[ 'in_categories' ] = array();
			$defaults[ 'ex_categories' ] = array();
			$defaults[ 'size_categories' ] = 0;
		} else {
			$defaults[ 'in_categories' ] = self::array_unshift_assoc( $categories, 0, $translated[ 'all' ] );
			$defaults[ 'ex_categories' ] = self::array_unshift_assoc( $categories, 0, $translated[ 'none' ] );
			$defaults[ 'size_categories' ] = self::get_selection_size( count( $categories ) + 1 );
		}

		// set thumbnail alignment selection box, default is based on current reading direction
		if ( is_rtl() ) {
			$defaults[ 'alignment_thumbnail' ]	= 'align_right';
			$defaults[ 'width_margin_left' ]	= 8;
			$defaults[ 'width_margin_right' ]	= 0;
		} else {
			$defaults[ 'alignment_thumbnail' ]	= 'align_left';
			$defaults[ 'width_margin_left' ]	= 0;
			$defaults[ 'width_margin_right' ]	= 8;
		}
		$defaults[ 'width_margin_top' ]			= 0;
		$defaults[ 'width_margin_bottom' ]		= 8;
		
		// set position selection boxes
		// only $positions needs to be changed to get the rest dynamically
		$positions = array(
			'position_post_thumbnail'			=> __( 'Position of post thumbnail', 'ultimate-post-list' ),
			'position_post_title'				=> __( 'Position of post title', 'ultimate-post-list' ),
			'position_post_date'				=> __( 'Position of post date', 'ultimate-post-list' ),
			'position_post_author'				=> __( 'Position of post author name', 'ultimate-post-list' ),
			'position_post_excerpt'				=> __( 'Position of post excerpt', 'ultimate-post-list' ),
		);

		// set date format selection
		/*
			l: Full name of a day (lower-case 'L')
			D: Abbreviated name of a day
			d: Number of a day of the month with leading zero
			j: Number of a day of the month without leading zero
			F: Full name of a month
			M: Abbreviated name of a month
			m: Number of a month with leading zero
			n: Number of a month without leading zero
			Y: Number of a year in 4 digits
			y: Number of a year in 2 digits
		*/
		$defaults[ 'date_formats' ] = array(
			'null' => $translated[ 'select' ],
		);
		$dateformats = array(
			'Day'	=> array( 'l d F Y', 'l j F Y', 'l, d F Y', 'l, d F, Y', 'l, d. F Y', 'l, d. F, Y', 'l, j F Y', 'l, j F, Y', 'l, j. F Y', 'l, j. F, Y', 'D, M d, Y', 'D, M j, Y', 'D, d F Y', 'D, d-F-Y', 'D, d. F Y', 'D, j F Y', 'D, j F Y', 'D, j M Y', 'D, j-F-Y', 'D, j. F Y', 'd F Y', 'd F y', 'd F, Y', 'd F, y', 'd M Y', 'd M y', 'd M. Y', 'd M. y', 'd-M-Y', 'd-M-y', 'd-m-Y', 'd-m-y', 'd-n-Y', 'd-n-y', 'd. F Y', 'd. M Y', 'd.m.Y', 'd.m.y', 'd.n.Y', 'd.n.y', 'd/F/Y', 'd/M/Y', 'd/M/y', 'd/m/Y', 'd/m/y', 'j F Y', 'j F y', 'j F, Y', 'j F, y', 'j M Y', 'j M y', 'j M. Y', 'j M. y', 'j-M-Y', 'j-M-y', 'j-n-y', 'j. F Y', 'j. M Y', 'j.n.y', 'j/F/Y', 'j/M/Y', 'j/M/y', 'j/n/Y', 'j/n/y', ),
			'Month'	=> array( 'F d, Y', 'F d, y', 'F j, Y', 'F j, y', 'F-d-Y', 'F-d-y', 'F-j-Y', 'F-j-y', 'M d, Y', 'M d, y', 'M j, Y', 'M j, y', 'M j, y', 'm-d-Y', 'm-d-y', 'm/d/Y', 'm/d/y', 'm/j/Y', 'm/j/y', 'n/d/Y', 'n/d/y', 'n/j/Y', 'n/j/y', ),
			'Year'	=> array( 'Y, F d, l', 'Y, F j, l', 'Y-m-d', 'Y-n-d', 'Y-n-j', 'Y.m.d', 'Y.m.d.', 'Y.m.j', 'Y/M/d', 'Y/m/d', 'Y/n/j', 'y m d', 'y-m-d', 'y-n-d', 'y-n-j', 'y.n.j', 'y/M/d', 'y/m/d', 'y/n/j', ) 
		);
		$testdate = strtotime( '1 Feb 2003' );
		foreach ( $dateformats as $base => $formats ) {
			foreach ( $formats as $format ) {
				$defaults[ 'date_formats' ][ $base ][ $format ] = date_i18n( $format, $testdate );
			}
		}

		// end of defaults definitions
		
		/* define the form sections, order by appereance, with headlines, and options
		 */
		self::$options_set = array(
			'list_options' => array(
				'title' => __( 'List Options', 'ultimate-post-list'),
				'list_display_options' => array(
					'headline' => __( 'List Display Options', 'ultimate-post-list' ),
					'options' => array(
						'visibility_if_empty' => array(
							'type'		=> 'radiobuttons',
							'label'		=> __( 'List Visibility', 'ultimate-post-list' ),
							'values'	=> array( 
								'show' => __( 'Show title and text if no post is found', 'ultimate-post-list'),
								'hide' => __( 'Hide list if no post is found', 'ultimate-post-list'),
							),
							'default'	=> 'show',
						),
						'text_if_empty' => array(
							'type'		=> 'textfield',
							'label'		=> __( 'Text if no posts', 'ultimate-post-list' ),
							'default'	=> $translated[ 'no_posts' ],
							'desc'		=> __( 'This field can be empty', 'ultimate-post-list' ),
						),
					),
				), // end list_display_options
				'list_title_options' => array(
					'headline' => __( 'List Title Options', 'ultimate-post-list' ),
					'options' => array(
						'list_title' => array(
							'type'	=> 'textfield',
							'label'	=> __( 'Title of the list in post content', 'ultimate-post-list' ),
							'desc'	=> __( 'If the list is displayed via shortcode in the content of a post or page you can set the list title by entering the headline. To remove the list title just leave this field empty. You can convert the text to a link and set the headline level with the next two options. If the list is displayed as a widget you can set the list title in the widget form.', 'ultimate-post-list' ),
						),
						'url_list_title' => array(
							'type'	=> 'url',
							'label'	=> __( 'URL of list title', 'ultimate-post-list' ),
							'desc'	=> __( 'If you want to link the list title to a web page enter the URL here. To remove the link just leave this field empty.', 'ultimate-post-list' ) . ' ' . $translated[ 'url_desc' ],
						),
						'list_title_element' => array(
							'type'		=> 'selection',
							'label'		=> __( 'HTML element of list title', 'ultimate-post-list' ),
							'values'	=> array(
								'h1'	=> 'H1',
								'h2'	=> 'H2',
								'h3'	=> 'H3',
								'h4'	=> 'H4',
								'h5'	=> 'H5',
								'h6'	=> 'H6',
							),
							'default'	=> array( 'h3' ),
							'desc'		=> __( 'Headline level in lists printed by shortcode, ignored in widgets.', 'ultimate-post-list' ),
						),
					),
				), // list_title_options
				'posts_list_options_general' => array(
					'headline' => __( 'Post List Options In General', 'ultimate-post-list' ),
					'options' => array(
						'number_posts'					=> array( 'type' => 'absint',		'label' => __( 'Number of posts', 'ultimate-post-list' ),	'default' => get_option( 'posts_per_page', 7 ),	'required' => 1, 'desc' => sprintf( __( 'Number of posts to show in the list. If the value is not an integer or 0 the default of %d is used.', 'ultimate-post-list' ), get_option( 'posts_per_page', 7 ) ), ),
						'offset_posts'					=> array( 'type' => 'absint',		'label' => __( 'Posts offset', 'ultimate-post-list' ),	'default' => 0,	'required' => 1, 'desc' => __( 'Number of post to displace or pass over. If the value is not an integer the default of 0 is used.', 'ultimate-post-list' ), ),
						'hide_current_viewed_post'		=> array( 'type' => 'checkbox',		'label' => __( 'Hide current viewed post in list', 'ultimate-post-list' ), ),
						'show_sticky_posts_on_top'		=> array( 'type' => 'checkbox',		'label' => __( 'Show sticky posts on top of the list', 'ultimate-post-list' ), ),
					),
				), // end posts_list_options_general
				'posts_sort_order' => array(
					'headline' => __( 'Posts Sort Order', 'ultimate-post-list' ),
					'options' => array(
						'posts_order_by' => array(
							'type'		=> 'selection',
							'label'		=> __( 'Order by', 'ultimate-post-list' ),
							'values'	=> array(
								'post_date'		=> $translated[ 'datetime' ],
								'post_title'	=> $translated[ 'post_title' ],
							),
							'default' => array( 'post_date' ),
						),
						'posts_order_direction' => array(
							'type'		=> 'selection',
							'label'		=> __( 'Order direction', 'ultimate-post-list' ),
							'values'	=> array(
								'ASC'	=> $translated[ 'asc' ],
								'DESC'	=> $translated[ 'desc' ]
							),
							'default' => array( 'DESC' ),
						),
					),
				), // end posts_sort_order
			), // end posts_list_options
			'posts_list_filter_options' => array(
				'title' => __( 'Post Filter Options', 'ultimate-post-list' ),
				'post_type_filter' => array(
					'headline' => __( 'Post Type Filter', 'ultimate-post-list' ),
					'description' => __( 'Filters the posts by post type.', 'ultimate-post-list' ),
					'options' => array(
						'post_type' => array(
							'type'		=> 'selection',
							'label'		=> __( 'Show posts of selected types only', 'ultimate-post-list' ),
							'attr'		=> array( 'multiple', array( 'size' => $defaults[ 'size_post_types' ] ) ),
							'values'	=> $defaults[ 'post_types' ],
							'default'	=> array( 'post' ),
							'required'	=> 1,
							'desc'		=> $translated[ 'in_desc' ],
						),
					),
				), // end post_type_filter
				'post_category_filter' => array(
					'headline' => __( 'Category Filters', 'ultimate-post-list' ),
					'description' => __( 'Filters the posts by categories.', 'ultimate-post-list' ),
					'options' => array(
						'included_categories' => array(
							'type'		=> 'selection',
							'label'		=> __( 'Show posts of selected categories only', 'ultimate-post-list' ),
							'attr'		=> array( 'multiple', array( 'size' => $defaults[ 'size_categories' ] ) ),
							'values'	=> $defaults[ 'in_categories' ],
							'default'	=> array( 0 ),
							'desc'		=> $translated[ 'in_desc' ],
						),
						'included_all_cats'	=> array(
							'type' => 'checkbox',
							'label' => __( 'Show only posts that are in all selected categories', 'ultimate-post-list' ),
						),
						/*
						'excluded_categories' => array(
							'type'		=> 'selection',
							'label'		=> __( 'Hide posts of selected categories', 'ultimate-post-list' ),
							'attr'		=> array( 'multiple', array( 'size' => $defaults[ 'size_categories' ] ) ),
							'values'	=> $defaults[ 'ex_categories' ],
							'default'	=> array( 0 ),
							'desc'		=> $translated[ 'in_desc' ],
						),
						*/
					),
				), // end post_category_filter
			), // end posts_list_filter_options
			'posts_list_item_options' => array(
				'title' => __( 'Post List Item Options', 'ultimate-post-list'),
				'post_data_order' => array(
					'headline' => __( 'Post Data Order', 'ultimate-post-list' ),
					'description' => __( 'Select a number to set the position of each post information. The higher the number the lower the position. If a number is used multiple times the result is not predictable. So use each number only once. Post informations which are set not to be shown will be ingored.', 'ultimate-post-list' ),
					'options' => self::get_position_options( $positions ),
				), // end post_data_order
				'post_display_options' => array(
					'headline' => __( 'Post Display Options', 'ultimate-post-list' ),
					'options' => array(
						'show_post_thumbnail'	=> array( 'type' => 'checkbox', 'label' => __( 'Show post thumbnail', 'ultimate-post-list' ), 'default' => 1, ),
						'show_post_title'		=> array( 'type' => 'checkbox', 'label' => __( 'Show post title', 'ultimate-post-list' ), 'default' => 1, ),
						'show_post_date'		=> array( 'type' => 'checkbox', 'label' => __( 'Show post date', 'ultimate-post-list' ), ),
						'show_post_author'		=> array( 'type' => 'checkbox', 'label' => __( 'Show post author name', 'ultimate-post-list' ), ),
						'show_post_excerpt'		=> array( 'type' => 'checkbox', 'label' => __( 'Show post excerpt', 'ultimate-post-list' ), ),
					),
				), // end post_options_in_general
				'post_link_options' => array(
					'headline' => __( 'Post Links Options', 'ultimate-post-list' ),
					'description' => __( 'Each link will point to the post unless otherwise specified.', 'ultimate-post-list' ),
					'options' => array(
						'set_post_title_clickable'			=> array( 'type' => 'checkbox', 'label' => __( 'Set post title clickable', 'ultimate-post-list' ), 'default' => 1, ),
						'set_post_thumbnail_clickable'		=> array( 'type' => 'checkbox', 'label' => __( 'Set post thumbnail clickable', 'ultimate-post-list' ), 'default' => 1, ),
						'set_post_date_clickable'			=> array( 'type' => 'checkbox', 'label' => __( 'Set post date clickable, pointing to the month archive', 'ultimate-post-list' ), ),
						'set_post_author_clickable'			=> array( 'type' => 'checkbox', 'label' => __( 'Set post author clickable, pointing to the author&#8217;s archive', 'ultimate-post-list' ), ),
						'set_post_excerpt_clickable'		=> array( 'type' => 'checkbox', 'label' => __( 'Set post excerpt clickable', 'ultimate-post-list' ), ),
						'open_post_links_in_new_window'		=> array( 'type' => 'checkbox',	'label' => __( 'Open post links in new windows', 'ultimate-post-list' ), ),
					),
				), // end post_link_options
				'post_title_options' => array(
					'headline' => __( 'Post Title Options', 'ultimate-post-list' ),
					'options' => array(
						'max_length_post_title'			=> array( 'type' => 'absint',		'label' => __( 'Maximum length of post title', 'ultimate-post-list' ), 'default' => 1000,	'required' => 1,	'desc' => __( 'Maximal number of letters', 'ultimate-post-list' ), ),
						'text_after_shortened_title'	=> array( 'type' => 'textfield',	'label' => __( 'Text after shortened title', 'ultimate-post-list' ), 'default' => '&hellip;' ),
					),
				), // end post_title_options
				'post_date_options' => array(
					'headline' => __( 'Post Date Options', 'ultimate-post-list' ),
					'description' => $translated[ 'date-format-desc' ],
					'options' => array(
						'format_date'	=> array(
							'type'		=> 'selection',
							'label'		=> __( 'Format of the post date', 'ultimate-post-list' ),
							'values'	=> $defaults[ 'date_formats' ],
							'default'	=> array( get_option( 'date_format', 'Y-m-d' ) ),
						),
					),
				), // end post_date_options
				'post_excerpt_options' => array(
					'headline' => __( 'Post Excerpt Options', 'ultimate-post-list' ),
					'options' => array(
						'max_length_post_excerpt'		=> array( 'type' => 'absint',		'label' => __( 'Maximum length of post excerpt', 'ultimate-post-list' ), 'default' => absint( apply_filters( 'excerpt_length', 55 ) ),	'required' => 1,	'desc' => __( 'Maximal number of letters', 'ultimate-post-list' ), ),
						'text_after_post_excerpt'		=> array( 'type' => 'textfield',	'label' => __( 'Text after shortened excerpt', 'ultimate-post-list' ), 'default' => '&hellip;' ),
						'ignore_post_excerpt_field'		=> array( 'type' => 'checkbox',		'label' => __( 'Ignore post excerpt field as excerpt source', 'ultimate-post-list' ), 'desc' => __( 'Normally the widget takes the excerpt from the text of the excerpt field unchanged and if there is no text it creates the excerpt from the post content automatically. If this option is activated the excerpt is created from the post content only.', 'ultimate-post-list' ), ),
						'ignore_post_content_excerpt'	=> array( 'type' => 'checkbox',		'label' => __( 'Ignore post content as excerpt source.', 'ultimate-post-list' ), 'desc' => __( 'If activated, the excerpts are created only by the excerpt fields. If both Ignore checkboxes are activated no excerpts are displayed in the list.', 'ultimate-post-list' ), ),
					),
				), // end post_excerpt_options
				'post_thumbnail_options' => array(
					'headline' => __( 'Post Thumbnail Options', 'ultimate-post-list' ),
					'options' => array(
						'source_thumbnail' => array(
							'type'		=> 'radiobuttons',
							'label'		=> __( 'Source of the post thumbnail', 'ultimate-post-list' ),
							'values'	=> array( 
								'featured_only'		=> __( 'Featured image', 'ultimate-post-list'),
								'first_only'		=> __( 'First post content image if previously uploaded to the media library', 'ultimate-post-list'),
								'first_or_featured'	=> __( 'Featured image if the first post content image is not available', 'ultimate-post-list'),
								'featured_or_first'	=> __( 'First post content image if the featured image is not available', 'ultimate-post-list'),
								'use_author_avatar'	=> $translated[ 'avatars' ],
							),
							'default'	=> 'featured_only',
						),
						'use_title_as_alt_text'		=> array( 'type' => 'checkbox', 'label' => __( 'Use post title as the alternative text for the thumbnail', 'ultimate-post-list' ), 'desc' => __( 'If the alternative texts of the thumbnails are incomprehensible or if this list does not show post titles, then it makes sense to activate this option.', 'ultimate-post-list' ) ),
						'show_default_thumbnail'	=> array( 'type' => 'checkbox', 'label' => __( 'Use default thumbnail if no image could be ascertained', 'ultimate-post-list' ), ),
						'url_thumbnail'	=> array(
							'type'		=> 'url',
							'label'		=> __( 'URL of default thumbnail', 'ultimate-post-list' ),
							'default'	=> plugins_url( 'public/images/default_thumb.gif', dirname( __FILE__ ) ),
							'desc'		=> $translated[ 'url_desc' ],
						),
						'size_thumbnail'	=> array(
							'type'		=> 'selection',
							'label'		=> __( 'Thumbnail size', 'ultimate-post-list' ), 
							'values'	=> self::get_image_size_options(),
							'default'	=> array( 'custom' ),
						),
						'width_thumbnail'		=> array( 'type' => 'absint', 'label' => __( 'Width of thumbnail in px', 'ultimate-post-list' ), 'default' => absint( round( get_option( 'thumbnail_size_w', 110 ) / 2 ) ) ),
						'height_thumbnail'		=> array( 'type' => 'absint', 'label' => __( 'Height of thumbnail in px', 'ultimate-post-list' ), 'default' => absint( round( get_option( 'thumbnail_size_h', 110 ) / 2 ) ) ),
						'keep_aspect_ratio'		=> array( 'type' => 'checkbox', 'label' => __( 'Use aspect ratios of original images', 'ultimate-post-list' ), ),
						'alignment_thumbnail'	=> array(
							'type'		=> 'selection',
							'label'		=> __( 'Thumbnail alignment', 'ultimate-post-list' ),
							'values'	=> array(
								'align_left'	=> $translated[ 'align_left' ],
								'align_center'	=> $translated[ 'align_center' ],
								'align_right'	=> $translated[ 'align_right' ],
							),
							'default'	=> array( $defaults[ 'alignment_thumbnail' ] ),
							'desc'		=> sprintf( __( 'If %s the values for the right and left margins will be ignored.', 'ultimate-post-list' ), $translated[ 'align_center' ] ),
						),
						'list_item_layout_type'	=> array(
							'type' => 'selection',
							'label' => __( 'Flow of text at the thumbnail', 'ultimate-post-list' ),
							'values' => array(
								'text_around_thumbnail'		=> __( 'Text floats around the thumbnail', 'ultimate-post-list' ),
								'text_next_to_thumbnail'	=> __( 'Text is next to the thumbnail', 'ultimate-post-list' ),
							),
							'default' => array( 'text_around_thumbnail' ),
						),
						'width_margin_top'		=> array( 'type' => 'absint', 'label' => __( 'Top image margin in px', 'ultimate-post-list' ),		'default' => $defaults[ 'width_margin_top' ],	'required' => 1, ),
						'width_margin_bottom'	=> array( 'type' => 'absint', 'label' => __( 'Bottom image margin in px', 'ultimate-post-list' ),	'default' => $defaults[ 'width_margin_bottom' ],'required' => 1, ),
						'width_margin_left'		=> array( 'type' => 'absint', 'label' => __( 'Left image margin in px', 'ultimate-post-list' ),		'default' => $defaults[ 'width_margin_left' ],	'required' => 1, ),
						'width_margin_right'	=> array( 'type' => 'absint', 'label' => __( 'Right image margin in px', 'ultimate-post-list' ),	'default' => $defaults[ 'width_margin_right' ],	'required' => 1, ),
						'width_radius_thumbnail'	=> array( 'type' => 'absint', 'label' => __( 'Radius of rounded image corners in px', 'ultimate-post-list' ), ),
					),
				), // end post_thumbnail_options
			), // end posts_list_item_options
			'posts_list_more_element_options' => array(
				'title' => __( '&#8220;More&#8221; Element Options', 'ultimate-post-list' ),
				'posts_list_more_element_appearance' => array(
					'headline' => __( '&#8220;More&#8221; Element Appearance', 'ultimate-post-list' ),
					'description' => __( 'Switch on and off an element to load further list items without leaving the page. The theme determines the appearance of the element. In this way, the element fits optically perfectly.', 'ultimate-post-list' ),
					'options' => array(
						'show_more_element'	=> array(
							'type' => 'checkbox',
							'label' => __( 'Show a clickable &#8220;More&#8221; element for loading further list items at the bottom of the list', 'ultimate-post-list' ),
						),
						'more_element_type' => array(
							'type'		=> 'radiobuttons',
							'label'		=> __( '&#8220;More&#8221; element type', 'ultimate-post-list' ),
							'values'	=> array( 
								'link'	=> __( 'Show element as a link', 'ultimate-post-list' ),
								'button'=> __( 'Show element as a button', 'ultimate-post-list' ),
							),
							'default'	=> 'button',
						),
						'more_element_label' => array(
							'type'		=> 'textfield',
							'label'		=> __( 'Label of &#8220;More&#8221; element', 'ultimate-post-list' ),
							'default'	=> $translated[ 'more_label' ],
						),
						'show_more_spinner'	=> array(
							'type' => 'checkbox',
							'label' => __( 'Show icon while new posts are loaded', 'ultimate-post-list' ),
							'default' => 1, 
						),
						'style_more_spinner' => array(
							'type'		=> 'selection',
							'label'		=> __( 'Icon style', 'ultimate-post-list' ),
							'values'	=> array(
								'null' => $translated[ 'select' ],
								__( 'Small icons', 'ultimate-post-list' )	=> array(
									'spinner'	=> __( 'Small gray circle with rotating dot', 'ultimate-post-list' ),
									'wpspin'	=> __( 'Small turning wheel', 'ultimate-post-list' ),
								),
								__( 'Big icons', 'ultimate-post-list' )	=> array(
									'spinner-2x'=> __( 'Big gray circle with rotating dot', 'ultimate-post-list' ),
									'wpspin-2x'	=> __( 'Big turning wheel', 'ultimate-post-list' ),
								),
							),
							'default'	=> 'wpspin',
						),
						'no_more_label' => array(
							'type'		=> 'textfield',
							'label'		=> __( 'Text that appears when no further posts have been found', 'ultimate-post-list' ),
							'default'	=> $translated[ 'no_posts' ],
						),
					),
				), // end posts_list_more_element_feedback_options
			), // end posts_list_more_element
			'list_layout_options' => array(
				'title' => __( 'List Layout Options', 'ultimate-post-list'),
				'layout_type_options' => array(
					'headline' => __( 'List Layout Type', 'ultimate-post-list' ),
					'options' => array(
						'type_list_layout'	=> array(
							'type' => 'selection',
							'label' => __( 'Type of list layout', 'ultimate-post-list' ),
							'values' => array(
								'vertical'		=> __( 'Vertical list', 'ultimate-post-list' ),
								//'horizontal'	=> __( 'Horizontal list', 'ultimate-post-list' ),
								'grid'			=> __( 'Responsive grid', 'ultimate-post-list' ),
							),
							'default' => array( 'vertical' ),
						),
					),
				), // end layout_type_options
				'grid_layout_options' => array(
					'headline' => __( 'Grid Layout Options', 'ultimate-post-list' ),
					'description' => __( 'These options only takes effect if a grid layout was selected in the previous option.', 'ultimate-post-list' ),
					'options' => array(
						'width_grid_item'		=> array( 'type' => 'absint', 'label' => __( 'Width of grid item content in px', 'ultimate-post-list' ),	'desc'	=> __( 'If the specified width is smaller than the thumbnail width the thumbnail width will be used.', 'ultimate-post-list' ), ),
						'height_min_grid_item'	=> array( 'type' => 'absint', 'label' => __( 'Minimal height of grid item in px', 'ultimate-post-list' ), ),
					),
				), // end grid_layout_options
				'list_item_margin_options' => array(
					'headline' => __( 'List Item Margin Options', 'ultimate-post-list' ),
					'description' => __( 'Set the space between each list item or grid element.', 'ultimate-post-list' ),
					'options' => array(
						'width_item_margin_top'		=> array( 'type' => 'absint', 'label' => __( 'Top item margin in px', 'ultimate-post-list' ),	 ),
						'width_item_margin_bottom'	=> array( 'type' => 'absint', 'label' => __( 'Bottom item margin in px', 'ultimate-post-list' ), 'default' => 24 ),
						'width_item_margin_left'	=> array( 'type' => 'absint', 'label' => __( 'Left item margin in px', 'ultimate-post-list' ),	 ),
						'width_item_margin_right'	=> array( 'type' => 'absint', 'label' => __( 'Right item margin in px', 'ultimate-post-list' ),	 ),
					),
				), // end list_item_margin_options
			), // end list_layout_options
		);
	}
		
	/**
	* Get the default settings
	*
	* @since	1.0.0
	*/
	public static function get_default_settings () {
		$defaults = array();
		if ( empty( self::$options_set ) ) {
			self::set_options();
		}
		foreach ( self::$options_set as $chapter => $sections ) {
			if ( ! is_array( $sections ) ) {
				continue;
			}
			foreach ( $sections as $section_key => $section_values ) {
				if ( ! is_array( $section_values ) or ! isset( $section_values[ 'options' ] ) ) {
					continue;
				}
				foreach ( $section_values[ 'options' ] as $option_name => $option_values ) {
					if ( isset( $option_values[ 'default' ] ) ) {
						$defaults[ $option_name ] = $option_values[ 'default' ];
					} else {
						switch ( $option_values[ 'type' ] ) {
							case 'checkbox':
							case 'absint':
							case 'int':
								$defaults[ $option_name ] = 0;
								break;
							case 'checkboxes':
								foreach ( array_keys( $option_values[ 'values' ] ) as $option_name ) {
									$defaults[ $option_name ] = 0 ;
								}
								break;
							case 'float':
								$defaults[ $option_name ] = 0.0;
								break;
							case 'selection':
								$defaults[ $option_name ] = array();
								break;
							// else all other form elements
							default:
								$defaults[ $option_name ] = '';
						} // end switch()
					}
				}
			}
		}
		return $defaults;
	}
		
	/**
	* Get the settings
	*
	* @since	1.0.0
	*/
	private static function get_stored_settings ( $post_id ) {
		
		$settings = get_post_meta( $post_id, UPL_OPTION_NAME );

		if ( empty( $settings ) ) {
			return self::get_default_settings();
		} else {
			// sanitize settings
			$settings = self::sanitize_options( $settings[ 0 ] );
			// set default settings for non-existing values
			foreach ( self::get_default_settings() as $option_name => $default_value ) {
				if ( ! isset( $settings[ $option_name ] ) ) {
					$settings[ $option_name ] = $default_value;
				}
			}
			
			return $settings;
			
		}
	}
		
	/**
	* Get rendered HTML code of the options
	*
	* @since	1.0.0
	*/
	public static function set_rendered_options ( $post_id = null ) {
		
		self::$options_rendered = array();
		if ( empty( self::$options_set ) ) {
			self::set_options();
		}

		if ( ! ( $post_id and self::$options_set ) ) {
			return;
		}

		$text_no_items = 'No items';
		$label_no_items = __( $text_no_items );

		$settings = self::get_stored_settings( $post_id );
		
		// build form with sections and options
		foreach ( self::$options_set as $chapter => $sections ) {
			if ( ! is_array( $sections ) ) {
				continue;
			}
			self::$options_rendered[ $chapter ] = array();
			foreach ( $sections as $section_key => $section_values ) {
				if ( ! is_array( $section_values ) or ! isset( $section_values[ 'options' ] ) ) {
					continue;
				}
				self::$options_rendered[ $chapter ][ $section_key ] = array(
					'headline' => $section_values[ 'headline' ]
				);
				if ( isset( $section_values[ 'description' ] ) ) {
					self::$options_rendered[ $chapter ][ $section_key ][ 'description' ] = $section_values[ 'description' ];
				}
				// set labels and callback function names per option name
				foreach ( $section_values[ 'options' ] as $option_name => $option_values ) {
					// set default description
					$desc = '';
					if ( isset( $option_values[ 'desc' ] ) and '' != $option_values[ 'desc' ] ) {
						$desc = sprintf( ' <span class="description">%s</span>', esc_html( $option_values[ 'desc' ] ) );
					}
					// build the form elements values
					switch ( $option_values[ 'type' ] ) {
						case 'radiobuttons':
							$stored_value = isset( $settings[ $option_name ] ) ? $settings[ $option_name ] : $option_values[ 'default' ];
							$attrs = '';
							if ( isset( $option_values[ 'attr' ] ) ) {
								$attrs = self::get_attributes_html( $option_values[ 'attr' ] );
							}
							$html = sprintf( '<fieldset><legend><span>%s</span></legend>', esc_html( $option_values[ 'label' ] ) );
							$leftover = count( $option_values[ 'values' ] );
							foreach ( $option_values[ 'values' ] as $value => $label ) {
								$html .= sprintf(
									'<label><input type="radio" name="%s[%s]" value="%s"%s%s /> <span>%s</span></label>',
									UPL_OPTION_NAME,
									esc_attr( $option_name ),
									esc_attr( $value ),
									checked( $stored_value, $value, false ),
									$attrs,
									esc_html( $label )
								);
								$leftover--;
								if ( $leftover ) {
									$html .= '<br />';
								}
							}
							$html .= $desc ? '<br />' . $desc : '';
							$html .= '</fieldset>';
							break;
						case 'checkboxes':
							$title = $option_values[ 'title' ];
							$attrs = '';
							if ( isset( $option_values[ 'attr' ] ) ) {
								$attrs = self::get_attributes_html( $option_values[ 'attr' ] );
							}
							$html = sprintf( '<fieldset><legend><span>%s</span></legend>', $title );
							$leftover = count( $option_values[ 'values' ] );
							foreach ( $option_values[ 'values' ] as $value => $label ) {
								$stored_value = isset( $settings[ $value ] ) ? esc_attr( $settings[ $value ] ) : '0';
								$checked = $stored_value ? checked( '1', $stored_value, false ) : '';
								$html .= sprintf(
									'<label><input name="%s[%s]" type="checkbox" id="%s" value="1"%s%s /> %s</label>',
									UPL_OPTION_NAME,
									$value,
									$value,
									$checked,
									$attrs,
									esc_html( $label )
								);
								$leftover--;
								if ( $leftover ) {
									$html .= '<br />';
								}
							}
							$html .= $desc ? '<br />' . $desc : '';
							$html .= '</fieldset>';
							break;
						case 'checkbox':
							$esc_name = esc_attr( $option_name );
							$desc = $desc ? '<br />' . $desc : '';
							$attrs = '';
							if ( isset( $option_values[ 'attr' ] ) ) {
								$attrs = self::get_attributes_html( $option_values[ 'attr' ] );
							}
							$html = sprintf(
								'<p><label for="%s"><input name="%s[%s]" type="checkbox" id="%s" value="1"%s%s /> %s</label>%s</p>' ,
								$esc_name,
								UPL_OPTION_NAME,
								$esc_name,
								$esc_name,
								checked( $settings[ $option_name ], 1, false ),
								$attrs,
								esc_html( $option_values[ 'label' ] ),
								$desc
							);
							break;
						case 'selection':
							$html = '<p>';
							if ( empty( $option_values[ 'values' ] ) ) {
								$html .= sprintf( '%s: %s', esc_html( $option_values[ 'label' ] ), esc_html( $label_no_items ) );
							} else {
								if ( isset( $settings[ $option_name ] ) ) {
									if ( is_array( $settings[ $option_name ] ) ) {
										$stored_values = $settings[ $option_name ];
									} else {
										$stored_values = array ( $settings[ $option_name ] );
									}
								} else {
									$stored_values = $option_values[ 'default' ];
								}
								$esc_name = esc_attr( $option_name );
								$attrs = '';
								if ( isset( $option_values[ 'attr' ] ) ) {
									$attrs = self::get_attributes_html( $option_values[ 'attr' ] );
								}
								$html .= sprintf(
									'<label for="%s">%s <select id="%s" name="%s[%s][]"%s>',
									$esc_name,
									esc_html( $option_values[ 'label' ] ),
									$esc_name,
									UPL_OPTION_NAME,
									$esc_name,
									$attrs
								);
								foreach ( $option_values[ 'values' ] as $value => $label ) {
									if ( is_array( $label ) ) {
										$html .= sprintf(
											'<optgroup label="%s">',
											esc_attr( $value )
										);
										foreach( $label as $sub_value => $sub_label ) {
											$html .= sprintf(
												'<option value="%s"%s>%s</option>',
												esc_attr( $sub_value ),
												selected( in_array( $sub_value, $stored_values ), true, false ),
												esc_html( $sub_label )
											);
										}
										$html .= '</optgroup>';
									} else {
										$html .= sprintf(
											'<option value="%s"%s>%s</option>',
											esc_attr( $value ),
											selected( in_array( $value, $stored_values ), true, false ),
											esc_html( $label )
										);
									}
								}
								$html .= '</select></label>';
							}
							$html .= $desc ? '<br />' . $desc : '';
							$html .= '</p>';
							break;
						case 'url':
							$value = isset( $settings[ $option_name ] ) ? $settings[ $option_name ] : '';
							$attrs = '';
							if ( isset( $option_values[ 'attr' ] ) ) {
								$attrs = self::get_attributes_html( $option_values[ 'attr' ] );
							}
							$esc_name = esc_attr( $option_name );
							$html = sprintf(
								'<p><label for="%s">%s <input type="text" id="%s" name="%s[%s]" value="%s"%s></label>',
								$esc_name,
								esc_html( $option_values[ 'label' ] ),
								$esc_name,
								UPL_OPTION_NAME,
								$esc_name,
								esc_url( $value ),
								$attrs
							);
							$html .= $desc ? '<br />' . $desc : '';
							$html .= '</p>';
							break;
						case 'textarea':
							$value = isset( $settings[ $option_name ] ) ? $settings[ $option_name ] : '';
							$attrs = '';
							if ( isset( $option_values[ 'attr' ] ) ) {
								$attrs = self::get_attributes_html( $option_values[ 'attr' ] );
							}
							$esc_name = esc_attr( $option_name );
							$html = sprintf(
								'<p><label for="%s">%s<br /><textarea id="%s" name="%s[%s]"%s>%s</textarea></label>',
								$esc_name,
								esc_html( $option_values[ 'label' ] ),
								$esc_name,
								UPL_OPTION_NAME,
								$esc_name,
								$attrs,
								esc_textarea( $value )
							);
							$html .= $desc ? '<br />' . $desc : '';
							$html .= '</p>';
							break;
						case 'colorpicker':
							$value = isset( $settings[ $option_name ] ) ? $settings[ $option_name ] : '#cccccc';
							$attrs = '';
							if ( isset( $option_values[ 'attr' ] ) ) {
								$attrs = self::get_attributes_html( $option_values[ 'attr' ] );
							}
							$esc_name = esc_attr( $option_name );
							$html = sprintf(
								'<p><label for="%s">%s <input type="text" id="%s" class="wp-color-picker" name="%s[%s]" value="%s"%s></label>',
								$esc_name,
								esc_html( $option_values[ 'label' ] ),
								$esc_name,
								UPL_OPTION_NAME,
								$esc_name,
								esc_attr( $value ),
								$attrs
							);
							$html .= $desc ? '<br />' . $desc : '';
							$html .= '</p>';
							break;
						/*case 'button':
							break;*/
						case 'password':
							$value = isset( $settings[ $option_name ] ) ? $settings[ $option_name ] : '';
							$attrs = '';
							if ( isset( $option_values[ 'attr' ] ) ) {
								$attrs = self::get_attributes_html( $option_values[ 'attr' ] );
							}
							$esc_name = esc_attr( $option_name );
							$html = sprintf(
								'<p><label for="%s">%s <input type="password" id="%s" name="%s[%s]" value="%s"%s></label>',
								$esc_name,
								esc_html( $option_values[ 'label' ] ),
								$esc_name,
								UPL_OPTION_NAME,
								$esc_name,
								esc_attr( $value ),
								$attrs
							);
							$html .= $desc ? '<br />' . $desc : '';
							$html .= '</p>';
							break;
						// else text field or unknown fields
						default:
							$value = isset( $settings[ $option_name ] ) ? $settings[ $option_name ] : '';
							$attrs = '';
							if ( isset( $option_values[ 'attr' ] ) ) {
								$attrs = self::get_attributes_html( $option_values[ 'attr' ] );
							}
							$esc_name = esc_attr( $option_name );
							$html = sprintf(
								'<p><label for="%s">%s <input type="text" id="%s" name="%s[%s]" value="%s"%s></label>',
								$esc_name,
								esc_html( $option_values[ 'label' ] ),
								$esc_name,
								UPL_OPTION_NAME,
								$esc_name,
								esc_attr( $value ),
								$attrs
							);
							$html .= $desc ? '<br />' . $desc : '';
							$html .= '</p>';
					} // end switch()
					
					self::$options_rendered[ $chapter ][ $section_key ][ 'options' ][] = $html;

				} // end foreach( section_values )
			} // end foreach( section )
		} // end foreach( chapter )
		
	}

	/**
	* Print the rendered HTML code of the options
	*
	* @since	1.0.0
	*/
	public static function print_rendered_options ( $selected_chapter = null ) {

		$sections = self::$options_rendered;

		// return whole tree or part of it if defined, else empty array
		if ( $selected_chapter ) {
			if ( isset( self::$options_rendered[ $selected_chapter ] ) ) {
				$sections = self::$options_rendered[ $selected_chapter ];
			} else {
				echo '<p>No options (1).</p>';
			}
		}

		if ( $sections ) {
			foreach( $sections as $section_values ) {
				if ( ! is_array( $section_values ) or ! isset( $section_values[ 'options' ] ) ) {
					continue;
				}
				printf( "<h3>%s</h3>\n", $section_values[ 'headline' ] );
				if ( isset( $section_values[ 'description' ] ) ) {
					printf( "<p>%s</p>\n", $section_values[ 'description' ] );
				}
				foreach( $section_values[ 'options' ] as $option ) {
					printf( "%s\n", $option );
				}
			}
		} else {
			echo '<p>No options (2).</p>';
		}
	}

	/**
	* Check and return correct values for the settings
	*
	* @since	1.0.0
	* @param	array	$input	Options and their values after submitting the form
	* @return	array			Options and their sanatized values
	*/
	public static function sanitize_options ( $input ) {
		
		if ( empty( $input ) or ! is_array( $input ) ) {
			return self::get_default_settings();
		}
		if ( empty( self::$options_set ) ) {
			self::set_options();
		}
		
		foreach ( self::$options_set as $chapter => $sections ) {
			if ( ! is_array( $sections ) ) {
				continue;
			}
			foreach ( $sections as $section_name => $section_values ) {
				if ( ! is_array( $section_values ) or ! isset( $section_values[ 'options' ] ) ) {
					continue;
				}
				foreach ( $section_values[ 'options' ] as $option_name => $option_values ) {
					switch ( $option_values[ 'type' ] ) {
						// if radio button is set assign selected value, else default value
						case 'radiobuttons':
							$input[ $option_name ] = isset( $input[ $option_name ] ) ? $input[ $option_name ] : $option_values[ 'default' ];
							break;
						// if checkbox is set assign '1', else '0'
						case 'checkbox':
							if ( isset( $input[ $option_name ] ) ) {
								$input[ $option_name ] = ( 1 == $input[ $option_name ] ) ? 1 : 0 ;
							} else {
								$input[ $option_name ] = 0;
							}
							break;
						// if checkbox of a group of checkboxes is set assign '1', else '0'
						case 'checkboxes':
							foreach ( array_keys( $option_values[ 'values' ] ) as $option_name ) {
								//$input[ $option_name ] = isset( $input[ $option_name ] ) ? 1 : 0 ;
								if ( isset( $input[ $option_name ] ) ) {
									$input[ $option_name ] = ( 1 == $input[ $option_name ] ) ? 1 : 0 ;
								} else {
									$input[ $option_name ] = 0;
								}
							}
							break;
						// clean selection fields
						case 'selection':
							if ( isset( $input[ $option_name ] ) ) {
								if ( ! is_array( $input[ $option_name ] ) ) {
									$input[ $option_name ] = (array) $input[ $option_name ];
								}
								if ( 'format_date' == $option_name ) {
									if ( ! preg_match( '@^[DdFjlMmnYy ,./-]+$@', $input[ $option_name ][ 0 ] ) ) {
										if ( isset( $option_values[ 'default' ] ) ) {
											$input[ $option_name ] = $option_values[ 'default' ];
										} else {
											$input[ $option_name ] = array();
										}
									}
								} else {
									$input[ $option_name ] = array_map( 'sanitize_text_field', $input[ $option_name ] );
								}
							} else {
								$input[ $option_name ] = isset( $option_values[ 'default' ] ) ? $option_values[ 'default' ] : array();
							}
							break;
						// clean email value
						case 'email':
							if ( isset( $input[ $option_name ] ) ) {
								if ( '' == $input[ $option_name ] and isset( $option_values[ 'required' ] ) ) {
									$input[ $option_name ] = $option_values[ 'default' ];
								} else {
									$email = sanitize_email( $input[ $option_name ] );
									$input[ $option_name ] = is_email( $email ) ? $email : '';
								}
							} else {
								$input[ $option_name ] = isset( $option_values[ 'default' ] ) ? $option_values[ 'default' ] : '';
							}
							break;
						// clean url values
						case 'url':
							if ( isset( $input[ $option_name ] ) ) {
								if ( '' == $input[ $option_name ] and isset( $option_values[ 'required' ] ) ) {
									$input[ $option_name ] = $option_values[ 'default' ];
								} else {
									$input[ $option_name ] = esc_url_raw( $input[ $option_name ] );
								}
							} else {
								$input[ $option_name ] = isset( $option_values[ 'default' ] ) ? $option_values[ 'default' ] : '';
							}
							break;
						// clean positive integers
						case 'absint':
							if ( isset( $input[ $option_name ] ) ) {
								$input[ $option_name ] = absint( $input[ $option_name ] );
							} else {
								$input[ $option_name ] = isset( $option_values[ 'default' ] ) ? $option_values[ 'default' ] : 0;

							}
							break;
						// clean integers
						case 'int':
							if ( isset( $input[ $option_name ] ) ) {
								$input[ $option_name ] = intval( $input[ $option_name ] );
							} else {
								$input[ $option_name ] = isset( $option_values[ 'default' ] ) ? $option_values[ 'default' ] : 0;
							}
							break;
						// clean floating point numbers
						case 'float':
							if ( isset( $input[ $option_name ] ) ) {
								$input[ $option_name ] = floatval( $input[ $option_name ] );
							} else {
								$input[ $option_name ] = isset( $option_values[ 'default' ] ) ? $option_values[ 'default' ] : 0.0;
							}
							break;
						// clean hexadecimal color values
						case 'colorpicker':
							// 3 or 6 hex digits, or the empty string
							if ( ! isset( $input[ $option_name ] ) or '' == $input[ $option_name ] or ! preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $input[ $option_name ] ) ) {
								$input[ $option_name ] = '';
							}
							break;
						// clean dates in the format yyyy-mm-dd
						case 'date-yyyy-mm-dd':
							if ( ! isset( $input[ $option_name ] ) or '' == $input[ $option_name ] or ! preg_match('|^([0-9]{4})-([0-9]{2})-([0-9]{2})$|', $input[ $option_name ] ) ) {
								$input[ $option_name ] = '';
							}
							break;
						// clean all other form elements values
						default:
							if ( isset( $input[ $option_name ] ) ) {
								if ( '' == $input[ $option_name ] and isset( $option_values[ 'required' ] ) ) {
									$input[ $option_name ] = $option_values[ 'default' ];
								} else {
									// new since WP 4.7
									if ( 'textarea' == $option_values[ 'type' ] and function_exists( 'sanitize_textarea_field' ) ) {
										$input[ $option_name ] = sanitize_textarea_field( $input[ $option_name ] );
									} else {
										$input[ $option_name ] = sanitize_text_field( $input[ $option_name ] );
									}
								}
							} else {
								$input[ $option_name ] = isset( $option_values[ 'default' ] ) ? $option_values[ 'default' ] : '';
							}
					} // end switch( type )
					
					// do some special checks
					switch ( $option_name ) {
						case 'number_posts':
							if ( 0 == $input[ $option_name ] or ! is_int( $input[ $option_name ] ) ) {
								$input[ $option_name ] = get_option( 'posts_per_page', 7 );
							}
							break;
					} // end switch()
				} // foreach( options )
			} // foreach( sections )
		} // foreach( chapter )
		
		return $input;
		
	} // end sanitize_options()

	/**
	* Get texts and values for image sizes dropdown
	*
	* @since	1.0.0
	* @return	array			Options and their sanatized values
	*/
	private static function get_image_size_options () {

		global $_wp_additional_image_sizes;
		$wp_standard_image_size_labels = array();
		$label = 'Full Size';	$wp_standard_image_size_labels[ 'full' ]		= __( $label );
		$label = 'Large';		$wp_standard_image_size_labels[ 'large' ]		= __( $label );
		$label = 'Medium';		$wp_standard_image_size_labels[ 'medium' ]		= __( $label );
		$label = 'Thumbnail';	$wp_standard_image_size_labels[ 'thumbnail' ]	= __( $label );
		
		$wp_standard_image_size_names = array_keys( $wp_standard_image_size_labels );
		$defaults[ 'size_options' ] = array(
			'custom' => __( 'Specified width and height', 'ultimate-post-list' ),
		);

		foreach ( get_intermediate_image_sizes() as $defaults[ 'size_name' ] ) {
			// Don't take numeric sizes that appear
			if( is_integer( $defaults[ 'size_name' ] ) ) {
				continue;
			}

			// Set name
			$name = in_array( $defaults[ 'size_name' ], $wp_standard_image_size_names ) ? $wp_standard_image_size_labels[$defaults[ 'size_name' ]] : $defaults[ 'size_name' ];
			
			// Set width
			$width = isset( $_wp_additional_image_sizes[$defaults[ 'size_name' ]]['width'] ) ? $_wp_additional_image_sizes[$defaults[ 'size_name' ]]['width'] : get_option( "{$defaults[ 'size_name' ]}_size_w" );
			
			// Set height
			$height = isset( $_wp_additional_image_sizes[$defaults[ 'size_name' ]]['height'] ) ? $_wp_additional_image_sizes[$defaults[ 'size_name' ]]['height'] : get_option( "{$defaults[ 'size_name' ]}_size_h" );
			
			// add option to options list
			$defaults[ 'size_options' ][ $defaults[ 'size_name' ] ] = sprintf( '%s (%d &times; %d)', esc_html( $name ), absint( $width ), absint( $height ) );
			
		}
		
		return $defaults[ 'size_options' ];
	}
		
	/**
	* Get texts and values for categories dropdown
	*
	* @since	4.2.0
	* @return	array			Options and their sanatized values
	*/
	private static function get_categories () {

		// get categories
		$terms = get_categories( array( 'hide_empty' => 0, 'hierarchical' => 1 ) );
		
		// make selection box entries
		if ( 0 < count( $terms ) ) {
			return self::get_terms_hierarchy( $terms );
		} else {
			return array();
		}

	}
		
	/**
	 * Return options list of selection fields for the positions of each post data
	 *
	 * @access   private
	 * @since    5.0.0
	 *
	 * @return    array   list of selection fields
	 */
	private static function get_position_options( $positions ) {
		$order_range = array();
		foreach ( range( 1, count( $positions ) ) as $i ) {
			$order_range[ $i ] = $i;
		}		
		$position_options = array();
		$i = 1;
		foreach ( $positions as $key => $label ) {
			$position_options[ $key ] = array(
				'type' => 'selection',
				'label' => $label,
				'values' => $order_range,
				'default' => array( $i ),
			);
			$i++;
		}
		return $position_options;
	}

	/**
	* Get texts and values for hierarchical terms dropdown
	*
	* @since	4.2.0
	* @return	array			Options and their sanatized values
	*/
	private static function get_terms_hierarchy ( $terms ) {

		$terms_list = array();
		$terms_hierarchy = array();
		
		// make a hierarchical list of tags
		while ( $terms ) {
			// go on with the first element in the tags list:
			// if there is no parent
			if ( 0 == $terms[ 0 ]->parent ) {
				// get and remove it from the tags list
				$current_entry = array_shift( $terms );
				// append the current entry to the new list
				$terms_list[] = array(
					'id'	=> $current_entry->term_id,
					'name'	=> $current_entry->name,
					'depth'	=> 0
				);
				// go on looping
				continue;
			}
			// if there is a parent:
			// try to find parent in new list and get its array index
			$parent_index = self::get_term_parent_index( $terms_list, $terms[ 0 ]->parent );
			// if parent is not yet in the new list: try to find the parent later in the loop
			if ( false === $parent_index ) {
				// get and remove current entry from the tags list
				$current_entry = array_shift( $terms );
				// append it at the end of the tags list
				$terms[] = $current_entry;
				// go on looping
				continue;
			}
			// if there is a parent and parent is in new list:
			// set depth of current item: +1 of parent's depth
			$depth = $terms_list[ $parent_index ][ 'depth' ] + 1;
			// set new index as next to parent index
			$new_index = $parent_index + 1;
			// find the correct index where to insert the current item
			foreach( $terms_list as $entry ) {
				// if there are items with same or higher depth than current item
				if ( $depth <= $entry[ 'depth' ] ) {
					// increase new index
					$new_index = $new_index + 1;
					// go on looping in foreach()
					continue;
				}
				// if the correct index is found:
				// get current entry and remove it from the tags list
				$current_entry = array_shift( $terms );
				// insert current item into the new list at correct index
				$end_array = array_splice( $terms_list, $new_index ); // $terms_list is changed, too
				$terms_list[] = array(
					'id'	=> absint( $current_entry->term_id ),
					'name'	=> $current_entry->name,
					'depth'	=> $depth
				);
				$terms_list = array_merge( $terms_list, $end_array );
				// quit foreach(), go on while-looping
				break;
			} // foreach( terms_list )
		} // while( tags )

		foreach ( $terms_list as $term ) {
			$pad = ( 0 < $term[ 'depth' ] ) ? str_repeat('&ndash;&nbsp;', $term[ 'depth' ] ) : '';
			$terms_hierarchy[ $term[ 'id' ] ] = $pad . $term[ 'name' ];
		}
			
		return $terms_hierarchy;
	}
		
	/**
	 * Return the array index of a given ID
	 *
	 * @since	4.2.0
	 */
	private static function get_term_parent_index( $arr, $id ) {
		$len = count( $arr );
		if ( 0 == $len ) {
			return false;
		}
		$id = absint( $id );
		for ( $i = 0; $i < $len; $i++ ) {
			if ( $id == $arr[ $i ][ 'id' ] ) {
				return $i;
			}
		}
		return false; 
	}
	
	/**
	 * Return the number of display rows (size) of a selection field
	 *
	 * @since 4.2.0
	 */
	private static function get_selection_size( $length ) {
		
		$min_size = 7;
		
		// check length; if < $min_size return as is, else 3rd part rounded up, at leat $min_size
		if ( $length < $min_size ) {
			return $length;
		} else {
			$size = ceil( $length / 3 );
			if ( $size < $min_size ) {
				return $min_size;
			} else {
				return $size;
			}
		}
		
	}
	
	/**
	 * Return the string of attributes of an HTML element
	 *
	 * @since 5.1.2
	 */
	private static function get_attributes_html ( $attributes ) {
		$html = '';
		foreach ( $attributes as $attr ) {
			if ( is_array( $attr ) ) {
				foreach( $attr as $key => $value ) {
					$html .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
				}
			} else {
				$html .= ' ' . esc_attr( $attr );
			}
		}
		return $html;
	}

	/**
	 * Insert a new entry as the first entry of an associative array
	 *
	 * @since 4.2.0
	 */
	private static function array_unshift_assoc( $arr, $key, $val ) {
		$arr = array_reverse($arr, true);
		$arr[$key] = $val;
		return array_reverse($arr, true);
	}

}