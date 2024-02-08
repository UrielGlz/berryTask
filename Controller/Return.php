<?php 
$controller = 'Return';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}

include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new ReturnForm();
$_return = new ReturnRepository();

switch($action){
    case 'insert':
        $form->setTokenForm($_POST['token_form']);
        $form->populate($_POST);   
        $_return->setOptions($_POST); 
        if ($form->isValid()) {
            if ($_return->save($_return->getOptions())) {                
                $_returnNo = $_return->getLastInsertId();                
                $flashmessenger->addMessage(array(
                    "success"=>$_translator->_getTranslation("Retorno")." #$_returnNo ".$_translator->_getTranslation("registrado exitosamente.")));
                
                header("Location: Return.php?action=edit&id=$_returnNo");
            }else{                
                $vista = 'Return.php';
                include $root . '/View/Template.php';                
            }
        } else {
            $vista = 'Return.php';
            include $root . '/View/Template.php';
        }
        break;
    
    case 'list':
        $_listReturns = $_return->getListReturn();
        
        $vista = 'ReturnList.php';
        include $root . '/View/Template.php';
        break;
        
    case 'export':
        $login = new Login();
        $_returnData = $_return->getById($id);
        if($login->getRole() != '1' && !in_array($_returnData['store_id'], $login->getStoreArray())){
            echo $_translator->_getTranslation('No tienes permiso para ejecutar esta accion.');
            exit;
        }
        
        switch($_GET['flag']){
            case 'pdf':
                $pdf = new ReturnPDF($_GET['id']);
                break;
        }        
        break;
    
    case 'edit':   
        if($_GET){
            $_returnData = $_return->getById($id);
            $_return->crearTablaDetallesForUser();
            $_return->setReturnDetailsById($id,$form->getTokenForm());
        }
        
        if($_POST){
            $_returnData = $_POST;
            $form->setTokenForm($_POST['token_form']);
        }
        
        $login = new Login();
        if($login->getRole() != '1' && !in_array($_returnData['store_id'], $login->getStoreArray())){
            header("Location: Transfer.php?action=list");
            exit;
        }
        
        $form->setActionController('edit');
        $form->setId($id);
        $form->populate($_returnData);
        
        $_disabled = null;
        if($_returnData['status']=='2'){        
            $form->disabledAllElements();
            $form->hideElement(array('agregar_producto','terminar'));
            $_disabled = true;
        }
        
        if(isset($_POST['id'])){
            if($form->isValid()){
                $_returnData['invoice_file'] = $_FILES;
                $_return->setOptions($_returnData);
                $result = $_return->update($id,$_return->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Retorno actualizado exitosamente.'));
                    header("Location: Return.php?action=edit&id=$id");
                }else{
                     $vista = 'Return.php';
                    include $root . '/View/Template.php';                    
                }       
            }else{
                $vista = 'Return.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'Return.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete':
        $login = new Login();
        $_returnData = $_return->getById($id);
        if($login->getRole() != '1' && !in_array($_returnData['store_id'], $login->getStoreArray())){
            $flashmessenger->addMessage(array('danger'=>'No tienes permiso para ejecutar esta accion.'));
            header("Location: Return.php?action=list");
            exit;
        }
        
        if($_return->delete($id)){
            $flashmessenger->addMessage(array('success'=>'Retorno eliminado satisfactoriamente.'));
        }        
        header("Location: Return.php?action=list");
        break;
        
    case 'ajax':
        $ajaxReturn = new ReturnAjax();
        $json = $ajaxReturn->getResponse($_POST['request'],$_POST);
        
        echo json_encode($json);
        break;       
    
    default:      
        $_return->crearTablaDetallesForUser();
        $vista = 'Return.php';
        include $root.'/View/Template.php';
        break;
}