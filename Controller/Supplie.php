<?php 
$controller = 'Supplie';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$_form = new SupplieForm();
$_supplie = new SupplieRepository();
$_listSupplies = $_supplie->getListSupplies();

switch($action){
    case 'insert':
        $_form->populate($_POST);
        if($_form->isValid()){
            $_supplie->setOptions($_POST);
            $result = $_supplie->save($_supplie->getOptions());
            if($result){
                $flashmessenger->addMessage(array('success'=>'Insumo registrado exitosamente.'));
                header('Location: Supplie.php');
            }else{
                $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
            }
        }else{
            $_noValid = true;
            $vista = "Supplie.php";
            include $root.'/View/Template.php';
        }
        break;
   
    case 'edit':
        if($_GET){
            $_supplieData = $_supplie->getById($id);
            $_supplieData['location'] = $_supplie->locationStringToArray($_supplieData['location']);
        }elseif($_POST){
            $_supplieData = $_POST;
        }      
        
        $_form->setActionController('edit');
        $_form->setId($id);
        $_form->populate($_supplieData);
        
        if(isset($_POST['id'])){
            if($_form->isValid()){
                $_supplie->setOptions($_supplieData);
                $result = $_supplie->update($id,$_supplie->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Insumo actualizado exitosamente.'));
                    header("Location: Supplie.php");
                }else{
                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
                }       
            }else{
                $_noValid = true;
                $vista = "Supplie.php";
                include $root . '/View/Template.php';
            }
        }else{
            $vista = "Supplie.php";
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete': 
        if(!$_supplie->isUsedInRecord($id)){
            if($_supplie->delete($id)){
                $flashmessenger->addMessage(array('success'=>'Insumo eliminado exitosamente.'));
            }
        }else{
            $message = 'Este Insumo no puede ser eliminado, esta siendo utilizado en almenos un registro.';
            $flashmessenger->addMessage(array('info'=>$message));
        }
       header("Location: Supplie.php");
        break;
        
     case 'ajax':
        $ajaxInsumo = new SupplieAjax();
        $json = $ajaxInsumo->getResponse($_POST['request'], $_POST);
        echo json_encode($json);
        break;
    
    default:

        $vista = "Supplie.php";
        include $root.'/View/Template.php';
}