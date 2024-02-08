<!-- Modal -->

<div id="modalAddFile" class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog"
    style=" overflow: hidden;background: transparent">

    <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content">

            <div class="modal-body">

                <div class="box box-primary">

                    <div class="box-header with-border">

                        <button type="button" class="close _closeModalFile"><i class="fa fa-window-close"></i></button>

                        <h4><i class='fa-fw fa fa-tags'></i> <span id="title_modal_file">
                                <?php echo $_translator->_getTranslation('Agregar Archivos'); ?>
                            </span></h4>

                    </div>

                    <?php
                    $form = new FileForm();

                    echo $form->openForm();

                    echo $form->showActionController();

                    echo $form->showId(); ?>

                    <div class="box-body">

                        <div class='flashmessenger-add_file'>
                            <?php $flashmessenger->showMessage(true); ?>
                        </div>

                        <div class="col-md-6 col-xs-6">

                            <?php
                            $form->showElement('task_id');
                            $form->showElement('project_id');

                            $form->showElement('name');

                            $form->showElement('id_category_file');

                            ?>
                        </div>

                        <div class="col-md-6 col-xs-6">

                            <?php

                            $form->showElement('attachement_file');

                            $form->showElement('expiration_date');
                            ?>
                        </div>

                    </div>

                    <div class="box-footer">

                        <div class="pull-right">

                            <?php $element = $form->getElement('terminar'); ?>

                            <?php echo $form->createElement($element); ?>

                        </div>

                    </div>

                    <?php echo $form->closeForm(); ?>

                </div>

            </div>

        </div>

    </div>
    <?php if (isset($_noValid)) { ?>
        <script>$('#modalAddFile').modal('show');</script>
    <?php } ?>
    <script>

        $(".imagesInput").fileinput({

            allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf'],

            maxFileSize: 1000,

            showUpload: false

        });
        $('#modalAddFile').on('shown.bs.modal', function (e) {

            if ($('#action').val() === 'insert') { $('#name').focus(); }



            var input = null;

            $("form[name=file]").find('div').each(function () {

                if ($(this).hasClass('has-error') === true) {

                    input = $(this).data('errorfor');

                    return false;

                }

            });



            if (input !== null) { $('#' + input).focus(); }

        });


        $('._addFile').on('click', function () {

            clearForm('file');

            $('form[name=file] #action').val('insert');

            $('form[name=file] #id').val('');

            $('.flashmessenger').html('');

            _getTranslation('Agregar Archivo', function (msj) { $('#title_modal_file').html(msj); });

            $('#modalAddFile').modal('show');

            if ($('#action').val() === 'insert') { $('#name').focus(); }

        });

        $('._closeModalFile').on('click', function () {

            clearForm('file');

            $('.flashmessenger').html('');

            $('#modalAddFile').modal('hide');

        });

    </script>

</div>