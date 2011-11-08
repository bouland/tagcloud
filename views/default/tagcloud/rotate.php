<?php
if (is_plugin_enabled('tagcloud')) {
	//echo elgg_view_title(elgg_echo('tagcloud'));
	if (!isset($vars['owner_guid'])) {
		$owner_guid = $vars['owner_guid'];
	}else{	
		$owner_guid = ELGG_ENTITIES_ANY_VALUE;
	}
	if (isset($vars['container_guid'])) {
		$container_guid = $vars['container_guid'];
	}else{
		$container_guid = ELGG_ENTITIES_ANY_VALUE;
	}
	if (isset($vars['container_guid'])) {
		$sort = $vars['container_guid'];
	}else{
		$sort = 'random';
	}
	if (isset($vars['scale'])) {
		$scale = $vars['scale'];
	}else{
		$scale = 1;
	}
	

	$options = array('owner_guids' => $owner_guid,
					'container_guids' => $container_guid,
					'limit' => 999,
					'threshold' => 0);
	$tags = elgg_get_tags($options);

	switch ($sort) {
		case 'alpha':
			usort($tags, create_function('$a, $b', 'return strcmp($a->tag, $b->tag);'));
			break;
		case 'random':
			shuffle($tags);
			break;
		case 'count':
		default:
			break;
	}
	$limit = 20;
	
	$counter = 0;
	$cloud = "";
	$total_max = 1;
	$total_min = 99999;
	$total_min_filter = 1;
	$index_init = 0;
	$index_max = count($tags);
	
	$index = $index_init;
	$index2 = 0;
	$selected_tags = array();
	// select at max $limit tags with at least $freq_min
	while ($index < $index_max && $index2 < $limit) {
		$tag = $tags[$index];
		if ($tag->total > $total_min_filter) {
			$selected_tags[] = $tag;
			$index2++;
		}
		if ($tag->total > $total_max) {
			$total_max = $tag->total;
		}
		if ($tag->total < $total_min) {
			$total_min = $tag->total;
		}
		$index++;
	}
	foreach ($selected_tags as $tag) {
/* I think this is overdoing what a simple linear scaling can handle
		// size is a percentage with the minimum percentage being 60%
		// protecting against division by zero warnings
		$size = round((log($tag->total) / log($max + .0001)) * 100 * $scale) + 30;
		if ($size < 60) {
			$size = 60;
		}
 */
		

		// colors - 4 to choose from - see css.php for color definitions


		$size = size_by_total($tag->total, $total_max, $total_min);

		$class = "tagcloud";
		if (get_plugin_setting('color','tagcloud') == 'yes') {
			$color_class = ceil(($size - 75) / 60.0);
			$class .= " tagcloud{$color_class}";
		}
		
		$encoded_tag = htmlspecialchars($tag->tag, ENT_QUOTES, 'UTF-8');
		$query = $encoded_tag;
		$style = "font-size: {$size};";
		//$style = "font-size: 8;";
		//$query = urlencode($query);
		//$encode = mb_detect_encoding($query);
		//$encoding_list = mb_list_encodings();
		//$query .= '&search_type=tags';
		//$query = mb_convert_encoding($query, 'ISO-8859-1', 'UTF-8');
		//$query = urlencode($query);
		$url = $vars['url'] . "pg/search/tagcloud/?q=". $query;
		//$url = mb_convert_encoding($url, 'ISO-8859-1', 'UTF-8');
		$cloud .= "<a href='$url' title='$encoded_tag ($tag->total)' class='$class' style='$style'>$encoded_tag</a>";
		$index++;
	}
	$cloud = "<tags>" . $cloud . "</tags>";
?>
	<div class="contentWrapper">
		<script type="text/javascript" src="<?php echo $vars['url'] ?>mod/tagcloud/vendors/swfobject.js"></script>
		<div id="flashcontent"></div>
		<script type="text/javascript">
			var so = new SWFObject("<?php echo $vars['url'] ?>mod/tagcloud/vendors/tagcloud.swf", "tagcloud", "100%", "375", "7", "#c0c0c0");
			so.addVariable("tcolor", "0x0e79af");
			so.addParam("wmode", "transparent");
			so.addVariable("tcolor2", "0xff6d06");
			so.addVariable("hicolor", "0xe41326");
			so.addVariable("mode", "");
			so.addVariable("distr", "true");
			so.addVariable("tspeed", "50");
			so.addVariable("tagcloud", "<?php echo $cloud ?>");
			so.write("flashcontent");
		</script>
	</div>
<?php
}
function size_by_total($freq, $freq_max = 1, $freq_min = 0, $scale = 1, $size_max = 36, $size_min = 8 ){
		return floor( ($size_max - $size_min) * ($freq - $freq_min)  / ($freq_max - $freq_min) ) + $size_min;
}
?>
