<section class="content-header">

    <h1><i class='fa-fw fa fa-tags'></i>
        <?php echo $_translator->_getTranslation('Catalogo de Categoria de Tareas'); ?></small>
    </h1>

    <ol class="breadcrumb">

        <li><a href="<?php echo ROOT_HOST ?>/Controller/Home.php"><i class="fa fa-dashboard"></i>
                <?php echo $_translator->_getTranslation('Inicio') ?>
            </a></li>

        <li class="active">
            <?php echo $_translator->_getTranslation('Categoria de Tareas'); ?>
        </li>

    </ol>

</section>

<section class="content">

    <div class="box">

        <div class="box-header with-border">

            <h3 class="box-title"></h3>

            <div class="box-tools pull-right">

                <span class="btn btn-default pull-right _addCategoryTask"><i class='fa fa-plus'></i>
                    <?php echo $_translator->_getTranslation('Agregar Categoria') ?>
                </span>

            </div><!-- /.box-tools -->

        </div><!-- /.box-header -->

        <div class="box-body">

            <div class='flashmessenger'>
                <?php $flashmessenger->showMessage(null); ?>
            </div>

            <div class="clear"></div>

            <div class="table-responsive">
            <form autocomplete="off">
            <table id="tblPaymentTerms"
                    class="table table-bordered table-condensed table-striped table-hover font-size-12 datatable_whit_filter_column _hideSearch"
                    style="width:100%">

                    <thead>

                        <th class="col-md-1 col-xs-1 text-center">
                            <?php echo $_translator->_getTranslation('No'); ?>
                        </th>

                        <th class="text-center">
                            <?php echo $_translator->_getTranslation('Categoria de Tarea'); ?>
                        </th>

                        <th class="text-center">
                            <?php echo $_translator->_getTranslation('Color'); ?>
                        </th>

                        <th class="col-md-1 col-xs-1 text-center">
                            <?php echo $_translator->_getTranslation('Accion'); ?>
                        </th>

                    </thead>

                    <tfoot>

                        <th class="filter text-center">
                            <?php echo $_translator->_getTranslation('No'); ?>
                        </th>

                        <th class="filter text-center">
                            <?php echo $_translator->_getTranslation('Categoria de Tarea'); ?>
                        </th>

                        <th class="filter text-center">
                            <?php echo $_translator->_getTranslation('Color'); ?>
                        </th>

                        <th></th>

                    </tfoot>

                    <tbody class="CategoryTask">

                        <?php

                        if ($_listCategoryTask) {

                            foreach ($_listCategoryTask as $CategoryTask) { ?>

                                <tr>

                                    <td class="text-center" data-id="<?php echo $CategoryTask['id'] ?>"> <?php echo $CategoryTask['id'] ?></td>

                                    <td class="text-center" data-id="<?php echo $CategoryTask['id'] ?>"> <?php echo $CategoryTask['name'] ?></td>

                                    <td class="text-center" data-id="<?php echo $CategoryTask['id'] ?>"> <?php echo $CategoryTask['color'] ?></td>

                                    <td class="text-center">

                                        <span class="btn btn-default _edit" data-id="<?php echo $CategoryTask['id'] ?>"><i
                                                class="fa fa-edit"></i></span>

                                        <span class="btn btn-danger _delete" data-id="<?php echo $CategoryTask['id'] ?>"><i
                                                class="fa fa-trash"></i></span>

                                    </td>

                                </tr>

                            <?php }

                        } ?>

                    </tbody>

                </table>

            </form>
               
            </div>

        </div><!-- /.box-body -->

    </div><!-- /.box -->

</section>

<?php include ROOT . "/View/Modal/addCategoryTask.php"; ?>

<?php if (isset($_noValid)) { ?>
    <script>$('#modalAddCategoryTask').modal('show');</script>
<?php } ?>

<script>
     $('.my-colorpicker1').colorpicker({        
        
        format: 'hex',
        colorSelectors: {
                'black': '#000000',
                'white': '#ffffff',
                'red': '#FF0000',
                'default': '#777777',
                'primary': '#337ab7',
                'success': '#5cb85c',
                'info': '#5bc0de',
                'warning': '#f0ad4e',
                'danger': '#d9534f'
            },
            sliders: {
                saturation: {
                    maxLeft: 100,
                    maxTop: 40
                },
                hue: {
                    maxTop: 40
                },
                alpha: {
                    maxTop: 100
                }
            },
    });
    $('tbody.CategoryTask td ._edit').on('click', function (e) {

        if (!$(e.target).closest('._delete').length) {

            clearForm('CategoryTask');

            $('.flashmessenger').html('');

            _getTranslation('Editar Categoria', function (msj) { $('#title_modal_CategoryTask').html(msj); });

            var id = $(this).data('id');

            setDataToEditCategoryTask(id);

        }

    });



    $('tbody.CategoryTask td ._delete').on('click', function () {

        var id = $(this).data('id');

        deleteCategoryTask(id);

    });



    $('._saveCategoryTask').on('click', function () { submit('CategoryTask'); });

</script>