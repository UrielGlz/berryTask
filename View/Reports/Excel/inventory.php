<?php 
    $sucursales = $data['stores'];
    $arrayData = $data['data'];
    $colTitulosTemp = $arrayData[0];
    
    $fieldsToPrint = array(
        'code'=>'Codigo',
        'description'=>'Descripcion',
        'presentation'=>'Presentacion',
    );
    
    foreach($sucursales as $key => $sucursal){
        $fieldsToPrint[$key] = $sucursal;
    }
    
    foreach($colTitulosTemp as $titulo => $value){
        if(key_exists($titulo, $fieldsToPrint)){            
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
    foreach ($arrayData as $key => $data) {
        $i=0;     
        foreach ($data as $field => $value) {             
            if(key_exists($field, $fieldsToPrint)){ 
                $col = $this->getColLetter($i);
                $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $value);
                $i++;
            }
            $objPHPExcel->getActiveSheet()->getStyle("A$row:C$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle("D$row:J$row")->getNumberFormat()->setFormatCode('#,##0.00_ ;[Red]-#,##0.00');
        }
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
        exit();
    }
    unset($objWriter);