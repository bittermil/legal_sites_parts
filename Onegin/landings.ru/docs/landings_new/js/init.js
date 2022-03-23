/* global catcher VK, mo */

$(function(){
    landing.init();
});

var landing = {};

landing.init = function(){
    landing.hashRedirects();
   // landing.adaptiveWin(); // масштабирование для мобильных экранов
    landing.dialogInit();
    landing.galleryInit();
    landing.formInit(); // общий функционал форм
    landing.VKInit();
    landing.priceInit();
    landing.sliderInit();
    landing.preloader();
    landing.preloaderStopAll();
    landing.protected_edit_setLang();

    // плавный скролл к якорю
    $('[href^="#"]').on('click', function(){
	   var selector = $(this).attr('href');
	   var $el = $(selector);

	   landing.scrollTo($el);
    });

    $.ui.dialog.prototype._focusTabbable = $.noop;
};

landing.hashRedirects = function(){

};

//landing.adaptiveWin = function(){
    //var side = ($(window).width() > $(window).height())?$(window).width():$(window).height();

    //if(side > 750){
	   //$('meta[name=viewport]').attr('content', 'initial-scale=1.5, user-scalable=no');
    //}
//};

landing.dialogInit = function(){
    // закрытие окошка (клик на иконку закрытия окна)
    $(document).on('click', '.ui-dialog a.close', function(e){
	   e.preventDefault();
	   $(this).closest('.ui-dialog-content').dialog('close');
    });

    // закрытие окошка (клик на фон)
    $(document).on('click', '.ui-widget-overlay', function(e){
	   $('.ui-dialog-content:visible:last').dialog('close');
    });

    // открыть окно обратной связи (клик на телефонную трубку)
    $('.phone-icon').on('click', function(){
	   landing.dialog($('#feedback_popup'), {width:343, height:170});
	   if($('#feedback_popup .title').length == 0)$('.feedback .form .title').clone().prependTo($('#feedback_popup'));
    });

    // открыть окошко "Прохождение практики"
    //$('a.practice').on('click', function(){
	//   landing.dialog($('#practice-feedback-win'), {width:600});
    //});

    $('.open-pay').on('click', function(e){
	   e.preventDefault();
	   landing.dialog($('#subscription_popup'), {width:400});
    });

    // открыть окошко "Политика конфиденциальности"
    $('a.open_policy').click(function(e){
	   e.preventDefault();

	   $('html, body').scrollTop(50);
	   landing.dialog($('#policy_win'), {width:600});
    });

    // открыть окошко "Правила участия"
    $('a.open_terms').click(function(e){
	   e.preventDefault();

	   if($(window).width() < 1000){
		  $('html, body').scrollTop(150);
		  landing.dialog($('#terms'), {width:500});
	   }
	   landing.dialog($('#terms'), {width:600});

    });

	// открыть окошко "Закон"
    $('a.law').on('click', function(e){
		console.log(1);
		e.preventDefault();
		landing.dialog($('#law'), {width:600});
    });

    // открыть форму обратной связи
    $('.feedback .el .feedback_button .button').click(function(e){
	   e.preventDefault();
	   landing.dialog($('#feedback_with_city'), {width:400});
    });

    // открыть форму для заявки
    $('.partner-option .buttons .apply').click(function(e){
	   e.preventDefault();
	   landing.dialog($('#feedback_apply'), {width:400});
	   var textToCopy = $(this).parent().siblings('.block-title').text();
	   $('#feedback_apply .title span').text(textToCopy);
    });

    // развернуть информацию
    $('.link_description .wrap .less').click(function(e){
	   e.preventDefault();
	   $(this).parent().addClass('hidden');
	   $(this).parent().siblings('.link_description .text .more').removeClass('open');
    });

    // свернуть информацию
    $('.link_description .text .more').click(function(e){
	   e.preventDefault();
	   $(this).next('.wrap').removeClass('hidden');
	   $(this).addClass('open');
    });

    // изменение размеров диалоговых окон при изменении ширины экрана
    $(window).on('resize.dialog', function(){
	   var $openedDialogs = $('.ui-dialog-content:visible');

	   $.map($openedDialogs, function(el){
		  landing.dialogSetAutosize($(el));
	   });
    });

    // навигация
    $('.videos .arrow-left, .reviews .items .arrow-left').click(function(){
	   $('.owl-carousel .owl-nav .owl-prev').click();
    });

    $('.videos .arrow-right, .reviews .items .arrow-right').click(function(){
	   $('.owl-carousel .owl-nav .owl-next').click();
    });

    // переключение между опциями в блоке "Что вы получите"
    $('.what-you-get .switch span').on('click', function(){
	   if($(this).is('active'))return;
	   $('.what-you-get .switch span').removeClass('active');
	   $(this).addClass('active');

	   $('.what-you-get .lines:visible').removeClass('active');
	   $('.what-you-get .lines.hidden').addClass('active').removeClass('hidden');
	   $('.what-you-get .lines:hidden').addClass('hidden');
    });

	 // переключение между опциями в блоке "Структура", лендинг "Практиканты"
    $('.structure .switch span').on('click', function(){
	   if($(this).is('active')) return;
	   $('.structure .switch span').removeClass('active');
	   $(this).addClass('active');

	   $('.structure .cols:visible').removeClass('active');
	   $('.structure .cols.hidden').addClass('active').removeClass('hidden');
	   $('.structure .cols:hidden').addClass('hidden');
    });

    // выпадающее меню со списокм языков по клику
    $('#mainmenu .els .language').on('click', function(){
	   $(this).next('.navigation').slideToggle();
    });

    // закрытие выпадающего меню по клику на фон
    $(document).on('click', function(e){
	   var el = $('.navigation');
	   if (!el.is(e.target) && !el.children().is(e.target) && !$('.mainmenu .els .language').is(e.target)) el.hide();
	});

    // открыть галерею сертификатов
    $('.why-us .lines .line .run-docs').click(function(){
	   $('.gallery.docs .el:nth-child(1)').click();
    });

    // открыть галерею отзывов
    $('.what-you-get .text-lines .line .run-reviews').click(function(){
	   $('.gallery.reviews .el:nth-child(1)').click();
    });
};

landing.galleryInit = function(){
    var isZoom = false;
    $(document).on('click', '.fancybox-container.fancybox-is-zoomable', function(){
	   isZoom = $(this).is('.fancybox-can-drag');
    });

    $('[data-fancybox]').fancybox({
	   mobile:{clickSlide:'close'},
	   loop:'true',
	   infobar:'true',
	   arrows:'true',
	   beforeShow:function(e){
		  setTimeout(function(){
			 if(!isZoom)return;
			 if($(window).width() < 1000)return;

			 var instance = $.fancybox.getInstance();
			 instance.scaleToActual(0, 0, 1);
		  });
	   }
    });

};

landing.sliderInit = function(){
    $('.slider-korobka').owlCarousel({
	   loop:true,
	   nav:true,
	   navText:["<img src=''>", "<img src=''>"],
	   margin:10,
	   mouseDrag:false,
	   touchDrag:false,
	   video:true,
	   responsive:{
		  300:{items:1},
		  580:{items:2},
		  760:{items:3}
	   }
    });
};

landing.formInit = function(e){
    // добавить название файла при загрузке
    $('.attached').on('change', function(e){
	   var fileName = $(this).val();
	   fileName = e.target.value.split('\\').pop();

	   if(fileName.substr(fileName.length - 3) != 'zip' && fileName.substr(fileName.length - 3) != 'rar'){
		  $('#success_win p').html('Запрещенный формат файла. Прикрепите файл формата .zip или .rar');
		  landing.dialog($('#success_win'), {width:350, height:100});

		  return;
	   }

	   if(fileName.length > 8){
		  fileName = fileName.substring(0, 8) + "..." + fileName.substr(fileName.length - 3);
	   }

	   var $fileNameEl = $('.remove_files_to_send span');

	   $.map($('.attached'), function(el){
		  var $el = $(el);
		  if(fileName){
			 $fileNameEl.empty();
			 $fileNameEl.append(fileName);
			 $('.remove_files_to_send').show();
		  }
	   });
    });

    $('.remove_files_to_send').on('click', function(){
	   $this = $(this);
	   $.map($('.attached'), function(el){
		  var $el = $(el);
		  if($el.val().length){
			 $el.val('');
			 $('[name="message"]').val('');
			 $('.remove_files_to_send').hide();
		  }
		  ;
	   });
    });


    // маска для ввода телефона
    $('input[name="phone"]').mask('+7 (999) 9999999');
    if($('input[name="phone"]').is('#taro_phone_input'))$('input[name="phone"]').mask('+0##').attr('maxlength', 23);
    if($('input[name="phone"]').is('#about_company_phone_input'))$('input[name="phone"]').mask('###').attr('maxlength', 100);

    $('input[name="phone"]').focus(function(){
	   if(!$(this).val() && $(this).is('#taro_phone_input'))$(this).val('+').keyup();
	   if(!$(this).val() && (!$(this).is('#about_company_phone_input')))$(this).val('+7 (').keyup();
    });

    $('input[name="phone"]').focusout(function(){
	   if($(this).val() == '+' && $(this).is('#taro_phone_input'))$(this).val('').keyup();
	   if($(this).val() == '+7 (')$(this).val('').keyup();
    });

    // запрет ввода буквенных символов в поле телефона
    $('input[name="phone"]').keydown(function(e){
	   if(!e.key)return;

	   // не символы
	   if(e.key == 'Delete')return;
	   if(e.key == 'Backspace')return;
	   if(e.key == 'Tab')return;
	   if($('input[name="phone"]').is('#taro_phone_input') && e.key == '+')return;

	   // не число и не тире
	   if(!e.key.match(/[0-9-]/))e.preventDefault();
    });

    // FIX: keyCode android mobile browser (а так же для ПК при быстром вводе)
    $('input[name="phone"]').keyup(function(){
	   // неверный код телефона
	   $(this).val($(this).val().replace(/(\+7 \(\d{0,2})\).*/, '$1'));

	   // неверный номер телефона
	   $(this).val($(this).val().replace(/(\+7 \(\d{3}\) \d{0,2})(-|$)/, '$1'));
	   $(this).val($(this).val().replace(/(\+7 \(\d{3}\) \d{3}-\d{0,1})(-|$)/, '$1'));
	   $(this).val($(this).val().replace(/(\+7 \(\d{3}\) \d{3}-\d{2}-\d{0,1})(-|$)/, '$1'));
    });

    // запрет ввода цифр в поле имени и города
    $('input[name="name"], input[name="city"]').keydown(function(e){
		if(!e.key)return;

	   // не символы
	   if(e.key == 'Delete')return;
	   if(e.key == 'Backspace')return;
	   if(e.key == 'Tab')return;
	   if(e.key == '-')return;

	   // не буквы
	   if(!e.key.match(/[a-zа-яё ]/i))e.preventDefault();
	});

    // FIX: keyCode android mobile browser
    $('input[name="name"], input[name="city"]').keyup(function(e){
	   if(e.keyCode == 229 || !e.keyCode){
		  $(this).val($(this).val().replace(/[^a-zа-яё ]/ig, ''));
	   }
    });

    $('.form').keydown(function(e){
	   if(e.keyCode == 13)landing.formSend($(this));
    });

    $(document).on('click', '.form .button', function(){
	   landing.formSend($(this).closest('.form'));
    });
};

// открыть диалоговое окно
landing.dialog = function($el, options){
    if(options === undefined)options = {};
    var isFirstOpen = !$el.data('defaultWidth');

    if(!$('a.close', $el).length)$el.append('<a href="." class="close"></a>');

    options = $.extend({
	   modal:true,
	   resizable:false,
	   show:{duration:300},
	   hide:{duration:300}
    }, options);

    $el.dialog(options);

    // диалоговое открывается первый раз, запомнить оригинальный размер
    if(isFirstOpen)$el.data('defaultWidth', options.width);

    landing.dialogSetAutosize($el);
};

// отрегулировать ширину диалогового окна
landing.dialogSetAutosize = function($el){
    var width = $(window).width() - 40;
    var defaultWidth = $el.data('defaultWidth');

    if(width && width > defaultWidth)width = defaultWidth;

    $el.dialog('option', 'width', width);
};

landing.inProcess = false;
landing.formSend = function($form){
    if(landing.inProcess)return;

    $form.find('.error').removeClass('error');

    var type = 'call';
    if($form.closest('#feedback').length)type = 'top';
    if($form.closest('#feedback_2').length)type = 'bottom';
    if($form.is('#win_words_to_deeds_feedback'))type = 'popup';
    if($form.closest('#feedback_with_message').length)type = 'textmessage';
    if($form.closest('#authorization').length)type = 'authorization';
    if($form.closest('#authorization').length && $form.is('#logout'))type = 'logOut';
    if($form.is('#feedback_with_city'))type = 'feedbackwithcity';
    if($form.is('#feedback_with_city_franshiza'))type = 'feedbackwithcityfranshiza';
    if($form.is('#feedback_with_city_lang'))type = 'feedbackwithcitylang';
    if($form.is('#feedback_apply'))type = 'feedbackapply';

    var $name = $('[name="name"]', $form);
    var $phone = $('[name="phone"]', $form);
    var $city = $('[name="city"]', $form);
    var $lang = $('[name="lang"]', $form);
    var $email = $('[name="email"]', $form);
    var $message = $('[name="message"]', $form);
    var $file = $('[name="file"]', $form);
    var $code = $('[name="code"]', $form);
    var $program = $('.title span', $form);
    var action = $('[name="action"]', $form).val() || 'feedback';

    var emailRegex = /.+@.+\..+/i;

    if(!$form.is('.open')){
	   if(!$name.is('#taro_name_input') && $name.length && $name.val().length < 2) $name.addClass('error');
	   if($name.is('#taro_name_input') && $name.length && $name.val().length < 3) $name.addClass('error');
	   if($email.length && $email.val().length < 5) $email.addClass('error');
	   if($email.length && !$email.val().match(emailRegex)) $email.addClass('error');
	   if(!$phone.is('#taro_phone_input') && !$phone.is('#about_company_phone_input') && $phone.length && $phone.val().length < 16) $phone.addClass('error');
	   if($phone.is('#taro_phone_input') && $phone.length && $phone.val().length < 6) $phone.addClass('error');
	   if($phone.is('#about_company_phone_input') && $phone.val().length < 6) $phone.addClass('error');
	   if($city.length && $city.val().trim().length < 3) $city.addClass('error');
	   if($message.length && $message.val().length < 5) $message.addClass('error');
	   if($code.length && $code.val().length < 1) $code.addClass('error');
    }

    if($('.error', $form).length)return;

    landing.preloader();

    var data = {
	   action:action
    };

    if($file.length)data.files = $file[0].files;
    if($code.length)data.code = $code.val();
    if($name.length)data.name = $name.val();
    if($phone.length)data.phone = $phone.val();
    if($city.length)data.city = $city.val();
    if($lang.length)data.lang = $lang.val();
    if($email.length)data.email = $email.val();
    if($message.length)data.message = $message.val();
    if($program.length)data.program = $program.text();
    if(type.length)data.type = type;

    landing.uploadFile(
		  '.',
		  data,
		  data.files,
		  function(){ },
		  function(res){
			 landing.inProcess = false;
			 landing.preloaderStopAll();
			 if(res && type != 'logOut' || res != 1 && type == 'authorization'){
				$('#success_win p').empty();
				$('#success_win p').html(res.message);
				landing.dialog($('#success_win'), {width:350, height:100});

				//очистка формы в коробке
				if($('.offline_support').length){
				    $('[name="message"]').val('');
				    $('.remove_files_to_send span').empty();
				    $('.remove_files_to_send').hide();
				}
			 }

			 if(type == 'authorization' && res == 1){
				$('#success_win p').empty();
				$('<p>Данные введены верно! Теперь Вам доступен весь функционал сайта.</p>').appendTo('#success_win');
				$('.ui-dialog .ui-dialog-content a.close').on('click', function(){
				    location.reload();
				});
			 }

			 if(type == 'logOut')location.reload();

			 if(type == 'call')$('#feedback_popup').dialog('close');
			 if(type == 'feedbackwithcity')$('#feedback_with_city').dialog('close');
			 if(type == 'feedbackapply')$('#feedback_apply').dialog('close');

			 if(type == 'popup')$('#win_words_to_deeds_feedback').dialog('close');
		  }, 'json',
		  function(){
			 landing.inProcess = false;
		  }
    );
};

landing.uploadFile = function(url, data, files, onProgress, onLoad, onError){
    var formData = new FormData();

    $.map(files, function(file, name){
	   formData.append(name, file);
    });

    $.map(data, function(value, name){
	   formData.append(name, value);
    });

    var xhr = new XMLHttpRequest();

    xhr.upload.onprogress = onProgress;
    xhr.onload = function(){
				 console.log(this.responseText);
	   var data = JSON.parse(this.responseText);
	   onLoad(data);
    };
    xhr.onerror = onError;

    xhr.open('POST', url, true);
    xhr.send(formData);
};

landing.scrollTo = function($el){
    $('html, body').animate({
	   scrollTop:($el.offset().top - 44)
    }, 800);
};

// Информация о загрузке
landing.preloader = function(show, $preloader, no_spin){
    if(no_spin == undefined)no_spin = false;
    if(show == undefined)show = true;

    var preloader_inner_html = '<div class="preloader-window"><i class="throbber"></i></div>';

    if($preloader == undefined)$preloader = $('#preloader');
    if($('> .preloader', $preloader).length)$preloader = $('> .preloader:eq(0)', $preloader);
    if($('.preloader', $preloader).length){
	   $preloader = $('.preloader:eq(0)', $preloader);
    }else if(!$preloader.is('.preloader')){
	   if($preloader.css('position') == 'static')$preloader.css('position', 'relative');
	   $preloader = $('<div class="preloader">' + preloader_inner_html + '</div>').appendTo($preloader);
    }
    if($preloader.html() == '')$preloader.html(preloader_inner_html);

    // масштаба прелоадера
    var k = 0.2;
    if($preloader.height() < 300)k = 0.7;
    var size = Math.round($preloader.height() * k);
    if(size > 74)size = 74;
    $('.throbber', $preloader).css({
	   fontSize:size + 'px',
	   lineHeight:size + 'px',
	   width:size,
	   height:size,
	   marginTop:-size / 2,
	   marginLeft:-size / 2
    });

    if(show){
	   $preloader.addClass('showed');

	   if(no_spin)
		  $('.throbber', $preloader).hide();
	   else
		  $('.throbber', $preloader).show();
    }else{
	   $preloader.removeClass('showed');
    }
};

landing.preloaderStopAll = function(){
    $('.preloader').removeClass('showed');
};

// Виджеты должны инициироваться (удаляться и создаваться заново) каждый раз при изменении размеров окна (иначе они не будут изменять свои размеры)
// Все теги с виджетами должны иметь подобный id="vk_XXXX"
// Все теги с виджетами должны иметь атрибут с id группы data-groupd-id="NN"
// Инициализация виджета происходит только п/осле паузы 100ms. Это сделано для того, чтобы не происходило многократное удаление и создание новыхЁЭъё
//ъъъ
// ъёЭвиджетов во время изменения размеров окна
var timer = landing.VKTimer;
landing.VKInit = function(){
    $(window).on('resize.VK', function(){
	   clearTimeout(landing.VKTimer);

	   landing.VKTimer = setTimeout(function(){
		  var $VKEls = $('[id^="vk_"]');
		  $VKEls.css({width:'auto', height:'auto'}).empty();

		  $.map($VKEls, function(el){
			 var $el = $(el);

			 var elId = $el.attr('id');
			 var groupId = $el.data('groupId');

			 VK.Widgets.Group(elId, {mode:3, no_cover:1, width:'auto'}, groupId);
		  });
	   }, 100);
    });

    $(window).trigger('resize.VK');
};

landing.priceInit = function(){
    var $winBuyItem = $('#prices_select_method_of_payment');

    $('.prices .btn-buy').on('click', function(){
	   var price = $(this).data('price');
	   var name = $(this).data('name');

	   $('.price-text', $winBuyItem).text(price);
	   $('.targets-text', $winBuyItem).text(name);

	   $('input[name="sum"]', $winBuyItem).val(price);
	   $('input[name="targets"]', $winBuyItem).val(name);

	   landing.dialog($winBuyItem, {width:456, height:236});
    });

    $('.selector-type-payment img', $winBuyItem).on('click', function(){
	   var paymentType = $(this).data('payment-type');

	   $(this).addClass('active')
			 .siblings().removeClass('active');

	   $('input[name="paymentType"]', $winBuyItem).val(paymentType);
    });
};

//переключение языка
landing.protected_edit_setLang = function(){
    $('#mainmenu .els .navigation .el').on('click', function(){
	   var lang = $(this).data('lang');
	   mo.edit2(function(){ location.reload(); }, 'users_2', 'setLang', {lang:lang});
    });
};