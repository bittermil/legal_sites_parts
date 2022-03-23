<!DOCTYPE html>
<html>
    <head>
	   <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no, user-scalable=no">
	   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	   <title><?=$landing->config['title']?></title>
	   <meta name="description" content="<?=$landing->config['description']?>">
	   <meta name="keywords" content="<?=$landing->config['keywords']?>">

	   <meta property="og:title" content="<?=$landing->config['title']?>">
	   <meta property="og:description" content="<?=$landing->config['description']?>">
	   <meta property="og:image" content="<?=$landing->config['image']?>">

	   <meta name="yandex-verification" content="4aba3264950baa7f" />

	   <!-- jquery -->
	   <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

	   <!-- jquery-ui -->
	   <link rel="stylesheet" type="text/css" href="/landings_new/js/jquery-ui/jquery-ui.theme.min.css">
	   <script type="text/javascript" src="/landings_new/js/jquery-ui/jquery-ui.min.js"></script>

	   <!-- jquery-ui -->
	   <link rel="stylesheet" type="text/css" href="/landings_new/js/jquery-ui/jquery-ui.theme.min.css">
	   <script type="text/javascript" src="/landings_new/js/jquery-ui/jquery-ui.min.js"></script>

	   <!-- owl-carousel -->
	   <link rel="stylesheet" type="text/css" href="/landings_new/js/owl-carousel/owl.carousel.min.css">
	   <link rel="stylesheet" type="text/css" href="/landings_new/js/owl-carousel/owl.theme.default.min.css">
	   <link rel="stylesheet" type="text/css" href="/landings_new/js/owl-carousel/owl.theme.green.min.css">
	   <script type="text/javascript" src="/landings_new/js/owl-carousel/owl.carousel.min.js"></script>

	   <!-- fancybox-v3 -->
	   <link rel="stylesheet" type="text/css" href="/landings_new/js/fancybox-v3/jquery.fancybox.min.css">

	   <!-- скрипты сайта -->
	   <link rel="stylesheet" type="text/css" href="/landings_new/style/style.css?v=3">
	   <script type="text/javascript" src="/landings_new/js/init.js?v=6"></script>
	   <script type="text/javascript" src="/landings_new/js/promo-timer.js"></script>

	   <link rel="stylesheet" type="text/css" href="/landings_new/style/pages/<?=$landing->name?>.css?v=5">
	   <script type="text/javascript" src="/landings_new/js/pages/<?=$landing->name?>.js"></script>

	   <link rel="icon shortcut" type="image/ico" href="<?=$landing->config['favicon']?>">

	   <?include(dirname(__FILE__).'/index.head.js.php');?>
    </head>
    <body>
	   <div id="mainmenu" class="mainmenu hide_for_mobile">
		  <div class="wrapper">
			 <div class="els">
				<?

				foreach($landing->config['modules'] as $module){
				    if(!$module['menuName']) continue;
				    ?>
    				<a class="el" href="#<?=$module['id']?>"><?=$module['menuName']?></a>
				<? }?>
				<div class="el phone-icon" data-title="обратный звонок">
				</div>
				<a class="phone" href="tel:88003507981"><span class="hide_for_desktop">Заказать сейчас: </span> 8 (800) 350 79 81</a>

				<div class="<?=(core()->lang)?> el language" data-title="язык"></div>
				<div class="navigation els">
				    <div class="en el" data-lang="en">English</div>
				    <div class="ee el" data-lang="ee">Eesti</div>
				    <div class="fi el" data-lang="fi">Suomi</div>
				    <div class="cn el" data-lang="cn">中文</div>
				    <div class="lt el" data-lang="lt">Lietuvių</div>
				    <div class="lv el" data-lang="lv">Latviešu</div>
				    <div class="se el" data-lang="se">Svenska</div>
				</div>
			 </div>
		  </div>
	   </div>

	   <?

	   foreach($landing->config['modules'] as $module){
		  ?>
        	   <div class="<?=$module['class']?>" id="<?=$module['id']?>">
    		  <div class="wrapper">
				<?include($landing->folderTpl.'/modules/'.$module['tpl']);?>
    		  </div>
        	   </div>
		  <?

	   }

	   include(dirname(__FILE__).'/index.body.js.php');

	   include($landing->folderTpl.'/modules/feedback_popup.php');
	   include($landing->folderTpl.'/modules/ok-bankrotstvo/subscription_popup.php');
	   ?>
	   <div id="preloader" class="preloader"></div>
    </body>
</html>