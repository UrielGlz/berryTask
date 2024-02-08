<?php
class DepositDetailsTempRepository extends EntityRepository {

    private $table = 'deposit_details_';
    
    private $options = array(
        'token_form'=>null,
        'id_detail'=>0,
        'id_deposit'=>0,
        'sale_date'=>null,
        'sale_date_final'=>null,
        'sale_total_cash'=>null,
        'sale_comments'=>null
    );
    
    private $options_aux = array(
        'type'=>null
    );
    
    /*Input double y que no son hide*/
    public $inputs_double = array(
        'sale_total_cash',
    );
    
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
    
    public function restartOptions(){
        foreach ($this->options as $option => $value){
            $this->options[$option] = null;
        }
    }
    
    // Guarda en temporal
    public function save(array $data, $table = null) {                    
        $idDetailTemp = $data['idDetailTemp'];
        
        $this->setOptions($data);
        $data = $this->getOptions();
        unset($data['id_detail'],$data['id_deposit']);
        
        if(isset($idDetailTemp) && trim($idDetailTemp)!= ''){
            $result = $this->getById($idDetailTemp);
            if($result){                
                return $this->updateTemp($idDetailTemp,$data);
            }
        }
        
        return $this->saveTemp($data);
    }
    
    public function saveTemp($data){       
        $tools = new Tools();
        $data['sale_date'] = $tools->setFormatDateToDB($data['sale_date']);
        $data['sale_date_final'] = $tools->setFormatDateToDB($data['sale_date_final']);
        parent::save($data, $this->table);
    }
    
    public function updateTemp($id,$data){                    
        $tools = new Tools();
        $data['sale_date'] = $tools->setFormatDateToDB($data['sale_date']);
        $data['sale_date_final'] = $tools->setFormatDateToDB($data['sale_date_final']);
        parent::update($id,$data, $this->table);
    }
    
    // Guarda en tabla comprasdetalles
    public function saveDetalles($idCompra,$tokenForm){
        $query = "SELECT * FROM $this->table WHERE token_form = '$tokenForm'";
        $result = $this->query($query);
        if($result->num_rows > 0){
           
            $result = $this->resultToArray($result);
            $tools = new Tools();
            foreach($result as $data){              
                $this->setOptions($data);
                $data = $this->getOptions();            
                $data['id_deposit'] = $idCompra;              
                
                unset($data['token_form'],$data['id_detail']);               
                if(!parent::save($data, 'deposit_details')){                    
                    return null;
                }
            }
            return true;
        }
        return true;
    } 
    
    public function updateDetalles($idExpense,$tokenForm){                
        $query = "SELECT id FROM deposit_details WHERE id_deposit = '$idExpense'";
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
            $tools = new Tools();
            
            foreach($result as $data){
                $idDetalle = $data['id_detail'];
                
                $this->setOptions($data);
                $data = $this->getOptions();     
                $this->restartOptions();

                $data['id_deposit'] = $idExpense;   
                
                unset($data['token_form'],$data['id_detail']);               
                
                if(!$idDetalle){
                    if(!parent::save($data, 'deposit_details')){
                        return null;
                    }                    
                }else{
                    #contiene los idDetalles de la compra original que se mantendran, lo que no esten aqui se eliminaran.
                    $idsDetalles[] = $idDetalle;
                    if(!parent::update($idDetalle,$data, 'deposit_details')){
                        return null;
                    }
                }  
            }
        }else{
            $this->flashmessenger->addMessage(array('danger'=>'Error 26052021.Error al tratar de editar Intente nuevamente.'));
            header("Location: Deposit.php?action=edit&id=$idExpense");
            exit;
        }       
        
        #Se eliminan ids de tabla comprasdetalls que ya no estan en $idsDetalles (este array contiene los ids que permanececieron en la compra despues del update)                
        if($detallesOrigin){
            foreach ($detallesOrigin as $detalle){
                if(!in_array($detalle['id'], $idsDetalles)){
                    if(!parent::delete($detalle['id'], 'deposit_details')){
                       return null;
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
    
    public function setDepositsDetailsById($idExpense,$tokenForm){
        $query = "INSERT INTO ".$this->table." (
                    id_detail,
                    token_form,
                    id_deposit,
                    sale_date,
                    sale_date_final,
                    sale_total_cash,
                    sale_comments)
                    
                    SELECT 
                    id,
                    '$tokenForm',
                    id_deposit,
                    sale_date,
                    sale_date_final,
                    sale_total_cash,
                    sale_comments
                    FROM deposit_details 
                    WHERE id_deposit = '$idExpense'";
        
        $result = $this->query($query);
        if($result){
            return true;
        }
        
        return null;
    }

    public function getById($id, $table = null,$selectAux = null) {
        return parent::getById($id, $this->table,$selectAux);
    }
    
    public function isThereItemsOnDeposit($tokenForm){
        $query = "SELECT count(id) as rows FROM ".$this->table." WHERE token_form = '$tokenForm'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $result->fetch_object();
            if($result->rows <= 0){
                return null;
            }
            return true;
        }
        return null;
    }
    
    public function getDepositDetails($token_form){
        $login = new Login();
        $query = "SELECT v.*,
                    v.id as idDetailTemp,
                    DATE_FORMAT(v.sale_date,'%m/%d/%Y')as sale_date,
                    DATE_FORMAT(v.sale_date_final,'%m/%d/%Y')as sale_date_final
                  FROM deposit_details_".$login->getId()." v 
                  WHERE token_form = '$token_form'
                  ORDER BY v.id";
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result);
            return $result;
        }
        
        return null;
    }
}