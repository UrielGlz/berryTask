<?php

/**

 * Description of Ajax

 *

 * @author Uriel

 */

class PrioritiesAjax extends PrioritiesRepository {

    

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

        $qualityRepo = new PrioritiesRepository();

        $data = $qualityRepo->getById($options['id']);

        $data['action'] = 'edit';

        

        return array(

            'response'=>true,

            'prioritiesData'=>$data

        );

    }


    public function deletePriorities($options){

        $PrioritiesRepo = new PrioritiesRepository();

        if(!$PrioritiesRepo->isUsedInRecord($options['id'])){
            if($PrioritiesRepo->delete($options['id'])){

                $this->flashmessenger->addMessage(array('success'=>'La prioridad se elimino exitosamente.'));                
    
            } 
        }else{
            $message = 'Oops !!, esta prioridad no puede ser eliminada, esta siendo utilizada en almenos un registro.';

            $this->flashmessenger->addMessage(array('info'=>$message));

        }               

        return array(

            'response'=>true

        );

    }

}