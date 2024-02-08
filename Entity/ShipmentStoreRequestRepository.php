<?php
class ShipmentStoreRequestRepository extends EntityRepository {

    private $table = 'shipment_store_requests';
    public $flashmessenger = null;
    
        private $options = array (
        'id_store_request'=>null,// Esta campo solo se ingresa en _generateShipment en StoreRequesrepository.php
        'num_shipment'=>null,
        'date'=>null,
        'to_store' => null,
        'comments'=>null,
        'status'=>null,
        'receiving_date'=>null,
        'receiving_comments'=>null,
        'last_sinc'=>null);
    
    private $options_aux = array(
        'token_form'=>null,
        'dateReceivingFormated'=>null,
        'dateFormated'=>null,
        'statusName'=>null,
        'userName'=>null
    );
    
    public function __construct() {
        if(!$this->flashmessenger instanceof FlashMessenger){
            $this->flashmessenger = new FlashMessenger();
        }
    }
  
    public function _getTranslation($text){
        return $this->flashmessenger->_getTranslation($text);
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
        return $this->options;
    }
    
     public function getNumShipment() {
       return $this->options['num_shipment'];
    }
    
    public function getIdStoreRequest() {
       return $this->options['id_store_request'];
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
    
    public function getToStore() {
       return $this->options['to_store'];
    }
    
    public function getReceivingDateFormated() {
       return $this->options_aux['dateReceivingFormated'];
    }
    
    public function getDateFormated() {
       return $this->options_aux['dateFormated'];
    }
    
    public function getComments(){
        return $this->options['comments'];
    }
    
    public function getReceivingComments(){
        return $this->options['receiving_comments'];
    }
    
    public function getFormatDate(){
        $date = substr($this->getDate(), 0, 10);
        $date = strftime('%d/%m/%Y',  strtotime($date));
        return $date;
    }
    
    public function getTokenForm(){
        return $this->options_aux['token_form'];
    }

    public function save(array $data, $table = null) {             
        $tools = new Tools();
        $data['date'] = $tools->setFormatDateTimeToDB($data['date']);    
        unset($data['id_store_request']);// Esta campo solo se ingresa en _generateShipment en StoreRequesrepository.php
        
        $settings = new SettingsRepository();
        $recibirAutomatico = null;
        $storeAutomatiReceiving = $settings->_get('sucursal_receiving_automatico');
        $storeAutomatiReceiving = explode(',', $storeAutomatiReceiving);
        if(in_array($data['to_store'], $storeAutomatiReceiving)){    
            $recibirAutomatico = true;
            $data['status'] = '2';
            $data['comments'] .= "Recibido automaticamente por sistema.\n";
            $data['receiving_date'] = $data['date'];
            $data['receiving_comments'] = $data['comments'];
        }else{            
            $data['status'] = '1';
            unset($data['receiving_date']);
        }
       
        try{
            $this->startTransaction(); 
            unset($data['num_shipment'],$data['last_sinc']);
            parent::save($data, $this->table);        

            $storeInDetailsTemp = new ShipmentStoreRequestDetailsTempRepository();
            $idShipment = $this->getInsertId();
            $this->setLastInsertId($idShipment);//Para utilizarlo en el Controller action insert

            $numShipment = $this->getPrefixNumberShipment().str_pad($idShipment, 7, '0', STR_PAD_LEFT);
            $this->updateString(array('num_shipment'=>$numShipment), " id = '$idShipment' ");

            if($storeInDetailsTemp->saveDetalles($idShipment,$this->getTokenForm(),$recibirAutomatico)){
                if($recibirAutomatico){
                    if(!$this->recibirAutomatico($idShipment,'save')){
                        $this->rollback();
                        return null;
                    }                    
                }
                $this->commit();
                $storeInDetailsTemp->truncate($this->getTokenForm());

                return true;
            }
        } catch (Exception $ex) {
            $this->rollback();    
            $this->flashmessenger->addMessage(array(
                'danger'=>$ex->getMessage()));
            return null;   
        }           
    }  
    
    public function delete($id, $table = null) {
        $currentData = $this->getById($id);
        if($currentData['status']== '4'){return true;}
        
        if($currentData['status']== '2' || $currentData['status']== '3'){
            $this->flashmessenger->addMessage(array('danger'=>'Envio no puede ser cancelado, ya ha sido recibido.'));
            return null;
        }
        
        $this->startTransaction();
        $rs = parent::update($id, array('status'=>'4'), $this->table);
        if($rs){
            $detallesAfectados = $this->getShipmentDetailsSaved($id);
            if(!$this->addInventoryFromShipmentDetalles($detallesAfectados)){
                $this->rollback();
                return null;
            }
            
            /*Cancelar recibo; si llego hasta aqui, es porque status =1 (En transito); y solo asi se puede cancelar Recibo*/
            $this->query("UPDATE receiving_store_requests SET status = '4' WHERE num_shipment = '{$currentData['num_shipment']}'");            
            
            $this->commit();
            return true;
        }
        $this->rollback();
        return null;
    }
    
    public function update($id, $data, $table = null) {              
        $tools = new Tools();
        $data['date'] = $tools->setFormatDateTimeToDB($data['date']);    
        
        $settings = new SettingsRepository();
        $recibirAutomatico = null;
        $storeAutomatiReceiving = $settings->_get('sucursal_receiving_automatico');
        $storeAutomatiReceiving = explode(',', $storeAutomatiReceiving);
        if(in_array($data['to_store'], $storeAutomatiReceiving)){    
            $recibirAutomatico = true;
            $data['comments'] .= "Recibido automaticamente por sistema.\n";
            $data['receiving_date'] = $data['date'];
            $data['receiving_comments'] = $data['comments'];
        }else{            
            unset($data['receiving_date']);
        }
        
        unset($data['id_store_request'],$data['status'],$data['last_sinc']);// id_store_request solo se ingresa en _generateShipment en StoreRequesrepository.php
        $this->startTransaction();
        $result = parent::update($id, $data, $this->table);
 
        if($result){
            $repository = new ShipmentStoreRequestDetailsTempRepository();
            if($repository->updateDetalles($id,$this->getTokenForm(),$recibirAutomatico)){
                if($recibirAutomatico){
                    if(!$this->recibirAutomatico($id,'update')){
                        $this->rollback();
                        return null;
                    }                    
                }
                $this->updateStatus($id);
                $this->commit();
                $repository->truncate($this->getTokenForm());   
                return true;               
            }
        }        
        $this->rollback();
        return null;
    }
    
    public function updateStatus($id){
        $query = "SELECT SUM(quantity)as quantity, SUM(received)as received "
                . "FROM shipment_store_requests_details "
                . "WHERE id_shipment = '$id' ";
        
        $result = $this->query($query);
      
        if($result){           
           $result = $result->fetch_object();
           
           if($result->received === NULL || $result->received == '0'){              
               $status = '1';
           }elseif($result->received >= $result->quantity){
               $status = '2';
           }elseif($result->received < $result->quantity){
               $status = '3'; 
           }
           
           return $this->updateString(array('status'=>$status), " id = '$id' ");
        } 
        return true;        
    }
    
    public function updateString($fields, $where, $table = null) {
        return parent::updateString($fields, $where, $this->table);
    }

    public function getById($id, $table = null,$selectAux = null) {
        $select = "SELECT s.*,"
                . "fxGetStoreName(s.to_store)as to_store_name,"
                . "IFNULL(s.id_store_request, 'Sin pedido')as id_store_request,"
                . "DATE_FORMAT(s.date,'%m/%d/%Y %r')as dateFormated,"
                . "DATE_FORMAT(r.delivery_date,'%m/%d/%Y')as deliveryDateFormated,"
                . "DATE_FORMAT(s.receiving_date,'%m/%d/%Y %r')as dateReceivingFormated,"
                . "fxGetUserName(s.creado_por)as userName,"
                . "fxGetStatusName(s.status,'ShipmentStoreRequest')as statusName, "
                . "r.comments as store_request_comments "
                . "FROM $this->table s "
                . "LEFT JOIN store_request r ON s.id_store_request = r.id  "
                . "WHERE s.id = '$id'";
        $result = $this->query($select);

        if ($result->num_rows>0) {
            $set = $this->resultToArray($result);
            return $set[0];
        }

        return false;
    }
    
    public function getByNumShipment($numShipment){
        $query = "SELECT * FROM $this->table WHERE num_shipment = '$numShipment'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result)[0];
        }
        
        return null;
    }

    public function isUsedInRecord($id, array $buscarEn = null,$andWhere = null) {
        return null;
        return parent::isUsedInRecord($id, array('store-out' => 'requisition'));
    }
    
    public function crearTablaDetallesForUser(){
        $login = new Login();
        
        $query = "CREATE TABLE IF NOT EXISTS shipment_store_requests_details_".$login->getId()." 
                 (  `id` int(11) NOT NULL AUTO_INCREMENT,
                    `token_form` char(50) NOT NULL,
                    `id_detail` int(11) NULL,
                    `id_shipment` int(11) NULL,
                    `id_product` int(11) NOT NULL,
                    `min_stock` double  NULL,
                    `real_stock_in_store` double NULL,
                    `quantity` double NULL,
                    `received` double  NULL,
                    PRIMARY KEY (`id`)
                 )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        
       $result = $this->query($query);
    }
    
    public function insertDetalle($data){
        $storeInDetailsTemp = new ShipmentStoreRequestDetailsTempRepository();
        
        return $storeInDetailsTemp->save($data);
    }
    
      public function getShipmentDetails($token_form){
        $login = new Login();
        $query = "SELECT v.*,
                    v.id as idDetailTemp,
                    p.id as product,
                    p.code,
                    p.description,
                    fxGetCategoryDescription(p.category)as category,
                    fxGetSizeDescription(p.size)as size
                  FROM shipment_store_requests_details_".$login->getId()." v LEFT JOIN products p
                  ON v.id_product = p.id
                  WHERE 1 = 1 AND  token_form = '$token_form'
                  ORDER BY p.size ASC,p.description";
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result);
            return $result;
        }        
        return null;
    }
    
    public function getShipmentDetailsSaved($id){
        $query = "SELECT c.*,
                    p.code as code,
                    p.description as description,
                    fxGetSizeDescription(p.size)as size
                    FROM shipment_store_requests_details c LEFT JOIN products p ON c.id_product = p.id
                    WHERE c.id_shipment = '$id' ORDER BY p.size,p.description";
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
    
    public function setShipmentStoreRequestDetailsById($idCompra,$tokenForm){
        $repository = new ShipmentStoreRequestDetailsTempRepository();
        
        return $repository->setShipmentStoreRequestDetailsById($idCompra,$tokenForm);
    }
    
    public function truncateIfIsEditInfo(){
        $repository = new ShipmentStoreRequestDetailsTempRepository();
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
    
    public function getListShipmentStoreRequests(){               
        
         $query = "SELECT r.*,
                fxGetStoreName(r.to_store) as toName,
                IFNULL(SUM(d.real_stock_in_store),'0')as required,
                SUM(d.quantity)as quantity,
                DATE_FORMAT(r.date,'%m/%d/%Y')as date,
                fxGetStatusName(r.`status`,'ShipmentStoreRequest')as statusName,
                fxGetUserName(r.creado_por) as user,
                DATE_FORMAT(s.delivery_date,'%m/%d/%Y')as required_date,
                fxGetAreaName(s.area_id)as area_name
                FROM $this->table r "
                . "LEFT JOIN shipment_store_requests_details d ON r.id = d.id_shipment "
                . "LEFT JOIN store_request s ON r.id_store_request = s.id  " 
              . "GROUP BY r.id "
              . "ORDER BY r.id DESC ";

    
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }        
        return null;
  }
  
  public function getPrefixNumberShipment(){
      $settings = new SettingsRepository();
      $prefix = $settings->_get('prefix_for_shipments');
      if($prefix){
          return $prefix;
      }
      return '';
  }
  
   public function recibirAutomatico($idShipment,$operation){
        $data = $this->getById($idShipment);
        $details = $this->getShipmentDetailsSaved($idShipment);
        
        $storeRepository = new StoreRepository();
        $storeData = $storeRepository->getById($data['to_store']);        
        $externalConnection = array(
                'EXTERNAL_DB_HOSTNAME'=>$storeData['DB_HOSTNAME'],
                'EXTERNAL_DB_USERNAME'=>$storeData['DB_USERNAME'],
                'EXTERNAL_DB_PASSWORD'=>$storeData['DB_PASSWORD'],
                'EXTERNAL_DB_NAME'=>$storeData['DB_NAME'],
            );
             
        $externalConnectionRepo = new ExternaConnectionRepository($externalConnection);
        if(!$externalConnectionRepo->isConnected()){return null;}
        
        switch($operation){
            case 'save':
                if($externalConnectionRepo->saveReceivingInStore($data,$details)){return true;}
                break;
                
            case 'update':
                if($externalConnectionRepo->updateReceivingInStore($idShipment,$data,$details)){return true;}
                break;
                
            case 'delete':
                if($externalConnectionRepo->deleteReceivingInStore($idShipment,$data,$details)){return true;}
                break;
        }
        return null;
    }
  
  /*PARA CONTROL DE INVENTARIOS*/
  public function addInventoryFromShipmentDetalles($detallesAfectados){ 
        $settings = new SettingsRepository();
        $idStore = $settings->_get('id_store_for_inventory_of_store_shipments');

        $storeRepo = new StoreRepository();
        $storeData = $storeRepo->getById($idStore);

        $array = array();
        foreach($detallesAfectados as $detalle){         
            $row = array(
                        'id_product'=>$detalle['id_product'],
                        'quantity'=>$detalle['quantity'],
                        'id_location'=>$storeData['default_location'],
                    );
            $array[] = $row;
        }
        
        $repoInventario = new InventoryRepository(); 
        return $repoInventario->deleteSubInventory($array);
    }
    
    public function existShipmentForStoreRequest($idStoreRequest){
        $query = "SELECT * FROM $this->table WHERE id_store_request = '$idStoreRequest' AND status != '4'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result)[0];
        }
        return null;
    }
    
    public function getDataShipment($numShipment){
        $query = "SELECT * FROM $this->table WHERE num_shipment = '{$numShipment}'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $shipmentData = $this->resultToArray($result)[0];            
            $shipmentDetails = $this->getShipmentDetailsSaved($shipmentData['id']);
            
            return array(
                'response'=>true,
                'data'=>$shipmentData,
                'details'=>$shipmentDetails
            );
        }
        
        return array('response'=>null,'msg'=>"No se ha generado Envio #{$numShipment}.");     
    }
    
     /*Update tbl Shipment de MainServer desde sucursal*/
    public function updateShipment($options){
        $data = $options['data'];
        $details = $options['details'];
        $numShipment = $data['num_shipment'];
        
        try{
            $query = "UPDATE shipment_store_requests "
                    . "SET receiving_date = '{$data['date']}',"
                    . "receiving_comments = '{$data['comments']}',"
                    . "status = '{$data['status']}' "
                    . "WHERE num_shipment = '{$numShipment}'";
                    
            $result = $this->query($query);
            
            $shipmentData = $this->getByNumShipment($numShipment);           
            if($result){
                foreach($details as $detail){                            
                    $query = "UPDATE shipment_store_requests s, shipment_store_requests_details d "
                            . "SET received = '{$detail['received']}' "
                            . "WHERE  s.id = d.id_shipment "
                            . "AND s.num_shipment = '{$numShipment}' "
                            . "AND d.id_product = '{$detail['id_product']}'";
                            
                    $result = $this->query($query);
                    
                    $query = "UPDATE store_request_details d "
                            . "SET received = '{$detail['received']}' "
                            . "WHERE  d.id_store_request = '{$shipmentData['id_store_request']}' "
                            . "AND d.id_product = '{$detail['id_product']}'";
                            
                    $result = $this->query($query);
                }
            } 
            
        } catch (Exception $ex) {
            return true;
        }
        return true;
    }
    
    #Cuando se cancela recibo en Sucursal que se recibe, todos los productos en el campo recibido de la tblEnvios de la Sucursal que envio se ponen en 0; 
    public function clearDetailsShipment($options){
        $data = $options['data'];
        $details = $options['details'];
        $numShipment = $data['num_shipment'];
        
        $options = array(
            'status'=>'1'
        );
        
        try{
            $this->updateString($options, " num_shipment = '{$numShipment}'", 'stores_shipments');
            
            foreach($details as $detail){
                $options = array('received'=>0);
                $this->updateString(
                        $options, 
                        " s.id = d.id_shipment AND s.num_shipment = '{$numShipment}' AND d.id_product = '{$detail['id_product']}'", 
                        'shipment_store_requests s, shipment_store_request_details d'
                                );
            }
            
        } catch (Exception $ex) {
            return true;
        }
        return true;
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