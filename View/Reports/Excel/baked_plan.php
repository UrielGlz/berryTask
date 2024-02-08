<?php    
    $objPHPExcel = new PHPExcel();    
    if(is_null($data['globalDetalles'])){
        $this->flashmessenger->addMessage(array('info'=>'No se encontraton resultados.')); 
        header('Location: Baked.php?action=list');
        exit;
    }
    
    $arrayData = $data['globalDetalles']; /*Diferente partes del pastel entre requisiciones y requisiciones especiales*/
    $requisitionDetalles = $data['requisitionsDetalles'];
    $specialRequisitionDetalles = $data['specialRequisitionsDetalles'];
    $colTitulosTemp = $arrayData[key(($arrayData))];
    
    $fieldsToPrint = array(
        'flavor'=>'Pan',
        'shapeName'=>'Forma',        
        'sizeName'=>'Tamano',
    );
    
    foreach($colTitulosTemp as $titulo => $value){
        if(key_exists($titulo, $fieldsToPrint)){            
            $colTitulos[] = $fieldsToPrint[$titulo];
        }
    }
    
    //$colTitulos[] = 'Vitrina';
    $colTitulos[] = 'Especial';
    //$colTitulos[] = 'Total';
    $colTitulos[] = 'Stock';
    $colTitulos[] = 'Hornear';
    $colTitulos[] = '[Numero/Letra] => Cantidad';
    
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

    $objPHPExcel->getActiveSheet()->getStyle("A2:".$lastCol."2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);    
    $objPHPExcel->getActiveSheet()->getStyle("A2:".$lastCol."2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->fromArray($colTitulos,NULL,'A2');  
    
    $row=3;
    $firstRow = $row;
    foreach ($arrayData as $key => $data) {
        $i=0;     
        foreach ($data as $col => $value) {             
            if(key_exists($col, $fieldsToPrint)){ 
                $col = $this->getColLetter($i);
                $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $value); 
                $i++;
            }           
        }    
        
        $quantity = 0;
        $specialQuantity = 0;
        $numberOrLetterOfCake = '';
        $numberOrLetterOfCakeArray = array();
        if(key_exists($key, $requisitionDetalles)){$quantity = $requisitionDetalles[$key]['quantity'];}
        if(key_exists($key, $specialRequisitionDetalles)){           
            $specialQuantity = $specialRequisitionDetalles[$key]['quantity'];
            
            $numberOrLetterOfCakeString = $specialRequisitionDetalles[$key]['number_of_cake'];
            if($numberOrLetterOfCakeString !== '' && !is_null($numberOrLetterOfCakeString)){
                 $numberOrLetterOfCakeExploded = explode(',', $numberOrLetterOfCakeString);
             
                foreach($numberOrLetterOfCakeExploded as $key => $numberOrLetter){
                     $numberOrLetterToArray  = array_map(null, str_split($numberOrLetter));
                     foreach($numberOrLetterToArray as $key => $value){
                        if(!isset($numberOrLetterOfCakeArray[$value])){$numberOrLetterOfCakeArray[$value] = 1;}
                        else{$numberOrLetterOfCakeArray[$value]++;}
                     }
                }

                foreach($numberOrLetterOfCakeArray as $numeroLetra => $cantidad){
                    $numberOrLetterOfCake .= '['.$numeroLetra.']=>'.$cantidad ."\n";
                }
            }           
        }
        /*
        $col = $this->getColLetter($i);
        $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $quantity);*/
        
        //$i++;
        $col = $this->getColLetter($i);
        $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $specialQuantity);
        
        /*
        $i++;        
        $col = $this->getColLetter($i);
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row, "=C$row+D$row");*/
        
        $i += 2;        
        $col = $this->getColLetter($i);
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row, "=D$row-E$row");
        
        $i++;
        $col = $this->getColLetter($i);
        $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $numberOrLetterOfCake);
        $objPHPExcel->getActiveSheet()->getStyle($col . $row)->getAlignment()->setWrapText(true);
        
        $row++;
    }    
    
    $objPHPExcel->getActiveSheet()->getStyle("A$firstRow:$lastCol$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);                
    $objPHPExcel->getActiveSheet()->getStyle("A$firstRow:$lastCol$row")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); 

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