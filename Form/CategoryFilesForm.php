<?php

/**

 * Description of NivelForm

 *

 * @author  Uriel

 */

class CategoryFilesForm extends Form {

    public function __construct() {

        $this->setActionForm('CategoryFiles.php');

        $this->setName('CategoryFiles');

        $this->setClass('CategoryFiles');

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

            'type' => 'text',

            'name' => 'color',

            'label'=>'Color',

            'class'=> 'my-colorpicker1 colorpicker-element',        

            'required'=> true,

        ));

    }

    

    public function populate($data){

        parent::populate($data);

    }

}