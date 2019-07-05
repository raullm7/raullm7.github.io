<?php

add_action('wp_head', 'aniadir_scripts');

function aniadir_scripts(){
    ?>
    <script src="https://www.paypalobjects.com/api/checkout.js"></script>

    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script type="text/javascript">
        jQuery(document).ready(function($){

            var precioTotal;

            //Para que al pulsar laminado aparezcan las opciones de laminado
            var radio_plastificado = $("input[type='radio'][name='plastificado']");
            var plastificado_extras = $(".plastificado-no-hidden");

            radio_plastificado.change(function(){
                if($(this).val() == "P"){
                    plastificado_extras.fadeIn()
                }else{
                    plastificado_extras.fadeOut()
                }
            })

            /* Calcular precio */
            var preloader = $("#cargando");
            var btn_calcular = $("#calcular");
            var resultado = $("#resultado");

            btn_calcular.click(function(){

                /* Validacion */
                var tamano = $("input[name=tamano-papel]:radio:checked").val();
                var paginas = $("select[name=paginas]").val();
                var cantidad = $("select[name=cantidad]").val();
                var destino = $("select[name=destino]").val();
                var gramaje_interior = $("select[name=gramaje_interior]").val();
                var papel_interior = $("input[name=papel_interior]:radio:checked").val();
                var gramaje_cubierta = $("select[name=gramaje_cubierta]").val();
                var papel_cubierta = $("input[name=papel_cubierta]:radio:checked").val();
                var plastificado = $("input[name=plastificado]:radio:checked").val();
                var caras = $("input[name=caras]:radio:checked").val();
                var plastificado_papel = $("input[name=papel_plasti]:radio:checked").val();

                var correcto = false;

                if(tamano != undefined && paginas != 0 && cantidad != 0 && gramaje_interior != 0 && papel_interior != undefined && gramaje_cubierta != 0 && papel_cubierta != undefined && plastificado != undefined){
                    //Si es plastificado hay que validar los campos adicionales
                    if(plastificado == "P"){
                        if(caras != undefined && plastificado_papel != undefined){
                            correcto = true;
                        }else{
                            correcto = false;
                        }
                    }else{
                        correcto = true;
                    }


                    //El gramaje interior no puede ser mayor que el de la cubierta
                    if(parseInt(gramaje_interior) > parseInt(gramaje_cubierta)){
                        correcto = false;
                    }

                    //Si selecciona Plastificado/Laminado el gramaje no puede ser < 170
                    if(plastificado == "P"){
                        if(parseInt(gramaje_cubierta) < 170){
                            correcto = false;
                        }
                    }

					//El gramaje no puede ser igual o mayor de 200 si se selecciona el mismo en ambas
					if(gramaje_interior == gramaje_cubierta && parseInt(gramaje_interior) >= 200){
						correcto = false;
					}
                }


                if(correcto){ //Validacion OK
                    var y = $(window).scrollTop();
                    $(window).scrollTop(y+300);
                    preloader.fadeIn();
                    resultado.hide();

                    var papelInt = papel_interior === "M" ? "mate" : "brillo";
                    var papelExt = papel_cubierta === "M" ? "mate" : "brillo";
                    var plastificadoBool = plastificado === "P";
                    var plastificadoBrillo = plastificado_papel === "B";
                    var plastificadoDosCaras = caras === 2;

                    var presupuestosUrl = "https://presupuestosgraficasandalusi.com/presupuesto?" +
                              "dimension=" + tamano +
                              "&tirada=" + cantidad +
                              "&paginas=" + paginas +
                              "&gramajeInt=" + gramaje_interior +
                              "&gramajeExt=" + gramaje_cubierta +
                              "&papelInt=" + papelInt +
                              "&papelExt=" + papelExt +
                              "&plastificado=" + plastificadoBool +
                              "&plastificadoBrillo=" + plastificadoBrillo +
                              "&plastificadoDosCaras=" + plastificadoDosCaras +
                              "&destino=" + destino +
                              "&encuadernacion=grapado&tintas=4%2F4&beneficio=15";

                    //Ajax
                    $.get(
                        presupuestosUrl,
                        function(response){
                            preloader.hide();
                            resultado.css("display", "table");

                            var obj = response;

                            var precioFinal = obj.preciosMaquinaVieja["PRECIO"];

                            obj.normal = Math.round(precioFinal * 1.115);
                            obj.mercado = Math.round(obj.normal * 1.3);
                            obj.total = Math.round(precioFinal);
                            obj.total_con_iva = Math.round(precioFinal * 1.21);


                            $(".precio-normal").text(obj.normal+"€")
                            $(".precio-mercado").text(obj.mercado+"€")
                            $(".precio-final").text(obj.total+"€")
							$('.precio-final').append($("<span class='mas-iva'>+ IVA</span>"))

							$('.precio-con-iva').text(obj.total_con_iva+"€")

                            precioTotal = obj.total_con_iva;


							//Datalayer paso 1
							dataLayer.push(
								{
									'event': 'calcular-precio',
									'formato' : tamano,
									'paginas' : paginas,
									'cantidad' : cantidad,
									'gramaje_interior' : gramaje_interior,
									'papel_interior' : papel_interior,
									'gramaje_cubierta' : gramaje_cubierta,
									'papel_cubierta' : papel_cubierta,
									'plastificado' : plastificado,
									'caras' : caras,
									'tipo_plastificado' : plastificado_papel,
									'precio' : obj.total_con_iva+'€'
								}
							);
                        }
                    );

                }else{
                    resultado.hide();
                }

                /* Errores */
                var c_tamano = $(".div-a4, .div-a3, .div-17x24");
                var c_paginas = $("select[name=paginas]");
                var c_cantidad = $("select[name=cantidad]");
                var c_gramaje_interior = $("select[name=gramaje_interior]");
                var c_papel_interior = $("input[name=papel_interior]");
                var c_gramaje_cubierta = $("select[name=gramaje_cubierta]");
                var c_papel_cubierta = $("input[name=papel_cubierta]");
                var c_plastificado = $("input[name=plastificado]");
                var c_caras = $("input[name=caras]");
                var c_plastificado_papel = $("input[name=papel_plasti]");

                if(tamano == undefined){
                    c_tamano.attr("style", "border: 2px solid red !important");
                }else{
                    c_tamano.attr("style", "")
                }

                c_tamano.click(function(){
                    c_tamano.attr("style", "")
                })

                if(paginas == 0){
                    aplicar_error(c_paginas, "select");
                }

                if(cantidad == 0){
                    aplicar_error(c_cantidad, "select")
                }

                if(gramaje_interior == 0){
                    aplicar_error(c_gramaje_interior, "select")
                }

                if(papel_interior == undefined){
                    aplicar_error(c_papel_interior, "toggle")
                }

                if(gramaje_cubierta == 0){
                    aplicar_error(c_gramaje_cubierta, "select")
                }

                if(parseInt(gramaje_cubierta) < parseInt(gramaje_interior)){
                    var tooltip_gramaje = $("#tooltip-gramaje");
                    c_gramaje_cubierta.addClass("error-validacion");
                    c_gramaje_interior.addClass("error-validacion");

                    //tooltip
                    tooltip_gramaje.fadeIn();

                    c_gramaje_cubierta.change(function(){
                        var valor_cubierta = $(this).val()
                        var valor_interior = $("select[name=gramaje_interior]").val()

                        if(parseInt(valor_cubierta) >= parseInt(valor_interior)){
                            c_gramaje_cubierta.removeClass("error-validacion");
                            c_gramaje_interior.removeClass("error-validacion");

                            //tooltip
                            tooltip_gramaje.fadeOut();

                        }else{
                            c_gramaje_cubierta.addClass("error-validacion");
                            c_gramaje_interior.addClass("error-validacion");

                            //tooltip
                            tooltip_gramaje.fadeIn();
                        }
                    })

                    c_gramaje_interior.change(function(){
                        var valor_interior = $(this).val()
                        var valor_cubierta = $("select[name=gramaje_cubierta]").val()

                        if(parseInt(valor_cubierta) >= parseInt(valor_interior)){
                            c_gramaje_cubierta.removeClass("error-validacion");
                            c_gramaje_interior.removeClass("error-validacion");

                            //tooltip
                            tooltip_gramaje.fadeOut();
                        }else{
                            c_gramaje_cubierta.addClass("error-validacion");
                            c_gramaje_interior.addClass("error-validacion");

                            //tooltip
                            tooltip_gramaje.fadeIn();


                        }
                    })
                }

                //Si selecciona Plastificado/Laminado el gramaje no puede ser < 170
                if(plastificado == "P"){
                    if(parseInt(gramaje_cubierta) < 150){
                        var tooltip_gramaje_excesivo = $("#tooltip-gramaje-excesivo");
                        c_gramaje_cubierta.addClass("error-validacion");

                        tooltip_gramaje_excesivo.fadeIn();

                        c_gramaje_cubierta.change(function(){
                            var valor_cubierta = $(this).val();

                            if(parseInt(valor_cubierta) >= 150){
                                tooltip_gramaje_excesivo.fadeOut();
                                c_gramaje_cubierta.removeClass("error-validacion");
                            }
                        })

                        c_plastificado.change(function(){
                            if($(this).val() == "L"){
                                tooltip_gramaje_excesivo.fadeOut();
                                c_gramaje_cubierta.removeClass("error-validacion");
                            }
                        })
                    }
                }

				var tooltip_gramaje_interior = $("#tooltip-gramaje-interior");
				if(gramaje_interior == gramaje_cubierta && parseInt(gramaje_interior) >= 200){

					c_gramaje_interior.addClass("error-validacion");

					tooltip_gramaje_interior.fadeIn();
				}

				c_gramaje_interior.change(function(){
					c_gramaje_interior.removeClass("error-validacion");
					tooltip_gramaje_interior.fadeOut();
				})

                if(papel_cubierta == undefined){
                    aplicar_error(c_papel_cubierta, "toggle")
                }

                if(plastificado == undefined){
                    aplicar_error(c_plastificado, "toggle")
                }

                if(plastificado == "P"){
                    if(caras == undefined){
                        aplicar_error(c_caras, "toggle")
                    }

                    if(plastificado_papel == undefined){
                        aplicar_error(c_plastificado_papel, "toggle")
                    }
                }
            })

            //Validacion datos
            $("#confirmar_datos").click(function(e){
                var event = e || window.event();
                event.preventDefault();

                //datos facturacion
                var f_nombre = $("#f_nombre").val();
                var f_empresa = $("#f_empresa").val();
                var f_cif_dni = $("#f_cif_dni").val();
                var f_direccion = $("#f_direccion").val();
                var f_email = $("#f_email").val();
                var f_cp = $("#f_cp").val();
                var f_provincia = $("#f_provincia").val();
				var f_localidad = $("#f_localidad").val();
                var f_telefono = $("#f_telefono").val();

                //datos envio
                var e_nombre = $("#e_nombre").val();
                var e_empresa = $("#e_empresa").val();
                var e_cif_dni = $("#e_cif_dni").val();
                var e_direccion = $("#e_direccion").val();
                var e_email = $("#e_email").val();
                var e_cp = $("#e_cp").val();
                var e_provincia = $("#e_provincia").val();
				var e_localidad = $("#e_localidad").val();
                var e_telefono = $("#e_telefono").val();

                if($('#datos_igual_envio').is(':checked')){
                    //Solo hay que validar los datos de facturación

                    var correcto = false;

                    if(f_nombre.length > 2 && f_cif_dni.length == 9 && f_direccion.length > 5 && f_email.length > 5 && f_cp.length == 5 && f_provincia != -1 && f_localidad.length > 2 && f_telefono.length > 8){
                        correcto = true;
                    }

                    if(!(/(.+)@(.+){2,}\.(.+){2,}/.test(f_email))){
                      correcto = false;
                    }

                    if(!f_telefono.match("[\d +]+") && !f_telefono.match("^[0-9]{9}$")){
                        correcto = false;
						console.log("er 1");
                    }

                    /*Errores facturacion (con el checkbox sin marcar)*/

                    var cf_nombre = $("#f_nombre");
                    var cf_cif_dni = $("#f_cif_dni");
                    var cf_direccion = $("#f_direccion");
                    var cf_email = $("#f_email");
                    var cf_cp = $("#f_cp");
                    var cf_provincia = $("#f_provincia");
					var cf_localidad = $("#f_localidad");
                    var cf_telefono = $("#f_telefono");

                    if(f_nombre.length < 3){
                        aplicar_error(cf_nombre, "texto");
                    }else if(cf_nombre.hasClass("error-validacion")){
                        cf_nombre.removeClass("error-validacion")
                    }

                    if(f_cif_dni.length != 9){
                        aplicar_error(cf_cif_dni, "texto");
                        $("#tooltip-fcif_dni").fadeIn();
                    }else if(cf_cif_dni.hasClass("error-validacion")){
                        $("#tooltip-fcif_dni").fadeOut();
                        cf_cif_dni.removeClass("error-validacion")
                    }

                    if(f_direccion.length < 6){
                        aplicar_error(cf_direccion, "texto");
                    }else if(cf_direccion.hasClass("error-validacion")){
                        cf_direccion.removeClass("error-validacion")
                    }

                    if(f_email.length < 6 || !(/(.+)@(.+){2,}\.(.+){2,}/.test(f_email))){
                        aplicar_error(cf_email, "texto");
                        $("#tooltip-femail").fadeIn();
                    }else if(cf_email.hasClass("error-validacion")){
                        $("#tooltip-femail").fadeOut();
                        cf_email.removeClass("error-validacion")
                    }

                    if(f_cp.length != 5){
                        aplicar_error(cf_cp, "texto");
                    }else if(cf_cp.hasClass("error-validacion")){
                        cf_cp.removeClass("error-validacion")
                    }

                    if(f_provincia == -1){
                        aplicar_error(cf_provincia, "select");
                    }else if(cf_provincia.hasClass("error-validacion")){
                        cf_provincia.removeClass("error-validacion")
                    }

					if(f_localidad.length < 3){
                        aplicar_error(cf_localidad, "texto");
                    }else if(cf_localidad.hasClass("error-validacion")){
                        cf_localidad.removeClass("error-validacion")
                    }

                    if(f_telefono.length < 9 || (!f_telefono.match("[\d +]+") && !f_telefono.match("^[0-9]{9}$"))){
                        aplicar_error(cf_telefono, "texto");
                        $("#tooltip-ftelefono").fadeIn();
						console.log("er 2");
                    }else if(cf_telefono.hasClass("error-validacion")){
                        $("#tooltip-ftelefono").fadeOut();
                        cf_telefono.removeClass("error-validacion")
                    }

					//ult
					var privacidad_aceptada = false;
                    if($("#consiento-privacidad").is(':checked')){
                        privacidad_aceptada = true;
                    }else{
                        $("#consiento-privacidad").addClass("error");
                    }

                    var condiciones_aceptadas = false;
                    if($("#consiento-condiciones").is(':checked')){
                        condiciones_aceptadas = true;
                    }else{
                        $("#consiento-condiciones").addClass("error");
                    }

                    if(correcto && privacidad_aceptada && condiciones_aceptadas){
						//Datalayer paso 2
						dataLayer.push(
							{
								'event': 'confirmar-datos',
								'facturacion_nombre' : f_nombre,
								'facturacion_empresa' : f_empresa,
								'facturacion_cif_dni' : f_cif_dni,
								'facturacion_direccion' : f_direccion,
								'facturacion_email' : f_email,
								'facturacion_cp' : f_cp,
								'facturacion_provincia' : f_provincia,
								'facturacion_localidad' : f_localidad,
								'facturacion_telefono' : f_telefono
							}
						);
                        mostrarBotonPaypal($);
                    }

                }else{
                    //Validacion facturacion
                    var f_correcto = false;

                    if(f_nombre.length > 2 && f_cif_dni.length == 9 && f_direccion.length > 5 && f_email.length > 5 && f_cp.length == 5 && f_provincia != -1 && f_localidad.length > 2 && f_telefono.length > 8){
                        f_correcto = true;
                    }

                    if(!(/(.+)@(.+){2,}\.(.+){2,}/.test(f_email))){
                        f_correcto = false;
                    }

                    if(!f_telefono.match("[\d +]+") && !f_telefono.match("^[0-9]{9}$")){
                        f_correcto = false;
						console.log("incorrecto 1");
                    }

                    /*Errores facturacion*/

                    var cf_nombre = $("#f_nombre");
                    var cf_cif_dni = $("#f_cif_dni");
                    var cf_direccion = $("#f_direccion");
                    var cf_email = $("#f_email");
                    var cf_cp = $("#f_cp");
                    var cf_provincia = $("#f_provincia");
					var cf_localidad = $("#f_localidad");
                    var cf_telefono = $("#f_telefono");

                    if(f_nombre.length < 3){
                        aplicar_error(cf_nombre, "texto");
                    }else if(cf_nombre.hasClass("error-validacion")){
                        cf_nombre.removeClass("error-validacion")
                    }

                    if(f_cif_dni.length != 9 ){
                        aplicar_error(cf_cif_dni, "texto");
                        $("#tooltip-fcif_dni").fadeIn();
                    }else if(cf_cif_dni.hasClass("error-validacion")){
                        $("#tooltip-fcif_dni").fadeOut();
                        cf_cif_dni.removeClass("error-validacion")
                    }

                    if(f_direccion.length < 6){
                        aplicar_error(cf_direccion, "texto");
                    }else if(cf_direccion.hasClass("error-validacion")){
                        cf_direccion.removeClass("error-validacion")
                    }

                    if(f_email.length < 6 || !(/(.+)@(.+){2,}\.(.+){2,}/.test(f_email))){
                        aplicar_error(cf_email, "texto");
                        $("#tooltip-femail").fadeIn();
                    }else if(cf_email.hasClass("error-validacion")){
                        $("#tooltip-femail").fadeOut();
                        cf_email.removeClass("error-validacion")
                    }

                    if(f_cp.length != 5){
                        aplicar_error(cf_cp, "texto");
                    }else if(cf_cp.hasClass("error-validacion")){
                        cf_cp.removeClass("error-validacion")
                    }

                    if(f_provincia == -1){
                        aplicar_error(cf_provincia, "select");
                    }else if(cf_provincia.hasClass("error-validacion")){
                        cf_provincia.removeClass("error-validacion")
                    }

					if(f_localidad.length < 3){
                        aplicar_error(cf_localidad, "texto");
                    }else if(cf_localidad.hasClass("error-validacion")){
                        cf_localidad.removeClass("error-validacion")
                    }

                    if(f_telefono.length < 9 || (!f_telefono.match("[\d +]+") && !f_telefono.match("^[0-9]{9}$"))){
                        aplicar_error(cf_telefono, "texto");
                        $("#tooltip-ftelefono").fadeIn();
						console.log("er 3");
                    }else if(cf_telefono.hasClass("error-validacion")){
                        $("#tooltip-ftelefono").fadeOut();
                        cf_telefono.removeClass("error-validacion")
                    }

                    //Validacion envio
                    var e_correcto = false;

                    if(e_nombre.length > 2 && e_cif_dni.length == 9 && e_direccion.length > 5 && e_email.length > 5 && e_cp.length == 5 && e_provincia != -1 && e_localidad.length > 2 && e_telefono.length > 8){
                        e_correcto = true;
                    }

                    if(!(/(.+)@(.+){2,}\.(.+){2,}/.test(e_email))){
                        e_correcto = false;
                    }

                    if(!e_telefono.match("[\d +]+") && !e_telefono.match("^[0-9]{9}$")){
                        e_correcto = false;
                    }

					//ult
					var privacidad_aceptada = false;
                    if($("#consiento-privacidad").is(':checked')){
                        privacidad_aceptada = true;
                    }else{
                        $("#consiento-privacidad").addClass("error");
                    }

                    var condiciones_aceptadas = false;
                    if($("#consiento-condiciones").is(':checked')){
                        condiciones_aceptadas = true;
                    }else{
                       $("#consiento-condiciones").addClass("error");
                    }

                    if(e_correcto && f_correcto && privacidad_aceptada && condiciones_aceptadas){

						//Datalayer paso 2
						dataLayer.push(
							{
								'event': 'confirmar-datos',
								'envio_nombre' : e_nombre,
								'envio_empresa' : e_empresa,
								'envio_cif_dni' : e_cif_dni,
								'envio_direccion' : e_direccion,
								'envio_email' : e_email,
								'envio_cp' : e_cp,
								'envio_provincia' : e_provincia,
								'envio_localidad' : e_localidad,
								'envio_telefono' : e_telefono,
								'facturacion_nombre' : f_nombre,
								'facturacion_empresa' : f_empresa,
								'facturacion_cif_dni' : f_cif_dni,
								'facturacion_direccion' : f_direccion,
								'facturacion_email' : f_email,
								'facturacion_cp' : f_cp,
								'facturacion_provincia' : f_provincia,
								'facturacion_localidad' : f_localidad,
								'facturacion_telefono' : f_telefono,
							}
						);

                        mostrarBotonPaypal($);
                    }

                    /* Errores envio */

                    var ce_nombre = $("#e_nombre");
                    var ce_cif_dni = $("#e_cif_dni");
                    var ce_direccion = $("#e_direccion");
                    var ce_email = $("#e_email");
                    var ce_cp = $("#e_cp");
                    var ce_provincia = $("#e_provincia");
					var ce_localidad = $("#e_localidad");
                    var ce_telefono = $("#e_telefono");

                    if(e_nombre.length < 3){
                        aplicar_error(ce_nombre, "texto");
                    }else if(ce_nombre.hasClass("error-validacion")){
                        ce_nombre.removeClass("error-validacion")
                    }

                    if(e_cif_dni.length != 9){
                        aplicar_error(ce_cif_dni, "texto");
                        $("#tooltip-ecif_dni").fadeIn();
                    }else if(ce_cif_dni.hasClass("error-validacion")){
                        $("#tooltip-ecif_dni").fadeOut();
                        ce_cif_dni.removeClass("error-validacion")
                    }

                    if(e_direccion.length < 6){
                        aplicar_error(ce_direccion, "texto");
                    }else if(ce_direccion.hasClass("error-validacion")){
                        ce_direccion.removeClass("error-validacion")
                    }

                    if(e_email.length < 6){
                        aplicar_error(ce_email, "texto");
                        $("#tooltip-eemail").fadeIn();
                    }else if(ce_email.hasClass("error-validacion")){
                        $("#tooltip-eemail").fadeOut();
                        ce_email.removeClass("error-validacion")
                    }

                    if(e_cp.length != 5){
                        aplicar_error(ce_cp, "texto");
                    }else if(ce_cp.hasClass("error-validacion")){
                        ce_cp.removeClass("error-validacion")
                    }

                    if(e_provincia == -1){
                        aplicar_error(ce_provincia, "select");
                    }else if(ce_provincia.hasClass("error-validacion")){
                        ce_provincia.removeClass("error-validacion")
                    }

					if(e_localidad.length < 3){
                        aplicar_error(ce_localidad, "texto");
                    }else if(ce_localidad.hasClass("error-validacion")){
                        ce_localidad.removeClass("error-validacion")
                    }

                    if(e_telefono.length < 9){
                        aplicar_error(ce_telefono, "texto");
                        $("#tooltip-etelefono").fadeIn();
                    }else if(ce_telefono.hasClass("error-validacion")){
                        $("#tooltip-etelefono").fadeOut();
                        ce_telefono.removeClass("error-validacion")
                    }
                }
            })

            $("#encargar").click(function(){
                $("#calculadora").hide();
                $("#datos-cliente").fadeIn();

                var tamano = $("input[name=tamano-papel]:radio:checked").val();
                var paginas = $("select[name=paginas]").val();
                var cantidad = $("select[name=cantidad]").val();
                var gramaje_interior = $("select[name=gramaje_interior]").val();
                var papel_interior = $("input[name=papel_interior]:radio:checked").val();
                var gramaje_cubierta = $("select[name=gramaje_cubierta]").val();
                var papel_cubierta = $("input[name=papel_cubierta]:radio:checked").val();
                var plastificado = $("input[name=plastificado]:radio:checked").val();
                var caras = $("input[name=caras]:radio:checked").val();
                var plastificado_papel = $("input[name=papel_plasti]:radio:checked").val();

                // Render the PayPal button
                paypal.Button.render({
                    // Set your environment
                    env: 'production', // sandbox | production

                    // Specify the style of the button
                    style: {
                        label: 'checkout',
                        size:  'small',    // small | medium | large | responsive
                        shape: 'pill',     // pill | rect
                        color: 'gold'      // gold | blue | silver | black
                    },

                    // PayPal Client IDs - replace with your own
                    // Create a PayPal app: https://developer.paypal.com/developer/applications/create

                    client: {
                        //sandbox:    'Aejdorr3Iew-Zy8mcPrbkYNvHdzx1U581LZR4WcEBu7XmHbjoj_q9oRyL-el1EernukmMkte12pqAdbL',
                        production: 'AZqXFK8TTdSsTsNoNv91PlxWi66TQZxRNOXZaPMqi4YFy5ueki-u_9_Cuf5eSYANO5hlzjeLzqd0m7SS'
                    },

                    payment: function(data, actions) {
                        return actions.payment.create({
                            payment: {
                                transactions: [
                                    {
                                        amount: { total: precioTotal, currency: 'EUR' }
                                    }
                                ]
                            }
                        });
                    },

                    onAuthorize: function(data, actions) {
                        return actions.payment.execute().then(function() {

							//Datalayer paso 3
							dataLayer.push(
								{
									'event': 'compra-confirmada',
									'precio' : precioTotal
								}
							);

                            window.alert('¡Pago completado, gracias por comprar en imprimirmirevista.es!');

                            //Ajax
                            $.post(
                                'https://'+window.location.host+'/wp-admin/admin-ajax.php',
                                {
                                    'action': 'funcion_insercion_pedido',
                                    'formato' : tamano,
                                    'paginas' : paginas,
                                    'cantidad' : cantidad,
                                    'gramaje_interior' : gramaje_interior,
                                    'papel_interior' : papel_interior,
                                    'gramaje_cubierta' : gramaje_cubierta,
                                    'papel_cubierta' : papel_cubierta,
                                    'plastificado_laminado' : plastificado,
                                    'caras' : caras,
                                    'plastificado' : plastificado_papel,
                                    'f_nombre' : $("#f_nombre").val(),
                                    'f_empresa' : $("#f_empresa").val(),
                                    'f_cif_dni' : $("#f_cif_dni").val(),
                                    'f_direccion' : $("#f_direccion").val(),
                                    'f_email' : $("#f_email").val(),
                                    'f_cp' : $("#f_cp").val(),
                                    'f_provincia' : $("#f_provincia").val(),
									'f_localidad' : $("#f_localidad").val(),
                                    'f_telefono' : $("#f_telefono").val(),
                                    'f_mismos_facturacion' : $("#datos_igual_envio:checked").val(),
                                    'e_nombre' : $("#e_nombre").val(),
                                    'e_empresa' : $("#e_empresa").val(),
                                    'e_cif_dni' : $("#e_cif_dni").val(),
                                    'e_direccion' : $("#e_direccion").val(),
                                    'e_email' : $("#e_email").val(),
                                    'e_cp' : $("#e_cp").val(),
                                    'e_provincia' : $("#e_provincia").val(),
									'e_localidad' : $("#e_localidad").val(),
                                    'e_telefono' : $("#e_telefono").val(),
                                    'precio_venta' : precioTotal
                                },
                                function(response){
                                    //wetransfer y confirmacion compra
                                    console.log(response)
                                    $("#boton-wetransfer").attr("href", $("#boton-wetransfer").attr("href")+response);
                                    $("#datos-cliente").hide();
                                    $("#confirmacion-compra").fadeIn();
                                }
                            )
                        });
                    }

                }, '#paypal-button-container');
            })

            $("#datos_igual_envio").change(function(){
                $("#caja-envio").fadeToggle();
            })
        })

        function mostrarBotonPaypal($){
            $("#confirmar_datos").hide();
            $("#paypal-button-container").fadeIn();
        }

        function aplicar_error(campo, tipo_campo){

            if(tipo_campo == "select"){

                campo.addClass("error-validacion");

                campo.change(function(){
                    if(jQuery(this).value != 0){
                        jQuery(this).removeClass("error-validacion")
                    }
                })

            }

            if(tipo_campo == "toggle"){

                campo.closest(".toggle").addClass("error-validacion");

                campo.change(function(){
                    jQuery(this).closest(".toggle").removeClass("error-validacion")
                })
            }

            if(tipo_campo == "texto"){
                campo.addClass("error-validacion");
            }

        }
    </script>
    <?php
}

function calculadora_funcion($atts) {
	$salida="";
	$salida.="<div id='calculadora'>";
	$salida.="<div class='calculadora-body'>";
	//<div class="col-6">
	$salida.="<div class='caja caja-formato'>";
	$salida.="<div class='caja-header'>";
	$salida.="<h2>FORMATO Y CANTIDAD (Cerrado y vertical)</h2>";
	$salida.="</div>";
	$salida.="<div class='caja-contenido'>";
    $salida.="<input id='papel-a4' type='radio' name='tamano-papel' value='a4' class='radio-papel hidden' />";
    $salida.="<label for='papel-a4' class='label-a4'><div class='div-a4'><span>A4</span><br/><span>(210x297mm)</span></div></label>";

  	$salida.="<input id='papel-a3' type='radio' name='tamano-papel' value='a5' class='radio-papel hidden' />";
  	$salida.="<label for='papel-a3' class='label-a3'><div class='div-a3'><span>A5</span><br/><span>(148x210mm)</span></div></label>";

  	$salida.="<input id='papel-17x24' type='radio' name='tamano-papel' value='17' class='radio-papel hidden' />";
  	$salida.="<label for='papel-17x24' class='label-17x24'><div class='div-17x24'><span>17x24</span><br/><span>(170x240mm)</span></div></label>";
	$salida.="<div class='caja-mas-contenido'>";
	$salida.="<div>";
	//Nº Páginas
    $salida.="<label>PÁGINAS (incluido portada)</label>";
    $salida.="<div class='custom-select'>";
    $salida.="<select name='paginas'>";
    $salida.="<option value='0'>Elige</option>".
    "<option value='8'>8</option>".
    "<option value='12'>12</option>".
    "<option value='16'>16</option>".
    "<option value='20'>20</option>".
    "<option value='24'>24</option>".
    "<option value='28'>28</option>".
    "<option value='32'>32</option>".
    "<option value='36'>36</option>".
    "<option value='40'>40</option>".
    "<option value='44'>44</option>".
    "<option value='48'>48</option>".
    "<option value='52'>52</option>".
    "<option value='56'>56</option>".
    "<option value='60'>60</option>".
    "<option value='64'>64</option>".
    "<option value='68'>68</option>".
    "<option value='72'>72</option>".
    "<option value='76'>76</option>".
    "<option value='80'>80</option>".
    "<option value='84'>84</option>".
    "<option value='88'>88</option>".
    "</select>".
    "</div>".

    "<div>".
      "<label>CANTIDAD</label>".
      "<div class='custom-select'>".
        "<select name='cantidad'>".
        "<option value='0'>Elige</option>".
        "<option value='500'>500</option>".
        "<option value='750'>750</option>".
        "<option value='1000'>1000</option>".
        "<option value='1250'>1250</option>".
        "<option value='1500'>1500</option>".
        "<option value='1750'>1750</option>".
        "<option value='2000'>2000</option>".
        "<option value='2250'>2250</option>".
        "<option value='2500'>2500</option>".
        "<option value='2750'>2750</option>".
        "<option value='3000'>3000</option>".
        "<option value='3500'>3500</option>".
        "<option value='4000'>4000</option>".
        "<option value='4500'>4500</option>".
        "<option value='5000'>5000</option>".
        "<option value='5500'>5500</option>".
        "<option value='6000'>6000</option>".
        "<option value='6500'>6500</option>".
        "<option value='7000'>7000</option>".
        "<option value='7500'>7500</option>".
        "<option value='8000'>8000</option>".
        "<option value='8500'>8500</option>".
        "<option value='9000'>9000</option>".
        "<option value='9500'>9500</option>".
        "<option value='10000'>10000</option>".
        "<option value='11000'>11000</option>".
        "<option value='12000'>12000</option>".
        "<option value='13000'>13000</option>".
        "<option value='14000'>14000</option>".
        "<option value='15000'>15000</option>".
        "</select>".
      "</div>".
    "</div>".

    "<div>".
      "<label>DESTINO</label>".
      "<div class='custom-select'>".
        "<select name='destino'>".
        "<option value='alava'>Alava</option>".
        "<option value='albacete'>Albacete</option>".
        "<option value='alicante'>Alicante</option>".
        "<option value='almeria'>Almeria</option>".
        "<option value='avila'>Avila</option>".
        "<option value='asturias'>Asturias</option>".
        "<option value='badajoz'>Badajoz</option>".
        "<option value='barcelona'>Barcelona</option>".
        "<option value='burgos'>Burgos</option>".
        "<option value='caceres'>Caceres</option>".
        "<option value='cadiz'>Cadiz</option>".
        "<option value='castellon'>Castellon</option>".
        "<option value='ciudad real'>Ciudad real</option>".
        "<option value='cordoba'>Cordoba</option>".
        "<option value='coruna'>Coruna</option>".
        "<option value='cuenca'>Cuenca</option>".
        "<option value='girona'>Girona</option>".
        "<option value='granada'>Granada</option>".
        "<option value='guadalajara'>Guadalajara</option>".
        "<option value='guipuzcua'>Guipuzcua</option>".
        "<option value='huelva'>Huelva</option>".
        "<option value='huesca'>Huesca</option>".
        "<option value='jaen'>Jaen</option>".
        "<option value='leon'>Leon</option>".
        "<option value='lleida'>Lleida</option>".
        "<option value='logrono'>Logrono</option>".
        "<option value='lugo'>Lugo</option>".
        "<option value='madrid'>Madrid</option>".
        "<option value='malaga'>Malaga</option>".
        "<option value='murcia'>Murcia</option>".
        "<option value='navarra'>Navarra</option>".
        "<option value='ourense'>Ourense</option>".
        "<option value='palencia'>Palencia</option>".
        "<option value='pontevedra'>Pontevedra</option>".
        "<option value='salamanca'>Salamanca</option>".
        "<option value='santander'>Santander</option>".
        "<option value='segovia'>Segovia</option>".
        "<option value='sevilla'>Sevilla</option>".
        "<option value='soria'>Soria</option>".
        "<option value='tarragona'>Tarragona</option>".
        "<option value='teruel'>Teruel</option>".
        "<option value='toledo'>Toledo</option>".
        "<option value='valencia'>Valencia</option>".
        "<option value='valladolid'>Valladolid</option>".
        "<option value='vizcaya'>Vizcaya</option>".
        "<option value='zamora'>Zamora</option>".
        "<option value='zaragoza'>Zaragoza</option>".
        "<option value='mallorca'>Mallorca</option>".
        "<option value='menorca'>Menorca</option>".
        "<option value='ibiza'>Ibiza</option>".
        "<option value='andorra'>Andorra</option>".
        "<option value='portugal'>Portugal</option>".
        "<option value='formentera'>Formentera</option>".
        "</select>".
      "</div>".
    "</div>".


    "</div>".
    "</div>".
    "</div>".

    "</div>";
    //</div>

    $salida.="<div class='col-6'>".
    "<div class='col-12'>".
    "<div class='caja  caja-interior'>".
    "<div class='caja-header'>".
    "<h2>INTERIOR</h2>".
    "</div>".
    "<div class='caja-contenido'>".
    "<div class='caja-mas-contenido'>".
    "<div>".
    "<label>GRAMAJE".
	"<div class='tooltip' id='tooltip-gramaje-interior' style='display: none'>".
    "<i class='fa fa-exclamation-circle'></i> <span class='tooltiptext'>El gramaje interior y exterior no pueden ser iguales y mayores que 170 en pedidos online. Llámanos ahora para hacer el pedido</span>".
    "</div>".
	"</label>".
    "<div class='custom-select'>".
    "<select name='gramaje_interior'>".
    "<option value='0'>Elige</option>".
    "<option value='90'>90</option>".
    "<option value='100'>100</option>".
    "<option value='115'>115</option>".
    "<option value='125'>125</option>".
    "<option value='135'>135</option>".
    "<option value='150'>150</option>".
    "<option value='170'>170</option>".
    "<option value='200'>200</option>".
    "<option value='250'>250</option>".
    "<option value='300'>300</option>".
    "<option value='350'>350</option>".
    "</select>".
    "</div>".
    "</div>".
    "<div>".
    "<label>PAPEL</label>".
    "<div class='custom-select custom-radio'>".
    "<div class='toggle'>".
    "<input type='radio' name='papel_interior' value='M' id='papel_interior_mate' />".
    "<label for='papel_interior_mate'>Mate</label>".
    "<input type='radio' name='papel_interior' value='B' id='papel_interior_brillo' />".
    "<label for='papel_interior_brillo'>Brillo</label>".
    "</div>".
    "</div>".
    "</div>".
    "</div>".
    "</div>".
    "</div>".
    "</div>".
    "<div class='col-12'>".
    "<div class='caja caja-cubierta'>".
    "<div class='caja-header'>".
    "<h2>CUBIERTAS</h2>".
    "</div>".
    "<div class='caja-contenido'>".
    "<div class='caja-mas-contenido'>".
    "<div>".
    "<label>GRAMAJE".
    "<div class='tooltip' id='tooltip-gramaje' style='display: none'>".
    "<i class='fa fa-exclamation-circle'></i> <span class='tooltiptext'>El gramaje de la cubierta debe ser superior al del interior</span>".
    "</div>".
    "<div class='tooltip' id='tooltip-gramaje-excesivo' style='display: none'>".
    "<i class='fa fa-exclamation-circle'></i> <span class='tooltiptext'>Cuando la cubierta es plastificada, el gramaje no puede ser menor de 150gr</span>".
    "</div>".
    "</label>".
    "<div class='custom-select'>".
    "<select name='gramaje_cubierta'>".
    "<option value='0'>Elige</option>".
    "<option value='90'>90</option>".
    "<option value='100'>100</option>".
    "<option value='115'>115</option>".
    "<option value='125'>125</option>".
    "<option value='135'>135</option>".
    "<option value='150'>150</option>".
    "<option value='170'>170</option>".
    "<option value='200'>200</option>".
    "<option value='250'>250</option>".
    "<option value='300'>300</option>".
    "<option value='350'>350</option>".
    "</select>".
    "</div>".
    "</div>".
    "<div>".
    "<label>PAPEL</label>".
    "<div class='custom-select custom-radio'>".
    "<div class='toggle'>".
    "<input type='radio' name='papel_cubierta' value='M' id='papel_cubierta_mate' />".
    "<label for='papel_cubierta_mate'>Mate</label>".
    "<input type='radio' name='papel_cubierta' value='B' id='papel_cubierta_brillo' />".
    "<label for='papel_cubierta_brillo'>Brillo</label>".
    "</div>".
    "</div>".
    "</div>".
    "<div>".
    "<label>PLASTIFICADO/<br/>LAMINADO</label>".
    "<div class='custom-select custom-radio'>".
    "<div class='toggle'>".
    "<input type='radio' name='plastificado' value='P' id='papel_plastificado_si' />".
    "<label for='papel_plastificado_si'>Si</label>".
    "<input type='radio' name='plastificado' value='L' id='papel_plastificado_no' />".
    "<label for='papel_plastificado_no'>No</label>".
    "</div>".
    "</div>".
    "</div>".
    "<div class='plastificado-no-hidden'>".
    "<label>CARAS</label>".
    "<div class='custom-select custom-radio'>".
    "<div class='toggle'>".
    "<input type='radio' name='caras' value='1' id='caras_1' />".
    "<label for='caras_1'>1</label>".
    "<input type='radio' name='caras' value='2' id='caras_2' />".
    "<label for='caras_2'>2</label>".
    "</div>".
    "</div>".
    "<label>PLASTIFICADO</label>".
    "<div class='custom-select custom-radio'>".
    "<div class='toggle'>".
    "<input type='radio' name='papel_plasti' value='B' id='plasti_brillo' />".
    "<label for='plasti_brillo'>Brillo</label>".
    "<input type='radio' name='papel_plasti' value='M' id='plasti_mate' />".
    "<label for='plasti_mate'>Mate</label>".
    "</div></div></div></div></div></div></div></div></div>".
    "<div class='col-12'>".
    "<a id='calcular'>".
    "<span class='hide-xs av_font_icon avia_animate_when_visible avia-icon-animate  av-icon-style-  av-no-color  avia_start_animation avia_start_delayed_animation' style=''><span class='av-icon-char' style='font-size:40px;line-height:40px;' aria-hidden='true' data-av_icon='' data-av_iconfont='entypo-fontello'></span></span>".
    "<span class='txt-calcula'>¡Calcula el precio de tu revista!</span></a>".
    "<div id='cargando'></div>".
    "<div id='resultado'>".
    "<div class='col-6'>".
    "<div class='col-xs'>".
    "<p class='txt-precio-normal'>Precio normal:</p>".
    "<p class='precio-normal'></p>".
    "</div>".
    "<div class='col-xs'>".
    "<p class='txt-precio-mercado'>Valor de mercado:</p>".
    "<p class='precio-mercado'></p>".
    "</div>".
    "</div>".
    "<div class='col-6' style='vertical-align:middle'>".
    "<p class='txt-precio-final'>TU PRECIO FINAL:</p>".
    "<p class='precio-final'></p>".
	"<p class='txt-precio-final'><span class='precio-con-iva'></span> IVA incl.</p>".
    "<a href='#' class='boton-verde' id='encargar'>ENCARGAR AHORA</a>".
    "</div></div></div></div>";

    $salida.="<div id='datos-cliente' style='display:none'>".
    "<div class='caja-2 caja-formato'>".
    "<div class='caja-header'>".
    "<h2>DATOS DE FACTURACIÓN</h2>".
    "</div>".
    "<div class='caja-contenido'>".
    "<div class='colu-4'>".
    "<input type='text' name='clif-nombre' placeholder='Nombre' id='f_nombre'/>".
    "</div>".
    "<div class='colu-4'>".
    "<input type='text' name='clif-empresa' placeholder='Empresa (opcional)' id='f_empresa'/>".
    "</div>".
    "<div class='colu-4'>".
    "<div class='fieldset'>".
    "<input type='text' name='clif-cif-dni' placeholder='CIF/DNI' id='f_cif_dni'/>".
    "<div class='tooltip-input' id='tooltip-fcif_dni' style='display: none'>".
    "<i class='fa fa-exclamation-circle'></i> <span class='tooltiptext'>DNI inválido</span>".
    "</div>".
    "</div>".
    "</div>".
    "<div class='colu-4'>".
    "<input type='text' name='clif-direccion' placeholder='Dirección'  id='f_direccion'/>".
    "</div>".
    "<div class='colu-4'>".
    "<div class='fieldset'>".
    "<input type='text' name='clif-email' placeholder='E-mail'  id='f_email'/>".
    "<div class='tooltip-input' id='tooltip-femail' style='display: none'>".
    "<i class='fa fa-exclamation-circle'></i> <span class='tooltiptext'>Email inválido</span>".
    "</div>".
    "</div>".
    "</div>".
    "<div class='colu-4'>".
    "<input type='text' name='clif-cp' placeholder='C.P' id='f_cp'/>".
    "</div>".
    "<div class='colu-4'>".
    "<div class='custom-select'>".
    "<select name='clif-provincia' id='f_provincia'>".
    "<option value='-1'>Provincia</option>".
    "<option value='arava'>Arava</option>".
    "<option value='albacete'>Albacete</option>".
    "<option value='alicante'>Alicante</option>".
    "<option value='almeria'>Almeria</option>".
    "<option value='avila'>Avila</option>".
    "<option value='badajoz'>Badajoz</option>".
    "<option value='islas_baleares'>Islas Baleares</option>".
    "<option value='barcelona'>Barcelona</option>".
    "<option value='burgos'>Burgos</option>".
    "<option value='caceres'>Caceres</option>".
    "<option value='cadiz'>Cadiz</option>".
    "<option value='castellon'>Castellón</option>".
    "<option value='ciudad_real'>Ciudad Real</option>".
    "<option value='cordoba'>Cordoba</option>".
    "<option value='a_corunia'>A Coruña</option>".
    "<option value='cuenca'>Cuenca</option>".
    "<option value='girona'>Girona</option>".
    "<option value='granada'>Granada</option>".
    "<option value='guadalajara'>Guadalajara</option>".
    "<option value='guipuzkoa'>Guipuzkoa</option>".
    "<option value='huelva'>Huelva</option>".
    "<option value='huesca'>Huesca</option>".
    "<option value='jaen'>Jaen</option>".
    "<option value='leon'>Leon</option>".
    "<option value='lleida'>Lleida</option>".
    "<option value='la rioja'>La Rioja</option>".
    "<option value='lugo'>Lugo</option>".
    "<option value='madrid'>Madrid</option>".
    "<option value='malaga'>Malaga</option>".
    "<option value='murcia'>Murcia</option>".
    "<option value='navarra'>Navarra</option>".
    "<option value='ourense'>Ourense</option>".
    "<option value='asturias'>Asturias</option>".
    "<option value='palencia'>Palencia</option>".
    "<option value='las_palmas'>Las Palmas</option>".
    "<option value='pontevedra'>Pontevedra</option>".
    "<option value='salamanca'>Salamanca</option>".
    "<option value='tenerife'>Santa Cruz de Tenerife</option>".
    "<option value='cantabria'>Cantabria</option>".
    "<option value='segovia'>Segovia</option>".
    "<option value='sevilla'>Sevilla</option>".
    "<option value='soria'>Soria</option>".
    "<option value='tarragona'>Tarragona</option>".
    "<option value='teruel'>Teruel</option>".
    "<option value='toledo'>Toledo</option>".
    "<option value='valencia'>Valencia</option>".
    "<option value='valladolid'>Valladolid</option>".
    "<option value='bizkaia'>Bizkaia</option>".
    "<option value='zamora'>Zamora</option>".
    "<option value='zaragoza'>Zaragoza</option>".
    "<option value='ceuta'>Ceuta</option>".
    "<option value='melilla'>Melilla</option>".
    "</select></div></div>".
	"<div class='colu-4'>".
	"<input type='text' name='clif-localidad' placeholder='Localidad' id='f_localidad' />".
	"</div>".
    "<div class='colu-4'>".
    "<div class='fieldset'>".
    "<input type='text' name='clif-telefono' placeholder='Teléfono'  id='f_telefono'/>".
    "<div class='tooltip-input' id='tooltip-ftelefono' style='display: none'>".
    "<i class='fa fa-exclamation-circle'></i> <span class='tooltiptext'>Teléfono inválido</span>".
    "</div>".
    "</div>".
    "</div>".
    "<div class='colu-12'>".
    "<input type='checkbox' id='datos_igual_envio' /><label for='datos_igual_envio' style='font-weight:normal'>Los datos de facturación son los mismos que los de envío</label>".
    "</div>".
    "</div>".
    "</div>".
    "<div class='caja-2 caja-formato' id='caja-envio'>".
    "<div class='caja-header'>".
    "<h2>DATOS DE ENVÍO</h2>".
    "</div>".
    "<div class='caja-contenido'>".
    "<div class='colu-4'>".
    "<input type='text' name='clie-nombre' placeholder='Nombre' id='e_nombre'/>".
    "</div>".
    "<div class='colu-4'>".
    "<input type='text' name='clie-empresa' placeholder='Empresa (opcional)' id='e_empresa'/>".
    "</div>".
    "<div class='colu-4'>".
    "<div class='fieldset'>".
    "<input type='text' name='clie-cif-dni' placeholder='CIF/DNI' id='e_cif_dni'/>".
    "<div class='tooltip-input' id='tooltip-ecif_dni' style='display: none'>".
    "<i class='fa fa-exclamation-circle'></i> <span class='tooltiptext'>DNI inválido</span>".
    "</div>".
    "</div>".
    "</div>".
    "<div class='colu-4'>".
    "<input type='text' name='clie-direccion' placeholder='Dirección' id='e_direccion'/>".
    "</div>".
    "<div class='colu-4'>".
    "<div class='fieldset'>".
    "<input type='text' name='clie-email' placeholder='E-mail'  id='e_email'/>".
    "<div class='tooltip-input' id='tooltip-eemail' style='display: none'>".
    "<i class='fa fa-exclamation-circle'></i> <span class='tooltiptext'>Email inválido</span>".
    "</div>".
    "</div>".
    "</div>".
    "<div class='colu-4'>".
    "<input type='text' name='clie-cp' placeholder='C.P' id='e_cp'/>".
    "</div>".
    "<div class='colu-4'>".
    "<div class='custom-select'>".
    "<select name='clie-provincia' id='e_provincia'>".
    "<option value='-1'>Provincia</option>".
    "<option value='arava'>Arava</option>".
    "<option value='albacete'>Albacete</option>".
    "<option value='alicante'>Alicante</option>".
    "<option value='almeria'>Almeria</option>".
    "<option value='avila'>Avila</option>".
    "<option value='badajoz'>Badajoz</option>".
    "<option value='islas_baleares'>Islas Baleares</option>".
    "<option value='barcelona'>Barcelona</option>".
    "<option value='burgos'>Burgos</option>".
    "<option value='caceres'>Caceres</option>".
    "<option value='cadiz'>Cadiz</option>".
    "<option value='castellon'>Castellón</option>".
    "<option value='ciudad_real'>Ciudad Real</option>".
    "<option value='cordoba'>Cordoba</option>".
    "<option value='a_corunia'>A Coruña</option>".
    "<option value='cuenca'>Cuenca</option>".
    "<option value='girona'>Girona</option>".
    "<option value='granada'>Granada</option>".
    "<option value='guadalajara'>Guadalajara</option>".
    "<option value='guipuzkoa'>Guipuzkoa</option>".
    "<option value='huelva'>Huelva</option>".
    "<option value='huesca'>Huesca</option>".
    "<option value='jaen'>Jaen</option>".
    "<option value='leon'>Leon</option>".
    "<option value='lleida'>Lleida</option>".
    "<option value='la rioja'>La Rioja</option>".
    "<option value='lugo'>Lugo</option>".
    "<option value='madrid'>Madrid</option>".
    "<option value='malaga'>Malaga</option>".
    "<option value='murcia'>Murcia</option>".
    "<option value='navarra'>Navarra</option>".
    "<option value='ourense'>Ourense</option>".
    "<option value='asturias'>Asturias</option>".
    "<option value='palencia'>Palencia</option>".
    "<option value='las_palmas'>Las Palmas</option>".
    "<option value='pontevedra'>Pontevedra</option>".
    "<option value='salamanca'>Salamanca</option>".
    "<option value='tenerife'>Santa Cruz de Tenerife</option>".
    "<option value='cantabria'>Cantabria</option>".
    "<option value='segovia'>Segovia</option>".
    "<option value='sevilla'>Sevilla</option>".
    "<option value='soria'>Soria</option>".
    "<option value='tarragona'>Tarragona</option>".
    "<option value='teruel'>Teruel</option>".
    "<option value='toledo'>Toledo</option>".
    "<option value='valencia'>Valencia</option>".
    "<option value='valladolid'>Valladolid</option>".
    "<option value='bizkaia'>Bizkaia</option>".
    "<option value='zamora'>Zamora</option>".
    "<option value='zaragoza'>Zaragoza</option>".
    "<option value='ceuta'>Ceuta</option>".
    "<option value='melilla'>Melilla</option>".
    "</select></div></div>".
	"<div class='colu-4'>".
	"<input type='text' name='clie-localidad' placeholder='Localidad' id='e_localidad' />".
	"</div>".
    "<div class='colu-4'>".
    "<div class='fieldset'>".
    "<input type='text' name='clie-telefono' placeholder='Teléfono'  id='e_telefono'>".
    "<div class='tooltip-input' id='tooltip-etelefono' style='display: none'>".
    "<i class='fa fa-exclamation-circle'></i> <span class='tooltiptext'>Teléfono inválido</span>".
    "</div>".
    "</div>".
    "</div>".
    "</div>".
    "</div>".
	"<div class='colu-12'>".//ult
    "<input type='checkbox' id='consiento-privacidad'/> <label for='consiento-privacidad'>Consiento expresamente la <a href='/politica-privacidad/' target='_blank'>política de privacidad</a></label>".
    "</div>".
    "<div class='colu-12'>".
    "<input type='checkbox' id='consiento-condiciones'/> <label for='consiento-condiciones'>He leído  y acepto las <a href='/condiciones-generales-de-compra/' target='_blank'>Condiciones Generales de Compra</a></label>".
    "</div>".
    "<div class='colu-12'>".
    "<div id='paypal-button-container' style='display:none'></div>". //botón paypal
    "<a href='#' class='boton-verde' id='confirmar_datos'>CONFIRMAR DATOS</a>".
    "</div>".
	"</div>";

	$salida.="<div id='confirmacion-compra' style='display:none'>".
    "<div class='caja-2 caja-formato'>".
    "<div class='caja-header'>".
    "<h2>CONFIRMACIÓN DE COMPRA</h2>".
    "</div>".
    "<div class='caja-contenido'>".
    "<div class='col-12' style='padding-top:20px'>".
    "<img src='wp-content/uploads/2019/01/icono-confirmacion-compra.png' alt='Icono gracias por su compra'>".
    "</div>".
    "<div class='col-12'>".
    "<p style='padding:20px'>Hemos recibido su pedido satisfactoriamente y le hemos enviado un correo de confirmación al email indicado. Ahora solo tiene que adjuntar los archivos de su revista.</p>".
    "<a href='https://imprimirmirevista.wetransfer.com/?msg=ID%20de%20pedido:%20' id='boton-wetransfer' target='_blank' class='boton-verde'>ADJUNTE SUS ARCHIVOS AQUÍ</a>".
    "</div".
    "</div";

	return $salida;
}

add_shortcode('calculadora', 'calculadora_funcion');


/****** BACKEND *******/

/* Estilos backend */

function aniadir_scripts_backend(){
    ?>
    <style>
        .page-numbers {
        	display: inline-block;
        	padding: 5px 10px;
        	margin: 0 2px 0 0;
        	border: 1px solid #eee;
        	line-height: 1;
        	text-decoration: none;
        	border-radius: 2px;
        	font-weight: 600;
        }
        .page-numbers.current,
        a.page-numbers:hover {
        	background: #f9f9f9;
        }
    </style>
    <?php
}

add_action('admin_head', 'aniadir_scripts_backend');


/* Añadir página al menú Wordpress */

add_action('admin_menu', 'plugin_admin_add_page');

function plugin_admin_add_page() {
    //Página de magazzine_interiorexteriorcomun
    add_menu_page('Calculador de precios', 'Calculador', 'manage_options', 'tabla_interiorexterior', 'plugin_options_page'/*,  plugins_url( 'imgs/cityplugin-icon.png', __FILE__ )*/);

    //Página de magazzine_porcentajes
    add_submenu_page('tabla_interiorexterior', 'Tabla porcentajes', 'Tabla porcentajes', 'manage_options', 'tabla_porcentajes', 'funcion_porcentajes');

    //Página de magazzine_porcentajes
    add_submenu_page('tabla_interiorexterior', 'Tabla descuentos', 'Tabla descuentos', 'manage_options', 'tabla_descuentos', 'funcion_descuentos');

    //Página de pedidos
    add_submenu_page('tabla_interiorexterior', 'Ver pedidos', 'Ver pedidos', 'manage_options', 'tabla_pedidos', 'funcion_pedidos');
}

/*
* Template de la página de tabla_interiorexterior
*/
function plugin_options_page() {
    global $wpdb;
    $nombre_tabla = "magazzine_interiorexteriorcomun";
    $url = removeqsvar($_SERVER['REQUEST_URI'], "borrar");
    ?>
    <div class="wrap">
    	<h1 class="wp-heading-inline">Tabla "magazzine_interiorexteriorcomun"</h1>
        <?php
    	if(!isset($_GET['aniadir']) && !isset($_GET['borrar']) && !isset($_GET['id'])){
        	?>
            <a href="<?= $url."&aniadir" ?>" class="page-title-action">Añadir</a>
            <?php
    	}

    if(isset($_GET['borrar'])){
        $borrar_id = $_GET['borrar'];
        $borrado = $wpdb->delete($nombre_tabla ,array('id' => $borrar_id));

        if($borrado){
            echo "<div class='updated'> <p>Borrado correctamente.</p> </div>";
        }else{
            echo "<div class='error'> <p>Error al borrar, inténtelo de nuevo más tarde</p> </div>";
        }
        ?>

        <?php
    }

    if(isset($_POST['insertar'])){
        $peso_insertar = stripslashes($_POST['peso']);
        $peso_cubierta_insertar = stripslashes($_POST['peso_cubierta']);
        $formato_insertar = stripslashes($_POST['formato']);
        $tipo_papel_insertar = stripslashes($_POST['tipo_papel']);
        $paginas_insertar = stripslashes($_POST['paginas']);
        $copias_insertar = stripslashes($_POST['copias']);
        $precio_insertar = stripslashes($_POST['precio']);

        if(!empty($peso_insertar) && !empty($peso_cubierta_insertar) && !empty($formato_insertar) && !empty($tipo_papel_insertar) && !empty($paginas_insertar) && !empty($copias_insertar) && !empty($precio_insertar)){

            $aniadido = $wpdb->insert($nombre_tabla, array(
                "weight" => $peso_insertar,
                "weight_cover" => $peso_cubierta_insertar,
                "format" => $formato_insertar,
                "type_paper" => $tipo_papel_insertar,
                "pages" => $paginas_insertar,
                "copies" => $copias_insertar,
                "price" => $precio_insertar
                ),
                array(
                  '%d',
                  '%d',
                  '%d',
                  '%s',
                  '%d',
                  '%d',
                  '%s',
                )
            );

            if($aniadido){
                echo "<div class='updated'> <p>Añadido correctamente.</p> </div>";
            }else{
                echo "<div class='error'> <p>Error al añadir, inténtelo de nuevo más tarde</p> </div>";
            }


        }else{
            echo "<div class='error'> <p> Debe rellenar todos los datos para insertar el registro.</p> </div>";
        }
    }

    if(isset($_POST['editar'])){
        $peso_editar = stripslashes($_POST['peso']);
        $peso_cubierta_editar = stripslashes($_POST['peso_cubierta']);
        $formato_editar = stripslashes($_POST['formato']);
        $tipo_papel_editar = stripslashes($_POST['tipo_papel']);
        $paginas_editar = stripslashes($_POST['paginas']);
        $copias_editar = stripslashes($_POST['copias']);
        $precio_editar = stripslashes($_POST['precio']);

        if(!empty($peso_editar) && !empty($peso_cubierta_editar) && !empty($formato_editar) && !empty($tipo_papel_editar) && !empty($paginas_editar) && !empty($copias_editar) && !empty($precio_editar)){
            $modificado = $wpdb->update($nombre_tabla, array(
                "weight" => $peso_editar,
                "weight_cover" => $peso_cubierta_editar,
                "format" => $formato_editar,
                "type_paper" => $tipo_papel_editar,
                "pages" => $paginas_editar,
                "copies" => $copias_editar,
                "price" => $precio_editar
                ),
                array(
                    "id" => $_GET['id']
                )
            );

            if($modificado){
                echo "<div class='updated'> <p>Modificado correctamente.</p> </div>";
            }else{
                echo "<div class='error'> <p>Error al modificar, inténtelo de nuevo más tarde</p> </div>";
            }
        }else{
            echo "<div class='error'> <p> Debe rellenar todos los datos para modificar el campo.</p> </div>";
        }


    }

    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $result = $wpdb->get_row("SELECT * FROM $nombre_tabla WHERE id = $id", OBJECT);
        ?>

        <form action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        	<table class="form-table">
            <tbody>
                <tr>
                    <th><label for="peso">Peso:</label></th>
                    <td><input type="number" id="peso" name="peso" value="<?= (!empty($result->weight)) ? $result->weight : '' ?>" /></td>
                </tr>

                <tr>
                    <th><label for="peso_cubierta">Peso cubierta</label></th>
                    <td><input type="number" id="peso_cubierta" name="peso_cubierta" value="<?= (!empty($result->weight_cover)) ? $result->weight_cover : '' ?>"></td>
                </tr>

                <tr>
                    <th><label for="formato">Formato</label></th>
                    <td><input type="text" id="formato" name="formato" value="<?= (!empty($result->format)) ? $result->format : '' ?>"></td>
                </tr>

                <tr>
                    <th><label for="tipo_papel">Tipo de papel</label></th>
                    <td><input type="text" id="tipo_papel" name="tipo_papel" value="<?= (!empty($result->type_paper)) ? $result->type_paper : '' ?>"></td>
                </tr>

                <tr>
                    <th><label for="paginas">Páginas</label></th>
                    <td><input type="number" id="paginas" name="paginas" value="<?= (!empty($result->pages)) ? $result->pages : '' ?>"></td>
                </tr>

                <tr>
                    <th><label for="copias">Copias</label></th>
                    <td><input type="number" id="copias" name="copias" value="<?= (!empty($result->copies)) ? $result->copies : '' ?>"></td>
                </tr>

                <tr>
                    <th><label for="precio">Precio</label></th>
                    <td><input type="number" id="precio" name="precio" value="<?= (!empty($result->price)) ? $result->price : '' ?>"></td>
                </tr>
            </tbody>
        </table>
            <p class="submit"><input type="submit" value="Editar" class="button-primary" name="editar"></p>
        </form>
        <?php
    }else if(isset($_GET['aniadir'])){
         ?>
        <form action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        	<table class="form-table">
            <tbody>
                <tr>
                    <th><label for="peso">Peso:</label></th>
                    <td><input type="number" id="peso" name="peso" value="<?= (!empty($result->weight)) ? $result->weight : '' ?>" /></td>
                </tr>

                <tr>
                    <th><label for="peso_cubierta">Peso cubierta</label></th>
                    <td><input type="number" id="peso_cubierta" name="peso_cubierta" value="<?= (!empty($result->weight_cover)) ? $result->weight_cover : '' ?>"></td>
                </tr>

                <tr>
                    <th><label for="formato">Formato</label></th>
                    <td><input type="text" id="formato" name="formato" value="<?= (!empty($result->format)) ? $result->format : '' ?>"></td>
                </tr>

                <tr>
                    <th><label for="tipo_papel">Tipo de papel</label></th>
                    <td><input type="text" id="tipo_papel" name="tipo_papel" value="<?= (!empty($result->type_paper)) ? $result->type_paper : '' ?>"></td>
                </tr>

                <tr>
                    <th><label for="paginas">Páginas</label></th>
                    <td><input type="number" id="paginas" name="paginas" value="<?= (!empty($result->pages)) ? $result->pages : '' ?>"></td>
                </tr>

                <tr>
                    <th><label for="copias">Copias</label></th>
                    <td><input type="number" id="copias" name="copias" value="<?= (!empty($result->copies)) ? $result->copies : '' ?>"></td>
                </tr>

                <tr>
                    <th><label for="precio">Precio</label></th>
                    <td><input type="number" id="precio" name="precio" value="<?= (!empty($result->price)) ? $result->price : '' ?>"></td>
                </tr>
            </tbody>
        </table>
            <p class="submit"><input type="submit" value="Insertar" class="button-primary" name="insertar"></p>
        </form>
        <?php
    }else{

        $url = removeqsvar($_SERVER['REQUEST_URI'], "borrar");
	    ?>
    		<table class="wp-list-table widefat fixed striped pages">
    		    <thead>
        		    <tr>
        		        <th class="title column-title has-row-actions column-primary page-title">ID</td>
        		        <th class="title column-title has-row-actions column-primary page-title">Peso</td>
        		        <th class="title column-title has-row-actions column-primary page-title">Peso de la cubierta</td>
        		        <th class="title column-title has-row-actions column-primary page-title">Formato</td>
        		        <th class="title column-title has-row-actions column-primary page-title">Tipo de papel</td>
        		        <th class="title column-title has-row-actions column-primary page-title">Páginas</td>
        		        <th class="title column-title has-row-actions column-primary page-title">Copias</td>
        		        <th class="title column-title has-row-actions column-primary page-title">Precio</td>
        		    </tr>
    		    </thead>
    		    <tbody>
        		    <?php

        		        //Page number
                        $paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
        		        $rows_per_page = 20;
        		        $start = ($paged-1) * $rows_per_page;

        		        $consulta= "SELECT * FROM $nombre_tabla LIMIT $start, $rows_per_page";

        		        @mysqli_query("BEGIN", $wpdb->dbh);

        		        $results = @mysqli_query($wpdb->dbh, $consulta);
        		        $results_total = @mysqli_query($wpdb->dbh, "SELECT * FROM $nombre_tabla");

                        // add pagination arguments from WordPress
                        $pagination_args = array(
                            'base' => add_query_arg('paged','%#%'),
                            'format' => '',
                            'total' => ceil(mysqli_num_rows($results_total)/$rows_per_page),
                            'current' => $paged,
                            'show_all' => false,
                            'type' => 'plain',
                        );


                        $link = '&paged='.$paged;

                        while( $elemento = mysqli_fetch_assoc($results)){
                            ?>
                            <tr>
                                <td class="title column-title has-row-actions column-primary page-title">
                                    <a href='<?= $url."&id=".$elemento['id'];?> '> <?= $elemento['id']; ?> </a>
                                    <div class="row-actions">
                                        <span class="trash"><a href="<?= $url."&borrar=".$elemento['id'] ?>" class="submitdelete">Eliminar</a></span>
                                    </div>
                                </td>

                                <td class="title column-title has-row-actions column-primary page-title">
                                   <span><?= $elemento['weight']; ?></span>
                                </td>

                                <td class="title column-title has-row-actions column-primary page-title">
                                    <span><?= $elemento['weight_cover']; ?></span>
                                </td>

                                <td class="title column-title has-row-actions column-primary page-title">
                                    <span><?= $elemento['format']; ?></span>
                                </td>

                                <td class="title column-title has-row-actions column-primary page-title">
                                    <span><?= $elemento['type_paper']; ?></span>
                                </td>

                                <td class="title column-title has-row-actions column-primary page-title">
                                    <span><?= $elemento['pages']; ?></span>
                                </td>

                                <td class="title column-title has-row-actions column-primary page-title">
                                    <span><?= $elemento['copies']; ?></span>
                                </td>

                                <td class="title column-title has-row-actions column-primary page-title">
                                    <span><?= $elemento['price']." €"; ?></span>
                                </td>
                            </tr>
                            <?php
                        }

                        //Add pagination links from WordPress
                        echo paginate_links($pagination_args);
        		    ?>
    		    </tbody>
    		</table>

    	</div>
 	    <?php
    }
    ?>
    </div>
    <?php
}

/*
* Template de la página de tabla porcentajes
*/
function funcion_porcentajes(){
    global $wpdb;
    $nombre_tabla = "magazzine_porcentajes";
    $url = removeqsvar($_SERVER['REQUEST_URI'], "borrar");
    ?>
    <div class="wrap">
    	<h1 class="wp-heading-inline">Tabla "magazzine_porcentajes"</h1>
        <?php
    	if(!isset($_GET['aniadir']) && !isset($_GET['borrar']) && !isset($_GET['id'])){
        	?>
            <a href="<?= $url."&aniadir" ?>" class="page-title-action">Añadir porcentaje</a>
            <?php
    	}

    if(isset($_GET['borrar'])){
        $borrar_id = $_GET['borrar'];
        $borrado = $wpdb->delete($nombre_tabla ,array('id' => $borrar_id));

        if($borrado){
            echo "<div class='updated'> <p>Borrado correctamente.</p> </div>";
        }else{
            echo "<div class='error'> <p>Error al borrar, inténtelo de nuevo más tarde</p> </div>";
        }
        ?>

        <?php
    }

     if(isset($_POST['insertar'])){
        $porcentaje_insertar = stripslashes($_POST['porcentaje']);
        $copias_insertar = stripslashes($_POST['copias']);
        $tipo_papel_insertar = stripslashes($_POST['tipo_papel']);
        $tipo_cubierta_insertar = stripslashes($_POST['tipo_cubierta']);
        $caras_insertar = stripslashes($_POST['caras']);

        if(!empty($porcentaje_insertar) && !empty($copias_insertar) && !empty($tipo_papel_insertar) && !empty($tipo_cubierta_insertar) && !empty($caras_insertar)){
            $aniadido = $wpdb->insert($nombre_tabla, array(
                "porcentaje" => $porcentaje_insertar,
                "copies" => $copias_insertar,
                "type_paper" => $tipo_papel_insertar,
                "type_cover" => $tipo_cubierta_insertar,
                "caras" => $caras_insertar,
                ),
                array(
                  '%f',
                  '%d',
                  '%s',
                  '%s',
                  '%s'
                )
            );

            if($aniadido){
                echo "<div class='updated'> <p>Añadido correctamente.</p> </div>";
            }else{
                echo "<div class='error'> <p>Error al añadir, inténtelo de nuevo más tarde</p> </div>";
            }


        }else{
            echo "<div class='error'> <p> Debe rellenar todos los datos para insertar el registro.</p> </div>";
        }


    }

    if(isset($_POST['editar'])){
        $porcentaje_editar = stripslashes($_POST['porcentaje']);
        $copias_editar = stripslashes($_POST['copias']);
        $tipo_papel_editar = stripslashes($_POST['tipo_papel']);
        $tipo_cubierta_editar = stripslashes($_POST['tipo_cubierta']);
        $caras_editar = stripslashes($_POST['caras']);

        if(!empty($porcentaje_editar) && !empty($copias_editar) && !empty($tipo_papel_editar) && !empty($tipo_cubierta_editar) && !empty($caras_editar)){
            $modificado = $wpdb->update($nombre_tabla, array(
                "porcentaje" => $porcentaje_editar,
                "copies" => $copias_editar,
                "type_paper" => $tipo_papel_editar,
                "type_cover" => $tipo_cubierta_editar,
                "caras" => $caras_editar
                ),
                array(
                    "id" => $_GET['id']
                )
            );

            if($modificado){
                echo "<div class='updated'> <p>Modificado correctamente.</p> </div>";
            }else{
                echo "<div class='error'> <p>Error al modificar, inténtelo de nuevo más tarde</p> </div>";
            }
        }else{
            echo "<div class='error'> <p> Debe rellenar todos los datos para modificar el campo.</p> </div>";
        }


    }

    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $result = $wpdb->get_row("SELECT * FROM $nombre_tabla WHERE id = $id", OBJECT);
        ?>

        <form action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        	<table class="form-table">
            <tbody>
                <tr>
                    <th><label for="porcentaje">Porcentaje:</label></th>
                    <td><input type="text" id="porcentaje" name="porcentaje" value="<?= (!empty($result->porcentaje)) ? $result->porcentaje : '' ?>" /></td>
                </tr>

                <tr>
                    <th><label for="copias">Copias</label></th>
                    <td><input type="number" id="copias" name="copias" value="<?= (!empty($result->copies)) ? $result->copies : '' ?>"></td>
                </tr>

                <tr>
                    <th><label for="tipo_papel">Tipo papel</label></th>
                    <td><input type="text" id="tipo_papel" name="tipo_papel" value="<?= (!empty($result->type_paper)) ? $result->type_paper : '' ?>"></td>
                </tr>

                <tr>
                    <th><label for="tipo_cubierta">Tipo de cubierta</label></th>
                    <td><input type="text" id="tipo_cubierta" name="tipo_cubierta" value="<?= (!empty($result->type_cover)) ? $result->type_cover : '' ?>"></td>
                </tr>

                <tr>
                    <th><label for="caras">Caras</label></th>
                    <td><input type="number" id="caras" name="caras" value="<?= (!empty($result->caras)) ? $result->caras : '' ?>"></td>
                </tr>
            </tbody>
        </table>
            <p class="submit"><input type="submit" value="Editar" class="button-primary" name="editar"></p>
        </form>
        <?php
    }else if(isset($_GET['aniadir'])){
        ?>
        <form action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        	<table class="form-table">
            <tbody>
                <tr>
                    <th><label for="porcentaje">Porcentaje:</label></th>
                    <td><input type="text" id="porcentaje" name="porcentaje" value="<?= (!empty($result->porcentaje)) ? $result->porcentaje : '' ?>" /></td>
                </tr>

                <tr>
                    <th><label for="copias">Copias</label></th>
                    <td><input type="number" id="copias" name="copias" value="<?= (!empty($result->copies)) ? $result->copies : '' ?>"></td>
                </tr>

                <tr>
                    <th><label for="tipo_papel">Tipo papel</label></th>
                    <td><input type="text" id="tipo_papel" name="tipo_papel" value="<?= (!empty($result->type_paper)) ? $result->type_paper : '' ?>"></td>
                </tr>

                <tr>
                    <th><label for="tipo_cubierta">Tipo de cubierta</label></th>
                    <td><input type="text" id="tipo_cubierta" name="tipo_cubierta" value="<?= (!empty($result->type_cover)) ? $result->type_cover : '' ?>"></td>
                </tr>

                <tr>
                    <th><label for="caras">Caras</label></th>
                    <td><input type="number" id="caras" name="caras" value="<?= (!empty($result->caras)) ? $result->caras : '' ?>"></td>
                </tr>
            </tbody>
        </table>
            <p class="submit"><input type="submit" value="Insertar" class="button-primary" name="insertar"></p>
        </form>
        <?php
    }else{

        $url = removeqsvar($_SERVER['REQUEST_URI'], "borrar");
	    ?>
    		<table class="wp-list-table widefat fixed striped pages">
    		    <thead>
        		    <tr>
        		        <th class="title column-title has-row-actions column-primary page-title">ID</td>
        		        <th class="title column-title has-row-actions column-primary page-title">Porcentaje</td>
        		        <th class="title column-title has-row-actions column-primary page-title">Copias</td>
        		        <th class="title column-title has-row-actions column-primary page-title">Tipo de papel</td>
        		        <th class="title column-title has-row-actions column-primary page-title">Tipo de cubierta</td>
        		        <th class="title column-title has-row-actions column-primary page-title">Caras</td>
        		    </tr>
    		    </thead>
    		    <tbody>
        		    <?php
        		        //Page number
                        $paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
        		        $rows_per_page = 20;
        		        $start = ($paged-1) * $rows_per_page;

        		        $consulta= "SELECT * FROM $nombre_tabla LIMIT $start, $rows_per_page";


        		        @mysqli_query("BEGIN", $wpdb->dbh);

        		        $results = @mysqli_query($wpdb->dbh, $consulta);
        		        $results_total = @mysqli_query($wpdb->dbh, "SELECT * FROM $nombre_tabla");

                        // add pagination arguments from WordPress
                        $pagination_args = array(
                            'base' => add_query_arg('paged','%#%'),
                            'format' => '',
                            'total' => ceil(mysqli_num_rows($results_total)/$rows_per_page),
                            'current' => $paged,
                            'show_all' => false,
                            'type' => 'plain',
                        );


                        $link = '&paged='.$paged;

                        while( $elemento = mysqli_fetch_assoc($results)){
                            ?>
                            <tr>
                                <td class="title column-title has-row-actions column-primary page-title">
                                    <a href='<?= $url."&id=".$elemento['id'];?> '> <?= $elemento['id']; ?> </a>
                                    <div class="row-actions">
                                        <span class="trash"><a href="<?= $url."&borrar=".$elemento['id'] ?>" class="submitdelete">Eliminar</a></span>
                                    </div>
                                </td>

                                <td class="title column-title has-row-actions column-primary page-title">
                                   <span><?= $elemento['porcentaje']; ?></span>
                                </td>

                                <td class="title column-title has-row-actions column-primary page-title">
                                    <span><?= $elemento['copies']; ?></span>
                                </td>

                                <td class="title column-title has-row-actions column-primary page-title">
                                    <span><?= $elemento['type_paper']; ?></span>
                                </td>

                                <td class="title column-title has-row-actions column-primary page-title">
                                    <span><?= $elemento['type_cover']; ?></span>
                                </td>

                                <td class="title column-title has-row-actions column-primary page-title">
                                    <span><?= $elemento['caras']; ?></span>
                                </td>
                            </tr>
                            <?php
                        }

                        //Add pagination links from WordPress
                        echo paginate_links($pagination_args);
        		    ?>
    		    </tbody>
    		</table>

    	</div>
 	    <?php
    }
    ?>
    </div>
    <?php
}

/*
* Template de la página de tabla_descuentos
*/
function funcion_descuentos(){
    global $wpdb;
    $nombre_tabla = "magazzine_descuentos";
    $url = removeqsvar($_SERVER['REQUEST_URI'], "borrar");
    ?>
    <div class="wrap">
    	<h1 class="wp-heading-inline">Tabla "magazzine_descuentos"</h1>
    	<?php
    	if(!isset($_GET['aniadir']) && !isset($_GET['borrar']) && !isset($_GET['id'])){
        	?>
            <a href="<?= $url."&aniadir" ?>" class="page-title-action">Añadir descuento</a>
            <?php
    	}

    if(isset($_GET['borrar'])){
        $borrar_id = $_GET['borrar'];
        $borrado = $wpdb->delete($nombre_tabla ,array('id' => $borrar_id));

        if($borrado){
            echo "<div class='updated'> <p>Borrado correctamente.</p> </div>";
        }else{
            echo "<div class='error'> <p>Error al borrar, inténtelo de nuevo más tarde</p> </div>";
        }
        ?>

        <?php
    }

    if(isset($_POST['editar'])){
        $copias_editar = stripslashes($_POST['copias']);
        $descuento_editar = stripslashes($_POST['descuento']);

        if(!empty($copias_editar) && !empty($descuento_editar)){
            $modificado = $wpdb->update($nombre_tabla, array(
                "copias_hasta" => $copias_editar,
                "descuento" => $descuento_editar
                ),
                array(
                    "id" => $_GET['id']
                )
            );

            if($modificado){
                echo "<div class='updated'> <p>Modificado correctamente.</p> </div>";
            }else{
                echo "<div class='error'> <p>Error al modificar, inténtelo de nuevo más tarde</p> </div>";
            }
        }else{
            echo "<div class='error'> <p> Debe rellenar todos los datos para modificar el campo.</p> </div>";
        }
    }

    if(isset($_POST['insertar'])){
        $copias_aniadir = stripslashes($_POST['copias']);
        $descuento_aniadir = stripslashes($_POST['descuento']);

        if(!empty($copias_aniadir) && !empty($descuento_aniadir)){
            $aniadido = $wpdb->insert($nombre_tabla, array(
                "copias_hasta" => $copias_aniadir,
                "descuento" => $descuento_aniadir
                ),
                array(
                  '%d',
                  '%d'
                )
            );

            if($aniadido){
                echo "<div class='updated'> <p>Añadido correctamente.</p> </div>";
            }else{
                echo "<div class='error'> <p>Error al añadir, inténtelo de nuevo más tarde</p> </div>";
            }

             //echo $wpdb->last_query;
        }else{
            echo "<div class='error'> <p> Debe rellenar todos los datos para insertar el registro.</p> </div>";
        }


    }

    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $result = $wpdb->get_row("SELECT * FROM $nombre_tabla WHERE id = $id", OBJECT);
        ?>

        <form action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        	<table class="form-table">
            <tbody>
                <tr>
                    <th><label for="copias">Copias hasta:</label></th>
                    <td><input type="number" id="copias" name="copias" value="<?= (!empty($result->copias_hasta)) ? $result->copias_hasta : '' ?>" /></td>
                </tr>

                <tr>
                    <th><label for="descuento">Descuento</label></th>
                    <td><input type="number" id="descuento" name="descuento" value="<?= (!empty($result->descuento)) ? $result->descuento : '' ?>"></td>
                </tr>

            </tbody>
        </table>
            <p class="submit"><input type="submit" value="Editar" class="button-primary" name="editar"></p>
        </form>
        <?php
    }else if(isset($_GET['aniadir'])){
        ?>
        <form action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        	<table class="form-table">
            <tbody>
                <tr>
                    <th><label for="copias">Copias hasta:</label></th>
                    <td><input type="number" id="copias" name="copias" value="<?= (!empty($result->copias_hasta)) ? $result->copias_hasta : '' ?>" /></td>
                </tr>

                <tr>
                    <th><label for="descuento">Descuento</label></th>
                    <td><input type="number" id="descuento" name="descuento" value="<?= (!empty($result->descuento)) ? $result->descuento : '' ?>"></td>
                </tr>

            </tbody>
        </table>
            <p class="submit"><input type="submit" value="Insertar" class="button-primary" name="insertar"></p>
        </form>
        <?php
    }else{

        $url = removeqsvar($_SERVER['REQUEST_URI'], "borrar");
	    ?>
    		<table class="wp-list-table widefat fixed striped pages">
    		    <thead>
        		    <tr>
        		        <th class="title column-title has-row-actions column-primary page-title">ID</td>
        		        <th class="title column-title has-row-actions column-primary page-title">Máx copias</td>
        		        <th class="title column-title has-row-actions column-primary page-title">Descuento</td>
        		    </tr>
    		    </thead>
    		    <tbody>
        		    <?php

        		        //Page number
                        $paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
        		        $rows_per_page = 20;
        		        $start = ($paged-1) * $rows_per_page;

        		        $consulta= "SELECT * FROM $nombre_tabla LIMIT $start, $rows_per_page";


        		        @mysqli_query("BEGIN", $wpdb->dbh);

        		        $results = @mysqli_query($wpdb->dbh, $consulta);
        		        $results_total = @mysqli_query($wpdb->dbh, "SELECT * FROM $nombre_tabla");

                        // add pagination arguments from WordPress
                        $pagination_args = array(
                            'base' => add_query_arg('paged','%#%'),
                            'format' => '',
                            'total' => ceil(mysqli_num_rows($results_total)/$rows_per_page),
                            'current' => $paged,
                            'show_all' => false,
                            'type' => 'plain',
                        );


                        $link = '&paged='.$paged;

                        while( $elemento = mysqli_fetch_assoc($results)){
                            ?>
                            <tr>
                                <td class="title column-title has-row-actions column-primary page-title">
                                    <a href='<?= $url."&id=".$elemento['id'];?> '> <?= $elemento['id']; ?> </a>
                                    <div class="row-actions">
                                        <span class="trash"><a href="<?= $url."&borrar=".$elemento['id'] ?>" class="submitdelete">Eliminar</a></span>
                                    </div>
                                </td>

                                <td class="title column-title has-row-actions column-primary page-title">
                                   <span><?= $elemento['copias_hasta']; ?></span>
                                </td>

                                <td class="title column-title has-row-actions column-primary page-title">
                                    <span><?= $elemento['descuento']; ?></span>
                                </td>
                            </tr>
                            <?php
                        }

                        //Add pagination links from WordPress
                        echo paginate_links($pagination_args);
        		    ?>
    		    </tbody>
    		</table>

    	</div>
 	    <?php
    }
    ?>
    </div>
    <?php
}

/*
* Template de la página de tabla_pedidos
*/
function funcion_pedidos(){
    global $wpdb;
    $nombre_tabla = "magazzine_pedidos";
    $url = removeqsvar($_SERVER['REQUEST_URI'], "borrar");
    ?>
    <div class="wrap">
    	<h1 class="wp-heading-inline">Tabla "magazzine_pedidos"</h1>

            <?php


    if(isset($_GET['borrar'])){
        $borrar_id = $_GET['borrar'];
        $borrado = $wpdb->delete($nombre_tabla ,array('id' => $borrar_id));

        if($borrado){
            echo "<div class='updated'> <p>Borrado correctamente.</p> </div>";
        }else{
            echo "<div class='error'> <p>Error al borrar, inténtelo de nuevo más tarde</p> </div>";
        }
        ?>

        <?php
    }

    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $result = $wpdb->get_row("SELECT * FROM $nombre_tabla WHERE id = $id", OBJECT);

        //we check if the page is visited by click on the tabs or on the menu button.
        //then we get the active tab.
        $active_tab = "datos-usuario";
        if(isset($_GET["tab"]))
        {
            if($_GET["tab"] == "datos-usuario")
            {
                $active_tab = "datos-usuario";
            }
            else
            {
                $active_tab = "datos-revista";
            }
        }
        ?>
        <h2 class="nav-tab-wrapper">
            <!-- when tab buttons are clicked we jump back to the same page but with a new parameter that represents the clicked tab. accordingly we make it active -->
            <a href="?page=tabla_pedidos&id=<?= $id ?>&tab=datos-usuario" class="nav-tab <?php if($active_tab == 'datos-usuario'){echo 'nav-tab-active';} ?> ">Datos cliente</a>
            <a href="?page=tabla_pedidos&id=<?= $id ?>&tab=datos-revista" class="nav-tab <?php if($active_tab == 'datos-revista'){echo 'nav-tab-active';} ?>">Datos revista</a>
        </h2>

        <?php
        if($active_tab == "datos-usuario"){
            //DATOS DEL USUARIO
            ?>
            <h3>Datos de facturación</h3>
            <ul>
                <li><strong>Nombre:</strong> <?= $result->f_nombre; ?></li>
                <li><strong>Empresa:</strong> <?= (!empty($result->f_empresa)) ? $result->f_empresa : "(No indicado)"; ?></li>
                <li><strong>CIF/DNI:</strong> <?= $result->f_cif_dni; ?></li>
                <li><strong>Dirección:</strong> <?= $result->f_direccion; ?></li>
                <li><strong>Email:</strong> <?= $result->f_email; ?></li>
                <li><strong>CP:</strong> <?= $result->f_cp; ?></li>
                <li><strong>Provincia:</strong> <?= $result->f_provincia; ?></li>
				<li><strong>Localidad:</strong> <?= $result->f_localidad; ?></li>
                <li><strong>Teléfono:</strong> <?= $result->f_telefono; ?></li>
            </ul>

            <h3>Datos de envío</h3>
            <?php
            if($result->mismos_facturacion == 0){
                ?>
                <ul>
                    <li><strong>Nombre:</strong> <?= $result->e_nombre; ?></li>
                    <li><strong>Empresa:</strong> <?= (!empty($result->e_empresa)) ? $result->e_empresa : "(No indicado)"; ?></li>
                    <li><strong>CIF/DNI:</strong> <?= $result->e_cif_dni; ?></li>
                    <li><strong>Dirección:</strong> <?= $result->e_direccion; ?></li>
                    <li><strong>Email:</strong> <?= $result->e_email; ?></li>
                    <li><strong>CP:</strong> <?= $result->e_cp; ?></li>
                    <li><strong>Provincia:</strong> <?= $result->e_provincia; ?></li>
					<li><strong>Localidad:</strong> <?= $result->e_localidad; ?></li>
                    <li><strong>Teléfono:</strong> <?= $result->e_telefono; ?></li>
                </ul>
                <?php
            }else{
                echo "<strong>Los datos de envío son los mismos que de facturación</strong>";
            }
        }else if($active_tab == "datos-revista"){
            //DATOS DE LA REVISTA

            ?>
            <table cellpadding="5px" style="margin: 15px;width:100%">
                <tr>
                    <td style="font-size: 17px"><strong>Formato: </strong><?= $result->formato ?></td>
                    <td style="font-size: 17px"><strong>Páginas: </strong><?= $result->paginas ?></td>
                </tr>
                <tr>
                    <td style="font-size: 17px"><strong>Cantidad: </strong><?= $result->cantidad ?></td>
                    <td style="font-size: 17px"><strong>Gramaje interior: </strong><?= $result->gramaje_interior ?></td>
                </tr>
                <tr>
                    <td style="font-size: 17px"><strong>Papel interior: </strong><?= $result->papel_interior ?></td>
                    <td style="font-size: 17px"><strong>Gramaje cubiertas: </strong><?= $result->gramaje_cubierta ?></td>
                </tr>
                <tr>
                    <td style="font-size: 17px"><strong>Papel cubiertas: </strong><?= $result->papel_cubierta ?></td>
                    <td style="font-size: 17px"><strong>Plastificado/Laminado: </strong><?= $result->plastificado_laminado ?></td>
                </tr>
                <tr>
                    <td style="font-size: 17px"><strong>Caras: </strong><?= $result->caras ?></td>
                    <td style="font-size: 17px"><strong>Plastificado: </strong><?= $result->plastificado ?></td>
                </tr>
                <tr>
                    <td style="font-size: 17px;"><strong>Precio:</strong> <?= $result->precio_venta ?> €</td>
                </tr>
            </table>

            <?php
        }
        ?>

        <?php
    }else{

        $url = removeqsvar($_SERVER['REQUEST_URI'], "borrar");
	    ?>
    		<table class="wp-list-table widefat fixed striped pages">
    		    <thead>
        		    <tr>
        		        <th class="title column-title has-row-actions column-primary page-title">ID</td>
        		        <th class="title column-title has-row-actions column-primary page-title">Cliente</td>
        		        <th class="title column-title has-row-actions column-primary page-title">Email</td>
        		        <th class="title column-title has-row-actions column-primary page-title">Total</td>
        		        <th class="title column-title has-row-actions column-primary page-title">Fecha</td>
        		    </tr>
    		    </thead>
    		    <tbody>
        		    <?php

        		        //Page number
                        $paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
        		        $rows_per_page = 20;
        		        $start = ($paged-1) * $rows_per_page;

        		        $consulta= "SELECT * FROM $nombre_tabla LIMIT $start, $rows_per_page";


        		        @mysqli_query("BEGIN", $wpdb->dbh);

        		        $results = @mysqli_query($wpdb->dbh, $consulta);
        		        $results_total = @mysqli_query($wpdb->dbh, "SELECT * FROM $nombre_tabla");

                        // add pagination arguments from WordPress
                        $pagination_args = array(
                            'base' => add_query_arg('paged','%#%'),
                            'format' => '',
                            'total' => ceil(mysqli_num_rows($results_total)/$rows_per_page),
                            'current' => $paged,
                            'show_all' => false,
                            'type' => 'plain',
                        );

                        $link = '&paged='.$paged;

                        while( $elemento = mysqli_fetch_assoc($results)){
                            ?>
                            <tr>

                                <td class="title column-title has-row-actions column-primary page-title">
                                    <a href='<?= $url."&id=".$elemento['id'];?> '> <?= $elemento['id']; ?> (Ver detalles) </a>
                                    <!--<div class="row-actions">
                                        <span class="trash"><a href="<?= $url."&borrar=".$elemento['id'] ?>" class="submitdelete">Eliminar</a></span>
                                    </div>-->
                                </td>

                                <td class="title column-title has-row-actions column-primary page-title">
                                   <span><?= $elemento['f_nombre']; ?></span>
                                </td>
                                <td class="title column-title has-row-actions column-primary page-title">
                                   <span><?= $elemento['f_email']; ?></span>
                                </td>
                                <td class="title column-title has-row-actions column-primary page-title">
                                   <span><?= $elemento['precio_venta']; ?></span>
                                </td>
                                <td class="title column-title has-row-actions column-primary page-title">
                                   <span><?= $elemento['fecha_venta']; ?></span>
                                </td>

                            </tr>
                            <?php
                        }

                        //Add pagination links from WordPress
                        echo paginate_links($pagination_args);
        		    ?>
    		    </tbody>
    		</table>

    	</div>
 	    <?php
    }
    ?>
    </div>
    <?php
}

/* Eliminar parámetro de URL */
function removeqsvar($url, $varname) {
    list($urlpart, $qspart) = array_pad(explode('?', $url), 2, '');
    parse_str($qspart, $qsvars);
    unset($qsvars[$varname]);
    $newqs = http_build_query($qsvars);
    return $urlpart . '?' . $newqs;
}

/* Ajax */

add_action( 'wp_ajax_funcion_insercion_pedido', 'funcion_insercion_pedido' );
add_action( 'wp_ajax_nopriv_funcion_insercion_pedido', 'funcion_insercion_pedido' );

function funcion_insercion_pedido(){
    global $wpdb;

    //revista
    $formato = $_POST['formato'];
    $paginas = $_POST['paginas'];
    $cantidad = $_POST['cantidad'];
    $gramaje_interior = $_POST['gramaje_interior'];
    $papel_interior = $_POST['papel_interior'];
    $gramaje_cubierta = $_POST['gramaje_cubierta'];
    $papel_cubierta = $_POST['papel_cubierta'];
    $plastificado_laminado = $_POST['plastificado_laminado'];
    $caras = $_POST['caras'];
    $plastificado = $_POST['plastificado'];

    //datos facturacion
    $f_nombre = $_POST['f_nombre'];
    $f_empresa = $_POST['f_empresa'];
    $f_cif_dni = $_POST['f_cif_dni'];
    $f_direccion = $_POST['f_direccion'];
    $f_email = $_POST['f_email'];
    $f_cp = $_POST['f_cp'];
    $f_provincia = $_POST['f_provincia'];
	$f_localidad = $_POST['f_localidad'];
    $f_telefono = $_POST['f_telefono'];
    $datos_mismos = $_POST['f_mismos_facturacion'];

    //datos envio
    $e_nombre = $_POST['e_nombre'];
    $e_empresa = $_POST['e_empresa'];
    $e_cif_dni = $_POST['e_cif_dni'];
    $e_direccion = $_POST['e_direccion'];
    $e_email = $_POST['e_email'];
    $e_cp = $_POST['e_cp'];
    $e_provincia = $_POST['e_provincia'];
	$e_localidad= $_POST['e_localidad'];
    $e_telefono = $_POST['e_telefono'];

    //venta
    $precio_venta = $_POST['precio_venta'];
    $fecha_venta = date('Y-m-d');

    /* ENVÍO DEL CORREO */

    //variables para el correo

    $em_formato; //formato papel

    if($formato == "a3"){
        $em_formato = "A5";
    }else if($formato == "17"){
        $em_formato = "17x24";
    }else if($formato == "a4"){
        $em_formato = "a4";
    }

    $em_papel_interior;
    if($papel_interior == "M"){
        $em_papel_interior = "Mate";
    }else if($papel_interior == "B"){
        $em_papel_interior = "Brillo";
    }

    $em_papel_cubierta;
    if($papel_cubierta == "M"){
        $em_papel_cubierta = "Mate";
    }else if($papel_cubierta == "B"){
        $em_papel_cubierta = "Brillo";
    }

    $em_plastificado_laminado;
    if($plastificado_laminado == "P"){
        $em_plastificado_laminado = "Si";
    }else if($plastificado_laminado == "L"){
        $em_plastificado_laminado = "No";
    }

    $em_plastificado;
    if($plastificado == "M"){
        $em_plastificado = "Mate";
    }else if($plastificado == "B"){
        $em_plastificado = "Brillo";
    }

    /* CORREO CLIENTE */

    $to = $f_email;
    $subject = '¡Tu compra en Imprimir mi Revista ha sido realizada!';

    $body="<table border='0' cellspacing='0' cellpadding='5' style='width:100%'>";
    $body.="<tr><td style='text-align: center' colspan='4'><img src='https://imprimirmirevista.es/wp-content/uploads/2018/12/logo-imprimir-mirevista.png' alt='Logo Imprimir mi Revista' width='200' /></td></tr>";
    $body.="<tr><td style='text-align: center;' colspan='4'><h1 style='font-size: 28px'>¡Tu compra se ha completado satisfactoriamente!</h1></td></tr>";
    $body.="<tr><td style='padding:0;text-align: center;padding-bottom: 20px' colspan='4'><p>Hola <b>$f_nombre</b>, gracias por completar tu compra en <a href='https://imprimirmirevista.es/'>Imprimir mi Revista</a>. Tu revista llegará al domicilio indicado en aproximadamente 4 días. <b>Resumen de tu pedido:</b></p></td></tr>";
    $body.="<tr><th style='text-align: left'>Formato:</th><td>$em_formato</td> <th style='text-align: left'>Páginas:</th><td>$paginas</td></tr>";
    $body.="<tr><th style='text-align: left'>Cantidad:</th><td>$cantidad</td> <th style='text-align: left'>Gramaje Interior:</th><td>$gramaje_interior</td></tr>";
    $body.="<tr><th style='text-align: left'>Papel interior:</th><td>$em_papel_interior</td> <th style='text-align: left'>Gramaje cubiertas:</th><td>$gramaje_cubierta</td></tr>";
    $body.="<tr><th style='text-align: left'>Papel cubierta:</th><td>$em_papel_cubierta</td> <th style='text-align: left'>Plastificado:</th><td>$em_plastificado_laminado</td></tr>";

    if($plastificado_laminado == "P"){
        $body.="<tr><th style='text-align: left'>Caras de la cubierta:</th><td>$caras</td> <th style='text-align: left'>Plastificado de la cubierta:</th><td>$em_plastificado</td></tr>";
    }

	$posicion = strpos($precio_venta, ',');
    if($posicion){
		$precio_venta = str_replace(',', '', $precio_venta);
	}

	$precio_sin_iva = $precio_venta / 1.21;

    $body.="<tr><th colspan='4' style='padding-top: 30px'><h2 style='font-size: 27px'>Precio sin IVA:</h2></th></tr>";
    $body.="<tr><th colspan='4' style='font-size: 31px'>".number_format($precio_sin_iva, 2)." €</th></tr>";

    $body.="<tr><th colspan='4' style='padding-top: 30px'><h2 style='font-size: 30px'>Precio total:</h2></th></tr>";
    $body.="<tr><th colspan='4' style='font-size: 35px'>".number_format($precio_venta, 2)."€</th></tr>";
	//ult
	$body.="<tr><td style='padding:0;text-align: center;padding-top: 20px' colspan='4'><p>Conforme a la normativa vigente en materia de Protección de Datos, como contenido anexo, se le detalla la Política de Privacidad consentida por usted al enviar dicho Formulario y los datos introducidos y como archivo adjunto recibe las Condiciones de Generales de Compra aceptadas. Gracias por su atención.</p></td></tr>";
    $body.="<tr><td style='padding:0;text-align: center;padding-top: 20px' colspan='4'><h6>“En imprimirmirevista.es tratamos la información que nos facilita según el Reglamento General de Protección de Datos y Ley Orgánica de Protección de Datos Personales y Garantía de Derechos Digitales, con el fin de gestionar los servicios o productos que le prestamos o nos demanda mediante la página Web, todos los datos solicitados (nombre, e-mail, y aquellos incluidos en la consulta) son estrictamente necesarios para la gestión de las consultas formuladas y/o para la compra del artículo, incluyendo la existencia de posibles decisiones automatizadas, no elaborando perfiles en ningún caso. Sus datos personales van a ser almacenados en el Registro de Actividades de Tratamiento: Formularios de Contacto Web y/o Clientes. La finalidad de dicho tratamiento es la gestión de las consultas recibidas a través de los formularios de contacto de la web por parte de los usuarios y/o para la gestión del servicio prestado a nuestros clientes, así como la facturación de los mismos. Los datos proporcionados se conservarán durante el tiempo necesario para la tramitación y respuesta de la consulta o durante los años necesarios para cumplir con las obligaciones legales. Los datos no se cederán a terceros salvo en los casos en que exista una obligación legal y para la prestación de los servicios ofertados, así haya que hacerlo y no están previstas las transferencias internacionales de datos.
	Los usuarios menores de 14 años necesitan el consentimiento de sus padres, tutores o representantes legales para la realización de la consulta a través del formulario. El usuario reconoce ser mayor de 14 años de edad o disponer del correspondiente consentimiento de sus padres, tutores o representantes legales para formular la consulta.
	Igualmente se informa de la posibilidad del acceso a la información propia del Usuario por parte de los distintos prestadores de servicios del Responsable, garantizando el máximo nivel de confidencialidad ya regulado. (La empresa dispone de toda la información relativa a estas empresas y/o profesionales para que pueda ser consultada previa solicitud).
	Así mismo, nos ajustamos a las condiciones que estable la “Ley 14/1966, de 18 de marzo, de Prensa e Imprenta”, y “Resolución de 8 de enero de 2018, de la Dirección General de Empleo, por la que se registra y publica el Convenio colectivo estatal de artes gráficas, manipulados de papel, manipulados de cartón, editoriales e industrias auxiliares 2017-2018”.
 	Usted tiene derecho a obtener confirmación sobre si en Etiquetas Alhambra, S.L. estamos tratando sus datos personales,  por tanto tiene derecho a acceder a sus datos personales, rectificar los datos inexactos o solicitar su supresión cuando los datos ya no sean necesarios, así como el derecho a la limitación u oposición a su tratamiento, portabilidad de sus datos y retirar el consentimiento aceptado al tratamiento de sus datos e incluso interponer reclamación ante la Agencia Española de Protección de Datos .

	En lo referente a comunicaciones comerciales, Etiquetas Alhambra, S.L. se compromete a NO REMITIR COMUNICACIONES COMERCIALES SIN IDENTIFICARLAS COMO TALES, conforme a lo dispuesto en la Ley 34/2002 de Servicios de la Sociedad de la Información y de Comercio Electrónico. No será considerado como comunicación comercial toda la información que se envíe al Usuario del portal de Etiquetas Alhambra, S.L.  siempre que tenga por finalidad el mantenimiento de la relación contractual existente entre usuario y Etiquetas Alhambra, S.L., así como el desempeño de las tareas de información, formación y otras actividades propias del servicio que el cliente/usuario pueda contratado con la Entidad.
	Etiquetas Alhambra, S.L.,se compromete a través de este medio a NO REALIZAR PUBLICIDAD ENGAÑOSA. A estos efectos, por lo tanto, no serán considerados como publicidad engañosa los errores formales o numéricos que puedan encontrarse a lo largo del contenido de las distintas secciones de la web de Etiquetas Alhambra, S.L., producidos como consecuencia de un mantenimiento y/o actualización incompleta o defectuosa de la información contenida es estas secciones. Etiquetas Alhambra, S.L., como consecuencia de lo dispuesto en este apartado, se compromete a corregirlo tan pronto como tenga conocimiento de dichos errores.
Responsable del Tratamiento:
Etiquetas Alhambra, S.L.
Camino Nuevo de Peligros, s/n.18210.Peligros. Granada. Tlf.- 958405655. E-mail: administracion@graficasandalusi.com
Protección de Datos: Etiquetas Alhambra, S.L., le informa que los datos de contacto recogidos en esta comunicación, han sido recabados de nuestro  Registro de Actividades, en concreto del Tratamiento, Formularios de Contacto Web y/o Clientes o facilitados voluntariamente por usted con la finalidad de poder llevar a cabo  las comunicaciones de índole comercial y/o informativas que puedan ser de su interés, quedando por tanto, informado de la posibilidad de usar sus datos con fines comerciales. Igualmente le informamos a los afectados que podrán ejercitar ante el Responsable del Tratamiento o ante su Delegado de Protección de Datos, los derechos de acceso, rectificación, cancelación y portabilidad de sus datos, y la limitación u oposición a su tratamiento, retirar el consentimiento en este documento aceptado e incluso interponer reclamación ante la Agencia Española de Protección de Datos.
Este mensaje contiene información confidencial destinada para ser leída exclusivamente por el destinatario. Queda prohibida la reproducción, publicación, divulgación, total o parcial del mensaje así como el uso no autorizados por el emisor. En caso de recibir el mensaje por error, se ruega su comunicación al remitente lo antes posible. Por favor, indique inmediatamente si usted o su empresa no aceptan comunicaciones de este tipo  por Internet. Las opiniones, conclusiones y demás información incluida en este mensaje que no esté relacionada con asuntos profesionales de Etiquetas Alhambra, S.L., se entenderá que nunca se ha dado, ni está respaldado por el mismo.
</h6></td></tr>";

    $body.="</table>";

    $headers = array('Content-Type: text/html; charset=UTF-8');

    wp_mail($to, $subject, $body, $headers);

    /* CORREO A EMPRESA */

    $to = "administracion@graficasandalusi.com, llani@graficasandalusi.com, juanma.avila@citysem.es";

    $subject = '¡Un cliente ha realizado una compra!';

    $body="<table border='0' cellspacing='0' cellpadding='5' style='width:100%'>";
    $body.="<tr><td style='text-align: center' colspan='4'><img src='https://imprimirmirevista.es/wp-content/uploads/2018/12/logo-imprimir-mirevista.png' alt='Imprimir mi Revista' width='200' /></td></tr>";
    $body.="<tr><td style='text-align: center;' colspan='4'><h1 style='font-size: 28px'>¡Un cliente ha realizado una compra!</h1></td></tr>";
    $body.="<tr><td style='padding:0;text-align: center;padding-bottom: 20px' colspan='4'><p>Alguien ha realizado una compra en <a href='https://imprimirmirevista.es/'>Imprimir mi Revista</a>. <b>Resumen del pedido:</b></p></td></tr>";
    $body.="<tr><th style='text-align: left'>Formato:</th><td>$em_formato</td> <th style='text-align: left'>Páginas:</th><td>$paginas</td></tr>";
    $body.="<tr><th style='text-align: left'>Cantidad:</th><td>$cantidad</td> <th style='text-align: left'>Gramaje Interior:</th><td>$gramaje_interior</td></tr>";
    $body.="<tr><th style='text-align: left'>Papel interior:</th><td>$em_papel_interior</td> <th style='text-align: left'>Gramaje cubiertas:</th><td>$gramaje_cubierta</td></tr>";
    $body.="<tr><th style='text-align: left'>Papel cubierta:</th><td>$em_papel_cubierta</td> <th style='text-align: left'>Plastificado:</th><td>$em_plastificado_laminado</td></tr>";

    if($plastificado_laminado == "P"){
        $body.="<tr><th style='text-align: left'>Caras de la cubierta:</th><td>$caras</td> <th style='text-align: left'>Plastificado de la cubierta:</th><td>$em_plastificado</td></tr>";
    }

	$precio_sin_iva2 = $precio_venta / 1.21;

    $body.="<tr><th colspan='4' style='padding-top: 30px'><h2 style='font-size: 27px'>Precio sin IVA:</h2></th></tr>";
    //$body.="<tr><th colspan='4' style='font-size: 31px'>".number_format($precio_sin_iva, 2)." €</th></tr>";
    $body.="<tr><th colspan='4' style='font-size: 31px'>".number_format($precio_sin_iva2, 2)." €</th></tr>";

    $body.="<tr><th colspan='4' style='padding-top: 30px'><h2 style='font-size: 30px'>Precio total:</h2></th></tr>";
    $body.="<tr><th colspan='4' style='font-size: 35px'>".number_format($precio_venta, 2)." €</th></tr>";

    $body.="<tr><td style='padding:0;text-align: center;padding-top: 20px' colspan='4'><p><h2>Datos del cliente (facturación ".(($datos_mismos != null) ? 'y envío' : '')."):</h2></p></td></tr>";
    $body.="<tr><th style='text-align: left'>Nombre:</th><td>$f_nombre</td> <th style='text-align: left'>Empresa:</th><td>$f_empresa</td></tr>";
    $body.="<tr><th style='text-align: left'>CIF/DNI:</th><td>$f_cif_dni</td> <th style='text-align: left'>Dirección:</th><td>$f_direccion</td></tr>";
    $body.="<tr><th style='text-align: left'>Email:</th><td>$f_email</td> <th style='text-align: left'>CP:</th><td>$f_cp</td></tr>";
    $body.="<tr><th style='text-align: left'>Provincia:</th><td>$f_provincia</td> <th style='text-align: left'>Teléfono:</th><td>$f_telefono</td></tr>";
	$body.="<tr><th style='text-align: left'>Localidad:</th><td>$f_localidad</td></tr>";

    if($datos_mismos == null){
        $body.="<tr><td style='padding:0;text-align: center;padding-top: 20px' colspan='4'><p><h2>Datos del cliente (envío):</h2></p></td></tr>";
        $body.="<tr><th style='text-align: left'>Nombre:</th><td>$e_nombre</td> <th style='text-align: left'>Empresa:</th><td>$e_empresa</td></tr>";
        $body.="<tr><th style='text-align: left'>CIF/DNI:</th><td>$e_cif_dni</td> <th style='text-align: left'>Dirección:</th><td>$e_direccion</td></tr>";
        $body.="<tr><th style='text-align: left'>Email:</th><td>$e_email</td> <th style='text-align: left'>CP:</th><td>$e_cp</td></tr>";
        $body.="<tr><th style='text-align: left'>Provincia:</th><td>$e_provincia</td> <th style='text-align: left'>Teléfono:</th><td>$e_telefono</td></tr>";
		$body.="<tr><th style='text-align: left'>Localidad:</th><td>$e_localidad</td></tr>";
    }

    $body.="</table>";

    $headers = array('Content-Type: text/html; charset=UTF-8');

    wp_mail($to, $subject, $body, $headers);

    /* INSERCIÓN DE LA VENTA */
    if($plastificado_laminado == "P"){
        if($datos_mismos != null){
            $insercion = $wpdb->insert(
            	'magazzine_pedidos',
            	array(
            		'formato' => $formato,
            		'paginas' => $paginas,
            		'cantidad' => $cantidad,
            		'gramaje_interior' => $gramaje_interior,
            		'papel_interior' => $papel_interior,
            		'gramaje_cubierta' => $gramaje_cubierta,
            		'papel_cubierta' => $papel_cubierta,
            		'plastificado_laminado' => $plastificado_laminado,
            		'caras' => $caras,
            		'plastificado' => $plastificado,
            		'f_nombre' => $f_nombre,
            		'f_empresa' => $f_empresa,
            		'f_cif_dni' => $f_cif_dni,
            		'f_direccion' => $f_direccion,
            		'f_email' => $f_email,
            		'f_cp' => $f_cp,
            		'f_provincia' => $f_provincia,
					'f_localidad' => $f_localidad,
            		'f_telefono' => $f_telefono,
            		'mismos_facturacion' => 1,
            		'precio_venta' => $precio_venta,
            		'fecha_venta' => $fecha_venta
            	),
            	array(
            		'%s',
            		'%d',
            		'%d',
            		'%d',
            		'%s',
            		'%d',
            		'%s',
            		'%s',
            		'%d',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
					'%s',
            		'%s',
            		'%d',
            		'%s',
            		'%s'
            	)
            );
        }else{
            $insercion = $wpdb->insert(
            	'magazzine_pedidos',
            	array(
            		'formato' => $formato,
            		'paginas' => $paginas,
            		'cantidad' => $cantidad,
            		'gramaje_interior' => $gramaje_interior,
            		'papel_interior' => $papel_interior,
            		'gramaje_cubierta' => $gramaje_cubierta,
            		'papel_cubierta' => $papel_cubierta,
            		'plastificado_laminado' => $plastificado_laminado,
            		'caras' => $caras,
            		'plastificado' => $plastificado,
            		'f_nombre' => $f_nombre,
            		'f_empresa' => $f_empresa,
            		'f_cif_dni' => $f_cif_dni,
            		'f_direccion' => $f_direccion,
            		'f_email' => $f_email,
            		'f_cp' => $f_cp,
            		'f_provincia' => $f_provincia,
					'f_localidad' => $f_localidad,
            		'f_telefono' => $f_telefono,
            		'mismos_facturacion' => 0,
            		'e_nombre' => $e_nombre,
            		'e_empresa' => $e_empresa,
            		'e_cif_dni' => $e_cif_dni,
            		'e_direccion' => $e_direccion,
            		'e_email' => $e_email,
            		'e_cp' => $e_cp,
            		'e_provincia' => $e_provincia,
					'e_localidad' => $e_localidad,
            		'e_telefono' => $e_telefono,
            		'precio_venta' => $precio_venta,
            		'fecha_venta' => $fecha_venta
            	),
            	array(
            		'%s',
            		'%d',
            		'%d',
            		'%d',
            		'%s',
            		'%d',
            		'%s',
            		'%s',
            		'%d',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
					'%s',
            		'%s',
            		'%d',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
					'%s',
            		'%s',
            		'%s',
            		'%s'
            	)
            );

        }


    }else if($plastificado_laminado == "L"){
        if($datos_mismos != null){
            $insercion = $wpdb->insert(
            	'magazzine_pedidos',
            	array(
            		'formato' => $formato,
            		'paginas' => $paginas,
            		'cantidad' => $cantidad,
            		'gramaje_interior' => $gramaje_interior,
            		'papel_interior' => $papel_interior,
            		'gramaje_cubierta' => $gramaje_cubierta,
            		'papel_cubierta' => $papel_cubierta,
            		'plastificado_laminado' => $plastificado_laminado,
            		'f_nombre' => $f_nombre,
            		'f_empresa' => $f_empresa,
            		'f_cif_dni' => $f_cif_dni,
            		'f_direccion' => $f_direccion,
            		'f_email' => $f_email,
            		'f_cp' => $f_cp,
            		'f_provincia' => $f_provincia,
					'f_localidad' => $f_localidad,
            		'f_telefono' => $f_telefono,
            		'mismos_facturacion' => 1,
            		'precio_venta' => $precio_venta,
            		'fecha_venta' => $fecha_venta
            	),
            	array(
            		'%s',
            		'%d',
            		'%d',
            		'%d',
            		'%s',
            		'%d',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
					'%s',
            		'%s',
            		'%s',
            		'%d',
            		'%s',
            		'%s'
            	)
            );
        }else{
            $insercion = $wpdb->insert(
            	'magazzine_pedidos',
            	array(
            		'formato' => $formato,
            		'paginas' => $paginas,
            		'cantidad' => $cantidad,
            		'gramaje_interior' => $gramaje_interior,
            		'papel_interior' => $papel_interior,
            		'gramaje_cubierta' => $gramaje_cubierta,
            		'papel_cubierta' => $papel_cubierta,
            		'plastificado_laminado' => $plastificado_laminado,
            		'f_nombre' => $f_nombre,
            		'f_empresa' => $f_empresa,
            		'f_cif_dni' => $f_cif_dni,
            		'f_direccion' => $f_direccion,
            		'f_email' => $f_email,
            		'f_cp' => $f_cp,
            		'f_provincia' => $f_provincia,
					'f_localidad' => $f_localidad,
            		'f_telefono' => $f_telefono,
            		'mismos_facturacion' => 0,
            		'e_nombre' => $e_nombre,
            		'e_empresa' => $e_empresa,
            		'e_cif_dni' => $e_cif_dni,
            		'e_direccion' => $e_direccion,
            		'e_email' => $e_email,
            		'e_cp' => $e_cp,
            		'e_provincia' => $e_provincia,
					'e_localidad' => $e_localidad,
            		'e_telefono' => $e_telefono,
            		'precio_venta' => $precio_venta,
            		'fecha_venta' => $fecha_venta
            	),
            	array(
            		'%s',
            		'%d',
            		'%d',
            		'%d',
            		'%s',
            		'%d',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
					'%s',
            		'%s',
            		'%d',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s',
					'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s'
            	)
            );
        }
    }
    $lastid = $wpdb->insert_id;
    echo $lastid;

    die();
}

add_action( 'wp_ajax_funcion_peticion_ajax', 'funcion_peticion_ajax' );
add_action( 'wp_ajax_nopriv_funcion_peticion_ajax', 'funcion_peticion_ajax' );

function funcion_peticion_ajax() {
    $formato = $_POST['formato'];
    $paginas = $_POST['paginas'];
    $cantidad = $_POST['cantidad'];
    $gramaje_interior = $_POST['gramaje_interior'];
    //$papel_interior = $_POST['papel_interior'];
    $gramaje_cubierta = $_POST['gramaje_cubierta'];
    $papel_cubierta = $_POST['papel_cubierta'];
    $plastificado = $_POST['plastificado'];
    $caras = $_POST['caras'];
    $tipo_plastificado = $_POST['tipo_plastificado'];

    global $wpdb;

    $consulta_precio = "SELECT price
        FROM magazzine_interiorexteriorcomun
        WHERE weight = $gramaje_interior
        AND weight_cover = $gramaje_cubierta
        AND format = '$formato'
        AND pages = $paginas
        AND copies = $cantidad
        LIMIT 1
    ";//AND type_paper = '$papel_interior'

    if($plastificado == "P"){
        $consulta_suplemento = "SELECT porcentaje
            FROM magazzine_porcentajes
            WHERE copies >= $cantidad
            AND type_paper = '$tipo_plastificado'
            AND type_cover = '$plastificado'
            AND caras = $caras
            ORDER BY copies ASC
            LIMIT 1
        ";
    }else{
        $consulta_suplemento = "SELECT porcentaje
            FROM magazzine_porcentajes
            WHERE copies >= $cantidad
            AND type_cover = '$plastificado'
            ORDER BY copies ASC
            LIMIT 1
        ";//AND type_paper = '$papel_cubierta'

    }


    $rsprecio = $wpdb->get_results($consulta_precio);
    //echo $wpdb->last_query;

    $rssuplemento = $wpdb->get_results($consulta_suplemento);
    //Si no encuentra descuento le aplica el de el tipo de papel mate
    if($wpdb->num_rows == 0 && $plastificado == "L"){
        $consulta_suplemento = "SELECT porcentaje
            FROM magazzine_porcentajes
            WHERE copies >= $cantidad
            AND type_paper = 'M'
            AND type_cover = '$plastificado'
            ORDER BY copies ASC
            LIMIT 1
        ";

        $rssuplemento = $wpdb->get_results($consulta_suplemento);
    }
    //echo $wpdb->last_query;


    $consulta_descuento = "SELECT descuento FROM magazzine_descuentos WHERE copias_hasta >= $cantidad LIMIT 1";
    $rsdescuento = $wpdb->get_results($consulta_descuento);

    $precio = 0;
    $suplemento = 0;
    $descuento = 0;

    $normal = 0;

    foreach ($rsprecio as $price){
        $precio = $price->price;
    }

    foreach ($rssuplemento as $sup){
        $suplemento = $sup->porcentaje;
    }

    if($suplemento > 0){
        $normal = (($suplemento / 100) * $precio) + $precio;
    }else if($suplemento < 0){
        $normal = $precio - ((abs($suplemento) / 100) * $precio) ;
    }

    foreach ($rsdescuento as $desc){
        $descuento = $desc->descuento;
    }



    $normal_formateado = number_format($normal, 2);
    $mercado = 0;
    $total = $normal - (10 / 100) * $normal; //Aplicamos el 10 por ciento de descuento que se aplica SIEMPRE

    //Aplicamos el porcentaje de descuento de la tabla magazzine_descuentos
    if($descuento >= 0){ //Si el descuento es positivo es un suplemento
        $total = $total + ($descuento / 100) * $normal;
    }else{ // si el descuento es negativo es un descuento
        $total = $total - (abs($descuento) / 100) * $normal;
    }

	//IVA
	$total_con_iva = $total * 1.21;

    //Precio mercado (alrededor de +30%)
    $rand = rand(50, 100) / 100;
    $porcentaje_aplicar = 29 + $rand;
    $mercado = $normal + ($porcentaje_aplicar / 100) * $normal;
    $mercado_formateado = number_format($mercado, 2);


    echo '{';
    echo '"normal": "'.$normal_formateado.'",';
    echo '"mercado": "'.$mercado_formateado.'",';
    echo '"total": "'.number_format($total, 2).'",';
	echo '"total_con_iva" : "'.number_format($total_con_iva, 2).'"';
    echo '}';

    wp_die();
}
