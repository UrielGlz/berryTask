<?php
class SpecialOrderRepository extends EntityRepository {

    private $table = 'special_orders';
    private $images = null;
    private $options_image = array(
        'allowedExtensions'=>array('image/jpg','image/jpeg','image/png','image/gif','jpg','jpeg','png','gif'),
        'maxFileSizeAllowed'=>16384
    );
    public $flashmessenger = null;
    
    private $options = array (        
        'store_id'=>null,
        'req_number'=>null,
        'date' => null,
        'delivery_date'=>null,
        'customer'=>null,
        'customer_name'=>null,
        'home_service'=>null,
        'address'=>null,
        'city'=>null,
        'phone'=>null,
        'email'=>null,
        'zipcode'=>null,
        'special_quantity'=>null,
        'ammount'=>null,
        'ammount_payments'=>null,       
        'comments'=>null,
        'comments_1'=>null,
        'status'=>null,
        'status_baked'=>null,
        'status_production'=>null,
        'status_payment'=>null,
        'status_delivery'=>null
    );
    
    private $options_aux = array(
        'statusName'=>null,
        'deliveryStatusName'=>null,
        'userName'=>null,
        'customerName'=>null,
        'storeName'=>null,
        'delivery_date_formated'=>null,
        'creado_fecha'=>null,
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
    
    public function setImage($images){
        $this->images = $images;
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
        
    public function getCustomerName(){
        return $this->options_aux['customerName'];
    }

    public function getCreadoPorNombre(){
        return $this->options['creado_por_nombre'];
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
   
    public function getDeliveryStatusName(){
        return $this->options_aux['deliveryStatusName'];
    }
    
    public function getReqNumber() {
       return $this->options['req_number'];
    }
    
    public function getType() {
        return 'Especial';
       #return $this->options['type'];
    }
    
     public function getAmmount(){
        return $this->options['ammount'];
    }
    
    public function getBalance(){
        return $this->options['ammount'] - $this->options['ammount_payments'];
    }
    
    public function getDate() {
       return $this->options['date'];
    }
    
    public function getCreadoFecha(){
       return $this->options_aux['creado_fecha'];
    }
    
    public function getFormatDate(){
        $date = substr($this->getDate(), 0, 10);
        $date = strftime('%m/%d/%Y',  strtotime($date));
        return $date;
    }
    
    public function setDeliveryDate($deliveryDate){
        $this->options['delivery_date'] = $deliveryDate;
    }
    
    public function getDeliveryDate() {
       return $this->options['delivery_date'];
    }
  
    
    public function getFormatDeliveryDate(){
        return $this->options_aux['delivery_date_formated'];
    }
    
    public function getCurrentDate(){
        $currentDate = $this->getCreadoFecha();
        $minDate = date("Y-m-d",strtotime($currentDate)); 
        
        return $minDate;
    }
    
    public function getMinDate(){
        $currentDate = $this->getCreadoFecha();
        $minDate = date("Y-m-d",strtotime($currentDate."- 1 days")); 
        
        return $minDate;
    }
    
    public function getTelefono() {
       return $this->options['phone'];
    }   
    
    public function getAddress() {
       return $this->options['address'];
    }
    
    public function getAddress1() {
       return $this->options['address_1'];
    }
    
    public function getCity() {
       return $this->options['city'];
    }
    
    public function getZipCode(){
        return $this->options['zipcode'];
    }
    
    public function getComments() {
       return $this->options['comments'];
    }
    
    public function getComments1() {
       return $this->options['comments_1'];
    }   
    
        
    public function getStoreName(){
        return $this->options_aux['storeName'];
    }
    
    public function getTokenForm(){
        return $this->options_aux['token_form'];
    }
    
    public function getListPayments(){
        $repo = new VentaRepository();
        return $repo->getPaymentsByIdSpecialRequisition($this->getId());
    }
    
    public function getSaldoPendiente($idReq){
        $totalPayments = $this->getTotalPayments($idReq);
        $saldoPendiente = $this->getAmmount() - $totalPayments;
        if($saldoPendiente > 0){
            $this->updateString(array('status_payment'=>'1','ammount_payments'=>$totalPayments), " id = '$idReq'");
            
        }else{
             $this->updateString(array('status_payment'=>'2','ammount_payments'=>$totalPayments), " id = '$idReq'");
        }
        
        return $saldoPendiente;
    }
        
    public function showImages(){
        $images = $this->getImages($this->getReqNumber());
        if(!$images){ return null;}
        
        $string = "<div class='galleryForSpecialRequisition'>";        
        foreach($images as $image){     
            $string .= '<div class="thumbail col-md-6">'
                    . '<img src="data:'.$image['type'].';base64,'.base64_encode( $image['image'] ).'" style="width:100%"/>'
                    . '<div class="col-lg-12 col-md-12 col-xs-12 text-right">'
                        . '<a class="btn btn-xs btn-danger" data-id="'.$image['id'].'" onclick="deleteImage(this)"><i class="fa fa-trash"></i> Eliminar</a>'
                    . '</div>'
                    . '</div>';
            
        }
        
        return $string."</div>";
    }
    
    public function showImagesForProductionAndBakedPlan(){
        /*No muestra boton para eliminar*/
        $images = $this->getImages($this->getReqNumber());
        if(!$images){ return null;}
        
        $string = "<div class='galleryForSpecialRequisition'>";        
        foreach($images as $image){     
            $string .= '<div class="thumbail col-md-6">'
                    . '<img src="data:'.$image['type'].';base64,'.base64_encode( $image['image'] ).'" style="width:100%"/>'
                    . '</div>';            
        }
        
        return $string."</div>";
    }
    
    public function resizeImage($image,$type){      
        $maxWidth = 480;
        
        switch($type){
            case 'image/jpeg':
            case 'image/jpg':
                $originImage = imagecreatefromjpeg($image);
                break;
            
            case 'image/png': 
                $originImage = imagecreatefrompng($image);
                break;
                
            case 'image/gif': 
                $originImage = imagecreatefromgif($image);
                break;
        }
        
        $originWidth = imagesx($originImage);
        $originHeight = imagesy($originImage);
        
        
        if($originWidth > $originHeight){
            #Es una imagen horizontal    
            $newWidth = $maxWidth;
            $newHeight = $maxWidth * $originHeight/$originWidth;
        }else{
            #Es una imagen vertical
            $newHeight = $maxWidth;
            $newWidth = $maxWidth * $originWidth/$originHeight;
        }
        
        $newImage = imagecreatetruecolor($newWidth, $newHeight); #tamano de nueva imagen
        imagecopyresized($newImage, $originImage, 0, 0, 0, 0, $newWidth, $newHeight, $originWidth, $originHeight);

        switch($type){
            case 'image/jpeg':
            case 'image/jpg':
                imagejpeg($newImage, $image);
                break;
            
            case 'image/png': 
                imagepng($newImage, $image);
                break;
                
            case 'image/gif': 
                imagegif($newImage, $image);
                break;
        }       
    }
    
    public function saveImage($reqNumber){      
        try {
            $upload = new UploadFile();     
            if($this->images['name'][0] != ''){
                $images = array();

                for($i=0; $i <= count($this->images['name'])-1; $i++){
                    if (in_array($this->images['type'][$i], $this->options_image['allowedExtensions']) && $this->images['size'][$i] <= $this->options_image['maxFileSizeAllowed'] * 1024){
                        $imagen_temporal = $this->images['tmp_name'][$i];

                        // Tipo de archivo
                        $tipo = $this->images['type'][$i]; 
                        $size = $this->images['size'][$i];                             

                        $file = array(
                            'name' => "Image_$i.".$upload->getExtension($this->images['name'][$i]),
                            'tmp_name' => $this->images['tmp_name'][$i],
                            'size' =>  $this->images['size'][$i],
                            'type' => $this->images['type'][$i]
                        );

                        $upload->uploadFile($file);
                        $uploadedFile = $upload->getUploadedFile();
                        $uploadedFile = end($uploadedFile);
                        $this->resizeImage($uploadedFile, $tipo);                               

                        // Leemos contenido y escapamos caracteres especiales
                        $data = file_get_contents($uploadedFile);
                        //$data = addslashes($data);

                        $array = array(
                            'req_number'=>$reqNumber,
                            'image'=>$data,
                            'type'=>$tipo,
                            'size'=>$size
                        );

                        parent::save($array, 'images');

                        /*Array Para server principal */
                        $images[] = array(
                            'req_number'=>$reqNumber,
                            'image'=>$data,
                            'size' =>  $this->images['size'][$i],
                            'type'=> $this->images['type'][$i]
                        );      

                    }else{
                        $this->flashmessenger->addMessage(array(
                        'danger'=>$this->_getTranslation("Formato de archivo no permitido o excede el tamaño limite de {$this->options_image['maxFileSizeAllowed']} Kbytes.")));
                        return null;
                    }
                }
            }
            return true;
        } catch (Exception $exc) {
            echo $this->flashmessenger->addMessage(array('danger'=>$exc->getMessage()));
            return null;
        }

        
    }
    
    public function getImages($reqNumber){
        $query = "SELECT * FROM images WHERE req_number = '$reqNumber'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }
        return null;
    }

    public function save(array $data, $table = null) {             
        $login = new Login();
        $tools = new Tools();
        $customerRepo = new CustomerRepository();
        $dataCustomer = $customerRepo->getById($data['customer']);
        
        $data['creado_por'] = $login->getId();
        $data['creado_fecha'] = date('Y-m-d H:i:s');
        $data['date'] = $tools->setFormatDateToDB($data['date']);
        $data['customer_name'] = $dataCustomer['name'];
        $data['status'] = '1';       
        $data['status_baked'] = '1';
        $data['status_production'] = '1';
        $data['status_delivery'] = '1';
        $data['status_payment'] = '1';
        
        if(trim($data['delivery_date']) == ''){
            unset($data['delivery_date']);
        }else{
            $data['delivery_date'] = $tools->setFormatDateTimeToDB($data['delivery_date']);
        }
        
        $this->startTransaction();        
        parent::save($data, $this->table);        
        
        $storeInDetailsTemp = new SpecialOrderDetailsTempRepository();
        $idRequisition = $this->getInsertId();
        $this->setLastInsertId($idRequisition);//Para utilizarlo en el Controller action insert
        
        $reqNumber = $this->getPrefixNumberRequisition().str_pad($idRequisition, 7, '0', STR_PAD_LEFT);
        $this->updateString(array('req_number'=>$reqNumber), " id = '$idRequisition' ");
        
        if($storeInDetailsTemp->saveDetalles($idRequisition,$reqNumber,$this->getTokenForm())){ 
            if(!$this->saveImage($reqNumber)){
                $this->rollback();
                return null;
            }
            
            $this->commit();
            $storeInDetailsTemp->truncate($this->getTokenForm());

            return true;
        }
        
        $this->rollback();    
        $this->flashmessenger->addMessage(array(
            'danger'=>$this->_getTranslation('Error. Intenta nuevamente o contacta a tu proveedor de sistemas.')));
        return null;        
    }
    
    public function delete($id, $table = null) {
        $currentData = $this->getById($id);
        if($currentData['status_delivery'] == '2'){
            $this->flashmessenger->addMessage(array('danger'=>'Esta orden especial no puede ser eliminada. Ya fue entregada.'));
            return null;
        }
        
        return parent::update($id, array('status'=>'2'), $this->table);
    }

    public function update($id, $data, $table = null) {              
        $tools = new Tools();
        $login = new Login();
        $customerRepo = new CustomerRepository();
        $dataCustomer = $customerRepo->getById($data['customer']);
        
        $data['customer_name'] = $dataCustomer['name'];
        $data['ultima_mod_por'] = $login->getId();
        $data['ultima_mod_fecha'] = date('Y-m-d H:i:s');
        $data['date'] = $tools->setFormatDateToDB($data['date']);
        
        if(trim($data['delivery_date']) == ''){
            unset($data['delivery_date']);
        }else{
            $data['delivery_date'] = $tools->setFormatDateTimeToDB($data['delivery_date']);
        }
        
        $reqNumber = $data['req_number'];
        unset($data['status'],$data['status_baked'],$data['status_production'],$data['status_payment'],$data['req_number']);
        $this->startTransaction();
 
        /*Verificar si se esta cambiando status_delivery*/
        $currentData = $this->getById($id);
        $changeStatus = null;
        if($currentData['status_delivery'] !== $data['status_delivery']){$changeStatus = true;}
        
        $result = parent::update($id, $data, $this->table);
        
        if($result){
            $repository = new SpecialOrderDetailsTempRepository();
            if($repository->updateDetalles($id,$reqNumber,$this->getTokenForm())){       
                if(!$this->saveImage($reqNumber)){
                    $this->rollback();
                    return null;
                }
                
                /*
                if($changeStatus){
                    if($data['status_delivery'] == '2'){
                        $this->descontarInventarioBySRId($id);
                    }elseif($data['status_delivery'] == '1'){
                        $this->agregarInventarioBySRId($id);
                    }
                }*/
   
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
    //modificar para pxgetparque
    public function getById($id, $table = null,$selectAux = null) {
        $select = "SELECT *,"
                . "DATEDIFF(CURDATE(),DATE(creado_fecha))as antiguedad, "   
                . "DATE_FORMAT(delivery_date,'%m/%d/%Y %h:%i %p')as delivery_date_formated,"                
                . "fxGetUserName(creado_por)as userName,"
                . "customer_name as customerName,"
                . "fxGetStoreName(store_id)as storeName,"
                . "fxGetStatusName(status,'Special order')as statusName, "
                . "fxGetStatusName(status_delivery,'Special order - status_delivery')as deliveryStatusName "
                . "FROM $this->table "
                . "WHERE id = '$id'";
        
        //var_dump($select); exit;
        $result = $this->query($select);
        
        if ($result->num_rows>0) {
            $set = $this->resultToArray($result);
            return $set[0];
        }

        return false;
    }
    
    public function getByReqNumber($id, $table = null,$selectAux = null) {
        $select = "SELECT *,"
                . "DATE_FORMAT(date,'%m/%d/%Y')as dateFormated,"
                . "fxGetUserName(creado_por)as userName,"
                . "fxGetCustomerName(customer)as customerNameAux,"
                . "fxGetStatusName(status,'Special order')as statusName, "
                . "fxGetStatusName(status,'Special order - status_delivery')as deliveryStatusName "

                . "FROM $this->table "
                . "WHERE req_number = '$id'";
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
        $query = "CREATE TABLE IF NOT EXISTS special_order_details_".$login->getId()." 
                 (  
                    `token_form` char(50) NOT NULL,
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `id_detail` int(11) NULL,
                    `id_special_order` int(11) NULL,
                    `type` varchar(25) NOT NULL,
                    `id_product` int(11) NOT NULL,
                    `quantity` double NOT NULL,
                    `price` double NOT NULL,
                    `number_of_cake` CHAR(5) NULL,
                    `multiple` INT(11) NULL,
                    PRIMARY KEY (`id`)
                 )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        
       $result = $this->query($query);
    }
    
    public function insertDetalle($data){
        $storeInDetailsTemp = new SpecialOrderDetailsTempRepository();
        
        return $storeInDetailsTemp->save($data);
    }
    
    public function getRequisitionDetails($tokenForm){
        $settings = new SettingsRepository();
        $categoryExtra = $settings->_get('id_category_for_extra_cakes');
        $login = new Login();        
        $query = "SELECT 
                    id,
                    id_detail,
                    type,
                    product,
                    IF(`type`='Line',fxGetProductName(product),null)as product_name,
                    size_id,
                    shape_id, 
                    category,
                    description,
                    MAX(pan_id)AS 'pan_id',
                    MAX(relleno_id)AS 'relleno_id',
                    MAX(decorado_id)AS 'decorado_id',
                    MAX(pan_name)AS 'pan_name',
                    MAX(relleno_name)AS 'relleno_name',
                    MAX(decorado_name)AS 'decorado_name',
                    fxGetSizeDescription(size_id)as size_name,
                    fxGetShapeDescription(shape_id)as shape_name,                    
                    quantity,
                    SUM(price)as price,
                    number_of_cake,
                    multiple  
                    FROM (
                        SELECT
                        id,
                        id_detail,
                        type,
                        product,
                        multiple, 
                        size_id,
                        shape_id,
                        category,
                        description,
                        CASE WHEN type = 'Special' AND category = '1' THEN product ELSE '-' END AS 'pan_id',
                        CASE WHEN type = 'Special' AND category = '2' THEN product ELSE '-' END AS 'relleno_id',
                        CASE WHEN type = 'Special' AND category = '3' THEN product ELSE '-' END AS 'decorado_id',
                        CASE WHEN type = 'Special' AND category = '1' THEN description ELSE '-' END AS 'pan_name',
                        CASE WHEN type = 'Special' AND category = '2' THEN description ELSE '-' END AS 'relleno_name',
                        CASE WHEN type = 'Special' AND category = '3' THEN description ELSE '-' END AS 'decorado_name',
                        CASE WHEN type = 'Special' AND category != '$categoryExtra' THEN 1 ELSE quantity END AS quantity,
                        price,
                        number_of_cake
                        FROM (
                                SELECT 
                                v.id,
                                v.id_detail,
                                v.type,
                                v.id_product,
                                v.quantity,
                                v.price,
                                number_of_cake,
                                IFNULL(v.multiple,UUID())AS multiple,
                                v.id as idDetailTemp, 
                                IF(v.type = 'Special',s.size,p.size)as size_id,
                                IF(v.type = 'Special',s.shape,null)as shape_id,
                                IF(v.type = 'Special',s.id,p.id)as product,
                                IF(v.type = 'Special',null,p.code)as code,
                                IF(v.type = 'Special',s.category,p.category)as category,
                                IF(v.type = 'Special',s.flavor,p.description)as description

                                FROM special_order_details_{$login->getId()} v 
                                LEFT JOIN products p ON v.id_product = p.id AND v.type = 'Line' 
                                LEFT JOIN product_slices s ON v.id_product = s.id AND v.type = 'Special' 
                                WHERE v.token_form = '{$tokenForm}' 
                                ORDER BY multiple ASC,category ASC 
                        )AS t
                    )AS x GROUP BY multiple ORDER BY id ASC";
        
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result);
            return $result;
        }
        
        return null;
    }    
    
    public function getRequisitionDetailsForSpecial($idRequisition){      
        $query = "SELECT v.*,
                    s.category,
                    v.id_product as product,                    
                    v.id as idDetailTemp,
                    s.id as s_product,                      
                    fxGetCategoryDescription(s.category)as s_category,
                    s.flavor as s_description,
                    fxGetSizeDescription(s.size)as s_size,
                    fxGetShapeDescription(s.shape)as s_shape
                  FROM special_order_details v 
                      LEFT JOIN product_slices s ON v.id_product = s.id 
                      WHERE v.type = 'Special' AND id_special_order = '$idRequisition'
                  ORDER BY multiple ASC,category ASC";
        
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result);
            return $result;
        }
        
        return null;
    }    
    
    public function getRequisitionDetailsSaved($id,$forProductionScreen = null){        
        if($forProductionScreen == null){
           $query = "SELECT
                    v.id_special_order,
                    v.id as idDetailTemp,
                    v.`type`,
                    v.multiple,
                    v.id_product,
                    p.id as product,
                    p.code as code,
                    p.description as description,
                    fxGetSizeDescription(p.size)as size,
                    fxGetCategoryDescription(p.category)as category,
                    v.quantity,v.`status`,
                    p.sale_price as price
                    FROM special_order_details v,products p 
                    WHERE v.id_product = p.id AND v.`type` = 'Line'
                    AND v.id_special_order = '$id'
                    UNION
                    SELECT 
                    v.id_special_order,
                    v.id as idDetailTemp,
                    v.`type`,
                    v.multiple,
                    v.id_product,
                    s.id as product,
                    '' as code,
                    s.flavor as description,
                    fxGetSizeDescription(s.size)as size,
                    fxGetCategoryDescription(s.category)as category,
                    v.quantity,v.`status`,
                     s.price as price
                    FROM special_order_details v,product_slices s 
                    WHERE v.id_product = s.id AND v.`type` = 'Special'
                    AND v.id_special_order = '$id'";
        }else{
            $query = "SELECT v.id_special_order,v.id as idDetailTemp,v.`type`,       
                    v.id_product,
                    p.id as product,
                    p.code as code,
                    p.description as description,
                    fxGetSizeDescription(p.size)as size,
                    fxGetCategoryDescription(p.category)as category,
                    v.quantity,
                     p.sale_price as price
                    FROM special_order_details v,products p 
                    WHERE v.id_product = p.id AND v.`type` = 'Line'
                    AND v.id_special_order = '$id'
                    UNION
                    SELECT r.id as id_special_order,r.id as idDetailTemp,'Special' as `type`,
                    '0' as id_product,
                    '0' as product,
                    '' as code,
                    'Especial' as description,
                    '' as size,
                    '' as category,
                    r.special_quantity,
                    SUM(d.price * d.quantity) as price
                    FROM special_orders r,special_order_details d
                    WHERE r.id = d.id_special_order AND r.id = '$id' AND type = 'Special'";
        }        
        
        $result = $this->query($query);
        
        if($result){
            $result = $this->resultToArray($result);
            return $result;
        }
        
        return null;
    }
    
    public function setRequisitionDetailsById($idCompra,$tokenForm){
        $repository = new SpecialOrderDetailsTempRepository();
        
        return $repository->setRequisitionDetailsById($idCompra,$tokenForm);
    }
    
    public function truncateIfIsEditInfo(){
        $repository = new SpecialOrderDetailsTempRepository();
        $repository->truncateIfIsEditInfo();
        
    }
    
    public function getProductById($idProducto,$type){
        if($type=='Line'){
           $query = "SELECT *,sale_price as price FROM products WHERE id = '$idProducto' LIMIT 1"; 
        }elseif($type=='Special'){
           $query = "SELECT * FROM product_slices WHERE id = '$idProducto' LIMIT 1";
        }
        
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $this->resultToArray($result);
            return $result[0];
        }        
        return null;
    }
    
      public function getListRequisitions($options){         
        $customer  = null;
        $home_service  = null;
        $status = null;
        $status_production = null;
        $status_delivery = null;
        $date = null;
        $store_id = null;
        $limit = null;   
        
        $login = new Login();
        if($login->getRole() != '1'){
            $store_id = " AND find_in_set(r.store_id,'{$login->getStoreId()}')";
        }       
        
        if($options != null){          
            $date = $this->createFilterFecha($options, 'delivery_date');     
           if(isset($options['store_id']) && $options['store_id']!=''){
               $stringStoreId = implode(',', $options['store_id']);
               $store_id = " AND find_in_set(store_id,'$stringStoreId')";
           }
            if(isset($options['customer']) && $options['customer']!=''){$customer = " AND customer_name like '%".$options['customer']."%'";}
            if(isset($options['home_service']) && $options['home_service']!='0'){$home_service = " AND home_service = '".$options['home_service']."'";}
            if(isset($options['status']) && $options['status']!='0'){$status = " AND r.status = '".$options['status']."'";}
            if(isset($options['status_production']) && $options['status_production']!='0'){$status_production = " AND status_production = '".$options['status_production']."'";}         
            if(isset($options['status_delivery']) && $options['status_delivery']!='0'){$status_delivery = " AND status_delivery = '".$options['status_delivery']."'";}         
        }else{
          $limit = " LIMIT 150 ";
        }        
        
        /*Filtro para roscas*/
        $roscas = null;
        if(isset($options['solo_roscas'])){$roscas = " AND s.size = 18 ";} /* cuando se manda desde Plan horneado roscas*/
        if(isset($options['sin_roscas'])){$roscas = "  AND (s.size != 18 OR s.size IS null ) ";} /* cuando se manda desde Plan horneado*/
 
       $query = "SELECT r.*,
                DATE_FORMAT(r.date,'%m/%d/%Y')as date,                
                DATE_FORMAT(r.delivery_date,'%m/%d/%Y %h:%i %p')as delivery_date,
                DATE_FORMAT(delivery_date,'%m/%d/%Y')as delivery_date_american_format,
                customer_name as customerName,
                fxGetStoreName(r.store_id) as storeName,
                r.phone,
                fxGetStatusName(r.`status`,'Special order')as statusName,      
                fxGetStatusName(r.`status_baked`,'Special order - status_baked')as statusBakedName,
                fxGetStatusName(r.`status_production`,'Special order - status_production')as statusProductionName,
                fxGetStatusName(r.`status_delivery`,'Special order - status_delivery')as statusDeliveryName,
                fxGetStatusName(r.`status_payment`,'Special order - status_payment')as statusPaymentName
                FROM special_orders r
                LEFT JOIN special_order_details d ON r.id = d.id_special_order 
                LEFT JOIN product_slices s ON d.id_product = s.id
                WHERE 1 = 1 
                $roscas "
              . "$customer "
              . "$store_id "
              . "$home_service "
              . "$status "
              . "$status_production "
              . "$status_delivery "
              . "$date " 
              . "GROUP BY r.id "
              . "ORDER BY r.id DESC $limit ";
    
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }        
        return null;
    }
  
  public function getListRequisitionsDecorado($options = null){    
      /*Filtro para roscas*/
        $roscas = null;
        if(isset($options['solo_roscas'])){$roscas = " AND s.size = 18 ";} /* cuando se manda desde Plan horneado roscas*/
        if(isset($options['sin_roscas'])){$roscas = " AND (s.size != 18 OR s.size IS null ) ";} /* cuando se manda desde Plan horneado*/        
        
      $query = "SELECT r.*,
                DATE_FORMAT(r.date,'%m/%d/%Y')as date,                
                DATE_FORMAT(r.delivery_date,'%m/%d/%Y %h:%i %p')as delivery_date,
                DATE_FORMAT(delivery_date,'%m/%d/%Y')as delivery_date_american_format,
                customer_name as customerName,
                fxGetStoreName(r.store_id) as storeName,
                r.phone,
                fxGetStatusName(r.`status`,'Special order')as statusName,      
                fxGetStatusName(r.`status_baked`,'Special order - status_baked')as statusBakedName,
                fxGetStatusName(r.`status_production`,'Special order - status_production')as statusProductionName,
                fxGetStatusName(r.`status_delivery`,'Special order - status_delivery')as statusDeliveryName,
                fxGetStatusName(r.`status_payment`,'Special order - status_payment')as statusPaymentName
                FROM special_orders r
                LEFT JOIN special_order_details d ON r.id = d.id_special_order
                LEFT JOIN product_slices s ON d.id_product = s.id
                WHERE 1 = 1
                $roscas
                AND ( status_production = '1' OR DATE(delivery_date) > DATE_SUB(CURDATE(),INTERVAL 3 DAY) ) 
                AND r.status != '2' "
              . "GROUP BY r.id "
              . "ORDER BY r.id DESC";

        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }        
        return null;
  }
  
  public function getListRequisitionsHorneado($options = null){     
       /*Filtro para roscas*/
        $roscas = null;
        if(isset($options['solo_roscas'])){$roscas = " AND s.size = 18 ";} /* cuando se manda desde Plan horneado roscas*/
        if(isset($options['sin_roscas'])){$roscas = " AND (s.size != 18 OR s.size IS null ) ";} /* cuando se manda desde Plan horneado*/
        
      $query = "SELECT r.*,
                DATE_FORMAT(r.date,'%m/%d/%Y')as date,                
                DATE_FORMAT(r.delivery_date,'%m/%d/%Y %h:%i %p')as delivery_date,
                DATE_FORMAT(delivery_date,'%m/%d/%Y')as delivery_date_american_format,
                customer_name as customerName,
                fxGetStoreName(r.store_id) as storeName,
                r.phone,
                fxGetStatusName(r.`status`,'Special order')as statusName,      
                fxGetStatusName(r.`status_baked`,'Special order - status_baked')as statusBakedName,
                fxGetStatusName(r.`status_production`,'Special order - status_production')as statusProductionName,
                fxGetStatusName(r.`status_delivery`,'Special order - status_delivery')as statusDeliveryName,
                fxGetStatusName(r.`status_payment`,'Special order - status_payment')as statusPaymentName
                FROM special_orders r
                LEFT JOIN special_order_details d ON r.id = d.id_special_order
                LEFT JOIN product_slices s ON d.id_product = s.id
                WHERE 1 = 1
                $roscas
                AND ( status_baked = '1' OR DATE(delivery_date) > DATE_SUB(CURDATE(),INTERVAL 3 DAY) )
                AND r.status != '2' "                
              . "GROUP BY r.id "
              . "ORDER BY r.id DESC";
    //echo $query;
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }        
        return null;
  }
  
  public function getTotalRequiredAndProduced(){
      /* Se debe setear $this->setId para que este disponible en este query*/
      $query = "SELECT SUM(totalRequired)as totalRequired,SUM(totalProduced)as totalProduced FROM ("
                . "SELECT "
                . "SUM(quantity)as totalRequired,"
                . "SUM(produced)as totalProduced "
                . "FROM special_order_details "
                . "WHERE id_special_order = '{$this->getId()}' AND `type` = 'Line' "
                . "UNION "
                . "SELECT "
                . "special_quantity as totalRequired, "
                . "produced as totalProduced "
                . "FROM $this->table "
                . "WHERE id = '{$this->getId()}')as t";              
              
      $result = $this->query($query);
      
      if($result){
          $result = $result->fetch_object();
          return $result;
      }
      return null;
  }
  
    
  public function getPrefixNumberRequisition(){
      $query = "SELECT * FROM settings WHERE `key` = 'operations_prefix'";
      $result = $this->query($query);
      
      if($result){
          $result = $result->fetch_object();
          return $result->value;
      }
      return null;
  }
  
   public function removeAllowEdit($idSpecialRequisition){
        parent::update($idSpecialRequisition, array('allow_edit'=>'0'), $this->table);
    }
    
    public function getTotalPayments($idReq){
        $query = "SELECT *,"
                . "IFNULL(SUM(monto),0)as monto "
                . "FROM ventas "
                . "WHERE id_special_order = '$idReq' "
                . "AND status != 3 ";
        
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $result->fetch_object();
            return $result->monto;
        }
        
        return 0;
    }
    
    public function descontarInventarioBySRId($id){
        $query = "SELECT srd.* "
                . "FROM special_orders sr, special_order_details srd  "
                . "WHERE sr.id = srd.id_special_order "
                . "AND type = 'Line' "
                . "AND sr.id = '$id'";
        
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $this->resultToArray($result);
            
            $repoInventario = new InventoryRepository();
            foreach($result as $producto){
                $repoInventario->subInventory(array(
                    'id_product'=>$producto['id_product'],
                    'quantity'=>$producto['quantity'],
                    'controller'=>"Ordenes especiales-$id"
                ));
            }
        }
        
        return true;
    }
    
    public function agregarInventarioBySRId($id){
        $query = "SELECT srd.* "
                . "FROM special_orders sr, special_order_details srd  "
                . "WHERE sr.id = srd.id_special_order "
                . "AND type = 'Line' "
                . "AND sr.id = '$id'";
        
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $this->resultToArray($result);
            
            $repoInventario = new InventoryRepository();
            foreach($result as $producto){
                $repoInventario->addInventory(array(
                    'id_product'=>$producto['id_product'],
                    'quantity'=>$producto['quantity'],
                    'controller'=>"Ordenes especiales-$id"
                ));
            }
        }
        
        return true;
    }
    
     public function getMaxMinDateFromIdsRequisitions($idsRequisition){
        $idsRequisition = implode(',',$idsRequisition);
        $query = "SELECT "
               . "DATE_FORMAT(MIN(delivery_date),'%m/%d/%Y')as startDate,"
               . "DATE_FORMAT(MAX(delivery_date),'%m/%d/%Y')as endDate "
               . "FROM $this->table "
               . "WHERE find_in_set(id,'$idsRequisition')";
        
        $result = $this->query($query);
        if($result->num_rows >0){
            $result = $this->resultToArray($result);
            return $result[0];
        }
    }
    
     public function getListRequisitionsByIds($ids){          
        $query = "SELECT r.*,
                fxGetStoreName(r.store_id)as storeName,
                DATE_FORMAT(r.date,'%d/%m/%Y')as date,                
                DATE_FORMAT(r.delivery_date,'%d/%m/%Y')as delivery_date,
                DATE_FORMAT(r.delivery_date,'%H:%i ')as delivery_time,
                customer_name as customerName,
                r.phone,
                fxGetStatusName(r.`status`,'Special-requisition')as statusName,                
                fxGetStatusName(r.`status_production`,'Special order - status_production')as statusProductionName,
                fxGetStatusName(r.`status_delivery`,'Special order - status_delivery')as statusDeliveryName,
                fxGetStatusName(r.`status_payment`,'Special order - status_payment')as statusPaymentName
                FROM special_orders r, special_order_details d
                WHERE r.id = d.id_special_order
                AND find_in_set(r.id,'$ids')" 
              . "GROUP BY r.id "
              . "ORDER BY r.date ASC ";
    
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }        
        return null;
  }
  
  public function getSpecialProductionPlan($idRequisitions){
      $settings = new SettingsRepository();
      $id_categories_for_special_orders = $settings->_get('id_categories_for_special_orders');
        $query = "SELECT * FROM ("
            . "SELECT "
            . "s.category,"
            . "sr.req_number,"
            . "multiple,"
            . "SUM(srd.quantity)as quantity,"
            . "s.flavor, "
            . "s.id as id_slice, "
            . "fxGetSizeDescription(s.size)as sizeName, "
            . "fxGetShapeDescription(s.shape)as shapeName, "
            . "GROUP_CONCAT(srd.number_of_cake)as number_of_cake "
            . "FROM $this->table sr, special_order_details srd, product_slices s "
            . "WHERE sr.id = srd.id_special_order "
            . "AND srd.id_product = s.id "
            . "AND find_in_set(sr.id,'$idRequisitions') "
            . "AND sr.status = 1 "
            . "AND srd.type = 'Special' "
            . "AND find_in_set(s.category,'$id_categories_for_special_orders')"
            . "GROUP BY srd.req_number,s.category,srd.id "
                
            . "UNION "
                
            . "SELECT "
            . "s.category,"
            . "sr.req_number,"
            . "multiple,"
            . "SUM(srd.quantity * p.quantity)as quantity,"
            . "s.flavor, "
            . "s.id as id_slice, "
            . "fxGetSizeDescription(s.size)as sizeName, "
            . "fxGetShapeDescription(s.shape)as shapeName, "
            . "GROUP_CONCAT(srd.number_of_cake)as number_of_cake "
            . "FROM $this->table sr "
            . "LEFT JOIN special_order_details srd ON sr.id = srd.id_special_order "
            . "LEFT JOIN products_details p ON srd.id_product = p.id_product "
            . "LEFT JOIN product_slices s ON p.id_slice = s.id "
            . "WHERE find_in_set(sr.id,'$idRequisitions') "
            . "AND sr.status = 1 "
            . "AND srd.type = 'Line' "
            . "AND find_in_set(s.category,'$id_categories_for_special_orders')"
            . "GROUP BY srd.req_number,s.category,srd.id )r "            
            . "ORDER BY multiple,flavor,sizeName";
        
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $this->resultToArray($result);
            return $result;
        }
        return null;
    }
    
     public function getBakedPlan($idRequisitions){
        $query = "SELECT SUM(quantity)as quantity,flavor,id_slice,shapeName,sizeName,number_of_cake FROM ("
            . "SELECT "
            . "SUM(srd.quantity)as quantity,"
            . "s.flavor, "
            . "s.id as id_slice, "
            . "fxGetSizeDescription(s.size)as sizeName,"
            . "fxGetShapeDescription(s.shape)as shapeName,"
            . "GROUP_CONCAT(srd.number_of_cake)as number_of_cake,"
            . "UUID() "
            . "FROM $this->table sr, special_order_details srd, product_slices s "
            . "WHERE sr.id = srd.id_special_order "
            . "AND srd.id_product = s.id "
            . "AND find_in_set(sr.id,'$idRequisitions') "
            . "AND sr.status = 1 "
            . "AND srd.type = 'Special' "
            . "AND s.category = '1' "
            . "GROUP BY s.id "
                
            . "UNION "
                
            . "SELECT "
            . "SUM(srd.quantity * p.quantity)as quantity,"
            . "s.flavor, "
            . "s.id as id_slice, "
            . "fxGetSizeDescription(s.size)as sizeName,"
            . "fxGetShapeDescription(s.shape)as shapeName,"
            . "''as number_of_cake,"
            . "UUID() "
            . "FROM $this->table sr, special_order_details srd, products_details p, product_slices s "
            . "WHERE sr.id = srd.id_special_order "
            . "AND srd.id_product = p.id_product "
            . "AND p.id_slice = s.id "
            . "AND find_in_set(sr.id,'$idRequisitions') "
            . "AND sr.status = 1 "
            . "AND srd.type = 'Line' "
            . "AND s.category = '1' "
            . "GROUP BY s.id )r "            
            . "GROUP BY id_slice ORDER BY flavor,sizeName";
        
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $this->resultToArray($result);
            return $result;
        }
        return null;
    }
    
    public function crearPDF(){
        $pdf = new SpecialOrderPDF($this->getId(),true);
        return $pdf->getPathFileCreated();           
    }    
    
    public function getSpecialOrderByDateRange($start,$end,$group = null){
        if($group){$group = " GROUP BY store_id";}
        
        $date = $this->createFilterFecha(array('startDate'=>$start,'endDate'=>$end),'date');
        $query = "SELECT "
                . "fxGetStoreName(store_id)as store_name, "
                . "SUM(ammount)as total_sales,"
                . "COUNT(1)as total_orders "
                . "FROM $this->table "
                . "WHERE status = 1 $date $group ORDER BY total_sales DESC ";
        
        $result = $this->query($query);
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }
        return null;
    }
    
    public function _getFeedback($options){
        $query = "SELECT feedback FROM $this->table WHERE id = '{$options['id']}'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $this->resultToArray($result)[0];
            return $result['feedback'];
        }
        
        return null;
    }
    
    public function _saveFeedback($options){
        $query = "UPDATE $this->table SET feedback = '{$options['feedback']}' WHERE id = '{$options['id']}'";
        $result = $this->query($query);
        
        if($result){
            return true;
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
                $fecha .=" AND DATE($campoFecha) BETWEEN '{$startDate}' AND '{$endDate}' ";
            }else{
                $fecha .=" AND DATE($campoFecha) BETWEEN '{$startDate}' AND '{$startDate}' ";
            }
        }elseif($endDate!=null){
            $fecha .=" AND DATE($campoFecha) BETWEEN '{$endDate}' AND '{$endDate}' ";
        }
        
        return $fecha;
    }
}