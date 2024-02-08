<?php
class StoreForm extends Form {

    public function __construct() {
        $this->setActionForm('Store.php');
        $this->setName('store');
        $this->setClass('store');
        $this->setMethod('post');
        parent::__construct();
        $this->init();
    }

    public function init() {      
        $login = new Login();       
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'name',
            'label'=>'Nombre',
            'required'=> true
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'address',
            'label'=>'Direccion',
            'required'=> true
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'city',
            'label'=>'Ciudad',
            'required'=> true
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'state',
            'label'=>'Estado',
            'required'=> true
        ));
        
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'country',
            'value' => 'US',
            'required'=> true
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'zipcode',
            'label'=>'Codigo postal',
            'required'=> false
        ));   
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'contact_name',
            'label'=>'Contacto',
            'required'=> false
        ));  
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'phone',
            'label'=>'Telefono',
            'required'=> false
        ));  
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'fax',
            'label'=>'Fax',
            'required'=> false
        ));  
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'email',
            'label'=>'Email',
            'required'=> false
        ));  
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'webpage',
            'label'=>'Pagina web',
            'required'=> false
        ));  
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'default_location',
            'label'=>'Locacion default',
            'required'=>true
         ));
        
        if($login->getRole() === '1'){
            $this->addElement(array(
                'type' => 'select',
                'name' => 'status',
                'label'=>'Status',
                'multiOptions' => $this->getListStatus(),
                'required'=> true
            ));
        }else{            
            $this->addElement(array(
                'type' => 'hidden',
                'name' => 'status',
                'required'=> true
            ));
        }       
    }
    
    public function getListStatus(){
        $repository = new StoreRepository();
        $list = $repository->getListStatus();
         
        $array = array(''=>'Seleccionar una opcion...');
        if($list){
            foreach($list as $key => $value){
                $array[$key] = $value;
            }
        }       
        return $array;            
    }    
}
