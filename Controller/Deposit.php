<?php 
$controller = 'Deposit';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}

include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$_form = new DepositForm();
$_deposit = new DepositRepository();

switch($action){
    case 'insert':        
        $_form->setTokenForm($_POST['token_form']);
        $_form->populate($_POST);  
        $_POST['attachments'] = $_FILES;
        $_deposit->setOptions($_POST);
        
        if ($_form->isValid()) {             
            if ($_deposit->save($_deposit->getOptions())) {                
                $_depositId = $_deposit->getLastInsertId();
                $_depositNo = $_deposit->getDepositNumberById($_depositId);
                $flashmessenger->addMessage(array(
                    "success"=>$_translator->_getTranslation("Deposito")." #$_depositNo ".$_translator->_getTranslation("se ha registrado exitosamente.")));
                
                header("Location: Deposit.php?action=edit&id=$_depositId");
            }else{                
                $vista = 'Deposit.php';
                include $root . '/View/Template.php';                
            }
        } else {
            $vista = 'Deposit.php';
            include $root . '/View/Template.php';
        }
        break;
        
    case 'list':          
        $searchFilter = null;
        if(isset($_POST['search'])){$searchFilter = $_POST;}
        $_listDeposits = $_deposit->getListDeposit($searchFilter);
        
        $vista = 'DepositList.php';
        include $root . '/View/Template.php';
    break;
        
    case 'export':
        switch($_GET['flag']){
            case 'pdf':
                $pdf = new DepositPDF($id);
                break;
            }   
        break;
    
    case 'edit': 
        $_hideWaybill = '';
        
        if($_GET){
            $_depositData = $_deposit->getById($id);
            $_deposit->crearTablaDetallesForUser();
            $_deposit->setDepositDetailsById($id,$_form->getTokenForm());     /*No exist esta funcion */       
        }
        
        if($_POST){            
            $_form->setTokenForm($_POST['token_form']);
            $_POST['attachments'] = $_FILES;
            $_depositData = $_POST;
        }
        
        $login = new Login();
        if($login->getRole() != '1' && !in_array($_depositData['store_id'], $login->getStoreArray())){
            header("Location: Deposit.php?action=list");
            exit;
        }
        
        $_form->setActionController('edit');
        $_form->setId($id);
        $_form->populate($_depositData);        
        
        $_disabled = null;
        
        if($_depositData['status'] == '3'){
            $_disabled = true;
            $_form->disabledAllElements();
            $_form->hideElement(array('terminar'));               
        }
        
        $_deposit->setOptions($_depositData);
        
        if(isset($_POST['id']) && $_depositData['status'] != '3'){
            if($_form->isValid()){       
                $result = $_deposit->update($id,$_deposit->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Deposito se actualizo exitosamente.'));
                    header("Location: Deposit.php?action=edit&id=$id");
                }else{
                    $vista = 'Deposit.php';
                    include $root . '/View/Template.php';                    
                }       
            }else{
                $vista = 'Deposit.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'Deposit.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete':
        if(!$_deposit->isUsedInRecord($id)){
            if($_deposit->delete($id)){
                $flashmessenger->addMessage(array('success'=>'Deposito se cancelo exitosamente.'));
            }
        }
        
        header('Location: Deposit.php?action=list');
        break;
        
    case 'ajax':
        $ajaxDeposit = new DepositAjax();
        $json = $ajaxDeposit->getResponse($_POST['request'],$_POST);
        
        echo json_encode($json);
        break;       
    
    default:      
        $_deposit->crearTablaDetallesForUser();
        
        //$_form->populate(array('tipo'=>$_tipo));
        $vista = 'Deposit.php';
        include $root.'/View/Template.php';
        break;
}