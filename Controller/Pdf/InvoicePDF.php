<?php  
class InvoicePDF {
    private $pathfFileCreated = null; 
    private $fileName = null; 
    private $pathfForLink = null;
    
    public function __construct($idVenta,$createFile = null) {
        $pdf = new PDF();
        $invoice = new InvoiceRepository();        
        $invoiceData = $invoice->getById($idVenta);
        $invoice->setOptions($invoiceData);
        $invoiceDetalles = $invoice->getInvoiceDetallesSaved($idVenta);
        $customerData = $invoice->getCustomerCompleteInfo();
        
        $empresa = new CompanyRepository();
        $empresaData = $empresa->getById(1);
        
        $pdf = new PDF();
        if($invoiceData['status']=='3'){
            //Modifique metodo AddPage para qe ricibiera un tercer parametro, dicho parametro contiene el texto para a marca de agua.
            // para crear la marca de agua agregue las funciones Header y RotatedText en pdf.php
            $pdf->AddPage('','','','Canceled');
        }
        else{
            $pdf->AddPage();
        }
        
        $pdf->SetFillColor(200,200,200);        
        $pdf->SetFont('Arial','B','16');
        $pdf->Cell(190,7, 'INVOICE #'.$invoice->getInvoiceNumber(), '0', 1, 'R');

        $pdf->Image(ROOT."/public/app/img/logo.jpg",10,2,30,0);
        
        /* PARA PONER IMG COMO MARCA DE AGUA
        $pdf->SetAlpha(0.5);   
        $pdf->Image(ROOT."/public/img/pdf_background.jpg",50,100,100,0);
        $pdf->SetAlpha(1);   */
        
        $pdf->Ln(2);                   
        $pdf->Cell(135, 5, "", '0', 0, 'L');
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(30, 5, "Invoice Date ", '0', 0, 'L');
        $pdf->SetFont('Arial','','8');
        $pdf->Cell(25, 5, $invoiceData['formatedDate'], '0', 1, 'R');  
        
        $y = $pdf->GetY();
        $pdf->Cell(135, 5, "", '0', 0, 'L');
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(30, 5, "Payment Terms", '', 0, 'L');
        $pdf->SetFont('Arial','','8');
        $pdf->Cell(25, 5, $invoiceData['paymentTermsName'], '0', 1, 'R');     
        
        $pdf->Cell(135, 5, "", '0', 0, 'L');
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(30, 5, "Due Date", '', 0, 'L');
        $pdf->SetFont('Arial','','8');
        $pdf->Cell(25, 5, $invoiceData['formatedDueDate'], '0', 1, 'R');            
        
        $pdf->Cell(135, 5, "", '0', 0, 'L');
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(30, 5, "Store", '', 0, 'L');
        $pdf->SetFont('Arial','','8');
        $pdf->Cell(25, 5, $invoiceData['store_name'], '0', 1, 'R');       
              
        $pdf->setY($y);
        $pdf->SetFont('Arial','B','10');
        $pdf->Cell(120, 5, $empresaData['name'], '0', 1, 'L');
        
        $pdf->SetFont('Arial','','8');        
        $direccion = '';
        if(trim($empresaData['address'])!== ''){$direccion .= $empresaData['address'];}
        if(trim($empresaData['city'])!== ''){$direccion .= "\n".$empresaData['city'];}
        if(trim($empresaData['state'])!== ''){$direccion .= ",".$empresaData['state'];}
        if(trim($empresaData['zipcode'])!== ''){$direccion .= " ".$empresaData['zipcode'];}
        if(trim($empresaData['phone'])!== ''){$direccion .= "\nPhone:".$empresaData['phone'];}        
        $pdf->Multicell(70, 4, $direccion, '0', 1, 'L');            
               
        $pdf->ln(5);
       
        $pdf->SetFont('Arial','B','8');        
        $pdf->Cell(35, 5, "BILL TO", '0', 1, 'L');
        $pdf->ln(1);
        
        $pdf->SetFont('Arial','B','10');
        $pdf->Cell(120, 5, utf8_decode($customerData['name']), '0', 1, 'L');
        
        $pdf->SetFont('Arial','','8');
        $vendorString = '';
        if(trim($customerData['address'])!== ''){$vendorString .= $customerData['address'];}
        if(trim($customerData['city'])!== ''){$vendorString .= "\n".$customerData['city'];}
        if(trim($customerData['state'])!== ''){$vendorString .= ",".$customerData['state'];}
        if(trim($customerData['zipcode'])!== ''){$vendorString .= " ".$customerData['zipcode'];}
        if(trim($customerData['phone'])!== ''){$vendorString .= "\nPhone:".$customerData['phone'];}        
        $pdf->Cell(170, 10, $vendorString, '0', 1, 'L');    

        $pdf->Ln(5);        
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(135, 6, "DESCRIPTION", 'B', 0, 'L');
        $pdf->Cell(15, 6, "QUANTITY", 'B', 0, 'R');
        $pdf->Cell(20, 6, "PRICE", 'B', 0, 'R'); 
        $pdf->Cell(20, 6, "TOTAL", 'B', 1, 'R');              
        
        $cantidadItems = 0;
        $total_importe = 0;
        $total_descuentos = 0;
        $total_subtotal = 0;
        $total_impuestos = 0;
        $taxes_applied = array();
        $total = 0;        
        
        foreach($invoiceDetalles as $detalle){ 
            $pdf->SetFont('Arial','','8');
            $cantidadItems += $detalle['quantity'];
            $importe = $detalle['price'] * $detalle['quantity'];
            $total_importe += $importe;
            $total_descuentos += $detalle['discount_amount'] + $detalle['discount_general_amount'];
            $total_subtotal += $detalle['amount'];
            $total_impuestos += $detalle['taxes_amount'];
            $total += $detalle['total'];
            
            if($detalle['type'] == 'product'){
                if(isset($taxes_applied[$detalle['taxName']], $taxes_applied)){
                    $taxes_applied[$detalle['taxName']] += $detalle['taxes_amount'];
                }else{
                    $taxes_applied[$detalle['taxName']] = $detalle['taxes_amount'];
                }   
            }            
            
            if($detalle['description_details'] != '' && $detalle['type'] == 'product'){                  
            
                $pdf->Cell(135, 4, utf8_decode($detalle['descripcion']), '0', 0, 'L');
                $pdf->Cell(15, 4, number_format($detalle['quantity'],2), '0', 0, 'R');
                $pdf->Cell(20, 4,number_format($detalle['price'],2), '0', 0, 'R');
                $pdf->Cell(20, 4, number_format($importe,2), '0', 1, 'R');
                
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                
                $pdf->SetXY($x + 3, $y); /*<=== aqui le puse 25 en lugar de 20; para dejarle margen iz  quierdo*/
                $pdf->Multicell(132, 4, htmlentities($detalle['description_details']), 'B','L');
                
                $yDesc = $pdf->GetY() - $y;
                
                $pdf->SetXY($x, $y);
                $pdf->Multicell(3, $yDesc, '', 'B','C');
                
                $pdf->SetXY($x + 135, $y);                
                $pdf->Multicell(15, $yDesc, '', 'B','C');
                
                $pdf->SetXY($x + 148, $y);                
                $pdf->Multicell(20, $yDesc, '', 'B','C');
                
                $pdf->SetXY($x + 168, $y);                
                $pdf->Multicell(22, $yDesc, '', 'B','C');

            }else{
                
                $pdf->Cell(135, 5, utf8_decode($detalle['descripcion']), 'B', 0, 'L');
                $pdf->Cell(15, 5, number_format($detalle['quantity'],2), 'B', 0, 'R');
                $pdf->Cell(20, 5, number_format($detalle['price'],2), 'B', 0, 'R');
                $pdf->Cell(20, 5, number_format($importe,2), 'B', 1, 'R');
            }            
        }          
        
        $pdf->Ln(5);   
        if($total_descuentos > 0 || $total_impuestos > 0){
            $pdf->SetFont('Arial','B','8');
            $pdf->Cell(170, 5, 'SUBTOTAL', '0', 0, 'R');
            $pdf->SetFont('Arial','','8');
            $pdf->Cell(20, 5, number_format($total_importe,2), '0', 1, 'R');
        }
       
        if($total_descuentos > 0){
            $pdf->SetFont('Arial','B','8');
            $pdf->Cell(170, 5, 'DISCOUNTS', '0', 0, 'R');
            $pdf->SetFont('Arial','','8');
            $pdf->Cell(20, 5, '-'.number_format($total_descuentos,2), '0', 1, 'R');
        }        
        
        if($total_impuestos > 0){
            foreach($taxes_applied as $taxName => $taxAmount){
                if($taxAmount > 0){
                    $pdf->SetFont('Arial','B','8');
                    $pdf->Cell(170, 5, $taxName, '0', 0, 'R');
                    $pdf->SetFont('Arial','','8');
                    $pdf->Cell(20, 5, number_format($taxAmount,2), '0', 1, 'R');
                }                
            }
        }
        
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(170, 5, 'TOTAL', '0', 0, 'R');
        $pdf->SetFont('Arial','','8');
        $pdf->Cell(20, 5, number_format($total,2), '0', 1, 'R');      
        
        if($invoice->getMessageOnInvoice() != ''){
            $pdf->Ln(1);
            $pdf->SetFont('Arial','B','8');
            $pdf->Cell(120, 5,'Notes', '', 1, 'L');

            $pdf->SetFont('Arial','','8');
            $pdf->Multicell(120,3,$invoice->getMessageOnInvoice(),'0',1);        
        }       
         
        if($createFile){
            $name = "Invoice-{$invoice->getInvoiceNumber()}.pdf";
            $pdf->Output(ROOT."/app/resources/docs/temp/pdf/invoice/$name","F");
            $this->fileName  = $name;
            $this->pathfFileCreated  = ROOT."/app/resources/docs/temp/pdf/invoice/$name";
            $this->pathfForLink = "/app/resources/docs/temp/pdf/invoice/$name";
        }else{
            $pdf->Output();
        }
    }
    
    public function getPathFileCreated(){
        return $this->pathfFileCreated;
    }
    
    public function getFileName(){
        return $this->fileName;
    }
    
    public function getPathForLink(){
        return $this->pathfForLink;
    }
}