<?php
/*
Plugin Name: WP-Stats Widget
Plugin URI: http://www.lesterchan.net/portfolio/programming.php
Description: Adds a Sidebar Widget To Display Partial Stats From WP-Stats Plugin
Version: 2.05
Author: GaMerZ
Author URI: http://www.lesterchan.net
*/


/*  Copyright 2006  Lester Chan  (email : gamerz84@hotmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


### Function: Init WP-Stats Widget
function widget_stats_init() {
	if (!function_exists('register_sidebar_widget')) {
		return;
	}

	### Function: WP-Stats Widget
	function widget_stats($args) {
		extract($args);
		$options = get_option('widget_stats');
		$stats_total_options = $options['stats_display_total'];
		$stats_most_options = $options['stats_display_most'];
		$limit = intval($options['most_limit']);
		$chars = intval($options['snippet_chars']);
		$title = htmlspecialchars($options['title']);
		if (function_exists('display_stats') && basename($_SERVER['PHP_SELF']) != 'wp-stats.php') {
			echo $before_widget.$before_title.$title.$after_title;
			if(!empty($stats_total_options)) {
				echo '<ul>'."\n";
				echo '<li><b>Total Stats</b></li>'."\n";
				echo '<li>'."\n";
				echo '<ul>'."\n";
				// Total Authors
				if($stats_total_options['authors'] == 1) {
					echo '<li><b>'.get_totalauthors(false).'</b> Authors</li>'."\n";
				}
				// Total Posts
				if($stats_total_options['posts'] == 1) {
					echo '<li><b>'.get_totalposts(false).'</b> Posts</li>'."\n";
				}
				// Total Pages
				if($stats_total_options['pages'] == 1) {
					echo '<li><b>'.get_totalpages(false).'</b> Pages</li>'."\n";
				}
				// Total Comments
				if($stats_total_options['comments'] == 1) {
					echo '<li><b>'.get_totalcomments(false).'</b> Comments</li>'."\n";
				}
				// Total Comment Posters
				if($stats_total_options['commenters'] == 1) {
					echo '<li><b>'.get_totalcommentposters(false).'</b> Comment Posters</li>'."\n";
				}
				// Total Links
				if($stats_total_options['links'] == 1) {
					echo '<li><b>'.get_totallinks(false).'</b> Links</li>'."\n";
				}
				echo '</ul>'."\n";
				echo '</li>'."\n";
				echo '</ul>'."\n";
			}
			// Most Commented
			if($stats_most_options['comments'] == 1) {
				echo '<ul>'."\n";
				echo '<li><b>'.$limit.' Most Commented</b></li>'."\n";
				echo '<li>'."\n";
				echo '<ul>'."\n";
				get_mostcommented('', $limit, $chars);
				echo '</ul>'."\n";
				echo '</li>'."\n";
				echo '</ul>'."\n";
			}
			// Most Emailed
			if($stats_most_options['emails'] == 1) {
				echo '<ul>'."\n";
				echo '<li><b>'.$limit.' Most Emailed</b></li>'."\n";
				echo '<li>'."\n";
				echo '<ul>'."\n";
				get_mostemailed('', $limit, $chars);
				echo '</ul>'."\n";
				echo '</li>'."\n";
				echo '</ul>'."\n";
			}
			// Highest Rated
			if($stats_most_options['ratings_highest'] == 1) {
				echo '<ul>'."\n";
				echo '<li><b>'.$limit.' Highest Rated</b></li>'."\n";
				echo '<li>'."\n";
				echo '<ul>'."\n";
				get_highest_rated('', $limit, $chars);
				echo '</ul>'."\n";
				echo '</li>'."\n";
				echo '</ul>'."\n";
			}
			// Most Rated
			if($stats_most_options['ratings_highest'] == 1) {
				echo '<ul>'."\n";
				echo '<li><b>'.$limit.' Most Rated</b></li>'."\n";
				echo '<li>'."\n";
				echo '<ul>'."\n";
				get_most_rated('', $limit, $chars);
				echo '</ul>'."\n";
				echo '</li>'."\n";
				echo '</ul>'."\n";
			}
			// Most Viewed
			if($stats_most_options['views'] == 1) {
				echo '<ul>'."\n";
				echo '<li><b>'.$limit.' Most Viewed</b></li>'."\n";
				echo '<li>'."\n";
				echo '<ul>'."\n";
				get_most_viewed('', $limit, $chars);
				echo '</ul>'."\n";
				echo '</li>'."\n";
				echo '</ul>'."\n";
			}
			if(intval($options['show_link']) == 1) {
				echo '<ul>'."\n";
				echo '<li><a href="'.stripslashes(get_settings('stats_url')).'">My Blog Statistics</a></li>'."\n";
				echo '</ul>'."\n";
			}
			echo $after_widget;
		}
	}

	### Function: WP-Stats Widget Options
	function widget_stats_options() {
		global $wpdb;
		$options = get_option('widget_stats');
		$stats_options_total_array = array();
		$stats_options_most_array = array();
		if (!is_array($options)) {
			$options = array('title' => 'Statistics', 'stats_display_total' => array(), 'stats_display_most' => array(), 'most_limit' => '10', 'show_link' => '1', 'snippet_chars' => 12);
		}
		if ($_POST['stats-submit']) {
			$most_limit = intval($_POST['most_limit']);
			$show_link = intval($_POST['show_link']);
			$snippet_chars = intval($_POST['snippet_chars']);
			$post_total_stats = $_POST['stats_display_total'];
			$post_most_stats = $_POST['stats_display_most'];
			if($post_total_stats) {
				foreach($post_total_stats as $post_total_stat) {
					$post_total_stat = addslashes($post_total_stat);
					$stats_options_total_array[$post_total_stat] = 1;
				}
			}
			if($post_most_stats) {
				foreach($post_most_stats as $post_most_stat) {
					$post_most_stat = addslashes($post_most_stat);
					$stats_options_most_array[$post_most_stat] = 1;
				}
			}
			$options['stats_display_total'] = $stats_options_total_array;
			$options['stats_display_most'] = $stats_options_most_array;
			$options['most_limit'] = $most_limit;
			$options['show_link'] = $show_link;
			$options['snippet_chars'] = $snippet_chars;
			$options['title'] = strip_tags(stripslashes($_POST['stats-title']));
			update_option('widget_stats', $options);
		}
		echo '<p style="text-align: left;"><label for="stats-title">Widget Title:</label>&nbsp;&nbsp;&nbsp;<input type="text" id="stats-title" name="stats-title" value="'.htmlspecialchars($options['title']).'" />';
		echo '<p style="text-align: left;"><label for="stats_display">Statistics To Display?</label>&nbsp;&nbsp;&nbsp;'."\n";
		echo '<p style="text-align: left;">'."\n";
		echo '<input type="checkbox" id="stats_display_total" name="stats_display_total[]" value="authors"';
		checked(1, $options['stats_display_total']['authors']);
		echo ' />&nbsp;&nbsp;Total Authors<br />'."\n";
		echo '<input type="checkbox" id="stats_display_total" name="stats_display_total[]" value="posts"';
		checked(1, $options['stats_display_total']['posts']);
		echo ' />&nbsp;&nbsp;Total Posts<br />'."\n";
		echo '<input type="checkbox" id="stats_display_total" name="stats_display_total[]" value="pages"';
		checked(1, $options['stats_display_total']['pages']);
		echo ' />&nbsp;&nbsp;Total Pages<br />'."\n";
		echo '<input type="checkbox" id="stats_display_total" name="stats_display_total[]" value="comments"';
		checked(1, $options['stats_display_total']['comments']);
		echo ' />&nbsp;&nbsp;Total Comments<br />'."\n";
		echo '<input type="checkbox" id="stats_display_total" name="stats_display_total[]" value="commenters"';
		checked(1, $options['stats_display_total']['commenters']);
		echo ' />&nbsp;&nbsp;Total Comment Posters<br />'."\n";
		echo '<input type="checkbox" id="stats_display_total" name="stats_display_total[]" value="links"';
		checked(1, $options['stats_display_total']['links']);
		echo ' />&nbsp;&nbsp;Total Links<br /><br />'."\n";
		echo '<input type="checkbox" id="stats_display_most" name="stats_display_most[]" value="comments"';
		checked(1, $options['stats_display_most']['comments']);
		echo ' />&nbsp;&nbsp;'.$options['most_limit'].' Most Commented Posts<br />'."\n";
		if(function_exists('wp_email')) {
			echo '<input type="checkbox" id="stats_display_most" name="stats_display_most[]" value="emails"';
			checked(1, $options['stats_display_most']['emails']);
			echo ' />&nbsp;&nbsp;'.$options['most_limit'].' Most Emailed Posts<br />'."\n";
		}
		if(function_exists('the_ratings')) {
			echo '<input type="checkbox" id="stats_display_most" name="stats_display_most[]" value="ratings_highest"';
			checked(1, $options['stats_display_most']['ratings_highest']);
			echo ' />&nbsp;&nbsp;'.$options['most_limit'].' Highest Rated Posts<br />'."\n";
			echo '<input type="checkbox" id="stats_display_most" name="stats_display_most[]" value="ratings_most"';
			checked(1, $options['stats_display_most']['ratings_most']);
			echo ' />&nbsp;&nbsp;'.$options['most_limit'].' Most Rated Posts<br />'."\n";
		}
		if(function_exists('the_views')) {
			echo '<input type="checkbox" id="stats_display_most" name="stats_display_most[]" value="views"';
			checked(1, $options['stats_display_most']['views']);
			echo ' />&nbsp;&nbsp;'.$options['most_limit'].' Most Viewed Posts<br />'."\n";
		}
		echo '</p>'."\n";
		echo '<p style="text-align: left;"><label for="most_limit">Post Title Length (Characters):</label>&nbsp;&nbsp;&nbsp;'."\n";
		echo '<p style="text-align: left;"><input type="text" id="snippet_chars" name="snippet_chars" value="'.$options['snippet_chars'].'" size="3" maxlength="3" /></p>'."\n";
		echo '<p style="text-align: left;"><label for="most_limit">Most Limit:</label>&nbsp;&nbsp;&nbsp;'."\n";
		echo '<p style="text-align: left;"><input type="text" id="most_limit" name="most_limit" value="'.$options['most_limit'].'" size="2" maxlength="2" /></p>'."\n";
		echo '<p style="text-align: left;"><label for="show_link">Show Link To Full Stats (wp-stats.php)?</label>&nbsp;&nbsp;&nbsp;'."\n";
		echo '<p style="text-align: left;">';
		echo '<input type="radio" id="show_link" name="show_link" value="1"';
		checked(1, intval($options['show_link']));
		echo ' />&nbsp;Yes&nbsp;&nbsp;&nbsp;<input type="radio" id="show_link" name="show_link" value="0"';
		checked(0, intval($options['show_link']));		
		echo ' />&nbsp;No</p>'."\n";
		echo '<input type="hidden" id="stats-submit" name="stats-submit" value="1" />'."\n";
	}

	// Register Widgets
	register_sidebar_widget('Statistics', 'widget_stats');
	register_widget_control('Statistics', 'widget_stats_options', 400, 550);
}


### Function: Load The WP-Stats Widget
add_action('plugins_loaded', 'widget_stats_init');
?>