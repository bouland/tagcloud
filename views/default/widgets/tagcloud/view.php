<div class="contentWrapper">
<?php

	$num_items = $vars['entity']->num_items;
	if (!isset($num_items)) {
		$num_items = 30;
	}

	echo tagcloud_create_cloud($num_items, page_owner());
?>
</div>
