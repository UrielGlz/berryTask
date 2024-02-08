<div class="box box-primary">
<div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-th-large"></i> <?php echo $_translator->_getTranslation(ucfirst($_reporte->getTituloReporte()));?></h3>
    <div class="box-tools pull-right">
        <a class="btn btn-default" href="<?php echo $_reporte->getStringToSendGET().'&action=create&output=excel' ?>"><i class="fa fa-file-excel-o"></i> <?php echo $_translator->_getTranslation('Exportar a excel')?></a>
        <!--<a class="btn btn-default" href="<?php echo $_reporte->getStringToSendGET().'&enviarPorMail' ?>" class="linkSendMail" data-toggle='modal' data-target='#modalSendReportToMail'><i class="fa fa-envelope"></i> <?php echo $_translator->_getTranslation('Enviar por correo')?></a>-->
    </div><!-- /.box-tools -->
</div><!-- /.box-header -->
<div class="box-body">
<?php
$data = $_reporte->getReporte();
if($data == null){
    echo "There is no information";
    exit;
}
$rangeDays = $data['rangeDays'];
$data = $data['data'];
?>
<div class="table-responsive">
<table id="tblReport" class="table table-striped table-bordered table-condensed table-hover">
<thead class="table-header">
<tr>
    <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Codigo');?></th>
    <th class="col-md-3 text-center"><?php echo $_translator->_getTranslation('Descripcion');?></th>
    <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Tamaño');?></th>
    <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Categoria');?></th>
    <?php foreach($rangeDays as $key => $sucursal){?>                       
        <th class="verticalTableHeader"><p><?php echo $sucursal;?></p></th>
    <?php }?>                
</tr>
</thead>
<tfoot>                        
    <th class="filter text-center"><?php echo $_translator->_getTranslation('Codigo');?></th>
    <th class="filter text-center"><?php echo $_translator->_getTranslation('Descripcion');?></th>
    <th class="filter text-center"><?php echo $_translator->_getTranslation('Tamaño');?></th>
    <th class="filter text-center"><?php echo $_translator->_getTranslation('Categoria');?></th>
     <?php foreach($rangeDays as $key => $sucursal){?>                       
        <th class="filter text-center"><?php echo $sucursal;?></th>
    <?php }?> 
</tfoot>
<tbody>
<?php 
$login = new Login();
$currentSucursal = $login->getStoreId();
foreach($data as $row){?>
    <tr >
    <td class="text-center"><?php echo $row['code'];?></td>
    <td class="text-center"><?php echo $row['product'];?></td>
    <td class="text-center"><?php echo $row['size'];?></td>
    <td class="text-center"><?php echo $row['category'];?></td>      
    <?php
    foreach($rangeDays as $idSucursal => $dia){
        $class = '';?>                       
        <td class="<?php echo $class; ?> text-right" ><?php echo number_format($row["$dia"],2);?></td><?php                   
    }?>   
    </tr><?php  
}?>                    
</tbody>
</table>
</div>
</div>
</div>
<?php //include ROOT."/View/Modal/editInventory.php";?>
<style>
    tfoot {
        display: table-header-group;
    }
    
    .verticalTableHeader {
        text-align:center;
        white-space:nowrap;
        g-origin:50% 50%;
        -webkit-transform: rotate(-90deg);
        -moz-transform: rotate(-90deg);
        -ms-transform: rotate(-90deg);
        -o-transform: rotate(-90deg);
        transform: rotate(-90deg);
        border: 0px !important;

    }
    .verticalTableHeader p {
        margin:0 -100% ;
        display:inline-block;
    }
    .verticalTableHeader p:before{
        content:'';
        width:0;
        padding-top:110%;/* takes width as reference, + 10% for faking some extra padding */
        display:inline-block;
        vertical-align:middle;
    }
    
    th.sorting.verticalTableHeader::after,
    th.sorting_asc.verticalTableHeader::after, 
    th.sorting_desc.verticalTableHeader::after { content:"" !important; }
</style>
<script type="text/javascript" language="javascript">
    
    $('#tblReport').DataTable({
        paginate:false,
        filter:true,
        aaSorting:[]
    });
    $('#tblReport')
        .removeClass( 'display' )
        .addClass('table table-striped table-bordered');

    $('#tblReport tfoot th.filter').each( function () {
           $(this).html( '<input type="text" placeholder="Search" style="width:100%" />' );
       } );  

       var table = $('#tblReport').DataTable();
       // Apply the search
       table.columns().every( function () {
           var that = this; 
           $( 'input', this.footer() ).on( 'keyup change', function () {
               if ( that.search() !== this.value ) {
                   that
                       .search (this.value.replace("/;/g", "|"), true, false)
                       .draw();
               }
           } );
       } );
</script>