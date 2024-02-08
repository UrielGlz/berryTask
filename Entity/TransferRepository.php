<?php
class TransferRepository extends EntityRepository {

    private $table = 'transfers';
    public $flashmessenger = null;

    private $options = array (
        'id'=>null,
        'date' => null,
        'requested_by'=>null,
        'from_store_id'=>null,
        'to_store_id'=>null,
        'comments'=>null,
        'status'=>null);
    
    private $options_aux = array(
        'userName'=>null,
        'statusName'=>null,
        'formatedDate'=>null,
        'fromStoreName'=>null,
        'toStoreName'=>null,
        'token_form'=>null #Se popula con setOption desde Controller, con post de formulario
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
    
    public function getRequestedBy(){        
        return $this->options['requested_by'];
    }
    
    public function getUserName(){        
        return $this->options_aux['userName'];
    }
    
    public function getStatusName(){        
        return $this->options_aux['statusName'];
    }
    
    public function getDate(){
        return $this->options['date'];
    }
    
    public function getFormatedDate(){
        return $this->options_aux['formatedDate'];
    }
    
    public function getFromStoreName(){
        return $this->options_aux['fromStoreName'];
    }
    
    public function getToStoreName(){
        return $this->options_aux['toStoreName'];
    }
    
    public function getComments(){
        return $this->options['comments'];
    }
    
    public function getStatus(){
        return $this->options['status'];
    }

    public function getOptions(){
        return $this->options;
    }
    
    public function getId() {
       return $this->options['id'];
    }
    
    public function getTokenForm(){
        return $this->options_aux['token_form'];
    }

    public function save(array $data, $table = null) {             
        $tools = new Tools();
        $data['date'] = $tools->setFormatDateTimeToDB($data['date']);
        $data['status'] = '1';  
        
        if($data['id']==null || $data['id']==''){unset($data['id']);}
        
        $this->startTransaction();        
        $rs = parent::save($data, $this->table);        
        $idTransfer = $this->getInsertId();
        $this->setLastInsertId($idTransfer);//Para utilizarlo en el Controller action insert
        
        if($rs){
            $purchaseDetailsTemp = new TransferDetailsTempRepository();

            if($purchaseDetailsTemp->saveDetalles($idTransfer,$this->getTokenForm())){   
                $this->commit();
                $purchaseDetailsTemp->truncate($this->getTokenForm());
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

        $rs = parent::update($id, array('status'=>'2'), $this->table);
        
        if($rs){
            $detallesAfectados = $this->getTransferDetailsSaved($id);
            if(!$this->addInventoryFromTransferDetalles($detallesAfectados)){
                $this->rollback();
                return null;
            }    
            
            return true;
        }

        return null;
    }

    public function update($id, $data, $table = null) {                
        $tools = new Tools();
        $data['date'] = $tools->setFormatDateTimeToDB($data['date']);        
        if(trim($data['status']) == ''){unset($data['status']);}
        
        $this->startTransaction();
        $result = parent::update($id, $data, $this->table);        
        if($result){
            $repository = new TransferDetailsTempRepository();
            if($repository->updateDetalles($id,$this->getTokenForm())){                   
                $this->commit();
                $repository->truncate($this->getTokenForm());        
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
        $select = "SELECT *,"
                . "fxGetStoreName(from_store_id)as fromStoreName, "
                . "fxGetStoreName(to_store_id)as toStoreName, "
                . "DATE_FORMAT(date,'%d/%m/%Y')as formatedDate,"
                . "fxGetStatusName(status,'Transfer')as statusName, "
                . "fxGetUserName(creado_por) as userName "
                . "FROM $this->table "
                . "WHERE id = '$id'";
        $result = $this->query($select);

        if ($result->num_rows>0) {
            $set = $this->resultToArray($result);
            return $set[0];
        }

        return false;
    }

    public function isUsedInRecord($id, array $buscarEn = null,$andWhere = null) {
        return null;        
    }
    
    public function crearTablaDetallesForUser(){
        $login = new Login();        
        $query = "CREATE TABLE IF NOT EXISTS transfer_details_".$login->getId()." 
                 (  
                    `token_form` char(50) NOT NULL,
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `id_detail` int(11) NULL,
                    `id_transfer` int(11) NULL,
                    `id_product` int(11) NOT NULL,
                    `quantity` double NOT NULL,
                    `received` double NULL,
                    `location` int(11) NOT NULL,
                    PRIMARY KEY (`id`)
                 )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        
       $result = $this->query($query);
    }
    
    public function insertDetalle($data){
        $purchaseDetailsTemp = new TransferDetailsTempRepository();        
        return $purchaseDetailsTemp->save($data);
    }
    
    public function getTransferDetails($token_form){
        $login = new Login();
        $query = "SELECT v.*,
                    v.id as idDetailTemp,
                    p.description,
                    p.code,
                    fxGetCategoryDescription(p.category)as category,
                    fxGetBrandDescription(p.brand)as brand,
                    fxGetPresentationDescription(p.presentation)as presentation,
                    fxGetLocationDescription(v.location)as location_name
                  FROM transfer_details_".$login->getId()." v LEFT JOIN products p
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
    
    public function getTransferDetailSaved($id){
        $query = "SELECT c.*,
                    p.code as code
                    FROM transfer_details c LEFT JOIN products p ON c.id_product = p.id
                    WHERE c.id = '$id'";
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result)[0];
            return $result;
        }
        
        return null;
    }
    
    public function getTransferDetailsSaved($id){
        $query = "SELECT c.*,
                    p.code as code,
                    p.description,
                    fxGetPresentationDescription(p.presentation)as presentation_name,
                    fxGetBrandDescription(p.brand)as brand_name,
                    fxGetLocationDescription(c.location)as location_name
                    FROM transfer_details c LEFT JOIN products p ON c.id_product = p.id
                    WHERE c.id_transfer = '$id'";
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result);
            return $result;
        }
        
        return null;
    }
    
    public function setTransferDetailsById($idTransfer,$tokenForm){
        $repository = new TransferDetailsTempRepository();
        
        return $repository->setTransferDetailsById($idTransfer,$tokenForm);
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
    
    public function getListTransfer(){   
        $store_id = null;
        
        $login = new Login();
        if($login->getRole() != '1'){
            /*$store_id = " AND find_in_set(c.from_store_id,'{$login->getStoreId()}') OR find_in_set(c.to_store_id,'{$login->getStoreId()}') ";*/
            $store_id = " AND find_in_set(c.from_store_id,'{$login->getStoreId()}') ";
        }       
        
        $query = "SELECT c.*,
                DATE_FORMAT(c.date,'%d/%m/%Y')as date,
                fxGetStoreName(c.`from_store_id`)as fromStoreName,
                fxGetStoreName(c.`to_store_id`)as toStoreName,
                fxGetStatusName(c.`status`,'Transfer')as statusName
                FROM transfers c
                WHERE  1=1 
                $store_id " 
              . "GROUP BY c.id "
              . "ORDER BY c.id DESC ";

    
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }
        
        return null;
  }
  
    public function addInventoryFromTransferDetalles($detallesAfectados){
        $repoInventario = new InventoryRepository();    

        $array = array();
        foreach($detallesAfectados as $detalle){         
            $row = array(                        
                        'id_product'=>$detalle['id_product'],
                        'quantity'=>$detalle['quantity'],
                        'id_location'=>$detalle['location'],
                        'controller'=>"Salidas-".$detalle['id_output']
                    );
            $array[] = $row;
        }

        return $repoInventario->deleteSubInventory($array);
    }
    
    public function getListTransferPendingToReceieve($id = null){
        if($id != null){$id = " OR id = '$id'";}
        $query = "SELECT *,"
                . "fxGetVendorName(vendor) as vendorName "
                . "FROM $this->table "
                . "WHERE status = '2' "
                . "$id ";
        $result = $this->query($query);
        
        if($result){
            $array = array();
            while($row = $result->fetch_object()){
                $array['purchase-'.$row->id] = 'Compra #'.$row->id." - ".$row->vendorName;
            }            
            return $array;
        }        
        return null;
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