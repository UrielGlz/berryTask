<?php 
$controller = 'Vendor';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new VendorForm();
$_vendor = new VendorRepository();
$_listVendors = $_vendor->getListVendors();

switch($action){
    case 'insert': 
        $form->populate($_POST);
        if($form->isValid()){            
            $_vendor->setOptions($_POST);
            $result = $_vendor->save($_vendor->getOptions());
            if($result){
                $flashmessenger->addMessage(array('success'=>'Proveedor registrado exitosamente.'));
                header('Location: Vendor.php');
            }else{
                $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
            }
        }else{
            $_noValid = true;
            $vista = 'Vendor.php';
            include $root.'/View/Template.php';
        }
        break;
   
    case 'edit':
        if($_GET){
            $_vendorData = $_vendor->getById($id);
        }
        if($_POST){$_vendorData = $_POST;}
        
        $_login = new Login();
        if($_login->getRole() != '1'){
            $flashmessenger->addMessage(array('info'=>'No tienes permiso para acceder a este modulo.'));
            header('Location: Home.php');
        }
        
        $form->setActionController('edit');
        $form->setId($id);
        $form->populate($_vendorData);
        
        if(isset($_POST['id'])){
            if($form->isValid()){
                $_vendor->setOptions($_vendorData);
                $result = $_vendor->update($id,$_vendor->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Proveedor actualizado exitosamente.'));
                    header("Location: Vendor.php");
                }else{
                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
                }       
            }else{
                $_noValid = true;
                $vista = 'Vendor.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'Vendor.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete': 
        if(!$_vendor->isUsedInRecord($id)){
            if($_vendor->delete($id)){
                $flashmessenger->addMessage(array('success'=>'Proveedor eliminado satisfactoriamente.'));
            }
        }else{
             $flashmessenger->addMessage(array('success'=>'Proveedor no puede ser eliminado esta siendo utilizado en almenos un registro.'));
        }
        
        header("Location: Vendor.php");
        break;
        
     case 'ajax':
        $ajaxVendor = new VendorAjax();
        $json = $ajaxVendor->getResponse($_POST['request'], $_POST);
        echo json_encode($json);
        break;
    
    default:       
        
        $vista = 'Vendor.php';
        include $root.'/View/Template.php';
}