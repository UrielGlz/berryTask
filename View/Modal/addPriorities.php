<!-- Modal -->

<div id="modalAddPriorities"  class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">

    <div class="modal-dialog" role="document">

    <div class="modal-content">

        <div class="modal-body"> 

            <div class="box box-primary">

            <div class="box-header with-border">

                <button type="button" class="close _closeModalProrities"><i class="fa fa-window-close"></i></button>

                <h4><i class='fa-fw fa fa-tags'></i> <span id="title_modal_priorities"><?php echo $_translator->_getTranslation('Agregar prioridad');?></span></h4>

            </div>

                <?php 

                if(!isset($_paymentForm)){$_paymentForm = new PrioritiesForm();}        

                echo $_paymentForm->openForm();

                echo $_paymentForm->showActionController();

                echo $_paymentForm->showId();?>

                <div class="box-body">

                    <div class='flashmessenger'><?php $flashmessenger->showMessage(true);?></div>

                    <div class="col-md-12 col-xs-12">

                        <?php

                        $_paymentForm->showElement('name');

                        $_paymentForm->showElement('color');

                        ?>

                    </div>   

                </div>

                <div class="box-footer">

                    <div class="pull-right">

                        <input type="button" value="<?php echo $_translator->_getTranslation('Terminar')?>" class='btn btn-primary _savePriorities'/>

                    </div>

                    <div class="pull-left">

                        <input type="button" value="<?php echo $_translator->_getTranslation('Cancelar')?>" class="btn btn-default _closeModalProrities" />

                    </div>

                </div>

                <?php echo $_paymentForm->closeForm();?>

            </div>

        </div>

    </div>           

    </div>

    <script>

        $('#modalAddPriorities').on('shown.bs.modal', function (e) {

            if($('form[name=priorities] #action').val() === 'insert'){$('form[name=priorities] #name').focus();}

            

            var input = null;

            $("form[name=priorities]").find('div').each(function() {

                if($(this).hasClass('has-error') === true){

                    input = $(this).data('errorfor');

                    return false;

                }

            });    

            

            if(input !== null){$('form[name=priorities] #'+input).focus();}

        });

        

        $('._addPriorities').on('click',function(){

            clearForm('priorities');                

            $('form[name=priorities] #action').val('insert');

            $('form[name=priorities] #id').val('');

            $('.flashmessenger').html('');

             _getTranslation('Agregar Prioridad',function(msj){ $('#title_modal_priorities').html(msj);});

            $('#modalAddPriorities').modal('show');

        });

    

        $('._closeModalProrities').on('click',function(){

            clearForm('paymentterms');

            $('.flashmessenger').html('');

            $('#modalAddPriorities').modal('hide');

        });

    </script>

</div>

