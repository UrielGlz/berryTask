<?php
class ReceivingRepository extends EntityRepository {

    private $table = 'receivings';
    public $flashmessenger = null;
    private $options = array (
        'id'=>null,
        'document_reference'=>null,
        'type'=>null,
        'reference_id'=>null,
        'store_id_of_document'=>null,
        'date_time' => null,
        'comments'=>null,
        'status'=>null,);
    
    private $options_aux = array(
        'user_name'=>null,
        'status_name'=>null,
        'receiving_date'=>null,
        'token_form'=>null, #Se popula con setOption desde Controller, con post de formulario
        
        /*Informacion de compra*/
        'purchase_date'=>null,
        'vendorName'=>null,
        'reference'=>null,
        'lot'=>null
        
    );      
    
    public function __construct() {
        if(!$this->flashmessenger instanceof FlashMessenger){
            $this->flashmessenger = new FlashMessenger();
        }
    }
    
    public function _getTranslation($text){
        return $this->flashmessenger->_getTranslation($text);
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
    
    public function getId() {
       return $this->options['id'];
    }
    
    public function getType(){
        return $this->options['type'];
    } 
    
    public function getReferenceId(){
        return $this->options['reference_id'];
    } 
    
    public function getUserName(){        
        return $this->options_aux['user_name'];
    }
    
    public function getStatusName(){        
        return $this->options_aux['status_name'];
    }  
    
    public function getComments(){
        return $this->options['comments'];
    }
    
    public function getStatus(){
        return $this->options['status'];
    }       
        
    public function getVendorName(){        
        return $this->options_aux['vendorName'];
    }    
        
    public function getReference(){
        return $this->options_aux['reference'];
    }
    
    public function getLot(){
        return $this->options_aux['lot'];
    }
    
    public function getPurchaseDate(){
        return $this->options_aux['purchase_date'];
    } 
    
    public function getFormatedDate(){
        return $this->options_aux['receiving_date'];
    }
    
    public function getStoreIdForDocument(){
        return $this->options['store_id_of_document'];
    }

    public function getTokenForm(){
        return $this->options_aux['token_form'];
    }

    public function save(array $data, $table = null) {            
        $receivingDetailsTemp = new ReceivingDetailsTempRepository();
        if(!$receivingDetailsTemp->isThereItemsOnReceiving($this->getTokenForm())){
            $this->flashmessenger->addMessage(array('danger'=>'Debe registrar almenos un producto, para guardar la compra.'));
            return null;
        }
        
        $tools = new Tools();    
        $data['date_time'] = $tools->setFormatDateTimeToDB($data['date_time']);
        $data['status'] = '1';  
        $reference = explode('-', $data['document_reference']);
        $data['type'] = $reference[0];
        $data['reference_id']  = $reference[1];
        
        if($data['id']==null || $data['id']==''){unset($data['id']);}
        
        $this->startTransaction();        
        $rs = parent::save($data, $this->table);        
        $idReceiving = $this->getInsertId();
        $this->setLastInsertId($idReceiving);//Para utilizarlo en el Controller action insert
        
        if($rs){
            if($receivingDetailsTemp->saveDetalles($idReceiving,$this->getTokenForm())){   
                $this->commit();
                $receivingDetailsTemp->truncate($this->getTokenForm());
                
                if($data['type'] == 'purchase'){
                     $this->updatePurchaseStatus(array('id'=>$data['reference_id'],'status'=>'1'));  
                }elseif($data['type'] == 'transfer'){
                     $this->updateTransferStatus(array('id'=>$data['reference_id'],'status'=>'1'));  
                }
                             
                return true;
            }
        }        
        
        $this->rollback();    
        $this->flashmessenger->addMessage(array(
            'error'=>$this->_getTranslation('Error. Intenta nuevamente o contacta a tu proveedor de sistemas.')));
        return null;        
    }
    
    public function delete($id, $table = null) {
        $currentData = $this->getById($id);
        if($currentData['status'] == '2'){return true;}
        
        $this->startTransaction();
        $rs = parent::update($id, array('status'=>'2'), $this->table);
        
        if($rs){                        
            if($currentData['type'] == 'purchase'){
                     $this->updatePurchaseStatus(array('id'=>$currentData['reference_id'],'status'=>'2'));  
            }elseif($currentData['type'] == 'transfer'){
                 $this->updateTransferStatus(array('id'=>$currentData['reference_id'],'status'=>'2'));  
            }
            
            $detallesAfectados = $this->getReceivingDetailsSaved($id);
            if(!$this->subInventoryFromReceivingDetalles($detallesAfectados)){
                $this->rollback();
                return null;
            }    
           
            $this->commit();
            return true;
        }

        return null;
    }

    public function update($id, $data, $table = null) {        
        $receivingDetailsTemp = new ReceivingDetailsTempRepository();
        if(!$receivingDetailsTemp->isThereItemsOnReceiving($this->getTokenForm())){
            $this->flashmessenger->addMessage(array('danger'=>'Debe registrar almenos un producto, para guardar la compra.'));
            return null;
        }        
        $tools = new Tools();
        $data['date_time'] = $tools->setFormatDateTimeToDB($data['date_time']);
        $data['status'] = '1';         
        $reference = explode('-', $data['document_reference']);
        $data['type'] = $reference[0];
        $data['reference_id']  = $reference[1];

        if(trim($data['status']) == ''){unset($data['status']);} 
        
        $this->startTransaction();
        $result = parent::update($id, $data, $this->table);        
        if($result){
            if($receivingDetailsTemp->updateDetalles($id,$this->getTokenForm())){                   
                $this->commit();
                $receivingDetailsTemp->truncate($this->getTokenForm());          
                
                if($data['type'] == 'purchase'){
                     $this->updatePurchaseStatus(array('id'=>$data['reference_id'],'status'=>'1'));  
                }elseif($data['type'] == 'transfer'){
                     $this->updateTransferStatus(array('id'=>$data['reference_id'],'status'=>'1'));  
                }
                return true;
            }
        }
        
        $this->rollback();
        return null;
    }
    
    public function updateString($fields, $where, $table = null) {
        return parent::updateString($fields, $where, $this->table);
    }

    public function getById($id, $table = null,$selectAux = null) {
        $result = parent::getById($id, $this->table);
        
        if($result){            
            if($result['type'] == 'purchase'){
                $select = "SELECT r.*,"
                . "DATE_FORMAT(r.date_time,'%m/%d/%Y %h:%i %p')as receiving_date,"
                . "fxGetStatusName(r.status,'Receiving')as status_name, "
                . "fxGetUserName(r.creado_por) as user_name, "
                . "p.reference,"
                . "p.lot,"
                . "DATE_FORMAT(p.date,'%d/%m/%Y')as purchase_date,"
                . "fxGetVendorName(p.vendor) as vendorName,"
                . "p.store_id "
                . "FROM $this->table r, purchases p "
                . "WHERE r.reference_id = p.id AND r.id = '$id'";
            
            }elseif($result['type'] == 'transfer'){
                $select = "SELECT r.*,"
                . "DATE_FORMAT(r.date_time,'%m/%d/%Y %h:%i %p')as receiving_date,"
                . "fxGetStatusName(r.status,'Receiving')as status_name, "
                . "fxGetUserName(r.creado_por) as user_name, "
                . "p.requested_by as reference,"
                . "'' as lot,"
                . "DATE_FORMAT(p.date,'%d/%m/%Y')as purchase_date,"
                . "fxGetStoreName(p.from_store_id) as vendorName,"
                . "p.to_store_id as store_id "
                . "FROM $this->table r, transfers p "
                . "WHERE r.reference_id = p.id AND r.id = '$id'";
            }
        }
        
        
        $result = $this->query($select);

        if ($result->num_rows>0) {
            $set = $this->resultToArray($result);
            return $set[0];
        }

        return false;
    }

    public function isUsedInRecord($id, array $buscarEn = null,$andWhere = null) {
        return null;
        return parent::isUsedInRecord($id, array('compras' => 'id')," AND status = 2 AND (type != 'BienesyServicios' AND type != 'Consigna' AND type != 'MateriaPrima')");
    }
    
    public function crearTablaDetallesForUser(){
        $login = new Login();        
        $query = "CREATE TABLE IF NOT EXISTS receiving_details_".$login->getId()." 
                 (  
                    `token_form` char(50) NOT NULL,
                    `store_id_of_document` int(11) NOT NULL,
                    `added` tinyint(50) NOT NULL,
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `id_detail` int(11) NULL,
                    `id_receiving` int(11) NULL,
                    `id_product` int(11) NOT NULL,
                    `description` varchar(255) NOT NULL,
                    `quantity` double NOT NULL,
                    `location` int(11) NULL,
                    `received` double NULL,
                    `cost` double  NULL,
                    `cost_without_tax` double  NULL,
                    `discount` double  NULL,
                    `discount_type` char(15)  NULL,
                    `discount_amount` double  NULL,
                    `discount_general` double  NULL,
                    `discount_general_type` char(15) NULL,
                    `discount_general_amount` double  NULL,
                    `taxes` int(11)  NULL,
                    `taxes_rate` double  NULL,
                    `taxes_amount` double  NULL,
                    `taxes_included` CHAR(50)  NULL,
                    `amount` double  NULL,
                    `total` double  NULL,
                    `expiration_date` date NULL,
                    PRIMARY KEY (`id`)
                 )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        
       $result = $this->query($query);
    }
    
    public function insertDetalle($data){
        $receivingDetailsTemp = new ReceivingDetailsTempRepository();        
        return $receivingDetailsTemp->save($data);
    }
    
    public function getReceivingDetails($token_form){
        $login = new Login();
        $query = "SELECT v.*,
                    v.id as idDetailTemp,
                    p.code,
                    IF(id_product != 0,v.id_product,v.description)as product,
                    fxGetCategoryDescription(p.category)as category,
                    fxGetBrandDescription(p.brand)as brand,
                    fxGetPresentationDescription(p.presentation)as presentation,
                    p.location as ids_locations
                  FROM receiving_details_".$login->getId()." v LEFT JOIN products p
                  ON v.id_product = p.id
                  WHERE token_form = '$token_form'
                  ORDER BY v.id";
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result);
            return $result;
        }
        
        return null;
    }
    
    public function getReceivingDetailSaved($id){
        $query = "SELECT c.*,
                    p.code as code
                    FROM receiving_details c LEFT JOIN products p ON c.id_product = p.id
                    WHERE c.id = '$id'";
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result)[0];
            return $result;
        }
        
        return null;
    }
    
    public function getReceivingDetailsSaved($id){
        $query = "SELECT c.*,
                    p.code as code,
                    fxGetTaxDescription(c.taxes)as taxName,
                    fxGetPresentationDescription(p.presentation)as presentation_name,
                    fxGetBrandDescription(p.brand)as brand_name,
                    fxGetLocationDescription(c.location)as location_name
                    FROM receiving_details c LEFT JOIN products p ON c.id_product = p.id
                    WHERE c.id_receiving = '$id'";
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result);
            return $result;
        }
        
        return null;
    }
    
    public function getListReceiving(){             
        $store_id_purchase = null;
        $store_id_transfer = null;
        
        $login = new Login();
        if($login->getRole() != '1'){
            $store_id_purchase = " AND find_in_set(store_id,'{$login->getStoreId()}')";
            $store_id_transfer = " AND find_in_set(to_store_id,'{$login->getStoreId()}')";
        }       
        
        $query = "
                SELECT * FROM (
                SELECT 
                r.id,
                'Compra' as type,
                p.id as reference_id,
                p.reference,
                DATE_FORMAT(p.date,'%d/%m/%Y')as date,
                DATE_FORMAT(r.date_time,'%m/%d/%Y %h:%i %p')as receiving_date,
                fxGetVendorName(p.vendor) as vendor,                
                fxGetStatusName(r.`status`,'Receiving')as statusName,
                fxGetStoreName(store_id)as storeName
                FROM receivings r, purchases p
                WHERE  r.reference_id = p.id 
                AND type = 'purchase' 
                $store_id_purchase " 
              . "GROUP BY r.id "
              . "UNION "
              . "SELECT 
                rt.id,
                'Traspaso' as type,
                t.id as reference_id,
                t.requested_by as reference,
                DATE_FORMAT(t.date,'%d/%m/%Y')as date,
                DATE_FORMAT(rt.date_time,'%m/%d/%Y %h:%i %p')as receiving_date,
                fxGetStoreName(t.from_store_id) as vendor,                
                fxGetStatusName(rt.`status`,'Receiving')as statusName,
                fxGetStoreName(to_store_id)as storeName
                FROM receivings rt, transfers t
                WHERE  rt.reference_id = t.id
                AND type = 'transfer' 
                $store_id_transfer " 
              . "GROUP BY t.id) as t ORDER BY date DESC ";

    
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }
        
        return null;
  }
  
   public function getProductById($idProducto){
        $query = "SELECT * FROM products WHERE id = '$idProducto' LIMIT 1";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $this->resultToArray($result);
            return $result[0];
        }        
        return null;
    }    
        
    public function updatePurchaseStatus($options){
        $statusArray = array(
            '1'=>'3',
            '2'=>'2'
        );
        
        $purchaseRepo = new PurchaseRepository();
        $id = $options['id'];
        $status = $statusArray[$options['status']];        
        $currentData = $purchaseRepo->getById($id);
                
        parent::update($id, array('status'=>$status), 'purchases');
        
        $data = $purchaseRepo->getById($id);
        $purchaseRepo->_history(array('id'=>$id,'action'=>'update','currentData'=>$currentData,'newData'=>$data));
    }
    
     public function updateTransferStatus($options){
        $statusArray = array(
            '1'=>'3',
            '2'=>'1'
        );
        
        $id = $options['id'];
        $status = $statusArray[$options['status']];        
                
        parent::update($id, array('status'=>$status), 'transfers');
    }
    
    public function subInventoryFromReceivingDetalles($detallesAfectados){
        $repoInventario = new InventoryRepository();    

        $array = array();
        foreach($detallesAfectados as $detalle){         
            $row = array(                        
                        'id_product'=>$detalle['id_product'],
                        'quantity'=>$detalle['received'],
                        'id_location'=>$detalle['location'],
                        'controller'=>"Recibos-".$detalle['id_receiving']
                    );
            $array[] = $row;
        }

        return $repoInventario->deleteAddInventory($array);
    }
    
    public function getListDocumentsPendingToReceieve($type = null,$id = null){
        $store_id_purchase = null;
        $store_id_transfer = null;
        $idPurchase = '';
        $idTransfer = '';
        
        $login = new Login();
        if($login->getRole() != '1'){
            $store_id_purchase = " AND find_in_set(store_id,'{$login->getStoreId()}')";
            $store_id_transfer = " AND find_in_set(to_store_id,'{$login->getStoreId()}')";
        }       
        
        if($type == 'purchase' && $id != null){$idPurchase = " OR id = '$id'";}
        if($type == 'transfer' && $id != null){$idTransfer = " OR id = '$id'";}
        
        $query = "SELECT * FROM ("
                . "SELECT "
                . "'purchase' as type,"
                . "'Compra' as type_for_label,"
                . "date,"
                . "id,"
                . "fxGetVendorName(vendor) as vendorName "
                . "FROM purchases "
                . "WHERE status = '2' "
                . "$store_id_purchase "
                . "$idPurchase " 
                . "UNION "
                . "SELECT "
                . "'transfer' as type,"
                . "'Traspaso' as type_for_label,"
                . "DATE(date)as date, "
                . "id,"
                . "fxGetStoreName(from_store_id)as vendorName "
                . "FROM transfers "
                . "WHERE status = '1'"
                . "$idTransfer "
                . "$store_id_transfer "
                . ") as t ORDER BY date ASC ";
        
        $result = $this->query($query);
        
        if($result){
            $array = array();
            while($row = $result->fetch_object()){
                $array[$row->type.'-'.$row->id] = $row->type_for_label.' #'.$row->id." - ".$row->vendorName;
            }
            
            return $array;
        }
    }
    
    public function createFilterFecha($options,$campoFecha = null ){
        if(!isset($options['startDate']) && !isset($options['endDate'])){return null;}        
        $startDate = $options['startDate'];
        $endDate = $options['endDate'];
        $fecha = null;
        $tools = new Tools();
        if($startDate!=null){
            $startDate = $tools->setFormatDateToDB($startDate);
            if($endDate!=null){
                $endDate = $tools->setFormatDateToDB($endDate);
                $fecha .=" AND $campoFecha BETWEEN '{$startDate}' AND '{$endDate}' ";
            }else{
                $fecha .=" AND $campoFecha BETWEEN '{$startDate}' AND '{$startDate}' ";
            }
        }elseif($endDate!=null){
            $fecha .=" AND $campoFecha BETWEEN '{$endDate}' AND '{$endDate}' ";
        }
        
        return $fecha;
    }   
}