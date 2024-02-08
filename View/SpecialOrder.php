<section class="content-header">

    <h1><i class='fa-fw fa fa-birthday-cake'></i> <?php echo $_translator->_getTranslation('Pedido especial'); if($action == 'edit'){echo " #".$specialOrder->getReqNumber();}?></small></h1>

    <ol class="breadcrumb">

        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>

        <li><a href="<?php echo ROOT_HOST?>/Controller/SpecialOrder.php?action=list"><?php echo $_translator->_getTranslation('Lista de pedidos especiales')?></a></li>

      <li class="active"><?php echo $_translator->_getTranslation('Pedidos especiales');?></li>

    </ol>

</section>

<section class="content">

<div class="box">

    <?php echo $form->openForm();?>

    <div style="display: none"><?php

        echo $form->showActionController();

        echo $form->showId();

        $form->showElement('status');

        $form->showElement('req_number');

        $form->showElement('ammount');

        $form->showElement('ammount_payments');

        $form->showElement('antiguedad');

        $form->showElement('idProductForSpecialDecorated');

        $form->showElement('id_shape_for_letter');

        $form->setIdShapeForNumbers();

        $form->showElement('id_shape_for_number');

        $form->showElement('allow_edit');

        $form->showElement('role_logued');?>

    </div>

    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>

    <div class="box-header with-border">

        <div class="box-tools">

            <?php 

            if($action === 'edit'){?>

                <a href="SpecialOrder.php" class="btn btn-default"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar orden especial')?></a>

                <a class="btn btn-default" href="#" onclick="javascript: void window.open('SpecialOrder.php?action=export&flag=pdf&id=<?php echo $form->getId();?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-print fa-"></i> <?php echo $_translator->_getTranslation('Imprimir') ?></a><?php 

            }?>

        </div><!-- /.box-tools -->

    </div><!-- /.box-header -->

    <div class="box-body">

     <div class="clear"></div>

            <div class="col-xs-12 col-md-12">

                <div class="col-xs-12 col-md-6">                    

                    <?php $form->showElement('store_id');?> 

                    <?php $form->showElement('date');?> 

                    <?php $form->showElement('delivery_date');?>

                    <?php $form->showElement('customer');?>

                    <?php $form->showElement('phone');?>

                    <?php $form->showElement('email');?>         

                </div> 

                <div class="col-xs-12 col-md-6">         

                    <?php $form->showElement('home_service');?>

                    <?php $form->showElement('address');?>

                    <?php $form->showElement('city');?>                    

                    <?php $form->showElement('zipcode');?>     

                    <?php 

                    if($action == 'edit'){

                        $form->showElement('status_delivery');

                    }?>

                </div>

            </div>

            <div class="clear"></div>

            <div class="col-xs-12 col-md-12">

                <hr/>

                <?php 

                    $compraAjax = new SpecialOrderAjax();

                    $listCompraDetalles = $compraAjax->getListRequisitionDetails($form->getTokenForm());?>

                <div class='table-responsive'>

                    <table id='requisition-table' class="table table-condensed table-striped table-hover">

                    <thead>                

                        <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Accion')?></th>

                        <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Cantidad')?></th> 

                        <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Tipo');?></th>

                        <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Forma/Tamaño');?></th>

                        <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Pan');?></th>

                        <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Relleno')?></th> 

                        <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Decorado')?></th> 

                        <th class="col-lg-1 text-right"><?php echo $_translator->_getTranslation('Precio')?></th> 

                        <th class="col-lg-1 text-right"><?php echo $_translator->_getTranslation('Total')?></th> 

                    </thead>

                    <tbody>

                        <?php echo $listCompraDetalles['requisitionDetails'];?>

                    </tbody>                    

                    <tfoot>

                        <tr>

                          <td class="text-right" colspan="8"><?php echo $_translator->_getTranslation('Total');?></td>

                          <td class="text-right"><span id='grandTotal'><?php echo number_format($listCompraDetalles['grandTotal'],2)?></span></td>                           

                        </tr>

                    </tfoot>

                    </table>

                </div>

                <br/>

                <div class="pull-right" >

                    <?php $element = $form->getElement('agregar_detalle');?>

                    <?php echo $form->createElement($element);?>

                </div>

                <div class="pull-right" style="margin-right: 10px">

                    <?php $element = $form->getElement('agregar_extra');?>

                    <?php echo $form->createElement($element);?>

                </div>

                <div class="pull-right" style="margin-right: 10px">

                    <?php $element = $form->getElement('agregar_detalle_wizard');?>

                    <?php echo $form->createElement($element);?>

                </div>

            </div>   

            <div class="clear"></div><br/>

            <div class="col-xs-12 col-md-12">

                <div class="col-md-6 col-xs-12">

                    <div>

                        <label><?php echo $_translator->_getTranslation('Notas');?></label>

                        <?php $element = $form->getElement('comments'); $form->createElement($element);?>

                    </div>   

                </div>

                <div class="col-md-6 col-xs-12">

                    <div>

                        <label><?php echo $_translator->_getTranslation('Detalles decorado');?></label>

                        <?php $element = $form->getElement('comments_1'); $form->createElement($element);?>

                    </div>       

                    

                </div>

            </div>

            <div class="col-xs-12 col-md-12">

                <div class="col-md-6 col-xs-12">

                    <div class="m-t-1 p-r-2"><?php $form->showElement('image[]');?></div>

                </div>

                <div class="col-md-6 col-xs-12">

                    <?php echo $specialOrder->showImages();?>

                </div>

            </div>

            <div class="pull-right">

                <?php $element = $form->getElement('terminar');?>

                <?php echo $form->createElement($element);?>

                <?php $element = $form->getElement('btn_allow_edit');?>

                <?php echo $form->createElement($element);?>

            </div>

  </div><!-- /.box-body -->

  <?php 

  include ROOT."/View/Modal/addSliceToSpecialOrderWizard.php";

  include ROOT."/View/Modal/addSliceToSpecialOrder.php";  

  echo $form->closeForm();

  include ROOT."/View/Modal/addCustomerFromSpecialOrder.php";

  ?>

</div><!-- /.box -->

</section>

<style>

</style>

<?php if(isset($_disabled) && $_disabled){?><script>$('#requisition-table tr td a').addClass('disabled');</script><?php } ?>

<?php if(isset($_allow_payments) && $_allow_payments === null){?><script>$("#add_payment").prop("onclick", null).off("click");</script><?php } ?>

<script>  

    $('#store_id,#customer,#home_service,#status_delivery,#status_payment,#status_production').select2();

    $('#size,#shape,#category,#product,#type').select2({placeholderOption: 'first'});

    

    $("#date,#datePicker").datetimepicker({format: 'MM/DD/YYYY',ignoreReadonly: true,minDate: '<?php echo $_minDate; ?>',maxDate: '<?php echo $_currentDate; ?>',useCurrent: false});

    $("#delivery_date").datetimepicker({format: "MM/DD/YYYY hh:mm A ",ignoreReadonly: true,useCurrent: false});

    $("#date").on('dp.change',function(e){if(e.oldDate !== null){setMinDeliveryDate(e);}});

    

    /*MANUAL VITRINA*/

    $('#agregar_detalle').on('click',function(){

        $('#type').html("<option value='Line' selected>Line</option>");

        clearModalAddSliceToSpecialOrder(function(){setUnsetCategoryField();});

        $('#size').parent('div').parent('div').hide();

        _getTranslation('Agregar pastel de vitrina',function(msj){ $('#title_modal_AddSliceToSpecialOrder').html(msj);});

        $('#modalAddSliceToSpecialOrder').modal('show');

    });

    

    $('._closeModalAddSliceToSpecialOrder').on('click',function(){clearModalAddSliceToSpecialOrder();$('#modalAddSliceToSpecialOrder').modal('hide');});

    

    $('#customer').on('select2:select',function(){setCustomerData();});

    $('#type').on('select2:select',function(){clearModalAddSliceToSpecialOrder();setUnsetCategoryField();});

    $('#category').on('select2:select',function(){setSlicesForSpecialOrder(null);});

    $('#product').on('select2:select',function(){setProductPrice();$('#quantity').focus();});

    $('#size').on('select2:select',function(){ 

        if($('#type').val() === 'Line'){

            setUnsetCategoryField();

        }else{

            setShapesBySize();

        }         

    });

    

     $('#shape').on('select2:select',function(){

        $('#product').val('').trigger('change');

        $('#price').val('');

        if($('#type').val() === 'Special'){setSlicesForSpecialOrder(null);}

        if($('#type').val() === 'Line'){setUnsetCategoryField();}

    });

    

    /*MANUAL EXTRAS*/

    $('#agregar_extra').on('click',function(){

        <?php $settings = new SettingsRepository(); $extraCategoryId = $settings->_get('id_category_for_extra_cakes');  ?>

        $('#type').html("<option value='Special' selected>Special</option>");

        $('#category').html("<option value='<?php echo $extraCategoryId ?>' selected>Extra</option>");

        clearModalAddSliceToSpecialOrder();

        setExtrasListToSpecialOrder();

        _getTranslation('Agregar extra a pastel',function(msj){ $('#title_modal_AddSliceToSpecialOrder').html(msj);});

        $('#modalAddSliceToSpecialOrder').modal('show');

    });

    

    $('._closeModalAddSliceToSpecialOrder').on('click',function(){clearModalAddSliceToSpecialOrder();$('#modalAddSliceToSpecialOrder').modal('hide');});

    

    $('._saveCustomer').on('click',function(){saveCustomer();});

    $('#btn_allow_edit').on('click',function(){allowEditSpecialOrder();});

    setMinDeliveryDate();

    

     $(".imagesInput").fileinput({

        allowedFileExtensions: ['jpg', 'png', 'gif','jpeg'],

        maxFileSize: 1000,

        showUpload: false

    });

    

    var table = $('#requisition-table').DataTable({ searching:false,

            paging:false,

            aaSorting:[],

            columnDefs: [{orderable: false, targets: "_all" }]});

        

    if($('#role_logued').val() !== '1'){table.columns([5,6]).visible( false );}



</script>