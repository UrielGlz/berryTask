<?php
class CompanyForm extends Form {

    public function __construct() {
        $this->setActionForm('Company.php');        
        $this->setEnctype('multipart/form-data');
        $this->setName('company');
        $this->setClass('company');
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
            'type' => 'file',
            'name' => "logo",
            'label' => 'Logo',
            'class' => 'upload',
            'required' => false,
            'optionals' => array(
                'title' => 'Logo'
            ),
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
            'onclick'=>"document.location = '".ROOT_HOST."/Controller/Company.php'"),
            'class'=>'btn btn-danger'
        ));
    }   
    
    public function populate($data) {
        return parent::populate($data);
    }
}
