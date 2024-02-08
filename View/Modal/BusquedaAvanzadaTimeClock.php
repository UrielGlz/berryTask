<!-- Modal -->
<div id="modalBusquedaAvanzadaTimeClock" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
    <div class="modal-body"> 
    <div class="box box-primary">
        <div class="box-header  with-border">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel"><i class="fa fa-clock-o"></i> <?php echo $_translator->_getTranslation('Busqueda avanzada');?></h3>            
        </div> 
        <div class="box-body">              
            <div id="flashmessenger-timeclock"></div>     
            <?php $_formBuscar = new TimeClockBuscarForm();
            echo $_formBuscar->openForm();?>
            <div class="box-body">
                <div class="col-md-12 col-xs-12">
                    <div class="form-group"><?php $_formBuscar->showElement('user');?></div>
                    <div class="form-group"><?php $_formBuscar->showElement('start_date');?></div>
                    <div class="form-group"><?php $_formBuscar->showElement('end_date');?></div>
                </div>
            </div>
            <div class="clear"></div>    
        </div>
        <div class="box-footer">
            <div class="col-lg-2 col-md-2 col-xs-6 pull-right  p-l-0 ">
                <?php $element = $_formBuscar->getElement('cerrar');echo $_formBuscar->createElement($element);?> 
            </div> 
            <div class="col-lg-2 col-md-2 col-xs-6 pull-right p-r-0">                
                <?php $element = $_formBuscar->getElement('search');echo $_formBuscar->createElement($element); ?>
            </div>
        </div>
         <?php echo $_formBuscar->closeForm();?>
    </div>
    </div>    
    </div>
    </div>
<script>
    $("#start_date,#end_date").datetimepicker({format: 'MM/DD/YYYY'});
    $('#user').select2();
</script>
</div>