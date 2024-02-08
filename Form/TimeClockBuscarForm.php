<?php
/**
 * Description of NivelForm
 *
 * @author carlos
 */

class TimeClockBuscarForm extends Form {

    public function __construct() {
        $this->setActionForm('TimeClock.php');
        $this->setMethod('post');
        $this->setDefaultFormLabelsColSize('5');
        $this->setDefaultFormElementsColSize('7');
        parent::__construct();
        $this->init();
    }

    public function init() {        
        $this->addElement(array(
            'type' => 'select multiple',
            'name' => 'user',
            'label'=>'Usuario',
            'multiOptions' => $this->getListUsuarios(),
            'required'=> true,
        ));       
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'start_date',
            'label'=>'Fecha inicio',
            'required'=> false
        ));                
        $this->addElement(array(
            'type' => 'text',
            'name' => 'end_date',
            'label'=>'Fecha fin',
            'required'=> false
        ));
        
        $this->addElement(array(
            'type' => 'submit',
            'name' => 'search',
            'value'=> $this->_getTranslation('Buscar'),
            'optionals'=>array(
                'onclick'=>"submit()"),
            'class'=>'btn btn-primary'
        ));
        
        $this->addElement(array(
            'type' => 'button',
            'name' => 'cerrar',
            'value'=> $this->_getTranslation('Cerrar'),
            'class'=>'btn btn-default',
            'optionals'=>array(
                'data-dismiss'=>'modal',
                'aria-hidden'=>'true',
                )
        ));
    }
    
    public function getListUsuarios(){
        $repository = new UserRepository();
        $list = $repository->getListSelectUsers();
         
         $array = array(''=>''); #Para poder aplicar "placeholder"  en select2 en vista
        foreach($list as $key => $value){
            $array[$key] = $value;
        }
        return $array;
    }
}