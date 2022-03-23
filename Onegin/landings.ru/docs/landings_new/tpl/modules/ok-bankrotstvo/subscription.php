<div id="prices_select_method_of_payment">
	<table>
		<tr>
			<td>Назначение перевода</td>
			<td><span class="targets-text"></span></td>
		</tr>
		<tr>
			<td>Сумма</td>
			<td><span class="price-text"></span> руб.</td>
		</tr>
		<tr>
			<td>Способ оплаты</td>
			<td>
				<div class="selector-type-payment">
					<img class="active" data-payment-type="PC" src="/landings_new/images/galichevsky/prices/yandex-money.svg" alt="Способ оплаты"/><img data-payment-type="AC" src="/landings_new/images/galichevsky/prices/card.svg" alt="Способ оплаты"/><img data-payment-type="MC" src="/landings_new/images/galichevsky/prices/mobile.svg" alt="Способ оплаты"/>
				</div>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<form method="POST" target="_blank" action="https://money.yandex.ru/quickpay/confirm.xml">
					<input name="receiver" value="410016554737211" type="hidden">
					<input name="label" value="" type="hidden">
					<input name="quickpay-form" value="shop" type="hidden">
					<input name="is-inner-form" value="true" type="hidden">
					<input name="referer" value="" type="hidden">
					<input name="need-fio" value="true" type="hidden">
					<input name="need-email" value="true" type="hidden">
					<input name="need-phone" value="true" type="hidden">
					<input name="need-address" value="true" type="hidden">
					<input name="successURL" value="http://xn--80aebjochgf7d3c.xn--p1ai/#prices" type="hidden">
					<input name="targets" value="Текст будет менятся в js" type="hidden">
					<input name="sum" value="Текст будет менятся в js" type="hidden">
					<input name="paymentType" value="PC" type="hidden">
					<input type="submit" value="Оплатить">
				</form>
			</td>
		</tr>
	</table>
</div>