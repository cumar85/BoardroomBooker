<?php

require_once('config.php');
require_once(PRJ_PATH.'/library/AutoloaderClasses.php');
require_once (PRJ_PATH.'/application/FrontController.php');


header('Content-Type: text/html; charset='.CHARSET);

set_include_path(get_include_path()
                .PATH_SEPARATOR.PRJ_PATH.'/application/controllers/'
                .PATH_SEPARATOR.PRJ_PATH.'/application/models/'
                .PATH_SEPARATOR.PRJ_PATH.'/application/views/'
                .PATH_SEPARATOR.PRJ_PATH.'/application/views/index/'
                .PATH_SEPARATOR.PRJ_PATH.'/library');

session_start();




AutoloaderClasses::getInstance();
FrontController::getInstance()->route();