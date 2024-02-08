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
        'req_number'=>$this->_getTranslation('Orden #'),
        'dateFormated'=>$this->_getTranslation('Fecha'),
        'customerName'=>$this->_getTranslation('Cliente'),
        'phone'=>$this->_getTranslation('Telefono'),
        'ammount'=>$this->_getTranslation('Total'),
        //'ammount_payments'=>$this->_getTranslation('Pagos')
    );
    
    foreach($fieldsToPrint as $titulo => $value){
        if(key_exists($titulo, $colTitulosTemp)){            
            $colTitulos[] = $fieldsToPrint[$titulo];
        }
    }
    
    //$colTitulos[] = $this->_getTranslation('Balance');   
    
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
            $arrayRow = array();
            foreach ($fieldsToPrint as $col => $value) {
                if(key_exists($col, $data)){ 
                    $arrayRow[] = $data[$col];                                        
                }  
                $objPHPExcel->getActiveSheet()->fromArray($arrayRow,NULL,'A'.$row);
                //$objPHPExcel->getActiveSheet()->setCellValue("H$row", "=SUM(F$row+G$row)");   
            }
            $row++;
        }                  
       
        $objPHPExcel->getActiveSheet()->getStyle("A$rowInicio:E$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle("F$rowInicio:F$row")->getNumberFormat()->setFormatCode('#,##0.00_ ;[Red]-$#,##0.00');
        
        $objPHPExcel->getActiveSheet()->mergeCells("A$row:E$row");            
        $objPHPExcel->getActiveSheet()->getStyle("A$row:H$row")->getFont()->setBold(true);          
        $objPHPExcel->getActiveSheet()->getStyle("A$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->setCellValue("A$row", "TOTAL ".$data['storeName']);
          
        $rowFin = $row - 1;
        $objPHPExcel->getActiveSheet()->setCellValue("F$row", "=SUM(F$rowInicio:F$rowFin)");   
        //$objPHPExcel->getActiveSheet()->setCellValue("G$row", "=SUM(G$rowInicio:G$rowFin)");   
        //$objPHPExcel->getActiveSheet()->setCellValue("H$row", "=SUM(H$rowInicio:H$rowFin)");   
        $objPHPExcel->getActiveSheet()->getStyle("F$row:F$row")->getNumberFormat()->setFormatCode('#,##0.00_ ;[Red]-$#,##0.00');     

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