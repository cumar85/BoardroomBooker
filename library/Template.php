<?php
class Template 
{
    private $_tpl = "";
    private $_vars = array(); 
    public function assign($arrOrKey,$value=null)
    {
        if(is_array($arrOrKey)) {
            $this->_vars += $arrOrKey;
        } else {
            $this->_vars[$arrOrKey] = $value;
        }
    }
    public function display($tpl)
    {
        $this->_tpl = $tpl; 
        if ($this->_vars) {
            extract($this->_vars);
        }
        ob_start();
        if(is_array($this->_tpl)) {
            foreach ($this->_tpl as $tp) {
                include $tp.'.phtml';    
            }
        } else {
            include $this->_tpl.'.phtml';
        }
        ob_end_flush();
       
    }
}
    
    
    
    
    
    



