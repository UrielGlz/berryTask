<?php
/**
 * Description of Ajax
 *
 * @author carlos
 */
class ProductAjax extends ProductRepository {
    
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
        $supplieRepo = new ProductRepository();
        $data = $supplieRepo->getById($options['id']);
        $data['action'] = 'edit';
        
        return array(
            'response'=>true,
            'productData'=>$data
        );
    }
    
    public function deleteProduct($options){
        $colegioRepo = new ProductRepository();
        
        if($colegioRepo->delete($options['id'])){
            $this->flashmessenger->addMessage(array('success'=>'Producto se elimino exitosamente.'));                
        }
        
        return array(
            'response'=>true
        );
    }
    
     public function getListaProducts($options){
        $repository = new ProductRepository();
        $item = $options['item'];
        
        $items = $repository->getProductsLike($item);
        
        if($items){
            $array = array();
            foreach($items as $item){
                $array[] = array(
                   'value'=>$item['id'],
                   'label'=>$item['description']." (".$item['code'].")"
                );
            }
        }
        return array(
            'response'=>true,
            'productos'=>$array
        );   
    }

}