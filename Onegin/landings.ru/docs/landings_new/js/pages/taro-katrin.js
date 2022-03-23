/* global landing */

$(function(){
	var $winBuyBook = $('#prices_select_method_of_payment');
	var $winWordsToDeedsFeedback = $('#win_words_to_deeds_feedback');

	// блок "Стоимость и оплата"
	$('.prices .btn-buy').on('click', function(){
		var price = $(this).data('price');
		var name = $(this).data('name');

		$('.price-text', $winBuyBook).text(price);
		$('.targets-text', $winBuyBook).text(name);

		$('input[name="sum"]', $winBuyBook).val(price);
		$('input[name="targets"]', $winBuyBook).val(name);

		landing.dialog($winBuyBook, {width: 456, height: 236});
	});

	$('.selector-type-payment img', $winBuyBook).on('click', function(){
		var paymentType = $(this).data('payment-type');

		$(this).addClass('active')
			.siblings().removeClass('active');

		$('input[name="paymentType"]', $winBuyBook).val(paymentType);
	});

	$('.guarantees_decency .thanks-link').on('click', function(){
		$('.gallery .els .el:nth-child(1)').click();
	});

	// блок "От слов к делу"
	$('.from_words_to_deeds .form_opener').click(function(){
		landing.dialog($winWordsToDeedsFeedback, {width: 360});
	});

	// блок "Карусель"
	$('.slider-taro').owlCarousel({
		items:5,
		loop: true,
		nav: true,
		navText: ["<img src=''>","<img src=''>"],
		margin:10,
		mouseDrag: false,
		touchDrag: false,
		video: true,
		responsive:{
			300:{ items:2 },
			480:{ items:2 },
			760:{ items:3 },
			1000:{ items:3 },
			1200:{ items:5 }
		}
	});
});