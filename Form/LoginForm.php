<?php
/**
 * Description of LoginForm
 *
 * @author carlos augusto vazquez lara
 */
class LoginForm extends Form {
    public function __construct() {
        $this->setActionForm('index.php');
        $this->setMethod('post');
        $this->setClass('form-signin');
        $this->init();
        parent::__construct();
    }

    public function init() {
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'usuario',
            'optionals'=>array('placeholder'=>'usuario'),
            'col-size-element'=>'12',
            'required'=>true
        ));
        
        $this->addElement(array(
            'type' => 'password',
            'name' => 'contrasena',
            'optionals'=>array('placeholder'=>'contraseña'),
            'col-size-element'=>'12',
            'required'=>true
        ));
        
        $this->addElement(array(
            'type'=>'submit',
            'name'=>'login',
            'value'=>'Entrar',
            'col-size-element'=>'12',
            'class'=>'btn btn-lg btn-primary btn-block btn-signin'
        ));
    }
}