<?php
class PhysicalInventoryRepository extends EntityRepository {

    private $table = 'physical_inventory';
    public $flashmessenger = null;
    private $options = array (
        'date' => null,
        'store_id' => null,
        'comments'=>null,
        'status'=>null);
    
    private $options_aux = array(
        'token_form'=>null,
        'formatedDate'=>null,
        'statusName'=>null
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
    
    public function getFormatedDate(){
        return $this->options_aux['formatedDate'];
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
        $data['status'] = '1'; 
        $data['creado_fecha'] = date('Y-m-d H:i:s');
        $data['creado_por'] = $login->getId();
        
        $this->startTransaction();        
        parent::save($data, $this->table);     
        $idPhysicalInventory = $this->getInsertId();
        $this->setLastInsertId($idPhysicalInventory);
        
        $storeRequestDetallesTemp = new PhysicalInventoryDetailsTempRepository();       
        if($storeRequestDetallesTemp->saveDetalles($idPhysicalInventory,$this->getTokenForm())){ 
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
                
        $this->startTransaction();
        $result = parent::update($id, $data, $this->table);      
        
        if($result){
            $repository = new PhysicalInventoryDetailsTempRepository();
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
                . "fxGetStatusName(status,'PhysicalInventory')as statusName "
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
        
        $query = "CREATE TABLE IF NOT EXISTS physical_inventory_details_".$login->getId()." 
                 (  `id` int(11) NOT NULL AUTO_INCREMENT,
                    `token_form` char(50) NOT NULL,
                    `id_detalle` int(11) NULL,
                    `id_physical_inventory` int(11) NULL,                    
                    `id_product` int(11) NOT NULL,
                    `id_size` int(11) NULL,
                    `id_category` int(11) NULL,
                    `quantity` double NULL,
                    PRIMARY KEY (`id`)
                 )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        
       $result = $this->query($query);
    }
    
    public function getPhysicalInventoryDetalles($token_form,$area_id){
        $login = new Login();
        $areaRepo = new AreaRepository();
        $areaData = $areaRepo->getById($area_id);        
       
        $query = "SELECT s.*,p.description,fxGetSizeDescription(p.size)as sizeName
                  FROM physical_inventory_details_".$login->getId()." s, products p 
                  WHERE s.id_product = p.id  
                  AND token_form = '$token_form'
                  AND find_in_set(id_category,'{$areaData['category_id']}')";
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result);
            return $result;
        }
        
        return null;
    }
    
    public function getPhysicalInventoryDetallesSaved($id){
        $query = "SELECT d.*,
                    fxGetProductName(id_product)as description,
                    fxGetSizeDescription(id_size)as size
                    FROM physical_inventory_details d
                    WHERE id_physical_inventory = '$id' 
                    ORDER BY id";
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result);
            return $result;
        }
        
        return null;
    }
    
    public function getPhysicalInventoryDetallesSavedPDF($id){
        $query = "SELECT d.*,
                    fxGetProductName(id_product)as description,
                    fxGetSizeDescription(id_size)as size
                    FROM physical_inventory_details d
                    WHERE id_physical_inventory = '$id' 
                    ORDER BY FIELD (id_category ,8,4),fxGetProductName(id_product) ASC  ";
                    //ORDER BY FIELD (id_category,'Smart','Audi','Seat')";

        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result);
            
            $areaRepo = new AreaRepository();            
            $categoryArea = $areaRepo->getArrayCategoryArea();
            
            foreach($result as $row){
                $area = $categoryArea[$row['id_category']];
                $array[$area['name']][] = $row;
            }           
            
            return $array;
        }
        
        return null;
    }
    
    public function setPhysicalInventoryDetallesById($idPhysicalInventory,$tokenForm){
        $repo = new PhysicalInventoryDetailsTempRepository();
        return $repo->setPhysicalInventoryDetallesById($idPhysicalInventory, $tokenForm);
    }

    public function getListPhysicalInventory($options = null){
        $physical_inventory_id = null;
        $store_id = null;
        $status = null;
        $limit = null;
        
        $date = $this->createFilterFecha($options,'date');
        
        $login = new Login();
        if($login->getRole() != '1'){
            $store_id = " AND find_in_set(m.store_id,'{$login->getStoreId()}')";
        }       

        if(trim($options['physical_inventory_id']) !== ''){$physical_inventory_id = " AND find_in_set(m.id,'{$options['physical_inventory_id']}')";}        
        if(isset($options['status']) && is_array($options['status']) && count($options['status']) > 0){
            $idsStatus = implode(',', $options['status']);
            $status = " AND find_in_set(status,'$idsStatus')";
        }  
        
        if($options === null){
            $limit = " LIMIT 100 ";
        }
        
        $query = "SELECT m.*,"
                . "fxGetStoreName(store_id) as storeName,"
                . "DATE_FORMAT(date,'%m/%d/%Y')as formatedDate,"
                . "fxGetStatusName(status,'PhysicalInventory')as statusName "
                . "FROM $this->table m LEFT JOIN physical_inventory_details d ON m.id = d.id_physical_inventory "
                . "WHERE 1 = 1  "
                . "$physical_inventory_id "
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
    
    function _thereIsPhysicalInventoryForToday($options){
        $date = $options['date'];
        $storeId = $options['store_id'];
        
        $tools = new Tools();
        $date = $tools->setFormatDateToDB($date);
        
        $query = "SELECT id FROM $this->table WHERE date = '{$date}' AND store_id = '{$storeId}' AND status = '1'";
        $result = $this->query($query);
        
        if($result->num_rows >0){
            $result = $result->fetch_object();
            return $result->id;
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