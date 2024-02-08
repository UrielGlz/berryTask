<?php 
$controller = 'ReceivingStoreRequest';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}

include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new ReceivingStoreRequestForm();
$receiving = new ReceivingStoreRequestRepository();

switch($action){
    case 'insert':
        $form->populate($_POST);   
        if ($form->isValid()) {
            $receiving->setOptions($_POST); 
            if ($receiving->save($receiving->getOptions())) {                
                $receivingNo = $receiving->getLastInsertId();
                $printReceivingStoreRequest = "<a href=\"\" onclick=\"javascript: void window.open('/Controller/ReceivingStoreRequest.php?action=import&flag=pdf&id=$receivingNo','','width=700,height=500,status=1,scrollbars=1,resizable=1')\">".$_translator->_getTranslation('Imprimir')."</a>";  
                $flashmessenger->addMessage(array(
                    "success"=>$_translator->_getTranslation("Recibo")." #$receivingNo ".$_translator->_getTranslation("registrado exitosamente.")." $printReceivingStoreRequest"));
                
                header("Location: ReceivingStoreRequest.php?action=edit&id=$receivingNo");
            }else{                
                $vista = 'ReceivingStoreRequest.php';
                include $root . '/View/Template.php';                
            }
        } else {
            $vista = 'ReceivingStoreRequest.php';
            include $root . '/View/Template.php';
        }
        break;
    
    case 'list':
        $_listReceivingStoreRequests = $receiving->getListReceivingStoreRequests();
        
        $vista = 'ReceivingStoreRequestList.php';
        include $root . '/View/Template.php';
        break;
        
    case 'export':
        switch($_GET['flag']){
            case 'pdf':
                $pdf = new ReceivingStoreRequestPDF($_GET['id']);
                break;
        }        
        break;
    
    case 'edit':          
         $edit = true;
        if($_GET){
            $_receivingData = $receiving->getById($id);
            
            if($login->getRole() != '1' && !in_array($_receivingData['store_id'], $login->getStoreArray())){
                header("Location: ReceivingStoreRequest.php?action=list");
            }   
            
            if($_receivingData['status'] != '4'){
                #Primero actualizamos informacion de receiving con informacion de MainServer
                $receiving->updateFromMainServer($id);
                $receiving->updateStatus($id,$_receivingData['received_incomplete']);
            }      
        
            $receiving->crearTablaDetallesForUser();
            $receiving->setReceivingStoreRequestDetailsById($id);
        }
        
        if($_POST){$_receivingData = $_POST;}
        
        $form->setActionController('edit');
        $form->setId($id);
        $form->populate($_receivingData);
        
        $form->setReadOnlydElements(array('num_shipment'));
        
        $_disabled = null;        
        
        if(!is_null($_receivingData['status_invoice']) && $_receivingData['status_invoice'] != '3'){              
            $form->disabledElements(array('terminar'));           
            if($_receivingData['allow_edit'] === '1'){
                $form->disabledElements(array('btn_allow_edit'));
                $receiving->removeAllowEdit($id);
                $form->enabledElement(array('terminar'));
            }else{
                $edit = null;
                $_disabled = true;
                $form->disabledAllElements();
                $form->hideElement(array('terminar'));
                $form->enabledElement(array('btn_allow_edit'));                 
            }      
        }               
        
        
        if($_receivingData['status'] == '4'){
            $form->disabledAllElements();
            $form->hideElement(array('buscar','terminar'));
            $_disabled = true;
        }        
        
        $receiving->setOptions($_receivingData);
        if(isset($_POST['id'])){
            if($form->isValid()){                
                $result = $receiving->update($id,$receiving->getOptions());
                if($result){ 
                    $flashmessenger->addMessage(array('success'=>'Recibo actualizado exitosamente.'));
                    header("Location: ReceivingStoreRequest.php?action=edit&id=$id");
                }else{
                     $vista = 'ReceivingStoreRequest.php';
                    include $root . '/View/Template.php';                    
                }       
            }else{
                $vista = 'ReceivingStoreRequest.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'ReceivingStoreRequest.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete':
        if($receiving->delete($id)){
            $flashmessenger->addMessage(array('success'=>'Recibo eliminado satisfactoriamente.'));
        }        
        header("Location: ReceivingStoreRequest.php?action=list");
        break;
        
    case 'ajax':
        $ajaxReceivingStoreRequest = new ReceivingStoreRequestAjax();
        $json = $ajaxReceivingStoreRequest->getResponse($_POST['request'],$_POST);
        
        echo json_encode($json);
        break;       
    
    default:      
        $receiving->crearTablaDetallesForUser();
        $vista = 'ReceivingStoreRequest.php';
        include $root.'/View/Template.php';
        break;
}