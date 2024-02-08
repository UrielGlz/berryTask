<section class="content-header">
    <h1><i class='fa-fw fa fa-list'></i> <?php echo $_translator->_getTranslation('Deposito');if($action == 'edit'){echo " #".$_deposit->getDepositNumber();}?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
        <li><a href="<?php echo ROOT_HOST?>/Controller/Deposit.php?action=list"><?php echo $_translator->_getTranslation('Lista de depositos')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Depositos');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
    <?php echo $_form->openForm();?>
    <div style="display: none"><?php
        echo $_form->showActionController();
        echo $_form->showId();
        echo $_form->showElement('total');
        echo $_form->showElement('status');
        ?>
    </div>
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="box-body">
    <div class="clear"></div>
    <div class="card">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#tab_general" aria-controls="home" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Informacion general') ?></a></li>                        
        </ul>
        <!-- Tab panes -->
        <div class="tab-content" style="padding-top: 20px">
            <div role="tabpanel" class="tab-pane active" id="tab_general">
                <div class="col-xs-12 col-md-6">               
                    <?php $_form->showElement('date');?> 
                    <?php $_form->showElement('store_id');?>
                    <?php $_form->showElement('comments');?> 
                </div> 
                <div class="col-xs-12 col-md-6">
                    <?php 
                    $_form->showElement('deposit_file');
                    $_form->showElement('attachments[]');
                    echo $_deposit->getListFiles($_form->getId());?>  
                </div>
            </div>
        </div>
    </div>    
    <div class="clear"></div>
    <div class='col-md-12'>
        <hr/>
        <?php 
            $gastoAjax = new DepositAjax();                
            $listGastoDetalles = $gastoAjax->getListDepositDetails(array('token_form'=>$_form->getTokenForm()));?>
        <div class="card">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#details" aria-controls="home" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Detalles de deposito') ?></a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content" style="padding-top: 20px">
                <div role="tabpanel" class="tab-pane active" id="details">
                    <div class='table-responsive'>
                        <table id='tableDeposit' class="table table-condensed table-striped table-hover datatable_whit_filter_column _hideSearch" style="width:100%">
                            <thead>                
                                <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Accion')?></th>
                                <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Fecha incial');?></th>   
                                <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Fecha final');?></th>  
                                <th class="col-md-4 text-left"><?php echo $_translator->_getTranslation('Notas')?></th>     
                                <th class="col-md-2 text-right"><?php echo $_translator->_getTranslation('Total')?></th>
                            </thead>
                            <tfoot>
                                <th></th>
                                <th class="filter"><?php echo $_translator->_getTranslation('Fecha inicial')?></th>
                                <th class="filter"><?php echo $_translator->_getTranslation('Fecha final')?></th>
                                <th class="filter"><?php echo $_translator->_getTranslation('Notas');?></th>     
                                <th class="filter"><?php echo $_translator->_getTranslation('Total')?></th>
                            </tfoot>
                            <tbody>
                                <?php echo $listGastoDetalles['depositDetails'];?>
                            </tbody>
                        </table>     
                    </div>
                </div>
            </div>
        </div>
       
        <div class="col-lg-6 col-md-6 text-left">
            <?php $element = $_form->getElement('addDepositDetail'); echo $_form->createOnlyElement($element);?>
        </div>      
        <div class="clear"></div>
        <div class="col-lg-4 col-md-4 pull-right">
            <table class="table table-condensed table-striped table-hover">
                <tfoot class="table table-condensed">
                <tr>
                    <th class="text-right" colspan="2" style="border: 0px;"><?php echo $_translator->_getTranslation('Total')?> $</th>
                    <td class="_total text-right col-lg-3 col-md-3"  style="border: 0px;"><?php echo number_format($listGastoDetalles['total'],2)?></td>
                </tr>
                </tfoot>                
            </table>
        </div>
    </div>
    <!-- Modal -->         
    <div class="col-lg-12">
        <div class="pull-right">
            <div style='float:left'>
                <?php $element = $_form->getElement('terminar');?>
                <?php echo $_form->createElement($element);?>
            </div>
        </div>
        <div class='clear'></div>
    </div>            
  </div><!-- /.box-body -->
  <?php echo $_form->closeForm();?>
</div><!-- /.box -->
</section>
<?php include ROOT."/View/Modal/addDeposit.php";?>
<style>
    .ui-autocomplete {
      z-index: 1510 !important;
    }
    
    tr th div.form-group {
        margin-bottom: 0px;
    }
    
</style>
<?php 
    if(isset($_disabled) && $_disabled === true){?>
        <script>$('table#tableDeposit tbody tr td a').addClass('disabled');</script>
<?php    }
    ?>
<script> 
    $("#date,#dateDatePicker").datetimepicker({format: 'MM/DD/YYYY'});
    $('#store_id').select2(); 
</script>