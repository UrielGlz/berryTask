<?php  
class PurchasePDF {
    private $pathfFileCreated = null; 
    public function __construct($idPurchase,$createFile = null) {
        $storeIn = new PurchaseRepository();
        $storeIn->setOptions($storeIn->getById($idPurchase));
        $storeIn->setId($idPurchase);
        
        $empresa = new CompanyRepository();
        $empresa->setOptions($empresa->getById(1));      
        
        $sucursal = new StoreRepository();     
        $sucursal->setOptions($sucursal->getById($storeIn->getStoreId()));
        
        //Modifique metodo AddPage para qe ricibiera un tercer parametro, dicho parametro contiene el texto para a marca de agua.
        // para crear la marca de agua agregue las funciones Header y RotatedText en pdf.php 
        $pdf = new PDF();
        if($storeIn->getStatus() == '3'){
            $pdf->AddPage('','','Cancelada');
        }else{
            $pdf->AddPage('','');
        }

        //$pdf->Image(ROOT."/public/img/logo.png",10,10,80,0);
        //$pdf->Image(ROOT."/public/img/logo.png",170,275,30,0);
        
        $pdf->SetFont('Arial','','12');
        $pdf->SetTextColor(0); 
        $pdf->Cell(190,7, $empresa->getName() , '0', 1, 'C');       
        $pdf->Cell(190,7, "COMPRA #: ".$idPurchase, '0', 1, 'C');     
        $pdf->Cell(190,7, "SUCURSAL: ".$sucursal->getName(), '0', 1, 'C');     
       
        $pdf->Ln(5);
        $pdf->SetFont('Arial','B','8');         
        $pdf->cell(95,6,'INFORMACION DE SALIDA:','0',0,'L'); 
        $pdf->cell(95,6,'INFORMACION DE PAGO:','0',1,'L'); 
        
        $pdf->SetFont('Arial','','8');         
        $pdf->Cell(20, 4, "Fecha:", '0', 0, 'R');
        $pdf->Cell(75, 4, $storeIn->getFormatedDate(), '0', 0, 'L');
        $pdf->Cell(20, 4, "Forma de pago:", '0', 0, 'R');
        $pdf->Cell(75, 4, $storeIn->getMethodPaymentName(), '0', 1, 'L');
        
        $pdf->Cell(20, 4, "Proveedor:", '0', 0, 'R');
        $pdf->Cell(75, 4, $storeIn->getVendorName(), '0', 0, 'L');
        $pdf->Cell(20, 4, "Dias de credito:", '0', 0, 'R');
        $pdf->Cell(75, 4, $storeIn->getCreditDays(), '0', 1, 'L');
        
        $pdf->Cell(20, 4, "Factura #:", '0', 0, 'R');
        $pdf->Cell(75, 4, $storeIn->getReference(), '0', 0, 'L');
        $pdf->Cell(20, 4, "Fecha de pago:", '0', 0, 'R');
        $pdf->Cell(75, 4, $storeIn->getFormatedDueDate(), '0', 1, 'L');
        
        $pdf->Cell(20, 4, "Lote #:", '0', 0, 'R');
        $pdf->Cell(75, 4, $storeIn->getLot(), '0', 1, 'L');
        $pdf->Cell(20, 4, "Creado por:", '0', 0, 'R');
        $pdf->Cell(50, 4, $storeIn->getUserName(), '0', 1, 'L');
        $pdf->Cell(20, 4, "Status:", '0', 0, 'R');
        $pdf->Cell(50, 4, $storeIn->getStatusName(), '0', 1, 'L');
       
        $pdf->Ln(5);        
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(15, 6, "Codigo", 'B', 0, 'C');
        $pdf->Cell(45, 6, "Descripcion", 'B', 0, 'L');
        $pdf->Cell(15, 6, "Cantidad", 'B', 0, 'R');
        $pdf->Cell(20, 6, "Costo", 'B', 0, 'R');  
        $pdf->Cell(20, 6, "Desc.", 'B', 0, 'R'); 
        $pdf->Cell(20, 6, "Desc.Gral", 'B', 0, 'R');
        $pdf->Cell(20, 6, "Subtotal", 'B', 0, 'R'); 
        $pdf->Cell(20, 6, "Impuestos", 'B', 0, 'R'); 
        $pdf->Cell(20, 6, "Total", 'B', 1, 'R'); 
        
        
        $pdf->SetFont('Arial','','8');
        $detalles = $storeIn->getPurchaseDetailsSaved($idPurchase);
        $cantidadItems = 0;
        $total_importe = 0;
        $total_descuentos = 0;
        $total_subtotal = 0;
        $total_impuestos = 0;
        $total_impuestos_string = '0.00';
        $taxes_applied = array();
        $total = 0;
       
        foreach($detalles as $detalle){      
            $cantidadItems += $detalle['quantity'];
            $total_importe += $detalle['cost'] * $detalle['quantity'];
            $total_descuentos += $detalle['discount_amount'] + $detalle['discount_general_amount'];
            $total_subtotal += $detalle['amount'];
            $total_impuestos + $detalle['taxes_amount'];
            $total += $detalle['total'];
            
            if(isset($taxes_applied[$detalle['taxName'].' '.$detalle['taxes_rate']], $taxes_applied)){
                $taxes_applied[$detalle['taxName'].' '.$detalle['taxes_rate']] += $detalle['taxes_amount'];
            }else{
                $taxes_applied[$detalle['taxName'].' '.$detalle['taxes_rate']] = $detalle['taxes_amount'];
            }            
            
            $pdf->Cell(15, 5, htmlentities($detalle['code']), 'B', 0, 'C');
            $pdf->Cell(45, 5, utf8_decode($detalle['description']), 'B', 0, 'L');
            $pdf->Cell(15, 5, number_format($detalle['quantity'],2), 'B', 0, 'R');
            $pdf->Cell(20, 5, number_format($detalle['cost'],2), 'B', 0, 'R');
            $pdf->Cell(20, 5, number_format($detalle['discount'],2), 'B', 0, 'R');
            $pdf->Cell(20, 5, $detalle['discount_general'].'% '.number_format($detalle['discount_general_amount'],2), 'B', 0, 'R');
            $pdf->Cell(20, 5, number_format($detalle['amount'],2), 'B', 0, 'R');
            $pdf->Cell(20, 5, $detalle['taxName'].' '.$detalle['taxes_rate'].'% '.number_format($detalle['taxes_amount'],2), 'B', 0, 'R');
            $pdf->Cell(20, 5, number_format($detalle['total'],2), 'B', 1, 'R');
        }      
        
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(175, 5, 'Importe', '0', 0, 'R');
        $pdf->SetFont('Arial','','8');
        $pdf->Cell(20, 5, number_format($total_importe,2), '0', 1, 'R');
        
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(175, 5, 'Descuentos', '0', 0, 'R');
        $pdf->SetFont('Arial','','8');
        $pdf->Cell(20, 5, number_format($total_descuentos,2), '0', 1, 'R');
        //$pdf->Cell(20, 5, number_format($storeIn->getCompraDescuento(),2), '0', 1, 'R');
        
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(175, 5, 'Subtotal', '0', 0, 'R');
        $pdf->SetFont('Arial','','8');
        $pdf->Cell(20, 5, number_format($total_subtotal,2), '0', 1, 'R');       
        
        if(count($taxes_applied)>0){
            $total_impuestos_string = '';
            foreach($taxes_applied as $tax => $amount){
                $pdf->SetFont('Arial','B','8');
                $pdf->Cell(175, 5, $tax.'%', '0', 0, 'R');
                $pdf->SetFont('Arial','','8');
                $pdf->Cell(20, 5, number_format($amount,2), '0', 1, 'R');
            }
        }  
        
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(175, 5, 'Total', '0', 0, 'R');
        $pdf->SetFont('Arial','','8');
        $pdf->Cell(20, 5, number_format($storeIn->getTotal(),2), '0', 1, 'R');
        
        $pdf->ln(5);
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(85, 7, "Comentarios", 'B', 1, 'L');        
        $pdf->SetFont('Arial','','8');
        //$pdf-Cell(85, 25,  $comments1, '0', 1, 'L');  
        $pdf->MultiCell(120, 4,  utf8_decode($storeIn->getComments()));   
        
        $pdf->Ln(20);
        $pdf->Cell(20, 7, "", '', 0, 'C');    
        $pdf->Cell(50, 7, $storeIn->getRequestedBy(), 'T', 0, 'C'); 
        $pdf->Cell(50, 7, "", '', 0, 'C');    
       $pdf->Cell(50, 7, $storeIn->getApprovedBy(), 'T', 1, 'C');       
        
        $pdf->Cell(20, 3, "", '', 0, 'C');    
        $pdf->Cell(50, 3, 'Requerido por', '', 0, 'C'); 
        $pdf->Cell(50, 3, "", '', 0, 'C');    
        $pdf->Cell(50, 3, 'Autorizado por', '', 1, 'C'); 

        $pdf->Output();

        if($createFile){
            if(!is_dir(PATH_TEMP_DOCS."purchases/")){
                mkdir(PATH_TEMP_DOCS."puchases/",0777,true);
            }
            $pdf->Output(PATH_TEMP_DOCS."purchases/PURCHASE-".$idPurchase.".pdf","F");
            $this->pathfFileCreated = PATH_TEMP_DOCS."purchases/PURCHASE-".$idPurchase.".pdf";
        }else{
            $pdf->Output();
        }
    }
    
    public function getPathFileCreated(){
        return $this->pathfFileCreated;
    }
}