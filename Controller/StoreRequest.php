<?php 

$controller = 'StoreRequest';

$action = '';

if(isset($_POST['action'])){

    $action = $_POST['action'];

    if (isset($_POST['id'])) {$id = $_POST['id'];}

}elseif(isset($_GET['action'])){

    $action = $_GET['action'];

    if (isset($_GET['id'])) {$id = $_GET['id'];}

}



include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';



$_form = new StoreRequestForm();

$_storeRequest = new StoreRequestRepository();

$_settings = new SettingsRepository();



switch($action){

    case 'insert':        

        $_form->setTokenForm($_POST['token_form']);

        $_form->populate($_POST);  

        $_storeRequest->setOptions($_POST);

        if ($_form->isValid()) {             

            if ($_storeRequest->save($_storeRequest->getOptions())) {                

                $_storeRequestNo = $_storeRequest->getLastInsertId();

                $printStoreRequest = "<a href=\"\" onclick=\"javascript: void window.open('/Controller/StoreRequest.php?action=export&format=pdf&flag=in&id=$_storeRequestNo','','width=700,height=500,status=1,scrollbars=1,resizable=1')\">".$_translator->_getTranslation('Imprimir')."</a>";  

                $flashmessenger->addMessage(array(

                    "success"=>$_translator->_getTranslation("Pedido de sucursal")." #$_storeRequestNo ".$_translator->_getTranslation("se ha registrado exitosamente.")));

                

                header("Location: StoreRequest.php?action=edit&id=$_storeRequestNo");

            }else{                

                $vista = 'StoreRequest.php';

                include $root . '/View/Template.php';                

            }

        } else {

            $vista = 'StoreRequest.php';

            include $root . '/View/Template.php';

        }

        break;

        

    case 'list':          

        $searchFilter = null;

        if(isset($_POST['search'])){$searchFilter = $_POST;}

        $_listStoreRequests = $_storeRequest->getListStoreRequest($searchFilter);

        

        $vista = 'StoreRequestList.php';

        include $root . '/View/Template.php';

    break;

        

    case 'export': 

        switch($_GET['format']){

            case 'excel':

                switch($_GET['flag']){

                    case 'search':

                        $_storeRequest->resultSearchToExcel($_GET);

                        break;

                }       

                break;

            

            case 'pdf':

                switch($_GET['flag']){

                    case 'store_request':

                        $pdf = new StoreRequestPDF($id);

                        break;

                    }   

                break;

        }

         

        break;

    

    case 'edit': 

        $login = new Login();

        

       if($_GET){

            $_storeRequestData = $_storeRequest->getById($id);

            $storeIds = explode(',', $login->getStoreId());

            

            if($login->getRole() != '1' &&  !in_array($_storeRequestData['store_id'], $storeIds) ){

                header("Location: StoreRequest.php?action=list");

            }              

            

            /*Si es usuario Sucursal y esta intentado editar un StoreRequest que no es de su sucursal ; lo envia a listado*/

            if($login->getRole() == '14' && !in_array($_storeRequestData['store_id'], $storeIds)){

                header("Location: StoreRequest.php?action=list");

            }

            

            $_storeRequest->crearTablaDetallesForUser();

            $_storeRequest->setStoreRequestDetallesById($id,$_form->getTokenForm());            

        }

        

        if($_POST){            

            $_form->setTokenForm($_POST['token_form']);

            $_storeRequestData = $_POST;

        }

        

        $_form->setActionController('edit');

        $_form->setId($id);

        $_form->populate($_storeRequestData);       

        $_form->setReadOnlydElements(array('store_id','area_id'));

        $_disabled = null;

        

        if($_storeRequestData['status'] == '2'){

            $_disabled = true;

            $_form->disabledAllElements();

            $_form->hideElement(array('terminar'));

        }

        

        $tools = new Tools();

        $deliveryDate = new DateTime($tools->setFormatDateToDB($_storeRequestData['delivery_date']));

        $today = new DateTime(date('Y-m-d'));

        

        if($today >= $deliveryDate){

            $_disabled = true;

            $_form->disabledAllElements();

            $_form->hideElement(array('terminar'));

        }else{

            $deliveryDate->modify('-1 day');

            if($deliveryDate == $today && date('G') >= '21'){ /*Si Hoy es un dia antes de la entrega y ya son mas de las 9*/

                $_disabled = true;

                $_form->disabledAllElements();

                $_form->hideElement(array('terminar'));

            }

        }      

        

        $_storeRequest->setOptions($_storeRequestData);

        if(isset($_POST['id'])){

            if($_form->isValid()){                

                $result = $_storeRequest->update($id,$_storeRequest->getOptions()); //no tengo id porque viene de post

                if($result){

                    $flashmessenger->addMessage(array('success'=>'Pedido de sucursal se actualizo exitosamente.'));

                    header("Location: StoreRequest.php?action=edit&id=$id");

                }else{

                     $vista = 'StoreRequest.php';

                    include $root . '/View/Template.php';                    

                }       

            }else{

                $vista = 'StoreRequest.php';

                include $root . '/View/Template.php';

            }

        }else{

            $vista = 'StoreRequest.php';

            include $root . '/View/Template.php';

        }        

        break;

    

    case 'delete':

        /*Si es usuario Sucursal y esta intentado eliminar un StoreRequest que no es de su sucursal ; lo envia a listado*/

        $_storeRequestData = $_storeRequest->getById($id);

        if($login->getRole() == '14' && $_storeRequestData['store_id'] !== $login->getStoreId()){

            header("Location: StoreRequest.php?action=list");

        }

        

        if($_storeRequest->delete($id)){

            $flashmessenger->addMessage(array('success'=>'Pedido de sucursal se cancelo exitosamente.'));

        }

            

        header('Location: StoreRequest.php?action=list');

        break;

        

    case 'ajax':

        $ajaxStoreRequest = new StoreRequestAjax();

        $json = $ajaxStoreRequest->getResponse($_POST['request'],$_POST);

        

        echo json_encode($json);

        break;       

    

    /*PARA ROLES DE PASTELERIA Y PANADERIA*/

    case 'store_request_list':       

        $searchFilter = null;

        if(isset($_POST['search'])){$searchFilter = $_POST;}        

        

        $login = new Login();

        $areaRepo = new AreaRepository();

        $_areaData = $areaRepo->getByRoleId($login->getRole());           

        $searchFilter['area_id'] = $_areaData['id'];

       

        $_listStoreRequests = $_storeRequest->getListStoreRequestForShipmentAndProduction($searchFilter);

        

        $vista = 'StoreRequestListToCreateShipment.php';

        include $root.'/View/Template.php';

        break;

    

    default:      

        $_storeRequest->crearTablaDetallesForUser();

        

        $vista = 'StoreRequest.php';

        include $root.'/View/Template.php';

        break;

}