<?php
class ReceivingStoreRequestForm extends Form {    
    public function __construct() {
        $this->setName('receiving_store_request');
        $this->setActionForm('ReceivingStoreRequest.php');
        $this->setClass('receiving_store_request');
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
         
        //$append = "<span class = 'btn input-group-addon' onclick='getShipmentData()'><i class='fa fa-eye'></i></span>";
        $this->addElement(array(
            'type' => 'select',
            'name' => 'num_shipment',
            'label'=>'Num de envio',
            'multiOptions'=>$this->getListShipmentStoreRequest(),
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
        
                
         $this->addElement(array(
            'type' => 'hidden',
            'name' => 'status_invoice',
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
            'required'=>false
        )); 
         
        $append = "<span class = 'btn input-group-addon' data-toggle='modal' data-target='#modalAgregarProducto'><i class='fa fa-plus'></i></span>";
        $this->addElement(array(
            'type' => 'text',
            'name' => 'product',
            'label'=>'Producto',
            'col-size-label'=>'12',
            'col-size-element'=>'12',
            'append'=>$append

        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'received',
            'label'=>'Recibido',
            'value'=>'1',
            'required'=>false,
            'col-size-element'=>'12'
        ));
        
         $this->addElement(array(
            'type' => 'hidden',
            'name' => 'allow_edit',
        ));
        
         $this->addElement(array(
            'type'=>'button',
            'name'=>'btn_allow_edit',
            'value'=>$this->_getTranslation('Permiso para editar'),
            'class'=>'btn btn-warning'
        ));
        
        $this->addElement(array(
            'type'=>'button',
            'name'=>'buscar',
            'value'=>$this->_getTranslation('Agregar'),
            'class'=>'btn btn-default',
            'optionals'=>array('onClick'=>'setReceivingStoreRequestDetails()')
        ));
        
        $this->addElement(array(
            'type'=>'button',
            'name'=>'terminar',
            'value'=>$this->_getTranslation('Terminar'),
            'class'=>'btn btn-primary',
        ));        
    }
    
     public function getListShipmentStoreRequest(){
        $repository = new ReceivingStoreRequestRepository();
        $list = $repository->getLisShipmentStoreRequestToReceive();
         
        $array = array(''=>'Seleccionar envio a recibir...');
        if($list){
            foreach($list as $key => $value){
                $array[$key] = $value;
            }
        }       
        return $array;            
    }    
    
    public function getListProducts(){
        $repository = new ProductoRepository();
        #1 = status activo
        $productos = $repository->getListaProductos('1');
        
        $array = array('0'=>'');        
        foreach($productos as $producto){      
            $array[$producto['id']] = $producto['codigo']." - ".$producto['descripcion']." ".$producto['tamano'];
        }
        
        $list= array();
        foreach ($array as $key => $value) {
            $list[$key] = $value;
        }
        return $list;
    }
    
    public function populate($data) { 
        $tools = new Tools();
        if(isset($data['date'])){
            $data['date'] = $tools->setFormatDateTimeToForm($data['date']);
        }
        
        if($this->getActionController() == 'edit'){
            $repo = new ReceivingStoreRequestRepository();
            $currentData = $repo->getById($this->getId());
            
            $purchaseRepo = new ShipmentStoreRequestRepository();
            $shipmentData = $purchaseRepo->getByNumShipment($currentData['num_shipment']);
            $reference = 'Envio #'.$shipmentData['num_shipment'].' - ( Pedido # '.$shipmentData['id_store_request']." )";

            $options[$currentData['num_shipment']] = $reference;            
            $this->setPropiedad('num_shipment', array('multiOptions'=>$options));    
        }
            
        parent::populate($data);
    } 
}