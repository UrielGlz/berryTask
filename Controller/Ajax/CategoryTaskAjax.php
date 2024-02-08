<?php

/**

 * Description of Ajax

 *

 * @author Uriel

 */

class CategoryTaskAjax extends CategoryTaskRepository {

    

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

        $qualityRepo = new CategoryTaskRepository();

        $data = $qualityRepo->getById($options['id']);

        $data['action'] = 'edit';

        

        return array(

            'response'=>true,

            'CategoryTaskData'=>$data

        );

    }


    public function deleteCategoryTask($options){

        $CategoryTask = new CategoryTaskRepository();

        if(!$CategoryTask->isUsedInRecord($options['id'])){
            if($CategoryTask->delete($options['id'])){

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