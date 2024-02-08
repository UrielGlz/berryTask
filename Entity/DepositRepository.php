<?php
class DepositRepository extends EntityRepository {

    private $table = 'deposits';
    public $flashmessenger = null;
    
    private $options = array (
        'deposit_number'=>null,
        'date'=>null,
        'store_id'=>null,
        'total'=>null,
        'comments'=>null,
        'status'=>null,
        'attachments'=>null);
    
    private $options_aux = array(
        'token_form'=>null #Se popula con setOption desde Controller, con post de formulario
    );
    
    /*Input double y que no son hide*/
    public $inputs_double = array();
    
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
    
    public function getTokenForm(){
        return $this->options_aux['token_form'];
    }
    
    public function getDepositNumber(){
        return $this->options['deposit_number'];
    }
    
    public function getNexDepositNumber(){
        $query = "SELECT deposit_number FROM $this->table ORDER BY deposit_number DESC LIMIT 1 ";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $result->fetch_object();
            $deposit_number = $result->deposit_number;
            $deposit_number++;
            return $deposit_number;
        }
        return 1;
    }

    public function save(array $data, $table = null) {           
        $purchaseDetailsTemp = new DepositDetailsTempRepository();
        if(!$purchaseDetailsTemp->isThereItemsOnDeposit($this->getTokenForm())){
            $this->flashmessenger->addMessage(array('danger'=>'Debe registrar almenos un detalle para guardar el deposito.'));
            return null;
        }
        
        $tools = new Tools();
        $data['deposit_number'] = $this->getNexDepositNumber();
        $data['date'] = $tools->setFormatDateToDB($data['date']);      
        $data['status'] = '1'; 
        $attachments = $data['attachments'];
        unset($data['attachments']);
        
        $this->startTransaction();        
        $rs = parent::save($data, $this->table);        
        $idCompra = $this->getInsertId();
        $this->setLastInsertId($idCompra);//Para utilizarlo en el Controller action insert
        
        if($rs){
            if($purchaseDetailsTemp->saveDetalles($idCompra,$this->getTokenForm())){   
                $this->commit();
                $purchaseDetailsTemp->truncate($this->getTokenForm());    
                
                $fileManagement = new FileManagement();
                $settings = new SettingsRepository();
                if (isset($attachments['deposit_file']['name'][0]) && $attachments['deposit_file']['name'][0] != '') {
                    $ext = pathinfo($attachments['deposit_file']['name'], PATHINFO_EXTENSION);
                    $attachments['deposit_file']['name'] = $settings->_get('name_for_deposit_file') . '.' . $ext;

                    $fileManagement->saveFile($attachments['deposit_file'], $idCompra, 'deposit');
                }

                if (isset($attachments['attachments']['name'][0]) && $attachments['attachments']['name'][0] != '') {
                    $fileManagement->saveFile($attachments['attachments'], $idCompra, 'deposit');
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

        $rs = parent::update($id, array('status'=>'2'), $this->table);
        
        if($rs){
            return true;
        }
        return null;
    }

    public function update($id, $data, $table = null) {       
        $purchaseDetailsTemp = new DepositDetailsTempRepository();
        if(!$purchaseDetailsTemp->isThereItemsOnDeposit($this->getTokenForm())){
            $this->flashmessenger->addMessage(array('danger'=>'Debe registrar almenos un detalle para guardar el deposito.'));
            return null;
        }

        $tools = new Tools();
        $data['date'] = $tools->setFormatDateToDB($data['date']);               
        unset($data['status'],$data['deposit_number']);    
        $attachments = $data['attachments'];
        unset($data['attachments']);

        $this->startTransaction();
        $result = parent::update($id, $data, $this->table);        
        if($result){
            $purchaseDetailsTemp = new DepositDetailsTempRepository();
            if($purchaseDetailsTemp->updateDetalles($id,$this->getTokenForm())){                   
                $this->commit();
                $purchaseDetailsTemp->truncate($this->getTokenForm());   
                
                $fileManagement = new FileManagement();
                $settings = new SettingsRepository();
                if (isset($attachments['deposit_file']['name'][0]) && $attachments['deposit_file']['name'][0] != '') {
                    $ext = pathinfo($attachments['deposit_file']['name'], PATHINFO_EXTENSION);
                    $attachments['deposit_file']['name'] = $settings->_get('name_for_deposit_file') . '.' . $ext;

                    $fileManagement->saveFile($attachments['deposit_file'], $id, 'deposit');
                }

                if (isset($attachments['attachments']['name'][0]) && $attachments['attachments']['name'][0] != '') {
                    $fileManagement->saveFile($attachments['attachments'], $id, 'deposit');
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
        $select = "SELECT *,"                
                . "DATE_FORMAT(date,'%d/%m/%Y')as formatedDate,"
                . "fxGetStatusName(status,'Deposit')as statusName,"
                . "fxGetStoreName(store_id)as store_name, "
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
        $query = "CREATE TABLE IF NOT EXISTS deposit_details_".$login->getId()." 
                 (  
                    `token_form` char(50) NOT NULL,
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `id_detail` int(11) NULL,
                    `id_deposit` int(11) NULL,
                    `sale_date` date NOT NULL,
                    `sale_date_final` date NOT NULL,
                    `sale_total_cash` double NOT NULL,
                    `sale_comments` TEXT NULL,
                    PRIMARY KEY (`id`)
                 )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        
       $result = $this->query($query);
    }
    
    public function insertDetalle($data){
        $purchaseDetailsTemp = new DepositDetailsTempRepository();        
        return $purchaseDetailsTemp->save($data);
    }
    
    public function getDepositDetails($token_form){
        $purchaseDetailsTemp = new DepositDetailsTempRepository();        
        return $purchaseDetailsTemp->getDepositDetails($token_form);       
    }
    
    public function getDepositDetailSaved($id){
        $query = "SELECT c.*
                    FROM deposit_details c 
                    WHERE c.id = '$id'";
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result)[0];
            return $result;
        }
        
        return null;
    }
    
    public function getDepositDetailsSaved($id){
        $query = "SELECT c.*
                    FROM deposit_details c 
                    WHERE c.id_deposit = '$id'";
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result);
            return $result;
        }
        
        return null;
    }
    
    public function setDepositDetailsById($idCompra,$tokenForm){
        $repository = new DepositDetailsTempRepository();
        
        return $repository->setDepositsDetailsById($idCompra,$tokenForm);
    }
    
    public function getListDeposit($options = null){         
        $limit = null;
        $deposit_number = null;
        $store_id = null;
        $status = null;
        $user_id = null;     
        $date = $this->createFilterFecha($options,"c.date");
        
        $login = new Login();
        if($login->getRole() != '1'){
            $store_id = " AND find_in_set(store_id,'{$login->getStoreId()}')";
        }       
        
        if($options){            
            if(isset($options['deposit_number']) && trim($options['deposit_number'])!= ''){$deposit_number = " AND find_in_set(c.deposit_number,'{$options['deposit_number']}')";}             
            
            if(isset($options['user_id'])){
                if(is_array($options['user_id']) && count($options['user_id']) > 0){
                    $userIds = implode(',', $options['user_id']);
                    $user_id = " AND find_in_set(c.creado_por,'{$userIds}')";
                }else{
                     if(trim($options['user_id'])!= ''){$user_id = " AND find_in_set(c.creado_por,'{$options['user_id']}')";}       
                }           
            }        
            
            if(isset($options['store_id'])){
                if(is_array($options['store_id']) && count($options['store_id']) > 0){
                    $storeIds = implode(',', $options['store_id']);
                    $store_id = " AND find_in_set(c.store_id,'{$storeIds}')";
                }else{
                     if(trim($options['store_id'])!= ''){$store_id = " AND find_in_set(c.store_id,'{$options['store_id']}')";}       
                }           
            }         
            
            if(isset($options['status'])){
                if(is_array($options['status']) && count($options['status']) > 0){
                    $statusIds = implode(',', $options['status']);
                    $status = " AND find_in_set(c.status,'{$statusIds}')";
                }else{
                    if(trim($options['status'])!= ''){$status = " AND find_in_set(c.status,'{$options['status']}')";}                     
                }           
            }
            
            
            
            if(is_null($date) 
                    && is_null($deposit_number)    
                    && is_null($status) 
                    && is_null($user_id)){$limit = " LIMIT 1000";}
            
        }else{
            $limit = " LIMIT 1000";
        }       
        
        
       
        $query = "SELECT c.*,
                DATE_FORMAT(c.date,'%d/%m/%Y')as formatedDate,               
                fxGetStatusName(c.`status`,'Deposit')as statusName,
                fxGetStoreName(store_id)as store_name, 
                CONCAT(DATE_FORMAT(d.sale_date,'%m/%d/%Y'),' - ',DATE_FORMAT(d.sale_date_final,'%m/%d/%Y'))as sales_date,
                fxGetUserName(c.creado_por)as createdByName,
                DATE_FORMAT(c.creado_fecha,'%d/%m/%Y')as formatedCreatedDate
                FROM $this->table c
                LEFT JOIN deposit_details d ON d.id_deposit = c.id
                WHERE  1=1 "
              . "$date "
              . "$store_id "
              . "$deposit_number "
              . "$status "
              . "$user_id "
              . "GROUP BY c.id "
              . "ORDER BY c.id DESC ";

        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }
        
        return null;
  }
    
     public function getDepositNumberById($id){
        $query = "SELECT deposit_number FROM $this->table WHERE id = '$id'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $result->fetch_object();
            return $result->deposit_number;
        }
     }
     
     public function getListFiles($id)
    {
        $fileManagement = new FileManagement();
        return $fileManagement->getStringListFilesByOperationAndPrefix('deposit', $id);
    }
    
    public function getListStatus(){
        $query = "SELECT * FROM status_code WHERE operation = 'Deposit'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $array = array();
            foreach($result as $status){
                $array[$status['code']] = $status['description'];
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