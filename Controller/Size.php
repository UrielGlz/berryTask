<?php 
$controller = 'Size';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new SizeForm();
$_size = new SizeRepository();
$_listSizes = $_size->getListSizes();
$_noValid = null;

switch($action){
    case 'insert': 
        $form->populate($_POST);
        if($form->isValid()){            
            $_size->setOptions($_POST);
            $result = $_size->save($_size->getOptions());
            if($result){
                $flashmessenger->addMessage(array('success'=>'Tamaño registrado exitosamente.'));
                header('Location: Size.php');
            }else{
                $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
            }
        }else{
            $_noValid = true;
            $vista = 'Size.php';
            include $root.'/View/Template.php';
        }
        break;
   
    case 'edit':
        if($_GET){
            $_sizeData = $_size->getById($id);
        }
        if($_POST){$_sizeData = $_POST;}
        
        $form->setActionController('edit');
        $form->setId($id);
        $form->populate($_sizeData);
        
        if(isset($_POST['id'])){
            if($form->isValid()){
                $_size->setOptions($_sizeData);
                $result = $_size->update($id,$_size->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Tamaño actualizado exitosamente.'));
                    header("Location: Size.php");
                }else{
                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
                }       
            }else{
                $_noValid = true;
                $vista = 'Size.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'Size.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete': 
        if(!$_size->isUsedInRecord($id)){
            if($_size->delete($id)){
                $flashmessenger->addMessage(array('success'=>'Tamaño eliminado satisfactoriamente.'));
            }
        }else{
             $flashmessenger->addMessage(array('success'=>'Tamaño no puede ser eliminado esta siendo utilizado en almenos un registro.'));
        }
        
        header("Location: Size.php");
        break;
        
     case 'ajax':
        $ajaxSize = new SizeAjax();
        $json = $ajaxSize->getResponse($_POST['request'], $_POST);
        echo json_encode($json);
        break;
    
    default:       
        $vista = 'Size.php';
        include $root.'/View/Template.php';
}