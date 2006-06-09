<?php
/*
Plugin Name: WP-Stats
Plugin URI: http://www.lesterchan.net/portfolio/programming.php
Description: Display Your WordPress Statistics.
Version: 2.04
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


### Function: WP-Stats Menu
add_action('admin_menu', 'stats_menu');
function stats_menu() {
	if (function_exists('add_submenu_page')) {
		add_submenu_page('index.php',  __('WP-Stats'),  __('WP-Stats'), 1, 'stats/stats.php', 'display_stats');
	}
}


### Display WP-Stats Admin Page
function display_stats() {
?>
	<!-- General Stats -->
	<div class="wrap">		
		<h2>General Stats</h2>
		<p><b>Total Stats</b></p>
		<ul>
			<li><b><?php get_totalauthors(); ?></b> Authors To This Blog.</li>
			<li><b><?php get_totalposts(); ?></b> Posts Were Posted.</li>
			<li><b><?php get_totalpages(); ?></b> Pages Were Created.</li>
			<li><b><?php get_totalcomments(); ?></b> Comments Were Posted.</li>
			<li><b><?php get_totalcommentposters(); ?></b> Different Nicks Were Represented In The Comments.</li>
			<li><b><?php get_totallinks(); ?></b> Links Were Added.</li>
		</ul>
	</div>

	<!-- Plugin Stats -->
	<div class="wrap">		
		<h2>Plugins Stats</h2>
		<!-- WP-EMail Stats -->
		<?php if(function_exists('wp_email')): ?>
			<p><b>WP-EMail</b></p>
			<ul>
				<li><b><?php get_emails(); ?></b> Emails Were Sent.</li>
				<li><b><?php get_emails_success(); ?></b> Emails Were Sent Successfully.</li>
				<li><b><?php get_emails_failed(); ?></b> Emails Failed To Send.</li>	
			</ul>		
		<?php endif; ?>
		<!-- WP-Polls Stats -->
		<?php if(function_exists('get_poll')): ?>
			<p><b>WP-Polls</b></p>
			<ul>
				<li><b><?php get_pollquestions(); ?></b> Polls Were Created.</li>
				<li><b><?php get_pollanswers(); ?></b> Polls' Answers Were Given.</li>
				<li><b><?php get_pollvotes(); ?></b> Votes Were Casted.</li>
			</ul>
		<?php endif; ?>
		<!-- WP-PostRatings Stats -->
		<?php if(function_exists('the_ratings')): ?>
			<p><b>WP-PostRatings</b></p>
			<ul>
				<li><b><?php get_ratings_votes(); ?></b> Votes Were Casted.</li>
				<li><b><?php get_ratings_users(); ?></b> Users Casted Their Vote.</li>	
			</ul>
		<?php endif; ?>
		<!-- WP-PostViews Stats -->
		<?php if(function_exists('the_views')): ?>
			<p><b>WP-PostViews</b></p>
			<ul>
				<li><b><?php get_totalviews(); ?></b> Views Were Generated.</li>
			</ul>
		<?php endif; ?>
		<!-- WP-UserOnline Stats -->
		<?php if(function_exists('useronline')): ?>
			<p><b>WP-UserOnline</b></p>
			<ul>
				<li><?php get_useronline(); ?> Now.</li>
				<li>Most users ever online was <b><?php get_most_useronline(); ?></b>.</li>
				<li>On <b><?php get_most_useronline_date(); ?></b>.</li>
			</ul>			
		<?php endif; ?>
	</div>

	<!-- Top 10 Stats-->
	<div class="wrap">		
		<h2>Top 10 Stats</h2>
		<!-- 10 Recent Posts -->
		<p><b>10 Recent Posts</b></p>
		<ul>
			<?php get_recentposts(); ?>
		</ul>
		<!-- 10 Recent Comments -->
		<p><b>10 Recent Comments</b></p>
		<ul>
			<?php get_recentcomments(); ?>
		</ul>
		<!-- 10 Most Commented Post -->
		<p><b>10 Most Commented Post</b></p>
		<ul>
			<?php get_mostcommented(); ?>
		</ul>
		<!-- WP-EMail (10 Most EMailed Post) -->
		<?php if(function_exists('wp_email')): ?>
			<p><b>10 Most Emailed Post</b></p>
			<ul>
				<?php get_mostemailed(); ?>
			</ul>
		<?php endif; ?>
		<!-- WP-PostRatings (10 Highest Rated Post) -->
		<?php if(function_exists('the_ratings')): ?>
			<p><b>10 Highest Rated Post</b></p>
			<ul>
				<?php get_highest_rated(); ?>
			</ul>
		<?php endif; ?>
		<!-- WP-PostRatings (10 Most Rated Post) -->
		<?php if(function_exists('the_ratings')): ?>
			<p><b>10 Most Rated Post</b></p>
			<ul>
				<?php get_most_rated(); ?>
			</ul>
		<?php endif; ?>
		<!-- WP-PostViews (10 Most Viewed Post) -->
		<?php if(function_exists('the_views')): ?>
			<p><b>10 Most Viewed Post</b></p>
			<ul><?php get_most_viewed(); ?></ul>
		<?php endif; ?>
	</div>

	<!-- Author Stats -->
	<div class="wrap">		
		<h2>Authors Stats</h2>
		<p><b>Authors</b></p>
		<ol>
			<?php get_authorsstats(); ?>
		</ol>
	</div>

	<!-- Comments' Members Stats -->
	<div class="wrap">		
		<h2>Comments' Members Stats</h2>
		<p><b>Comment Members</b></p>
		<ol>
			<?php get_commentmembersstats(); ?>
		</ol>
	</div>

	<!-- Misc Stats -->
	<div class="wrap">		
		<h2>Misc Stats</h2>
		<!-- Post Categories -->
		<p><b>Post Categories</b></p>
		<ul>
			<?php list_cats(1,'All','name','asc','',true,0,1,0,1,true,0,0,0,'','','',true); ?>
		</ul>
		<!-- Link Categories -->
		<p><b>Link Categories</b></p>
		<ul>
			<?php get_linkcats(); ?>
		</ul>
	</div>
<?php
}


### Function: Get Total Authors
function get_totalauthors($display = true) {
	global $wpdb;
	$totalauthors = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users WHERE user_activation_key = ''");
	if($display) {
		echo number_format($totalauthors);
	} else {
		return number_format($totalauthors);
	}
}


### Function: Get Total Posts
function get_totalposts($display = true) {
	global $wpdb;
	$totalposts = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_status = 'publish'");
	if($display) {
		echo number_format($totalposts);
	} else {
		return number_format($totalposts);
	}
}


### Function: Get Total Pages
function get_totalpages($display = true) {
	global $wpdb;
	$totalpages = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_status = 'static'");
	if($display) {
		echo number_format($totalpages);
	} else {
		return number_format($totalpages);
	}
}


### Function: Get Total Comments
function get_totalcomments($display = true) {
	global $wpdb;
	$totalcomments = $wpdb->get_var("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_approved = '1'");
	if($display) {
		echo number_format($totalcomments);
	} else {
		return number_format($totalcomments);
	}
}


### Function: Get Total Comments Poster
function get_totalcommentposters($display = true) {
	global $wpdb;
	$totalcommentposters = $wpdb->get_var("SELECT COUNT(DISTINCT comment_author) FROM $wpdb->comments WHERE comment_approved = '1'");
	if($display) {
		echo number_format($totalcommentposters);
	} else {
		return number_format($totalcommentposters);
	}
}


### Function: Get Total Links
function get_totallinks($display = true) {
	global $wpdb;
	$totallinks = $wpdb->get_var("SELECT COUNT(link_id) FROM $wpdb->links");
	if($display) {
		echo number_format($totallinks);
	} else {
		return number_format($totallinks);
	}
}


### Function: Get Recent Posts
function get_recentposts($mode = '', $limit = 10) {
    global $wpdb, $post;
	$where = '';
	if($mode == 'post') {
			$where = 'post_status = \'publish\'';
	} elseif($mode == 'page') {
			$where = 'post_status = \'static\'';
	} else {
			$where = '(post_status = \'publish\' OR post_status = \'static\')';
	}
    $recentposts = $wpdb->get_results("SELECT $wpdb->posts.ID, post_title, post_name, post_status, post_date, user_login, display_name FROM $wpdb->posts LEFT JOIN $wpdb->users ON $wpdb->users.ID = $wpdb->posts.post_author WHERE post_date < '".current_time('mysql')."' AND $where AND post_password = '' ORDER  BY post_date DESC LIMIT $limit");
	if($recentposts) {
		foreach ($recentposts as $post) {
				$post_title = htmlspecialchars(stripslashes($post->post_title));
				$post_date = mysql2date('d.m.Y', $post->post_date);
				$display_name = htmlspecialchars(stripslashes($post->display_name));
				echo "<li>$post_date - <a href=\"".get_permalink()."\">$post_title</a> ($display_name)</li>";
		}
	} else {
		echo '<li>'.__('N/A').'</li>';
	}
}


### Function: Get Recent Comments
function get_recentcomments($mode = '', $limit = 10) {
    global $wpdb, $post;
	$where = '';
	if($mode == 'post') {
			$where = 'post_status = \'publish\'';
	} elseif($mode == 'page') {
			$where = 'post_status = \'static\'';
	} else {
			$where = '(post_status = \'publish\' OR post_status = \'static\')';
	}
    $recentcomments = $wpdb->get_results("SELECT $wpdb->posts.ID, post_title, post_name, post_status, comment_author, post_date, comment_date FROM $wpdb->posts INNER JOIN $wpdb->comments ON $wpdb->posts.ID = $wpdb->comments.comment_post_ID WHERE comment_approved = '1' AND post_date < '".current_time('mysql')."' AND $where AND post_password = '' ORDER  BY comment_date DESC LIMIT $limit");
	if($recentcomments) {
		foreach ($recentcomments as $post) {
				$post_title = htmlspecialchars(stripslashes($post->post_title));
				$comment_author = htmlspecialchars(stripslashes($post->comment_author));
				$comment_date = mysql2date('d.m.Y @ H:i', $post->comment_date);
				echo "<li>$comment_date - $comment_author (<a href=\"".get_permalink()."\">$post_title</a>)</li>";
		}
	} else {
		echo '<li>'.__('N/A').'</li>';
	}
}


### Function: Get Top Commented Posts
function get_mostcommented($mode = '', $limit = 10, $chars = 0) {
    global $wpdb, $post;
	$where = '';
	if($mode == 'post') {
			$where = 'post_status = \'publish\'';
	} elseif($mode == 'page') {
			$where = 'post_status = \'static\'';
	} else {
			$where = '(post_status = \'publish\' OR post_status = \'static\')';
	}
    $mostcommenteds = $wpdb->get_results("SELECT $wpdb->posts.ID, post_title, post_name, post_status, post_date, COUNT($wpdb->comments.comment_post_ID) AS 'comment_total' FROM $wpdb->posts LEFT JOIN $wpdb->comments ON $wpdb->posts.ID = $wpdb->comments.comment_post_ID WHERE comment_approved = '1' AND post_date < '".current_time('mysql')."' AND $where AND post_password = '' GROUP BY $wpdb->comments.comment_post_ID ORDER  BY comment_total DESC LIMIT $limit");
	if($mostcommenteds) {
		if($chars > 0) {
			foreach ($mostcommenteds as $post) {
					$post_title = htmlspecialchars(stripslashes($post->post_title));
					$comment_total = intval($post->comment_total);
					echo "<li><a href=\"".get_permalink()."\">".snippet_chars($post_title, $chars)."</a> - $comment_total ".__('comments')."</li>";
			}
		} else {
			foreach ($mostcommenteds as $post) {
					$post_title = htmlspecialchars(stripslashes($post->post_title));
					$comment_total = intval($post->comment_total);
					echo "<li><a href=\"".get_permalink()."\">$post_title</a> - $comment_total ".__('comments')."</li>";
			}
		}
	} else {
		echo '<li>'.__('N/A').'</li>';
	}
}


### Function: Get Author Stats
function  get_authorsstats() {
	global $wpdb, $wp_rewrite;
	$where = '';
	if($mode == 'post') {
			$where = 'post_status = \'publish\'';
	} elseif($mode == 'page') {
			$where = 'post_status = \'static\'';
	} else {
			$where = '(post_status = \'publish\' OR post_status = \'static\')';
	}
	$posts = $wpdb->get_results("SELECT COUNT($wpdb->posts.ID) AS 'posts_total', $wpdb->users.display_name,  $wpdb->users.user_nicename FROM $wpdb->posts LEFT JOIN $wpdb->users ON $wpdb->users.ID = $wpdb->posts.post_author AND user_activation_key = '' AND $where GROUP BY $wpdb->posts.post_author");
	if($posts) {
		$using_permalink = get_settings('permalink_structure');
		$permalink = $wp_rewrite->get_author_permastruct();
		foreach ($posts as $post) {
				$author_link = str_replace('%author%', strip_tags(stripslashes($post->user_nicename)), $permalink);
				$display_name = urlencode($post->display_name);
				$posts_total = intval($post->posts_total);				
				if($using_permalink) {
					echo "<li><a href=\"".get_settings('siteurl').$author_link."\">$display_name</a> ($posts_total)</li>";
				} else {
					echo "<li><a href=\"".get_settings('siteurl')."/?author_name=$post_author\">$display_name</a> ($posts_total)</li>";
				}
		}
	} else {
		echo '<li>'.__('N/A').'</li>';
	}
}


### Function: Get Comments' Members Stats
// Treshhold = Number Of Posts User Must Have Before It Will Display His Name Out
// 5 = Default Treshhold; -1 = Disable Treshhold
function get_commentmembersstats($threshhold = -1) {
	global $wpdb;
	$comments = $wpdb->get_results("SELECT comment_author, COUNT(comment_ID) AS 'comment_total' FROM $wpdb->comments WHERE comment_approved = '1' GROUP BY comment_author ORDER BY comment_total DESC");
	if($comments) {
		foreach ($comments as $comment) {
				$comment_author = strip_tags(stripslashes($comment->comment_author));
				$comment_author_link = urlencode($comment_author);
				$comment_total = intval($comment->comment_total);
				echo "<li><a href=\"".get_settings('siteurl')."/wp-content/plugins/stats/wp-stats.php?author=$comment_author_link\">$comment_author</a> ($comment_total)</li>";
				// If Total Comments Is Below Threshold
				if($comment_total <= $threshhold && $threshhold != -1) {
					return;
				}
		}
	} else {
		echo '<li>'.__('N/A').'</li>';
	}
}


### Function:  Get Links Categories Stats
function get_linkcats() {
	global $wpdb;
	$linkcats =  $wpdb->get_results("SELECT $wpdb->linkcategories.cat_name, COUNT(*)  AS  'total_links' FROM $wpdb->links INNER JOIN $wpdb->linkcategories ON $wpdb->linkcategories.cat_id = $wpdb->links.link_category GROUP BY $wpdb->linkcategories.cat_id ORDER  BY total_links DESC ");
	if($linkcats) {
		foreach ($linkcats as $linkcat) {
				$cat_name = htmlspecialchars(stripslashes($linkcat->cat_name));
				$total_links = intval($linkcat->total_links);
				echo "<li>$cat_name ($total_links)</li>\n";
		}
	} else {
		echo '<li>'.__('N/A').'</li>';
	}
}


### Function: Snippet Characters
function snippet_chars($text, $length = 0) {
	 if (strlen($text) > $length){       
		return substr($text,0,$length).'...';             
	 } else {
		return $text;
	 }
}
?>