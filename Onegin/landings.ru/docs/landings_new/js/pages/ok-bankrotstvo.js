/* global landing */

landing.priceInit = function(){
	var $winBuyItem = $('#prices_select_method_of_payment');

	$('.btn-buy').on('click', function(){
		var price = $(this).data('price');
		var name = $(this).data('name');

		$('.price-text', $winBuyItem).text(price);
		$('.targets-text', $winBuyItem).text(name);

		$('input[name="sum"]', $winBuyItem).val(price);
		$('input[name="targets"]', $winBuyItem).val(name);

		landing.dialog($winBuyItem, {width: 456, height: 236});
	});

	$('.selector-type-payment img', $winBuyItem).on('click', function(){
		var paymentType = $(this).data('payment-type');

		$(this).addClass('active')
			.siblings().removeClass('active');

		$('input[name="paymentType"]', $winBuyItem).val(paymentType);
	});
};