<?php
class ShipmentStoreRequestForm extends Form {    
    public function __construct() {
        $this->setName('shipment_store_request');
        $this->setActionForm('ShipmentStoreRequest.php');
        $this->setClass('shipment_store_request');
        $this->setMethod('post');
        parent::__construct();
        $this->init();
    }

    public function init() {                
        $attributes_wrapper_append_select = array('class'=>'select2-bootstrap-append');
        
        $attributes_wrapper_append_date = array('class'=>'date','id'=>'dateDatePicker');
        $append = "<span class = 'btn input-group-addon'><i class='fa fa-calendar'></i></span>";
        $this->addElement(array(
            'type' => 'text',
            'name' => 'date',
            'label'=>'Fecha',
           'validators'=>array('datetime'),
            'required'=>true,
            'append'=>$append,
            'wrapper_attributes'=>$attributes_wrapper_append_date
        ));  
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'to_store',
            'label'=>'Sucursal',
            'multiOptions'=>$this->getListStores(),
            'required'=>true
         ));
        
         $this->addElement(array(
            'type' => 'text',
            'name' => 'id_store_request',
            'label'=>'Pedido #',
            'required'=>true
        ));  
        
        $this->addElement(array(
            'type' => 'textarea',
            'name' => 'comments',
            'label'=>'Notas',
            'required'=>false
        ));  
        
         $this->addElement(array(
            'type' => 'hidden',
            'name' => 'status'
        ));   
                 
         /*Se utiliza para consulta si existe en la tabla de requisitions_details_X, si existe se actualiza registro.*/
         $this->addElement(array(
            'type' => 'hidden',
            'name' => 'idDetailTemp',
            'required'=>false
        ));          
         
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'idProduct',
        ));
          
        $append = "<span class = 'btn input-group-addon' data-toggle='modal' data-target='#modalAgregarProducto'><i class='fa fa-plus'></i></span>";
        $this->addElement(array(
            'type' => 'text',
            'name' => 'product',
            'label'=>'Producto',
            'col-size-element'=>'12',
            'append'=>$append
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'quantity',
            'label'=>'Cantidad',
            'value'=>'1',
            'required'=>false,
            'col-size-element'=>'12'
        ));
        
        $this->addElement(array(
            'type'=>'button',
            'name'=>'buscar',
            'value'=>$this->_getTranslation('Agregar'),
            'class'=>'btn btn-default',
            'optionals'=>array('onClick'=>'setShipmentDetails()')
        ));
        
        $this->addElement(array(
            'type'=>'button',
            'name'=>'terminar',
            'value'=>$this->_getTranslation('Terminar'),
            'class'=>'btn btn-primary',
            'optionals'=>array("onClick"=>"submit('shipment')")
        ));        
    }
    
    public function getListProducts(){
        $repository = new ProductRepository();
        #1 = status activo
        $productos = $repository->getListProducts('1');
        
        $array = array('0'=>'Seleccionar una opcion...');        
        foreach($productos as $producto){      
            $array[$producto['id']] = $producto['code']." - ".$producto['description']." ".$producto['size'];
        }
        
        $list= array();
        foreach ($array as $key => $value) {
            $list[$key] = $value;
        }
        return $list;
    }
    
    public function getListStores(){
        $repo = new StoreRepository();
        $result = $repo->getListSelectStores();
        
        if(count($result)>1){ $array = array(''=>'Seleccionar una opcion...');}
        if ($result) {               
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }        
    }
    
    public function populate($data) { 
        $tools = new Tools();
        if(isset($data['date'])){
            $data['date'] = $tools->setFormatDateTimeToForm($data['date']);
        }
        parent::populate($data);
    } 
}