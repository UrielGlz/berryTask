<section class="content-header">

    <h1><i class='fa-fw fa fa-shopping-basket'></i> <?php echo $_translator->_getTranslation('Compras');?></small></h1>

    <ol class="breadcrumb">

        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>

        <li><a href="<?php echo ROOT_HOST?>/Controller/Purchase.php?action=list"><?php echo $_translator->_getTranslation('Lista de compras')?></a></li>

      <li class="active"><?php echo $_translator->_getTranslation('Compras');?></li>

    </ol>

</section>

<section class="content">

<div class="box">

    <?php echo $form->openForm();?>

    <div style="display: none"><?php

        echo $form->showActionController();

        echo $form->showId();

        //echo $form->showElement('total');

        echo $form->showElement('status');

        ?>

    </div>

    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>

    <div class="box-header with-border">

        <?php 

        if($action === 'edit' && $isUserApprover){echo "<div class='pull-left'>".$form->createOnlyElement($form->getElement('status_approval'))."&nbsp;&nbsp;<label>Aprobar compra</label></div>"; }?>  

        <div class="box-tools">

            <?php 

            if($action === 'edit'){?>

                <a href="Purchase.php" class="btn btn-default"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar compra')?></a>

                <a class="btn btn-default" href="#" onclick="javascript: void window.open('Purchase.php?action=export&flag=pdf&id=<?php echo $form->getId();?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-print fa-"></i> <?php echo $_translator->_getTranslation('Imprimir') ?></a><?php 

            }?>

        </div><!-- /.box-tools -->

    </div><!-- /.box-header -->

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

                    <?php $form->showElement('date');?> 

                    <?php $form->showElement('store_id');?> 

                    <?php $form->showElement('requested_by');?>

                    <?php $form->showElement('approved_by');?>

                    <?php $form->showElement('vendor');?>

                    <?php $form->showElement('reference');?>                    

                    <?php $form->showElement('lot');?>

                    <?php $form->showElement('discount_general');?>

                </div> 

                <div class="col-xs-12 col-md-6">

                    <?php $form->showElement('method_payment');?> 

                    <?php $form->showElement('credit_days');?>

                    <?php $form->showElement('due_date');?>                        

                    <?php /*

                    $form->showElement('credit_note_file');

                    $file = "/app/resources/docs/facturas_de_compras/Nota_de_credito".$form->getId()."_".$form->getValueElement('reference').".pdf"; */?>

                    <!--

                    <?php if(file_exists(ROOT.$file)){?>

                                <div class="col-md-9 pull-right m-b-2">

                                    <a href="<?php echo ROOT_HOST.$file ?>" target="_blank">Ver Nota de credito</a>                        

                                </div>

                                <div class="clear"></div>

                    <?php } ?>-->     

                    <?php /*

                    $form->showElement('credit_note');

                    $form->showElement('credit_note_amount');*/

                    $form->showElement('comments');?>                    

                </div>

            </div>

            <div role="tabpanel" class="tab-pane" id="tab_attachments">

                 <div class="col-xs-12 col-md-6">

                    <?php 

                    $form->showElement('invoice_file');

                    $form->showElement('attachments[]');

                    echo $_purchase->getListFiles($form->getId());?>  

                </div>

            </div>

        </div>

         </div>                

            <div class="clear"></div>

            <div class='col-md-12'>

                <hr/>

                <?php 

                    $compraAjax = new PurchaseAjax();

                    $edit = null;

                    if($action=='edit'){$edit = true;}                    

                    $listCompraDetalles = $compraAjax->getListPurchaseDetails($form->getTokenForm());?>

                <div class='table-responsive'>

                <table id='purchase-table' style="font-size:11px" class="table table-condensed table-striped table-hover">

                    <thead>                

                        <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Accion')?></th>

                        <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Codigo');?></th>

                        <th class="col-lg-3"><?php echo $_translator->_getTranslation('Descripcion');?></th>   

                        <th class="col-lg-1 text-right"><?php echo $_translator->_getTranslation('Cantidad')?></th>                     

                        <th class="col-lg-1 text-right"><?php echo $_translator->_getTranslation('Costo')?></th>

                        <th class="col-lg-1 text-right"><?php echo $_translator->_getTranslation('Desc.')?></th>

                        <th class="col-lg-1 text-right"><?php echo $_translator->_getTranslation('Desc.Gral')?></th>

                        <th class="col-lg-1 text-right"><?php echo $_translator->_getTranslation('Subtotal')?></th>

                        <th class="col-lg-1 text-right"><?php echo $_translator->_getTranslation('Impuestos')?></th>

                        <th class="col-lg-1 text-right"><?php echo $_translator->_getTranslation('Total')?></th>

                    </thead>

                    <tbody>

                        <?php echo $listCompraDetalles['purchaseDetails'];?>

                    </tbody>

                    <tfoot>

                        <tr>

                            <th colspan="9" class="text-right"><?php echo $_translator->_getTranslation('Importe ');?></th>

                            <th id='total_importe' class="text-right">

                                <?php echo $listCompraDetalles['total_importe'];?>                               

                            </th>

                        </tr>

                        <tr>

                            <th colspan="9" class="text-right"><?php echo $_translator->_getTranslation('Descuentos');?></th>

                            <th id='total_descuentos' class="text-right"><?php echo $listCompraDetalles['total_descuentos'];?></th>

                            <!--<th><?php //echo $form->showElement('compra_descuento'); ?></th>-->

                        </tr>

                        <tr>

                            <th colspan="9" class="text-right"><?php echo $_translator->_getTranslation('Subtotal');?></th>

                            <th id='total_subtotal' class="text-right"><?php echo $listCompraDetalles['total_subtotal'];?></th>

                            <!--<th><?php //echo $form->showElement('compra_subtotal'); ?></th>-->

                        </tr>

                        

                        <?php 

                        if($listCompraDetalles['total_impuestos'] !== '0.00'){

                            echo $listCompraDetalles['total_impuestos'];

                        }else{ ?>

                            <tr>

                                <th colspan="9" class="text-right"><?php echo $_translator->_getTranslation('Impuestos');?></th>

                                <th id='total_impuestos' class="text-right"><?php echo $listCompraDetalles['total_impuestos'];?></th>

                            </tr>

                        <?php } 

                        ?>   

                        <!--

                        <tr>

                            <th colspan="9" class="text-right"><?php echo $_translator->_getTranslation('IVA');?></th>

                            <th><?php //echo $form->showElement('compra_iva'); ?></th>

                        </tr>

                        <tr>

                            <th colspan="9" class="text-right"><?php echo $_translator->_getTranslation('IEPS');?></th>

                            <th><?php //echo $form->showElement('compra_ieps'); ?></th>

                        </tr>-->

                        <tr>

                            <th colspan="9" class="text-right"><?php echo $_translator->_getTranslation('Total');?></th>

                            <!--<th id='total_label' class="text-right"><?php echo number_format($listCompraDetalles['total'],2);?></th>-->

                            <th><?php echo $form->showElement('total'); ?></th>

                        </tr>

                    </tfoot>

                </table>

                </div>

            </div>

            <!-- Modal -->         

            <div class="col-lg-12">

                <div class="pull-right">

                    <div style='float:left'>

                        <?php $element = $form->getElement('agregar_producto');?>

                        <?php echo $form->createElement($element);?>

                    </div>

                    <div style='float:left'>

                        <?php $element = $form->getElement('terminar');?>

                        <?php echo $form->createElement($element);?>

                    </div>

                </div>

                <div class='clear'></div>

            </div>            

        <?php

        if(isset($_timeLine)){?>

            <div class="col-lg-4">

            <ul class="timeline"><?php

            foreach($_timeLine as $row){?>

                <li>

                    <!-- timeline icon -->

                    <i class="fa fa-check"></i>

                    <div class="timeline-item">                        

                        <h3 class="timeline-header"><a><?php echo $row['action_subject']; ?></a></h3>

                        <div class="timeline-body">                            

                            <small> <?php echo $row['date']?></small><br/>

                            <?php echo $row['user']; ?>

                        </div>

                    </div>

                </li><?php

            } ?>

                <li>

                    <i class="fa fa-clock-o bg-blue"></i>

                </li>

            </ul>

            </div><?php

        } ?> 

  </div><!-- /.box-body -->

  <?php echo $form->closeForm();?>

</div><!-- /.box -->

</section>

<?php include ROOT."/View/Modal/addPurchaseProduct.php";?>

<style>

    .input-group-btn.descuento_tipo{

        width:32%;      

    }

    

    label.taxes_included{

        margin-top: -2px;

    }



    .ui-autocomplete {

      z-index: 1510 !important;

    }

    

    tr th div.form-group {

        margin-bottom: 0px;

    }

</style>

<?php 

    if(isset($_disabled) && $_disabled === true){?>

        <script>$('table#purchase-table tbody tr td a').addClass('disabled');</script>

<?php    }

    ?>

<script> 

    $('.switch').simpleSwitch();

    $('#status_approval').on('click',function(){approvePurchase();});

    $('#discount_general').on('blur',function(){calculateGralDiscount();});

    $("#date,#due_date,#dateDatePicker,#expiration_date,#expirationDatePicker").datetimepicker({format: 'MM/DD/YYYY'});

    $('#store_id,#vendor,#location,#taxes,#taxes_included,#method_payment').select2(); 

    

    /*

    $('#vendor').on('change',function(){getVendorMethodPayment();});    

    $('#date').on('dp.change',function(){setDueDate();});    

    $('#credit_days').on('change',function(){setDueDate();});    

    

    $('#method_payment').on('change',function(){

        $('#credit_days').val('0');

        if(this.value == '2'){$('#credit_days').prop('readOnly',false);}

        else if(this.value == '1'){$('#credit_days').prop('readOnly',true);}

        setDueDate();

    });*/

    

    $('#agregar_producto').on('click',function(){

        clearModalAddProduct();

        _getTranslation('Agregar producto',function(msj){ $('#title_modal_purchaseProduct').html(msj);});

        $('#modalAddPurchaseProduct').modal('show');

    });

    $('._addProduct').on('click',function(){setPurchaseDetails()});

    $('._closeModalAddProduct').on('click',function(){clearModalAddProduct();$('#modalAddPurchaseProduct').modal('hide');});

    

     $("#invoice_file,#credit_note_file").fileinput({

        showPreview: false,

        allowedFileExtensions: ['pdf'],

        maxFileSize: 1000,

        showUpload:false,

        showRemove:false

    });

    

    $( "#product" ).autocomplete({

        source: function( request, response ) {

          $.post('/Controller/Purchase.php', {

              action: 'ajax',

              request: 'getListProduct',

              item: request.term

          }, function(data) {

                  response(data.products);

             }, 'json');

        }, 



        focus: function( event, ui ) {

            $( "#product" ).val( ui.item.label );

            return false;

          },      

        select: function( event, ui ) {

            $( "#id_product" ).val( ui.item.value ); 

            getDefaultDataProduct();

            return false;

          }    

    } );     

</script>