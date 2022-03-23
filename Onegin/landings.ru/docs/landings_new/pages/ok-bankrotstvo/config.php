<?

$config = array(
    'managerEmail' => 'igor.galichevsky@yandex.ru',
//	'managerEmail' => 'artemeey.ru@mail.ru',
//	'managerEmail' => 'alvassilieva@gmail.com',
    'title' => 'Экономьте на банкротстве физического лица правильно.',
    'description' => 'Экономьте на банкротстве физического лица правильно.',
    'keywords' => '',
    'image' => '/images/page-icons/key.png',
    'landingName' => 'ok-bankrotstvo',
    'ringostatHidden' => (date('N') == 6 || date('N') == 7),
    'modules' => array(
	   array('tpl' => 'ok-bankrotstvo/header.php', 'menuName' => false, 'class' => 'header', 'id' => 'header'),
	   array('tpl' => 'ok-bankrotstvo/feedback.php', 'menuName' => false, 'class' => 'feedback', 'id' => 'authorization'),
	   array('tpl' => 'ok-bankrotstvo/first_consultation.php', 'menuName' => false, 'class' => 'first_consultation', 'id' => 'first_consultation'),
	   array('tpl' => 'ok-bankrotstvo/guide.php', 'menuName' => false, 'class' => 'guide', 'id' => 'guide'),
	   array('tpl' => 'ok-bankrotstvo/bankrotstvo_process.php', 'menuName' => false, 'class' => 'bankrotstvo_process', 'id' => 'bankrotstvo_process'),
	   array('tpl' => 'ok-bankrotstvo/videos.php', 'menuName' => false, 'class' => 'videos', 'id' => 'videos'),
	   array('tpl' => 'ok-bankrotstvo/needed_documents.php', 'menuName' => false, 'class' => 'needed_documents', 'id' => 'needed_documents'),
	   array('tpl' => 'ok-bankrotstvo/offline_support.php', 'menuName' => false, 'class' => 'offline_support feedback_2', 'id' => 'feedback_with_message'),
	   array('tpl' => 'ok-bankrotstvo/recommendations.php', 'menuName' => false, 'class' => 'recommendations', 'id' => 'recommendations'),
	   array('tpl' => 'ok-bankrotstvo/related_links.php', 'menuName' => false, 'class' => 'related_links', 'id' => 'related_links'),
	   array('tpl' => 'ok-bankrotstvo/need_help.php', 'menuName' => false, 'class' => 'need_help feedback_2', 'id' => 'need_help'),
	   array('tpl' => 'ok-bankrotstvo/contacts.policy.php', 'menuName' => false, 'class' => 'policy', 'id' => 'policy'),
	   array('tpl' => 'ok-bankrotstvo/footer.php', 'menuName' => false, 'class' => 'footer', 'id' => 'footer')
    )
);
