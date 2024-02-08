<!-- Modal -->
<div id="modalBusquedaAvanzadaPago" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel"><i class="fa fa-search-plus" ></i> <?php echo $_translator->_getTranslation('Busqueda')?></h3>
        </div>
            <?php 
            $_formBuscar = new PagoBuscarForm();
            echo $_formBuscar->openForm();
            echo $_formBuscar->showActionController();?>
            <div class="modal-body">
                <div class="col-lg-10 col-lg-offset-1 col-md-10 col-lg-offset-1 col-xs-12">
                    <?php $_formBuscar->showElement('proveedor');?>
                    <?php $_formBuscar->showElement('num_factura');?>
                    <?php $_formBuscar->showElement('monto');?>
                    <?php $_formBuscar->showElement('forma_de_pago');?>
                    <?php $_formBuscar->showElement('num_operacion');?>
                    <?php $_formBuscar->showElement('status');?>
                    <?php $_formBuscar->showElement('startDate');?>
                    <?php $_formBuscar->showElement('endDate');?>
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
    $('#proveedor,#forma_de_pago,#status').select2();
    $('#startDate,#endDate').datetimepicker({format: 'DD/MM/YYYY'});
</script>
