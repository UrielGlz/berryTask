<?php
class VendorForm extends Form {

    public function __construct() {
        $this->setActionForm('Vendor.php');
        $this->setName('vendor');
        $this->setClass('vendor');
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
            'name' => 'name',
            'label'=>'Nombre',
            'required'=> true
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'address',
            'label'=>'Direccion',
            'required'=> false
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'city',
            'label'=>'Ciudad',
            'required'=> false
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'state',
            'label'=>'Estado',
            'required'=> false
        ));
        
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'country',
            'value' => 'US',
            'required'=> false
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
            'name' => 'phone_1',
            'label'=>'Telefono adicional',
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
            'name' => 'email_1',
            'label'=>'Email adicional',
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
            'name' => 'status',
            'label'=>'Status',
            'multiOptions' => $this->getListStatus(),
            'required'=> true
        ));
       
        $this->addElement(array(
            'type' => 'select',
            'name' => 'payment_method',
            'label'=>'Forma de pago',
            'multiOptions'=>$this->getListMetodoPago(),
            'required'=>true
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'credit_days',
            'label'=>'Dias de credito',
            'optionals'=>array('readOnly'=>true),
            'required'=>true
        ));
         
        $this->addElement(array(
            'type' => 'textarea',
            'name' => 'comments',
            'label'=>'Comentarios',
            'required'=> false
        ));
        
        $this->addElement(array(
            'type' => 'button',
            'name' => 'send',
            'value'=> $this->_getTranslation('Terminar'),
            'optionals'=>array(
                'onclick'=>"submit()"),
            'class'=>'btn btn-primary'
        ));
        
        $this->addElement(array(
            'type' => 'button',
            'name' => 'cancelar',
            'value'=> $this->_getTranslation('Cancelar'),
            'optionals'=>array(
            'onclick'=>"document.location = '".ROOT_HOST."/Controller/Vendor.php'"),
            'class'=>'btn btn-danger'
        ));
    }
    
    public function getListStatus(){
        $repository = new VendorRepository();
        $list = $repository->getListStatus();
         
        $array = array();
        if($list){
            foreach($list as $key => $value){
                $array[$key] = $value;
            }
        }       
        return $array;            
    }   
    
    public function getListMetodoPago(){
        $repository = new VendorRepository();
        $result = $repository->getListMetodoPago();        

        $array = array();
        if ($result) {               
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
        }
        return $array;
    }    
    
    public function populate($data) {
        if($data['payment_method']=='2'){
            $this->deleteOptionals('credit_days', array('readOnly'));
        }
        parent::populate($data);
    }
}
