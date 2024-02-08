<?php
class InvoiceDetailsTempRepository extends EntityRepository {

    private $table = 'invoice_details_';
    public $flashmessenger = null;
    private $_not_save = null;
    private $options = array (
        'type'=>null,
        'id_product'=>null,
        'descripcion'=>null,
        'description_details'=>null,       
        'quantity'=>null,
        'price'=>null,
        'discount' =>null,
        'discount_type'=>null,
        'discount_amount'=>null,
        'discount_general'=>null,
        'discount_general_type'=>null,
        'discount_general_amount'=>null,
        'taxes'=>null,
        'taxes_rate'=>null,
        'taxes_amount'=>null,
        'token_form'=>null,
        'amount'=>null,
        'total'=>null
    );
    
    public $fields_to_get_qb  = array(
        'id_product'=>null,
        'id_variety'=>null,
        'id_color'=>null,
        'id_madurity'=>null,
        'id_size'=>null,
        'id_packing'=>null,
        'weight'=>null,
        'id_brand'=>null,
        'id_quality'=>null
    );
    
     /*Input double y que no son hide*/
    public $inputs_double = array(
        'quantity','price','discount','discount_amount','discount_general','discount_general_amount','amount','total','discount_items,taxes_rate,taxes_amount'
    );
    
    public function __construct(){
        $login = new Login();
        $this->table = $this->table.$login->getId();
        
        if(!$this->flashmessenger instanceof FlashMessenger){
           $this->flashmessenger = new FlashMessenger();
        }
    }
    
    public function setOptions($data){
        foreach ($this->options as $option => $value){
            if(isset($data[$option])){
              $this->options[$option] = $data[$option];
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
    
    public function _getTranslation($text){
        return $this->flashmessenger->_getTranslation($text);
    }
   
    // Guarda en temporal
    public function save(array $data, $table = null) {   
        if(key_exists('not_save', $data)){$this->_not_save = true;}
        $idDetalleTemp = null;        
        if(isset($data['idDetailTemp']) && trim($data['idDetailTemp'])!= ''){$idDetalleTemp = $data['idDetailTemp'];}        
        
        $this->setOptions($data);        
        $data = $this->getOptions();      
        $data = $this->_rawNumber($data, $this->inputs_double);
        
        if($idDetalleTemp){        
            $result = $this->getById($idDetalleTemp);
            if($result){
                return $this->updateTemp($idDetalleTemp,$data);
            }
        }        
        
        return $this->saveTemp($data);
    }
    
    public function saveTemp($data){       
        $repoProduct = new ProductRepository();     
        $dataTax = 0;
        $dataTax = $repoProduct->getDataTaxesById($data['taxes']);
                 
        
        $discount_amount = 0;        
        $subtotal = round($data['price'] * $data['quantity'],4,PHP_ROUND_HALF_UP);
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
        
        $taxes_amount =  round($importe * ($dataTax['rate']/100),4,PHP_ROUND_HALF_UP);        
        $data['discount_amount'] =  round($discount_amount,2,PHP_ROUND_HALF_UP);
        $data['taxes_rate'] = $dataTax['rate'];
        $taxes_amount = round($taxes_amount,2,PHP_ROUND_HALF_UP);
        $data['taxes_amount'] = $taxes_amount;
        $importe = round($importe,2,PHP_ROUND_HALF_UP);
        $data['amount'] = $importe;
        $data['total'] = $importe + $taxes_amount;
        
        $this->setOptions($data);
        $data = $this->getOptions();  
        
         if($this->_not_save){
            return $data;
        }else{
            return parent::save($data, $this->table);
        }        
    }
    
    public function updateTemp($idDetalleTemp,$data){         
        return $this->update($idDetalleTemp, $data,$this->table);
    }       
        
    public function delete($id, $table = null) {
        return parent::delete($id, $this->table);
    }   
    
    // Guarda en tabla invoice_details
    public function saveDetalles($idManifest,$tokenForm){
        $query = "SELECT * FROM $this->table WHERE token_form = '$tokenForm'";
        $result = $this->query($query);
        if($result->num_rows > 0){            
            $result = $this->resultToArray($result);
            
            foreach($result as $data){
                $this->setOptions($data);
                $data = $this->getOptions(); 
                $this->restartOptions();
                $data['id_invoice'] = $idManifest;                      
                unset($data['token_form']); 
                
                parent::save($data, 'invoice_details');
            }            
        }
        return true;
    }
    
    public function updateDetalles($idInvoice,$tokenForm){
        $query = "SELECT id FROM invoice_details WHERE id_invoice = '$idInvoice'";
        $result = $this->query($query);
        $detallesOrigin = null;            
        $idsDetalles = array(); 
        
        if($result->num_rows > 0){
            $detallesOrigin = $this->resultToArray($result);
        }

        $query = "SELECT * FROM $this->table WHERE token_form = '$tokenForm'";
        $result = $this->query($query);

        if($result->num_rows > 0){            
            $result = $this->resultToArray($result);
            
            foreach($result as $data){
                $idDetalle = $data['id_detalle'];
                
                $this->setOptions($data);
                $data = $this->getOptions(); 
                $this->restartOptions();
                $data['id_invoice'] = $idInvoice;
                unset($data['token_form']); 
                
                if(!$idDetalle){ 
                    parent::save($data, 'invoice_details');
                    
                }else{
                    #contiene los idDetalles de la compra original que se mantendran, lo que no esten aqui se eliminaran.
                    $idsDetalles[] = $idDetalle;
                    $rs = parent::update($idDetalle,$data, 'invoice_details');
               
                }                       
            }
        }
        
        #Se eliminan ids de tabla comprasdetalls que ya no estan en $idsDetalles (este array contiene los ids que permanececieron en la compra despues del update)                
        if($detallesOrigin){
            $entityRepository = new EntityRepository();
            foreach ($detallesOrigin as $detalle){
                if(!in_array($detalle['id'], $idsDetalles)){
                    $entityRepository->delete($detalle['id'], 'invoice_details');
                }
            }
        }              
                   
        
            
        return true;
    } 
    
    public function truncate($tokenForm){
        $query = "DELETE FROM ".$this->table." WHERE token_form = '$tokenForm'";
        $result = $this->query($query);
        
        if($result){
            return true;
        }
        
        return null;
    }
    
    public function setManifestDetallesById($idManifest,$tokenForm){
        $query = "INSERT INTO ".$this->table." (token_form,id_detalle,id_invoice,type,id_product,descripcion,description_details,quantity,price,discount,discount_type,discount_amount,discount_general,discount_general_type,discount_general_amount,taxes,taxes_rate,taxes_amount,amount,total)
                    SELECT '$tokenForm',id,id_invoice,type,id_product,descripcion,description_details,quantity,IFNULL(price,0),discount,discount_type,discount_amount,discount_general,discount_general_type,discount_general_amount,taxes,taxes_rate,taxes_amount,amount,total
                    FROM invoice_details WHERE id_invoice = '$idManifest'";
        
        $result = $this->query($query);
        if($result){
            return true;
        }
        
        return null;
    }

    public function getById($id, $table = null,$selectAux = null) {
        return parent::getById($id, $this->table,$selectAux);
    }
    
     public function getInvoiceDetalles($tokenForm){
        $login = new Login();
        $query = "SELECT v.*,
                v.id as idDetalleTemp,
                fxGetTaxDescription(taxes)as taxName                
                FROM invoice_details_".$login->getId()." v 
                WHERE token_form = '$tokenForm'
                AND `type` = 'product'";
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result);
            return $result;
        }
        
        return null;
    }
    
    public function applyGeneralDiscountToItems($options){
        $options = parent::_rawNumber($options,$this->inputs_double);
        if($options['subtotal'] <= 0){return true;}
        
        /* Subtotal = importe sin descuentos */
        $discountType = $options['discount_general_type'];
        $discount = $options['discount_general'];
        $subtotal = $options['subtotal'] - $options['discount_items'];
        $tokenForm = $options['token_form'];
        
        if($discountType == 'percent'){$percent = $discount;}
        elseif($discountType == 'amount'){$percent = ($discount*100)/$subtotal;}        
        
        $repoProduct = new ProductRepository();         
        $details = $this->getInvoiceDetalles($tokenForm);
        
        foreach($details as $detail){     
            $dataTax = 0;
            if($detail['type'] == 'good_and_services'){$dataTax = $repoProduct->getDataTaxesById($detail['taxes']);}            
            $subtotal = ($detail['quantity'] * $detail['price']) - $detail['discount_amount'];
            $discountAmount = ($subtotal * ($percent/100));
            $subtotal -= $discountAmount;
            
            $taxes_amount =  round($subtotal * ($dataTax['rate']/100),4,PHP_ROUND_HALF_UP);
            
            $array = array(
                'discount_general_type'=>$discountType,
                'discount_general'=>$discount,
                'discount_general_amount'=>$discountAmount,
                'taxes_amount'=>$taxes_amount,
                'amount'=>$subtotal,
                'total'=>$subtotal + $taxes_amount
            );
            
            parent::update($detail['id'], $array, $this->table);
        }
        
        return true;
    }
   
}