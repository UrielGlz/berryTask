<section class="content-header">

    <h1><i class='fa-fw fa fa-birthday-cake'></i> <?php echo $_translator->_getTranslation('Lista de pedidos especiales');?></small></h1>

    <ol class="breadcrumb">

        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>

        <li class="active"><?php echo $_translator->_getTranslation('Lista de pedidos especiales');?></li>

    </ol>

</section>

<section class="content">

<div class="box">

  <div class="box-header with-border">

    <h3 class="box-title"></h3>

    <div class="box-tools pull-right">   

        <a href="SpecialOrder.php" class="btn btn-default"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar pedido especial')?></a> 

        <span class="btn btn-default _searchSpecialOrder"><i class="fa fa-search-plus"></i> <?php echo $_translator->_getTranslation('Busqueda avanzada')?></span> 

    </div><!-- /.box-tools -->

  </div><!-- /.box-header -->

  <div class="box-body">

    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>

    <div class="clear"></div>

    <div class='table-responsive'>

        <table id="tblSpecialOrder" class="table table-bordered table-striped table-hover table-condensed datatable_whit_filter_column">

            <thead>

            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('No.');?></th>

            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>

            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>

            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Fecha entrega');?></th>                     

            <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Cliente');?></th>

            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Telefono');?></th>

            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Status');?></th>

            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Producido');?></th>

            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Entregado');?></th>

            <th class="col-lg-1 text-center">Accion</th>

            </thead>

            <tfoot>

            <th class="filter text-center"><?php echo $_translator->_getTranslation('No.');?></th>

            <th class="filter text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>

            <th class="filter text-center"><?php echo $_translator->_getTranslation('Fecha');?></th> 

            <th class="filter text-center"><?php echo $_translator->_getTranslation('Fecha entrega');?></th>                     

            <th class="filter text-center"><?php echo $_translator->_getTranslation('Cliente');?></th>

            <th class="filter text-center"><?php echo $_translator->_getTranslation('Telefono');?></th>

            <th class="filter text-center"><?php echo $_translator->_getTranslation('Status');?></th>

            <th class="filter text-center"><?php echo $_translator->_getTranslation('Producido');?></th>

            <th class="filter text-center"><?php echo $_translator->_getTranslation('Entregado');?></th>

            <th></th>

            </tfoot>

            <tbody>

            <?php 

                if($_listSpecialOrders){

                    foreach($_listSpecialOrders as $ordenEspecial){?>

                        <tr>

                        <td class="text-center"><?php echo $ordenEspecial['req_number']?></td> 

                        <td class="text-center"><?php echo $ordenEspecial['storeName']?></td> 

                        <td class="text-center"><?php echo $ordenEspecial['date']?></td>

                        <td class="text-center"><?php echo $ordenEspecial['delivery_date']?></td>  

                        <td class="text-center"><?php echo $ordenEspecial['customerName']?></td>

                        <td class="text-center"><?php echo $ordenEspecial['phone']?></td>

                        <td class="text-center"><?php echo $ordenEspecial['statusName']?></td>

                        <td class="text-center _statusProduction_<?php echo $ordenEspecial['id'] ?>"><?php if($ordenEspecial['status_production'] == '2'){?><i class="fa fa-check fa-2x text-olive"></i><?php }else{echo "";}?></td>

                        <td class="text-center _statusDelivery_<?php echo $ordenEspecial['id'] ?>"><?php if($ordenEspecial['status_delivery'] == '2'){?><i class="fa fa-check fa-2x text-olive"></i><?php }else{ echo ""; }?></td>

                        <td class="text-center" style='white-space:nowrap'>

                            <a class="btn btn-sm btn-default" href="SpecialOrder.php?action=edit&id=<?php echo $ordenEspecial['id']?>"><i class="fa fa-pencil fa-"></i></a>

                            <a class="btn btn-sm btn-default" href="#" onclick="javascript: void window.open('SpecialOrder.php?action=export&flag=pdf&id=<?php echo $ordenEspecial['id']?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-file-pdf-o fa-"></i></a>                                                        

                            <div class='dropup' style="display: inline">

                                <button class='btn btn-sm btn-default' data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-tasks"></i></button>

                                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="moreOptions">

                                    <?php if($ordenEspecial['status_delivery'] == '1'){$classEntregado = '';$classNOEntregado = 'hide';}?>

                                    <?php if($ordenEspecial['status_delivery'] == '2'){$classEntregado = 'hide';$classNOEntregado = '';}?>                                                  

                                    <li><span class="span_on_dropdown_menu <?php echo $classEntregado ?> _li_entregado_<?php echo $ordenEspecial['id']?>" onclick="changeStatusForSR(this)" data-id="<?php echo $ordenEspecial['id']; ?>" data-statusfield="status_delivery" data-status="2" data-statusname="Entregada"><i class="fa fa-check"></i> <?php echo $_translator->_getTranslation('Entregada') ?></span></li>

                                    <li><span class="span_on_dropdown_menu <?php echo $classNOEntregado ?> _li_entregado_<?php echo $ordenEspecial['id']?>" onclick="changeStatusForSR(this)" data-id="<?php echo $ordenEspecial['id']; ?>" data-statusfield="status_delivery" data-status="1" data-statusname="Pendiente"><i class="fa fa-close"></i> <?php echo $_translator->_getTranslation('NO entregada') ?></span></li>

                                    <li><span class="span_on_dropdown_menu _emailing" data-id="<?php echo $ordenEspecial['id']; ?>"><i class="fa fa-envelope"></i> <?php echo $_translator->_getTranslation('Enviar por correo') ?></span></li>

                                </ul>

                            </div>

                            <?php if(trim($ordenEspecial['feedback']) != '' && !is_null($ordenEspecial['feedback'])){?> <span class="btn btn-warning  _setModalFeedbackSpecialOrder" data-idspecialorderforfeedback="<?php echo $ordenEspecial['id'] ?>"><i class="fa fa-comments"></i></span><?php }else{ ?><span class="btn btn-default disabled"><i class="fa fa-comments"></i></span><?php }?>

                            <a class="btn btn-sm btn-danger" onclick="return confirmAction('delete_special_order',function(){deleteRegistry('<?php echo $_translator->_getTranslation('Desea continuar y eliminar este registro ?') ?>','SpecialOrder.php?action=delete&id=<?php echo $ordenEspecial['id']?>')})" href="#"><i class="fa fa-trash"></i></a>                                        

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

<style>

    .span_on_dropdown_menu{

        display:block;

        padding:3px 20px;

        font-weight: 400;

        line-height: 1.42857143;

        white-space: nowrap;

    }

    

    .span_on_dropdown_menu:hover{

        background-color: #e1e3e9;

        cursor: pointer;

    }

</style>

<?php  include ROOT."/View/Modal/BusquedaAvanzadaSR.php"; ?>

<?php include ROOT."/View/Modal/emailingSpecialRequisition.php"; ?>

<?php include ROOT."/View/Modal/addSpecialOrderFeedback.php";?>

<script type="text/javascript" language="javascript">

    $('._emailing').on('click',function(){

        prepareEmailingSpecialRequisition($(this).data('id'));

    });

    

     $('#send_emailing').on('click',function(){

        emailingSepecialRequisition();

    });    

    

    $('._setModalFeedbackSpecialOrder').on('click',function(){

        $('#id_special_order_for_feedback').val($(this).data('idspecialorderforfeedback'));

        $('._saveModalSpecialOrderFeedback').hide();

         $('#feedback').attr('readonly',true);

        getSpecialOrderFeedback();

    });    

    

    $('._closeModalSpecialOrderFeedback').on('click',function(){                

        $('#feedback').val('');

        $('#modalAddSpecialOrderFeedback').modal('hide');

    });    

    

    $('._searchSpecialOrder').on('click',function(){$('#modalBusquedaAvanzadaSR').modal('show');});    

    $('#tblSpecialOrder').DataTable({   

            paginate:false,

            filter:true,

            aaSorting:[],

            dom: 'Bfrtip',

            buttons: [

            'excel'

            ]

        });

</script>