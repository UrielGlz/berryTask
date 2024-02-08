<section class="content-header">
    <h1>
        <i class='fa-fw fa fa-cube'></i> 
        <?php 
        if($action == 'list-production-roscas'){
            echo $_translator->_getTranslation('Plan de Roscas');
        }else{
            echo $_translator->_getTranslation('Plan de decorado (Lista ordenes especiales)');
        }        
        ?>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
        <li class="active"><?php echo $_translator->_getTranslation('Lista de ordenes especiales');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
        <i class="btn btn-default _searchSpecialOrder"><i class="fa fa-search-plus"></i> <?php echo $_translator->_getTranslation('Busqueda avanzada')?></i>                   
        <i class="btn btn-default _generatePlanProduction"> <i class="fa fa-file-excel-o"></i> <?php echo $_translator->_getTranslation('Generar plan de produccion')?></i>
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="clear"></div>
     <?php
        $form = new Form();
        $form->setName('special_production');
        $form->setMethod('POST');
        $form->setActionForm('SpecialOrder.php');
        $form->setActionController('batch');
        $form->addElement(array('type'=>'hidden','name'=>'action-batch'));
        $form->addElement(array('type'=>'hidden','name'=>'report_name','value'=>$action)); /*Depende de action, puede ser list-production o list-production-roscas*/
        $form->addElement(array(
            'type' => 'text',
            'name' => 'fechaInicio',
            'optionals'=>array('placeholder'=>'Fecha Inicio'),
            'required'=>false,            
            'col-size-element'=>'12',
        ));
        $form->addElement(array(
            'type' => 'text',
            'name' => 'fechaFin',
            'optionals'=>array('placeholder'=>'Fecha Inicio'),
            'required'=>false,            
            'col-size-element'=>'12',
        ));
        echo $form->openForm();
        echo $form->showActionController();
        echo $form->showElement('action-batch');
        echo $form->showElement('report_name');
        ?>
    <div class='pull-right'>
        <div class='col-md-6'><?php $form->showElement('fechaInicio');?></div>
        <div class='col-md-6' style='padding-right: 0px'><?php $form->showElement('fechaFin');?></div>                        
    </div>    
    <div class="clear"></div>
    <div class='table-responsive'>
        <table id="tblSpecialOrder" class="table table-bordered table-striped table-hover table-condensed font-size-12">
            <thead>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Batch');?></th>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('No.');?></th>                    
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Fecha entrega');?></th>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Fecha entrega');?></th>
            <th class="col-lg-3 text-center"><?php echo $_translator->_getTranslation('Cliente');?></th>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Servicio a domicilio');?></th>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Status');?></th>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Status Produccion');?></th>
            <th class="col-lg-1 text-center">Accion</th>
            </thead>
            <tbody>
            <?php 
                if($_listSpecialOrders){
                    foreach($_listSpecialOrders as $order){?>
                        <tr>
                            <td class="text-center">
                                <?php $disabled = ""; if($order['status_production']=='2' OR $order['status']=='2'){ $disabled = 'disabled';}?>      
                                <input type="checkbox" <?php echo $disabled; ?> id="req_<?php echo $order['req_number']?>" name="special_orders[<?php echo $order['id'] ?>]" value="<?php echo $order['id']?>" />
                            </td>
                            <td class="text-center"><?php echo $order['req_number']?></td> 
                            <td class="text-center"><?php echo $order['storeName']?></td>
                            <td class="text-center"><?php echo $order['delivery_date_american_format']?></td>
                            <td class="text-center"><?php echo $order['delivery_date']?></td>
                            <td class="text-center"><?php echo $order['customerName']?></td>
                            <td class="text-center"><?php echo $order['home_service']?></td>
                            <td class="text-center"><?php echo $order['statusName']?></td>
                            <td class="text-center _<?php echo $order['id'] ?>">
                                <?php
                                if($order['status_production']=='1'){?>
                                    <span class="btn btn-sm btn-default" onclick="changeStatusForSR(this)" data-id="<?php echo $order['id']; ?>" data-reqnumber="<?php echo $order['req_number'] ?>" data-statusfield='status_production' data-status="2" data-statusname="Terminada"><i class="fa fa-star-half-full"></i>  <?php echo $order['statusProductionName']?></span>
                                <?php
                                }?>
                                <?php
                                if($order['status_production']=='2'){?>
                                    <span class="btn btn-sm btn-primary" onclick="changeStatusForSR(this)" data-id="<?php echo $order['id']; ?>" data-reqnumber="<?php echo $order['req_number'] ?>" data-statusfield='status_production' data-status="1" data-statusname="Pendiente"><i class="fa fa-star"></i>  <?php echo $order['statusProductionName']?></span>
                                <?php
                                }?>      
                            </td>
                            <td class="text-center" style='white-space:nowrap'>
                                <a class="btn btn-sm btn-default" href="#" onclick="javascript: void window.open('SpecialOrder.php?action=export&flag=pdf&id=<?php echo $order['id']?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-file-pdf-o"></i></a>                            
                                <a href="#" class="btn btn-sm btn-default" data-toggle='modal' data-target='#modalImagesSR' onclick="getImagesForSR(<?php echo $order['id'] ?>)"><i class="fa fa-camera"></i></a>                                                                           
                                <span class="btn btn-sm btn-default _setModalFeedbackSpecialOrder" data-idspecialorderforfeedback="<?php echo $order['id'] ?>"><i class="fa fa-comments"></i></span>                            
                            </td>
                            
                        </tr><?php
                    }                   
                }?>
            </tbody>
        </table>
    </div>
    <?php echo $form->closeForm();?>
  </div><!-- /.box-body -->
</div><!-- /.box -->
<?php  include ROOT."/View/Modal/BusquedaAvanzadaSR.php"; ?>
<?php include ROOT."/View/Modal/imagesSR.php";?>
<?php include ROOT."/View/Modal/addSpecialOrderFeedback.php";?>
</section>
<script src="<?php echo ROOT_HOST?>/public/datatables.net/plug-ins/sorting/datetime-moment.js"></script>
<script type="text/javascript" language="javascript">   
    
    $('._searchSpecialOrder').on('click',function(){
        $('form[name=search_special_order] #action').val('list-production');    
        $('#modalBusquedaAvanzadaSR').modal('show');
    });
    
    $('._generatePlanProduction').on('click',function(){
        $('#action-batch').val('special_production_plan');
        submit('special_production');
    });
    
    $('._setModalFeedbackSpecialOrder').on('click',function(){
        $('#id_special_order_for_feedback').val($(this).data('idspecialorderforfeedback'));
        getSpecialOrderFeedback();
    });    
    
    $('._closeModalSpecialOrderFeedback').on('click',function(){                
        $('#feedback').val('');
        $('#modalAddSpecialOrderFeedback').modal('hide');
    });    
    $('._saveModalSpecialOrderFeedback').on('click',function(){saveSpecialOrderFeedback();});
    
    
    $ = jQuery.noConflict();
    $.fn.dataTable.moment( 'DD/M/YYYY hh:mm a' );
    $(function() {
        var oTable = $('#tblSpecialOrder').DataTable({
          oLanguage: {
            sSearch: "Buscar"
          },
          columnDefs: [{
                targets: [ 3 ],
                visible: false
            }],
          iDisplayLength: -1,
          sPaginationType: "full_numbers",
          paginate: false,
          aaSorting:[]

        });
        
        $("#fechaInicio").datepicker({
          "onSelect": function(date) {
            minDateFilter = new Date(date).getTime();
            oTable.draw();
          }
        }).keyup(function() {
          minDateFilter = new Date(this.value).getTime();
          oTable.draw();
        });

        $("#fechaFin").datepicker({
          "onSelect": function(date) {
            maxDateFilter = new Date(date).getTime();
            oTable.draw();
          }
        }).keyup(function() {
          maxDateFilter = new Date(this.value).getTime();
          oTable.draw();
        });

    });

    // Date range filter
    minDateFilter = "";
    maxDateFilter = "";

    $.fn.dataTableExt.afnFiltering.push(
      function(oSettings, aData, iDataIndex) {
        if (typeof aData._date == 'undefined') {
          aData._date = new Date(aData[3]).getTime();
        }

        if (minDateFilter && !isNaN(minDateFilter)) {
          if (aData._date < minDateFilter) {
            return false;
          }
        }

        if (maxDateFilter && !isNaN(maxDateFilter)) {
          if (aData._date > maxDateFilter) {
            return false;
          }
        }

        return true;
      }
    );
</script>