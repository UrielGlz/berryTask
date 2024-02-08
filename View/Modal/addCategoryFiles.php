<!-- Modal -->

<div id="modalAddCategoryFiles"  class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">

    <div class="modal-dialog" role="document">

    <div class="modal-content">

        <div class="modal-body"> 

            <div class="box box-primary">

            <div class="box-header with-border">

                <button type="button" class="close _closeModalCategoryFiles"><i class="fa fa-window-close"></i></button>

                <h4><i class='fa-fw fa fa-tags'></i> <span id="title_modal_CategoryFiles"><?php echo $_translator->_getTranslation('Agregar Categoria de tarea');?></span></h4>

            </div>

                <?php 

                if(!isset($_CategoryFilesForm)){$_CategoryFilesForm = new CategoryFilesForm();}        

                echo $_CategoryFilesForm->openForm();

                echo $_CategoryFilesForm->showActionController();

                echo $_CategoryFilesForm->showId();?>

                <div class="box-body">

                    <div class='flashmessenger'><?php $flashmessenger->showMessage(true);?></div>

                    <div class="col-md-12 col-xs-12">

                        <?php

                        $_CategoryFilesForm->showElement('name');

                        $_CategoryFilesForm->showElement('color');

                        ?>

                    </div>   

                </div>

                <div class="box-footer">

                    <div class="pull-right">

                        <input type="button" value="<?php echo $_translator->_getTranslation('Terminar')?>" class='btn btn-primary _saveCategoryFiles'/>

                    </div>

                    <div class="pull-left">

                        <input type="button" value="<?php echo $_translator->_getTranslation('Cancelar')?>" class="btn btn-default _closeModalCategoryFiles" />

                    </div>

                </div>

                <?php echo $_CategoryFilesForm->closeForm();?>

            </div>

        </div>

    </div>           

    </div>

    <script>

        $('#modalAddCategoryFiles').on('shown.bs.modal', function (e) {

            if($('form[name=CategoryFiles] #action').val() === 'insert'){$('form[name=CategoryFiles] #name').focus();}

            

            var input = null;

            $("form[name=CategoryFiles]").find('div').each(function() {

                if($(this).hasClass('has-error') === true){

                    input = $(this).data('errorfor');

                    return false;

                }

            });    

            

            if(input !== null){$('form[name=CategoryFiles] #'+input).focus();}

        });

        

        $('._addCategoryFiles').on('click',function(){

            clearForm('CategoryFiles');                

            $('form[name=CategoryFiles] #action').val('insert');

            $('form[name=CategoryFiles] #id').val('');

            $('.flashmessenger').html('');

             _getTranslation('Agregar Categoria de Tarea',function(msj){ $('#title_modal_CategoryFiles').html(msj);});

            $('#modalAddCategoryFiles').modal('show');

        });

    

        $('._closeModalCategoryFiles').on('click',function(){

            clearForm('CategoryFiles');

            $('.flashmessenger').html('');

            $('#modalAddCategoryFiles').modal('hide');

        });

    </script>

</div>

