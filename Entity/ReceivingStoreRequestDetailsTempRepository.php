<?php
class ReceivingStoreRequestDetailsTempRepository extends EntityRepository {

    private $table = 'receiving_store_request_details_';
    
    public function __construct(){
        $login = new Login();
        $this->table = $this->table.$login->getId();
    }
    
    public function getTableName(){
        return $this->table;
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
        $query = "SELECT id,received FROM ".$this->table.
                " WHERE id_product = '".$data['id_product']."' ";
        
        $result = $this->query($query);      
         
        if($result->num_rows > 0){
            $result = $result->fetch_object();
            $data['received'] = $data['received'] + $result->received;
            $id = $result->id;
            return parent::update($id, $data,$this->table);
        }else{            
            $data['real_stock_in_store'] = '0';
            $data['quantity'] = '0';
            return parent::save($data, $this->table);
            /*return true;*/
        }
    }
    
    // Guarda en tabla comprasdetalles
    public function saveDetalles($idReceiving){
        $query = "SELECT * FROM $this->table";
        $result = $this->query($query);
        if($result->num_rows > 0){  
            $repoInventarory = new InventarioRepository();
            $result = $this->resultToArray($result); 
            
            foreach($result as $data){
                unset($data['id']);
                unset($data['id_detail'],$data['quantity']); 
                $data['id_receiving'] = $idReceiving;
                
                if(parent::save($data, 'receiving_store_request_details')){
                    if(!$repoInventarory->addInventory(array('id_product'=>$data['id_product'],'quantity'=>$data['received'],'controller'=>"Recibos-$idReceiving"))){
                        return null;
                    }
                }
            }  
        }
        return true;
    } 
    
    public function updateDetalles($idReceiving){
        //$repoInventarory = new InventoryRepository();
        $query = "SELECT id,received FROM receiving_store_request_details WHERE id_receiving = '$idReceiving'";
        $result = $this->query($query);
        $detallesOrigin = null;    
        if($result->num_rows > 0){
            $detallesOriginTemp = $this->resultToArray($result);
            foreach($detallesOriginTemp as $detalle){
                $detallesOrigin[$detalle['id']] = $detalle;
            }
        } 
        
        $query = "SELECT * FROM $this->table";
        $result = $this->query($query);
       
        if($result->num_rows > 0){
            $result = $this->resultToArray($result);
            $idsDetalles = array();     
            
            foreach($result as $data){
                $idDetalle = $data['id_detail'];
                unset($data['id'],$data['id_detail']);
                $data['id_receiving'] = $idReceiving;
                if($data['received']==''){$data['received'] = '0';}                
                
                if(!$idDetalle){
                    if(parent::save($data, 'receiving_store_request_details')){   
                        /*
                        if(!$repoInventarory->addInventory(array('id_product'=>$data['id_product'],'quantity'=>$data['received'],'controller'=>"Recibos-$idReceiving"))){
                            return null;
                        }*/
                    }
                }else{
                    #contiene los idDetalles de la requisicion original que se mantendran, lo que no esten aqui se eliminaran.
                    $currentData = $detallesOrigin[$idDetalle];
                    $idsDetalles[] = $idDetalle;
                       
                    if(parent::update($idDetalle,$data, 'receiving_store_request_details')){
                        /*
                         if(!$repoInventarory->updateAddInventory(array(
                                        'id_product'=>$data['id_product'],
                                        'current_quantity'=>$currentData['received'],
                                        'new_quantity'=>$data['received'],
                                        'controller'=>"Recibos-$idReceiving"
                                ))){
                             return null;
                         }*/
                    }                    
                }  
            }
        }
        #Se eliminan ids de tabla requisition_details que ya no estan en $idsDetalles (este array contiene los ids que permanececieron en la requisicion despues del update)                
        if($detallesOrigin){
            foreach ($detallesOrigin as $detalle){
                if(!in_array($detalle['id'], $idsDetalles)){                                        
                    if(parent::delete($detalle['id'], 'receiving_store_request_details')){      
                        /*
                        if(!$repoInventarory->subInventory(array('id_product'=>$detalle['id_product'],'quantity'=>$detalle['received'],'controller'=>"Recibos-$idReceiving"))){
                            return null;
                        }*/
                    }
                }
            }
        }              
        return true;
    }
    
    public function delete($id, $table = null) {
        return parent::delete($id, $this->table);
    }
    
    public function truncate(){
        $query = "TRUNCATE ".$this->table;
        $result = $this->query($query);
        
        if($result){
            return true;
        }
        
        return null;
    }
    
    public function drop(){
        $query = "DROP TABLE ".$this->table;
        $result = $this->query($query);
        
        if($result){
            return true;
        }        
        return null;
    }
    
    public function setReceivingStoreRequestDetailsById($idReceiving){
        $this->query("TRUNCATE ".$this->table);
        $query = "INSERT INTO ".$this->table." (id_detail,id_receiving,id_product,real_stock_in_store,quantity,received)
                    SELECT id,id_receiving,id_product,real_stock_in_store,quantity,received
                    FROM receiving_store_request_details WHERE id_receiving = '$idReceiving'";
        
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