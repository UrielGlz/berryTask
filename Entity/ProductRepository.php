<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class ProductRepository extends EntityRepository {

    private $table = 'products';
    private $options = array(
        'type'=>null,
        'code' => null,
        'description' => null,
        'category' => null,
        'supplie'=>null,
        'masa'=>null,
        'flour'=>null,
        'brand' => null,
        'size' => null,
        'presentation' => null,
        'unit_of_measurement'=>null,
        'cost'=>null,
        'sale_price'=>null,   
        'discount'=>null,
        'taxes'=>null,
        'taxes_included'=>null,
        'min_stock'=>null,
        'inventory'=>null,
        'location'=>null,
        'show_on_store_request'=>null,
        'status'=>null,
        'comments'=>null
    );
    
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
        if(trim($data['code'])== ''){$data['code'] = '0';} 
        if(is_null($data['status'])){$data['status'] = '1';}  
        
        if(is_array($data['location']) && count($data['location']) > 0){
            $data['location'] = trim(implode(',', $data['location']),',');
        }
        elseif($data['location'] == ''){
            $data['location'] = $this->getDefaultLocation();
            
        }
        
        return parent::save($data, $this->table);
    }    

    public function update($id, $data, $table = null) {      
        if(trim($data['code'])== ''){$data['code'] = '0';} 
        if(is_array($data['location']) && count($data['location']) > 0){
            $data['location'] = trim(implode(',', $data['location']),',');
        }
        elseif($data['location'] == '' || is_null($data['location'])){
            $data['location'] = $this->getDefaultLocation();
        }
        
        /*Es unico select en el formulario donde 'seleccionar una opcion' esta como ''*/
        if(isset($data['supplie']) && trim($data['supplie'] == '')){$data['supplie'] = '0';}
      
        return parent::update($id, $data, $this->table);
    }    
        
    public function delete($id, $table = null) {
        return parent::delete($id, $this->table);
    }    

    public function getById($id, $table = null,$selectAux = null) {
        return parent::getById($id, $this->table,$selectAux);
    }

    public function isUsedInRecord($id, array $buscarEn = null,$andWhere = null) {
       return parent::isUsedInRecord($id, array(
           'purchases_detail' => 'product_id'));
    }    
            
    public function getDefaultLocation(){
        $login = new Login();
        
        $query = "SELECT CONCAT(default_location)as location FROM stores WHERE find_in_set(id,'{$login->getStoreId()}')";
        $result = $this->query($query);
        
        if($result->num_rows){
            $result = $result->fetch_object();
            return $result->location;
        }
    }
    
    public function getProductsByIds($ids){
        $select = "SELECT * FROM $this->table WHERE id IN($ids)";
        $result = $this->query($select);
        
        if($result){
            return $this->resultToArray($result);
        }
        
        return null;
    }
    
    public function getListSelectProducts($category = null,$size = null) {
       if($category){$category = " AND category = '$category'";}
       if($size){$size = " AND size = '$size'";}
       
       $select = "SELECT *,"
                . "fxGetCategoryDescription(category)as category_name,"
                . "fxGetBrandDescription(brand)as brand_name,"
                . "fxGetSizeDescription(size)as size_name,"
                . "fxGetPresentationDescription(presentation)as presentation_name "
                . "FROM $this->table WHERE (supplie is null OR supplie = 0) $category $size";
        $result = $this->query($select);

        if ($result) {
            $array = array();
            while ($row = $result->fetch_assoc()) {
                $array[$row['id']] = $row['description'].' '.$row['size_name'];
            }
            return $array;
        }
        return null;
    }
    
     public function getListProducts() {
        $select = "SELECT *,"
                . "fxGetCategoryDescription(category)as category_name,"
                 . "fxGetSizeDescription(size)as size_name,"
                . "fxGetBrandDescription(brand)as brand_name,"
                . "fxGetPresentationDescription(presentation)as presentation_name,"
                . "fxGetMasaName(masa)as masa_name, "
                . "fxGetUMDescription(unit_of_measurement)as um_name, "
                . "fxGetStatusName(status,'Product') as status_name, "
                . "fxGetStoreName(location)as store_name "
                . "FROM $this->table "
                . "WHERE 1 = 1 "
                . "AND type = 'product'";
        
        $result = $this->query($select);

        if ($result) {
           return $this->resultToArray($result);
        }
        return null;
    }
    
    public function getListStatus(){
        $query = "SELECT * FROM status_code WHERE operation = 'Product'";
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
    
    public function getLastIdInsumo(){
        $query = "SELECT id FROM $this->table ORDER BY id DESC LIMIT 1";
        
        $result =$this->query($query);
        
        if($result->num_rows > 0){
            $result = $result->fetch_object();
            return $result->id;
        }else{
            return 0;
        }
    }
    
    public function existeCodigo($codigo,$idInsumo = null){
        $codigo = strtolower(trim($codigo));
        if($codigo == ''){return null;}
        else{$codigo = " code = '$codigo' ";}
        if($idInsumo != null && trim($idInsumo)!=''){$idInsumo = " AND id != '$idInsumo' ";}
        
        $query = "SELECT code,
                         description,
                         fxGetCategoryDescription(category) as category,
                         fxGetBrandDescription(brand) as brand,
                         fxGetSizeDescription(size) as size,
                         fxGetPresentationDescription(presentation) as presentation
                    FROM $this->table 
                    WHERE $codigo $idInsumo
                    LIMIT 1 ";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result =  $this->resultToArray($result);
            return $result[0];
        }
        return null;
    }
    
    public function getProductsLike($item){
        $query = "SELECT *,
                    fxGetSizeDescription(size)as size,
                    fxGetCategoryDescription(category)as category,
                    fxGetBrandDescription(brand)as brand,
                    fxGetPresentationDescription(presentation)as presentation
                    FROM $this->table WHERE code like '%$item%' OR description like '%$item%' ORDER BY description";
        $result = $this->query($query);
        
        if($result){
            return $this->resultToArray($result);
        }
        
        return null;
    }
    
    public function getListaSelectImpuestos() {
        $select = "SELECT * FROM taxes ";
        $result = $this->query($select);

        if ($result) {
            $array = array();
            while ($row = $result->fetch_assoc()) {
                $array[$row['id']] = $row['description']." - ".$row['rate']." %";
            }
            return $array;
        }
        return null;
    }
    
    public function getDataTaxesById($id){
        $query = "SELECT * FROM taxes WHERE id = '$id'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result)[0];
        }
        
        return null;
    }
      
  public function getByCode($code) {
        $query = "SELECT * FROM $this->table WHERE code = '$code' LIMIT 1";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $this->resultToArray($result);
            return $result[0];
        }
        return null;
    }
    
    public function getListSelectMasas(){        
        $query = "SELECT id,description "              
                . "FROM masas "
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
}