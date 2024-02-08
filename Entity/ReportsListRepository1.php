<?php
class ReportsListRepository extends EntityRepository {
    
    public function getInventory(){      
        $settings = new SettingsRepository();
        $categoryForSupplie = $settings->_get('id_category_for_supplies');
        
        $repo = new StoreRepository();
        $sucursales = $repo->getListStores();
        
        $case = '';
        foreach($sucursales as $sucursal){
            $case .= " sum(CASE WHEN id_store = '{$sucursal['id']}' THEN i.stock ELSE 0 END) as '{$sucursal['id']}',";
            $arraySucursales[$sucursal['id']] = $sucursal['name'];
        }
        
        $case = trim($case,',');
        $query = "SELECT "
                . "p.id as id_product,"
                . "p.code,"
                . "p.description,"
                . "fxGetPresentationDescription(p.presentation)as presentation,"
                . "fxGetCategoryDescription(p.category)as category,"
                . "fxGetBrandDescription(p.brand)as brand,"
                . "$case "
                . "FROM products p LEFT JOIN inventory i ON p.id = i.id_product "
                . "WHERE type = 'product'"
                . "AND p.category = '$categoryForSupplie' "
                . "GROUP BY p.id";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            //echo "<pre>";var_dump($this->resultToArray($result));echo "</pre>";exit;
            return array(
                'data'=>$this->resultToArray($result),
                'stores'=>$arraySucursales
            );
        }else{
            null;
        }
    }
    
    public function getSpecialProductionPlan($options){        
        $idsRequisitions = implode(',',$options['special_orders']);
        $repoSR = new SpecialOrderRepository();
        $requisitionsList = $repoSR->getListRequisitionsByIds($idsRequisitions);
        $requisitionsDetailsList = $repoSR->getSpecialProductionPlan($idsRequisitions);
        
        if($requisitionsList == null){
            return null;
        }
       
        return array(
            'requisitionsList'=>$requisitionsList,
            'requisitionsDetailsList'=>$requisitionsDetailsList
        );
    }
    
    public function getBakedPlan($options){
        $detalles = array();
        $listRequisitions = null;
        $specialDetalles = array();
        $listSpecialRequisitions = null;
        $globalDetalles = null;
        
        /*
         if($options['requisitions']){
            $requisition = new RequisitionEntity();
            $idRequisitions = implode(',', $options['requisitions']);
            $detalles = $requisition->getBakedPlan($idRequisitions);
            $listRequisitions = $requisition->getListRequisitionsByIds($idRequisitions);
            
            if($detalles){ 
                $detallesTemp = null;
                foreach($detalles as $detalle){ 
                    $detallesTemp[$detalle['id_slice']] = $detalle;
                    $globalDetalles[$detalle['id_slice']] = $detalle;
                }
                $detalles = $detallesTemp;
            }else{
                $detalles = array();
            }            
        }*/
        
        if($options['special_orders']){
            $specialRequisition = new SpecialOrderRepository();
            $idRequisitions = implode(',', $options['special_orders']);
            $specialDetalles = $specialRequisition->getBakedPlan($idRequisitions);
            $listSpecialRequisitions = $specialRequisition->getListRequisitionsByIds($idRequisitions);
            
            if($specialDetalles){
                $specialDetallesTemp = null;
                foreach($specialDetalles as $specialDetalle){ 
                    $specialDetallesTemp[$specialDetalle['id_slice']] = $specialDetalle;
                    $globalDetalles[$specialDetalle['id_slice']] = $specialDetalle;
                }
                $specialDetalles = $specialDetallesTemp;
            }else{
                $specialDetalles = array();
            }
        }
        
        return array(
            'globalDetalles'=>$globalDetalles,
            'requisitionsDetalles'=>$detalles,
            'specialRequisitionsDetalles'=>$specialDetalles,
            'listRequisitions'=>$listRequisitions,
            'listSpecialRequisitions'=>$listSpecialRequisitions
        );
    }
    
    public function getStoreRequest($options){
        $storeArray = array();
        $date = null;
        $area_bakery_production_id = null;  
        $masa = null;
        
        $date = $this->createFilterFecha($options['startDate'], $options['endDate'], 'r.delivery_date');   
        if(isset($options['area_id']) && is_array($options['area_id']) && count($options['area_id']) > 0){
               $area_bakery_production_ids = implode(',', $options['area_id']);
                $area_bakery_production_id = " AND find_in_set(r.area_id,'$area_bakery_production_ids')";
        }elseif(isset($options['area_id']) && trim($options['area_id'])!=='' && trim($options['area_id'])!=='0' && !is_null($options['area_id'])){
            $area_bakery_production_id = " AND find_in_set(r.area_id,'{$options['area_id']}')";
        }  
        
        if(isset($options['masa']) && is_array($options['masa']) && count($options['masa']) > 0){
               $masas = implode(',', $options['masa']);
               $masa = " AND find_in_set(p.masa,'$masas')";
        }elseif(isset($options['masa']) && trim($options['masa'])!=='' && trim($options['masa'])!=='0' && !is_null($options['masa'])){
            $masa = " AND find_in_set(p.masa,'{$options['masa']}')";
        }  
        
        $repo = new StoreRepository();
        $sucursales = $repo->getListStores();
        
        $case = '';
        foreach($sucursales as $sucursal){
            $case .= " sum(CASE WHEN r.store_id = '{$sucursal['id']}' THEN d.quantity ELSE 0 END) as '{$sucursal['id']}',";
            $arraySucursales[$sucursal['id']] = $sucursal['name'];
        }
        
        $case = trim($case,',');
        $query = "SELECT "
                . "d.id as id_product, "
                . "p.description, "
                . "fxGetSizeDescription(d.id_size)as size, "
                . "$case "
                . "FROM store_request r "
                . "LEFT JOIN store_request_details d ON r.id = d.id_store_request "
                . "LEFT JOIN products p ON d.id_product = p.id "
                . "LEFT JOIN masas m ON p.masa = m.id  "
                . "WHERE r.status = '1' "
                . "$date "
                . "$area_bakery_production_id "
                . "$masa "
                . "GROUP BY d.id_product "
                . "ORDER BY p.description ASC ";
        
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return array(
                'data'=>$this->resultToArray($result),
                'stores'=>$arraySucursales
            );
        }else{
            null;
        }      
    }
    
     public function getTimeClock($options = null){     
        $user  = null;
        $date = null;
        $store_id = null;
        $limit = null;        

       if($options != null){         
            $date = $this->createFilterFecha($options['startDate'], $options['endDate'], 't.date');
            if(isset($options['store_id']) && is_array($options['store_id']) && count($options['store_id']) > 0){
                //$store_ids = implode(',', $options['store_id']);
                //$store_id = " AND find_in_set(t.store_id,'$store_ids')";                
                
                $store_id = "AND (";
                $storeIdArray = $options['store_id'];
                foreach($storeIdArray as $key => $storeId ){
                    $store_id .= " find_in_set($storeId,t.store_id) OR ";
                }            

                $store_id = trim($store_id, " OR ");
                $store_id .= ") ";
                
                
                
            }elseif(isset($options['store_id']) && trim($options['store_id'])!=='' && trim($options['store_id'])!=='0' && !is_null($options['store_id'])){
                $store_id = " AND find_in_set({$options['store_id']},t.store_id)";
            }  
            
            if(isset($options['id_user']) && is_array($options['id_user']) && count($options['id_user']) > 0){
                $userIds = implode(',', $options['id_user']);
                $user = " AND find_in_set(t.id_user,'$userIds')";
            }
            
        }else{
          $limit = " LIMIT 1000 ";          
        }        
 
        $select = "SELECT 
                    t.id,
                    t.id_user,
                    t.approved,
                    t.date as format_date_for_sort,
                    date_format(t.date,'%c/%d/%Y')as date,
                    fxGetStoreName(t.store_id)as sucursalName,
                    date_format(t.check_in,'%r')as check_in,
                    date_format(t.check_out,'%r')as check_out,
                    fxGetUserName(t.id_user)as userName,
                    timediff(t.check_out,t.check_in)as total_work,
                    ROUND(((time_to_sec(timediff(t.check_out,t.check_in)))/60)/60 ,2) as total
                    from timeclock t, users u 
                    WHERE t.id_user = u.id  
                    $date $user $store_id
                    ORDER BY u.last_name ASC, u.name ASC";
        //echo $select;exit;
        $result = $this->query($select);

        if ($result->num_rows > 0) {
           $result = $this->resultToArray($result);
           
           $data = null;
           foreach($result as $row){
               $data[$row['id_user']][] = $row;
           }
           
           return array(
               'data'=>$data
           );
        }
        return null;
    }
    
    public function getSales($options = null){
        $date = null;
        $store_id = null;
        $limit = null;        

        if($options != null){         
            $date = $this->createFilterFecha($options['startDate'], $options['endDate'], 'DATE(date)');
            if(isset($options['store_id']) && is_array($options['store_id']) && count($options['store_id']) > 0){
                $store_ids = implode(',', $options['store_id']);
                $store_id = " AND find_in_set(store_id,'$store_ids')";
            }elseif(isset($options['store_id']) && trim($options['store_id'])!=='' && trim($options['store_id'])!=='0' && !is_null($options['store_id'])){
                $store_id = " AND find_in_set(store_id,'{$options['store_id']}')";
            }  
            
        }else{
          $limit = " LIMIT 1000 ";          
        }        
        
        $query = "SELECT *,"
                . "date_format(date,'%c/%d/%Y')as dateFormated,"
                . "fxGetStoreName(store_id)as storeName, "
                . "(final_cash + debit_card  + credit_card + `check` + stamp )as total_sales,"
                . "status,"
                . "fxGetStatusName(status,'SalesRecord')as status_name "
                . "FROM sales_record "
                . "WHERE 1 = 1 "
                . "$store_id "
                . "$date "
                . "$limit "
                . "ORDER BY date ASC";
        
       // echo $query;exit;
        
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $data = null;
           foreach($result as $row){
               $data[$row['storeName']][] = $row;
           }
           
           return array(
               'data'=>$data
           );
        }
        return null;
    }
    
    public function getSpecialOrders($options){
        $date = null;
        $store_id = null;
        $limit = null;        

        if($options != null){         
            $date = $this->createFilterFecha($options['startDate'], $options['endDate'], 'date');
            if(isset($options['store_id']) && is_array($options['store_id']) && count($options['store_id']) > 0){
                $store_ids = implode(',', $options['store_id']);
                $store_id = " AND find_in_set(store_id,'$store_ids')";
            }elseif(isset($options['store_id']) && trim($options['store_id'])!=='' && trim($options['store_id'])!=='0' && !is_null($options['store_id'])){
                $store_id = " AND find_in_set(store_id,'{$options['store_id']}')";
            }  
            
        }else{
          $limit = " LIMIT 1000 ";          
        }        
        
        $query = "SELECT *,"
                . "date_format(date,'%c/%d/%Y')as dateFormated,"
                . "fxGetCustomerName(customer)as customerName, "
                . "phone,"
                . "ammount,"
                . "ammount_payments,"
                . "fxGetStoreName(store_id)as storeName "
                . "FROM special_orders WHERE 1 = 1 $store_id $date $limit ORDER BY date ASC";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
           $data = null;
           foreach($result as $row){
               $data[$row['storeName']][] = $row;
           }
           
           return array(
               'data'=>$data
           );
        }
        return null;
    }
    
    public function getInventoryTemplatePDF(){
        $settings = new SettingsRepository();
        $id_categories_of_products_in_physical_inventory = $settings->_get('id_categories_of_products_in_store_request');
        
        $query = "SELECT description,category,fxGetSizeDescription(size)as sizeName
                    FROM products 
                    WHERE status = '1' "
                 . "AND show_on_store_request = '1' "
                 . "AND find_in_set(category,'$id_categories_of_products_in_physical_inventory') "
                 . "ORDER BY description ASC";
        
        $result = $this->query($query);
        if($result->num_rows > 0){    
            $result = $this->resultToArray($result);
            $areaRepo = new AreaRepository();            
            $categoryArea = $areaRepo->getArrayCategoryArea();
            
            foreach($result as $row){               
                $area = $categoryArea[$row['category']];
                $array[$area['name']][] = $row;
            }           
            
            return array(
               'data'=>$array
            ); 
        }        
    }
    
    public function getPhysicalInventory($options){
        $tools = new Tools();
        $date = $this->createFilterFecha($options['startDate'], $options['endDate'],'i.date');
        
        $startDate = new DateTime($tools->setFormatDateToDB($options['startDate']));
        $endDate = new DateTime($tools->setFormatDateToDB($options['endDate']));
        
        $stringCASE = "";
        $rangeDays = array();
        if($startDate->format('Y-m-d') != $endDate->format('Y-m-d')){      
            while($startDate->format('Y-m-d') != $endDate->format('Y-m-d')){
                $stringCASE .= "CASE WHEN i.date = '{$startDate->format('Y-m-d')}' THEN quantity ELSE 0 END as '{$startDate->format('m/d/Y')}',";                
                $rangeDays[] = $startDate->format('m/d/Y');
                $startDate->add(new DateInterval('P1D'));            
            }
            /*EL IF deberia ser mientras startDate < endDate; pero por tiempo mejor lo deja asi, y agregue estas dos linea para que agregue cuando sea igual*/            
            $rangeDays[] = $startDate->format('m/d/Y');
            $stringCASE .= "CASE WHEN i.date = '{$startDate->format('Y-m-d')}' THEN quantity ELSE 0 END as '{$startDate->format('m/d/Y')}',";
        }else{            
            $rangeDays[] = $startDate->format('m/d/Y');
            $stringCASE = "CASE WHEN i.date = '{$startDate->format('Y-m-d')}' THEN quantity ELSE 0 END as '{$startDate->format('m/d/Y')}'";
        }
        
        $stringCASE = trim($stringCASE,',');        
        $query = "SELECT "
                . "p.code,"
                . "p.description as product,"
                . "fxGetSizeDescription(p.size)AS size,"
                . "fxGetCategoryDescription(p.category)AS category,"
                . "$stringCASE "
                . "FROM physical_inventory i "
                . "LEFT JOIN physical_inventory_details d ON i.id = d.id_physical_inventory "
                . "LEFT JOIN products p ON d.id_product = p.id "
                . "WHERE i.status = 1 "
                . "$date "
                . "ORDER BY category ASC, fxGetProductName(d.id_product) ASC";
        
        $result = $this->query($query);
        if($result->num_rows >0){
            return array(
               'data'=>$this->resultToArray($result),
               'rangeDays'=>$rangeDays
            );    
        }
    }
    
    public function getBakeryProduction($options){
        if($options['startDate'] == ''){return null;}
        $storeArray = array();
        $date = null;
        $area_bakery_production_id = null;  
        
        $date = $this->createFilterFecha($options['startDate'], $options['endDate'], 'r.delivery_date');   
        if(isset($options['area_bakery_production_id']) && is_array($options['area_bakery_production_id']) && count($options['area_bakery_production_id']) > 0){
               $area_bakery_production_ids = implode(',', $options['area_bakery_production_id']);
                $area_bakery_production_id = " AND find_in_set('$area_bakery_production_ids',p.area_bakery_production_id)";
        }elseif(isset($options['area_bakery_production_id']) && trim($options['area_bakery_production_id'])!=='' && trim($options['area_bakery_production_id'])!=='0' && !is_null($options['area_bakery_production_id'])){
            $area_bakery_production_id = " AND find_in_set('{$options['area_bakery_production_id']}',p.area_bakery_production_id)";
        }   
        
        $repo = new StoreRepository();
        $sucursales = $repo->getListSelectStores();
        
        $case = '';
        foreach($sucursales as $key => $name){
            $case .= "sum(CASE WHEN r.store_id = '{$key}' THEN d.quantity ELSE 0 END) as '{$key}',";
            $arraySucursales[$key] = $name;
        }
        
        $case = trim($case,',');
        $query = "SELECT "
                . "d.id as id_product, "
                . "p.description, "
                . "fxGetSizeDescription(d.id_size)as size, "
                . "$case,"
                . "p.masa "
                . "FROM store_request r "
                . "LEFT JOIN store_request_details d ON r.id = d.id_store_request "
                . "LEFT JOIN products p ON d.id_product = p.id "
                . "WHERE r.status = '1' "
                . "$date "
                . "$area_bakery_production_id "
                . "GROUP BY d.id_product "
                . "ORDER BY p.description ASC ";
        //echo $query;exit;
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $this->resultToArray($result);
            $areaName = null;
            if($options['area_bakery_production_id']!=''){
                $areaRepo = new AreaRepository();
                $areaProduccionPanaderia = $areaRepo->getAreaProduccionPanaderiaById($options['area_bakery_production_id']);
                $areaName = $areaProduccionPanaderia['name'];
            }
            
            /*Condicion para paÃ±o*/            
            $dataPanyo = array();
            $settings = new SettingsRepository();            
            if($settings->_get('id_production_for_panyo') == $options['area_bakery_production_id']){
                $masasList = $this->query("SELECT * FROM masas");
                $masasList = $this->resultToArray($masasList);
                foreach($masasList as $masa){$masasArray[$masa['id']] = $masa['description'];}
                
                foreach($result as $row){
                    $dataPanyo[$row['masa']][] = $row;
                    foreach($sucursales as $key => $name){
                       if(key_exists($key, $row)){$dataPanyo[$row['masa']]['total_pano_para_masa'] += $row[$key];}
                    }                    
                }
            }
            
             //echo "<pre>";var_dump($array);echo "</pre>";exit;
            /*COMENTARIOS*/
            $query = "SELECT "
                . "fxGetStoreName(r.store_id)as store_name,"
                . "r.comments "
                . "FROM store_request r "
                . "LEFT JOIN store_request_details d ON r.id = d.id_store_request "
                . "LEFT JOIN products p ON d.id_product = p.id "
                . "WHERE r.status = '1' "
                . "$date "
                . "$area_bakery_production_id "
                . "GROUP BY r.id "
                . "ORDER BY p.description ASC ";
        
            $result2 = $this->query($query);

            $commentsArray = null;
            if($result2->num_rows > 0){
                $result2 = $this->resultToArray($result2);
                foreach($result2 as $row){
                    $commentsArray[] = $row;
                }
            }
            
            return array(
                'data'=>$result,
                'comments'=>$commentsArray,
                'dataPanyo'=>$dataPanyo,
                'stores'=>$arraySucursales,
                'areaBakeryProduction'=>$areaName,
                'masasList'=>$masasArray
            );
        }else{
            null;
        }      
    }
    
    public function getDetailedBakeryOrders($options){
        $store_id = null;
        $area_id = null;
        $filterDate = null;
        
        $settings = new SettingsRepository();
        $area_id = $settings->_get('id_area_for_pasteles_vitrina');
        
        $filterDate = $this->createFilterFecha($options['startDate'], $options['endDate'], 's.delivery_date');   
        if(isset($options['store_id']) && $options['store_id'] != 0 && !is_null($options['store_id'])){
            $store_id = " AND s.store_id = '{$options['store_id']}'";
        }        
        
        $query = "SELECT DISTINCT(delivery_date) FROM store_request s WHERE 1 = 1 $filterDate $store_id ORDER BY delivery_date ASC";
        $result = $this->query($query);
        if($result->num_rows > 0){
            $requestDates = $this->resultToArray($result);
            $stringRequestDates = '';
            $stringRequestDates2 = '';
            
            foreach($requestDates as $key => $date){
                $stringRequestDates .="CASE WHEN delivery_date = '{$date['delivery_date']}' THEN requested ELSE 0 END as '{$date['delivery_date']}_requested',";
                $stringRequestDates .="CASE WHEN delivery_date = '{$date['delivery_date']}' THEN received ELSE 0 END as '{$date['delivery_date']}_received',";
            }  
            
            foreach($requestDates as $key => $date){
                $dateFormated = date_create($date['delivery_date']);
                $dateFormated = date_format($dateFormated,'m/d/Y'); /*Aplico formato a la fecha, solo es necesario hacerlo aqui*/
                $stringRequestDates2 .="MAX(`{$date['delivery_date']}_requested`) as '{$dateFormated}_requested',";
                $stringRequestDates2 .="MAX(`{$date['delivery_date']}_received`) as '{$dateFormated}_received',";
            }  
            
            $stringRequestDates = trim($stringRequestDates, ',');
            $stringRequestDates2 = trim($stringRequestDates2, ',');
            
          $query = "SELECT product_id,product,$stringRequestDates2 "
                    . "FROM (SELECT  product_id,product,"
                    . "$stringRequestDates "
                . "FROM ("
                    . "SELECT "
                    . "p.id as product_id,"
                    . "p.description as product, "
                    . "s.delivery_date,"
                    . "SUM(IFNULL(d.quantity,0))as requested,"
                    . "SUM(IFNULL(ssd.received,0))as received "
                    . "FROM store_request s "
                    . "LEFT JOIN store_request_details d ON s.id = d.id_store_request "
                    . "LEFT JOIN shipment_store_requests ss ON ss.id_store_request = s.id "
                    . "LEFT JOIN shipment_store_requests_details ssd ON ssd.id_shipment = ss.id "
                    . "LEFT JOIN products p ON d.id_product = p.id "
                    . "WHERE s.status = '1' "
                    . "$filterDate "
                    . "$store_id "
                    . " AND s.area_id != $area_id "
                    . "GROUP BY d.id_product,s.delivery_date "
                    . "ORDER BY s.delivery_date ASC"
                . ")as t )as t2 GROUP BY product_id";
        
            $result = $this->query($query);

            if($result->num_rows > 0){
                return array(
                    'data'=>$this->resultToArray($result),
                    'requestDates'=>$requestDates
                );
            }
        }
       return null;
    }
    
    public function getSalesToStore($options){
        $date = null;
        $store_id = null;
        $limit = null;        

        if($options != null){         
            $date = $this->createFilterFecha($options['startDate'], $options['endDate'], 'DATE(date)');
            if(isset($options['store_id']) && is_array($options['store_id']) && count($options['store_id']) > 0){
                $store_ids = implode(',', $options['store_id']);
                $store_id = " AND find_in_set(to_store,'$store_ids')";
            }elseif(isset($options['store_id']) && trim($options['store_id'])!=='' && trim($options['store_id'])!=='0' && !is_null($options['store_id'])){
                $store_id = " AND find_in_set(to_store,'{$options['store_id']}')";
                $storeRepo = new StoreRepository();
                $storeData = $storeRepo->getById($options['store_id']);
            }  
            
        }else{
          $limit = " LIMIT 1000 ";          
        }        
        
        $query = "SELECT "
                . "p.description,"
                . "SUM(IFNULL(sd.quantity,0))as quantity,"
                . "SUM(IFNULL(sd.received,0))as received,"
                . "IFNULL(p.cost,0)as sale_price "
                . "FROM shipment_store_requests s "
                . "LEFT JOIN shipment_store_requests_details sd ON sd.id_shipment = s.id "
                . "LEFT JOIN products p ON  sd.id_product = p.id  "
                . "WHERE s.status != 4 "
                . "$date "
                . "$store_id "
                . "$limit "
                . "GROUP BY sd.id_product";

        $result = $this->query($query);
        
        if($result->num_rows >0){
            $result = $this->resultToArray($result);
            return array(
                'data'=>$result,
                'storeName'=>$storeData['name']
            );
        }
        
        return null;
    }
    
     public function getSalesByStore($options){
        $date = null;
        $store_id = null;
        $limit = null;        

        if($options != null){         
            $date = $this->createFilterFecha($options['startDate'], $options['endDate'], 'DATE(v.fecha)');
            if(isset($options['store_id']) && is_array($options['store_id']) && count($options['store_id']) > 0){
                $store_ids = implode(',', $options['store_id']);
                $store_id = " AND find_in_set(v.idSucursal,'$store_ids')";
            }elseif(isset($options['store_id']) && trim($options['store_id'])!=='' && trim($options['store_id'])!=='0' && !is_null($options['store_id'])){
                $store_id = " AND find_in_set(v.idSucursal,'{$options['store_id']}')";
                $storeRepo = new StoreRepository();
                $storeData = $storeRepo->getById($options['store_id']);
            }  
            
        }else{
          $limit = " LIMIT 1000 ";          
        }        
        
        $query = "SELECT v.*, "
                . "fxGetStoreName(v.idSucursal)as store_name,"
                . "DATE_FORMAT(v.fecha,'%m/%d/%Y')as formated_date "
                . "FROM ventas v "
                . "WHERE v.status != 3 "
                . "$date "
                . "$store_id "
                . "$limit ";

        $result = $this->query($query);
        
        if($result->num_rows >0){
            $result = $this->resultToArray($result);
            
            /*detalles de venta*/
            $queryDetalles = "SELECT vd.*, "
                . "vd.precio_sin_impuestos * vd.cantidad as importe,"
                . "vd.descuento_item_monto + vd.descuento_orden_monto as descuento,"
                . "v.num_venta,"
                . "fxGetStoreName(v.idSucursal)as store_name,"
                . "DATE_FORMAT(v.fecha,'%m/%d/%Y')as formated_date "
                . "FROM ventas v "
                . "LEFT JOIN ventasdetalles vd ON vd.ticket = v.ticket "
                . "WHERE v.status != 3 "
                . "$date "
                . "$store_id "
                . "$limit ";
             
            $resultDetalles = $this->query($queryDetalles);
            if($resultDetalles->num_rows > 0){
                $resultDetalles = $this->resultToArray($resultDetalles);                
                $arrayDetalles = array();
                
                foreach($resultDetalles as $detalle){
                    $total = round($detalle['importe'],2) - round($detalle['descuento'],2) + round($detalle['impuestos_monto'],2);
                    $detalle['total'] = $total;
                    $arrayDetalles[] = $detalle;                    
                }
            } 
            
            return array(
                'data'=>$result,
                'details'=>$arrayDetalles,
                'storeName'=>$storeData['name']                
            );
        }
        
        return null;
    }
    
    public function getReviewPayroll($options){        
        $data = $this->getTimeClock($options);        
        $groupByEmployee = array();
        
        if($data['data']){
            $data = $data['data'];
            foreach($data as $user_id => $info){
                foreach($info as $row){
                     if(key_exists($user_id, $groupByEmployee)){
                        $groupByEmployee[$row['id_user']]['total'] += round($row['total'],2);
                    }else{
                        $groupByEmployee[$row['id_user']] = array('name'=>$row['userName'],'storeName'=>$row['sucursalName'],'total'=>round($row['total'],2));
                    }              
                }                
            }
           
            return array(
                'data'=>$data,
                'groupByEmployee'=>$groupByEmployee
            );
        }
        
        return null;
    }
    
    public function getInvoices($options){
        $invoiceRepo = new InvoiceRepository();
        $list = $invoiceRepo->getListInvoice($options);
        
        if($list){
            return array('data'=>$list);
        }
        
        return null;
    }
    
    public function createFilterFecha($fechaInicio,$fechaFin,$campoFecha = null ){
        $fecha = null;
        $tools = new Tools();
        if($fechaInicio!=null){
            $fechaInicio = $tools->setFormatDateToDB($fechaInicio);
            if($fechaFin!=null){
                $fechaFin = $tools->setFormatDateToDB($fechaFin);
                $fecha .=" AND $campoFecha BETWEEN '{$fechaInicio}' AND '{$fechaFin}' ";
            }else{
                $fecha .=" AND $campoFecha BETWEEN '{$fechaInicio}' AND '{$fechaInicio}' ";
            }
        }elseif($fechaFin!=null){
            $fecha .=" AND $campoFecha BETWEEN '{$fechaFin}' AND '{$fechaFin}' ";
        }
        
        return $fecha;
    }  
}