<?

$config = array(
	'managerEmail' => '',
//	'managerEmail' => '',
//	'managerEmail' => '',
	'title' => L('ABOUTCOMPANY_Website_title'),
	'description' => L('ABOUTCOMPANY_Website_title'),
	'keywords' => L('ABOUTCOMPANY_Website_title'),
	'image' => '/images/page-icons/key.png',
	'favicon' => '/landings_new/images/favicon/favicon.ico',
	'landingName' => 'about_company',
	'ringostatHidden' => true, 
	'modules' => [
		['tpl' => 'about_company/'.core()->lang.'/header.php', 'menuName' => false, 'class' => 'header', 'id' => 'header'],
		['tpl' => 'about_company/'.core()->lang.'/feedback.php', 'menuName' => false, 'class' => 'feedback', 'id' => 'feedback'],
		['tpl' => 'about_company/'.core()->lang.'/structure.php', 'menuName' => L('ABOUTCOMPANY_Menu_structure'), 'class' => 'structure spider-graph', 'id' => 'structure'],
		['tpl' => 'about_company/'.core()->lang.'/what_you_get.php', 'menuName' => L('ABOUTCOMPANY_What_you_get'), 'class' => 'what_you_get', 'id' => 'what_you_get'],
		['tpl' => 'about_company/'.core()->lang.'/guarantees.php', 'menuName' => L('ABOUTCOMPANY_Guarantees'), 'class' => 'what_you_get our_guarantees', 'id' => 'our_guarantees'],
		['tpl' => 'about_company/'.core()->lang.'/about_us.php', 'menuName' => L('ABOUTCOMPANY_About_us'), 'class' => 'gallery reviews', 'id' => 'reviews'],
		['tpl' => 'about_company/'.core()->lang.'/workflow.php', 'menuName' => L('ABOUTCOMPANY_Workflow'), 'class' => 'workflow', 'id' => 'workflow'],
		['tpl' => 'about_company/'.core()->lang.'/feedback_2.php', 'menuName' => L('ABOUTCOMPANY_Stll_hesitating'), 'class' => 'feedback_2 hesitating', 'id' => 'feedback_2'],
		['tpl' => 'about_company/'.core()->lang.'/contacts.php', 'menuName' => L('ABOUTCOMPANY_Contacts'), 'class' => 'contacts', 'id' => 'contacts'],
		['tpl' => 'about_company/'.core()->lang.'/footer.php', 'menuName' => false, 'class' => 'footer', 'id' => 'footer']
	]
);