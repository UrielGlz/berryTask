<?php
class AlumnoBuscarForm extends Form {

    public function __construct() {
        $this->setActionForm('Alumno.php');
        $this->setActionController('');
        $this->setClass('alumno');
        $this->setMethod('post');
        parent::__construct();
        $this->init();
    }

    public function init() {       
        $this->addElement(array(
            'type' => 'select multiple',
            'name' => 'colegio_id',
            'label'=>'Colegio',
            'multiOptions'=>$this->getListColegios(),
            'required'=> true
        ));   
                
        $this->addElement(array(
            'type' => 'text',
            'name' => 'fechaInicio',
            'label'=>'Fecha inicio',
            'required'=> true
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'fechaFin',
            'label'=>'Fecha fin',
            'required'=> true
        ));
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'status',
            'label'=>'Status',
            'multiOptions'=>array(''=>'Seleccionar una opcion...','1'=>'Activo','2'=>'Inactivo'),
            'required'=> true
        ));
        
       $this->addElement(array(
            'type'=>'submit',
            'name'=>'search',
            'label'=>'',
            'value'=>$this->_getTranslation('Buscar'),
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
    
   public function getListColegios(){
        $repository = new ColegioRepository();
        $list = $repository->getListSelectColegios();
         
        $array = array(''=>'Seleccionar una opcion...');
        if($list){
            foreach($list as $key => $value){
                $array[$key] = $value;
            }
        }       
        return $array;            
    }
}