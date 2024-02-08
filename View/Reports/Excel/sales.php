<?php
    if(is_null($data['data'])){
        $this->flashmessenger->addMessage(array('info'=>'No se encontraton resultados.')); 
        header('Location: Reports.php');
        exit;
    }

    $arrayData = $data['data'];
    $primerElemento = reset($arrayData);
    $colTitulosTemp = $primerElemento[0];
   
    $fieldsToPrint = array(        
        'storeName'=>$this->_getTranslation('Sucursal'),
        'dateFormated'=>$this->_getTranslation('Fecha'),
        'final_cash'=>$this->_getTranslation('Efectivo'),
        'debit_card'=>$this->_getTranslation('Tarjeta de debito'),
        'credit_card'=>$this->_getTranslation('Tarjeta de credito'),
        'check'=>$this->_getTranslation('Cheque'),
        'stamp'=>$this->_getTranslation('Estampillas'),
        'total_sales'=>$this->_getTranslation('Total venta'),
        'status_name'=>$this->_getTranslation('Status')
    );
    
    foreach($fieldsToPrint as $titulo => $value){
        if(key_exists($titulo, $colTitulosTemp)){            
            $colTitulos[] = $fieldsToPrint[$titulo];
        }
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
   
    $objPHPExcel->getActiveSheet()->getStyle("A2:".$lastCol."2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->fromArray($colTitulos,NULL,'A2');    
   
    $row=3;  
    foreach ($arrayData as $key => $array) { 
        $rowInicio = $row;
        $totalEfectivo = 0;
        $totalTarjetaDebito = 0;
        $totalTarjetaCredito = 0;
        $totalCheques = 0;
        $totalEstampillas = 0;
        $totalRetiros = 0;
        $totalSales = 0;
        foreach($array as $key => $data ){       
            $arrayRow = array();
            $i = 0;
            foreach ($fieldsToPrint as $field => $value) {
                if(key_exists($field, $data)){ 
                    $col = $this->getColLetter($i);   
                    $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $data[$field]);                                
                    $i++;                                                      
                }  
            }
            
            if($data['status_name'] == 'Activo'){
                $totalEfectivo += round($data['final_cash'],2);
                $totalTarjetaDebito += round($data['debit_card'],2);
                $totalTarjetaCredito += round($data['credit_card'],2);
                $totalCheques += round($data['check'],2);
                $totalEstampillas += round($data['stamp'],2);
                $totalSales += round($data['final_cash'],2) + round($data['debit_card'],2) + round($data['credit_card'],2) + round($data['check'],2) + round($data['stamp'],2);
            }
            
            $row++;
        }          
        
        $objPHPExcel->getActiveSheet()->getStyle("A$rowInicio:B$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle("C$rowInicio:I$row")->getNumberFormat()->setFormatCode('$#,##0.00_ ;[Red]-$#,##0.00');
        
        $objPHPExcel->getActiveSheet()->mergeCells("A$row:B$row");            
        $objPHPExcel->getActiveSheet()->getStyle("A$row:I$row")->getFont()->setBold(true);          
        $objPHPExcel->getActiveSheet()->getStyle("A$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("A$row", "TOTAL ".$data['storeName']);
          
        $rowFin = $row - 1;
        $arrayTotales = array();
        $arrayTotales[] = "$totalEfectivo";
        $arrayTotales[] = "$totalTarjetaDebito";
        $arrayTotales[] = "$totalTarjetaCredito";
        $arrayTotales[] = "$totalCheques";
        $arrayTotales[] = "$totalEstampillas";
        $arrayTotales[] = "$totalSales";
        
        $objPHPExcel->getActiveSheet()->fromArray($arrayTotales,NULL,'C'.$row);
        $objPHPExcel->getActiveSheet()->getStyle("C$row:I$row")->getNumberFormat()->setFormatCode('$#,##0.00_ ;[Red]-$#,##0.00');     

        $row++;
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
        exit;
    }
    unset($objWriter);