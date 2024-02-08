<!-- Modal -->
<div id="modalSeleccionarIdioma" class='modal fade' tabindex="-1" data-backdrop='false' data-keyboard="false" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-body">    
            <div class='flashmessenger'><?php $flashmessenger->showMessage(true);?></div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4><i class='fa-fw fa fa-flag'></i> <span><?php echo $_translator->_getTranslation('Seleccionar idioma');?></span></h4>
                </div>
                <div class="panel-body">
                    <div class="col-lg-6 col-md-6 col-xs-6">
                        <div class="thumbnail text-center _idioma" data-idioma="_es">
                            <i class="fa fa-flag fa-4x"></i>
                            <div class="caption">
                              <h5><?php echo $_translator->_getTranslation('Español')?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-xs-6">
                        <div class="thumbnail text-center _idioma" data-idioma="_en">
                            <i class="fa fa-flag fa-4x"></i>
                            <div class="caption">
                              <h5><?php echo $_translator->_getTranslation('Ingles')?></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>           
    </div>
    <script>
        $('._idioma').on('click',function(){
            var idioma = $(this).data('idioma');
             $.post('/Controller/Ajax.php', {
                action: 'ajax',
                request: 'setIdioma',
                idioma: idioma
            }, function(data) {
                if (data.response){
                    document.location.reload();
                }
            }, 'json');
        });
    </script>
</div>