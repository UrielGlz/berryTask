<?php

/**

 * Description of Ajax

 *

 * @author Uriel

 */

class CategoryFilesAjax extends CategoryFilesRepository {

    

    public $flashmessenger = null;

    

    public function __construct() {

        if(!$this->flashmessenger instanceof FlashMessenger){

            $this->flashmessenger = new FlashMessenger();

        }

    }

    

    public function getResponse($request, $options) {

        return $this->$request($options);

    }

    

    public function _getTranslation($text){

        $translator = new Translator();

        return $translator->_getTranslation($text);

    }

    

    public function getTranslation($options){

        $msj = $options['msj'];

        

        return array(

            'response'=>true,

            'translation'=>$this->_getTranslation($msj)

                );

    }    

    

    public function getDataToEdit($options){

        $qualityRepo = new CategoryFilesRepository();

        $data = $qualityRepo->getById($options['id']);

        $data['action'] = 'edit';

        

        return array(

            'response'=>true,

            'CategoryFilesData'=>$data

        );

    }


    public function deleteCategoryFiles($options){

        $CategoryFiles = new CategoryFilesRepository();

        if(!$CategoryFiles->isUsedInRecord($options['id'])){
            if($CategoryFiles->delete($options['id'])){

                $this->flashmessenger->addMessage(array('success'=>'La Categoria se elimino exitosamente.'));                
    
            } 
        }else{
            $message = 'Oops !!, esta categoria no puede ser eliminada, esta siendo utilizada en almenos un registro.';

            $this->flashmessenger->addMessage(array('info'=>$message));

        }               

        return array(

            'response'=>true

        );

    }

}