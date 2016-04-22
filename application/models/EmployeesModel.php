<?php
class EmployeesModel
{
    protected $_db, $_error;
    public function __construct() 
    {
         $this->_db = ConnectDb::getInstance()
         ->getConnect();
         $this->_error = new ErrorCounter();
    }
    public function getAllEmployees() 
    {
        $sql = "SELECT id, name, email FROM employees WHERE 1";
        try {
            $stmt = $this->_db->prepare($sql);
            $stmt->execute();
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        return  $stmt->fetchall();
    }
    public function getEmployee($id)
    {
        $sql = "SELECT id, name, email FROM employees WHERE id = :id";
        try {
            $stmt = $this->_db->prepare($sql);
            $stmt->execute(array(':id'=>$id));
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        return  $stmt->fetch(); 
    }
    public function addEmployee($formData) 
    {
         ConnectDb::insert('employees',$formData);
    }
    public function editEmployee($id, $formData)
    {
         ConnectDb::update('employees', $id, $formData);
    }
    public function checkEmployeeInDb($formData, $id='')
    {
        $name = $formData['name'];
        $email = $formData['email'];
        
        $sql = "SELECT count(*) FROM employees
                WHERE (name = :name OR email = :email)";
        if (!empty($id)) {
            $sql .= " AND id <> :id";     
        }
        try {
            $stmt = $this->_db->prepare($sql);
            if (!empty($id)) {
                $stmt->bindparam(':id',$id);    
            }
            $stmt->bindparam(':name',$name);
            $stmt->bindparam('email', $email); 
            $stmt->execute();
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        $count = $stmt->fetch(PDO::FETCH_COLUMN);
        if($count) {
            $this->_error->add('name or mail','Name or email is already in the database');    
        }
        return $this->_error;
    }
    public function deleteEmployee($id)
    {
        $sql = 'DELETE FROM employees
        WHERE id = :id';
        try {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindparam(':id',$id);
            $stmt->execute();
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    public function checkEmployee($formData)
    {
        $name = $formData['name'];
        $email = $formData['email'];
        
        if (empty($name))  {
            $this->_error->add('name','Name should not be empty');
        } elseif (!preg_match("/^[_a-zA-Z0-9]+$/",$name)) {
            $this->_error->add('name','Invalid characters in the name');
        } elseif (!preg_match("/^.{4,15}$/",$name)) {
            $this->_error->add('name','Incorrect length of the name');
        }
        
        if (empty($email))  {
            $this->_error->add('email','Email should not be empty');
        } elseif (!preg_match("|([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is",$email)) {
            $this->_error->add('email','Incorrect E-mail'); 
        }
        return $this->_error;
    }
    
   
    
}

