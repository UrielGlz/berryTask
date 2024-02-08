<?php
class ReceivingStoreRequestRepository extends EntityRepository {

    private $table = 'receiving_store_requests';
    public $flashmessenger = null;        
    private $options = array (
        //'store_id'=>null, /**/
        'num_shipment'=>null,
        'date'=>null,
        'comments'=>null,
        'shipment_date'=>null,
        'shipment_comments'=>null,        
        'status'=>null,
        'received_incomplete'=>0);
    
    private $options_aux = array(
        'dateShipmentFormated'=>null,
        'dateFormated'=>null,
        'statusName'=>null,
        'userName'=>null,
        'id_store_request'=>null,
        'storeName'=>null,        
    );
    
    public function __construct() {
        if(!$this->flashmessenger instanceof FlashMessenger){
            $this->flashmessenger = new FlashMessenger();
        }
    }
  
    public function _getTranslation($text){
        return $this->flashmessenger->_getTranslation($text);
    }
    
    public function setOptions($data){        //var_dump($data);exit;
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
    
    public function getTableName(){
        return $this->table;
    }

    public function getOptions(){
        return $this->options;
    }
    
     public function getStoreId() {
       return $this->options['store_id'];
    }
    
    public function getNumShipment() {
       return $this->options['num_shipment'];
    }
    
    public function getStatus() {
       return $this->options['status'];
    }
    
    public function getUserName(){
        return $this->options_aux['userName'];
    }
    
    public function getStatusName(){
        return $this->options_aux['statusName'];
    }
    
     public function getDate() {
       return $this->options['date'];
    }
    
    public function getShipmentDateFormated() {
       return $this->options_aux['dateShipmentFormated'];
    }
    
    public function getStoreName() {
       return $this->options_aux['storeName'];
    }
    
    public function getDateFormated() {
       return $this->options_aux['dateFormated'];
    }
    
    public function getIdStoreRequest() {
       return $this->options_aux['id_store_request'];
    }
    
    public function getShipmentComments(){
        return $this->options['shipment_comments'];
    }
    
     public function getComments(){
        return $this->options['comments'];
    }
    
    public function getFormatDate(){
        $date = substr($this->getDate(), 0, 10);
        $date = strftime('%m/%d/%Y',  strtotime($date));
        return $date;
    }
   

    public function save(array $data, $table = null) {            
        $login = new Login();
        $tools = new Tools();
        $data['date'] = $tools->setFormatDateTimeToDB($data['date']);
        $data['status'] = '1';
        $data['store_id'] = $login->getStoreId();
        
        $this->startTransaction(); 
        unset($data['shipment_date'],$data['shipment_comments'],$data['last_sinc']); 
        parent::save($data, $this->table);        

        $storeInDetailsTemp = new ReceivingStoreRequestDetailsTempRepository();
        $idReceivingStoreRequest = $this->getInsertId();
        $this->setLastInsertId($idReceivingStoreRequest);//Para utilizarlo en el Controller action insert
        
        if($storeInDetailsTemp->saveDetalles($idReceivingStoreRequest)){  
            $this->commit();
            $storeInDetailsTemp->drop();

            return true;
        }
        
        $this->rollback();    
        $this->flashmessenger->addMessage(array(
            'error'=>$this->_getTranslation('Error. Intenta nuevamente o contacta a tu proveedor de sistemas.')));
        return null;        
    }
    
    public function delete($id, $table = null) {
        $currentData = $this->getById($id);
        if($currentData['status'] == '1'){return true;}
        
        $this->startTransaction();
        $rs = parent::update($id, array('status'=>'1'), $this->table);
        
        if($rs){
            
            $this->clearDetailsById($id);
            /*
            $detallesAfectados = $this->getReceivingStoreRequestDetailsSaved($id);
            if(!$this->subInventoryFromReceivingStoreRequestDetalles($detallesAfectados)){
                $this->rollback();
                return null;
            }    */
        }
        $this->commit();
        
        $shipmentStoreRequest = new ShipmentStoreRequestRepository();
        $shipmentStoreRequest->clearDetailsShipment(array(
                            'data'=>$this->getById($id),
                            'details'=>$this->getReceivingStoreRequestDetailsSaved($id)));            
        return true;
    }
    
    public function clearDetailsById($id){
        $query = "UPDATE receiving_store_request_details SET received = 0 WHERE id_receiving ='$id'";
        $result = $this->query($query);
        
        if($result){
            return true;
        }
        return null;
    }
    
    public function update($id, $data, $table = null) {     
        $login = new Login();
        $tools = new Tools();
        $data['date'] = $tools->setFormatDateTimeToDB($data['date']);
        
        unset($data['status'],$data['shipment_date'],$data['shipment_comments'],$data['last_sinc']); 
        $this->startTransaction();
        
        //Actualizo tabla compras
        $result = parent::update($id, $data, $this->table);
        
        if($result){
            $repository = new ReceivingStoreRequestDetailsTempRepository();
            if($repository->updateDetalles($id)){                  
                $this->updateStatus($id,$this->options['received_incomplete']);
                    
                $repository->drop();                
                
                $shipmentStoreRequest = new ShipmentStoreRequestRepository();
                $shipmentStoreRequest->updateShipment(array(
                                    'data'=>$this->getById($id),
                                    'details'=>$this->getReceivingStoreRequestDetailsSaved($id)));    
                
                 $this->commit();         
                return true;
            }
        }
        
        $this->rollback();
        return null;
    }
    
    public function updateStatus($id,$received_incomplete){ 
        $query = "SELECT SUM(quantity)as quantity, SUM(received)as received "
                . "FROM receiving_store_request_details "
                . "WHERE id_receiving= '$id' ";
        
        $result = $this->query($query);
        
        if($result){           
           $result = $result->fetch_object();
         if($result->received === NULL || $result->received == '0'){              
               $status = '1';
           }elseif($result->received >= $result->quantity){
               $status = '2';
           }elseif($result->received < $result->quantity){
               $status = '3';
               if($received_incomplete == '1'){
                   $status = '5'; 
               }
           } 
           
           return parent::update($id, array('status'=>$status), $this->table);
        }
        return true;        
    }
    
    public function updateString($fields, $where, $table = null) {
        return parent::updateString($fields, $where, $this->table);
    }

    public function getById($id, $table = null,$selectAux = null) {
        $select = "SELECT r.*,"
                . "fxGetStoreName(r.store_id)as storeName,"
                . "IFNULL(s.id_store_request, 'Sin pedido')as id_store_request,"
                . "DATE_FORMAT(r.date,'%m/%d/%Y %r')as dateFormated,"
                . "DATE_FORMAT(r.shipment_date,'%m/%d/%Y %r')as dateShipmentFormated,"
                . "fxGetUserName(r.creado_por)as userName,"
                . "fxGetStatusName(r.status,'ReceivingStoreRequest')as statusName,"
                . "i.status as status_invoice "
                . "FROM $this->table r "
                . "LEFT JOIN shipment_store_requests s ON r.num_shipment = s.num_shipment "
                . "LEFT JOIN invoices i ON r.invoice_id = i.id "
                . "WHERE r.id = '$id'";
        $result = $this->query($select);

        if ($result->num_rows>0) {
            $set = $this->resultToArray($result);
            return $set[0];
        }

        return false;
    }
    
    public function getByNumShipment($id, $table = null,$selectAux = null) {
        $select = "SELECT *,"
                . "DATE_FORMAT(date,'%m/%d/%Y %h:%i %p ')as dateFormated,"
                . "fxGetUserName(creado_por)as userName,"
                . "fxGetStatusName(status,'ReceivingStoreRequest')as statusName "
                . "FROM $this->table "
                . "WHERE num_shipment = '$id'";
        $result = $this->query($select);

        if ($result->num_rows>0) {
            $set = $this->resultToArray($result);
            return $set[0];
        }

        return false;
    }
    
    public function existProductInReceivingStoreRequest($idReceivingStoreRequest,$idProduct){
        $query = "SELECT r.id FROM receiving_store_requests r,receiving_store_request_details d "
                . "WHERE r.id = d.id_receiving "
                . "AND r.id = '$idReceivingStoreRequest' "
                . "AND d.id_product = '$idProduct' "
                . "AND r.status != '4'";
        
        $result = $this->query($query);
        if($result->num_rows > 0){
            return true;
        }
        return null;
    }

    public function isUsedInRecord($id, array $buscarEn = null,$andWhere = null) {
        return null;
        return parent::isUsedInRecord($id, array('store-out' => 'requisition'));
    }
    
    public function crearTablaDetallesForUser(){
        $login = new Login();
        $query = "DROP TABLE IF EXISTS receiving_store_request_details_".$login->getId();
        $this->query($query);
        
        $query = "CREATE TABLE IF NOT EXISTS receiving_store_request_details_".$login->getId()." 
                 (  `id` int(11) NOT NULL AUTO_INCREMENT,
                    `id_detail` int(11) NULL,
                    `id_receiving` int(11) NULL,
                    `id_product` int(11) NOT NULL,                    
                    `real_stock_in_store` double NULL,
                    `quantity` double NULL,
                    `received` double  NULL,
                    PRIMARY KEY (`id`)
                 )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        
       $result = $this->query($query);
    }
    
    public function insertDetalle($data){
        $storeInDetailsTemp = new ReceivingStoreRequestDetailsTempRepository();
        
        return $storeInDetailsTemp->save($data);
    }
    
      public function getReceivingStoreRequestDetails(){
        $login = new Login();
        $query = "SELECT v.*,
                    v.id as idDetailTemp,
                    p.id as product,
                    p.code,
                    p.description,
                    fxGetCategoryDescription(p.category)as category,
                    fxGetSizeDescription(p.size)as size
                  FROM receiving_store_request_details_".$login->getId()." v LEFT JOIN products p
                  ON v.id_product = p.id
                  ORDER BY p.description";
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result);
            return $result;
        }        
        return null;
    }
    
    public function getReceivingStoreRequestDetailsSaved($id){
        $query = "SELECT c.*,
                    IFNULL(real_stock_in_store,0)as real_stock_in_store,
                    IFNULL(quantity,0)as quantity,
                    IFNULL(received,0)as received,
                    p.code,
                    p.description,
                    fxGetSizeDescription(p.size)as size
                    FROM receiving_store_request_details c LEFT JOIN products p ON c.id_product = p.id
                    WHERE c.id_receiving = '$id'";
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result);
            return $result;
        }
        
        return null;
    }
    
    public function getProductoByCode($code){
        $repo = new ProductRepository();
        return $repo->getByCode($code);
    }
    
    public function setReceivingStoreRequestDetailsById($idCompra){
        $repository = new ReceivingStoreRequestDetailsTempRepository();
        
        return $repository->setReceivingStoreRequestDetailsById($idCompra);
    }
    
    public function truncateIfIsEditInfo(){
        $repository = new ReceivingStoreRequestDetailsTempRepository();
        $repository->truncateIfIsEditInfo();
        
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
    
    public function getListReceivingStoreRequests(){          
        $store_id = null;
        
        $login = new Login();
        if($login->getRole() != '1'){
            $store_id = " AND find_in_set(r.store_id,'{$login->getStoreId()}')";
        }       
        
        $query = "SELECT r.*,
                IFNULL(ss.id_store_request, 'Sin pedido')as id_store_request,
                fxGetStoreName(r.store_id)as storeName,
                SUM(d.quantity)as quantity,
                IFNULL(SUM(d.received),'0')as received,
                DATE_FORMAT(r.date,'%m/%d/%Y %h:%i %p ')as date,
                fxGetStatusName(r.`status`,'ReceivingStoreRequest')as statusName,
                fxGetUserName(r.creado_por) as user,
                fxGetAreaName(sr.area_id)as area_name
                FROM $this->table r, receiving_store_request_details d, shipment_store_requests ss 
                LEFT JOIN store_request sr ON ss.id_store_request = sr.id
                WHERE r.id = d.id_receiving "
              . "AND r.num_shipment = ss.num_shipment "
              . "$store_id " 
              . "GROUP BY r.id "
              . "ORDER BY r.id DESC LIMIT 1000 ";

    
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }        
        return null;
  }
  
      public function saveUsingDataFromMainServer($data){
       $receiving = $data['data'];
       $details = $data['details'];
       
        try {
           $this->startTransaction();
           $options = array(
               'num_shipment'=>$receiving['num_shipment'],
               'store_id'=>$receiving['to_store'],
               'shipment_date'=>$receiving['date'],
               'shipment_comments'=>$receiving['comments'],
               'status'=>$receiving['status']
           );
           
           parent::save($options,'receiving_store_requests');       
           $idReceivingStoreRequest = parent::getInsertId();
           
           foreach($details as $detail){
               $options = array(
                   'id_receiving'=>$idReceivingStoreRequest,
                   'id_product'=>$detail['id_product'],
                   'real_stock_in_store'=>$detail['real_stock_in_store'],
                   'quantity'=>$detail['quantity'],
               );
               parent::save($options,'receiving_store_request_details'); 
           }           
           parent::commit();        
           
           return $idReceivingStoreRequest;

        } catch (Exception $exc) {
            parent::rollback();    
            $this->flashmessenger->addMessage(array('danger'=>$exc->getMessage()));
            return null;
        }   
    }
    
    public function updateFromMainServer($id){
        $receivingData = $this->getById($id);      

        $shipmentStoreRequestRepo = new ShipmentStoreRequestRepository();        
        $options = $shipmentStoreRequestRepo->getDataShipment($receivingData['num_shipment']);
        
        if(!$options['response']){return null;}
        
        $data = $options['data'];
        $detailsFromServer = $options['details'];
        
        $options = array(
            'shipment_date'=>$data['date'],
            'shipment_comments'=>$data['comments'],
            'status'=>$data['status'],
        );
        
        parent::startTransaction();
        $rs = parent::updateString($options, " num_shipment = '{$data['num_shipment']}'", $this->table);
        if($rs){
            $idProducts = null;
            foreach($detailsFromServer as $serverDetail){
                $idProducts[] = $serverDetail['id_product'];
                
                if($this->existProductInReceivingStoreRequest($id,$serverDetail['id_product'])){
                    $options = array ('quantity'=>$serverDetail['quantity']);  
                    $rs = parent::updateString($options, " id_receiving = '$id' AND id_product = '{$serverDetail['id_product']}'", 'receiving_store_request_details');
                }else{
                    $options = array(
                        'id_receiving'=>$id,
                        'id_product'=>$serverDetail['id_product'],
                        'quantity'=>$serverDetail['quantity'],
                    );
                    $rs = parent::save($options, 'receiving_store_request_details');                    
                }
                
                if(!$rs){parent::rollback();return null;}
            }
            
             /*Esto eliminaba todo lo que se recibio y que no esta en el envio*/            
            /*
            $detailsFromLocal = $this->getReceivingStoreRequestDetailsSaved($id);            
            foreach ($detailsFromLocal as $localDetail){
                if(!in_array($localDetail['id_product'], $idProducts) && ($localDetail['received'] != '0') && $localDetail['received'] != null ){
                   $query = "DELETE FROM receiving_store_request_details WHERE id_receiving = '$id' AND id_product = '{$localDetail['id_product']}' ";
                   if(!$this->query($query)){
                       parent::rollback();
                       return null;
                   }
                }
            }*/
            
            parent::commit();
            return true;
            
        }else{
            parent::rollback();
            return null;
        }            
    }
    
     public function getLisShipmentStoreRequestToReceive($id = null){
        $store_id = null;
        $idPurchase = '';
        if($id != null){$idPurchase = " OR id = '$id'";}
        
        $login = new Login();
        if($login->getRole() != '1'){
            $store_id = " AND find_in_set(id_store_request,'{$login->getStoreId()}')";
        }             
        
        $query = "SELECT * "
                . "FROM shipment_store_requests "
                . "WHERE status = '1' "
                . "$store_id "
                . "$idPurchase ORDER BY date ASC ";
        
        $result = $this->query($query);
        
        if($result){
            $array = array();
            while($row = $result->fetch_object()){
                $array[$row->num_shipment] = "Envio #".$row->num_shipment." - ( Pedido #".$row->id_store_request." )";
            }
            
            return $array;
        }
    }
    
     /*PARA CONTROL DE INVENTARIOS*/
  public function subInventoryFromReceivingStoreRequestDetalles($detallesAfectados){
        $repoInventario = new InventarioRepository();         

        $array = array();
        foreach($detallesAfectados as $detalle){         
            $row = array(                        
                        'id_product'=>$detalle['id_product'],
                        'quantity'=>$detalle['received'],
                        'controller'=>'Recibos-'.$detalle['id_receiving']
                    );
            $array[] = $row;
        }

        return $repoInventario->deleteAddInventory($array);
    }
    
     public function removeAllowEdit($idSpecialRequisition){
        parent::update($idSpecialRequisition, array('allow_edit'=>'0'), $this->table);
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