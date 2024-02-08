<?php
class SliceRepository extends EntityRepository {

    private $table = 'product_slices';
    private $flashmessenger = null;    
    private $options = array(
        'flavor'=>null,
        'size'=>null,
        'shape'=>null,
        'category'=>null,
        'price'=>null,
        'comments'=>null,        
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
        if(is_null($data['status'])){$data['status'] = '1';} 
        return parent::update($id, $data, $this->table);        
    }

    public function getById($id, $table = null,$selectAux = null) {
        $query = "SELECT *,fxGetStatusName(status,'Slice')as status_name FROM $this->table WHERE id = '$id'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result)[0];
        }
        return null;
    }

    public function isUsedInRecord($id, array $buscarEn = null,$andWhere = null) {
        $query = "SELECT id FROM products_details WHERE id_slice = '$id'";
        $result = $this->query($query);
        if($result->num_rows > 0){
            return true;
        }
        
        return null;
    }
    
    public function getShapesBySize($size){
        $query = "SELECT shape,fxGetShapeDescription(shape)as shapeName FROM $this->table WHERE size = '$size' AND status = 1";
        $result = $this->query($query);
        
        if ($result->num_rows > 0) {
            $array = array();
            $result = $this->resultToArray($result);
            foreach($result as $row){
                $array[$row['shape']] = $row['shapeName'];
            }
            return $array;
        }
        return null;
    }
    
    public function getListSelectSlices($category = null,$size = null,$shape = null){
        if($category !== null){$category = " AND c.category = '$category'";}
        if($size !== null){$size = " AND c.size = '$size'";}
        if($shape !== null){$shape = " AND c.shape = '$shape'";}
        $query = "SELECT c.*,
                fxGetStatusName(c.`status`,'Slice')as statusName,
                fxGetCategoryDescription(c.category)as categoryName,
                fxGetSizeDescription(c.size)as sizeName,
                fxGetShapeDescription(c.shape)as shapeName
                FROM $this->table c
                WHERE  1=1 $category $size $shape " 
              . "GROUP BY c.id "
              . "ORDER BY c.flavor ASC ";
    
        $result = $this->query($query);
        
       if ($result->num_rows > 0) {
            $array = array();
            $result = $this->resultToArray($result);
            foreach($result as $row){
                $array[$row['id']] = $row['flavor'];
            }
            return $array;
        }
        return null;
    }
    
    public function getListSlicesWizard($size = null,$shape = null){
        if($size !== null){$size = " AND c.size = '$size'";}
        if($shape !== null){$shape = " AND c.shape = '$shape'";}
        $query = "SELECT c.*,
                fxGetSizeDescription(c.size)as sizeName,
                fxGetShapeDescription(c.shape)as shapeName
                FROM $this->table c
                WHERE  1=1 $size $shape " 
              . "GROUP BY c.id "
              . "ORDER BY c.flavor ASC ";
    
        $result = $this->query($query);
        
       if ($result->num_rows > 0) {
            return $this->resultToArray($result);
        }
        return null;
    }
    
     public function getListSlices($options = null){          
        $query = "SELECT *,"
                . "fxGetShapeDescription(shape)as shape_name, "
                . "fxGetStatusName(status,'Slice')as status_name, "
                . "fxGetCategoryDescription(category)as category_name,"
                . "fxGetSizeDescription(size)as size_name "
                . "FROM $this->table ";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }
        
        return null;
    }
    
    public function getListStatus(){
        $query = "SELECT * FROM status_code WHERE operation = 'Slice'";
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