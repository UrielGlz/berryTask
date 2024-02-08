<?php 
$controller = 'Store';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new StoreForm();
$_store = new StoreRepository();
$_listStores = $_store->getListStores();

switch($action){
    case 'insert': 
        $form->populate($_POST);
        if($form->isValid()){            
            $_store->setOptions($_POST);
            $result = $_store->save($_store->getOptions());
            if($result){
                $flashmessenger->addMessage(array('success'=>'Sucursal registrada exitosamente.'));
                header('Location: Store.php');
            }else{
                $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
            }
        }else{
            $_noValid = true;
            $vista = 'Store.php';
            include $root.'/View/Template.php';
        }
        break;
   
    case 'edit':
        if($_GET){
            $_storeData = $_store->getById($id);
        }
        if($_POST){$_storeData = $_POST;}
        
        $form->setActionController('edit');
        $form->setId($id);
        $form->populate($_storeData);
        
        if(isset($_POST['id'])){
            if($form->isValid()){
                $_store->setOptions($_storeData);
                $result = $_store->update($id,$_store->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Sucursal actualizada exitosamente.'));
                    header("Location: Store.php");
                }else{
                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
                }       
            }else{
                $_noValid = true;
                $vista = 'Store.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'Store.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete': 
        if(!$_store->isUsedInRecord($id)){
            if($_store->delete($id)){
                $flashmessenger->addMessage(array('success'=>'Sucursal eliminada satisfactoriamente.'));
            }
        }else{
             $flashmessenger->addMessage(array('success'=>'Sucursal no puede ser eliminada esta siendo utilizada en almenos un registro.'));
        }
        
        header("Location: Store.php");
        break;
        
     case 'ajax':
        $ajaxStore = new StoreAjax();
        $json = $ajaxStore->getResponse($_POST['request'], $_POST);
        echo json_encode($json);
        break;
    
    default:       
        
        $vista = 'Store.php';
        include $root.'/View/Template.php';
}