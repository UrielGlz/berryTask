<section class="content-header">
    <h1><i class='fa-fw fa fa-bookmark'></i> <?php echo $_translator->_getTranslation('Inventario fisico');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
        <li><a href="<?php echo ROOT_HOST?>/Controller/PhysicalInventory.php?action=list"><?php echo $_translator->_getTranslation('Lista de inventarios fisicos')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Inventario fisico');?></li>
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
                <a href="PhysicalInventory" class="btn btn-default"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar pedido de sucursal')?></a>
                <a class="btn btn-default" href="#" onclick="javascript: void window.open('PhysicalInventory?action=export&format=pdf&flag=physical_inventory&id=<?php echo $_form->getId();?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-print fa-"></i> <?php echo $_translator->_getTranslation('Imprimir') ?></a><?php 
            }?>
        </div><!-- /.box-tools -->
    </div><!-- /.box-header -->
    <div class="box-body">
     <div class="clear"></div>
            <div class="col-xs-12 col-md-12">
                <div class="col-xs-12 col-md-6">           
                    <?php $_form->showElement('date');?>  
                    <?php $_form->showElement('store_id');?>     
                </div> 
                <div class="col-xs-12 col-md-6">                    
                    <?php $_form->showElement('comments');?>
                    <?php if($action == 'edit'){$_form->showElement('statusName');} ?>     
                </div> 
            </div>
            <div class="clear"></div>
            <div class="col-xs-12 col-md-12">
                <h4><?php echo $_translator->_getTranslation('Detalles de inventario fisico');?></h4>
                <hr/>
                <div class="card">
                <?php 
                $physicalInventoryAjax = new PhysicalInventoryAjax();
                $settings = new SettingsRepository();?>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#tab_panaderia" aria-controls="home" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Panaderia') ?></a></li>
                    <li role="presentation"><a href="#tab_pasteleria" aria-controls="profile" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Pasteleria') ?></a></li>
                    <li role="presentation"><a href="#tab_miniatura" aria-controls="profile" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Miniaturas') ?></a></li>            
                    <li role="presentation"><a href="#tab_otros" aria-controls="profile" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Otros') ?></a></li>    
                </ul>
                <!-- Tab panes -->
                <div class="tab-content" style="padding-top: 20px">
                    <div role="tabpanel" class="tab-pane active" id="tab_panaderia">
                        <?php                 
                        $listStoreRequestDetalles = $physicalInventoryAjax->getListPhysicalInventoryDetalles($_form->getTokenForm(),$settings->_get('id_area_for_panaderia'));?>                        
                        <div class="table-responsive">
                            <table id='physicalInventoryPanaderia' class="table table-condensed table-striped table-hover font-size-11 datatable_whit_filter_column _hideSearch" style="width:100%">
                                <thead>                
                                    <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Producto')?></th>
                                    <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Tamaño')?></th>
                                    <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Stock');?></th>
                                </thead>
                                <tfoot>   
                                <th class="filter"><?php echo $_translator->_getTranslation('Producto');?></th>
                                <th class="filter"><?php echo $_translator->_getTranslation('Tamaño');?></th>
                                <th></th>
                                </tfoot>
                                <tbody>
                                    <?php echo $listStoreRequestDetalles['physicalInventoryDetalles'];?>
                                </tbody>
                            </table>   
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_pasteleria">
                        <?php                 
                        $listStoreRequestDetalles = $physicalInventoryAjax->getListPhysicalInventoryDetalles($_form->getTokenForm(),$settings->_get('id_area_for_pasteles_vitrina'));?>                        
                        <div class="table-responsive">
                            <table id='physicalInventoryPasteleria' class="table table-condensed table-striped table-hover font-size-11 datatable_whit_filter_column _hideSearch" style="width:100%">
                                <thead>                
                                    <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Producto')?></th>
                                    <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Tamaño')?></th>
                                    <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Stock');?></th>
                                </thead>
                                <tfoot>   
                                <th class="filter"><?php echo $_translator->_getTranslation('Producto');?></th>
                                <th class="filter"><?php echo $_translator->_getTranslation('Tamaño');?></th>
                                <th></th>
                                </tfoot>
                                <tbody>
                                    <?php echo $listStoreRequestDetalles['physicalInventoryDetalles'];?>
                                </tbody>
                            </table>   
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_miniatura">
                        <?php                 
                        $listStoreRequestDetalles = $physicalInventoryAjax->getListPhysicalInventoryDetalles($_form->getTokenForm(),$settings->_get('id_area_for_miniatura'));?>                        
                        <div class="table-responsive">
                            <table id='physicalInventoryMiniatura' class="table table-condensed table-striped table-hover font-size-11 datatable_whit_filter_column _hideSearch" style="width:100%">
                                <thead>                
                                    <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Producto')?></th>
                                    <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Tamaño')?></th>
                                    <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Stock');?></th>
                                </thead>
                                <tfoot>   
                                <th class="filter"><?php echo $_translator->_getTranslation('Producto');?></th>
                                <th class="filter"><?php echo $_translator->_getTranslation('Tamaño');?></th>
                                <th></th>
                                </tfoot>
                                <tbody>
                                    <?php echo $listStoreRequestDetalles['physicalInventoryDetalles'];?>
                                </tbody>
                            </table>   
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_otros">
                        <?php                 
                        $listStoreRequestDetalles = $physicalInventoryAjax->getListPhysicalInventoryDetalles($_form->getTokenForm(),$settings->_get('id_area_for_otros'));?>                        
                        <div class="table-responsive">
                            <table id='physicalInventoryOtros' class="table table-condensed table-striped table-hover font-size-11 datatable_whit_filter_column _hideSearch" style="width:100%">
                                <thead>                
                                    <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Producto')?></th>
                                    <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Tamaño')?></th>
                                    <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Stock');?></th>
                                </thead>
                                <tfoot>   
                                <th class="filter"><?php echo $_translator->_getTranslation('Producto');?></th>
                                <th class="filter"><?php echo $_translator->_getTranslation('Tamaño');?></th>
                                <th></th>
                                </tfoot>
                                <tbody>
                                    <?php echo $listStoreRequestDetalles['physicalInventoryDetalles'];?>
                                </tbody>
                            </table>   
                        </div>
                    </div>
                </div>
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
        $('#physicalInventoryDetails tr td input').addClass('disabled');
        $('._physicalInventoryQuantity').prop('disabled',true);
    </script>
<?php } ?>
<style>
    tfoot {
    display: table-header-group;
}
</style>
<script>  
    $('#date').datetimepicker({format: 'MM/DD/YYYY'});       
    $('#store_id').select2();
    $('#terminar').on('click',function(){
       updatePhysicalInventoryQty(function(){submit('physicalInventoryForm');});
    });
    
    $('#store_id').on('select2:select',function(){
        thereIsPhysicalInventoryForToday(function(r){
            if(r.response){
                $('#store_id').val('').trigger('cahnge')
                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: 'Mensaje',
                    content: r.msg,
                    buttons:{                
                        cancel: {
                            text:'Cerrar',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function(){
                               $(this).remove();
                            }
                        },
                        confirm: {
                            text: 'Ir a inventario fisico',
                            btnClass: 'btn-primary col-md-5 pull-right',
                            action: function(){
                               document.location = 'PhysicalInventory.php?action=edit&id='+r.physicalInventory;
                            }
                        }
                    }
                });        
            }             
        });
    });
    
    $('select').on('select2:opening',function(e){
        e.preventDefault();
        if($(this).attr('readonly') !== 'readonly'){$(this).unbind('select2:opening').select2('open');}
    });
    
    
   
</script>