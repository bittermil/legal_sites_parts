<div class="els">
	<div class="el">
		<div class="description">Мы разработали дистанционную ОНЛАЙН поддержку ДОЛЖНИКА и КРЕДИТОРА по БАНКРОТСТВУ ФИЗИЧЕСКОГО ЛИЦА, чтобы вы не переплачивали. Ниже представлены
			<b>7 простых шагов</b>, которые помогут разобраться в сложных процессах. Чтобы получить полный доступ к их содержанию, оформите
			<a <?=(!$landing->authOk)?'href="javascript:void(0);" data-price="5000" data-name="Подписка на www.ok-bankrotstvo.ru" class="btn-buy"':''?>><b>НЕОГРАНИЧЕННУЮ ПОДПИСКУ</b></a> за 5 тысяч рублей. После оплаты и проверки поступления денег, на указанный вами e-mail будет отправлен код для авторизации.
		</div>
		<div class="theses el">
			<div class="thesis">
				<div class="text">
					<b>Вы получите:</b>
				</div>
			</div>
			<div class="thesis">
				<div class="icon"></div>
				<div class="text">
					Четкое понимание, что такое банкротство физического лица
				</div>
			</div>
			<div class="thesis">
				<div class="icon"></div>
				<div class="text">
					Поддержку действующих арбитражных управляющих
				</div>
			</div>
			<div class="thesis">
				<div class="icon"></div>
				<div class="text">
					Достаточные знания, чтобы контролировать ход процедуры
				</div>
			</div>
			<div class="thesis">
				<div class="icon"></div>
				<div class="text">
					Возможность свести к минимуму любые дополнительные траты
				</div>
			</div>
		</div>
	</div>
	<div class="el">
		<?include(dirname(__FILE__).'/feedback_form.php');?>
		<div class="note">* Техническая поддержка<br> <a href="mailto:info@onegin-consulting.ru">info@onegin-consulting.ru</a></div>

		<div class="feedback_button">
			<button class="button">Заказать звонок</button>
		</div>
	</div>
</div>

<div class="form callback" id="feedback_with_city">
	<div class="title hide_for_mobile">Заказать обратный звонок</div>
	<input  name="name" type="text"  placeholder="Имя">
	<input  name="city" type="text"  placeholder="Город">
	<input  name="phone"  type="text"  placeholder="Моб. тел., пример: +79304225846">

	<button class="button">Оставить заявку</button>
</div>