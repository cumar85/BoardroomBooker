<?php
class ErrorCounter {
    private $_errorMsgsArray, $_errorCount;
    public function __construct() {
        $this->_errorMsgsArray = array();
        $this->_errorCount = 0;
    }

    public function add($MsgKey, $Msg)
    {
        $this->_errorMsgsArray["$MsgKey"] = $Msg;
        $this->_errorCount++;
    }
    public function check()
    {
        return !($this->_errorCount);
    }
    public function getMsgsArr()
    {
        return $this->_errorMsgsArray;
    }
    public function getMsg($MsgKey)
    {   
        if (isset($this->_errorMsgsArray["$MsgKey"])) {
            return $this->_errorMsgsArray["$MsgKey"];    
        } else {
            return false;
        }
    }
   
}

