<?php
class EmployeesController implements IController 
{
    private $_fc, $_tpl, $_em;
    public function __construct() {
        $this->_fc = FrontController::getInstance();
        $this->_tpl = new Template();
        $this->_em = new EmployeesModel();
    }
    public function indexAction() 
    {
        if (!isset($_SESSION['user']) or $_SESSION['user'] != APP_LOGIN) {
            header("Location: ".PRJ_URL."/index/auth");
            exit();
        }
        $employees = $this->_em->getAllEmployees();
        $this->_tpl->assign(array("employees"=>$employees));
        $this->_tpl->display(array('header','employees','footer'));
    }
    public function addFormAction() 
    {
        if (!isset($_SESSION['user']) or $_SESSION['user'] != APP_LOGIN) {
            header("Location: ".PRJ_URL."/index/auth");
            exit();
        }
        $this->_tpl->display(array('header','addform','footer'));
    }
    public function addAction() 
    {
        if (!isset($_SESSION['user']) or $_SESSION['user'] != APP_LOGIN) {
            header("Location: ".PRJ_URL."/index/auth");
            exit();
        }
        $formData = $this->_fc->getParam('formData');
        $error = $this->_em->checkEmployee($formData);
        if($error->check()){
            $error = $this->_em->checkEmployeeInDb($formData);
        }
        if ($error->check()) {
            $this->_em->addEmployee($formData); 
            $success = 'Employee successfully added';
            $this->_tpl->assign(array("success"=>$success));
        } else {
           $this->_tpl->assign(array("error"=>$error,'formData'=>$formData)); 
        }
        $this->_tpl->display(array('header','addform','footer'));
    }
    public function editFormAction()
    {
        if (!isset($_SESSION['user']) or $_SESSION['user'] != APP_LOGIN) {
            header("Location: ".PRJ_URL."/index/auth");
            exit();
        }
        $id = $this->_fc->getParam('id');
        $employee = $this->_em->getEmployee($id);
        $this->_tpl->assign(array("employee"=>$employee));
        $this->_tpl->display(array('header','editform','footer'));
    }
    public function editAction() 
    {
        if (!isset($_SESSION['user']) or $_SESSION['user'] != APP_LOGIN) {
            header("Location: ".PRJ_URL."/index/auth");
            exit();
        }
        $id = $this->_fc->getParam('id');
        $employee = $this->_em->getEmployee($id);
        $formData = $this->_fc->getParam('formData');
        $error = $this->_em->checkEmployee($formData);
        if($error->check()){
            $error = $this->_em->checkEmployeeInDb($formData,$id);
        }
        if ($error->check()) {
            $this->_em->editEmployee($id, $formData);
            header("Location:".PRJ_URL."/employees");
        } else {
           $this->_tpl->assign(array("error"=>$error,'employee'=>$employee)); 
        }
        $this->_tpl->display(array('header','editform','footer'));
    }
    public function deleteAction() 
    {
        if (!isset($_SESSION['user']) or $_SESSION['user'] != APP_LOGIN) {
            header("Location: ".PRJ_URL."/index/auth");
            exit();
        }
        $id = $this->_fc->getParam('id');
        $this->_em->deleteEmployee($id);
        header("Location:".PRJ_URL."/employees");
    }
}
