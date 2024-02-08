<?php
class UMRepository extends EntityRepository {

    private $table = 'product_ums';
    private $flashmessenger = null;    
    private $options = array(
        'description'=>null,       
        'abbreviation'=>null,
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

        $this->startTransaction();
        $rs = parent::save($data, $this->table);
        
        if(!$rs){
            $this->rollback();
            return null;
        }
        $this->commit();
        return true;        
    }
    
    public function delete($id, $table = null) {
        return parent::delete($id, $this->table);
    }
    
    public function update($id, $data, $table = null) {           
        return parent::update($id, $data, $this->table);        
    }

    public function getById($id, $table = null,$selectAux = null) {
        $query = "SELECT *,fxGetStatusName(status,'UM')as status_name FROM $this->table WHERE id = '$id'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result)[0];
        }
        return null;
    }

    public function isUsedInRecord($id, array $buscarEn = null,$andWhere = null) {
        $query = "SELECT id FROM supplies WHERE um_id = '$id'";
        $result = $this->query($query);
        if($result->num_rows > 0){
            return true;
        }else{
            $query = "SELECT id FROM products WHERE unit_of_measurement = '$id'";
            $result = $this->query($query);
            if($result->num_rows > 0){
                return true;
            }
        }
        
        return null;
    }
    
    public function getListSelectUMs(){        
        $query = "SELECT id,description "              
                . "FROM $this->table "
                . "WHERE 1 = 1 "
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
    
     public function getListUMs($options = null){          
        $query = "SELECT *,"
                . "fxGetStatusName(status,'UM')as status_name "
                . "FROM $this->table ";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }
        
        return null;
    }
    
    public function getListStatus(){
        $query = "SELECT * FROM status_code WHERE operation = 'UM'";
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