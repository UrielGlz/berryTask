<?php
class LocationRepository extends EntityRepository {

    private $table = 'product_locations';
    private $flashmessenger = null;    
    private $options = array(
        'description'=>null,       
        'store_id'=>null,       
        'status'=>null
    );
    
    public function __construct() {
        if(!$this->flashmessenger instanceof FlashMessenger){
            $this->flashmessenger = new FlashMessenger();
        }
    }
    
    public function _getTranslation($text){
        $translator = new Translator();
        return $translator->_getTranslation($text);
    }
    
    public function setOptions($data){
      foreach ($this->options as $option => $value){
          if(isset($data[$option])){
            $this->options[$option] = $data[$option];
          }
      }
    }
  
    public function getOptions(){
        return $this->options;
    }
    
    public function getTable(){
        return $this->table;
    }

    public function save(array $data, $table = null) {        
        if(is_null($data['status'])){$data['status'] = '1';}      

        $rs = parent::save($data, $this->table);
        
        if(!$rs){
            return null;
        }
        return true;        
    }
    
    public function delete($id, $table = null) {
        return parent::delete($id, $this->table);
    }
    
    public function update($id, $data, $table = null) {           
        return parent::update($id, $data, $this->table);        
    }

    public function getById($id, $table = null,$selectAux = null) {
        $query = "SELECT *,fxGetStatusName(status,'Location')as status_name FROM $this->table WHERE id = '$id'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result)[0];
        }
        return null;
    }

    public function isUsedInRecord($id, array $buscarEn = null,$andWhere = null) {
        $query = "SELECT id FROM products WHERE location = '$id'";
        $result = $this->query($query);
        if($result->num_rows > 0){
            return true;
        }
        
        return null;
    }
    
    public function getListSelectLocations($idsLocations = null,$storeId = null){    
        /*$idLocations lo envio desde receivingAjax, y son las locaciones asignadas al producto que se esta recibiendo*/
        if($idsLocations !== null){ $idsLocations = " AND find_in_set(id,'$idsLocations')";}
        if($storeId){$storeId = " AND store_id = '$storeId'";}
        
        $query = "SELECT id,description "              
                . "FROM $this->table "
                . "WHERE 1 = 1 "
                . "$storeId "
                . "$idsLocations "
                . "ORDER BY description ASC";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $array = array();
            while($row = $result->fetch_object()){
                $array[$row->id] = $row->description; 
            }            
            return $array;
        }
        
        return null;
    }
    
    public function getListSelectLocationsByStoreId($storeId){            
        $query = "SELECT id,description "              
                . "FROM $this->table "
                . "WHERE 1 = 1 "
                . "AND store_id = '$storeId'"
                . "ORDER BY description ASC";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $array = array();
            while($row = $result->fetch_object()){
                $array[$row->id] = $row->description; 
            }            
            return $array;
        }
        
        return null;
    }
    
     public function getListLocations($options = null){          
        $query = "SELECT *,"
                . "fxGetStatusName(status,'Location')as status_name, "
                . "fxGetStoreName(store_id)as store_name "
                . "FROM $this->table ";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }
        
        return null;
    }
    
    public function getListStatus(){
        $query = "SELECT * FROM status_code WHERE operation = 'Location'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $array = array();
            foreach($result as $status){
                $array[$status['code']] = $status['description'];
            }
            return $array;
        }
        return null;
    } 
}