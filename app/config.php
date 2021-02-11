<?php 
    
define('SCRIPTURL','http://imrashop.website/');
define('SCRIPTDIR', BASEPATH.'/');

return  [
    
    'app' => [
        'version'            => '2.9.123',
        'debug'              => true,
    ],
      
    'db_live' => [
        'driver'             => 'mysql',
        'host'               => 'localhost',
        'name'               => 'imrashop',
        'username'           => 'root',
        'password'           => 'sm@rtboy7A',
        'charset'            => 'utf8',
        'collation'          => 'utf8_general_ci',
        'strict'             => 'false',
        'prefix'             => 'na_'
    ],

    'views'              => '',
  
    'url' => [
        'base'               => SCRIPTURL,
        'ads'                => SCRIPTURL.'uploads/undetected/',
        'admin_assets'       => SCRIPTURL.'admin_assets/',
        'website_assets'     => SCRIPTURL.'assets/',
        'assets'             => '/assets/',
        'avatars'            => SCRIPTURL.'uploads/avatar/',
        'media'              => SCRIPTURL.'uploads/media/',    
        'uploads'            => SCRIPTURL.'uploads/',    
    ],
    
    'dir' => [
        'base'               => SCRIPTDIR,
        'media'              => SCRIPTDIR.'public/uploads/media/',
        'filat'              => SCRIPTDIR.'public/uploads/filat/',
        'csv'              => SCRIPTDIR.'public/uploads/csv/',
    ] 

    
    
];