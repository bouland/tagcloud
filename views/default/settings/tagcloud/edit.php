<p>
<?php

echo elgg_echo('tagcloud:color_instruct') . ": ";

$color = get_plugin_setting('color','tagcloud');
if ($color === FALSE) {
	$color = 'no';
}

$options = array('yes', 'no');
echo elgg_view('input/pulldown', array('options' => $options,
										'value' => $vars['entity']->color,
										'internalname' => 'params[color]'));
?>
</p>