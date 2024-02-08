<?php
class StoreRequestRepository extends EntityRepository {

    private $table = 'store_request';
    public $flashmessenger = null;
    private $options = array (
        'date' => null,
        'delivery_date' => null,
        'store_id' => null,
        'area_id' => null,
        'comments'=>null,
        'status'=>null);
    
    private $options_aux = array(
        'token_form'=>null,
        'formatedDate'=>null,
        'formatedDeliveryDate'=>null,
        'statusName'=>null,
        'areaName'=>null
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
    
    public function getStatus(){
        return $this->options['status'];
    }
    
    public function getStoreId(){
        return $this->options['store_id'];
    }
    
    public function getStatusName(){
        return $this->options_aux['statusName'];
    }
    
    public function getAreaName(){
        return $this->options_aux['areaName'];
    }
    
    public function getFormatedDate(){
        return $this->options_aux['formatedDate'];
    }
    
    public function getFormatedDeliveryDate(){
        return $this->options_aux['formatedDeliveryDate'];
    }
    
     public function getComments(){
        return $this->options['comments'];
    }
    
    public function getTokenForm(){
        return $this->options_aux['token_form'];
    }

    public function save(array $data, $table = null) {              
        $tools = new Tools();        
        $login = new Login();
        $data['date'] = $tools->setFormatDateToDB($data['date']); 
        $data['delivery_date'] = $tools->setFormatDateToDB($data['delivery_date']); 
        $data['status'] = '1'; 
        $data['creado_fecha'] = date('Y-m-d H:i:s');
        $data['creado_por'] = $login->getId();
        
        $this->startTransaction();        
        parent::save($data, $this->table);     
        $idStoreRequest = $this->getInsertId();
        $this->setLastInsertId($idStoreRequest);
        
        $storeRequestDetallesTemp = new StoreRequestDetailsTempRepository();       
        if($storeRequestDetallesTemp->saveDetalles($idStoreRequest,$this->getTokenForm())){ 
                $this->commit();
                $storeRequestDetallesTemp->truncate($this->getTokenForm());
                return true;   
        }
        
        $this->rollback();    
        $this->flashmessenger->addMessage(array(
            'error'=>$this->_getTranslation('Error. Intenta nuevamente o contacta a tu proveedor de sistemas.')));
        return null;        
    }

    public function update($id, $data, $table = null) {         
        $tools = new Tools();
        $data['date'] = $tools->setFormatDateToDB($data['date']);     
        $data['delivery_date'] = $tools->setFormatDateToDB($data['delivery_date']); 
                
        $this->startTransaction();
        $result = parent::update($id, $data, $this->table);      
        
        if($result){
            $repository = new StoreRequestDetailsTempRepository();
            if($repository->updateDetalles($id,$this->getTokenForm())){      
                $this->commit();
                $repository->truncate($this->getTokenForm());                   
                return true;
            }
        }
        
        $this->rollback();
        return null;
    }    
        
    public function delete($id, $table = null) {
        $currentData = $this->getById($id);
        if($currentData['status'] === '2'){return true;}
        
        return parent::update($id, array('status'=>'2'), $this->table);
    }
    
    public function updateString($fields, $where, $table = null) {
        return parent::updateString($fields, $where, $this->table);
    }

    public function getById($id, $table = null,$selectAux = null) {
        $select = "SELECT *,"
                . "fxGetStoreName(store_id) as storeName,"
                . "DATE_FORMAT(date,'%m/%d/%Y')as formatedDate,"
                . "DATE_FORMAT(delivery_date,'%m/%d/%Y')as formatedDeliveryDate,"
                . "fxGetStatusName(status,'StoreRequest')as statusName "
                . "FROM $this->table "
                . "WHERE id = '$id'";
        $result = $this->query($select);

        if ($result->num_rows>0) {
            $set = $this->resultToArray($result);
            return $set[0];
        }

        return false;
    }
    
    public function crearTablaDetallesForUser(){
        $login = new Login();
        
        $query = "CREATE TABLE IF NOT EXISTS store_request_details_".$login->getId()." 
                 (  `id` int(11) NOT NULL AUTO_INCREMENT,
                    `token_form` char(50) NOT NULL,
                    `id_detalle` int(11) NULL,
                    `id_store_request` int(11) NULL,                    
                    `id_product` int(11) NOT NULL,
                    `id_size` int(11) NULL,
                    `last_inventory` double NULL,
                    `pending_to_receive` double NULL,
                    `quantity` double NULL,
                    `received` double NULL,
                    PRIMARY KEY (`id`)
                 )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        
       $result = $this->query($query);
    }
    
    public function getStoreRequestDetalles($token_form){
        $login = new Login();
        $query = "SELECT s.*,
                    p.description,
                    fxGetSizeDescription(p.size)as sizeName,
                    p.comments
                  FROM store_request_details_".$login->getId()." s, products p 
                  WHERE s.id_product = p.id  
                  AND token_form = '$token_form' "
                //. "ORDER BY p.description ASC, p.size ASC";
                ."ORDER BY FIELD (p.size ,'16') ASC,p.description ASC, p.size ASC";
                
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result);
            return $result;
        }
        
        return null;
    }
    
    public function getStoreRequestDetallesSaved($id){
        $query = "SELECT d.*,
                    fxGetProductName(id_product)as description,
                    fxGetSizeDescription(id_size)as size
                    FROM store_request_details d
                    WHERE id_store_request = '$id' 
                    ORDER BY id";
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result);
            return $result;
        }
        
        return null;
    }
    
    public function getStoreRequestDetallesSavedPDF($id){
        $query = "SELECT d.*,
                    p.description as description,
                    p.size as size
                    FROM store_request_details d, products p
                    WHERE d.id_product = p.id
                    AND id_store_request = '$id' 
                    AND quantity > 0
                   ORDER BY p.description ASC, p.size ASC";
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result);
            return $result;
        }
        
        return null;
    }
    
    public function setStoreRequestDetallesById($idStoreRequest,$tokenForm){
        $repo = new StoreRequestDetailsTempRepository();
        return $repo->setStoreRequestDetallesById($idStoreRequest, $tokenForm);
    }

    public function getListStoreRequest($options = null){
        $store_request_id = null;
        $store_id = null;
        $status = null;
        $limit = null;
        
        $date = $this->createFilterFecha($options,'date');
        
        $login = new Login();
        if($login->getRole() != '1'){
            $store_id = " AND find_in_set(m.store_id,'{$login->getStoreId()}')";
        }       

        if(trim($options['store_request_id']) !== ''){$store_request_id = " AND find_in_set(m.id,'{$options['store_request_id']}')";}        
        if(isset($options['status']) && is_array($options['status']) && count($options['status']) > 0){
            $idsStatus = implode(',', $options['status']);
            $status = " AND find_in_set(status,'$idsStatus')";
        }  
        
        if($options === null){
            $limit = " LIMIT 500 ";
        }
        
        $query = "SELECT m.*,"
                . "fxGetStoreName(store_id) as storeName,"
                . "fxGetAreaName(area_id) as areaName,"
                . "DATE_FORMAT(date,'%m/%d/%Y')as formatedDate,"
                . "DATE_FORMAT(delivery_date,'%m/%d/%Y')as formatedDeliveryDate,"
                . "fxGetStatusName(status,'StoreRequest')as statusName "
                . "FROM $this->table m LEFT JOIN store_request_details d ON m.id = d.id_store_request "
                . "WHERE 1 = 1  "
                . "$store_request_id "
                . "$store_id "
                . "$status "
                . "$date "
                . "GROUP BY m.id "
                . "ORDER BY m.id DESC "
                . "$limit ";
        
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }
        
        return null;
    }
    
    public function getListStoreRequestForShipmentAndProduction($options = null){
        $store_request_id = null;
        $area_id = null;
        $store_id = null;
        $status = null;
        $limit = null;
        
        $date = $this->createFilterFecha($options,'date');  
        if(isset($options['area_id'])){$area_id = " AND area_id = '{$options['area_id']}'";}
        if(isset($options['store_request_id']) && trim($options['store_request_id']) !== ''){$store_request_id = " AND find_in_set(m.id,'{$options['store_request_id']}')";}        
        if(isset($options['status']) && is_array($options['status']) && count($options['status']) > 0){
            $idsStatus = implode(',', $options['status']);
            $status = " AND find_in_set(status,'$idsStatus')";
        }  
        
        if($options === null){
            $limit = " LIMIT 100 ";
        }      
        
        $query = "SELECT m.*,"
                . "fxGetStoreName(store_id) as storeName,"
                . "fxGetAreaName(area_id) as areaName,"
                . "DATE_FORMAT(date,'%m/%d/%Y')as formatedDate,"
                . "DATE_FORMAT(delivery_date,'%m/%d/%Y')as formatedDeliveryDate,"
                . "fxGetStatusName(status,'StoreRequest')as statusName "
                . "FROM $this->table m LEFT JOIN store_request_details d ON m.id = d.id_store_request "
                . "WHERE 1 = 1  "
                . "$area_id "
                . "$store_request_id "
                . "$store_id "
                . "$status "
                . "$date "
                . "GROUP BY m.id "
                . "ORDER BY m.id DESC "
                . "$limit ";
        
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }
        
        return null;
    }
    
    public function _generateShipment($idStoreRequest){      
        $data = $this->getById($idStoreRequest);
        $login = new Login();        
        $receivingRepo = new ReceivingStoreRequestRepository();
        $areaRepo = new AreaRepository();
        $areaData = $areaRepo->getById($data['area_id']);
        $automaticShipment = $areaData['automatic_shipment'];
        
        $repo = new ShipmentStoreRequestRepository();
        $dataExist = $repo->existShipmentForStoreRequest($idStoreRequest);
        if($dataExist != null){     
            /*
            if($automaticShipment){
                
                $receivingData = $receivingRepo->getByNumShipment($dataExist['num_shipment']);            
                
                if($receivingData == null){
                    $this->_generateReceiving($dataExist['id']);
                    $receivingData = $receivingRepo->getByNumShipment($dataExist['num_shipment']);
                }                
                return array(
                    'response'=>true,
                    'automatic_shipment'=>true,
                    'shipment_id'=>$receivingData['id']
                );
            }*/
            
            $msg = $this->_getTranslation('Ya existe un envio para este pedido. Envio #');
            $msg .=" <a href='ShipmentStoreRequest.php?action=edit&id={$dataExist['id']}'>{$dataExist['num_shipment']}</a>";
            $this->flashmessenger->addMessage(array('danger'=>$msg));
            return array(
                'response'=>true,
                'msg'=>$this->flashmessenger->getMessageString()
            );                   
        }       
        
        //if($login->getRole() == '2'){return array('response'=>false,'msg'=>'No se ha generado el envio de este pedido.');}
        if($data['status'] =='2'){return array('response'=>false);}
        
        #Save 
        $shipmentData = array(
            'date'=>$data['date'],
            'to_store'=>$data['store_id'],
            'id_store_request'=>$idStoreRequest,
            'status'=>'1'
        );
        
        parent::startTransaction();
        parent::save($shipmentData, $repo->getTableName());
        $idShipment = parent::getInsertId();
        
        $numShipment = $repo->getPrefixNumberShipment().str_pad($idShipment, 7, '0', STR_PAD_LEFT);
        parent::update($idShipment, array('num_shipment'=>$numShipment), $repo->getTableName());
        
        $details = $this->getStoreRequestDetallesSaved($idStoreRequest);
        
        /*$cero y $quantity se usa con $$enviado*/
        $cero = 0;
        $shipmentOptions = array(
            '0'=>"cero",
            '1'=>"quantity"
        );
        
        foreach($details as $detail){            
            if($detail['quantity'] > 0){
                $quantity = $detail['quantity'];
                $enviado = $shipmentOptions[$automaticShipment];
                
                $arrayDetail = array(
                    'id_shipment'=>$idShipment,                    
                    'id_product'=>$detail['id_product'],
                    'real_stock_in_store'=>$detail['quantity'], /*Para paris, esto es lo que piden, no lo que tienen en stock*/
                    'quantity'=>$$enviado,
                    'creado_por'=>$login->getId(),
                    'creado_fecha'=>date('Y-m-d H:i:s')
                );

                if(!parent::save($arrayDetail, 'shipment_store_requests_details')){
                    return null;
                }                  
            
            }
        }
        
        if($automaticShipment){$this->_generateReceiving($idShipment);} 
        
        parent::commit();        
        
        if($automaticShipment){            
            $dataExist = $repo->existShipmentForStoreRequest($idStoreRequest);
            /*$receivingData = $receivingRepo->getByNumShipment($dataExist['num_shipment']);*/
            
            $msg = $this->_getTranslation('Se genero envio exitosamente. Envio #');
            $msg .=" <a href='ShipmentStoreRequest.php?action=edit&id={$idShipment}'>{$numShipment}</a>";
            $this->flashmessenger->addMessage(array('success'=>$msg));
        
            return array(
                'response'=>true,
                'automatic_shipment'=>true,
                'shipment_id'=>$dataExist['id'],
                'msg'=>$this->flashmessenger->getMessageString()
                /*'receiving_id'=>$receivingData['id']*/
            );
        }       
            
        $msg = $this->_getTranslation('Se genero envio exitosamente. Envio #');
        $msg .=" <a href='ShipmentStoreRequest.php?action=edit&id={$idShipment}'>{$numShipment}</a>";
        $this->flashmessenger->addMessage(array('success'=>$msg));
        return array(
            'response'=>true,
            'msg'=>$this->flashmessenger->getMessageString()
        );
    }   
    
     public function _generateReceiving($idShipmentStoreRequest){     
        $login = new Login();
        $receivingStoreRequest = new ReceivingStoreRequestRepository();
        
        $shipmentStoreRequestRepo = new ShipmentStoreRequestRepository();
        $shipmentStoreRequestData = $shipmentStoreRequestRepo->getById($idShipmentStoreRequest);
        $shipmentStoreRequestDetails = $shipmentStoreRequestRepo->getShipmentDetailsSaved($idShipmentStoreRequest);
        
        $receivingData = array(
            'date'=>$shipmentStoreRequestData['date'],
            'store_id'=>$shipmentStoreRequestData['to_store'],
            'num_shipment'=>$shipmentStoreRequestData['num_shipment'],
            'date'=>date('Y-m-d H:i:s'),
            'shipment_date'=>$shipmentStoreRequestData['date'],
            'status'=>'1'
        );        
        
        parent::save($receivingData, $receivingStoreRequest->getTableName());
        $idReceiving = parent::getInsertId();
        //echo "<pre>";var_dump($shipmentStoreRequestDetails);echo "</pre>";exit;
        foreach($shipmentStoreRequestDetails as $detail){            
            if($detail['real_stock_in_store'] > 0){                
                $arrayDetail = array(
                    'id_receiving'=>$idReceiving,                    
                    'id_product'=>$detail['id_product'],
                    'real_stock_in_store'=>$detail['real_stock_in_store'], /*Para paris, esto es lo que piden, no lo que tienen en stock*/
                    'quantity'=>$detail['real_stock_in_store'], //Se envia lo que se idio, cuando es automatic_shipment
                    'received'=>0,
                    'creado_por'=>$login->getId(),
                    'creado_fecha'=>date('Y-m-d H:i:s')
                );
                
              
                if(!parent::save($arrayDetail, 'receiving_store_request_details')){
                    return null;
                }       
            }
        }
             
    }       
    
    function _thereIsOrderForToday($options){
        $date = $options['delivery_date'];
        $storeId = $options['store_id'];
        $areaId = $options['area_id'];
        
        $tools = new Tools();
        $date = $tools->setFormatDateToDB($date);
        
        $query = "SELECT id FROM $this->table WHERE delivery_date = '{$date}' AND store_id = '{$storeId}' AND area_id = '{$areaId}' AND status = '1'";
        $result = $this->query($query);
        
        if($result->num_rows >0){
            $result = $result->fetch_object();
            return $result->id;
        }
        
        return null;
    }
    
    /*SE USA PARA PASTELERIA; EN EL REPORTE  PEDIDOS DE SUCURSAL; SE MANDA LLAMAR EN View/Reports/Pdf/store_request*/
    /*SE USA PARA PANADERIA; EN EL REPORTE  PEDIDOS DE SUCURSAL; SE MANDA LLAMAR EN View/Reports/Pdf/bakery_production*/
    public function setStatusInProcessByRangeDate($options){
        if(!isset($options['startDate']) && !isset($options['endDate'])){return null;}              
        
        $date = $this->createFilterFecha(array('fechaInicio'=>$options['startDate'],'fechaFin'=>$options['endDate']),'delivery_date');
        $areaId = $options['area_id'];
        
        $query = "UPDATE $this->table SET in_process = '1' WHERE 1 = 1 $date AND area_id = '$areaId'";
        return $this->query($query);      
    }
    
    public function updateInProcess($options){
        $query = "UPDATE $this->table SET in_process = '{$options['inProcess']}' WHERE id = '{$options['id_store_request']}'";
        $result = $this->query($query);
        
        if($result){
            return true;
        }
        return null;
    }


    public function createFilterFecha($options,$campoFecha = null ){
        if(!isset($options['fechaInicio']) && !isset($options['fechaFin'])){return null;}        
        $fechaInicio = $options['fechaInicio'];
        $fechaFin = $options['fechaFin'];
        $fecha = null;
        $tools = new Tools();
        if($fechaInicio!=null){
            $fechaInicio = $tools->setFormatDateToDB($fechaInicio);
            if($fechaFin!=null){
                $fechaFin = $tools->setFormatDateToDB($fechaFin);
                $fecha .=" AND $campoFecha BETWEEN '{$fechaInicio}' AND '{$fechaFin}' ";
            }else{
                $fecha .=" AND $campoFecha BETWEEN '{$fechaInicio}' AND '{$fechaInicio}' ";
            }
        }elseif($fechaFin!=null){
            $fecha .=" AND $campoFecha BETWEEN '{$fechaFin}' AND '{$fechaFin}' ";
        }
        
        return $fecha;
    }
}