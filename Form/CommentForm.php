<?php

class CommentForm extends Form {    

    public function __construct() {


        $this->setName('comment');

        $this->setActionForm('Comment.php');
       
        $this->setClass('form-horizontal');

        $this->setMethod('post');

        parent::__construct();

        $this->init();

    }



    public function init() {           
       
        

        $this->addElement(array(

            'type' => 'textarea',

            'name' => 'comment',

            'label'=>'Comentario',            

            'required'=> true,

        ));

        

        $this->addElement(array(

            'type'=>'submit',

            'name'=>'terminar',           

            'value'=>$this->_getTranslation('Terminar'),

            'class'=>'btn btn-primary'

        ));        

    }




    

    public function populate($data) {            

        parent::populate($data);

    } 

}