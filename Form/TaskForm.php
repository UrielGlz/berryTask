<?php

class TaskForm extends Form {    

    public function __construct() {


        $this->setName('task');
        $this->setDefaultFormLabelsColSize('4');

        $this->setDefaultFormElementsColSize('7');
        $this->setActionForm('Task.php');
       
       // $this->setClass('form-horizontal');
       $this->setEnctype('multipart/form-data');

        $this->setMethod('post');

        parent::__construct();

        $this->init();

    }



    public function init() {           
        

        $this->addElement(array(

            'type' => 'hidden',

            'name' => 'parent_task_id'

        ));

        $this->addElement(array(

            'type' => 'hidden',

            'name' => 'task_id'

        ));

        $this->addElement(array(

            'type' => 'hidden',

            'name' => 'project_id'

        )); 
        
        $this->addElement(array(

            'type' => 'hidden',

            'name' => 'uuid'

        )); 

        $this->addElement(array(

            'type' => 'hidden',

            'name' => 'customer_id'

        ));   
        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'task_name',

            'label'=>'Nombre de tarea',            

            'required'=> false,

        ));

        $this->addElement(array(

            'type' => 'textarea',

            'name' => 'description',

            'optionals'=>array('style'=>'min-height:140px;max-height:140px'),
            'col-size-element'=>'12',


            'label'=>'Descripción',

            'required'=>false

        ));
        
       

        $attributes_wrapper_append_select = array('class'=>'select2-bootstrap-append');

        

        $attributes_wrapper_append_date = array('class'=>'date','id'=>'dateDatePicker');

        $append = "<span class = 'btn input-group-addon'><i class='fa fa-calendar'></i></span>";


        $this->addElement(array(

            'type' => 'text',

            'name' => 'due_date',

            'label'=>'Fecha de entrega',

            'validators'=>array('date'),

            'required'=>false,

            'append'=>$append,

             //'col-size-label'=>'4',

            // 'col-size-element'=>'4',

            'wrapper_attributes'=>$attributes_wrapper_append_date

        ));  

        $this->addElement(array(

            'type' => 'text',

            'name' => 'due_time',

            'label'=>'Hora',

            'validators'=>array('time'),

            'required'=>false,

            'append'=>$append,

            'wrapper_attributes'=>$attributes_wrapper_append_date

        )); 

        $this->addElement(array(

            'type' => 'select',

            'name' => 'responsable',

            'label'=>'Responsable',

            'multiOptions'=>$this->getListUsers(),

            'required'=>false

         ));

         

         $this->addElement(array(

            'type' => 'select',

            'name' => 'category_id',

            'label'=>'Categoria',

            'multiOptions'=>$this->getListCategoryTask(),

            'required'=>false

         ));

         $this->addElement(array(

            'type' => 'select',

            'name' => 'prioritie_id',

            'label'=>'Prioridad',

            // 'col-size-label'=>'3',

            // 'col-size-element'=>'3',

            'multiOptions'=>$this->getListPrioritie(),

            'required'=>false

         ));

         $this->addElement(array(
            'type' => 'hidden',
            'name' => 'date',
            //'label'=>'Fecha',
            //'required'=> true
        ));   
    

       
        //  $this->addElement(array(

        //     'type' => 'hidden',

        //     'name' => 'status'

        // ));   

        $this->addElement(array(

            'type' => 'select',

            'name' => 'status',

            'label'=>'Status',

            // 'optionals'=>array('disabled'=>true),

            'multiOptions' => $this->getListStatus(),

            'required'=> false

        ));
        

        $this->addElement(array(

            'type'=>'button',

            'name'=>'terminar',           

            'value'=>$this->_getTranslation('Terminar'),

            //'optionals'=>array("onClick"=>"submit('return')"),

            'class'=>'btn btn-primary'

        ));        
        

    }

    public function getListStatus(){

        $repository = new TaskRepository();

        $list = $repository->getListStatus();         

        $array = array('' => 'Estado');

        if($list){

            foreach($list as $key => $value){

                $array[$key] = $value;

            }

        }       

        return $array;            

    } 
    public function getListCategoryTask(){
        $categoryTask = new CategoryTaskRepository();

        $result = $categoryTask->getListSelectCategoryTask();
        
        $array = array('' => 'Categoria');

        if ($result) {

            //$array = array();

            foreach ($result as $key => $value) {

                $array[$key] = $value;

            }
            //var_dump($array);exit;
            return $array;

        }
    }

    public function getListPrioritie(){
        $categoryTask = new PrioritiesRepository();

        $result = $categoryTask->getListSelectPriorities();
        
        $array = array('' => 'Prioridad');

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

        $array = array(''=>'Responsable');
        
        if ($result) {
        
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

    public function getListCustomer(){
        $customer = new CustomerRepository();

        $result = $customer->getListSelectCustomers();
        
        $array = array('' => 'Cliente');

        if ($result) {

            //$array = array();

            foreach ($result as $key => $value) {

                $array[$key] = $value;

            }
            //var_dump($array);exit;
            return $array;

        }
    }


    

    public function populate($data) { 


       
        $tools = new Tools();

        if(isset($data['due_date'])){

            $data['due_date'] = $tools->setFormatDateToForm($data['due_date']);

           // $data['date_end'] = $tools->setFormatDateToForm($data['date_end']);

        }

        parent::populate($data);

    } 

}