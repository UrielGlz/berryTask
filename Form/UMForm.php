<?php
class UMForm extends Form {

    public function __construct() {
        $this->setActionForm('UM.php');
        $this->setName('um');
        $this->setClass('um');
        $this->setMethod('post');
        $this->setDefaultFormLabelsColSize(5);
        $this->setDefaultFormElementsColSize(7);
        parent::__construct();
        $this->init();
    }

    public function init() {      
        $login = new Login();       
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'description',
            'label'=>'Unidad de medida',
            'required'=> true
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'abbreviation',
            'label'=>'Abreviacion',
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
        $repository = new UMRepository();
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
