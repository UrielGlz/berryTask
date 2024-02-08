<?php 
$controller = 'Location';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new LocationForm();
$_location = new LocationRepository();
$_listLocations = $_location->getListLocations();
$_noValid = null;

switch($action){
    case 'insert': 
        $form->populate($_POST);
        if($form->isValid()){            
            $_location->setOptions($_POST);
            $result = $_location->save($_location->getOptions());
            if($result){
                $flashmessenger->addMessage(array('success'=>'Locacion registrada exitosamente.'));
                header('Location: Location.php');
            }else{
                $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
            }
        }else{
            $_noValid = true;
            $vista = 'Location.php';
            include $root.'/View/Template.php';
        }
        break;
   
    case 'edit':
        if($_GET){
            $_locationData = $_location->getById($id);
        }
        if($_POST){$_locationData = $_POST;}
        
        $form->setActionController('edit');
        $form->setId($id);
        $form->populate($_locationData);
        
        if(isset($_POST['id'])){
            if($form->isValid()){
                $_location->setOptions($_locationData);
                $result = $_location->update($id,$_location->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Locacion actualizada exitosamente.'));
                    header("Location: Location.php");
                }else{
                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
                }       
            }else{
                $_noValid = true;
                $vista = 'Location.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'Location.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete': 
        if(!$_location->isUsedInRecord($id)){
            if($_location->delete($id)){
                $flashmessenger->addMessage(array('success'=>'Locacion eliminada satisfactoriamente.'));
            }
        }else{
             $flashmessenger->addMessage(array('success'=>'Locacion no puede ser eliminada esta siendo utilizada en almenos un registro.'));
        }
        
        header("Location: Location.php");
        break;
        
     case 'ajax':
        $ajaxLocation = new LocationAjax();
        $json = $ajaxLocation->getResponse($_POST['request'], $_POST);
        echo json_encode($json);
        break;
    
    default:       
        $vista = 'Location.php';
        include $root.'/View/Template.php';
}