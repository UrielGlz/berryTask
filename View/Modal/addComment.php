<!-- Modal -->

<div id="modalAddComment" class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog"
    style=" overflow: hidden;background: transparent">

    <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content">

            <div class="modal-body">

                <div class="box box-primary">

                    <div class="box-header with-border">

                        <button type="button" class="close _closeModalComment"><i
                                class="fa fa-window-close"></i></button>

                        <h4><i class='fa-fw fa fa-tags'></i> <span id="title_modal_comment">
                                <?php echo $_translator->_getTranslation('Agregar Comentario'); ?>
                            </span></h4>

                    </div>

                    <?php
                    $form = new CommentForm();

                    echo $form->openForm();

                    echo $form->showActionController();

                    echo $form->showId(); ?>

                    <div class="box-body">

                        <div class='flashmessenger-add_task'>
                            <?php $flashmessenger->showMessage(true); ?>
                        </div>

                        <div class="col-md-12 col-xs-12">

                            <?php
                            $form->showElement('comment');

                            ?>


                        </div>



                    </div>

                    <div class="box-footer">

                        <div class="pull-right">

                            <input type="button" value="<?php echo $_translator->_getTranslation('Terminar') ?>"
                                class='btn btn-primary _saveComment' />

                        </div>

                        <div class="pull-left">

                            <input type="button" value="<?php echo $_translator->_getTranslation('Cancelar') ?>"
                                class="btn btn-default _closeModalComment" />

                        </div>

                    </div>

                    <?php echo $form->closeForm(); ?>

                </div>

            </div>

        </div>

    </div>

    <script>


        $('#modalAddComment').on('shown.bs.modal', function (e) {

            if ($('#action').val() === 'insert') { $('#comment').focus(); }



            var input = null;

            $("form[name=comment]").find('div').each(function () {

                if ($(this).hasClass('has-error') === true) {

                    input = $(this).data('errorfor');

                    return false;

                }

            });



            if (input !== null) { $('#' + input).focus(); }

        });

        $('._addComment').on('click', function () {

            clearForm('comment');

            $('form[name=comment] #action').val('insert');

            $('form[name=comment] #id').val('');

            $('.flashmessenger').html('');

            _getTranslation('Agregar Comentario', function (msj) { $('#title_modal_comment').html(msj); });

            $('#modalAddComment').modal('show');

            $('#comment').focus();

        });

        $('._closeModalComment').on('click', function () {

            clearForm('comment');

            $('.flashmessenger').html('');

            $('#modalAddComment').modal('hide');

        });

    </script>

</div>