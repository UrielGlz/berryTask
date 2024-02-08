<?php  
class PhysicalInventoryPDF {
    private $pathfFileCreated = null; 
    public function __construct($idShipment,$createFile = null) {
        $shipment = new PhysicalInventoryRepository();
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
        
        $pdf->SetFont('Arial','','12');
        $pdf->SetTextColor(0); 
        $pdf->Cell(190,7, $empresa->getName() , '0', 1, 'C');       
        $pdf->Cell(190,7, "INVENTARIO FISICO #: ".$idShipment, '0', 1, 'C');     
        $pdf->Cell(190,7, "SUCURSAL: ".$sucursal->getName(), '0', 1, 'C');     
        $pdf->Cell(190,7, "FECHA DE INVENTARIO: ".$shipment->getFormatedDate(), '0', 1, 'C');     
       
        $pdf->Ln(5);        
        $detalles = $shipment->getPhysicalInventoryDetallesSavedPDF($idShipment,true);
        $i = 1;
        $j = 1;  
        $k = 1;
        $yForComments = $pdf->GetY();
        
        foreach($detalles as $areaName => $detalles){
            $y = $pdf->GetY();
            $x = $pdf->GetX();
            
            $pdf->SetFont('Arial','B','8');            
            $pdf->Cell(90, 7, $areaName, 'B', 1, 'L');            
            $pdf->SetX($x);
            $pdf->Cell(65, 7, "Producto", 'B', 0, 'L');
            $pdf->Cell(25, 7, "Stock", 'B', 1, 'R');    

            $pdf->SetFont('Arial','','8');            
            foreach($detalles as $detalle){                 
                 /*TITULOS PARA SEGUNDA COLUMNA*/
                if($j > 30){
                    $j = 1;                
                    $yForComments = $pdf->GetY();
                    $pdf->SetY($y);
                    $pdf->SetFont('Arial','B','8');
                    $pdf->Cell(98, 7, "", '', 0, 'L');/*Margin left*/
                    $pdf->Cell(90, 7, $areaName, 'B', 1, 'L');
                    
                    $pdf->Cell(98, 7, "", '', 0, 'L');/*Margin left*/
                    $pdf->Cell(5, 7, "#", 'B', 0, 'L');
                    $pdf->Cell(60, 7, "Producto", 'B', 0, 'L');
                    $pdf->Cell(25, 7, "Stock", 'B', 1, 'R');              
                }
                
                /* SEGUNDA COLUMNA */
                if($k > 30){
                    $pdf->SetFont('Arial','','8');
                    $pdf->Cell(98, 7, "", '', 0, 'L');/*Margin left*/    
                    $x = $pdf->GetX();                
                    $pdf->Cell(5, 5, $i, 'B', 0, 'C');
                    $pdf->Cell(60, 5, utf8_decode($detalle['description']), 'B', 0, 'L');
                    $pdf->Cell(25, 5, number_format($detalle['quantity']), 'B', 1, 'R');

                /* PRIMER COLUMNA */
                }else{                          
                    $pdf->SetFont('Arial','','8');
                    $pdf->Cell(5, 5, $i, 'B', 0, 'C');
                    $pdf->Cell(60, 5, utf8_decode($detalle['description']), 'B', 0, 'L');
                    $pdf->Cell(25, 5, number_format($detalle['quantity']), 'B', 1, 'R');
                    $yForComments = $pdf->GetY();                    
                }            
                $i++;
                $j++;
                $k++;                
            }      
            
            $pdf->ln(5);
            $pdf->SetX($x);
        }
             
        $pdf->SetY($yForComments);
        $pdf->Ln(10);    
        $pdf->SetFont('Arial','B','8');
        $pdf->Cell(85, 7, "Notas de inventario fisico", 'B', 1, 'L');
        $pdf->SetFont('Arial','','8');
        $pdf->Multicell(85, 3,  $shipment->getComments(), '0', 1, 'L');

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