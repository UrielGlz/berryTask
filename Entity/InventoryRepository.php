<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class InventoryRepository extends EntityRepository {

    private $table = 'inventory';

    public function save(array $data, $table = null) {
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
    
    public function getListInventarioPorSucursal() {
        $select = "SELECT * from $this->table ";
        $result = $this->query($select);

        if ($result) {
           return $this->resultToArray($result);
        }
        return null;
    }
    
     public function getLocationForProduct($idProduct,$storeId){
        $login = new Login();
        $query = "SELECT GROUP_CONCAT(DISTINCT(id_location))as location "
                . "FROM $this->table "
                . "WHERE id_product = '$idProduct'"
                . "AND id_store = '$storeId'"
                . "AND stock > 0 ";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $result->fetch_object();
            return $result->location;
        }
        
        return null;
    }
    
     public function getInventorySucursalByIdProduct($idProduct,$idStore){
        $query = "SELECT id,IFNULL(stock,0)as stock FROM $this->table WHERE id_product = '$idProduct' AND id_location = '$idStore' ";
        $result = $this->query($query);
        
        if($result->num_rows > 0){            
            return $this->resultToArray($result)[0];
        }
        return null;
    }
    
     public function addInventory($array){
        $idProduct = $array['id_product'];
        $quantity = $array['quantity'];
        $id_location = $array['id_location'];

        if($quantity != 0){
            $data = $this->getInventorySucursalByIdProduct($idProduct,$id_location);
            $currentStock = $data['stock'];
            
            if($currentStock != null){ 
                $currentStock += $quantity;            
                return parent::update($data['id'],array('stock'=>$currentStock), $this->table);            
            }else{     
                $locationRepo = new LocationRepository();
                $locationData = $locationRepo->getById($id_location);
                return parent::save(array('id_product'=>$idProduct,'id_location'=>$id_location,'stock'=>$quantity,'id_store'=>$locationData['store_id']), $this->table);
            }          
        }        
        return true;
    }
    
    public function subInventory($array){
        $idProduct = $array['id_product'];
        $quantity = $array['quantity'];
        $id_location = $array['id_location'];
        
        if($quantity != 0){
            $data = $this->getInventorySucursalByIdProduct($idProduct,$id_location);
            $currentStock = $data['stock'];
            
            if($currentStock != null){
                $currentStock -= $quantity;
                return parent::update($data['id'],array('stock'=>$currentStock), $this->table);    
            }else{
               $quantity *= -1;
               $locationRepo = new LocationRepository();
               $locationData = $locationRepo->getById($id_location);
               return parent::save(array('id_product'=>$idProduct,'id_location'=>$id_location,'stock'=>$quantity,'id_store'=>$locationData['store_id']), $this->table);
            }                
        }        
        return true;
    }
    
    public function updateAddInventory($array){ 
        $idProduct = $array['id_product'];
        $currentQuantity = $array['current_quantity'];
        $newQuantity = $array['new_quantity'];
        $currentSucursal = $array['current_id_location'];
        $newSucursal = $array['new_id_location'];
       
        if($currentSucursal != $newSucursal){
            $this->subInventory(array('id_product'=>$idProduct,'quantity'=>$currentQuantity,'id_location'=>$currentSucursal));
            return $this->addInventory(array('id_product'=>$idProduct,'quantity'=>$newQuantity,'id_location'=>$newSucursal));
        }else{
            $diff = $newQuantity - $currentQuantity;
           
            if($diff > 0){ 
                return $this->addInventory(array('id_product'=>$idProduct,'quantity'=>$diff,'id_location'=>$newSucursal));
            }elseif($diff < 0){
                return $this->subInventory(array('id_product'=>$idProduct,'quantity'=>abs($diff),'id_location'=>$newSucursal));
            }        
        }
       
        return true;
    }
    
    public function deleteAddInventory($products){
        if(is_array($products)){
            foreach($products as $product){
                $rs = $this->subInventory(array('id_product'=>$product['id_product'],'quantity'=>$product['quantity'],'id_location'=>$product['id_location']));
                if(!$rs){
                    return null;
                }
            }
        }
        return true;
    }
    
    public function updateSubInventory($array){        
        $idProduct = $array['id_product'];
        $currentQuantity = $array['current_quantity'];
        $newQuantity = $array['new_quantity'];
        $currentSucursal = $array['current_id_location'];
        $newSucursal = $array['new_id_location'];
       
        if($currentSucursal != $newSucursal){
            $this->addInventory(array('id_product'=>$idProduct,'quantity'=>$currentQuantity,'id_location'=>$currentSucursal));
            return $this->subInventory(array('id_product'=>$idProduct,'quantity'=>$newQuantity,'id_location'=>$newSucursal));
        }else{
            $diff = $newQuantity - $currentQuantity;
           
            if($diff > 0){ 
                return $this->subInventory(array('id_product'=>$idProduct,'quantity'=>$diff,'id_location'=>$newSucursal));
            }elseif($diff < 0){
                return $this->addInventory(array('id_product'=>$idProduct,'quantity'=>abs($diff),'id_location'=>$newSucursal));
            }        
        }
       
        return true;
    }
    
    public function deleteSubInventory($products){
                                
        if(is_array($products)){ 
            foreach($products as $product){               
                $rs = $this->addInventory(array('id_product'=>$product['id_product'],'quantity'=>$product['quantity'],'id_location'=>$product['id_location']));
                if(!$rs){
                    return null;
                }
            }
        }
        return true;
    }  
    
    /*Lo ocupo para editar inventario manual desde Reportes*/
    public function updateInventory($array){
        $idProduct = $array['id_product'];
        $quantity = $array['quantity'];
        $id_location = $array['id_location'];
        $notes = '';
        if(isset($array['notes'])){$notes = $array['notes'];}        

        $data = $this->getInventorySucursalByIdProduct($idProduct,$id_location);
        $currentStock = $data['stock'];

        if($currentStock != null){            
            return parent::update($data['id'],array('stock'=>$quantity,'notes'=>$notes), $this->table);
        }else{   
            return parent::save(array('id_product'=>$idProduct,'id_location'=>$id_location,'stock'=>$quantity,'notes'=>$notes), $this->table);
        }          
    
        return true;
    }
}
