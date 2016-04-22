<?
class ConnectDb 
{
    
    protected $_db;
    static private $_instance = null;
    private function __construct() 
    {
        try {   
            $this->_db = new PDO('mysql:host='.DB_HOST, DB_USER, DB_PASSWORD);
            //$this->_db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
            $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ); 
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        try {
            $this->_db->exec("SET NAMES " . DB_CHARSET);
            $this->_db->exec("USE ". DB_NAME);
        } catch (PDOException $e) {
            $error = $this->_db->errorInfo();
            if($error[1] == 1049) {
             //   if (file_exists('../127.0.0.1.sql')) {
             //       $sql = file_get_contents('../127.0.0.1.sql', false);
             //   } else {
                     //header("Location: ".PRJ_URL."/index/DatabaseNotFound");
             //   }
                    die('Database "'. DB_NAME .'" Not Found!');
                try {
             //     $this->_db->exec($sql);
                    $this->_db->exec("USE ". DB_NAME);
                } catch (PDOException $e) {
                    die($e->getMessage());
                }
            } else {
                die($e->getMessage());
            }
        }
    }
    
    public function __destruct() 
    {
        $this->_db = null;
    }
    
    public static function getInstance()
    {
        if(self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getConnect()
    {
        return $this->_db; 
    }
    
    public static function insert($table, $dataArr)
    {
        $db = self::getInstance()->getConnect();
        if (!empty($dataArr)) {
        $fields ='';
        $values ='';
        $isfirst = true;
        foreach($dataArr as $k=>$v) {
            if($isfirst) {
               $isfirst = false; 
            } else {
                $fields .= ', '; $values .= ', ';   
            }
            $fields .= $k; $values .= ':'.$k;
        }
        $sql = "INSERT INTO $table ( $fields )
            VALUES ( $values );";
       // print_r($sql);
        
            try {
                $stmt = $db->prepare($sql);
                foreach ($dataArr as $k=>$v) {
                    $stmt->bindValue(':'.$k, $v);
                }
                $stmt->execute();
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        } else {
            return false;
        }
        
         return $db->lastInsertId(); 
    }
    //"UPDATE per­sons SET street = 'Nis­sesti­en 67', ci­ty = 'Sand­nes' WHERE lastname = 'Tjes­sem'";
    public static function update($table, $id, $dataArr)
    {
        $db = self::getInstance()->getConnect();
        if (!empty($dataArr)) {
        $set ='';
        $isfirst = true;
        foreach($dataArr as $k=>$v) {
            if($isfirst) {
               $isfirst = false; 
            } else {
                $set .= ', ';   
            }
            $set .= $k.' = :'.$k;
        }
        $sql = "UPDATE  $table SET $set
           WHERE id = :id;";
        
        
            try {
                $stmt = $db->prepare($sql);
                    $stmt->bindValue(':id', $id);
                foreach ($dataArr as $k=>$v) {
                    $stmt->bindValue(':'.$k, $v);
                }
                $stmt->execute();
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        } else {
            return false;
        }
        
        return true;
    }
}
