<?php
/**
 * Description of Ajax
 *
 * @author carlos
 */
class StoreAjax extends StoreRepository {
    
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
        $storeRepo = new StoreRepository();
        $data = $storeRepo->getById($options['id']);
        $data['action'] = 'edit';
        
        
        $locationsRepo = new LocationRepository();
        $listLocations = $locationsRepo->getListSelectLocationsByStoreId($options['id']);
        
        $list = "<option value=''>".$this->_getTranslation('Seleccionar una opcion...')."</option>";
        if($listLocations){
            foreach($listLocations as $key => $value){
                $selected = "";
                if($key == $data['default_location']){$selected = "selected";}
                $list .= "<option value='$key' $selected >$value</option>";
            }
        }
        
        return array(
            'response'=>true,
            'storeData'=>$data,
            'locations'=>$list
        );
    }
    
    public function deleteStore($options){
        $colegioRepo = new StoreRepository();
        
        if($colegioRepo->delete($options['id'])){
            $this->flashmessenger->addMessage(array('success'=>'La Sucursal se elimino exitosamente.'));                
        }
        
        return array(
            'response'=>true
        );
    }

}