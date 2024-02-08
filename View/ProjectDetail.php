<section class="content-header">

    <div class="col-sm-6">
        <div class="clearfix">
            <small class="pull-right">
                <?php echo round($_brandData['progreso'], 2); ?>%
            </small>
        </div>
        <div class="progress xs">
            <div class="progress-bar progress-bar-green"
                style="width: <?php echo round($_brandData['progreso'], 2); ?>%;">
            </div>
        </div>

    </div>
    <ol class="breadcrumb">

        <li><a href="<?php echo ROOT_HOST ?>/Controller/Home.php"><i class="fa fa-dashboard"></i>
                <?php echo $_translator->_getTranslation('Inicio') ?>
            </a></li>

        <li><a href="<?php echo ROOT_HOST ?>/Controller/Project.php?action=list"><?php echo $_translator->_getTranslation('Lista de proyectos') ?></a></li>

        <li class="active">
            <?php echo $_translator->_getTranslation('Editar Proyecto'); ?>
        </li>

    </ol>
</section>
<section class="content">

    <div class="row">
        <div class="col-md-7">
            <?php echo $_form->openForm(); ?>

            <div style="display: none">
                <?php

                echo $_form->showActionController();

                echo $_form->showId();

                $_form->showElement('status'); ?>

            </div>


            <div class="box">
                <div class="box-header with-border" id="_breadcrumb">
                    <h3 class="box-title">
                        <i class='fa-fw fa fa-bookmark'></i>
                        <a
                            href="<?php echo ROOT_HOST ?>/Controller/Project.php?action=edit&id=<?php echo $_GET['id'] ?>">
                            <?php echo $_brandData['name']; ?></a>

                    </h3>




                    <div class="box-tools pull-right">
                        <!-- Collapse Button -->
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                            <i class="fa fa-minus"></i></button>

                    </div>


                </div>

                <div class='flashmessenger'>
                    <?php $flashmessenger->showMessage(); ?>
                </div>

                <div class="box-body">
                    <div class="card">
                        <!-- Start tab -->
                        <ul class="nav nav-tabs" role="tablist">

                            <li role="presentation">
                                <a href="#general" aria-controls="home" role="tab" data-toggle="tab">
                                    <?php echo $_translator->_getTranslation('Resumen') ?>
                                </a>
                            </li>

                            <li role="presentation" class="active">
                                <a href="#listaTareas" aria-controls="home" role="tab" data-toggle="tab">
                                    <?php echo $_translator->_getTranslation('Lista') ?>
                                </a>
                            </li>

                        </ul>
                        <br />
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane" id="general">
                                <div class="clear"></div>

                                <div class="col-xs-12 col-md-12">

                                    <div class="col-xs-12 col-md-6">

                                        <?php $_form->showElement('uuid'); ?>

                                        <?php $_form->showElement('customer_name'); ?>

                                        <?php $_form->showElement('name'); ?>

                                        <?php $_form->showElement('members'); ?>

                                        <?php $_form->showElement('customer_id'); ?>

                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <?php echo $_translator->_getTranslation('Categoria') ?>
                                                    </th>
                                                    <th style="width: 10px">
                                                        <?php echo $_translator->_getTranslation('Total') ?>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_resumenP">
                                                <?php
                                                if (isset($_brandData)) {?>

                                                        <tr>
                                                            <td>
                                                                <?php echo $_translator->_getTranslation('Total de tareas') ?>
                                                            </td>
                                                            <td>
                                                                <span class="label label-default">
                                                                    <?php echo $_brandData['total_tareas'] ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <?php echo $_translator->_getTranslation('Tareas finalizadas') ?>
                                                            </td>
                                                            <td><span class="label label-success">
                                                                    <?php echo $_brandData['completas'] ?>
                                                                </span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <?php echo $_translator->_getTranslation('Tareas en proceso') ?>
                                                            </td>
                                                            <td><span class="label label-info">
                                                                    <?php echo $_brandData['proceso'] ?>
                                                                </span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <?php echo $_translator->_getTranslation('Tareas retrazadas') ?>
                                                            </td>
                                                            <td><span class="label label-danger">
                                                                    <?php echo $_brandData['retrazo'] ?>
                                                                </span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <?php echo $_translator->_getTranslation('Tareas en riesgo') ?>
                                                            </td>
                                                            <td><span class="label label-warning">
                                                                    <?php echo $_brandData['riesgo'] ?>
                                                                </span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <?php echo $_translator->_getTranslation('Tareas nuevas') ?>
                                                            </td>
                                                            <td><span class="label label-primary">
                                                                    <?php echo $_brandData['nueva'] ?>
                                                                </span></td>
                                                        </tr>

                                                    <?php 
                                                } ?>

                                            </tbody>

                                        </table>
                                    </div>

                                    <div class="col-xs-12 col-md-6">

                                        <?php $_form->showElement('date_start'); ?>

                                        <?php $_form->showElement('date_end'); ?>

                                        <?php $_form->showElement('description'); ?>

                                        <?php $_form->showElement('status'); ?>

                                    </div>


                                    <div class="pull-right">

                                        <?php $_form->showElement('terminar'); ?>

                                    </div>

                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane active" id="listaTareas">
                                <div class="pull-left">
                                    <span class="btn btn-default pull-left _addTask"><i class='fa fa-plus'></i>
                                        <?php echo $_translator->_getTranslation('Agregar Tarea') ?>
                                    </span>
                                </div>
                                <br>
                                <br>

                                <div class="card-datatable table-responsive" id="accordion">

                                    <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                                    <table class="dt-row-grouping table display compact" id="TaskListTable">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Descipción de la tarea</th>
                                                <th>Fecha vencimiento</th>
                                                <th>Categoria</th>
                                                <th>Prioridad</th>
                                                <th>Responsable</th>
                                                <th>Status</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="Tbody_taskList">

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="clear"></div>

                    <div class="box-footer">

                        <div class="pull-right">

                            <!-- <?php $element = $_form->getElement('terminar'); ?>
                            
                                            <?php echo $_form->createElement($element); ?> -->

                        </div>

                        <div class="pull-left">


                        </div>

                    </div>

                </div><!-- /.box-body -->

                <?php echo $_form->closeForm(); ?>

            </div><!-- /.box -->

        </div>

        <!-- apartado de TAREA DETALLES -->
        <div class="col-xs-5 col-md-5">
            <div class="box">
                <div class="box-header with-border">
                    <!-- boton de minimizar -->
                    <!-- <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                title="Collapse">
                                <i class="fa fa-minus"></i></button>

                        </div> -->

                </div>

                <div class="box-body hide" id="div_detailsTask">

                    <div class="card">
                        <!-- Start tab -->
                        <?php $form = new TaskForm();
                        echo $form->openForm();


                        ?>
                        <div class="box-body">

                            <div class="row">
                                <div class='flashmessenger-add_task'>
                                    <?php $flashmessenger->showMessage(true); ?>
                                </div>
                                <div class="col-sm-12 invoice-col">
                                    <!-- Botones de subtarea, actualizar , comentarios -->
                                    <div class="form-group pull-left">

                                        <button type="button" class="btn btn-defaul btn-sm hide" id="btnCloseTask"><i
                                                class="fa fa-check"></i> Marcar como finalizada</button>
                                    </div>
                                    <div class="form-group pull-right">
                                        <button type="button" class="btn bg-maroon btn-sm _addTask" id="parent_task"><i
                                                class="fa fa-copy"></i>
                                            Subtarea</button>
                                        <button type="submit" class="btn btn-success btn-sm" id="btnSaveTask"><i
                                                class="fa fa-save"></i>
                                            Actualizar</button>
                                        <!-- <button type="button" class="btn btn-default btn-sm _addComment"><i
                                                    class="fa fa-commenting"></i></button> -->
                                        <button type="button" class="btn btn-default btn-sm" id="_showParenTask"><i
                                                class="fa fa-link"></i></button>
                                        <button type="button" class="btn btn-default btn-sm" id="_copyTask"><i
                                                class="fa fa-copy"></i></button>



                                    </div>

                                </div>
                                <!-- Barra de progress por tarea -->
                                <div class="col-sm-12 invoice-col">
                                    <div class="progress" id="progess_task">


                                    </div>
                                </div>

                                <div class="col-sm-12 invoice-col">
                                    <!-- Input text task_name -->
                                    <div class="form-group">
                                        <input type="text" required="true" class="form-control" id="task_name"
                                            name="task_name" style="font-weight:bold;" placeholder="Nombre de Tarea">
                                    </div>
                                </div>
                                <div class="col-sm-6 invoice-col">
                                    <!-- Select members -->
                                    <div class="form-group">
                                        <select class="form-control select2" style="width: 100%;" id="responsable"
                                            name="responsable" required="true">
                                            <?php

                                            $listPriorities = $form->getListUsers();

                                            foreach ($listPriorities as $key => $value) { ?>
                                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                            <?php }
                                            ?>
                                        </select>
                                    </div>
                                    <!-- Input type date due_date -->
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="due_date" name="due_date"
                                            placeholder="Fecha">
                                    </div>
                                    <!-- Select category_id -->
                                    <div class="form-group">
                                        <select class="form-control select2" style="width: 100%;" id="category_id"
                                            name="category_id">
                                            <?php

                                            $listPriorities = $form->getListCategoryTask();

                                            foreach ($listPriorities as $key => $value) { ?>
                                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                            <?php }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 invoice-col">
                                    <!-- Select prioritie_id  -->
                                    <div class="form-group">
                                        <select class="form-control select2" style="width: 100%;" id="customer_id"
                                            name="customer_id">
                                            <?php
                                            $listCustomers = $form->getListCustomer();

                                            foreach ($listCustomers as $key => $value) { ?>
                                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                            <?php }
                                            ?>
                                        </select>
                                    </div>
                                    <!-- Select prioritie_id  -->
                                    <div class="form-group">
                                        <select class="form-control select2" style="width: 100%;" id="prioritie_id"
                                            name="prioritie_id">
                                            <?php
                                            $listPriorities = $form->getListPrioritie();

                                            foreach ($listPriorities as $key => $value) { ?>
                                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                            <?php }
                                            ?>
                                        </select>
                                    </div>
                                    <!-- select status -->
                                    <div class="form-group">
                                        <select class="form-control select2" style="width: 100%;" id="status"
                                            name="status">
                                            <?php

                                            $listPriorities = $form->getListStatus();

                                            foreach ($listPriorities as $key => $value) { ?>
                                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                            <?php }
                                            ?>
                                        </select>
                                    </div>

                                </div>
                                <div class="col-sm-12 invoice-col">
                                    <div class="form-group">
                                        <textarea class="form-control" id="description" name="description" rows="7"
                                            placeholder="Descripción ..."></textarea>
                                    </div>
                                    <div class="form-group">
                                        <input type="file" id="attachement_file" name="attachement_file"
                                            class="form-control upload fileinput" />
                                    </div>
                                    <!-- Listado de archivos por tarea -->
                                    <div class="form-group" id="ul_Listfiles">

                                    </div>
                                    <!-- Listado de comentarios creados por tarea -->
                                    <div class="form-group">
                                        <ul class="timeline timeline-inverse" id="list_comment"> </ul>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <?php

                        echo $form->showActionController();

                        echo $form->showId();

                        $form->showElement('task_id');

                        $form->showElement('project_id');

                        $form->showElement('parent_task_id');

                        $form->showElement('uuid');

                        echo $form->closeForm(); ?>

                        <div class="clear"></div>
                        <!-- Apartado de agregar comentarios por tarea -->
                        <div class="box-footer">
                            <?php
                            $form_comment = new CommentForm();

                            echo $form_comment->openForm();

                            echo $form_comment->showActionController();

                            echo $form_comment->showId(); ?>
                            <div class="input-group">
                                <input type="text" id="comment" name="comment" placeholder="Comentario ..."
                                    class="form-control" autocomplete="false" required="true">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-primary btn-flat _saveComment">Enviar</button>
                                </span>
                            </div>
                            <?php echo $form_comment->closeForm(); ?>

                        </div>

                    </div><!-- /.box-body -->

                </div><!-- /.box -->
            </div>
        </div>
    </div>

</section>


<style>

</style>
<?php
include ROOT . "/View/Modal/addTask.php";
//include ROOT . "/View/Modal/addComment.php";

include ROOT . "/View/Modal/addFile.php";



?>
<?php if (isset($_disabled) && $_disabled) { ?>

    <script>

        $('.date_start').prop('disabled', true);

        $('.date_end').prop('disabled', true);

    </script>

<?php } ?>




<script>
    let url_ = '<?php echo ROOT_HOST ?>';

    // getTaskByProject();

    let searchParams = new URLSearchParams(window.location.search);

    let param = searchParams.get('task');
    let id_project = searchParams.get('id');

    $('form[name=task] #project_id').val(id_project);


    if (param) {
        _parent_task_id = param;
        getTaskByProject();
        getTaskById(param);
        // getNotification();

        searchParams.delete(param);
    } else {
        getTaskByProject();
    }

    $(".fileinput").fileinput({

        allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf'],

        maxFileSize: 10000,

        showUpload: false

    });

    $("#_showParenTask").on("click", function () {
        _parent_task_id = $('form[name=task] #task_id').val();
        _task_name = $('form[name=task] #task_name').val();
        _project_id = $('form[name=project] #id').val();
        $("#a_task_name").removeClass('hide');
        getTaskByProject();

        breadcrumb = "<a class='' href='" + url_ + "/Controller/Project.php?action=edit&id=" + _project_id + "&task=" + _parent_task_id + "'>" + _task_name + "</a>";
        $("#_breadcrumb").append(" / " + breadcrumb);
        $("#div_detailsTask").addClass('hide');
    });
    $("#_btnStartTask").on("click", function () {
        startTask();
    });
    $("#btnCloseTask").on("click", function () {
        var id = $('form[name=task] #task_id').val();
        closeTask(id);

    });
    $("#_copyTask").on("click", function () {
        copyTask();
    });

    $('._showTask').on('click', function () {

        getTaskById();

    });
    $('._saveTask').on('click', function () { saveTask(); });
    $('._saveComment').on('click', function () { saveComment(); });
    $('._saveFile').on('click', function () { saveFile(); });




    $("[data-widget='collapse']").click();
    $('#date_start,#date_end,#due_date,#expiration_date').datetimepicker({ format: 'MM/DD/YYYY', useCurrent: false });

    $('#customer_id').select2();

    $('select').on('select2:opening', function (e) {

        e.preventDefault();

        if ($(this).attr('readonly') !== 'readonly') { $(this).unbind('select2:opening').select2('open'); }

    });

    $('#members').select2();

    $('#customer_id').on('select2:select', function () {

        $('#date_start').data("DateTimePicker").destroy();

        $('#date_start').datetimepicker({ format: 'MM/DD/YYYY', useCurrent: false });

        $('#date_start').data("DateTimePicker").clear();

        $('#date_end').data("DateTimePicker").destroy();

        $('#date_end').datetimepicker({ format: 'MM/DD/YYYY', useCurrent: false });

        $('#date_end').data("DateTimePicker").clear();

    });



</script>