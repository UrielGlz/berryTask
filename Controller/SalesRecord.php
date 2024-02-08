<?php 

$controller = 'SalesRecord';

$action = '';

if(isset($_POST['action'])){

    $action = $_POST['action'];

    if (isset($_POST['id'])) {$id = $_POST['id'];}

}elseif(isset($_GET['action'])){

    $action = $_GET['action'];

    if (isset($_GET['id'])) {$id = $_GET['id'];}

}

include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';



$form = new SalesRecordForm();

$_salesRecord = new SalesRecordRepository();



$searchFilter = null;

if(isset($_POST['search'])){$searchFilter = $_POST;}



$_listSalesRecords = $_salesRecord->getListSalesRecords($searchFilter);

$_noValid = null;



switch($action){

    case 'insert': 

        $form->setTokenForm($_POST['token_form']);

        $form->populate($_POST);

        if($form->isValid()){            

            $_salesRecord->setOptions($_POST);

            $result = $_salesRecord->save($_salesRecord->getOptions());

            if($result){

                $flashmessenger->addMessage(array('success'=>'Registro de ventas registrado exitosamente.'));

                header('Location: SalesRecord.php');

            }else{

                $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));

            }

        }else{

            $_noValid = true;

            $vista = 'SalesRecord.php';

            include $root.'/View/Template.php';

        }

        break;

   

    case 'edit':

        if($_GET){

            $_salesRecordData = $_salesRecord->getById($id);



            

            $_salesRecord->crearTablaDetallesForUser();

            $_salesRecord->setSalesRecordExpenseDetallesById($id,$form->getTokenForm()); 

        }

        if($_POST){

            $form->setTokenForm($_POST['token_form']);

            $_salesRecordData = $_POST;

        }

        

        $login = new Login();

        if($login->getRole() != '1' && !in_array($_salesRecordData['store_id'], $login->getStoreArray())){

            header("Location: SalesRecord");

            exit;

        }

        

        $form->setActionController('edit');

        $form->setId($id);

        $form->populate($_salesRecordData);

        

        $edit = null;

        if($_salesRecordData['allow_edit'] === '1'){

            $edit = true;

            $_salesRecord->removeAllowEdit($id);

        }else{

            $_disabled = true;

            $form->disabledAllElements();

            $form->hideElement(array('terminar'));

            $form->enabledElement(array('btn_allow_edit'));                 

        }      

        

        $_salesRecord->setOptions($_salesRecordData);

        if(isset($_POST['id'])){

            if($form->isValid()){

                $result = $_salesRecord->update($id,$_salesRecord->getOptions()); //no tengo id porque viene de post

                if($result){

                    $flashmessenger->addMessage(array('success'=>'Registro de ventas actualizado exitosamente.'));

                    header("Location: SalesRecord.php");

                }else{

                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));

                }       

            }else{

                $_noValid = true;

                $vista = 'SalesRecord.php';

                include $root . '/View/Template.php';

            }

        }else{

            $vista = 'SalesRecord.php';

            include $root . '/View/Template.php';

        }        

        break;

    

    case 'delete': 

        if(!$_salesRecord->isUsedInRecord($id)){

            if($_salesRecord->delete($id)){

                $flashmessenger->addMessage(array('success'=>'Registro de venta eliminado satisfactoriamente.'));

            }

        }else{

             $flashmessenger->addMessage(array('success'=>'Registro de ventas no puede ser eliminado esta siendo utilizado en almenos un registro.'));

        }

        

        header("Location: SalesRecord.php");

        break;

        

     case 'ajax':

        $ajaxSalesRecord = new SalesRecordAjax();

        $json = $ajaxSalesRecord->getResponse($_POST['request'], $_POST);

        echo json_encode($json);

        break;

    

    default:       

        $_salesRecord->crearTablaDetallesForUser();

        $vista = 'SalesRecord.php';

        include $root.'/View/Template.php';

}