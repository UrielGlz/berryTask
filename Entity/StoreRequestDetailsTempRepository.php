<?php

class StoreRequestDetailsTempRepository extends EntityRepository {



    private $table = 'store_request_details_';

    

    public function __construct(){

        $login = new Login();

        $this->table = $this->table.$login->getId();

    }    

    

    public function getTableName(){

        return $this->table;

    }

    

    // Guarda en temporal

    public function save(array $data, $table = null) {

        if(trim($data['weight']) == ''){$data['weight'] = '0';}

        if(isset($data['idDetalleTemp']) && trim($data['idDetalleTemp'])!= ''){

            $result = $this->getById($data['idDetalleTemp']);

            if($result){

                $id = $data['idDetalleTemp'];

                unset($data['idDetalleTemp']);

                return $this->updateTemp($id, $data);

            }

         }

        

        unset($data['idDetalleTemp']);

        return $this->saveTemp($data);

    }

    

    public function saveTemp($data){ 

        return  parent::save($data, $this->table);          

    }

    

    public function updateTemp($id,$data){

        return $this->update($id, $data,$this->table);

    }       

        

    public function delete($id, $table = null) {

        return parent::delete($id, $this->table);

    }   



    // Guarda en tabla store_request_details

    public function saveDetalles($idStoreRequest,$tokenForm){

        $query = "SELECT * FROM $this->table WHERE token_form = '$tokenForm'";

        $result = $this->query($query);

        if($result->num_rows > 0){            

            $result = $this->resultToArray($result);

            foreach($result as $data){

                unset($data['token_form'],$data['id'],$data['id_detalle']); 

                $data['id_store_request'] = $idStoreRequest;

                parent::save($data, 'store_request_details');

                

            }            

        }

        return true;

    }

    

    public function updateDetalles($idStoreRequest,$tokenForm){

        $query = "SELECT id FROM store_request_details WHERE id_store_request = '$idStoreRequest'";

        $result = $this->query($query);

        $detallesOrigin = null;    

        if($result->num_rows > 0){

            $detallesOrigin = $this->resultToArray($result);

        }



        $query = "SELECT * FROM $this->table WHERE token_form = '$tokenForm'";

        $result = $this->query($query);

        

        if($result->num_rows > 0){

            $idsDetalles = array();        

            $result = $this->resultToArray($result);

            foreach($result as $data){

                $idDetalle = $data['id_detalle'];

                unset($data['token_form'],$data['id'],$data['id_detalle']);

                $data['id_store_request'] = $idStoreRequest;

                

                if(!$idDetalle){

                    parent::save($data, 'store_request_details');

                    

                }else{

                    #contiene los idDetalles de la compra original que se mantendran, lo que no esten aqui se eliminaran.

                    $idsDetalles[] = $idDetalle;

                    parent::update($idDetalle,$data, 'store_request_details');

                }                       

            }

            #Se eliminan ids de tabla comprasdetalls que ya no estan en $idsDetalles (este array contiene los ids que permanececieron en la compra despues del update)                

            if($detallesOrigin){

                $entityRepository = new EntityRepository();

                foreach ($detallesOrigin as $detalle){

                    if(!in_array($detalle['id'], $idsDetalles)){

                        $entityRepository->delete($detalle['id'], 'store_request_details');

                    }

                }

            }          

        }

        return true;

    } 

    

    public function truncate($tokenForm){

         $query = "DELETE  FROM ".$this->table." WHERE token_form = '$tokenForm'";

        $result = $this->query($query);

        

        if($result){

            return true;

        }

        

        return null;

    }

    

    public function setStoreRequestDetallesById($idStoreRequest,$tokenForm){

        $query = "INSERT INTO ".$this->table." (token_form,id_detalle,id_store_request,id_product,id_size,last_inventory,pending_to_receive,quantity,received)

                    SELECT '$tokenForm',id,id_store_request,id_product,id_size,last_inventory,pending_to_receive,quantity,received

                    FROM store_request_details WHERE id_store_request = '$idStoreRequest'";

        

        $result = $this->query($query);

        if($result){

            return true;

        }

        

        return null;

    }   

    

    public function setStoreRequestDetallesForArea($options){

        $tokenForm = $options['token_form'];

        $store_id = $options['store_id'];

        $area_id = $options['area_id'];

        

        $repoArea = new AreaRepository();

        $categoryId = $repoArea->getCategoryByAreaId($area_id);

        

        /*Se usa para listar todas loas productos que pueden ser mostrado en todos los pedidos (panaderida, pasteleria etc)*/

        /*$settings = new SettingsRepository();

        $id_categories_of_products_in_store_request = $settings->_get('id_categories_of_products_in_store_request');*/

        

        $query = "DELETE FROM $this->table WHERE token_form = '$tokenForm'";

        $this->query($query);

        

        /*Obtener ultimo inventario fisico */

        $query = "SELECT id FROM physical_inventory WHERE store_id = '$store_id' ORDER BY creado_fecha DESC LIMIT 1";

        $result = $this->query($query);

        

        if($result->num_rows > 0){

            $result = $result->fetch_object(); 

            $idInventory = $result->id;

        }else{

            $idInventory = 0;

        }

        

         /*Obtener ultimo pedido */

        $query = "SELECT id FROM store_request WHERE store_id = '$store_id' AND area_id = '$area_id' ORDER BY creado_fecha DESC LIMIT 1";

        $result = $this->query($query);

        

        if($result->num_rows > 0){

            $result = $result->fetch_object(); 

            $idStoreRequest = $result->id;

        }else{

            $idStoreRequest = 0;

        }

        

        $query = "INSERT INTO ".$this->table." (token_form,id_product,id_size,last_inventory,pending_to_receive)

                    SELECT '$tokenForm',p.id,p.size,i.quantity,(s.quantity - IFNULL(received,0))

                    FROM products p 

                    LEFT JOIN physical_inventory_details i ON p.id = i.id_product AND i.id_physical_inventory = '$idInventory'

                    LEFT JOIN store_request_details s ON p.id = s.id_product AND s.id_store_request = '$idStoreRequest'

                    WHERE p.status = '1' "

                 . "AND p.show_on_store_request = '1' "

                 . "AND find_in_set(p.category,'$categoryId') "

                 . "ORDER BY p.description ASC";

        
       
        $result = $this->query($query);

        if($result){

            return true;

        }

        

        return null;

    }   

}

