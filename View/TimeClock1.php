<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <?php      
            $currentAction = '';
            $agregarNuevo = '';
            if($action =='edit'){
                $timeClockAction = 'Editar Checada ';
                $currentAction = "<li class='active'><i class='fa fa-pencil'></i> ".$_translator->_getTranslation('Editar')."</li>";
                $agregarNuevo = "<a href='TimeClock.php' class='btn btn-sm btn-default'><i class='fa fa-plus'></i> ".$_translator->_getTranslation('Agregar checada')."</a>";
                $agregarNuevo .= '<hr>';                
            }elseif($action =='' || $action =='insert'){
                $timeClockAction = 'Agregar Checada ';
                $currentAction = "<li class='active'><i class='fa fa-plus'></i> ".$_translator->_getTranslation('Agregar')."</li>";
            } ?>
            <li><i class="fa fa-dashboard"></i>  <a href="<?php echo ROOT_HOST?>/Controller/Home.php"><?php echo $_translator->_getTranslation('Home')?></a></li>
            <li class="active"><i class="fa fa-clock-o"></i> <?php echo $_translator->_getTranslation('Reloj chechador')?></li>
            <?php echo $currentAction?>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div id="flashmessenger"><?php $flashmessenger->showMessage();?></div>
        <div class="box">
            <div class="box-header"> 
                <h3><i class='fa-fw fa fa-clock-o'></i><?php echo $_translator->_getTranslation($timeClockAction);?></h3>
                <div class='box-icon'><i class='icon fa fa-tasks'></i></div>
            </div>
            <div class='box-content'>
                <div class="row">
                    <div class="col-md-12 col-xs-12 text-right">
                        <?php echo $agregarNuevo;?>                        
                        <div class="clear"></div>
                    </div>  
                        <?php 
                            echo $_form->openForm();
                            echo $_form->showActionController();
                            echo $_form->showId();
                            ?>
                       <div class="col-lg-6">
                            <div><?php echo $_form->showElement('date');?></div>
                            <div><?php echo $_form->showElement('id_user');?></div>
                            <div><?php echo $_form->showElement('check_in');?></div>
                            <div><?php echo $_form->showElement('check_out');?></div>
                            <div class="text-right">
                                <?php $element = $_form->getElement('send'); echo $_form->createOnlyElement($element);?>
                                <?php $element = $_form->getElement('cancelar'); echo $_form->createOnlyElement($element);?>                               
                            </div>
                        </div>       
                        <?php echo $_form->closeForm();?>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <hr/>
                        <a class="btn btn-default pull-right" data-toggle='modal' data-target='#modalBusquedaAvanzadaTimeClock'><i class="fa fa-search-plus" ></i> <?php echo $_translator->_getTranslation('Busqueda');?></a>                
                        <div class="clear"></div>
                        <hr class="m-t-1"/>
                        <div class='table-responsive'>
                        <table id="tblTimeClock" class="table table-striped table-hover table-condensed font-size-10">
                            <thead>
                            <th class="text-center">Accion</th>
                            <th class="text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>
                            <th class="text-center"><?php echo $_translator->_getTranslation('Usuario');?></th>
                            <th class="text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
                            <th class="text-center"><?php echo $_translator->_getTranslation('Hora inicio');?></th>
                            <th class="text-center"><?php echo $_translator->_getTranslation('Hora final');?></th>
                            <th class="text-center"><?php echo $_translator->_getTranslation('Total horas');?></th>
                            </thead>
                            <tfoot>     
                            <th></th>
                            <th class="filter text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>
                            <th class="filter text-center"><?php echo $_translator->_getTranslation('Usuario');?></th>
                            <th class="filter text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
                            <th class="filter text-center"><?php echo $_translator->_getTranslation('Hora inicio');?></th>
                            <th class="filter text-center"><?php echo $_translator->_getTranslation('Hora fin');?></th>
                            <th class="filter text-center"><?php echo $_translator->_getTranslation('Total horas');?></th>                            
                            </tfoot>
                            <tbody>
                            <?php 
                                if($_listaTimeClocks){
                                    foreach($_listaTimeClocks as $timeClock){?>
                                        <tr>
                                            <td>
                                                <a href="TimeClock.php?action=edit&id=<?php echo $timeClock['id']?>"><i class="fa fa-pencil btn btn-sm btn-default"></i></a>
                                                <a onclick="return deleteRegistry('TimeClock.php?action=delete&id=<?php echo $timeClock['id']?>')" 
                                                   href="#"><i class="fa fa-trash btn btn-sm btn-danger"></i></a>
                                            </td>
                                            <td class="text-center"><?php echo $timeClock['formated_date']?></td>
                                            <td class="text-center"><?php echo $timeClock['userName']?></td>
                                            <td class="text-center"><?php echo $timeClock['sucursalName']?></td>
                                            <td class="text-center"><?php echo $timeClock['check_in']?></td>
                                            <td class="text-center"><?php echo $timeClock['check_out']?></td>
                                            <td class="text-center"><?php echo number_format($timeClock['total'],2)?></td>
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
    </div>
    <?php include ROOT."/View/Modal/BusquedaAvanzadaTimeClock.php";?>
</div>
<style>
    tfoot {
    display: table-header-group;
}
</style>
<script type="text/javascript" language="javascript">
    $('#date').datetimepicker({format: "MM/DD/YYYY"});
    $('#check_in,#check_out').datetimepicker({format: "MM/DD/YYYY hh:mm A "});
    $('#id_user').select2();
    
    $('#tblTimeClock').DataTable({
        paginate:false,
        filter:true,
        bFilter:true,
        aaSorting:[],
        dom: 'Bfrtip',
        buttons: [
            'copy', 'excel', 'pdf', 'print'
        ]
    });
    $('#tblTimeClock')
            .removeClass( 'display' )
            .addClass('table table-striped table-bordered');

    $('#tblTimeClock tfoot th.filter').each( function () {
        //var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Buscar" style="width:100%" />' );
    } );  

    var table = $('#tblTimeClock').DataTable();
    // Apply the search
    table.columns().every( function () {
        var that = this; 
        $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search (this.value.replace("/;/g", "|"), true, false)
                    .draw();
            }
        } );
    } );
</script>