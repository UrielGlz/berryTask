<?php
/**
 * Description of Ajax
 *
 * @author carlos
 */
class CategoryAjax extends CategoryRepository {
    
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
        $categoryRepo = new CategoryRepository();
        $data = $categoryRepo->getById($options['id']);
        $data['action'] = 'edit';
        
        return array(
            'response'=>true,
            'categoryData'=>$data
        );
    }
    
    public function deleteCategory($options){
        $categoryRepo = new CategoryRepository();
        
        if($categoryRepo->delete($options['id'])){
            $this->flashmessenger->addMessage(array('success'=>'Categoria se elimino exitosamente.'));                
        }
        
        return array(
            'response'=>true
        );
    }
}