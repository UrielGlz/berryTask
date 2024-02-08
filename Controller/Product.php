<?php 

$controller = 'Product';

$action = '';

if(isset($_POST['action'])){

    $action = $_POST['action'];

    if (isset($_POST['id'])) {$id = $_POST['id'];}

}elseif(isset($_GET['action'])){

    $action = $_GET['action'];

    if (isset($_GET['id'])) {$id = $_GET['id'];}

}

include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';



$_form = new ProductForm();

$_product = new ProductRepository();

$_listProducts = $_product->getListProducts();



$_settings = new SettingsRepository();

$_idCategoryForSupplie = $_settings->_get('id_category_for_supplies');

$_idCategoryForPanaderia = $_settings->_get('id_category_for_panaderia');



switch($action){

    case 'insert':

        $_form->populate($_POST);

        

        $cagegoryId = $_form->getValueElement('category');        

        if($cagegoryId == $_idCategoryForSupplie){$_form->setAsRequired(array('supplie')); $_form->enabledElement(array('supplie'));}

        if($cagegoryId == $_idCategoryForPanaderia){$_form->setAsRequired(array('masa'));$_form->enabledElement(array('masa'));}

        

        if($_form->isValid()){

            $_product->setOptions($_POST);

            $result = $_product->save($_product->getOptions());

            if($result){

                $flashmessenger->addMessage(array('success'=>'Producto registrado exitosamente.'));

                header('Location: Product.php');

            }else{

                $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));

            }

        }else{

            $_noValid = true;

            $vista = "Product.php";

            include $root.'/View/Template.php';

        }

        break;

   

    case 'edit':

        if($_GET){

            $_productData = $_product->getById($id);

        }elseif($_POST){

            $_productData = $_POST;

        }      

        

        $_form->setActionController('edit');

        $_form->setId($id);

        $_form->populate($_productData);

        

        if(isset($_POST['id'])){

            $cagegoryId = $_form->getValueElement('category');        

            if($cagegoryId == $_idCategoryForSupplie){$_form->setAsRequired(array('supplie')); $_form->enabledElement(array('supplie'));}

            if($cagegoryId == $_idCategoryForPanaderia){$_form->setAsRequired(array('masa'));$_form->enabledElement(array('masa'));}

            

            if($_form->isValid()){

                $_product->setOptions($_productData);

                $result = $_product->update($id,$_product->getOptions()); //no tengo id porque viene de post

                if($result){

                    $flashmessenger->addMessage(array('success'=>'Producto actualizado exitosamente.'));

                    header("Location: Product.php");

                }else{

                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));

                }       

            }else{

                $_noValid = true;

                $vista = "Product.php";

                include $root . '/View/Template.php';

            }

        }else{

            $vista = "Product.php";

            include $root . '/View/Template.php';

        }        

        break;

    

    case 'delete': 

        if(!$_product->isUsedInRecord($id)){

            if($_product->delete($id)){

                $flashmessenger->addMessage(array('success'=>'Producto eliminado exitosamente.'));

            }

        }else{

            $message = 'Este Producto no puede ser eliminado, esta siendo utilizado en almenos un registro.';

            $flashmessenger->addMessage(array('info'=>$message));

        }

       header("Location: Product.php");

        break;

        

     case 'ajax':

        $ajaxProducto = new ProductAjax();

        $json = $ajaxProducto->getResponse($_POST['request'], $_POST);

        echo json_encode($json);

        break;

    

    default:



        $vista = "Product.php";

        include $root.'/View/Template.php';

}