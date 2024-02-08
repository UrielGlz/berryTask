<?php
/**
 * Description of Ajax
 *
 * @author carlos
 */
class BrandAjax extends BrandRepository {
    
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
        $brandRepo = new BrandRepository();
        $data = $brandRepo->getById($options['id']);
        $data['action'] = 'edit';
        
        return array(
            'response'=>true,
            'brandData'=>$data
        );
    }
    
    public function deleteBrand($options){
        $brandRepo = new BrandRepository();
        
        if($brandRepo->delete($options['id'])){
            $this->flashmessenger->addMessage(array('success'=>'Marca se elimino exitosamente.'));                
        }
        
        return array(
            'response'=>true
        );
    }
}