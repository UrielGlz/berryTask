<section class="content-header">
    <h1><i class='fa-fw fa fa-building'></i> <?php echo $_translator->_getTranslation('Empresa');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Empresa');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="box-header with-border">
        <div class="box-tools"></div><!-- /.box-tools -->
    </div><!-- /.box-header -->
    <div class="box-body">
     <div class="clear"></div>
            <?php echo $form->openForm();?>
            <div style="display: none"><?php
                echo $form->showActionController();
                echo $form->showId();
                ?>
            </div>
            <div class="col-xs-12 col-md-12">
                <div class="col-xs-12 col-md-6">
                    <?php 
                    $form->showElement('name');
                    $form->showElement('address');
                    $form->showElement('city');
                    $form->showElement('state');
                    $form->showElement('zipcode');
                    $form->showElement('contact_name');
                    $form->showElement('phone');
                    ?>
                </div> 
                <div class="col-xs-4 col-md-6">
                    <?php

                    $form->showElement('phone_1');
                    $form->showElement('fax');
                    $form->showElement('email');
                    $form->showElement('email_1');
                    $form->showElement('webpage');
                    $form->showElement('logo');
                    ?>
                </div>
                <div class="clear"></div>
                <div class="pull-right">
                    <?php $form->showElement('send') ?>
                </div>
                
            </div>
            <div class="clear"></div>
            <!-- Modal -->         
        <?php echo $form->closeForm();?>
  </div><!-- /.box-body -->
</div><!-- /.box -->
</section>
<script>
 $("#logo").fileinput({
        showPreview: false,
        allowedFileExtensions: ['jpeg','jpg','png'],
        maxFileSize: 1000,
        showUpload:false,
        showRemove:false
    });
    </script>