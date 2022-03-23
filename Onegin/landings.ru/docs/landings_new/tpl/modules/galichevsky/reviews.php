<div class="els">
	<?
	$images = $landing->getImagesForReview();
	foreach($images as $image){
		?>
		<a href="<?=$image?>" data-fancybox="album-reviews" class="el">
			<img src="/landings_new/libraries/phpthumb/phpThumb.php?src=<?=$image?>&amp;w=141&amp;h=200&amp;q=95" width="141" height="200" alt="">
		</a>
	<? }?>
</div>