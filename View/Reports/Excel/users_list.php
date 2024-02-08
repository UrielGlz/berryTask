<?php 
    $arrayData = $data['data'];        
    
    $colTitulos[] = $this->_getTranslation('Num. empleado');
    $colTitulos[] = $this->_getTranslation('Nombre usuario');
    $colTitulos[] = $this->_getTranslation('Nombre completo');    
    $colTitulos[] = $this->_getTranslation('Numero Seguro Social');
    $colTitulos[] = $this->_getTranslation('Fecha de alta');
    $colTitulos[] = $this->_getTranslation('Fecha alta payroll');
    $colTitulos[] = $this->_getTranslation('Fecha baja payroll');
    $colTitulos[] = $this->_getTranslation('Telefono');
    $colTitulos[] = $this->_getTranslation('Correo electronico');
    $colTitulos[] = $this->_getTranslation('Direccion');    
    $colTitulos[] = $this->_getTranslation('Status');
    $colTitulos[] = $this->_getTranslation('Sucursal');
    
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
        $sheet->setCellValue("A$row", $data['employe_number']);                          
        $sheet->setCellValue("B$row", $data['user']);
        $sheet->setCellValue("C$row", $data['complete_name']);
        $sheet->setCellValue("D$row", $data['ssn']);
        $sheet->setCellValue("E$row", $data['created_date']);
        $sheet->setCellValue("F$row", $data['alta_payroll']);
        $sheet->setCellValue("G$row", $data['baja_payroll']);
        $sheet->setCellValue("H$row", $data['phone']);
        $sheet->setCellValue("I$row", $data['email']);
        $sheet->setCellValue("J$row", $data['address']);
        $sheet->setCellValue("K$row", $data['status_name']);        
        $sheet->setCellValue("L$row", $data['store_name']);
        
        $row++;        
    }
    
    $sheet->getStyle("A$firstRow:$lastCol$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("A$firstRow:$lastCol$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);

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