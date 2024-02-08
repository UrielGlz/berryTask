<?php
class SalesRecordExpesnsesDetailsTemp extends EntityRepository {

    private $table = 'sales_record_expenses_details_';
    
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

    // Guarda en tabla store_request_details
    public function saveDetalles($idSalesRecord,$tokenForm){
        $query = "SELECT * FROM $this->table WHERE token_form = '$tokenForm'";
        $result = $this->query($query);
        if($result->num_rows > 0){            
            $result = $this->resultToArray($result);
            
            foreach($result as $data){
                unset($data['token_form'],$data['id'],$data['id_detalle']); 
                $data['id_sales_record'] = $idSalesRecord;
                if(!parent::save($data, 'sales_record_expenses_details')){
                    return null;
                }
            }            
        }
        return true;
    }
    
    public function updateDetalles($idSalesRecord,$tokenForm){
        $query = "SELECT id FROM sales_record_expenses_details WHERE id_sales_record = '$idSalesRecord'";
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
                $data['id_sales_record'] = $idSalesRecord;
                
                if(!$idDetalle){
                    parent::save($data, 'sales_record_expenses_details');
                    
                }else{
                    #contiene los idDetalles de la compra original que se mantendran, lo que no esten aqui se eliminaran.
                    $idsDetalles[] = $idDetalle;
                    parent::update($idDetalle,$data, 'sales_record_expenses_details');
                }                       
            }
            #Se eliminan ids de tabla comprasdetalls que ya no estan en $idsDetalles (este array contiene los ids que permanececieron en la compra despues del update)                
            if($detallesOrigin){
                $entityRepository = new EntityRepository();
                foreach ($detallesOrigin as $detalle){
                    if(!in_array($detalle['id'], $idsDetalles)){
                        $entityRepository->delete($detalle['id'], 'sales_record_expenses_details');
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
    
    public function setSalesRecordExpenseDetallesById($idSalesRecord,$tokenForm){
        $query = "INSERT INTO ".$this->table." (token_form,id_detalle,id_sales_record,id_category_expense,comments,amount)
                    SELECT '$tokenForm',id,id_sales_record,id_category_expense,comments,amount
                    FROM sales_record_expenses_details WHERE id_sales_record = '$idSalesRecord'";
        
        $result = $this->query($query);
        if($result){
            return true;
        }
        
        return null;
    }   
    
    public function setSalesRecordExpenseDetallailsForNew($tokenForm){
        $query = "INSERT INTO ".$this->table." (token_form,id_category_expense)
                    SELECT '$tokenForm',id
                    FROM product_categories 
                    WHERE status = '1' "
                 . "AND type = 'expense' "
                . "ORDER BY description ASC";
        
        $result = $this->query($query);
        if($result){
            return true;
        }
        
        return null;
    }   
}
