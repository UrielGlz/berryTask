<?php
/**
 * Description of FlashMessenger
 *
 * @author carlos
 */
class FlashMessenger extends Translator{   
    public function __construct() {
        if(!isset($_SESSION['flashmessenger'])){
            $_SESSION['flashmessenger'] = array();
        }
            parent::__construct();
    }
    
    public function addMessage($message){
        $_SESSION['flashmessenger'][] = $message;
    }
    
    public function showMessage($clearMessages = true){
        if($this->getMessages()){
            $messages = '';
            foreach ($_SESSION['flashmessenger'] as $message){
                $messages .= $this->prepareMessage($message);
            }            
            echo $messages;//exit;
            if($clearMessages){ $_SESSION['flashmessenger'] = array();}
           
        }
        return null;
    }
    
    public function prepareMessage($message){
        foreach ($message as $key => $value){$tipo = $key; $mensaje = $value;}
        $messages = '';
        $messages .= "<div class='alert alert-$tipo'>";
        $faIcon = array('info'=>'fa-info-circle','success'=>'fa-check-circle','danger'=>'fa-times-circle');
        $messages .="<button type='button' class='close' data-dismiss='alert'>&times;</button>";
        $messages .= "<i class='fa {$faIcon[$tipo]} fa-lg '></i> ".$this->_getTranslation($mensaje);
        $messages .= "</div>";

        return $messages;
    }
    
    public function getMessages(){ 
        if(count($_SESSION['flashmessenger']) > 0){
    
            return $_SESSION['flashmessenger'];
        }
        return null;
    }
    
    public function getMessageString($ontas = null){
        if($this->getMessages()){
            $messages = '';
            foreach ($_SESSION['flashmessenger'] as $message){
                $messages .= $this->prepareMessage($message);
            }
            
            $_SESSION['flashmessenger'] = array();
            return $messages;//exit;
            
        }
        return null;
    }
    
    public function getRawMessage(){
        if($this->getMessages()){
            $messages = '';
            foreach ($_SESSION['flashmessenger'] as $message){
                foreach ($message as $key => $value){$tipo = $key; $mensaje = $value;}
                $messages .= $mensaje;
            }            
            $_SESSION['flashmessenger'] = array();
            return $messages;//exit;
        }
        return null;
    }


    public function clear(){
        $_SESSION['flashmessenger'] = array();
    }
}
