<?php
/*
Plugin Name: WP-Stats Widget
Plugin URI: http://lesterchan.net/portfolio/programming/php/
Description: Adds a Stats Widget to display stats from WP-Stats Plugin. You will need to activate WP-Stats first.
Version: 2.31
Author: Lester 'GaMerZ' Chan
Author URI: http://lesterchan.net
*/


/*  Copyright 2008  Lester Chan  (email : lesterchan@gmail.com)

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
		$title = htmlspecialchars(stripslashes($options['title']));
		if (function_exists('display_stats')) {
			echo $before_widget.$before_title.$title.$after_title;
			if(!empty($stats_total_options)) {
				echo '<ul>'."\n";
				echo '<li><strong>'.__('Total Stats', 'wp-stats').'</strong></li>'."\n";
				echo '<li>'."\n";
				echo '<ul>'."\n";
				// Total Authors
				if($stats_total_options['authors'] == 1) {
					echo '<li>'.sprintf(__ngettext('<strong>%s</strong> Author', '<strong>%s</strong> Authors', get_totalauthors(false), 'wp-stats'), get_totalauthors(false)).'</li>'."\n";
				}
				// Total Posts
				if($stats_total_options['posts'] == 1) {
					echo '<li>'.sprintf(__ngettext('<strong>%s</strong> Post', '<strong>%s</strong> Posts', get_totalposts(false), 'wp-stats'), get_totalposts(false)).'</li>'."\n";
				}
				// Total Pages
				if($stats_total_options['pages'] == 1) {
					'<li>'.sprintf(__ngettext('<strong>%s</strong> Page', '<strong>%s</strong> Pages', get_totalpages(false), 'wp-stats'), get_totalpages(false)).'</li>'."\n";
				}
				// Total Tags
				if($stats_total_options['tags'] == 1) {
					echo '<li>'.sprintf(__ngettext('<strong>%s</strong> Tag', '<strong>%s</strong> Tags', wp_count_terms('post_tag'), 'wp-stats'), wp_count_terms('post_tag')).'</li>'."\n";
				}
				// Total Comments
				if($stats_total_options['comments'] == 1) {
					echo '<li>'.sprintf(__ngettext('<strong>%s</strong> Comment', '<strong>%s</strong> Comments', get_totalcomments(false), 'wp-stats'), get_totalcomments(false)).'</li>'."\n";
				}
				// Total Comment Posters
				if($stats_total_options['commenters'] == 1) {
					echo '<li>'.sprintf(__ngettext('<strong>%s</strong> Comment Poster', '<strong>%s</strong> Comment Posters', get_totalcommentposters(false), 'wp-stats'), get_totalcommentposters(false)).'</li>'."\n";
				}
				// Total Links
				if($stats_total_options['links'] == 1) {
					echo '<li>'.sprintf(__ngettext('<strong>%s</strong> Link', '<strong>%s</strong> Links', get_totallinks(false), 'wp-stats'), get_totallinks(false)).'</li>'."\n";
				}
				// Total Post Categories
				if($stats_total_options['post_cats'] == 1) {
					echo '<li>'.sprintf(__ngettext('<strong>%s</strong> Post Category', '<strong>%s</strong> Post Categories', wp_count_terms('category'), 'wp-stats'), wp_count_terms('category')).'</li>'."\n";
				}
				// Total Link Categories
				if($stats_total_options['link_cats'] == 1) {
					echo '<li>'.sprintf(__ngettext('<strong>%s</strong> Link Category', '<strong>%s</strong> Link Categories', wp_count_terms('link_category'), 'wp-stats'), wp_count_terms('link_category')).'</li>'."\n";
				}
				// Total Spam
				if($stats_total_options['spam'] == 1 && function_exists('akismet_spam_count')) {
					echo '<li>'.sprintf(__ngettext('<strong>%s</strong> Spam Blocked', '<strong>%s</strong> Spam Blockeds', number_format(akismet_spam_count()), 'wp-stats'), number_format(akismet_spam_count())).'</li>'."\n";
				}
				echo apply_filters('wp_stats_widget_general', $widget_general_stats);
				echo '</ul>'."\n";
				echo '</li>'."\n";
				echo '</ul>'."\n";
			}
			// Most Commented
			if($stats_most_options['comments'] == 1) {
				echo '<ul>'."\n";
				echo '<li><strong>'.$limit.' '.__('Most Commented', 'wp-stats').'</strong></li>'."\n";
				echo '<li>'."\n";
				echo '<ul>'."\n";
				get_mostcommented('post', $limit, $chars);
				echo '</ul>'."\n";
				echo '</li>'."\n";
				echo '</ul>'."\n";
			}
			echo apply_filters('wp_stats_widget_most', $widget_most_stats);
			if(intval($options['show_link']) == 1) {
				echo '<ul>'."\n";
				echo '<li><a href="'.stripslashes(get_option('stats_url')).'">'.__('My Blog Statistics', 'wp-stats').'</a></li>'."\n";
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
			$options = array('title' => __('Statistics', 'wp-stats'), 'stats_display_total' => array(), 'stats_display_most' => array(), 'most_limit' => '10', 'show_link' => '1', 'snippet_chars' => 12);
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
			$options['title'] = strip_tags($_POST['stats-title']);
			update_option('widget_stats', $options);
		}
		echo '<p style="text-align: left;"><label for="stats-title">'.__('Widget Title', 'wp-stats').':</label>&nbsp;&nbsp;&nbsp;<input type="text" id="stats-title" name="stats-title" value="'.htmlspecialchars(stripslashes($options['title'])).'" />';
		echo '<p style="text-align: left;">'.__('Statistics To Display?', 'wp-stats').'&nbsp;&nbsp;&nbsp;'."\n";
		echo '<p style="text-align: left;">'."\n";
		echo '<input type="checkbox" id="wpstats_widget_authors" name="stats_display_total[]" value="authors"';
		checked(1, $options['stats_display_total']['authors']);
		echo ' />&nbsp;&nbsp;<label for="wpstats_widget_authors">'.__('Total Authors', 'wp-stats').'</label><br />'."\n";
		echo '<input type="checkbox" id="wpstats_widget_posts" name="stats_display_total[]" value="posts"';
		checked(1, $options['stats_display_total']['posts']);
		echo ' />&nbsp;&nbsp;<label for="wpstats_widget_posts">'.__('Total Posts', 'wp-stats').'</label><br />'."\n";
		echo '<input type="checkbox" id="wpstats_widget_pages" name="stats_display_total[]" value="pages"';
		checked(1, $options['stats_display_total']['pages']);
		echo ' />&nbsp;&nbsp;<label for="wpstats_widget_pages">'.__('Total Pages', 'wp-stats').'</label><br />'."\n";
		echo '<input type="checkbox" id="wpstats_widget_tags" name="stats_display_total[]" value="tags"';
		checked(1, $options['stats_display_total']['tags']);
		echo ' />&nbsp;&nbsp;<label for="wpstats_widget_tags">'.__('Total Tags', 'wp-stats').'</label><br />'."\n";
		echo '<input type="checkbox" id="wpstats_widget_comments" name="stats_display_total[]" value="comments"';
		checked(1, $options['stats_display_total']['comments']);
		echo ' />&nbsp;&nbsp;<label for="wpstats_widget_comments">'.__('Total Comments', 'wp-stats').'</label><br />'."\n";
		echo '<input type="checkbox" id="wpstats_widget_commenters" name="stats_display_total[]" value="commenters"';
		checked(1, $options['stats_display_total']['commenters']);
		echo ' />&nbsp;&nbsp;<label for="wpstats_widget_commenters">'.__('Total Comment Posters', 'wp-stats').'</label><br />'."\n";
		echo '<input type="checkbox" id="wpstats_widget_links" name="stats_display_total[]" value="links"';
		checked(1, $options['stats_display_total']['links']);
		echo ' />&nbsp;&nbsp;<label for="wpstats_widget_links">'.__('Total Links', 'wp-stats').'</label><br />'."\n";
		echo '<input type="checkbox" id="wpstats_widget_post_cats" name="stats_display_total[]" value="post_cats"';
		checked(1, $options['stats_display_total']['post_cats']);
		echo ' />&nbsp;&nbsp;<label for="wpstats_widget_post_cats">'.__('Total Post Categories', 'wp-stats').'</label><br />'."\n";
		echo '<input type="checkbox" id="wpstats_widget_link_cats" name="stats_display_total[]" value="link_cats"';
		checked(1, $options['stats_display_total']['link_cats']);
		echo ' />&nbsp;&nbsp;<label for="wpstats_widget_link_cats">'.__('Total Link Categories', 'wp-stats').'</label><br />'."\n";
		if(function_exists('akismet_spam_count')) {
			echo '<input type="checkbox" id="wpstats_widget_spam" name="stats_display_total[]" value="spam"';
			checked(1, $options['stats_display_total']['spam']);
			echo ' />&nbsp;&nbsp;<label for="wpstats_widget_spam">'.__('Total Spam Blocked', 'wp-stats').'</label>';
		}
		echo apply_filters('wp_stats_widget_admin_general', $widget_admin_general_stats);
		echo '<br /><br />'."\n";
		echo '<input type="checkbox" id="wpstats_widget_most_comments" name="stats_display_most[]" value="comments"';
		checked(1, $options['stats_display_most']['comments']);
		echo ' />&nbsp;&nbsp;<label for="wpstats_widget_most_comments">'.sprintf(__ngettext('%s Most Commented Post', '%s Most Commented Posts', $options['most_limit'], 'wp-stats'), $options['most_limit']).'</label><br />'."\n";
		echo apply_filters('wp_stats_widget_admin_most', $widget_admin_most_stats);
		echo '</p>'."\n";
		echo '<p style="text-align: left;"><label for="most_limit">'.__('Post Title Length (Characters)', 'wp-stats').':</label>&nbsp;&nbsp;&nbsp;'."\n";
		echo '<p style="text-align: left;"><input type="text" id="snippet_chars" name="snippet_chars" value="'.$options['snippet_chars'].'" size="3" maxlength="3" /></p>'."\n";
		echo '<p style="text-align: left;"><label for="most_limit">'.__('Most Limit', 'wp-stats').':</label>&nbsp;&nbsp;&nbsp;'."\n";
		echo '<p style="text-align: left;"><input type="text" id="most_limit" name="most_limit" value="'.$options['most_limit'].'" size="2" maxlength="2" /></p>'."\n";
		echo '<p style="text-align: left;">'.__('Show Link To Full Stats?', 'wp-stats').'&nbsp;&nbsp;&nbsp;'."\n";
		echo '<p style="text-align: left;">';
		echo '<input type="radio" id="show_link-1" name="show_link" value="1"';
		checked(1, intval($options['show_link']));
		echo ' />&nbsp;<label for="show_link-1">'.__('Yes', 'wp-stats').'</label>&nbsp;&nbsp;&nbsp;<input type="radio" id="show_link-0" name="show_link" value="0"';
		checked(0, intval($options['show_link']));		
		echo ' />&nbsp;<label for="show_link-0">'.__('No', 'wp-stats').'</label></p>'."\n";
		echo '<input type="hidden" id="stats-submit" name="stats-submit" value="1" />'."\n";
	}

	// Register Widgets
	register_sidebar_widget('WP-Stats', 'widget_stats');
	register_widget_control('WP-Stats', 'widget_stats_options', 400, 550);
}


### Function: Load The WP-Stats Widget
add_action('plugins_loaded', 'widget_stats_init');
?>