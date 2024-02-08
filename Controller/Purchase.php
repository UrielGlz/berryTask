<?php 

$controller = 'Purchase';

$action = '';

if(isset($_POST['action'])){

    $action = $_POST['action'];

    if (isset($_POST['id'])) {$id = $_POST['id'];}

}elseif(isset($_GET['action'])){

    $action = $_GET['action'];

    if (isset($_GET['id'])) {$id = $_GET['id'];}

}



include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';



$form = new PurchaseForm();

$_purchase = new PurchaseRepository();



$isUserApprover = null;

$settings = new SettingsRepository();

$purchase_user_approver = $settings->_get('purchase_user_approver');

$purchase_user_approver = explode(',', $purchase_user_approver);

if(in_array($login->getRole(), $purchase_user_approver)){

    $isUserApprover = true;

}



switch($action){

    case 'insert':

        $form->setTokenForm($_POST['token_form']);

        $form->populate($_POST);   

        $_POST['attachments'] = $_FILES;

        $_purchase->setOptions($_POST); 

        if ($form->isValid()) {            

            if ($_purchase->save($_purchase->getOptions())) {                

                $_purchaseNo = $_purchase->getLastInsertId();                

                $flashmessenger->addMessage(array(

                    "success"=>$_translator->_getTranslation("Compra")." #$_purchaseNo ".$_translator->_getTranslation("registrada exitosamente.")));

                

                header("Location: Purchase.php?action=edit&id=$_purchaseNo");

            }else{                

                $vista = 'Purchase.php';

                include $root . '/View/Template.php';                

            }

        } else {

            $vista = 'Purchase.php';

            include $root . '/View/Template.php';

        }

        break;

    

    case 'list':

        $_listPurchase = $_purchase->getListPurchase();

        

        $vista = 'PurchaseList.php';

        include $root . '/View/Template.php';

        break;

        

    case 'export':

        $login = new Login();

        $_purchaseData = $_purchase->getById($id);

        if($login->getRole() != '1' && !in_array($_purchaseData['store_id'], $login->getStoreArray())){

            echo $_translator->_getTranslation('No tienes permiso para ejecutar esta accion.');

            exit;

        }

        

        switch($_GET['flag']){

            case 'pdf':

                $pdf = new PurchasePDF($_GET['id']);

                break;

        }        

        break;

    

    case 'edit':           

        $login = new Login();

        

        if($_GET){

            $_purchaseData = $_purchase->getById($id);

            

            if($login->getRole() != '1' && $login->getStoreId() != $_purchaseData['store_id']){

                header("Location: Purchase.php?action=list");

            }   

            

            $_purchase->crearTablaDetallesForUser();

            $_purchase->setPurchaseDetailsById($id,$form->getTokenForm());

            

            $_purchase->_history(array('action'=>'viewed','id'=>$id));

        }

        

        if($_POST){           

            $form->setTokenForm($_POST['token_form']);

            $_POST['attachments'] = $_FILES;

            $_purchaseData = $_POST;

       }        

        

        $login = new Login();

        if($login->getRole() != '1' && !in_array($_purchaseData['store_id'], $login->getStoreArray())){

            header("Location: Purchase.php?action=list");

            exit;

        }

        

        $form->setActionController('edit');

        $form->setId($id);        

        $form->populate($_purchaseData);

        

        $_disabled = null;

        if($_purchaseData['status']=='2'){

            

            if(isset($_purchaseData['status_approval']) && $_purchaseData['status_approval'] =='2'){ /*Aprobado por sistema*/

                $form->disabledElements(array('status_approval'));

                $_disabled = null;

            }

            

           if(isset($_purchaseData['status_approval']) && $_purchaseData['status_approval'] =='1'){ /*Aprobado por aprobador*/

                $form->hideElement(array('agregar_producto'));

                $_disabled = true;

            }

        }

        

        if($_purchaseData['status']=='3'){

                $form->disabledElements(array('status_approval'));

                $form->hideElement(array('agregar_producto'));

                $_disabled = true;

        }

        

        if($_purchaseData['status']=='4'){

                $form->disabledAllElements();

                $form->hideElement(array('agregar_producto','terminar'));

                $_disabled = true;

        }

        

        $_timeLine = $_purchase->getTimeLine($id);

        

        if(isset($_POST['id'])){

            if($form->isValid()){

                $_purchaseData['invoice_file'] = $_FILES;

                $_purchase->setOptions($_purchaseData);

                $result = $_purchase->update($id,$_purchase->getOptions()); //no tengo id porque viene de post

                if($result){

                    $flashmessenger->addMessage(array('success'=>'Compra actualizada exitosamente.'));

                    header("Location: Purchase.php?action=edit&id=$id");

                }else{

                     $vista = 'Purchase.php';

                    include $root . '/View/Template.php';                    

                }       

            }else{

                $vista = 'Purchase.php';

                include $root . '/View/Template.php';

            }

        }else{

            $vista = 'Purchase.php';

            include $root . '/View/Template.php';

        }        

        break;

    

    case 'delete':

        $login = new Login();

        $_purchaseData = $_purchase->getById($id);

        if($login->getRole() != '1' && !in_array($_purchaseData['store_id'], $login->getStoreArray())){

            $flashmessenger->addMessage(array('danger'=>'No tienes permiso para ejecutar esta accion.'));

            header("Location: Purchase.php?action=list");

            exit;

        }

        

        if($_purchase->delete($id)){

            $flashmessenger->addMessage(array('success'=>'Compra eliminada satisfactoriamente.'));

        }        

        header("Location: Purchase.php?action=list");

        break;

        

    case 'ajax':

        $ajaxPurchase = new PurchaseAjax();

        $json = $ajaxPurchase->getResponse($_POST['request'],$_POST);

        

        echo json_encode($json);

        break;       

    

    default:      

        $_purchase->crearTablaDetallesForUser();

        $vista = 'Purchase.php';

        include $root.'/View/Template.php';

        break;

}