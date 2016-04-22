<?php
class IndexController implements IController 
{
    private $_fc, $_tpl, $_em, $_ap;
    public function __construct() {
        $this->_fc = FrontController::getInstance();
        $this->_tpl = new Template();
        $this->_em = new EmployeesModel();
        $this->_ap = new AppointmentsModel();
        
    }
    public function indexAction() 
    {
        if (!isset($_SESSION['user']) or $_SESSION['user'] != APP_LOGIN) {
            header("Location: ".PRJ_URL."/index/auth");
            exit();
        }
        $boardroom = $this->_fc->getParam('boardroom');
        $boardroom = !empty($boardroom) ? $boardroom : '1';
        $month = $this->_fc->getParam('month');
        $year = $this->_fc->getParam('year');
        $calendar = Calendar::getCalendar($month, $year);
        $books = $this->_ap->getBooks($boardroom,$month, $year);
        $this->_tpl->assign(array("calendar"=>$calendar, "boardroom"=>$boardroom, 'books'=>$books));
        $this->_tpl->display(array('header','index','footer'));
    }
    public function bookFormAction() 
    {
        if (!isset($_SESSION['user']) or $_SESSION['user'] != APP_LOGIN) {
            header("Location: ".PRJ_URL."/index/auth");
            exit();
        }
        $boardroom = $this->_fc->getParam('boardroom');
        $employees = $this->_em->getAllEmployees();
        $this->_tpl->assign(array("employees"=>$employees, "boardroom"=>$boardroom));
        $this->_tpl->display(array('header','bookForm','footer'));
    }
    public function bookInsertAction() 
    {
        if (!isset($_SESSION['user']) or $_SESSION['user'] != APP_LOGIN) {
            header("Location: ".PRJ_URL."/index/auth");
            exit();
        }
        $formData = $this->_fc->getParam('formData');
        $boardroom = $formData['boardroom'];
        $employees = $this->_em->getAllEmployees();
        $book = $this->_ap->convertToBook($formData);
        $error = $this->_ap->checkBook($book);
        
        if ($error->check()) {
            $error = $this->_ap->checkBookInDb($book);
        }
        if ($error->check()) {
            $this->_ap->addBook($book);
            $success = array();
            $success['start_time'] = $book['timestampsArr'][0]['start_time'];
            $success['end_time'] = $book['timestampsArr'][0]['end_time'];
            $success['note'] = trim($book['note']);        
            $this->_tpl->assign(array("success"=>$success,'formData'=>$formData, 'boardroom'=>$boardroom, "employees"=>$employees)); 
        } else {
            $this->_tpl->assign(array("error"=>$error,'formData'=>$formData, "boardroom"=>$boardroom,"employees"=>$employees));
        }
        $this->_tpl->display(array('header','bookForm','footer')); 
        
    }
    public function bookStatisticAction()
    {
        if (!isset($_SESSION['user']) or $_SESSION['user'] != APP_LOGIN) {
            header("Location: ".PRJ_URL."/index/auth");
            exit();
        }
        $employees = $this->_em->getAllEmployees();
        $id = $this->_fc->getParam('id');
        $book = $this->_ap->getBook($id);
        $this->_tpl->assign(array("book"=>$book,'employees'=>$employees));
        $this->_tpl->display(array('header','bookStatistic','footer')); 
    }
    public function bookUpdateAction() 
    {
        if (!isset($_SESSION['user']) or $_SESSION['user'] != APP_LOGIN) {
            header("Location: ".PRJ_URL."/index/auth");
            exit();
        }
        $formData = $this->_fc->getParam('formData');
        $employees = $this->_em->getAllEmployees();
        
        $UpdateBook = $this->_ap->convertToUpdateBook($formData);
        $error = $this->_ap->checkBook($UpdateBook);
        if ($error->check()) {
           $error = $this->_ap->checkBookInDb($UpdateBook);
        }
        if ($error->check()) {
            $this->_ap->updateBook($UpdateBook);
            $book = $this->_ap->getBook($formData['id']);
            $success = array();
            $success['start_time'] = $book->start_time;
            $success['end_time'] = $book->end_time;
            $this->_tpl->assign(array("book"=>$book,'employees'=>$employees,'success'=>$success));
        } else {
            $book = $this->_ap->getBook($formData['id']);
            $this->_tpl->assign(array("book"=>$book,'employees'=>$employees,'error'=>$error));
        }
        $this->_tpl->display(array('header','bookStatistic','footer')); 
    }
    public function DeleteBookAction()
    {
        if (!isset($_SESSION['user']) or $_SESSION['user'] != APP_LOGIN) {
            header("Location: ".PRJ_URL."/index/auth");
            exit();
        }
        $id = $this->_fc->getParam('id');
        $recurring_id = $this->_fc->getParam('recurring_id');
        $IsRec = $this->_fc->getParam('IsRec');
        $book = $this->_ap->getBook($id);
        $this->_ap->deleteBook($id, $recurring_id, $IsRec);
        $this->_tpl->assign(array('book'=>$book));
        $this->_tpl->display(array('header','deleteMsg','footer')); 
    }
    public function authAction() 
    {
        if (isset($_SESSION['user']) &&  $_SESSION['user'] == APP_LOGIN) {
            header("Location: ".PRJ_URL."/index/index");
            exit();
        } else {
            $user = $this->_fc->getParam('user');
            $password = $this->_fc->getParam('password');
            if($user == APP_LOGIN && $password == APP_PASSWORD){
                $_SESSION['user'] = $user;
                header("Location: ".PRJ_URL."/index/auth");
                exit();
            } else {
                if (isset($user) or isset($password)) {
                    $error = 'Incorrect login or password';
                    $this->_tpl->assign(array("error"=>$error));    
                }
                $this->_tpl->display(array('header','auth','footer'));    
            }
            
        }
    }
    public function error404Action()
    {
       header('HTTP/1.1 404 Not Found');
       $this->_tpl->display(array('header','error404','footer'));
    }
    
    
}
