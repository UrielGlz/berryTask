<?php

class SpecialOrderForm extends Form {    

    public function __construct() {

        $this->setName('special_order');

        $this->setClass('special_order');

        $this->setEnctype('multipart/form-data');

        $this->setActionForm('SpecialOrder.php');

        $this->setMethod('post');

        parent::__construct();

        $this->init();

    }



    public function init() {              

        $this->addElement(array(

            'type' => 'text',

            'name' => 'status'

        ));



        $login = new Login();

        $this->addElement(array(

            'type' => 'text',

            'name' => 'role_logued',

            'value'=>$login->getRole()

        ));

        

        $settings = new SettingsRepository();

        $this->addElement(array(

            'type'=>'hidden',

            'name'=>'idProductForSpecialDecorated',

            'value'=>$settings->_get('id_product_for_special_decorated')

        ));

        

        $this->addElement(array(

            'type'=>'hidden',

            'name'=>'id_shape_for_letter',

            'value'=>$settings->_get('id_shape_for_letter')

        ));

        

        $this->addElement(array(

            'type' => 'hidden',

            'name' => 'number_of_cake',

        ));

        

        /*Auxiliar solo para mostar comentario del pastel de vitrina*/

        $this->addElement(array(

            'type' => 'textarea',

            'label'=>'Comentarios',

            'name' => 'comments_cake',

            'optionals'=>array('style'=>'min-height:120px;max-height:120px','readOnly'=>'readOnly'),

            'required'=>false

        ));   

        

        $this->addElement(array(

            'type' => 'select',

            'name' => 'store_id',

            'label'=>'Sucursal',

            'multiOptions'=>$this->getListStores(),

            'required'=>true

         ));

        

        $attributes_wrapper_append_select = array('class'=>'select2-bootstrap-append');        

        $attributes_wrapper_append_date = array('id'=>'datePicker');

        $append = "<span class = 'btn input-group-addon'><i class='fa fa-calendar'></i></span>";

        $this->addElement(array(

            'type' => 'text',

            'name' => 'date',

            'label'=>'Fecha',

            'validators'=>array('date'),

            //'optionals'=>array('disabled'=>true), //Andres <- agreggue esta propiedad

            'optionals'=>array('readOnly'=>'readOnly'),

            'value'=>date('m/d/Y'),

            'required'=>true,

            'append'=>$append,

            'wrapper_attributes'=>$attributes_wrapper_append_date

        ));

        

        $this->addElement(array(

            'type' => 'select',

            'name' => 'type',

            'label'=>'Tipo',

            'multiOptions'=>array('Line'=>'Vitrina'),

            #'optionals'=>array('onclick'=>'alertClearDetails()','onchange'=>'clearSpecialRequisitionDetails();setUnsetCategoryField()'),

            'required'=>false

        )); 

        

        

        $attributes_wrapper_append_date = array();

        $append = "<span class = 'btn input-group-addon' id = 'deliveryDatePicker'><i class='fa fa-calendar'></i></span>";

        $this->addElement(array(

            'type' => 'text',

            'name' => 'delivery_date',

            'label'=>'Fecha entrega',

            'validators'=>array('datetime'),

            'optionals'=>array('readOnly'=>'readOnly'),

            'required'=>true,

            'append'=>$append,

            //'wrapper_attributes'=>$attributes_wrapper_append_date

        ));

        

        $append = "<span class = 'btn input-group-addon' data-action='insert' onclick='setModalCustomer(this)'><i class='fa fa-plus'></i></span>";

        $append .= "<span class = 'btn input-group-addon' data-action='edit' onclick='setModalCustomer(this)'><i class='fa fa-pencil'></i></span>";

         $this->addElement(array(

            'type' => 'select',

            'name' => 'customer',

            'label'=>'Cliente',

            'multiOptions'=>$this->getListCustomers(),

            'append'=>$append,

            'required'=>true

        ));  

         

        $this->addElement(array(

            'type' => 'text',

            'name' => 'phone',

            'label'=>'Telefono',

            'class'=>'_maskPhone',

            'optionals'=>array('maxlength'=>'14'),

            'required'=>true

        ));

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'email',

            'label'=>'Correo electronico',

            'required'=>false

        ));

        

         $this->addElement(array(

            'type' => 'select',

            'name' => 'home_service',

            'label'=>'Servicio a domicilio',

            'multiOptions'=>array('No'=>'No','Si'=>'Si'),

            'required'=>true,

            'col-size-label'=>'6',

            'col-size-element'=>'5',

        )); 

         

        $this->addElement(array(

            'type' => 'text',

            'name' => 'zipcode',

            'label'=>'Codigo postal',

            'required'=>false

        ));

         

        $this->addElement(array(

            'type' => 'text',

            'name' => 'address',

            'label'=>'Direccion',

            'required'=>false

        )); 

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'city',

            'label'=>'Ciudad',

            'required'=>false

        )); 

        

        $this->addElement(array(

            'type' => 'select',

            'name' => 'status_delivery',

            'label'=>'Status de entrega',

            'multiOptions'=>array('1'=>'Pendiente de entrega','2'=>'Entregada'),

            'required'=>true,

            'col-size-label'=>'6',

            'col-size-element'=>'5',

        )); 

        

        $this->addElement(array(

            'type' => 'select',

            'name' => 'status_payment',

            'label'=>'Pago',

            'multiOptions'=>array('1'=>'Pendiente','2'=>'Pagada'),

            'optionals'=>array('disabled'=>true),

            'required'=>true

        )); 

        

        $this->addElement(array(

            'type' => 'select',

            'name' => 'status_production',

            'label'=>'Produccion',

            'multiOptions'=>array('1'=>'Pendiente','2'=>'Terminada'),

            'optionals'=>array('disabled'=>true),

            'required'=>true

        ));  

        

        $this->addElement(array(

            'type' => 'textarea',

            'name' => 'comments',

            'col-size-element'=>'12',

            'required'=>false

        ));   

        

        $this->addElement(array(

            'type' => 'textarea',

            'name' => 'comments_1',

            'col-size-element'=>'12',

            'required'=>false

        ));   

        

        $this->addElement(array(

            'type' => 'file',

            'name' => "image[]",

            'class' => 'upload imagesInput',

            'required' => false,

            'col-size-element'=>'12',

            'optionals' => array(

                'multiple'=>''

            ),

        ));

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'special_quantity',

            'label'=>'Cantidad de especiales',

            'required'=> true,

            'validators'=>array('double'),

            'col-size-label'=>'6',

            'col-size-element'=>'5',

        ));             

         

         $this->addElement(array(

            'type' => 'hidden',

            'name' => 'idDetailTemp',

            'required'=>false

        ));  

         /*Num requisicion con prefijo*/

        $this->addElement(array(

            'type' => 'hidden',

            'name' => 'req_number',

            'required'=>false

        )); 

        

        $this->addElement(array(

            'type' => 'select',

            'name' => 'size',

            'label'=>'Tamaño',

            'multiOptions'=>$this->listSizes(),

            'required'=>false

        ));

        

         $this->addElement(array(

            'type' => 'select',

            'name' => 'shape',

            'label'=>'Forma',

            'multiOptions'=>array(''=>'Seleccionar una opcion...'),

            'required'=>false

        ));

         

        $this->addElement(array(

            'type' => 'select',

            'name' => 'category',

            'label'=>'Categoria',

            'multiOptions'=>$this->listCategories('Parts of the cake')

        ));

         

         /*When type = 'specia' THEN key = id_slice  value = flavorName*/

         $append = "<span class = 'btn input-group-addon' data-toggle='modal' data-target='#modalAgregarProducto'><i class='fa fa-plus'></i></span>";

         $this->addElement(array(

            'type' => 'select',

            'name' => 'product',

            'label'=>'Producto',

            'multiOptions'=>array(''=>'Seleccionar una opcion...'),

            'append'=>$append



         ));

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'quantity',

            'label'=>'Cantidad',

            'required'=>false

        ));

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'price',

            'label'=>'Precio',

            'required'=>false,

            'optionals'=>array('readOnly'=>'readonly')

        ));



        /*

        $this->addElement(array(

            'type' => 'text',

            'name' => 'multiple',

            'label'=>'PE #',

            'required'=>false,

        ));*/

        

        $this->addElement(array(

            'type' => 'hidden',

            'name' => 'multiple',

            'value'=>'1',

            'required'=>false

        ));

        

        $this->addElement(array(

            'type' => 'hidden',

            'name' => 'ammount',

            'value'=>'0',

            'required'=>true,

        ));

        

        $this->addElement(array(

            'type' => 'hidden',

            'name' => 'ammount_payments',

            'value'=>'0',

            'required'=>true,

        ));

        

        $this->addElement(array(

            'type'=>'button',

            'name'=>'buscar',

            'value'=>$this->_getTranslation('Agregar'),

            'class'=>'btn btn-primary',

            'optionals'=>array('onClick'=>'setSpecialOrderDetails()')

        ));

        

        

        $this->addElement(array(

            'type'=>'button',

            'name'=>'addSliceWizard',

            'value'=>$this->_getTranslation('Agregar'),

            'class'=>'btn btn-primary',

            'optionals'=>array('onClick'=>'setSpecialOrderDetailsWizard()')

        ));

        

        $this->addElement(array(

            'type'=>'button',

            'name'=>'cerrar_modal',

            'value'=>$this->_getTranslation('Cerrar'),

            'class'=>'btn btn-default _closeModalAddSliceToSpecialOrder _closeModalAddSliceToSpecialOrderWizard _closeModalAddExtraToSpecialOrder'

        ));

        

        $this->addElement(array(

            'type'=>'button',

            'name'=>'agregar_detalle',

            'value'=>$this->_getTranslation('Agregar pastel de vitrina'),

            'class'=>'btn btn-default'

        ));

        

        $this->addElement(array(

            'type'=>'button',

            'name'=>'agregar_extra',

            'value'=>$this->_getTranslation('Agregar extra a pastel'),

            'class'=>'btn btn-default'

        ));

        

        $this->addElement(array(

            'type'=>'button',

            'name'=>'agregar_detalle_wizard',

            'value'=>$this->_getTranslation('Agregar pastel especial'),

            'class'=>'btn btn-default'

        ));

        

        $this->addElement(array(

            'type'=>'button',

            'name'=>'terminar',

            'value'=>$this->_getTranslation('Terminar'),

            'class'=>'btn btn-primary',

            'optionals'=>array("onClick"=>"submit('special_order')")

        ));   

        

        $this->addElement(array(

            'type' => 'hidden',

            'name' => 'antiguedad',

        ));

        

        $this->addElement(array(

            'type' => 'hidden',

            'name' => 'allow_edit',

        ));

        

        $this->addElement(array(

            'type'=>'button',

            'name'=>'btn_allow_edit',

            'value'=>$this->_getTranslation('Permiso para editar'),

            'class'=>'btn btn-primary'

        ));

    }

    

    public function getListCustomers(){

        $repository = new CustomerRepository();

        $result = $repository->getListSelectCustomers();



        $array = array('' => 'Seleccionar una opcion...');

        if ($result) {           

            foreach ($result as $key => $value) {

                $array[$key] = $value;

            }

        }

        return $array;

    }

    

    public function listCategories($type = null){

        $repository = new CategoryRepository();

        $result = $repository->getListSelectCategories($type);

        

        $array = array('' => 'Seleccionar una opcion...');

        if ($result) {

            foreach ($result as $key => $value) {

                $array[$key] = $value;

            }

            return $array;

        }

    }

    

    public function getListProducts(){

        $repository = new SliceRepository();

        #1 = status activo

        $productos = $repository->getListSelectSlices();

        

        $list = array(''=>'Seleccionar una opcion...');

        foreach ($productos as $key => $value) {

            $list[$key] = $value;

        }

        return $list;

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

    

    public function listShapes(){

        $repository = new ShapeRepository();

        $result = $repository->getListSelectShapes();

        

        $array = array('' => 'Seleccionar una opcion...');

        if ($result) {

            foreach ($result as $key => $value) {

                $array[$key] = $value;

            }

        }

         return $array;

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

    

    public function setIdShapeForNumbers(){

        $settings = new SettingsRepository();

        $id_shape_for_number = $settings->_get('id_shape_for_number');     

        $id_shape_for_numberArray = explode(",", $id_shape_for_number);

        

        foreach($id_shape_for_numberArray as $key => $value){

            $value = explode("|", $value);

            $this->addElement(array(

                'type'=>'hidden',

                'name'=>'idShapeForNumber_'.$value[0],

                'value'=>$value[1]

            ));      

            

            $this->showElement('idShapeForNumber_'.$value[0]);

        }      

    }    

    

    public function populate($data) { 

        $tools = new Tools();

        if(isset($data['date']) && $tools->isValidaDateYYYMMDD($data['date'])){

            $data['date'] = $tools->setFormatDateToForm($data['date']);

        }

        

        if(isset($data['delivery_date']) && substr_count($data['delivery_date'], '-') > 0){

           $data['delivery_date'] = $tools->setFormatDateTimeToForm($data['delivery_date']);

        }

        

        parent::populate($data);

    } 

}