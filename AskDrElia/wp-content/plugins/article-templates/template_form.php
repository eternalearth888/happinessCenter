<div class="wrap">
<h2><?php echo __(ucfirst($_REQUEST['action']) . " Template"); ?></h2>

<?php
$template_id= 0;
$title		= '';
$content	= '';

//Edit an existing template
if($_REQUEST['action'] == 'edit' and isset($_REQUEST['template']) and is_numeric($_REQUEST['template'])) {
	$template_id = $_REQUEST['template'];
	$template_data = $wpdb->get_results("SELECT ID,post_title,post_name,post_content,menu_order FROM {$wpdb->posts} "
				. " WHERE ID='$template_id' AND post_type='template' AND post_author='{$current_user->ID}'");
				
	$title	= $template_data[0]->post_title;
	$name	= $template_data[0]->post_name;
	$content= $template_data[0]->post_content;
	$default_template= $template_data[0]->menu_order;
}

wp_enqueue_script( 'prototype' );
if ( user_can_richedit() )
	wp_enqueue_script('editor');
wp_enqueue_script('media-upload');
wp_print_scripts();

?>

<form name="post" action="edit.php?page=article-templates/manage.php" method="post" id="post">
<div id="poststuff">
<div id="titlediv">
<h3><?php _e('Template Title') ?></h3>

<div id="titlewrap"><input type="text" name="post_title" size="30" tabindex="1" value="<?php echo attribute_escape( $title ); ?>" id="title" /></div>
</div>

<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea">
<h3><?php _e('Template Content') ?>
<?php if ( 'edit' == $action and $template_id) { ?>
<a href="<?php echo clean_url(apply_filters('preview_post_link', add_query_arg('preview', 'true', get_permalink($template_id)))); ?>" style="position: absolute; right: 2em; margin-right: 19em; text-decoration: underline;" target="_blank"><?php _e('Preview &raquo;'); ?></a>
<?php } ?></h3>

	<?php the_editor($content); ?>
</div>

<p><legend><?php _e('Default Template') ?></legend>
<input type="checkbox" name="menu_order" value="1" <?=($default_template)?"checked='checked'":''?> /></p>

<p class="submit">
<input type="hidden" value="" name="post_name" />
<input type="hidden" name="action" value="<?=stripslashes($_REQUEST['action'])?>" />
<input type="hidden" name="post_ID" value="<?=stripslashes($_REQUEST['template'])?>" />
<input type="hidden" name="saveasprivate" value="1" />
<input type="hidden" name="post_type" value="template" />
<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID ?>" />

<span id="autosave"></span>
<input type="submit" name="submit" value="<?php _e('Save') ?>" style="font-weight: bold;" tabindex="4" />
</p>

<p><?php do_action('edit_page_form'); ?></p>
</div>
</form>

</div>
