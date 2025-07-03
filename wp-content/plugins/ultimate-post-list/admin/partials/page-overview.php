<?php

/**
 * Provide a page to add a new post list
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.kybernetik-services.com
 * @since      1.0.0
 *
 * @package    Ultimate_Post_List
 * @subpackage Ultimate_Post_List/admin/partials
 */

// get URL of image folder once
$image_root_url = dirname( plugin_dir_url( __FILE__ ) ) . '/images/';

// translate strings of WP core
$text = 'Widgets';
$widgets_link = sprintf(
	'<a href="%s">%s</a>',
	esc_url( admin_url( 'widgets.php^' ) ),
	esc_html__( $text )
); 
$text = 'Example: Nifty blogging software';
$title_text = esc_html__( $text );
$text = 'News';
$news_text = esc_html__( $text );
$text = 'Tools';
$text_tools = __( $text );
$text = 'Export';
$text_export = __( $text );
$text = 'Import';
$text_import = __( $text );
$text = 'Choose what to export';
$text_choose = esc_html__( $text );
$text = 'Download Export File';
$text_download = esc_html__( $text );

// translate multiple used strings
$example_text = esc_html__( 'example:', 'ultimate-post-list' );

// set export target text based on users capatibility
$text_target = sprintf(
	'%s &gt; %s',
	$text_tools,
	$text_export
);
if ( current_user_can( 'export' ) ) {
	$text_target_export = sprintf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'export.php' ) ),
		esc_html( $text_target )
	);
} else {
	$text_target_export = esc_html( $text_target );
}

// set import target text based on users capatibility
$text_target = sprintf(
	'%s &gt; %s',
	$text_tools,
	$text_import
);
if ( current_user_can( 'import' ) ) {
	$text_target_import = sprintf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'import.php' ) ),
		esc_html( $text_target )
	);
} else {
	$text_target_import = esc_html( $text_target );
}

?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<div class="upl_wrapper">
		<div id="upl_main">
			<div class="upl_content">
				<h2><?php esc_html_e( 'How to use the generator', 'ultimate-post-list' ); ?></h2>
				<ol>
					<li><?php
					printf(
						'<a href="%s">%s</a>', 
						esc_url( admin_url( $this->post_type_new ) ),
						esc_html__( 'Create a new list.', 'ultimate-post-list' )
					); ?></li>
					<li><?php esc_html_e( 'After you published the new list you can use it both as a widget and as content in a post via the shortcode.', 'ultimate-post-list' ); ?></li>
				</ol>
				<h2><?php esc_html_e( 'How to use the list as a widget', 'ultimate-post-list' ); ?></h2>
				<p><?php printf( esc_html__( 'Go to the page %s, move the widget "%s" into the desired area and select the list you want to see in the website.', 'ultimate-post-list' ), $widgets_link, esc_html__( 'Ultimate Post List', 'ultimate-post-list' ) ); ?></p>
				<p><?php esc_html_e( 'You can add a title for the widget to show it on the website. Save the widget. The selected list is displayed on the website immediately.', 'ultimate-post-list' ); ?></p>
				<h2><?php esc_html_e( 'Shortcode Attributes', 'ultimate-post-list' ); ?></h2>
				<h3><?php esc_html_e( 'How to use the shortcode', 'ultimate-post-list' ); ?></h3>
				<div id="screenshot-shortcode-box"><img src="<?php echo $image_root_url; ?>screenshot-shortcode-box.gif" alt="<?php esc_attr_e( 'Screenshot of the Shortcode box', 'ultimate-post-list' ); ?>" width="283" height="118"><br><em><?php esc_html_e( 'Screenshot of the Shortcode box', 'ultimate-post-list'); ?></em></div>
				<p><?php esc_html_e( 'Copy the shortcode in the Shortcode box and insert it at the desired place in the content.', 'ultimate-post-list' ); ?></p>
				<p><?php esc_html_e( 'You can find the shortcode both in the table list of all post lists and in the Shortcode column and in the Shortcode box on the edit page of each list in the Shortcode box.', 'ultimate-post-list' ); ?></p>
				<p><?php esc_html_e( 'You can add some attributes to overwrite the respective settings of the list.', 'ultimate-post-list' ); ?></p>
				<dl class="atts-list">
					<dt>list_title</dt>
					<dd><?php esc_html_e( 'Sets the headline of the list. To remove a headline use an empty string.', 'ultimate-post-list' ); ?></dd>
					<dd><?php echo $example_text; ?> <code>list_title="<?php echo $title_text; ?>"</code></dd>
					<dd><?php echo $example_text; ?> <code>list_title=""</code></dd>
					<dt>included_categories</dt>
					<dd><?php esc_html_e( 'Displays only posts of the categories specified by their IDs, slugs or names, separated by commas.', 'ultimate-post-list' ); ?></dd>
					<dd><?php echo $example_text; ?> <code>included_categories="323,245,788"</code></dd>
					<dd><?php echo $example_text; ?> <code>included_categories="lorem-ipsum,fringilla-mauris,dolor-sit-amet"</code></dd>
					<dd><?php echo $example_text; ?> <code>included_categories="Lorem ipsum,Fringilla mauris,Dolor sit amet"</code></dd>
					<dd><?php esc_html_e( 'You can use specifiers of different types in a comma-separated list.', 'ultimate-post-list' ); ?></dd>
					<dd><?php echo $example_text; ?> <code>included_categories="Lorem ipsum,245,dolor-sit-amet"</code></dd>
				</dl>
				<h3><?php esc_html_e( 'Example of a shortcode with attributes', 'ultimate-post-list'); ?></h3>
				<p><?php esc_html_e( 'This shortcode prints the list of ID 48, shows only posts of the specified category with a new headline.', 'ultimate-post-list' ); ?></p>
				<p><code>[ultimate-post-list id="48" list_title="<?php echo $title_text; ?>" included_categories="<?php echo $news_text; ?>"]</code></p>
				<h2><?php esc_html_e( 'Why do I not see the list?', 'ultimate-post-list' ); ?></h2>
				<p><?php esc_html_e( 'You can use only published post lists as a widget or via the shortcode. If a formerly published list would be set to draft or deleted there would be no output. Not even an error message, fortunately. The widget or the shortcode remains in the widget area or in the text editor until you remove it.', 'ultimate-post-list' ); ?></p>
				<h2><?php esc_html_e( 'Export and Import of lists', 'ultimate-post-list' ); ?></h2>
				<p><?php printf(
					esc_html__( 'To export all Ultimate Post Lists in a XML file go to %s. Go to the section "%s", select the option "%s" and click on the button "%s".', 'ultimate-post-list' ),
					$text_target_export,
					$text_choose,
					esc_html__( 'Ultimate Post Lists', 'ultimate-post-list' ),
					$text_download
				); ?></p>
				<p><?php printf(
					esc_html__( 'To import all Ultimate Post Lists in the XML file go to %s and follow the instructions on that page.', 'ultimate-post-list' ),
					$text_target_import
				); ?></p>
				<h2><?php esc_html_e( 'Do you need more options?', 'ultimate-post-list' ); ?></h2>
				<p><a href="https://www.kybernetik-services.com/shop/wordpress/plugin/ultimate-post-list-pro/?utm_source=wordpress_org&utm_medium=plugin&utm_campaign=ultimate-post-list&utm_content=update-notice" target="_blank"><?php esc_html_e( 'Get the Pro version.', 'ultimate-post-list' ); ?></a></p>

<?php include_once( 'section-footer.php' ); ?>