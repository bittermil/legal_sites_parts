<h2 class="<?=(!$landing->authOk)?'':'open'?>">Необходимые документы</h2>
<div class="el">
	<ul>
		<li><a href="<?=($landing->authOk)?'/landings_new/files/informacija_o_poshline.zip':'.'?>" target="_blank" class="<?=(!$landing->authOk)?'open-pay':''?>">Информация об оплате пошлины&nbsp;↓</a></li>
		<li><a href="<?=($landing->authOk)?'/landings_new/files/zayavlenie_o_priznanii_grazhdanina_bankrotom.zip':'.'?>" target="_blank" class="<?=(!$landing->authOk)?'open-pay':''?>">Заявление от гражданина на признание его банкротом&nbsp;↓</a></li>
		<li><a href="<?=($landing->authOk)?'/landings_new/files/hodataistvo_ob_otlozhenii_dela.zip':'.'?>" target="_blank" class="<?=(!$landing->authOk)?'open-pay':''?>">Ходатайство об отложении дела&nbsp;↓</a></li>
		<li><a href="<?=($landing->authOk)?'/landings_new/files/hodataistvo_o_rassmotrenii_dela_bez_uchastiya.zip':'.'?>" target="_blank" class="<?=(!$landing->authOk)?'open-pay':''?>">Ходатайство о рассмотрении дела без участия&nbsp;↓</a></li>
		<li><a href="<?=($landing->authOk)?'/landings_new/files/zhaloba_na_arbitrazhnogo_upravlyayushego.zip':'.'?>" target="_blank" class="<?=(!$landing->authOk)?'open-pay':''?>">Жалоба на арбитражного управляющего&nbsp;↓</a></li>
		<li><a href="<?=($landing->authOk)?'/landings_new/files/polozhenie_o_torgah.zip':'.'?>" target="_blank" class="<?=(!$landing->authOk)?'open-pay':''?>">Типовое положение о торгах&nbsp;↓</a></li>
		<li><a href="<?=($landing->authOk)?'/landings_new/files/zayavlenie_ob_osparivanii_reshenii.zip':'.'?>" target="_blank" class="<?=(!$landing->authOk)?'open-pay':''?>">Заявление об оспаривании решений, принятых на собрании кредиторов&nbsp;↓</a></li>
	</ul>
</div>