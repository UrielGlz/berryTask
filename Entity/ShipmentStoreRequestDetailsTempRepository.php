<?php
class ShipmentStoreRequestDetailsTempRepository extends EntityRepository {

    private $table = 'shipment_store_requests_details_';
    
    public function __construct(){
        $login = new Login();
        $this->table = $this->table.$login->getId();
    }
    // Guarda en temporal
    public function save(array $data, $table = null) {
        if(isset($data['idDetailTemp']) && trim($data['idDetailTemp'])!= ''){
            $result = $this->getById($data['idDetailTemp']);
            if($result){
                $id = $data['idDetailTemp'];
                unset($data['idDetailTemp']);    
                return parent::update($id, $data,$this->table);
            }
         }

        unset($data['idDetailTemp']);        
        $query = "SELECT id,quantity FROM ".$this->table.
                " WHERE id_product = '".$data['id_product']."'  AND token_form = '{$data['token_form']}'";
        
        $result = $this->query($query);      
         
        if($result->num_rows > 0){
            $result = $result->fetch_object();
            $data['quantity'] = $data['quantity'] + $result->quantity;
            $id = $result->id;
            return parent::update($id, $data,$this->table);
        }else{
            return parent::save($data, $this->table);
        }
    }
    
    // Guarda en tabla shipment_detalles
    public function saveDetalles($idShipment,$tokenForm,$recibirAutomatico = null){
        $query = "SELECT *,IFNULL(min_stock,0)as min_stock,IFNULL(real_stock_in_store,0)as real_stock_in_store  FROM $this->table WHERE token_form = '$tokenForm'";
        $result = $this->query($query);
        if($result->num_rows > 0){           
            $result = $this->resultToArray($result);            
            $repoProduct = new InventoryRepository();
            
            $settings = new SettingsRepository();
            $idStore = $settings->_get('id_store_for_inventory_of_store_shipments');
            
            $storeRepo = new StoreRepository();
            $storeData = $storeRepo->getById($idStore);
            
            foreach($result as $data){
                $data['id_shipment'] = $idShipment;
                unset($data['token_form'],$data['id'],$data['id_detail']);
                
                if($recibirAutomatico){$data['received'] = $data['quantity'];}
                else{unset($data['received']);}
                
                if(parent::save($data, 'shipment_store_requests_details')){
                    if(!$repoProduct->subInventory(array(
                        'id_product'=>$data['id_product'],
                        'quantity'=>$data['quantity'],
                        'id_location'=>$storeData['default_location']
                            ))){
                        
                    return null;
                    }
                }else{
                    return null;
                }
            }  
        }         
        return true;
    } 
    
    public function updateDetalles($idShipment,$tokenForm,$recibirAutomatico = null){                    
        $settings = new SettingsRepository();
        $idStore = $settings->_get('id_store_for_inventory_of_store_shipments');

        $storeRepo = new StoreRepository();
        $storeData = $storeRepo->getById($idStore);
            
        $query = "SELECT id,id_product,quantity FROM shipment_store_requests_details WHERE id_shipment = '$idShipment'";
        $result = $this->query($query);
        $detallesOrigin = null;    
        if($result->num_rows > 0){
            $detallesOriginTemp = $this->resultToArray($result);
            foreach($detallesOriginTemp as $detalle){
                $detallesOrigin[$detalle['id']] = $detalle;
            }
        }
        
        $query = "SELECT * FROM $this->table WHERE token_form = '$tokenForm'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $this->resultToArray($result);
            $idsDetalles = array();            
            $inventoryRepo = new InventoryRepository();        
            
            foreach($result as $data){
                $idDetalle = $data['id_detail'];                
                $data['id_shipment'] = $idShipment;
                unset($data['token_form'],$data['id'],$data['id_detail']);
                
                if($recibirAutomatico){$data['received'] = $data['quantity'];}
                else{unset($data['received']);}
              
                if(!$idDetalle){
                    if(parent::save($data, 'shipment_store_requests_details')){
                        if(!$inventoryRepo->subInventory(array(
                            'id_product'=>$data['id_product'],
                            'quantity'=>$data['quantity'],
                            'id_location'=>$storeData['default_location']
                         ))){
                            
                            return null;
                        }
                    }
                }else{
                    #contiene los idDetalles de la requisicion original que se mantendran, lo que no esten aqui se eliminaran.
                    $currentData = $detallesOrigin[$idDetalle];
                    $idsDetalles[] = $idDetalle;
          
                    if(parent::update($idDetalle,$data, 'shipment_store_requests_details')){
                        if(!$inventoryRepo->updateSubInventory(array(
                            'id_product'=>$data['id_product'],
                            'current_quantity'=>$currentData['quantity'],
                            'new_quantity'=>$data['quantity'],
                            'current_id_location'=>$storeData['default_location'],
                            'new_id_location'=>$storeData['default_location']
                        ))){
                            
                            return null;
                        }
                    }
                }  
            }
        }

        #Se eliminan ids de tabla requisition_details que ya no estan en $idsDetalles (este array contiene los ids que permanececieron en la requisicion despues del update)                
        if($detallesOrigin){
            foreach ($detallesOrigin as $detalle){
                if(!in_array($detalle['id'], $idsDetalles)){                    
                    if(parent::delete($detalle['id'], 'shipment_store_requests_details')){           
                        if(!$inventoryRepo->addInventory(array(
                            'id_product'=>$detalle['id_product'],
                            'quantity'=>$detalle['quantity'],
                            'id_location'=>$storeData['default_location']
                        ))){
                            return null;
                        }
                    }
                }
            }
        }  
            
        return true;
    }
    
    public function delete($id, $table = null) {
        return parent::delete($id, $this->table);
    }
    
    public function truncate($tokenForm){
        $query = "DELETE  FROM ".$this->table." WHERE token_form = '$tokenForm'";
        $result = $this->query($query);
        
        if($result){
            return true;
        }
        
        return null;
    }
    
    public function setShipmentStoreRequestDetailsById($idShipment,$tokenForm){
        $query = "INSERT INTO ".$this->table." (token_form,id_detail,id_shipment,id_product,min_stock,real_stock_in_store,quantity,received)
                    SELECT '$tokenForm',id,id_shipment,id_product,min_stock,real_stock_in_store,quantity,received
                    FROM shipment_store_requests_details WHERE id_shipment = '$idShipment'";
        
        $result = $this->query($query);
        if($result){
            return true;
        }
        
        return null;
    }

    public function getById($id, $table = null,$selectAux = null) {
        return parent::getById($id, $this->table,$selectAux);
    }
}