<?php

class StoreRequestForm extends Form {    

    public function __construct() {

        $this->setName('storeRequestForm');

        $this->setActionForm('StoreRequest.php');

        $this->setClass('form-horizontal');

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

            'label'=>'Fecha de pedido',

            'validators'=>array('date'),

            'required'=>true,

            'optionals'=>array('readOnly'=>'readOnly'),

            'value'=>date('m/d/Y'),

            'append'=>$append,

            'wrapper_attributes'=>$attributes_wrapper_append_date

        ));  

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'delivery_date',

            'label'=>'Fecha de entrega',

            'validators'=>array('date'),

            'required'=>true,

            'append'=>$append,

            'wrapper_attributes'=>$attributes_wrapper_append_date

        ));  

        

        $this->addElement(array(

            'type' => 'select',

            'name' => 'store_id',

            'label'=>'Sucursal',

            'multiOptions'=>$this->getListStores(),

            'required'=>true

         ));

        

        $this->addElement(array(

            'type' => 'select',

            'name' => 'area_id',

            'label'=>'Area',

            'multiOptions'=>$this->getListAreas(),

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

            'type'=>'button',

            'name'=>'terminar',

            'value'=>$this->_getTranslation('Terminar'),

            'class'=>'btn btn-primary'

        ));        

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

    

    public function getListAreas(){

        $repo = new AreaRepository();

        $result = $repo->getListSelectAreas();

        $array = array(''=>'Seleccionar una opcion...');

        if ($result) {               

            foreach ($result as $key => $value) {

                $array[$key] = $value;

            }

        }   

        

        return $array;

    }

    

    public function populate($data) { 

        $tools = new Tools();

        if(isset($data['date'])){

            $data['date'] = $tools->setFormatDateToForm($data['date']);

            $data['delivery_date'] = $tools->setFormatDateToForm($data['delivery_date']);

        }

        parent::populate($data);

    } 

}