<?php

/**

 * Description of Ajax

 *

 * @author carlos

 */

class SalesRecordAjax extends SalesRecordRepository {

    

    public $flashmessenger = null;

    

    public function __construct() {

        if(!$this->flashmessenger instanceof FlashMessenger){

            $this->flashmessenger = new FlashMessenger();

        }

    }

    

    public function getResponse($request, $options) {

        return $this->$request($options);

    }

    

    public function _getTranslation($text){

        $translator = new Translator();

        return $translator->_getTranslation($text);

    }

    

    public function getTranslation($options){

        $msj = $options['msj'];

        

        return array(

            'response'=>true,

            'translation'=>$this->_getTranslation($msj)

                );

    }    

    

    public function setFormToInsert($options){

        $form = new Form();

        $form->setTokenForm();

        

        $_salesRecordExpenseDetailsTemp = new SalesRecordExpesnsesDetailsTemp();

        $_salesRecordExpenseDetailsTemp->setSalesRecordExpenseDetallailsForNew($form->getTokenForm());

        $expenseList = $this->getListSalesRecordExpensesDetalles($form->getTokenForm());

        

        return array(

            'response'=>true,

            'tokenForm'=>$form->getTokenForm(),

            'expensesDetails'=>$expenseList['salesRecordExpenseDetails']

        );

    }

    

    public function getDataToEdit($options){

        $tools = new Tools();

        $salesRecordRepo = new SalesRecordRepository();

        $data = $salesRecordRepo->getById($options['id']);

        $data['action'] = 'edit';

        

        $form = new Form();

        $form->setTokenForm();

        $data['token_form'] = $form->getTokenForm();

        $this->setSalesRecordExpenseDetallesById($options['id'], $form->getTokenForm());

        $expenseList = $this->getListSalesRecordExpensesDetalles($form->getTokenForm());

        

        return array(

            'response'=>true,

            'salesRecordData'=>$data,

            'expensesDetails'=>$expenseList['salesRecordExpenseDetails'],

            'totalExpenses'=>$expenseList['totalExpenses']

        );

    }

    

    public function deleteSalesRecord($options){

        $salesRecordRepo = new SalesRecordRepository();

        

        if($salesRecordRepo->delete($options['id'])){

            $this->flashmessenger->addMessage(array('success'=>'Registro de venta se elimino exitosamente.'));                

        }

        

        return array(

            'response'=>true

        );

    }

    

    public function allowEditSalesRecord($options){

        $entityRepository = new EntityRepository();

        $entityRepository->update($options['idSalesRecord'], array('allow_edit'=>'1'), 'sales_record');

        return array(

            'response'=>true,

        );

    }

    

    /*EXPENSES DETAILS*/

    public function listSalesRecordExpensestDetalles($detalles){

        $listDetalles = "";

        $totalExpense = 0;

        $tokenForm = null;

        

        foreach($detalles as $detalle){

            $tokenForm = $detalle['token_form'];

            if(!is_null($detalle['amount'])){$totalExpense += $detalle['amount']; }         

            

            $listDetalles .= "<tr>"             

                ."<td class='text-left'>".$detalle['category_expense_name']."</td>"

                ."<td class='text-right'><input id='comments_{$detalle['id']}' name='comments_{$detalle['id']}' type='text' value='{$detalle['comments']}' class='_salesRecordExpense' style='width:100%' /></td>"

                ."<td class='text-right'><input id='amount_{$detalle['id']}' name='amount_{$detalle['id']}' type='text' value='{$detalle['amount']}' class='_salesRecordExpense text-right' style='width:100%' /></td>"

                ."<td class='text-right'><a class='btn btn-default' data-id='{$detalle['id']}'><i class='fa fa-eraser'></i></a></td>"                

                ."</tr>";

        }

        

        return array('listDetalles'=>$listDetalles,

                     'totalExpenses'=>$totalExpense);

    }

    

    public function getListSalesRecordExpensesDetalles($tokenForm){

        $manifestDetalles = $this->getSalesRecordExpenseDetalles($tokenForm);

        $detalles = $this->listSalesRecordExpensestDetalles($manifestDetalles);



            $json = array(

                'response' => true,

                'salesRecordExpenseDetails' => $detalles['listDetalles'],

                'totalExpenses'=>$detalles['totalExpenses']

            );       

            return $json;

    }

    

    public function updateSalesRecordExpenseAmount($options){

        if(!isset($options['sales_recotd_expense'])){return array('response'=>true);}

        $data = array();

        foreach($options['sales_recotd_expense'] as $row){

            $data[$row['name']] = $row['value'];

        }   

       

        $dataUpdate = array(); 

        foreach($data as $key => $value){

            $key = explode('_',$key);

            $dataUpdate[$key[1]][$key[0]] = $value;

        }



        $repository = new SalesRecordExpesnsesDetailsTemp();   

        foreach ($dataUpdate as $id => $values){

            $stringSet = null;

            foreach($values as $field => $value){

                if($value == '_NULL' || is_null($value) || trim($value)==''){

                    $stringSet .= "$field = NULL,";

                }else{

                    $stringSet .= "$field = '$value',";

                }  

            }

            

            $stringSet = trim($stringSet, ',');   

            parent::query("UPDATE ".$repository->getTableName()." SET $stringSet WHERE id = '$id'");

        }       

        

        return array(

            'response'=>true

        );

    }

    /*END EXPENSES DETAILS*/


    //UG ADD Query Get Trafico
    public function getTrafico($options){
        $salesRecord = new SalesRecordRepository();

        $salesData = $salesRecord->getTraficoByStoreAndDateRange($options['start'],$options['end'],$options['group']);  
        $totalSales = 0;        
        $existSales = null;
        $data = null;
        if($salesData){
            $existSales = true;
            foreach($salesData as $sale_data){
                $data[$sale_data['store_name']] = $sale_data;
            }
        }    

        $totalSales = 0;
        $chartData = array();
        if($data){            
            foreach($data as $sale){
               // $totalSales += $sale['total_sales'];
                $chartData[$sale['store_name']] = number_format($sale['_trafico'],2,'.','');
            }        
        } 
        if($existSales){
            return array(
               'response'=>true,
               'caption'=>$this->_getTranslation('Trafico')." ".$this->_getTranslation('de')." ".$options['start']." ".$this->_getTranslation('a')." ".$options['end'],
               //'totalSales'=>number_format($totalSales,2,'.',''),
               'chartData'=>$chartData 
           );
       }    
    }

    //UG ADD Mermas
    public function getMermas_s($options){
        $salesRecord = new SalesRecordRepository();

        $salesData = $salesRecord->getMermasByStoreAndDateRange($options['start'],$options['end'],$options['group']);  
        $totalSales = 0;        
        $existSales = null;
        $data = null;
        if($salesData){
            $existSales = true;
            foreach($salesData as $sale_data){
                $data[$sale_data['store_name']] = $sale_data;
            }
        }    

        $totalSales = 0;
        $chartData = array();
        if($data){            
            foreach($data as $sale){
               // $totalSales += $sale['total_sales'];
                $chartData[$sale['store_name']] = number_format($sale['_mermas'],2,'.','');
            }        
        } 
        if($existSales){
            return array(
               'response'=>true,
               'caption'=>$this->_getTranslation('Trafico')." ".$this->_getTranslation('de')." ".$options['start']." ".$this->_getTranslation('a')." ".$options['end'],
               //'totalSales'=>number_format($totalSales,2,'.',''),
               'chartData'=>$chartData 
           );
       }
       return array('response'=>false);    
    }
   
    public function getTotalSales($options){

        $salesRecord = new SalesRecordRepository();

        $salesData = $salesRecord->getSalesByDateRange($options['start'],$options['end']);        

        $salesDataPOS = $salesRecord->getSalesByDateRangeFromSalesPOS($options['start'],$options['end']);

        

        $existSales = null;

        $data = null;

        if($salesData){

            $existSales = true;

            foreach($salesData as $sale_data){

                $data[$sale_data['store_name']] = $sale_data;

            }

        }

        

        if($salesDataPOS){

            $existSales = true;

            foreach($salesDataPOS as $sale_data_pos){

                $data[$sale_data_pos['store_name']] = $sale_data_pos;

            }

        }



        $string = '';

        $totalSales = 0;

        $chartData = array();

        if($data){            

            foreach($data as $sale){

                $totalSales += $sale['total_sales'];

                $chartData[$sale['store_name']] = number_format($sale['total_sales'],2,'.','');

                $string .= "<tr>";

                $string .= "<td class='text-center'>{$sale['store_name']}</td>";

                $string .= "<td class='text-right'>$".number_format($sale['total_sales'],2)."</td>";

                $string .= "</tr>";

            }        

        }        

        

        if($existSales){

             return array(

                'response'=>true,

                'caption'=>$this->_getTranslation('Ventas')." ".$this->_getTranslation('de')." ".$options['start']." ".$this->_getTranslation('a')." ".$options['end'],

                'salesData'=>$string,

                'totalSales'=>number_format($totalSales,2,'.',''),

                'chartData'=>$chartData 

            );

        }      

        

        return array('response'=>false);

    }

    

     public function getTotalSalesByStoreId($options){

        $salesRecord = new SalesRecordRepository();

        $salesData = $salesRecord->getSalesByStoreIdByDateRange($options['start'],$options['end']);

        

        $string = '';

        $totalSales = 0;

        $chartData = array();

        if($salesData){

            foreach($salesData as $sale){

                $totalSales += $sale['total_sales'];

                $chartData[$sale['dayname']] = number_format($sale['total_sales'],2,'.','');

                $string .= "<tr>";

                $string .= "<td class='text-center'>{$sale['dayname']} <small>{$sale['date']}</small></td>";

                $string .= "<td class='text-right'>$".number_format($sale['total_sales'],2)."</td>";

                $string .= "</tr>";

            }

            

            return array(

                'response'=>true,

                'caption'=>$this->_getTranslation('Ventas')." ".$this->_getTranslation('de')." ".$options['start']." a ".$options['end'],

                'salesData'=>$string,

                'totalSales'=>number_format($totalSales,2,'.',''),

                'chartData'=>$chartData 

            );

        }   

        

        return array('response'=>false);

    }

}