<?php
class OutputDetailsTempRepository extends EntityRepository {

    private $table = 'output_details_';
    
    private $options = array(
        'token_form'=>null,
        'id_detail'=>0,
        'id_output'=>0,
        'id_product'=>null,
        'quantity'=>null,
        'location'=>null
    );
    
    private $options_aux = array();
    
    public function __construct(){
        $login = new Login();
        $this->table = $this->table.$login->getId();
    }
    
    public function setOptions($data){
      foreach ($this->options as $option => $value){
          if(isset($data[$option])){
            $this->options[$option] = $data[$option];
          }
      }
      
      foreach ($this->options_aux as $option => $value){
            if(isset($data[$option])){
              $this->options_aux[$option] = $data[$option];
            }
        }
    }
    
    public function getOptions(){        
        return $this->options;
    }     
    
    // Guarda en temporal
    public function save(array $data, $table = null) {
        $tools = new Tools();            
        $idDetailTemp = $data['idDetailTemp'];
        
        $this->setOptions($data);
        $data = $this->getOptions();
        unset($data['id_detail'],$data['id_output']);
                
        if(isset($idDetailTemp) && trim($idDetailTemp)!= ''){
            $result = $this->getById($idDetailTemp);
            if($result){                
                return $this->updateTemp($idDetailTemp,$data);
            }
        }
        
        $query = "SELECT * FROM $this->table WHERE id_product = '{$data['id_product']}' AND location = '{$data['location']}' AND token_form = '{$data['token_form']}' ";
        $result = $this->query($query);
        if($result->num_rows > 0){
            $result = $this->resultToArray($result)[0];
            $data['quantity'] += $result['quantity'];
            return $this->updateTemp($result['id'], $data);
        }
        
        return $this->saveTemp($data);
    }
    
    public function saveTemp($data){                             
        parent::save($data, $this->table);
    }
    
    public function updateTemp($id,$data){        
        parent::update($id,$data, $this->table);
    }
    
    // Guarda en tabla comprasdetalles
    public function saveDetalles($idOutput,$tokenForm){
        $query = "SELECT * FROM $this->table WHERE token_form = '$tokenForm'";
        $result = $this->query($query);
        if($result->num_rows > 0){
           
            $result = $this->resultToArray($result);
            $repoInventarory = new InventoryRepository();
            
            foreach($result as $data){              
                $this->setOptions($data);
                $data = $this->getOptions();            
                $data['id_output'] = $idOutput;               
                
                unset($data['token_form'],$data['id_detail']);               
                if(parent::save($data, 'output_details')){                    
                    if(!$repoInventarory->subInventory(array(
                        'id_product'=>$data['id_product'],
                        'quantity'=>$data['quantity'],
                        'id_location'=>$data['location'],
                        'controller'=>"Salidas-$idOutput"))){
                        
                        return null;
                    }
                }
            }
            return true;
        }
        return true;
    } 
    
    public function updateDetalles($idOutput,$tokenForm){                
        $query = "SELECT id,id_product,quantity,location FROM output_details WHERE id_output = '$idOutput'";
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
            $idsDetalles = array();        
            $result = $this->resultToArray($result);
            $repoInventarory = new InventoryRepository();
            
            foreach($result as $data){
                $idDetalle = $data['id_detail'];
                
                $this->setOptions($data);
                $data = $this->getOptions();            
                $data['id_output'] = $idOutput;   
                
                unset($data['token_form'],$data['id_detail']);               
                
                if(!$idDetalle){
                    if(parent::save($data, 'output_details')){
                        if(!$repoInventarory->subInventory(array(
                            'id_product'=>$data['id_product'],
                            'quantity'=>$data['quantity'],
                            'id_location'=>$data['location'],
                            'controller'=>"Salidas-$idOutput"))){

                            return null;
                        }
                    }                    
                }else{
                    #contiene los idDetalles de la compra original que se mantendran, lo que no esten aqui se eliminaran.
                    $currentData = $detallesOrigin[$idDetalle];
                    $idsDetalles[] = $idDetalle;
                    if(parent::update($idDetalle,$data, 'output_details')){
                        if(!$repoInventarory->updateSubInventory(array(
                            'id_product'=>$data['id_product'],
                            'current_quantity'=>$currentData['quantity'],
                            'new_quantity'=>$data['quantity'],
                            'current_id_location'=>$currentData['location'],
                            'new_id_location'=>$data['location'],
                            'controller'=>"Salidas-$idOutput"))){
                             return null;
                         }
                    }
                }  
            }
        }

        #Se eliminan ids de tabla comprasdetalls que ya no estan en $idsDetalles (este array contiene los ids que permanececieron en la compra despues del update)                
        if($detallesOrigin){
            foreach ($detallesOrigin as $detalle){
                if(!in_array($detalle['id'], $idsDetalles)){
                    if(parent::delete($detalle['id'], 'output_details')){
                        if(!$repoInventarory->addInventory(array(
                            'id_product'=>$detalle['id_product'],
                            'quantity'=>$detalle['quantity'],
                            'id_location'=>$data['location'],
                            'controller'=>"Salidas-$idOutput"))){
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
    
    public function setOutputDetailsById($idOutput,$tokenForm){
        $query = "INSERT INTO ".$this->table." (
                    id_detail,
                    token_form,
                    id_output,
                    id_product,
                    quantity,
                    location)
                    
                    SELECT 
                    id,
                    '$tokenForm',
                    id_output,
                    id_product,
                    quantity,
                    location
                    FROM output_details 
                    WHERE id_output = '$idOutput'";
        
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