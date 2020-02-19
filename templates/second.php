<?php
?>
<ul>
	<?php
	foreach ($rel_item as $item) {
		//Каждый related post
		$thumbnail = $item['thumbnail'];
		$link = $item['link'];
		$text = $item['text'];
		?>
		<li>
			<a href="<?php echo $link; ?>"><img src="<?php echo $thumbnail ?>" /></a>
			<div><?php echo $text; ?></div>
		</li>
	<?php } ?>
</ul>
