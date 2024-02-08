<?php 

$controller = 'Reports';

$action = '';



if(isset($_POST['action'])){

    $action = $_POST['action'];

    $data = $_POST;

}elseif(isset($_GET['action'])){

    $action = $_GET['action'];

   $data = $_GET;

}



include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new ReportForm();

$reportList = new ReportsListEntity();  



switch($action){

    case 'create':

        $reportList->setOptions($data);

        if($reportList->getReporte() === null){

            $vista = 'Reports.php';

            include $root.'/View/Template.php';



            break;

        }

        

        if(isset($data['enviarPorMail'])){

            $reportList->saveFile();     

            $reportList->getReporteOnFile();

            $mail = new Emailer();

            $data['attachment'] = $reportList->getFileSaved();   



            if($mail->sendEmail($data)){

                $reportList->flashmessenger->addMessage(array('success'=>'Reporte enviado correctamente.'));

                header('Location: Reports.php');

            }

        }else{        

            if($data['output']=='excel'){

                $reportList->getReporteOnFile('excel');

            }elseif($data['output']=='pdf'){

                $reportList->getReporteOnFile('pdf');

            }elseif($data['output']=='screen'){

               $_reporte = $reportList;              

            }

        }

        

        $vista = 'Reports.php';

        include $root.'/View/Template.php';

        

        break;

        

    case 'ajax':

        $ajaxReport = new ReportAjax();

        $json = $ajaxReport->getResponse($_POST['request'],$_POST);

        

        echo json_encode($json);

        break;       



    default:        

        $vista = 'Reports.php';

        include $root.'/View/Template.php';

        break;

}

