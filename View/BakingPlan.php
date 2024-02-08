<section class="content-header">
    <h1><i class='fa-fw fa fa-cube'></i> <?php echo $_translator->_getTranslation('Plan de horneado (Lista ordenes especiales)');?></small></h1>
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
        <i class="btn btn-default _generatePlanBaked"> <i class="fa fa-file-excel-o"></i> <?php echo $_translator->_getTranslation('Generar plan de horneado')?></i>
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="clear"></div>
     <?php
        $form = new Form();
        $form->setName('baked');
        $form->setMethod('POST');
        $form->setActionForm('Baked.php');
        $form->setActionController('batch');
        $form->addElement(array('type'=>'hidden','name'=>'action-batch'));
       $form->addElement(array(
            'type' => 'text',
            'name' => 'fechaInicio',
            'optionals'=>array('placeholder'=>'Fecha inicio'),
            'required'=>false,            
            'col-size-element'=>'12',
        ));
        $form->addElement(array(
            'type' => 'text',
            'name' => 'fechaFin',
            'optionals'=>array('placeholder'=>'Fecha fin'),
            'required'=>false,            
            'col-size-element'=>'12',
        ));
        echo $form->openForm();
        echo $form->showActionController();
        echo $form->showElement('action-batch');
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
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Fecha American Format');?></th>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('No.');?></th>                    
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
            <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Cliente');?></th>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Status');?></th>
            <th class="col-lg-3 text-center"><?php echo $_translator->_getTranslation('Status Horneado');?></th>
            <th class="col-lg-1 text-center">Accion</th>
            </thead>
            <tbody>
            <?php 
            if($_specialRequisitionsList){
                foreach($_specialRequisitionsList as $specialRequisicion){?>
                    <tr>
                        <td class="text-center">
                            <?php $disabled = ""; if($specialRequisicion['status_baked']!=='2' OR $specialRequisicion['status']=='2'){ $disabled = 'disabled';}?>      
                            <input type="checkbox" <?php echo $disabled; ?> id="req_<?php echo $specialRequisicion['req_number']?>" name="special_orders[<?php echo $specialRequisicion['id'] ?>]" value="<?php echo $specialRequisicion['id']?>" />
                        </td>                                    
                        <td class="text-center"><?php echo $specialRequisicion['delivery_date']?></td>
                        <td class="text-center"><?php echo $specialRequisicion['delivery_date_american_format']?></td>
                        <td class="text-center"><?php echo $specialRequisicion['req_number']?></td> 
                        <td class="text-center"><?php echo $specialRequisicion['storeName']?></td>
                        <td class="text-center"><?php echo $specialRequisicion['customerName']?></td>
                        <td class="text-center"><?php echo $specialRequisicion['statusName']?></td>
                        <td class="text-center">
                            <?php 
                            if($login->getRole()==='4'){
                                echo $specialRequisicion['statusBakedName'];
                            }else{ 
                                $pendienteClassBtn = 'btn-default';
                                $enProcesoClassBtn = 'btn-default';
                                $terminadoClassBtn = 'btn-default';

                                switch($specialRequisicion['status_baked']){
                                    case '1': $pendienteClassBtn = 'btn-primary'; break;
                                    case '2': $enProcesoClassBtn = 'btn-primary'; break;
                                    case '3': $terminadoClassBtn = 'btn-primary'; break;
                                }

                                if($specialRequisicion['status']=='2'){
                                    $pendienteClassBtn .= ' disabled';
                                    $enProcesoClassBtn .= ' disabled';
                                    $terminadoClassBtn .= ' disabled';
                                }

                                ?>                                                
                                <a href="#" class="btn btn-sm <?php echo $specialRequisicion['req_number']; ?> <?php echo $pendienteClassBtn; ?>" onclick="changeStatusBaked(this)" data-checkbox='special_orders[<?php echo $specialRequisicion['id'];?>]' data-controller='SpecialOrder' data-id="<?php echo $specialRequisicion['id']; ?>" data-reqnumber="<?php echo $specialRequisicion['req_number']; ?>" data-statusfield='status_baked' data-status="1" data-statusname="Pendiente"><i class="fa fa-star-o"></i> Pendiente</a>
                                <a href="#" class="btn btn-sm <?php echo $specialRequisicion['req_number']; ?> <?php echo $enProcesoClassBtn; ?>" onclick="changeStatusBaked(this)" data-checkbox='special_orders[<?php echo $specialRequisicion['id'];?>]' data-controller='SpecialOrder' data-id="<?php echo $specialRequisicion['id']; ?>" data-reqnumber="<?php echo $specialRequisicion['req_number']; ?>" data-statusfield='status_baked' data-status="2" data-statusname="En proceso"><i class="fa fa-star-half-full"></i> En proceso</a>
                                <a href="#" class="btn btn-sm <?php echo $specialRequisicion['req_number']; ?> <?php echo $terminadoClassBtn; ?>" onclick="changeStatusBaked(this)" data-checkbox='special_orders[<?php echo $specialRequisicion['id'];?>]' data-controller='SpecialOrder' data-id="<?php echo $specialRequisicion['id']; ?>" data-reqnumber="<?php echo $specialRequisicion['req_number']; ?>" data-statusfield='status_baked' data-status="3" data-statusname="Terminado"><i class="fa fa-star"></i> Terminado</a>
                            <?php
                            }?>
                        </td>
                        <td class="text-right">
                            <a href="#" class="btn btn-sm btn-primary" onclick="javascript: void window.open('SpecialOrder.php?action=export&flag=pdf&id=<?php echo $specialRequisicion['id']?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-list"></i></a>
                            <a href="#" class="btn btn-sm btn-info" data-toggle='modal' data-target='#modalImagesSR' onclick="getImagesForSR(<?php echo $specialRequisicion['id'] ?>)"><i class="fa fa-camera"></i></a>                                           
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
</section>
<script src="<?php echo ROOT_HOST?>/public/datatables.net/plug-ins/sorting/datetime-moment.js"></script>
<script type="text/javascript" language="javascript">   
    
    $('._searchSpecialOrder').on('click',function(){
        $('form[name=search_special_order] #action').val('list');    
        $('#modalBusquedaAvanzadaSR').modal('show');
    });
    
    $('._generatePlanBaked').on('click',function(){
        var checked = 0;
        $("input:checkbox:checked").each(function(){
            checked++;
        });
        
        if(checked > 0){            
            $('#action-batch').val('baked_plan');
            submit('baked');
        }else{
            alert('Debes selccionar almenos una orden para generar plan de horneado.');
        }      
    });    
    
    $ = jQuery.noConflict();
    $.fn.dataTable.moment( 'MM/DD/YYYY hh:mm a' );
    $(function() {
        var oTable = $('#tblSpecialOrder').DataTable({
          oLanguage: {
            sSearch: "Buscar"
          },
          columnDefs: [{
                targets: [ 2 ],
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
          aData._date = new Date(aData[2]).getTime();
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