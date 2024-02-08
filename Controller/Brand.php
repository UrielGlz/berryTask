<?php 
$controller = 'Brand';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new BrandForm();
$_brand = new BrandRepository();
$_listBrands = $_brand->getListBrands();
$_noValid = null;

switch($action){
    case 'insert': 
        $form->populate($_POST);
        if($form->isValid()){            
            $_brand->setOptions($_POST);
            $result = $_brand->save($_brand->getOptions());
            if($result){
                $flashmessenger->addMessage(array('success'=>'Marca registrada exitosamente.'));
                header('Location: Brand.php');
            }else{
                $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
            }
        }else{
            $_noValid = true;
            $vista = 'Brand.php';
            include $root.'/View/Template.php';
        }
        break;
   
    case 'edit':
        if($_GET){
            $_brandData = $_brand->getById($id);
        }
        if($_POST){$_brandData = $_POST;}
        
        $form->setActionController('edit');
        $form->setId($id);
        $form->populate($_brandData);
        
        if(isset($_POST['id'])){
            if($form->isValid()){
                $_brand->setOptions($_brandData);
                $result = $_brand->update($id,$_brand->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Marca actualizado exitosamente.'));
                    header("Location: Brand.php");
                }else{
                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
                }       
            }else{
                $_noValid = true;
                $vista = 'Brand.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'Brand.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete': 
        if(!$_brand->isUsedInRecord($id)){
            if($_brand->delete($id)){
                $flashmessenger->addMessage(array('success'=>'Marca eliminada satisfactoriamente.'));
            }
        }else{
             $flashmessenger->addMessage(array('success'=>'Marca no puede ser eliminada esta siendo utilizada en almenos un registro.'));
        }
        
        header("Location: Brand.php");
        break;
        
     case 'ajax':
        $ajaxBrand = new BrandAjax();
        $json = $ajaxBrand->getResponse($_POST['request'], $_POST);
        echo json_encode($json);
        break;
    
    default:       
        $vista = 'Brand.php';
        include $root.'/View/Template.php';
}