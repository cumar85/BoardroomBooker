<?php


//database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'boardroombooker');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_CHARSET', 'utf8');



//application configuration
define('APP_LOGIN', 'login');
define('APP_PASSWORD', 'password');
define('BOARDROOMS_COUNT', '3');
define('TIME_FORMAT', '12'); //12 or 24
define('FIRST_DAY', 'Sunday'); //Sunday or Monday





define('PRJ_PATH', str_replace('\\', '/', realpath(dirname(__FILE__))));
define('CHARSET', 'utf-8');


define('PRJ_NAME', 'BoardroomBooker');

if(substr_count($_SERVER['REQUEST_URI'], PRJ_NAME)) {
    define('PRJ_URL', '/'.PRJ_NAME);    
} else {
    define('PRJ_URL', '');    
}


define('CSS_URL', PRJ_URL.'/css');
define('JS_URL', PRJ_URL.'/js');
define('TPL_URL', PRJ_URL.'/application/views');









