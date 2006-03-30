-> Installation Instructions
--------------------------------------------------
// Open wp-content/plugins folder

Put:
------------------------------------------------------------------
stats.php
------------------------------------------------------------------


// Activate WP-Stats Plugin


// Open root Wordpress folder

Put:
------------------------------------------------------------------
wp-stats.php
------------------------------------------------------------------


// Open wp-content/themes/<YOUR THEME NAME>/sidebar.php 

Add:
------------------------------------------------------------------
<li>
	<h2>Statistics</h2>
	<ul>
		<li><a href="<?php echo get_settings('home'); ?>/wp-stats.php">My Blog Stats</a></li>
	</ul>
</li>
------------------------------------------------------------------


// Tutorial On How To Integrate wp-stats.php With Your Theme

Go To:
------------------------------------------------------------------
http://www.lesterchan.net/wordpress/tutorials/integrating/
------------------------------------------------------------------