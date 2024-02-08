<?php

class autoloader {

    static function autoloadClasses($className) {

        $classes = array(

            'Form' => '/Form/Form.php',

            'EntityRepository' => '/Entity/EntityRepository.php',

            'Acl' => '/class/ACL/Acl.php',

            'AclRepository' => '/class/ACL/AclRepository.php',

            'Translator' => '/class/Translator/Translator.php',

            'Login' => '/class/Login/Login.php',

            'LoginRepository' => '/class/Login/LoginRepository.php',

            'LoginForm' => '/Form/LoginForm.php',

            'MailForm' => '/Form/MailForm.php',

            'DataBase' => '/class/DataBase/DataBase.php',

            'FlashMessenger' => '/class/FlashMessenger/FlashMessenger.php',

            'FormValidator' => '/class/FormValidator/FormValidator.php',

            'Ajax'=>'/Controller/Ajax/Ajax.php',

            'AjaxModalForms'=>'/Controller/Ajax/AjaxModalForms.php',

            'PDF' => '/class/Pdf/pdf.php',

            'FPDI' => '/class/Pdf/fpdi.php',

            'UploadFile' => '/class/UploadFile/UploadFile.php',

            'FileManagement' => '/class/FileManagement/FileManagement.php',

            'jUploadPHP' => '/class/UploadFile/jUploadPHP.php',

            'Tools' => '/class/Tools/Tools.php',

            'zipfile' => '/class/ZipFile/zipfile.php',

            //'Emailer' => '/class/PHPMailer/Emailer.php',
            //'PHPMailer' => '/class/PHPMailer/class.phpmailer.php',
            'LogSystemRepository' => '/Entity/LogSystemRepository.php',
            'Emailer' => '/class/sendgrid-php/vendor/Emailer.php',

            'PHPExcel' => '/class/PHPExcel/PHPExcel.php',

            'PHPExcel_IOFactory' => '/class/PHPExcel/PHPExcel/IOFactory.php',

            'PHPExcel_Reader_IReadFilter' => '/class/PHPExcel/PHPExcel/Reader/IReadFilter.php',

            'NumeroALetra' => '/class/Tools/NumeroALetra.php',

            'CURP' => '/class/CURP/CURP.php',

            'RFC' => '/class/RFC/RFC.php',

            'BarCode' => '/class/BarCode/BarCode.php',

            'SettingsRepository'=>'/Entity/SettingsRepository.php',

            'HistoryRepository'=>'/Entity/HistoryRepository.php',

            'AddProductForm'=>'/Form/AddProductForm.php',

            'EmailingForm'=>'/Form/EmailingForm.php',

            

            'AdvancedSearchForm' => '/Form/AdvancedSearchForm.php', 



            'CompanyRepository' => '/Entity/CompanyRepository.php',

            'CompanyForm' => '/Form/CompanyForm.php', 

            

            'AreaRepository' => '/Entity/AreaRepository.php',

            
            'ProjectAjax'=>'/Controller/Ajax/ProjectAjax.php',

            'ProjectRepository' => '/Entity/ProjectRepository.php',

            'ProjectForm' => '/Form/ProjectForm.php', 


            'TaskAjax'=>'/Controller/Ajax/TaskAjax.php',

            'TaskRepository' => '/Entity/TaskRepository.php',

            'TaskForm' => '/Form/TaskForm.php', 

            'CommentAjax'=>'/Controller/Ajax/CommentAjax.php',

            'CommentRepository' => '/Entity/CommentRepository.php',

            'CommentForm' => '/Form/CommentForm.php', 
            

            'FileAjax'=>'/Controller/Ajax/FileAjax.php',

            'FileRepository' => '/Entity/FileRepository.php',

            'FileForm' => '/Form/FileForm.php', 


            'UserAjax'=>'/Controller/Ajax/UserAjax.php',

            'UserRepository' => '/Entity/UserRepository.php',

            'UserForm' => '/Form/UserForm.php', 

            

            // 'StoreAjax'=>'/Controller/Ajax/StoreAjax.php',

            // 'StoreRepository' => '/Entity/StoreRepository.php',

            // 'StoreForm' => '/Form/StoreForm.php',

            

            'VendorAjax'=>'/Controller/Ajax/VendorAjax.php',

            'VendorRepository' => '/Entity/VendorRepository.php',

            'VendorForm' => '/Form/VendorForm.php',

            

            'CustomerAjax'=>'/Controller/Ajax/CustomerAjax.php',

            'CustomerRepository' => '/Entity/CustomerRepository.php',

            'CustomerForm' => '/Form/CustomerForm.php',

            

            'CategoryAjax'=>'/Controller/Ajax/CategoryAjax.php',

            'CategoryRepository' => '/Entity/CategoryRepository.php',

            'CategoryForm' => '/Form/CategoryForm.php',

            

            'BrandAjax'=>'/Controller/Ajax/BrandAjax.php',

            'BrandRepository' => '/Entity/BrandRepository.php',

            'BrandForm' => '/Form/BrandForm.php',

            

            'FlourAjax'=>'/Controller/Ajax/FlourAjax.php',

            'FlourRepository' => '/Entity/FlourRepository.php',

            'FlourForm' => '/Form/FlourForm.php',

            

            'SizeAjax'=>'/Controller/Ajax/SizeAjax.php',

            'SizeRepository' => '/Entity/SizeRepository.php',

            'SizeForm' => '/Form/SizeForm.php',

            

            'ShapeAjax'=>'/Controller/Ajax/ShapeAjax.php',

            'ShapeRepository' => '/Entity/ShapeRepository.php',

            'ShapeForm' => '/Form/ShapeForm.php',

            

            'PresentationAjax'=>'/Controller/Ajax/PresentationAjax.php',

            'PresentationRepository' => '/Entity/PresentationRepository.php',

            'PresentationForm' => '/Form/PresentationForm.php',

            

            /*Unidad de medida*/

            'UMAjax'=>'/Controller/Ajax/UMAjax.php',

            'UMRepository' => '/Entity/UMRepository.php',

            'UMForm' => '/Form/UMForm.php',

            

            'LocationAjax'=>'/Controller/Ajax/LocationAjax.php',

            'LocationRepository' => '/Entity/LocationRepository.php',

            'LocationForm' => '/Form/LocationForm.php',

            

            'SupplieAjax'=>'/Controller/Ajax/SupplieAjax.php',

            'SupplieRepository' => '/Entity/SupplieRepository.php',

            'SupplieForm' => '/Form/SupplieForm.php',           

            

            'ProductAjax'=>'/Controller/Ajax/ProductAjax.php',

            'ProductRepository' => '/Entity/ProductRepository.php',

            'ProductForm' => '/Form/ProductForm.php',    

            

            'ServiceAjax'=>'/Controller/Ajax/ServiceAjax.php',

            'ServiceRepository' => '/Entity/ServiceRepository.php',

            'ServiceForm' => '/Form/ServiceForm.php',    

            

            'SalesRecordAjax'=>'/Controller/Ajax/SalesRecordAjax.php',

            'SalesRecordRepository' => '/Entity/SalesRecordRepository.php',

            'SalesRecordExpesnsesDetailsTemp'=>'/Entity/SalesRecordExpesnsesDetailsTemp.php',

            'SalesRecordForm' => '/Form/SalesRecordForm.php',

            

            'BakedPlanPDF' => '/Controller/Pdf/BakedPlanPDF.php', 

            'SpecialOrderAjax'=>'/Controller/Ajax/SpecialOrderAjax.php',

            'SpecialOrderPDF'=>'/Controller/Pdf/SpecialOrderPDF.php',

            'SpecialOrderRepository' => '/Entity/SpecialOrderRepository.php',

            'SpecialOrderDetailsTempRepository'=>'/Entity/SpecialOrderDetailsTempRepository.php',

            'SpecialOrderForm' => '/Form/SpecialOrderForm.php',

            'SpecialOrderBuscarForm' => '/Form/SpecialOrderBuscarForm.php',

            

            // 'StoreRequestAjax'=>'/Controller/Ajax/StoreRequestAjax.php',

            // 'StoreRequestPDF'=>'/Controller/Pdf/StoreRequestPDF.php',

            // 'StoreRequestRepository' => '/Entity/StoreRequestRepository.php',

            // 'StoreRequestDetailsTempRepository'=>'/Entity/StoreRequestDetailsTempRepository.php',

            // 'StoreRequestForm' => '/Form/StoreRequestForm.php',

            

            'ShipmentStoreRequestAjax'=>'/Controller/Ajax/ShipmentStoreRequestAjax.php',

            'ShipmentStoreRequestPDF'=>'/Controller/Pdf/ShipmentStoreRequestPDF.php',

            'ShipmentStoreRequestRepository' => '/Entity/ShipmentStoreRequestRepository.php',

            'ShipmentStoreRequestDetailsTempRepository'=>'/Entity/ShipmentStoreRequestDetailsTempRepository.php',

            'ShipmentStoreRequestForm' => '/Form/ShipmentStoreRequestForm.php',

            

            'ReceivingStoreRequestAjax'=>'/Controller/Ajax/ReceivingStoreRequestAjax.php',

            'ReceivingStoreRequestPDF'=>'/Controller/Pdf/ReceivingStoreRequestPDF.php',

            'ReceivingStoreRequestRepository' => '/Entity/ReceivingStoreRequestRepository.php',

            'ReceivingStoreRequestDetailsTempRepository'=>'/Entity/ReceivingStoreRequestDetailsTempRepository.php',

            'ReceivingStoreRequestForm' => '/Form/ReceivingStoreRequestForm.php',

            

            'CustomerRepository' => '/Entity/CustomerRepository.php',

            

            'PurchaseAjax'=>'/Controller/Ajax/PurchaseAjax.php',

            'PurchasePDF'=>'/Controller/Pdf/PurchasePDF.php',

            'PurchaseRepository' => '/Entity/PurchaseRepository.php',

            'PurchaseDetailsTempRepository'=>'/Entity/PurchaseDetailsTempRepository.php',

            'PurchaseForm' => '/Form/PurchaseForm.php',

            

            'PagoAjax'=>'/Controller/Ajax/PagoAjax.php',

            'PagoPDF'=>'/Controller/Pdf/PagoPDF.php',

            'PagoRepository' => '/Entity/PagoRepository.php',

            'PagoDetailsTempRepository'=>'/Entity/PagoDetailsTempRepository.php',

            'PagoForm' => '/Form/PagoForm.php',

            'PagoBuscarForm' => '/Form/PagoBuscarForm.php',

            

            'ReceivingAjax'=>'/Controller/Ajax/ReceivingAjax.php',

            'ReceivingPDF'=>'/Controller/Pdf/ReceivingPDF.php',

            'ReceivingRepository' => '/Entity/ReceivingRepository.php',

            'ReceivingDetailsTempRepository'=>'/Entity/ReceivingDetailsTempRepository.php',

            'ReceivingForm' => '/Form/ReceivingForm.php',

            

            'OutputAjax'=>'/Controller/Ajax/OutputAjax.php',

            'OutputPDF'=>'/Controller/Pdf/OutputPDF.php',

            'OutputRepository' => '/Entity/OutputRepository.php',

            'OutputDetailsTempRepository'=>'/Entity/OutputDetailsTempRepository.php',

            'OutputForm' => '/Form/OutputForm.php',

            

            'ReturnAjax'=>'/Controller/Ajax/ReturnAjax.php',

            'ReturnPDF'=>'/Controller/Pdf/ReturnPDF.php',

            'ReturnRepository' => '/Entity/ReturnRepository.php',

            'ReturnDetailsTempRepository'=>'/Entity/ReturnDetailsTempRepository.php',

            'ReturnForm' => '/Form/ReturnForm.php',

            

            'TransferAjax'=>'/Controller/Ajax/TransferAjax.php',

            'TransferPDF'=>'/Controller/Pdf/TransferPDF.php',

            'TransferRepository' => '/Entity/TransferRepository.php',

            'TransferDetailsTempRepository'=>'/Entity/TransferDetailsTempRepository.php',

            'TransferForm' => '/Form/TransferForm.php',

            

            'SliceAjax'=>'/Controller/Ajax/SliceAjax.php',

            'SliceRepository' => '/Entity/SliceRepository.php',

            'SliceForm' => '/Form/SliceForm.php',

            

            'PhysicalInventoryAjax'=>'/Controller/Ajax/PhysicalInventoryAjax.php',

            'PhysicalInventoryPDF'=>'/Controller/Pdf/PhysicalInventoryPDF.php',

            'PhysicalInventoryRepository' => '/Entity/PhysicalInventoryRepository.php',

            'PhysicalInventoryDetailsTempRepository'=>'/Entity/PhysicalInventoryDetailsTempRepository.php',

            'PhysicalInventoryForm' => '/Form/PhysicalInventoryForm.php',

            

            'TimeClockAjax'=>'/Controller/Ajax/TimeClockAjax.php',

            'TimeClockPDF'=>'/Controller/Pdf/TimeClockPDF.php',

            'TimeClockRepository' => '/Entity/TimeClockRepository.php',

            'TimeClockDetailsTempRepository'=>'/Entity/TimeClockDetailsTempRepository.php',

            'TimeClockForm' => '/Form/TimeClockForm.php',

            'TimeClockBuscarForm' => '/Form/TimeClockBuscarForm.php',

            

            'InventoryRepository' => '/Entity/InventoryRepository.php',

            

            'InvoicePDF'=>'/Controller/Pdf/InvoicePDF.php',

            'InvoiceAjax'=>'/Controller/Ajax/InvoiceAjax.php',

            'InvoiceRepository'=>'/Entity/InvoiceRepository.php',

            'InvoiceDetailsTempRepository'=>'/Entity/InvoiceDetailsTempRepository.php',

            'InvoiceForm' => '/Form/InvoiceForm.php',

            

            'PrioritiesAjax' => '/Controller/Ajax/PrioritiesAjax.php',

            'PrioritiesRepository' => '/Entity/PrioritiesRepository.php',

            'PrioritiesForm' => '/Form/PrioritiesForm.php',


            'CategoryTaskAjax' => '/Controller/Ajax/CategoryTaskAjax.php',

            'CategoryTaskRepository' => '/Entity/CategoryTaskRepository.php',

            'CategoryTaskForm' => '/Form/CategoryTaskForm.php',

            
            'CategoryFilesAjax' => '/Controller/Ajax/CategoryFilesAjax.php',

            'CategoryFilesRepository' => '/Entity/CategoryFilesRepository.php',

            'CategoryFilesForm' => '/Form/CategoryFilesForm.php',
            

            'ReportAjax'=>'/Controller/Ajax/ReportAjax.php',

            'ReportForm' => '/Form/ReportForm.php',            

            'ReportsListEntity' => '/Entity/ReportsListEntity.php',

            'ReportsListRepository' => '/Entity/ReportsListRepository.php',      

            

            'CustomerPaymentAjax' => '/Controller/Ajax/CustomerPaymentAjax.php',

            'CustomerPaymentEntity' => '/Entity/CustomerPaymentEntity.php',

            'CustomerPaymentRepository' => '/Entity/CustomerPaymentRepository.php',

            'CustomerPaymentForm' => '/Form/CustomerPaymentForm.php',

            

            'DepositAjax' => '/Controller/Ajax/DepositAjax.php',

            'DepositRepository' => '/Entity/DepositRepository.php',

            'DepositDetailsTempRepository'=>'/Entity/DepositDetailsTempRepository.php',

            'DepositForm' => '/Form/DepositForm.php',

            'AddDepositDetailForm'=>'/Form/AddDepositDetailForm.php',

            

        );

        if (isset($classes[$className])) {

            require_once(ROOT . $classes[$className]);

        }

    }    

}