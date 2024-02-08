<?php 

$controller = 'SpecialOrder';

$action = '';

if(isset($_POST['action'])){

    $action = $_POST['action'];

    if (isset($_POST['id'])) {$id = $_POST['id'];}

}elseif(isset($_GET['action'])){

    $action = $_GET['action'];

    if (isset($_GET['id'])) {$id = $_GET['id'];}

}



include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';



$_reload = null;

$form = new SpecialOrderForm();

$specialOrder = new SpecialOrderRepository();



$_currentDate = date('Y-m-d');

$_minDate = date("Y-m-d",strtotime($_currentDate."- 1 days")); 



switch($action){

    case 'insert':

        $vista = 'SpecialOrder.php';     

        $form->setTokenForm($_POST['token_form']);

        $form->disabledElements(array('btn_allow_edit'));

        $form->hideElement(array('btn_allow_edit'));  

        $form->populate($_POST);          

        if ($form->isValid()) {

            $specialOrder->setOptions($_POST); 

             $specialOrder->setImage($_FILES['image']);

            if ($specialOrder->save($specialOrder->getOptions())) {                

                $specialOrderNo = $specialOrder->getLastInsertId();

                $printSpecialOrder = "<a href=\"\" onclick=\"javascript: void window.open('/Controller/SpecialOrder.php?action=import&flag=pdf&id=$specialOrderNo','','width=700,height=500,status=1,scrollbars=1,resizable=1')\">".$_translator->_getTranslation('Imprimir')."</a>";  

                $flashmessenger->addMessage(array(

                    "success"=>$_translator->_getTranslation("Pedido")." #$specialOrderNo ".$_translator->_getTranslation("registrado exitosamente.")));

              

               header("Location: SpecialOrder.php?action=list");

                   

            }else{                               

                include $root . '/View/Template.php';                

            }

        } else {         

            include $root . '/View/Template.php';

        }

        break;

    

    case 'list':        

        $_queryString = NULL;

        $searchFilter = null;



        if (isset($_GET['search'])) {

            $searchFilter = $_GET;

        }

        $_listSpecialOrders  = $specialOrder->getListRequisitions($searchFilter);



        $vista = 'SpecialOrderList.php';       

        include $root . '/View/Template.php';

        break;

        

    case 'list-production':

        $searchFilter = null;

        if(isset($_POST['search'])){

            $searchFilter = $_POST;

            $searchFilter['sin_roscas'] = true;

            $_listSpecialOrders = $specialOrder->getListRequisitions($searchFilter);      

        }else{

            $searchFilter['sin_roscas'] = true;

            $_listSpecialOrders = $specialOrder->getListRequisitionsDecorado($searchFilter);      

        }                 

        

        $vista = 'SpecialOrderListProduction.php';

        include $root . '/View/Template.php';       

        

        break;

        

    case 'list-production-roscas':

        $searchFilter = null;

        if(isset($_POST['search'])){

            $searchFilter = $_POST;

            $searchFilter['solo_roscas'] = true;

            $_listSpecialOrders = $specialOrder->getListRequisitions($searchFilter);      

        }else{

            $searchFilter['solo_roscas'] = true;

            $_listSpecialOrders = $specialOrder->getListRequisitionsDecorado($searchFilter);      

        }                 

        

        $vista = 'SpecialOrderListProduction.php';

        include $root . '/View/Template.php';       

        

        break;

        

    case 'batch':        

        switch($_POST['action-batch']){

            case 'special_production_plan':

                $_specialRequisitions = null;

                //var_dump($_POST);exit;

                if(isset($_POST['special_orders'])){$_specialRequisitions = $_POST['special_orders'];}              

               

                $startEndDate = $specialOrder->getMaxMinDateFromIdsRequisitions($_specialRequisitions);

                

                $reportName = array('list-production'=>'special_production_plan','list-production-roscas'=>'special_production_plan_roscas');

                $data = array(

                   'report'=>$reportName[$_POST['report_name']],

                   'startDate'=>$startEndDate['startDate'],

                   'endDate'=>$startEndDate['endDate'],

                   'special_orders'=>$_specialRequisitions

               );

               

               $reportList = new ReportsListEntity();              

               $reportList->setOptions($data);  

               $reportList->getReporteOnFile('excel'); 

               break;

        }

        

        $_listRequisitions = $specialOrder->getListRequisitions();



        $vista = 'SpecialRequisitionListProduction.php';

        include $root . '/View/Template.php';



        break;



    case 'export':

        switch($_GET['flag']){

            case 'pdf':

                $pdf = new SpecialOrderPDF($_GET['id']);

                break;

        }        

        break;

    

    case 'edit':

        $edit = true;

        $vista = 'SpecialOrder.php';

        $tools = new Tools();        

        $login = new Login();

        

        if($_GET){

            $specialOrderData = $specialOrder->getById($id);

            $specialOrder->crearTablaDetallesForUser();

            $specialOrder->setRequisitionDetailsById($id,$form->getTokenForm());           

        } 

        

        if($login->getRole() != '1' && $login->getStoreId() != $specialOrderData['store_id']){

            header("Location: SpecialOrder.php?action=list");

        }       

        

        if($_POST){

            $form->setTokenForm($_POST['token_form']);

            $specialOrderData = $_POST;

            $deliveryDate = $specialOrderData['delivery_date'];

            $specialOrderData['delivery_date'] = $tools->setFormatDateTimeToDB($specialOrderData['delivery_date']);            

        }  

        

        $specialOrder->setOptions($specialOrderData);

        $specialOrder->setId($id);

        

        /*Para actualizar status_pago y ammount_payments*/

        /*Esto, porque en ocasiones no se actualizaba cuando se capturaba pago de la req en POS*/

        //$specialOrder->getSaldoPendiente($id);

        

        $_minDate = $specialOrder->getMinDate();

        $_currentDate = $specialOrder->getCurrentDate();

        //$_listPayments = $specialOrder->getListPayments();        

        

        $form->setActionController('edit');

        $form->setId($id);

        $form->populate($specialOrderData);

        

        $_disabled = null;

        $_allow_payments = true;

        if($specialOrderData['status']=='2' || $specialOrderData['status_delivery'] == '2'){

            $form->disabledAllElements();

            $form->hideElement(array('terminar'));

            $_disabled = true;

            $_allow_payments = null;            

        }

        elseif($specialOrderData['status']=='1'){

            if($specialOrderData['antiguedad'] > 0){

                $form->disabledAllElements();

                $_disabled = true; 

                $edit = null;

                

                if($specialOrderData['allow_edit'] === '1'){

                    $form->disabledElements(array('btn_allow_edit'));

                    $form->hideElement(array('btn_allow_edit'));  

                    

                    $form->enabledAllElement();

                    $_disabled = null; 

                    $edit = true;

                    $specialOrder->removeAllowEdit($id);

                }else{

                    $form->enabledElement(array('btn_allow_edit'));                    

                    $form->hideElement(array('terminar'));

                }

            }else{

                $form->disabledElements(array('btn_allow_edit'));

                $form->hideElement(array('btn_allow_edit'));  

            }     

        }    

        

        if(isset($_POST['id']) && $edit){

            $specialOrder->setDeliveryDate($deliveryDate);

            if($form->isValid()){

                $specialOrder->setImage($_FILES['image']);

                $result = $specialOrder->update($id,$specialOrder->getOptions()); //no tengo id porque viene de post

                if($result){ 

                    $flashmessenger->addMessage(array('success'=>'Orden especial actualizada exitosamente.'));

                    header("Location: SpecialOrder.php?action=list");

                    

                }else{

                    

                    include $root . '/View/Template.php';                    

                }       

            }else{

               

                include $root . '/View/Template.php';

            }

        }else{

            

            include $root . '/View/Template.php';

        }        

        break;

    

    case 'delete':

        if($specialOrder->delete($id)){

            $flashmessenger->addMessage(array('success'=>'Orden especial eliminada satisfactoriamente.'));

        }        

        header("Location: SpecialOrder.php?action=list");

        break;

        

    case 'ajax':

        $ajaxSpecialOrder = new SpecialOrderAjax();

        $json = $ajaxSpecialOrder->getResponse($_POST['request'],$_POST);

        echo json_encode($json); 

        break;       

        

    case 'pdf':

        $specialOrderPDF = new SpecialOrderPDF($_GET['id']);

        break;

    

    default:     

        $form->disabledElements(array('btn_allow_edit'));

        $form->hideElement(array('btn_allow_edit'));  

        $specialOrder->crearTablaDetallesForUser();

        $vista = 'SpecialOrder.php';        

        include $root.'/View/Template.php';        

        

        break;

}