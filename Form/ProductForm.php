<?php
class ProductForm extends Form {
    
    public function __construct() {
        $this->setActionForm('Product.php');
        $this->setName('product');
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
            'value'=>'product',
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
            'required'=>true
        ));  
        
        $append = "<span class = 'btn input-group-addon' data-toggle='modal' data-target='#modalAgregarPresentacion'><i class='fa fa-plus'></i></span>";
        $this->addElement(array(
            'type' => 'select',
            'name' => 'supplie',
            'label'=>'Insumo',
            'multiOptions'=>$this->listSupplies(),
            //'append'=>$append,
            'required'=>false,
            'optionals'=>array('disabled'=>true)
        ));
        
        $append = "<span class = 'btn input-group-addon' data-toggle='modal' data-target='#modalAgregarPresentacion'><i class='fa fa-plus'></i></span>";
        $this->addElement(array(
            'type' => 'select',
            'name' => 'masa',
            'label'=>'Tipo de masa',
            'multiOptions'=>$this->listMasas(),
            //'append'=>$append,
            'required'=>false,
            'optionals'=>array('disabled'=>true)
        ));
        
        $append = "<span class = 'btn input-group-addon' data-toggle='modal' data-target='#modalAgregarPresentacion'><i class='fa fa-plus'></i></span>";
        $this->addElement(array(
            'type' => 'select',
            'name' => 'flour',
            'label'=>'Harina',
            'multiOptions'=>$this->listFlours(),
            //'append'=>$append,
            'required'=>false,
            'optionals'=>array('disabled'=>true)
        ));
        
        $append = "<span class = 'btn input-group-addon' data-toggle='modal' data-target='#modalAgregarPresentacion'><i class='fa fa-plus'></i></span>";
        $this->addElement(array(
            'type' => 'select',
            'name' => 'brand',
            'label'=>'Marca',
            'multiOptions'=>$this->listaMarcas(),
            //'append'=>$append,
            'required'=>false
        ));   
        
        $append = "<span class = 'btn input-group-addon' data-toggle='modal' data-target='#modalAgregarPresentacion'><i class='fa fa-plus'></i></span>";
        $this->addElement(array(
            'type' => 'select',
            'name' => 'size',
            'label'=>'Tamaño',
            'multiOptions'=>$this->listSizes(),
            //'append'=>$append,
            'required'=>false
        ));
        
        $append = "<span class = 'btn input-group-addon' data-toggle='modal' data-target='#modalAgregarPresentacion'><i class='fa fa-plus'></i></span>";
        $this->addElement(array(
            'type' => 'select',
            'name' => 'presentation',
            'label'=>'Presentacion',
            'multiOptions'=>$this->listaPresentaciones(),
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
            'label'=>'Precio costo',
            'validators'=>array('double'),
            'required'=>false,
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'sale_price',
            'label'=>'Precio sucursal',
            'validators'=>array('double'),
            'required'=>false,
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'wholesale_price',
            'label'=>'Precio venta mayoreo',
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
            'type' => 'select',
            'name' => 'show_on_store_request',
            'label'=>'Mostrar para produccion',
            'multiOptions'=>array('1'=>'Si','0'=>'No'),
            'value'=>'1'
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'min_stock',
            'label'=>'Stock Min',
            'required'=>false
        ));
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'inventory',
            'label'=>'Llevar inventario',
            'multiOptions'=>array('1'=>'Si','0'=>'No'),
            'value'=>'1',
            'required'=>false
        ));
         
         $this->addElement(array(
            'type' => 'select multiple',
            'name' => 'location',
            'label'=>'Locacion',
            'multiOptions' => $this->getListLocations(),
            'value'=>'',
            'required'=>false
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
        
        $array = array('' => 'Seleccionar una opcion...');
        if ($result) {
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }
    }
    
    public function listSupplies(){
        $repository = new SupplieRepository();
        $result = $repository->getListSelectSupplies();
        
        $array = array('' => 'Seleccionar una opcion...');
        if ($result) {
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
        }
         return $array;
    }
    
    public function listMasas(){
        $repository = new ProductRepository();
        $result = $repository->getListSelectMasas();
        
         $array = array('' => 'Seleccionar una opcion...');
        if ($result) {           
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }
    }   
    
    public function listFlours(){
        $repository = new FlourRepository();
        $result = $repository->getListSelectFlours();
        
         $array = array('' => 'Seleccionar una opcion...');
        if ($result) {           
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }
    }   
    
    public function listSizes(){
        $repository = new SizeRepository();
        $result = $repository->getListSelectSizes();
        
        $array = array('' => 'Seleccionar una opcion...');
        if ($result) {
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
        }
         return $array;
    }
    
    public function listaMarcas(){
        $repository = new BrandRepository();
        $result = $repository->getListSelectBrands();
        
         $array = array(0 => 'Seleccionar una opcion...');
        if ($result) {           
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }
    }   
    
    public function listaPresentaciones(){
        $repository = new PresentationRepository();
        $result = $repository->getListSelectPresentations();
        
         $array = array(0 => 'Seleccionar una opcion...');
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
    
    public function getNextIdProduct(){
        $repo = new ProductRepository();
        $lastId = $repo->getLastIdInsumo();
        $nextId = $lastId + 1;
        
        return $nextId;
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
        if(isset($data['location']) && $data['location']!=null && is_array($data['location'])){
            $sucursales = array();
            foreach($data['location'] as $sucursal){
                $sucursales[$sucursal] = $sucursal;
            }
            $data['location'] = $sucursales;
        }
        
        /*1 = insumo*/  
        if($data['category'] == '1'){
            $this->deleteOptionals('supplie', array('disabled'));
            $this->setAsRequired(array('supplie'));
        }
        
        parent::populate($data);
    }
}