<?php
class AddDepositDetailForm extends Form {    
    public function __construct() {
        $this->setName('addDepositDetail');
        $this->setDefaultFormLabelsColSize(5);
        $this->setDefaultFormElementsColSize(7);
        parent::__construct();
        $this->init();
    }

    public function init() {                    
         $this->addElement(array(
            'type' => 'hidden',
            'name' => 'idDetailTemp',
            'required'=>false
        ));              
         
        $this->addElement(array(
            'type' => 'text',
            'name' => 'sale_date',
            'label'=>'Fecha de venta inicial',
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'sale_date_final',
            'label'=>'Fecha de venta final',
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'sale_total_cash',
            'label'=>'Total venta en efectivo',
        ));
         
         
        $this->addElement(array(
            'type' => 'textarea',
            'name' => 'sale_comments',
            'label'=>'Notas',
            'required'=>false
        ));          
        
        $this->addElement(array(
            'type'=>'button',
            'name'=>'save_deposit_detail',
            'value'=>$this->_getTranslation('Agregar'),
            'class'=>'btn btn-primary _saveDepositDetail'
        ));
        
        $this->addElement(array(
            'type'=>'button',
            'name'=>'cerrar_modal',
            'value'=>$this->_getTranslation('Cerrar'),
            'class'=>"btn btn-default _closeModalAddDeposit"
        ));        
    }
    
    public function populate($data){
        parent::populate($data);
    }
}