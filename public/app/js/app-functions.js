$.fn.modal.Constructor.prototype.enforceFocus = function () { };
$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);

$(function () {
    $('._maskPhone').keydown(function (e) {
        var key = e.which || e.charCode || e.keyCode || 0;
        $phone = $(this);

        // Don't let them remove the starting '('
        if ($phone.val().length === 1 && (key === 8 || key === 46)) {
            $phone.val('(');
            return false;
        }
        // Reset if they highlight and type over first char.
        else if ($phone.val().charAt(0) !== '(') {
            $phone.val('(' + $phone.val());
        }

        // Auto-format- do not expose the mask as the user begins to type
        if (key !== 8 && key !== 9) {
            if ($phone.val().length === 4) {
                $phone.val($phone.val() + ')');
            }
            if ($phone.val().length === 5) {
                $phone.val($phone.val() + ' ');
            }
            if ($phone.val().length === 9) {
                $phone.val($phone.val() + '-');
            }
        }

        // Allow numeric (and tab, backspace, delete) keys only
        return (key == 8 ||
            key == 9 ||
            key == 46 ||
            (key >= 48 && key <= 57) ||
            (key >= 96 && key <= 105));
    })

        .bind('focus click', function () {
            $phone = $(this);

            if ($phone.val().length === 0) {
                $phone.val('(');
            }
            else {
                var val = $phone.val();
                $phone.val('').val(val); // Ensure cursor remains at the end
            }
        })

        .blur(function () {
            $phone = $(this);

            if ($phone.val() === '(') {
                $phone.val('');
            }
        });

    $("._deleteFile").on("click", function () { deleteFile(this); });
    $('._emailing').on('click', function () { prepareEmailing(this); });
    $('._closeModalEmailing').on('click', function () {
        clearForm('emailing');
        $('.flashmessenger').html('');
        $('#modalEmailing').modal('hide');
    });

    $('#send_emailing').on('click', function () { emailing(); });

});

function deleteFile(element) {
    var fileDelete = $(element).data('filedelete');
    var uuid = $(element).data('uuid');
    $.confirm({
        theme: 'material',
        columnClass: 'col-md-6 col-md-offset-3',
        icon: 'fa fa-trash',
        title: 'Eliminar documento',
        content: 'Desea eliminar este documento ?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-default col-md-4 pull-right',
                action: function () {
                    $(this).remove();
                }
            },
            confirm: {
                text: 'Si ',
                btnClass: 'btn-primary col-md-4 pull-right',
                action: function () {
                    $.post('/Controller/Ajax.php', {
                        action: 'ajax',
                        request: 'deleteFile',
                        fileDelete: fileDelete
                    }, function (data) {
                        if (data.response) {
                            $(".flashmessenger").html(data.msj);
                            $("li." + uuid).remove();

                        } else {
                            $(".flashmessenger").html(data.msj);
                        }
                    }, 'json');
                }
            }
        }
    });
}

function _rawNumber(number) {
    number = $.trim(number);
    if (number == '') return 0;

    return number.replace(/,/g, "");
}

$(document).on('focus', '.select2', function (e) {
    if (e.originalEvent && $(this).find(".select2-selection--single").length > 0) {
        $(this).siblings('select').select2('open');
    }
});

$(function () { $('.modal-content').draggable(); });

$(function () {
    $('.datatable_whit_filter_column').each(function () {
        if (!$.fn.DataTable.isDataTable('#' + this.id)) {
            setDataTable(this.id);
        } else {
            applyFiltersOnDataTable(this.id);
        }
    });

    /*Ajustar columnas cuando se activa Tab
     * Por ejemplo si hay dos tabs, las columnas de la tabla en el tab inicialmente activo se muestan bien,
     * pero cuando se hace click en otro tab, la tabla que esta en ese tab no se muestra con los width correctos en las columnas; no se muestran bien porque como estan hide, no se aplica el width */
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $.fn.dataTable
            .tables({ visible: true, api: true })
            .columns.adjust();
    });
});

function setDataTable(tableId, paginate = false, filter = true, bInfo = false) {
    $('#' + tableId).dataTable({ paginate: paginate, filter: filter, aaSorting: [], bInfo: bInfo });

    if ($('#' + tableId).hasClass('_hideSearch')) {
        $('#' + tableId + '_filter').hide();
    }

    applyFiltersOnDataTable(tableId);
}

function applyFiltersOnDataTable(tableId) {
    $('#' + tableId).removeClass('display').addClass('table table-striped table-bordered');
    $('#' + tableId + ' tfoot th.filter').each(function () {
        var input_name = $(this).data('filtername');
        $(this).html('<input type="text" name="filter_' + input_name + '" class="column_filter" placeholder="Buscar" style="width:100%" />');
    });

    var table = $('#' + tableId).DataTable();
    // Apply the search
    table.columns().every(function () {
        var that = this;
        $('input', this.footer()).on('keyup change', function () {
            if (that.search() !== this.value) {
                that
                    .search('\\b' + this.value.replace("/;/g", "|"), true, false)
                    .draw();
            }
        });
    });
}

function number_format(number, decimals, dec_point, thousands_sep) {
    number = number * 1;//makes sure `number` is numeric value
    var str = number.toFixed(decimals ? decimals : 0).toString().split('.');
    var parts = [];
    for (var i = str[0].length; i > 0; i -= 3) {
        parts.unshift(str[0].substring(Math.max(0, i - 3), i));
    }
    str[0] = parts.join(thousands_sep ? thousands_sep : ',');
    return str.join(dec_point ? dec_point : '.');
}

function fadeOutAlert(paramTime) {
    var time = 3500;
    if (paramTime) {
        time = paramTime;
    }
    window.setTimeout(function () {
        $('.alert').slideUp('slow', function () {
            $('.alert').remove();
        });
    }, time);
}

function destroyDataTable(table_id) {
    if ($.fn.dataTable.isDataTable('#' + table_id)) {
        var table = $('#' + table_id).DataTable();
        table.destroy();
    }
}

function addMsgFlashmessenger(tipo, msg, messengerId) {
    alert(tipo + ' ' + msg + ' ' + messengerId);
    $('#msg').remove();
    $('#' + messengerId).append("<div id='msg' class='alert alert-" + tipo + "'></div>");
    var btnClose = "<button type='button' class='close' data-dismiss='alert'>&times;</button>";
    $('#msg').html(btnClose + msg);
    //fadeOutAlert();
}
function submit(formName) {
    if (formName == "file") {
        if ($("#name").val() == "" || $("#id_category_file").val() == "" || $("#expiration_date").val() == "" || $("#attachement_file").val() == "") {

            $("file").submit(function (e) {
                e.preventDefault();
            });
            alert("Todos los campos son requeridos");

        }
    } else {
        $('form[name=' + formName + ']').submit();

    }
}

function clearForm(formName) {
    $('form[name=' + formName + ']').trigger('reset');
    $('form[name=' + formName + ']').find('.has-error').removeClass("has-error");
    $('form[name=' + formName + ']').find('.has-success').removeClass("has-success");
    $('form[name=' + formName + ']').find('.form-control-feedback').remove();
    $('select').trigger('change');
}

function _getTranslation(msj, callback) {
    $.post('/Controller/Ajax.php', {
        action: 'ajax',
        request: 'getTranslation',
        msj: msj
    }, function (data) {
        if (callback) { callback(data.translation); }
    }, 'json');
}

function _alert(msg, callbackOk) {
    $.alert({
        title: "<i class='fa fa-info-circle'></i> Mensaje",
        content: msg,
        columnClass: 'col-md-6 col-md-offset-3',
        buttons: {
            ok: {
                text: "OK",
                btnClass: 'btn-default',
                keys: ['enter'],
                action: function () {
                    callbackOk();
                }
            }
        }
    });
}

function confirmDelete(msg, link) {
    $.confirm({
        theme: 'material',
        columnClass: 'col-md-6 col-md-offset-3',
        icon: 'fa fa-trash',
        title: 'Eliminar registro',
        content: msg,
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-default col-md-4 pull-right',
                action: function () {
                    $(this).remove();
                }
            },
            confirm: {
                text: 'Si ',
                btnClass: 'btn-primary col-md-4 pull-right',
                action: function () {
                    document.location = link;
                }
            }
        }
    });
    return false;
}

function deleteRegistry(msg, link) {
    $.confirm({
        theme: 'material',
        columnClass: 'col-md-6 col-md-offset-3',
        icon: 'fa fa-trash',
        title: _getTranslation('Eliminar registro'),
        content: msg,
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-default col-md-4 pull-right',
                action: function () {
                    $(this).remove();
                }
            },
            confirm: {
                text: 'Si ',
                btnClass: 'btn-primary col-md-4 pull-right',
                action: function () {
                    document.location = link;
                }
            }
        }
    });
    return null;
}

function confirmAction(operation, callback) {
    $.confirm({
        title: 'Confirmar operacion !',
        icon: 'fa fa-key',
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<label>Ingresa clave para confirmar operacion.</label>' +
            '<input id="master_key" type="password" placeholder="Clave" class="name form-control" required />' +
            '</div>' +
            '</form>',
        buttons: {
            formSubmit: {
                text: 'Submit',
                btnClass: 'btn-blue _confirmAction',
                action: function () {
                    $.post('/Controller/Ajax.php', {
                        action: 'ajax',
                        request: 'confirmAction',
                        operation: operation,
                        masterKey: $('#master_key').val()
                    }, function (data) {
                        if (data.response) {
                            callback();
                        } else {
                            $('.flashmessenger').html(data.msg);
                            return false;
                        }
                    }, 'json');
                }
            },
            cancel: function () {
                //close
            }
        },
        onContentReady: function () {
            $('#master_key').focus();
            $('#master_key').on('keydown', function (e) {
                var keycode = e.keyCode || e.which;
                if (keycode === 13) {
                    e.preventDefault();
                    $('._confirmAction').trigger('click');
                }
            });
        }
    });
}

/* STORES */

function setDataToEditStore(id) {
    $.post('/Controller/Store.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.storeData) {
                $('form[name=store] #' + name).val(data.storeData[name]);
            }

            $('#default_location').html(data.locations);
            $('select').trigger('change');
            $('#modalAddStore').modal('show');
        }
    }, 'json');
}

function deleteStore(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar esta sucursal ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/Store.php', {
                                    action: 'ajax',
                                    request: 'deleteStore',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'Store.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}

/* END STORES*/

/* USERS */
function setDataToEditUser(id) {
    $.post('/Controller/User.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.userData) {
                $('form[name=user] #' + name).val(data.userData[name]);
            }

            //if(data.userData['store_id'] !== null){$('#store_id').val(data.userData['store_id'].split(','));}       
            $('select').trigger('change');
            // if($('#role').val() === '4'){
            //     $('#area_bakery_production_id').prop('disabled',false);        
            // }else{
            //     $('#area_bakery_production_id').prop('disabled',true);      
            // }
            $('#divPhoto').html(data.photo);
            $('#modalAddUser').modal('show');
        }
    }, 'json');
}

function deleteUser(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar este usuario ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/User.php', {
                                    action: 'ajax',
                                    request: 'deleteUser',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'User.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}

function deletePhoto(photo) {
    $.confirm({
        theme: 'material',
        columnClass: 'col-md-6 col-md-offset-3',
        icon: 'fa fa-trash',
        title: 'Eliminar foto',
        content: 'Desea eliminar esta foto ?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-default col-md-4 pull-right',
                action: function () {
                    $(this).remove();
                }
            },
            confirm: {
                text: 'Si ',
                btnClass: 'btn-primary col-md-4 pull-right',
                action: function () {
                    var id = $(photo).data('id');
                    $.post('/Controller/User.php', {
                        action: 'ajax',
                        request: 'deletePhoto',
                        user_id: id
                    }, function (data) {
                        if (data.response) {
                            $('#divPhoto').html('');
                        }
                    }, 'json');
                }
            }
        }
    });
    return null;
}

/* END USERS */

/* VENDORS */

function setDataToEditVendor(id) {
    $.post('/Controller/Vendor.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.vendorData) {
                $('form[name=vendor] #' + name).val(data.vendorData[name]);
            }

            $('select').trigger('change');
            $('#modalAddVendor').modal('show');
        }
    }, 'json');
}

function deleteVendor(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar este proveedor ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/Vendor.php', {
                                    action: 'ajax',
                                    request: 'deleteVendor',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'Vendor.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}

/* END VENDORS */

/* CUSTOMERS */

function setDataToEditCustomer(id) {
    $.post('/Controller/Customer.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.customerData) {
                $('form[name=customer] #' + name).val(data.customerData[name]);
            }

            $('select').trigger('change');
            $('#modalAddCustomer').modal('show');
        }
    }, 'json');
}

function deleteCustomer(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar este cliente ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/Customer.php', {
                                    action: 'ajax',
                                    request: 'deleteCustomer',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'Customer.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}

/* END CUSTOMERS */

/* BRANS */

function setDataToEditBrand(id) {
    $.post('/Controller/Brand.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.brandData) {
                $('form[name=brand] #' + name).val(data.brandData[name]);
            }

            $('select').trigger('change');
            $('#modalAddBrand').modal('show');
        }
    }, 'json');
}

function deleteBrand(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar esta marca ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/Brand.php', {
                                    action: 'ajax',
                                    request: 'deleteBrand',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'Brand.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}

/* END BRANS */

/* FLOUR */

function setDataToEditFlour(id) {
    $.post('/Controller/Flour.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.flourData) {
                $('form[name=flour] #' + name).val(data.flourData[name]);
            }

            $('select').trigger('change');
            $('#modalAddFlour').modal('show');
        }
    }, 'json');
}

function deleteFlour(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar esta harina ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/Flour.php', {
                                    action: 'ajax',
                                    request: 'deleteFlour',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'Flour.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}

/* END FLOUR */

/* CATEGORY */
function setDataToEditCategory(id) {
    $.post('/Controller/Category.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.categoryData) {
                $('form[name=category] #' + name).val(data.categoryData[name]);
            }

            $('select').trigger('change');
            $('#modalAddCategory').modal('show');
        }
    }, 'json');
}

function deleteCategory(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar esta categoria ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/Category.php', {
                                    action: 'ajax',
                                    request: 'deleteCategory',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'Category.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}

/* END CATEGORY */

/* PRESENTATION */
function setDataToEditPresentation(id) {
    $.post('/Controller/Presentation.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.presentationData) {
                $('form[name=presentation] #' + name).val(data.presentationData[name]);
            }

            $('select').trigger('change');
            $('#modalAddPresentation').modal('show');
        }
    }, 'json');
}

function deletePresentation(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar esta presentacion ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/Presentation.php', {
                                    action: 'ajax',
                                    request: 'deletePresentation',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'Presentation.php';

                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}

/* END PRESENTATION */

/* UM */
function setDataToEditUM(id) {
    $.post('/Controller/UM.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.umData) {
                $('form[name=um] #' + name).val(data.umData[name]);
            }

            $('select').trigger('change');
            $('#modalAddUM').modal('show');
        }
    }, 'json');
}

function deleteUM(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar esta unidad de medida ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/UM.php', {
                                    action: 'ajax',
                                    request: 'deleteUM',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'UM.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}

/* END UM */

/* SIZE */
function setDataToEditSize(id) {
    $.post('/Controller/Size.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.sizeData) {
                $('form[name=size] #' + name).val(data.sizeData[name]);
            }

            $('select').trigger('change');
            $('#modalAddSize').modal('show');
        }
    }, 'json');
}

function deleteSize(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar este tamaño ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/Size.php', {
                                    action: 'ajax',
                                    request: 'deleteSize',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'Size.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}

/* END SIZE */

/* SHAPE */
function setDataToEditShape(id) {
    $.post('/Controller/Shape.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.shapeData) {
                $('form[name=shape] #' + name).val(data.shapeData[name]);
            }

            $('select').trigger('change');
            $('#modalAddShape').modal('show');
        }
    }, 'json');
}

function deleteShape(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar esta forma ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/Shape.php', {
                                    action: 'ajax',
                                    request: 'deleteShape',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'Shape.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}

/* END SHAPES */

/* SUPPLIES */
function setDataToEditSupplie(id) {
    $.post('/Controller/Supplie.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.supplieData) {
                $('form[name=supplie] #' + name).val(data.supplieData[name]);
            }

            $('select').trigger('change');
            $('#modalAddSupplie').modal('show');
        }
    }, 'json');
}

function deleteSupplie(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar este insumo ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/Supplie.php', {
                                    action: 'ajax',
                                    request: 'deleteSupplie',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'Supplie.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}

/* END SUPPLIES */

/* PRODUCTS */
function setDataToEditProduct(id) {
    $.post('/Controller/Product.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.productData) {
                $('form[name=product] #' + name).val(data.productData[name]);
            }

            if (data.productData['location'] !== null) { $('#location').val(data.productData['location'].split(',')); }
            $('select').trigger('change');
            $('#category').trigger("select2:select");
            $('#modalAddProduct').modal('show');
        }
    }, 'json');
}

function deleteProduct(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar este producto ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/Product.php', {
                                    action: 'ajax',
                                    request: 'deleteProduct',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'Product.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}

/* END PRODUCTS */

/* SERVICES */
function setDataToEditService(id) {
    $.post('/Controller/Service.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.serviceData) {
                $('form[name=service] #' + name).val(data.serviceData[name]);
            }

            $('select').trigger('change');
            $('#modalAddService').modal('show');
        }
    }, 'json');
}

function deleteService(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar este servicio ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/Service.php', {
                                    action: 'ajax',
                                    request: 'deleteService',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'Service.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}

/* END SERVICES */

/* SALES RECORD */
function setSalesRecordformToInsert() {
    clearForm('salesrecord');
    $('form[name=salesrecord] #action').val('insert');
    $('form[name=salesrecord] #id').val('');
    $('.flashmessenger').html('');
    _getTranslation('Agregar registro de venta', function (msj) { $('#title_modal_salesrecord').html(msj); });

    $.post('/Controller/SalesRecord.php', {
        action: 'ajax',
        request: 'setFormToInsert'
    }, function (data) {
        if (data.response) {
            $('form[name=salesrecord] #token_form').val(data.tokenForm);
            $('form[name=salesrecord] :input').prop('disabled', false);
            $('#btn_allow_edit').hide();
            $('#salesRecordDetails :input').prop('disabled', false);
            $('#salesRecordDetails tr td a').removeClass('disabled');
            $("#salesRecordDetails").find('tbody').empty().append(data.expensesDetails);
            $('#modalAddSalesRecord').modal('show');
        }
    }, 'json');
}

function setDataToEditSalesRecord(id) {
    $.post('/Controller/SalesRecord.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.salesRecordData) {
                $('form[name=salesrecord] #' + name).val(data.salesRecordData[name]);
            }

            $('select').trigger('change');
            $('status').select2();
            $("#salesRecordDetails").find('tbody').empty().append(data.expensesDetails);

            $('form[name=salesrecord] :input').prop('disabled', true);
            $('form[name=salesrecord] #btn_allow_edit').prop('disabled', false);
            $('#btn_allow_edit').show();
            $('#salesRecordDetails :input').prop('disabled', true);
            $('#salesRecordDetails tr td a').addClass('disabled');

            sumTotalSales();

            $('#modalAddSalesRecord').modal('show');
        }
    }, 'json');
}

function deleteSalesRecord(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar este registro de ventas ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/SalesRecord.php', {
                                    action: 'ajax',
                                    request: 'deleteSalesRecord',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'SalesRecord.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}

function updateSalesRecordExpense(callback) {
    var sales_recotd_expense = $('._salesRecordExpense').serializeArray();
    $.post('/Controller/SalesRecord.php', {
        action: 'ajax',
        request: 'updateSalesRecordExpenseAmount',
        token_form: $('#token_form').val(),
        sales_recotd_expense: sales_recotd_expense
    }, function (data) {
        if (data.response) {
            callback();
        } else {
            $('#flashmessenger').html(data.message);
        }
    }, 'json');
}

function allowEditSalesRecord() {
    confirmAction('edit_salesRecord', function () {
        var idSalesRecord = $('form[name=salesrecord] input[name=id]').val();
        $.post('/Controller/SalesRecord.php', {
            action: 'ajax',
            request: 'allowEditSalesRecord',
            idSalesRecord: idSalesRecord
        }, function (data) {
            if (data.response) {
                $('form[name=salesrecord] :input').prop('disabled', false);
                $('#salesRecordDetails :input').prop('disabled', false);
                $('#salesRecordDetails tr td a').removeClass('disabled');
                $('#allow_edit').val('1',);
                $('#btn_allow_edit').hide();
                $('.flashmessenger').html('');
            } else {
                $('.flashmessenger').html(data.message);
                fadeOutAlert();
            }
        }, 'json');
    });
}

function sumTotalSales() {
    var totalSales = 0;
    $('._sumSales').each(function () {
        if ($(this).val() != '') {
            totalSales = parseFloat(totalSales) + parseFloat($(this).val());
        }
    });

    $('._minusSales').each(function () {
        if ($(this).val() != '') {
            totalSales = parseFloat(totalSales) - parseFloat($(this).val());
        }
    });

    $('._totalSales').html(number_format(totalSales, 2, '.', ','));
}

/* END SALES RECORD */

/* LOCATION */
function setDataToEditLocation(id) {
    $.post('/Controller/Location.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.locationData) {
                $('form[name=location] #' + name).val(data.locationData[name]);
            }

            $('#modalAddLocation').modal('show');
        }
    }, 'json');
}

function deleteLocation(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar esta locacion ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/Location.php', {
                                    action: 'ajax',
                                    request: 'deleteLocation',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'Location.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}
/* END LOCATION*/

/* PARTS OF THE CAKE */
function setDataToEditSlice(id) {
    $.post('/Controller/Slice.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.sliceData) {
                $('form[name=slice] #' + name).val(data.sliceData[name]);
            }

            $('select').trigger('change');
            $('#modalAddSlice').modal('show');
        }
    }, 'json');
}

function deleteSlice(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar esta parte del pastel ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/Slice.php', {
                                    action: 'ajax',
                                    request: 'deleteSize',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'Slice.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}
/* END  PARTS OF THE CAKE*/

/* PURCHASE */

function clearModalAddProduct() {
    $('#idDetailTemp').val('');
    $('#id_product').val('');
    $('#product').val('');
    $('#added').val('1');
    $('#quantity').val('');
    $('#cost').val('');
    $('#discount').val('');
    $('#expiration_date').val('');
    $('#location').val(1).trigger('change');
    $("#taxes").val('').trigger('change');
    $("#taxes_included").val('').trigger('change');
    $('.flashmessenger_modal_add_product').html('');
}

function getDefaultDataProduct() {
    var product = $('#id_product').val();
    if (product === '0') { return true; }
    $.post('/Controller/Purchase.php', {
        action: 'ajax',
        request: 'getDefaultDataProduct',
        product: product
    }, function (data) {
        if (data.response) {
            $('#cost').val(data.cost).trigger('change');
            $('#taxes').val(data.taxes).trigger('change');
            $('#taxes_included').val(data.taxes_included).trigger('change');
            $('#quantity').focus();
        } else {
            $('.flashmessenger_modal_add_purchase_product').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function setPurchaseDetails() {
    var options = $("form[name=addProduct]").serializeArray();
    options.push({ name: 'discount_general_type', value: $('form[name=purchase] #discount_general_type').val() });
    options.push({ name: 'discount_general', value: $('form[name=purchase] #discount_general').val() });
    options.push({ name: 'token_form', value: $('form[name=purchase] #token_form').val() });

    $.post('/Controller/Purchase.php', {
        action: 'ajax',
        request: 'setPurchaseDetails',
        options: options
    }, function (data) {
        if (data.response) {
            $("#purchase-table").find('tbody').empty().append(data.purchaseDetails);
            $("#totalItems").html(data.totalItems);
            $("#total_importe").html(data.total_importe);
            $("#total_descuentos").html(data.total_descuentos);
            $("#total_subtotal").html(data.total_subtotal);
            $("#total_impuestos").parent().replaceWith(data.total_impuestos);
            $("#total_label").html((number_format(data.total, 2, '.', ',')));
            $("#total").val(data.total);

            $("#product").focus();
            clearModalAddProduct();
        } else {
            $('.flashmessenger_modal_add_purchase_product').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function setDetailPurchaseToEdit(data) {
    for (var name in data) {
        $('form[name=addProduct] #' + name).val(data[name]);
    }

    $('select').trigger('change');
    $('.flashmessenger_modal_add_product').html('');
    _getTranslation('Editar producto', function (msj) { $('#title_modal_purchaseProduct').html(msj); });
    $('#modalAddPurchaseProduct').modal('show');
}

function deleteDetalles(id) {
    $.confirm({
        theme: 'material',
        columnClass: 'col-md-6 col-md-offset-3',
        icon: 'fa fa-trash',
        title: 'Eliminar producto',
        content: 'Desea eliminar el producto ?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-default col-md-4 pull-right',
                action: function () {
                    $(this).remove();
                }
            },
            confirm: {
                text: 'Si ',
                btnClass: 'btn-primary col-md-4 pull-right',
                action: function () {
                    $.post('/Controller/Purchase.php', {
                        action: 'ajax',
                        request: 'deleteDetalles',
                        id: id
                    }, function (data) {
                        if (data.response) {
                            $("#purchase-table").find('tbody').empty().append(data.purchaseDetails);
                            $("#totalItems").html(data.totalItems);
                            $("#total_importe").html(data.total_importe);
                            $("#total_descuentos").html(data.total_descuentos);
                            $("#total_subtotal").html(data.total_subtotal);
                            $("#total_impuestos").parent().replaceWith(data.total_impuestos);
                            $("#total_label").html((number_format(data.total, 2, '.', ',')));
                            $("#total").val(data.total);

                            clearModalAddProduct();
                            $("#product").focus();
                        } else {
                            $(".flashmessenger").html(data.mensaje);
                            $("#id_product").focus();
                        }
                    }, 'json');
                }
            }
        }
    });
}

function getVendorMethodPayment() {
    var vendor = $('#vendor').val();
    if (vendor === '' || vendor === '0') {
        $("#method_payment").val('').trigger('change');
        $("#credit_days").val('');
        $("#due_date").val('');
        return true;
    }

    var date = $('#date').val();
    $.post('/Controller/Purchase.php', {
        action: 'ajax',
        request: 'getVendorMethodPayment',
        vendor: vendor,
        date: date
    }, function (data) {
        if (data.response) {
            if (data.method_payment === '2') { $("#method_payment").prop('readOnly', false); }
            else if (data.method_payment === '1') { $("#method_payment").prop('readOnly', true); }

            $("#method_payment").val(data.method_payment).trigger('change');
            $("#credit_days").val(data.credit_days);
            $("#due_date").val(data.due_date);
        } else {
            $('.flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function setDueDate() {
    var date = $('#date').val();

    if (date === '') { return true; }

    var method_payment = $('#method_payment').val();
    var credit_days = $('#credit_days').val();

    if (method_payment == '1') { $('#due_date').val(date); }
    if (method_payment == '2' && (credit_days === '' || credit_days === '0')) { $('#due_date').val(date); }

    if (method_payment == '2' && credit_days !== '' && credit_days !== '0') {

        $.post('/Controller/Purchase.php', {
            action: 'ajax',
            request: 'setDueDate',
            date: date,
            credit_days: credit_days
        }, function (data) {
            if (data.response) {
                $("#due_date").val(data.due_date);
            } else {
                $('.flashmessenger').html(data.message);
                fadeOutAlert();
            }
        }, 'json');
    }
}

function calculateGralDiscount() {
    var discount = $('#discount_general').val();

    $.post('/Controller/PurchaseGoodAndService.php', {
        action: 'ajax',
        request: 'calculateGralDiscount',
        discount: discount
    }, function (data) {
        if (data.response) {
            $("#purchase-table").find('tbody').empty().append(data.purchaseDetails);
            $("#totalItems").html(data.totalItems);
            $("#total_importe").html(data.total_importe);
            $("#total_descuentos").html(data.total_descuentos);
            $("#total_subtotal").html(data.total_subtotal);
            $("#total_impuestos").parent().replaceWith(data.total_impuestos);
            $("#total_label").html((number_format(data.total, 2, '.', ',')));
            $("#total").val(data.total);

            clearModalAddProduct();
        }
    }, 'json');
}

function onEnterPurchase(ev, element) {
    if (ev === 13) {
        switch (element.name) {
            case 'product':
                $('#product').autocomplete('close');
                var code = $("#product").val();
                //IdProduct seria el texto ingresado en el campo producto, que seria el codigo a buscar en tabla productos

                $.post('/Controller/Purchase.php', {
                    action: 'ajax',
                    request: 'getProductByCode',
                    code: code
                }, function (data) {
                    if (data.response) {
                        $('#id_product').val(data.id_product);
                        getDefaultDataProduct();
                    }
                }, 'json');

                break;
        }
    }
}

function approvePurchase() {
    var status_approval = 0;
    if ($('#status_approval').prop('checked') === true) { status_approval = 1; }
    $.post('/Controller/Purchase.php', {
        action: 'ajax',
        request: 'approvePurchase',
        purchase_id: $("form[name=purchase] #id").val(),
        status_approval: status_approval
    }, function (data) {
        if (data.response) {
            document.location.reload();
        }
    }, 'json');
}

function approvePurchaseList(purchase) {
    var status_approval = 0;
    if ($(purchase).prop('checked') === true) { status_approval = 1; }

    $.post('/Controller/Purchase.php', {
        action: 'ajax',
        request: 'approvePurchase',
        purchase_id: $(purchase).data('purchaseid'),
        status_approval: status_approval
    }, function (data) {
        if (data.response) {
            document.location.reload();
        }
    }, 'json');
}

/* END PURCHASE */


/* RECEIVING*/
function getPurchaseDetailsToReceive(_onSelect) {

    $.post('/Controller/Receiving.php', {
        action: 'ajax',
        request: 'getPurchaseDetailsToReceive',
        document_reference: $('#document_reference').val(),
        token_form: $('#token_form').val(),
        _onSelect: _onSelect
    }, function (data) {
        if (data.response) {
            $('#store_id_of_document').val(data.storeIdOfDocument);

            for (var name in data.purchaseData) {
                $('#purchase_' + name).html(data.purchaseData[name]);
            }

            if (_onSelect) {
                $("#receiving-table").find('tbody').empty().append(data.purchaseDetails);
            }
        }
    }, 'json');
}

function setReceivingDetails() {
    var options = $("form[name=addProduct]").serializeArray();
    options.push({ name: 'added', value: $('form[name=addProduct] #added').val() });
    options.push({ name: 'token_form', value: $('form[name=receiving] #token_form').val() });

    $.post('/Controller/Receiving.php', {
        action: 'ajax',
        request: 'setReceivingDetails',
        options: options
    }, function (data) {
        if (data.response) {
            $("#receiving-table").find('tbody').empty().append(data.receivingDetails);
            $("#totalPedido").html(data.totalPedido);
            $("#totalItems").html(data.totalItems);
            $("#product").focus();
            clearModalAddProduct();
        } else {
            $('.flashmessenger_modal_add_receiving_product').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function setDetailReceivingToEdit(data) {
    for (var name in data) {
        $('form[name=addProduct] #' + name).val(data[name]);
    }

    $('select').trigger('change');
    $('.flashmessenger_modal_add_product').html('');
    _getTranslation('Editar recibo', function (msj) { $('#title_modal_receivingProduct').html(msj); });

    if ($('#added').val() == '0') {
        $("form[name=addProduct] :input").attr("disabled", true);
        $("form[name=addProduct] #cerrar_modal").attr("disabled", false);
    }

    if ($('#added').val() == '1') {
        $("form[name=addProduct] :input").attr("disabled", false);
    }

    $('#modalAddReceivingProduct').modal('show');
}

function deleteDetallesReceiving(id) {
    $.confirm({
        theme: 'material',
        columnClass: 'col-md-6 col-md-offset-3',
        icon: 'fa fa-trash',
        title: 'Eliminar producto',
        content: 'Desea eliminar el producto ?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-default col-md-4 pull-right',
                action: function () {
                    $(this).remove();
                }
            },
            confirm: {
                text: 'Si ',
                btnClass: 'btn-primary col-md-4 pull-right',
                action: function () {
                    $.post('/Controller/Receiving.php', {
                        action: 'ajax',
                        request: 'deleteDetalles',
                        id: id
                    }, function (data) {
                        if (data.response) {
                            $("#receiving-table").find('tbody').empty().append(data.receivingDetails);
                            $("#totalPedido").html(data.totalPedido);
                            $("#totalItems").html(data.totalItems);
                            $("#total").val(data.total);

                            clearModalAddProduct();
                            $("#product").focus();
                        } else {
                            $(".flashmessenger").html(data.mensaje);
                            $("#id_product").focus();
                        }
                    }, 'json');
                }
            }
        }
    });
}

function updateReceiviedQuantity(callback) {
    var received_quantity = $('._receivedQuantity').serializeArray();
    $.post('/Controller/Receiving.php', {
        action: 'ajax',
        request: 'updateReceiviedQuantity',
        token_form: $('#token_form').val(),
        received_quantity: received_quantity
    }, function (data) {
        if (data.response) {
            callback();
        } else {
            $('.flashmessenger').html(data.message);
        }
    }, 'json');
}

/* END RECEIVING*/

/*OUTPUTS*/
/*Is used to fill list locations */
function setListLocations(callback) {
    $.post('/Controller/Output.php', {
        action: 'ajax',
        request: 'getLocations',
        id_product: $('#id_product').val(),
        store_id: $('#store_id').val()
    }, function (data) {
        if (data.response) {
            $('#location').html(data.location);
            if (callback) { callback(); }
        } else {
            $('#flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

/* Is used to validate if exist more than one option for location*/
function validateLocations() {
    var id_product = $('#id_product').val();
    $.post('/Controller/Output.php', {
        action: 'ajax',
        request: 'getLocations',
        id_product: id_product,
        store_id: $('#store_id').val()
    }, function (data) {
        if (data.response) {
            $('#location').html(data.location);
            if (data.numLocations > 1) {
                $('#location').select2('open');
            } else {
                setOutputDetails(true);
            }
        } else {
            $('#flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function setOutputDetails(byCode) {
    var options = $("form[name=output]").serializeArray();

    $.post('/Controller/Output.php', {
        action: 'ajax',
        request: 'setOutputDetails',
        options: options,
        byCode: byCode
    }, function (data) {
        if (data.response) {
            $("#output-table").find('tbody').empty().append(data.outputDetails);
            $("#totalItems").html(data.totalItems);

            $('#idDetailTemp').val('');
            $('#id_product').val('');
            $("#product").val('');
            $("#location").html('');
            $('#quantity').val('1');
            $('#product').prop('readOnly', false);
            $('#product').focus();
        } else {
            $('.flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function setDetailOutputToEdit(data) {
    $('#idProduct').val(data['id_product']);
    $('#product').val(data['description'] + ' ' + data['code']);

    for (var name in data) {
        $('form[name=output] #' + name).val(data[name]);
    }

    setListLocations(function () {
        $('#location').val(data['location']);
        $('#location').trigger('change');
    });

    $('#product').prop('readOnly', true);
}

function deleteOutputDetalles(id) {
    $.confirm({
        theme: 'material',
        columnClass: 'col-md-6 col-md-offset-3',
        icon: 'fa fa-trash',
        title: 'Eliminar producto',
        content: 'Desea eliminar el producto ?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-default col-md-4 pull-right',
                action: function () {
                    $(this).remove();
                }
            },
            confirm: {
                text: 'Si ',
                btnClass: 'btn-primary col-md-4 pull-right',
                action: function () {
                    $.post('/Controller/Output.php', {
                        action: 'ajax',
                        request: 'deleteDetalles',
                        id: id
                    }, function (data) {
                        if (data.response) {
                            $("#output-table").find('tbody').empty().append(data.outputDetails);
                            $("#totalItems").html(data.totalItems);
                            $("#total").val(data.total);

                            $('#idDetailTemp').val('');
                            $('#id_product').val('');
                            $("#product").val('');
                            $("#location").html('');
                            $('#quantity').val('1');
                            $('#product').prop('readOnly', false);

                        } else {
                            $(".flashmessenger").html(data.mensaje);
                            $("#id_product").focus();
                        }
                    }, 'json');
                }
            }
        }
    });
}

function onEnterOutput(ev, element) {
    if (ev === 13) {
        switch (element.name) {
            case 'product':
                $('#product').autocomplete('close');
                var code = $("#product").val();
                //IdProduct seria el texto ingresado en el campo producto, que seria el codigo a buscar en tabla productos

                $.post('/Controller/Output.php', {
                    action: 'ajax',
                    request: 'getProductByCode',
                    code: code
                }, function (data) {
                    if (data.response) {
                        $('#id_product').val(data.id_product);
                        validateLocations(true);
                    }
                }, 'json');

                break;
        }
    }
}
/* END OUTPUTS*/

/*TRANSFER*/
/*Is used to fill list locations */
function setListLocationsTransfer(callback) {
    $.post('/Controller/Transfer.php', {
        action: 'ajax',
        request: 'getLocations',
        id_product: $('#id_product').val(),
        store_id: $('#from_store_id').val()
    }, function (data) {
        if (data.response) {
            $('#location').html(data.location);
            if (callback) { callback(); }
        } else {
            $('#flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

/* Is used to validate if exist more than one option for location*/
function validateLocationsTransfer() {
    var id_product = $('#id_product').val();
    $.post('/Controller/Transfer.php', {
        action: 'ajax',
        request: 'getLocations',
        id_product: id_product,
        store_id: $('#from_store_id').val()
    }, function (data) {
        if (data.response) {
            $('#location').html(data.location);
            if (data.numLocations > 1) {
                $('#location').select2('open');
            } else {
                setTransferDetails(true);
            }
        } else {
            $('#flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function setTransferDetails(byCode) {
    var options = $("form[name=transfer]").serializeArray();

    $.post('/Controller/Transfer.php', {
        action: 'ajax',
        request: 'setTransferDetails',
        options: options,
        byCode: byCode
    }, function (data) {
        if (data.response) {
            $("#transfer-table").find('tbody').empty().append(data.transferDetails);
            $("#totalItems").html(data.totalItems);

            $('#idDetailTemp').val('');
            $('#id_product').val('');
            $("#product").val('');
            $("#location").html('');
            $('#quantity').val('1');
            $('#product').prop('readOnly', false);
            $('#product').focus();
        } else {
            $('.flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function setDetailTransferToEdit(data) {
    $('#idProduct').val(data['id_product']);
    $('#product').val(data['description'] + ' ' + data['code']);

    for (var name in data) {
        $('form[name=transfer] #' + name).val(data[name]);
    }

    setListLocationsTransfer(function () {
        $('#location').val(data['location']);
        $('#location').trigger('change');
    });

    $('#product').prop('readOnly', true);
}

function deleteTransferDetalles(id) {
    $.confirm({
        theme: 'material',
        columnClass: 'col-md-6 col-md-offset-3',
        icon: 'fa fa-trash',
        title: 'Eliminar producto',
        content: 'Desea eliminar el producto ?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-default col-md-4 pull-right',
                action: function () {
                    $(this).remove();
                }
            },
            confirm: {
                text: 'Si ',
                btnClass: 'btn-primary col-md-4 pull-right',
                action: function () {
                    $.post('/Controller/Transfer.php', {
                        action: 'ajax',
                        request: 'deleteDetalles',
                        id: id
                    }, function (data) {
                        if (data.response) {
                            $("#transfer-table").find('tbody').empty().append(data.transferDetails);
                            $("#totalItems").html(data.totalItems);
                            $("#total").val(data.total);

                            $('#idDetailTemp').val('');
                            $('#id_product').val('');
                            $("#product").val('');
                            $("#location").html('');
                            $('#quantity').val('1');
                            $('#product').prop('readOnly', false);

                        } else {
                            $(".flashmessenger").html(data.mensaje);
                            $("#id_product").focus();
                        }
                    }, 'json');
                }
            }
        }
    });
}

function onEnterTransfer(ev, element) {
    if (ev === 13) {
        switch (element.name) {
            case 'product':
                $('#product').autocomplete('close');
                var code = $("#product").val();
                //IdProduct seria el texto ingresado en el campo producto, que seria el codigo a buscar en tabla productos

                $.post('/Controller/Transfer.php', {
                    action: 'ajax',
                    request: 'getProductByCode',
                    code: code
                }, function (data) {
                    if (data.response) {
                        $('#id_product').val(data.id_product);
                        validateLocationsTransfer(true);
                    }
                }, 'json');

                break;
        }
    }
}
/* END TRANSFER*/

/*RETURNS*/
/*Is used to fill list locations */
function setListLocationsReturn(callback) {
    var id_product = $('#id_product').val();
    $.post('/Controller/Return.php', {
        action: 'ajax',
        request: 'getLocations',
        id_product: id_product,
        store_id: $('#store_id').val()
    }, function (data) {
        if (data.response) {
            $('#location').html(data.location);
            if (callback) { callback(); }
        } else {
            $('#flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

/* Is used to validate if exist more than one option for location*/
function validateLocationsReturn() {
    var id_product = $('#id_product').val();
    $.post('/Controller/Return.php', {
        action: 'ajax',
        request: 'getLocations',
        id_product: id_product,
        store_id: $('#store_id').val()
    }, function (data) {
        if (data.response) {
            $('#location').html(data.location);
            if (data.numLocations > 1) {
                $('#location').select2('open');
            } else {
                setReturnDetails(true);
            }
        } else {
            $('#flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function setReturnDetails(byCode) {
    var options = $("form[name=return]").serializeArray();

    $.post('/Controller/Return.php', {
        action: 'ajax',
        request: 'setReturnDetails',
        options: options,
        byCode: byCode
    }, function (data) {
        if (data.response) {
            $("#return-table").find('tbody').empty().append(data.returnDetails);
            $("#totalItems").html(data.totalItems);

            $('#idDetailTemp').val('');
            $('#id_product').val('');
            $("#product").val('');
            $("#location").html('');
            $('#quantity').val('1');
            $('#product').prop('readOnly', false);
            $('#product').focus();
        } else {
            $('.flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function setDetailReturnToEdit(data) {
    $('#idProduct').val(data['id_product']);
    $('#product').val(data['description'] + ' ' + data['code']);

    for (var name in data) {
        $('form[name=return] #' + name).val(data[name]);
    }

    setListLocationsReturn(function () {
        $('#location').val(data['location']);
        $('#location').trigger('change');
    });

    $('#product').prop('readOnly', true);
}

function deleteReturnDetalles(id) {
    $.confirm({
        theme: 'material',
        columnClass: 'col-md-6 col-md-offset-3',
        icon: 'fa fa-trash',
        title: 'Eliminar producto',
        content: 'Desea eliminar el producto ?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-default col-md-4 pull-right',
                action: function () {
                    $(this).remove();
                }
            },
            confirm: {
                text: 'Si ',
                btnClass: 'btn-primary col-md-4 pull-right',
                action: function () {
                    $.post('/Controller/Return.php', {
                        action: 'ajax',
                        request: 'deleteDetalles',
                        id: id
                    }, function (data) {
                        if (data.response) {
                            $("#return-table").find('tbody').empty().append(data.returnDetails);
                            $("#totalItems").html(data.totalItems);
                            $("#total").val(data.total);

                            $('#idDetailTemp').val('');
                            $('#id_product').val('');
                            $("#product").val('');
                            $("#location").html('');
                            $('#quantity').val('1');
                            $('#product').prop('readOnly', false);

                        } else {
                            $(".flashmessenger").html(data.mensaje);
                            $("#id_product").focus();
                        }
                    }, 'json');
                }
            }
        }
    });
}

function onEnterReturn(ev, element) {
    if (ev === 13) {
        switch (element.name) {
            case 'product':
                $('#product').autocomplete('close');
                var code = $("#product").val();
                //IdProduct seria el texto ingresado en el campo producto, que seria el codigo a buscar en tabla productos

                $.post('/Controller/Return.php', {
                    action: 'ajax',
                    request: 'getProductByCode',
                    code: code
                }, function (data) {
                    if (data.response) {
                        $('#id_product').val(data.id_product);
                        validateLocationsReturn(true);
                    }
                }, 'json');

                break;
        }
    }
}
/* END RETURNS*/

/* PAGOS */
function getListFacturasProveedores() {
    var proveedor = $('#proveedor').val();
    $.post('/Controller/Payment.php', {
        action: 'ajax',
        request: 'getListFacturasByProveedor',
        proveedor: proveedor
    }, function (data) {
        if (data.response) {
            $("#listFacturasProveedores").html(data.listFacturas);
        }
    }, 'json');
    setSumPagos();
}

function addInvoiceToPayment(trId) {
    var montoTotalPago = $('#monto').val();
    if (montoTotalPago.trim() === '') {
        _getTranslation('Antes de seleccionar Facturas, ingresa el monto del pago.', function (msj) { $.alert({ content: msj, title: "<i class='fa fa-info-circle'></i> Mensaje", columnClass: 'col-md-6 col-md-offset-3' }); });
        $('#monto').focus();
    }

    var pagos = $(".ammountPymt").serializeArray();
    var sumPagos = 0;
    $.each(pagos, function (i, pago) {
        if ($.trim(pago.value) !== '') {
            sumPagos = parseFloat(sumPagos) + parseFloat(pago.value);
        }
    });

    if (montoTotalPago > sumPagos) {
        $.post('/Controller/Payment.php', {
            action: 'ajax',
            request: 'addInvoiceToPayment',
            idFactura: trId,
            montoTotalPago: montoTotalPago,
            sumPagos: sumPagos
        }, function (data) {
            if (data.response) {
                $("#listFacturasAPagar").append(data.factura);
                $('#addInvoice_' + trId).hide();
                setSumPagos();
            }
        }, 'json');
    }
}

function deleteInvoiceFromPayment(trId) {
    $('#pago[' + trId + ']').remove();
    $('#deleteInvoice_' + trId).remove();
    $('#addInvoice_' + trId).show();
    setSumPagos();
}

function setSumPagos() {
    var pagos = $(".ammountPymt").serializeArray();
    var sumPagos = 0;

    $.each(pagos, function (i, pago) {
        if ($.trim(pago.value) !== '') {
            sumPagos = parseFloat(sumPagos) + parseFloat(pago.value);
        }
    });

    var checkAmmountDecimal = sumPagos.toFixed(2);
    $('#suma_de_pagos').val(checkAmmountDecimal);
    sumPagos = "$" + checkAmmountDecimal.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    $('.sumPagos').html(sumPagos);
    $('.label_suma_pagos').html(sumPagos);


}

function limpiarFacturasAPagarPorCambioMonto() {
    var monto_original = $('#monto_original').val();
    $.confirm({
        theme: 'material',
        columnClass: 'col-md-6 col-md-offset-3',
        icon: 'fa fa-refresh',
        title: 'Cambiar monto de pago',
        content: 'Si cambia el monto de pago, se cancelaran las facturas seleccionadas hasta ahora. Desea continuar ?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-default col-md-4 pull-right',
                action: function () {
                    $('#monto').val(monto_original);
                    $('#monto_original').val(monto_original);
                    setLabelMonto(monto_original);
                    $(this).remove();
                }
            },
            confirm: {
                text: 'Si ',
                btnClass: 'btn-primary col-md-4 pull-right',
                action: function () {
                    $("#listFacturasAPagar").html('');
                    getListFacturasProveedores();
                }
            }
        }
    });
}

function setLabelMonto(monto) {
    var monto = parseFloat(monto);
    $('.label_monto_pago').html('$' + monto.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
}

/* END */

/*SPECIAL REQUISITIONS*/
function clearModalAddSliceToSpecialOrder(callback) {
    $('#idDetailTemp').val('');
    $('#type').select2({ placeholderOption: 'first' });
    $('#size').val('').trigger('change');
    $('#category').val('').trigger('change');
    $('#product').html('<option>Seleccionar una opcion...</option>').trigger('change');
    $('#quantity').val('1');
    $('#price').val('');
    $('#number_of_cake').val('');
    $('#multiple').val('');
    $('.flashmessenger_addSliceToSpecialOrder').html('');

    if (callback) {
        callback();
    }
}

function configSepecialOrderColumnTable(table_id) {
    var table = $('#' + table_id).DataTable({
        searching: false,
        paging: false,
        aaSorting: [],
        columnDefs: [{ orderable: false, targets: "_all" }]
    });

    if ($.fn.dataTable.isDataTable('#' + table_id)) {
        table = $('#' + table_id).DataTable();
    }

    if ($('#role_logued').val() !== '1') { table.columns([5, 6]).visible(false); }
}

function setSpecialOrderDetailsWizard() {
    var pan = $('._wizard_pan ._selected_option_wizard').data('panid');
    var relleno = $('._wizard_relleno ._selected_option_wizard').data('rellenoid');
    var decorado = $('._wizard_decorado ._selected_option_wizard').data('decoradoid');

    if (typeof pan === "undefined" || typeof relleno === "undefinded" || typeof decorado === "undefined") {
        $.confirm({
            theme: 'material',
            columnClass: 'col-md-6 col-md-offset-3',
            icon: 'fa fa-exclamation-triangle',
            title: 'Mensaje',
            content: 'Debes seleccionar por lo menos 1 Pan, 1 Relleno y 1 Decorado, para poder terminar.',
            buttons: {
                cancel: {
                    text: 'Aceptar',
                    btnClass: 'btn-primary col-md-4 pull-right',
                    action: function () {
                        $(this).remove();
                    }
                }
            }
        });
        return null;
    }

    $.post('/Controller/SpecialOrder.php', {
        action: 'ajax',
        request: 'setSpecialOrderDetailsWizard',
        pan: pan,
        relleno: relleno,
        decorado: decorado,
        precio: $('#price').val(),
        number_of_cake: $('#number_of_cake').val(),
        token_form: $("form[name='special_order'] #token_form").val(),
        idProductForSpecialDecorated: $('#idProductForSpecialDecorated ').val(),
        idDetailTemp: $('#idDetailTemp').val(),
        type: 'Special'
    }, function (data) {
        if (data.response) {
            destroyDataTable('requisition-table');
            $("#requisition-table").find('tbody').empty().append(data.requisitionDetails);
            configSepecialOrderColumnTable('requisition-table');

            $('#idDetailTemp').val('');
            $('#category').val('').trigger('change');
            $('#size').val('').trigger('change');
            $('#product').val('').trigger('change');
            $('#quantity').val('1');
            $('#price').val('');
            $('#number_of_cake').val('');
            $('#multiple').val('');
            $('#ammount').val(data.grandTotal);
            $('#grandTotal').html(number_format(data.grandTotal, 2, '.', ','));

            clearSpecialOrderWizard();
            $('#modalAddSliceToSpecialOrderWizard').modal('hide');

        } else {
            $('#flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function clearSpecialOrderWizard() {
    $('._wizard_sizes div').removeClass('_selected_option_wizard');
    $('._wizard_shape').html('');
    $('._wizard_pan').html('');
    $('._wizard_relleno').html('');
    $('._wizard_decorado').html('');
}


function setSpecialOrderDetails() {
    var quantity = $('#quantity').val();
    if (quantity.trim() === '0' || quantity.trim() === '') {
        $.confirm({
            theme: 'material',
            columnClass: 'col-md-6 col-md-offset-3',
            icon: 'fa fa-exclamation-triangle',
            title: 'Cantidad y/o Precio no puede ser cero',
            content: 'Cantidad y/o Precio deben ser mayor a cero !!',
            buttons: {
                cancel: {
                    text: 'Aceptar',
                    btnClass: 'btn-primary col-md-4 pull-right',
                    action: function () {
                        $(this).remove();
                    }
                }
            }
        });
        return null;
    }

    var options = $("form[name=special_order]").serializeArray();
    options.push({ name: 'token_form', value: $("form[name='special_order'] #token_form").val() });

    $.post('/Controller/SpecialOrder.php', {
        action: 'ajax',
        request: 'setSpecialOrderDetails',
        options: options
    }, function (data) {
        if (data.response) {
            destroyDataTable('requisition-table');
            $("#requisition-table").find('tbody').empty().append(data.requisitionDetails);
            configSepecialOrderColumnTable('requisition-table');

            $('#idDetailTemp').val('');
            $('#category').val('').trigger('change');
            $('#product').val('').trigger('change');
            $('#quantity').val('1');
            $('#price').val('');
            $('#multiple').val('');
            $('#ammount').val(data.grandTotal);
            $('#grandTotal').html(number_format(data.grandTotal, 2, '.', ','));
        } else {
            $('#flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function setDetailSpecialOrderToEdit(data) {
    for (var name in data) {
        $('form[name=special_order] #' + name).val(data[name]);
    }

    if (data['type']) {
        $("#type").val(data['type']).trigger('change');
        $("#size").val(data['size']).trigger('change');
        if (data['type'] === 'Line') {
            $('#type').html("<option value='Line' selected>Line</option>");
            setUnsetCategoryField(function () {
                $("#product").val(data['product']).trigger('change');
            });
        } else {
            $('#type').html("<option value='Special' selected>Especial</option>");
            setUnsetCategoryField(function () {
                $("#product").val(data['product']).trigger('change');
            });
        }
    }

    if (data['category']) {
        $("#category").val(data['category']).trigger('change');

        if (data['type'] === 'Special') {
            setSlicesForSpecialOrder(function () { $("#product").val(data['product']).trigger('change'); });
        }
    }

    $('#modalAddSliceToSpecialOrder').modal('show');
}

function deleteSpecialOrderDetail(id, item) {
    var type = $(item).data('type');
    $.post('/Controller/SpecialOrder.php', {
        action: 'ajax',
        request: 'deleteDetalles',
        id: id,
        type: type
    }, function (data) {
        if (data.response) {
            $("#requisition-table").find('tbody').empty().append(data.requisitionDetails);
            $('#idDetalleTemp').val('');
            $('#product').val('0').trigger('change');
            $('#quantity').val('1');
            $('#multiple').val('');
            $('#ammount').val(data.grandTotal);
            $('#grandTotal').html(number_format(data.grandTotal, 2, '.', ','));
        } else {
            $("#flashmessenger").html(data.mensaje);
        }
    }, 'json');
}

function setShapesBySize(callback) {
    $('._wizard_pan').html('');
    $('._wizard_relleno').html('');
    $('._wizard_decorado').html('');
    $.post('/Controller/SpecialOrder.php', {
        action: 'ajax',
        request: 'setShapesBySize',
        size: $('#size').val()
    }, function (data) {
        if (data.response) {
            $('#s2id_shape').select2("destroy");
            $('#shape').html(data.listShapes);
            $('#shape').select2({ placeholderOption: 'first' });

            /*WIZARD*/
            $('._wizard_shape').html(data.listShapesWizard);

            if (callback) {
                callback();
            }
        } else {
            $('#flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}


function setSlicesForSpecialOrder(callbackSetValue) {
    var size = $('#size').val();
    if (size === '') {
        $.confirm({
            theme: 'material',
            icon: 'fa fa-info-circle',
            title: 'Message',
            content: "Primero debes seleccionar el tamaño del pastel.",
            type: 'blue',
            buttons: {
                confirm: {
                    text: 'OK',
                    btnClass: 'btn-blue col-xs-6 pull-right',
                    action: function () { $('#category').val('').trigger('change'); $(this).remove(); }
                }
            }
        });
    }

    var shape = $('#shape').val();
    if (shape === '') {
        $.confirm({
            theme: 'material',
            icon: 'fa fa-info-circle',
            title: 'Message',
            content: "Primero debes seleccionar la forma del pastel.",
            type: 'blue',
            buttons: {
                confirm: {
                    text: 'OK',
                    btnClass: 'btn-blue col-xs-6 pull-right',
                    action: function () { $('#shape').val('').trigger('change'); $(this).remove(); }
                }
            }
        });
    }

    var category = $('#category').val();
    $.post('/Controller/SpecialOrder.php', {
        action: 'ajax',
        request: 'setSliceFlavor',
        category: category,
        size: size,
        shape: shape
    }, function (data) {
        if (data.response) {
            $('#s2id_product').select2("destroy");
            $('#product').html(data.listSlices);
            $('#product').select2({ placeholderOption: 'first' });

            if (callbackSetValue) {
                callbackSetValue();
            }

        } else {
            $('#flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function setSlicesForSpecialOrderWizard(callback) {
    $.post('/Controller/SpecialOrder.php', {
        action: 'ajax',
        request: 'setSlicesWizard',
        size: $('#size').val(),
        shape: $('#shape').val()
    }, function (data) {
        if (data.response) {
            $('._wizard_pan').html(data.listPan);
            $('._wizard_relleno').html(data.listRelleno).hide();
            $('._wizard_decorado').html(data.listDecorado).hide();

            $("body").tooltip({
                selector: '[data-toggle=tooltip]',
                template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner large"></div></div>'
            });

            if (callback) {
                callback();
            }
        } else {
            $('#flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function setDetailSpecialOrderToEditWizard(data) {
    $('#idDetailTemp').val(data.idDetailTemp);
    $('._wizard_sizes div[data-sizeid=' + data.size + ']').addClass('_selected_option_wizard');
    $('#size').val(data.size);
    $('#number_of_cake').val(data.number_of_cake);

    setShapesBySize(function () {
        $('#shape').val(data.shape);
        $('._wizard_shape div[data-shapeid=' + data.shape + ']').addClass('_selected_option_wizard');
        setSlicesForSpecialOrderWizard(function () {
            $('._wizard_pan div[data-panid=' + data.pan_id + ']').addClass('_selected_option_wizard');
            $('._wizard_relleno div[data-rellenoid=' + data.relleno_id + ']').addClass('_selected_option_wizard');
            $('._wizard_decorado div[data-decoradoid=' + data.decorado_id + ']').addClass('_selected_option_wizard');

            $('._wizard_relleno').show();
            $('._wizard_decorado').show();
        });
    });

    $('#modalAddSliceToSpecialOrderWizard').modal('show');
}

function setUnsetCategoryField(callbackSetValue) {
    var type = $('#type').val();
    var size = $('#size').val();
    if (type !== 'Special') {
        $('#category').select2({ placeholderOption: 'first' });
        $('#category').prop('disabled', true);
    } else {
        $('#category').prop('disabled', false);
    }

    $.post('/Controller/SpecialOrder.php', {
        action: 'ajax',
        request: 'getListProducts',
        type: type,
        size: size
    }, function (data) {
        if (data.response) {
            $('#product').html(data.listProducts);
            $('#s2id_product').select2("destroy");
            $('#product').select2({ placeholderOption: 'first' });
            if (callbackSetValue) {
                callbackSetValue();
            }

        } else {
            $('#flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function setExtrasListToSpecialOrder() {
    $('#size').parent('div').parent('div').hide();
    $.post('/Controller/SpecialOrder.php', {
        action: 'ajax',
        request: 'getListExtras'
    }, function (data) {
        if (data.response) {
            $('#product').html(data.listProducts);
            $('#product').trigger("change");
        } else {
            $('#flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function addPaymentToSpecialReq(idReq) {
    $.post('/Controller/SpecialOrder.php', {
        action: 'ajax',
        request: 'addPaymentToSpecialReq',
        idReq: idReq
    }, function (data) {
        if (data.response) {
            document.location = '/Controller/Pos.php?sr';
        }
    }, 'json');
}

function setProductPrice() {
    var product = $('#product').val();
    var type = $('#type').val();
    var idProductForSpecialDecorated = $('#idProductForSpecialDecorated').val();

    if (type === 'Special' && product === idProductForSpecialDecorated) {
        confirmAction('special_decorated', function () {
            $('#price').prop('readonly', false);
        });

    } else {
        $('#price').prop('readonly', true);
    }

    $.post('/Controller/SpecialOrder.php', {
        action: 'ajax',
        request: 'getProductPrice',
        type: type,
        product: product
    }, function (data) {
        if (data.response) {
            $('#price').val(data.price);
            $('#comments_cake').val(data.comments);
        }
    }, 'json');
}

function deleteImage(image) {
    $.confirm({
        theme: 'material',
        columnClass: 'col-md-6 col-md-offset-3',
        icon: 'fa fa-trash',
        title: 'Eliminar imagen',
        content: 'Desea eliminar la imagen ?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-default col-md-4 pull-right',
                action: function () {
                    $(this).remove();
                }
            },
            confirm: {
                text: 'Si ',
                btnClass: 'btn-primary col-md-4 pull-right',
                action: function () {
                    var id = $(image).data('id');
                    $.post('/Controller/SpecialOrder.php', {
                        action: 'ajax',
                        request: 'deleteImage',
                        id: id
                    }, function (data) {
                        if (data.response) {
                            $(image).closest('.thumbail').remove();
                        }
                    }, 'json');
                }
            }
        }
    });
    return null;
}

function changeStatusForSR(element) {
    var id = $(element).data('id');
    var status = $(element).data('status');
    var field = $(element).data('statusfield');
    var req_number = $(element).data('reqnumber');
    /*
    $.confirm({
            theme: 'material',
            columnClass: 'col-md-6 col-md-offset-3',
            icon: 'fa fa-trash',
            title: 'Cambiar status a Orden',
            content: 'Desea cambiar a status <b>'+ statusName+'</b> a esta Orden especial ?',
            buttons:{                
                cancel: {
                    text:'No',
                    btnClass: 'btn-default col-md-4 pull-right',
                    action: function(){
                       $(this).remove();
                    }
                },
                confirm: {
                    text: 'Si ',
                    btnClass: 'btn-primary col-md-4 pull-right',
                    action: function(){*/
    $.post('/Controller/SpecialOrder.php', {
        action: 'ajax',
        request: 'changeStatus',
        id: id,
        field: field,
        status: status,
        req_number: req_number
    }, function (data) {
        if (data.response) {
            if (field == 'status_production') {
                /*Este status solo se modifica desde SpecialOrderListProduction */
                $('._' + id).html(data.btnStatus);

                $('#req_' + req_number).prop('checked', false);
                if (status == '1') { $('#req_' + req_number).attr('disabled', false); }
                if (status == '2') { $('#req_' + req_number).attr('disabled', true); }

            } else if (field == 'status_delivery') {
                /*Este status solo se modifica desde SpecialOrderList */
                $('._statusDelivery_' + id).html(data.string);
                $('._li_entregado_' + id).removeClass('hide');
                $(element).addClass('hide');
            }
        }
    }, 'json');/*
                    }
                }
            }
        });*/
}

function setModalCustomer(btn) {
    var action = $(btn).data('action');

    if (action === 'insert') {
        clearForm('customer');
        $('form[name=customer] #action').val(action);
        $('#title_modal_customer').html('Agregar cliente');
        $('#modalAddCustomerGadget').modal('show');
    }

    if (action === 'edit') {
        if ($('#customer').val() !== '' && $('#customer').val() !== null) {
            setCustomerData();
            $('form[name=customer] #action').val(action);
            $('#title_modal_customer').html('Actualizar cliente');
            $('#modalAddCustomerGadget').modal('show');
        }
    }
}
function saveCustomer() { //alert($('#nombre').val());
    var options = $('form[name=customer]').serializeArray();
    $.post('/Controller/SpecialOrder.php', { //pos
        action: 'ajax',
        request: 'saveCustomer',
        options: options,
        customer: $('#customer').val()
    }, function (data) {
        if (data.response) {
            $('#flashmessenger').html(data.message);
            fadeOutAlert();

            $('#modalAddCustomerGadget').modal('hide');
            clearForm('customer');

            $('#s2id_customer').select2('destroy');
            $('#customer').html(data.customerList);
            $('#customer').select2();
            setCustomerData();
        } else {
            $('#flashmessenger-agregarCliente').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function setCustomerData() {
    $.post('/Controller/SpecialOrder.php', {
        action: 'ajax',
        request: 'getCustomerData',
        id: $('#customer').val()
    }, function (data) {
        if (data.response) {
            for (var name in data.customerData) {
                $('form[name=special_order] #' + name).val(data.customerData[name]);
                $('form[name=customer] #' + name).val(data.customerData[name]);
            }
        }
    }, 'json');
}

function getImagesForSR(idReq) {
    $('#divImages').html('');
    $.post('/Controller/SpecialOrder.php', {
        action: 'ajax',
        request: 'getImages',
        idReq: idReq
    }, function (data) {
        if (data.response) {
            $('#divImages').html(data.images);
        }
    }, 'json');
}

function changeStatusBaked(element) {
    var id = $(element).data('id');
    var reqNumber = $(element).data('reqnumber');
    var field = $(element).data('statusfield');
    var controller = $(element).data('controller');
    var status = $(element).data('status');

    $.post('/Controller/' + controller + '.php', {
        action: 'ajax',
        request: 'changeStatus',
        id: id,
        field: field,
        status: status
    }, function (data) {
        if (data.response) {
            if (status === 1 || status === 3) {
                $('#req_' + reqNumber).prop("disabled", true);
                $('#req_' + reqNumber).prop("checked", false);

            } else if (status === 2) {
                $('#req_' + reqNumber).prop("disabled", false);
                $('#req_' + reqNumber).prop("checked", true);
            }

            $('.' + reqNumber).removeClass('btn-primary');
            $('.' + reqNumber).addClass('btn-default');
            $(element).removeClass('btn-default');
            $(element).addClass('btn-primary');
        }
    }, 'json');

}

function allowEditSpecialOrder() {
    confirmAction('edit_special_order', function () {
        $.post('/Controller/SpecialOrder.php', {
            action: 'ajax',
            request: 'allowEditSpecialOrder',
            idSpecialOrder: $('form[name=special_order] input[name=id]').val()
        }, function (data) {
            if (data.response) {
                document.location.reload();
            } else {
                $('#flashmessenger').html(data.message);
                fadeOutAlert();
            }
        }, 'json');
    });
}

function setMinDeliveryDate() {
    var dateArray = $('#date').val().split('/');
    var currentDate = new Date(dateArray[2], dateArray[0] - 1, dateArray[1]);

    var days = [];
    days[0] = 2;
    days[1] = 2;
    days[2] = 2;
    days[3] = 2;
    days[4] = 2;
    days[5] = 2;
    days[6] = 3;

    currentDate.setDate(currentDate.getDate() + days[currentDate.getDay()]);
    var minDeliveryDate = moment(currentDate.getTime()).format('YYYY-MM-DD');
    $('#delivery_date').data("DateTimePicker").destroy();
    $("#delivery_date").datetimepicker({ format: "MM/DD/YYYY hh:mm A ", minDate: minDeliveryDate, ignoreReadonly: true, useCurrent: false });
}

function getSpecialOrderFeedback() {
    $.post('/Controller/SpecialOrder.php', {
        action: 'ajax',
        request: 'getFeedback',
        id: $("#id_special_order_for_feedback").val()
    }, function (data) {
        if (data.response) {
            $('#feedback').val(data.feedback);
            $('#modalAddSpecialOrderFeedback').modal('show');
        }
    }, 'json');
}

function saveSpecialOrderFeedback() {
    $.post('/Controller/SpecialOrder.php', {
        action: 'ajax',
        request: 'saveFeedback',
        id: $("#id_special_order_for_feedback").val(),
        feedback: $("#feedback").val()
    }, function (data) {
        if (data.response) {
            $('#feedback').val('');
            $('#modalAddSpecialOrderFeedback').modal('hide');
        }
    }, 'json');
}

function prepareEmailingSpecialRequisition(id) {
    $("form[name=emailing] #id_special_requisition").val(id);
    $.post('/Controller/SpecialOrder.php', {
        action: 'ajax',
        request: 'getDataSpecialOrder',
        id: id
    }, function (data) {
        if (data.response) {
            $('#to').val(data.to);
            $('#subject').val(data.subject);
            $('#message').val(data.messageMail);

            $('#modalEmailingSpecialRequisition').modal('show');
        }
    }, 'json');
}

function emailingSepecialRequisition() {
    var options = $('form[name=emailing]').serializeArray();

    $.post('/Controller/SpecialOrder.php', {
        action: 'ajax',
        request: 'emailingSpecialOrder',
        options: options
    }, function (data) {
        if (data.response) {
            clearForm('emailing');
            $('.flashmessenger').html(data.msg);
            $('#modalEmailingSpecialRequisition').modal('hide');
        } else {
            $('.flashmessenger').html(data.msg);
        }
    }, 'json');
}

/* END SPECIAL REQUISITION*/

/* STORE REQUEST*/
function setStoreRequestDetallesForNewStoreRequest() {
    $.post('/Controller/StoreRequest.php', {
        action: 'ajax',
        request: 'setStoreRequestDetallesForArea',
        store_id: $('#store_id').val(),
        area_id: $('#area_id').val(),
        token_form: $('#token_form').val()
    }, function (data) {
        if (data.response) {
            destroyDataTable('storeRequestDetails');
            $("#storeRequestDetails").find('tbody').empty().append(data.storeRequestDetalles);
            setDataTable('storeRequestDetails');
        } else {
            $('#flashmessenger').html(data.message);
        }
    }, 'json');
}

function updateStoreRequestQty(callback) {
    var table = $('#storeRequestDetails').DataTable();
    var store_request_quantity = $('._storeRequestQuantity', table.rows().nodes()).serializeArray();
    $.post('/Controller/StoreRequest.php', {
        action: 'ajax',
        request: 'updateStoreRequestQty',
        token_form: $('#token_form').val(),
        storage_request_quantity: store_request_quantity
    }, function (data) {
        if (data.response) {
            callback();
        } else {
            $('#flashmessenger').html(data.message);
        }
    }, 'json');
}

function generateShipment(id) {
    $.post('/Controller/StoreRequest.php', {
        action: 'ajax',
        request: 'generateShipment',
        id_store_request: id
    }, function (data) {
        if (data.response) {
            /*automatic_shipment se usa para en el enviar enviar lo mismo que se pide en el pedido
             * y se usaba para direccionar a recibdo direcatamente cuando se hacia click en el camioncito de la lista,
             * (Para esto ultimo ya no se usa, solo para lo primero)*/
            /*if(data.automatic_shipment){
                document.location = "ReceivingStoreRequest.php?action=edit&id="+data.receiving_id;
            }*/
            $('.flashmessenger').html(data.msg);
        }
    }, 'json');
}

function thereIsOrderForToday(elem) {
    var storeId = $('#store_id').val();
    var areaId = $('#area_id').val();
    var delivery_date = $('#delivery_date').val();

    if (storeId === '' || areaId === '' || delivery_date === '') { return null; }

    var options = $('form[name=storeRequestForm]').serializeArray();
    $.post('/Controller/StoreRequest.php', {
        action: 'ajax',
        request: 'thereIsOrderForToday',
        options: options
    }, function (data) {
        if (data.response) {
            if ($(elem).attr('id') == 'delivery_date') { $(elem).data("DateTimePicker").clear(); }
            else { $(elem).val('').trigger('change'); }

            $.confirm({
                theme: 'material',
                columnClass: 'col-md-6 col-md-offset-3',
                icon: 'fa fa-trash',
                title: 'Mensaje',
                content: data.msg,
                buttons: {
                    cancel: {
                        text: 'Cerrar',
                        btnClass: 'btn-default col-md-4 pull-right',
                        action: function () {
                            $(this).remove();
                        }
                    },
                    confirm: {
                        text: 'Ir a pedido ',
                        btnClass: 'btn-primary col-md-4 pull-right',
                        action: function () {
                            document.location = 'StoreRequest.php?action=edit&id=' + data.storeRequest;
                        }
                    }
                }
            });

        } else {
            setStoreRequestDetallesForNewStoreRequest();
        }
    }, 'json');
}

function blockUnblockOrder(elem) {
    var inProcess = $(elem).data('inprocess');
    var id_store_request = $(elem).data('id');

    $.post('/Controller/StoreRequest.php', {
        action: 'ajax',
        request: 'blockUnblockOrder',
        id_store_request: $(elem).data('id'),
        inProcess: inProcess
    }, function (data) {
        if (data.response) {
            if (inProcess == '0') { $(elem).data('inprocess', '1'); }
            if (inProcess == '1') { $(elem).data('inprocess', '0'); }
            $(elem).text(data.newInProcessText);
            $('._inProcess_' + id_store_request).html(data.inProcessColumn);
        }
    }, 'json');
}
/* END STORE REQUEST*/

/*SHIPMENTS*/
function setShipmentDetails(byCode, callback) {
    var options = $("form[name=shipment_store_request]").serializeArray();
    var filters = $('.column_filter').serializeArray();
    $.post('/Controller/ShipmentStoreRequest.php', {
        action: 'ajax',
        request: 'setShipmentDetails',
        options: options,
        byCode: byCode
    }, function (data) {
        if (data.response) {
            destroyDataTable('shipment-table');
            $("#shipment-table").find('tbody').empty().append(data.shipmentDetails);
            $("#requiredItems").html(data.requiredItems);
            $("#totalItems").html(data.totalItems);
            $("#receivedItems").html(data.receivedItems);
            setDataTable('shipment-table');

            /*popular filtros - recorrer filter y asigna valores*/
            $(filters).each(function (i, field) {
                $('input[name=' + field.name + ']').val(field.value);
            });

            /*Apply the search*/
            var table = $('#shipment-table').DataTable();
            table.columns().every(function () {
                var that = this;
                var criteria = $('input', this.footer()).val();

                if (typeof criteria !== "undefined" && criteria !== '') {
                    if (that.search() !== criteria) {
                        that
                            .search(criteria.replace("/;/g", "|"), true, false)
                            .draw();
                    }
                }

            });

            $('#idDetailTemp').val('');
            $('#idProduct').val('');
            $('#product').val('');
            $("#s2id_product").select2("val", '0');
            $('#product').prop('disabled', false);
            $('#quantity').val('1');

            if (callback) {
                callback();
            } else {
                $('#product').focus();
            }
        } else {
            $('#idProduct').val('');
            $('#product').val('');
            $('#product').prop('disabled', false);
            $.confirm({
                theme: 'material',
                icon: 'fa fa-info-circle',
                title: 'Error',
                content: data.message,
                type: 'red',
                buttons: {
                    confirm: {
                        text: 'OK',
                        btnClass: 'btn-red col-xs-6 pull-right',
                        action: function () { $(this).remove(); }
                    }
                }
            });
        }

    }, 'json');
}


function setDetailShipmentToEdit(data) {
    for (var name in data) {
        $('form[name=shipment_store_request] #' + name).val(data[name]);
    }

    $('#idProduct').val(data['id_product']);
    $('#product').val(data['description'] + ' ' + data['size']);
    $('#product').prop('disabled', true);
    $('#quantity').select().focus();
}

function deleteShipmentDetails(id) {
    $.confirm({
        theme: 'material',
        columnClass: 'col-md-6 col-md-offset-3',
        icon: 'fa fa-trash',
        title: 'Eliminar envio',
        content: 'Desea eliminar el envio ?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-default col-md-4 pull-right',
                action: function () {
                    $(this).remove();
                }
            },
            confirm: {
                text: 'Si ',
                btnClass: 'btn-success col-md-4 pull-right',
                action: function () {
                    $.post('/Controller/ShipmentStoreRequest.php', {
                        action: 'ajax',
                        request: 'deleteDetalles',
                        id: id
                    }, function (data) {
                        if (data.response) {
                            $("#shipment-table").find('tbody').empty().append(data.shipmentDetails);
                            $("#requiredItems").html(data.requiredItems);
                            $("#totalItems").html(data.totalItems);
                            $("#receivedItems").html(data.receivedItems);

                            $('#idDetalleTemp').val('');
                            $('#product').val('');
                            $("#s2id_product").select2("val", '0');
                            $('#product').prop('disabled', false);
                            $('#quantity').val('');
                            $("#s2id_product").focus();
                        } else {
                            $("#flashmessenger").html(data.mensaje);
                            $("#s2id_product").focus();
                        }
                    }, 'json');
                }
            }
        }
    });
}

function onEnterShipment(ev, element) {
    if (ev === 13) {
        switch (element.name) {
            case 'product':
                $('#product').autocomplete('close');
                var idProduct = $("#product").val();
                //IdProduct seria el texto ingresado en el campo producto, que seria el codigo a buscar en tabla productos
                $("#idProduct").val(idProduct);
                setShipmentDetails(true);
                break;
        }
    }
}

/* END SHIPMENTS*/

/*RECEIVING STORE REQUEST*/
function setReceivingStoreRequestDetails(byCode) {
    var options = $("form[name=receiving_store_request]").serializeArray();

    $.post('/Controller/ReceivingStoreRequest.php', {
        action: 'ajax',
        request: 'setReceivingDetails',
        options: options,
        byCode: byCode
    }, function (data) {
        if (data.response) {
            destroyDataTable("receiving-table");
            $("#receiving-table").find('tbody').empty().append(data.receivingDetails);
            setDataTable("receiving-table");

            $("#totalPedido").html(data.totalPedido);
            $("#totalItems").html(data.totalItems);
            $("#receivedItems").html(data.receivedItems);

            if (parseInt(data.totalPedido) <= parseInt(data.receivedItems)) {
                $('#received_incomplete').prop('disabled', true);
            } else {
                $('#received_incomplete').prop('disabled', false);
            }

            $('#idDetailTemp').val('');
            $('#idProduct').val('');
            $('#product').val('');
            $('#product').prop('disabled', false);
            $('#received').val('1');
        } else {
            $('#idProduct').val('');
            $('#product').val('');
            $('#product').prop('disabled', false);
            $.confirm({
                theme: 'material',
                icon: 'fa fa-info-circle',
                title: 'Error',
                content: data.message,
                type: 'red',
                buttons: {
                    confirm: {
                        text: 'OK',
                        btnClass: 'btn-red col-xs-6 pull-right',
                        action: function () { $(this).remove(); }
                    }
                }
            });
        }
    }, 'json');
}

function setDetailReceivingStoreRequestToEdit(data) {
    for (var name in data) {
        $('form[name=receiving_store_request] #' + name).val(data[name]);
    }

    $('#idProduct').val(data['product']);
    $('#product').val(data['description'] + ' ' + data['size']);
    $('#product').prop('disabled', true);
    $('#received').select().focus();
}

function deleteReceivingStoreRequestDetails(id) {
    $.confirm({
        theme: 'material',
        columnClass: 'col-md-6 col-md-offset-3',
        icon: 'fa fa-trash',
        title: 'Eliminar cantidad recibida',
        content: 'Desea eliminar cantidad recibida ?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-default col-md-4 pull-right',
                action: function () {
                    $(this).remove();
                }
            },
            confirm: {
                text: 'Si ',
                btnClass: 'btn-primary col-md-4 pull-right',
                action: function () {
                    $.post('/Controller/ReceivingStoreRequest.php', {
                        action: 'ajax',
                        request: 'deleteDetalles',
                        id: id
                    }, function (data) {
                        if (data.response) {
                            destroyDataTable("receiving-table");
                            $("#receiving-table").find('tbody').empty().append(data.receivingDetails);
                            setDataTable("receiving-table");

                            $("#totalPedido").html(data.totalPedido);
                            $("#totalItems").html(data.totalItems);
                            $("#receivedItems").html(data.receivedItems);

                            if (parseInt(data.totalPedido) <= parseInt(data.receivedItems)) {
                                $('#received_incomplete').prop('disabled', true);
                            } else {
                                $('#received_incomplete').prop('disabled', false);
                            }

                            $('#idDetalleTemp').val('');
                            $('#idProduct').val('');
                            $('#product').val('');
                            $('#product').prop('disabled', false);
                            $('#received').val('1');
                        } else {
                            $(".flashmessenger").html(data.mensaje);
                        }
                    }, 'json');
                }
            }
        }
    });
}

function getShipmentData() {
    var numShipment = $('#num_shipment').val();

    $.post('/Controller/ReceivingStoreRequest.php', {
        action: 'ajax',
        request: 'getShipmentData',
        numShipment: numShipment
    }, function (data) {
        if (data.existReceiving) {
            document.location = "ReceivingStoreRequest.php?action=edit&id=" + data.idReceiving;
        }

        $('#idDetailTemp').val('');
        $('#idProduct').val('');
        $('#product').val('');
        $('#received').val('');
        $('.flashmessenger').html(data.message);

    }, 'json');
}

function onEnterReceivingStoreRequest(element) {
    switch (element.name) {
        case 'product':
            $('#product').autocomplete('close');
            var idProduct = $("#product").val();
            $("#idProduct").val(idProduct);
            setReceivingStoreRequestDetails(true);
            break;
    }
}

function updateReceivingStoreRequestQty(callback) {
    var table = $('#receiving-table').DataTable();
    var received_quantity = $('._receivedQuantity', table.rows().nodes()).serializeArray();

    $.post('/Controller/ReceivingStoreRequest.php', {
        action: 'ajax',
        request: 'updateReceivingStoreRequestQty',
        token_form: $('#token_form').val(),
        received_quantity: received_quantity
    }, function (data) {
        if (data.response) {
            callback();
        } else {
            $('#flashmessenger').html(data.message);
        }
    }, 'json');
}

function allowEditReceivingStoreRequest() {
    confirmAction('editarReceivingStoreRequest', function () {
        $.post('/Controller/ReceivingStoreRequest.php', {
            action: 'ajax',
            request: 'allowEditReceivingStoreRequest',
            receiving_store_request_id: $('form[name=receiving_store_request] input[name=id]').val()
        }, function (data) {
            if (data.response) {
                document.location.reload();
            } else {
                $('#flashmessenger').html(data.message);
                fadeOutAlert();
            }
        }, 'json');
    });
}

/*  END RECEIVING STORE REQEUEST*/

/* PHYSICAL INVENTORY*/
function setPhysicalInventoryDetallesForNew() {
    $.post('/Controller/PhysicalInventory.php', {
        action: 'ajax',
        request: 'setPhysicalInventoryDetallesForNew',
        token_form: $('#token_form').val()
    }, function (data) {
        if (data.response) {
            destroyDataTable('physicalInventoryDetails');
            $("#physicalInventoryDetails").find('tbody').empty().append(data.physicalInventoryDetalles);
            setDataTable('physicalInventoryDetails');
        } else {
            $('#flashmessenger').html(data.message);
        }
    }, 'json');
}


function updatePhysicalInventoryQty(callback) {
    var tablePanaderia = $('#physicalInventoryPanaderia').DataTable();
    var panaderia = $('._physicalInventoryQuantity', tablePanaderia.rows().nodes()).serializeArray();

    var tablePasteleria = $('#physicalInventoryPasteleria').DataTable();
    var pasteleria = $('._physicalInventoryQuantity', tablePasteleria.rows().nodes()).serializeArray();

    var tableMiniatura = $('#physicalInventoryMiniatura').DataTable();
    var miniatura = $('._physicalInventoryQuantity', tableMiniatura.rows().nodes()).serializeArray();

    var tableOtros = $('#physicalInventoryOtros').DataTable();
    var otros = $('._physicalInventoryQuantity', tableOtros.rows().nodes()).serializeArray();

    panaderia = panaderia.concat(pasteleria, miniatura, otros);

    $.post('/Controller/PhysicalInventory.php', {
        action: 'ajax',
        request: 'updatePhysicalInventoryQty',
        token_form: $('#token_form').val(),
        physical_inventory_quantity: panaderia
    }, function (data) {
        if (data.response) {
            callback();
        } else {
            $('#flashmessenger').html(data.message);
        }
    }, 'json');
}

function thereIsPhysicalInventoryForToday(callback) {
    var options = $('form[name=physicalInventoryForm]').serializeArray();
    $.post('/Controller/PhysicalInventory.php', {
        action: 'ajax',
        request: 'thereIsPhysicalInventoryForToday',
        options: options
    }, function (data) {
        callback(data);
    }, 'json');
}

/* END PHYSICAL INVENTORY*/

/* TIMECLOCK MOBILE*/

function setPunchTimeClock() {
    var nip_user = $('#nip_user').val();
    $('#nip_user').val('');
    $.post('/Controller/TimeClock.php', {
        action: 'ajax',
        request: 'setPunchTimeClock',
        nip_user: nip_user
    }, function (data) {
        if (data.response) {
            $('#flashmessenger-gadgetTimeclock').html(data.message);
        }
    }, 'json');
}

function setDataToEditTimeClock(id) {
    $.post('/Controller/TimeClock.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.timeClockData) {
                $('form[name=time_clock] #' + name).val(data.timeClockData[name]);
            }

            $('select').trigger('change');
            $('#modalAddTimeClock').modal('show');
        }
    }, 'json');
}

function deleteTimeClock(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar este registro ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/TimeClock.php', {
                                    action: 'ajax',
                                    request: 'deleteTimeClock',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'TimeClock.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}
/* END TIMECLOCK MOBILE*/
/* REPORTS */
function setFilterReport(report, callback) {
    var reportName = $(report).data('reportname');
    $('#report').val(reportName);

    $.post('/Controller/Reports.php', {
        action: 'ajax',
        request: 'getFiltersReport',
        report: reportName
    }, function (data) {
        if (data.response) {
            $('#filtersArea').html(data.filters);
            $('select').select2();
            $("#startDate,#endDate").datetimepicker({ format: 'MM/DD/YYYY' });
            $('#modalFiltroReportes').modal('show');
            callback();
        }
    }, 'json');
}
/* END REPORTS*/


/* ACCOUTING REPORTS*/
function listPendingInvoices(vendor) {
    $('#active_vendor').val(vendor);
    $.post('/Controller/Payment.php', {
        action: 'ajax',
        request: 'getListFacturasByProveedorForAPReport',
        proveedor: vendor
    }, function (data) {
        if (data.response) {
            if ($.fn.dataTable.isDataTable('#detalleFacturas')) {
                $('#detalleFacturas').DataTable().destroy();
            }

            $("#detalleFacturas").find('tbody').empty().html(data.listFacturas);
            var table = $('#detalleFacturas').DataTable({
                searching: true,
                info: false,
                paginate: false,
                filter: true,
                bFilter: true,
                aaSorting: [],
                dom: 'Bfrtip',
                buttons: [
                    'excel'
                ]
            });

            $('#detalleFacturas').removeClass('display').addClass('table table-striped table-bordered');
            $('#detalleFacturas tfoot th.filter').each(function () {
                $(this).html('<input type="text" placeholder="Buscar" style="width:100%" />');
            });

            // Apply the search
            table.columns().every(function () {
                var that = this;
                $('input', this.footer()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value.replace("/;/g", "|"), true, false)
                            .draw();
                    }
                });
            });
        }
    }, 'json');
}

function payVendorInvoice() {
    var options = $('form[name=pago]').serializeArray();
    $.post('/Controller/Payment.php', {
        action: 'ajax',
        request: 'payVendorInvoice',
        options: options
    }, function (data) {
        if (data.response) {
            clearModalPayVendorInvoice();
            $("#detalleFacturas").find('tbody').empty().append(data.listFacturas);
            $('#modalpayVendorInvoice').modal('hide');
        }
    }, 'json');

}

function clearModalPayVendorInvoice() {
    $('#active_vendor').val('');
    $('#active_invoice').val('');
    $('#fecha').val('');
    $('#forma_de_pago').val('').trigger('change');
    $('#num_operacion').val('');
    $('#monto').val('');
    $('#comentarios').val('');
}

/* END ACCOUTING REPORTS*/

function setPriceForSuperSpecialDecorated() {
    $.confirm({
        title: 'Precio de decorado',
        icon: 'fa fa-dollar',
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<label>Ingresa precio de decorado.</label>' +
            '<input id="precio_decorado" type="text" placeholder="precio" class="precio_decorado form-control" required value="' + $('#price').val() + '" />' +
            '</div>' +
            '</form>',
        buttons: {
            formSubmit: {
                text: 'Submit',
                btnClass: 'btn-blue _submit',
                action: function () {
                    if ($('#precio_decorado').val() == '') { return null; }
                    $('#price').val($('#precio_decorado').val());
                }
            }
        },
        onContentReady: function () {
            $('#precio_decorado').focus();
            var jc = this;
            this.$content.find('form').on('submit', function (e) {
                // if the user submits the form by pressing enter in the field.
                e.preventDefault();
                jc.$$formSubmit.trigger('click'); // reference the button and click it
            });

            this.$content.find('#precio_decorado').on('keydown', function (e) {
                var keycode = e.keyCode || e.which;
                if (keycode === 13) {
                    $('._submit').click();
                }
            });
        }
    });
}

function setNumbers(elem) {
    var digitos = $(elem).val();
    $.confirm({
        title: 'Numero para pastel',
        icon: "fa fa-check-circle-o",
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<label>Ingresa numero de pastel de ' + digitos + ' digitos.</label>' +
            '<input id="numero_de_pastel" type="text" maxlength="' + digitos + '" placeholde="numero de pastel" class="numero_de_pastel_modal form-control" required value="' + $('#number_of_cake').val() + '" />' +
            '</div>' +
            '</form>',
        buttons: {
            formSubmit: {
                text: 'Submit',
                btnClass: 'btn-blue _submit',
                action: function () {
                    if ($('#numero_de_pastel').val() == '') { return null; }
                    if ($('#numero_de_pastel').val().length != $('#numero_de_pastel').attr('maxlength')) {
                        return null;
                    }
                    $('#number_of_cake').val($('#numero_de_pastel').val());
                }
            }
        },
        onContentReady: function () {
            $('#numero_de_pastel').focus();
            var jc = this;
            this.$content.find('form').on('submit', function (e) {
                // if the user submits the form by pressing enter in the field.
                e.preventDefault();
                jc.$$formSubmit.trigger('click'); // reference the button and click it
            });

            this.$content.find('#numero_de_pastel').on('keydown', function (e) {
                var keycode = e.keyCode || e.which;
                if (keycode === 13) {
                    $('._submit').click();
                }
            });
        }
    });
}

function setLetter() {
    $.confirm({
        title: 'Letra para pastel',
        icon: "fa fa-check-circle-o",
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<label>Ingresa Letra de pastel.</label>' +
            '<input id="numero_de_pastel" type="text" maxlength="1" placeholde="Letra de pastel" class="form-control" required value="' + $('#number_of_cake').val() + '" />' +
            '</div>' +
            '</form>',
        buttons: {
            formSubmit: {
                text: 'Submit',
                btnClass: 'btn-blue _submit',
                action: function () {
                    if ($('#numero_de_pastel').val() == '') { return null; }
                    if ($('#numero_de_pastel').val().length != $('#numero_de_pastel').attr('maxlength')) {
                        return null;
                    }
                    $('#number_of_cake').val($('#numero_de_pastel').val());
                }
            }
        },
        onContentReady: function () {
            $('#numero_de_pastel').focus();
            var jc = this;
            this.$content.find('form').on('submit', function (e) {
                // if the user submits the form by pressing enter in the field.
                e.preventDefault();
                jc.$$formSubmit.trigger('click'); // reference the button and click it
            });

            this.$content.find('#numero_de_pastel').on('keydown', function (e) {
                var keycode = e.keyCode || e.which;
                if (keycode === 13) {
                    $('._submit').click();
                }
            });
        }
    });
}

function setMaxEndDateForPhysicalInventory() {
    var dateArray = $('#startDate').val().split('/');
    var currentDate = new Date(dateArray[2], dateArray[0] - 1, dateArray[1]);

    currentDate.setDate(currentDate.getDate() + 7);
    var minDeliveryDate = moment(currentDate.getTime()).format('YYYY-MM-DD');
    $('#endDate').data("DateTimePicker").destroy();
    $("#endDate").datetimepicker({ format: "MM/DD/YYYY", maxDate: minDeliveryDate, ignoreReadonly: true, useCurrent: false });
}

/*INVOICES*/

function setInvoiceDetalles() {
    if ($('#type').val() === 'produce') {
        var cajas = _rawNumber($('#quantity_boxes').val());
        if (!$.isNumeric(cajas)) { cajas = '0'; }
        if (cajas == '0') {
            _getTranslation('El campo Cajas deben ser mayor a 0.', function (msj) { $.alert({ content: msj, title: "<i class='fa fa-info-circle'></i> Message", columnClass: 'col-md-6 col-md-offset-3' }); });
            return null;
        }
        var options = $("form[name='agregar_producto_a_manifiesto']").serializeArray();
    } else {
        var options = $("form[name='addProduct']").serializeArray();
    }

    options.push({ name: 'type', value: $('form[name=invoice] #type').val() });
    options.push({ name: 'id_invoice', value: $('form[name=invoice] #id').val() });
    options.push({ name: 'token_form', value: $('form[name=invoice] #token_form').val() });
    options.push({ name: 'status', value: $('form[name=invoice] #status').val() });

    $.post('/Controller/Invoice.php', {
        action: 'ajax',
        request: 'setInvoiceDetalles',
        options: options
    }, function (data) {
        if (data.response) {
            $("#subtotal").val(data.total_importe);
            $("#descuento_items").val(data.descuento_items);
            $("#total").val(data.total);

            $("#product").focus();
            if ($('#type').val() === 'product') {
                clearModalAddInvoiceProduct();
            }

            applyGeneralDiscountToInvoiceItems();
        } else {
            $.alert({ content: data.msg, title: "<i class='fa fa-info-circle'></i> Message", columnClass: 'col-md-6 col-md-offset-3' });
        }
    }, 'json');
}

function setDetailInvoiceToEdit(data) {
    for (var name in data) {
        $('form[name=addProduct] #' + name).val(data[name]);
    }
    $('#type').val(data['type']);
    $('select').trigger('change');
    $('#quantity').val(number_format(data['quantity'], 2, '.', ','));
    $('#price').val(number_format(data['price'], 2, '.', ','));
    $('#discount').val(number_format(data['discount'], 2, '.', ','));
    $('.flashmessenger_modal_add_product').html('');
    _getTranslation('Editar producto', function (msj) { $('#title_modal_invoiceProduct').html(msj); });
    $('#modalAddInvoiceProduct').modal('show');
}

function setDetailProduceInvoiceToEdit(data, btn) {
    setFormAddProductToManifest(btn, function () {
        //Asigno manual el valor de id_producto para cuando se ejecute setListProductPropierties ya tenga el valor id_product.
        $('#id_product').val(data['id_product']).trigger('change');
        $('#type').val(data['type']);

        setListProductPropierties(function () {
            for (var name in data) { $('form[name=agregar_producto_a_manifiesto] #' + name).val(data[name]).trigger('change'); }
            //denyEditProductInModalSR('#id_product');                     
            $('#modalAgregarProductoAManifiesto').modal('show');
        });
    });
}

function deleteInvoiceDetalles(id) {
    $.confirm({
        theme: 'material',
        columnClass: 'col-md-6 col-md-offset-3',
        icon: 'fa fa-trash',
        title: 'Eliminar registro',
        content: 'Desea eliminar el registro ?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-default col-md-4 pull-right',
                action: function () {
                    $(this).remove();
                }
            },
            confirm: {
                text: 'Si ',
                btnClass: 'btn-primary col-md-4 pull-right',
                action: function () {
                    $.post('/Controller/Invoice.php', {
                        action: 'ajax',
                        request: 'deleteInvoiceDetalles',
                        id: id
                    }, function (data) {
                        if (data.response) {
                            destroyDataTable('invoiceDetails');
                            $("#invoiceDetails").find('tbody').html(data.invoiceDetails);
                            $("#subtotal").val(data.total_importe);
                            $("#descuento_items").val(data.descuento_items);
                            $("#total").val(data.total);

                            applyGeneralDiscountToInvoiceItems();
                            setDataTable('invoiceDetails');
                        } else {
                            $("#flashmessenger").html(data.mensaje);
                        }
                    }, 'json');
                }
            }
        }
    });
    return null;
}

function updateInvoicePriceByProductKeyPrice(elem) {
    var keyProductPrice = $(elem).data('keyproductprice');
    var price = $(elem).val();

    $.post('/Controller/Invoice.php', {
        action: 'ajax',
        request: 'updateSalesOrderPricetByProductKeyPrice',
        keyProductPrice: keyProductPrice,
        price: price,
        tokenForm: $('form[name=invoice] #token_form').val(),
        status: $('form[name=invoice] #status').val()
    }, function (data) {
        if (data.response) {
            destroyDataTable('invoiceDetails');

            $("#invoiceDetails").find('tbody').html(data.invoiceDetails);
            $(".precioTotal").html(number_format(data.precioTotal, 2, '.', ','));
            $('#subtotal').val(data.precioTotal);

            applyGeneralDiscountToInvoiceItems();
            setDataTable('invoiceDetails');

        } else {
            $('.flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}


function clearModalAddInvoiceProduct() {
    $('#idDetailTemp').val('');
    $('#id_product').val('');
    $('#product').val('');
    $('#quantity').val('');
    $('#price').val('');
    $('#discount').val('');
    $("#taxes").val('').trigger('change');
    $('#description_details').val('');
    $('.flashmessenger_modal_add_invoice_product').html('');
}

function getDefaultDataInvoiceProduct() {
    var product = $('form[name=addProduct] #id_product').val();
    if (product === '0') { return true; }
    $.post('/Controller/Invoice.php', {
        action: 'ajax',
        request: 'getDefaultDataProduct',
        product: product
    }, function (data) {
        if (data.response) {
            $('form[name=addProduct] #price').val(data.price).trigger('change');
            $('form[name=addProduct] #taxes').val(data.taxes).trigger('change');
            $('form[name=addProduct] #quantity').focus();
        } else {
            $('.flashmessenger_modal_add_invoice_product').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function applyGeneralDiscountToInvoiceItems() {
    var discount = _rawNumber($('form[name=invoice] #discount_general').val());

    $.post('/Controller/Invoice.php', {
        action: 'ajax',
        request: 'applyGeneralDiscountToItems',
        subtotal: $('form[name=invoice] #subtotal').val(),
        discount_items: $('form[name=invoice] #descuento_items').val(),
        discount_general_type: $('form[name=invoice] #discount_general_type').val(),
        discount_general: discount,
        token_form: $('form[name=invoice] #token_form').val()
    }, function (data) {
        if (data.response) {
            $("#invoiceDetails").find('tbody').empty().append(data.invoiceDetails);
            $("._label_importe").html((number_format(data.total_importe, 2, '.', ',')));
            $("._label_descuento_items").html((number_format(data.descuento_items, 2, '.', ',')));
            $("._label_descuento_general").html((number_format(data.descuento_general, 2, '.', ',')));
            $("._label_subtotal").html(data.total_subtotal);
            $("#invoice_totals").find('tbody#_label_impuestos').empty().append(data.total_impuestos);
            $("._label_total").html((number_format(data.total, 2, '.', ',')));
            $("#subtotal").val(data.total_importe);
            $("#descuento_items").val(data.descuento_items);
            $("#discount_general_amount").val(data.descuento_general);
            $("#total").val(data.total);
        } else {
            $('.flashmessenger_modal_add_invoice_product').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function getCustomerMethodPayment() {
    var customer = $('#id_customer').val();
    if (customer === '' || customer === '0') {
        $("#payment_terms_id").val('').trigger('change');
        $("#due_date").val('');
        return true;
    }

    var date = $('#date').val();
    $.post('/Controller/Invoice.php', {
        action: 'ajax',
        request: 'getCustomerMethodPayment',
        customer: customer,
        date: date
    }, function (data) {
        if (data.response) {
            $('#payment_terms_id').val(data.payment_terms).trigger('change');
            $("#due_date").val(data.due_date);
        } else {
            $('.flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

/*New porque ya existe otra funcion que se llama igual*/
function setDueDateNew() {
    if ($('#date').val() === '') { return true; }

    $.post('/Controller/PaymentTerms.php', {
        action: 'ajax',
        request: 'setDueDate',
        date: $('#date').val(),
        payment_terms_id: $('#payment_terms_id').val()
    }, function (data) {
        if (data.response) {
            $("#due_date").val(data.due_date);
        } else {
            $('.flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}


/* CUSTOMER PAYMENT */
function getListFacturasCustomers() {
    var customer = $('#customer').val();
    $.post('/Controller/CustomerPayment.php', {
        action: 'ajax',
        request: 'getListFacturasByCustomer',
        customer: customer
    }, function (data) {
        if (data.response) {
            destroyDataTable('pendingInvoices');
            $("#pendingInvoices").find('tbody').empty().append(data.listFacturas);
            setDataTable('pendingInvoices');
        }
    }, 'json');
    setSumPagos();
}

function addInvoiceToPaymentCustomer(trId) {
    var montoTotalPago = $('#monto_original').val();
    if (montoTotalPago.trim() === '') {
        _getTranslation('Antes de seleccionar Facturas, ingresa el monto del pago.', function (msj) { $.alert({ content: msj, title: "<i class='fa fa-info-circle'></i> Mensaje", columnClass: 'col-md-6 col-md-offset-3' }); });
        $('#monto').focus();
    }

    var pagos = $(".ammountPymt").serializeArray();
    var sumPagos = 0;
    $.each(pagos, function (i, pago) {
        if ($.trim(pago.value) !== '') {
            sumPagos = parseFloat(sumPagos) + parseFloat(pago.value);
        }
    });

    if (montoTotalPago > sumPagos) {
        $.post('/Controller/CustomerPayment.php', {
            action: 'ajax',
            request: 'addInvoiceToPaymentCustomer',
            invoice_id: trId,
            montoTotalPago: montoTotalPago,
            sumPagos: sumPagos
        }, function (data) {
            if (data.response) {
                $("#listFacturasAPagar").append(data.factura);
                $('#addInvoice_' + trId).hide();
                setSumPagos();
            }
        }, 'json');
    }
}

function limpiarFacturasAPagarPorCambioMontoCustomer() {
    var monto_original = $('#monto_original').val();
    $.confirm({
        theme: 'material',
        columnClass: 'col-md-6 col-md-offset-3',
        icon: 'fa fa-refresh',
        title: 'Cambiar monto de pago',
        content: 'Si cambia el monto de pago, se cancelaran las facturas seleccionadas hasta ahora. Desea continuar ?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-default col-md-4 pull-right',
                action: function () {
                    $('#monto').val(monto_original);
                    $('#monto_original').val(monto_original);
                    setLabelMonto(monto_original);
                    $(this).remove();
                }
            },
            confirm: {
                text: 'Si ',
                btnClass: 'btn-primary col-md-4 pull-right',
                action: function () {
                    //$("#listFacturasAPagar").html('');
                    $(".label_suma_pagos").html('');
                    //getListFacturasProveedores();                    
                    $('tbody#listFacturasAPagar tr td a.btn-danger').trigger('click');
                }
            }
        }
    });
}

/* END */

/*DEPOSITS*/
function clearModalAddDepositDetail() {
    $("#form_addDepositDetail").find('input:text, input:password, input:file, select, textarea, input:hidden').not('input:button').val('');
    $('.flashmessenger_modal_add_deposit').html('');
}

function setDepositDetails() {
    var options = $("form[name=addDepositDetail]").serializeArray();
    options.push({ name: 'token_form', value: $('form[name=deposit] #token_form').val() });

    $.post('/Controller/Deposit.php', {
        action: 'ajax',
        request: 'setDepositDetails',
        options: options
    }, function (data) {
        if (data.response) {
            $("#tableDeposit").find('tbody').empty().append(data.depositDetails);
            $("#total").val(data.total);
            $("._total").html(number_format(data.total, 2, '.', ','));
            clearModalAddDepositDetail();
        } else {
            $('.flashmessenger_modal_add_deposit').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function setDetailDepositToEdit(data) {

    for (var name in data) {
        $('form[name=addDepositDetail] #' + name).val(data[name]);
    }

    $('.flashmessenger_modal_add_deposit').html('');
    _getTranslation('Editar detalle de deposito', function (msj) { $('#title_modal_quotationProduct').html(msj); });

    $('#modalAddDeposit').modal('show');
}

function deleteDepositDetalles(id) {
    $.confirm({
        theme: 'material',
        columnClass: 'col-md-6 col-md-offset-3',
        icon: 'fa fa-trash',
        title: 'Eliminar detalle',
        content: 'Desea eliminar este detalle ?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-default col-md-4 pull-right',
                action: function () {
                    $(this).remove();
                }
            },
            confirm: {
                text: 'Si ',
                btnClass: 'btn-primary col-md-4 pull-right',
                action: function () {
                    $.post('/Controller/Deposit.php', {
                        action: 'ajax',
                        request: 'deleteDetalles',
                        id: id
                    }, function (data) {
                        if (data.response) {
                            $("#tableDeposit").find('tbody').empty().append(data.depositDetails);
                            $("#total").val(data.total);
                            $("._total").html(number_format(data.total, 2, '.', ','));

                            clearModalAddDepositDetail();
                        } else {
                            $(".flashmessenger_modal_add_deposit").html(data.mensaje);
                        }
                    }, 'json');
                }
            }
        }
    });
}



/*Priorities module */



function setDataToEditPriorities(id) {
    $.post('/Controller/Priorities.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.prioritiesData) {
                $('form[name=priorities] #' + name).val(data.prioritiesData[name]);
            }


            $('select').trigger('change');
            $('#modalAddPriorities').modal('show');
        }
    }, 'json');
}

function deletePriorities(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar esta prioridad ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/Priorities.php', {
                                    action: 'ajax',
                                    request: 'deletePriorities',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'Priorities.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}


/*Priorities Category Task */



function setDataToEditCategoryTask(id) {
    $.post('/Controller/CategoryTask.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.CategoryTaskData) {
                $('form[name=CategoryTask] #' + name).val(data.CategoryTaskData[name]);
            }


            $('select').trigger('change');
            $('#modalAddCategoryTask').modal('show');
        }
    }, 'json');
}

function deleteCategoryTask(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar esta Categoria ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/CategoryTask.php', {
                                    action: 'ajax',
                                    request: 'deleteCategoryTask',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'CategoryTask.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}
/*Priorities Category Files */



function setDataToEditCategoryFiles(id) {
    $.post('/Controller/CategoryFiles.php', {
        action: 'ajax',
        request: 'getDataToEdit',
        id: id
    }, function (data) {
        if (data.response) {
            for (var name in data.CategoryFilesData) {
                $('form[name=CategoryFiles] #' + name).val(data.CategoryFilesData[name]);
            }


            $('select').trigger('change');
            $('#modalAddCategoryFiles').modal('show');
        }
    }, 'json');
}

function deleteCategoryFiles(id) {
    var title = '';
    var content = '';
    var comfirmText = ''
    _getTranslation('Eliminar registro', function (translation) {
        title = translation;

        _getTranslation('Desea eliminar esta Categoria ?', function (translation) {
            content = translation;

            _getTranslation('Si', function (translation) {
                comfirmText = translation;

                $.confirm({
                    theme: 'material',
                    columnClass: 'col-md-6 col-md-offset-3',
                    icon: 'fa fa-trash',
                    title: title,
                    content: content,
                    buttons: {
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default col-md-4 pull-right',
                            action: function () {
                                $(this).remove();
                            }
                        },
                        confirm: {
                            text: comfirmText,
                            btnClass: 'btn-primary col-md-4 pull-right',
                            action: function () {
                                $.post('/Controller/CategoryFiles.php', {
                                    action: 'ajax',
                                    request: 'deleteCategoryFiles',
                                    id: id
                                }, function (data) {
                                    if (data.response) {
                                        document.location.href = 'CategoryFiles.php';
                                    }
                                }, 'json');
                            }
                        }
                    }
                });
                return null;
            });

        });
    });
}

/* TASK MODULE */
let _parent_task_id = "";
function saveTask() { //alert($('#nombre').val());

    var files = document.getElementById("attachement_file").files;
    var options = $('form[name=task]').serializeArray();
    
    options.push({ name: 'project_id', value: $('form[name=project] #id').val() });
    options.push({ name: 'customer_id', value: $('form[name=project] #customer_id').val() });
    options.push({ name: 'files', value: files[0] });

    

    _parent_task_id = $('form[name=task] #task_id').val();

    $.post('/Controller/Project.php', { //pos
        action: 'ajax',
        request: 'saveTask',
        options: options,
//         processData: false,  // tell jQuery not to process the data
//   contentType: false 

    }, function (data) {
        if (data.response) {
            $('.flashmessenger').html(data.message);

            $('#modalAddTask').modal('hide');
            $("#div_detailsTask").addClass('hide');
            clearForm('task');
            getTaskByProject();
            if (_parent_task_id !== "") {
                $("#a_task_name").removeClass('hide');
            }
        } else {
            $('.flashmessenger-add_task').html(data.message);
            //fadeOutAlert();
        }
    }, 'json');
}

function getTaskByProject() {
    var nameTabla = "TaskListTable";
    DestroyTable(nameTabla);
    $("#Tbody_taskList").html("");
    //UG Si falla revisar aqui las tareas hijas
    // _parent_task_id = $('form[name=task] #task_id').val();

    var options = $('form[name=project]').serializeArray();
    if (_parent_task_id == undefined) {
        _parent_task_id = "";
    }

    options.push({ name: 'parent_task_id', value: _parent_task_id });


    $.post('/Controller/Project.php', { //pos
        action: 'ajax',
        request: 'getTaskByProject',
        options: options

    }, function (data) {
        if (data.response) {

            $("#Tbody_taskList").html(data.accordionProject);

            Table(nameTabla);
        } else {
            $('.flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');



}
//UG obtener la informacion de la tarea necesaria para poder ser vista a detalle
function getTaskById(id) {
    var breadcrumb = "";

    $.post('/Controller/Project.php', {
        action: 'ajax',
        request: 'getTaskById',
        id_task: id

    }, function (data) {
        if (data.response) {

            $("#description  label").remove();
            $("#divDueDate span").remove();

            $("#divPrioritie label").remove();
            $("#divPrioritie span").remove();


            $("#div_detailsTask").removeClass('hide');

            $("#_status_name").html('Status: ' + data.taskDetail[0]['status_name']);

            //$("#_task_name").html(data.taskDetail[0]['task_name']);
            $("#_responsable").html(data.taskDetail['userName']);
            $("#_description").html(data.taskDetail[0]['description']);
            $("#_txt_due_date").html(data.taskDetail[0]['due_date']);
            //  $("#_txt_due_date").css("background-color", "#" +data.taskDetail[0]['button_due_date']);

            $("#_txt_due_date").addClass(data.taskDetail[0]['button_due_date']);

            //        
            $("#_txt_prioritie_name").html(data.taskDetail[0]['prioritie_name']);
            $("#_txt_prioritie_name").css("background-color", "#" + data.taskDetail[0]['color']);


            $("#_txt_responsable").html(data.taskDetail['initials']);
            $("#_txt_responsable").css("background-color", "#" + data.taskDetail['userColor']);

            for (var name in data.taskDetail[0]) {
                $('form[name=task] #' + name).val(data.taskDetail[0][name]);
                $('form[name=file] #' + name).val(data.taskDetail[0][name]);
            }
            if (data.taskDetail[0]['parent'] > 0) {
                $("#_showParenTask").removeClass('hide');

                $("#a_task_name").html(" / " + data.taskDetail[0]['task_name']);
                // breadcrumb = "<a class='' href='"+url_+"/Controller/Project.php?action=edit&id="+data.taskDetail[0]['project_id']+"&task="+data.taskDetail[0]['task_id']+"'>"+ data.taskDetail[0]['task_name'] +"</a>";
                // $("#_breadcrumb").append(" / " + breadcrumb);
                // if (data.taskDetail[0]['last_task'] !== "NA") {
                //     breadcrumb = "<a class='' href='" + url_ + "/Controller/Project.php?action=edit&id=" + data.taskDetail[0]['project_id'] + "&task=" + data.taskDetail[0]['parent_task_id'] + "'>" + data.taskDetail[0]['last_task'] + "</a>";
                //     $("#_breadcrumb").append(" / " + breadcrumb);
                //     $("#div_detailsTask").addClass('hide');
                // } else {
                //     breadcrumb = "<a class='' href='" + url_ + "/Controller/Project.php?action=edit&id=" +  data.taskDetail[0]['project_id'] + "&task=" + data.taskDetail[0]['task_id'] + "'>" +  data.taskDetail[0]['task_name'] + "</a>";
                //     $("#_breadcrumb").append(" / " + breadcrumb);
                //     $("#div_detailsTask").addClass('hide');
                // } UG ESTO SE DEBE DE MODIFICAR PARA QUE  NO CAUSE CONFLICTO CON EL EDIT DE LA TAREA COLOR AZUL BOTON
    

            } else {
                $("#_showParenTask").addClass('hide');
                $("#a_task_name").addClass('hide');

            }
          

            if (data.taskDetail[0]['status'] == '1' || data.taskDetail[0]['status'] == '3' || data.taskDetail[0]['status'] == '4') {
                $("._groupBtn").removeClass('hide');
            } else {
                $("._groupBtn").addClass('hide');
            }
            if (data.taskDetail[0]['status'] !== '5' ) {
                $("#btnCloseTask").removeClass('hide');
            }
            
            if (data.ulFiles) {
                $("#ul_Listfiles").html(data.ulFiles);
            }
            $("._generalButton").removeClass('hide');

            //Pintar barra de progress
            var progressTask = "<div class='progress-bar progress-bar-green' style='width: "+data.taskDetail[0]['progreso_task']+"%;color: white;text-align: center;line-height: 20px; border-radius: 10px;transition: width 0.5s;justify-content: center;'>"+data.taskDetail[0]['progreso_task']+"% </div>";
            $("#progess_task").html(progressTask);
            getCommentByTask();

        } else {
            $('.flashmessenger-add_task').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function closeTask(id) {
    $.post('/Controller/Project.php', {
        action: 'ajax',
        request: 'closeTaskById',
        id: id,
        token_form: $('form[name=project] #token_form').val(),
        status: '5' //Status = Terminado

    }, function (data) {
        if (data.response) {

            // $('.flashmessenger').html(data.message);
            //fadeOutAlert();

            //setTimeout(, 2000);
            getTaskByProject();
            getNotification();
            $("#div_detailsTask").addClass('hide');

        } else {
            $('.flashmessenger-add_task').html(data.message);
            fadeOutAlert();
        }
    }, 'json');

}
function startTask() {
    var options = $('form[name=task]').serializeArray();

    options.push({ name: 'status', value: '2' });// Status = en proceso

    $.post('/Controller/Project.php', {
        action: 'ajax',
        request: 'StartTaskById',
        options: options

    }, function (data) {
        if (data.response) {

            $('.flashmessenger').html(data.message);
            //fadeOutAlert();

            //setTimeout(, 2000);
            $("._groupBtn").addClass('hide');
            getTaskByProject();

        } else {
            $('.flashmessenger-add_task').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}

function saveComment() { //alert($('#nombre').val());
    var options = $('form[name=comment]').serializeArray();
    options.push({ name: 'task_id', value: $('form[name=task] #task_id').val() });
    $.post('/Controller/Project.php', { //pos
        action: 'ajax',
        request: 'saveComment',
        options: options

    }, function (data) {
        if (data.response) {
            $('.flashmessenger').html(data.message);
            clearForm('comment');

            $('form[name=comment] #action').val('insert');

            $('form[name=comment] #id').val('');

            $('.flashmessenger').html('');

            getCommentByTask();

        } else {
            $('.flashmessenger-add_task').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}
function getCommentByTask() {

    var options = $('form[name=task]').serializeArray();

    $.post('/Controller/Project.php', {
        action: 'ajax',
        request: 'getCommentsByIdTask',
        options: options

    }, function (data) {
        if (data.response) {
            $("#list_comment").html(data.list_comment);

        } else {
            $('.flashmessenger-add_task').html(data.message);
            // fadeOutAlert();
            $("#list_comment").html("");
        }
    }, 'json');
}

function saveFile() { //alert($('#nombre').val());
    //var options = $('form[name=file]').serializeArray();
    // var dato_archivo = $('.imagesInput').prop("files")[0];
    var dato_archivo = document.getElementById('image[]').files;
    //   options.push({ name: 'task_id', value: $('form[name=task] #task_id').val() });   
    //options.push({ name: 'file', value: dato_archivo[0] });   

    var options = new FormData();
    options.append('add', $('form[name=file]').serializeArray());
    options.append('file', dato_archivo[0]);


    $.post('/Controller/File.php', { //pos
        action: 'ajax',
        request: 'saveFile',
        options: options,
        dataType: 'json',
        contentType: false,
        processData: false,



    }, function (data) {
        if (data.response) {
            $('.flashmessenger').html(data.message);

            $('#modalAddFile').modal('hide');
            clearForm('file');
            getCommentByTask();
        } else {
            $('.flashmessenger-add_file').html(data.message);
            fadeOutAlert();
        }
    }, 'json');

    // $.ajax({
    //     url: "/Controller/File.php", 
    //     type: 'post',
    //     action: 'ajax', 
    //     options: options,
    //     processData: false,
    //     contentType: false,
    //     success: function(data) {
    //         console.log(data);
    //     }
    // });
}
function Table(table) {
    $('#' + table).DataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        /** add this */
        initComplete: function() {
            $(this.api().table().container()).find('input').parent().wrap('<form>').parent().attr('autocomplete', 'off');
        },
         /****** add this */
        "lengthMenu": [[10, 20, 30, -1], [10, 20, 30, "Todos"]],
        paginate: false,
        lengthChange: false,
        filter: false,
        buttons: [{ extend: 'excel', text: 'Descargar en excel' }]



    });
}
function Table_v2(table) {
    $('#' + table).DataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        /** add this */
        initComplete: function() {
            $(this.api().table().container()).find('input').parent().wrap('<form>').parent().attr('autocomplete', 'off');
        },
         /****** add this */
        "lengthMenu": [[10, 20, 30, -1], [10, 20, 30, "Todos"]],
        paginate: true,
        lengthChange: true,
        filter: true,
        buttons: [{ extend: 'excel', text: 'Descargar en excel' }]



    });
}
function DestroyTable(tablea) {
    var table = $('#' + tablea).DataTable();
    table.destroy();
}
function getParentTask(id) {
    _parent_task_id = id;


    $.post('/Controller/Project.php', {
        action: 'ajax',
        request: 'getTaskById',
        id_task: id

    }, function (data) {
        if (data.response) {
            $("#_showParenTask").removeClass('hide');


            _task_name = data.taskDetail[0]['task_name'];
            _project_id = data.taskDetail[0]['project_id'];


            getTaskByProject();

            breadcrumb = "<a class='' href='" + url_ + "/Controller/Project.php?action=edit&id=" + _project_id + "&task=" + data.taskDetail[0]['task_id'] + "'>" + _task_name + "</a>";
            $("#_breadcrumb").append(" / " + breadcrumb);
            $("#div_detailsTask").addClass('hide');
            //Pintar barra de progress
            var progressTask = "<div class='progress-bar progress-bar-green' style='width: "+data.taskDetail[0]['progreso_task']+"%;color: white;text-align: center;line-height: 20px; border-radius: 10px;transition: width 0.5s;justify-content: center;'>"+data.taskDetail[0]['progreso_task']+"% </div>";
            $("#progess_task").html(progressTask);
            

        } else {
            $('.flashmessenger-add_task').html(data.message);
            fadeOutAlert();
        }
    }, 'json');

}

function copyTask(){
    var options = $('form[name=task]').serializeArray();
    
    $.post('/Controller/Project.php', { //pos
        action: 'ajax',
        request: 'copyTask',
        options: options,

    }, function (data) {
        if (data.response) {
            $('.flashmessenger').html(data.message);

            $('#modalAddTask').modal('hide');
            $("#div_detailsTask").addClass('hide');
            clearForm('task');
            getTaskByProject();
            
        } else {
            $('.flashmessenger-add_task').html(data.message);            
        }
    }, 'json');
    
}
function getNotification(){
    
    
    $.post('/Controller/Project.php', {
        action: 'ajax',
        request: 'getNotification'
    
    }, function (data) {
        if (data.response) {
            $("#list_notification").html(data.list_notification);

        } else {
            $('.flashmessenger-add_task').html(data.message);
            // fadeOutAlert();
            $("#list_notification").html("");
        }
    }, 'json');
}
function readNotification(){
    $.post('/Controller/Project.php', {
        action: 'ajax',
        request: 'readNotification'    

    }, function (data) {
        if (data.response) {

            $('.flashmessenger').html(data.message);
            getNotification();           

        } else {
            $('.flashmessenger-add_task').html(data.message);
            fadeOutAlert();
        }
    }, 'json');
}
function getTaskByResponsable(options = null){
    console.log(options);
    var nameTabla = "Taskbyresponsable";
    DestroyTable(nameTabla);
    
    $("#Tbody_taskbyresponsable").html("");
    $.post('/Controller/Project.php', { 
        action: 'ajax',
        request: 'getTaskByResponsable',
        options: options,        

    }, function (data) {
        if (data.response) {

            $("#Tbody_taskbyresponsable").html(data.accordionProject);

            Table_v2(nameTabla);
        } else {
            $('.flashmessenger').html(data.message);
            fadeOutAlert();
        }
    }, 'json');

}
//No se utiliza
function savetaskAjax(){

    var files = document.getElementById("attachement_file").files;
    var options = $('form[name=task]').serializeArray();
    
    options.push({ name: 'project_id', value: $('form[name=project] #id').val() });
    options.push({ name: 'customer_id', value: $('form[name=project] #customer_id').val() });
    options.push({ name: 'files', value: files[0] });

    

    _parent_task_id = $('form[name=task] #task_id').val();


    $.ajax({
        type: 'POST',
        url: '/Controller/Project.php',
        action: 'ajax',
        request: 'saveTask',
        data: options,
        async: false,
        content: 'json',
        contentType: 'application/json',
        contentType: false,
        processData: false,
        error: function (xhr) {
            //overlay('Desbloquear');
            alertify.error("Ocurrio un error!, Verifique su conexion a internet.");
        },
        success: function (data) {
            var json = data.d.split(',');
            var result = json[0];
            if (result === 'OK') {
                localStorage.setItem("DatosEm", data.d);
                location.href = 'TPM.html';
            }
            else if (result === 'Activa') {
                $.toast({
                    heading: 'Precaucion',
                    text: 'La sesion ya se encuentra activa.',
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'warning',
                    hideAfter: 3500
                });

            }
            else if (result === 'NO') {
                $.toast({
                    heading: 'Verificar',
                    text: 'El usuario o contraseña es incorrecto!',
                    position: 'top-right',
                    loaderBg: '#ed4040',
                    icon: 'error',
                    hideAfter: 3500

                });
            }
        }
    });
}


/* END */