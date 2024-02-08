<section class="content-header">
    <h1><i class=' fa fa-file-text-o'></i> <?php echo $_translator->_getTranslation('Factura');if($action == 'edit'){echo " #".$_invoice->getInvoiceNumber();}?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
        <li><a href="<?php echo ROOT_HOST?>/Controller/Invoice.php?action=list"><?php echo $_translator->_getTranslation('Lista de facturas')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Facturas');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
    <?php echo $_form->openForm();?>
    <div style="display: none"><?php 
        echo $_form->showActionController(); 
        echo $_form->showId();
        $_form->showElement('sales_order_id');   
        $_form->showElement('type');    
        $_form->showElement('status');       
        $_form->showElement('subtotal');
        $_form->showElement('discount_general_amount');
        $_form->showElement('total');
        $_form->showElement('sales_order_id');?>
    </div>
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <?php 
    if($action === 'edit'){?>
        <div class="box-header">
            <div class="col-lg-12 col-xs-12">
            <div class="box-tools pull-right">            
                <a href="Invoice.php" class="btn btn-default"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar factura')?></a>
                <a class="btn btn-default" href="#" onclick="javascript: void window.open('Invoice.php?action=export&format=pdf&flag=invoice&id=<?php echo $_form->getId();?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-print fa-"></i> <?php echo $_translator->_getTranslation('Imprimir') ?></a>
                <a class='btn btn-default _emailing' href='#' data-operationname="invoice" data-operationid='<?php echo $_form->getId()?>' title='Enviar por correo'><i class='fa fa-envelope'></i> <?php echo $_translator->_getTranslation('Enviar por correo')?></a>
            </div><!-- /.box-tools -->
            <div class="clear"></div><br/>
            <div class="h_totals pull-right">
                <h3><?php echo $_translator->_getTranslation('Total Factura'); ?></h3>
                <h2>$<?php echo number_format($_invoice->getTotal(),2) ?> </h2>
                <h4><?php echo $_translator->_getTranslation('Saldo'); ?> $ <?php echo number_format($_invoice->getBalance(),2)?></h4>
                <?php /*
                if($_invoiceData['sales_order_id'] != '0'){?>
                    <a class="btn btn-box-tool btn-tumblr" href="#" onclick="javascript: void window.open('SalesOrder.php?action=export&format=pdf&flag=sales_order&id=<?php echo $_invoiceData['sales_order_id'];?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-file-pdf-o"></i> <?php echo $_translator->_getTranslation('Ver orden de venta').' #'.$_invoiceData['sales_order_num'] ?></a>                            
                    <a class="btn btn-box-tool btn-tumblr" href="SalesOrder.php?action=edit&id=<?php echo $_invoiceData['sales_order_id'] ?>" ><i class="fa fa-external-link"></i> <?php echo $_translator->_getTranslation('Ir a orden de venta').' #'.$_invoiceData['sales_order_num'] ?></a><?php    
                } */?>
            </div>  
            </div>
        </div><!-- /.box-header --><?php 
    }?>
    <div class="box-body"> 
    <div class="card">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#tab_general" aria-controls="home" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Informacion general') ?></a></li>
            <li role="presentation"><a href="#tab_attachments" aria-controls="profile" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Adjuntos') ?></a></li>          
        </ul>
        <!-- Tab panes -->
        <div class="tab-content" style="padding-top: 20px">
            <div role="tabpanel" class="tab-pane active" id="tab_general">
                <div class="col-xs-12 col-md-6">
                    <?php 
                    $_form->showElement('invoice_num');   
                    $_form->showElement('date');                  
                    $_form->showElement('id_customer');
                    $_form->showElement('billed_to_store');   
                    //$_form->showElement('customer_po');?>                   
                </div> 
                <div class="col-xs-12 col-md-6">                                        
                    <?php 
                    if($action == 'edit'){$_form->showElement('statusName');}
                    $_form->showElement('payment_terms_id');
                    $_form->showElement('due_date');
                    $_form->showElement('comments');?>
                </div>
            </div>            
            <div role="tabpanel" class="tab-pane" id="tab_attachments">
                <div class="col-xs-12 col-md-6">
                    <?php 
                    $_form->showElement('customer_po_file');
                    $_form->showElement('attachments[]');
                    echo $_invoice->getListFiles($_form->getId());?>  
                </div>
            </div>
        </div>
    </div>    
    <div class="clear"></div>
    <div class='col-md-12'>
        <hr/>
        <?php  
        $_invoiceDataInvoicedAjax = new InvoiceAjax(); 
        $listManifestDetalles = $_invoiceDataInvoicedAjax->getListInvoiceDetalles($_form->getTokenForm(),$_form->getValueElement('status'));
        $_form->setValueToElement($listManifestDetalles['descuento_items'],'descuento_items');
        echo $_form->showElement('descuento_items');?>
        <!-- Nav tabs -->
        <div class="card">
        <ul class="nav nav-tabs" role="tablist">            
            <li role="presentation" class="active"><a href="#details" aria-controls="home" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Detalles de factura') ?></a></li>                        
        </ul>

        <!-- Tab panes -->
        <div class="tab-content" style="padding-top: 20px">            
            <div role="tabpanel" class="tab-pane active" id="details">                  
                <div class="table-responsive">                    
                <table id='invoiceDetails' class="table table-condensed table-striped table-hover font-size-12 datatable_whit_filter_column _hideSearch" style="width:100%">
                    <thead>                
                        <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Accion')?></th>
                        <th class="col-md-5 text-center"><?php echo $_translator->_getTranslation('Descripcion');?></th>
                        <th class="col-md-1 text-right"><?php echo $_translator->_getTranslation('Cantidad')?></th> 
                        <th class="col-md-1 text-right"><?php echo $_translator->_getTranslation('Precio')?></th> 
                        <th class="col-md-1 text-right"><?php echo $_translator->_getTranslation('Des')?></th>
                        <th class="col-md-1 text-right"><?php echo $_translator->_getTranslation('Desc Gral')?></th>
                        <th class="col-md-1 text-right"><?php echo $_translator->_getTranslation('Impuestos')?></th>
                        <th class="col-md-1 text-right"><?php echo $_translator->_getTranslation('Total')?></th>
                    </thead>
                    <tfoot>
                        <th></th>
                        <th class="filter"><?php echo $_translator->_getTranslation('Descripcion');?></th>
                        <th class="filter"><?php echo $_translator->_getTranslation('Cantidad')?></th>                         
                        <th class="filter"><?php echo $_translator->_getTranslation('Precio')?></th> 
                        <th class="filter"><?php echo $_translator->_getTranslation('Des')?></th>
                        <th class="filter"><?php echo $_translator->_getTranslation('Desc Gral')?></th>
                        <th class="filter"><?php echo $_translator->_getTranslation('Impuestos')?></th>
                        <th class="filter"><?php echo $_translator->_getTranslation('Total')?></th>
                    </tfoot>
                    <tbody>
                        <?php echo $listManifestDetalles['invoiceDetails'];?>
                    </tbody>                    
                </table>   
                </div>
            </div>
        </div>
        </div>               
    </div>
    <div class="col-lg-12 col-md-12 text-left">        
        <?php $element = $_form->getElement('agregar_producto_o_servicio'); echo $_form->createOnlyElement($element);?>
    </div>            
    <div class="clear"></div>
    <br/><br/><br/>
    <div class="col-lg-6 col-md-6">
        <label><?php echo $_translator->_getTranslation('Mensaje en factura')?></label>
        <?php $element = $_form->getElement('message_on_invoice'); echo $_form->createOnlyElement($element); ?>
    </div>
    <div class="col-lg-4 col-md-4 pull-right">
        <table id="invoice_totals" class="table-condensed">
            <tbody>
                <tr>
                    <th class="text-right" colspan="2" style="border: 0px;"><?php echo $_translator->_getTranslation('Importe')?></th>
                    <td class="_label_importe text-right col-lg-3 col-md-3"  style="border: 0px;border-bottom: 1px solid #eee"><?php echo number_format($listManifestDetalles['total_importe'],2)?></td>
                </tr>
                <tr>
                    <th class="text-right" colspan="2" style="border: 0px;"><?php echo $_translator->_getTranslation('Descuento en items')?></th>
                    <td class="_label_descuento_items text-right col-lg-3 col-md-3"  style="border: 0px;border-bottom: 1px solid #eee"><?php echo number_format($listManifestDetalles['descuento_items'],2)?></td>
                </tr>
                <tr>
                    <th style="border: 0px;"><?php $element = $_form->getElement('discount_general_type'); echo $_form->createOnlyElement($element); ?></th>
                    <td style="border: 0px;"><?php $element = $_form->getElement('discount_general'); echo $_form->createOnlyElement($element); ?></td>
                    <td class="_label_descuento_general text-right" style="border: 0px;border-bottom: 1px solid #eee; vertical-align: middle"><?php echo number_format($listManifestDetalles['descuento_general'],2)?></td>
                </tr>
                <tr>
                    <th class="text-right" colspan="2" style="border: 0px;"><?php echo $_translator->_getTranslation('Subtotal')?></th>
                    <td class="_label_subtotal text-right col-lg-3 col-md-3"  style="border: 0px;border-bottom: 1px solid #eee"><?php echo $listManifestDetalles['total_subtotal']?></td>
                </tr>
            </tbody>
            <tbody id="_label_impuestos">
                <tr></tr><?php /*Lo agrego para que se vea el border bottom de Total; fue lo unico que encontre para que se viera*/?>                        
                <?php echo $listManifestDetalles['total_impuestos']; ?>
            </tbody>
            <tbody>
                <tr>
                    <th class="text-right" colspan="2" style="border: 0px;"><?php echo $_translator->_getTranslation('Total')?></th>
                    <td class="_label_total text-right" style="border: 0px;border-bottom: 1px solid #eee"><?php echo number_format($listManifestDetalles['total'],2);?></td>
                </tr>
            </tbody>                
        </table>
    </div>
    <div class="col-lg-12" style="padding-top: 20px">
        <div class="pull-right">           
            <?php $element = $_form->getElement('terminar');?>
            <?php echo $_form->createElement($element);?>
        </div>
    </div>        
  </div><!-- /.box-body -->
  <?php echo $_form->closeForm();?>
</div><!-- /.box -->
</section>
<?php include ROOT."/View/Modal/addCustomer.php";?>
<?php //include ROOT."/View/Modal/addCustomerAddress.php";?>
<?php include ROOT."/View/Modal/addPaymentTerms.php";?>
<?php include ROOT."/View/Modal/addInvoiceProduct.php";?>
<?php //include ROOT."/View/Modal/emailing.php"; ?>
<style>
    label.shipping_address{
        margin-top:-5px;
    }
    
    .ui-autocomplete {
      z-index: 1510 !important;
    }
    
    .input-group-btn.descuento_tipo{
        width:32%;      
    }
</style>
<?php if(isset($_disabled) && $_disabled){?> <script>$('#invoiceDetails tr td a.btn-danger').hide('disabled');</script><?php } ?>
<script>    
    $('#date').datetimepicker({format: 'MM/DD/YYYY'});      
    $("select:not('._discount_type')").select2(); 
    
    //specialTemplateSelect();
    
    $('#id_customer').on('select2:select',function(){getCustomerMethodPayment();});  
    $('#date').on('dp.change',function(){setDueDateNew();});    
    $('#payment_terms_id').on('select2:select',function(){setDueDateNew();});    
    $('#discount_general').on('blur',function(){applyGeneralDiscountToInvoiceItems();});
    $('#discount_general_type').on('select2:select',function(){applyGeneralDiscountToInvoiceItems();});      
    
    $('select').on('select2:opening',function(e){
        e.preventDefault();
        if($(this).attr('readonly') !== 'readonly'){$(this).unbind('select2:opening').select2('open');}
    });
    
    $('#invoiceDetails').on('blur','tbody tr td input._priceByProductKeyPrice',function(){
        updateInvoicePriceByProductKeyPrice(this);
    });    
    
    $('#invoiceDetails_filter,#salesOrderSummary_filter').hide();   
    setDueDateNew();
    
    $('#guardar').on('click',function(){setInvoiceDetalles();});
    
    $('._saveCustomer').on('click',function(){
        _gadgetSaveForm('customer',function(data){
            if(data.response){ 
                getCustomerList(function(r){
                    $('#id_customer').html(r.customerList);
                    $('#id_customer').val(data.customer_id).trigger('select2:select');     
                    $('#modalAddCustomer').modal('hide');
                });                
            }
        });
    });
    
    $('._editCustomer').on('click',function(){
        clearForm('customer');
        $('.flashmessenger').html('');
        _getTranslation('Editar cilente',function(msj){ $('#title_modal_customer').html(msj);});
        setDataToEditCustomer($('form[name=invoice] #id_customer').val());    
    }); 
        
    $('._savePaymentTerms').on('click',function(){
        _gadgetSaveForm('paymentterms',function(data){
            if(data.response){ 
                getPaymentTermsList(function(r){
                    $('#payment_terms_id').html(r.paymentTermList);
                    $('#payment_terms_id').val(data.payment_terms_id).trigger('select2:select');     
                    $('#modalAddPaymentTerms').modal('hide');
                });                
            }
        });
    });
    
    /*MODAL addInvoiceProduct*/
    $('#agregar_producto_o_servicio').on('click',function(){
        clearModalAddInvoiceProduct();
        _getTranslation('Agregar productos o servicios',function(msj){ $('#title_modal_invoiceProduct').html(msj);});
         $('#type').val($(this).data('type'));
        $('#modalAddInvoiceProduct').modal('show');
    });
    
    $('._addProduct').on('click',function(){setInvoiceDetalles()});
    $('._closeModalAddProduct').on('click',function(){clearModalAddInvoiceProduct();$('#modalAddInvoiceProduct').modal('hide');});
    
    $( "form[name=addProduct] #product" ).autocomplete({
        source: function( request, response ) {
          $.post('/Controller/Purchase.php', {
              action: 'ajax',
              request: 'getListProduct',
              item: request.term
          }, function(data) {
                  response(data.products);
             }, 'json');
        },      
        select: function( event, ui ) {
            $( "form[name=addProduct] #product" ).val( ui.item.label );
            $( "form[name=addProduct] #id_product" ).val( ui.item.value ); 
            getDefaultDataInvoiceProduct();
            return false;
          }    
    } );     
    
</script>