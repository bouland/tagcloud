<?php
if (is_plugin_enabled('tagcloud')) {
	echo elgg_view_title(elgg_echo('tagcloud'));
	echo '<div class="contentWrapper">';
	echo tagcloud_create_cloud(25);
	echo '</div>';
}
?>