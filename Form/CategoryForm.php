<?php

class CategoryForm extends Form {



    public function __construct() {

        $this->setActionForm('Category.php');

        $this->setName('category');

        $this->setClass('category');

        $this->setMethod('post');

        $this->setDefaultFormLabelsColSize(4);

        $this->setDefaultFormElementsColSize(8);

        parent::__construct();

        $this->init();

    }



    public function init() {      

        $login = new Login();       

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'description',

            'label'=>'Categoria',

            'required'=> true

        ));

        

        $this->addElement(array(

            'type' => 'select',

            'name' => 'type',

            'label'=>'Tipo',

            'multiOptions' => array('product'=>'Producto','supplie'=>'Insumo','expense'=>'Gasto','parts of the cake'=>'Parte del pastel'),

            'required'=> true

        ));

        

        $this->addElement(array(

            'type' => 'select',

            'name' => 'status',

            'label'=>'Status',

            'multiOptions' => $this->getListStatus(),

            'required'=> true

        ));

    }

    

    public function getListStatus(){

        $repository = new CategoryRepository();

        $list = $repository->getListStatus();

         

        $array = array();

        if($list){

            foreach($list as $key => $value){

                $array[$key] = $value;

            }

        }       

        return $array;            

    }    

}

