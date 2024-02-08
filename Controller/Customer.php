<?php 
$controller = 'Customer';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new CustomerForm();
$_customer = new CustomerRepository();
$_listCustomers = $_customer->getListCustomers();

switch($action){
    case 'insert': 
        $form->populate($_POST);
        if($form->isValid()){            
            $_customer->setOptions($_POST);
            $result = $_customer->save($_customer->getOptions());
            if($result){
                $flashmessenger->addMessage(array('success'=>'Cilente registrado exitosamente.'));
                header('Location: Customer.php');
            }else{
                $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
            }
        }else{
            $_noValid = true;
            $vista = 'Customer.php';
            include $root.'/View/Template.php';
        }
        break;
   
    case 'edit':
        if($_GET){
            $_customerData = $_customer->getById($id);
        }
        if($_POST){$_customerData = $_POST;}
        
        $_login = new Login();
        if($_login->getRole() != '1'){
            $flashmessenger->addMessage(array('info'=>'No tienes permiso para acceder a este modulo.'));
            header('Location: Home.php');
        }
        
        $form->setActionController('edit');
        $form->setId($id);
        $form->populate($_customerData);
        
        if(isset($_POST['id'])){
            if($form->isValid()){
                $_customer->setOptions($_customerData);
                $result = $_customer->update($id,$_customer->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Cilente actualizado exitosamente.'));
                    header("Location: Customer.php");
                }else{
                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
                }       
            }else{
                $_noValid = true;
                $vista = 'Customer.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'Customer.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete': 
        if(!$_customer->isUsedInRecord($id)){
            if($_customer->delete($id)){
                $flashmessenger->addMessage(array('success'=>'Cilente eliminado satisfactoriamente.'));
            }
        }else{
             $flashmessenger->addMessage(array('success'=>'Cilente no puede ser eliminado esta siendo utilizado en almenos un registro.'));
        }
        
        header("Location: Customer.php");
        break;
        
     case 'ajax':
        $ajaxCustomer = new CustomerAjax();
        $json = $ajaxCustomer->getResponse($_POST['request'], $_POST);
        echo json_encode($json);
        break;
    
    default:       
        
        $vista = 'Customer.php';
        include $root.'/View/Template.php';
}