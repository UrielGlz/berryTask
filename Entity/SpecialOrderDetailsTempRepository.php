<?php
class SpecialOrderDetailsTempRepository extends EntityRepository {

    private $table = 'special_order_details_';
    public $flashmessenger = null;
    
    public function __construct(){
        $login = new Login();
        $this->table = $this->table.$login->getId();
        
        if(!$this->flashmessenger instanceof FlashMessenger){
            $this->flashmessenger = new FlashMessenger();
        }
    }
    
    public function _getTranslation($text){
        return $this->flashmessenger->_getTranslation($text);
    }
    
    // Guarda en temporal
    public function save(array $data, $table = null) {
        if(isset($data['price']) && trim($data['price'])== ''){$data['price'] = 0;}
        if(isset($data['idDetailTemp']) && trim($data['idDetailTemp'])!= ''){
            $result = $this->getById($data['idDetailTemp']);
            if($result){
                $id = $data['idDetailTemp'];
                unset($data['idDetailTemp']);    
                return parent::update($id, $data,$this->table);
            }
         }
        
        unset($data['idDetailTemp']);        
        /*
        $query = "SELECT id,quantity FROM ".$this->table.
                " WHERE id_product = '".$data['id_product']."'"
              . " AND type = '{$data['type']}' "
              . " AND price = '{$data['price']}' "
              . " AND multiple = '{$data['multiple']}' "
              . " AND token_form = '{$data['token_form']}'" ;
        
        $result = $this->query($query);      
         
        if($result->num_rows > 0){
            $result = $result->fetch_object();
            $data['quantity'] = $data['quantity'] + $result->quantity;
            $id = $result->id;
            return parent::update($id, $data,$this->table);
        }else{
            return parent::save($data, $this->table);
        }
        */
        return parent::save($data, $this->table);
    }
    
    public function getNextMultiple($data){
        if($data['type'] == 'Line'){return 0;
        
        }
        $query = "SELECT MAX(multiple)as multiple FROM $this->table WHERE token_form = '{$data['token_form']}'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $result->fetch_object();
            $multiple = $result->multiple;
            $multiple++;
            return $multiple;
        }
        
        return 1;
    }
    
    // Guarda en tabla comprasdetalles
    public function saveDetalles($idRequisition,$reqNumber,$tokenForm){
        $query = "SELECT * FROM $this->table WHERE token_form = '{$tokenForm}'";
        $result = $this->query($query);
        if($result->num_rows > 0){
           
            $result = $this->resultToArray($result);
            
            foreach($result as $data){
                unset($data['id']);
                unset($data['id_detail'],$data['token_form']); 
                $data['id_special_order'] = $idRequisition;
                $data['req_number'] = $reqNumber;
                
                parent::save($data, 'special_order_details');
            }   
            /*
            if(!$this->saveSupplies($idRequisition,$reqNumber)){
                return null;
            }*/
            return true;
        }else{
            $this->flashmessenger->addMessage(array('danger'=>'Debes ingresar almenos un producto para guardar la Requisicion.'));
            return null;
        }
        
    } 
    
    public function updateDetalles($idRequisition,$reqNumber,$tokenForm){
        $query = "SELECT id FROM special_order_details WHERE id_special_order = '$idRequisition'";
        $result = $this->query($query);
        $detallesOrigin = null;    
        if($result->num_rows > 0){
            $detallesOrigin = $this->resultToArray($result);
        }
        
        $query = "SELECT * FROM $this->table WHERE token_form = '{$tokenForm}'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $idsDetalles = array();
        
            $result = $this->resultToArray($result);
            foreach($result as $data){
                $idDetalle = $data['id_detail'];
                unset($data['id'],$data['id_detail'],$data['token_form']);
                $data['id_special_order'] = $idRequisition;
                $data['req_number'] = $reqNumber;
              
                if(!$idDetalle){
                    parent::save($data, 'special_order_details');
                }else{
                    #contiene los idDetalles de la requisicion original que se mantendran, lo que no esten aqui se eliminaran.
                    $idsDetalles[] = $idDetalle;
                    parent::update($idDetalle,$data, 'special_order_details');         
                }  
            }
        }

        #Se eliminan ids de tabla requisition_details que ya no estan en $idsDetalles (este array contiene los ids que permanececieron en la requisicion despues del update)                
        if($detallesOrigin){
            foreach ($detallesOrigin as $detalle){
                if(!in_array($detalle['id'], $idsDetalles)){
                    parent::delete($detalle['id'], 'special_order_details');
                }
            }
        }          
        return true;
    }
    
    public function delete($id, $table = null) {
        return parent::delete($id, $this->table);
    }
    
    public function deleteByMultiple($multiple,$tokenForm){
        $query = "DELETE FROM $this->table WHERE multiple = '$multiple' AND token_form = '{$tokenForm}'";
        if($this->query($query)){
            return true;
        }
        return null;
    }
    
    public function truncate($tokenForm){
        $query = "DELETE FROM ".$this->table." WHERE token_form = '{$tokenForm}'";
        $result = $this->query($query);
        
        if($result){
            return true;
        }
        
        return null;
    }
    
    public function setRequisitionDetailsById($idRequisition,$tokenForm){
        $query = "INSERT INTO ".$this->table." (id_detail,token_form,id_special_order,type,id_product,quantity,multiple,price,number_of_cake)
                    SELECT id,'$tokenForm',id_special_order,type,id_product,quantity,multiple,price,number_of_cake
                    FROM special_order_details WHERE id_special_order = '$idRequisition'";
        
        $result = $this->query($query);
        if($result){
            return true;
        }
        
        return null;
    }

    public function getById($id, $table = null,$selectAux = null) {
        return parent::getById($id, $this->table,$selectAux);
    }
    
    public function getByMultiple($multiple,$tokenForm){
        $query = "SELECT t.*,s.category "
                . "FROM $this->table t, product_slices s "
                . "WHERE t.id_product = s.id "
                . "AND multiple = '$multiple' "
                . "AND token_form = '{$tokenForm}'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $this->resultToArray($result);
            foreach($result as $row){
                $array[$row['category']] = $row;
            }
            return $array;
        }
        
        return null;
    }
}