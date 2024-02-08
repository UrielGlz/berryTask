<?php
class EmailingForm extends Form {

    public function __construct() {
        $this->setName('emailing');
        parent::__construct();
        $this->init();
    }

    public function init() {
        /*For send SpecialRequisition*/
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'id_special_requisition'
        ));
        /*END For send BOL*/
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'to',
            'label'=>'Para',
            'required'=> true,
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'cc',
            'label'=>'Con copia',
            'required'=> true,
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'subject',
            'label'=>'Titulo',
            'required'=> true,
        ));
        
        $this->addElement(array(
            'type' => 'textarea',
            'name' => 'message',
            'label'=>'Mensaje',
            'required'=> true,
        ));
        
        $this->addElement(array(
            'type' => 'button',
            'name' => 'send_emailing',
            'value'=> $this->_getTranslation('Enviar correo'),
            'class'=>'btn btn-primary'
        ));
    }
    
    public function populate($data){
        parent::populate($data);
    }
}