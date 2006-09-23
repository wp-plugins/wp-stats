<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.0 Plugin: WP-Stats 2.05										|
|	Copyright (c) 2005 Lester "GaMerZ" Chan									|
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
	$update_stats_text[] = __('Stats URL');
	$update_stats_text[] = __('Stats Most Limit');
	$update_stats_text[] = __('Stats Display Options');
	$i=0;
	$text = '';
	foreach($update_stats_queries as $update_stats_query) {
		if($update_stats_query) {
			$text .= '<font color="green">'.$update_stats_text[$i].' '.__('Updated').'</font><br />';
		}
		$i++;
	}
	if(empty($text)) {
		$text = '<font color="red">'.__('No Stats Option Updated').'</font>';
	}
}
?>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<div class="wrap"> 
	<h2><?php _e('Stats Options'); ?></h2> 
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>"> 
		<fieldset class="options">
			<legend><?php _e('Stats Options'); ?></legend>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
				 <tr valign="top">
					<th align="left" width="30%"><?php _e('Stats URL'); ?></th>
					<td align="left">
						<input type="text" name="stats_url" value="<?php echo get_settings('stats_url'); ?>" size="50" /><br />URL To Stats Page.<br />Example: http://www.yoursite.com/blogs/stats/<br />Example: http://www.yoursite.com/blogs/?page_id=2
					</td>
				</tr>
				 <tr valign="top">
					<th align="left" width="30%"><?php _e('Stats Most Limit'); ?></th>
					<td align="left">
						<input type="text" name="stats_mostlimit" value="<?php echo $stats_mostlimit ?>" size="2" /><br />Top X Stats, where X is the most limit.
					</td>
				</tr>
				 <tr valign="top">
					<th align="left" width="30%"><?php _e('Type Of Stats To Display'); ?></th>
					<td align="left">
						<p><strong>General Stats</strong></p>
						<input type="checkbox" name="stats_display[]" value="total_stats"<?php checked(1, $stats_display['total_stats']); ?> />&nbsp;&nbsp;Total<br />
						<p><strong>Plugin Stats</strong></p>
						<?php 
							if(function_exists('wp_email')) {
								echo '<input type="checkbox" name="stats_display[]" value="email"';
								checked(1, $stats_display['email']);
								echo ' />&nbsp;&nbsp;WP-EMail<br />'."\n";
							}
							if(function_exists('get_poll')) {
								echo '<input type="checkbox" name="stats_display[]" value="polls"';
								checked(1, $stats_display['polls']);
								echo ' />&nbsp;&nbsp;WP-Polls<br />'."\n";
							}
							if(function_exists('the_ratings')) {
								echo '<input type="checkbox" name="stats_display[]" value="ratings"';
								checked(1, $stats_display['ratings']);
								echo ' />&nbsp;&nbsp;WP-PostRatings<br />'."\n";
							}
							if(function_exists('the_views')) {
								echo '<input type="checkbox" name="stats_display[]" value="views"';
								checked(1, $stats_display['views']);
								echo ' />&nbsp;&nbsp;WP-PostViews<br />'."\n";
							}
							if(function_exists('useronline')) {
								echo '<input type="checkbox" name="stats_display[]" value="useronline"';
								checked(1, $stats_display['views']);
								echo ' />&nbsp;&nbsp;WP-UserOnline<br />'."\n";
							}
						?>
						<p><strong>Top <?php echo get_settings('stats_mostlimit'); ?> Stats</strong></p>
						<input type="checkbox" name="stats_display[]" value="recent_posts"<?php checked(1, $stats_display['recent_posts']); ?> />&nbsp;&nbsp;<?php echo $stats_mostlimit ?> Most Recent Posts<br />
						<input type="checkbox" name="stats_display[]" value="recent_commtents"<?php checked(1, $stats_display['recent_commtents']); ?> />&nbsp;&nbsp;<?php echo $stats_mostlimit ?> Most Recent Comments<br />
						<input type="checkbox" name="stats_display[]" value="commented_post"<?php checked(1, $stats_display['commented_post']); ?> />&nbsp;&nbsp;<?php echo $stats_mostlimit ?> Most Commented Posts<br />
						<?php 
							if(function_exists('wp_email')) {
								echo '<input type="checkbox" name="stats_display[]" value="emailed_most"';
								checked(1, $stats_display['emailed_most']);
								echo ' />&nbsp;&nbsp;'.$stats_mostlimit.' Most Emailed Posts<br />'."\n";
							}
							if(function_exists('the_ratings')) {
								echo '<input type="checkbox" name="stats_display[]" value="rated_highest"';
								checked(1, $stats_display['rated_highest']);
								echo ' />&nbsp;&nbsp;'.$stats_mostlimit.' Highest Rated Posts<br />'."\n";
								echo '<input type="checkbox" name="stats_display[]" value="rated_most"';
								checked(1, $stats_display['rated_most']);
								echo ' />&nbsp;&nbsp;'.$stats_mostlimit.' Most Rated Posts<br />'."\n";
							}
							if(function_exists('the_views')) {
								echo '<input type="checkbox" name="stats_display[]" value="viewed_most"';
								checked(1, $stats_display['viewed_most']);
								echo ' />&nbsp;&nbsp;'.$stats_mostlimit.' Most Viewed Posts<br />'."\n";
							}
						?>
						<p><strong>Authors Stats</strong></p>
						<input type="checkbox" name="stats_display[]" value="authors"<?php checked(1, $stats_display['authors']); ?> />&nbsp;&nbsp;Authors<br />
						<p><strong>Comments' Members Stats</strong></p>
						<input type="checkbox" name="stats_display[]" value="comment_members"<?php checked(1, $stats_display['comment_members']); ?> />&nbsp;&nbsp;Comment Members<br />
						<p><strong>Misc Stats</strong></p>
						<input type="checkbox" name="stats_display[]" value="post_cats"<?php checked(1, $stats_display['post_cats']); ?> />&nbsp;&nbsp;Post Categories<br />
						<input type="checkbox" name="stats_display[]" value="link_cats"<?php checked(1, $stats_display['link_cats']); ?> />&nbsp;&nbsp;Link Categories<br />
					</td>
				</tr>
			</table>
		</fieldset>
		<div align="center">
			<input type="submit" name="Submit" class="button" value="<?php _e('Update Options'); ?>" />&nbsp;&nbsp;<input type="button" name="cancel" value="Cancel" class="button" onclick="javascript:history.go(-1)" /> 
		</div>
	</form> 
</div> 