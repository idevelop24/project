<?php

// site
$_['site_url'] = '';


// errors
$_['errors_display'] = true;
$_['errors_log'] = true;


// images
$_['images_allowed_extentions'] = ['jpg','jpeg','gif','png','webp'];


// admin
$_['admin_grid_rows_limit'] = 8;
$_['admin_image_allowed_extentions'] = ['jpg','jpeg','gif','png','webp'];
$_['admin_image_size_vs'] = ['45','45'];
$_['admin_image_size_s'] = ['50','50'];
$_['admin_image_size_sg'] = ['250','250'];
$_['admin_image_size_g'] = ['300','300'];
$_['admin_image_size_item'] = ['450','450'];

// image
$_['image_upload_size'] = 216;
$_['image_allowed_types'] = ['jpg','jpeg','png','gif'];
$_['image_not_allowed_types'] = ['gif'];


// mail
$_['mail_engine'] = 'smtp';
$_['mail_from'] = 'Yourshop@domain.com';
$_['mail_sender'] = 'Yourshop name';
$_['mail_reply_to'] = 'Yourshop@domain.com';
$_['mail_smtp_hostname'] = '';
$_['mail_smtp_username'] = '';
$_['mail_smtp_password'] = getenv('SMTP_PASSWORD');
$_['mail_smtp_port'] = 25;
$_['mail_smtp_timeout'] = 5;
$_['mail_verp'] = false;
$_['mail_parameter'] = '';
$_['mail_attach'] = '';


// cache
$_['cache_engine'] = 'file';
$_['cache_expire'] = 3600;


$_['session_expire'] = 86400; // 24 hours
$_['session_path'] = '/';
$_['session_domain'] = ''; // Your domain
$_['session_samesite'] = 'Strict'; // Or 'Strict'


