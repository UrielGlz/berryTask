<?php  
class SpecialOrderPDF {
    private $pathfFileCreated = null; 
    public function __construct($idRequisition,$createFile = null) {
        $requisition = new SpecialOrderRepository();
        $requisition->setOptions($requisition->getById($idRequisition));
        $requisition->setId($idRequisition);
        
        $empresa = new CompanyRepository();
        $empresa->setOptions($empresa->getById(1));        
        
        //Modifique metodo AddPage para qe ricibiera un tercer parametro, dicho parametro contiene el texto para a marca de agua.
        // para crear la marca de agua agregue las funciones Header y RotatedText en pdf.php 
        $pdf = new PDF();
        if($requisition->getStatus() == '2'){
            $pdf->AddPage('','','  Cancelada');
        }else{
            $pdf->AddPage('','');
        }

        //$pdf->Image(ROOT."/public/img/logo.png",10,10,80,0);
        //$pdf->Image(ROOT."/public/img/logo.png",170,275,30,0);
        $pdf->SetTextColor(255);
        $pdf->SetTextColor(0);

        $pdf->SetFont('Arial','','16');
        
        $pdf->Cell(190, 7, $empresa->getName() , '0', 1, 'C');       
        $pdf->Cell(190,7, "PEDIDO ESPECIAL #: ".$requisition->getReqNumber(), '0', 1, 'C');     
        $pdf->Cell(190,7, "SUCURSAL: ".$requisition->getStoreName(), '0', 1, 'C');     
        
       $pdf->Ln(5);
        $pdf->SetFont('Arial','B','8');         
        $pdf->cell(95,7,'INFORMACION:','',0,'L'); 
        $pdf->cell(105,7,'DIRECCION DE ENTREGA','',1,'L'); 
        
        $pdf->SetFont('Arial','','10');         
        $pdf->Cell(20, 5, "Fecha:", '0', 0, 'L');
        $pdf->Cell(40, 5, $requisition->getFormatDate(), '0', 0, 'L');
        $pdf->Cell(35, 5, '', '0', 0);
        $pdf->Cell(30, 5, "Direccion:", '0', 0, 'L');
        $pdf->Cell(50, 5, $requisition->getAddress(), '0', 1, 'L');
        
        $pdf->Cell(20, 5, "Cliente:", '0', 0, 'L');
        $pdf->Cell(40, 5, $requisition->getCustomerName(), '0', 0, 'L');
        $pdf->Cell(35, 5, '', '0', 0);
        $pdf->Cell(30, 5, "Ciudad:", '0', 0, 'L');
        $pdf->Cell(50, 5, $requisition->getCity(), '0', 1, 'L');       
        
        $pdf->Cell(20, 5, "Entrega:", '0', 0, 'L');
        $pdf->Cell(40, 5, $requisition->getFormatDeliveryDate(), '0', 0, 'L');
        $pdf->Cell(35, 5, '', '0', 0);
        $pdf->Cell(30, 5, "Codigo postal:", '0', 0, 'L');
        $pdf->Cell(50, 5, $requisition->getZipCode(), '0', 1, 'L');        
        
        $pdf->Cell(20, 5, "Telefono:", '0', 0, 'L');
        $pdf->Cell(40, 5, $requisition->getTelefono(), '0', 0, 'L');  
        $pdf->Cell(35, 5, '', '0', 0);
        $pdf->Cell(30, 5, "Status de entrega:", '0', 0, 'L');
        $pdf->Cell(50, 5, $requisition->getDeliveryStatusName(), '0', 1, 'L');     
        
        $pdf->Cell(20, 5, "Status:", '0', 0, 'L');
        $pdf->Cell(40, 5, $requisition->getStatusName(), '0', 1, 'L');       
        
        $pdf->Cell(20, 5, "Creado por:", '0', 0, 'L');
        $pdf->Cell(40, 5, $requisition->getUserName(), '0', 1, 'L');
       
        $pdf->Ln(5);        
        $pdf->SetFont('Arial','B','8');
        
        $pdf->Cell(80, 7, "Descripcion", 'B', 0, 'L');
        $pdf->Cell(25, 7, "Total", 'B', 1, 'R'); 
       
        $pdf->SetFont('Arial','','8');
        $detalles = $requisition->getRequisitionDetailsSaved($idRequisition,true);
        $grandTotal = 0;
        
        foreach($detalles as $detalle){
            if($detalle['type']=='Line'){
                $total = $detalle['quantity'] * $detalle['price'];
                $grandTotal += $total;  
                $quantity = number_format($detalle['quantity'],2);
                $price = number_format($detalle['price'],2);
                
            }elseif($detalle['type']=='Special'){
                $total = $detalle['price'];
                $grandTotal += $total;  
                $quantity = 'X';
                $price = 'X';
            }             
            
            $pdf->Cell(80, 5, utf8_decode($detalle['description']), 'B', 0, 'L');
            $pdf->Cell(25, 5, number_format($total,2), 'B', 1, 'R');
        }      
        
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(80, 5, 'Total', '', 0, 'R');
        $pdf->SetFont('Arial','','8');
        $pdf->Cell(25, 5, number_format($grandTotal,2), 'T', 0, 'R');
        
        
        $detalles = $requisition->getRequisitionDetailsForSpecial($idRequisition);
        if($detalles){
            $pdf->Ln(5);        
            $pdf->SetFont('Arial','B','8');
            
            $pdf->Cell(160, 7, "Detalles de pedidos especiales", 'B', 1, 'L');
           
           $pdf->Cell(25, 7, "Cantidad", 'B', 0, 'C'); 
            $pdf->Cell(25, 7, "Categoria", 'B', 0, 'C');
            $pdf->Cell(25, 7, "Forma", 'B', 0, 'C');
            $pdf->Cell(60, 7, "Descripcion", 'B', 0, 'L'); 
            $pdf->Cell(25, 7, utf8_decode("Tamaño"), 'B', 1, 'C'); 
            

           $pdf->SetFont('Arial','','8');
            foreach($detalles as $detalle){
                $prefix = '';
                if($detalle['type']=='Special'){
                    $prefix = "s_";
                    $pdf->Cell(25, 5, $detalle['quantity'], 'B', 0, 'C');
                    $pdf->Cell(25, 5, $detalle[$prefix.'category'], 'B', 0, 'C');
                    $pdf->Cell(25, 5, $detalle[$prefix.'shape'], 'B', 0, 'C');
                    $pdf->Cell(60, 5, $detalle[$prefix.'description'], 'B', 0, 'L'); 
                    $pdf->Cell(25, 5, $detalle[$prefix.'size'], 'B', 1, 'C');               
                }          
            }
        }
        
        $pdf->Ln(2);
        $msgArray = explode("<br />",nl2br($requisition->getComments()));
        $comments = "";
        //echo "<pre>";var_dump($msgArray);echo "</pre>";exit;
        foreach($msgArray as $line){
            $comments .= $line;
        }
        
        $msgArray = explode("<br />",nl2br($requisition->getComments1()));
        $comments1 = "";
        foreach($msgArray as $line){
            $comments1 .= $line;
        }
        
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(120, 7, "Notas de pastel", 'B', 1, 'L');
        $pdf->SetFont('Arial','','8');
        $pdf->MultiCell(120, 5,  $comments, '0', 1, 'L');
        
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(120, 7, "Notas de decorado", 'B', 1, 'L');        
        $pdf->SetFont('Arial','','8');
        //$pdf->Cell(85, 6,  $comments1, '0', 1, 'L');  
        $pdf->MultiCell(120, 5,  $comments1);      
        
        /*        
        //Crear codigo
        QRcode::png('?re='.$empresa->getRFC().'&rr='.$requisition->getRFC().'&tt='.$requisition->getTotalParaCodigoQR().'&id='.$requisition->getUUID(),PATH_TEMP_DOCS."qrCode".$requisition->getSerie()."-".$requisition->getFolio().".png",'Q',3); // creates file
        $pdf->Image(PATH_TEMP_DOCS."qrCode".$requisition->getSerie()."-".$requisition->getFolio().".png",10,165,30,0);
        
        $pdf->ln(12);
       
        */
        if($createFile){
            if(!is_dir(PATH_TEMP_DOCS."requisitions/")){
                mkdir(PATH_TEMP_DOCS."requisitions/",0777,true);
            }
            $pdf->Output(PATH_TEMP_DOCS."requisitions/SpecialOrder-".$requisition->getReqNumber().".pdf","F");
            $this->pathfFileCreated = PATH_TEMP_DOCS."requisitions/SpecialOrder-".$requisition->getReqNumber().".pdf";
        }else{
            $pdf->Output();
        }
    }
    
    public function getPathFileCreated(){
        return $this->pathfFileCreated;
    }
}