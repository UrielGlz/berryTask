<?php

class EntityRepository {
    
    private $id;
    private $lastInsertId = null;
    private $_backButton = null;
    
    protected $_options = array();
   

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }
    
    public function delete($id,$table){           
       $select = "DELETE  FROM $table WHERE id = $id";
       $result = $this->query($select);

        return $result;
    }

    public function save(array $data, $table) {
        $login = new Login();
        $noCreateDate = array(
            'inventory',
            'images',
            'purchase_history',
            'purchase_details_'.$login->getId(),
            'receiving_details_'.$login->getId(),
            'output_details_'.$login->getId(),
            'transfer_details_'.$login->getId(),
            'return_details_'.$login->getId(),
            'special_order_details_'.$login->getId(),
            'shipment_store_requests_details_'.$login->getId(),
            'receiving_store_request_details_'.$login->getId(),
            'invoice_details_'.$login->getId(),
            'deposit_details_'.$login->getId()
        );
        
        $data['creado_por'] = $login->getId();
        $data['creado_fecha'] = date('Y-m-d H:i:s');
        
        if(in_array($table, $noCreateDate)){unset($data['creado_por'],$data['creado_fecha']);}       
                
        $select = "INSERT INTO $table";
        $fields = "";
        $values = "";
        foreach ($data as $key => $value) {
            $fields .= "`$key`,";
            $value = trim($value);
            $value = mysqli_real_escape_string(DataBase::getInstance(),$value);
            if($value == '_NULL' || is_null($value) || trim($value)==''){
                $values .= "NULL,";
            }else{
                $values .= "'$value',";
            }
            
        }
        $fields = substr($fields, 0, strlen($fields) - 1);
        $values = substr($values, 0, strlen($values) - 1);
       
        $select = $select . "(" . $fields . ") VALUES (" . $values . ")";      
        $result = $this->query($select);
        
        return $result;
    }
    
    public function getInsertId(){
        return DataBase::getInsertId();
    }
    
    public function setLastInsertId($id){
        $this->lastInsertId = $id;
    }
    
    public function getLastInsertId(){
        return $this->lastInsertId;
    }

    public function update($id, $fields, $table) { 
        $login = new Login();
        $noCreateDate = array(
            'inventory',
            'images',
            'purchase_details_'.$login->getId(),
            'receiving_details_'.$login->getId(),
            'output_details_'.$login->getId(),
            'transfer_details_'.$login->getId(),
            'return_details_'.$login->getId(),
            'special_order_details_'.$login->getId(),
            'shipment_store_requests_details_'.$login->getId(),
            'receiving_store_request_details_'.$login->getId(),
            'invoice_details_'.$login->getId(),
            'deposit_details_'.$login->getId()
        );
        
        
        if(!in_array($table,$noCreateDate)){
            $originData = EntityRepository::getById($id, $table);                        
            $modifications = $this->getUpdatedFields($originData,$fields);           
            
            if($modifications){
                $modificadoPor = $login->getId();
                $modificadoFecha = date('Y-m-d H:i:s');

                $fields['ultima_mod_por'] = $modificadoPor;
                $fields['ultima_mod_fecha'] = $modificadoFecha;              
                $fields['modificaciones'] = $this->getModifications($originData,$modifications);
            }
        }
        
        $select = "UPDATE $table SET ";
        foreach ($fields as $key => $value) {
            $value = trim($value);
            $value = mysqli_real_escape_string(DataBase::getInstance(),$value);
            if($value == '_NULL' || is_null($value) || trim($value)==''){
                $select .= "`$key` = NULL,";
            }else{
                $select .= "`$key` = '$value',";
            }  
        }

        $select = substr($select, 0, strlen($select) - 1);
        $select .= " WHERE id = $id";
        
        //echo $select;exit;
        $result = $this->query($select);            
        return $result;
    }     
        
    public function getUpdatedFields($originData,$newData){
        #regresa un array con los campos que se actualizaron
        $modifications = array();
        //var_dump($newData);exit;
        foreach($originData as $key => $value){
            if(key_exists($key, $newData) && $newData[$key] !== '_NULL' && trim($newData[$key]) != trim($value)){
                $modifications[$key] = $newData[$key];
            }
        }
        if(count($modifications) > 0){
            return $modifications;
        }
        /*Si regreso null marcar Warning en EntityRepository 123*/
        return null;
    }
    
    public function getModifications($originData,$modifications){
        #regresa un array de arrays serializado donde cada array es un campo que se actualizo array(campo=>valorAnterior)
        $login = new Login();
        $modifications = array('fecha'=>date('Y-m-d H:i:s'),'usuario'=>$login->getId(),'modificaciones'=>$modifications);
        if(isset($originData['modificaciones']) && trim($originData['modificaciones'])!==''){
            $originModifications = unserialize($originData['modificaciones']);
            $originModifications[] = $modifications;

            return serialize($originModifications);
        }else{
            return serialize(array($modifications));
        }         
    }
    
    public function updateString($fields,$where,$table) {
        #Obtener tablas que se usan en la consulta update
        #Si solo es una tabla, se hace select con where y se obtiene ids de la tabla que seran actualizados
        #con estos ids se hace getById para obtener dataOrigin
        
        if(!strpos($table, ',')){
            $query = "SELECT * FROM $table WHERE $where";
            $rs = $this->query($query);
            if($rs->num_rows > 0){
                $rowsOrigin = $this->resultToArray($rs);
                $this->startTransaction();
                foreach($rowsOrigin as $row){
                    $result = EntityRepository::update($row['id'],$fields,$table);
                    if(!$result){
                        $this->rollback();
                        return null;
                    }
                }
                $this->commit();
                return true;
            }
            return true;
        }        
    }

    public function getById($id, $table,$selectAux = null) {
        $select = "SELECT * ";
        if($selectAux){$select .= ",$selectAux";}
        
        $select = "$select FROM $table WHERE id = '$id'";
        $result = $this->query($select);

        if ($result->num_rows>0) {
            $set = $this->resultToArray($result);
            return $set[0];
        }

        return null;
    }
    
    public function isUsedInRecord($id,array $buscarEn, $andWhere = null){
        foreach($buscarEn as $key => $value){
            $select = "SELECT id FROM $key WHERE $value = '$id' $andWhere limit 1";
            $result = $this->query($select);
            if($result->num_rows > 0){
                $result = $result->fetch_object();
                return array('id'=>$result->id,'table'=>$key);
            }
        }
        
        return null;
    }
    
    public function query($query){
        $result = DataBase::getInstance()->execute($query);
        return $result;
    }
    
    public function resultToArray($result){
        for ($set = array(); $row = $result->fetch_assoc(); $set[] = $row);
  
        return $set;
    }
    
    public function startTransaction(){
        return $this->query('START TRANSACTION');
    }
    
    public function commit(){
        return $this->query('COMMIT');
    }
    
    public function rollback(){
        return $this->query('ROLLBACK');
    }    
    public function getQueryVarsFromGET($noReturn = array()){
        $queryVars='';
        foreach ($_GET as $clave => $valor){
          if(!in_array($clave,$noReturn)){
          $queryVars .= $clave . "=" . $valor . "&amp;";
        }
      }
        return $queryVars;
    }
    
    public function _rawNumber($data,$inputs){
        foreach($inputs as $value){
            if(isset($data[$value])){
                if(trim($data[$value]) == '' || is_null($data[$value])){$data[$value] = 0;}
                $data[$value] = str_replace(',', '', $data[$value]);
            }
        }
        
        return $data;
    }
}