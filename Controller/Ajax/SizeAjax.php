<?php
/**
 * Description of Ajax
 *
 * @author carlos
 */
class SizeAjax extends SizeRepository {
    
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
        $sizeRepo = new SizeRepository();
        $data = $sizeRepo->getById($options['id']);
        $data['action'] = 'edit';
        
        return array(
            'response'=>true,
            'sizeData'=>$data
        );
    }
    
    public function deleteSize($options){
        $sizeRepo = new SizeRepository();
        
        if($sizeRepo->delete($options['id'])){
            $this->flashmessenger->addMessage(array('success'=>'Tamaño se elimino exitosamente.'));                
        }
        
        return array(
            'response'=>true
        );
    }
}