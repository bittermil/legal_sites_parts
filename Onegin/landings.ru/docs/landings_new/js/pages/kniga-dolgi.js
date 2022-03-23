/* global landing */

$(function(){
	var $winWordsToDeedsFeedback = $('#win_words_to_deeds_feedback');

	$('.guarantees_decency .thanks-link').on('click', function(){
		$('.gallery .els .el:nth-child(1)').click();
	});

	// блок "От слов к делу"
	$('.from_words_to_deeds .form_opener').click(function(){
		landing.dialog($winWordsToDeedsFeedback, {width: 360});
	});
});