<h2 class="<?=(!$landing->authOk)?'':'open'?>">Первичная личная или ОНЛАЙН-консультация действующего арбитражного управляющего</h2>
<div class="els">
	<div class="el">
		<p>
			Авторизовавшись на сайте, вы получаете право на РАЗОВУЮ консультацию до 1 часа, которая проходит
			в офисе компании "Онегин-Консалтинг" в Санкт-Петербурге/Москве или посредством видеосвязи через Skype/WhatsApp. Общение осуществляется лично с
			Галичевским Игорем или иным действующим арбитражным управляющим.
			Предварительно РЕКОМЕНДУЕМ максимально подробно заполнить анкету и выслать ее нам <?=($landing->authOk)?'на адрес <a href="mailto:info@onegin-consulting.ru">info@onegin-consulting.ru</a>':''?>
			для комплексной оценки вашей ситуации.<br><br>

			•  Если вы должник, то анкета <a href="<?=($landing->authOk)?'/landings_new/files/anketa_ot_dolzhnika.docx':'.'?>" class="<?=(!$landing->authOk)?'open-pay':''?>">здесь</a><br><br>

			•  Если вы кредитор, то анкета <a href="<?=($landing->authOk)?'/landings_new/files/anketa_ot_kreditora.docx':'.'?>" class="<?=(!$landing->authOk)?'open-pay':''?>">здесь</a><br>
			<?=(!$landing->authOk)?'':'<br><br><b>Запись по телефону:</b><a href="tel: 88003507981"> 8 800 350 79 81</a>'?>
		</p>

		<div class="el photo hide_for_desktop"></div>
	</div>
</div>