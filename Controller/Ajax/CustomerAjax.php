<?php
/**
 * Description of Ajax
 *
 * @author carlos
 */
class CustomerAjax extends CustomerRepository {
    
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
        $customerRepo = new CustomerRepository();
        $data = $customerRepo->getById($options['id']);
        $data['action'] = 'edit';
        
        return array(
            'response'=>true,
            'customerData'=>$data
        );
    }
    
    public function deleteCustomer($options){
        $customerRepo = new CustomerRepository();
        
        if($customerRepo->delete($options['id'])){
            $this->flashmessenger->addMessage(array('success'=>'Cliente se elimino exitosamente.'));                
        }
        
        return array(
            'response'=>true
        );
    }
}