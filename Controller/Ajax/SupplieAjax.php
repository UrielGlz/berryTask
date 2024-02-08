<?php
/**
 * Description of Ajax
 *
 * @author carlos
 */
class SupplieAjax extends SupplieRepository {
    
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
        $supplieRepo = new SupplieRepository();
        $data = $supplieRepo->getById($options['id']);
        $data['action'] = 'edit';
        
        return array(
            'response'=>true,
            'supplieData'=>$data
        );
    }
    
    public function deleteSupplie($options){
        $colegioRepo = new SupplieRepository();
        
        if($colegioRepo->delete($options['id'])){
            $this->flashmessenger->addMessage(array('success'=>'El insumo se elimino exitosamente.'));                
        }
        
        return array(
            'response'=>true
        );
    }

}