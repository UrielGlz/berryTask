<?php 
$controller = 'Empresa';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

#Es para asegurar que solo se puede editar empresa con id = 1;
$action = 'edit';
$id = '1';

$_form = new EmpresaForm();
$_empresa = new EmpresaRepository();
$_empresa->setOptions($_empresa->getById($id)); 

switch($action){
    case 'insert':
        $_form->populate($_POST);
        if($_form->isValid()){
            $_empresa->setOptions($_POST);
            //$_empresa->setImage($_FILES['logo']);
            $result = $_empresa->save($_empresa->getOptions());
            if($result){
                $flashmessenger->addMessage(array('success'=>'Genial !! La Empresa se registro exitosamente.'));
                header('Location: Empresa.php');
            }else{
                $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
            }
        }else{
            $vista = 'Empresa.php';
            include $root.'/View/Template.php';
        }
        break;
   
    case 'edit':
        $_empresaData = $_empresa->getById($id);
        if($_GET){$_empresaData = $_empresa->getById($id);}
        if($_POST){$_empresaData = $_POST;}
        
        $_form->setActionController('edit');
        $_form->setId($id);
        $_form->populate($_empresaData);   
        
        if(isset($_POST['id'])){
            if($_form->isValid()){
                $_empresaData['archivos'] = $_FILES;
                $_empresa->setOptions($_empresaData);
                //$_empresa->setImage($_FILES['logo']);
                $result = $_empresa->update($id,$_empresa->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Estupendo !! La Empresa se actualizo exitosamente.'));
                    header("Location: Empresa.php?action=edit&id=$id");
                }else{
                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
                }       
            }else{
                $vista = 'Empresa.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'Empresa.php';
            include $root . '/View/Template.php';
        }        
        break;
}