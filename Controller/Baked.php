<?php 
$controller = 'Baked';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}

include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

//$requisition = new RequisitionEntity();
$specialRequisition = new SpecialOrderRepository();

switch($action){    
    case 'batch':
        switch($_POST['action-batch']){
            case 'baked_plan_pdf':
                $requisitions = null;
                $specialRequisitions = null;
                
                //if(isset($_POST['requisitions'])){$requisitions = $_POST['requisitions'];}                
                if(isset($_POST['special_orders'])){$specialRequisitions = $_POST['special_orders'];}
                
                $pdf = new BakedPlanPDF($requisitions,$specialRequisitions);
                break;
                
            case 'baked_plan':
                $requisitions = null;
                $specialRequisitions = null;
                
                //if(isset($_POST['requisitions'])){$requisitions = $_POST['requisitions'];}                
                if(isset($_POST['special_orders'])){$specialRequisitions = $_POST['special_orders'];}
                
                $data = array(
                   'report'=>'baked_plan',
                   'requisitions'=>$requisitions,
                   'special_orders'=>$specialRequisitions
               );
               
               try{
                    $reportList = new ReportsListEntity();              
                    $reportList->setOptions($data);                      
                    $reportList->getReporteOnFile('excel');                   
                    
               } catch (Exception $ex) {
                   $specialRequisition->flashmessenger->addMessage(array('danger'=>$ex->getMessage()));
               }
              
               break;
        }
        break;
        
    case 'export':
        switch($_GET['flag']){
            case 'pdf':
                $pdf = new SpecialRequisitionPDF($_GET['id']);
                break;
        }        
        break;    
        
    case 'list':
        $searchFilter = null;
        if(isset($_POST['search'])){
            $searchFilter = $_POST;
            $searchFilter['sin_roscas'] = true;
            $_specialRequisitionsList = $specialRequisition->getListRequisitions($searchFilter);    
        }else{
            $searchFilter['sin_roscas'] = true;
            $_specialRequisitionsList = $specialRequisition->getListRequisitionsHorneado($searchFilter);    
        }

        
        $vista = 'BakingPlan.php';
        if($login->getRole() === '4'){
             include $root.'/View/TemplateProduction.php';
        }else{
             include $root.'/View/Template.php';
        }
        
        break;
        
    case 'listr':
        $searchFilter = null;
        if(isset($_POST['search'])){
            $searchFilter = $_POST;
            $searchFilter['solo_roscas'] = true;
            $_specialRequisitionsList = $specialRequisition->getListRequisitionsRoscas($searchFilter);    
        }else{
            $searchFilter['solo_roscas'] = true;
            $_specialRequisitionsList = $specialRequisition->getListRequisitionsHorneado($searchFilter);    
        }

        
        $vista = 'BakingPlan.php';
        if($login->getRole() === '4'){
             include $root.'/View/TemplateProduction.php';
        }else{
             include $root.'/View/Template.php';
        }
        
        break;
}