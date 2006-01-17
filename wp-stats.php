<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.0 Plugin: WP-Stats 2.01										|
|	Copyright (c) 2005 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://www.lesterchan.net													|
|																							|
|	File Information:																	|
|	- WordPress Statistics															|
|	- wp-stats.php																	|
|																							|
+----------------------------------------------------------------+
*/


### Require WordPress Header
require(dirname(__FILE__).'/wp-blog-header.php');

### Variables Variables Variables
$comment_author = urldecode(strip_tags(stripslashes(trim($_GET['author']))));
$page = intval($_GET['page']);

### Function: Get Total Posts
function get_totalposts() {
	global $wpdb;
	$totalposts = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_status = 'publish'");
	echo $totalposts;
}

### Function: Get Total Pages
function get_totalpages() {
	global $wpdb;
	$totalpages = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_status = 'static'");
	echo $totalpages;
}

### Function: Get Total Comments
function get_totalcomments() {
	global $wpdb;
	$totalcomments = $wpdb->get_var("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_approved = '1'");
	echo $totalcomments;
}

### Function: Get Total Comments Poster
function get_totalcommentposters() {
	global $wpdb;
	$totalcommentposters = $wpdb->get_var("SELECT COUNT(DISTINCT comment_author) FROM $wpdb->comments WHERE comment_approved = '1'");
	echo $totalcommentposters;
}

### Function: Get Total Links
function get_totallinks() {
	global $wpdb;
	$totallinks = $wpdb->get_var("SELECT COUNT(link_id) FROM $wpdb->links");
	echo $totallinks;
}

### Function: Get Recent Posts
function get_recentposts($limit = 10) {
    global $wpdb, $post;
    $recentposts = $wpdb->get_results("SELECT $wpdb->posts.ID, post_title, post_name, post_status, post_date, user_login FROM $wpdb->posts LEFT JOIN $wpdb->users ON $wpdb->users.ID = $wpdb->posts.post_author WHERE post_date < '".current_time('mysql')."' AND (post_status = 'publish' OR post_status = 'static') AND post_password = '' ORDER  BY post_date DESC LIMIT $limit");
	if($recentposts) {
		foreach ($recentposts as $post) {
				$post_title = htmlspecialchars(stripslashes($post->post_title));
				$post_date = mysql2date('d.m.Y', $post->post_date);
				$user_nickname = htmlspecialchars(stripslashes($post->user_login));
				echo "<li>$post_date - <a href=\"".get_permalink()."\">$post_title</a> ($user_nickname)</li>";
		}
	} else {
		echo '<li>'.__('N/A').'</li>';
	}
}

### Function: Get Recent Comments
function get_recentcomments($limit = 10) {
    global $wpdb, $post;
    $recentcomments = $wpdb->get_results("SELECT $wpdb->posts.ID, post_title, post_name, post_status, comment_author, post_date, comment_date FROM $wpdb->posts INNER JOIN $wpdb->comments ON $wpdb->posts.ID = $wpdb->comments.comment_post_ID WHERE comment_approved = '1' AND post_date < '".current_time('mysql')."' AND (post_status = 'publish' OR post_status = 'static') AND post_password = '' ORDER  BY comment_date DESC LIMIT $limit");
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
function get_mostcommented($limit = 10) {
    global $wpdb, $post;
    $mostcommenteds = $wpdb->get_results("SELECT $wpdb->posts.ID, post_title, post_name, post_status, post_date, COUNT($wpdb->comments.comment_post_ID) AS 'comment_total' FROM $wpdb->posts LEFT JOIN $wpdb->comments ON $wpdb->posts.ID = $wpdb->comments.comment_post_ID WHERE comment_approved = '1' AND post_date < '".current_time('mysql')."' AND (post_status = 'publish' OR post_status = 'static') AND post_password = '' GROUP BY $wpdb->comments.comment_post_ID ORDER  BY comment_total DESC LIMIT $limit");
	if($mostcommenteds) {
		foreach ($mostcommenteds as $post) {
				$post_title = htmlspecialchars(stripslashes($post->post_title));
				$comment_total = intval($post->comment_total);
				echo "<li><a href=\"".get_permalink()."\">$post_title</a> - $comment_total ".__('comments')."</li>";
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
				echo "<li><a href=\"wp-stats.php?author=$comment_author_link\">$comment_author</a> ($comment_total)</li>";
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

### Function: Get Poll Total Questions
function get_pollquestions() {
	global $wpdb;
	if(function_exists('get_poll')) {
		$totalpollq = $wpdb->get_var("SELECT COUNT(pollq_id) FROM $wpdb->pollsq");
		echo $totalpollq;
	}
}

### Function: Get Poll Total Answers
function get_pollanswers() {
	global $wpdb;
	if(function_exists('get_poll')) {
		$totalpolla = $wpdb->get_var("SELECT COUNT(polla_aid) FROM $wpdb->pollsa");
		echo $totalpolla;
	}
}

### Function: Get Poll Total Votes
function get_pollvotes() {
	global $wpdb;
	if(function_exists('get_poll')) {
		$totalpollip = $wpdb->get_var("SELECT COUNT(pollip_id) FROM $wpdb->pollsip");
		echo $totalpollip;
	}
}

### Function: Get EMail Total Sent
function get_emails() {
	global $wpdb;
	if(function_exists('wp_email')) {
		$totalemails = $wpdb->get_var("SELECT COUNT(email_id) FROM $wpdb->email");
		echo $totalemails;
	}
}

### Function: Get EMail Total Sent Success
function get_emails_success() {
	global $wpdb; 
	if(function_exists('wp_email')) {
		$totalemails_success = $wpdb->get_var("SELECT COUNT(email_id) FROM $wpdb->email WHERE email_status = '".__('Success')."'");
		echo $totalemails_success;
	}
}

### Function: Get EMail Total Sent Failed
function get_emails_failed() {
	global $wpdb; 
	if(function_exists('wp_email')) {
		$totalemails_failed = $wpdb->get_var("SELECT COUNT(email_id) FROM $wpdb->email WHERE email_status = '". __('Failed')."'");
		echo $totalemails_failed;
	}
}

### Function: Get Most Emailed Post
function get_mostemailed($limit = 10) {
	global $wpdb, $post;
	if(function_exists('wp_email')) {
		$mostemailed= $wpdb->get_results("SELECT $wpdb->posts.ID, post_title, post_name, post_date, COUNT($wpdb->email.email_postid) AS 'email_total' FROM $wpdb->email LEFT JOIN $wpdb->posts ON $wpdb->email.email_postid = $wpdb->posts.ID WHERE post_date < '".current_time('mysql')."' AND (post_status = 'publish' OR post_status = 'static') AND post_password = '' GROUP BY $wpdb->email.email_postid ORDER  BY email_total DESC LIMIT $limit");
		if($mostemailed) {
			foreach ($mostemailed as $post) {
					$post_title = htmlspecialchars(stripslashes($post->post_title));
					$email_total = intval($post->email_total);
					echo "<li><a href=\"".get_permalink()."\">$post_title</a> - $email_total ".__('Emails')."</li>";
			}
		} else {
			echo '<li>'.__('N/A').'</li>';
		}
	}
}
?>
<?php get_header(); ?>
	<div id="content" class="narrowcolumn">
<?php
	### Default wp-stats.php Page
	if(empty($comment_author)) {
?>
		<h2 class="pagetitle">General Stats</h2>
			<ul>
				<li><b><?php get_totalposts(); ?></b> Posts Were Posted</li>
				<li><b><?php get_totalpages(); ?></b> Pages Were Created</li>
				<li><b><?php get_totalcomments(); ?></b> Comments Were Posted</li>
				<li><b><?php get_totalcommentposters(); ?></b> Different Nicks Were Represented In The Comments</li>
				<li><b><?php get_totallinks(); ?></b> Links Were Added</li>
			</ul>
		<?php if(function_exists('get_poll')): ?>
			<h2 class="pagetitle">Poll Stats</h2>
			<ul>
				<li><b><?php get_pollquestions(); ?></b> Polls Were Created</li>
				<li><b><?php get_pollanswers(); ?></b> Polls' Answers Were Given</li>
				<li><b><?php get_pollvotes(); ?></b> Votes Were Casted</li>
			</ul>
		<?php endif; ?>
		<?php if(function_exists('wp_email')): ?>
			<h2 class="pagetitle">Email Stats</h2>
			<ul>
				<li><b><?php get_emails(); ?></b> Emails Were Sent</li>
				<li><b><?php get_emails_success(); ?></b> Emails Were Sent Successfully</li>
				<li><b><?php get_emails_failed(); ?></b> Emails Failed To Send</li>	
			</ul>
		<?php endif; ?>
		<h2 class="pagetitle">10 Recent Posts</h2>
			<ul><?php get_recentposts(); ?></ul>
		<h2 class="pagetitle">10 Recent Comments</h2>
			<ul><?php get_recentcomments(); ?></ul>
		<h2 class="pagetitle">10 Most Commented Post</h2>
			<ul><?php get_mostcommented(); ?></ul>
		<?php if(function_exists('wp_email')): ?>
			<h2 class="pagetitle">10 Most Emailed Post</h2>
				<ul><?php get_mostemailed(); ?></ul>
		<?php endif; ?>
		<h2 class="pagetitle">Comments' Members Stats</h2>
			<ol><?php get_commentmembersstats(); ?></ol>
		<h2 class="pagetitle">Post Categories Stats</h2>
			<ul><?php list_cats(1,'All','name','asc','',true,0,1,0,1,true,0,0,0,'','','',true); ?></ul>
		<h2 class="pagetitle">Link Categories Stats</h2>
			<ul><?php get_linkcats(); ?></ul>
<?php
	### Displaying Comments Posted By User
	} else {
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
		$gmz_comments =  $wpdb->get_results("SELECT $wpdb->posts.ID, comment_author, comment_date, comment_content, ID, comment_ID, post_date, post_title, post_name FROM $wpdb->comments INNER  JOIN $wpdb->posts ON $wpdb->comments.comment_post_ID = $wpdb->posts.ID WHERE comment_author =  '$comment_author_sql' AND comment_approved = '1' AND post_date < '".current_time('mysql')."' AND (post_status = 'publish' OR post_status = 'static') ORDER  BY comment_post_ID DESC, comment_date DESC  LIMIT $offset, $perpage");
?>
		<h2 class="pagetitle">Comments Posted By <?php echo $comment_author; ?></h2>
		<p>Displaying <b><?php echo $displayonpage; ?></b> To <b><?php echo $maxonpage; ?></b> Of <b><?php echo $totalcomments; ?></b> Comments</p>
		<?php
			// Get Comments
			if($gmz_comments) {
				foreach($gmz_comments as $post) {
					$comment_id = intval($post-> comment_ID);
					$comment_author2 = htmlspecialchars(stripslashes($post->comment_author));
					$comment_date = mysql2date('d.m.Y @ H:i', $post->comment_date);
					$comment_content = wpautop(stripslashes($post->comment_content));
					$post_date = mysql2date('d.m.Y @ H:i', $post->post_date);
					$post_title = htmlspecialchars(stripslashes($post->post_title));

					// If New Title, Print It Out
					if($post_title != $cache_post_title) {
						echo "<p><b><a href=\"".get_permalink()."\" title=\"Posted On $post_date\">$post_title</a></b></p>";
					}
					echo "<blockquote>$comment_content <a href=\"".get_permalink()."#comment-$comment_id\">Comment</a> Posted By <b>$comment_author2</b> On $comment_date</blockquote>";

					$cache_post_title = $post_title;
				}
			} else {
					echo "<p>$comment_author has not made any comments yet.</p>";
			}

			// If Total Pages Is More Than 1, Display Page Navigation
			if($totalpages > 1) {
		?>
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td align="left" width="50%">
					<br />
					<?php
						if($page > 1 && ((($page*$perpage)-($perpage-1)) < $totalcomments)) {
							echo '<p><b>&laquo;</b> <a href="wp-stats.php?author='.$comment_author_link.'&amp;page='.($page-1).'">Previous Page</a></p>';
						} else {
							echo '<p>&nbsp;</p>';
						}
					?>
				</td>
				<td align="right" width="50%">
					<br />
					<?php
						if($page >= 1 && ((($page*$perpage)+1) <  $totalcomments)) {
							echo '<p><a href="wp-stats.php?author='.$comment_author_link.'&amp;page='.($page+1).'">Next page</a> <b>&raquo;</b></p>';
						} else {
							echo '<p>&nbsp;</p>';
						}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<br />
					<p>Pages (<?php echo $totalpages; ?>):
					<?php
						if ($page >= 4) {
							echo "<a href=\"wp-stats.php?author=$comment_author_link\">&laquo; First</a> ... ";
						}
						if($page > 1) {
							echo " <a href=\"wp-stats.php?author=$comment_author_link&amp;page=".($page-1)."\">&laquo;</a> ";
						}
						for($i = $page - 2 ; $i  <= $page +2; $i++) {
							if ($i >= 1 && $i <= $totalpages) {
								if($i == $page) {
									echo " [$i] ";
								} else {
									echo "<a href=\"wp-stats.php?author=$comment_author_link&amp;page=$i\">$i</a> ";
								}
							}
						}
						if($page < $totalpages) {
							echo " <a href=\"wp-stats.php?author=$comment_author_link&amp;page=".($page+1)."\">&raquo;</a> ";
						}
						if (($page+2) < $totalpages) {
							echo " ... <a href=\"wp-stats.php?author=$comment_author_link&amp;page=$totalpages\">Last &raquo;</a>";
						}
					?>
					</p>
				</td>
			</tr>
		</table>
		<?php
			}
		?>
		<p><b>&laquo;&laquo;</b> <a href="<?php get_settings('home'); ?>wp-stats.php">Back To Stats Page</a></p>
<?php
	} // End If
?>
	</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>