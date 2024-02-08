<?php 
$controller = 'PhysicalInventory';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}

include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$_form = new PhysicalInventoryForm();
$_storeRequest = new PhysicalInventoryRepository();

switch($action){
    case 'insert':        
        $_form->setTokenForm($_POST['token_form']);
        $_form->populate($_POST);  
        $_storeRequest->setOptions($_POST);
        if ($_form->isValid()) {             
            if ($_storeRequest->save($_storeRequest->getOptions())) {                
                $_storeRequestNo = $_storeRequest->getLastInsertId();
                $printPhysicalInventory = "<a href=\"\" onclick=\"javascript: void window.open('/Controller/PhysicalInventory.php?action=export&format=pdf&flag=in&id=$_storeRequestNo','','width=700,height=500,status=1,scrollbars=1,resizable=1')\">".$_translator->_getTranslation('Imprimir')."</a>";  
                $flashmessenger->addMessage(array(
                    "success"=>$_translator->_getTranslation("Inventario fisico")." #$_storeRequestNo ".$_translator->_getTranslation("se ha registrado exitosamente.")));
                
                header("Location: PhysicalInventory.php?action=edit&id=$_storeRequestNo");
            }else{                
                $vista = 'PhysicalInventory.php';
                include $root . '/View/Template.php';                
            }
        } else {
            $vista = 'PhysicalInventory.php';
            include $root . '/View/Template.php';
        }
        break;
        
    case 'list':          
        $searchFilter = null;
        if(isset($_POST['search'])){$searchFilter = $_POST;}
        $_listPhysicalInventorys = $_storeRequest->getListPhysicalInventory($searchFilter);
        
        $vista = 'PhysicalInventoryList.php';
        include $root . '/View/Template.php';
    break;
        
    case 'export': 
        switch($_GET['format']){
            case 'excel':
                switch($_GET['flag']){
                    case 'search':
                        $_storeRequest->resultSearchToExcel($_GET);
                        break;
                }       
                break;
            
            case 'pdf':
                switch($_GET['flag']){
                    case 'store_request':
                        $pdf = new PhysicalInventoryPDF($id);
                        break;
                    }   
                break;
        }
         
        break;
    
    case 'edit': 
        $login = new Login();
        
        if($_GET){
            $_storeRequestData = $_storeRequest->getById($id);
            
            if($login->getRole() != '1' &&  $login->getStoreId() != $_storeRequestData['store_id']){
                header("Location: PhysicalInventory.php?action=list");
            } 
            
            /*Si es usuario Sucursal y esta intentado editar un PhysicalInventory que no es de su sucursal ; lo envia a listado*/
            if($login->getRole() == '14' && $_storeRequestData['store_id'] !== $login->getStoreId()){
                header("Location: PhysicalInventory.php?action=list");
            }
            
            $_storeRequest->crearTablaDetallesForUser();
            $_storeRequest->setPhysicalInventoryDetallesById($id,$_form->getTokenForm());            
        }
        
        if($_POST){            
            $_form->setTokenForm($_POST['token_form']);
            $_storeRequestData = $_POST;
        }
        
        $_form->setActionController('edit');
        $_form->setId($id);
        $_form->populate($_storeRequestData);       
        $_form->setReadOnlydElements(array('store_id','area_id'));
        $_disabled = null;
        
        if($_storeRequestData['status'] == '2'){
            $_disabled = true;
            $_form->disabledAllElements();
            $_form->hideElement(array('terminar'));
        }
        
        $_storeRequest->setOptions($_storeRequestData);
        if(isset($_POST['id'])){
            if($_form->isValid()){                
                $result = $_storeRequest->update($id,$_storeRequest->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Inventario fisico se actualizo exitosamente.'));
                    header("Location: PhysicalInventory.php?action=edit&id=$id");
                }else{
                     $vista = 'PhysicalInventory.php';
                    include $root . '/View/Template.php';                    
                }       
            }else{
                $vista = 'PhysicalInventory.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'PhysicalInventory.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete':
        /*Si es usuario Sucursal y esta intentado eliminar un PhysicalInventory que no es de su sucursal ; lo envia a listado*/
        $_storeRequestData = $_storeRequest->getById($id);
        if($login->getRole() == '14' && $_storeRequestData['store_id'] !== $login->getStoreId()){
            header("Location: PhysicalInventory.php?action=list");
        }
        
        if($_storeRequest->delete($id)){
            $flashmessenger->addMessage(array('success'=>'Inventario fisico se cancelo exitosamente.'));
        }
            
        header('Location: PhysicalInventory.php?action=list');
        break;
        
    case 'ajax':
        $ajaxPhysicalInventory = new PhysicalInventoryAjax();
        $json = $ajaxPhysicalInventory->getResponse($_POST['request'],$_POST);
        
        echo json_encode($json);
        break;       
    
    default:      
        $_storeRequest->crearTablaDetallesForUser();
        $physicalInventoryDetailsTemp = new PhysicalInventoryDetailsTempRepository();
        $physicalInventoryDetailsTemp->setPhysicalInventoryDetallesForNew($_form->getTokenForm());
        
        $vista = 'PhysicalInventory.php';
        include $root.'/View/Template.php';
        break;
}