<div class="container-title"></div>
<div class="row">
    <div class="col-lg-12"><ol class="breadcrumb">
            <?php      
            $currentAction = '';
            $agregarNuevo = '';
            if($action =='edit'){
                $currentAction = "<li class='active'><i class='fa fa-pencil'></i> ".$_translator->_getTranslation('Editar')."</li>";
                $agregarNuevo = "<a href='Store-request.php' class='btn btn-primary'><i class='fa fa-plus'></i> ".$_translator->_getTranslation('Agregar pedido de sucursal')."</a>";
            }elseif($action =='' || $action =='insert'){
                $currentAction = "<li class='active'><i class='fa fa-plus'></i> ".$_translator->_getTranslation('Agregar')."</li>";
            } ?>
            <li><i class="fa fa-dashboard"></i>  <a href="<?php echo ROOT_HOST?>/Controller/Home.php"><?php echo $_translator->_getTranslation('Home')?></a></li>
            <li class="active"><i class="fa fa-list"></i> <?php echo $_translator->_getTranslation('Lista de pedidos de sucursal')?></li>
            <?php echo $currentAction?>
        </ol>
    </div>
</div>
<div id="flashmessenger"><?php $flashmessenger->showMessage();?></div>
<div class="box">
    <div class="box-header">       
        <h3><i class='fa-fw fa fa-list'></i><?php echo $_translator->_getTranslation('Lista de pedidos de sucursal');?></h3>
        <div class='box-icon'><i class='icon fa fa-tasks'></i></div>
    </div>
    <div class='box-content'>
        <div class='row'>
            <div class="col-lg-12">
               <div class="pull-right"><a href='Store-request.php' class='btn btn-default'><i class='fa fa-plus' style='color:black'></i> <?php echo $_translator->_getTranslation('Agregar pedido sucursal')?></a></div>               
               <div class="clear" ></div>
               <hr/>
                <div class='table-responsive'>
                <table id="tblStoreRquest" class="table table-striped table-hover table-condensed">
                    <thead>
                    <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('No.');?></th>
                    <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>                     
                    <th class="col-lg-3 text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
                    <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Status');?></th>
                    <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Accion');?></th>
                    </thead>
                    <tfoot>
                    <th class="filter"><?php echo $_translator->_getTranslation('No.');?></th>
                    <th class="filter"><?php echo $_translator->_getTranslation('Fecha');?></th>                     
                    <th class="filter"><?php echo $_translator->_getTranslation('Sucursal');?></th>
                    <th class="filter"><?php echo $_translator->_getTranslation('Status');?></th>
                    <th></th>
                    </tfoot>
                    <tbody>
                    <?php 
                        if($_listStoreRequests){
                            foreach($_listStoreRequests as $requisicion){?>
                                <tr>
                                    <td class="text-center"><?php echo $requisicion['id']?></td> 
                                    <td class="text-center"><?php echo $requisicion['formatedDate']?></td>  
                                    <td class="text-center"><?php echo $requisicion['storeName']?></td>
                                    <td class="text-center"><?php echo $requisicion['statusName']?></td>
                                    <td class="text-center">
                                        <a href="Store-request.php?action=edit&id=<?php echo $requisicion['id']?>" class="btn btn-sm btn-default"><i class="fa fa-pencil fa-"></i></a>
                                        <a href="#"  class="btn btn-sm btn-default" onclick="javascript: void window.open('Store-request.php?action=import&format=pdf&flag=store_request&id=<?php echo $requisicion['id']?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-file-pdf-o fa-"></i></a>                                                                              
                                        <?php 
                                        if($requisicion['status'] == '1' && $login->getRole() !== '14'){?>
                                            <a href="#" class="btn btn-sm btn-default" onclick="generateShipment('<?php echo $requisicion['id'] ?>')"><i class="fa fa-truck"></i> </a>
                                <?php   }elseif($requisicion['status'] == '2'){?>
                                            <a href="#" class="btn btn-sm btn-default disabled"><i class="fa fa-truck"></i> </a>
                                <?php   } ?>                                               
                                            <a href="#" class="btn btn-sm btn-danger" onclick="return deleteRegistry('Store-request.php?action=delete&id=<?php echo $requisicion['id']?>')"><i class="fa fa-trash"></i></a>                                        
                                    </td>
                                </tr><?php
                            }                   
                        }?>
                    </tbody>
                </table>
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
    $('#tblStoreRquest').DataTable({paginate:false,filter:true,aaSorting:[]});
    $('#tblStoreRquest')
            .removeClass( 'display' )
            .addClass('table table-striped table-bordered');
        
    $('#tblStoreRquest tfoot th.filter').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Buscar" style="width:100%" />' );
    } );  
 
    var table = $('#tblStoreRquest').DataTable();
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