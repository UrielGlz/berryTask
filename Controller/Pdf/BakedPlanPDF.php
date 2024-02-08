<?php  
class BakedPlanPDF {
    private $pathfFileCreated = null; 
    public function __construct($arrayRequisitions = null,$arraySpecialRequisitions = null ,$createFile = null) {
        $pdf = new PDF();
        $pdf->AddPage('','');
        
        $detalles = array();
        $listRequisitions = null;
        $specialDetalles = array();
        $listSpecialRequisitions = null;
        $globalDetalles = null;
        
         if($arrayRequisitions){
            $requisition = new RequisitionEntity();
            $idRequisitions = implode(',', $arrayRequisitions);
            $detalles = $requisition->getBakedPlan($idRequisitions);
            $listRequisitions = $requisition->getListRequisitionsByIds($idRequisitions);
            
            if($detalles){ 
                $detallesTemp = null;
                foreach($detalles as $detalle){ 
                    $detallesTemp[$detalle['id_slice']] = $detalle;
                    $globalDetalles[$detalle['id_slice']] = $detalle;
                }
                $detalles = $detallesTemp;
            }else{
                $detalles = array();
            }            
        }
        
        if($arraySpecialRequisitions){
            $specialRequisition = new SpecialRequisitionEntity();
            $idRequisitions = implode(',', $arraySpecialRequisitions);
            $specialDetalles = $specialRequisition->getBakedPlan($idRequisitions);
            $listSpecialRequisitions = $specialRequisition->getListRequisitionsByIds($idRequisitions);
            
            if($specialDetalles){
                $specialDetallesTemp = null;
                foreach($specialDetalles as $specialDetalle){ 
                    $specialDetallesTemp[$specialDetalle['id_slice']] = $specialDetalle;
                     $globalDetalles[$specialDetalle['id_slice']] = $specialDetalle;
                }
                $specialDetalles = $specialDetallesTemp;
            }else{
                $specialDetalles = array();
            }
        }
        
        //$output = array_merge(array_diff_key($array1, $array2), array_diff_key($array2, $array1));
        
        $empresa = new CompanyRepository();
        $empresa->setOptions($empresa->getById(1));         
        
        $pdf->SetFont('Arial','','14');
        $pdf->SetTextColor(255);
        $pdf->SetFillColor(51,153,255);
        $pdf->SetX(0);
        $pdf->Cell(90,10, "PLAN DE TRABAJO PARA HORNEADO ", '0', 0, 'R',true);   
        $pdf->Cell(15, 10, "", '', 0, 'C'); //<== margin left 
        $pdf->SetTextColor(0);
        $pdf->Cell(95, 10, $empresa->getName(), 'L', 1, 'R');       
        
        $pdf->SetFont('Arial','','9');
        $pdf->Cell(40, 7, 'FECHA: ', '', 0, 'R');
        $pdf->Cell(40, 7, date('d/m/Y g:i A'), '', 0, 'R');   
        $pdf->Cell(15, 10, "", '', 0, 'C'); //<== margin left         
        $direccion  ="\n".$empresa->getAddress();
        $direccion .= "\n".$empresa->getCity().', '.$empresa->getState().' '.$empresa->getZipCode();
        $direccion .= "\n".$empresa->getPhone();
        $pdf->Cell(95, 15, $direccion, '0', 1, 'R');         
        
        $pdf->SetFont('Arial','B','10');         
        $pdf->cell(140,7,'RESUMEN PARA HORNEADO:','B',1,'L'); 
        
        $pdf->SetFont('Arial','B','8');                
        $pdf->Cell(58, 7, "Pan", 'B', 0, 'L');
        $pdf->Cell(37, 7, utf8_decode("Tamaño"), 'B', 0, 'L');
        $pdf->Cell(15, 7, "Linea", 'B', 0, 'R');
        $pdf->Cell(15, 7, "Especial", 'B', 0, 'R');
        $pdf->Cell(15, 7, "Total", 'B', 1, 'R');
       
        $pdf->SetFont('Arial','','8');
        $grandTotal = 0;
        
        if($globalDetalles){            //var_dump($globalDetalles);
            foreach($globalDetalles as $key => $detalle){
                //$grandTotal += $detalle['quantity'];
                
                $quantity = 0;
                $specialQuantity = 0;
                if(key_exists($key, $detalles)){$quantity = $detalles[$key]['quantity'];}
                if(key_exists($key, $specialDetalles)){$specialQuantity = $specialDetalles[$key]['quantity'];}

                $pdf->Cell(58, 5, utf8_decode($detalle['flavor']), 'B', 0, 'L');
                $pdf->Cell(37, 5, utf8_decode($detalle['sizeName']), 'B', 0, 'L');                
                $pdf->Cell(15, 5,  number_format($quantity,2), 'B', 0, 'R');
                $pdf->Cell(15, 5,  number_format($specialQuantity,2), 'B', 0, 'R');
                $pdf->Cell(15, 5,  number_format($quantity + $specialQuantity,2), 'B', 1, 'R');
            }      
        }    
        
        $pdf->ln(10);
        $pdf->SetFont('Arial','B','10');         
        $pdf->cell(116,7,'INFORMACION DE ORDENES:','B',1,'L'); 
        
        $pdf->SetFont('Arial','B','8');                
        $pdf->Cell(58, 7, "Orden #", 'B', 0, 'L');
        $pdf->Cell(58, 7, "Fecha de entrega", 'B', 1, 'L');
       
        $pdf->SetFont('Arial','','8');
        
        if($listRequisitions){
            foreach($listRequisitions as $detalleRequisition){
                $pdf->Cell(58, 5, $detalleRequisition['req_number'], 'B', 0, 'L');
                $pdf->Cell(58, 5, $detalleRequisition['date'], 'B', 1, 'L');      
            }      
        }    
        
        if($listSpecialRequisitions){
            foreach($listSpecialRequisitions as $detalle){
                $pdf->Cell(58, 5, $detalle['req_number'], 'B', 0, 'L');
                $pdf->Cell(58, 5, $detalle['delivery_date'], 'B', 1, 'L');      
            }      
        }    
        
        if($createFile){
            if(!is_dir(PATH_TEMP_DOCS."requisitions/")){
                mkdir(PATH_TEMP_DOCS."requisitions/",0777,true);
            }
            $pdf->Output(PATH_TEMP_DOCS."requisitions/REQ-".$idRequisition.".pdf","F");
            $this->pathfFileCreated = PATH_TEMP_DOCS."requisitions/REQ-".$idRequisition.".pdf";
        }else{
            $pdf->Output();
        }
    }
    
    public function getPathFileCreated(){
        return $this->pathfFileCreated;
    }
}