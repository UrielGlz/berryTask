<?php  
class StoreRequestPDF {
    private $pathfFileCreated = null; 
    public function __construct($idShipment,$createFile = null) {
        $shipment = new StoreRequestRepository();
        $shipment->setOptions($shipment->getById($idShipment));
        $shipment->setId($idShipment);
        
        $empresa = new CompanyRepository();
        $empresa->setOptions($empresa->getById(1));        
        
        $sucursal = new StoreRepository();     
        $sucursal->setOptions($sucursal->getById($shipment->getStoreId()));
        
        //Modifique metodo AddPage para qe ricibiera un tercer parametro, dicho parametro contiene el texto para a marca de agua.
        // para crear la marca de agua agregue las funciones Header y RotatedText en pdf.php 
        $pdf = new PDF();
        if($shipment->getStatus() == '2'){
            $pdf->AddPage('','','  Cancelada');
        }else{
            $pdf->AddPage('','');
        }
        
        $pdf->SetTextColor(0); 
        $y = $pdf->GetY();
        $pdf->SetFont('Arial','B','12');
        $pdf->Cell(95,7, strtoupper($empresa->getName()), '0', 1, 'C');        
        $pdf->SetFont('Arial','','10');
        $pdf->Cell(95,5, "FECHA DE PEDIDO: ".$shipment->getFormatedDate(), '0', 1, 'C');    
        $pdf->SetFont('Arial','B','12');
        $pdf->Cell(95,5, "FECHA DE ENTREGA: ".$shipment->getFormatedDeliveryDate(), '0', 1, 'C');             
        
        $pdf->SetY($y);
        $pdf->SetFont('Arial','','10');
        $pdf->Cell(95,5, "", '0', 0, 'C'); /*Margin left*/
        $pdf->Cell(95,5, "PEDIDO", '0', 1, 'R'); 
        $pdf->Cell(95,5, "", '0', 0, 'C'); /*Margin left*/
        $pdf->SetFont('Arial','B','16');
        $pdf->Cell(95,5, '#'.$idShipment, '0', 1, 'R'); 
        
        $pdf->Ln(2);
        $pdf->SetFont('Arial','','10');
        $pdf->Cell(95,5, "", '0', 0, 'C'); /*Margin left*/
        $pdf->Cell(95,5, "SUCURSAL", '0', 1, 'R'); 
        $pdf->Cell(95,5, "", '0', 0, 'C'); /*Margin left*/
        $pdf->SetFont('Arial','B','16');
        $pdf->Cell(95,5, strtoupper($sucursal->getName()), '0', 1, 'R');       
        
        /*
        $pdf->SetTextColor(0);       
        $pdf->SetFont('Arial','B','8');         
        $pdf->cell(105,4,'INFORMACION DE SUCURAL:','',1,'L');      
        
        $toStore = "\nSucursal: ".$sucursal->getName();
        $toStore .= "\n".$sucursal->getAddress();
        $toStore .= "\n".$sucursal->getCity().", ".$sucursal->getState()." ".$sucursal->getZipCode();
        $toStore .= "\n"."Telefono: ".$sucursal->getPhone();
        
        $pdf->SetFont('Arial','','8'); 
        $pdf->cell(105,12,$toStore,'0',1,'L'); */      
       
        $pdf->Ln(10);        
        $pdf->SetFont('Arial','B','8');
        
        $y = $pdf->GetY();
        $pdf->Cell(5, 7, "#", 'B', 0, 'L');
        $pdf->Cell(58, 7, "Descripcion", 'B', 0, 'L');
        $pdf->Cell(15, 7, "Pedido", 'B', 1, 'R'); 
               
        $pdf->SetFont('Arial','','8');
        $detalles = $shipment->getStoreRequestDetallesSavedPDF($idShipment,true);
        $totalCharolas = 0;
        $existeSegundaColumna = null;
        $i = 1;
        $j = 1;  
        $k = 1;
        $yForComments = $pdf->GetY();
        
        foreach($detalles as $detalle){      
            $totalCharolas += round($detalle['quantity'],2);
            /*TITULOS PARA SEGUNDA COLUMNA*/
            if($j > 30){
                $j = 1;                
                $yForComments = $pdf->GetY();
                $pdf->SetY($y);
                $pdf->SetFont('Arial','B','8');
                $pdf->Cell(98, 7, "", '', 0, 'L');/*Margin left*/
                $pdf->Cell(5, 7, "#", 'B', 0, 'L');
                $pdf->Cell(58, 7, "Descripcion", 'B', 0, 'L');
                $pdf->Cell(15, 7, "Pedido", 'B', 1, 'R');              
            }
            
            /* SEGUNDA COLUMNA */
            if($k > 30){
                $existeSegundaColumna = true;
                $pdf->SetFont('Arial','','8');
                $pdf->Cell(98, 7, "", '', 0, 'L');/*Margin left*/
                $pdf->Cell(5, 5, $i, 'B', 0, 'C');
                $pdf->Cell(58, 5, utf8_decode($detalle['description']), 'B', 0, 'L');
                $pdf->Cell(15, 5, number_format($detalle['quantity']), 'B', 1, 'R');  
            
            /* PRIMER COLUMNA */
            }else{               
                $pdf->SetFont('Arial','','8');
                $pdf->Cell(5, 5, $i, 'B', 0, 'C');
                $pdf->Cell(58, 5, utf8_decode($detalle['description']), 'B', 0, 'L');
                $pdf->Cell(15, 5, number_format($detalle['quantity']), 'B', 1, 'R');
                $yForComments = $pdf->GetY();
            }            
            $i++;
            $j++;
            $k++;            
        }       
        
        /*TOTAL CHAROLAS*/
        if(is_null($existeSegundaColumna)){
            $pdf->SetFont('Arial','B','8');
            $pdf->Cell(5, 5, '', '', 0, 'C');
            $pdf->Cell(58, 5, 'TOTAL', '', 0, 'L');
            $pdf->Cell(15, 5, number_format($totalCharolas), '', 0, 'R');
        }else{
            $pdf->SetFont('Arial','B','8');            
            $pdf->Cell(98, 7, "", '', 0, 'L');/*Margin left*/
            $pdf->Cell(5, 5, '', '', 0, 'C');
            $pdf->Cell(58, 5, 'TOTAL', '', 0, 'C');
            $pdf->Cell(15, 5, number_format($totalCharolas), '', 0, 'R');
        }
                 
        $pdf->SetY($yForComments);
        $pdf->Ln(10);
        /*
        $msgArray = explode("<br />",nl2br($shipment->getComments()));
        $comments = "";
        foreach($msgArray as $line){
            $comments .= utf8_decode($line)."\n";
        }*/
        
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(93, 7, "Notas de pedido", 'B', 1, 'L');
        $pdf->Cell(93, 2, "", '0', 1, 'L');
        $pdf->SetFont('Arial','','8');
        $pdf->Multicell(93,3,  $shipment->getComments(), '0', 1, 'L');

        if($createFile){
            if(!is_dir(PATH_TEMP_DOCS."requisitions/")){
                mkdir(PATH_TEMP_DOCS."requisitions/",0777,true);
            }
            $pdf->Output(PATH_TEMP_DOCS."requisitions/REQ-".$idShipment.".pdf","F");
            $this->pathfFileCreated = PATH_TEMP_DOCS."requisitions/REQ-".$idShipment.".pdf";
        }else{
            $pdf->Output();
            unlink(ROOT.'app/resources/docs/temp/barcodes/'.$shipment->getNumShipment().'.png');
        }
    }
    
    public function getPathFileCreated(){
        return $this->pathfFileCreated;
    }
}