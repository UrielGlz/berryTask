<div class="box box-primary">

<div class="box-header with-border">

    <h3 class="box-title"><i class="fa fa-th-large"></i> <?php echo $_translator->_getTranslation(ucfirst($_reporte->getTituloReporte()));?></h3>

    <div class="box-tools pull-right"></div><!-- /.box-tools -->

</div><!-- /.box-header -->

<div class="box-body">

<?php

$reportResult = $_reporte->getReporte();

if($reportResult == null){

    echo "There is no information";

    exit;

}

$storeName = $reportResult['storeName'];

$data = $reportResult['data'];

$details = $reportResult['details'];



$total_sales = 0;

$count_sales = 0;

$average_sales = 0;



foreach($data as $sale){

    $total_sales += round($sale['total_venta'],2);

    $count_sales++;

}

?>

<div class="table-responsive">

    <table class="table table-bordered">

        <tbody>

        <tr>

            <td class="text-center"><h2>$ <?php echo number_format($total_sales,2); ?></h2></td>

            <td class="text-center"><h2>$ <?php echo number_format($total_sales/$count_sales,2); ?></h2></td>

            <td class="text-center"><h2><?php echo number_format($count_sales,2); ?></h2></td>

        </tr>

        <tr>

            <th class="text-center label-success"><?php echo $_translator->_getTranslation('Venta neta') ?></th>

            <th class="text-center label-primary"><?php echo $_translator->_getTranslation('Venta promedio'); ?></th>

            <th class="text-center label-info"><?php echo $_translator->_getTranslation('Total de ventas'); ?></th>

        </tr>

        </tbody>

    </table>

    <div class="col-md-6">

        <?php        

            $arrayProducts = array(

                '161'=>array('name'=>'Pan por libra','qty_sold'=>0,'total_sold'=>0) /*238*/

            );

            foreach($details as $detail){

                /*Si es sucursal 5 (Milla 3)  y producto es 2, Forzamos a que sea 161*/

                if($detail['idSucursal'] == '5'){

                    if($detail['idProducto'] == '161'){$detail['idProducto'] = 'xxx';}

                    if($detail['idProducto'] == '2'){$detail['idProducto'] = '161';}                

                }

                

                 /*Si es sucursal 6 (NOlana)  y producto es 2, Forzamos a que sea 161*/

                if($detail['idSucursal'] == '6'){

                    if($detail['idProducto'] == '161'){$detail['idProducto'] = 'xxx';}

                    if($detail['idProducto'] == '2'){$detail['idProducto'] = '161';}                

                }

                

                /*Si es sucursal 13 (Harlingen)  y producto es 2, Forzamos a que sea 161*/

                if($detail['idSucursal'] == '13'){

                    if($detail['idProducto'] == '161'){$detail['idProducto'] = 'xxx';}

                    if($detail['idProducto'] == '2'){$detail['idProducto'] = '161';}                

                }

                

                /*Si es sucursal 18 (Mcoll)  y producto es 2, Forzamos a que sea 161*/

                if($detail['idSucursal'] == '18'){

                    if($detail['idProducto'] == '161'){$detail['idProducto'] = 'xxx';}

                    if($detail['idProducto'] == '2'){$detail['idProducto'] = '161';}                

                }

                

                /*Si es sucursal 19  y producto es 238, Forzamos a que sea 161*/

                if($detail['idSucursal'] == '19'){

                    if($detail['idProducto'] == '161'){$detail['idProducto'] = 'xxx';}

                    if($detail['idProducto'] == '238'){$detail['idProducto'] = '161';}                

                }

                

                /*Si es sucursal 22  y producto es 2, Forzamos a que sea 161*/

                if($detail['idSucursal'] == '22'){

                    if($detail['idProducto'] == '161'){$detail['idProducto'] = 'xxx';}

                    if($detail['idProducto'] == '2'){$detail['idProducto'] = '161';}                

                }

                

                if(key_exists($detail['idProducto'], $arrayProducts)){                    

                    $arrayProducts[$detail['idProducto']]['qty_sold'] += round($detail['cantidad'],2);

                    $arrayProducts[$detail['idProducto']]['total_sold'] += round($detail['total'],2);

                }

            }

        

        //$arrayProducts = array();

        foreach($details as $detail){

            $arrayProducts[$detail['category_name']]['name'] = $detail['category_name'];

            $arrayProducts[$detail['category_name']]['qty_sold'] += round($detail['cantidad'],2);

            $arrayProducts[$detail['category_name']]['total_sold'] += round($detail['total'],2);

        }         

            

        ?>

        <table class="table table-condensed table-striped">

            <thead>

            <tr>

                <th class="text-center"><?php echo $_translator->_getTranslation('Categoria')?></th>

                <th class="text-center"><?php echo $_translator->_getTranslation('Cantidad vendida')?></th>

                <th class="text-center"><?php echo $_translator->_getTranslation('Total venta')?></th>

            </tr>

            </thead>

            <tbody>

                <?php

                foreach($arrayProducts as $product){?>

                    <tr>

                        <td class="text-center"><?php echo $product['name']; ?></td>

                        <td class="text-center"><?php echo number_format($product['qty_sold'],2); ?></td>

                        <td class="text-center">$<?php echo number_format($product['total_sold'],2); ?></td>

                    </tr><?php                    

                }

                ?>

            </tbody>

        </table>

    </div>

    <div class="clear"></div>

    <div class="card">

        <ul class="nav nav-tabs" role="tablist">

            <li role="presentation" class="active"><a href="#sales_list" aria-controls="home" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Lista de ventas') ?></a></li>

            <li role="presentation"><a href="#sales_details" aria-controls="profile" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Detalles de ventas') ?></a></li>                        

        </ul>

        <!-- Tab panes -->

        <div class="tab-content" style="margin-top: 20px">

            <div role="tabpanel" class="tab-pane active" id="sales_list">

                <table id="tblSales" class="table table-striped table-bordered table-hover table-condensed datatable_whit_filter_column _hideSearch">

                    <thead class="table-header">

                    <tr>

                        <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>

                        <th class="col-md-3 text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>

                        <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Venta #');?></th>           

                        <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Total');?></th>
                      
                        <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Metodo de Pago');?></th>

                    </tr>

                    </thead>

                    <tfoot>                        

                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>

                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>

                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Venta #');?></th>

                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Metodo de Pago');?></th>                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Total');?></th>


                    </tfoot>

                    <tbody>

                    <?php 

                    foreach($data as $row){?>

                        <tr >

                        <td class="text-center"><?php echo $row['store_name'];?></td>

                        <td class="text-center"><?php echo $row['formated_date'];?></td>

                        <td class="text-center"><?php echo $row['num_venta'];?></td>      

                        <td class="text-center">$<?php echo number_format($row['total_venta'],2);?></td> 

                        <td class="text-center"><?php echo $row['metodo_pago_'];?></td>      

                        </tr><?php  

                    }?>                    

                    </tbody>

                </table>

            </div>

            <div role="tabpanel" class="tab-pane" id="sales_details">

                <table id="tblSalesDetails" class="table table-striped table-bordered table-hover table-condensed datatable_whit_filter_column _hideSearch">

                    <thead class="table-header">

                    <tr>

                        <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>

                        <th class="col-md-3 text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>

                        <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Venta #');?></th>           

                        <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Producto');?></th> 

                        <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Categoria');?></th>

                        <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Cantidad');?></th> 

                        <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Precio');?></th> 

                        <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Total');?></th>

                        <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Metodo de Pago');?></th>

                    </tr>

                    </thead>

                    <tfoot>                        

                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>

                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>

                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Venta #');?></th>

                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Producto');?></th>

                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Categoria');?></th>

                        <th class="filter text-center"><?php echo $_translator->_getTranslation('CAntidad');?></th>

                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Precio');?></th>

                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Total');?></th>

                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Metodo de Pago');?></th>

                    </tfoot>

                    <tbody>

                    <?php 

                    foreach($details as $detail){?>

                        <tr >

                        <td class="text-center"><?php echo $detail['store_name'];?></td>

                        <td class="text-center"><?php echo $detail['formated_date'];?></td>

                        <td class="text-center"><?php echo $detail['num_venta'];?></td>      

                        <td class="text-center"><?php echo $detail['descripcion'];?></td>

                        <td class="text-center"><?php echo $detail['category_name'];?></td>

                        <td class="text-center"><?php echo $detail['cantidad'];?></td>

                        <td class="text-center">$<?php echo number_format($detail['precio'],2);?></td>

                        <td class="text-right">$<?php echo number_format($detail['total'],2);?></td> 

                        <td class="text-center"><?php echo $detail['metodo_pago_'];?></td>


                        </tr><?php  

                    }?>                    

                    </tbody>

                </table>

            </div>

        </div>

    </div>





</div>

</div>

</div>

<style>

    tfoot {

        display: table-header-group;

    }

</style>

<script type="text/javascript" language="javascript">

    $('#tblSales').DataTable({

        paginate:false,

        lengthChange: false,

        filter:true,

        bFilter:true,

        aaSorting:[],

        dom: 'Bfrtip',

        buttons: [{ extend: 'excel', text: 'Descargar en excel'}]

    });

    

    

    $('#tblSalesDetails').DataTable({

        paginate:false,

        lengthChange: false,

        filter:true,

        bFilter:true,

        aaSorting:[],

        dom: 'Bfrtip',

        buttons: [{ extend: 'excel', text: 'Descargar en excel'}]

    });

</script>