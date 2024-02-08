<?php
class SliceForm extends Form {    
    public function __construct() {
        $this->setName('slice');
        $this->setActionForm('Slice.php');
        $this->setClass('compra form-horizontal');
        $this->setMethod('post');
        parent::__construct();
        $this->init();
    }

    public function init() {                
        $attributes_wrapper_append_select = array('class'=>'select2-bootstrap-append');
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'category',
            'label'=>'Categoria',
            'multiOptions'=>$this->listCategories(),
            'required'=>true
        ));
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'size',
            'label'=>'Tamaño',
            'multiOptions'=>$this->listSizes(),
            'required'=>true
        ));
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'shape',
            'label'=>'Forma',
            'multiOptions'=>$this->listShapes(),
            'required'=>true
        ));
        
         $this->addElement(array(
            'type' => 'text',
            'name' => 'flavor',
            'label'=>'Sabor',
            'required'=>true
        )); 
         
        $this->addElement(array(
            'type' => 'text',
            'name' => 'price',
            'label'=>'Precio',
            'validators'=>array('double'),
            'required'=>true,
        ));
        
        $this->addElement(array(
            'type' => 'textarea',
            'name' => 'comments',
            'label'=>'Notas',
            'required'=>false,
            'optionals'=>array('style'=>'min-height:190px')
        ));          
         
         /*Se utiliza para consulta si existe en la tabla de comprasdetalles_X, si existe se actualiza registro.*/
         $this->addElement(array(
            'type' => 'hidden',
            'name' => 'idDetailTemp',
            'required'=>false
        ));            
        
        $this->addElement(array(
            'type'=>'button',
            'name'=>'terminar',
            'value'=>$this->_getTranslation('Terminar'),
            'class'=>'btn btn-primary',
            'optionals'=>array("onClick"=>"submit('slice')")
        ));        
    }
    
    public function listCategories(){
        $repository = new CategoryRepository();
        $list = $repository->getListSelectCategories('parts of the cake');

        if(is_array($list) && count($list)>1){ $array = array(''=>'Seleccionar una opcion...');}
        if ($list) {               
            foreach ($list as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }        
        return array(''=>'Seleccionar una opcion...');
    }
    
   public function listSizes(){
        $repository = new SizeRepository();
        $list = $repository->getListSelectSizes();
        
        if(count($list)>1){$array = array(''=>'Seleccionar una opcion...');}
        if ($list) {               
            foreach ($list as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }
        return array(''=>'Seleccionar una opcion...');
    }
    
     public function listShapes(){
        $repository = new ShapeRepository();
        $list = $repository->getListSelectShapes();
        
        if(count($list)>1){$array = array(''=>'Seleccionar una opcion...');}
        if ($list) {               
            foreach ($list as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }
        return array(''=>'Seleccionar una opcion...');
    }
    
    public function populate($data) { 
        parent::populate($data);
    } 
}