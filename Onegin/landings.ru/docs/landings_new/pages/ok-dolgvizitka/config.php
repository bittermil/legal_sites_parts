<?

$config = array(
	'managerEmail' => '',
//	'managerEmail' => '',
//	'managerEmail' => '',
	'title' => 'Взыскание долгов с юр. и физ. лиц',
	'description' => 'Взыскание долгов с юр. и физ. лиц',
	'keywords' => '',
	'image' => '/images/page-icons/key.png',
	'favicon' => '/landings_new/images/favicon/favicon.ico',
	'landingName' => 'ok-dolgvizitka.ru',
	'ringostatHidden' => true,
	'modules' => array(
		array('tpl' => 'ok-dolgvizitka/header.php', 'menuName' => false, 'class' => 'header', 'id' => 'header'),
		array('tpl' => 'ok-dolgvizitka/feedback_2.php', 'menuName' => false, 'class' => 'feedback_2', 'id' => 'feedback_with_message'),
		array('tpl' => 'ok-dolgvizitka/contacts.policy.php', 'menuName' => false, 'class' => 'policy', 'id' => 'policy'),
		array('tpl' => 'ok-dolgvizitka/footer.php', 'menuName' => false, 'class' => 'footer', 'id' => 'footer')
	)
);
