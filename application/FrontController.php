<?php
class FrontController
{
    protected $_controller;
    protected $_action;
    protected $_params = array();
    protected static $_instance;
    public static function getInstance() 
    {
        if(!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }    
        return self::$_instance;
    }
    private function __construct()
    {
        $request = $_SERVER['REQUEST_URI'];
        $request = str_replace(PRJ_URL, '', $request);
        $splits = explode('/', trim($request,'/'));
        $this->_controller = !empty($splits[0]) ? 
                ucfirst($splits[0]).'Controller' : 'IndexController';
        $this->_action = !empty($splits[1]) ? 
                strtolower($splits[1]).'Action': 'indexAction';
        if(!empty($splits[2]) && !stristr($splits[2],'?')) {
            $keys = $values = array();
            for($i=2, $cnt=count($splits) ; $i<$cnt ; $i++) {
                if( $i%2 == 0 ) {
                    $keys[] = $splits[$i];
                } else { 
                    $values[] = $splits[$i];
                }
            }
            if(count($keys) != count($values)) {
                header("Location: ".PRJ_URL."/index/error404");
                throw new Exception ('Wromng params');
                exit();    
            }
            $this->_params = array_combine($keys, $values);
              
        }
      
        if ($_GET) {
            $inputsGetArr = filter_input_array(INPUT_GET, 
                    FILTER_SANITIZE_SPECIAL_CHARS);
            $this->_params += $inputsGetArr; 
        }
        if ($_POST) {
             $inputsPostArr = filter_input_array(INPUT_POST, 
                    FILTER_SANITIZE_SPECIAL_CHARS);
            $this->_params += $inputsPostArr; 
        }
        if ($_FILES) {
            $this->_params += $_FILES; 
        }
        if ($_COOKIE) {
            $this->_params += $_COOKIE; 
        }
     }
     public function route()
     {
        if(class_exists($this->getController())) {
            $rc = new ReflectionClass($this->getController());
            if($rc->implementsInterface('IController')) {
                if($rc->hasMethod($this->getAction())) {
                    $controller = $rc->newInstance();
                    $method = $rc->getMethod($this->getAction());
                    $method->invoke($controller);
                } else {
                    header("Location: ".PRJ_URL."/index/error404");
                    throw new Exception ('Wromng Action '.$this->getAction());
                    exit();
                }
            } else {
                header("Location: ".PRJ_URL."/index/error404");
                throw new Exception ('Wromng Interface not a IController');
                exit();
            }
        } else {
            header("Location: ".PRJ_URL."/index/error404");
            throw new Exception ('Wromng Controller '.$this->getController());
            exit();
        }
    }
    function getParam($key, $default = NULL)
    {
        $val = (isset($this->_params[$key])) ? 
            $this->_params[$key] : $default ;
        return $val; 
     
    }
    function getParams()
    {
        return $this->_params;
    }
    function getController()
    {
        return $this->_controller;
    }
    function getAction()
    {
        return $this->_action;
    }
}
