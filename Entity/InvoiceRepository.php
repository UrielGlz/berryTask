<?php
class InvoiceRepository extends EntityRepository
{

    private $table = 'invoices';
    public $flashmessenger = null;

    private $options = array(
        'invoice_num' => null,
        'date' => null,
        'id_customer' => null,
        'billed_to_store'=>null,
        'customer_po' => null,
        'shipping_address' => null,
        'payment_terms_id' => null,
        'due_date' => null,
        'subtotal' => null,
        'discount_general_type' => null,
        'discount_general' => null,
        'discount_general_amount' => null,
        'total' => null,
        'payments' => 0,
        'comments' => null,
        'message_on_invoice' => null,
        'status' => null,
        'attachments' => null
    );

    private $options_aux = array(
        'customerName' => null,
        'formatedDate' => null,
        'formatedDueDate ' => null,
        'formatedDeliveryDate' => null,
        'statusName' => null,
        'token_form' => null #Se popula con setOption desde Controller, con post de formulario
    );

    /*Input double y que no son hide*/
    public $inputs_double = array(
        'discount_general'
    );

    public function __construct()
    {
        if (!$this->flashmessenger instanceof FlashMessenger) {
            $this->flashmessenger = new FlashMessenger();
        }
    }

    public function _getTranslation($text)
    {
        return $this->flashmessenger->_getTranslation($text);
    }

    public function setOptions($data)
    {
        foreach ($this->options as $option => $value) {
            if (isset($data[$option])) {
                $this->options[$option] = $data[$option];
            }
        }

        foreach ($this->options_aux as $option => $value) {
            if (isset($data[$option])) {
                $this->options_aux[$option] = $data[$option];
            }
        }
    }

    public function getTableName()
    {
        return $this->table;
    }

    public function getInvoiceNumber()
    {
        return $this->options['invoice_num'];
    }

    public function getTotal()
    {
        return $this->options['total'];
    }

    public function getBalance()
    {
        return $this->options['total'] - $this->options['payments'];
    }

    public function getCustomerName()
    {
        return $this->options_aux['customerName'];
    }

    public function getDate()
    {
        return $this->options['date'];
    }

    public function getFormatedDate()
    {
        return $this->options_aux['formatedDate'];
    }

    public function getFormatedDeliveryDate()
    {
        return $this->options_aux['formatedDeliveryDate'];
    }

    public function getCustomer()
    {
        return $this->options['id_customer'];
    }

    public function getCustomerPO()
    {
        return $this->options['customer_po'];
    }

    public function getStatus()
    {
        return $this->options['status'];
    }

    public function getComments()
    {
        return $this->options['comments'];
    }

    public function getMessageOnInvoice()
    {
        return $this->options['message_on_invoice'];
    }

    public function getStatusName()
    {
        return $this->options_aux['statusName'];
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getId()
    {
        return $this->options['id'];
    }

    public function getCustomerCompleteInfo()
    {
        $proveedor = new CustomerRepository();

        return $proveedor->getById($this->options['id_customer']);
    }

    public function getDataShippingAddress()
    {
        $cliente = new CustomerAddressRepository();

        return $cliente->getById($this->options['shipping_address']);
    }

    public function getTokenForm()
    {
        return $this->options_aux['token_form'];
    }

    public function save(array $data, $table = null)
    {
        $settings = new SettingsRepository();
        $tools = new Tools();
        /* CUANDO manual_sales_order ESTA EN 0; invoice_num SE LLENA CON  getNextInvoiceNumber*/
        /* CUANDO manual_sales_order ESTA EN 1; invoice_num SE LLENA CON  DESDE FORMULARIO */
        $manualNumberInvoice = $settings->_get('manual_number_invoice');
        
        if(!is_null($manualNumberInvoice) && $manualNumberInvoice == '0'){
             $data['invoice_num'] = $this->getNextInvoiceNumber();
        }        
        
        $data['date'] = $tools->setFormatDateToDB($data['date']);
        $data['due_date'] = $tools->setFormatDateToDB($data['due_date']);
        $data['status'] = '1';
        $data['total'] = round($data['total'], 2);
        $data['payments'] = 0;
        $attachments = $data['attachments'];
        unset($data['attachments']);

        foreach ($data as $field => $value) {
            if ($value === '' || $value === null) {
                $data[$field] = '_NULL';
            }
        }

        $this->startTransaction();
        parent::save($data, $this->table);        
        $idManifest = $this->getInsertId();
        $this->setLastInsertId($idManifest);//Para utilizarlo en el Controller action insert

        $manifestDetallesTemp = new InvoiceDetailsTempRepository();   

        if ($manifestDetallesTemp->saveDetalles($idManifest, $this->getTokenForm())) {

            $this->commit();
            $manifestDetallesTemp->truncate($this->getTokenForm());

            $fileManagement = new FileManagement();
            if (isset($attachments['customer_po_file']['name'][0]) && $attachments['customer_po_file']['name'][0] != '') {
                $ext = pathinfo($attachments['customer_po_file']['name'], PATHINFO_EXTENSION);
                $attachments['customer_po_file']['name'] = $settings->_get('name_for_customer_po_file') . '.' . $ext;

                $fileManagement->saveFile($attachments['customer_po_file'], $idManifest, 'invoice');
            }

            if (isset($attachments['attachments']['name'][0]) && $attachments['attachments']['name'][0] != '') {
                $fileManagement->saveFile($attachments['attachments'], $idManifest, 'invoice');
            }


            return true;
        }

        $this->rollback();
        $this->flashmessenger->addMessage(array(
            'error' => $this->_getTranslation('Error. Intenta nuevamente o contacta a tu proveedor de sistemas.')
        ));
        return null;
    }

    public function update($id, $data, $table = null)
    {
        $data['total'] = round($data['total'], 2);
        $tools = new Tools();
        if(isset($data['date']))
            $data['date'] = $tools->setFormatDateToDB($data['date']);

        if(isset($data['due_date']))
            $data['due_date'] = $tools->setFormatDateToDB($data['due_date']);

        /* CUANDO manual_sales_order ESTA EN 0; invoice_num SE LLENA CON  getNextInvoiceNumber*/
        /* CUANDO manual_sales_order ESTA EN 1; invoice_num SE LLENA CON  DESDE FORMULARIO */
        if (trim($data['invoice_num']) == '' || is_null($data['invoice_num'])) {
            unset($data['invoice_num']);
        }

        $attachments = $data['attachments'];
        unset($data['attachments']);

        foreach ($data as $field => $value) {
            if ($value === '' || $value === null) {
                $data[$field] = '_NULL';
            }
        }

        unset($data['payments']);
        $this->startTransaction();
        $result = parent::update($id, $data, $this->table);

        if ($result) {
            $repository = new InvoiceDetailsTempRepository();
            if ($repository->updateDetalles($id, $this->getTokenForm())) {
                $this->commit();
                $repository->truncate($this->getTokenForm());

                $fileManagement = new FileManagement();
                if (isset($attachments['customer_po_file']['name'][0]) && $attachments['customer_po_file']['name'][0] != '') {
                    $settings = new SettingsRepository();
                    $ext = pathinfo($attachments['customer_po_file']['name'], PATHINFO_EXTENSION);
                    $attachments['customer_po_file']['name'] = $settings->_get('name_for_customer_po_file') . '.' . $ext;

                    $fileManagement->saveFile($attachments['customer_po_file'], $id, 'invoice');
                }

                if (isset($attachments['attachments']['name'][0]) && $attachments['attachments']['name'][0] != '') {
                    $fileManagement = new FileManagement();
                    $fileManagement->saveFile($attachments['attachments'], $id, 'invoice');
                }

                return true;
            }
        }

        $this->rollback();
        return null;
    }

    public function delete($id, $table = null)
    {
        $currentData = $this->getById($id);
        if ($currentData['status'] === '3') {
            return true;
        }

        $this->startTransaction();
        parent::update($id, array('status' => '3'), $this->table);

        $this->commit();
        return true;
    }

    public function updateString($fields, $where, $table = null)
    {
        return parent::updateString($fields, $where, $this->table);
    }

    public function getById($id, $table = null, $selectAux = null)
    {
        $select = "SELECT s.*,"
            . "fxGetCustomerName(s.id_customer) as customerName,"
            . "DATE_FORMAT(s.date,'%m/%d/%Y')as formatedDate,"
            . "DATE_FORMAT(s.due_date,'%m/%d/%Y')as formatedDueDate,"
            . "fxGetPaymentTermsName(s.payment_terms_id)as paymentTermsName,"
            . "fxGetStatusName(s.status,'Invoice')as statusName, "
            . "(s.total - IFNULL(payments,0))as balance,"
            . "fxGetStoreName(billed_to_store)as store_name "
            . "FROM $this->table s "
            . "WHERE  s.id = '$id'";
        $result = $this->query($select);

        if ($result->num_rows > 0) {
            $set = $this->resultToArray($result);
            return $set[0];
        }

        return false;
    }

    public function isUsedInRecord($id, array $buscarEn = null, $andWhere = null)
    {
        $query  = "SELECT * FROM customer_payments cp, customer_payment_details d WHERE cp.id = d.payment_id AND d.invoice_id = '$id' AND status = '1'";
        $result = $this->query($query);

        if ($result->num_rows > 0) {
            return true;
        }

        return null;
    }

    public function crearTablaDetallesForUser()
    {
        $login = new Login();
        $query = "CREATE TABLE IF NOT EXISTS invoice_details_" . $login->getId() . "
                 (  `id` int(11) NOT NULL AUTO_INCREMENT,
                    `token_form` char(50) NOT NULL,
                    `id_detalle` int(11) NULL,
                    `id_invoice` int(11) NULL,
                    `type` char(50) NOT NULL,
                    `id_detail_assigned_to_customer_from_purchase` int(11) NULL,
                    `lot` varchar(255) NULL,
                    `id_product` int(11) NOT NULL,
                    `descripcion` varchar(255) NOT NULL,
                    `description_details` TEXT NULL,                   
                    `quantity` double NOT NULL,
                    `price` double NULL,
                    `discount` double NULL,
                    `discount_type` char(15) NULL,
                    `discount_amount` double NULL,
                    `discount_general` double NULL,
                    `discount_general_type` char(15) NULL,
                    `discount_general_amount` double NULL,
                    `taxes` int(11) NULL,
                    `taxes_rate` double NULL,
                    `taxes_amount` double NULL,
                    `amount` double NULL,
                    `total` double NULL,
                    PRIMARY KEY (`id`)
                 )ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $result = $this->query($query);
    }

    public function insertDetalle($data)
    {
        $manifestDetallesTemp = new InvoiceDetailsTempRepository();
        return $manifestDetallesTemp->save($data);
    }

    public function getInvoiceDetalles($tokenForm)
    {
        $repo = new InvoiceDetailsTempRepository();
        return $repo->getInvoiceDetalles($tokenForm);
    }

    public function getInvoiceDetallesSaved($id)
    {
        $query = "SELECT *,               
                fxGetTaxDescription(v.taxes)as taxName
                    FROM invoice_details v
                    WHERE id_invoice = '$id'
                  ORDER BY v.id ";
        $result = $this->query($query);

        if ($result) {
            $result = $this->resultToArray($result);
            return $result;
        }

        return null;
    }

    public function getInvoiceDetallesSavedPDF($id)
    {
        $query = "SELECT *,
                    GROUP_CONCAT(v.descripcion)as descripcion,             
                    GROUP_CONCAT(price)as price,
                    GROUP_CONCAT(quantity)as quantity_concat,
                    SUM(quantity)as quantity,
                    SUM(IFNULL(v.quantity,0) * IFNULL(v.price,0))as price_total
                    FROM invoice_details v
                    WHERE id_invoice = '$id'
                    GROUP BY id
                  ORDER BY v.id ";
        $result = $this->query($query);

        if ($result) {
            $result = $this->resultToArray($result);
            return $result;
        }

        return null;
    }

    public function setInvoiceDetallesById($idManifest, $tokenForm)
    {
        $repository = new InvoiceDetailsTempRepository();

        return $repository->setManifestDetallesById($idManifest, $tokenForm);
    }

    public function getProductoById($id_product)
    {
        $query = "SELECT * FROM products WHERE id = '$id_product' LIMIT 1";
        $result = $this->query($query);

        if ($result->num_rows > 0) {
            $result = $this->resultToArray($result);
            return $result[0];
        }
        return null;
    }

    public function getGoodAndServiceById($id){
        $repo = new ProductRepository();
        return $repo->getById($id);
    }

    public function getListInvoice($options = null)
    {
        $limit = null;
        $date = null;
        $dueDate = null;
        $invoice_num = null;
        $customer_id = null;
        $customer_po = null;
        $status = null;
        $user_id = null;

        $date = $this->createFilterFecha($options, "m.date");
        $dueDate = $this->createFilterFecha(array('startDate' => $options['dueStartDate'], 'endDate' => $options['dueStartDate']), "m.due_date");
        if ($options) {
            if (isset($options['invoice_num']) && trim($options['invoice_num']) != '') {
                $invoice_num = " AND find_in_set(m.invoice_num,'{$options['invoice_num']}')";
            }
            if (isset($options['customer_po']) && trim($options['customer_po']) != '') {
                $customer_po = " AND find_in_set(m.customer_po,'{$options['customer_po']}')";
            }

            if (isset($options['customer_id'])) {
                if (is_array($options['customer_id']) && count($options['customer_id']) > 0) {
                    $customerIds = implode(',', $options['customer_id']);
                    $customer_id = " AND find_in_set(m.id_customer,'{$customerIds}')";
                } else {
                    if (trim($options['vendor_id']) != '') {
                        $customer_id = " AND find_in_set(m.id_customer,'{$options['customer_id']}')";
                    }
                }
            }

            if (isset($options['user_id'])) {
                if (is_array($options['user_id']) && count($options['user_id']) > 0) {
                    $userIds = implode(',', $options['user_id']);
                    $user_id = " AND find_in_set(m.creado_por,'{$userIds}')";
                } else {
                    if (trim($options['user_id']) != '') {
                        $user_id = " AND find_in_set(m.creado_por,'{$options['user_id']}')";
                    }
                }
            }

            if (isset($options['status'])) {
                if (is_array($options['status']) && count($options['status']) > 0) {
                    $statusIds = implode(',', $options['status']);
                    $status = " AND find_in_set(m.status,'{$statusIds}')";
                } else {
                    if (trim($options['status']) != '') {
                        $status = " AND find_in_set(m.status,'{$options['status']}')";
                    }
                }
            }

            if (
                is_null($date)
                && is_null($dueDate)
                && is_null($invoice_num)
                && is_null($customer_id)
                && is_null($customer_po)
                && is_null($status)
                && is_null($user_id)
            ) {
                $limit = " LIMIT 300";
            }
        } else {
            $limit = " LIMIT 300";
        }

        $query = "SELECT m.*,"
            . "fxGetCustomerName(m.id_customer) as customerName,"
            . "DATE_FORMAT(m.date,'%m/%d/%Y')as formatedDate,"
            . "DATE_FORMAT(m.due_date,'%m/%d/%Y')as formatedDueDate,"
            . "fxGetStatusName(m.status,'Invoice')as statusName,"
            . "SUM(d.quantity)as quantity,"
            . "r.num_shipment, "
            . "fxGetStoreName(billed_to_store)as store_name,"
            . "ROUND(m.total - IFNULL(m.payments,0),2)as balance,"
            . "fxGetUserName(m.creado_por)as createdByName,"
            . "DATE_FORMAT(m.creado_fecha,'%m/%d/%Y %r')as formatedCreatedDate "
            . "FROM $this->table m "
            . "LEFT JOIN invoice_details d ON m.id = d.id_invoice "
            . "LEFT JOIN receiving_store_requests r ON m.receiving_id = r.id "
            . "WHERE 1 = 1 "
            . "$invoice_num "
            . "$customer_id "
            . "$customer_po "
            . "$status "
            . "$date "
            . "$dueDate "
            . "GROUP BY m.id "
            . "ORDER BY m.id DESC "
            . "$limit ";

        $result = $this->query($query);

        if ($result->num_rows > 0) {
            return $this->resultToArray($result);
        }

        return null;
    }

    public function getDueDate($date, $creditDays)
    {
        $query = "SELECT DATE_ADD('{$date}',INTERVAL $creditDays DAY)as due_date";
        $result = $this->query($query);

        if ($result) {
            $result = $result->fetch_object();
            return $result->due_date;
        }
        return '';
    }

    public function getInvoiceNumberById($id)
    {
        $query = "SELECT invoice_num FROM $this->table WHERE id = '$id'";
        $result = $this->query($query);

        if ($result->num_rows > 0) {
            $result = $result->fetch_object();
            return $result->invoice_num;
        }
    }

    public function updateTotalSalesOrder($id)
    {
        $query = "SELECT "
            . "m.discount_general_type,"
            . "m.discount_general,"
            . "SUM(d.quantity * d.price)as subtotal "
            . "FROM $this->table m,invoice_details d "
            . "WHERE m.id = d.id_invoice AND m.id = '$id' ";

        $result = $this->query($query);

        if ($result->num_rows > 0) {
            $result = $result->fetch_object();
            $subtotal = $result->subtotal;

            switch ($result->discount_general_type) {
                case 'amount':
                    $total = $subtotal - $result->discount_general_amount;
                    break;
                case 'percent':
                    $total = $subtotal - ($subtotal * ($result->discount_general) / 100);
                    break;
            }

            $query = "UPDATE $this->table "
                . "SET subtotal = '$subtotal',"
                . "total = '$total'"
                . "WHERE id = $id";

            $this->query($query);

            return true;
        }

        return null;
    }

    public function getNextInvoiceNumber()
    {
        $query = "SELECT invoice_num FROM $this->table ORDER BY invoice_num DESC LIMIT 1 ";
        $result = $this->query($query);

        if ($result->num_rows > 0) {
            $result = $result->fetch_object();
            $invoiceNumber = $result->invoice_num;
            $invoiceNumber++;
            return $invoiceNumber;
        }
        return 1;
    }

    public function  getSalesOrderNumberById($idInvoice)
    {
        $query = "SELECT invoice_num FROM $this->table WHERE id = '$idInvoice' ";
        $result = $this->query($query);

        if ($result->num_rows > 0) {
            $result = $result->fetch_object();
            return $result->invoice_num;
        }
        return null;
    }

    public function getListFiles($id)
    {
        $fileManagement = new FileManagement();
        return $fileManagement->getStringListFilesByOperationAndPrefix('invoice', $id);
    }

    public function getListStatus()
    {
        $query = "SELECT * FROM status_code WHERE operation = 'Invoice'";
        $result = $this->query($query);

        if ($result->num_rows > 0) {
            $array = array();
            foreach ($result as $status) {
                $array[$status['code']] = $status['description'];
            }
            return $array;
        }
        return null;
    }

    public function getListFacturaPendientesByCustomer($options)
    {

        $customerId =  $options['customer'];
        $query = "SELECT f.*,
                  fxGetCustomerName(f.id_customer)as customerName,
                  DATE_FORMAT(convert(substring(f.date,1,10),date),'%m/%d/%Y')as formatedDate,
                  DATE_FORMAT(due_date,'%m/%d/%Y') as formatedDueDate,
                  (total - IFNULL(payments,0))as balance
                  FROM invoices f
                  WHERE  1=1
                  AND status != '3'
                  AND (total - IFNULL(payments,0)) > '0'
                  AND id_customer = '$customerId' "
            . "GROUP BY f.id "
            . "ORDER BY f.id ASC";

        $result = $this->query($query);

        if ($result->num_rows > 0) {
            return $this->resultToArray($result);
        }

        return null;
    }

    public function getSalesDetailsByLot($lot)
    {
        $query = "SELECT
                i.invoice_num,
                DATE_FORMAT(i.date,'%m/%d/%Y') as formated_date,
                fxGetCustomerName(i.id_customer)as customer_name,
                d.descripcion,
                d.price,
                d.quantity,
                d.total,
                d.lot
                FROM invoices i
                LEFT JOIN invoice_details d ON i.id = d.id_invoice
                WHERE i.id = d.id_invoice
                AND d.lot = '{$lot}'
                AND i.status != 3
                ORDER BY d.id_quality,d.id_size";

        $result = $this->query($query);

        if ($result->num_rows > 0) {
            return $this->resultToArray($result);
        }

        return null;
    }

    public function getSalesSummaryWithAveragePriceDetailsByLot($lot)
    {
        $query = "SELECT                
                i.invoice_num,
                DATE_FORMAT(i.date,'%m/%d/%Y') as formated_date,
                fxGetCustomerName(i.id_customer)as customer_name,
                d.descripcion,             
                SUM(d.quantity)as quantity,
                SUM(d.total)as total_sales
                FROM invoices i
                LEFT JOIN invoice_details d ON i.id = d.id_invoice
                WHERE i.id = d.id_invoice
                AND d.lot = '{$lot}'
                AND i.status != 3
                GROUP BY id_product";

        $result = $this->query($query);

        if ($result->num_rows > 0) {
            $result =  $this->resultToArray($result);
            $array = array();
            foreach ($result as $row) {
                $row['ave_price'] = number_format($row['total_sales'] / $row['quantity'], 2, '.', '');
                $array[$row['product_key']] = $row;
            }
            return $array;
        }

        return null;
    }

    public function setInvoiceAsSent($id)
    {
        return $this->query("UPDATE $this->table SET invoice_sent = '1' WHERE id = $id");
    }  
    

    public function createFilterFecha($options, $campoFecha = null)
    {
        if (!isset($options['startDate']) && !isset($options['endDate'])) {
            return null;
        }
        $startDate = $options['startDate'];
        $endDate = $options['endDate'];
        $fecha = null;
        $tools = new Tools();
        if ($startDate != null) {
            $startDate = $tools->setFormatDateToDB($startDate);
            if ($endDate != null) {
                $endDate = $tools->setFormatDateToDB($endDate);
                $fecha .= " AND $campoFecha BETWEEN '{$startDate}' AND '{$endDate}' ";
            } else {
                $fecha .= " AND $campoFecha BETWEEN '{$startDate}' AND '{$startDate}' ";
            }
        } elseif ($endDate != null) {
            $fecha .= " AND $campoFecha BETWEEN '{$endDate}' AND '{$endDate}' ";
        }
        return $fecha;
    }

}
