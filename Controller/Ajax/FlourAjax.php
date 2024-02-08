<?php
/**
 * Description of Ajax
 *
 * @author carlos
 */
class FlourAjax extends FlourRepository {
    
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
        $flourRepo = new FlourRepository();
        $data = $flourRepo->getById($options['id']);
        $data['action'] = 'edit';
        
        return array(
            'response'=>true,
            'flourData'=>$data
        );
    }
    
    public function deleteFlour($options){
        $flourRepo = new FlourRepository();
        
        if($flourRepo->delete($options['id'])){
            $this->flashmessenger->addMessage(array('success'=>'Harina se elimino exitosamente.'));                
        }
        
        return array(
            'response'=>true
        );
    }
}