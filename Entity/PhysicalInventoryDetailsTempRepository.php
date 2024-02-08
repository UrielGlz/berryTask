<?php
class PhysicalInventoryDetailsTempRepository extends EntityRepository {

    private $table = 'physical_inventory_details_';
    
    public function __construct(){
        $login = new Login();
        $this->table = $this->table.$login->getId();
    }    
    
    public function getTableName(){
        return $this->table;
    }
    
    // Guarda en temporal
    public function save(array $data, $table = null) {
        if(isset($data['idDetalleTemp']) && trim($data['idDetalleTemp'])!= ''){
            $result = $this->getById($data['idDetalleTemp']);
            if($result){
                $id = $data['idDetalleTemp'];
                unset($data['idDetalleTemp']);
                return $this->updateTemp($id, $data);
            }
         }
        
        unset($data['idDetalleTemp']);
        return $this->saveTemp($data);
    }
    
    public function saveTemp($data){ 
        return  parent::save($data, $this->table);          
    }
    
    public function updateTemp($id,$data){
        return $this->update($id, $data,$this->table);
    }       
        
    public function delete($id, $table = null) {
        return parent::delete($id, $this->table);
    }   

    // Guarda en tabla physical_inventory_details
    public function saveDetalles($idPhysicalInventory,$tokenForm){
        $query = "SELECT * FROM $this->table WHERE token_form = '$tokenForm'";
        $result = $this->query($query);
        if($result->num_rows > 0){            
            $result = $this->resultToArray($result);
            
            foreach($result as $data){
                unset($data['token_form'],$data['id'],$data['id_detalle']); 
                $data['id_physical_inventory'] = $idPhysicalInventory;
                parent::save($data, 'physical_inventory_details');
            }            
        }
        return true;
    }
    
    public function updateDetalles($idPhysicalInventory,$tokenForm){
        $query = "SELECT id FROM physical_inventory_details WHERE id_physical_inventory = '$idPhysicalInventory'";
        $result = $this->query($query);
        $detallesOrigin = null;    
        if($result->num_rows > 0){
            $detallesOrigin = $this->resultToArray($result);
        }

        $query = "SELECT * FROM $this->table WHERE token_form = '$tokenForm'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $idsDetalles = array();        
            $result = $this->resultToArray($result);
            foreach($result as $data){
                $idDetalle = $data['id_detalle'];
                unset($data['token_form'],$data['id'],$data['id_detalle']);
                $data['id_physical_inventory'] = $idPhysicalInventory;
                
                if(!$idDetalle){
                    parent::save($data, 'physical_inventory_details');
                    
                }else{
                    #contiene los idDetalles de la compra original que se mantendran, lo que no esten aqui se eliminaran.
                    $idsDetalles[] = $idDetalle;
                    parent::update($idDetalle,$data, 'physical_inventory_details');
                }                       
            }
            #Se eliminan ids de tabla comprasdetalls que ya no estan en $idsDetalles (este array contiene los ids que permanececieron en la compra despues del update)                
            if($detallesOrigin){
                $entityRepository = new EntityRepository();
                foreach ($detallesOrigin as $detalle){
                    if(!in_array($detalle['id'], $idsDetalles)){
                        $entityRepository->delete($detalle['id'], 'physical_inventory_details');
                    }
                }
            }          
        }
        return true;
    } 
    
    public function truncate($tokenForm){
         $query = "DELETE  FROM ".$this->table." WHERE token_form = '$tokenForm'";
        $result = $this->query($query);
        
        if($result){
            return true;
        }
        
        return null;
    }
    
    public function setPhysicalInventoryDetallesById($idPhysicalInventory,$tokenForm){
        $query = "INSERT INTO ".$this->table." (token_form,id_detalle,id_physical_inventory,id_product,id_size,id_category,quantity)
                    SELECT '$tokenForm',id,id_physical_inventory,id_product,id_size,id_category,quantity
                    FROM physical_inventory_details WHERE id_physical_inventory = '$idPhysicalInventory'";
        
        $result = $this->query($query);
        if($result){
            return true;
        }
        
        return null;
    }   
    
    public function setPhysicalInventoryDetallesForNew($tokenForm){        
        /*Se usa para listar todas loas productos que pueden ser mostrado en todos los pedidos (panaderida, pasteleria etc)*/
        $settings = new SettingsRepository();
        $id_categories_of_products_in_physical_inventory = $settings->_get('id_categories_of_products_in_store_request');
        
        $query = "DELETE FROM $this->table WHERE token_form = '$tokenForm'";
        $this->query($query);
        
        $query = "INSERT INTO ".$this->table." (token_form,id_product,id_size,id_category)
                    SELECT '$tokenForm',id,size,category
                    FROM products 
                    WHERE status = '1' "
                 . "AND show_on_store_request = '1' "
                 . " AND find_in_set(category,'$id_categories_of_products_in_physical_inventory') "
                 . "ORDER BY size ASC";
        
        $result = $this->query($query);
        if($result){
            return true;
        }
        
        return null;
    }   
}
