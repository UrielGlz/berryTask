<?php
/**
 * Description of Ajax
 *
 * @author carlos
 */
class UMAjax extends UMRepository {
    
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
        $umRepo = new UMRepository();
        $data = $umRepo->getById($options['id']);
        $data['action'] = 'edit';
        
        return array(
            'response'=>true,
            'umData'=>$data
        );
    }
    
    public function deleteUM($options){
        $umRepo = new UMRepository();
        
        if($umRepo->delete($options['id'])){
            $this->flashmessenger->addMessage(array('success'=>'Unidad de medida se elimino exitosamente.'));                
        }
        
        return array(
            'response'=>true
        );
    }
}