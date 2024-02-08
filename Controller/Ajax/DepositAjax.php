<?php
class DepositAjax extends DepositRepository {    
    public function __construct() {
        parent::__construct();
    }

    public function getResponse($request, $options) {
        return $this->$request($options);
    }

    public function setDepositDetails(array $options) {
        $data = array();
        foreach($options['options'] as $row){
            $data[$row['name']] = $row['value'];           
        }

        $this->insertDetalle($data);
        $compraDetalles = $this->getDepositDetails($data['token_form']);
        $detalles = $this->listDepositDetalles($compraDetalles);

        $json = array(
            'response' => true,
            'depositDetails' => $detalles['listDetails'],
            'total'=>$detalles['total'],
        );

        return $json;        
    }
    
    public function listDepositDetalles($detalles){
        $listDetalles = "";
        $total = 0;
        
        foreach($detalles as $detalle){            
            $total += $detalle['sale_total_cash'];     
            $id = $detalle['id'];                          
            $array = json_encode($detalle);
            
            $listDetalles .= "<tr>  
                <td class='text-left'>                       
                    <a class='btn btn-sm btn-default' onclick='setDetailDepositToEdit($array);'><i class='fa fa-pencil'></i></a>
                    <a class='btn btn-sm btn-danger' onclick='deleteDepositDetalles($id);'><i class='fa fa-minus'></i></a>
                </td>
                <td class='text-center'>".$detalle['sale_date']."</td>                    
                <td class='text-center'>".$detalle['sale_date_final']."</td>   
                <td class='text-center'>".$detalle['sale_comments']."</td>
                <td class='text-right'>".number_format((double)$detalle['sale_total_cash'],2)."</td>
                </tr>";
        }       
        
        return array('listDetails'=>$listDetalles,
                     'total'=>$total);
    }
    
    public function getListDepositDetails($options){
        $tokenForm = $options['token_form'];
        
        $compraDetalles = $this->getDepositDetails($tokenForm);
        $detalles = $this->listDepositDetalles($compraDetalles);
            
            $json = array(
                'response' => true,
                'depositDetails' => $detalles['listDetails'],
                'total'=>$detalles['total'],
            ); 
            
            return $json;
    }

    public function deleteDetalles(array $options){
        $id = $options['id'];
        $repository = new DepositDetailsTempRepository();
        $currentData = $repository->getById($id);
        
        if($repository->delete($id)){
            $response = true;
            $msj = 'Detalle eliminado correctamente.';
        }else{
            $response = null;
            $msj = "No se pudo eliminar detalle. Intente nuevamente.";
        }
        
        return $this->getListDepositDetails(array('token_form'=>$currentData['token_form']));
    }   
}