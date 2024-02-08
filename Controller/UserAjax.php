<?php
/**
 * Description of Ajax
 *
 * @author carlos
 */
class UserAjax extends UserRepository {
    
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
        $colegioRepo = new UserRepository();
        $data = $colegioRepo->getById($options['id']);
        $data['action'] = 'edit';
        $data['password'] = '';
        $data['nip'] = '';
        
         if(is_null($data['store_id']) || $data['store_id'] == ''){
            $data['store_id'] = null;
        }
        
        return array(
            'response'=>true,
            'userData'=>$data
        );
    }
    
    public function deleteUser($options){
        $colegioRepo = new UserRepository();
        
        if(!$colegioRepo->isUsedInRecord($options['id'])){
            if($colegioRepo->delete($options['id'])){
                $this->flashmessenger->addMessage(array('success'=>'El Usuario se elimino satisfactoriamente.'));                
            }
        }else{
            $message = 'Este Usuario no puede ser eliminado, esta siendo utilizado en almenos un registro.';
            $this->flashmessenger->addMessage(array('danger'=>$message));
        }
        
        return array(
            'response'=>true
        );
    }

}