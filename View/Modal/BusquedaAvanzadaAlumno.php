<!-- Modal -->
<div id="modalFiltroAlumno" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel"><i class="fa fa-search-plus" ></i> <?php echo $_translator->_getTranslation('Busqueda')?></h3>
        </div>
            <?php 
            $_formBuscar = new AlumnoBuscarForm();
            echo $_formBuscar->openForm();
            echo $_formBuscar->showActionController();?>
            <div class="modal-body">
                <div class="col-lg-10 col-lg-offset-1 col-md-10 col-lg-offset-1 col-xs-12">
                    <?php $_formBuscar->showElement('colegio_id');?>
                    <?php $_formBuscar->showElement('status');?>
                    <?php $_formBuscar->showElement('fechaInicio');?>
                    <?php $_formBuscar->showElement('fechaFin');?>
                </div>
            </div>
            <div class="clear"></div>
            <div class="modal-footer">
                <div class="col-lg-2 col-md-2 col-xs-6 pull-right  p-l-0 ">
                    <?php $element = $_formBuscar->getElement('cerrar');echo $_formBuscar->createElement($element);?>
                </div>
                <div class="col-lg-2 col-md-2 col-xs-6 pull-right p-r-0">                
                    <?php $element = $_formBuscar->getElement('search');echo $_formBuscar->createElement($element); ?>
                </div>
            </div>
        </div>
        <?php echo $_formBuscar->closeForm();?>
    </div>
</div>
<script>
    $('form[name=AlumnoBuscarForm] #status').select2();
   $('form[name=AlumnoBuscarForm] #colegio_id').select2();
    $('#fechaInicio,#fechaFin').datetimepicker({format: 'DD/MM/YYYY'});
</script>
