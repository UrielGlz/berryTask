<?php

class BrandForm extends Form {



    public function __construct() {

        $this->setActionForm('Brand.php');

        $this->setName('brand');

        $this->setClass('brand');

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

            'label'=>'Marca',

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

        $repository = new BrandRepository();

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

