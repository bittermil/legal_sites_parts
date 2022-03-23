<h2>Omdöme</h2>

<div class="els">
	<?
	$images = $landing->getImagesForReview();
	foreach($images as $image){
		?>
		<a href="<?=$image?>" data-fancybox="album-reviews" class="el">
			<img src="/landings_new/libraries/phpthumb/phpThumb.php?src=<?=$image?>&amp;w=210&amp;h=160&amp;q=95" width="210" height="160" alt="">
		</a>
	<? }?>
</div>