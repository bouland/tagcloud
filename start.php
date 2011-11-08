<?php

/**
 * Tag cloud plugin
 * Cash Costello
 */

/**
 * Instructions for including on front page
 *
 * If using the custom_index plugin, edit the layout view. This is found at
 * /mod/custom_index/views/default/canvas/layouts/new_index.php. Each section
 * begins with a "if (is_plugin_enabled()) {" and ends with a closing bracket
 * "}". Be careful when editing this file to make sure every <?php has a
 * corresponding ?>.
 *
 * To insert the front page tag cloud, figure out where you want it and then
 * add this code:

<?php
if (is_plugin_enabled('tagcloud')) {
?>
	<!-- display latest tags -->
	<div class="index_box">
		<h2><?php echo elgg_echo('tagcloud'); ?></h2>
		<div class="contentWrapper">
			<?php echo tagcloud_create_cloud(25); ?>
		</div>
	</div>
<?php
}
?>

 * See the documentation for tagcloud_create_cloud() for information on its
 * options.
 */



/**
 * Display a tag cloud
 * 
 * @param int $num_tags Number of tags to display
 * @param int $owner_guid Who's tags - 0 for all site tags
 * @param int $container_guid What group's tags - 0 to ignore this
 * @param string $sort Sort type: random, alpha, count (popularity)
 * @param int $scale How large should the tags be (default is 100)
 * @return string
 */
function tagcloud_create_cloud($num_tags, $owner_guid=0, $container_guid=0, $sort='random', $scale=1) {
	
	if ($owner_guid == 0) {
		$owner_guid = ELGG_ENTITIES_ANY_VALUE;
	}
	if ($container_guid == 0) {
		$container_guid = ELGG_ENTITIES_ANY_VALUE;
	}

	$options = array('owner_guids' => $owner_guid,
					'container_guids' => $container_guid,
					'limit' => $num_tags,
					'threshold' => 0);
	$tag_data = elgg_get_tags($options);

	$view_vars = array(	'value' => $tag_data,
						'sort' => $sort,
						'scale' => $scale,
					);
	if ($owner_guid) {
		$view_vars['owner_guid'] = $owner_guid;
	}
	if ($container_guid) {
		$view_vars['container_guid'] = $container_guid;
	}

	return elgg_view("output/tagcloud", $view_vars);
}


/**
 * init tag cloud plugin
 */
function tagcloud_init() {
	unregister_page_handler('search','search_page_handler');
	register_page_handler('search','search_page_handler_tagcloud');
	
	elgg_extend_view('css','tagcloud/css');
	
	elgg_extend_view('custom_index_inria/extend','tagcloud/rotate');

	add_widget_type('tagcloud', elgg_echo('tagcloud:widget:title'), elgg_echo('tagcloud:widget:description'));
}
/**
 * Replace search handler to deal with tagcloud links
 * These links have the folowing  structure :
 * $vars['url'] . "pg/search/tag/" . $tag->tag
 **/


function search_page_handler_tagcloud($page) {
	global $CONFIG;
	// if there is no q set, we're being called from a legacy installation
	// it expects a search by tags.
	// actually it doesn't, but maybe it should.
	// maintain backward compatibility
	
	switch ($page[0]) {
		case 'tag':
			set_input('q', $page[1]);
			set_input('search_type', 'tags');
			include_once('search/index.php');
		case 'tagcloud':
			set_context('tagcloud');
		default:
			if(!get_input('q', get_input('tag', NULL))) {
				set_input('q', $page[0]);
				//set_input('search_type', 'tags');
			}
			include_once('search/index.php');
			break;
	}
	
}

register_elgg_event_handler('init', 'system', 'tagcloud_init');	
?>