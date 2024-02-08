<?php 
$controller = 'Invoice';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    }
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    }
}

include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$_form = new InvoiceForm();
$_invoice = new InvoiceRepository();

switch($action){
    case 'insert':        
        $_form->setTokenForm($_POST['token_form']);
        $_form->populate($_POST);  
        $_POST['attachments'] = $_FILES;
        $_invoice->setOptions($_POST);
        if ($_form->isValid()) {             
            if ($_invoice->save($_invoice->getOptions())) {   
                $_invoiceId = $_invoice->getLastInsertId();
                $_invoiceNo = $_invoice->getSalesOrderNumberById($_invoiceId);
                $printInvoice = "<a href=\"\" onclick=\"javascript: void window.open('/Controller/Invoice.php?action=export&format=pdf&flag=in&id=$_invoiceId','','width=700,height=500,status=1,scrollbars=1,resizable=1')\">".$_translator->_getTranslation('Imprimir')."</a>";  
                $flashmessenger->addMessage(array(
                    "success" => $_translator->_getTranslation("Factura") . " #$_invoiceNo " . $_translator->_getTranslation("se ha registrado exitosamente.")
                ));
                
                header("Location: Invoice.php?action=edit&id=$_invoiceId");
            }else{                
                $vista = 'Invoice.php';
                include $root . '/View/Template.php';                
            }
        } else {
            $vista = 'Invoice.php';
            include $root . '/View/Template.php';
        }
        break;
        
    case 'list': 
        $searchFilter = null;
        if (isset($_POST['search'])) {
            $searchFilter = $_POST;
        }
        $_listSalesOrders = $_invoice->getListInvoice($searchFilter);        
        
        $vista = 'InvoiceList.php';
        include $root . '/View/Template.php';
    break;
        
    case 'export':
        switch($_GET['format']){
            case 'excel':
                switch($_GET['flag']){
                    case 'search':
                        $_invoice->resultSearchToExcel($_GET);
                        break;
                }       
                break;
            
            case 'pdf':
                switch($_GET['flag']){
                    case 'invoice':
                        $pdf = new InvoicePDF($id);
                        break;
                    }   
                break;
        }
         
        break;
    
    case 'edit': 
        if($_GET){
            $_invoiceData = $_invoice->getById($id);
            $_invoice->crearTablaDetallesForUser();
            $_invoice->setInvoiceDetallesById($id,$_form->getTokenForm());            
        }
        if($_POST){
            $_form->setTokenForm($_POST['token_form']);
            $_POST['attachments'] = $_FILES;
            $_invoiceData = $_POST;
        }
        
        $_form->setActionController('edit');
        $_form->setId($id);
        $_form->populate($_invoiceData);        
        
        $_disabled = null;
        if($_invoiceData['status'] == '3'){
            $_disabled = true;
            $_form->setReadOnlydAllElements();
            $_form->hideElement(array('agregar_producto','agregar_producto_o_servicio'));
        }        

        if(isset($_form->getElements()['invoice_num']) && $_invoiceData['sales_order_id'] != '0' && !is_null($_invoiceData['sales_order_id'])){
            $_form->setReadOnlydElements(array('invoice_num'));
        }
        
        $_hideSalesOrderVsShipping = '';
        $_invoice->setOptions($_invoiceData);
        
        
        if(isset($_POST['id']) && $_invoiceData['status'] != '3'){
            if($_form->isValid()){                
                $result = $_invoice->update($id,$_invoice->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Factura se actualizo exitosamente.'));
                    header("Location: Invoice.php?action=edit&id=$id");
                }else{
                     $vista = 'Invoice.php';
                    include $root . '/View/Template.php';                    
                }       
            }else{
                $vista = 'Invoice.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'Invoice.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete':
        if(!$_invoice->isUsedInRecord($id)){
            if($_invoice->delete($id)){
                $flashmessenger->addMessage(array('success'=>'Factura se cancelo exitosamente.'));
            }
        }else{
            $flashmessenger->addMessage(array('info'=>'Esta Factura no puede ser eliminada, tienes pagos asignados.'));
        }
        
        header('Location: Invoice.php?action=list');
        break;
        
    case 'ajax':
        $ajaxInvoice = new InvoiceAjax();
        $json = $ajaxInvoice->getResponse($_POST['request'],$_POST);
        
        echo json_encode($json);
        break;       
    
    default:      
        $_invoice->crearTablaDetallesForUser();
        $vista = 'Invoice.php';
        include $root.'/View/Template.php';
        break;
}
