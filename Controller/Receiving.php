<?php 
$controller = 'Receiving';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}

include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new ReceivingForm();
$_receiving = new ReceivingRepository();

switch($action){
    case 'insert':
        $form->setTokenForm($_POST['token_form']);
        $form->populate($_POST);   
        $_receiving->setOptions($_POST); 
        if ($form->isValid()) {
            if ($_receiving->save($_receiving->getOptions())) {                
                $_receivingNo = $_receiving->getLastInsertId();                
                $flashmessenger->addMessage(array(
                    "success"=>$_translator->_getTranslation("Recibo")." #$_receivingNo ".$_translator->_getTranslation("registrado exitosamente.")));
                
                header("Location: Receiving.php?action=edit&id=$_receivingNo");
            }else{                
                $vista = 'Receiving.php';
                include $root . '/View/Template.php';                
            }
        } else {
            $vista = 'Receiving.php';
            include $root . '/View/Template.php';
        }
        break;
    
    case 'list':
        $_listReceiving = $_receiving->getListReceiving();
        
        $vista = 'ReceivingList.php';
        include $root . '/View/Template.php';
        break;
        
    case 'export':
        $login = new Login();
        $_receivingData = $_receiving->getById($id);
        if($login->getRole() != '1' && !in_array($_receivingData['store_id'], $login->getStoreArray())){
            echo $_translator->_getTranslation('No tienes permiso para ejecutar esta accion.');
            exit;
        }
        
        switch($_GET['flag']){
            case 'pdf':
                $pdf = new ReceivingPDF($_GET['id']);
                break;
        }        
        break;
    
    case 'edit':   
        if($_GET){
            $_receivingData = $_receiving->getById($id);
            $_receiving->crearTablaDetallesForUser();
            
            $repositoryTempDetails = new ReceivingDetailsTempRepository();
            $repositoryTempDetails->setReceivingDetailsById($id,$_receivingData['store_id_of_document'],$form->getTokenForm());
            
            /*Lo pongo aqui porque en $_POST no tenemos store_id*/        
            $login = new Login();
            if($login->getRole() != '1' && !in_array($_receivingData['store_id'], $login->getStoreArray())){
                header("Location: Receiving.php?action=list");
                exit;
            }
        }        
        
        if($_POST){
            $_receivingData = $_POST;
            $form->setTokenForm($_POST['token_form']);
        }
        
        $form->setActionController('edit');
        $form->setId($id);
        $form->populate($_receivingData);
        
        $_disabled = null;
        if($_receivingData['status']=='2'){
            $form->disabledAllElements();
            $form->hideElement(array('agregar_producto','terminar'));
            $_disabled = true;
        }
        
        $_receiving->setOptions($_receivingData);
        if(isset($_POST['id'])){
            if($form->isValid()){
                $result = $_receiving->update($id,$_receiving->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Recibo actualizado exitosamente.'));
                    header("Location: Receiving.php?action=edit&id=$id");
                }else{
                     $vista = 'Receiving.php';
                    include $root . '/View/Template.php';                    
                }       
            }else{
                $vista = 'Receiving.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'Receiving.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete':
        $login = new Login();
        $_receivingData = $_receiving->getById($id);
        if($login->getRole() != '1' && !in_array($_receivingData['store_id'], $login->getStoreArray())){
            $flashmessenger->addMessage(array('danger'=>'No tienes permiso para ejecutar esta accion.'));
            header("Location: Receiving.php?action=list");
            exit;
        }
        
        if($_receiving->delete($id)){
            $flashmessenger->addMessage(array('success'=>'Recibo eliminado exitosamente.'));
        }        
        header("Location: Receiving.php?action=list");
        break;
        
    case 'ajax':
        $ajaxReceiving = new ReceivingAjax();
        $json = $ajaxReceiving->getResponse($_POST['request'],$_POST);
        
        echo json_encode($json);
        break;       
    
    default:      
        $_receiving->crearTablaDetallesForUser();
        $vista = 'Receiving.php';
        include $root.'/View/Template.php';
        break;
}