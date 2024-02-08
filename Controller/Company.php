<?php 
$controller = 'Company';
$action = '';

if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}
$action = 'edit';
$id = 1;
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new CompanyForm();
$_company = new CompanyRepository();
$_noValid = null;

switch($action){   
    case 'edit':
        $_companyData = $_company->getById($id);
        if($_POST){$_companyData = $_POST;}
        
        $form->setActionController('edit');
        $form->setId($id);
        $form->populate($_companyData);
        
        if(isset($_POST['id'])){
            if($form->isValid()){
                $_company->setOptions($_companyData);
                $result = $_company->update($id,$_company->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Empresa actualizada exitosamente.'));
                    header("Location: Company.php");
                }else{
                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
                }       
            }else{
                $_noValid = true;
                $vista = 'Company.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'Company.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    default:       
        $vista = 'Company.php';
        include $root.'/View/Template.php';
}