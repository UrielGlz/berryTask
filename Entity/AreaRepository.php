<?php
class AreaRepository extends EntityRepository {

    private $table = 'areas';
    private $flashmessenger = null;    
    private $options = array(
        'name'=>null,       
        'category_id'=>null,
        'automatic_shipment'=>null
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
        $query = "SELECT * FROM $this->table WHERE id = '$id'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result)[0];
        }
        return null;
    }
    
    public function getByRoleId($roleId, $table = null,$selectAux = null) {
        $query = "SELECT * FROM $this->table WHERE role_id = '$roleId'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result)[0];
        }
        return null;
    }

    public function isUsedInRecord($id, array $buscarEn = null,$andWhere = null) {        
        return true;
    }
    
    public function getListSelectAreas(){
        $query = "SELECT * FROM areas ";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $array = array();
            while($row = $result->fetch_object()){
                $array[$row->id] = $row->name; 
            }            
            return $array;
        }
        
        return null;
    }
    
     public function getListAreas($options = null){          
        $query = "SELECT *,"
                . "fxGetStatusName(status,'Area')as status_name "
                . "FROM $this->table ";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }
        
        return null;
    }
    
    public function getCategoryByAreaId($areaId){
        $query = "SELECT category_id FROM areas WHERE id = '$areaId'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $result->fetch_object();
            return $result->category_id;
        }
        return null;
    }
    
    public function getArrayCategoryArea(){
        $query = "SELECT * FROM $this->table";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $this->resultToArray($result);
            $array = array();
            
            foreach($result as $row){
                $areaArray = explode(',', $row['category_id']);
                foreach($areaArray as $key => $value){$array[$value] = $row;}
            }
            
            return $array;
        }
        
        return null;
    }
    
    public function getListStatus(){
        $query = "SELECT * FROM status_code WHERE operation = 'Area'";
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
    
    /* AREA DE PRODUCCION DE PANADERIA*/
    public function getListSelectAreasProduccionPanaderia(){
        $query = "SELECT * FROM areas_produccion_panaderia ";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $array = array();
            while($row = $result->fetch_object()){
                $array[$row->id] = $row->name; 
            }            
            return $array;
        }        
        return null;
    }
    
     public function getAreaProduccionPanaderiaById($id, $table = null,$selectAux = null) {
        $query = "SELECT * FROM areas_produccion_panaderia WHERE id = '$id'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result)[0];
        }
        return null;
    }
    
}