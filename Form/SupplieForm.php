<?php
class SupplieForm extends Form {

    public function __construct() {
        $this->setActionForm('Supplie.php');
        $this->setName('supplie');
        $this->setClass('supplie');
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
            'label'=>'Insumo',
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
        $repository = new SupplieRepository();
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
