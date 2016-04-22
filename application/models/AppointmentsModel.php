<?php
class AppointmentsModel {
   protected $_db, $_error;
    public function __construct() 
    {
         $this->_db = ConnectDb::getInstance()
         ->getConnect();
         $this->_error = new ErrorCounter();
    }
    
    public function convertToBook($formData) {
        $sh = $formData['start_hours'];
        $sm = $formData['start_minutes'];
        $st = $formData['start_am_pm'];
        
        $eh = $formData['end_hours'];
        $em = $formData['end_minutes'];
        $et = $formData['end_am_pm'];
        
        $s_t = Calendar::AmPmToTstamp($sh, $sm, $st);
        $e_t = Calendar::AmPmToTstamp($eh, $em, $et);
        
        
        $m = $formData['month'];
        $d = $formData['day'];
        $y = $formData['year'];
        
        $start_time = mktime(date('H',$s_t), date('i',$s_t), date('s',$s_t), $m, $d, $y);
        $end_time = mktime(date('H',$e_t), date('i',$e_t), date('s',$e_t), $m, $d, $y);
      
        
        $timestampsArr = array();
        $timestampsArr[0]['start_time'] = $start_time;
        $timestampsArr[0]['end_time'] = $end_time;
        
        
        
       
        if(!empty($formData['recurring']) && !empty($formData['recurring_type']) && !empty($formData['recurring_num'])) {
            if($formData['recurring']) {
                $recurring_num = (int)$formData['recurring_num'];
                switch ($formData['recurring_type']) {
                    case 'weekly':
                        $recurring_num = $recurring_num > 4 ? 4 : $recurring_num;
                        for($i=1; $i < $recurring_num; $i++ ) {
                             $timestampsArr[$i]['start_time'] =  $start_time + 86400 * 7 * $i;
                             $timestampsArr[$i]['end_time'] =  $end_time + 86400 * 7 * $i;
                        }
                        break;
                    case 'bi-weekly':
                        $recurring_num = $recurring_num > 4 ? 4 : $recurring_num;
                        $recurring_num = $recurring_num % 2 == 0 ? $recurring_num : $recurring_num - 1;
                        for($i=1; $i < $recurring_num; $i++ ) {
                             $timestampsArr[$i]['start_time'] =  $start_time + 86400 * 14 * $i;
                             $timestampsArr[$i]['end_time'] =  $end_time + 86400 * 14 * $i;
                        }
                        break;
                    case 'monthly':
                        for($i=1; $i < $recurring_num; $i++ ) {
                         $timestampsArr[$i]['start_time'] =   
                                mktime(date('H',$start_time),
                                       date('i',$start_time),
                                       date('s',$start_time),
                                       date('m',$start_time) + $i,
                                       date('d',$start_time),
                                       date('Y',$start_time)
                                        );
                         $timestampsArr[$i]['end_time'] =    
                                mktime(date('H',$end_time),
                                       date('i',$end_time),
                                       date('s',$end_time),
                                       date('m',$end_time) + $i,
                                       date('d',$end_time),
                                       date('Y',$end_time)
                                        );  
                        }
                        break;
                }
            }
        }  
           
        $book = array();
        $book['boardroom'] = $formData['boardroom'];
        $book['employee_id'] = $formData['employee_id'];
        $book['note'] = trim($formData['note']);
        $book['recurring'] = $formData['recurring'];
        $book['timestampsArr'] = $timestampsArr;
        return $book;
    }
    
    public function convertToUpdateBook($formData)
    {
        $s_e_t = strtotime($formData['start_edit_time']);
        $e_e_t = strtotime($formData['end_edit_time']);
        if ($s_e_t and $e_e_t) {
           
            $timestampsArr = array();  
            $timestampsArr[0]['id'] = $formData['id'];
            $timestampsArr[0]['start_time'] = $formData['start_time'];
            $timestampsArr[0]['end_time'] = $formData['end_time'];  
            
            if(!empty($formData['recurring'])) {
                $RecurringTimestamps = self::getRecurringTimestamps($formData['recurring_id'],$formData['id']);
                $timestampsArr = array_merge( $timestampsArr, $RecurringTimestamps);
            }
            foreach($timestampsArr as &$timestamp) {
                 $timestamp['start_time'] = mktime(date('H',$s_e_t), date('i',$s_e_t), date('s',$s_e_t), 
                     date('m',$timestamp['start_time']), date('d',$timestamp['start_time']), date('Y',$timestamp['start_time']));
                 $timestamp['end_time']  = mktime(date('H',$e_e_t), date('i',$e_e_t), date('s',$e_e_t), 
                     date('m',$timestamp['end_time']), date('d',$timestamp['end_time']), date('Y',$timestamp['end_time']));
             }
             unset($timestamp);
               
            $book = array();
            $book['boardroom'] = $formData['boardroom'];
            $book['employee_id'] = $formData['employee_id'];
            $book['note'] = trim($formData['note']);
            $book['timestampsArr'] = $timestampsArr;
            return $book;   
               
               
        }
        return false;
        
    }
    public function getRecurringTimestamps($recurring_id, $id)
    {
         $sql = "SELECT id, start_time, end_time FROM appointments 
                 WHERE recurring_id = :recurring_id AND id <> :id
                 AND end_time > " .time();
            try {
                $stmt = $this->_db->prepare($sql);
                $stmt->execute(array(':recurring_id'=>$recurring_id,':id'=>$id));
            } catch (PDOException $e) {
                die($e->getMessage());
            }
            $RecurringTimestampsArr = $stmt->fetchall(PDO::FETCH_ASSOC);
            return $RecurringTimestampsArr;
    }
    public function getRecurringId($id)
    {
         $sql = "SELECT recurring_id FROM appointments 
                 WHERE id = :id";
            try {
                $stmt = $this->_db->prepare($sql);
                $stmt->execute(array(':id'=>$id));
            } catch (PDOException $e) {
                die($e->getMessage());
            }
            $recurring_id = $stmt->fetc(PDO::FETCH_COLUMN);
            return $recurring_id;
    }
    public function checkBook($book) 
    {
        if (!$book) {
            $this->_error->add('time','Invalid time format');     
        } else {
            $start_time = $book['timestampsArr'][0]['start_time'];
            $end_time = $book['timestampsArr'][0]['end_time'];
            if ( (($end_time) - ($start_time)) < 10 ) {
                $this->_error->add('time','Time the meeting must be more than 0 minutes');    
            }
            if ( $start_time <= time()  ) {
                $this->_error->add('date','This date and time has already passed');    
            }    
        }
        return $this->_error; 
    }
    public function checkBookInDb($book)
    {
        if(!empty($book['timestampsArr'][0]['id'])) {
            $idsStr = '( ';
            $isFirst = true;        
            foreach($book['timestampsArr'] as $val){
                if($isFirst) {
                    $isFirst = false;   
                } else {
                    $idsStr.= ', ';    
                }
                $idsStr .= $val['id'];    
            }
            $idsStr .= ' )';
        }
        //echo $idsStr;
        $boardroom = $book['boardroom'];
        foreach ($book['timestampsArr'] as $dt) {
            $start_time = $dt['start_time'];
            $end_time = $dt['end_time'];
            
            $sql = "SELECT count(*) FROM appointments 
                WHERE boardroom = :boardroom AND
                (:start_time BETWEEN start_time AND end_time
                OR :end_time BETWEEN start_time AND end_time
                OR start_time BETWEEN :start_time AND :end_time
                OR end_time BETWEEN :start_time AND :end_time)";
            if(!empty($idsStr)) {
                $sql.=" AND id NOT IN $idsStr";    
            }
            try {
                $stmt = $this->_db->prepare($sql);
                $stmt->execute(array(':start_time'=>$start_time,':end_time' => $end_time,'boardroom' => $boardroom));
            } catch (PDOException $e) {
                die($e->getMessage());
            }
            $count = $stmt->fetch(PDO::FETCH_COLUMN); 
            if($count) {
                $this->_error->add('booked','It is already booked');
                break;
            }
        }
        return $this->_error;
    }
    
    public function addBook($book) 
    {
        $timestampsArr = $book['timestampsArr'];
        unset($book['timestampsArr']);
        $book['start_time'] = $timestampsArr[0]['start_time'];
        $book['end_time'] = $timestampsArr[0]['end_time'];
        $recurring_id = ConnectDb::insert('appointments', $book);
        if ($book['recurring']) {
            ConnectDb::update('appointments', $recurring_id, array('recurring_id'=>$recurring_id));
            for ($i=1, $cnt = count($timestampsArr); $i<$cnt; $i++) {
                $book['start_time'] = $timestampsArr[$i]['start_time'];
                $book['end_time'] = $timestampsArr[$i]['end_time'];
                $book['recurring_id'] = $recurring_id;
                ConnectDb::insert('appointments',$book);
            }
        } 
    }
    public function updateBook($book) 
    {
     
        $timestampsArr = $book['timestampsArr'];
        unset($book['timestampsArr']);
        unset($book['boardroom']);
        foreach($timestampsArr as $ts) {
            $book['start_time'] = $ts['start_time'];
            $book['end_time'] = $ts['end_time'];
            ConnectDb::update('appointments', $ts['id'], $book);
        }
    }
    public function getBooks($boardroom = 1,$month = false,$year = false) 
    {
        $month = $month ? $month : date('n');
        $year = $year ? $year : date('Y');
        
        $start_time = mktime(0, 0, 0, $month, 1, $year);
        $end_time = mktime(23, 59, 0, $month+1, 0, $year);
        
         $sql = "SELECT id, boardroom, employee_id, note, recurring, recurring_id,
                start_time, end_time
                FROM appointments 
                WHERE boardroom = :boardroom AND start_time BETWEEN :start_time AND :end_time
                ORDER BY start_time";
            try {
                $stmt = $this->_db->prepare($sql);
                $stmt->execute(array(':start_time'=>$start_time,':end_time' => $end_time,'boardroom' => $boardroom));
            } catch (PDOException $e) {
                die($e->getMessage());
            }
            $books = $stmt->fetchall();
            $arr = array();
            foreach($books as $book) {
                $arr[date('j',$book->start_time)][] = $book;
            }
            return $arr;
            
    }
    public function deleteBook($id, $recurring_id, $IsRec)
    {
     
       
        if($IsRec) {
             print_r($IsRec);
             $sql = "DELETE FROM appointments
                     WHERE recurring_id = :recurring_id";
        } else {
            $sql = "DELETE FROM appointments
                     WHERE id = :id";
        } 
        //print_r($sql);
       
        try {
            $stmt = $this->_db->prepare($sql);
            if($IsRec) {
                 $stmt->execute(array(':recurring_id'=>$recurring_id));
            } else {
                 $stmt->execute(array(':id'=>$id));
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
         
    }
    public function getBook($id) 
    {
        $sql = "SELECT id, boardroom, employee_id, note, recurring, recurring_id,
                start_time, end_time, add_date
                FROM appointments 
                WHERE id = :id";
            try {
                $stmt = $this->_db->prepare($sql);
                $stmt->execute(array(':id'=>$id));
            } catch (PDOException $e) {
                die($e->getMessage());
            }
            $book = $stmt->fetch();
        return $book;
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
    
}


