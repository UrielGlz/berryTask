<?php 
$controller = 'Transfer';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}

include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new TransferForm();
$_transfer = new TransferRepository();

switch($action){
    case 'insert':
        $form->setTokenForm($_POST['token_form']);
        $form->populate($_POST);   
        $_transfer->setOptions($_POST); 
        if ($form->isValid()) {
            if ($_transfer->save($_transfer->getOptions())) {                
                $_transferNo = $_transfer->getLastInsertId();                
                $flashmessenger->addMessage(array(
                    "success"=>$_translator->_getTranslation("Traspaso")." #$_transferNo ".$_translator->_getTranslation("registrado exitosamente.")));
                
                header("Location: Transfer.php?action=edit&id=$_transferNo");
            }else{                
                $vista = 'Transfer.php';
                include $root . '/View/Template.php';                
            }
        } else {
            $vista = 'Transfer.php';
            include $root . '/View/Template.php';
        }
        break;
    
    case 'list':
        $_listTransfers = $_transfer->getListTransfer();
        
        $vista = 'TransferList.php';
        include $root . '/View/Template.php';
        break;
        
    case 'export':
        $login = new Login();
        $_transferData = $_transfer->getById($id);
        if($login->getRole() != '1' && !in_array($_transferData['from_store_id'], $login->getStoreArray())){
            echo $_translator->_getTranslation('No tienes permiso para ejecutar esta accion.');
            exit;
        }
        
        switch($_GET['flag']){
            case 'pdf':
                $pdf = new TransferPDF($_GET['id']);
                break;
        }        
        break;
    
    case 'edit':   
        if($_GET){
            $_transferData = $_transfer->getById($id);
            $_transfer->crearTablaDetallesForUser();
            $_transfer->setTransferDetailsById($id,$form->getTokenForm());
        }
        if($_POST){
            $_transferData = $_POST;
            $form->setTokenForm($_POST['token_form']);
        }
        
        $login = new Login();
        if($login->getRole() != '1' && !in_array($_transferData['from_store_id'], $login->getStoreArray())){
            header("Location: Transfer.php?action=list");
            exit;
        }
        
        $form->setActionController('edit');
        $form->setId($id);
        $form->populate($_transferData);
        
        $_disabled = null;
        if($_transferData['status']!='1'){            
            $form->hideElement(array('agregar_producto','terminar'));
            //$form->disabledAllElements();
            $_disabled = true;
        }
        
        if(isset($_POST['id'])){
            if($form->isValid()){
                $_transfer->setOptions($_transferData);
                $result = $_transfer->update($id,$_transfer->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Traspaso actualizado exitosamente.'));
                    header("Location: Transfer.php?action=edit&id=$id");
                }else{
                     $vista = 'Transfer.php';
                    include $root . '/View/Template.php';                    
                }       
            }else{
                $vista = 'Transfer.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'Transfer.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete':
        $login = new Login();
        $_transferData = $_transfer->getById($id);
        if($login->getRole() != '1' && !in_array($_transferData['from_store_id'], $login->getStoreArray())){
            $flashmessenger->addMessage(array('danger'=>'No tienes permiso para ejecutar esta accion.'));
            header("Location: Transfer.php?action=list");
            exit;
        }
        
        if($_transfer->delete($id)){
            $flashmessenger->addMessage(array('success'=>'Traspaso eliminado satisfactoriamente.'));
        }        
        header("Location: Transfer.php?action=list");
        break;
        
    case 'ajax':
        $ajaxTransfer = new TransferAjax();
        $json = $ajaxTransfer->getResponse($_POST['request'],$_POST);
        
        echo json_encode($json);
        break;       
    
    default:      
        $_transfer->crearTablaDetallesForUser();
        $vista = 'Transfer.php';
        include $root.'/View/Template.php';
        break;
}