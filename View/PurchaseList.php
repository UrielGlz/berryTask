<section class="content-header">
    <h1><i class='fa-fw fa fa-shopping-basket'></i> <?php echo $_translator->_getTranslation('Lista de compras');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
        <li class="active"><?php echo $_translator->_getTranslation('Lista de compras');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
       <a href="Purchase.php" class="btn btn-default pull-right"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar compra')?></a> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="clear"></div>
    <div class='table-responsive'>
        <table id="tblPurchase" class="table table-bordered table-striped table-hover table-condensed font-size-12 datatable_whit_filter_column">
            <thead>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Compra #');?></th>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Factura #');?></th>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Proveedor');?></th>                    
            <th class="col-lg-1 text-right"><?php echo $_translator->_getTranslation('Total');?></th>
            <th class="col-lg-1 text-right"><?php echo $_translator->_getTranslation('Saldo');?></th>                             
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Aprobacion');?></th>  
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Status');?></th>              
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
            <th class="col-lg-1 text-center">Accion</th>
            </thead>
            <tfoot>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Compra #');?></th>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Factura #');?></th>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Proveedor');?></th>                    
            <th class="filter text-right"><?php echo $_translator->_getTranslation('Total');?></th>
            <th class="filter text-right"><?php echo $_translator->_getTranslation('Saldo');?></th>                             
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Aprobacion');?></th>  
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Status');?></th>              
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
            <th></th>
            </tfoot>
            <tbody>
            <?php 
                if($_listPurchase){
                    foreach($_listPurchase as $compra){?>
                        <tr>
                            <td class="text-center"><?php echo $compra['id']?></td>
                            <td class="text-center"><?php echo $compra['date']?></td>                                    
                            <td class="text-center"><?php echo $compra['reference']?></td>
                            <td class="text-center"><?php echo $compra['vendor']?></td>
                            <td class="text-right"><?php echo number_format($compra['total'],2);?></td>
                            <td class="text-right"><?php echo number_format($compra['balance'],2);?></td>
                            <td class="text-center">
                            <?php
                                if($isUserApprover && $compra['status'] != '3' && $compra['status'] != '4'){
                                    $checked = ''; 
                                    $disabled = '';
                                    if($compra['status_approval'] != '0'){
                                        $checked = 'checked'; 
                                        /*status_approval = 2; aprobado por sistema     status = 3; recibida*/
                                        /*Aprobado por sistema o recibida ya no se puede desaprobar*/
                                        if($compra['status_approval'] == '2'){$disabled = 'disabled';}
                                    }?>
                                <input type="checkbox" class="switch" data-purchaseid="<?php echo $compra['id']?>" <?php echo $checked.' '.$disabled?> /><?php
                                }else{
                                    echo $compra['statusApprovalName'];
                                }?>
                            </td>                                                        
                            <td class="text-center"><?php echo $compra['statusName']?></td>                            
                            <td class="text-center"><?php echo $compra['storeName']?></td>
                            <td class="text-center">
                                <a class="btn btn-sm btn-default" href="Purchase.php?action=edit&id=<?php echo $compra['id']?>"><i class="fa fa-pencil"></i></a>
                                <a class="btn btn-sm btn-default" href="#" onclick="javascript: void window.open('Purchase.php?action=export&flag=pdf&id=<?php echo $compra['id']?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-file-pdf-o fa-"></i></a>                                
                                <?php
                                if($compra['status'] != '3' && $compra['status'] != '4'){?>
                                    <a class="btn btn-sm btn-danger" onclick="return confirmDelete('<?php echo $_translator->_getTranslation('Esta seguro de cancelar este registro ?')?>',this)" href="Purchase.php?action=delete&id=<?php echo $compra['id']?>"><i class="fa fa-trash"></i></a>
                                <?php
                                }else{?>
                                    <a class="btn btn-sm btn-danger" href="#"><i class="fa fa-trash"></i></a>                                    
                                <?php
                                } ?>
                                
                            </td>
                        </tr><?php
                    }                   
                }?>
            </tbody>
        </table>
    </div>
  </div><!-- /.box-body -->
</div><!-- /.box -->
</section>
<script type="text/javascript" language="javascript">
    $('.switch').simpleSwitch();
    $('.switch').on('click',function(){approvePurchaseList(this);});
    $('#tblPurchase').DataTable({
            paginate:false,
            filter:true,
            aaSorting:[],
            dom: 'Bfrtip',
            buttons: [
            'excel'
            ]
        });
</script>