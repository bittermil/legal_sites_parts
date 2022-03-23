<div <?=(!$landing->authOk)?'':'id="logout"'?> class="form <?=(!$landing->authOk)?'':'open'?>">
	<div class="title">Авторизация</div>

	<input name="email"  type="text"  placeholder="Ваш e-mail" <?=(!$landing->authOk)?'':'disabled'?>>
	<input name="code" type="password" placeholder="Ваш код" <?=(!$landing->authOk)?'':'disabled'?>>
	<input name="action" type="hidden" value="<?=(!$landing->authOk)?'bankrotstvoAuth':'bankrotstvoLogout'?>">

	<button class="button blue"><?=(!$landing->authOk)?'Войти':'Выйти'?></button>
	<a href="javascript:void(0);" data-price="5000" data-name="ok-bankrotstvo.ru - Оформление подписки" class="<?=(!$landing->authOk)?'btn-buy':''?> green">Оформить подписку</a>
</div>

<?include(dirname(__FILE__)).'/subscription.php';?>


<div id="auth_feedback_popup" class="form">
	<div>Данные введены верно. Теперь Вам доступен весь функционал сайта.</div>

	<button class="button">OK</button>
</div>
