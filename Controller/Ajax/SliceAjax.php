<?php
/**
 * Description of Ajax
 *
 * @author carlos
 */
class SliceAjax extends SliceRepository {
    
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
        $sliceRepo = new SliceRepository();
        $data = $sliceRepo->getById($options['id']);
        $data['action'] = 'edit';
        
        return array(
            'response'=>true,
            'sliceData'=>$data
        );
    }
    
    public function deleteSlice($options){
        $sliceRepo = new SliceRepository();
        
        if($sliceRepo->delete($options['id'])){
            $this->flashmessenger->addMessage(array('success'=>'Parte del pastel se elimino exitosamente.'));                
        }
        
        return array(
            'response'=>true
        );
    }
}