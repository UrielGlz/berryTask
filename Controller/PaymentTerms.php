<?php 
$controller = 'PaymentTerms';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$_paymentForm = new PaymentTermsForm();
$_brand = new PaymentTermsRepository();
$_listPaymentTerms  = $_brand->getListPaymentTerms();

switch($action){
    case 'insert':
        $_paymentForm->populate($_POST);
        if($_paymentForm->isValid()){
            $_brand->setOptions($_POST);
            $result = $_brand->save($_brand->getOptions());
            if($result){
                $flashmessenger->addMessage(array('success'=>'Genial !! El termino de pago se registro exitosamente.'));
                header('Location: PaymentTerms.php');
            }else{
                $flashmessenger->addMessage(array('danger'=>'Oops !!, algo salio mal al intentar registrar el Termino de pago. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
            }
        }else{
            $_noValid = true;
            $vista = 'PaymentTerms.php';
            include $root.'/View/Template.php';
        }
        break;
   
    case 'edit':
        if($_GET){$_brandData = $_brand->getById($id);}
        if($_POST){$_brandData = $_POST;}
        
        $_paymentForm->setActionController('edit');
        $_paymentForm->setId($id);
        $_paymentForm->populate($_brandData);
        
        if(isset($_POST['id'])){
            if($_paymentForm->isValid()){
                $_brand->setOptions($_brandData);
                $result = $_brand->update($id,$_brand->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Estupendo !! El termino de pago se actualizo exitosamente.'));
                    header("Location: PaymentTerms.php?action=edit&id=$id");
                }else{
                    $flashmessenger->addMessage(array('danger'=>'Oops !!, algo salio mal al intenta actualizar la Calidad. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
                }       
            }else{
                $_noValid = true;
                $vista = 'PaymentTerms.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'PaymentTerms.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete': 
        if(!$_brand->isUsedInRecord($id)){
            if($_brand->delete($id)){
                $flashmessenger->addMessage(array('success'=>'Hecho !! El termino de pago se elimino satisfactoriamente.'));                
            }
        }else{
            $message = 'Oops !!, esta Calidad no puede ser eliminada, esta siendo utilizada en almenos un registro.';
            $flashmessenger->addMessage(array('info'=>$message));
        }
        header('Location: PaymentTerms.php');
        break;    
        
     case 'ajax':
        $ajaxPaymentTerms = new PaymentTermsAjax();
        $json = $ajaxPaymentTerms->getResponse($_POST['request'], $_POST);
        echo json_encode($json);
        break;
    
    default:
        $vista = 'PaymentTerms.php';
        include $root.'/View/Template.php';
}