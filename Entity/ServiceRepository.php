<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class ServiceRepository extends EntityRepository {

    private $table = 'products';
    private $options = array(
       'type'=>null,
        'code' => null,
        'description' => null,
        'category' => null,       
        'cost'=>null,
        'sale_price'=>null,
        //'wholesale_price'=>null,        
        'discount'=>null,
        'taxes'=>null,
        'taxes_included'=>null,       
        'inventory'=>null,
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
        if(is_null($data['status'])){$data['status'] = '1';}  
        
        return parent::save($data, $this->table);
    }
    
    public function delete($id, $table = null) {
        return parent::delete($id, $this->table);
    }

    public function update($id, $data, $table = null) {              
        return parent::update($id, $data, $this->table);
    }

    public function getById($id, $table = null,$selectAux = null) {
        return parent::getById($id, $this->table,$selectAux);
    }
    
    public function getListServices() {
        $select = "SELECT *,"
                . "fxGetCategoryDescription(category)as category_name,"
                . "fxGetBrandDescription(brand)as brand_name,"
                . "fxGetPresentationDescription(presentation)as presentation_name,"
                . "fxGetStatusName(status,'Product') as status_name, "
                . "fxGetStoreName(location)as store_name "
                . "FROM $this->table "
                . "WHERE 1 = 1 AND type = 'service'";
        
        $result = $this->query($select);

        if ($result) {
           return $this->resultToArray($result);
        }
        return null;
    }
    
    public function getListStatus(){
        $query = "SELECT * FROM status_code WHERE operation = 'Service'";
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