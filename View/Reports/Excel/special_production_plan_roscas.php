<?php 
    if($data != null){
        $settings = new SettingsRepository();
        $id_category_for_relleno = $settings->_get('id_category_for_relleno');
        $id_category_for_betun = $settings->_get('id_category_for_betun');
        
        $requisitionsList = $data['requisitionsList'];
        foreach($requisitionsList as $row){
            $newRow = array(
                'req_number'=>$row['req_number'],
                'storeName'=>$row['storeName'],
                'delivery_date'=>$row['delivery_date'],
                'delivery_time'=>$row['delivery_time'],
                'customerName'=>$row['customerName'],
                'home_service'=>$row['home_service'],
                'comments_1'=>$row['comments_1']
            );
            $newData[] = $newRow;
        }
        
        $requisitionsList = $newData;
        $colTitulosTemp = $requisitionsList[0];    
        
        $requisitionsDetailsList = $data['requisitionsDetailsList'];        
        $details = array();
        foreach($requisitionsDetailsList as $detail){
            $details[$detail['req_number']][] = $detail;
        }       

        $fieldsToPrint = array(
            'req_number'=>'No',
            'storeName'=>'Sucursal',
            'delivery_date'=>'Fecha entrega',
            'delivery_time'=>'Hora entrega',
            'customerName'=>'Cliente',
            'home_service'=>'Servicio a domicilio'
        );

        foreach($fieldsToPrint as $titulo => $value){
            if(key_exists($titulo, $colTitulosTemp)){            
                $colTitulos[] = $fieldsToPrint[$titulo];
            }
        }
        
        //$colTitulos[] = 'Pan';
        $colTitulos[] = 'Rosca';
        //$colTitulos[] = 'Betun';
        $colTitulos[] = 'Decorado';
        $colTitulos[] = 'Notas';

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->freezePane('A3');
        $col = count($colTitulos);
        $lastCol = $this->getColLetter($col-1);        
        
        foreach($objPHPExcel->getActiveSheet()->getRowDimensions() as $rd) { 
            $rd->setRowHeight(-1); 
        }

        $objPHPExcel->getActiveSheet()->mergeCells("A1:".$lastCol."1");            
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(50);
        $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setCellValue("A1", $this->headerExcelReport);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        
        $objPHPExcel->getActiveSheet()->getStyle("A2:{$lastCol}2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle("A2:{$lastCol}2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);                   

        $objPHPExcel->getActiveSheet()->fromArray($colTitulos,NULL,'A2');    

        $row=3;
        foreach ($requisitionsList as $key => $data) {                        
            $objPHPExcel->getActiveSheet()->getStyle("A$row:$lastCol$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle("A$row:$lastCol$row")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle("A$row:$lastCol$row")->getAlignment()->setWrapText(true);
            $i=0;     
            foreach ($data as $col => $value) {             
                if(key_exists($col, $fieldsToPrint)){ 
                    $col = $this->getColLetter($i);
                    $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $value);
                    $i++;
                }
            }
            
            if(isset($details[$data['req_number']])){      
                $detailsReq = $details[$data['req_number']];
                
                /*
                #Imprimir columna de pan (category = 1)
                $string = '';       
                $numberOrLetterOfCake = '';
                $numberOrLetterOfCakeArray = array();               
                
                foreach($detailsReq as $detail){
                    if($detail['category'] == '1'){
                        $numberOrLetterOfCakeString = $detail['number_of_cake'];
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
                        //$string .= "PE#".$detail['multiple'].": - ".$detail['quantity']." ".$detail['flavor']." ".$detail['sizeName']."\n";
                        $string .= $detail['quantity']." ".$detail['flavor']." ".$detail['shapeName']." ".$detail['sizeName']."\n".$numberOrLetterOfCake;
                    }                    
                }
                
                $col = $this->getColLetter($i);
                $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $string);
                $objPHPExcel->getActiveSheet()->getStyle("$col$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $i++;*/
                
                #Imprimir columna de relleno (category = 2)
                $string = '';                
                foreach($detailsReq as $detail){
                    if($detail['category'] == '2'){
                        //$string .= "PE#".$detail['multiple'].": - ".$detail['quantity']." ".$detail['flavor']." ".$detail['sizeName']."\n";
                        $string .= $detail['quantity']." ".$detail['flavor']." ".$detail['shapeName']." ".$detail['sizeName']."\n";
                    }                    
                }
                $col = $this->getColLetter($i);
                $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $string);
                $objPHPExcel->getActiveSheet()->getStyle("$col$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $i++;      
                
                /*
                #Imprimir columna de betun (category = 3)
                $string = '';                
                foreach($detailsReq as $detail){
                    if($detail['category'] == '3'){
                        //$string .= "PE#".$detail['multiple'].": - ".$detail['quantity']." ".$detail['flavor']." ".$detail['sizeName']."\n";
                        $string .= $detail['quantity']." ".$detail['flavor']." ".$detail['shapeName']." ".$detail['sizeName']."\n";
                    }                    
                }
                $col = $this->getColLetter($i);
                $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $string);
                $objPHPExcel->getActiveSheet()->getStyle("$col$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $i++;  */    
            }
            
            $col = $this->getColLetter($i);
            $objPHPExcel->getActiveSheet()->setCellValue($col . $row, $data['comments_1']);          
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
    }else{
        $this->flashmessenger->addMessage(array('info'=>'No se encontraron resultados con la seleccion.'));
    }