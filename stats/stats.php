<?php
/*
Plugin Name: WP-Stats
Plugin URI: http://www.lesterchan.net/portfolio/programming.php
Description: Display Your WordPress Statistics.
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


### Function: WP-Stats Menu
add_action('admin_menu', 'stats_menu');
function stats_menu() {
	if (function_exists('add_submenu_page')) {
		add_submenu_page('index.php',  __('WP-Stats'),  __('WP-Stats'), 1, 'stats/stats.php', 'display_stats');
	}
	if (function_exists('add_options_page')) {
		add_options_page(__('Stats'), __('Stats'), 'manage_options', 'stats/stats-options.php');
	}
}


### Display WP-Stats Admin Page
function display_stats() {
	$stats_page = stats_page();
	echo "<div class=\"wrap\">\n$stats_page</div>\n";
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
function get_recentposts($mode = '', $limit = 10, $display = true) {
    global $wpdb, $post;
	$where = '';
	$temp = '';
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
			$temp .= "<li>$post_date - <a href=\"".get_permalink()."\">$post_title</a> ($display_name)</li>\n";
		}
	} else {
		$temp = '<li>'.__('N/A').'</li>';
	}
	if($display) {
		echo $temp;
	} else {
		return $temp;
	}
}


### Function: Get Recent Comments
function get_recentcomments($mode = '', $limit = 10, $display = true) {
    global $wpdb, $post;
	$where = '';
	$temp = '';
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
			$temp .= "<li>$comment_date - $comment_author (<a href=\"".get_permalink()."\">$post_title</a>)</li>\n";
		}
	} else {
		$temp = '<li>'.__('N/A').'</li>';
	}
	if($display) {
		echo $temp;
	} else {
		return $temp;
	}
}


### Function: Get Top Commented Posts
function get_mostcommented($mode = '', $limit = 10, $chars = 0, $display = true) {
    global $wpdb, $post;
	$where = '';
	$temp = '';
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
				$temp .= "<li><a href=\"".get_permalink()."\">".snippet_chars($post_title, $chars)."</a> - $comment_total ".__('comments')."</li>";
			}
		} else {
			foreach ($mostcommenteds as $post) {
				$post_title = htmlspecialchars(stripslashes($post->post_title));
				$comment_total = intval($post->comment_total);
				$temp .= "<li><a href=\"".get_permalink()."\">$post_title</a> - $comment_total ".__('comments')."</li>";
			}
		}
	} else {
		$temp = '<li>'.__('N/A').'</li>';
	}
	if($display) {
		echo $temp;
	} else {
		return $temp;
	}
}


### Function: Get Author Stats
function get_authorsstats($display = true) {
	global $wpdb, $wp_rewrite;
	$where = '';
	$temp = '';
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
					$temp .= "<li><a href=\"".get_settings('siteurl').$author_link."\">$display_name</a> ($posts_total)</li>\n";
				} else {
					$temp .= "<li><a href=\"".get_settings('siteurl')."/?author_name=$post_author\">$display_name</a> ($posts_total)</li>\n";
				}
		}
	} else {
		$temp = '<li>'.__('N/A').'</li>';
	}
	if($display) {
		echo $temp;
	} else {
		return $temp;
	}
}


### Function: Get Comments' Members Stats
// Treshhold = Number Of Posts User Must Have Before It Will Display His Name Out
// 5 = Default Treshhold; -1 = Disable Treshhold
function get_commentmembersstats($threshhold = -1, $display = true) {
	global $wpdb;
	$temp = '';
	$comments = $wpdb->get_results("SELECT comment_author, COUNT(comment_ID) AS 'comment_total' FROM $wpdb->comments WHERE comment_approved = '1' GROUP BY comment_author ORDER BY comment_total DESC");
	if($comments) {
		foreach ($comments as $comment) {
				$comment_author = strip_tags(stripslashes($comment->comment_author));
				$comment_author_link = urlencode($comment_author);
				$comment_total = intval($comment->comment_total);
				$temp .= "<li><a href=\"".stats_page_link($comment_author_link)."\">$comment_author</a> ($comment_total)</li>\n";
				// If Total Comments Is Below Threshold
				if($comment_total <= $threshhold && $threshhold != -1) {
					return;
				}
		}
	} else {
		$temp = '<li>'.__('N/A').'</li>';
	}
	if($display) {
		echo $temp;
	} else {
		return $temp;
	}
}


### Function:  Get Links Categories Stats
function get_linkcats($display = true) {
	global $wpdb;
	$temp = '';
	$linkcats =  $wpdb->get_results("SELECT $wpdb->linkcategories.cat_name, COUNT(*)  AS  'total_links' FROM $wpdb->links INNER JOIN $wpdb->linkcategories ON $wpdb->linkcategories.cat_id = $wpdb->links.link_category GROUP BY $wpdb->linkcategories.cat_id ORDER  BY total_links DESC ");
	if($linkcats) {
		foreach ($linkcats as $linkcat) {
				$cat_name = htmlspecialchars(stripslashes($linkcat->cat_name));
				$total_links = intval($linkcat->total_links);
				$temp .= "<li>$cat_name ($total_links)</li>\n";
		}
	} else {
		$temp = '<li>'.__('N/A').'</li>';
	}
	if($display) {
		echo $temp;
	} else {
		return $temp;
	}
}


### Function: Snippet Characters
function snippet_chars($text, $length = 0) {
	$text = htmlspecialchars_decode($text);
	 if (strlen($text) > $length){       
		return htmlspecialchars(substr($text,0,$length)).'...';             
	 } else {
		return htmlspecialchars($text);
	 }
}


### Function: HTML Special Chars Decode
if (!function_exists('htmlspecialchars_decode')) {
   function htmlspecialchars_decode($text) {
       return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
   }
}


### Function: Place Statistics Page In Content
add_filter('the_content', 'place_stats', '7');
function place_stats($content){
     $content = preg_replace( "/\[page_stats\]/ise", "stats_page()", $content); 
    return $content;
}


### Function: Stats Page
function stats_page_link($author, $page = 0) {
	$stats_url = get_settings('stats_url');
	if($page > 1) {
		$page = "&amp;stats_page=$page";
	} else {
		$page = '';
	}
	if(strpos($stats_url, '?') !== false) {
		$stats_url = "$stats_url&amp;stats_author=$author$page";
	} else {
		$stats_url = "$stats_url?stats_author=$author$page";
	}
	return $stats_url;
}


### Function: Statistics Page
function stats_page() {
	global $wpdb, $post;
	// Variables Variables Variables
	$comment_author = urldecode(strip_tags(stripslashes(trim($_GET['stats_author']))));
	$page = intval($_GET['stats_page']);
	$temp_stats = '';
	$temp_post = $post;
	$stats_mostlimit = intval(get_settings('stats_mostlimit'));
	$stats_display = get_settings('stats_display');

	// Default wp-stats.php Page
	if(empty($comment_author)) {
		// General Stats
		if($stats_display['total_stats'] == 1) {
			$temp_stats .= '<h2>General Stats</h2>'."\n";
			$temp_stats .= '<p><b>Total Stats</b></p>'."\n";
			$temp_stats .= '<ul>'."\n";
			$temp_stats .= '<li><b>'.get_totalauthors(false).'</b> Authors To This Blog.</li>'."\n";
			$temp_stats .= '<li><b>'.get_totalposts(false).'</b> Posts Were Posted.</li>'."\n";
			$temp_stats .= '<li><b>'.get_totalpages(false).'</b> Pages Were Created.</li>'."\n";
			$temp_stats .= '<li><b>'.get_totalcomments(false).'</b> Comments Were Posted.</li>'."\n";
			$temp_stats .= '<li><b>'.get_totalcommentposters(false).'</b> Different Nicks Were Represented In The Comments.</li>'."\n";
			$temp_stats .= '<li><b>'.get_totallinks(false).'</b> Links Were Added.</li>'."\n";
			$temp_stats .= '</ul>'."\n";
		}

		// Plugin Stats
		if($stats_display['email'] == 1 || $stats_display['polls'] == 1 || $stats_display['ratings'] == 1 || $stats_display['views'] || $stats_display['useronline'] == 1) {
			$temp_stats .= '<h2>Plugins Stats</h2>'."\n";
		}

		// WP-EMail Stats		
		if(function_exists('wp_email') && $stats_display['email'] == 1) {
			$temp_stats .= '<p><b>WP-EMail</b></p>'."\n";
			$temp_stats .= '<ul>'."\n";
			$temp_stats .= '<li><b>'.get_emails(false).'</b> Emails Were Sent.</li>'."\n";
			$temp_stats .= '<li><b>'.get_emails_success(false).'</b> Emails Were Sent Successfully.</li>'."\n";
			$temp_stats .= '<li><b>'.get_emails_failed(false).'</b> Emails Failed To Send.</li>'."\n";
			$temp_stats .= '</ul>'."\n";
		}
		
		// WP-Polls Stats		
		if(function_exists('get_poll') && $stats_display['polls'] == 1) {
			$temp_stats .= '<p><b>WP-Polls</b></p>'."\n";
			$temp_stats .= '<ul>'."\n";
			$temp_stats .= '<li><b>'.get_pollquestions(false).'</b> Polls Were Created.</li>'."\n";
			$temp_stats .= '<li><b>'.get_pollanswers(false).'</b> Polls\' Answers Were Given.</li>'."\n";
			$temp_stats .= '<li><b>'.get_pollvotes(false).'</b> Votes Were Casted.</li>'."\n";
			$temp_stats .= '</ul>'."\n";
		}
		
		// WP-PostRatings Stats		
		if(function_exists('the_ratings') && $stats_display['ratings'] == 1) {
			$temp_stats .= '<p><b>WP-PostRatings</b></p>'."\n";
			$temp_stats .= '<ul>'."\n";
			$temp_stats .= '<li><b>'.get_ratings_votes(false).'</b> Votes Were Casted.</li>'."\n";
			$temp_stats .= '<li><b>'.get_ratings_users(false).'</b> Users Casted Their Vote.</li>'."\n";
			$temp_stats .= '</ul>'."\n";
		}
		
		// WP-PostViews Stats		
		if(function_exists('the_views') && $stats_display['views'] == 1) {
			$temp_stats .= '<p><b>WP-PostViews</b></p>'."\n";
			$temp_stats .= '<ul>'."\n";
			$temp_stats .= '<li><b>'.get_totalviews(false).'</b> Views Were Generated.</li>'."\n";
			$temp_stats .= '</ul>'."\n";
		}

		// WP-UserOnline Stats		
		if(function_exists('useronline') && $stats_display['useronline'] == 1) {
			$temp_stats .= '<p><b>WP-UserOnline</b></p>'."\n";
			$temp_stats .= '<ul>'."\n";
			$temp_stats .= '<li><b>'.get_useronline('', '', false).'</b> User(s) Online Now.</li>'."\n";
			$temp_stats .= '<li>Most users ever online was <b>'.get_most_useronline(false).'</b>.</li>'."\n";
			$temp_stats .= '<li>On <b>'.get_most_useronline_date(false).'</b>.</li>'."\n";
			$temp_stats .= '</ul>'."\n";
		}
		
		// Top Stats
		if($stats_display['recent_posts'] == 1 || $stats_display['recent_commtents'] == 1 || $stats_display['commented_post'] == 1 || $stats_display['emailed_most'] == 1 || $stats_display['rated_highest'] == 1 || $stats_display['rated_most'] == 1 || $stats_display['viewed_most'] == 1) {
			$temp_stats .= '<h2>Top '.$stats_mostlimit.' Stats</h2>'."\n";
		}

		// Recent Posts
		if($stats_display['recent_posts'] == 1) {
			$temp_stats .= '<p><b>'.$stats_mostlimit.' Recent Posts</b></p>'."\n";
			$temp_stats .= '<ul>'."\n";
			$temp_stats .= get_recentposts('', $stats_mostlimit, false);
			$temp_stats .= '</ul>'."\n";
		}

		// Recent Comments
		if($stats_display['recent_commtents'] == 1) {
			$temp_stats .= '<p><b>'.$stats_mostlimit.' Recent Comments</b></p>'."\n";
			$temp_stats .= '<ul>'."\n";
			$temp_stats .= get_recentcomments('', $stats_mostlimit, false);
			$temp_stats .= '</ul>'."\n";
		}

		// Most Commented Post
		if($stats_display['commented_post'] == 1) {
			$temp_stats .= '<p><b>'.$stats_mostlimit.' Most Commented Post</b></p>'."\n";
			$temp_stats .= '<ul>'."\n";
			$temp_stats .= get_mostcommented('', $stats_mostlimit, 0, false);
			$temp_stats .= '</ul>'."\n";
		}

		// WP-EMail (Most EMailed Post)
		if(function_exists('wp_email') && $stats_display['emailed_most'] == 1) {
			$temp_stats .= '<p><b>'.$stats_mostlimit.' Most Emailed Post</b></p>'."\n";
			$temp_stats .= '<ul>'."\n";
			$temp_stats .= get_mostemailed('', $stats_mostlimit, 0, false);
			$temp_stats .= '</ul>'."\n";
		}
		
		// WP-PostRatings (Highest Rated Post) (Most Rated Post)
		if(function_exists('the_ratings')) {
			if($stats_display['rated_highest'] == 1) {
				$temp_stats .= '<p><b>'.$stats_mostlimit.' Highest Rated Post</b></p>'."\n";
				$temp_stats .= '<ul>'."\n";
				$temp_stats .= get_highest_rated('', $stats_mostlimit, 0, false);
				$temp_stats .= '</ul>'."\n";
			}
			if($stats_display['rated_most'] == 1) {
				$temp_stats .= '<p><b>'.$stats_mostlimit.' Most Rated Post</b></p>'."\n";
				$temp_stats .= '<ul>'."\n";
				$temp_stats .= get_most_rated('', $stats_mostlimit, 0, false);
				$temp_stats .= '</ul>'."\n";
			}
		}
		
		// WP-PostViews (Most Viewed Post)
		if(function_exists('the_views') && $stats_display['viewed_most'] == 1) {
			$temp_stats .= '<p><b>'.$stats_mostlimit.' Most Viewed Post</b></p>'."\n";
			$temp_stats .= '<ul>'."\n";
			$temp_stats .= get_most_viewed('', $stats_mostlimit, 0, false);
			$temp_stats .= '</ul>'."\n";
		}
		
		// Author Stats
		if($stats_display['authors'] == 1) {
			$temp_stats .= '<h2>Authors Stats</h2>'."\n";
			$temp_stats .= '<p><b>Authors</b></p>'."\n";
			$temp_stats .= '<ol>'."\n";
			$temp_stats .= get_authorsstats(false);
			$temp_stats .= '</ol>'."\n";
		}
		
		// Comments' Members Stats
		if($stats_display['comment_members'] == 1) {
			$temp_stats .= '<h2>Comments\' Members Stats</h2>'."\n";
			$temp_stats .= '<p><b>Comment Members</b></p>'."\n";
			$temp_stats .= '<ol>'."\n";
			$temp_stats .= get_commentmembersstats(-1, false);
			$temp_stats .= '</ol>'."\n";
		}

		// Misc Stats
		if($stats_display['post_cats'] == 1 || $stats_display['link_cats'] == 1) {
			$temp_stats .= '<h2>Misc Stats</h2>'."\n";
		}

		// Post Categories
		if($stats_display['post_cats'] == 1) {
			$temp_stats .= '<p><b>Post Categories</b></p>'."\n";
			$temp_stats .= '<ul>'."\n";
			$temp_stats .= list_cats(1,'All','name','asc','',true,0,1,0,1,true,0,0,1,'','','',true);
			$temp_stats .= '</ul>'."\n";
		}

		// Link Categories
		if($stats_display['link_cats'] == 1) {
			$temp_stats .= '<p><b>Link Categories</b></p>'."\n";
			$temp_stats .= '<ul>'."\n";
			$temp_stats .= get_linkcats(false);
			$temp_stats .= '</ul>'."\n";
		}

	// Displaying Comments Posted By User
	} else {
		// Stats URL
		$stats_url = get_settings('stats_url');
		// Number Of Comments Per Page
		$perpage = 10;
		// Comment Author Link
		$comment_author_link = urlencode($comment_author);
		// Comment Author SQL
		$comment_author_sql = $wpdb->escape($comment_author);
		// Total Comments Posted By User
		$totalcomments = $wpdb->get_var("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_author='$comment_author_sql'");
		// Checking $page and $offset
		if (empty($page) || $page == 0) { $page = 1; }
		if (empty($offset)) { $offset = 0; }
		// Determin $offset
		$offset = ($page-1) * $perpage;
		// Some Comments Stats
		if(($offset + $perpage) > $totalcomments) { $maxonpage = $totalcomments ; } else { $maxonpage = ($offset+$perpage); }
		if (($offset + 1) > ($totalcomments)) { $displayonpage = $totalcomments ; } else { $displayonpage = ($offset+1); }
		// Count Total Pages
		$totalpages = ceil($totalcomments/$perpage);
		// Getting The Comments
		$gmz_comments =  $wpdb->get_results("SELECT $wpdb->posts.ID, comment_author, comment_date, comment_content, ID, comment_ID, post_date, post_title, post_name, post_password FROM $wpdb->comments INNER  JOIN $wpdb->posts ON $wpdb->comments.comment_post_ID = $wpdb->posts.ID WHERE comment_author =  '$comment_author_sql' AND comment_approved = '1' AND post_date < '".current_time('mysql')."' AND (post_status = 'publish' OR post_status = 'static') ORDER  BY comment_post_ID DESC, comment_date DESC  LIMIT $offset, $perpage");

		$temp_stats .= '<h2>Comments Posted By '.$comment_author.'</h2>'."\n";
		$temp_stats .= '<p>Displaying <b>'.$displayonpage.'</b> To <b>'.$maxonpage.'</b> Of <b>'.$totalcomments.'</b> Comments</p>'."\n";

		// Get Comments
		if($gmz_comments) {
			foreach($gmz_comments as $post) {
				$comment_id = intval($post-> comment_ID);
				$comment_author2 = htmlspecialchars(stripslashes($post->comment_author));
				$comment_date = mysql2date('d.m.Y @ H:i', $post->comment_date);
				$comment_content = wpautop(stripslashes($post->comment_content));
				$post_date = mysql2date('d.m.Y @ H:i', $post->post_date);
				$post_title = htmlspecialchars(stripslashes($post->post_title));

				// Check For Password Protected Post
				if(!empty($post->post_password) && stripslashes($_COOKIE['wp-postpass_'.COOKIEHASH]) != $post->post_password) {
					// If New Title, Print It Out
					if($post_title != $cache_post_title) {
						$temp_stats .= "<p><b><a href=\"".get_permalink()."\" title=\"Posted On $post_date\">Protected: $post_title</a></b></p>\n";
						$temp_stats .= "<blockquote>Comments Protected</blockquote>\n";	
					}							
				} else {
					// If New Title, Print It Out
					if($post_title != $cache_post_title) {
						$temp_stats .= "<p><b><a href=\"".get_permalink()."\" title=\"Posted On $post_date\">$post_title</a></b></p>\n";
					}
					$temp_stats .= "<blockquote>$comment_content <a href=\"".get_permalink()."#comment-$comment_id\">Comment</a> Posted By <b>$comment_author2</b> On $comment_date</blockquote>\n";						
				}
				$cache_post_title = $post_title;
			}
		} else {
				$temp_stats .= "<p>$comment_author has not made any comments yet.</p>\n";
		}

		// If Total Pages Is More Than 1, Display Page Navigation
		if($totalpages > 1) {
			// Previous Page
			$temp_stats .= '<p>'."\n";
			$temp_stats .= '<span style="float: left">'."\n";
			if($page > 1 && ((($page*$perpage)-($perpage-1)) < $totalcomments)) {
				$temp_stats .= '<b>&laquo;</b> <a href="'.stats_page_link($comment_author_link, $page-1).'" title="&laquo; '.__('Previous Page').'">'.__('Previous Page').'</a>'."\n";
			} else {
				$temp_stats .= '&nbsp;'."\n";
			}
			$temp_stats .= '</span>'."\n";
			// Next Page
			$temp_stats .= '<span style="float: right">'."\n";
			if($page >= 1 && ((($page*$perpage)+1) <  $totalcomments)) {
				$temp_stats .= '<a href="'.stats_page_link($comment_author_link, $page+1).'" title="'.__('Next Page').' &raquo;">'.__('Next Page').'</a> <b>&raquo;</b>'."\n";
			} else {
				$temp_stats .= '&nbsp;'."\n";
			}
			$temp_stats .= '</span>'."\n";
			$temp_stats .= '</p>'."\n";
			// Pages
			$temp_stats .= '<br style="clear: both" />'."\n";
			$temp_stats .= '<p align="center">'."\n";
			$temp_stats .= 'Pages ('.$totalpages.') :'."\n";
			if ($page >= 4) {
				$temp_stats .= '<b><a href="'.stats_page_link($comment_author_link).'" title="'.__('Go to First Page').'">&laquo; '.__('First').'</a></b> ... '."\n";
			}
			if($page > 1) {
				$temp_stats .= ' <b><a href="'.stats_page_link($comment_author_link, $page-1).'" title="&laquo; '.__('Go to Page').' '.($page-1).'">&laquo;</a></b> '."\n";
			}
			for($i = $page - 2 ; $i  <= $page +2; $i++) {
				if ($i >= 1 && $i <= $totalpages) {
					if($i == $page) {
						$temp_stats .= "<b>[$i]</b> "."\n";
					} else {
						$temp_stats .= '<a href="'.stats_page_link($comment_author_link, $i).'" title="'.__('Page').' '.$i.'">'.$i.'</a> '."\n";
					}
				}
			}
			if($page < $totalpages) {
				$temp_stats .= ' <b><a href="'.stats_page_link($comment_author_link, $page+1).'" title="'.__('Go to Page').' '.($page+1).' &raquo;">&raquo;</a></b> '."\n";
			}
			if (($page+2) < $totalpages) {
				$temp_stats .= ' ... <b><a href="'.stats_page_link($comment_author_link, $totalpages).'" title="'.__('Go to Last Page').'">'.__('Last').' &raquo;</a></b>'."\n";
			}
			$temp_stats .= '</p>'."\n";
		}
		$temp_stats .= '<p><b>&laquo;&laquo;</b> <a href="'.$stats_url.'">Back To Stats Page</a></p>'."\n";
	} // End If
	
	// Assign Back $post
	$post = $temp_post;

	// Output Stats Page
	return $temp_stats;
}


### Function: Stats Option
add_action('activate_stats/stats.php', 'stats_init');
function stats_init() {
	global $wpdb;
	$stats_display = array('total_stats'  => 1, 'email'  => 1, 'polls' => 1, 'ratings' => 1, 'views' => 1, 'useronline' => 1, 'recent_posts' => 1, 'recent_commtents' => 1, 'commented_post' => 1, 'emailed_most' => 1, 'rated_highest' => 1, 'rated_most' => 1, 'viewed_most' => 1, 'authors' => 1, 'comment_members' => 1, 'post_cats' => 1, 'link_cats' => 1);  
	add_option('stats_mostlimit', '10', 'Stats Most Limit');
	add_option('stats_display', $stats_display, 'Stats To Display');
	add_option('stats_url', get_settings('siteurl').'/stats/', 'Stats URL');
}
?>