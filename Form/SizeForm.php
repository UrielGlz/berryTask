<?php
class SizeForm extends Form {

    public function __construct() {
        $this->setActionForm('Size.php');
        $this->setName('size');
        $this->setClass('size');
        $this->setMethod('post');
        $this->setDefaultFormLabelsColSize(4);
        $this->setDefaultFormElementsColSize(8);
        parent::__construct();
        $this->init();
    }

    public function init() {      
        $login = new Login();       
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'description',
            'label'=>'Tamaño',
            'required'=> true
        ));
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'status',
            'label'=>'Status',
            'multiOptions' => $this->getListStatus(),
            'required'=> true
        ));
    }
    
    public function getListStatus(){
        $repository = new SizeRepository();
        $list = $repository->getListStatus();
         
        $array = array();
        if($list){
            foreach($list as $key => $value){
                $array[$key] = $value;
            }
        }       
        return $array;            
    }    
}
