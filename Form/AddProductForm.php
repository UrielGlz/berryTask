<?php
class AddProductForm extends Form {    
    public function __construct() {
        $this->setName('addProduct');
        parent::__construct();
        $this->init();
    }

    public function init() {      
        /*Se utiliza en modal addReceivingProduct*/
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'added',
            'required'=> true,
            'value'=>'1'
        ));
         
         /*Se utiliza para consulta si existe en la tabla de comprasdetalles_X, si existe se actualiza registro.*/
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
            'optionals'=>array('onKeyPress'=>'onEnterPurchase(event.keyCode,this)','placeholder'=>'Teclea o escanea producto.'),
            'append'=>$append
         ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'quantity',
            'label'=>'Cantidad',
            'validators'=>array('double'),
            'required'=>false,
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'cost',
            'label'=>'Costo',
            'validators'=>array('double'),
            'required'=>false,
        ));
        
         $this->addElement(array(
            'type' => 'text',
            'name' => 'price',
            'label'=>'Precio',
            'validators'=>array('double'),
            'required'=>false,
        ));
        
        $append = "<div class='input-group-btn descuento_tipo' style='width:40%'>
                    <select class='form-control _discount_type' name='discount_type' >                    
                      <option value='monto'>Monto</option>
                      <option value='porcentaje'>Porcentaje</option>
                    </select>
                  </div>";
        
       $this->addElement(array(
            'type' => 'text',
            'name' => 'discount',
            'label'=>'Descuento',
           'append'=>$append,
            'required'=>false
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
            'multiOptions'=>array('no'=>'No','si'=>'Si'),
        ));
        
        $attributes_wrapper_append_date = array('id'=>'expirationDatePicker');
        $append = "<span class = 'btn input-group-addon'><i class='fa fa-calendar'></i></span>";
        $this->addElement(array(
            'type' => 'text',
            'name' => 'expiration_date',
            'label'=>'Expiracion',
            'validators'=>array('date'),
            'required'=>false,
            'append'=>$append,
            'wrapper_attributes'=>$attributes_wrapper_append_date
        ));
        
        $this->addElement(array(
            'type' => 'textarea',
            'name' => 'description_details',
            'label'=>'Detalles',
            'required'=>false,
            'class'=>'form-control',
            'optionals'=>array('style'=>'min-height:180px')
        ));
        
        $this->addElement(array(
            'type'=>'button',
            'name'=>'buscar',
            'value'=>$this->_getTranslation('Agregar'),
            'class'=>'btn btn-primary _addProduct'
        ));
        
        $this->addElement(array(
            'type'=>'button',
            'name'=>'cerrar_modal',
            'value'=>$this->_getTranslation('Cerrar'),
            'class'=>"btn btn-default _closeModalAddProduct"
        ));        
    }
    
    public function getListProducts(){
        $repository = new SupplieRepository();
        #1 = status activo
        $productos = $repository->getListSupplies();
        
        $array = array('0'=>'Seleccionar una opcion...');        
        foreach($productos as $producto){       
            $array[$producto['id']] = $producto['description']." (".$producto['code'].")";
        }
        
        $list= array();
        foreach ($array as $key => $value) {
            $list[$key] = $value;
        }
        return $list;
    }
    
    public function listaImpuestos(){            
        $repository = new ProductRepository();
        $result = $repository->getListaSelectImpuestos();
        
        $array = array();
        if ($result) {            
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }
    }       
}