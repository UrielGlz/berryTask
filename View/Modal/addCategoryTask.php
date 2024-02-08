<!-- Modal -->

<div id="modalAddCategoryTask"  class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">

    <div class="modal-dialog" role="document">

    <div class="modal-content">

        <div class="modal-body"> 

            <div class="box box-primary">

            <div class="box-header with-border">

                <button type="button" class="close _closeModalCategoryTask"><i class="fa fa-window-close"></i></button>

                <h4><i class='fa-fw fa fa-tags'></i> <span id="title_modal_CategoryTask"><?php echo $_translator->_getTranslation('Agregar Categoria de tarea');?></span></h4>

            </div>

                <?php 

                if(!isset($_CategoryTaskForm)){$_CategoryTaskForm = new CategoryTaskForm();}        

                echo $_CategoryTaskForm->openForm();

                echo $_CategoryTaskForm->showActionController();

                echo $_CategoryTaskForm->showId();?>

                <div class="box-body">

                    <div class='flashmessenger'><?php $flashmessenger->showMessage(true);?></div>

                    <div class="col-md-12 col-xs-12">

                        <?php

                        $_CategoryTaskForm->showElement('name');

                        $_CategoryTaskForm->showElement('color');

                        ?>

                    </div>   

                </div>

                <div class="box-footer">

                    <div class="pull-right">

                        <input type="button" value="<?php echo $_translator->_getTranslation('Terminar')?>" class='btn btn-primary _saveCategoryTask'/>

                    </div>

                    <div class="pull-left">

                        <input type="button" value="<?php echo $_translator->_getTranslation('Cancelar')?>" class="btn btn-default _closeModalCategoryTask" />

                    </div>

                </div>

                <?php echo $_CategoryTaskForm->closeForm();?>

            </div>

        </div>

    </div>           

    </div>

    <script>

        $('#modalAddCategoryTask').on('shown.bs.modal', function (e) {

            if($('form[name=CategoryTask] #action').val() === 'insert'){$('form[name=CategoryTask] #name').focus();}

            

            var input = null;

            $("form[name=CategoryTask]").find('div').each(function() {

                if($(this).hasClass('has-error') === true){

                    input = $(this).data('errorfor');

                    return false;

                }

            });    

            

            if(input !== null){$('form[name=CategoryTask] #'+input).focus();}

        });

        

        $('._addCategoryTask').on('click',function(){

            clearForm('CategoryTask');                

            $('form[name=CategoryTask] #action').val('insert');

            $('form[name=CategoryTask] #id').val('');

            $('.flashmessenger').html('');

             _getTranslation('Agregar Categoria de Tarea',function(msj){ $('#title_modal_CategoryTask').html(msj);});

            $('#modalAddCategoryTask').modal('show');

        });

    

        $('._closeModalCategoryTask').on('click',function(){

            clearForm('CategoryTask');

            $('.flashmessenger').html('');

            $('#modalAddCategoryTask').modal('hide');

        });

    </script>

</div>

