<section class="content-header">
    <h1><i class='fa-fw fa fa-upload'></i> <?php echo $_translator->_getTranslation('Traspasos');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
        <li><a href="<?php echo ROOT_HOST?>/Controller/Transfer.php?action=list"><?php echo $_translator->_getTranslation('Lista de traspasos')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Traspasos');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
    <?php echo $form->openForm();?>
    <div style="display: none"><?php
        echo $form->showActionController();
        echo $form->showId();
        echo $form->showElement('status');
        echo $form->showElement('id_product');
        echo $form->showElement('idDetailTemp');
        ?>
    </div>
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="box-header with-border">
        <div class="box-tools">
            <?php 
            if($action === 'edit'){?>
            <a href="Transfer.php" class="btn btn-default"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar traspaso')?></a>
            <a class="btn btn-default" href="#" onclick="javascript: void window.open('Transfer.php?action=export&flag=pdf&id=<?php echo $form->getId();?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-print fa-"></i> <?php echo $_translator->_getTranslation('Imprimir') ?></a><?php 
            
            }?>
        </div><!-- /.box-tools -->
    </div><!-- /.box-header -->
    <div class="box-body">
     <div class="clear"></div>
            <div class="col-xs-12 col-md-12">
                <div class="col-xs-12 col-md-6">
                    <?php 
                    $form->showElement('date');
                    $form->showElement('from_store_id');
                    $form->showElement('to_store_id');
                    $form->showElement('requested_by');?>
                </div> 
                <div class="col-xs-12 col-md-6">
                    <?php $form->showElement('comments');?>
                </div> 
            </div>
            <div class="clear"></div>
            <div class='col-md-12'>
                <hr/>
                <?php 
                    $transferAjax = new TransferAjax();
                    $edit = null;
                    if($action=='edit'){$edit = true;}                    
                    $listTransferDetalles = $transferAjax->getListTransferDetails($form->getTokenForm());?>
                <div class='table-responsive'>
                <table id='transfer-table' style="font-size:11px" class="table table-condensed table-striped table-hover">
                    <thead>                
                        <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Accion')?></th>
                        <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Codigo');?></th>
                        <th class="col-lg-3"><?php echo $_translator->_getTranslation('Descripcion');?></th>   
                        <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Presentacion');?></th>
                        <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Marca');?></th>
                        <th class="col-lg-1 text-right"><?php echo $_translator->_getTranslation('Cantidad')?></th>   
                        <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Locacion')?></th>
                    </thead>
                    <tbody>
                        <?php echo $listTransferDetalles['transferDetails'];?>
                     </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-right"><?php echo $_translator->_getTranslation('Total');?></th>
                            <th id='totalItems' class="text-right"><?php echo number_format($listTransferDetalles['totalItems'],2);?></th>
                            <!--<th><?php //echo $form->showElement('total'); ?></th>-->
                        </tr>
                    </tfoot>
                </table>
                </div>
                <div class='col-lg-1'>
                    <?php $element = $form->getElement('quantity');?>
                    <div class='col-lg-12 p-a-0'><label><?php echo $element['label']?></label></div>
                    <?php echo $form->createElement($element);?>
                </div>
                <div class='col-lg-5'>
                    <?php $element = $form->getElement('product');?>
                    <div class='col-lg-12 p-a-0'><label><?php echo $element['label']?></label></div>
                    <?php echo $form->createElement($element);?>
                </div>
                <div class='col-lg-3'>
                    <?php $element = $form->getElement('location');?>
                    <div class='col-lg-12 p-a-0'><label><?php echo $element['label']?></label></div>
                    <?php echo $form->createElement($element);?>
                </div>
                <div class='col-lg-3' style="padding-top: 25px">
                    <?php $element = $form->getElement('agregar_producto');?>
                    <?php echo $form->createElement($element);?>
                </div>
            </div>
            <!-- Modal -->         
            <div class="col-lg-12">
                <div class="pull-right">
                    <div style='float:left'></div>
                    <div style='float:left'>
                        <?php $element = $form->getElement('terminar');?>
                        <?php echo $form->createElement($element);?>
                    </div>
                </div>
                <div class='clear'></div>
            </div>            
  </div><!-- /.box-body -->
  <?php echo $form->closeForm();?>
</div><!-- /.box -->
</section>
<?php 
    if(isset($_disabled) && $_disabled === true){?>
        <script>$('table#transfer-table tbody tr td a').addClass('disabled'); $('form[name=transfer] :input').prop('disabled',true); </script>
<?php    }
    ?>
<script> 
    $('#from_store_id,#to_store_id,#location').select2();
    $("#date").datetimepicker({format: 'MM/DD/YYYY hh:mm A'}); 
    $('#agregar_producto').on('click',function(){
        setTransferDetails();
    });
    $('._addProduct').on('click',function(){});
    
    $( "#product" ).autocomplete({
        source: function( request, response ) {
          $.post('/Controller/Transfer.php', {
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
            //$("#product").val('');
            validateLocationsTransfer();
            return false;
          }    
    } );     
    
    
    
</script>