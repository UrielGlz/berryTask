<?php 
$controller = 'Service';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$_form = new ServiceForm();
$_service = new ServiceRepository();
$_listServices = $_service->getListServices();

switch($action){
    case 'insert':
        $_form->populate($_POST);
        if($_form->isValid()){
            $_service->setOptions($_POST);
            $result = $_service->save($_service->getOptions());
            if($result){
                $flashmessenger->addMessage(array('success'=>'Servicio registrado exitosamente.'));
                header('Location: Service.php');
            }else{
                $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
            }
        }else{
            $_noValid = true;
            $vista = "Service.php";
            include $root.'/View/Template.php';
        }
        break;
   
    case 'edit':
        if($_GET){
            $_serviceData = $_service->getById($id);
        }elseif($_POST){
            $_serviceData = $_POST;
        }      
        
        $_form->setActionController('edit');
        $_form->setId($id);
        $_form->populate($_serviceData);
        
        if(isset($_POST['id'])){
            if($_form->isValid()){
                $_service->setOptions($_serviceData);
                $result = $_service->update($id,$_service->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Servicio actualizado exitosamente.'));
                    header("Location: Service.php");
                }else{
                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
                }       
            }else{
                $_noValid = true;
                $vista = "Service.php";
                include $root . '/View/Template.php';
            }
        }else{
            $vista = "Service.php";
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete': 
        if(!$_service->isUsedInRecord($id)){
            if($_service->delete($id)){
                $flashmessenger->addMessage(array('success'=>'Servicio eliminado exitosamente.'));
            }
        }else{
            $message = 'Este Servicio no puede ser eliminado, esta siendo utilizado en almenos un registro.';
            $flashmessenger->addMessage(array('info'=>$message));
        }
       header("Location: Service.php");
        break;
        
     case 'ajax':
        $ajaxService = new ServiceAjax();
        $json = $ajaxService->getResponse($_POST['request'], $_POST);
        echo json_encode($json);
        break;
    
    default:

        $vista = "Service.php";
        include $root.'/View/Template.php';
}