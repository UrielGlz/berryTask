<?php

    define('ROOT',$_SERVER["DOCUMENT_ROOT"]."/");

    define('ROOT_HOST',"https://".$_SERVER['HTTP_HOST'].'');

    define('PATH_IMG',"https://".$_SERVER['HTTP_HOST'].'/public/img');

    define('PATH_IMG_PRODUCTS',ROOT."/public/img/products/");

    define('PATH_TEMP_DOCS',ROOT."/app/resources/docs/temp/");  

    define('PATH_PURCHASE_INVOICES',ROOT."/app/resources/docs/facturas_de_compras/"); 

    define('PATH_INVOICE_ATTACHMENT',ROOT."/app/resources/docs/invoice_attachments/"); 

    define('PATH_CUSTOMER_PAYMENTS_ATTACHMENT',ROOT."/app/resources/docs/customer_payments_attachments/");

    define('PATH_PURCHASE_ATTACHMENT',ROOT."/app/resources/docs/purchase_attachments/");

    define('PATH_TASK_ATTACHMENT',ROOT."/app/resources/docs/task_attachments/");


    define('PATH_DEPOSIT_ATTACHMENT',ROOT."/app/resources/docs/deposit_attachments/");

  

    

    ini_set('date.timezone', 'America/Chicago');

    

    define('GENERIC_USER','registro');

    define('GENERIC_PASSWORD','NightHawk21%');

    

    /* Congfiguracion envio facturas por correo*/

    define('SET_FROM_MAIL','no-reply2@lunis.mx');

    define('SET_MANAGER_MAIL','carlos.vazquez@aglsolutions.com');

    define('SET_FROM_TITLE','Facturacion');

    

    define('SEND_BCC_WHEN_SEND_INVOICE',null); // IF true - Se envia BCC a SET_MANAGER_MAIL cada vez que se envia invoice.

    define('SEND_MAIL_WHEN_NEW_EDIT_INVOICE',null);//// IF true - Se envia mail a SET_MANAGER_MAIL cada vez que se crea o actualiza invoice.