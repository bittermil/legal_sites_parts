<?

$config = array(
	'managerEmail' => '',
//	'managerEmail' => '',
//	'managerEmail' => '',
	'title' => 'Банкротство физического лица (гражданина). Все, что нужно знать должнику и кредитору.',
	'description' => 'Банкротство физического лица (гражданина). Все, что нужно знать должнику и кредитору.',
	'keywords' => '',
	'image' => '/images/page-icons/key.png',
	'landingName' => 'галичевский.рф',
	'ringostatHidden' => (date('N') == 6 || date('N') == 7),
	'modules' => array(
		array('tpl' => 'galichevsky/header.php', 'menuName' => false, 'class' => 'header', 'id' => 'header'),
		array('tpl' => 'galichevsky/about_book.php', 'menuName' => 'О книге', 'class' => 'about_book', 'id' => 'about_book'),
		array('tpl' => 'galichevsky/about_author.php', 'menuName' => 'Об авторе', 'class' => 'about_author', 'id' => 'about_author'),
		array('tpl' => 'galichevsky/why_buy.php', 'menuName' => 'Почему стоит купить', 'class' => 'why_buy', 'id' => 'why_buy'),
		array('tpl' => 'galichevsky/prices.php', 'menuName' => 'Оплата', 'class' => 'prices', 'id' => 'prices'),
		array('tpl' => 'galichevsky/confirmation_delivery.php', 'menuName' => 'Доставка', 'class' => 'confirmation_delivery', 'id' => 'confirmation_delivery'),
		array('tpl' => 'galichevsky/guarantees_decency.php', 'menuName' => 'Гарантии', 'class' => 'guarantees_decency', 'id' => 'guarantees_decency'),
		array('tpl' => 'galichevsky/reviews.php', 'menuName' => false, 'class' => 'gallery reviews', 'id' => 'reviews'),
		array('tpl' => 'galichevsky/photos.php', 'menuName' => false, 'class' => 'photos', 'id' => 'photos'),
		array('tpl' => 'galichevsky/from_words_to_deeds.php', 'menuName' => 'От слов к действию', 'class' => 'from_words_to_deeds', 'id' => 'from_words_to_deeds'),
		array('tpl' => 'galichevsky/what_else_we_offer.php', 'menuName' => 'Что еще?', 'class' => 'what_else_we_offer', 'id' => 'what_else_we_offer'),
		array('tpl' => 'galichevsky/social_media.php', 'menuName' => 'Сообщества', 'class' => 'social_media', 'id' => 'social_media'),
		array('tpl' => 'galichevsky/cooperation.php', 'menuName' => 'Сотрудничество', 'class' => 'cooperation', 'id' => 'cooperation'),
		array('tpl' => 'galichevsky/contacts.policy.php', 'menuName' => false, 'class' => 'policy', 'id' => 'policy'),
		array('tpl' => 'galichevsky/footer.php', 'menuName' => false, 'class' => 'footer', 'id' => 'footer')
	)
);
