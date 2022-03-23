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

// произвести редирект на другой хеш
landing.hashRedirects = function(){
	var redirects = {
		'#1':'#about_book',
		'#2':'#about_authors',
		'#3':'#why_buy',
		'#4':'#try_book',
		'#5':'#prices',
		'#6':'#confirmation_delivery',
		'#8':'#cooperation',
	};

	if(redirects[location.hash]) location.hash = redirects[location.hash];
};