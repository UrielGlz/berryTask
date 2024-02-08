<?php
/**
 * Description of NivelForm
 *
 * @author carlos
 */

class PaymentTermsForm extends Form {

    public function __construct() {
        $this->setActionForm('PaymentTerms.php');
        $this->setName('paymentterms');
        $this->setClass('paymentterms');
        $this->setMethod('post');
        parent::__construct();
        $this->init();
    }

    public function init() {
        $this->addElement(array(
            'type' => 'text',
            'name' => 'name',
            'label'=>'Nombre',
            'required'=> true,
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'days',
            'label'=>'Dias',
            'required'=> true,
        ));
    }
    
    public function populate($data){
        parent::populate($data);
    }
}