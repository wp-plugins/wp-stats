-> Installation Instructions
--------------------------------------------------
// Open root Wordpress folder

Put
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
<li>
------------------------------------------------------------------