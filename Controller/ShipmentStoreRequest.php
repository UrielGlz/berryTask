<?php 
$controller = 'ShipmentStoreRequest';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}

include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new ShipmentStoreRequestForm();
$shipment = new ShipmentStoreRequestRepository();

switch($action){
    case 'insert':
        $form->setTokenForm($_POST['token_form']);
        $form->populate($_POST);   
        if ($form->isValid()) {
            $shipment->setOptions($_POST); 
            if ($shipment->save($shipment->getOptions())) {                
                $shipmentNo = $shipment->getLastInsertId();
                $printShipmentStoreRequest = "<a href=\"\" onclick=\"javascript: void window.open('/Controller/ShipmentStoreRequest.php?action=import&flag=pdf&id=$shipmentNo','','width=700,height=500,status=1,scrollbars=1,resizable=1')\">".$_translator->_getTranslation('Imprimir')."</a>";  
                $flashmessenger->addMessage(array(
                    "success"=>$_translator->_getTranslation("Envio")." #$shipmentNo ".$_translator->_getTranslation("registrado exitosamente.")." $printShipmentStoreRequest"));
                
                header("Location: ShipmentStoreRequest.php?action=edit&id=$shipmentNo");
            }else{                
                $vista = 'ShipmentStoreRequest.php';
                include $root . '/View/Template.php';                
            }
        } else {
            $vista = 'ShipmentStoreRequest.php';
            include $root . '/View/Template.php';
        }
        break;
    
    case 'list':
        $_listShipmentStoreRequests = $shipment->getListShipmentStoreRequests();
        
        $vista = 'ShipmentStoreRequestList.php';
        include $root . '/View/Template.php';
        break;
        
    case 'export':
        switch($_GET['flag']){
            case 'pdf':
                $pdf = new ShipmentStoreRequestPDF($_GET['id']);
                break;
        }        
        break;
    
    case 'edit':   
        #Para usar status y statusName
        $shipment->setOptions($shipment->getById($id));
        
        if($_GET){
            $shipmentData = $shipment->getById($id);
            $shipment->crearTablaDetallesForUser();
            $shipment->setShipmentStoreRequestDetailsById($id,$form->getTokenForm());
        }
        if($_POST){
            $form->setTokenForm($_POST['token_form']);
            $shipmentData = $_POST;
        }

        $form->setActionController('edit');
        $form->setId($id);
        $form->populate($shipmentData);
        
        if($shipmentData['id_store_request']!= ''){$form->setReadOnlydElements(array('to_store'));}
        
        $_disabled = null;
        if($shipmentData['status'] != '1'){
            $form->disabledAllElements();
            $form->hideElement(array('buscar','terminar'));
            $_disabled = true;
        }
        
        $form->disabledElements(array('id_store_request'));
        if(isset($_POST['id'])){
            if($form->isValid()){
                $shipment->setOptions($shipmentData);
                $result = $shipment->update($id,$shipment->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Envio actualizado exitosamente.'));
                    header("Location: ShipmentStoreRequest.php?action=edit&id=$id");
                }else{
                     $vista = 'ShipmentStoreRequest.php';
                    include $root . '/View/Template.php';                    
                }       
            }else{
                $vista = 'ShipmentStoreRequest.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'ShipmentStoreRequest.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete':
        if($shipment->delete($id)){
            $flashmessenger->addMessage(array('success'=>'Envio eliminado satisfactoriamente.'));
        }        
        header("Location: ShipmentStoreRequest.php?action=list");
        break;
        
    case 'ajax':
        $ajaxShipmentStoreRequest = new ShipmentStoreRequestAjax();
        $json = $ajaxShipmentStoreRequest->getResponse($_POST['request'],$_POST);
        
        echo json_encode($json);
        break;       
    
    default:      

        $shipment->crearTablaDetallesForUser();
        $vista = 'ShipmentStoreRequest.php';
        include $root.'/View/Template.php';
        break;
}