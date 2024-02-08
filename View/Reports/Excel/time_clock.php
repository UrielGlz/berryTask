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
        'date'=>'Fecha',
        'userName'=>'Usuario',
        'sucursalName'=>'Sucursal',
        'check_in'=>'Hora inicio',
        'check_out'=>'Hora fin',
        'total'=>'Total horas'
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
        foreach($array as $key => $data ){
            $i=0; 
             //echo "<pre>";var_dump($data);echo "</pre>";exit;
            foreach ($fieldsToPrint as $col => $value) {             
                if(key_exists($col, $data)){ 
                    $colLetter = $this->getColLetter($i);
                    $objPHPExcel->getActiveSheet()->setCellValue($colLetter . $row, $data[$col]);
                    //$objPHPExcel->getActiveSheet()->getStyle("A$row:E$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    //$objPHPExcel->getActiveSheet()->getStyle("F$row")->getNumberFormat()->setFormatCode('#,##0.00_ ;[Red]-$#,##0.00');
                    $i++;
                }  
            }
            $row++;
        }   
        
        $objPHPExcel->getActiveSheet()->mergeCells("A$row:E$row");            
        $objPHPExcel->getActiveSheet()->getStyle("A$row:F$row")->getFont()->setBold(true);          
        $objPHPExcel->getActiveSheet()->getStyle("A$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->setCellValue("A$row", $data['userName'].":");
          
        $rowFin = $row - 1;
        $objPHPExcel->getActiveSheet()->setCellValue("F$row", "=SUM(F$rowInicio:F$rowFin)");   
        $objPHPExcel->getActiveSheet()->getStyle("F$row")->getNumberFormat()->setFormatCode('#,##0.00_ ;[Red]-$#,##0.00');     

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