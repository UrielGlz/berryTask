<?php
class LocationForm extends Form {

    public function __construct() {
        $this->setActionForm('Location.php');
        $this->setName('location');
        $this->setClass('location');
        $this->setMethod('post');
        $this->setDefaultFormLabelsColSize(4);
        $this->setDefaultFormElementsColSize(8);
        parent::__construct();
        $this->init();
    }

    public function init() {              
        $this->addElement(array(
            'type' => 'select',
            'name' => 'store_id',
            'label'=>'Sucursal',
            'multiOptions'=>$this->getListStores(),
            'required'=>true
         ));
        
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'description',
            'label'=>'Locacion',
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
        $repository = new LocationRepository();
        $list = $repository->getListStatus();
         
        $array = array();
        if($list){
            foreach($list as $key => $value){
                $array[$key] = $value;
            }
        }       
        return $array;            
    }    
    
     public function getListStores(){
        $repo = new StoreRepository();
        $result = $repo->getListSelectStores();
        
        $login = new Login();
        if($login->getRole()!='1'){
            if ($result) {
                $array = array();
                foreach ($result as $key => $value) {
                    if($key == $login->getStoreId()){$array[$key] = $value;}
                }
            }   
        }else{
            $array = array(''=>'Seleccionar una opcion...');
            if ($result) {               
                foreach ($result as $key => $value) {
                    $array[$key] = $value;
                }
            }   
        } 
        return $array;
    }
}
