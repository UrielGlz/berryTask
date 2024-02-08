<?php

/**

 * Description of userForm

 *

 * @author Uriel

 */



class UserForm extends Form {



    public function __construct() {

        $this->setActionForm('User.php');

        $this->setName('user');

        $this->setClass('user');

        $this->setMethod('post');

        $this->setEnctype('multipart/form-data');

        parent::__construct();

        $this->init();

    }



    public function init() {

        $this->addElement(array(

            'type' => 'text',

            'name' => 'user',

            'label'=>'user',

            'required'=> true

        ));



        $this->addElement(array(

            'type' => 'password',

            'name' => 'password',

            'label'=>'Contraseña',

            'required'=> true

        ));

        

        $this->addElement(array(

            'type' => 'password',

            'name' => 'confirm_password',

            'label'=>'Confirmar contraseña',

            'required'=> true

        ));

        

        $this->addElement(array(

            'type' => 'password',

            'name' => 'nip',

            'label'=>'NIP',

            'required'=> false,

        ));

        

         $this->addElement(array(

            'type' => 'password',

            'name' => 'confirm_nip',

            'label'=>'Confirmar NIP',

            'required'=> false,

        ));

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'name',

            'label'=>'Nombre',

            'required'=> true

        ));

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'last_name',

            'label'=>'Apellido',

            'required'=> true

        ));

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'phone',

            'label'=>'Telefono',

            'validators'=>array('telefono'),

            'required'=> false

        ));

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'email',

            'label'=>'Email',

            'validators'=>array('email'),

            'required'=> false

        ));

        

        $this->addElement(array(

            'type' => 'select',

            'name' => 'role',

            'label'=>'Role',

            'multiOptions' => $this->getListRoles(),

            'required'=> true

        ));

        

        //  $this->addElement(array(

        //     'type' => 'select',

        //     'name' => 'area_bakery_production_id',

        //     'label'=>'Area',

        //     'multiOptions'=>$this->getListAreasProduccionPanaderia(),

        //     'required'=> true

        // ));

        

        $this->addElement(array(

            'type' => 'select',

            'name' => 'status',

            'label'=>'Estado',

            'multiOptions' => $this->getListStatus(),

            'required'=> true

        ));

        

        // $this->addElement(array(

        //     'type' => 'select multiple',

        //     'name' => 'store_id',

        //     'label'=>'Sucursal',

        //     'multiOptions'=>$this->getListStores(),

        //     'value'=>'',

        //     'required'=>true,

        // )); 

        

        /* Pudin */


        $this->addElement(array(

            'id'=> 'attachments',

            'type' => 'file',

            'name' => "photo[]",

            'label' => 'Foto',

            'class' => 'file upload',

            'required' => false,

            'optionals' => array(

                'title' => 'Foto',

                'data-show-preview'=>false,

                'data-show-upload'=>false

            ),

            'col-size-element'=>'8',

            'col-size-label'=>'4',

        ));     

        
        $this->addElement(array(

            'type' => 'text',

            'name' => 'initials',

            'label'=>'Iniciales',

            'required'=> true

        ));

        $this->addElement(array(

            'type' => 'text',

            'name' => 'color',

            'label'=>'Color',

            'class'=> 'my-colorpicker1 colorpicker-element',        

            'required'=> true,

        ));

    }

    

    public function getListRoles(){

        $repository = new UserRepository();

        $list = $repository->getListRoles();

         

        $array = array('' => 'Seleccionar una opcion...');

        

        $login = new Login();

        switch($login->getRole()){

            case '2':

                $listTemp = array(

                    '2'=>$list['2'],

                    //'5'=>$list['5'],

                );

                

                $list = $listTemp;

                break;

            

            case '6':

                $listTemp = array(

                    '2'=>$list['2'],

                    '5'=>$list['5'],

                    '6'=>$list['6'],

                );

                 

                $list = $listTemp;

                break;         

        }       

       

        foreach($list as $key => $value){

            $array[$key] = $value;

        }

        return $array;

    }
    public function getListStatus(){

        $repository = new UserRepository();

        $list = $repository->getListStatus();

         

        if($list){

            foreach($list as $key => $value){

                $array[$key] = $value;

            }

        }       

        return $array;            

    }

    public function populate($data){ 

        // if(isset($data['store_id']) && $data['store_id']!=null && is_array($data['store_id'])){

        //     $stores = array();

        //     foreach($data['store_id'] as $store){$stores[$store] = $store;}

        //     $data['store_id'] = $stores;

        // }



        parent::populate($data);

    }

    

    public function setEditForm(){

        $this->noRequired(array('password','confirm_password','nip','confirm_nip'));

    }

    

    public function isValid() {

        $rs = parent::isValid();

        

        if($rs){

            $flasmessenger = new FlashMessenger();

            $userRepo = new UserRepository();

            $existUser = $userRepo->existUserName($this->getValueElement('user'),$this->getId());            

            

            if($existUser){

                $flasmessenger->addMessage(array('danger'=>"Nombre de usuario ingresado ya existe. Intente nuevamente."));

                return null;

            }

            

            $contrasena = $this->getValueElement('password');

            $confirmar_contrasena = $this->getValueElement('confirm_password');

            

            if(strtolower($contrasena) !== strtolower($confirmar_contrasena)){

                $flasmessenger->addMessage(array('danger'=>'Contraseñas ingreadas no coinciden.'));

                return null;

            }

            

            $nip = $this->getValueElement('nip');

            $confirmar_nip = $this->getValueElement('confirm_nip');

            

            if(strtolower($nip) !== strtolower($confirmar_nip)){

                $flasmessenger->addMessage(array('danger'=>'NIP ingreados no coinciden.'));

                return null;

            }          

            

            $existNIP = $userRepo->existNIP($this->getValueElement('nip'),$this->getId());

            if($existNIP){

                $flasmessenger->addMessage(array('danger'=>"NIP ingresado ya existe. Intente nuevamente."));

                return null;

            }

            

            // if($this->getValueElement('role') == '4' && $this->getValueElement('area_bakery_production_id') == '0'){

            //     $this->setErrorToElement('X', 'area_bakery_production_id');

            //     $flasmessenger->addMessage(array('danger'=>'Todos los campos marcados con "X" son requeridos.'));

            //     return null;

            // }

        }

        

        return $rs;

    }

}