<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.1 Plugin: WP-Stats 2.20										|
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
$mode = trim($_GET['mode']);
$stats_settings = array('stats_mostlimit', 'stats_display', 'stats_url', 'widget_stats');


### Form Processing 
if(!empty($_POST['do'])) {
	// Decide What To Do
	switch($_POST['do']) {
		case __('Update Options', 'wp-stats'):
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
			break;
		// Uninstall WP-Stats
		case __('UNINSTALL WP-Stats', 'wp-stats') :
			if(trim($_POST['uninstall_stats_yes']) == 'yes') {
				echo '<div id="message" class="updated fade">';
				echo '<p>';
				foreach($stats_settings as $setting) {
					$delete_setting = delete_option($setting);
					if($delete_setting) {
						echo '<font color="green">';
						printf(__('Setting Key \'%s\' has been deleted.', 'wp-stats'), "<strong><em>{$setting}</em></strong>");
						echo '</font><br />';
					} else {
						echo '<font color="red">';
						printf(__('Error deleting Setting Key \'%s\'.', 'wp-stats'), "<strong><em>{$setting}</em></strong>");
						echo '</font><br />';
					}
				}
				echo '</p>';
				echo '</div>'; 
				$mode = 'end-UNINSTALL';
			}
			break;
	}
}


### Determines Which Mode It Is
switch($mode) {
		//  Deactivating WP-Stats
		case 'end-UNINSTALL':
			$deactivate_url = 'plugins.php?action=deactivate&amp;plugin=stats/stats.php';
			if(function_exists('wp_nonce_url')) { 
				$deactivate_url = wp_nonce_url($deactivate_url, 'deactivate-plugin_stats/stats.php');
			}
			echo '<div class="wrap">';
			echo '<h2>'.__('Uninstall WP-Stats', 'wp-stats').'</h2>';
			echo '<p><strong>'.sprintf(__('<a href="%s">Click Here</a> To Finish The Uninstallation And WP-Stats Will Be Deactivated Automatically.', 'wp-stats'), $deactivate_url).'</strong></p>';
			echo '</div>';
			break;
	// Main Page
	default:
		$stats_mostlimit = intval(get_option('stats_mostlimit'));
		$stats_display = get_option('stats_display');
?>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>"> 
<div class="wrap"> 
	<h2><?php _e('Stats Options', 'wp-stats'); ?></h2> 
	<fieldset class="options">
		<legend><?php _e('Stats Options', 'wp-stats'); ?></legend>
		<table width="100%"  border="0" cellspacing="3" cellpadding="3">
			 <tr valign="top">
				<th align="left" width="30%"><?php _e('Stats URL', 'wp-stats'); ?></th>
				<td align="left">
					<input type="text" name="stats_url" value="<?php echo get_option('stats_url'); ?>" size="50" /><br /><?php _e('URL To Stats Page.<br />Example: http://www.yoursite.com/blogs/stats/<br />Example: http://www.yoursite.com/blogs/?page_id=2', 'wp-stats'); ?>
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
							checked(1, $stats_display['useronline']);
							echo ' />&nbsp;&nbsp;'.__('WP-UserOnline', 'wp-stats').'<br />'."\n";
						}
						if(function_exists('download_file')) {
							echo '<input type="checkbox" name="stats_display[]" value="downloads"';
							checked(1, $stats_display['downloads']);
							echo ' />&nbsp;&nbsp;'.__('WP-DownloadManager', 'wp-stats').'<br />'."\n";
						}
					?>
					<p><strong><?php printf(__('Top %s Post Stats', 'wp-stats'), get_option('stats_mostlimit')); ?></strong></p>
					<input type="checkbox" name="stats_display[]" value="recent_posts"<?php checked(1, $stats_display['recent_posts']); ?> />&nbsp;&nbsp;<?php echo $stats_mostlimit ?> <?php _e('Most Recent Posts', 'wp-stats'); ?><br />
					<input type="checkbox" name="stats_display[]" value="recent_commtents"<?php checked(1, $stats_display['recent_commtents']); ?> />&nbsp;&nbsp;<?php echo $stats_mostlimit ?> <?php _e('Most Recent Comments', 'wp-stats'); ?><br />
					<?php
						if(function_exists('download_file')) {
							echo '<input type="checkbox" name="stats_display[]" value="recent_downloads"';
							checked(1, $stats_display['recent_downloads']);
							echo ' />&nbsp;&nbsp;'.$stats_mostlimit.' '.__('Most Recent Downloads', 'wp-stats').'<br />'."\n";
						}
					?>
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
						if(function_exists('download_file')) {
							echo '<input type="checkbox" name="stats_display[]" value="downloaded_most"';
							checked(1, $stats_display['downloaded_most']);
							echo ' />&nbsp;&nbsp;'.$stats_mostlimit.' '.__('Most Downloaded File', 'wp-stats').'<br />'."\n";
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
		<input type="submit" name="do" class="button" value="<?php _e('Update Options', 'wp-stats'); ?>" />&nbsp;&nbsp;<input type="button" name="cancel" value="<?php _e('Cancel', 'wp-stats'); ?>" class="button" onclick="javascript:history.go(-1)" /> 
	</div>
</div>
</form>

<!-- Uninstall WP-Stats -->
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>"> 
<div class="wrap"> 
	<h2><?php _e('Uninstall WP-Stats', 'wp-stats'); ?></h2>
	<p style="text-align: left;">
		<?php _e('Deactivating WP-Stats plugin does not remove any data that may have been created, such as the stats options. To completely remove this plugin, you can uninstall it here.', 'wp-stats'); ?>
	</p>
	<p style="text-align: left; color: red">
		<strong><?php _e('WARNING:', 'wp-stats'); ?></strong><br />
		<?php _e('Once uninstalled, this cannot be undone. You should use a Database Backup plugin of WordPress to back up all the data first.', 'wp-stats'); ?>
	</p>
	<p style="text-align: left; color: red">
		<strong><?php _e('The following WordPress Options will be DELETED:', 'wp-stats'); ?></strong><br />
	</p>
	<table width="70%"  border="0" cellspacing="3" cellpadding="3">
		<tr class="thead">
			<td align="center"><strong><?php _e('WordPress Options', 'wp-stats'); ?></strong></td>
		</tr>
		<tr>
			<td valign="top" style="background-color: #eee;">
				<ol>
				<?php
					foreach($stats_settings as $settings) {
						echo '<li>'.$settings.'</li>'."\n";
					}
				?>
				</ol>
			</td>
		</tr>
	</table>
	<p>&nbsp;</p>
	<p style="text-align: center;">
		<input type="checkbox" name="uninstall_stats_yes" value="yes" />&nbsp;<?php _e('Yes', 'wp-stats'); ?><br /><br />
		<input type="submit" name="do" value="<?php _e('UNINSTALL WP-Stats', 'wp-stats'); ?>" class="button" onclick="return confirm('<?php _e('You Are About To Uninstall WP-Stats From WordPress.\nThis Action Is Not Reversible.\n\n Choose [Cancel] To Stop, [OK] To Uninstall.', 'wp-stats'); ?>')" />
	</p>
</div> 
</form>
<?php
} // End switch($mode)
?>