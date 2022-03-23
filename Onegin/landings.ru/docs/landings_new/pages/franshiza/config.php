<?

$config = array(
	'managerEmail' => 'info@onegin-consulting.ru',
//	'managerEmail' => 'artemeey.ru@mail.ru',
//	'managerEmail' => 'alvassilieva@gmail.com',
	'title' => 'Франшиза «Онегин-Консалтинг»',
	'description' => 'Франшиза «Онегин-Консалтинг»',
	'keywords' => 'Франшиза «Онегин-Консалтинг»',
	'image' => '/images/page-icons/key.png',
	'favicon' => '/landings_new/images/favicon/favicon.ico',
	'landingName' => 'franshiza.ru',
	'ringostatHidden' => (date('N') == 6 || date('N') == 7),
	'modules' => array(
		array('tpl' => 'franshiza/header.php', 'menuName' => false, 'class' => 'header', 'id' => 'header'),
		array('tpl' => 'franshiza/feedback.php', 'menuName' => false, 'class' => 'feedback', 'id' => 'feedback'),
		array('tpl' => 'franshiza/target_audience.php', 'menuName' => 'Кому это может быть интересно', 'class' => 'target_audience', 'id' => 'target_audience'),
		array('tpl' => 'franshiza/reviews.php', 'menuName' => 'Наша репутация', 'class' => 'gallery reviews', 'id' => 'reviews'),
		array('tpl' => 'franshiza/what-you-get-note.php', 'menuName' => false, 'class' => 'what-you-get-note', 'id' => 'what-you-get-note'),
		array('tpl' => 'franshiza/what-you-get.php', 'menuName' => 'Что вы получите', 'class' => 'what-you-get', 'id' => 'what-you-get'),
		array('tpl' => 'franshiza/what-you-should-know.php', 'menuName' => 'Что нужно знать о франшизе', 'class' => 'what-you-should-know', 'id' => 'what-you-should-know'),
		array('tpl' => 'franshiza/feedback_2.php', 'menuName' => 'Стоимость', 'class' => 'feedback_2', 'id' => 'feedback_2'),
		array('tpl' => 'franshiza/workflow.php', 'menuName' => 'Как получить франшизу', 'class' => 'workflow', 'id' => 'workflow'),
		array('tpl' => 'franshiza/what_else_we_offer.php', 'menuName' => 'Что еще?', 'class' => 'what-else-we-offer', 'id' => 'what-else-we-offer'),
		array('tpl' => 'franshiza/contacts.php', 'menuName' => 'Контакты', 'class' => 'contacts', 'id' => 'contacts'),
		array('tpl' => 'footer.php', 'menuName' => false, 'class' => 'footer', 'id' => 'footer')
	)
);
