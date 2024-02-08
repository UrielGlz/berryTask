<?php
/**
 * Description of Ajax
 *
 * @author carlos
 */
class VendorAjax extends VendorRepository {
    
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
        $vendorRepo = new VendorRepository();
        $data = $vendorRepo->getById($options['id']);
        $data['action'] = 'edit';
        
        return array(
            'response'=>true,
            'vendorData'=>$data
        );
    }
    
    public function deleteVendor($options){
        $vendorRepo = new VendorRepository();
        
        if($vendorRepo->delete($options['id'])){
            $this->flashmessenger->addMessage(array('success'=>'Proveedor se elimino exitosamente.'));                
        }
        
        return array(
            'response'=>true
        );
    }
}