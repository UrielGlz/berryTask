<?php

/**

 * Description of Ajax

 *

 * @author carlos

 */

class ReportAjax extends EntityRepository {

    

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

    

    public function getFiltersReport($options){

        $form = new ReportForm();        

        

        return array(

            'response'=>true,

            'filters'=>$form->getStringFiltersForm($options['report'])

        );

    }

    //UG ADD Query Get Mermas
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

}