<div class="container-title"></div>
<div class="row">
    <div class="col-lg-12"><ol class="breadcrumb">
            <?php      
            $currentAction = '';
            $agregarNuevo = '';
            if($action =='edit'){
                $currentAction = "<li class='active'><i class='fa fa-pencil'></i> ".$_translator->_getTranslation('Editar')."</li>";
                $agregarNuevo = "<a href='Shipment.php' class='btn btn-primary'><i class='fa fa-plus'></i> ".$_translator->_getTranslation('Agregar envio a sucursal')."</a>";
            }elseif($action =='' || $action =='insert'){
                $currentAction = "<li class='active'><i class='fa fa-plus'></i> ".$_translator->_getTranslation('Agregar')."</li>";
            } ?>
            <li><i class="fa fa-dashboard"></i>  <a href="<?php echo ROOT_HOST?>/Controller/Home.php"><?php echo $_translator->_getTranslation('Inicio')?></a></li>
            <li class="active"><i class="fa fa-cab"></i> <?php echo $_translator->_getTranslation('Lista de envios a sucursal')?></li>
            <?php echo $currentAction?>
        </ol>
    </div>
</div>
<div id="flashmessenger"><?php $flashmessenger->showMessage();?></div>
<div class="box">
    <div class="box-header">       
        <h3><i class='fa-fw fa fa-cab'></i><?php echo $_translator->_getTranslation('Lista de envios a sucursal');?></h3>
        <div class='box-icon'><i class='icon fa fa-tasks'></i></div>
    </div>
    <div class='box-content'>
        <div class='row'>
            <div class="col-lg-12">
                <div class="pull-right"><a href='Shipment.php' class='btn btn-default'><i class='fa fa-plus' style='color:black'></i> <?php echo $_translator->_getTranslation('Agregar envio')?></a></div>
                <div class="clear" ></div>
                <hr/>
                <div class='table-responsive'>
                <table id="tblShipment" class="table table-bordered table-striped table-hover table-condensed datatable_whit_filter_column">
                    <thead>
                    <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('No.');?></th>
                    <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>
                    <th class="col-md-2 text-left"><?php echo $_translator->_getTranslation('Para');?></th>                     
                    <th class="col-md-1 text-right"><?php echo $_translator->_getTranslation('Enviado');?></th>
                    <th class="col-md-1 text-right"><?php echo $_translator->_getTranslation('Recibido');?></th>
                    <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Status');?></th>
                    <th class="col-lg-1 text-center">Accion</th>
                    </thead>
                    <tfoot>
                    <th class="filter text-center"><?php echo $_translator->_getTranslation('No.');?></th>
                    <th class="filter text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>
                    <th class="filter text-left"><?php echo $_translator->_getTranslation('Para');?></th>                     
                    <th class="filter text-right"><?php echo $_translator->_getTranslation('Enviado');?></th>
                    <th class="filter text-right"><?php echo $_translator->_getTranslation('Recibido');?></th>
                    <th class="filter text-center"><?php echo $_translator->_getTranslation('Status');?></th>
                    <th></th>
                    </tfoot>
                    <tbody>
                    <?php 
                        if($_listShipments){
                            foreach($_listShipments as $shipment){?>
                                <tr>
                                    <td class="text-center"><?php echo $shipment['num_shipment']?></td>
                                    <td class="text-center"><?php echo $shipment['date']?></td>   
                                    <td class="text-center"><?php echo $shipment['toName']?></td>  
                                    <td class="text-right"><?php echo $shipment['quantity']?></td>
                                    <td class="text-right"><?php echo $shipment['received']?></td>
                                    <td class="text-center"><?php echo $shipment['statusName']?></td>
                                    <td class="text-center">
                                        <a class="btn btn-sm btn-primary" href="Shipment.php?action=edit&id=<?php echo $shipment['id']?>"><i class="fa fa-pencil fa-"></i></a>
                                        <a class="btn btn-sm btn-info" href="#" onclick="javascript: void window.open('Shipment.php?action=import&flag=pdf&id=<?php echo $shipment['id']?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-file-pdf-o fa-"></i></a>
                                        <a class="btn btn-sm btn-danger" onclick="return deleteRegistry('Shipment.php?action=delete&id=<?php echo $shipment['id']?>')" 
                                               href="#"><i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr><?php
                            }                   
                        }?>
                    </tbody>
                </table>
                </div>
            </div>                
        </div>
    </div>
</div>
<script type="text/javascript" language="javascript">        
        $('#tblShipment').DataTable({
            paginate:false,
            filter:true,
            aaSorting:[],
            dom: 'Bfrtip',
            buttons: [
            'excel'
            ]
        });
        $('#tblShipment')
		.removeClass( 'display' )
		.addClass('table table-striped table-bordered');
</script>