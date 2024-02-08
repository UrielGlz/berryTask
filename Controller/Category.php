<?php 
$controller = 'Category';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new CategoryForm();
$_category = new CategoryRepository();
$_listCategories = $_category->getListCategories();

switch($action){
    case 'insert': 
        $form->populate($_POST);
        if($form->isValid()){            
            $_category->setOptions($_POST);
            $result = $_category->save($_category->getOptions());
            if($result){
                $flashmessenger->addMessage(array('success'=>'Categoria registrada exitosamente.'));
                header('Location: Category.php');
            }else{
                $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
            }
        }else{
            $_noValid = true;
            $vista = 'Category.php';
            include $root.'/View/Template.php';
        }
        break;
   
    case 'edit':
        if($_GET){
            $_categoryData = $_category->getById($id);
        }
        if($_POST){$_categoryData = $_POST;}
        
        $form->setActionController('edit');
        $form->setId($id);
        $form->populate($_categoryData);
        
        if(isset($_POST['id'])){
            if($form->isValid()){
                $_category->setOptions($_categoryData);
                $result = $_category->update($id,$_category->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Categoria actualizado exitosamente.'));
                    header("Location: Category.php");
                }else{
                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
                }       
            }else{
                $_noValid = true;
                $vista = 'Category.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'Category.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete': 
        if(!$_category->isUsedInRecord($id)){
            if($_category->delete($id)){
                $flashmessenger->addMessage(array('success'=>'Categoria eliminada satisfactoriamente.'));
            }
        }else{
             $flashmessenger->addMessage(array('success'=>'Categoria no puede ser eliminada esta siendo utilizada en almenos un registro.'));
        }
        
        header("Location: Category.php");
        break;
        
     case 'ajax':
        $ajaxCategory = new CategoryAjax();
        $json = $ajaxCategory->getResponse($_POST['request'], $_POST);
        echo json_encode($json);
        break;
    
    default:       
        
        $vista = 'Category.php';
        include $root.'/View/Template.php';
}