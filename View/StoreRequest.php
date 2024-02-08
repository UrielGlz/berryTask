<section class="content-header">

    <h1><i class='fa-fw fa fa-bookmark'></i> <?php echo $_translator->_getTranslation('Pedidos de sucursal');?></small></h1>

    <ol class="breadcrumb">

        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>

        <li><a href="<?php echo ROOT_HOST?>/Controller/StoreRequest.php?action=list"><?php echo $_translator->_getTranslation('Lista de pedidos de sucursal')?></a></li>

      <li class="active"><?php echo $_translator->_getTranslation('Pedidos de sucursal');?></li>

    </ol>

</section>

<section class="content">

<div class="box">

    <?php echo $_form->openForm();?>

    <div style="display: none"><?php

        echo $_form->showActionController();

        echo $_form->showId();

        $_form->showElement('status');?>

    </div>

    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>

    <div class="box-header with-border">

        <div class="box-tools">

            <?php 

            if($action === 'edit'){?>

                <a href="StoreRequest.php" class="btn btn-default"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar pedido de sucursal')?></a>

                <a class="btn btn-default" href="#" onclick="javascript: void window.open('StoreRequest.php?action=export&format=pdf&flag=store_request&id=<?php echo $_form->getId();?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-print fa-"></i> <?php echo $_translator->_getTranslation('Imprimir') ?></a><?php 

            }?>

        </div><!-- /.box-tools -->

    </div><!-- /.box-header -->

    <div class="box-body">

     <div class="clear"></div>

            <div class="col-xs-12 col-md-12">

                <div class="col-xs-12 col-md-6">                      

                    <?php $_form->showElement('store_id');?> 

                    <?php $_form->showElement('area_id');?>   

                    <?php $_form->showElement('date');?>  

                    <?php $_form->showElement('delivery_date');?>  

                </div> 

                <div class="col-xs-12 col-md-6">                    

                    <?php $_form->showElement('comments');?>

                    <?php if($action == 'edit'){$_form->showElement('statusName');} ?>     

                </div> 

            </div>

            <div class="clear"></div>

            <div class="col-xs-12 col-md-12">

                <?php 

                $storeRequestAjax = new StoreRequestAjax();

                $listStoreRequestDetalles = $storeRequestAjax->getListStoreRequestDetalles($_form->getTokenForm());?>

                <h4><?php echo $_translator->_getTranslation('Detalles de pedido');?></h4>

                <hr/>

                <div class="table-responsive">

                    <table id='storeRequestDetails' class="table table-condensed table-striped table-hover font-size-11 datatable_whit_filter_column _hideSearch" style="width:100%">

                        <thead>                

                            <th class="col-md-3 text-center"><?php echo $_translator->_getTranslation('Producto')?></th>

                            <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Ultimo inventario fisico');?>

                            <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Pendiente por recibir');?></th>

                            <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Pedido');?></th>

                        </thead>

                        <tfoot>   

                        <th class="filter"><?php echo $_translator->_getTranslation('Producto');?></th>

                        <th></th>

                        <th></th>

                        <th></th>

                        </tfoot>

                        <tbody>

                            <?php echo $listStoreRequestDetalles['storeRequestDetalles'];?>

                        </tbody>

                    </table>   

                </div>

            </div>   

            <div class="pull-right">

                <?php $element = $_form->getElement('terminar');?>

                <?php echo $_form->createElement($element);?>

            </div>

  </div><!-- /.box-body -->

  <?php echo $_form->closeForm();?>

</div><!-- /.box -->

</section>

<style>

</style>

<?php if(isset($_disabled) && $_disabled){?> 

    <script>

        $('#storeRequestDetails tr td input').addClass('disabled');

        $('._storeRequestQuantity').prop('disabled',true);

    </script>

<?php } ?>



<script>  

    $('#date,#delivery_date').datetimepicker({format: 'MM/DD/YYYY',useCurrent: false});       

    $('#store_id,#area_id').select2();

    $('#terminar').on('click',function(){

       updateStoreRequestQty(function(){submit('storeRequestForm');});

    });

    

    $('#store_id').on('select2:select',function(){thereIsOrderForToday(this);});

    $('#delivery_date').on('dp.change',function(){ thereIsOrderForToday(this);});

    

    $('select').on('select2:opening',function(e){

        e.preventDefault();

        if($(this).attr('readonly') !== 'readonly'){$(this).unbind('select2:opening').select2('open');}

    });

    

    $('#area_id').on('select2:select',function(){        

        $('#delivery_date').data("DateTimePicker").destroy();

        $('#delivery_date').datetimepicker({format: 'MM/DD/YYYY',useCurrent: false});             

        $('#delivery_date').data("DateTimePicker").clear();

        

        if($(this).val() === '<?php echo $_settings->_get('id_area_for_pasteles_vitrina') ?>'){ 

            $('#delivery_date').data("DateTimePicker").options({daysOfWeekDisabled:[0,2,3,5,6]});

        }

        

        thereIsOrderForToday(this);        

    });

   

</script>