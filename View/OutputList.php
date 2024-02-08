<section class="content-header">
    <h1><i class='fa-fw fa fa-upload'></i> <?php echo $_translator->_getTranslation('Lista de salidas');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
        <li class="active"><?php echo $_translator->_getTranslation('Lista de salidas');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
        <a href="Output.php" class="btn btn-default pull-right"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar salida')?></a> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="clear"></div>
    <div class='table-responsive'>
        <table id="tblOutput" class="table table-bordered table-striped table-hover table-condensed datatable_whit_filter_column">
            <thead>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Salida #');?></th>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>       
            <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>             
            <th class="col-lg-3 text-center"><?php echo $_translator->_getTranslation('Requerido por');?></th>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Status');?></th>
            <th class="col-lg-1 text-center">Accion</th>
            </thead>
            <tfoot>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Salida #');?></th>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>       
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>             
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Requerido por');?></th>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Status');?></th>
            <th></th>
            </tfoot>
            <tbody>
            <?php 
                if($_listOutputs){
                    foreach($_listOutputs as $salida){?>
                        <tr>
                            <td class="text-center"><?php echo $salida['id']?></td>                                    
                            <td class="text-center"><?php echo $salida['date']?></td>    
                            <td class="text-center"><?php echo $salida['storeName']?></td>
                            <td class="text-center"><?php echo $salida['requested_by']?></td>
                            <td class="text-center"><?php echo $salida['statusName']?></td>
                            <td class="text-center">
                                <a class="btn btn-sm btn-default" href="Output.php?action=edit&id=<?php echo $salida['id']?>"><i class="fa fa-pencil"></i></a>
                                <a class="btn btn-sm btn-default" href="#" onclick="javascript: void window.open('Output.php?action=export&flag=pdf&id=<?php echo $salida['id']?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-file-pdf-o fa-"></i></a>
                                <a class="btn btn-sm btn-danger" onclick="return confirmDelete('<?php echo $_translator->_getTranslation('Esta seguro de cancelar este registro ?')?>',this)" href="Output.php?action=delete&id=<?php echo $salida['id']?>"><i class="fa fa-trash"></i></a>
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
    $('#tblOutput').DataTable({   
            paginate:false,
            filter:true,
            aaSorting:[],
            dom: 'Bfrtip',
            buttons: [
            'excel'
            ]
        });
</script>