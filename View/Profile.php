<section class="content-header">
    <h1><i class='fa-fw fa fa-user'></i> <?php echo $_translator->_getTranslation('Usuarios');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Usuarios');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
      <span class="btn btn-default pull-right _addUser"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar usuario')?></span> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
     <div class="clear"></div>
     <div class="table-responsive">
        <table id="tblUser" class="table table-condensed table-striped table-hover table-bordered">
            <thead>
                <th class="text-center"><?php echo $_translator->_getTranslation('Usuario');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Nombre');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Rol');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Telefono');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Status');?></th>   
                <th class="text-center"><?php echo $_translator->_getTranslation('Accion');?></th>    
            </thead>
        <tbody class="users">
        <?php
        if($_listUsers){
        foreach($_listUsers as $user){?>
            <tr>
            <td class="_edit text-center" data-id="<?php echo $user['id']?>"> <?php echo $user['user']?></td>
            <td class="_edit text-center" data-id="<?php echo $user['id']?>"> <?php echo $user['complete_name']?></td>
            <td class="_edit text-center" data-id="<?php echo $user['id']?>"> <?php echo $user['store_name']?></td>
            <td class="_edit text-center" data-id="<?php echo $user['id']?>"> <?php echo $user['role_name']?></td>
            <td class="_edit text-center" data-id="<?php echo $user['id']?>"> <?php echo $user['phone']?></td>
            <td class="_edit text-center" data-id="<?php echo $user['id']?>"> <?php echo $user['status_name']?></td>
            <td><span class="pull-right btn btn-default _delete" data-id="<?php echo $user['id']?>"><i class="fa fa-trash"></i></span></td>
            </tr>
        <?php }
        }?>
        </tbody>
        </table>
     </div>
  </div><!-- /.box-body -->
</div><!-- /.box -->
</section>
<?php include ROOT."/View/Modal/addUser.php";?>
<?php if(isset($_noValid)){?> <script>$('#modalAddUser').modal('show');</script> <?php }?>
<style>
    tfoot {
        display: table-header-group;
    }
</style>
<script>
    $('#role,#store_id,#status').select2();
    $('._addUser').on('click',function(){
        clearForm('user');
        $('form[name=user] #action').val('insert');
        $('form[name=user] #id').val('');
        $('.flashmessenger').html('');
         _getTranslation('Agregar usuario',function(msj){ $('#title_modal_user').html(msj);});
        $('#modalAddUser').modal('show');
    });
    
    $('._closeModalUser').on('click',function(){
        clearForm('user');
        $('.flashmessenger').html('');
        $('#modalAddUser').modal('hide');
    });
    
    $('tbody.users td._edit').on('click',function(e){  
        if (!$(e.target).closest('._delete').length) {
            clearForm('user');
            $('.flashmessenger').html('');
             _getTranslation('Editar usuario',function(msj){ $('#title_modal_user').html(msj);});
            var id = $(this).data('id'); 
            setDataToEditUser(id);
        }       
    }); 
    
    $('tbody.users td ._delete').on('click',function(){
        var id = $(this).data('id');
        deleteUser(id);
    });
    
    $('#tblUsers').DataTable({
        paginate:true,
        iDisplayLength :50,
        filter:true,
        bFilter:true,
        aaSorting:[]
    });
    
    $('#tblUsers').removeClass( 'display' ).addClass('table table-striped table-bordered');
    $('#tblUsers tfoot th.filter').each( function () {
        $(this).html( '<input type="text" placeholder="Search" style="width:100%" />' );
    } );
 
    // Apply the search
    var table = $('#tblUsers').DataTable(); 
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