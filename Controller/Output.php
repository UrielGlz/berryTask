<?php 
$controller = 'Output';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}

include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new OutputForm();
$_output = new OutputRepository();

switch($action){
    case 'insert':
        $form->setTokenForm($_POST['token_form']);
        $form->populate($_POST);   
        $_output->setOptions($_POST); 
        if ($form->isValid()) {
            if ($_output->save($_output->getOptions())) {                
                $_outputNo = $_output->getLastInsertId();                
                $flashmessenger->addMessage(array(
                    "success"=>$_translator->_getTranslation("Salida")." #$_outputNo ".$_translator->_getTranslation("registrada exitosamente.")));
                
                header("Location: Output.php?action=edit&id=$_outputNo");
            }else{                
                $vista = 'Output.php';
                include $root . '/View/Template.php';                
            }
        } else {
            $vista = 'Output.php';
            include $root . '/View/Template.php';
        }
        break;
    
    case 'list':
        $_listOutputs = $_output->getListOutput();
        
        $vista = 'OutputList.php';
        include $root . '/View/Template.php';
        break;
        
    case 'export':
        $login = new Login();
        $_outputData = $_output->getById($id);
        if($login->getRole() != '1' && !in_array($_outputData['store_id'], $login->getStoreArray())){
            echo $_translator->_getTranslation('No tienes permiso para ejecutar esta accion.');
            exit;
        }
        
        switch($_GET['flag']){
            case 'pdf':
                $pdf = new OutputPDF($_GET['id']);
                break;
        }        
        break;
    
    case 'edit':   
        if($_GET){
            $_outputData = $_output->getById($id);
            $_output->crearTablaDetallesForUser();
            $_output->setOutputDetailsById($id,$form->getTokenForm());
        }
        if($_POST){
            $_outputData = $_POST;
            $form->setTokenForm($_POST['token_form']);
        }
        
        $login = new Login();
        if($login->getRole() != '1' && !in_array($_outputData['store_id'], $login->getStoreArray())){
            header("Location: Output.php?action=list");
            exit;
        }
        
        $form->setActionController('edit');
        $form->setId($id);
        $form->populate($_outputData);
        
        $_disabled = null;
        if($_outputData['status']=='2'){    
            $form->disabledAllElements();
            $form->hideElement(array('agregar_producto','terminar'));
            $_disabled = true;
        }
        
        if(isset($_POST['id'])){
            if($form->isValid()){
                $_outputData['invoice_file'] = $_FILES;
                $_output->setOptions($_outputData);
                $result = $_output->update($id,$_output->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Salida actualizada exitosamente.'));
                    header("Location: Output.php?action=edit&id=$id");
                }else{
                     $vista = 'Output.php';
                    include $root . '/View/Template.php';                    
                }       
            }else{
                $vista = 'Output.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'Output.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete':
        $login = new Login();
        $_outputData = $_output->getById($id);
        if($login->getRole() != '1' && !in_array($_outputData['store_id'], $login->getStoreArray())){
            $flashmessenger->addMessage(array('danger'=>'No tienes permiso para ejecutar esta accion.'));
            header("Location: Output.php?action=list");
            exit;
        }
        
        if($_output->delete($id)){
            $flashmessenger->addMessage(array('success'=>'Salida eliminada satisfactoriamente.'));
        }        
        header("Location: Output.php?action=list");
        break;
        
    case 'ajax':
        $ajaxOutput = new OutputAjax();
        $json = $ajaxOutput->getResponse($_POST['request'],$_POST);
        
        echo json_encode($json);
        break;       
    
    default:      
        $_output->crearTablaDetallesForUser();
        $vista = 'Output.php';
        include $root.'/View/Template.php';
        break;
}