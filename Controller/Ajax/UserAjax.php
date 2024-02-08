<?php

/**

 * Description of Ajax

 *

 * @author Uriel

 */

class UserAjax extends UserRepository
{



    public $flashmessenger = null;



    public function __construct()
    {

        if (!$this->flashmessenger instanceof FlashMessenger) {

            $this->flashmessenger = new FlashMessenger();

        }

    }



    public function getResponse($request, $options)
    {

        return $this->$request($options);

    }



    public function _getTranslation($text)
    {

        $translator = new Translator();

        return $translator->_getTranslation($text);

    }



    public function getTranslation($options)
    {

        $msj = $options['msj'];



        return array(

            'response' => true,

            'translation' => $this->_getTranslation($msj)

        );

    }



    public function getDataToEdit($options)
    {


        $colegioRepo = new UserRepository();

        $data = $colegioRepo->getById($options['id']);



        // $data['alta_payroll'] = $data['formated_alta_payroll'];

        // $data['baja_payroll'] = $data['formated_baja_payroll'];

        $data['action'] = 'edit';

        $data['password'] = '';

        $data['nip'] = '';



        //  if(is_null($data['store_id']) || $data['store_id'] == ''){

        //     $data['store_id'] = null;

        // }



        unset($data['photo'], $data['photo_type'], $data['photo_size'], $data['modificaciones']);

        $photo = $this->showPhoto($options['id']);



        return array(

            'response' => true,

            'userData' => $data,

            'photo' => $photo

        );

    }



    public function deleteUser($options)
    {

        $colegioRepo = new UserRepository();



        if (!$colegioRepo->isUsedInRecord($options['id'])) {

            if ($colegioRepo->delete($options['id'])) {

                $this->flashmessenger->addMessage(array('success' => 'El Usuario se elimino satisfactoriamente.'));

            }

        } else {

            $message = 'Este Usuario no puede ser eliminado, esta siendo utilizado en almenos un registro.';

            $this->flashmessenger->addMessage(array('info' => $message));

        }



        return array(

            'response' => true

        );

    }



    public function deletePhoto($options)
    {

        $this->_deletePhoto($options['user_id']);



        return array(

            'response' => true

        );



    }

}