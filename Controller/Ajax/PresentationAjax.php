<?php
/**
 * Description of Ajax
 *
 * @author carlos
 */
class PresentationAjax extends PresentationRepository {
    
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
        $presentationRepo = new PresentationRepository();
        $data = $presentationRepo->getById($options['id']);
        $data['action'] = 'edit';
        
        return array(
            'response'=>true,
            'presentationData'=>$data
        );
    }
    
    public function deletePresentation($options){
        $presentationRepo = new PresentationRepository();
        
        if($presentationRepo->delete($options['id'])){
            $this->flashmessenger->addMessage(array('success'=>'Presentacion se elimino exitosamente.'));                
        }
        
        return array(
            'response'=>true
        );
    }
}