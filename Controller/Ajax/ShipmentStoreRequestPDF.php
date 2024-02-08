<?php  
class ShipmentStoreRequestPDF {
    private $pathfFileCreated = null; 
    public function __construct($idShipment,$createFile = null) {
        $shipment = new ShipmentStoreRequestRepository();
        $shipment->setOptions($shipment->getById($idShipment));
        $shipment->setId($idShipment);
        
        $empresa = new CompanyRepository();
        $empresa->setOptions($empresa->getById(1));        
        
        $sucursal = new StoreRepository();     
        $sucursal->setOptions($sucursal->getById($shipment->getToStore()));
        
        //Modifique metodo AddPage para qe ricibiera un tercer parametro, dicho parametro contiene el texto para a marca de agua.
        // para crear la marca de agua agregue las funciones Header y RotatedText en pdf.php 
        $pdf = new PDF();
        if($shipment->getStatus() == '4'){
            $pdf->AddPage('','',0,'Cancelado');
        }else{
            $pdf->AddPage('','');
        }

        //$pdf->Image(ROOT."/public/img/logo.png",10,10,80,0);
        //$pdf->Image(ROOT."/public/img/logo.png",170,275,30,0);
        
        $pdf->SetFont('Arial','','12');
        $pdf->SetTextColor(0); 
        $pdf->Cell(190,7, $empresa->getName() , '0', 1, 'C');       
        $pdf->Cell(190,7, "ENVIO DE PEDIDO DE SUCURSAL #: ".$idShipment, '0', 1, 'C');     
        $pdf->Cell(190,7, "PARA SUCURSAL: ".$sucursal->getName(), '0', 1, 'C');     
        $pdf->Cell(190,7, "PEDIDO #: ".$shipment->getIdStoreRequest(), '0', 1, 'C');     
        
        $barCode = new BarCode();
        $barCode->setBarCode(array(
            'filepath'=>PATH_TEMP_DOCS.'/barcodes/'.$shipment->getNumShipment().'.png',
            'text'=>$shipment->getNumShipment(),
            'print'=>false));       
        $pdf->Image(PATH_TEMP_DOCS.'barcodes/'.$shipment->getNumShipment().'.png',158,15,40,0);
       
        $pdf->SetFont('Arial','','8');                
        $pdf->Cell(150, 6, '', '', 0, 'R'); 
        $pdf->Cell(36, 6, "CODIGO DE ENVIO ", '0',1, 'C');  
        
        $pdf->Ln(5);
        $pdf->SetFont('Arial','B','8');         
        $pdf->cell(40,6,'INFORMACION DE ENVIO:','0',0,'L'); 
        $pdf->Cell(70, 6, '', '', 0, 'l'); 
        $pdf->cell(40,6,'ENVIO PARA:','',1,'L');      
        
        $pdf->SetFont('Arial','','8');
        $pdf->Cell(40, 4, "STATUS: ", '', 0, 'L');
        $pdf->Cell(40, 4, $shipment->getStatusName(), '', 0, 'L');              
        $pdf->Cell(30, 4, '', '', 0, 'l'); 
        $pdf->Cell(20, 4, "SUCURSAL: ", '', 0, 'L');
        $pdf->Cell(40, 4, $sucursal->getName(), '', 1, 'L');      

        $pdf->Cell(40, 4, 'FECHA Y HORA DE ENVIO: ', '', 0, 'L');
        $pdf->Cell(40, 4, $shipment->getDateFormated(), '', 0, 'L');  
        $pdf->Cell(30, 4, '', '', 0, 'l'); 
        $pdf->Cell(20, 4, "DIRECCION: ", '', 0, 'L');
        $pdf->Cell(40, 4, $sucursal->getAddress(), '', 1, 'L');          
        
        $pdf->Cell(40, 4, 'FECHA Y HORA DE RECIBO: ', '', 0, 'L');
        $pdf->Cell(40, 4, $shipment->getReceivingDateFormated(), '', 0, 'L');
        $pdf->Cell(30, 4, '', '', 0, 'l'); 
        $pdf->Cell(20, 4, "", '', 0, 'L');
        $pdf->Cell(40, 4, $sucursal->getCity().", ".$sucursal->getState()." ".$sucursal->getZipCode(), '', 1, 'L');    
        
        $pdf->Cell(40, 4, '', '', 0, 'L');
        $pdf->Cell(40, 4, '', '', 0, 'L');  
        $pdf->Cell(30, 4, '', '', 0, 'l'); 
        $pdf->Cell(20, 4, "TELEFONO: ", '', 0, 'L');
        $pdf->Cell(40, 4, $sucursal->getPhone(), '', 1, 'L');          
       
        $pdf->Ln(5);        
        $pdf->SetFont('Arial','B','8');
        
        $pdf->Cell(80, 7, "Descripcion", 'B', 0, 'L');
        $pdf->Cell(30, 7, utf8_decode("Tamaño"), 'B', 0, 'L');
        $pdf->Cell(25, 7, "Enviado", 'B', 0, 'R'); 
        $pdf->Cell(25, 7, "Recibido", 'B', 0, 'R'); 
        $pdf->Cell(25, 7, "Diferencia", 'B', 1, 'R'); 
               
        $pdf->SetFont('Arial','','8');
        $detalles = $shipment->getShipmentDetailsSaved($idShipment,true);
        $totalEnviado = 0;
        $totalRecibido = 0;
        
        foreach($detalles as $detalle){
            $totalEnviado += $detalle['quantity'];
            $totalRecibido += $detalle['received'];
            
            $pdf->Cell(80, 5, utf8_decode($detalle['description']), 'B', 0, 'L');
            $pdf->Cell(30, 5, utf8_decode($detalle['size']), 'B', 0, 'L');
            $pdf->Cell(25, 5, number_format($detalle['quantity']), 'B', 0, 'R');
            $pdf->Cell(25, 5, number_format($detalle['received']), 'B', 0, 'R');
            $signo = '';
            if($detalle['received'] > $detalle['quantity']){$signo = '+';}
            elseif($detalle['received'] < $detalle['quantity']){$signo = '-';}
            $pdf->Cell(25, 5, $signo.number_format($detalle['received'] - $detalle['quantity']), 'B', 1, 'R');
        }      
        
        $pdf->Cell(110, 7, "Total", 'B', 0, 'R');
        $pdf->Cell(25, 7, $totalEnviado, 'B', 0, 'R'); 
        $pdf->Cell(25, 7, $totalRecibido, 'B', 0, 'R'); 
        $signo = '';
        if($totalRecibido > $totalEnviado){$signo = '+';}
        elseif($totalRecibido < $totalEnviado){$signo = '-';}
        $pdf->Cell(25, 7, $signo.number_format($totalRecibido - $totalEnviado), 'B', 1, 'R'); 
        
                      
        $pdf->Ln(2);
        $msgArray = explode("<br />",nl2br($shipment->getComments()));
        $comments = "";
        foreach($msgArray as $line){
            $comments .= $line."\n";
        }
        
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(85, 7, "Notas envio", 'B', 1, 'L');
        $pdf->SetFont('Arial','','8');
        $pdf->Cell(85, 25,  utf8_decode($comments), '0', 1, 'L');
        
        $msgArray = explode("<br />",nl2br($shipment->getReceivingComments()));
        $commentsReceiving = "";
        foreach($msgArray as $line){
            $commentsReceiving .= $line."\n";
        }
        
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(85, 7, "Notas recibo", 'B', 1, 'L');        
        $pdf->SetFont('Arial','','8');
        $pdf->Cell(85, 25,  utf8_decode($commentsReceiving), '0', 1, 'L');    
        
        /*        
        //Crear codigo
        QRcode::png('?re='.$empresa->getRFC().'&rr='.$shipment->getRFC().'&tt='.$shipment->getTotalParaCodigoQR().'&id='.$shipment->getUUID(),PATH_TEMP_DOCS."qrCode".$shipment->getSerie()."-".$shipment->getFolio().".png",'Q',3); // creates file
        $pdf->Image(PATH_TEMP_DOCS."qrCode".$shipment->getSerie()."-".$shipment->getFolio().".png",10,165,30,0);
        
        $pdf->ln(12);
       
        */
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