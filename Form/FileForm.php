<?php

class FileForm extends Form {    

    public function __construct() {

        $this->setName('file');

        $this->setActionForm('File.php');
       
        $this->setClass('form-horizontal');

        $this->setEnctype('multipart/form-data');


        $this->setMethod('post');

        parent::__construct();

        $this->init();

    }

    public function init() {           
        
        $this->addElement(array(

            'type' => 'hidden',

            'name' => 'task_id'

        ));             
        $this->addElement(array(

            'type' => 'hidden',

            'name' => 'project_id'

        ));          

        $this->addElement(array(

            'type' => 'text',

            'name' => 'name',

            'label'=>'Nombre del archivo',            

            'required'=> true,

        ));

        $this->addElement(array(

            'type' => 'select',

            'name' => 'id_category_file',

            'label'=>'Categoria de archivo',

            'multiOptions' => $this->getListCategoryFile(),

            'required'=> true

        )); 
          
          
         $attributes_wrapper_append_date = array('class'=>'date','id'=>'dateDatePicker');

         $append = "<span class = 'btn input-group-addon'><i class='fa fa-calendar'></i></span>";
 
 
         $this->addElement(array(
 
             'type' => 'text',
 
             'name' => 'expiration_date',
 
             'label'=>'Fecha de Expiración',
 
             'validators'=>array('date'),
 
             'required'=>false,
 
             'append'=>$append,
 
             'wrapper_attributes'=>$attributes_wrapper_append_date
 
         )); 

        $this->addElement(array(

            'type'=>'button',

            'name'=>'terminar',           

            'value'=>$this->_getTranslation('Terminar'),

            'class'=>'btn btn-primary',

            'optionals'=>array("onClick"=>"submit('file')")

        ));        
        
        $this->addElement(array(
            
            //'id' => 'attachement_file',

            'type' => 'file',

            'name' => "attachement_file",

            'class' => 'upload imagesInput',

            'required' => true,

            'col-size-element'=>'10',

            'optionals' => array(

                'multiple'=>''

            ),

        ));
    }

    public function getListCategoryFile(){

        $repository = new CategoryFilesRepository();

        $list = $repository->getListCategoryFiles();         

        $array = array('' => 'Seleccionar una opcion...');


        if($list){

            foreach($list as $key => $value){

                $array[$key] = $value;

            }

        }       
      //  echo "<pre>";var_dump($array);echo "</pre>";exit;
        return $array;            

    } 
    

    public function populate($data) { 

        $tools = new Tools();

        if(isset($data['expiration_date'])){

            $data['expiration_date'] = $tools->setFormatDateToForm($data['expiration_date']);         
        }

        parent::populate($data);

    } 

}