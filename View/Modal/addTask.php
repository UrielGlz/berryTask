<!-- Modal -->

<div id="modalAddTask" class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog"
    style=" overflow: hidden;background: transparent">

    <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content">

            <div class="modal-body">

                <div class="box box-primary">

                    <div class="box-header with-border">

                        <button type="button" class="close _closeModalTask"><i class="fa fa-window-close"></i></button>

                        <h4><i class='fa-fw fa fa-tags'></i> <span id="title_modal_task">
                                <?php echo $_translator->_getTranslation('Agregar Tarea'); ?>
                            </span></h4>

                    </div>

                    <?php
                    $form = new TaskForm();

                    echo $form->openForm();

                    echo $form->showActionController();

                    echo $form->showId(); ?>

                    <div class="box-body">

                        <div class='flashmessenger-add_task'>
                            <?php $flashmessenger->showMessage(true); ?>
                        </div>

                        <div class="col-md-6 col-xs-12">

                            <?php
                            $form->showElement('task_id');
                            $form->showElement('task_name');

                            $form->showElement('description');

                            $form->showElement('due_date');

                            $form->showElement('due_time');



                            ?>

                        </div>

                        <div class="col-md-6 col-xs-12">

                            <?php

                            $form->showElement('responsable');

                            $form->showElement('category_id');

                            $form->showElement('prioritie_id');

                            //  $form->showElement('status');
                            
                            $form->showElement('parent_task_id');

                            ?>

                        </div>

                    </div>

                    <div class="box-footer">

                        <div class="pull-right">

                            <input type="button" value="<?php echo $_translator->_getTranslation('Terminar') ?>"
                                class='btn btn-primary _saveTask' />

                        </div>

                        <div class="pull-left">

                            <input type="button" value="<?php echo $_translator->_getTranslation('Cancelar') ?>"
                                class="btn btn-default _closeModalTask" />

                        </div>

                    </div>

                    <?php echo $form->closeForm(); ?>

                </div>

            </div>

        </div>

    </div>

    <script>
        $("#due_time").datetimepicker({ format: "hh:mm A " });

        $('#modalAddTask').on('shown.bs.modal', function (e) {

            if ($('#action').val() === 'insert') { $('#name').focus(); }



            var input = null;

            $("form[name=task]").find('div').each(function () {

                if ($(this).hasClass('has-error') === true) {

                    input = $(this).data('errorfor');

                    return false;

                }

            });



            if (input !== null) { $('#' + input).focus(); }

        });

        $('._addTask').on('click', function () {
            $("#div_detailsTask").removeClass('hide');
            clearForm('task');

            $('form[name=task] #action').val('insert');

            $('form[name=task] #id').val('');

            $('form[name=task] #task_name').focus();

            $("#list_comment").html("");
            
            $("#_showParenTask").addClass('hide'); //Add hide class a boton de parent_task

            $("._addComment").addClass('hide'); //Add hide class a boton de alta de comentarios

            $("#btnSaveTask").html('Guardar');

            
         
            //$('form[name=task] #id').val($('form[name=task] #project_id').val());
            if ($(this).attr("id")) {
                
                $('form[name=task] #parent_task_id').val($('form[name=task] #task_id').val());

            } else {
              //  $("#div_detailsTask").addClass('hide');
                $('form[name=task] #parent_task_id').val('');//eliminamos cualquier relacion con parent_task_id ya que es un insert nuevo            

            }

          //  $("#div_detailsTask").addClass('hide');

            $('.flashmessenger').html('');

            //_getTranslation('Agregar Tarea', function (msj) { $('#title_modal_task').html(msj); });

            //$('#modalAddTask').modal('show');

        });

        $('._closeModalTask').on('click', function () {

            clearForm('task');

            $('.flashmessenger').html('');

            $('#modalAddTask').modal('hide');

        });

    </script>

</div>