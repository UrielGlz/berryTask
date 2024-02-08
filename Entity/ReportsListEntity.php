<?php

class ReportsListEntity Extends ReportsListRepository{

    

    public $flashmessenger = null;

    private $saveFile = null;

    private $options = array(

        'report'=>null,

        'startDate'=>null,

        'endDate'=>null,

        'dueStartDate'=>null,

        'dueEndDate'=>null,

        'store_id'=>null,

        'status'=>null,

        'special_orders'=>null,

        'user_id'=>null,

        'area_id'=>null,

        'masa'=>null,

        'area_bakery_production_id'=>null

    );

    private $nombreArchivo = null;

    public $reporte = null;

    private $tempFolder = null;

    private $headerExcelReport = null;

    

     private $reportes = array(

        'inventory'=>'inventory',

        'special_production_plan'=>'specialProductionPlan',

        'special_production_plan_roscas'=>'specialProductionPlan',

        'baked_plan'=>'bakedPlan',

        'store_request'=>'storeRequest',

        'time_clock'=>'timeClock',

        'sales'=>'sales',

        'special_orders'=>'specialOrders',

        'inventory_template_pdf'=>'inventoryTemplatePDF',

        'physical_inventory'=>'physicalInventory',

        'bakery_production'=>'bakeryProduction',

        'detailed_bakery_orders'=>'detailedBakeryOrders',

        'sales_to_store'=>'salesToStore',

        'sales_by_store'=>'salesByStore',

        'review_payroll'=>'reviewPayroll',

        'invoices'=>'invoices',

        'deposits'=>'deposits',

        'users_list'=>'usersList',
        'mermas' => 'mermas'

    );

    

    private $nombreReportes = array(

        'inventory'=>'Inventario',

        'special_production_plan'=>'Plan de produccion especiales',

        'special_production_plan_roscas'=>'Plan de produccion roscas',

        'baked_plan'=>'Plan de horneado',

        'store_request'=>'Pedidos de sucursal',

        'time_clock'=>'Reloj checador',

        'sales'=>'Ventas',

        'special_orders'=>'Ordenes especiales',

        'inventory_template_pdf'=>'Registro de inventario fisico',

        'physical_inventory'=>'Inventario fisico',

        'bakery_production'=>'Produccion de panaderia',

        'detailed_bakery_orders'=>'Pedidos panaderia detallado',

        'sales_to_store'=>'Ventas a sucursal',

        'sales_by_store'=>'Ventas netas por sucursal',

        'review_payroll'=>'Revision de payroll',

        'invoices'=>'Facturas',

        'deposits'=>'Depositos',

        'users_list'=>'Lista de usuarios',
        'mermas' => 'mermas'

    );

    

    #Para excel 'nombreReporte'=>'nombreArchivo en View/Reports/Excel'

    private $reportesEspeciales = array(

        'inventory'=>'inventory',

        'special_production_plan'=>'special_production_plan',

        'special_production_plan_roscas'=>'special_production_plan_roscas',

        'baked_plan'=>'baked_plan',

        'time_clock'=>'time_clock',

        'sales'=>'sales',

        'special_orders'=>'special_order',

        'detailed_bakery_orders'=>'detailed_bakery_orders',

        'sales_to_store'=>'sales_to_store',

        'invoices'=>'invoices',

        'deposits'=>'deposits',

        'users_list'=>'users_list',
        'mermas' => 'mermas'

    );

    

    #Para pdf 'nombreReporte'=>'nombreArchivo en View/Reports/Pdf'

    private $reportesEspecialesPDF = array(

        'store_request'=>'store_request',        

        'inventory_template_pdf'=>'inventory_template_pdf',

        'bakery_production'=>'bakery_production'

    );

    

    #Para pantala=la 'nombreReporte'=>'nombreArchivo en View/Reports/Screen'

    private $reportesEspecialesScreen = array(

        'inventory'=>'inventory',

        'physical_inventory'=>'physical_inventory',

        'sales_by_store'=>'sales_by_store',

        'review_payroll'=>'review_payroll'

    );    

    

    public function __construct() {

        if(!$this->flashmessenger instanceof FlashMessenger){

            $this->flashmessenger = new FlashMessenger();

        }

    }

    

    public function _getTranslation($text){

        return $this->flashmessenger->_getTranslation($text);

    }

    

    public function setOptions($data){        

      foreach ($this->options as $option => $value){

          if(isset($data[$option])){

            $this->options[$option] = $data[$option];

          }

      } 

      $this->createReporte();

    }

    

    public function getOptions(){

        return $this->options;

    }

    

    public function getNombreReporte(){

        return $this->options['report'];

    }

    

    public function getTituloReporte(){

        return $this->nombreReportes[$this->options['report']];

    }

    

    public function getNombreArchivo(){

        return str_replace(' ', '', $this->nombreArchivo).' '.date('m d Y H_i_s');

    }

    

    public function setNombreArchivo(){

        if(isset($this->nombreReportes[$this->getNombreReporte()])){

            $this->nombreArchivo = $this->nombreReportes[$this->getNombreReporte()];

        }

    }

    

    public function setNombreReporteManual($nombreArchivo){

        $this->nombreArchivo = $nombreArchivo;

    }

    

    public function setHeaderExcelReportManual($headerReport){

        $this->headerExcelReport = $headerReport;

    }

    

    public function createReporte(){

        ini_set('max_execution_time', 18000);

        ini_set('mysql.connect_timeout', 18000);

        ini_set('default_socket_timeout', 18000);

        ini_set('memory_limit', '-1');

       

        $nombreReporte = $this->reportes[$this->getNombreReporte()];       

        $this->setNombreArchivo();

        $this->setReporte($this->$nombreReporte());  

        

        $_SESSION['optionsCurrentReport'] = array(

            'options'=>$this->getOptions()

        );

    }

    

    public function getStringToSendGET(){

        $options = $this->getOptionsCurrentReport();

        $string = "?";

        foreach($options['options'] as $key => $value){

            $string .= $key."=".$value."&";

        }

        

        $string = trim($string,"&");

        return $string;

    }

    

    public function getOptionsCurrentReport(){

        return $_SESSION['optionsCurrentReport'];

    }

    

     public function setReporte($data){    

        

        if($data == null){ 

            $this->flashmessenger->addMessage(array('danger'=>'No se encontraron resultados.'));

            return null;

        }

        

        $this->reporte = $data; 

    }

    

    public function getReporte(){

       return $this->reporte;

    }

    

     public function inventory(){        

        return parent::getInventory($this->getOptions());

    }    

        

    public function specialProductionPlan(){

        return parent::getSpecialProductionPlan($this->getOptions());

    }

    

    public function BakedPlan(){

        return parent::getBakedPlan($this->getOptions());

    }

    

    public function storeRequest(){

        return parent::getStoreRequest($this->getOptions());

    }

    

    public function timeClock(){

        return parent::getTimeClock($this->getOptions());

    }

    

    public function sales(){

        return parent::getSales($this->getOptions());

    }
    public function mermas(){
        return parent::getMermas($this->getOptions());
    }
    

    public function specialOrders(){

        return parent::getSpecialOrders($this->getOptions());

    }

    

    public function inventoryTemplatePDF(){

        return parent::getInventoryTemplatePDF();

    }

    

    public function physicalInventory(){

         return parent::getPhysicalInventory($this->getOptions());

    }

    

    public function bakeryProduction(){

        return parent::getBakeryProduction($this->getOptions());

    }

    

    public function detailedBakeryOrders(){

        return parent::getDetailedBakeryOrders($this->getOptions());

    }

    

     public function salesToStore(){

        return parent::getSalesToStore($this->getOptions());

    }

    

    public function salesByStore(){

        return parent::getSalesByStore($this->getOptions());

    }

    

    public function reviewPayroll(){

        return parent::getReviewPayroll($this->getOptions());

    }  

    

    public function invoices(){

        return parent::getInvoices($this->getOptions());

    }

    

    public function deposits(){

        return parent::getDeposits($this->getOptions());

    }

    

    public function usersList(){

        return parent::getUsersList($this->getOptions());

    }

    

    public function getReporteOnFile($formato = null){ 

        $empresa = new CompanyRepository();

        $empresa = $empresa->getById(1);

        

        $startDate = $this->options['startDate'];

        $endDate = $this->options['endDate'];

        if(trim($startDate)=='' && trim($endDate)==''){

            $startDate = $this->_getTranslation("Desde el inicio de los tiempos");

            $endDate = strftime('%m/%d/%Y',strtotime('now'));

        }elseif(trim($startDate)=='' && trim($endDate)!=''){

            $startDate = $endDate;

        }elseif(trim($startDate)!='' && trim($endDate)==''){

            $endDate = $startDate;

        }

        $this->setHeaderExcelReportManual($empresa['name']."\n".$this->_getTranslation($this->getTituloReporte())."\n".$startDate." - ".$endDate);

      

        if($formato == 'excel'){ 

            if(isset($this->reportesEspeciales[$this->options['report']])){ 

             $this->reporteEspecial();

            }else{

                $this->reporteDefault();

            }

        }elseif($formato == 'pdf'){ 

            if(isset($this->reportesEspecialesPDF[$this->options['report']])){

                $this->reporteEspecialPDF();

            }

        }                       

    }

    

    public function reporteEspecial(){

        $data = $this->reporte;

        include ROOT.'/View/Reports/Excel/'.$this->options['report'].".php";

    }

    

    public function reporteEspecialPDF(){

        $data = $this->reporte;

        include ROOT.'/View/Reports/Pdf/'.$this->options['report'].".php";

    }

    

    public function getTemplateReporteOnScreen(){

        if(isset($this->reportesEspecialesScreen[$this->options['report']])){

            return ROOT.'View/Reports/Screen/'.$this->options['report'].".php";

        }else{

            return ROOT.'View/Reports/Screen/default.php';

        }

    }

    

    public function reporteDefault(){

        $arrayData = $this->reporte;

        $arrayData = $arrayData['data'];

                $colTitulosTemp = $arrayData[0];

        foreach($colTitulosTemp as $titulo => $value){

            $colTitulos[] = $titulo;

        }

        

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getActiveSheet()->freezePane('A3');

        $col = count($colTitulos);

        $lastCol = $this->getColLetter($col-1);



        $objPHPExcel->getActiveSheet()->mergeCells("A1:".$lastCol."1");            

        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(50);

        $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->setCellValue("A1", $this->headerExcelReport);

        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



        $objPHPExcel->getActiveSheet()->getStyle("A2:".$lastCol."2")->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->getStyle("A2:".$lastCol."2")->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->getStyle("A2:".$lastCol."2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objPHPExcel->getActiveSheet()->getStyle("A2:".$lastCol."2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);     



        $objPHPExcel->getActiveSheet()->fromArray($colTitulos,NULL,'A2');   

            

        $row = 3;

        foreach ($arrayData as $key => $data) {

            $i=0;

            foreach ($data as $col => $value) {

                $col = $this->getColLetter($i);

                $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $value);

                $i++;

            }

            $row++;

        }

        

        foreach(range('A',$lastCol) as $columnID) {

        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)

            ->setAutoSize(true);

        }

        

        $objPHPExcel->setActiveSheetIndex(0);          

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



        if($this->saveFile == true){

            $objWriter->save($this->getTempFolder()."/".$this->getNombreArchivo().".xlsx");

        }else{                

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

            header("Content-Disposition: attachment;filename=".$this->getNombreArchivo().".xlsx");

            header('Cache-Control: max-age=0');

            $objWriter->save('php://output');

            unset($objWriter);

            exit;

        }

    }

    

    public function reportToPDF(){

        $reporteName = $this->getNombreReporte();

        #Widths de columnas del reporte

        $reportsWidths = array(

            'entradas'=>array(17,17,17,30,60,40,17,17,14,14,14,17),

            'salidas'=>array(17,22,13,20,13,17,60,40,17,17,14,14,17),

            'inventario_por_cliente'=>array(60,50,25,25,12,12,12),

            'inventario_por_lote'=>array(29,40,17,17,17,12,12,12,40),

            'inventario_por_locacion'=>array(25,40,20,20,12,12,12,50)

        );

        

        #Aligns de columnas del reporte

        $reportsAligns = array(

            'entradas'=>array('C','C','C','C','C','C','C','C','R','R','R','C'),

            'salidas'=>array('C','C','C','C','C','C','C','C','C','C','R','R','R'),

            'inventario_por_cliente'=>array('C','C','C','C','C','R','R'),

            'inventario_por_lote'=>array('C','C','C','C','C','R','R','C','C',),

            'inventario_por_locacion'=>array('C','C','C','C','C','R','R','C')

        );

        

        #Columnas que no se imprimen en reporte pdf

        $colNoPrint = array(

            'entradas'=>array('Load status','Product status'),

            'salidas'=>array(),

            'inventario_por_cliente'=>array(),

            'inventario_por_lote'=>array(),

            'inventario_por_locacion'=>array()

        );

        

        #Orienteacion de la pagina dependiendo del reporte.

        $orientationPage = array(

            'entradas'=>'L',

            'salidas'=>'L',

            'inventario_por_cliente'=>'P',

            'inventario_por_lote'=>'P',

            'inventario_por_locacion'=>'P',

        );

        

        #Set X dependiendo del reporte

        $setX = array(

            'entradas'=>'170',

            'salidas'=>'170',

            'inventario_por_cliente'=>'100',

            'inventario_por_lote'=>'100',

            'inventario_por_locacion'=>'100'

        );

        

        $pdf = new PDF();

        $pdf->AddPage($orientationPage[$reporteName]);

        

        $pdf->SetFillColor(200,200,200);

        $pdf->Image(ROOT."/public/img/logo.png",10,10,40,0);

        

        /*Para titulo de reporte*/

        $pdf->SetFont('Arial','','10');

        $pdf->SetWidths(array(100));

        $pdf->SetAligns(array('C'));

        $pdf->SetX($setX[$reporteName]);

        $pdf->Row(array($this->headerExcelReport));              

        

        $arrayData = $this->reporte;  

        $arrayData = $arrayData['data'];

       

        /* GET COL TITLES */

        $colTitulosTemp = $arrayData[0];

        $colNoPrint = $colNoPrint[$reporteName];

        foreach($colTitulosTemp as $titulo => $value){

            if(!in_array($titulo,$colNoPrint )){

                $colTitulos[$titulo] = $titulo;

            }            

        }       



        $pdf->SetFont('Arial','','6');

        #Agregar titulos de columnas en primera posicion del array.

        array_unshift($arrayData, $colTitulos);

        $pdf->Ln(10);

        #Setear width de columnas de la tabla

        $pdf->SetWidths($reportsWidths[$reporteName]);

        $pdf->SetAligns($reportsAligns[$reporteName]);

        #Imprimir tabla con informacion

        $pdf->printTable($arrayData,array('width'=>270,'align'=>'C','padding'=>5),$colNoPrint);

        $pdf->Output();        

    }

    

    public function saveFile(){

        $this->setTempFolder();

        $this->saveFile = true;

    }

    

    private function getColLetter($i){

        $colName = array(

            "A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",

            "AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ",

            "BA","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP","BQ","BR","BS","BT","BU","BV","BW","BX","BY","BZ",

            "CA","CB","CC","CD","CE","CF","CG","CH","CI","CJ","CK","CL","CM","CN","CO","CP","CQ","CR","CS","CT","CU","CV","CW","CX","CY","CZ",

            "DA","DB","DC","DD","DE","DF","DG","DH","DI","DJ","DK","DL","DM","DN","DO","DP","DQ","DR","DS","DT","DU","DV","DW","DX","DY","DZ",

            "EA","EB","EC","ED","EE","EF","EG","EH","EI","EJ","EK","EL","EM","EN","EO","EP","EQ","ER","ES","ET","EU","EV","EW","EX","EY","EZ",

            "FA","FB","FC","FD","FE","FF","FG","FH","FI","FJ","FK","FL","FM","FN","FO","FP","FQ","FR","FS","FT","FU","FV","FW","FX","FY","FZ",

            "GA","GB","GC","GD","GE","GF","GG","GH","GI","GJ","GK","GL","GM","GN","GO","GP","GQ","GR","GS","GT","GU","GV","GW","GX","GY","GZ",

            "HA","HB","HC","HD","HE","HF","HG","HH","HI","HJ","HK","HL","HM","HN","HO","HP","HQ","HR","HS","HT","HU","HV","HW","HX","HY","HZ",

            "IA","IB","IC","ID","IE","IF","IG","IH","II","IJ","IK","IL","IM","IN","IO","IP","IQ","IR","IS","IT","IU","IV","IW","IX","IY","IZ",

            "JA","JB","JC","JD","JE","JF","JG","JH","JI","JJ","JK","JL","JM","JN","JO","JP","JQ","JR","JS","JT","JU","JV","JW","JX","JY","JZ",

            "KA","KB","KC","KD","KE","KF","KG","KH","KI","KJ","KK","KL","KM","KN","KO","KP","KQ","KR","KS","KT","KU","KV","KW","KX","KY","KZ",

            "LA","LB","LC","LD","LE","LF","LG","LH","LI","LJ","LK","LL","LM","LN","LO","LP","LQ","LR","LS","LT","LU","LV","LW","LX","LY","LZ",

            "MA","MB","MC","MD","ME","MF","MG","MH","MI","MJ","MK","ML","MM","MN","MO","MP","MQ","MR","MS","MT","MU","MV","MW","MX","MY","MZ",

            "NA","NB","NC","ND","NE","NF","NG","NH","NI","NJ","NK","NL","NM","NN","NO","NP","NQ","NR","NS","NT","NU","NV","NW","NX","NY","NZ",

            "OA","OB","OC","OD","OE","OF","OG","OH","OI","OJ","OK","OL","OM","ON","OO","OP","OQ","OR","OS","OT","OU","OV","OW","OX","OY","OZ",

            "PA","PB","PC","PD","PE","PF","PG","PH","PI","PJ","PK","PL","PM","PN","PO","PP","PQ","PR","PS","PT","PU","PV","PW","PX","PY","PZ");

        

        return $colName[$i];

    }

    

    private function setTempFolder(){

        $login = new Login();

        $this->tempFolder = PATH_TEMP_DOCS.$login->getId();

        if(!is_dir($this->tempFolder)){

            mkdir($this->tempFolder,0777,true);

        }

    }

    

    private function getTempFolder(){

        return $this->tempFolder;

    }

    

    public function getFileSaved(){

        return $this->getTempFolder()."/".$this->getNombreArchivo().'.xlsx';

    }

}