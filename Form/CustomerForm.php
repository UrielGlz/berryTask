<?php

class CustomerForm extends Form
{



    public function __construct()
    {

        $this->setActionForm('Customer.php');

        $this->setName('customer');

        $this->setClass('customer');

        $this->setMethod('post');

        $this->setDefaultFormLabelsColSize(4);

        $this->setDefaultFormElementsColSize(8);

        parent::__construct();

        $this->init();

    }



    public function init()
    {

        $login = new Login();



        $this->addElement(
            array(

                'type' => 'text',

                'name' => 'name',

                'label' => 'Nombre',

                'required' => true

            )
        );



        $this->addElement(
            array(

                'type' => 'text',

                'name' => 'address',

                'label' => 'Direccion',

                'required' => false

            )
        );

        $this->addElement(
            array(

                'type' => 'text',

                'name' => 'phone',

                'label' => 'Telefono',

                'required' => true

            )
        );

        $this->addElement(
            array(

                'type' => 'text',

                'name' => 'email1',

                'label' => 'Email',

                'required' => false

            )
        );

        $this->addElement(
            array(

                'type' => 'text',

                'name' => 'email2',

                'label' => 'Email 2',

                'required' => false

            )
        );

        $this->addElement(
            array(

                'type' => 'text',

                'name' => 'email3',

                'label' => 'Email 3',

                'required' => false

            )
        );

        $this->addElement(
            array(

                'type' => 'text',

                'name' => 'email4',

                'label' => 'Email 4',

                'required' => false

            )
        );

        $this->addElement(
            array(

                'type' => 'text',

                'name' => 'contact',

                'label' => 'Contacto',

                'required' => true

            )
        );    

        $this->addElement(
            array(

                'type' => 'select',

                'name' => 'status',

                'label' => 'Status',

                'multiOptions' => $this->getListStatus(),

                'required' => true

            )
        );

        $this->addElement(
            array(

                'type' => 'button',

                'name' => 'send',

                'value' => $this->_getTranslation('Terminar'),

                'optionals' => array(

                    'onclick' => "submit()"
                ),

                'class' => 'btn btn-primary'

            )
        );



        $this->addElement(
            array(

                'type' => 'button',

                'name' => 'cancelar',

                'value' => $this->_getTranslation('Cancelar'),

                'optionals' => array(

                    'onclick' => "document.location = '" . ROOT_HOST . "/Controller/Customer.php'"
                ),

                'class' => 'btn btn-danger'

            )
        );

    }



    public function getListStatus()
    {

        $repository = new CustomerRepository();

        $list = $repository->getListStatus();



        $array = array();

        if ($list) {

            foreach ($list as $key => $value) {

                $array[$key] = $value;

            }

        }

        return $array;

    }

    public function populate($data)
    {

        parent::populate($data);

    }

}