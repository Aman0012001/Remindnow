<?php
$WEBSITE_URL				=	env("APP_URL");
$NODE_WEB_URL 				= env('NODE_APP_URL');
return [
	'ALLOWED_TAGS_XSS'   	=> '<iframe><a><strong><b><p><br><i><font><img><h1><h2><h3><h4><h5><h6><span><div><em><table><ul><li><section><thead><tbody><tr><td><figure><article>',
	'DS'     				=> '/',
	'ROOT'     				=> base_path(),
	'APP_PATH'     			=> app_path(),

	'NODE_WEBSITE_URL'                      => $NODE_WEB_URL,

	"IMAGE_PATH"							=>	$WEBSITE_URL.'img/',
	"IMAGE_ROOT_PATH"						=>	"img/",

	"LANGUAGE_IMAGE_PATH"					=>	$WEBSITE_URL.'/public/uploads/language_image/',
	"LANGUAGE_IMAGE_ROOT_PATH"				=>	"public/uploads/language_image/",

	'STATE' => [
    'STATE_TITLE' => "State",
    'STATE_TITLES' => "States",
],

	"USER_IMAGE_PATH"						=>	$WEBSITE_URL.'uploads/User-image/',
	"USER_IMAGE_ROOT_PATH"					=>	"uploads/User-image/",
	
	"FESTIVAL_IMAGE_PATH"						=>	$WEBSITE_URL.'uploads/Festival-image/',
	"FESTIVAL_IMAGE_ROOT_PATH"					=>	"uploads/Festival-image/",

	
	
	"TEMPLE_IMAGE_PATH"						=>	$WEBSITE_URL.'uploads/Temple-image/',
	"TEMPLE_IMAGE_ROOT_PATH"					=>	"uploads/Temple-image/",

	"SEO_PAGE_IMAGE_IMAGE_PATH"		 		=>	$WEBSITE_URL.'uploads/sep-image/',
	"SEO_PAGE_IMAGE_ROOT_PATH"				=>	"uploads/sep-image/",

	"CK_EDITOR_URL"		 				        =>	$WEBSITE_URL . 'uploads/ck_editor_images/',
	"CK_EDITOR_ROOT_PATH"				        =>	"uploads/ck_editor_images/",


	'MESSAGE' => [
		'INACTIVE_MEMBER_STAFF' => "You can't login in site panel, please contact to site admin!",
	],

	'GENDER' => [
		'1' 	=> "Men",
		'2' 	=> "Women",
		'0' 	=> "Other",
	],

	'CUSTOMER' => [
		'CUSTOMERS_TITLE' 	=> "Customer",
		'CUSTOMERS_TITLES' 	=> "Customers",
	],
	
	'TEMPLE' => [
		'TEMPLE_TITLE' 	=> "Temple",
		'TEMPLE_TITLES' 	=> "Temple",
	],

	'TIPTAP' => [
    'TIPTAP_TITLE' => "Tiptap",
    'TIPTAP_TITLES' => "Tiptaps",
],

'TIPTAP_IMAGE_PATH' => $WEBSITE_URL . 'uploads/Tiptap-image/',
'TIPTAP_IMAGE_ROOT_PATH' => "uploads/Tiptap-image/",

'NOTIFICATION_IMAGE_PATH' => $WEBSITE_URL . 'uploads/notifications/',
'NOTIFICATION_IMAGE_ROOT_PATH' => "uploads/notifications/",
	
	'FESTIVAL' => [
		'FESTIVAL_TITLE' 	=> "Festival",
		'FESTIVAL_TITLES' 	=> "Festivals",
	],

	'SEO' => [
		'SEO_TITLE' 	=> "Seo pages",
	],

	'CMS_MANAGER' => [
		'CMS_PAGES_TITLE' 	=> "Cms Pages",
		'CMS_PAGE_TITLE' 	=> "Cms Page",
		'VIEW_PAGE' 		=> "View Page",
	],

	'FAQ' => [
		'FAQ_TITLE'	 => "Faq",
		'FAQS_TITLE' => "Faq's",
		'VIEW_PAGE'  => "Faq View",
	],

	'EMAIL_TEMPLATES' => [
		'EMAIL_TEMPLATES_TITLE' => "Email Templates",
		'EMAIL_TEMPLATE_TITLE' 	=> "Email Template",
	],

	'EMAIL_LOGS' => [
		'EMAIL_LOGS_TITLE' 		=> "Email Logs",
		'EMAIL_DETAIL_TITLE' 	=> "Email Detail",
	],

	'LANGUAGE_SETTING' => [
		'LANGUAGE_SETTINGS_TITLE' 	=> "Language Setting",
		'LANGUAGE_SETTING_TITLE' 	=> "Language Setting",
	],

	'ACL' => [
		'ACLS_TITLE' => "Acl",
		'ACL_TITLE' => "Acl Management",
	],

	'SETTING' => [
		'SETTINGS_TITLE' 	=> "Settings",
		'SETTING_TITLE' 	=> "Setting",
	],

	'DESIGNATION' => [
		'DESIGNATIONS_TITLE' 	=> "Roles",
		'DESIGNATION_TITLE' 	=> "Role",
	],

	'STAFF' => [
		'STAFFS_TITLE' 		=> "Staff's",
		'STAFF_TITLE' 		=> "Staff",
	],

	'ROLE_ID' => [
		'ADMIN_ID' 					=> 1,
		'SUPER_ADMIN_ROLE_ID' 		=> 1,
		'CUSTOMER_ROLE_ID' 			=> 2,
		'STAFF_ROLE_ID' 			=> 3,
	],

	'DEFAULT_LANGUAGE' => [
		'FOLDER_CODE' 	=> 'eng',
		'LANGUAGE_CODE' => 1,
		'LANGUAGE_NAME' => 'English'
	],

	'SETTING_FILE_PATH'	=> base_path() . "/" .'config'."/". 'settings.php',

	'WEBSITE_ADMIN_URL' => base_path() . "/" .'adminpnlx',

];
