<?php 
    $requestDates = $data['requestDates'];
    $arrayData = $data['data'];
    $colTitulosTemp = $arrayData[0];
    
    $storeRepo = new StoreRepository();
    $storeData = $storeRepo->getById($this->options['store_id']);
   
    $fieldsToPrint = array(
        'product'=>'Producto'
    );
    
    unset($colTitulosTemp['product_id']);/*Este no se imprime*/
    foreach($colTitulosTemp as $key => $value){
        $fieldsToPrint[$key] = $key;        
    }
    
    foreach($requestDates as $key => $date){
        $dateFormated = date_create($date['delivery_date']);
        $dateFormated = date_format($dateFormated,'m/d/Y'); /*Aplico formato a la fecha, solo es necesario hacerlo aqui*/
        $colTitulos[] = $dateFormated;
    }
    
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getActiveSheet()->freezePane('A4');
    
    $i = 1;
    $firstCol = $this->getColLetter($i);
    foreach($colTitulos as $titulo){
        $col = $this->getColLetter($i);
        $firstCol = $col;
        $i++;
        $col = $this->getColLetter($i);
        $secondCol = $col;
        $objPHPExcel->getActiveSheet()->mergeCells($firstCol."2:".$secondCol."2");    
        $objPHPExcel->getActiveSheet()->setCellValue($firstCol.'2', $titulo);
        $objPHPExcel->getActiveSheet()->getStyle($firstCol.'2')->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle($firstCol.'2')->getFont()->setBold(true);
        
        $i++;
    }    
    
    $col = $this->getColLetter($i);
    $firstCol = $col;
    $i++;
    $col = $this->getColLetter($i);
    $secondCol = $col;
    $objPHPExcel->getActiveSheet()->mergeCells($firstCol."2:".$secondCol."2");    
    $objPHPExcel->getActiveSheet()->setCellValue($firstCol.'2', 'TOTAL');
    $objPHPExcel->getActiveSheet()->getStyle($firstCol.'2')->getAlignment()->setWrapText(true);  
    
    $lastCol = $this->getColLetter($i);
    $objPHPExcel->getActiveSheet()->getStyle($firstCol.'2:'.$lastCol.'2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle($firstCol.'2:'.$lastCol.'2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);    
    $objPHPExcel->getActiveSheet()->getStyle($firstCol.'2:'.$lastCol.'2')->getFont()->setBold(true);
    
    $i = 1;
    $firstCol = $this->getColLetter($i);
    foreach($colTitulos as $titulo){
        $col = $this->getColLetter($i);
        $objPHPExcel->getActiveSheet()->setCellValue($col.'3', 'Ordenado');

        $i++;
        $col = $this->getColLetter($i);
        $objPHPExcel->getActiveSheet()->setCellValue($col.'3', 'Recibido');
        
        $i++;
    }    
    
    $col = $this->getColLetter($i);
    $objPHPExcel->getActiveSheet()->setCellValue($col.'3', 'Ordenado');

    $i++;
    $col = $this->getColLetter($i);
    $objPHPExcel->getActiveSheet()->setCellValue($col.'3', 'Recibido');

    $i++;   
   
    $objPHPExcel->getActiveSheet()->getStyle($firstCol.'3:'.$lastCol.'3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle($firstCol.'3:'.$lastCol.'3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle($firstCol.'3:'.$lastCol.'3')->getFont()->setBold(true);
    
    $objPHPExcel->getActiveSheet()->mergeCells("A2:A3");    
    $objPHPExcel->getActiveSheet()->setCellValue("A2", 'Producto'); 
    $objPHPExcel->getActiveSheet()->getStyle("A2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("A2:".$lastCol."2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);   
    $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
    
    $objPHPExcel->getActiveSheet()->mergeCells("A1:".$lastCol."1");            
    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(80);
    $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->setCellValue("A1", $this->headerExcelReport."\n".$storeData['name']."\n".'Generado: '.date('M/d/Y  g:i a'));
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    
    $row=4;
   
    foreach ($arrayData as $key => $data) {
        $i=0;     
        $sumRequested = "";
        $sumReceived = "";
        foreach ($data as $field => $value) {           
            if(key_exists($field, $fieldsToPrint)){ 
                $col = $this->getColLetter($i);
                $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $value);   
                
                if(strpos($field,'_requested')){$sumRequested .= "+".$col.$row;}
                if(strpos($field,'_received')){$sumReceived .= "+".$col.$row;}
                $i++;
            }            
        }
        $col = $this->getColLetter($i);
        $objPHPExcel->getActiveSheet()->setCellValue($col . $row, "=".$sumRequested);   
        
        $i++;
        $col = $this->getColLetter($i);
        $objPHPExcel->getActiveSheet()->setCellValue($col . $row, "=".$sumReceived);   
        
        $objPHPExcel->getActiveSheet()->getStyle("$firstCol$row:$lastCol$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
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