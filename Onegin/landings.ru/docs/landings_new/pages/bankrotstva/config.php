<?

$config = array(
	'managerEmail' => '',
//	'managerEmail' => '',
//	'managerEmail' => '',
	'title' => 'Банкротство и ликвидация предприятия: возврат или списание долгов, опытные арбитражные управляющие',
	'description' => 'Банкротство юридических лиц с долгами. Профессиональная юридическая помощь арбитражного управляющего на любой стадии процедуры.',
	'keywords' => 'Банкротство, конкурсное производство, арбитражный управляющий, ликвидация предприятия, списание долгов, субсидиарная ответственность.',
	'image' => '/images/page-icons/key.png',
	'favicon' => '/landings_new/images/favicon/favicon.ico',
	'landingName' => 'bankrotstva-ok.ru',
	'ringostatHidden' => (date('N') == 6 || date('N') == 7),
	'modules' => array(
		array('tpl' => 'bankrotstva/header.php', 'menuName' => 'Главная', 'class' => 'header', 'id' => 'header'),
		array('tpl' => 'bankrotstva/feedback.php', 'menuName' => false, 'class' => 'feedback', 'id' => 'feedback'),
		array('tpl' => 'bankrotstva/what-you-get.php', 'menuName' => 'Что Вы получите', 'class' => 'what-you-get', 'id' => 'advantages'),
		array('tpl' => 'bankrotstva/desirable-client.php', 'menuName' => 'Желанный клиент', 'class' => 'desirable-client', 'id' => 'desirable-client'),
		array('tpl' => 'bankrotstva/discounts.php', 'menuName' => 'Скидки', 'class' => 'discounts', 'id' => 'discounts'),
		array('tpl' => 'bankrotstva/why-us.php', 'menuName' => 'Почему мы', 'class' => 'why-us spider-graph', 'id' => 'why-us'),
		array('tpl' => 'bankrotstva/reviews.php', 'menuName' => 'Репутация', 'class' => 'gallery reviews', 'id' => 'reviews'),
		array('tpl' => 'bankrotstva/problem-solution.php', 'menuName' => 'Решения', 'class' => 'problem-solution', 'id' => 'problem-solution'),
		array('tpl' => 'bankrotstva/docs.php', 'menuName' => 'Свидетельства', 'class' => 'gallery docs', 'id' => 'docs'),
		array('tpl' => 'bankrotstva/workflow.php', 'menuName' => 'Как мы работаем', 'class' => 'workflow', 'id' => 'workflow'),
		array('tpl' => 'bankrotstva/author_recommendations.php', 'menuName' => 'Рекомендации', 'class' => 'author_recommendations', 'id' => 'author_recommendations'),
		array('tpl' => 'bankrotstva/feedback_2.php', 'menuName' => 'Сомневаетесь?', 'class' => 'feedback_2', 'id' => 'feedback_2'),
		array('tpl' => 'bankrotstva/contacts.php', 'menuName' => 'Контакты', 'class' => 'contacts', 'id' => 'contacts'),
		array('tpl' => 'footer.php', 'menuName' => false, 'class' => 'footer', 'id' => 'footer')
	)
);
