<?php
class CategoryRepository extends EntityRepository {

    private $table = 'product_categories';
    private $flashmessenger = null;    
    private $options = array(
        'description'=>null,       
        'type'=>null,
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
        $query = "SELECT *,fxGetStatusName(status,'Category')as status_name FROM $this->table WHERE id = '$id'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result)[0];
        }
        return null;
    }

    public function isUsedInRecord($id, array $buscarEn = null,$andWhere = null) {
        $query = "SELECT id FROM supplies WHERE category_id = '$id'";
        $result = $this->query($query);
        if($result->num_rows > 0){
            return true;
        }else{
            $query = "SELECT id FROM products WHERE category = '$id'";
            $result = $this->query($query);
            if($result->num_rows > 0){
                return true;
            }
        }
        
        return null;
    }
    
    public function getListSelectCategories($type = null){  
        $orderBy = " ORDER BY description ASC ";
        if($type == 'Parts of the cake'){$orderBy = " ORDER BY FIELD (description,'Pan','Relleno','Decorado')";}
        if($type){$type = " AND type = '$type'";}
       
        $query = "SELECT id,description "              
                . "FROM $this->table "
                . "WHERE 1 = 1 "
                . "$type "
                . "$orderBy";
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
    
     public function getListCategories($options = null){          
        $query = "SELECT *,"
                . "fxGetStatusName(status,'Category')as status_name "
                . "FROM $this->table ";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }
        
        return null;
    }
    
    public function getListStatus(){
        $query = "SELECT * FROM status_code WHERE operation = 'Category'";
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