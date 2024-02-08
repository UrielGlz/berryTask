<?php
class PagoBuscarForm extends Form {    
    public function __construct() {
        $this->setName('pagobuscar');
        $this->setActionForm('Payment.php');
        $this->setActionController('list');
        $this->setClass('pagobuscar form-horizontal');
        $this->setMethod('post');
        parent::__construct();
        $this->init();
    }

    public function init() {      
        $this->addElement(array(
            'type'=>'select',
            'name'=>'proveedor',
            'label'=>'Proveedor',
            'multiOptions'=>$this->getListProveedores(),
            'optionals'=>array('onchange'=>'getListFacturasProveedores()'),
            'required'=>false
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'num_factura',
            'label'=>'Num. Factura',
            'required'=> false
        ));    
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'monto',
            'label'=>'Monto',
            'validators'=>array('double'),
            'required'=> true
        ));         
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'forma_de_pago',
            'label'=>'Forma de pago',
            'multiOptions'=>$this->getListFormaPago(),
            'required'=>true
        ));                 

        $this->addElement(array(
            'type' => 'text',
            'name' => 'num_operacion',
            'label'=>'Num. operacion',
            'required'=> false
        ));        
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'status',
            'label'=>'Status',
            'multiOptions'=>array(''=>'Todos','1'=>'Activo','2'=>'Cancelado'),
            'required'=>true
        )); 
         
        $this->addElement(array(
            'type' => 'text',
            'name' => 'startDate',
            'label'=>'Fecha inicio',
            'validators'=>array('date'),
            'required'=>false
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'endDate',
            'label'=>'Fecha fin',
            'validators'=>array('date'),
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
    
   public function getListProveedores(){
        $repository = new VendorRepository();
        $result = $repository->getListSelectVendors();

        if ($result) {
            $array = array(null => 'Selecciona una opcion');
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }
    }

    public function getListFormaPago(){
        $repository = new PagoRepository();
        $result = $repository->getListFormaPago();        

        if(count($result)>1){ $array = array(''=>'Seleccionar una opcion...');}
        if ($result) {               
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }
    }    
}