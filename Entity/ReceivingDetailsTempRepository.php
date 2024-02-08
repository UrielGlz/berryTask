<?php
class ReceivingDetailsTempRepository extends EntityRepository {

    private $table = 'receiving_details_';
    
    private $options = array(
        'token_form'=>null,
        'store_id_of_document'=>null, /*Auxiliar solo se usa en tempral*/
        'added'=>null,
        'id_detail'=>0,
        'id_receiving'=>0,
        'id_product'=>null,
        'description'=>null,
        'quantity'=>null,
        'received'=>null,
        'location'=>null,
        'cost'=>null,
        'cost_without_tax'=>null,
        'discount'=>null,
        'discount_type'=>null,
        'discount_amount'=>0,
        'discount_general'=>null,
        'discount_general_type'=>null,
        'discount_general_amount'=>0,
        'taxes'=>0,
        'taxes_rate'=>0,
        'taxes_amount'=>0,
        'taxes_included'=>null,
        'amount'=>0,
        'total'=>0,
        'expiration_date'=>null,
    );
    
    private $options_aux = array();
    
    public function __construct(){
        $login = new Login();
        $this->table = $this->table.$login->getId();
    }
    
    public function getTableName(){
        return $this->table;
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
        $notNullNoEmpty = array('discount','discount_general');
        foreach($notNullNoEmpty as $key => $value){
            if(trim($this->options[$value]) == '' || is_null($this->options[$value])){
                $this->options[$value] = 0;
            }
        }
        
        if(isset($this->options['expiration_date']) && trim($this->options['expiration_date']) == ''){
            unset($this->options['expiration_date']);
        }
        
        return $this->options;
    }     
    
    // Guarda en temporal
    public function save(array $data, $table = null) {
        $tools = new Tools();        
        $data['expiration_date'] = $tools->setFormatDateToDB($data['expiration_date']);        
        $idDetailTemp = $data['idDetailTemp'];
        
        $this->setOptions($data);
        $data = $this->getOptions();
        unset($data['id_detail'],$data['id_receiving']);
                
        if(isset($idDetailTemp) && trim($idDetailTemp)!= ''){
            $result = $this->getById($idDetailTemp);
            if($result){                
                return $this->updateTemp($idDetailTemp,$data);
            }
        }
        
        return $this->saveTemp($data);
    }
    
    public function saveTemp($data){       
        $data['cost_without_tax'] = $data['cost'];
        $repoProduct = new ProductRepository();  
        $dataTax = $repoProduct->getDataTaxesById($data['taxes']);             
        
        $discount_amount = 0;
        $discount_general_amount = 0;
        $precioWithoutTax = $data['cost'];
        
        if($data['taxes_included'] === 'si'){
            $costUnitWithTax = $data['cost'];
            $porcent = 1 + $dataTax['rate']/100;
            $precioWithoutTax = round($costUnitWithTax/$porcent,4,PHP_ROUND_HALF_UP);
            $data['cost_without_tax'] -= round($precioWithoutTax * $dataTax['rate']/100,4,PHP_ROUND_HALF_UP);
        }
        
        $subtotal = round($precioWithoutTax * $data['quantity'],4,PHP_ROUND_HALF_UP);
        $importe = $subtotal;
        
        #Descuento aplicado a item
        if(trim($data['discount']) !== '' && !is_null($data['discount'])){
            if($data['discount_type'] == 'porcentaje'){
                $discount_amount = round($importe * ($data['discount']/100),4,PHP_ROUND_HALF_UP);
                $importe = $importe - $discount_amount;
            }elseif($data['discount_type'] == 'monto'){
                $discount_amount = $data['discount'];
                $importe = $importe - $data['discount'];     
            }
        }        
        
        #Descuento aplicado a compra
        if($data['discount_general_type'] == 'porcentaje'){
            $discount_general_amount = round($importe * ($data['discount_general']/100),4,PHP_ROUND_HALF_UP);
            $importe = $importe - $discount_general_amount;
        }elseif($data['discount_general_type'] == 'monto'){
            $discount_general_amount = $data['discount_general'];
            $importe = $importe - $data['discount_general'];
        }
        
        $taxes_amount =  round($importe * ($dataTax['rate']/100),4,PHP_ROUND_HALF_UP);
        
        $data['discount_amount'] =  round($discount_amount,2,PHP_ROUND_HALF_UP);
        $data['discount_general_amount'] = round($discount_general_amount,2,PHP_ROUND_HALF_UP);
        $data['taxes_rate'] = $dataTax['rate'];
        $taxes_amount = round($taxes_amount,2,PHP_ROUND_HALF_UP);
        $data['taxes_amount'] = $taxes_amount;
        $importe = round($importe,2,PHP_ROUND_HALF_UP);
        $data['amount'] = $importe;
        $data['total'] = $importe + $taxes_amount;
        
        $data['received'] = $data['quantity'];
        
        /*Lo eliminamos porque en el fomrulario para agregar producto no esta este campo, cuando se lista en la tabla se pone por default la locacion asignada como 'default'*/
        unset($data['location']);
        parent::save($data, $this->table);
    }
    
    public function updateTemp($id,$data){
        $data['cost_without_tax'] = $data['cost'];
        $repoProduct = new ProductRepository();  
        $dataTax = $repoProduct->getDataTaxesById($data['taxes']);             
        
        $discount_amount = 0;
        $discount_general_amount = 0;
        
        if($data['taxes_included'] === 'si'){
            $costUnitWithTax = $data['cost'];
            $porcent = 1 + $dataTax['rate']/100;
            $precioWithoutTax = round($costUnitWithTax/$porcent,4,PHP_ROUND_HALF_UP);
            $data['cost_without_tax'] -= round($precioWithoutTax * $dataTax['rate']/100,2,PHP_ROUND_HALF_UP);
        }
        
        $subtotal = round($data['cost_without_tax'] * $data['quantity'],4,PHP_ROUND_HALF_UP);
        $importe = $subtotal;
        
        #Descuento aplicado a item
        if(trim($data['discount']) !=='' && !is_null($data['discount'])){ 
            if($data['discount_type'] == 'porcentaje'){
                $discount_amount = round($importe * ($data['discount']/100),4,PHP_ROUND_HALF_UP);
                $importe = $importe - $discount_amount;
            }elseif($data['discount_type'] == 'monto'){
                $discount_amount = $data['discount'];
                $importe = $importe - $data['discount'];     
                 
            }
        }        
        
        #Descuento aplicado a compra
        if($data['discount_general_type'] == 'porcentaje'){
            $discount_general_amount = round($importe * ($data['discount_general']/100),4,PHP_ROUND_HALF_UP);
            $importe = $importe - $discount_general_amount;
        }elseif($data['discount_general_type'] == 'monto'){
            $discount_general_amount = $data['discount_general'];
            $importe = $importe - $data['discount_general'];
        }
        
        $taxes_amount =  round($importe * ($dataTax['rate']/100),4,PHP_ROUND_HALF_UP);
        
        $data['discount_amount'] =  round($discount_amount,2,PHP_ROUND_HALF_UP);
        $data['discount_general_amount'] = round($discount_general_amount,2,PHP_ROUND_HALF_UP);
        $data['taxes_rate'] = $dataTax['rate'];
        $taxes_amount = round($taxes_amount,2,PHP_ROUND_HALF_UP);
        $data['taxes_amount'] = $taxes_amount;
        $importe = round($importe,2,PHP_ROUND_HALF_UP);
        $data['amount'] = $importe;
        $data['total'] = $importe + $taxes_amount;
        
        parent::update($id,$data, $this->table);
    }
    
    public function setGeneralDiscount($discount){
       $query = "SELECT * FROM $this->table";
       $result = $this->query($query);
       
       if($result->num_rows > 0){
           $result = $this->resultToArray($result);
           foreach($result as $row){
               $this->setOptions($row);
               $data = $this->getOptions();
               $data['discount_general'] = $discount;
               $this->updateTemp($row['id'],$data);
           }           
       }
       return null;
    }
    
    // Guarda en tabla comprasdetalles
    public function saveDetalles($idReceiving,$tokenForm){
        $query = "SELECT * FROM $this->table WHERE token_form = '$tokenForm'";
        $result = $this->query($query);
        if($result->num_rows > 0){           
            $result = $this->resultToArray($result);
            
            $tools = new Tools();
            $repoInventarory = new InventoryRepository();
            
            foreach($result as $data){              
                $this->setOptions($data);
                $data = $this->getOptions();            
                $data['id_receiving'] = $idReceiving;
                if(isset($data['expiration_date'])){$data['expiration_date'] = $tools->setFormatDateToDB($data['expiration_date']);}                
                
                unset($data['token_form'],$data['store_id_of_document'],$data['id_detail']);  
                
                if(parent::save($data, 'receiving_details')){                    
                    if(!$repoInventarory->addInventory(array(
                        'id_product'=>$data['id_product'],
                        'quantity'=>$data['received'],
                        'id_location'=>$data['location'],
                        'controller'=>"Recibos-$idReceiving"))){
                        
                        return null;
                    }
                }
            }
            return true;
        }
        return true;
    } 
    
    public function updateDetalles($idReceiving,$tokenForm){                
        $query = "SELECT id,id_product,quantity,received,location FROM receiving_details WHERE id_receiving = '$idReceiving'";
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
            $repoInventarory = new InventoryRepository();  
        
            $result = $this->resultToArray($result);
            $tools = new Tools();
            
            foreach($result as $data){
                $idDetalle = $data['id_detail'];
                
                $this->setOptions($data);
                $data = $this->getOptions();            
                $data['id_receiving'] = $idReceiving;   
                
                if(isset($data['expiration_date'])){
                    if($data['expiration_date'] == ''){unset($data['expiration_date']);}
                    else{$data['expiration_date'] = $tools->setFormatDateToDB($data['expiration_date']);}
                }
                
                unset($data['token_form'],$data['store_id_of_document'],$data['id_detail']);               
                
                if(!isset($idDetalle)){
                    if(parent::save($data, 'receiving_details')){
                        if(!$repoInventarory->addInventory(array(
                            'id_product'=>$data['id_product'],
                            'quantity'=>$data['received'],
                            'id_location'=>$data['location'],
                            'controller'=>"Recibos-$idReceiving"))){
                            return null;
                        }
                    }                    
                }else{
                    #contiene los idDetalles de la compra original que se mantendran, lo que no esten aqui se eliminaran.
                    $currentData = $detallesOrigin[$idDetalle];
                    $idsDetalles[] = $idDetalle;
                    
                    if(parent::update($idDetalle,$data, 'receiving_details')){
                        if(!$repoInventarory->updateAddInventory(array(
                            'id_product'=>$data['id_product'],
                            'current_quantity'=>$currentData['received'],
                            'new_quantity'=>$data['received'],
                            'current_id_location'=>$currentData['location'],
                            'new_id_location'=>$data['location'],
                            'controller'=>"Recibos-$idReceiving"))){
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
                    if(parent::delete($detalle['id'], 'receiving_details')){
                       if(!$repoInventarory->subInventory(array(
                            'id_product'=>$detalle['id_product'],
                            'quantity'=>$detalle['received'],
                            'id_location'=>$detalle['location'],
                            'controller'=>"Recibos-$idReceiving"))){
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
    
    public function setReceivingDetailsById($idReceiving,$storeIdOfDocument,$tokenForm){
        
        $query = "INSERT INTO ".$this->table." (
                    id_detail,
                    token_form,
                    store_id_of_document,
                    added,
                    id_receiving,
                    id_product,
                    description,
                    quantity,
                    location,
                    received,
                    cost,
                    cost_without_tax,
                    discount,
                    discount_type,
                    discount_amount,
                    discount_general,
                    discount_general_type,
                    discount_general_amount,
                    taxes,
                    taxes_rate,
                    taxes_amount,
                    taxes_included,
                    amount,
                    total,
                    expiration_date)
                    
                    SELECT 
                    id,
                    '$tokenForm',
                    '$storeIdOfDocument',
                    added,
                    id_receiving,
                    id_product,
                    description,
                    quantity,
                    location,
                    received,
                    cost,
                    cost_without_tax,
                    discount,
                    discount_type,
                    discount_amount,
                    discount_general,
                    discount_general_type,
                    discount_general_amount,
                    taxes,
                    taxes_rate,
                    taxes_amount,
                    taxes_included,
                    amount,
                    total,
                    expiration_date
                    FROM receiving_details 
                    WHERE id_receiving = '$idReceiving'";
        
        $result = $this->query($query);
        if($result){
            return true;
        }
        
        return null;
    }
    
    public function setReceivingDetailsByIdPurchase($idPurchase,$tokenForm){
        $this->query("DELETE FROM $this->table WHERE token_form = '$tokenForm'");
        
        $purchaseRepo = new PurchaseRepository();
        $purchaseData = $purchaseRepo->getById($idPurchase);
        
        $storeRepo = new StoreRepository();
        $storeData = $storeRepo->getById($purchaseData['store_id']);
        
        $query = "INSERT INTO ".$this->table." (
                    token_form,
                    store_id_of_document,
                    added,
                    id_product,
                    description,
                    quantity,
                    location,
                    cost,
                    cost_without_tax,
                    discount,
                    discount_type,
                    discount_amount,
                    discount_general,
                    discount_general_type,
                    discount_general_amount,
                    taxes,
                    taxes_rate,
                    taxes_amount,
                    taxes_included,
                    amount,
                    total,
                    expiration_date)
                    
                    SELECT 
                    '$tokenForm',
                    '{$purchaseData['store_id']}',
                    '0',
                    id_product,
                    description,
                    quantity,
                    '{$storeData['default_location']}',
                    cost,
                    cost_without_tax,
                    discount,
                    discount_type,
                    discount_amount,
                    discount_general,
                    discount_general_type,
                    discount_general_amount,
                    taxes,
                    taxes_rate,
                    taxes_amount,
                    taxes_included,
                    amount,
                    total,
                    expiration_date
                    FROM purchase_details 
                    WHERE id_purchase = '$idPurchase'";
        
        $result = $this->query($query);
        if($result){
            return true;
        }
        
        return null;
    }
    
    public function setReceivingDetailsByIdTransfer($idTransfer,$tokenForm){
        $this->query("DELETE FROM $this->table WHERE token_form = '$tokenForm'");
        
        $transferRepo = new TransferRepository();
        $transferData = $transferRepo->getById($idTransfer);
        
        $storeRepo = new StoreRepository();
        $storeData = $storeRepo->getById($transferData['to_store_id']);
        
        $query = "INSERT INTO ".$this->table." (
                    token_form,
                    store_id_of_document,
                    added,
                    id_product,
                    description,
                    quantity,
                    location)
                    
                    SELECT 
                    '$tokenForm',
                    '{$transferData['to_store_id']}',
                    '0',
                    id_product,
                    fxGetProductName(id_product)as description,
                    quantity,
                    '{$storeData['default_location']}'
                    FROM transfer_details 
                    WHERE id_transfer = '$idTransfer'";
        
        $result = $this->query($query);
        if($result){
            return true;
        }
        
        return null;
    }

    public function getById($id, $table = null,$selectAux = null) {
        return parent::getById($id, $this->table,$selectAux);
    }
    
     public function isThereItemsOnReceiving($tokenForm){
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
}