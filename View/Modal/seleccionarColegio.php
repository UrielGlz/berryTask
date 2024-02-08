<!-- Modal -->
<div id="modalSeleccionarColegio" class='modal fade' tabindex="-1" data-backdrop='false' data-keyboard="false" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-body">    
            <div class='flashmessenger'><?php $flashmessenger->showMessage(true);?></div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4><i class='fa-fw fa fa-university'></i> <span id="titulo_modal_alumno"><?php echo $_translator->_getTranslation('Seleccionar colegio');?></span></h4>
                </div>
                <div class="panel-body">
                    <?php
                    $login = new Login();
                    $colegioRepo = new ColegioRepository();
                    $colegios = $colegioRepo->getListColegios($login->getColegioId());
                    
                    if($colegios){?>
                        <ul class='colegios'><?php
                        foreach($colegios as $colegio){?>
                            <li data-id='<?php echo $colegio['id'] ?>'><?php echo $colegio['nombre'] ?></li><?php
                        }?>
                        </ul><?php    
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>           
    </div>
    <script>
        $('ul.colegios li').on('click',function(){
            var colegio_id = $(this).data('id');
             $.post('/Controller/Ajax.php', {
                action: 'ajax',
                request: 'setColegioActualNombre',
                colegio_id: colegio_id
            }, function(data) {
                if (data.response){
                    document.location.reload();
                }
            }, 'json');
        });
    </script>
</div>