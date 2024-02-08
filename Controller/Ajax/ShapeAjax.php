<?php
/**
 * Description of Ajax
 *
 * @author carlos
 */
class ShapeAjax extends ShapeRepository {
    
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
        $shapeRepo = new ShapeRepository();
        $data = $shapeRepo->getById($options['id']);
        $data['action'] = 'edit';
        
        return array(
            'response'=>true,
            'shapeData'=>$data
        );
    }
    
    public function deleteShape($options){
        $shapeRepo = new ShapeRepository();
        
        if($shapeRepo->delete($options['id'])){
            $this->flashmessenger->addMessage(array('success'=>'Forma se elimino exitosamente.'));                
        }
        
        return array(
            'response'=>true
        );
    }
}