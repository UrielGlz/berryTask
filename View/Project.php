<section class="content-header">

    <h1><i class='fa-fw fa fa-bookmark'></i>
        <?php echo $_translator->_getTranslation('Nuevo Proyecto'); ?></small>
    </h1>

    <ol class="breadcrumb">

        <li><a href="<?php echo ROOT_HOST ?>/Controller/Home.php"><i class="fa fa-dashboard"></i>
                <?php echo $_translator->_getTranslation('Inicio') ?>
            </a></li>

        <li><a href="<?php echo ROOT_HOST ?>/Controller/Project.php?action=list"><?php echo $_translator->_getTranslation('Lista de proyectos') ?></a></li>

        <li class="active">
            <?php echo $_translator->_getTranslation('Nuevo Proyecto'); ?>
        </li>

    </ol>

</section>

<section class="content">

    <div class="box">

        <?php echo $_form->openForm(); ?>

        <div style="display: none">
            <?php

            echo $_form->showActionController();

            echo $_form->showId();

            $_form->showElement('status'); ?>

        </div>

        <div class='flashmessenger'>
            <?php $flashmessenger->showMessage(); ?>
        </div>


        <div class="box-body">

            <div class="clear"></div>

            <div class="col-xs-12 col-md-12">

                <div class="col-xs-12 col-md-6">

                    <?php $_form->showElement('name'); ?>

                    <?php $_form->showElement('members'); ?>

                    <?php $_form->showElement('customer_id'); ?>

                </div>

                <div class="col-xs-12 col-md-6">

                    <?php $_form->showElement('date_start'); ?>

                    <?php $_form->showElement('date_end'); ?>

                    <?php $_form->showElement('description'); ?>

                    <?php if ($action == 'edit') {
                        $_form->showElement('statusName');
                    } ?>

                </div>

            </div>

            <div class="clear"></div>

            

            <div class="box-footer">

                <div class="pull-right">

                    <?php $element = $_form->getElement('terminar'); ?>

                    <?php echo $_form->createElement($element); ?>

                </div>

                <div class="pull-left">

                    
                </div>

            </div>

        </div><!-- /.box-body -->

        <?php echo $_form->closeForm(); ?>

    </div><!-- /.box -->

</section>

<style>

</style>

<?php if (isset($_disabled) && $_disabled) { ?>

    <script>

        $('.date_start').prop('disabled', true);

        $('.date_end').prop('disabled', true);

    </script>

<?php } ?>



<script>

    $('#date_start,#date_end').datetimepicker({ format: 'MM/DD/YYYY', useCurrent: false });

    $('#customer_id,#members').select2();


    $('select').on('select2:opening', function (e) {

        e.preventDefault();

        if ($(this).attr('readonly') !== 'readonly') { $(this).unbind('select2:opening').select2('open'); }

    });



    $('#customer_id').on('select2:select', function () {

        $('#date_start').data("DateTimePicker").destroy();

        $('#date_start').datetimepicker({ format: 'MM/DD/YYYY', useCurrent: false });

        $('#date_start').data("DateTimePicker").clear();

        $('#date_end').data("DateTimePicker").destroy();

        $('#date_end').datetimepicker({ format: 'MM/DD/YYYY', useCurrent: false });

        $('#date_end').data("DateTimePicker").clear();

    });



</script>