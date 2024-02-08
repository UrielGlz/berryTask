<?php 
    $arrayData = $data['data'];        
    
    $colTitulos[] = $this->_getTranslation('Factura');
    $colTitulos[] = $this->_getTranslation('Fecha');
    $colTitulos[] = $this->_getTranslation('Fecha de pago');
    $colTitulos[] = $this->_getTranslation('Cliente');
    $colTitulos[] = $this->_getTranslation('Sucursal');
    $colTitulos[] = $this->_getTranslation('Total');
    $colTitulos[] = $this->_getTranslation('Balance');
    $colTitulos[] = $this->_getTranslation('Status');
    
    $objPHPExcel = new PHPExcel();
    $sheet = $objPHPExcel->getActiveSheet();
    $sheet->freezePane('A3');
    $col = count($colTitulos);
    $lastCol = $this->getColLetter($col-1);    
    
    $sheet->mergeCells("A1:".$lastCol."1");            
    $sheet->getRowDimension('1')->setRowHeight(50);
    $sheet->getStyle("A1")->getFont()->setBold(true);
    $sheet->setCellValue("A1", $this->headerExcelReport);
    $sheet->getStyle('A1')->getAlignment()->setWrapText(true);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("A1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    
    $sheet->getStyle("A2:".$lastCol."2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->fromArray($colTitulos,NULL,'A2');  
    
    $row=3;
    $firstRow = $row;
    foreach ($arrayData as $key => $data) {       
        $sheet->setCellValue("A$row", $data['invoice_num']);                          
        $sheet->setCellValue("B$row", $data['formatedDate']);
        $sheet->setCellValue("C$row", $data['formatedDueDate']);
        $sheet->setCellValue("D$row", $data['customerName']);
        $sheet->setCellValue("E$row", $data['store_name']);
        $sheet->setCellValue("F$row", $data['total']);
        $sheet->setCellValue("G$row", $data['balance']);
        $sheet->setCellValue("H$row", $data['statusName']);
        
        $row++;        
    }
    
    $sheet->getStyle("A$firstRow:E$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("A$firstRow:E$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->getStyle("F$firstRow:G$row")->getNumberFormat()->setFormatCode('$#,##0.00_ ;[Red]-#,##0.00');
    $sheet->getStyle("H$firstRow:H$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    $lastRow = $row - 1;
    $sheet->setCellValue("E$row", "TOTAL");
    $sheet->setCellValue("F$row", "=SUM(F$firstRow:F$lastRow)"); 
    $sheet->setCellValue("G$row", "=SUM(G$firstRow:G$lastRow)");
    
    $sheet->getStyle("A$row:G$row")->getFont()->setBold(true);
    $sheet->getStyle("F$row:G$row")->getNumberFormat()->setFormatCode('$#,##0.00_ ;[Red]-#,##0.00');

    foreach(range('A',$lastCol) as $columnID) {
        $sheet->getColumnDimension($columnID)
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