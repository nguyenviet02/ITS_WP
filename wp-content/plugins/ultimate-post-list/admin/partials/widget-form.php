<h4><?php esc_html_e( 'List Selection:', 'ultimate-post-list' ); ?></h4>
<?php #printf( '<pre>$lists = %s</pre>', var_export( get_categories( array( 'hide_empty' => 0, 'hierarchical' => 1 ) ), true ) ); ?>
<p><label><?php $text = 'Title:'; esc_html_e( $text ); ?>
<input class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $widget_title ); ?>" /></label></p>

<p><label><?php esc_html_e( 'List to display:', 'ultimate-post-list' ); ?>
	<select name="<?php echo $this->get_field_name( 'list_id' ); ?>">
		<option value="0" <?php selected( $list_id, 0 ); ?>><?php $text = '&mdash; Select &mdash;';  esc_html_e( $text ); ?></option>
<?php
// Display the sizes in the array
foreach ( $lists as $list ) {
?>
		<option value="<?php echo esc_attr( $list->ID ); ?>"<?php selected( $list_id, $list->ID ); ?>><?php echo esc_html( $list->post_title ); ?></option>
<?php
} // end foreach(option)
?>
	</select></label><br />
	<em><?php esc_html_e( 'Select a post list. Only published lists are listed. The widget is visible in the website only a list was selected. If no list is displayed on the website please check: Was the list published? Is the list in the trash? Does the list exist at all?', 'ultimate-post-list' ); ?></em>
</p>

<p><?php
$link = sprintf( '<a href="https://wordpress.org/support/view/plugin-reviews/ultimate-post-list" target="_blank">%s</a>', esc_html__( 'Reviews at wordpress.org', 'ultimate-post-list' ) );
esc_html_e( 'Do you like the plugin?', 'ultimate-post-list' );
printf( esc_html__( 'Rate it on %s.', 'ultimate-post-list' ), $link );
?></p>
