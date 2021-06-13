$(document).ready(function(){

    //--------------------- SELECCIONAR FOTO PRODUCTO ---------------------
    $("#foto").on("change",function(){
        var uploadFoto = document.getElementById("foto").value;
        var foto       = document.getElementById("foto").files;
        var nav = window.URL || window.webkitURL;
        var contactAlert = document.getElementById('form_alert');
        
            if(uploadFoto !='')
            {
                var type = foto[0].type;
                var name = foto[0].name;
                if(type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png')
                {
                    contactAlert.innerHTML = '<p class="errorArchivo">El archivo no es válido.</p>';                        
                    $("#img").remove();
                    $(".delPhoto").addClass('notBlock');
                    $('#foto').val('');
                    return false;
                }else{  
                        contactAlert.innerHTML='';
                        $("#img").remove();
                        $(".delPhoto").removeClass('notBlock');
                        var objeto_url = nav.createObjectURL(this.files[0]);
                        $(".prevPhoto").append("<img id='img' src="+objeto_url+">");
                        $(".upimg label").remove();
                        
                    }
              }else{
                alert("No selecciono foto");
                $("#img").remove();
              }              
    });

    $('.delPhoto').click(function(){
        $('#foto').val('');
        $(".delPhoto").addClass('notBlock');
        $("#img").remove();

        if($("#foto_actual") && $("#foto_remove")){
            $("#foto_remove").val('img_producto.png');

        }
    });

    // Agregar el evento a añadir producto

    $('.add_producto').click(function(e){
        /*acción on del evento*/
        e.preventDefault();
        var producto = $(this).attr('product');
        var action ='infoProducto';
        /*Código con ajax*/
        $.ajax({
            url:'ajax.php',
            type:'POST',
            async:true,
            data: {action:action,producto:producto},

            success: function(response){

                if(response != 'error'){
                    //convierto en objeto el formato JSON para poder acceder a los atributos
                    var info= JSON.parse(response);
                   // $('#producto_id').val(info.codproducto);
                   // $('.nameProducto').html(info.descripcion);
                   

                    $('.bodyModal').html(
                        '<form action="" method="post" name="form_add_product" id="form_add_product" onsubmit="event.preventDefault(); sendDataProduct();">'+
                            '<h1><i class="fas fa-cubes" style="font-size: 45pt;"></i><br>Agregar Producto</h1>'+
                            '<h2 class="nameProducto">'+info.descripcion+'</h2><br>'+
                            '<input type="number" name="cantidad" id="txtCantidad" placeholder="Cantidad del producto" required><br>'+
                            '<input type="text" name="precio" id="txtPrecio" placeholder="Precio del producto" required>'+
                            '<input type="hidden" name="producto_id" id="producto_id" value="'+info.codproducto+'" required>'+
                            '<input type="hidden" name="action" value="addProduct" required>'+
                            '<div class="alert alertAddProduct"></div>'+
                            '<button type="submit" class="btn_new"><i class="fas fa-plus"></i>Agregar</button>'+
                            '<a href="#" class="btn_ok closeModal" onclick="closeModal();"><i class="fas fa-ban"></i>Cerrar</a>'+   
                        '</form>');

                }
            },

            error: function(error){
                console.log(error)
            }
        });

        $('.modal').fadeIn();
    });

    // Agregar el evento de eliminar producto

    $('.del_product').click(function(e){
        /*acción on del evento*/
        e.preventDefault();
        var producto = $(this).attr('product');
        var action ='infoProducto';
        /*Código con ajax*/
        $.ajax({
            url:'ajax.php',
            type:'POST',
            async:true,
            data: {action:action,producto:producto},

            success: function(response){

                if(response != 'error'){
                    //convierto en objeto el formato JSON para poder acceder a los atributos
                    var info= JSON.parse(response);
                                   
                    $('.bodyModal').html(
                        '<form action="" method="post" name="form_del_product" id="form_del_product" onsubmit="event.preventDefault(); delProduct();">'+
                            '<h1><i class="fas fa-cubes" style="font-size: 45pt;"></i><br>Eliminar Producto</h1>'+
                            '<p>¿Está seguro de eliminar el siguiente registro?<p>'+  
                            '<h2 class="nameProducto">'+info.descripcion+'</h2><br>'+
                            '<input type="hidden" name="producto_id" id="producto_id" value="'+info.codproducto+'" required>'+
                            '<input type="hidden" name="action" value="delProduct" required>'+
                            '<div class="alert alertAddProduct"></div>'+
                            '<a href= "#" class="btn_cancel" onclick="closeModal();">Cerrar</a>'+
                            '<button type="submit" value="Eliminar" class="btn_ok">Eliminar</button>'+  
                        '</form>');

                }
            },

            error: function(error){
                console.log(error)
            }
        });

        $('.modal').fadeIn();
    });



      //Activa campos para registrar cliente
        $('.btn_new_cliente').click(function(e){
            e.preventDefault();
            $('#nom_cliente').removeAttr('disabled');
            $('#tel_cliente').removeAttr('disabled');
            $('#dir_cliente').removeAttr('disabled');

            $('#div_registro_cliente').slideDown();
        });


   //Buscar cliente
    $('#nif_cliente').keyup(function(e){
        e.preventDefault();

        var cl= $(this).val();
        var action = 'searchCliente';

        $.ajax({
            url:'ajax.php',
            type:'POST',
            async:true,
            data: {action:action,cliente:cl},

            success: function(response){
              if(response == 0){
                //limpo todos los campos si el cliente no existe
                    $('#idcliente').val('');
                    $('#nom_cliente').val('');
                    $('#tel_cliente').val('');
                    $('#dir_cliente').val('');
                    //Mostrar boton agregar
                    $('.btn_new_cliente').slideDown();
       
              }else{
                //Si el cliente existe convierto los datos en objeto
                var data= $.parseJSON(response);
                $('#idcliente').val(data.idcliente);
                $('#nom_cliente').val(data.nombre);
                $('#tel_cliente').val(data.telefono);
                $('#dir_cliente').val(data.direccion);
                //Ocultar boton agregar
                $('.btn_new_cliente').slideUp();

                //bloque campos
                 $('#nom_cliente').attr('disabled','disabled');
                 $('#tel_cliente').attr('disabled','disabled');
                 $('#dir_cliente').attr('disabled','disabled');
                 //Ocultar boton guardar
                  $('#div_registro_cliente').slideUp();
              }
            },
            error: function(error){

            }

        });

    });

    //Crear cliente desde Factura
    $('#form_new_cliente_factura').submit(function(e){
        e.preventDefault();
         $.ajax({
            url:'ajax.php',
            type:'POST',
            async:true,
            data: $('#form_new_cliente_factura').serialize(),

            success: function(response){
             if(response != 'error'){
                //agregar id a input hiden
                $('#idcliente').val(response);
                //Bloque campos
                $('#nom_cliente').attr('disabled','disabled');
                $('#tel_cliente').attr('disabled','disabled');
                $('#dir_cliente').attr('disabled','disabled');
                //Ocultar boton agregar
                $('.btn_new_cliente').slideUp();
                //Ocultar boton guardar
                $('#div_registro_cliente').slideUp();

             }
            },
            error: function(error){

            }

        });

    });

    //Buscar Producto
    $('#txt_cod_producto').keyup(function(e){
        e.preventDefault();

        var producto = $(this).val();
        var action = 'infoProducto';

        if(producto != ''){
             $.ajax({
                url:'ajax.php',
                type:'POST',
                async:true,
                data: {action:action,producto:producto},

                success: function(response){
                
                    if(response != 'Error'){
                        var info = JSON.parse(response);

                        console.log("============")
                        console.log(info)
                        console.log("============")
                        $('#txt_descripcion').html(info.descripcion);
                        $('#txt_existencia').html(info.existencia);
                        $('#txt_cant_producto').val('1');
                        $('#txt_precio').html(info.precio);
                        $('#txt_precio_total').html(info.precio);

                        //Activar cantidad
                        $('#txt_cant_producto').removeAttr('disabled');
                        //Mostrar boton agregar
                        $('#add_product_factura').slideDown();
                    }else{
                        $('#txt_descripcion').html('-');
                        $('#txt_existencia').html('-');
                        $('#txt_cant_producto').val('0');
                        $('#txt_precio').html('0.00');
                        $('#txt_precio_total').html('0.00');

                        //Bloquear cantidad
                        $('#txt_cant_producto').attr('disabled','disabled');
                        //Ocultar boton agregar
                        $('#add_product_factura').slideUp();

                    }

                },
                error: function(error){

                }

            });
        }

    });



    //Validar cantidad del producto antes de agregar
    $('#txt_cant_producto').keyup(function(e){
        e.preventDefault();

        var precio_total = $(this).val() * $('#txt_precio').html();
        var existencia = parseInt($('#txt_existencia').html());
        $('#txt_precio_total').html(precio_total);

        //Oculta el boton agregar si la cantidad es menor que 1 o si no es un número
        if( ($(this).val() < 1 || isNaN($(this).val())) || ($(this).val() > existencia) ){
            $('#add_product_venta').slideUp();
        }else{
           $('#add_product_venta').slideDown(); 
        }
    });

    //Agregar producto al detalle
    $('#add_product_venta').click(function(e){
        e.preventDefault();

        if($('#txt_cant_producto').val() > 0){
            var codproducto = $('#txt_cod_producto').val();
            var cantidad = $('#txt_cant_producto').val();
            var action = 'addProductoDetalle';
            $.ajax({
                url:'ajax.php',
                type:'POST',
                async: true,
                data: {action:action,producto:codproducto,cantidad:cantidad},

                success: function(response){
                    if(response != 'error'){
                        //Convierto los datos json q m devuelve el ajax en un objeto
                        var info = JSON.parse(response);
                        console.log("============")
                        console.log(info)
                        console.log("============")
                        $('#detalle_venta').html(info.detalle);
                        $('#detalle_totales').html(info.totales);

                        $('#txt_cod_producto').val('');
                        $('#txt_descripcion').html('-');
                        $('#txt_existencia').html('-');
                        $('#txt_cant_producto').val('0');
                        $('#txt_precio').html('0.00');
                        $('#txt_precio_total').html('0.00');  

                        //Bloquear el campo Cantidad
                        $('#txt_cant_producto').attr('disabled','disabled');

                        //Ocultar el botos Agregar
                        $('#add_product_venta').slideUp();

                    }else{
                        console.log('Error no existen datos');
                    }

                    Ocultar_btn_Procesar();

                },
                error: function(error){

                }
            });
        }
    });


    //Anular venta

$('#btn_anular_venta').click(function(e){
    e.preventDefault();

    var rows = $('#detalle_venta tr').length;
    if(rows > 0){

        var action = 'anularVenta';

         $.ajax({
                url:'ajax.php',
                type:'POST',
                async: true,
                data: {action:action},

                success: function(response){
                   if(response != 'error'){
                    //refresco la página
                    location.reload();
                   }

                },
                error: function(error){

                }
            });
    }
});


//Facturar venta

$('#btn_facturar_venta').click(function(e){
    e.preventDefault();

    var rows = $('#detalle_venta tr').length;
    if(rows > 0){

        var action = 'procesarVenta';
        var codcliente = $('#idcliente').val();

         $.ajax({
                url:'ajax.php',
                type:'POST',
                async: true,
                data: {action:action, codcliente:codcliente},

                success: function(response){
                   if(response != 'error'){
                       var info = JSON.parse(response);
                        //console.log(info);
                        generarPDF(info.codcliente,info.nofactura);
                        //refresco la página
                        location.reload();
                   }else{
                    console.log('No contiene datos');
                   }

                },
                error: function(error){

                }
            });
    }
});


    //Ver Factura

    $('.view_factura').click(function(e){
        e.preventDefault();
        var codCliente = $(this).attr('cl');
        var noFactura = $(this).attr('f');
        //console.log(codCliente,noFactura);
        generarPDF(codCliente,noFactura);
    });



});//End

function generarPDF(cliente,factura){
    var ancho = 1000;
    var alto = 800;
    //Calcular posicion x,y para centrar la ventana
    var x = parseInt((window.screen.width/2) - (ancho/2));
    var y = parseInt((window.screen.height/2) - (alto /2));

    $url = 'factura/generaFactura.php?cl='+cliente+'&f='+factura;
    window.open($url,"factura","left="+x+",top="+y+",height="+alto+",width="+ancho+",scrollbar=si,location=no, resizable=si,menubar=no");
}


//Eliminar Producto
function del_product_detalle(correlativo){
    var action = 'delProductoDetalle';
    var id_detalle = correlativo;

     $.ajax({
                url:'ajax.php',
                type:'POST',
                async: true,
                data: {action:action,id_detalle:id_detalle},

                success: function(response){
                    if(response != 'error'){
                        var info = JSON.parse(response);
                        $('#detalle_venta').html(info.detalle);
                        $('#detalle_totales').html(info.totales);

                        $('#txt_cod_producto').val('');
                        $('#txt_descripcion').html('-');
                        $('#txt_existencia').html('-');
                        $('#txt_cant_producto').val('0');
                        $('#txt_precio').html('0.00');
                        $('#txt_precio_total').html('0.00');  

                        //Bloquear el campo Cantidad
                        $('#txt_cant_producto').attr('disabled','disabled');

                        //Ocultar el botos Agregar
                        $('#add_product_venta').slideUp();

                    }else{
                        $('#detalle_venta').html('');
                        $('#detalle_totales').html('');
                    }
                     Ocultar_btn_Procesar();

                },
                error: function(error){

                }
            });

}

//Mostrar/ ocultar boton procesar
function Ocultar_btn_Procesar(){
    if($('#detalle_venta tr').length > 0){
        //muestra el boton
        $('#btn_facturar_venta').show();
    }else{
        //oculta el boton
        $('#btn_facturar_venta').hide();
    }

}


function serchForDetalle(id){
    var action = 'serchForDetalle';
    var user = id;

     $.ajax({
                url:'ajax.php',
                type:'POST',
                async: true,
                data: {action:action,user:user},

                success: function(response){
                    if(response != 'error'){
                        //Convierto los datos json q m devuelve el ajax en un objeto
                        var info = JSON.parse(response);
                        $('#detalle_venta').html(info.detalle);
                        $('#detalle_totales').html(info.totales);

                    }else{
                        console.log('Error no existen datos');
                    }
                     Ocultar_btn_Procesar();
                },
                error: function(error){

                }
            });
}


function sendDataProduct(){
   $('.alertAddProduct').html('');

      $.ajax({
            url:'ajax.php',
            type:'POST',
            async:true,
            //para enviar todos los datos del objeto(envia los inputs de forma serializada)
            data: $('#form_add_product').serialize(),

            success: function(response){
                if(response == 'Error'){
                    $('.alertAddProduct').html('<p style="color:red;">Error al agregar el producto.</p>');
                }else{
                    var info= JSON.parse(response);
                    $('.row'+info.producto_id +' .celPrecio').html(info.nuevo_precio);
                    $('.row'+info.producto_id +' .celExistencia').html(info.nueva_existencia);
                    $('#txtCantidad').val('');
                    $('#txtPrecio').val('');
                    $('.alertAddProduct').html('<p>Producto guardado correctamente.</p>');
                }
               
            },

            error: function(error){
                console.log(error)
            }
        });
}

//Eliminar producto
function delProduct(){
   var pr = $('#producto_id').val();
   $('.alertAddProduct').html('');

      $.ajax({
            url:'ajax.php',
            type:'POST',
            async:true,
            //para enviar todos los datos del objeto(envia los inputs de forma serializada)
            data: $('#form_del_product').serialize(),

            success: function(response){
                if(response == 'error'){
                    $('.alertAddProduct').html('<p style="color:red;">Error al eliminar el producto.</p>');
                }else{
                    $('.row'+pr).remove();
                    $('#form_del_product .btn_ok').remove();
                    $('.alertAddProduct').html('<p>Producto eliminado correctamente.</p>');
                }
               
            },

            error: function(error){
                console.log(error);
            }



        });
}


function closeModal(){
    $('.alertAddProduct').html('');
    $('#txtCantidad').val('');
    $('#txtPrecio').val('');
    $('.modal').fadeOut();
}
