<?php

class AutoloaderClasses {
        private static $_instance;
        public static function getInstance()
        {
            if (self::$_instance == NULL) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
        private function __construct() 
        {
            spl_autoload_register(array($this, 'ClassLoader'));
            spl_autoload_register(array($this, 'ClassCLoader'));
        }
        public function ClassLoader($className)
        {
            spl_autoload_extensions('.php');
            spl_autoload($className);
        }
        public function ClassCLoader($className)
        {
            spl_autoload_extensions('.class.php');
            spl_autoload($className);
        }
}
