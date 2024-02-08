<section class="content-header">
    <h1><i class='fa-fw fa fa-bar-chart'></i> <?php echo $_translator->_getTranslation('Reportes');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Reportes');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right"></div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="clear"></div>
    <div class="grid-container">
        <?php        
        $_settings = new SettingsRepository();
        $_idAreaForPanaderia = $_settings->_get('id_area_for_panaderia');
                
        $login = new Login();
        switch($login->getRole()){
            case '1':?>
                <a class="grid-item"  href="?action=create&report=inventory&output=screen">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Inventario');?>
                </a>
                <a class="grid-item _report" data-reportname="store_request">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Pedidos de sucursales');?>
                </a>
                <a class="grid-item _report" data-reportname="time_clock">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Reloj checador');?>
                </a>
                <a class="grid-item _report" data-reportname="sales">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Ventas');?>
                </a>
                <a class="grid-item _report" data-reportname="special_orders">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Ordenes especiales');?>
                </a>
                <a class="grid-item"  href="?action=create&report=inventory_template_pdf&output=pdf" target="_pdf_para_inventario">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('PDF para inventarios');?>
                </a>
                <a class="grid-item _report" data-reportname="physical_inventory">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Inventario fisico');?>
                </a>
                <a class="grid-item _report" data-reportname="bakery_production">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Produccion panaderia');?>
                </a>
                <a class="grid-item _report" data-reportname="detailed_bakery_orders">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Pedidos panaderia detallado');?>
                </a>         
                <a class="grid-item _report" data-reportname="sales_to_store">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Ventas a sucursal');?>
                </a> 
                <a class="grid-item _report" data-reportname="sales_by_store">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Ventas netas por sucursal');?>
                </a> 
                <a class="grid-item _report" data-reportname="review_payroll">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Revision de payroll');?>
                </a>
                <a class="grid-item _report" data-reportname="invoices">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Facturas');?>
                </a>
                    <?php
                break;
            
            case '2':?>
                <a class="grid-item"  href="?action=create&report=inventory&output=screen">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Inventario');?>
                </a>
                <a class="grid-item _report" data-reportname="store_request">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Pedidos de sucursales');?>
                </a>
                <a class="grid-item _report" data-reportname="time_clock">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Reloj checador');?>
                </a>
                <a class="grid-item _report" data-reportname="sales">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Ventas');?>
                </a>
                <a class="grid-item _report" data-reportname="special_orders">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Ordenes especiales');?>
                </a>
                <a class="grid-item"  href="?action=create&report=inventory_template_pdf&output=pdf" target="_pdf_para_inventario">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('PDF para inventarios');?>
                </a>
                <a class="grid-item _report" data-reportname="physical_inventory">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Inventario fisico');?>
                </a>
                <a class="grid-item _report" data-reportname="detailed_bakery_orders">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Pedidos panaderia detallado');?>
                </a> 
                 <a class="grid-item _report" data-reportname="review_payroll">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Revision de payroll');?>
                </a><?php
                break;
            
            case '3':?>
                <a class="grid-item _report" data-reportname="store_request">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Pedidos de sucursales');?>
                </a>
                <a class="grid-item _report" data-reportname="physical_inventory">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Inventario fisico');?>
                </a><?php
                break;
            
            case '4':?>                 
                <a class="grid-item _report" data-reportname="bakery_production">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Produccion panaderia');?>
                </a><?php
                break;
            
            case '5':?>        
                <a class="grid-item"  href="?action=create&report=inventory_template_pdf&output=pdf" target="_pdf_para_inventario">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('PDF para inventarios');?>
                </a><?php
                break;
            
            case '6':?>
                <a class="grid-item _report" data-reportname="sales">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Ventas');?>
                </a>
                <a class="grid-item _report" data-reportname="special_orders">
                    <i class="fa fa-signal"></i><br /><?php echo $_translator->_getTranslation('Ordenes especiales');?>
                </a><?php
                break;
        } ?>
    </div> 
    <?php 
    if(isset($_reporte)){ 
        include $_reporte->getTemplateReporteOnScreen();
    } ?>
  </div><!-- /.box-body -->
</div><!-- /.box -->
</section>
 <?php include ROOT."/View/Modal/filtroReportes.php";?>
 <?php //include ROOT."/View/Modal/enviarMailReporte.php";?>
<style>
  .grid-container {
    display: grid;
    grid-template-columns: 25% 25% 25% 25%;
    /*background-color: #2196F3;*/
    padding: 10px;
  }
  .grid-item {
    /*background-color: rgba(255, 255, 255, 0.8);*/
    border: 1px solid #ccc;
    padding: 20px;
    font-size: 1.5rem;
    text-align: center;
    cursor: pointer;
  }
 
  </style>
<script type="text/javascript">
    $('.linkSendMail').click( function(e) {
        e.preventDefault(); 
        $('#modalSendReportToMail').modal('show');
        $("#optionsFromGet").val(this.href);
        return false; 
    });
    
    $('._report').on('click',function(){
        setFilterReport(this,function(){
            if($('#report').val() === 'store_request'){
                $('#area_id').on('select2:select',function(){ 
                    if($(this).val() == '<?php echo $_idAreaForPanaderia?>'){
                        $('#masa').prop('disabled',false);
                    }else{
                        $('#masa').val('').trigger('change');
                        $('#masa').prop('disabled',true);
                    }
                });
                
            }else if($('#report').val() === 'physical_inventory'){
                $("#startDate").on('dp.change',function(e){if(e.oldDate !== null){setMaxEndDateForPhysicalInventory(e);}});
            }
        });
    });
</script>