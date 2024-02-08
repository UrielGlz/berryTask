<?php

class PurchaseRepository extends EntityRepository {



    private $table = 'purchases';

    public $flashmessenger = null;

    private $options_files = array(

        'allowedExtensions'=>array('pdf'),

        'maxFileSizeAllowed'=>1000000,

        'pathToSave'=>PATH_PURCHASE_INVOICES

    );

    private $options = array (

        'id'=>null,

        'store_id'=>null,

        'date' => null,

        'requested_by'=>null,

        'approved_by'=>null,

        'vendor'=>null,

        'reference' => null,

        'lot' => null,        

        'discount_general_type'=>null,

        'discount_general'=>null,

        'total'=>null,

        'payments'=>null,

        'method_payment'=>null,

        'credit_days'=>null,

        'due_date'=>null,

        'comments'=>null,

        'status'=>null,

        'attachments' => null);

    

    private $options_aux = array(

        'vendorName'=>null,

        'userName'=>null,

        'statusName'=>null,

        'methodPaymentName'=>null,

        'formatedDueDate'=>null,

        'formatedDate'=>null,

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

    

    public function getStoreId(){        

        return $this->options['store_id'];

    }

    

    public function getVendorName(){        

        return $this->options_aux['vendorName'];

    }

    

    public function getMethodPaymentName(){        

        return $this->options_aux['methodPaymentName'];

    }

    

    public function getRequestedBy(){        

        return $this->options['requested_by'];

    }

    

     public function getApprovedBy(){        

        return $this->options['approved_by'];

    }

    

    public function getCreditDays(){        

        return $this->options['credit_days'];

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

    

    public function getFormatedDueDate(){

        return $this->options_aux['formatedDueDate'];

    }

    

    public function getVendor(){

        return $this->options['vendor'];

    }

    

    public function getReference(){

        return $this->options['reference'];

    }

    

    public function getLot(){

        return $this->options['lot'];

    }

    

    public function getTotal(){

        return $this->options['total'];

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

    

    public function getVendorInfoComplete(){

        $proveedor = new VendorRepository();        

        return $proveedor->getById($this->options['vendor']);

    }

    

    public function getSaldoPendiente(){

        return $this->options['total'] - $this->options['payments'];

    }

    

    public function getTokenForm(){

        return $this->options_aux['token_form'];

    }

    

    public function saveFiles($files,$idCompra,$numFactura){       

        $file = new UploadFile();

        $file->setAllowedExtensions($this->options_files['allowedExtensions']);

        $file->setMaxFileSizeAllowed($this->options_files['maxFileSizeAllowed']);

        $file->setTempFolder($this->options_files['pathToSave']);

        

        $rsUpload = $file->uploadFile($files['invoice_file'],null,"Factura_de_compra_".$idCompra."_".$numFactura); 

        

        if(!$rsUpload){

            $this->flashmessenger->addMessage(array('info'=>$file->getMessageError()));  

            return null;

        }        

        

        return true;        

    }



    public function save(array $data, $table = null) {   

        $purchaseDetailsTemp = new PurchaseDetailsTempRepository();

        if(!$purchaseDetailsTemp->isThereItemsOnPurchase($this->getTokenForm())){

            $this->flashmessenger->addMessage(array('danger'=>'Debe registrar almenos un producto para guardar la compra.'));

            return null;

        }

        

        /* Valida que Vendor Invoice y Lote no esten registrados */

        $isValid = $this->validarReferencias($data);

        if($isValid === null) return null;

        

        $tools = new Tools();        

        $data['total'] = round($data['total'],2);

        $data['date'] = $tools->setFormatDateToDB($data['date']);

        $data['payments'] = 0;        

        $data['due_date'] = $tools->setFormatDateToDB($data['due_date']);

        $data['status'] = '1';

        $data['status_payment'] = 1;

        $data['status_approval'] = $this->setApprovalStatusByTotal($data);

        if($data['status_approval'] == '2'){$data['status'] = '2';}

      

        /*Se obtiene informacion de pago en esta parte, porque en el fomulario de insert y edit estos campos estan deshabilitados*/

        /*

            $purchaseAjax = new PurchaseAjax();

            $vendorPaymentData = $purchaseAjax->getVendorMethodPayment(array('vendor'=>$data['vendor'],'date'=>$data['date']));

            $data['method_payment'] = $vendorPaymentData['method_payment'];

            $data['credit_days'] = $vendorPaymentData['credit_days'];

            $data['due_date'] = $tools->setFormatDateToDB($vendorPaymentData['due_date']);

         */

        /**/       

        

        $attachments = $data['attachments'];

        unset($data['attachments']);

        

        if($data['id']==null || $data['id']==''){unset($data['id']);}

        

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

                if (isset($attachments['invoice_file']['name'][0]) && $attachments['invoice_file']['name'][0] != '') {

                    $ext = pathinfo($attachments['invoice_file']['name'], PATHINFO_EXTENSION);

                    $attachments['invoice_file']['name'] = $settings->_get('name_for_invoice_file') . '.' . $ext;



                    $fileManagement->saveFile($attachments['invoice_file'], $idCompra, 'purchase');

                }



                if (isset($attachments['attachments']['name'][0]) && $attachments['attachments']['name'][0] != '') {

                    $fileManagement->saveFile($attachments['attachments'], $idCompra, 'purchase');

                }

            

                $this->_history(array('id'=>$idCompra,'action'=>'save','data'=>$data));

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

        if($currentData['status'] == '4'){return true;}



        $rs = parent::update($id, array('status'=>'4'), $this->table);

        

        if($rs){

            return true;

        }



        return null;

    }



    public function update($id, $data, $table = null) {      

        $purchaseDetailsTemp = new PurchaseDetailsTempRepository();

        if(!$purchaseDetailsTemp->isThereItemsOnPurchase($this->getTokenForm())){

            $this->flashmessenger->addMessage(array('danger'=>'Debe registrar almenos un producto para guardar la compra.'));

            return null;

        }

        

        $isValid = $this->validarReferencias(array(

                                    'id'=>$id,

                                    'reference'=>$data['reference'],

                                    'vendor'=>$data['vendor']

                ));

        if($isValid === null){return null;}

        

        $tools = new Tools();

        $data['total'] = round($data['total'],2);

        $currentData = $this->getById($id);

        $data['date'] = $tools->setFormatDateToDB($data['date']); 

        $data['due_date'] = $tools->setFormatDateToDB($data['due_date']);

        

        if($currentData['status_approval'] == '0' || $currentData['status_approval'] == '2'){

            $data['status_approval'] = $this->setApprovalStatusByTotal($data);

        }

        

        

        /*Se obtiene informacion de pago en esta parte, porque en el fomulario de insert y edit estos campos estan deshabilitados*/

        /*

            $purchaseAjax = new PurchaseAjax();

            $vendorPaymentData = $purchaseAjax->getVendorMethodPayment(array('vendor'=>$data['vendor'],'date'=>$data['date']));

            $data['method_payment'] = $vendorPaymentData['method_payment'];

            $data['credit_days'] = $vendorPaymentData['credit_days'];

            $data['due_date'] = $tools->setFormatDateToDB($vendorPaymentData['due_date']);

         */

        /**/               

        

        unset($data['payments'],$data['status_payment']);

        if(trim($data['status']) == ''){unset($data['status']);}

        $attachments = $data['attachments'];

        unset($data['attachments']);

        

        $this->startTransaction();

        $result = parent::update($id, $data, $this->table);        

        if($result){

            $purchaseDetailsTemp = new PurchaseDetailsTempRepository();

            if($purchaseDetailsTemp->updateDetalles($id,$this->getTokenForm())){                   

                $this->commit();

                $purchaseDetailsTemp->truncate($this->getTokenForm());   

                

                $fileManagement = new FileManagement();

                $settings = new SettingsRepository();

                if (isset($attachments['invoice_file']['name'][0]) && $attachments['invoice_file']['name'][0] != '') {

                    $ext = pathinfo($attachments['invoice_file']['name'], PATHINFO_EXTENSION);

                    $attachments['invoice_file']['name'] = $settings->_get('name_for_invoice_file') . '.' . $ext;



                    $fileManagement->saveFile($attachments['invoice_file'], $id, 'purchase');

                }



                if (isset($attachments['attachments']['name'][0]) && $attachments['attachments']['name'][0] != '') {

                    $fileManagement->saveFile($attachments['attachments'], $id, 'purchase');

                }    

                

               $this->_history(array('id'=>$id,'action'=>'update','currentData'=>$currentData,'newData'=>$data));

               return true;

            }

        }

        

        $this->rollback();

        return null;

    }

    

    public function updateString($fields, $where, $table = null) {

        return parent::updateString($fields, $where, $this->table);

    }



    public function validarReferencias($data){

        $result = true;

        $referencia = $this->existeReferencia($data['id'],$data['reference'],$data['vendor']);

        if($referencia){

            $this->flashmessenger->addMessage(array(

                'info'=>$this->_getTranslation('Este Num. Factura ya fue utilizada en Compra:').' #'.$referencia['id']));

            $result =  null;

        }

        

        return $result;

    }

    

    public function existeReferencia($id,$reference,$vendor){

       $query = "SELECT * FROM purchases_reference_vendor "

               . "WHERE reference = '$reference' "

               . "AND vendor = '$vendor' "

               . "AND id != '$id' "

               . "AND status != 3"; 

        $result = $this->query($query);

        

        if($result->num_rows > 0){

            $result = $this->resultToArray($result);

            return $result[0];

        }

        return null;

    }



    public function getById($id, $table = null,$selectAux = null) {

        $select = "SELECT *,"

                . "DATE_FORMAT(date,'%m/%d/%Y')as formatedDate,"

                . "DATE_FORMAT(due_date,'%m/%d/%Y')as formatedDueDate,"

                . "(total - IFNULL(payments,0)) as saldo_pendiente,"

                . "fxGetVendorName(vendor) as vendorName,"

                . "fxGetFormaPagoName(method_payment) as methodPaymentName,"

                . "fxGetStatusName(status,'Purchase')as statusName, "

                . "fxGetStatusName(status_approval,'Purchase_approval')as statusApprovalName, "

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

        return parent::isUsedInRecord($id, array('compras' => 'id')," AND status = 2 AND (type != 'BienesyServicios' AND type != 'Consigna' AND type != 'MateriaPrima')");

    }

    

    public function crearTablaDetallesForUser(){

        $login = new Login();        

        $query = "CREATE TABLE IF NOT EXISTS purchase_details_".$login->getId()." 

                 (  

                    `token_form` char(50) NOT NULL,

                    `id` int(11) NOT NULL AUTO_INCREMENT,

                    `id_detail` int(11) NULL,

                    `id_purchase` int(11) NULL,

                    `id_product` int(11) NOT NULL,

                    `description` varchar(255) NOT NULL,

                    `quantity` double NOT NULL,

                    `cost` double NOT NULL,

                    `cost_without_tax` double NOT NULL,

                    `discount` double NOT NULL,

                    `discount_type` char(15) NOT NULL,

                    `discount_amount` double NOT NULL,

                    `discount_general` double NOT NULL,

                    `discount_general_type` char(15) NOT NULL,

                    `discount_general_amount` double NOT NULL,

                    `taxes` int(11) NOT NULL,

                    `taxes_rate` double NOT NULL,

                    `taxes_amount` double NOT NULL,

                    `taxes_included` CHAR(50) NOT NULL,

                    `amount` double NOT NULL,

                    `total` double NOT NULL,

                    `expiration_date` date NULL,

                    PRIMARY KEY (`id`)

                 )ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        

       $result = $this->query($query);

    }

    

    public function insertDetalle($data){

        $purchaseDetailsTemp = new PurchaseDetailsTempRepository();        

        return $purchaseDetailsTemp->save($data);

    }

    

    public function getPurchaseDetails($token_form){

        $login = new Login();

        $query = "SELECT v.*,

                    v.id as idDetailTemp,

                    p.code,

                    fxGetTaxDescription(v.taxes)as taxName,

                    IF(id_product != 0,v.id_product,v.description)as product,

                    fxGetCategoryDescription(p.category)as category,

                    fxGetBrandDescription(p.brand)as brand,

                    fxGetPresentationDescription(p.presentation)as presentation

                  FROM purchase_details_".$login->getId()." v LEFT JOIN products p

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

    

    public function getPurchaseDetailSaved($id){

        $query = "SELECT c.*,

                    p.code as code

                    FROM purchase_details c LEFT JOIN products p ON c.id_product = p.id

                    WHERE c.id = '$id'";

        $result = $this->query($query);

        

        if($result){

            $result = $this->resultToArray($result)[0];

            return $result;

        }

        

        return null;

    }

    

    public function getPurchaseDetailsSaved($id){

        $query = "SELECT c.*,

                    p.code as code,

                    fxGetTaxDescription(c.taxes)as taxName

                    FROM purchase_details c LEFT JOIN products p ON c.id_product = p.id

                    WHERE c.id_purchase = '$id'";

        $result = $this->query($query);

        

        if($result){

            $result = $this->resultToArray($result);

            return $result;

        }

        

        return null;

    }

    

    public function setPurchaseDetailsById($idCompra,$tokenForm){

        $repository = new PurchaseDetailsTempRepository();

        

        return $repository->setPurchaseDetailsById($idCompra,$tokenForm);

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

    

    public function getListPurchase(){       

        $store_id = null;

        

        $login = new Login();

        if($login->getRole() != '1'){

            $store_id = " AND find_in_set(c.store_id,'{$login->getStoreId()}')";

        }       

        

        $query = "SELECT c.*,

                DATE_FORMAT(c.date,'%m/%d/%Y')as date,

                (total - payments)as balance,

                fxGetStatusName(c.`status`,'Purchase')as statusName,

                fxGetStatusName(c.`status_approval`,'Purchase approval')as statusApprovalName,

                fxGetVendorName(c.vendor) as vendor,

                fxGetStoreName(c.store_id)as storeName

                FROM purchases c

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

    

    public function getDueDate($date,$creditDays){

        $query = "SELECT DATE_ADD('{$date}',INTERVAL $creditDays DAY)as due_date";

        $result = $this->query($query);

        

        if($result){

            $result = $result->fetch_object();

            return $result->due_date;

        }

        return '';

    }

    

    public function getListFacturaPendientesByProveedor($options){                

        $store_id = null;        

        $login = new Login();

        

        if($login->getRole() != '1'){

            $store_id = " AND find_in_set(f.store_id,'{$login->getStoreId()}')";

        }       

        

        $idProveedor =  $options['proveedor'];

        $query = "SELECT f.*,

                  fxGetVendorName(f.vendor)as proveedorName,

                  DATE_FORMAT(convert(substring(f.date,1,10),date),'%m/%d/%Y')as fecha,

                  DATE_FORMAT(due_date,'%m/%d/%Y') as fecha_pago,

                  (total - IFNULL(payments,0))as saldo_pendiente

                  FROM purchases f

                  WHERE  1=1

                  AND status != '4'

                  AND status_approval != '0'

                  AND (total - IFNULL(payments,0)) > '0'

                  AND vendor = '$idProveedor' "

                . "$store_id "

                . "GROUP BY f.id "

                . "ORDER BY f.id ASC";



        $result = $this->query($query);

        

        if($result->num_rows > 0){

            return $this->resultToArray($result);

        }

        

        return null;

  }

  

  public function getListMetodoPago(){

        $query = "SELECT id,description FROM payment_methods WHERE tipo = 'Metodo pago'";

        $result = $this->query($query);

        

        if($result){

            $array = array();

            while($row = $result->fetch_object()){

                $array[$row->id] = $row->description;

            }

            

            return $array;

        }

    }  

    

    public function setApprovalStatusByTotal($options){

        $setting = new SettingsRepository();

        $maxAmountSinAprobacion = $setting->_get('purchase_monto_max_sin_aprobacion');

        

        if($options['total'] > $maxAmountSinAprobacion){

            return 0; 

        }else{

            return 2; /*Aprobada. Automatica cuando monto es menor a monto max sin aprobacion*/

        }

    }

    

    public function setApprovalStatusByApprover($options){

        $id = $options['purchase_id'];

        $currentData = $this->getById($id);

        if($currentData['status'] == '3' || $currentData['status'] == '4'){return null;}

       

        $login = new Login();

        $status = 1;

        $approverName = $login->getCompleteName();

        if($options['status_approval']=='1'){$status = '2';}      

        if($options['status_approval']=='0'){$approverName = '';}

        

        parent::update($id, array('status'=>$status,'status_approval'=>$options['status_approval'],'approved_by'=>$approverName), $this->table);

        $newData = $this->getById($id);



        unset($currentData['status']);

        $this->_history(array('id'=>$id,'action'=>'update','currentData'=>$currentData,'newData'=>$newData));

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

    

    public function _history($options){

        $action = $options['action'];

        $login = new Login();

        $data = array(

            'purchase_id'=>$options['id'],

            'action'=>$options['action'],            

            'datetime'=>date('Y-m-d H:i:s'),

            'user'=>$login->getId()

        );

        

        switch($action){

            case 'save':

                $data['action_subject'] = 'Creada';                

                parent::save($data, 'purchase_history');

                

                if($options['data']['status_payment'] === '2'){

                    $data['action_subject'] = 'Pagada';

                    parent::save($data, 'purchase_history');

                }

                

                if($options['data']['status_approval'] != '0'){

                    $data['action_subject'] = 'Aprobada';

                    parent::save($data, 'purchase_history');

                }

                

                break;

                

            case 'update':

                $currentData = $options['currentData'];

                $newData = $options['newData'];

                

                if(isset($currentData['status']) && $currentData['status']  != $newData['status']){

                    if($options['newData']['status'] == '3'){$data['action_subject'] = 'Recibida';}

                    elseif($options['newData']['status'] == '2'){$data['action_subject'] = 'Pendiente de recibir';}

                    parent::save($data, 'purchase_history');

                }

                

                if(isset($currentData['status_approval']) && $currentData['status_approval'] != $newData['status_approval']){

                    if($options['newData']['status_approval'] != '0'){ $data['action_subject'] = 'Aprobada';}       

                    else{$data['action_subject'] = 'Aprobacion pendiente';}

                    parent::save($data, 'purchase_history');

                }

                

                if(isset($currentData['status_payment']) && $currentData['status_payment'] != $newData['status_payment']){

                    if($options['newData']['status_approval'] != '1'){

                        $data['action_subject'] = 'Pagada';

                        parent::save($data, 'purchase_history');

                    }

                }              

                

                break;

            

            case 'delete':

                $data['action_subject'] = 'Cancelada';

                parent::save($data,'purchase_history');

                break;

            

            case 'viewed':

                $data['action_subject'] = 'Visto';

               // parent::save($data, 'purchase_history');

                break;

        }

    }

    

    public function getTimeLine($id){

        $query = "SELECT *,"

                . "DATE_FORMAT(datetime,'%m/%d/%Y %h:%i %p')as date,"

                . "fxGetUserName(user)as user "

                . " FROM purchase_history "

                . "WHERE purchase_id = '$id' ORDER BY datetime DESC";

        $result = $this->query($query);

        

        if($result->num_rows > 0){

            return $this->resultToArray($result);

        }

        return null;

    }

    

    public function getListPurchasesPendingToReceieve($id = null){

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

    

     public function getListFiles($id)

    {

        $fileManagement = new FileManagement();

        return $fileManagement->getStringListFilesByOperationAndPrefix('purchase', $id);

    }

}