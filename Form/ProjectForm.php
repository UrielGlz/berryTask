<?php

class ProjectForm extends Form {    

    public function __construct() {


        $this->setName('project');

        $this->setActionForm('Project.php');
       
        $this->setClass('form-horizontal');

        $this->setMethod('post');

        parent::__construct();

        $this->init();

    }



    public function init() {           
        
        $this->addElement(array(

            'type' => 'text',

            'name' => 'name',

            'label'=>'Nombre',            

            'required'=> true,

        ));

        $this->addElement(array(

            'type' => 'textarea',

            'name' => 'description',

            'label'=>'Descripción',

            'required'=>true

        ));  

        $this->addElement(
            array(

                'type' => 'select multiple',

                'name' => 'members',

                'label' => 'Miembros',

                'multiOptions' => $this->getListUsers(),

                'required' => true

            )
        );



        $attributes_wrapper_append_select = array('class'=>'select2-bootstrap-append');

        

        $attributes_wrapper_append_date = array('class'=>'date','id'=>'dateDatePicker');

        $append = "<span class = 'btn input-group-addon'><i class='fa fa-calendar'></i></span>";

        // $this->addElement(array(

        //     'type' => 'text',

        //     'name' => 'date',

        //     'label'=>'Fecha de pedido',

        //     'validators'=>array('date'),

        //     'required'=>true,

        //     'optionals'=>array('readOnly'=>'readOnly'),

        //     'value'=>date('m/d/Y'),

        //     'append'=>$append,

        //     'wrapper_attributes'=>$attributes_wrapper_append_date

        // ));  

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'date_start',

            'label'=>'Fecha de inicio',

            'validators'=>array('date'),

            'required'=>true,

            'append'=>$append,

            'wrapper_attributes'=>$attributes_wrapper_append_date

        ));  

        
        $this->addElement(array(

            'type' => 'text',

            'name' => 'date_end',

            'label'=>'Fecha de fin',

            'validators'=>array('date'),

            'required'=>true,

            'append'=>$append,

            'wrapper_attributes'=>$attributes_wrapper_append_date

        ));  

        $this->addElement(array(

            'type' => 'select',

            'name' => 'customer_id',

            'label'=>'Cliente',

            'multiOptions'=>$this->getListCustomer(),

            'value'=>'',

            'required'=>false

         ));           

         $this->addElement(array(

            'type' => 'hidden',

            'name' => 'uuid'

        ));   
        $this->addElement(array(

            'type' => 'hidden',

            'name' => 'customer_name'

        ));   
        
        $this->addElement(array(

            'type' => 'select',

            'name' => 'status',

            'label'=>'Status',

            // 'optionals'=>array('disabled'=>true),

            'multiOptions' => $this->getListStatus(),

            'required'=> false

        ));
        

        $this->addElement(array(

            'type'=>'submit',

            'name'=>'terminar',

            'value'=>$this->_getTranslation('Terminar'),

            'class'=>'btn btn-primary'

        ));        

    }
    public function getListStatus(){

        $repository = new ProjectRepository();

        $list = $repository->getListStatus();         

        $array = array('' => 'Seleccionar una opcion...');

        if($list){

            foreach($list as $key => $value){

                $array[$key] = $value;

            }

        }       

        return $array;            

    }
    public function getListCustomer(){
        $customer = new CustomerRepository();

        $result = $customer->getListSelectCustomers();
        
        $array = array('' => 'Seleccionar una opcion...');

        if ($result) {

            //$array = array();

            foreach ($result as $key => $value) {

                $array[$key] = $value;

            }
            //var_dump($array);exit;
            return $array;

        }
    }
    public function getListUsers(){
        $user = new UserRepository();

        $result = $user->getListSelectUsers();
        
        if ($result) {

            $array = array();

            foreach ($result as $key => $value) {

                $array[$key] = $value;

            }

            return $array;

        }

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

        if(isset($data['date_start'])){

            $data['date_start'] = $tools->setFormatDateToForm($data['date_start']);

            $data['date_end'] = $tools->setFormatDateToForm($data['date_end']);

        }

        if(isset($data['members']) && $data['members']!=null && is_array($data['members'])){

            $stores = array();

            foreach($data['members'] as $store){$stores[$store] = $store;}

            $data['members'] = $stores;

        }

        parent::populate($data);

    } 

}