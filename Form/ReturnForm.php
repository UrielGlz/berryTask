<?php
class ReturnForm extends Form {    
    public function __construct() {
        $this->setName('return');
        $this->setActionForm('Return.php');
        $this->setClass('return form-horizontal');
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
            'name' => 'store_id',
            'label'=>'Sucursal',
            'multiOptions'=>$this->getListStores()
         ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'returned_by',
            'label'=>'Retornado por',
            'required'=>true,
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
            'name' => 'id_product',
        ));
          
        $append = "<span class = 'btn input-group-addon' data-toggle='modal' data-target='#modalAgregarProducto'><i class='fa fa-plus'></i></span>";
        $this->addElement(array(
            'type' => 'text',
            'name' => 'product',
            'label'=>'Producto',
            'optionals'=>array('onKeyPress'=>'onEnterReturn(event.keyCode,this)','placeholder'=>'Teclea o escanea producto para la salida'),
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
            'type' => 'select',
            'name' => 'location',
            'label'=>'Locacion',
            'col-size-element'=>'12'
         ));
        
        $this->addElement(array(
            'type'=>'button',
            'name'=>'agregar_producto',
            'value'=>$this->_getTranslation('Agregar'),
            'class'=>'btn btn-default'
        ));
        
        $this->addElement(array(
            'type'=>'button',
            'name'=>'terminar',
            'value'=>$this->_getTranslation('Terminar'),
            'class'=>'btn btn-primary',
            'optionals'=>array("onClick"=>"submit('return')")
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
        
        $login = new Login();
        if($login->getRole()!='1'){
            $storesArray = explode(',', $login->getStoreId());
            if(count($storesArray) > 1){$array = array(''=>'Seleccionar una opcion...');}
            if ($result) {
                $array = array();
                foreach ($result as $key => $value) {
                    if(in_array($key, $storesArray)){$array[$key] = $value;}
                }
            }   
        }else{
            $array = array(''=>'Seleccionar una opcion...');
            if ($result) {               
                foreach ($result as $key => $value) {
                    $array[$key] = $value;
                }
            }   
        } 
        return $array;
    }
    
    public function populate($data) { 
        $tools = new Tools();
        if(isset($data['date'])){
            $data['date'] = $tools->setFormatDateTimeToForm($data['date']);
        }
        
        if($this->getActionController() == 'edit'){
            $repo = new StoreRepository();
            $storeData = $repo->getById($data['store_id']);

            $options[$storeData['id']] = $storeData['name'];            
            $this->setPropiedad('store_id', array('multiOptions'=>$options));            
        }
        parent::populate($data);
    } 
}