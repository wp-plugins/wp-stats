<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.0 Plugin: WP-Stats 2.04										|
|	Copyright (c) 2005 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://www.lesterchan.net													|
|																							|
|	File Information:																	|
|	- WordPress Statistics															|
|	- wp-content/plugins/stats/wp-stats.php									|
|																							|
+----------------------------------------------------------------+
*/


### Require WordPress Header
require('../../../wp-blog-header.php');

### Variables Variables Variables
$comment_author = urldecode(strip_tags(stripslashes(trim($_GET['author']))));
$page = intval($_GET['page']);
?>
<?php get_header(); ?>
	<div id="content" class="narrowcolumn">
<?php
	### Default wp-stats.php Page
	if(empty($comment_author)) {
?>
		<!-- General Stats -->
		<h2 class="pagetitle">General Stats</h2>
		<p><b>Total Stats</b></p>
		<ul>
			<li><b><?php get_totalauthors(); ?></b> Authors To This Blog.</li>
			<li><b><?php get_totalposts(); ?></b> Posts Were Posted.</li>
			<li><b><?php get_totalpages(); ?></b> Pages Were Created.</li>
			<li><b><?php get_totalcomments(); ?></b> Comments Were Posted.</li>
			<li><b><?php get_totalcommentposters(); ?></b> Different Nicks Were Represented In The Comments.</li>
			<li><b><?php get_totallinks(); ?></b> Links Were Added.</li>
		</ul>

		<!-- Plugin Stats -->
		<h2 class="pagetitle">Plugins Stats</h2>		
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

		<!-- Top 10 Stats-->
		<h2 class="pagetitle">Top 10 Stats</h2>
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

		<!-- Author Stats -->
		<h2 class="pagetitle">Authors Stats</h2>
		<p><b>Authors</b></p>
		<ol>
			<?php get_authorsstats(); ?>
		</ol>

		<!-- Comments' Members Stats -->
		<h2 class="pagetitle">Comments' Members Stats</h2>
		<p><b>Comment Members</b></p>
		<ol>
			<?php get_commentmembersstats(); ?>
		</ol>

		<!-- Misc Stats -->
		<h2 class="pagetitle">Misc Stats</h2>
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
		$gmz_comments =  $wpdb->get_results("SELECT $wpdb->posts.ID, comment_author, comment_date, comment_content, ID, comment_ID, post_date, post_title, post_name, post_password FROM $wpdb->comments INNER  JOIN $wpdb->posts ON $wpdb->comments.comment_post_ID = $wpdb->posts.ID WHERE comment_author =  '$comment_author_sql' AND comment_approved = '1' AND post_date < '".current_time('mysql')."' AND (post_status = 'publish' OR post_status = 'static') ORDER  BY comment_post_ID DESC, comment_date DESC  LIMIT $offset, $perpage");
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

					// Check For Password Protected Post
					if(!empty($post->post_password) && stripslashes($_COOKIE['wp-postpass_'.COOKIEHASH]) != $post->post_password) {
						// If New Title, Print It Out
						if($post_title != $cache_post_title) {
							echo "<p><b><a href=\"".get_permalink()."\" title=\"Posted On $post_date\">Protected: $post_title</a></b></p>\n";
							echo "<blockquote>Comments Protected</blockquote>\n";	
						}							
					} else {
						// If New Title, Print It Out
						if($post_title != $cache_post_title) {
							echo "<p><b><a href=\"".get_permalink()."\" title=\"Posted On $post_date\">$post_title</a></b></p>\n";
						}
						echo "<blockquote>$comment_content <a href=\"".get_permalink()."#comment-$comment_id\">Comment</a> Posted By <b>$comment_author2</b> On $comment_date</blockquote>\n";						
					}
					$cache_post_title = $post_title;
				}
			} else {
					echo "<p>$comment_author has not made any comments yet.</p>\n";
			}

			// If Total Pages Is More Than 1, Display Page Navigation
			if($totalpages > 1) {
		?>
				<p>
					<span style="float: left">
						<?php
							if($page > 1 && ((($page*$perpage)-($perpage-1)) < $totalcomments)) {
								echo '<b>&laquo;</b> <a href="'.get_settings('siteurl').'/wp-content/plugins/stats/wp-stats.php?author='.$comment_author_link.'&amp;page='.($page-1).'" title="&laquo; '.__('Previous Page').'">'.__('Previous Page').'</a>'."\n";
							} else {
								echo '&nbsp;'."\n";
							}
						?>
					</span>
					<span style="float: right">
						<?php
							if($page >= 1 && ((($page*$perpage)+1) <  $totalcomments)) {
								echo '<a href="'.get_settings('siteurl').'/wp-content/plugins/stats/wp-stats.php?author='.$comment_author_link.'&amp;page='.($page+1).'" title="'.__('Next Page').' &raquo;">'.__('Next Page').'</a> <b>&raquo;</b>'."\n";
							} else {
								echo '&nbsp;'."\n";
							}
						?>
					</span>
				</p>
				<br style="clear: both" />
				<p align="center">
					Pages (<?php echo $totalpages; ?>) :
					<?php
						if ($page >= 4) {
							echo '<a href="'.get_settings('siteurl').'/wp-content/plugins/stats/wp-stats.php?author='.$comment_author_link.'" title="'.__('Go to First Page').'">&laquo; '.__('First').'</a></b> ... '."\n";
						}
						if($page > 1) {
							echo ' <a href="'.get_settings('siteurl').'/wp-content/plugins/stats/wp-stats.php?author='.$comment_author_link.'&amp;page='.($page-1).'" title="&laquo; '.__('Go to Page').' '.($page-1).'">&laquo;</a></b> '."\n";
						}
						for($i = $page - 2 ; $i  <= $page +2; $i++) {
							if ($i >= 1 && $i <= $totalpages) {
								if($i == $page) {
									echo "<b>[$i]</b> "."\n";
								} else {
									echo '<a href="'.get_settings('siteurl').'/wp-content/plugins/stats/wp-stats.php?author='.$comment_author_link.'&amp;page='.$i.'" title="'.__('Page').' '.$i.'">'.$i.'</a> '."\n";
								}
							}
						}
						if($page < $totalpages) {
							echo ' <b><a href="'.get_settings('siteurl').'/wp-content/plugins/stats/wp-stats.php?author='.$comment_author_link.'&amp;page='.($page+1).'" title="'.__('Go to Page').' '.($page+1).' &raquo;">&raquo;</a></b> '."\n";
						}
						if (($page+2) < $totalpages) {
							echo ' ... <b><a href="'.get_settings('siteurl').'/wp-content/plugins/stats/wp-stats.php?author='.$comment_author_link.'&amp;page='.$totalpages.'" title="'.__('Go to Last Page').'">'.__('Last').' &raquo;</a></b>'."\n";
						}
					?>
				</p>
		<?php
			}
		?>
		<p><b>&laquo;&laquo;</b> <a href="<?php echo get_settings('siteurl'); ?>/wp-content/plugins/stats/wp-stats.php">Back To Stats Page</a></p>
<?php
	} // End If
?>
	</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>