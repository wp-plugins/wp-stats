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

### If Form Is Submitted
if($_POST['Submit']) {
	$stats_url = addslashes(trim($_POST['stats_url']));
	$update_stats_queries = array();
	$update_stats_text = array();
	$update_stats_queries[] = update_option('stats_url', $stats_url);
	$update_stats_text[] = __('Stats URL');
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
			</table>
		</fieldset>
		<div align="center">
			<input type="submit" name="Submit" class="button" value="<?php _e('Update Options'); ?>" />&nbsp;&nbsp;<input type="button" name="cancel" value="Cancel" class="button" onclick="javascript:history.go(-1)" /> 
		</div>
	</form> 
</div> 