<?php
class ServiceForm extends Form {
    
    public function __construct() {
        $this->setActionForm('Service.php');
        $this->setName('service');
        $this->setMethod('post');
        $this->setDefaultFormLabelsColSize(4);
        $this->setDefaultFormElementsColSize(8);
        parent::__construct();
        $this->init();
    }

    public function init() {     
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'type',
            'value'=>'service',
        ));    
         
        $this->addElement(array(
            'type' => 'text',
            'name' => 'code',
            'label'=>'Codigo',
            //'value'=>$this->getNextIdProduct(),
            'required'=>false
        ));

        $this->addElement(array(
            'type' => 'text',
            'name' => 'description',
            'label'=>'Descripcion',
            'required'=>true
        ));        
        
        $append = "<span class = 'btn input-group-addon' data-toggle='modal' data-target='#modalAgregarPresentacion'><i class='fa fa-plus'></i></span>";
        $this->addElement(array(
            'type' => 'select',
            'name' => 'category',
            'label'=>'Categoria',
            'multiOptions'=>$this->listaCategorias(),
            //'append'=>$append,
            'required'=>false
        ));  
        
        $append = "<span class = 'btn input-group-addon' data-toggle='modal' data-target='#modalAgregarPresentacion'><i class='fa fa-plus'></i></span>";
        $this->addElement(array(
            'type' => 'select',
            'name' => 'unit_of_measurement',
            'label'=>'Unidad medida',
            'multiOptions'=>$this->listaUnidadesMedida(),
            //'append'=>$append,
            'required'=>false
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'cost',
            'label'=>'Precio compra',
            'validators'=>array('double'),
            'required'=>false,
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'sale_price',
            'label'=>'Precio venta',
            'validators'=>array('double'),
            'required'=>false,
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'discount',
            'label'=>'Descuento',
            'validators'=>array('double'),
            'required'=>false,
        ));
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'taxes',
            'label'=>'Impuestos',
            'multiOptions'=>$this->listaImpuestos(),
        ));
        
         $this->addElement(array(
            'type' => 'select',
            'name' => 'taxes_included',
            'label'=>'Impuestos incluidos',
            'multiOptions'=>array('si'=>'Si','no'=>'No'),
            'value'=>'no'
        ));
        
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'inventory',
            'value'=>'0',
        ));
         
        $this->addElement(array(
            'type' => 'select',
            'name' => 'status',
            'label'=>'Status',
            'multiOptions'=>array('1'=>'Activo','2'=>'Inactivo'),
            'required'=>true
        ));
        
        $this->addElement(array(
            'type' => 'textarea',
            'name' => 'comments',
            'label'=>'Comentarios',
        ));        
    }
    
    public function getListStatus(){
        $repository = new ProductRepository();
        $list = $repository->getListStatus();
         
        $array = array();
        if($list){
            foreach($list as $key => $value){
                $array[$key] = $value;
            }
        }       
        return $array;            
    } 
    
    public function listaCategorias(){
        $repository = new CategoryRepository();
        $result = $repository->getListSelectCategories('product');
        
        $array = array('0' => 'Seleccionar una opcion...');
        if ($result) {
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }
    }
    
    public function listaUnidadesMedida(){
        $repository = new UMRepository();
        $result = $repository->getListSelectUMs();
        
        $array = array(0 => 'Seleccionar una opcion...');
        if ($result) {            
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
        }
        return $array;
    }    
    
    public function listaImpuestos(){            
        $repository = new ProductRepository();
        $result = $repository->getListaSelectImpuestos();
        
        if ($result) {            
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }
    }   
    
    public function getListLocations(){            
        $repository = new LocationRepository();
        $result = $repository->getListSelectLocations();
        
        $array = array();
        if ($result) {            
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
        }
        return $array;
    }   

    public function isValid() {
        $valid = parent::isValid();        
        $flashmessenger = new FlashMessenger();                
        
        $idProducto = $this->getId();
        $codigo = $this->getElement('code');
        
        $repository = new ProductRepository();
        $producto = $repository->existeCodigo($codigo['value'],$idProducto);
        if($producto !== null){
            $message  = $this->_getTranslation('Ya se esta utilizando este codigo para el producto: ');
            $message .= $producto['description'].' '.$producto['brand'];
            $flashmessenger->addMessage(array('danger'=>$message));
            return null;
        }  
         
        return $valid;
    }
    
    public function populate($data){        
        parent::populate($data);
    }
}