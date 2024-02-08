<?php  
class TransferPDF {
    private $pathfFileCreated = null; 
    public function __construct($idTransfer,$createFile = null) {
        $output = new TransferRepository();
        $output->setOptions($output->getById($idTransfer));
        $output->setId($idTransfer);
        
        $empresa = new CompanyRepository();
        $empresa->setOptions($empresa->getById(1));      
        
        //Modifique metodo AddPage para qe ricibiera un tercer parametro, dicho parametro contiene el texto para a marca de agua.
        // para crear la marca de agua agregue las funciones Header y RotatedText en pdf.php 
        $pdf = new PDF();
        if($output->getStatus() == '2'){
            $pdf->AddPage('','','CanceladO');
        }else{
            $pdf->AddPage('','');
        }

        //$pdf->Image(ROOT."/public/img/logo.png",10,10,80,0);
        //$pdf->Image(ROOT."/public/img/logo.png",170,275,30,0);
        
        $pdf->SetFont('Arial','','12');
        $pdf->SetTextColor(0); 
        $pdf->Cell(190,7, $empresa->getName() , '0', 1, 'C');       
        $pdf->Cell(190,7, "TRASPASO #: ".$idTransfer, '0', 1, 'C');     
        $pdf->Cell(190,7, "SUCURSAL: ".$output->getFromStoreName(), '0', 1, 'C');     
       
        $pdf->SetFont('Arial','B','8');         
        $pdf->cell(95,6,'INFORMACION DE TRASPASO:','0',1,'L'); 
        
        $pdf->SetFont('Arial','','8');         
        $pdf->Cell(25, 4, "Fecha de traspaso:", '0', 0, 'R');
        $pdf->Cell(75, 4, $output->getFormatedDate(), '0', 1, 'L');
        
        $pdf->Cell(25, 4, "Desde:", '0', 0, 'R');
        $pdf->Cell(75, 4, $output->getFromStoreName(), '0', 1, 'L');
        
        $pdf->Cell(25, 4, "Para:", '0', 0, 'R');
        $pdf->Cell(75, 4, $output->getToStoreName(), '0', 1, 'L');
        
        $pdf->Cell(25, 4, "Requerido por:", '0', 0, 'R');
        $pdf->Cell(75, 4, $output->getRequestedBy(), '0', 1, 'L');
        
        $pdf->Cell(25, 4, "Registrado por:", '0', 0, 'R');
        $pdf->Cell(75, 4, $output->getUserName(), '0', 1, 'L');
        
        $pdf->Cell(25, 4, "Status:", '0', 0, 'R');
        $pdf->Cell(75, 4, $output->getStatusName(), '0', 1, 'L');
       
        $pdf->Ln(5);        
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(15, 6, "Codigo", 'B', 0, 'C');
        $pdf->Cell(45, 6, "Descripcion", 'B', 0, 'L');
        $pdf->Cell(30, 6, "Presentacion", 'B', 0, 'C');        
        $pdf->Cell(30, 6, "Marca", 'B', 0, 'C');
        $pdf->Cell(15, 6, "Cantidad", 'B', 0, 'R');
        $pdf->Cell(20, 6, "Locacion", 'B', 0, 'C');
        $pdf->Cell(15, 6, "Recibido", 'B', 1, 'R');
        
        $pdf->SetFont('Arial','','8');
        $detalles = $output->getTransferDetailsSaved($idTransfer);
        $cantidadItems = 0;
       
        foreach($detalles as $detalle){      
            $cantidadItems += $detalle['quantity'];       
            
            $pdf->Cell(15, 5, htmlentities($detalle['code']), 'B', 0, 'C');
            $pdf->Cell(45, 5, utf8_decode($detalle['description']), 'B', 0, 'L');
            $pdf->Cell(30, 5, utf8_decode($detalle['presentation_name']), 'B', 0, 'C');
            $pdf->Cell(30, 5, utf8_decode($detalle['brand_name']), 'B', 0, 'C');
            $pdf->Cell(15, 5, number_format($detalle['quantity'],2), 'B', 0, 'R');
            $pdf->Cell(20, 5, utf8_decode($detalle['location_name']), 'B', 0, 'C');
            $pdf->Cell(15, 5, number_format($detalle['received'],2), 'B', 1, 'R');
        }      
        
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(120, 5, 'Total items', '0', 0, 'R');
        $pdf->SetFont('Arial','','8');
        $pdf->Cell(15, 5, number_format($cantidadItems,2), '0', 1, 'R');    
        
        $pdf->ln(5);
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(85, 7, "Comentarios", 'B', 1, 'L');        
        $pdf->SetFont('Arial','','8');
        //$pdf-Cell(85, 25,  $comments1, '0', 1, 'L');  
        $pdf->MultiCell(120, 4,  utf8_decode($output->getComments()));   

        $pdf->output();

        if($createFile){
            if(!is_dir(PATH_TEMP_DOCS."ouputs/")){
                mkdir(PATH_TEMP_DOCS."puchases/",0777,true);
            }
            $pdf->Transfer(PATH_TEMP_DOCS."ouputs/PURCHASE-".$idTransfer.".pdf","F");
            $this->pathfFileCreated = PATH_TEMP_DOCS."ouputs/PURCHASE-".$idTransfer.".pdf";
        }else{
            $pdf->Output();
        }
    }
    
    public function getPathFileCreated(){
        return $this->pathfFileCreated;
    }
}