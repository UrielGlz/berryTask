<?php 
    $arrayData = $data['data'];
    $storeName = $data['storeName'];
    
    /*Para generar encabezado de reporte*/
    $empresa = new CompanyRepository();
    $empresaData = $empresa->getById(1);
    $startDate = $this->options['startDate'];
    $endDate = $this->options['endDate'];
    if(trim($startDate)=='' && trim($endDate)==''){
        $startDate = $this->_getTranslation("Desde el inicio de los tiempos");
        $endDate = strftime('%m/%d/%Y',strtotime('now'));
    }elseif(trim($startDate)=='' && trim($endDate)!=''){
        $startDate = $endDate;
    }elseif(trim($startDate)!='' && trim($endDate)==''){
        $endDate = $startDate;
    }        
    /*Fin epara generar encabezado*/
    
    $colTitulos[] = 'Producto';
    $colTitulos[] = 'Enviado';
    $colTitulos[] = 'Recibido';
    $colTitulos[] = 'Precio';
    $colTitulos[] = 'Total enviado';
    $colTitulos[] = 'Total recibido';
    
    $objPHPExcel = new PHPExcel();
    $sheet = $objPHPExcel->getActiveSheet();
    $sheet->freezePane('A3');
    $col = count($colTitulos);
    $lastCol = $this->getColLetter($col-1);    
    
    $sheet->mergeCells("A1:".$lastCol."1");            
    $sheet->getRowDimension('1')->setRowHeight(50);
    $sheet->getStyle("A1")->getFont()->setBold(true);
    $sheet->setCellValue("A1", $empresaData['name']."\n".$this->_getTranslation($this->getTituloReporte()).' '.$storeName."\n".$startDate." - ".$endDate);
    $sheet->getStyle('A1')->getAlignment()->setWrapText(true);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("A1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    
    $sheet->getStyle("A2:".$lastCol."2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->fromArray($colTitulos,NULL,'A2');  
    
    $row=3;
    $firstRow = $row;
   foreach ($arrayData as $key => $data) {       
        $sheet->setCellValue("A$row", $data['description']);                          
        $sheet->setCellValue("B$row", $data['quantity']);
        $sheet->setCellValue("C$row", $data['received']);
        $sheet->setCellValue("D$row", $data['sale_price']);
        $sheet->setCellValue("E$row", "=SUM(B$row*D$row)");
        $sheet->setCellValue("F$row", "=SUM(C$row*D$row)");
        
        $row++;        
    }
    
    $sheet->getStyle("A$firstRow:$lastCol$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("A$firstRow:$lastCol$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->getStyle("B$firstRow:C$row")->getNumberFormat()->setFormatCode('#,##0.00_ ;[Red]-#,##0.00');
    $sheet->getStyle("D$firstRow:F$row")->getNumberFormat()->setFormatCode('$#,##0.00_ ;[Red]-#,##0.00');
    
    $lastRow = $row - 1;
    $sheet->setCellValue("A$row", "TOTAL");
    $sheet->setCellValue("B$row", "=SUM(B$firstRow:B$lastRow)"); 
    $sheet->setCellValue("C$row", "=SUM(C$firstRow:C$lastRow)");
    $sheet->setCellValue("E$row", "=SUM(E$firstRow:E$lastRow)");
    $sheet->setCellValue("F$row", "=SUM(F$firstRow:F$lastRow)");
    
    $sheet->getStyle("A$row:F$row")->getFont()->setBold(true);
    $sheet->getStyle("D$row:F$row")->getNumberFormat()->setFormatCode('$#,##0.00_ ;[Red]-#,##0.00');

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