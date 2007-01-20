<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.1 Plugin: WP-Stats 2.1	0 										|
|	Copyright (c) 2007 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://www.lesterchan.net													|
|																							|
|	File Information:																	|
|	- WordPress Statistics															|
|	- wp-content/plugins/stats/stats-options.php							|
|																							|
+----------------------------------------------------------------+
*/


### Variables Variables Variables
$base_name = plugin_basename('stats/stats-options.php');
$base_page = 'admin.php?page='.$base_name;
$stats_mostlimit = intval(get_settings('stats_mostlimit'));
$stats_display = get_settings('stats_display');

### If Form Is Submitted
if($_POST['Submit']) {
	$stats_url = addslashes(trim($_POST['stats_url']));
	$stats_mostlimit = intval(trim($_POST['stats_mostlimit']));
	$stats_display = $_POST['stats_display'];
	if($stats_display) {
		foreach($stats_display as $stat_display) {
			$stat_display = addslashes($stat_display);
			$stats_display_array[$stat_display] = 1;
		}
	}
	$stats_display = $stats_display_array;
	$update_stats_queries = array();
	$update_stats_text = array();
	$update_stats_queries[] = update_option('stats_url', $stats_url);
	$update_stats_queries[] = update_option('stats_mostlimit', $stats_mostlimit);
	$update_stats_queries[] = update_option('stats_display', $stats_display);
	$update_stats_text[] = __('Stats URL', 'wp-stats');
	$update_stats_text[] = __('Stats Most Limit', 'wp-stats');
	$update_stats_text[] = __('Stats Display Options', 'wp-stats');
	$i=0;
	$text = '';
	foreach($update_stats_queries as $update_stats_query) {
		if($update_stats_query) {
			$text .= '<font color="green">'.$update_stats_text[$i].' '.__('Updated', 'wp-stats').'</font><br />';
		}
		$i++;
	}
	if(empty($text)) {
		$text = '<font color="red">'.__('No Stats Option Updated', 'wp-stats').'</font>';
	}
}
?>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<div class="wrap"> 
	<h2><?php _e('Stats Options', 'wp-stats'); ?></h2> 
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>"> 
		<fieldset class="options">
			<legend><?php _e('Stats Options', 'wp-stats'); ?></legend>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
				 <tr valign="top">
					<th align="left" width="30%"><?php _e('Stats URL', 'wp-stats'); ?></th>
					<td align="left">
						<input type="text" name="stats_url" value="<?php echo get_settings('stats_url'); ?>" size="50" /><br /><?php _e('URL To Stats Page.<br />Example: http://www.yoursite.com/blogs/stats/<br />Example: http://www.yoursite.com/blogs/?page_id=2', 'wp-stats'); ?>
					</td>
				</tr>
				 <tr valign="top">
					<th align="left" width="30%"><?php _e('Stats Most Limit', 'wp-stats'); ?></th>
					<td align="left">
						<input type="text" name="stats_mostlimit" value="<?php echo $stats_mostlimit ?>" size="2" /><br /><?php _e('Top X Stats, where X is the most limit.', 'wp-stats'); ?>
					</td>
				</tr>
				 <tr valign="top">
					<th align="left" width="30%"><?php _e('Type Of Stats To Display', 'wp-stats'); ?></th>
					<td align="left">
						<p><strong><?php _e('General Stats', 'wp-stats'); ?></strong></p>
						<input type="checkbox" name="stats_display[]" value="total_stats"<?php checked(1, $stats_display['total_stats']); ?> />&nbsp;&nbsp;Total<br />
						<p><strong><?php _e('Plugin Stats', 'wp-stats'); ?></strong></p>
						<?php 
							if(function_exists('wp_email')) {
								echo '<input type="checkbox" name="stats_display[]" value="email"';
								checked(1, $stats_display['email']);
								echo ' />&nbsp;&nbsp;'.__('WP-EMail', 'wp-stats').'<br />'."\n";
							}
							if(function_exists('get_poll')) {
								echo '<input type="checkbox" name="stats_display[]" value="polls"';
								checked(1, $stats_display['polls']);
								echo ' />&nbsp;&nbsp;'.__('WP-Polls', 'wp-stats').'<br />'."\n";
							}
							if(function_exists('the_ratings')) {
								echo '<input type="checkbox" name="stats_display[]" value="ratings"';
								checked(1, $stats_display['ratings']);
								echo ' />&nbsp;&nbsp;'.__('WP-PostRatings', 'wp-stats').'<br />'."\n";
							}
							if(function_exists('the_views')) {
								echo '<input type="checkbox" name="stats_display[]" value="views"';
								checked(1, $stats_display['views']);
								echo ' />&nbsp;&nbsp;'.__('WP-PostViews', 'wp-stats').'<br />'."\n";
							}
							if(function_exists('useronline')) {
								echo '<input type="checkbox" name="stats_display[]" value="useronline"';
								checked(1, $stats_display['views']);
								echo ' />&nbsp;&nbsp;'.__('WP-UserOnline', 'wp-stats').'<br />'."\n";
							}
						?>
						<p><strong><?php printf(__('Top %s Post Stats', 'wp-stats'), get_settings('stats_mostlimit')); ?></strong></p>
						<input type="checkbox" name="stats_display[]" value="recent_posts"<?php checked(1, $stats_display['recent_posts']); ?> />&nbsp;&nbsp;<?php echo $stats_mostlimit ?> <?php _e('Most Recent Posts', 'wp-stats'); ?><br />
						<input type="checkbox" name="stats_display[]" value="recent_commtents"<?php checked(1, $stats_display['recent_commtents']); ?> />&nbsp;&nbsp;<?php echo $stats_mostlimit ?> <?php _e('Most Recent Comments', 'wp-stats'); ?><br />
						<input type="checkbox" name="stats_display[]" value="commented_post"<?php checked(1, $stats_display['commented_post']); ?> />&nbsp;&nbsp;<?php echo $stats_mostlimit ?> <?php _e('Most Commented Posts', 'wp-stats'); ?><br />
						<?php 
							if(function_exists('wp_email')) {
								echo '<input type="checkbox" name="stats_display[]" value="emailed_most"';
								checked(1, $stats_display['emailed_most']);
								echo ' />&nbsp;&nbsp;'.$stats_mostlimit.' '.__('Most Emailed Posts', 'wp-stats').'<br />'."\n";
							}
							if(function_exists('the_ratings')) {
								echo '<input type="checkbox" name="stats_display[]" value="rated_highest"';
								checked(1, $stats_display['rated_highest']);
								echo ' />&nbsp;&nbsp;'.$stats_mostlimit.' '.__('Highest Rated Posts', 'wp-stats').'<br />'."\n";
								echo '<input type="checkbox" name="stats_display[]" value="rated_most"';
								checked(1, $stats_display['rated_most']);
								echo ' />&nbsp;&nbsp;'.$stats_mostlimit.' '.__('Most Rated Posts', 'wp-stats').'<br />'."\n";
							}
							if(function_exists('the_views')) {
								echo '<input type="checkbox" name="stats_display[]" value="viewed_most"';
								checked(1, $stats_display['viewed_most']);
								echo ' />&nbsp;&nbsp;'.$stats_mostlimit.' '.__('Most Viewed Posts', 'wp-stats').'<br />'."\n";
							}
						?>
						<p><strong><?php _e('Authors Stats', 'wp-stats'); ?></strong></p>
						<input type="checkbox" name="stats_display[]" value="authors"<?php checked(1, $stats_display['authors']); ?> />&nbsp;&nbsp;<?php _e('Authors', 'wp-stats'); ?><br />
						<p><strong><?php _e('Comments\' Members Stats', 'wp-stats'); ?></strong></p>
						<input type="checkbox" name="stats_display[]" value="comment_members"<?php checked(1, $stats_display['comment_members']); ?> />&nbsp;&nbsp;<?php _e('Comment Members', 'wp-stats'); ?><br />
						<p><strong><?php _e('Misc Stats', 'wp-stats'); ?></strong></p>
						<input type="checkbox" name="stats_display[]" value="post_cats"<?php checked(1, $stats_display['post_cats']); ?> />&nbsp;&nbsp;<?php _e('Post Categories', 'wp-stats'); ?><br />
						<input type="checkbox" name="stats_display[]" value="link_cats"<?php checked(1, $stats_display['link_cats']); ?> />&nbsp;&nbsp;<?php _e('Link Categories', 'wp-stats'); ?><br />
					</td>
				</tr>
			</table>
		</fieldset>
		<div align="center">
			<input type="submit" name="Submit" class="button" value="<?php _e('Update Options'); ?>" />&nbsp;&nbsp;<input type="button" name="cancel" value="Cancel" class="button" onclick="javascript:history.go(-1)" /> 
		</div>
	</form> 
</div> 