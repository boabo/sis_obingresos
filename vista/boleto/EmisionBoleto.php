<?php
/**
 *@package pXP
 *@file EmisionBoleto.php
 *@author  breydi.vasquez
 *@date 11-11-2021
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.EmisionBoleto=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.maestro=config.maestro;
                this.grupo = 'no';
                this.tipo_usuario = 'cajero';
                this.firstA = {}
                this.iniciar_tiempo='si';
                this.actualizar_automatico=0;


                Ext.Ajax.request({
                    url:'../../sis_ventas_facturacion/control/Venta/getVariablesBasicas',
                    params: {'prueba':'uno'},
                    success:this.successGetVariables,
                    failure: this.conexionFailure,
                    arguments:config,
                    timeout:this.timeout,
                    scope:this
                });                
            },

            /*Aumentando para los nuevos medios de pago (Ismael Valdivia 24/11/2020)*/
            fontMediosPago :function (variables) {
              if (variables.instancias_de_pago_nuevas == 'no') {
                this.Atributos[this.getIndAtributo('id_forma_pago')].config.tpl = '<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p><b><p>Codigo:<font color="green">{codigo}</font></b></p><p><b>Moneda:<font color="red">{desc_moneda}</font></b></p> </div></tpl>';
                this.Atributos[this.getIndAtributo('id_forma_pago')].config.fieldLabel = 'Forma de Pago BoA';
                this.Atributos[this.getIndAtributo('tipo_comision')].config.fieldLabel = 'Tipo Comision';
                this.Atributos[this.getIndAtributo('cambio')].config.fieldLabel = 'Cambio M/L';
                this.Atributos[this.getIndAtributo('cambio_moneda_extranjera')].config.fieldLabel = 'Cambio M/E';
                this.Atributos[this.getIndAtributo('monto_recibido_forma_pago')].config.fieldLabel = 'Importe Recibido Forma Pago M/L';
                this.Atributos[this.getIndAtributo('monto_forma_pago')].config.fieldLabel = 'Importe Forma Pago BoA';
                this.Atributos[this.getIndAtributo('numero_tarjeta')].config.fieldLabel = 'No Tarjeta';
                this.Atributos[this.getIndAtributo('codigo_tarjeta')].config.fieldLabel = 'Codigo de Autorización 1';
                this.Atributos[this.getIndAtributo('id_auxiliar')].config.fieldLabel = 'Cuenta Corriente';

                this.Atributos[this.getIndAtributo('id_forma_pago2')].config.tpl = '<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p><b><p>Codigo:<font color="green">{codigo}</font></b></p><p><b>Moneda:<font color="red">{desc_moneda}</font></b></p> </div></tpl>';
                this.Atributos[this.getIndAtributo('id_forma_pago2')].config.fieldLabel = 'Forma de Pago 2';
                this.Atributos[this.getIndAtributo('monto_forma_pago2')].config.fieldLabel = 'Importe Forma de Pago BoA 2';
                this.Atributos[this.getIndAtributo('numero_tarjeta2')].config.fieldLabel = 'No Tarjeta 2';
                this.Atributos[this.getIndAtributo('codigo_tarjeta2')].config.fieldLabel = 'Codigo de Autorización 2';
                this.Atributos[this.getIndAtributo('id_auxiliar2')].config.fieldLabel = 'Cuenta Corriente';

                this.Atributos[this.getIndAtributo('localizador')].config.fieldLabel = 'Pnr';
                this.Atributos[this.getIndAtributo('nro_boleto')].config.fieldLabel = 'Billete: 930-';
                this.Atributos[this.getIndAtributo('forma_pago_amadeus')].config.fieldLabel = 'Pago Amadeus';
                this.Atributos[this.getIndAtributo('moneda')].config.fieldLabel = 'Moneda';
                this.Atributos[this.getIndAtributo('total')].config.fieldLabel = 'Total M/L';
                this.Atributos[this.getIndAtributo('comision')].config.fieldLabel = 'Comisión AGT M/L';
                this.Atributos[this.getIndAtributo('comision_moneda_extranjera')].config.fieldLabel = 'Comisión AGT M/E';
                this.Atributos[this.getIndAtributo('fecha_emision')].config.fieldLabel = 'Fecha Emision';
                this.Atributos[this.getIndAtributo('agente_venta')].config.fieldLabel = 'Agente Venta';
                this.Atributos[this.getIndAtributo('total_moneda_extranjera')].config.fieldLabel = 'Total M/E';
                this.Atributos[this.getIndAtributo('pasajero')].config.fieldLabel = 'Pasajero';



              } else {
                this.Atributos[this.getIndAtributo('id_forma_pago')].config.tpl = '<tpl for="."><div class="x-combo-list-item"><p><b>Medio de Pago:<font color="red">{nombre}</font></b></p><b><p>Codigo:<font color="green">{codigo}</font></b></p></div></tpl>';
                this.Atributos[this.getIndAtributo('id_forma_pago')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/TarjetaCredito.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Medio de Pago BoA</span>';
                this.Atributos[this.getIndAtributo('tipo_comision')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/comision.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Tipo Comisión</span>';
                this.Atributos[this.getIndAtributo('cambio')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/MonedaDolar.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Cambio M/L</span>';
                this.Atributos[this.getIndAtributo('cambio_moneda_extranjera')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/MonedaDolar.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Cambio M/E</span>';
                this.Atributos[this.getIndAtributo('monto_recibido_forma_pago')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/BolsaDinero.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Importe Recibido Forma Pago M/L</span>';
                this.Atributos[this.getIndAtributo('monto_forma_pago')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/BolsaDinero.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Importe Forma Pago BoA</span>';
                this.Atributos[this.getIndAtributo('numero_tarjeta')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/TarjetaCreditos.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> No Tarjeta</span>';
                this.Atributos[this.getIndAtributo('codigo_tarjeta')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/Codigo.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Codigo de Autorización 1</span>';
                this.Atributos[this.getIndAtributo('id_auxiliar')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/CuentaCorriente.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Cuenta Corriente</span>';

                this.Atributos[this.getIndAtributo('id_forma_pago2')].config.tpl = '<tpl for="."><div class="x-combo-list-item"><p><b>Medio de Pago:<font color="red">{nombre}</font></b></p><b><p>Codigo:<font color="green">{codigo}</font></b></p></div></tpl>';
                this.Atributos[this.getIndAtributo('id_forma_pago2')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/TarjetaCredito.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Medio de Pago</span>';
                this.Atributos[this.getIndAtributo('monto_forma_pago2')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/BolsaDinero.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Importe Forma Pago BoA 2</span>';
                this.Atributos[this.getIndAtributo('numero_tarjeta2')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/TarjetaCreditos.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> No Tarjeta 2</span>';
                this.Atributos[this.getIndAtributo('codigo_tarjeta2')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/Codigo.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Codigo de Autorización 2</span>';
                this.Atributos[this.getIndAtributo('id_auxiliar2')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/CuentaCorriente.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Cuenta Corriente</span>';


                this.Atributos[this.getIndAtributo('localizador')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/Codigo.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> PNR</span>';
                this.Atributos[this.getIndAtributo('nro_boleto')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/ticket.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Billete: 930-</span>';
                this.Atributos[this.getIndAtributo('forma_pago_amadeus')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/TarjetaCreditos.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Pago Amadeus</span>';
                this.Atributos[this.getIndAtributo('moneda')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/MonedaDolar.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Moneda</span>';
                this.Atributos[this.getIndAtributo('total')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/BolsaDinero.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Total M/L</span>';
                this.Atributos[this.getIndAtributo('comision')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/comision.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Comisión AGT M/L</span>';
                this.Atributos[this.getIndAtributo('comision_moneda_extranjera')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/comision.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Comisión AGT M/E</span>';
                this.Atributos[this.getIndAtributo('fecha_emision')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/calendario.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Fecha Emisión</span>';
                this.Atributos[this.getIndAtributo('agente_venta')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/usuario.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Agente Venta</span>';
                this.Atributos[this.getIndAtributo('total_moneda_extranjera')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/BolsaDinero.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Total M/E</span>';
                this.Atributos[this.getIndAtributo('pasajero')].config.fieldLabel = '<img src="../../../lib/imagenes/facturacion/usuario.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Pasajero</span>';

              }
            },

            condicionesRegionales : function() {
              this.Cmp.id_forma_pago.store.baseParams.regionales = this.variables_globales.ESTACION_inicio;
              this.Cmp.id_forma_pago2.store.baseParams.regionales = this.variables_globales.ESTACION_inicio;

              if (this.variables_globales.instancias_de_pago_nuevas == 'no') {
                 this.ocultarComponente(this.Cmp.id_moneda2);
                 this.ocultarComponente(this.Cmp.id_moneda);
                 this.Cmp.id_moneda.allowBlank = true;
                 this.Cmp.id_moneda2.allowBlank = true;
                 this.Cmp.id_forma_pago.store.baseParams.instancias_nuevas = 'no';
                 this.Cmp.id_forma_pago2.store.baseParams.instancias_nuevas = 'no';



              } else {
                this.mostrarComponente(this.Cmp.id_moneda2);
                this.mostrarComponente(this.Cmp.id_moneda);
                this.Cmp.id_moneda.allowBlank = false;
                this.Cmp.id_moneda2.allowBlank = true;
                this.Cmp.id_forma_pago.store.baseParams.instancias_nuevas = 'si';
                this.Cmp.id_forma_pago2.store.baseParams.instancias_nuevas = 'si';
              }
            },
            /*****************************************************************************/

            onButtonInvoicePNRPDF: function () {
                var rec = this.sm.getSelected().data;                
                if (rec) {
                    Phx.CP.loadingShow();
                    Ext.Ajax.request({
                        url : '../../sis_obingresos/control/Boleto/GetInvoicePNRPDF',
                        params : {
                             'pnr' : rec.localizador,
                             'identificador': rec.pasajero
                        },
                        success : this.imprimirBoletoReserva,
                        failure : this.conexionFailure,
                        timeout : this.timeout,
                        scope : this
                    });
                }
            },

            imprimirBoletoReserva: function (resp) {
                Phx.CP.loadingHide();
                reg = JSON.parse(resp.responseText)
                if (reg.pdf != undefined && reg.pdf != '') {
                    var html = '';
                    html += '<html>';
                    html += '<body style="margin:0!important">';
                    html += '<embed width="100%" height="100%" src="data:application/pdf;base64,'+reg.pdf+'" type="application/pdf" />';
                    html += '</body>';
                    html += '</html>'
                    var win = window.open("","_blank")
                    win.document.write(html)
                } else {
                    Ext.Msg.show({
                        title: 'Alerta',
                        msg: '<div style="text-align: justify;"><b style="color: red;">Estimado Usuario:</b> <br> <br><b>No se pudo recuperar la factura relacionada al PNR: '+reg.pnr+' <br> favor intentarlo nuevamente, si problema persiste comuniquece con el dpto. Sistemas.</b>'+'</div>',
                        buttons: Ext.Msg.OK,
                        width: 500,
                        maxWidth: 1024,
                        icon: Ext.Msg.ALERT
                    });
                }
            },

            imprimirBoleto: function(){

                var rec = this.sm.getSelected().data;

                if (rec) {
                    Phx.CP.loadingShow();
                    Ext.Ajax.request({
                        url : '../../sis_obingresos/control/Boleto/traerReservaBoletoExch',
                        params : {
                            'pnr' : rec.localizador
                        },
                        success : this.successExport,
                        failure : this.conexionFailure,
                        timeout : this.timeout,
                        scope : this
                    });
                }
            },
            successGetVariables : function (response,request) {
                //llama al constructor de la clase padre
                var respuesta = JSON.parse(response.responseText);
          			if('datos' in respuesta){
          					this.variables_globales = respuesta.datos;
                    /*Aumentando Variables para el cambio de las nuevas instancias de pago*/
                    this.fontMediosPago(this.variables_globales);
                    /**********************************************************************/
          			}
                Phx.vista.EmisionBoleto.superclass.constructor.call(this,request.arguments);
                this.store.baseParams.pes_estado = 'revisados';
                this.store.baseParams.todos = 'no';
                this.store.baseParams.emisionReservaBoletos = 'si'
                var me = this;
                this.init();
                this.recuperarBase();
                this.addButton('btnAnularBoleto',
                    {
                        //text: 'Anular Boleto',
                        //iconCls: 'block',
                        text: '<i class="fa fa-file-excel-o fa-3x"></i> Anular', /*iconCls:'' ,*/
                        grupo: [0, 1],
                        disabled: true,
                        handler: this.anularBoleto,
                        tooltip: '<b>Anular</b><br/>Anular Boleto'
                    }
                );

                this.addButton('btnPagarGrupo',
                    {
                        grupo: [1],
                        text: 'Pagar Grupo',
                        iconCls: 'bmoney',
                        disabled: true,                        
                        handler: this.onGrupo,
                        tooltip: 'Paga todos los boletos seleccionados'
                    }
                );

                this.addButton('btnImprimir',
                    {
                        text: 'Imprimir',
                        iconCls: 'bpdf32',
                        // grupo: [0,1],
                        disabled: true,
                        handler: this.imprimirBoleto,
                        tooltip: '<b>Imprimir Boleto</b><br/>Imprime el boleto'
                    }
                );

                this.addButton('btnBoletosTodos',
                    {
                        grupo: [3],
                        text: 'Traer Todos Boletos',
                        iconCls: 'breload2',
                        disabled: false,
                        handler: this.onTraerBoletosTodos,
                        tooltip: 'Traer todos boletos vendidos'
                    }
                );
                // ini {dev:breydi.vasquez, date, 05/10/2012, desc: funcionalidad registro PNR y cobro reserva}
                this.nro_pnr_reserva = new Ext.form.TextField({
                    name: 'nro_pnr_reserva',
                    grupo: this.beditGroups,
                    text: 'PNR Reserva',
                    fieldLabel: 'PNR Reserva',
                    emptyText: 'pnr reserva...',
                    allowBlank: true,                                        
                    width: 90,                                        
                    hidden : false,
                    style: 'text-transform: upperCase',
                    listeners: {
                        'render': function(cmp) {                            
                            cmp.getEl().on('keypress', function(e) {
                                if (e.getKey() == e.ENTER) {
                                    me.onInfoPnr();
                                }
                            })
                        }
                    }
                });
                            
                this.campo_fecha = new Ext.form.DateField({
                    name: 'fecha_reg',
                    grupo: this.grupoDateFin,
                    fieldLabel: 'Fecha',
                    allowBlank: false,
                    anchor: '60%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    hidden : false
                });

                this.punto_venta = new Ext.form.Label({
                    name: 'punto_venta',
                    grupo: this.grupoDateFin,
                    fieldLabel: 'P.V.',
                    readOnly:true,
                    anchor: '150%',
                    gwidth: 150,
                    format: 'd/m/Y',
                    hidden : false,
                    style: 'font-size: 170%; font-weight: bold; background-image: none;'
                });

                this.addButton('btnVoucherCode',
                    {
                        grupo: [3],
                        text: 'Voucher Code',
                        iconCls: 'bdocuments',
                        disabled: true,
                        handler: this.onButtonVoucherCode,
                        tooltip: 'Voucher Code'
                    }
                );

                this.addButton('btnInvoicePNRPDF',
                    {
                        grupo: [0, 1],
                        text: 'Factura Boleto',
                        iconCls: 'bpdf32',
                        disabled: true,
                        handler: this.onButtonInvoicePNRPDF,
                        tooltip: 'Factura Boleto'
                    }
                );

                this.getBoton('btnVoucherCode').setVisible(false);
                this.getBoton('btnInvoicePNRPDF').setVisible(true);
                this.getBoton('btnBoletosTodos').setVisible(false);
                this.getBoton('btnPagarGrupo').setVisible(false);                
                this.getBoton('btnImprimir').setVisible(false);                
                // this.getBoton('btnAnularBoleto').setVisible(false);                
                this.tbar.addField(" ");
                this.tbar.addField(this.nro_pnr_reserva);
                this.tbar.addField(" ");
                this.tbar.addField(this.campo_fecha);
                this.tbar.addField(this.punto_venta);
                var datos_respuesta = JSON.parse(response.responseText);
                var fecha_array = datos_respuesta.datos.fecha.split('/');
                this.campo_fecha.setValue(new Date(fecha_array[2],parseInt(fecha_array[1]) - 1,fecha_array[0]));
                this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('Ymd');

                this.campo_fecha.on('select',function(value){
                    this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('Ymd');
                    this.load();
                },this);

                //this.addButton('cerrar',{grupo:[0],text:'Cerrar Caja',iconCls: 'block',disabled:false,handler:this.preparaCerrarCaja,tooltip: '<b>Cerrar la Caja</b>'});

                //this.store.baseParams.estado = 'borrador';
                this.iniciarEventos();
                this.finCons = true;
                this.seleccionarPuntoVentaSucursal();
                // this.grid.addListener('cellclick', this.oncellclick,this);
                this.bloquearOrdenamientoGrid();

            },

            gruposBarraTareas:[
                {name:'revisados',title:'<H1 align="center"><i class="fa fa-eye"></i> Revisados</h1>',grupo:0,height:0},
                {name:'no_revisados',title:'<H1 align="center"><i class="fa fa-eye"></i> No Revisados</h1>',grupo:1,height:0}
            ],

            actualizarSegunTab: function(name, indice){
                if(this.finCons){
                    this.store.baseParams.pes_estado = name;
                    this.load({params:{start:0, limit:this.tam_pag}});
                }
            },

            beditGroups: [0],
            bdelGroups:  [0],
            bactGroups:  [0,1],
            btestGroups: [0],
            bexcelGroups: [0,1],
            grupoDateFin: [0,1],

            seleccionarPuntoVentaSucursal : function () {
                var validado = false;
                var storeCombo = new Ext.data.JsonStore({
                    url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
                    id: 'id_punto_venta',
                    root: 'datos',
                    sortInfo: {
                        field: 'nombre',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_punto_venta', 'nombre', 'codigo'],
                    remoteSort: true,
                    baseParams: {tipo_usuario: this.tipo_usuario,par_filtro: 'puve.nombre#puve.codigo'}
                });


                storeCombo.load({params:{start:0,limit:this.tam_pag},
                    callback : function (r) {
                      /*Cambiando la condicion para cuando el usuario solo tenga un punto de venta 1 por 0*/
                      //if (r.length == 1 ) {
                        if (r.length == 0 ) {
                            this.id_punto_venta = r[0].data.id_punto_venta;
                            this.store.baseParams.id_punto_venta = r[0].data.id_punto_venta;
                            this.Cmp.id_forma_pago.store.baseParams.id_punto_venta = this.id_punto_venta;
                            this.Cmp.id_forma_pago2.store.baseParams.id_punto_venta = this.id_punto_venta;
                            this.punto_venta.setText(r[0].data.nombre);
                            //this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('d/m/Y');
                            this.argumentExtraSubmit.id_punto_venta = this.id_punto_venta;
                            this.load({params:{start:0, limit:this.tam_pag}});
                        } else {

                            var combo2 = new Ext.form.ComboBox(
                                {
                                    typeAhead: false,
                                    fieldLabel: 'Punto de Venta',
                                    allowBlank : false,
                                    store: storeCombo,
                                    mode: 'remote',
                                    pageSize: 15,
                                    triggerAction: 'all',
                                    valueField : 'id_punto_venta',
                                    displayField : 'nombre',
                                    forceSelection: true,
                                    tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
                                    allowBlank : false,
                                    anchor: '100%'
                                });

                            var formularioInicio = new Ext.form.FormPanel({
                                items: [combo2],
                                padding: true,
                                bodyStyle:'padding:5px 5px 0',
                                border: false,
                                frame: false
                            });

                            var VentanaInicio = new Ext.Window({
                                title: 'Punto de Venta / Sucursal',
                                modal: true,
                                width: 550,
                                height: 160,
                                bodyStyle: 'padding:5px;',
                                layout: 'fit',
                                hidden: true,
                                buttons: [
                                    {
                                        text: '<i class="fa fa-check"></i> Aceptar',
                                        handler: function () {
                                            if (formularioInicio.getForm().isValid()) {
                                                validado = true;
                                                VentanaInicio.close();
                                                this.id_punto_venta  = combo2.getValue();
                                                this.Cmp.id_forma_pago.store.baseParams.id_punto_venta = this.id_punto_venta;
                                                this.Cmp.id_forma_pago2.store.baseParams.id_punto_venta = this.id_punto_venta;
                                                this.store.baseParams.id_punto_venta = combo2.getValue();
                                                this.argumentExtraSubmit.id_punto_venta = this.id_punto_venta;
                                                this.store.baseParams.primera_carga = 'no';
                                                this.punto_venta.setText(combo2.lastSelectionText)                                                
                                                this.load({params:{start:0, limit:this.tam_pag}});
                                                // this.iniciarTiempo();
                                            }
                                        },
                                        scope: this
                                    }],
                                items: formularioInicio,
                                autoDestroy: true,
                                closeAction: 'close'
                            });
                            VentanaInicio.show();
                            VentanaInicio.on('beforeclose', function (){
                                if (!validado) {
                                    alert('Debe seleccionar el punto de venta o sucursal de trabajo');
                                    return false;
                                }
                            },this)
                        }

                    }, scope : this
                });



            },

            recuperarBase : function () {
              /******************************OBTENEMOS LA MONEDA BASE*******************************************/
              var fecha = new Date();
              var dd = fecha.getDate();
              var mm = fecha.getMonth() + 1; //January is 0!
              var yyyy = fecha.getFullYear();
              this.fecha_actual = dd + '/' + mm + '/' + yyyy;
              Ext.Ajax.request({
                  url:'../../sis_ventas_facturacion/control/AperturaCierreCaja/getTipoCambio',
                  params:{fecha_cambio:this.fecha_actual},
                  success: function(resp){
                      var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                      //this.tipo_cambio = reg.ROOT.datos.v_tipo_cambio;
                      this.store.baseParams.moneda_base = reg.ROOT.datos.v_codigo_moneda;
                  },
                  failure: this.conexionFailure,
                  timeout:this.timeout,
                  scope:this
              });
              /***********************************************************************************/

            },
            //loadMask :false,
            Atributos:[
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_boleto_amadeus'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'ids_seleccionados'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'moneda_sucursal'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'moneda_fp1'
                    },
                    type:'TextField',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'moneda_fp2'
                    },
                    type:'TextField',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'codigo_forma_pago'
                    },
                    type:'TextField',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'codigo_forma_pago2'
                    },
                    type:'TextField',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'tc'
                    },
                    type:'TextField',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'ffid'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'voucher_code'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'saldo_recibo'
                    },
                    type:'NumberField',
                    form:true
                },
                {
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'saldo_recibo_2'
                    },
                    type:'NumberField',
                    form:true
                },
                {
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'emisionReservaPnr'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'PnrReserva'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'offReserva'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'fechaEmisionPnr'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'identifierPnr'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'monedaBasePnr'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_cliente'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config: {
                        name: 'id_boleto_vuelo',
                        fieldLabel: 'Vuelo Ini Retorno',
                        allowBlank: true,
                        emptyText: 'Vuelo...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_obingresos/control/BoletoVuelo/listarBoletoVuelo',
                            id: 'id_boleto_vuelo',
                            root: 'datos',
                            sortInfo: {
                                field: 'cupon',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_boleto_vuelo', 'boleto_vuelo'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'bvu.aeropuerto_origen#bvu.aeropuerto_destino'}
                        }),
                        valueField: 'id_boleto_vuelo',
                        displayField: 'boleto_vuelo',
                        gdisplayField: 'vuelo_retorno',
                        hiddenName: 'id_boleto_vuelo',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        gwidth: 150,
                        listWidth:450,
                        resizable:true,
                        minChars: 2,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['vuelo_retorno']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 3,
                    grid: false,
                    form: true
                },
                {
                    config:{
                        name: 'estado',
                        fieldLabel: 'Revisado',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 80,
                        maxLength:3,
                        renderer: function (value, p, record, rowIndex, colIndex){

                            //check or un check row
                            var checked = '',
                                state = '',
                                momento = 'no';
                            if(value == 'revisado'){
                                checked = 'checked';
                            }
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('<div style="vertical-align:middle;text-align:center;pointer-events: none"><input style="height:37px;width:37px;" type="checkbox"  {0} {1}></div>',checked, state);
                            }
                            else{
                                return '';
                            }
                        }
                    },
                    type: 'TextField',
                    filters: { pfiltro:'nr.estado',type:'string'},
                    id_grupo: 3,
                    grid: true,
                    form: true
                },
                {
                    config:{
                        name: 'voided',
                        fieldLabel: 'Anulado',
                        anchor: '60%',
                        gwidth: 60,
                        readOnly:true,
                        renderer : function(value, p, record) {
                            if (record.data['voided'] != 'si') {
                                return String.format('<div title="Anulado"><b><font color="green">{0}</font></b></div>', value);

                            } else {
                                return String.format('<div title="Anulado"><b><font color="red">{0}</font></b></div>', value);
                            }
                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.voided',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'boletos',
                        fieldLabel: 'Boletos a Pagar',
                        anchor: '100%',
                        gwidth: 80,
                        readOnly:true

                    },
                    type:'TextArea',
                    id_grupo:2,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'moneda_boletos',
                        fieldLabel: 'Moneda Boletos',
                        anchor: '100%',
                        gwidth: 80,
                        readOnly:true
                    },
                    type:'TextField',
                    id_grupo:2,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'monto_total_boletos',
                        fieldLabel: 'Importe Total',
                        anchor: '100%',
                        gwidth: 80,
                        readOnly:true
                    },
                    type:'TextField',
                    id_grupo:2,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'monto_total_neto',
                        fieldLabel: 'Importe Neto',
                        anchor: '100%',
                        disabled : true,
                        gwidth: 80,
                        readOnly:true
                    },
                    type:'TextField',
                    id_grupo:3,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'monto_total_comision',
                        fieldLabel: 'Importe Comision',
                        anchor: '100%',
                        gwidth: 80,
                        readOnly:true
                    },
                    type:'TextField',
                    id_grupo:2,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'nit',
                        fieldLabel: 'Nit',                        
                        allowBlank: true,
                        gwidth: 50,
                        disabled: false,
                        maxLength:20
                    },
                    type:'NumberField',
                    id_grupo:0,
                    grid:false,
                    form:true,                    
                },                
                {
                    config:{
                        name: 'razonSocial',
                        fieldLabel: 'Razon Social',
                        allowBlank: true,
                        gwidth: 50,
                        disabled: false,
                        style:'text-transform:uppercase',
                    },
                    type:'TextField',
                    id_grupo:0,
                    grid:false,
                    form:true,                    
                },                
                {
                    config:{
                        name: 'localizador',
                        fieldLabel: 'Pnr',
                        anchor: '80%',
                        disabled: true,
                        gwidth: 60,
                        renderer : function(value, p, record) {
                            if ((record.data['neto'] == 30 && record.data['moneda'] == 'BOB'  || record.data['neto'] == 60 && record.data['moneda'] == 'BOB' ) ||
                                (record.data['neto'] == 40 && record.data['moneda'] == 'USD'  || record.data['neto'] == 80 && record.data['moneda'] == 'USD' )||
                                (record.data['neto'] == 70 && record.data['moneda'] == 'USD'  || record.data['neto'] == 140 && record.data['moneda'] == 'USD')||
                                (record.data['neto'] == 130 && record.data['moneda'] == 'USD' || record.data['neto'] == 260 && record.data['moneda'] == 'USD')){
                                return '<tpl for="."><p><font color="black">' + record.data['localizador'] + '</font><p><b><font color="#8b008b">Voucher</font></p></tpl>';
                            }else{
                                return '<tpl for="."><p><font color="black">' + record.data['localizador'] + '</tpl>';

                            }
                            return '<tpl for="."><p><font color="black">' + record.data['localizador'] + '</tpl>';
                        }
                    },
                    type:'TextField',
                    id_grupo:0,
                    grid:true,
                    form:true,
                    bottom_filter: true
                },

                {
                    config:{
                        name: 'trans_code',
                        fieldLabel: 'Code',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 50,
                        maxLength:10,
                        minLength:10,
                        enableKeyEvents:true,
                        renderer : function(value, p, record) {

                            return '<tpl for="."><p><font color="#20b2aa">' + record.data['trans_code'] + '</tpl>';
                        }
                    },
                    type:'TextField',
                    id_grupo:0,
                    grid:true,
                    form:false,
                    bottom_filter: true
                },
                {
                    config:{
                        name: 'trans_code_exch',
                        fieldLabel: 'Tipo',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 50,
                        maxLength:10,
                        minLength:10,
                        enableKeyEvents:true,
                        renderer : function(value, p, record) {

                            return '<tpl for="."><p><font color="green">' + record.data['trans_code_exch'] + '</tpl>';
                        }
                    },
                    type:'TextField',
                    id_grupo:0,
                    grid:true,
                    form:false,
                    bottom_filter: true
                },

                {
                    config:{
                        name: 'nro_boleto',
                        fieldLabel: 'Billete: 930-',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:10,
                        minLength:10,
                        enableKeyEvents:true,
                        renderer : function(value, p, record) {

                            return '<tpl for="."><p><font color="red">' + record.data['nro_boleto'] + '</tpl>';
                        }
                    },
                    type:'TextField',
                    id_grupo:0,
                    grid:true,
                    form:true,
                    bottom_filter: true
                },
                {
                    config:{
                        name: 'pasajero',
                        fieldLabel: 'Pasajero',
                        anchor: '100%',
                        disabled: true,
                        anchor: '90%',
                        gwidth: 130,
                        readOnly:true
                    },
                    type:'TextField',
                    id_grupo:0,
                    grid:true,
                    form:true,
                    bottom_filter: true
                },
                {
                    config:{
                        name: 'forma_pago_amadeus',
                        fieldLabel: 'Pago Amadeus',
                        gwidth: 120,
                        anchor: '90%',
                        disabled: true,
                        readOnly:true
                    },
                    type:'TextField',
                    grid:true,
                    id_grupo:0,
                    form:true
                },
                {
                    config:{
                        name: 'moneda',
                        fieldLabel: 'Moneda',
                        disabled: true,
                        anchor: '90%',
                        gwidth: 70,
                        readOnly:true

                    },
                    type:'TextField',
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'total',
                        fieldLabel: 'Total M/L',
                        disabled: true,
                        anchor: '90%',
                        gwidth: 70	,
                        readOnly:true
                    },
                    type:'NumberField',
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'comision',
                        fieldLabel: 'Comision',
                        disabled: true,
                        anchor: '90%',
                        gwidth: 70	,
                        readOnly:true
                    },
                    type:'NumberField',
                    id_grupo:0,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'total_moneda_extranjera',
                        fieldLabel: 'Total M/E',
                        disabled: true,
                        anchor: '90%',
                        gwidth: 70	,
                        readOnly:true
                    },
                    type:'NumberField',
                    id_grupo:0,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'neto',
                        fieldLabel: 'Neto',
                        disabled: true,
                        anchor: '90%',
                        gwidth: 70
                    },
                    type:'NumberField',
                    id_grupo: 3,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'comision',
                        fieldLabel: 'Comisión AGT M/L',
                        allowBlank:true,
                        anchor: '90%',
                        disabled:true,
                        gwidth: 40
                    },
                    type:'NumberField',
                    valor_inicial:0,
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'comision_moneda_extranjera',
                        fieldLabel: 'Comisión AGT M/E',
                        allowBlank:true,
                        anchor: '90%',
                        disabled:true,
                        gwidth: 40
                    },
                    type:'NumberField',
                    valor_inicial:0,
                    id_grupo:0,
                    grid:false,
                    form:true
                },

                {
                    config:{
                        name: 'tipo_comision',
                        fieldLabel: 'Tipo Comision',
                        qtip: 'Si tiene comision',
                        allowBlank: false,
                        emptyText: 'Tipo...',
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'local',
                        gwidth: 100,
                        anchor: '100%',
                        store: ['ninguno','nacional','internacional']
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    filters:{
                        type: 'list',
                        pfiltro:'bol.tipo_comision',
                        options: ['ninguno','nacional','internacional'],
                    },
                    valorInicial: 'ninguno',
                    grid:false,
                    form:true
                },
                {
                    config: {
                        name: 'id_moneda',
                        fieldLabel: '<img src="../../../lib/imagenes/facturacion/MonedaDolar.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Moneda</span>',
                        allowBlank: true,
                        //width:150,
                        listWidth:250,
                        resizable:true,
                        // style: {
                        //      background: '#EFFFD6',
                        //      color: 'red',
                        //      fontWeight:'bold'
                        //    },
                        emptyText: 'Moneda a pagar...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_parametros/control/Moneda/listarMoneda',
                            id: 'id_moneda',
                            root: 'datos',
                            sortInfo: {
                                field: 'moneda',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_moneda', 'codigo', 'moneda', 'codigo_internacional'],
                            remoteSort: true,
                            baseParams: {filtrar: 'si',par_filtro: 'moneda.moneda#moneda.codigo#moneda.codigo_internacional'}
                        }),
                        valueField: 'id_moneda',
                        gdisplayField : 'codigo_internacional',
                        displayField: 'codigo_internacional',
                        hiddenName: 'id_moneda',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:green;"><b style="color:black;">Moneda:</b> <b>{moneda}</b></p><p style="color:red;"><b style="color:black;">Código:</b> <b>{codigo_internacional}</b></p></div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        //disabled:true,
                        minChars: 2
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    form: true
                },
                {
                    config: {
                        name: 'id_forma_pago',
                        fieldLabel: 'Forma de Pago',
                        allowBlank: false,
                        emptyText: 'Forma de Pago...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_ventas_facturacion/control/FormaPago/listarFormaPago',
                            id: 'id_forma_pago',
                            root: 'datos',
                            sortInfo: {
                                field: 'nombre',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_forma_pago', 'nombre', 'desc_moneda','registrar_tarjeta','registrar_cc','codigo', 'codigo_fp'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'forpa.name#pago.fop_code'/*'forpa.nombre#forpa.codigo#mon.codigo_internacional'*/,sw_tipo_venta:'BOLETOS', emisionReservaBoletos:'si'}
                        }),
                        valueField: 'id_forma_pago',
                        displayField: 'nombre',
                        gdisplayField: 'forma_pago',
                        hiddenName: 'id_forma_pago',
                        tpl:'',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        gwidth: 150,
                        listWidth:250,
                        resizable:true,
                        minChars: 2,
                        disabled:true,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['forma_pago']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config:{
                        name: 'cambio',
                        fieldLabel: 'Cambio M/L',
                        allowBlank:true,
                        //anchor: '80%',
                        width:100,
                        allowDecimals:true,
                        decimalPrecision:2,
                        allowNegative : false,
                        readOnly:true,
                        gwidth: 110,
                        style: 'background-color: #3cf251;  background-image: none; height:30px; fontSize:20px;'
                    },
                    type:'NumberField',
                    id_grupo:20,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'cambio_moneda_extranjera',
                        fieldLabel: 'Cambio M/E',
                        allowBlank:true,
                        //anchor: '80%',
                        width:100,
                        allowDecimals:true,
                        decimalPrecision:2,
                        allowNegative : false,
                        readOnly:true,
                        gwidth: 110,
                        style: 'background-color: #3cf251;  background-image: none; height:30px; fontSize:20px;'
                    },
                    type:'NumberField',
                    id_grupo:20,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'monto_recibido_forma_pago',
                        fieldLabel: 'Importe Recibido Forma Pago M/L',
                        allowBlank:false,
                        anchor: '80%',
                        allowDecimals:true,
                        decimalPrecision:2,
                        allowNegative : false,
                        enableKeyEvents: true,
                        disabled:false,
                        gwidth: 110,
                        style: 'background-color: #f2f23c;  background-image: none;'
                    },
                    type:'NumberField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'monto_forma_pago',
                        fieldLabel: 'Importe Forma Pago BoA',
                        allowBlank:false,
                        anchor: '80%',
                        allowDecimals:true,
                        decimalPrecision:2,
                        allowNegative : false,
                        disabled:true,
                        gwidth: 140
                    },
                    type:'NumberField',
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'agente_venta',
                        fieldLabel: 'Agente Venta',
                        disabled: true,
                        anchor: '100%',
                        gwidth: 270
                    },
                    type:'TextField',
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'codigo_agente',
                        fieldLabel: 'Codigo Agente',
                        anchor: '70%',
                        disabled: true,
                        gwidth: 100
                    },
                    type:'TextField',
                    id_grupo:3,
                    grid:true,
                    form:true
                },
                ///modificado
                {
                    config:{
                        name: 'numero_tarjeta',
                        fieldLabel: 'No Tarjeta',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        enableKeyEvents: true,
                        minLength:15,
                        maxLength:20,
                        maskRe: /[0-9\s]+/i,
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                ///nuevo
                {
                    config:{
                        name: 'mco',
                        fieldLabel: 'MCO',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        minLength:15,
                        maxLength:20
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'codigo_tarjeta',
                        fieldLabel: 'Codigo de Autorización 1',
                        allowBlank: true,
                        anchor: '80%',
                        minLength:6,
                        maxLength:6,
                        style:'text-transform:uppercase;',
                        maskRe: /[a-zA-Z0-9]+/i,
                        regex: /[a-zA-Z0-9]+/i

                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },
                {
                    config: {
                        name: 'id_auxiliar',
                        fieldLabel: 'Cuenta Corriente',
                        allowBlank: true,
                        emptyText: '',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_contabilidad/control/Auxiliar/listarAuxiliar',
                            id: 'id_auxiliar',
                            root: 'datos',
                            sortInfo: {
                                field: 'codigo_auxiliar',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_auxiliar', 'codigo_auxiliar','nombre_auxiliar'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'auxcta.codigo_auxiliar#auxcta.nombre_auxiliar',corriente:'si'}
                        }),
                        valueField: 'id_auxiliar',
                        displayField: 'nombre_auxiliar',
                        gdisplayField: 'codigo_auxiliar',
                        hiddenName: 'id_auxiliar',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><b><p style="color:red;">{nombre_auxiliar}</p><p>Codigo: <span style="color:green;">{codigo_auxiliar}</span></p></b></div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        gwidth: 150,
                        listWidth:350,
                        resizable:true,
                        minChars: 2,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['nombre_auxiliar']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'id_venta',
                        fieldLabel: 'Nro. Recibo',
                        allowBlank: true,
                        emptyText: '',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_ventas_facturacion/control/Venta/listarReciboBoletosAmadeus',
                            id: 'id_venta',
                            root: 'datos',
                            sortInfo: {
                                field: 'v.nro_factura, v.nombre_factura ',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_venta', 'nro_factura','nombre_factura','total_venta','saldo', 'tex_saldo','moneda'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'v.nro_factura#v.nombre_factura'}
                        }),
                        valueField: 'id_venta',
                        displayField: 'nro_factura',
                        gdisplayField: 'nro_factura',
                        hiddenName: 'id_venta',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><div style="font-weight:bold;">Numero <span style="color:blue;"">&nbsp;{nro_factura}</span><br> Nombre: <span style="color:green;"">{nombre_factura}</span> <br> Monto: <span style="color:red;">&nbsp;&nbsp;{total_venta}&nbsp;&nbsp;&nbsp{tex_saldo}</span><br></div></div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        gwidth: 150,
                        listWidth:370,
                        resizable:true,
                        minChars: 1,
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    grid: false,
                    form: true
                },
                // {
            		// 	config:{
            		// 		name: 'nro_documento',
            		// 		fieldLabel: 'Nro. Recibo',
            		// 		allowBlank: true,
                //     hidden:true,
            		// 	},
            		// 	type:'NumberField',
            		// 	id_grupo:1,
            		// 	form:true,
            		// },
                {
                    config:{
                        name: 'nro_cupon',
                        fieldLabel: 'Nro Cupon',
                        //labelStyle:'color:blue; font-weight:bold;',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        //minLength:15,
                        //maxLength:20,
                        style:{
                          background:'#B6E7CE',
                        } ,
                        gwidth: 110
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'nro_cuota',
                        fieldLabel: 'Nro Cuota',
                        //labelStyle:'color:blue; font-weight:bold;',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        //minLength:15,
                        //maxLength:20,
                        style:{
                          background:'#B6E7CE',
                        } ,
                        gwidth: 110
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },
                {
                    config: {
                        name: 'id_moneda2',
                        fieldLabel: '<img src="../../../lib/imagenes/facturacion/MonedaDolar.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Moneda</span>',
                        allowBlank: true,
                        //width:150,
                        listWidth:250,
                        resizable:true,
                        // style: {
                        //      background: '#EFFFD6',
                        //      color: 'red',
                        //      fontWeight:'bold'
                        //    },
                        emptyText: 'Moneda a pagar...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_parametros/control/Moneda/listarMoneda',
                            id: 'id_moneda',
                            root: 'datos',
                            sortInfo: {
                                field: 'moneda',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_moneda', 'codigo', 'moneda', 'codigo_internacional'],
                            remoteSort: true,
                            baseParams: {filtrar: 'si',par_filtro: 'moneda.moneda#moneda.codigo#moneda.codigo_internacional'}
                        }),
                        valueField: 'id_moneda',
                        gdisplayField : 'codigo_internacional',
                        displayField: 'codigo_internacional',
                        hiddenName: 'id_moneda',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:green;"><b style="color:black;">Moneda:</b> <b>{moneda}</b></p><p style="color:red;"><b style="color:black;">Código:</b> <b>{codigo_internacional}</b></p></div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        //disabled:true,
                        minChars: 2
                    },
                    type: 'ComboBox',
                    id_grupo: 4,
                    form: true
                },
                {
                    config: {
                        name: 'id_forma_pago2',
                        fieldLabel: 'Forma de Pago 2',
                        allowBlank: true,
                        emptyText: 'Forma de Pago...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_ventas_facturacion/control/FormaPago/listarFormaPago',
                            id: 'id_forma_pago',
                            root: 'datos',
                            sortInfo: {
                                field: 'nombre',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_forma_pago', 'nombre', 'desc_moneda','registrar_tarjeta','registrar_cc','codigo', 'codigo_fp'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'forpa.name#pago.fop_code'/*'forpa.nombre#forpa.codigo#mon.codigo_internacional'*/,sw_tipo_venta:'BOLETOS', emisionReservaBoletos:'si'}
                        }),
                        valueField: 'id_forma_pago',
                        displayField: 'nombre',
                        gdisplayField: 'forma_pago2',
                        hiddenName: 'id_forma_pago',
                        anchor: '100%',
                        tpl:'',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        gwidth: 150,
                        listWidth:350,
                        resizable:true,
                        minChars: 2,
                        disabled:true,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['forma_pago2']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 4,
                    grid: true,
                    form: true
                },
                {
                    config:{
                        name: 'monto_forma_pago2',
                        fieldLabel: 'Importe Forma de Pago 2',
                        allowBlank:true,
                        anchor: '80%',
                        allowDecimals:true,
                        decimalPrecision:2,
                        allowNegative : false,
                        disabled:true,
                        gwidth: 140,
                        style: 'background-color: #f2f23c;  background-image: none;'
                    },
                    type:'NumberField',
                    id_grupo:4,
                    grid:true,
                    form:true
                },
                //modifcado
                {
                    config:{
                        name: 'numero_tarjeta2',
                        fieldLabel: 'No Tarjeta 2',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength:50,
                        enableKeyEvents: true,
                        maskRe: /[0-9\s]+/i,
                    },
                    type:'TextField',
                    id_grupo:4,
                    grid:false,
                    form:true
                },
                ///nuevo
                {
                    config:{
                        name: 'mco2',
                        fieldLabel: 'MCO 2',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        minLength:15,
                        maxLength:20
                    },
                    type:'TextField',
                    id_grupo:4,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'codigo_tarjeta2',
                        fieldLabel: 'Codigo de Autorización 2',
                        allowBlank: true,
                        anchor: '80%',
                        maxLength:20,
                        style:'text-transform:uppercase;',
                        maskRe: /[a-zA-Z0-9]+/i,
                        regex: /[a-zA-Z0-9]+/i

                    },
                    type:'TextField',
                    id_grupo:4,
                    grid:false,
                    form:true
                },
                {
                    config: {
                        name: 'id_auxiliar2',
                        fieldLabel: 'Cuenta Corriente',
                        allowBlank: true,
                        emptyText: '',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_contabilidad/control/Auxiliar/listarAuxiliar',
                            id: 'id_auxiliar',
                            root: 'datos',
                            sortInfo: {
                                field: 'codigo_auxiliar',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_auxiliar', 'codigo_auxiliar','nombre_auxiliar'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'auxcta.codigo_auxiliar#auxcta.nombre_auxiliar',corriente:'si'}
                        }),
                        valueField: 'id_auxiliar',
                        displayField: 'nombre_auxiliar',
                        gdisplayField: 'nombre_auxiliar2',
                        hiddenName: 'id_auxiliar',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><b><p style="color:red;">{nombre_auxiliar}</p><p>Codigo: <span style="color:green;">{codigo_auxiliar}</span></p></b></div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        gwidth: 150,
                        listWidth:350,
                        resizable:true,
                        minChars: 2,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['nombre_auxiliar2']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 4,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'id_venta_2',
                        fieldLabel: 'Nro. Recibo',
                        allowBlank: true,
                        emptyText: '',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_ventas_facturacion/control/Venta/listarReciboBoletosAmadeus',
                            id: 'id_venta',
                            root: 'datos',
                            sortInfo: {
                                field: 'v.nro_factura, v.nombre_factura ',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_venta', 'nro_factura','nombre_factura','total_venta','saldo','tex_saldo','moneda'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'v.nro_factura#v.nombre_factura'}
                        }),
                        valueField: 'id_venta',
                        displayField: 'nro_factura',
                        gdisplayField: 'nro_factura',
                        hiddenName: 'id_venta',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><div style="font-weight:bold;">Numero <span style="color:blue;"">&nbsp;{nro_factura}</span><br> Nombre: <span style="color:green;"">{nombre_factura}</span> <br> Monto: <span style="color:red;">&nbsp;&nbsp;{total_venta}&nbsp;&nbsp;&nbsp{tex_saldo}</span><br></div></div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        gwidth: 150,
                        listWidth:370,
                        resizable:true,
                        minChars: 1,
                    },
                    type: 'ComboBox',
                    id_grupo: 4,
                    grid: false,
                    form: true
                },
                // {
            		// 	config:{
            		// 		name: 'nro_documento_2',
            		// 		fieldLabel: 'Nro. Recibo',
            		// 		allowBlank: true,
                //     hidden:true,
            		// 	},
            		// 	type:'NumberField',
            		// 	id_grupo:4,
            		// 	form:true,
            		// },
                {
                    config:{
                        name: 'nro_cupon_2',
                        fieldLabel: 'Nro Cupon 2',
                        //labelStyle:'color:blue; font-weight:bold;',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        // minLength:15,
                        // maxLength:20,
                        style:{
                          background:'#B6E7CE',
                        } ,
                        gwidth: 110
                    },
                    type:'TextField',
                    id_grupo:4,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'nro_cuota_2',
                        fieldLabel: 'Nro Cuota 2',
                        //labelStyle:'color:blue; font-weight:bold;',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        // minLength:15,
                        // maxLength:20,
                        style:{
                          background:'#B6E7CE',
                        } ,
                        gwidth: 110
                    },
                    type:'TextField',
                    id_grupo:4,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'fecha_emision',
                        fieldLabel: 'Fecha Emision',
                        anchor: '80%',
                        gwidth: 100,
                        disabled: true,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type:'DateField',
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'codigo_agencia',
                        fieldLabel: 'agt',
                        gwidth: 100
                    },
                    type:'TextField',
                    grid:false,
                    form:false
                },
                {
                    config:{
                        name: 'nombre_agencia',
                        fieldLabel: 'Agencia',
                        gwidth: 120
                    },
                    type:'TextField',
                    grid:false,
                    form:false
                },
                {
                    config:{
                        name: 'estado_reg',
                        fieldLabel: 'Estado Reg.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:10
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:true,
                    form:false
                },

                {
                    config:{
                        name: 'id_usuario_ai',
                        fieldLabel: '',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:4
                    },
                    type:'Field',
                    id_grupo:1,
                    grid:false,
                    form:false
                },
                {
                    config:{
                        name: 'usr_reg',
                        fieldLabel: 'Creado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:4
                    },
                    type:'Field',
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'fecha_reg',
                        fieldLabel: 'Fecha creación',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                    },
                    type:'DateField',
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'usuario_ai',
                        fieldLabel: 'Funcionaro AI',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'usr_mod',
                        fieldLabel: 'Modificado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:4
                    },
                    type:'Field',
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'fecha_mod',
                        fieldLabel: 'Fecha Modif.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                    },
                    type:'DateField',
                    id_grupo:1,
                    grid:true,
                    form:false
                }
            ],
            tam_pag:50,
            fwidth: '95%',
            fheight: '70%',
            title:'<h1>Emision Boleto<h1>',            
            ActSave:'../../sis_obingresos/control/Boleto/modificarBoletoAmadeusVenta',
            //ActDel:'../../sis_obingresos/control/Boleto/eliminarBoleto',
            //ActList:'../../sis_obingresos/control/Boleto/traerBoletosJson',
            ActList:'../../sis_obingresos/control/Boleto/listarBoletosAmadeusLocalmente',

            id_store:'id_boleto_amadeus',
            fields: [
                {name:'id_boleto_amadeus', type: 'numeric'},
                {name:'fecha_emision', type: 'date',dateFormat:'Y-m-d'},
                {name:'tipo_comision', type: 'string'},
                {name:'estado', type: 'string'},
                {name:'id_agencia', type: 'numeric'},
                {name:'moneda', type: 'string'},
                {name:'total', type: 'numeric'},
                {name:'total_moneda_extranjera', type: 'numeric'},
                {name:'pasajero', type: 'string'},
                {name:'id_moneda_boleto', type: 'numeric'},
                {name:'estado_reg', type: 'string'},
                {name:'codigo_agencia', type: 'string'},
                {name:'neto', type: 'numeric'},
                {name:'tc', type: 'numeric'},
                {name:'localizador', type: 'string'},
                {name:'monto_pagado_moneda_boleto', type: 'numeric'},
                {name:'monto_total_fp', type: 'numeric'},
                {name:'liquido', type: 'numeric'},
                {name:'comision', type: 'numeric'},
                {name:'nro_boleto', type: 'string'},
                {name:'id_usuario_ai', type: 'numeric'},
                {name:'id_usuario_reg', type: 'numeric'},
                {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'usuario_ai', type: 'string'},
                {name:'id_usuario_mod', type: 'numeric'},
                {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'usr_reg', type: 'string'},
                {name:'usr_mod', type: 'string'},
                {name:'nombre_agencia', type: 'string'},
                {name:'id_forma_pago', type: 'numeric'},
                {name:'forma_pago', type: 'string'},
                {name:'codigo_forma_pago', type: 'string'},
                {name:'forma_pago_amadeus', type: 'string'},
                {name:'numero_tarjeta', type: 'string'},
                {name:'codigo_tarjeta', type: 'string'},
                {name:'id_auxiliar', type: 'numeric'},
                {name:'nombre_auxiliar', type: 'string'},
                {name:'monto_forma_pago', type: 'numeric'},
                {name:'id_forma_pago2', type: 'numeric'},
                {name:'forma_pago2', type: 'string'},
                {name:'codigo_forma_pago2', type: 'string'},
                {name:'numero_tarjeta2', type: 'string'},
                {name:'codigo_tarjeta2', type: 'string'},
                {name:'id_auxiliar2', type: 'numeric'},
                {name:'nombre_auxiliar2', type: 'string'},
                {name:'monto_forma_pago2', type: 'numeric'},
                {name:'pais', type: 'string'},
                {name:'agente_venta', type: 'string'},
                {name:'codigo_agente', type: 'string'},
                {name:'moneda_sucursal', type: 'string'},
                {name:'moneda_fp1', type: 'string'},
                {name:'moneda_fp2', type: 'string'},
                {name:'voided', type: 'string'},
                {name:'ffid', type: 'string'},
                {name:'voucher_code', type: 'string'},
                {name:'mco', type: 'string'},
                {name:'mco2', type: 'string'},
                {name:'ffid_consul', type: 'string'},
                {name:'voucher_consu', type: 'string'},
                {name:'trans_code', type: 'string'},
                {name:'trans_issue_indicator', type: 'string'},
                {name:'trans_code_exch', type: 'string'},
                {name:'impreso', type: 'string'}

            ],
            sortInfo:{
                field: 'nro_boleto',
                direction: 'DESC'
            },
            arrayDefaultColumHidden:['estado_reg','usuario_ai',
                'fecha_reg','fecha_mod','usr_reg','usr_mod','codigo_agencia','nombre_agencia','neto','comision', 'trans_code', 'trans_code_exch'],

            bdel:false,
            bsave:false,
            bnew:false,
            bedit:false,
            btest:false,

            iniciarEventos : function () {

              /*Mandaremos el filtro para regionales (Ismael Valdivia 24/11/2020)*/
              this.condicionesRegionales();
              /*******************************************************************/
                this.Cmp.monto_forma_pago.on('change',function(field,newValue,oldValue){
                    var valueOld = this.getMontoMonBol(oldValue, this.Cmp.moneda_fp1.getValue());
                    var valueNew = this.getMontoMonBol(newValue, this.Cmp.moneda_fp1.getValue());
                    if (valueNew < valueOld) {
                        this.Cmp.id_forma_pago2.setDisabled(false);
                        this.Cmp.monto_forma_pago2.setDisabled(false);
                    }
                },this);

                /*Aqui aumentamos cuando se seleccione la moneda se resetee el combo recibo  (22/04/2021)*/
                this.Cmp.id_moneda.on('select',function(combo,record){
                    this.Cmp.id_venta.reset()
                    this.Cmp.id_venta.store.baseParams.id_moneda=record.data.id_moneda;
                    this.Cmp.id_venta.store.baseParams.id_auxiliar_anticipo=this.Cmp.id_auxiliar.getValue();
                    this.Cmp.id_venta.modificado = true;
                },this);

                this.Cmp.id_moneda2.on('select',function(combo,record){
                    this.Cmp.id_venta_2.reset()
                    this.Cmp.id_venta_2.store.baseParams.id_moneda=record.data.id_moneda;
                    this.Cmp.id_venta_2.store.baseParams.id_auxiliar_anticipo=this.Cmp.id_auxiliar2.getValue();
                    this.Cmp.id_venta_2.modificado = true;
                },this);
                /*filtro de recibo por id_auxiliar ****/
                this.Cmp.id_auxiliar.on('select', function(c,r){
                    this.Cmp.id_venta.reset()
                    this.Cmp.id_venta.store.baseParams.id_moneda=this.Cmp.id_moneda.getValue();
                    this.Cmp.id_venta.store.baseParams.id_auxiliar_anticipo=r.data.id_auxiliar;
                    this.Cmp.id_venta.modificado = true;
                },this)

                this.Cmp.id_auxiliar2.on('select', function(c,r){
                    this.Cmp.id_venta_2.reset()
                    this.Cmp.id_venta_2.store.baseParams.id_moneda=this.Cmp.id_moneda2.getValue();
                    this.Cmp.id_venta_2.store.baseParams.id_auxiliar_anticipo=r.data.id_auxiliar;
                    this.Cmp.id_venta_2.modificado = true;
                },this)
                /*****************************************************************************************/



                this.Cmp.monto_recibido_forma_pago.on('keyup',function(field,newValue,oldValue){

                    //console.log('field',field,'newValue',newValue,'oldValue',oldValue,'ORIGINAL', this.Cmp.monto_forma_pago.getValue());
                    if(this.grupo=='no') {

                      /*Aumentamos la condicion para los nuevos medios de pago (Ismael Valdivia 25/11/2020)*/
                      if (this.variables_globales.instancias_de_pago_nuevas == 'no') {
                        if (this.Cmp.moneda.getValue() == this.Cmp.moneda_fp1.getValue()) {

                            console.log('monto_recibido, misma moneda de boleto, moneda forma de pago');
                            if (newValue > (this.Cmp.total.getValue() - this.Cmp.comision.getValue())) {
                                console.log('A');
                                this.Cmp.monto_forma_pago.setValue(this.Cmp.total.getValue() - this.Cmp.comision.getValue());


                                if(this.Cmp.moneda_sucursal.getValue()=='USD') {
                                    this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                }else{


                                    if(this.Cmp.moneda_fp1.getValue()=='USD'){
                                        this.Cmp.cambio.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) * this.Cmp.tc.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                    }else{
                                        this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) / this.Cmp.tc.getValue());
                                    }

                                }

                            } else {
                                this.Cmp.monto_forma_pago.setValue(newValue);
                                this.Cmp.cambio.setValue(0);
                                /*Aumentando porque el cambio se quedaba con el monto calculado anteriormente (Ismael Valdivia 07/01/2020)*/
                                this.Cmp.cambio_moneda_extranjera.setValue(0);
                                /**********************************************************************************************************/
                            }

                        } else if(this.Cmp.moneda.getValue() == 'USD' && this.Cmp.moneda_fp1.getValue() == this.Cmp.moneda_sucursal.getValue()){

                            console.log('monto_recibido, misma moneda sucursal y moneda forma de pago, boleto en usd');
                            if (newValue > (this.Cmp.total_moneda_extranjera.getValue() * this.Cmp.tc.getValue() - this.Cmp.comision_moneda_extranjera.getValue() * this.Cmp.tc.getValue())) {
                                console.log('B');
                                this.Cmp.monto_forma_pago.setValue((this.Cmp.total_moneda_extranjera.getValue()/** this.Cmp.tc.getValue()*/ - this.Cmp.comision_moneda_extranjera.getValue()) * this.Cmp.tc.getValue());
                                //console.log('a',this.Cmp.total_moneda_extranjera.getValue(),'b',this.Cmp.tc.getValue(),'c',this.Cmp.comision_moneda_extranjera.getValue(),'d',this.Cmp.tc.getValue())

                                if(this.Cmp.moneda_sucursal.getValue()=='USD') {
                                    console.log('B1');
                                    this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                }else{
                                    console.log('B2');
                                    if(this.Cmp.moneda_fp1.getValue()=='USD'){
                                        console.log('B21');
                                        this.Cmp.cambio.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) * this.Cmp.tc.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                    }else{
                                        console.log('B22', newValue, 'ORIGINAL', this.Cmp.monto_forma_pago.getValue());
                                        console.log('extranjero', newValue, this.Cmp.monto_forma_pago.getValue(), this.Cmp.tc.getValue());
                                        this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) / this.Cmp.tc.getValue());
                                    }

                                }

                            } else {
                                this.Cmp.monto_forma_pago.setValue(newValue);
                                this.Cmp.cambio.setValue(0);
                                /*Aumentando porque el cambio se quedaba con el monto calculado anteriormente (Ismael Valdivia 07/01/2020)*/
                                this.Cmp.cambio_moneda_extranjera.setValue(0);
                                /**********************************************************************************************************/
                            }
                        }
                        else if(this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && this.Cmp.moneda_fp1.getValue() == 'USD'){

                            console.log('monto_recibido, misma moneda sucursal y moneda boleto, moneda forma de pago en usd');
                            if (newValue > (this.Cmp.total_moneda_extranjera.getValue() - this.Cmp.comision_moneda_extranjera.getValue())) {
                                console.log('C');
                                this.Cmp.monto_forma_pago.setValue((this.Cmp.total.getValue() - this.Cmp.comision.getValue())/ this.Cmp.tc.getValue());

                                //this.Cmp.monto_recibido_forma_pago.setValue((this.Cmp.total.getValue() - this.Cmp.comision.getValue())/ this.Cmp.tc.getValue());
                                //this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());

                                if(this.Cmp.moneda_sucursal.getValue()=='USD') {
                                    this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                }else{

                                    if(this.Cmp.moneda_fp1.getValue()=='USD'){
                                        this.Cmp.cambio.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) * this.Cmp.tc.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                    }else{
                                        this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) / this.Cmp.tc.getValue());
                                    }

                                }
                            } else {
                                this.Cmp.monto_forma_pago.setValue(newValue);
                                this.Cmp.cambio.setValue(0);
                                /*Aumentando porque el cambio se quedaba con el monto calculado anteriormente (Ismael Valdivia 07/01/2020)*/
                                this.Cmp.cambio_moneda_extranjera.setValue(0);
                                /**********************************************************************************************************/
                            }
                        }
                        else { console.log('D');
                            this.Cmp.monto_forma_pago.setValue(0);
                            this.Cmp.cambio.setValue(0);
                            /*Aumentando porque el cambio se quedaba con el monto calculado anteriormente (Ismael Valdivia 07/01/2020)*/
                            this.Cmp.cambio_moneda_extranjera.setValue(0);
                            /**********************************************************************************************************/
                        }
                      }//Aqui Empieza la condicion para las nuevas instancias
                      else {
                        if (this.Cmp.moneda.getValue() == this.Cmp.id_moneda.lastSelectionText) {

                            console.log('monto_recibido, misma moneda de boleto, moneda forma de pago');
                            if (this.Cmp.monto_recibido_forma_pago.getValue() > (this.Cmp.total.getValue() - this.Cmp.comision.getValue())) {
                                console.log('A');
                                this.Cmp.monto_forma_pago.setValue(this.Cmp.total.getValue() - this.Cmp.comision.getValue());


                                if(this.Cmp.moneda_sucursal.getValue()=='USD') {
                                    this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue());
                                }else{


                                    if(this.Cmp.id_moneda.lastSelectionText=='USD'){
                                        this.Cmp.cambio.setValue((this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue()) * this.Cmp.tc.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue(this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue());
                                    }else{
                                        this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue((this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue()) / this.Cmp.tc.getValue());
                                    }

                                }

                            } else {
                                this.Cmp.monto_forma_pago.setValue(this.Cmp.monto_recibido_forma_pago.getValue());
                                this.Cmp.cambio.setValue(0);
                                /*Aumentando porque el cambio se quedaba con el monto calculado anteriormente (Ismael Valdivia 07/01/2020)*/
                                this.Cmp.cambio_moneda_extranjera.setValue(0);
                                /**********************************************************************************************************/
                            }

                        } else if(this.Cmp.moneda.getValue() == 'USD' && this.Cmp.id_moneda.lastSelectionText == this.Cmp.moneda_sucursal.getValue()){

                            console.log('monto_recibido, misma moneda sucursal y moneda forma de pago, boleto en usd');
                            if (this.Cmp.monto_recibido_forma_pago.getValue() > (this.Cmp.total_moneda_extranjera.getValue() * this.Cmp.tc.getValue() - this.Cmp.comision_moneda_extranjera.getValue() * this.Cmp.tc.getValue())) {
                                console.log('B');
                                this.Cmp.monto_forma_pago.setValue((this.Cmp.total_moneda_extranjera.getValue()/** this.Cmp.tc.getValue()*/ - this.Cmp.comision_moneda_extranjera.getValue()) * this.Cmp.tc.getValue());
                                //console.log('a',this.Cmp.total_moneda_extranjera.getValue(),'b',this.Cmp.tc.getValue(),'c',this.Cmp.comision_moneda_extranjera.getValue(),'d',this.Cmp.tc.getValue())

                                if(this.Cmp.moneda_sucursal.getValue()=='USD') {
                                    console.log('B1');
                                    this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue());
                                }else{
                                    console.log('B2');
                                    if(this.Cmp.id_moneda.lastSelectionText=='USD'){
                                        console.log('B21');
                                        this.Cmp.cambio.setValue((this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue()) * this.Cmp.tc.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                    }else{
                                        console.log('B22', this.Cmp.monto_recibido_forma_pago.getValue(), 'ORIGINAL', this.Cmp.monto_forma_pago.getValue());
                                        console.log('extranjero', this.Cmp.monto_recibido_forma_pago.getValue(), this.Cmp.monto_forma_pago.getValue(), this.Cmp.tc.getValue());
                                        this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) / this.Cmp.tc.getValue());
                                    }

                                }

                            } else {
                                this.Cmp.monto_forma_pago.setValue(this.Cmp.monto_recibido_forma_pago.getValue());
                                this.Cmp.cambio.setValue(0);
                                /*Aumentando porque el cambio se quedaba con el monto calculado anteriormente (Ismael Valdivia 07/01/2020)*/
                                this.Cmp.cambio_moneda_extranjera.setValue(0);
                                /**********************************************************************************************************/
                            }
                        }
                        else if(this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && this.Cmp.id_moneda.lastSelectionText == 'USD'){

                            console.log('monto_recibido, misma moneda sucursal y moneda boleto, moneda forma de pago en usd');
                            if (this.Cmp.monto_recibido_forma_pago.getValue() > (this.Cmp.total_moneda_extranjera.getValue() - this.Cmp.comision_moneda_extranjera.getValue())) {
                                console.log('C');
                                this.Cmp.monto_forma_pago.setValue((this.Cmp.total.getValue() - this.Cmp.comision.getValue())/ this.Cmp.tc.getValue());

                                //this.Cmp.monto_recibido_forma_pago.setValue((this.Cmp.total.getValue() - this.Cmp.comision.getValue())/ this.Cmp.tc.getValue());
                                //this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());

                                if(this.Cmp.moneda_sucursal.getValue()=='USD') {
                                    this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue());
                                }else{

                                    if(this.Cmp.id_moneda.lastSelectionText=='USD'){
                                        this.Cmp.cambio.setValue((this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue()) * this.Cmp.tc.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue(this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue());
                                    }else{
                                        this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue((this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue()) / this.Cmp.tc.getValue());
                                    }

                                }
                            } else {
                                this.Cmp.monto_forma_pago.setValue(this.Cmp.monto_recibido_forma_pago.getValue());
                                this.Cmp.cambio.setValue(0);
                                /*Aumentando porque el cambio se quedaba con el monto calculado anteriormente (Ismael Valdivia 07/01/2020)*/
                                this.Cmp.cambio_moneda_extranjera.setValue(0);
                                /**********************************************************************************************************/
                            }
                        }
                        else { console.log('D');
                            this.Cmp.monto_forma_pago.setValue(0);
                            this.Cmp.cambio.setValue(0);
                            /*Aumentando porque el cambio se quedaba con el monto calculado anteriormente (Ismael Valdivia 07/01/2020)*/
                            this.Cmp.cambio_moneda_extranjera.setValue(0);
                            /**********************************************************************************************************/
                        }
                      }//Aqui termina la condicion para las nuevas instancias

                    }else{

                      if (this.variables_globales.instancias_de_pago_nuevas == 'no') {
                        var valueOld = this.Cmp.monto_forma_pago.getValue();

                        if (this.Cmp.moneda_fp1.getValue() !== 'USD') {

                            if (newValue > (this.total_grupo['total_boletos_'+this.Cmp.moneda_fp1.getValue()] - this.total_grupo['total_comision_'+this.Cmp.moneda_fp1.getValue()])) {
                                console.log('AA');
                                this.Cmp.monto_forma_pago.setValue(this.total_grupo['total_boletos_'+this.Cmp.moneda_fp1.getValue()] - this.total_grupo['total_comision_'+this.Cmp.moneda_fp1.getValue()]);
                                //this.Cmp.monto_recibido_forma_pago.setValue(this.total_grupo['total_boletos_'+this.Cmp.moneda_fp1.getValue()] - this.total_grupo['total_comision_'+this.Cmp.moneda_fp1.getValue()]);
                                //this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());

                                if(this.Cmp.moneda_sucursal.getValue()=='USD') {
                                    this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                }else{

                                    if(this.Cmp.moneda_fp1.getValue()=='USD'){
                                        this.Cmp.cambio.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) * this.Cmp.tc.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                    }else{
                                        this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) / this.Cmp.tc.getValue());
                                    }

                                }
                            } else {
                                this.Cmp.monto_forma_pago.setValue(newValue);
                                this.Cmp.cambio.setValue(0);
                                /*Aumentando porque el cambio se quedaba con el monto calculado anteriormente (Ismael Valdivia 07/01/2020)*/
                                this.Cmp.cambio_moneda_extranjera.setValue(0);
                                /**********************************************************************************************************/
                            }
                        } else {
                            if (newValue > (this.total_grupo['total_boletos_USD'] - this.total_grupo['total_comision_USD'])) {
                                console.log('BB');
                                this.Cmp.monto_forma_pago.setValue(this.total_grupo['total_boletos_USD'] - this.total_grupo['total_comision_USD']);
                                //this.Cmp.monto_recibido_forma_pago.setValue(this.total_grupo['total_boletos_USD'] - this.total_grupo['total_comision_USD']);
                                //this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());

                                if(this.Cmp.moneda_sucursal.getValue()=='USD') {
                                    this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                }else{

                                    if(this.Cmp.moneda_fp1.getValue()=='USD'){
                                        this.Cmp.cambio.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) * this.Cmp.tc.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                    }else{
                                        this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) / this.Cmp.tc.getValue());
                                    }

                                }
                            } else {

                                this.Cmp.monto_forma_pago.setValue(newValue);
                                this.Cmp.cambio.setValue(0);
                                /*Aumentando porque el cambio se quedaba con el monto calculado anteriormente (Ismael Valdivia 07/01/2020)*/
                                this.Cmp.cambio_moneda_extranjera.setValue(0);
                                /**********************************************************************************************************/
                            }
                        }
                        var valueNew = this.Cmp.monto_forma_pago.getValue();

                        if (valueNew < valueOld) {

                            this.Cmp.id_forma_pago2.setDisabled(false);
                            this.Cmp.monto_forma_pago2.setDisabled(false);
                        }
                        //this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                      } /*Aqui empieza condicion para nuevas instancias de pago*/
                      else {
                        var valueOld = this.Cmp.monto_forma_pago.getValue();
                        console.log("aqui calcular el grupo",this.Cmp.id_moneda.lastSelectionText);
                        if (this.Cmp.id_moneda.lastSelectionText !== 'USD') {
                            if (this.Cmp.monto_recibido_forma_pago.getValue() > (this.total_grupo['total_boletos_'+this.Cmp.id_moneda.lastSelectionText] - this.total_grupo['total_comision_'+this.Cmp.id_moneda.lastSelectionText])) {
                                console.log('AA');
                                this.Cmp.monto_forma_pago.setValue(this.total_grupo['total_boletos_'+this.Cmp.id_moneda.lastSelectionText] - this.total_grupo['total_comision_'+this.Cmp.id_moneda.lastSelectionText]);
                                //this.Cmp.monto_recibido_forma_pago.setValue(this.total_grupo['total_boletos_'+this.Cmp.moneda_fp1.getValue()] - this.total_grupo['total_comision_'+this.Cmp.moneda_fp1.getValue()]);
                                //this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());

                                if(this.Cmp.moneda_sucursal.getValue()=='USD') {
                                    this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue());
                                }else{

                                    if(this.Cmp.id_moneda.lastSelectionText=='USD'){
                                        this.Cmp.cambio.setValue((this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue()) * this.Cmp.tc.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                    }else{
                                        this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue((this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue()) / this.Cmp.tc.getValue());
                                    }

                                }
                            } else {
                                this.Cmp.monto_forma_pago.setValue(this.Cmp.monto_recibido_forma_pago.getValue());
                                this.Cmp.cambio.setValue(0);
                                /*Aumentando porque el cambio se quedaba con el monto calculado anteriormente (Ismael Valdivia 07/01/2020)*/
                                this.Cmp.cambio_moneda_extranjera.setValue(0);
                                /**********************************************************************************************************/
                            }
                        } else {
                            if (this.Cmp.monto_recibido_forma_pago.getValue() > (this.total_grupo['total_boletos_USD'] - this.total_grupo['total_comision_USD'])) {
                                console.log('BB');
                                this.Cmp.monto_forma_pago.setValue(this.total_grupo['total_boletos_USD'] - this.total_grupo['total_comision_USD']);
                                //this.Cmp.monto_recibido_forma_pago.setValue(this.total_grupo['total_boletos_USD'] - this.total_grupo['total_comision_USD']);
                                //this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());

                                if(this.Cmp.moneda_sucursal.getValue()=='USD') {
                                    this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue());
                                }else{

                                    if(this.Cmp.id_moneda.lastSelectionText=='USD'){
                                        this.Cmp.cambio.setValue((this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue()) * this.Cmp.tc.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue(this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue());
                                    }else{
                                        this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue((this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue()) / this.Cmp.tc.getValue());
                                    }

                                }
                            } else {

                                this.Cmp.monto_forma_pago.setValue(this.Cmp.monto_recibido_forma_pago.getValue());
                                this.Cmp.cambio.setValue(0);
                                /*Aumentando porque el cambio se quedaba con el monto calculado anteriormente (Ismael Valdivia 07/01/2020)*/
                                this.Cmp.cambio_moneda_extranjera.setValue(0);
                                /**********************************************************************************************************/
                            }
                        }
                        var valueNew = this.Cmp.monto_recibido_forma_pago.getValue();

                        if (valueNew < valueOld) {

                            this.Cmp.id_forma_pago2.setDisabled(false);
                            this.Cmp.monto_forma_pago2.setDisabled(false);
                        }
                        //this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                      }//Aqui termina condicion para las nuevos medios de pago

                    }
                },this);
                /*
                 this.Cmp.monto_forma_pago2.on('blur',function(field){
                 console.log(this);
                 console.log(this.Cmp);
                 this.Cmp.cambio.setValue((this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue()) - newValue);
                 });*/

                this.Cmp.id_forma_pago.on('select', function (combo,record){

                  if (this.variables_globales.instancias_de_pago_nuevas == 'no') {
                    if (record) {
                        this.Cmp.moneda_fp1.setValue(record.data.desc_moneda);
                        this.manejoComponentesFp1(record.data.id_forma_pago,record.data.codigo);
                    } else {
                        this.manejoComponentesFp1(this.Cmp.id_forma_pago.getValue(),this.Cmp.codigo_forma_pago.getValue());
                    }
                    if (this.grupo == 'no') {

                        var monto_pagado_fp2 = this.getMontoMonBol(this.Cmp.monto_forma_pago2.getValue(), this.Cmp.moneda_fp2.getValue());

                        if (monto_pagado_fp2 > -1) {
                            //Si la forma de pago y el boleto estan en la misma moneda
                            if (this.Cmp.moneda.getValue() == this.Cmp.moneda_fp1.getValue()) {
                                console.log('id_forma_pago, misma moneda de boleto, moneda forma de pago');
                                this.Cmp.monto_forma_pago.setValue(this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2);
                                this.Cmp.monto_recibido_forma_pago.setValue(this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2);
                                //this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue()-this.Cmp.monto_forma_pago.getValue());
                            }
                            //Si el boleto esta en usd y la forma de pago es distinta a usd y la forma de pago es igual a la moneda de la sucursal
                            else if (this.Cmp.moneda.getValue() == 'USD' && this.Cmp.moneda_fp1.getValue() == this.Cmp.moneda_sucursal.getValue()) {
                                console.log('id_forma_pago, misma moneda sucursal y moneda forma de pago, boleto en usd');
                                //convertir de  dolares a moneda sucursal(multiplicar)
                                //convertir de  moneda sucursal a dolares(dividir)

                                /*Comentando porque el redondeo lo esta haciendo por todos los decimales cambiaremos para tomar igual q la base de datos solo los 2 primeros decimales Ismael Valdivia (07/01/2019)*/
                                //this.Cmp.monto_forma_pago.setValue(this.round(((this.Cmp.total.getValue() -this.Cmp.comision.getValue() - monto_pagado_fp2) * this.Cmp.tc.getValue()), 2));
                                //this.Cmp.monto_recibido_forma_pago.setValue(this.round(((this.Cmp.total.getValue() -this.Cmp.comision.getValue() - monto_pagado_fp2) * this.Cmp.tc.getValue()), 2));
                                /********************************************************************************************************/

                                /*Aumentando esta parte*/
                                this.Cmp.monto_forma_pago.setValue(((this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2) * this.Cmp.tc.getValue()).toFixed(2));
                                this.Cmp.monto_recibido_forma_pago.setValue((((this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2) * this.Cmp.tc.getValue())).toFixed(2));
                                /***********************/

                                //this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue()-this.Cmp.monto_forma_pago.getValue());

                                //Si el boleto esta en moneda sucursal y la forma de pago es usd y la moneda de la sucursales distinta a usd
                            } else if (this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && this.Cmp.moneda_fp1.getValue() == 'USD') {
                                console.log('id_forma_pago, misma moneda sucursal y moneda boleto, moneda forma de pago en usd');

                                /*Comentando porque el redondeo lo esta haciendo por todos los decimales cambiaremos para tomar igual q la base de datos solo los 2 primeros decimales Ismael Valdivia (07/01/2019)*/
                                //this.Cmp.monto_forma_pago.setValue(this.round(((this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2) / this.Cmp.tc.getValue()), 2));
                                //this.Cmp.monto_recibido_forma_pago.setValue(this.round(((this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2) / this.Cmp.tc.getValue()), 2));
                                /*******************************************************************************************************************/
                                /*Aumentando esta parte*/
                                this.Cmp.monto_forma_pago.setValue(((this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2) / this.Cmp.tc.getValue()).toFixed(2));
                                this.Cmp.monto_recibido_forma_pago.setValue((((this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2) / this.Cmp.tc.getValue())).toFixed(2));
                                /***********************/



                                //this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue()-this.Cmp.monto_forma_pago.getValue());

                            } else {
                                this.Cmp.monto_forma_pago.setValue(0);
                                //this.Cmp.monto_recibido_forma_pago.setValue(0);
                            }
                        } else {
                            this.Cmp.monto_forma_pago.setValue(0);
                        }
                    }else {
                        this.calculoFp1Grupo(record);
                    }
                  } /*Aqui empieza condicion para nuevos medios de pago*/
                  else {
                    if (record) {
                        this.Cmp.moneda_fp1.setValue(this.Cmp.id_moneda.lastSelectionText);
                        this.manejoComponentesFp1(record.data.id_forma_pago,record.data.codigo);
                        this.codigo_medio_pago = record.data.codigo_fp;
                    } else {
                        this.manejoComponentesFp1(this.Cmp.id_forma_pago.getValue(),this.Cmp.codigo_forma_pago.getValue());
                    }
                    if (this.grupo == 'no') {

                        var monto_pagado_fp2 = this.getMontoMonBol(this.Cmp.monto_forma_pago2.getValue(), this.Cmp.id_moneda2.lastSelectionText);

                        if (monto_pagado_fp2 > -1) {
                            //Si la forma de pago y el boleto estan en la misma moneda
                            if (this.Cmp.moneda.getValue() == this.Cmp.moneda_fp1.getValue()) {
                                console.log('id_forma_pago, misma moneda de boleto, moneda forma de pago');
                                this.Cmp.monto_forma_pago.setValue(this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2);
                                this.Cmp.monto_recibido_forma_pago.setValue(this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2);
                                //this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue()-this.Cmp.monto_forma_pago.getValue());
                            }
                            //Si el boleto esta en usd y la forma de pago es distinta a usd y la forma de pago es igual a la moneda de la sucursal
                            else if (this.Cmp.moneda.getValue() == 'USD' && this.Cmp.moneda_fp1.getValue() == this.Cmp.moneda_sucursal.getValue()) {
                                console.log('id_forma_pago, misma moneda sucursal y moneda forma de pago, boleto en usd');
                                //convertir de  dolares a moneda sucursal(multiplicar)
                                //convertir de  moneda sucursal a dolares(dividir)

                                /*Comentando porque el redondeo lo esta haciendo por todos los decimales cambiaremos para tomar igual q la base de datos solo los 2 primeros decimales Ismael Valdivia (07/01/2019)*/
                                //this.Cmp.monto_forma_pago.setValue(this.round(((this.Cmp.total.getValue() -this.Cmp.comision.getValue() - monto_pagado_fp2) * this.Cmp.tc.getValue()), 2));
                                //this.Cmp.monto_recibido_forma_pago.setValue(this.round(((this.Cmp.total.getValue() -this.Cmp.comision.getValue() - monto_pagado_fp2) * this.Cmp.tc.getValue()), 2));
                                /********************************************************************************************************/

                                /*Aumentando esta parte*/
                                this.Cmp.monto_forma_pago.setValue(((this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2) * this.Cmp.tc.getValue()).toFixed(2));
                                this.Cmp.monto_recibido_forma_pago.setValue((((this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2) * this.Cmp.tc.getValue())).toFixed(2));
                                /***********************/

                                //this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue()-this.Cmp.monto_forma_pago.getValue());

                                //Si el boleto esta en moneda sucursal y la forma de pago es usd y la moneda de la sucursales distinta a usd
                            } else if (this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && this.Cmp.moneda_fp1.getValue() == 'USD') {
                                console.log('id_forma_pago, misma moneda sucursal y moneda boleto, moneda forma de pago en usd');

                                /*Comentando porque el redondeo lo esta haciendo por todos los decimales cambiaremos para tomar igual q la base de datos solo los 2 primeros decimales Ismael Valdivia (07/01/2019)*/
                                //this.Cmp.monto_forma_pago.setValue(this.round(((this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2) / this.Cmp.tc.getValue()), 2));
                                //this.Cmp.monto_recibido_forma_pago.setValue(this.round(((this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2) / this.Cmp.tc.getValue()), 2));
                                /*******************************************************************************************************************/
                                /*Aumentando esta parte*/
                                this.Cmp.monto_forma_pago.setValue(((this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2) / this.Cmp.tc.getValue()).toFixed(2));
                                this.Cmp.monto_recibido_forma_pago.setValue((((this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2) / this.Cmp.tc.getValue())).toFixed(2));
                                /***********************/



                                //this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue()-this.Cmp.monto_forma_pago.getValue());

                            } else {
                                this.Cmp.monto_forma_pago.setValue(0);
                                //this.Cmp.monto_recibido_forma_pago.setValue(0);
                            }
                        } else {
                            this.Cmp.monto_forma_pago.setValue(0);
                        }
                    }else {
                        this.calculoFp1Grupo(this.Cmp.id_moneda.lastSelectionText);
                    }
                  } //Aqui termina if medios de pago


                },this);

                this.Cmp.id_forma_pago2.on('select', function (combo,record) {

                  if (this.variables_globales.instancias_de_pago_nuevas == 'no') {
                    if (record) {
                        this.Cmp.moneda_fp2.setValue(record.data.desc_moneda);
                        this.manejoComponentesFp2(record.data.id_forma_pago,record.data.codigo);
                    } else {
                        this.manejoComponentesFp2(this.Cmp.id_forma_pago2.getValue(),this.Cmp.codigo_forma_pago2.getValue());
                    }
                    if (this.grupo == 'no') {
                        var monto_pagado_fp1 = this.getMontoMonBol(this.Cmp.monto_forma_pago.getValue(), this.Cmp.moneda_fp1.getValue());

                        if (monto_pagado_fp1 > -1) {
                            //Si la forma de pago y el boleto estan en la misma moneda
                            if (this.Cmp.moneda.getValue() == this.Cmp.moneda_fp2.getValue()) {
                                this.Cmp.monto_forma_pago2.setValue(this.Cmp.total.getValue() - monto_pagado_fp1);
                            }
                            //Si el boleto esta en usd y la forma de pago es distinta a usd y la forma de pago es igual a la moneda de la sucursal
                            else if (this.Cmp.moneda.getValue() == 'USD' && this.Cmp.moneda_fp2.getValue() == this.Cmp.moneda_sucursal.getValue()) {
                                //convertir de  dolares a moneda sucursal(multiplicar)
                                /*Comentando esta parte para tomar los dos primeros decimales (Ismael Valdivia 07/01/2020)*/
                                //this.Cmp.monto_forma_pago2.setValue(this.round(((this.Cmp.total.getValue() - monto_pagado_fp1) * this.Cmp.tc.getValue()), 2));
                                /*Aumentando esta parte*/
                                this.Cmp.monto_forma_pago2.setValue(((this.Cmp.total.getValue() - monto_pagado_fp1) * this.Cmp.tc.getValue()).toFixed(2));
                                /***********************/

                                //Si el boleto esta en moneda sucursal y la forma de pago es usd y la moneda de la sucursales distinta a usd
                            } else if (this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && this.Cmp.moneda_fp2.getValue() == 'USD') {
                                //convertir de  moneda sucursal a dolares(dividir)
                                /*Comentando esta parte para tomar los dos primeros decimales (Ismael Valdivia 07/01/2020)*/
                                //this.Cmp.monto_forma_pago2.setValue(this.round(((this.Cmp.total.getValue() - monto_pagado_fp1) / this.Cmp.tc.getValue()), 2));
                                /*Aumentando esta parte*/
                                this.Cmp.monto_forma_pago2.setValue(((this.Cmp.total.getValue() - monto_pagado_fp1) / this.Cmp.tc.getValue()).toFixed(2));
                                /***********************/

                            } else {
                                this.Cmp.monto_forma_pago2.setValue(0);
                            }
                        } else {
                            this.Cmp.monto_forma_pago2.setValue(0);
                        }
                    }else {
                        this.calculoFp2Grupo(record);
                    }
                  } /*Aqui inisia nuevos medios de pago*/ else {
                    if (record) {
                        this.Cmp.moneda_fp2.setValue(this.Cmp.id_moneda2.lastSelectionText);
                        this.manejoComponentesFp2(record.data.id_forma_pago,record.data.codigo);
                        this.codigo_medio_pago_2 = record.data.codigo_fp;
                    } else {
                        this.manejoComponentesFp2(this.Cmp.id_forma_pago2.getValue(),this.Cmp.codigo_forma_pago2.getValue());
                    }
                    if (this.grupo == 'no') {
                        var monto_pagado_fp1 = this.getMontoMonBol(this.Cmp.monto_forma_pago.getValue(), this.Cmp.moneda_fp1.getValue());

                        if (monto_pagado_fp1 > -1) {
                            //Si la forma de pago y el boleto estan en la misma moneda
                            if (this.Cmp.moneda.getValue() == this.Cmp.moneda_fp2.getValue()) {
                                this.Cmp.monto_forma_pago2.setValue((this.Cmp.total.getValue() - this.Cmp.comision.getValue()) - monto_pagado_fp1);
                            }
                            //Si el boleto esta en usd y la forma de pago es distinta a usd y la forma de pago es igual a la moneda de la sucursal
                            else if (this.Cmp.moneda.getValue() == 'USD' && this.Cmp.moneda_fp2.getValue() == this.Cmp.moneda_sucursal.getValue()) {
                                //convertir de  dolares a moneda sucursal(multiplicar)
                                /*Comentando esta parte para tomar los dos primeros decimales (Ismael Valdivia 07/01/2020)*/
                                //this.Cmp.monto_forma_pago2.setValue(this.round(((this.Cmp.total.getValue() - monto_pagado_fp1) * this.Cmp.tc.getValue()), 2));
                                /*Aumentando esta parte*/
                                this.Cmp.monto_forma_pago2.setValue((((this.Cmp.total.getValue() - this.Cmp.comision.getValue()) - monto_pagado_fp1) * this.Cmp.tc.getValue()).toFixed(2));
                                /***********************/

                                //Si el boleto esta en moneda sucursal y la forma de pago es usd y la moneda de la sucursales distinta a usd
                            } else if (this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && this.Cmp.moneda_fp2.getValue() == 'USD') {
                                //convertir de  moneda sucursal a dolares(dividir)
                                /*Comentando esta parte para tomar los dos primeros decimales (Ismael Valdivia 07/01/2020)*/
                                //this.Cmp.monto_forma_pago2.setValue(this.round(((this.Cmp.total.getValue() - monto_pagado_fp1) / this.Cmp.tc.getValue()), 2));
                                /*Aumentando esta parte*/
                                this.Cmp.monto_forma_pago2.setValue((((this.Cmp.total.getValue() - this.Cmp.comision.getValue()) - monto_pagado_fp1) / this.Cmp.tc.getValue()).toFixed(2));
                                /***********************/

                            } else {
                                this.Cmp.monto_forma_pago2.setValue(0);
                            }
                        } else {
                            this.Cmp.monto_forma_pago2.setValue(0);
                        }
                    }else {
                        this.calculoFp2Grupo(this.Cmp.id_moneda2.lastSelectionText);
                    }

                  } /*Aqui finaliza condicion medios de pago*/

                },this);

                this.Cmp.tipo_comision.on('select', function (combo,record) {
                    if(this.grupo=='no') {
                        if (record['json'][0] == 'nacional') {
                            /// fecha_emision
                            var f1 = new Date('02/01/2018');
                            var f2 = new Date(this.Cmp.fecha_emision.getValue());
                            console.log('f1',f1.dateFormat('d/m/Y'));
                            console.log('f2',f2.dateFormat('d/m/Y'));
                            /*  if (f2 >= f1 ){
                                      console.log('mayor');
                                  this.Cmp.comision.setValue((this.Cmp.neto.getValue() * 0.06).toFixed(2));
                              }else{
                                      console.log('menor');
                                  this.Cmp.comision.setValue((this.Cmp.neto.getValue() * 0.1).toFixed(2));
                              }*/
                            this.Cmp.comision.setValue((this.Cmp.neto.getValue() * 0.06).toFixed(2));
                            if(this.Cmp.moneda.getValue()!=='USD') {
                                this.Cmp.comision_moneda_extranjera.setValue(this.Cmp.comision.getValue() / this.Cmp.tc.getValue());
                            }else{
                                this.Cmp.comision_moneda_extranjera.setValue(this.Cmp.comision.getValue());
                            }
                        } else {
                            if (record['json'][0] == 'internacional') {
                                this.Cmp.comision.setValue((this.Cmp.neto.getValue() * 0.06).toFixed(2));
                                if(this.Cmp.moneda.getValue()!=='USD') {
                                    this.Cmp.comision_moneda_extranjera.setValue(this.Cmp.comision.getValue() / this.Cmp.tc.getValue());
                                }else{
                                    this.Cmp.comision_moneda_extranjera.setValue(this.Cmp.comision.getValue());
                                }
                            } else {
                                this.Cmp.comision.setValue(0);
                                this.Cmp.comision_moneda_extranjera.setValue(0);
                            }
                        }
                        if(this.Cmp.moneda_fp1.getValue()!=='USD') {
                            this.Cmp.monto_forma_pago.setValue(this.Cmp.total.getValue() - this.Cmp.comision.getValue());
                            this.Cmp.monto_recibido_forma_pago.setValue(this.Cmp.total.getValue() - this.Cmp.comision.getValue());
                            this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue() - (this.Cmp.total.getValue() - this.Cmp.comision.getValue()));
                        }else{
                            this.Cmp.monto_forma_pago.setValue(this.Cmp.total_moneda_extranjera.getValue() - this.Cmp.comision_moneda_extranjera.getValue());
                            this.Cmp.monto_recibido_forma_pago.setValue(this.Cmp.total_moneda_extranjera.getValue() - this.Cmp.comision_moneda_extranjera.getValue());
                            this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue() - (this.Cmp.total_moneda_extranjera.getValue() - this.Cmp.comision_moneda_extranjera.getValue()));
                        }
                    }else{
                        if (record['json'][0] == 'nacional') {

                            var f1 = new Date('02/01/2018');
                            var f2 = new Date(this.Cmp.fecha_emision.getValue());
                            console.log('f1',f1.dateFormat('d/m/Y'));
                            console.log('f2',f2.dateFormat('d/m/Y'));
                            /* if (f2 >= f1 ){
                                 console.log('mayor');
                                 this.Cmp.monto_total_comision.setValue((this.Cmp.monto_total_neto.getValue() * 0.06).toFixed(2));
                             }else{
                                 console.log('menor');
                                 this.Cmp.monto_total_comision.setValue((this.Cmp.monto_total_neto.getValue() * 0.1).toFixed(2));
                             }*/
                            this.Cmp.monto_total_comision.setValue((this.Cmp.monto_total_neto.getValue() * 0.06).toFixed(2));
                            this.Cmp.monto_forma_pago.setValue(this.Cmp.monto_total_boletos.getValue() - this.Cmp.monto_total_comision.getValue());
                            this.Cmp.monto_recibido_forma_pago.setValue(this.Cmp.monto_total_boletos.getValue() - this.Cmp.monto_total_comision.getValue());
                        } else {
                            if (record['json'][0] == 'internacional') {
                                this.Cmp.monto_total_comision.setValue((this.Cmp.monto_total_neto.getValue() * 0.06).toFixed(2));
                                this.Cmp.monto_forma_pago.setValue(this.Cmp.monto_total_boletos.getValue() - this.Cmp.monto_total_comision.getValue());
                                this.Cmp.monto_recibido_forma_pago.setValue(this.Cmp.monto_total_boletos.getValue() - this.Cmp.monto_total_comision.getValue());
                            } else {
                                this.Cmp.monto_total_comision.setValue(0);
                                this.Cmp.monto_forma_pago.setValue(this.Cmp.monto_total_neto.getValue() - this.Cmp.monto_total_comision.getValue());
                                this.Cmp.monto_recibido_forma_pago.setValue(this.Cmp.monto_total_neto.getValue() - this.Cmp.monto_total_comision.getValue());
                            }
                        }
                    }
                },this);

                this.Cmp.nit.on('blur',function(c) {
                    if (this.Cmp.nit.getValue() != '' || this.Cmp.nit.getValue() == '0') {
                        this.Cmp.razonSocial.reset();
                            Ext.Ajax.request({
                                url : '../../sis_ventas_facturacion/control/VentaFacturacion/RecuperarCliente',
                                params : {
                                    'nit' : this.Cmp.nit.getValue(),
                                    'razon_social' : this.Cmp.razonSocial.getValue(),
                                },
                                success: function(resp){
                                var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                                this.Cmp.razonSocial.setValue(reg.ROOT.datos.razon);
                                this.Cmp.id_cliente.setValue(reg.ROOT.datos.id_cliente);
                            },
                                failure : this.conexionFailure,
                                timeout : this.timeout,
                                scope : this
                        });

                    }
                }, this)

            },

            // onDestroy: function() {
          	// 		//Phx.baseInterfaz.superclass.destroy.call(this,c);
          	// 		this.iniciar_tiempo = 'no';
            //     console.log("destroy ventana",this.iniciar_tiempo);
          	// 		this.fireEvent('closepanel',this);

          	// 		if (this.window) {
          	// 				this.window.destroy();
          	// 		}
          	// 		if (this.form) {
          	// 				this.form.destroy();
          	// 		}

            //     if (this.timer_id != undefined) {
            //       Ext.TaskMgr.stop(this.timer_id);
            //       console.log("Proceso Automatico eliminado");
            //     }

            //     if (this.timer_actualizar != undefined) {
            //       Ext.TaskMgr.stop(this.timer_actualizar);
            //       console.log("Proceso Actualizar Automatico eliminado");
            //     }

            //     if (this.timer_actualizar_automatico != undefined) {
            //       Ext.TaskMgr.stop(this.timer_actualizar_automatico);
            //       console.log("Proceso Actualizar VG eliminado 30s");
            //     }

          	// 		Phx.CP.destroyPage(this.idContenedor);
          	// 		delete this;

          	// },

            onButtonAct:function () {
              Phx.vista.EmisionBoleto.superclass.onButtonAct.call(this);
            
             if (this.timer_id != undefined) {
                Ext.TaskMgr.stop(this.timer_id);
                console.log("Proceso Automatico eliminado 30s");
              }
              if (this.timer_actualizar != undefined) {
                Ext.TaskMgr.stop(this.timer_actualizar);
              }

              if (this.timer_actualizar_automatico != undefined) {
                Ext.TaskMgr.stop(this.timer_actualizar_automatico);
              }

              this.actualizar_automatico = 0;
              console.log("Se Cancelo el proceso automatico de actualizar");

              //   this.iniciarTiempo();

            },

            iniciarTiempo:function () {
                        /*Aqui Iniciamos El timer para verificar la variable Global*/
                        Ext.Ajax.request({
                            url:'../../sis_obingresos/control/Boleto/actualizarTablaError',
                            params:{error: 'si',
                                    id_punto_venta: this.id_punto_venta},
                            success:function(resp){
                                var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                                if (reg.ROOT.datos.automatico == 'si') {
                                  /*Aumentando para que los boletos sean traidos de manera automatica (Ismael Valdivia 10/01/2020)*/
                                    this.timer_id=Ext.TaskMgr.start({
                                       run: Ftimer,
                                       interval:30000,
                                       //interval:3000,
                                       scope:this
                                   });
                                  /************************************************************************************************/

                                } else {
                                  if (this.timer_id != undefined) {
                                    Ext.TaskMgr.stop(this.timer_id);
                                    console.log("Proceso Automatico eliminado por la VaGl");
                                    }

                                    this.timer_actualizar_automatico=Ext.TaskMgr.start({
                                       run: FactualizarAutomatico,
                                       interval:300000,
                                       //interval:10000,
                                       scope:this
                                   });

                                   function FactualizarAutomatico(){
                                     if (this.actualizar_automatico > 0) {
                                       this.onButtonAct();
                                     } else {
                                       this.actualizar_automatico = 1;
                                     }
                                     console.log("Se Inicio el proceso automatico de actualizar");
                                   }

                                }
                            },
                            failure: this.conexionFailure,
                            timeout:this.timeout,
                            scope:this
                        });

                      /********************************************************************/


                            function Ftimer(){

                              if (this.timer_actualizar_automatico != undefined) {
                                Ext.TaskMgr.stop(this.timer_actualizar_automatico);
                              }
                              /*Aumenanto para verificar si el servicio Amadeus Cae*/
                              Ext.Ajax.request({
                                  url:'../../sis_obingresos/control/Boleto/verificarErrorAmadeus',
                                  params:{error: 'si',
                                          id_punto_venta: this.id_punto_venta},
                                  success:function(resp){
                                      var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));

                                      if (reg.ROOT.datos.error <= 5) {

                                        if (this.store.baseParams.primera_carga == 'no') {
                                            if (this.iniciar_tiempo == 'si') {
                                              console.log("Se inicio el proceso automatico");
                                              this.onTraerBoletosTodosAutomatico();
                                            }
                                        } else {
                                          this.store.baseParams.primera_carga = 'no';
                                        }
                                      } else {
                                            Ext.TaskMgr.stop(this.timer_id);

                                            Ext.Msg.show({
                                             title:'<h1 style="color:red"><center>ERROR SERVICIO AMADEUS</center></h1>',
                                             msg: 'Se tiene problemas con el servicio Amadeus, <b style="color:red">Favor Actualizar el navegador dentro de 30 min</b> si el error persiste contactarse con Informática',
                                             maxWidth : 550,
                                             width: 550,
                                             buttons: Ext.Msg.OK,
                                             scope:this
                                          });

                                          this.timer_actualizar=Ext.TaskMgr.start({
                                             run: Factualizar,
                                             interval:1800000,
                                             //interval:15000,
                                             scope:this
                                         });

                                         function Factualizar(){
                                          console.log("aqui llega",this.actualizar_automatico);
                                           if (this.actualizar_automatico > 0) {
                                             this.onButtonAct();
                                           } else {
                                             this.actualizar_automatico = 1;
                                           }
                                           console.log("Se Inicio el proceso automatico de actualizar");
                                         }
                                           console.log("Se Cancelo el proceso automatico");
                                      }
                                  },
                                  failure: this.conexionFailure,
                                  timeout:this.timeout,
                                  scope:this
                              });
                              /*****************************************************/
                              //Ext.TaskMgr.stop(this.timer_id);

                          }


              },
            //devuelve el monto en la moenda del boleto
            getMontoMonBol : function (monto, moneda_fp) {
                //Si la forma de pago y el boleto estan en la misma moneda
                if (monto == 0) {
                    return 0;
                } else if (this.Cmp.moneda.getValue() == moneda_fp){
                    return monto;
                } //Si el boleto esta en usd y la forma de pago es distinta a usd y la forma de pago es igual a la moneda de la sucursal
                else if (this.Cmp.moneda.getValue() == 'USD' && moneda_fp == this.Cmp.moneda_sucursal.getValue()) {
                    //convertir a dolares(dividir)
                    return (monto/this.Cmp.tc.getValue()).toFixed(2);
                    //return this.roundMenor(monto/this.Cmp.tc.getValue(),2);
                    //Si el boleto esta en moneda sucursal y la forma de pago es usd y la moneda de la sucursales distinta a usd
                } else if (this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && moneda_fp == 'USD') {
                    //convertir a moneda sucursal(mutiplicar)
                    return (monto*this.Cmp.tc.getValue()).toFixed(2);
                    //return this.roundMenor(monto*this.Cmp.tc.getValue(),2);
                } else {
                    return -1;
                }
            },
            /*
             onTraerBoletos : function () {
             Phx.CP.loadingShow();
             Ext.Ajax.request({
             url:'../../sis_obingresos/control/Boleto/traerBoletosJson',
             params: {id_punto_venta: this.id_punto_venta,start:0,limit:this.tam_pag,sort:'id_boleto_amadeus',dir:'DESC',fecha:this.campo_fecha.getValue().dateFormat('Ymd'), todos:'no'},
             success:this.successSinc,
             failure: this.conexionFailure,
             timeout:this.timeout,
             scope:this
             });
             },*/
             /********************FUNCION PARA TRAER AUTOMATICAMENTE LOS BOLETOS AMADEUS (ISMAEL VALDIVIA 10/01/2020)*************************/
             onTraerBoletosTodosAutomatico : function () {

                 Ext.Ajax.request({
                     url:'../../sis_obingresos/control/Boleto/traerBoletosJson',
                     params: {moneda_base:this.store.baseParams.moneda_base,id_punto_venta: this.id_punto_venta,start:0,limit:this.tam_pag,sort:'id_boleto_amadeus',dir:'DESC',fecha:this.campo_fecha.getValue().dateFormat('Ymd'), todos:'si'},
                     success:this.successSincro,
                     failure: this.conexionFailure,
                     timeout:this.timeout,
                     scope:this
                 });
             },

             successSincro: function(resp) {
                 Phx.CP.loadingHide();
                 var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
             },


             /********************************************************************************/
            onTraerBoletosTodos : function () {
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url:'../../sis_obingresos/control/Boleto/traerBoletosJson',
                    params: {moneda_base:this.store.baseParams.moneda_base,id_punto_venta: this.id_punto_venta,start:0,limit:this.tam_pag,sort:'id_boleto_amadeus',dir:'DESC',fecha:this.campo_fecha.getValue().dateFormat('Ymd'), todos:'si'},
                    success:this.successSinc,
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });
            },

            successSinc: function(resp) {
                Phx.CP.loadingHide();
                var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                this.reload();

            },

            /* preparaCerrarCaja:function(){
             Phx.CP.loadWindows('../../../sis_ventas_facturacion/vista/apertura_cierre_caja/FormCierreCaja.php',
             'Cerrar Caja',
             {
             modal:true,
             width:1000,
             height:400
             }, {data:this}, this.idContenedor,'FormCierreCaja',
             {
             config:[{
             event:'beforesave',
             delegate: this.cerrarCaja,
             }
             ],
             scope:this
             })
             },

             cerrarCaja:function(wizard,resp){
             var me=this;
             Phx.CP.loadingShow();
             Ext.Ajax.request({
             url:'../../sis_ventas_facturacion/control/AperturaCierreCaja/insertarAperturaCierreCaja',
             params:{
             id_apertura_cierre_caja: resp.id_apertura_cierre_caja,
             id_sucursal: resp.id_sucursal,
             id_punto_venta: resp.id_punto_venta,
             obs_cierre: resp.obs_cierre,
             arqueo_moneda_local: resp.arqueo_moneda_local,
             arqueo_moneda_extranjera: resp.arqueo_moneda_extranjera,
             accion :'cerrar',
             monto_inicial: resp.monto_inicial,
             obs_apertura: resp.obs_apertura,
             monto_inicial_moneda_extranjera: resp.monto_inicial_moneda_extranjera
             },
             argument:{wizard:wizard},
             success:this.successWizard,
             failure: this.conexionFailure,
             timeout:this.timeout,
             scope:this
             });

             },

             successWizard:function(resp){
             Phx.CP.loadingHide();
             resp.argument.wizard.panel.destroy()
             this.reload();
             },*/

            onButtonEdit : function () {  
                if(this.window.buttons.length < 3) {
                    this.window.buttons.unshift(this.firstA)
                }          
                Phx.vista.EmisionBoleto.superclass.onButtonEdit.call(this); 
                this.window.buttons[1].btnEl.dom.innerText = 'Guardar'
                this.window.buttons[1].btnEl.dom.innerHTML = '<i class=\"fa fa-check\"></i> Guardar'

                this.ocultarGrupo(2);
                this.ocultarGrupo(3);
                this.mostrarGrupo(0);
                //this.ocultarGrupo(0);
                this.Cmp.nit.setVisible(false)
                this.Cmp.nit.allowBlank = true
                this.Cmp.razonSocial.setVisible(false)
                this.Cmp.razonSocial.allowBlank = true

                /*Aqui para poner por defecto*/

                this.Cmp.id_moneda.store.load({params:{start:0,limit:50},
                       callback : function (r) {
                                for (var i = 0; i < r.length; i++) {
                                  if (r[i].data.codigo_internacional == this.Cmp.moneda.getValue()) {
                                    this.Cmp.id_moneda.setValue(r[i].data.id_moneda);
                                    this.Cmp.id_moneda.fireEvent('select', this.Cmp.id_moneda,r[i]);
                                  }
                                }
                        }, scope : this
                    });

              this.Cmp.id_forma_pago.store.load({params:{start:0,limit:50},
                     callback : function (r) {
                              for (var i = 0; i < r.length; i++) {
                                if (r[i].data.codigo == this.Cmp.forma_pago_amadeus.getValue()) {
                                  this.Cmp.id_forma_pago.setValue(r[i].data.id_forma_pago);
                                  this.Cmp.id_forma_pago.fireEvent('select', this.Cmp.id_forma_pago,r[i]);
                                }
                              }
                      }, scope : this
                  });




                /*Aqui aumentamos cuando se seleccione la moneda se resetee el medio de pago (26/11/2020)*/
                // this.Cmp.id_moneda.on('select',function(combo,record){
                //     this.Cmp.id_forma_pago.reset();
                // },this);
                //
                // this.Cmp.id_moneda2.on('select',function(combo,record){
                //     this.Cmp.id_forma_pago2.reset();
                // },this);
                /*****************************************************************************************/

                /****************************/






                this.grupo = 'no';
                this.Cmp.nro_boleto.allowBlank = true;
                this.Cmp.nro_boleto.setDisabled(false);
                //this.Cmp.voucherCode.setReadOnly(false);

                this.Cmp.monto_recibido_forma_pago.setValue(this.Cmp.monto_forma_pago.getValue());


                this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue()-this.Cmp.monto_forma_pago.getValue());
                this.manejoComponentesFp1(this.sm.getSelected().data['id_forma_pago'],this.sm.getSelected().data['codigo_forma_pago']);
                this.manejoComponentesFp2(this.sm.getSelected().data['id_forma_pago2'],this.sm.getSelected().data['codigo_forma_pago2']);

                if (this.sm.getSelected().data['monto_total_fp'] < (this.sm.getSelected().data['total']) ) {
                    this.Cmp.id_forma_pago2.setDisabled(false);
                    this.Cmp.monto_forma_pago2.setDisabled(false);
                }
                //romer probando con ext.ajax.request llamado a consultaviajerofrecuente
                /*m=this;
                if(this.sm.getSelected().data['nro_boleto']  != ''){
                    console.log('llego y comparo');
                        Ext.Ajax.request({
                            url:'../../sis_obingresos/control/ConsultaViajeroFrecuente/listarConsultaViajeroFrecuente',
                            params:{id_consulta_viajero_frecuente: this.id_consulta_viajero_frecuente,
                                ffid: this.ffid, voucherCode:this.voucher , ticketNumber: m.sm.getSelected().data['nro_boleto'],
                                pnr:this.pnr },
                            success:function(resp){
                                var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                                console.log('id',reg);
                            },
                            failure: this.conexionFailure,
                            timeout:this.timeout,
                            scope:this
                        });
                }*/
                if (this.sm.getSelected().data['ffid'] == '' && this.sm.getSelected().data['voucher_code']  == '' && this.sm.getSelected().data['estado'] == 'borrador' )
                {
                    if ((this.sm.getSelected().data['moneda'] == 'BOB' && this.sm.getSelected().data['neto'] == 30 && this.sm.getSelected().data['estado'] == 'borrador'|| this.sm.getSelected().data['moneda'] == 'BOB' && this.sm.getSelected().data['neto'] == 60 && this.sm.getSelected().data['estado'] == 'borrador') ||
                        (this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] == 40 && this.sm.getSelected().data['estado'] == 'borrador'||this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] ==  80 && this.sm.getSelected().data['estado'] == 'borrador') ||
                        (this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] == 70 && this.sm.getSelected().data['estado'] == 'borrador'||this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] ==  140&& this.sm.getSelected().data['estado'] == 'borrador') ||
                        (this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] == 130 && this.sm.getSelected().data['estado'] == 'borrador'||this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] == 260 && this.sm.getSelected().data['estado'] == 'borrador')) {

                        /*this.Cmp.ffid_consul.allowBlank = false;
                        this.Cmp.ffid_consul.setDisabled(true);
                        this.Cmp.voucher_consu.allowBlank = false;
                        this.Cmp.voucher_consu.setDisabled(true);*/
                        //this.Cmp.voucherCode.setReadOnly(false);
                        this.formFormual();

                    }
                }
            },

            onInfoPnr: function(){
                Phx.CP.loadingShow();
                var pnr = this.nro_pnr_reserva.getValue();
                var fecha = this.campo_fecha.getValue();
                Ext.Ajax.request({
                    url:'../../sis_obingresos/control/Boleto/consultaReservaBoletoExch',                    
                    params:{ pnr: pnr, id_punto_venta: this.id_punto_venta, fecha_emision: fecha},
                    success: this.successInfoPnr,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });


            },

            successInfoPnr: function(resp){
                Phx.CP.loadingHide();
                data = JSON.parse(resp.responseText)                      
                
                if(data.exito){                             
                    
                    if (this.window.buttons.length == 3) {
                        this.firstA = this.window.buttons[0]                        
                        this.window.buttons.shift(1)
                    }                    
                    
                    Phx.vista.EmisionBoleto.superclass.onButtonNew.call(this);                     

                    this.window.buttons[0].btnEl.dom.innerText = 'Emitir Boleto'
                    this.window.buttons[0].btnEl.dom.innerHTML = '<i class=\"fa fa-check\"></i> Emitir Boleto'
                                        

                    this.ocultarGrupo(2);
                    this.ocultarGrupo(3);
                    this.mostrarGrupo(0);

                    this.Cmp.nit.setVisible(true)
                    this.Cmp.nit.allowBlank = false;
                    this.Cmp.razonSocial.setVisible(true)                    
                    this.Cmp.razonSocial.allowBlank = false;

                    this.Cmp.nro_boleto.setVisible(false)
                    this.Cmp.pasajero.setVisible(false)
                    this.Cmp.forma_pago_amadeus.setVisible(false)                    
                    this.Cmp.comision.setVisible(false)
                    this.Cmp.comision_moneda_extranjera.setVisible(false)
                    this.Cmp.agente_venta.setVisible(false)
                    this.Cmp.fecha_emision.setVisible(false)

                    this.Cmp.identifierPnr.setValue(data.identifierPnr)
                    this.Cmp.localizador.setValue(data.pnr)
                    this.Cmp.moneda.setValue(data.moneda)
                    this.Cmp.total.setValue(data.importeTotal)
                    this.Cmp.monto_recibido_forma_pago.setValue(data.importeTotal)
                    this.Cmp.monto_forma_pago.setValue(data.importeTotal)                  
                    this.Cmp.emisionReservaPnr.setValue('true')
                    this.Cmp.PnrReserva.setValue(data.pnr)
                    this.Cmp.offReserva.setValue(data.offReserva)
                    this.Cmp.fechaEmisionPnr.setValue(this.campo_fecha.getValue().dateFormat('Ymd'));
                    this.Cmp.monedaBasePnr.setValue(this.store.baseParams.moneda_base)
                    this.Cmp.id_moneda.store.load({params:{start:0,limit:50},
                       callback : function (r) {
                                for (var i = 0; i < r.length; i++) {
                                  if (r[i].data.codigo_internacional == this.Cmp.moneda.getValue()) {
                                    this.Cmp.id_moneda.setValue(r[i].data.id_moneda);
                                    this.Cmp.id_moneda.fireEvent('select', this.Cmp.id_moneda,r[i]);
                                  }
                                }
                        }, scope : this
                    });

                    this.Cmp.id_forma_pago.store.load({params:{start:0,limit:50},
                     callback : function (r) {
                              for (var i = 0; i < r.length; i++) {
                                if (r[i].data.codigo == this.Cmp.forma_pago_amadeus.getValue()) {
                                  this.Cmp.id_forma_pago.setValue(r[i].data.id_forma_pago);
                                  this.Cmp.id_forma_pago.fireEvent('select', this.Cmp.id_forma_pago,r[i]);
                                }
                              }
                      }, scope : this
                    });

                    this.grupo = 'no';
                    this.Cmp.nro_boleto.allowBlank = true;
                    this.Cmp.nro_boleto.setDisabled(false);                    
                    this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue()-this.Cmp.monto_forma_pago.getValue());

                    this.manejoComponentesFp1(null, 'CA');
                    this.manejoComponentesFp2(null, 'CA');     
                    this.Cmp.id_forma_pago2.setDisabled(false);
                    this.Cmp.monto_forma_pago2.setDisabled(false);                                        

                }else{
                    Ext.Msg.show({
                        title: 'Alerta',
                        msg: '<div style="text-align: justify;"><b style="color: red;">Estimado Usuario:</b> <br> <br><b>No se tiene información relacionada con el PNR: '+data.pnr+' de reserva</b>'+'</div>',
                        buttons: Ext.Msg.OK,
                        width: 500,
                        maxWidth: 1024,
                        icon: Ext.Msg.WARNING
                    });
                }                
            },
            successSave: function(resp){
                Phx.CP.loadingHide();
                reg = JSON.parse(resp.responseText)                
                if((reg.error != false) && (reg.ROOT.datos[0]!= undefined)){
                    var html = '';
                    html += '<html>';
                    html += '<body style="margin:0!important">';
                    html += '<embed width="100%" height="100%" src="data:application/pdf;base64,'+reg.ROOT.datos[0].fileInvoice+'" type="application/pdf" />';
                    html += '</body>';
                    html += '</html>'
                    var win = window.open("","_blank")
                    win.document.write(html) 
                }                
                Phx.vista.EmisionBoleto.superclass.successSave.call(this,resp);
            },
            oncellclick : function(grid, rowIndex, columnIndex, e) {

                var record = this.store.getAt(rowIndex),
                    fieldName = grid.getColumnModel().getDataIndex(columnIndex); // Get field name

                if(fieldName == 'estado') {

                    if (this.sm.getSelected().data['ffid'] != '' && this.sm.getSelected().data['voucher_code']  != '' && this.sm.getSelected().data['estado'] == 'borrador' ){
                        if(record.data.tipo_reg != 'summary'){
                            this.cambiarRevision(record);
                        }
                    }  else if((this.sm.getSelected().data['moneda'] == 'BOB' && this.sm.getSelected().data['neto'] == 30 && this.sm.getSelected().data['estado'] == 'borrador'||this.sm.getSelected().data['moneda'] == 'BOB' && this.sm.getSelected().data['neto'] == 60 && this.sm.getSelected().data['estado'] == 'borrador') ||
                        (this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] == 40 && this.sm.getSelected().data['estado'] == 'borrador'||      this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] ==  80 && this.sm.getSelected().data['estado'] == 'borrador') ||
                        (this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] == 70 && this.sm.getSelected().data['estado'] == 'borrador'||      this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] ==  140&& this.sm.getSelected().data['estado'] == 'borrador') ||
                        (this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] == 130 && this.sm.getSelected().data['estado'] == 'borrador'||     this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] == 260 && this.sm.getSelected().data['estado'] == 'borrador')) {
                        this.formFormualRevi();
                    }
                    else {
                        if(record.data.tipo_reg != 'summary'){
                            this.cambiarRevision(record);
                        }
                    }
                }
                if(fieldName == 'nro_boleto') {
                  if(record.data.tipo_reg != 'summary'){
                    if (this.variables_globales.instancias_de_pago_nuevas == 'no') {
                        this.onButtonEdit(this);
                    } else {
                      if (record.data.forma_pago_amadeus != 'CC') {
                        this.onButtonEdit(this);
                      }
                    }


                    }
                }
            },

            cambiarRevision: function(record){

                Phx.CP.loadingShow();
                var d = record.data;
                Ext.Ajax.request({
                    url:'../../sis_obingresos/control/Boleto/cambiarRevisionBoleto',
                    params:{ id_boleto_amadeus: d.id_boleto_amadeus},
                    success: this.successRevision,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });


            },

            successRevision: function(resp){

                Phx.CP.loadingHide();
                var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                if (!reg.ROOT.error) {
                    this.reload();
                }

            },

            anularBoleto:function(){
                Phx.CP.loadingShow();
                var d = this.sm.getSelected().data;
                Ext.Ajax.request({
                    url:'../../sis_obingresos/control/Boleto/anularBoleto',
                    params:{id_boleto_amadeus:d.id_boleto_amadeus},
                    success:this.successAnularBoleto,
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });

            },

            successAnularBoleto:function(resp){
                Phx.CP.loadingHide();
                var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                if(!reg.ROOT.error){
                    this.reload();
                }
            },

            calculoFp1Grupo : function (record) {

              if (this.variables_globales.instancias_de_pago_nuevas == 'no') {
                this.moneda_grupo_fp1 = record.data.desc_moneda;
                if (this.moneda_grupo_fp2 == '') {
                    console.log('moneda grupo 2 vacio');
                    this.Cmp.monto_forma_pago.setValue(this.total_grupo['total_boletos_'+record.data.desc_moneda]);
                    /*Aumentando para calcular el monto recibido*/
                    this.Cmp.monto_recibido_forma_pago.setValue((this.total_grupo['total_boletos_'+record.data.desc_moneda] - this.Cmp.monto_total_comision.getValue()).toFixed(2));
                    /***********************************************/
                } else if (this.moneda_grupo_fp2 == this.moneda_grupo_fp1) {
                    console.log('moneda grupo 2 igual moneda grupo 1');
                    this.Cmp.monto_forma_pago.setValue(((this.total_grupo['total_boletos_'+record.data.desc_moneda] - this.Cmp.monto_total_comision.getValue()) - this.Cmp.monto_forma_pago2.getValue()).toFixed(2));
                    /*Aumentando para calcular el monto recibido*/
                    this.Cmp.monto_recibido_forma_pago.setValue(((this.total_grupo['total_boletos_'+record.data.desc_moneda] - this.Cmp.monto_total_comision.getValue()) - this.Cmp.monto_forma_pago2.getValue()).toFixed(2));
                    /***********************************************/
                } else {
                    if (this.moneda_grupo_fp2 == 'USD') {
                        console.log('monedas distintas grupo 2 usd');
                        this.Cmp.monto_forma_pago.setValue(((this.total_grupo['total_boletos_'+record.data.desc_moneda] - (this.Cmp.monto_total_comision.getValue() * this.tc_grupo)) - (this.Cmp.monto_forma_pago2.getValue() * this.tc_grupo)).toFixed(2));//this.roundMenor(this.Cmp.monto_forma_pago2.getValue() * this.tc_grupo , 2));
                        /*Aumentando para calcular el monto recibido*/
                        this.Cmp.monto_recibido_forma_pago.setValue(((this.total_grupo['total_boletos_'+record.data.desc_moneda]-(this.Cmp.monto_total_comision.getValue() * this.tc_grupo)) - (this.Cmp.monto_forma_pago2.getValue() * this.tc_grupo)).toFixed(2));//this.roundMenor(this.Cmp.monto_forma_pago2.getValue() * this.tc_grupo , 2));
                        /***********************************************/
                    } else {
                        console.log('monedas distintas grupo 2 bob');
                        this.Cmp.monto_forma_pago.setValue(((this.total_grupo['total_boletos_'+record.data.desc_moneda] - (this.Cmp.monto_total_comision.getValue() / this.tc_grupo)) - (this.Cmp.monto_forma_pago2.getValue() / this.tc_grupo)).toFixed(2));//this.roundMenor(this.Cmp.monto_forma_pago2.getValue() / this.tc_grupo , 2));
                        /*Aumentando para calcular el monto recibido*/
                        this.Cmp.monto_recibido_forma_pago.setValue(((this.total_grupo['total_boletos_'+record.data.desc_moneda]-(this.Cmp.monto_total_comision.getValue() / this.tc_grupo)) - (this.Cmp.monto_forma_pago2.getValue() / this.tc_grupo)).toFixed(2));//this.roundMenor(this.Cmp.monto_forma_pago2.getValue() / this.tc_grupo , 2));
                        /***********************************************/
                    }
                }
              } /*Aqui inicia las condiciones para los nuevos medios de pago (Ismael Valdivia 25/11/2020)*/else{
                this.moneda_grupo_fp1 = record;
                if (this.moneda_grupo_fp2 == '') {
                    console.log('moneda grupo 2 vacio');
                    if (this.moneda_grupo_fp1 == 'USD' && this.Cmp.moneda_boletos.getValue() != 'USD') {
                      this.Cmp.monto_forma_pago.setValue((this.total_grupo['total_boletos_'+record] - (this.Cmp.monto_total_comision.getValue()/this.tc_grupo)).toFixed(2));
                      /*Aumentando para calcular el monto recibido*/
                      this.Cmp.monto_recibido_forma_pago.setValue((this.total_grupo['total_boletos_'+record] - (this.Cmp.monto_total_comision.getValue()/this.tc_grupo)).toFixed(2));
                      /***********************************************/
                    } else if (this.moneda_grupo_fp1 != 'USD' && this.Cmp.moneda_boletos.getValue() == 'USD') {
                      this.Cmp.monto_forma_pago.setValue((this.total_grupo['total_boletos_'+record] - (this.Cmp.monto_total_comision.getValue()*this.tc_grupo)).toFixed(2));
                      /*Aumentando para calcular el monto recibido*/
                      this.Cmp.monto_recibido_forma_pago.setValue((this.total_grupo['total_boletos_'+record] - (this.Cmp.monto_total_comision.getValue()*this.tc_grupo)).toFixed(2));
                      /***********************************************/
                    } else if (this.moneda_grupo_fp1 == this.Cmp.moneda_boletos.getValue()) {
                      this.Cmp.monto_forma_pago.setValue((this.total_grupo['total_boletos_'+record] - this.Cmp.monto_total_comision.getValue()).toFixed(2));
                      /*Aumentando para calcular el monto recibido*/
                      this.Cmp.monto_recibido_forma_pago.setValue((this.total_grupo['total_boletos_'+record] - this.Cmp.monto_total_comision.getValue()).toFixed(2));
                      /***********************************************/
                    }

                } else if (this.moneda_grupo_fp2 == this.moneda_grupo_fp1) {
                    console.log('moneda grupo 2 igual moneda grupo 1');
                    this.Cmp.monto_forma_pago.setValue(((this.total_grupo['total_boletos_'+record] -  this.Cmp.monto_total_comision.getValue()) - this.Cmp.monto_forma_pago2.getValue()).toFixed(2));
                    /*Aumentando para calcular el monto recibido*/
                    this.Cmp.monto_recibido_forma_pago.setValue(((this.total_grupo['total_boletos_'+record] -  this.Cmp.monto_total_comision.getValue()) - this.Cmp.monto_forma_pago2.getValue()).toFixed(2));
                    /***********************************************/
                } else {
                    if (this.moneda_grupo_fp2 == 'USD') {
                        console.log('monedas distintas grupo 2 usd');
                        this.Cmp.monto_forma_pago.setValue(((this.total_grupo['total_boletos_'+record] - (this.Cmp.monto_total_comision.getValue()* this.tc_grupo)) - (this.Cmp.monto_forma_pago2.getValue() * this.tc_grupo)).toFixed(2));//this.roundMenor(this.Cmp.monto_forma_pago2.getValue() * this.tc_grupo , 2));
                        /*Aumentando para calcular el monto recibido*/
                        this.Cmp.monto_recibido_forma_pago.setValue(((this.total_grupo['total_boletos_'+record]-(this.Cmp.monto_total_comision.getValue()* this.tc_grupo)) - (this.Cmp.monto_forma_pago2.getValue() * this.tc_grupo)).toFixed(2));//this.roundMenor(this.Cmp.monto_forma_pago2.getValue() * this.tc_grupo , 2));
                        /***********************************************/
                    } else {
                        console.log('monedas distintas grupo 2 bob');
                        this.Cmp.monto_forma_pago.setValue(((this.total_grupo['total_boletos_'+record] - (this.Cmp.monto_total_comision.getValue() / this.tc_grupo)) - (this.Cmp.monto_forma_pago2.getValue() / this.tc_grupo)).toFixed(2));//this.roundMenor(this.Cmp.monto_forma_pago2.getValue() / this.tc_grupo , 2));
                        /*Aumentando para calcular el monto recibido*/
                        this.Cmp.monto_recibido_forma_pago.setValue(((this.total_grupo['total_boletos_'+record] - (this.Cmp.monto_total_comision.getValue() / this.tc_grupo)) - (this.Cmp.monto_forma_pago2.getValue() / this.tc_grupo)).toFixed(2));//this.roundMenor(this.Cmp.monto_forma_pago2.getValue() / this.tc_grupo , 2));
                        /***********************************************/
                    }
                }
              }




            },
            calculoFp2Grupo : function (record) {
              if (this.variables_globales.instancias_de_pago_nuevas == 'no') {
                this.moneda_grupo_fp2 = record.data.desc_moneda;
                if (this.moneda_grupo_fp1 == '') {
                    console.log('sin moneda fp 1');
                    this.Cmp.monto_forma_pago2.setValue(this.total_grupo['total_boletos_'+record.data.desc_moneda]);
                } else if (this.moneda_grupo_fp2 == this.moneda_grupo_fp1) {
                    console.log('con misma moneda grupos');
                    this.Cmp.monto_forma_pago2.setValue(this.total_grupo['total_boletos_'+record.data.desc_moneda] - this.Cmp.monto_forma_pago.getValue());
                } else {
                    console.log('moneda grupos diferentes');
                    if (this.moneda_grupo_fp1 == 'USD') {
                        this.Cmp.monto_forma_pago2.setValue((this.total_grupo['total_boletos_'+record.data.desc_moneda] - (this.Cmp.monto_forma_pago.getValue() * this.tc_grupo)).toFixed(2));//this.roundMenor(this.Cmp.monto_forma_pago.getValue() * this.tc_grupo , 2));
                    } else {
                        this.Cmp.monto_forma_pago2.setValue((this.total_grupo['total_boletos_'+record.data.desc_moneda] - (this.Cmp.monto_forma_pago.getValue() / this.tc_grupo)).toFixed(2));//this.roundMenor(this.Cmp.monto_forma_pago.getValue() / this.tc_grupo , 2));
                    }
                }
              } else {
                this.moneda_grupo_fp2 = record;
                if (this.moneda_grupo_fp1 == '') {
                    console.log('sin moneda fp 1');
                    this.Cmp.monto_forma_pago2.setValue(this.total_grupo['total_boletos_'+record]);
                } else if (this.moneda_grupo_fp2 == this.moneda_grupo_fp1) {
                    console.log('con misma moneda grupos');
                    if(this.moneda_grupo_fp2 == 'USD' && this.Cmp.moneda_boletos.getValue() != 'USD') {
                      this.Cmp.monto_forma_pago2.setValue((this.total_grupo['total_boletos_'+record] - (this.Cmp.monto_total_comision.getValue() / this.tc_grupo) )- this.Cmp.monto_forma_pago.getValue());
                    } else if (this.moneda_grupo_fp2 != 'USD' && this.Cmp.moneda_boletos.getValue() == 'USD') {
                      this.Cmp.monto_forma_pago2.setValue((this.total_grupo['total_boletos_'+record] - (this.Cmp.monto_total_comision.getValue() * this.tc_grupo) )- this.Cmp.monto_forma_pago.getValue());

                    } else if (this.moneda_grupo_fp2 == this.Cmp.moneda_boletos.getValue()) {
                      this.Cmp.monto_forma_pago2.setValue((this.total_grupo['total_boletos_'+record] - this.Cmp.monto_total_comision.getValue()  )- this.Cmp.monto_forma_pago.getValue());

                    }
                } else {
                    console.log('moneda grupos diferentes',((this.total_grupo['total_boletos_'+record] - (this.Cmp.monto_total_comision.getValue() / this.tc_grupo))-(this.Cmp.monto_forma_pago.getValue() / this.tc_grupo)).toFixed(2));
                    if (this.moneda_grupo_fp1 == 'USD') {
                        this.Cmp.monto_forma_pago2.setValue(((this.total_grupo['total_boletos_'+record]-this.Cmp.monto_total_comision.getValue() )-(this.Cmp.monto_forma_pago.getValue() * this.tc_grupo)).toFixed(2));// this.total_grupo['total_boletos_'+record] - this.roundMenor(this.Cmp.monto_forma_pago.getValue() * this.tc_grupo , 2));
                    } else {
                        this.Cmp.monto_forma_pago2.setValue(((this.total_grupo['total_boletos_'+record] - (this.Cmp.monto_total_comision.getValue() / this.tc_grupo))-(this.Cmp.monto_forma_pago.getValue() / this.tc_grupo)).toFixed(2));//this.total_grupo['total_boletos_'+record] - this.roundMenor(this.Cmp.monto_forma_pago.getValue() / this.tc_grupo , 2));
                    }
                }
              }
            },
            onGrupo : function () {
                if(this.window.buttons.length < 3) {
                    this.window.buttons.unshift(this.firstA)
                }
                Phx.vista.EmisionBoleto.superclass.onButtonEdit.call(this);
                this.window.buttons[1].btnEl.dom.innerText = 'Guardar'
                this.window.buttons[1].btnEl.dom.innerHTML = '<i class=\"fa fa-check\"></i> Guardar'
                this.grupo = 'si';
                var seleccionados = this.sm.getSelections();
                this.total_grupo = new Object;

                this.Cmp.id_moneda.store.load({params:{start:0,limit:50},
                       callback : function (r) {
                                for (var i = 0; i < r.length; i++) {
                                  if (r[i].data.codigo_internacional == this.Cmp.moneda.getValue()) {
                                    this.Cmp.id_moneda.setValue(r[i].data.id_moneda);
                                    this.Cmp.id_moneda.fireEvent('select', this.Cmp.id_moneda,r[i]);
                                  }
                                }
                        }, scope : this
                    });

                //console.log("llega aqui el dato22222222",this.Cmp.moneda.getValue());
                //this.total_grupo['USD'] = 0;
                //this.total_grupo[seleccionados[0].data.moneda_sucursal] = 0;
                this.total_grupo['total_boletos_USD'] = 0;
                this.total_grupo['total_neto_USD'] = 0;
                this.total_grupo['total_comision_USD'] = 0;
                this.total_grupo['total_boletos_'+seleccionados[0].data.moneda_sucursal] = 0;
                this.total_grupo['total_neto_'+seleccionados[0].data.moneda_sucursal] = 0;
                this.total_grupo['total_comision_'+seleccionados[0].data.moneda_sucursal] = 0;

                for (var i = 0 ; i< seleccionados.length;i++) {
                    if (i == 0) {
                        this.Cmp.ids_seleccionados.setValue(seleccionados[i].data.id_boleto_amadeus);
                        this.Cmp.boletos.setValue('930'+seleccionados[i].data.nro_boleto + ' ('+ seleccionados[i].data.total +' '+seleccionados[i].data.moneda+')');
                    } else {
                        this.Cmp.ids_seleccionados.setValue(this.Cmp.ids_seleccionados.getValue() + ',' + seleccionados[i].data.id_boleto_amadeus);
                        this.Cmp.boletos.setValue(this.Cmp.boletos.getValue() + ', 930' + seleccionados[i].data.nro_boleto + ' ('+ seleccionados[i].data.total +' '+seleccionados[i].data.moneda+')');
                    }
                    if (seleccionados[i].data.moneda_sucursal == 'USD') {
                        /*this.total_grupo['total_boletos_'+seleccionados[0].data.moneda_sucursal] += parseFloat(seleccionados[i].data.total);
                         this.total_grupo['total_neto_'+seleccionados[0].data.moneda_sucursal] += parseFloat(seleccionados[i].data.neto);
                         this.total_grupo['total_comision_'+seleccionados[0].data.moneda_sucursal] += parseFloat(seleccionados[i].data.comision);*/

                         /**********************************Comentnado esta parte*************************************************/
                        this.total_grupo['total_boletos_USD'] += this.round(seleccionados[i].data.total , 2);
                        this.total_grupo['total_neto_USD'] += this.round(seleccionados[i].data.neto , 2);
                        this.total_grupo['total_comision_USD'] += this.round(seleccionados[i].data.comision , 2);
                        /**********************************************************************************************************/


                        this.Cmp.moneda_boletos.setValue(seleccionados[i].data.moneda);
                        this.Cmp.monto_total_boletos.setValue(this.total_grupo['total_boletos_USD']);// +seleccionados[0].data.moneda_sucursal].toFixed(2));
                        this.Cmp.monto_total_neto.setValue(this.total_grupo['total_neto_USD']); //+seleccionados[0].data.moneda_sucursal].toFixed(2));
                        this.Cmp.monto_total_comision.setValue(this.total_grupo['total_comision_USD']); //+seleccionados[0].data.moneda_sucursal].toFixed(2));

                        this.Cmp.monto_forma_pago.setValue(this.total_grupo['total_boletos_USD']); //+seleccionados[0].data.moneda_sucursal].toFixed(2));
                        this.Cmp.monto_recibido_forma_pago.setValue(this.total_grupo['total_boletos_USD']); //+seleccionados[0].data.moneda_sucursal].toFixed(2));
                    } else if (seleccionados[i].data.moneda == 'USD') {
                        this.Cmp.moneda_boletos.setValue(seleccionados[i].data.moneda);
                        this.total_grupo['total_boletos_'+seleccionados[0].data.moneda_sucursal] += parseFloat(seleccionados[i].data.total * seleccionados[i].data.tc);
                        this.total_grupo['total_neto_'+seleccionados[0].data.moneda_sucursal] += parseFloat(seleccionados[i].data.neto * seleccionados[i].data.tc);
                        this.total_grupo['total_comision_'+seleccionados[0].data.moneda_sucursal] += parseFloat(seleccionados[i].data.comision * seleccionados[i].data.tc);
                        //this.total_grupo['total_boletos_' + seleccionados[0].data.moneda_sucursal] += this.round(seleccionados[i].data.total * seleccionados[i].data.tc , 2);
                        this.total_grupo['total_boletos_USD'] += parseFloat(seleccionados[i].data.total);
                        this.total_grupo['total_neto_USD'] += parseFloat(seleccionados[i].data.neto);
                        this.total_grupo['total_comision_USD'] += parseFloat(seleccionados[i].data.comision);

                        this.Cmp.monto_total_boletos.setValue(this.total_grupo['total_boletos_USD'].toFixed(2));
                        this.Cmp.monto_total_neto.setValue(this.total_grupo['total_neto_USD'].toFixed(2));
                        this.Cmp.monto_forma_pago.setValue(this.total_grupo['total_boletos_USD'].toFixed(2));
                        this.Cmp.monto_recibido_forma_pago.setValue(this.total_grupo['total_boletos_USD'].toFixed(2));
                    } else if (seleccionados[i].data.moneda !== 'USD') {
                        this.Cmp.moneda_boletos.setValue(seleccionados[i].data.moneda);
                        //this.total_grupo['total_boletos_USD'] += this.round(seleccionados[i].data.total / seleccionados[i].data.tc , 2);
                        this.total_grupo['total_boletos_'+seleccionados[0].data.moneda_sucursal] += parseFloat(seleccionados[i].data.total);
                        this.total_grupo['total_neto_'+seleccionados[0].data.moneda_sucursal] += parseFloat(seleccionados[i].data.neto);
                        this.total_grupo['total_comision_'+seleccionados[0].data.moneda_sucursal] += parseFloat(seleccionados[i].data.comision);
                        this.total_grupo['total_boletos_USD'] += parseFloat(seleccionados[i].data.total / seleccionados[i].data.tc);//Math.ceil(parseFloat(seleccionados[i].data.total / seleccionados[i].data.tc)*100)/100;
                        this.total_grupo['total_neto_USD'] += parseFloat(seleccionados[i].data.neto / seleccionados[i].data.tc);// Math.ceil(parseFloat(seleccionados[i].data.neto / seleccionados[i].data.tc)*100)/100;
                        this.total_grupo['total_comision_USD'] +=parseFloat(seleccionados[i].data.comision / seleccionados[i].data.tc);  //Math.ceil(parseFloat(seleccionados[i].data.comision / seleccionados[i].data.tc)*100)/100;

                        this.Cmp.monto_total_boletos.setValue(this.total_grupo['total_boletos_'+seleccionados[0].data.moneda_sucursal].toFixed(2));
                        this.Cmp.monto_total_neto.setValue(this.total_grupo['total_neto_'+seleccionados[0].data.moneda_sucursal].toFixed(2));
                        this.Cmp.monto_total_comision.setValue(this.total_grupo['total_comision_'+seleccionados[0].data.moneda_sucursal].toFixed(2));
                        this.Cmp.monto_forma_pago.setValue(this.total_grupo['total_boletos_'+seleccionados[0].data.moneda_sucursal].toFixed(2));
                        this.Cmp.monto_recibido_forma_pago.setValue(this.total_grupo['total_boletos_'+seleccionados[0].data.moneda_sucursal].toFixed(2));
                    }else {
                        alert('No se puede calcular la forma de pago ya que la moneda de un boleto no es la moneda de la sucursal ni dolares americanos');
                        return;
                    }
                }

                //habilitamos el formulario
                this.ocultarGrupo(0);
                this.ocultarGrupo(3);
                this.mostrarGrupo(2);

                this.Cmp.id_forma_pago.setDisabled(false);

                this.Cmp.monto_forma_pago.setDisabled(true);
                //this.Cmp.monto_forma_pago.reset();
                this.Cmp.nro_boleto.allowBlank = true;
                this.manejoComponentesFp1(this.Cmp.id_forma_pago.getValue(),this.Cmp.codigo_forma_pago.getValue());
                this.manejoComponentesFp2(this.Cmp.id_forma_pago2.getValue(),this.Cmp.codigo_forma_pago2.getValue());
                this.moneda_grupo_fp1 = '';
                this.moneda_grupo_fp2 = '';
                this.tc_grupo = seleccionados[0].data.tc;

            },

            preparaMenu:function(tb){
                Phx.vista.EmisionBoleto.superclass.preparaMenu.call(this,tb);

                this.getBoton('btnPagarGrupo').enable();
                var data = this.getSelectedData();

                //f.e.a verificar si es exchange
                if (data['trans_code'] == 'EXCH'){
                    this.getBoton('btnImprimir').setVisible(true);
                    this.getBoton('btnImprimir').enable();
                }else{
                    this.getBoton('btnImprimir').setVisible(false);
                    this.getBoton('btnImprimir').disable();                    

                    /*Ext.Ajax.request({
                        url : '../../sis_obingresos/control/Boleto/verificarBoletoExch',
                        params : {
                            'pnr' : data.localizador
                        },
                        success : function(resp){
                            var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                            console.log('reg', reg.ROOT.datos);
                            if(reg.ROOT.datos[0].exchange == true){
                                this.getBoton('btnImprimir').setVisible(true);
                                this.getBoton('btnImprimir').enable();
                            }else{
                                this.getBoton('btnImprimir').setVisible(false);
                                this.getBoton('btnImprimir').disable();
                            }
                        },
                        failure : this.conexionFailure,
                        timeout : this.timeout,
                        scope : this
                    });*/
                }


                if(data['voided']== 'no'){
                    this.getBoton('btnAnularBoleto').setDisabled(false);
                }
                else{
                    this.getBoton('btnAnularBoleto').setDisabled(false);
                }
                if (data['ffid_consul'] != '' && data['voucher_consu'] != '' || data['ffid'] != '' && data['voucher_code'] != '' ){
                    this.getBoton('btnVoucherCode').enable();
                }else{
                    this.getBoton('btnVoucherCode').disable();
                }
                
                if (data['pasajero'] != '' && data['localizador'] != '') {
                    this.getBoton('btnInvoicePNRPDF').enable();
                } else {
                    this.getBoton('btnInvoicePNRPDF').disable();
                }                
                //this.getBoton('btnImprimir').enable();
            },

            liberaMenu:function(tb){
                Phx.vista.EmisionBoleto.superclass.liberaMenu.call(this,tb);
                this.getBoton('btnPagarGrupo').disable();
                this.getBoton('btnImprimir').disable();
                this.getBoton('btnImprimir').setVisible(false);
                this.getBoton('btnAnularBoleto').setDisabled(true);

            },

            tabsouth:[{
                url:'../../../sis_obingresos/vista/boleto_amadeus_forma_pago/BoletoAmadeusFormaPago.php',
                title:'Formas de Pago',
                height:'40%',
                cls:'BoletoAmadeusFormaPago'
            }],

            Grupos:[{
                layout: 'column',
                items:[
                    {
                        bodyStyle: 'padding-right:10px;',
                        items:[
                            {
                                xtype:'fieldset',
                                layout: 'form',
                                border: true,
                                title: 'Datos Boleto',
                                bodyStyle: 'padding:0 10px 0;',
                                columnWidth: 0.5,
                                items:[],
                                id_grupo:0,
                                collapsible:true
                            }
                        ]
                    },
                    {
                        bodyStyle: 'padding-right:10px;',
                        items: [
                            {
                                xtype: 'fieldset',
                                layout: 'form',
                                border: true,
                                title: 'Boletos',
                                bodyStyle: 'padding:0 10px 0;',
                                columnWidth: 0.5,
                                items: [],
                                id_grupo: 2,
                                collapsible: true
                            }
                        ]
                    },
                    {
                        bodyStyle: 'padding-right:10px;',
                        items: [
                            {
                                xtype:'fieldset',
                                layout: 'form',
                                border: true,
                                title: 'Forma de Pago',
                                bodyStyle: 'padding:0 10px 0;',
                                columnWidth: 0.5,
                                items:[],
                                id_grupo:1,
                                collapsible:true,
                                collapsed:false
                            }
                        ]
                    },
                    {
                        bodyStyle: 'padding-right:10px;',
                        items: [
                            {
                                xtype:'fieldset',
                                layout: 'form',
                                border: true,
                                title: 'Otros',
                                bodyStyle: 'padding:0 10px 0;',
                                columnWidth: 0.5,
                                items:[],
                                id_grupo:3,
                                collapsible:true,
                                collapsed:false
                            }
                        ]
                    },
                    {
                        bodyStyle: 'padding-right:10px;',
                        items: [
                            {
                                xtype:'fieldset',
                                layout: 'form',
                                border: true,
                                title: 'Segunda Forma de Pago',
                                bodyStyle: 'padding:0 10px 0;',
                                columnWidth: 0.5,
                                items:[],
                                id_grupo:4,
                                collapsible:true,
                                collapsed:false
                            }
                        ]
                    },
                    {
                        bodyStyle: 'padding-right:10px;',
                        items: [
                            {
                                xtype:'fieldset',
                                layout: 'form',
                                border: true,
                                title: 'Cambio',
                                bodyStyle: 'padding:0 10px 0;',
                                columnWidth: 0.5,
                                items:[],
                                id_grupo:20,
                                collapsible:true,
                                collapsed:false
                            }
                        ]
                    }
                ]
            }],

            round : function(value, decimals) {
                return Math.ceil(value*100)/100;
            },
            roundMenor : function(value, decimals) {
                return Math.floor(value*100)/100;
            },
            manejoComponentesFp1 : function (id_fp1,codigo_fp1){
                if (this.store.baseParams.moneda_base == 'ARS') {
                  if (id_fp1 == 0) {
                      this.Cmp.id_forma_pago.setDisabled(true);
                      this.Cmp.monto_forma_pago.setDisabled(true);
                      this.ocultarComponente(this.Cmp.numero_tarjeta);
                      this.ocultarComponente(this.Cmp.codigo_tarjeta);

                      /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                      this.ocultarComponente(this.Cmp.nro_cupon);
                      this.ocultarComponente(this.Cmp.nro_cuota);
                      /**********************************************/

                      this.ocultarComponente(this.Cmp.mco);
                      this.ocultarComponente(this.Cmp.id_auxiliar);
                      this.ocultarComponente(this.Cmp.id_venta);
                      this.Cmp.numero_tarjeta.allowBlank = true;
                      this.Cmp.numero_tarjeta.allowBlank = true;
                      this.Cmp.mco.allowBlank = true;
                      this.Cmp.id_auxiliar.allowBlank = true;
                  } else {
                      this.Cmp.id_forma_pago.setDisabled(false);
                      this.Cmp.monto_forma_pago.setDisabled(true);
                      if (codigo_fp1.startsWith("CC") || codigo_fp1.startsWith("SF")) {
                          this.ocultarComponente(this.Cmp.id_auxiliar);
                          this.ocultarComponente(this.Cmp.mco);
                          this.Cmp.id_auxiliar.reset();
                          this.mostrarComponente(this.Cmp.numero_tarjeta);
                          this.mostrarComponente(this.Cmp.codigo_tarjeta);

                          /*Aumentando para solo dijitar los primeros y ultimos digitos de la tarjeta*/
                          this.Cmp.numero_tarjeta.on('keyup', function (cmp, e) {
                              //inserta guiones en codigo de contorl
                              var value = cmp.getValue(),
                              tmp = '',
                              tmp2 = '',
                              sw = 0;

                              if (this.codigo_medio_pago == 'AX') {
                                var campoX = 'XXXXXXX';
                              } else {
                                var campoX = 'XXXXXXXX';
                              }

                              tmp = value.replace(/-/g, '');
                              for (var i = 0; i < tmp.length; i++) {
                                  tmp2 = tmp2 + tmp[i];
                                  if ((i + 1) % 4 == 0 && tmp.length == 4) {
                                      tmp2 = tmp2 + campoX;
                                  }
                              }
                              cmp.setValue(tmp2.toUpperCase());
                          }, this);
                          /***************************************************************************/

                          /*Aumentando control en Interfaz para que el numero de tarjeta y codigo tarjeta no sean el mismo a
                          la primera forma de pago (Ismael Valdivia 16/09/2021)*/
                          this.Cmp.numero_tarjeta.on('change',function(field,newValue,oldValue){
                            if (this.Cmp.numero_tarjeta2.getValue() == this.Cmp.numero_tarjeta.getValue()) {

                               if (this.Cmp.codigo_tarjeta2.getValue() != '' && this.Cmp.codigo_tarjeta.getValue() != '') {
                                  if (this.Cmp.codigo_tarjeta2.getValue() == this.Cmp.codigo_tarjeta.getValue()) {
                                    Ext.Msg.show({
                                     title:'<h1 style="color:red"><center>AVISO</center></h1>',
                                     msg: '<b>El número de tarjeta y el codigo de tarjeta no pueden ser iguales en las formas de pago</b>',
                                     maxWidth : 400,
                                     width: 380,
                                     buttons: Ext.Msg.OK,
                                     scope:this
                                    });
                                  }
                               }
                            }
                          },this);

                          this.Cmp.codigo_tarjeta.on('change',function(field,newValue,oldValue){
                            if (this.Cmp.codigo_tarjeta2.getValue() == this.Cmp.codigo_tarjeta.getValue()) {
                               if (this.Cmp.numero_tarjeta2.getValue() != '' && this.Cmp.numero_tarjeta.getValue() != '') {
                                  if (this.Cmp.numero_tarjeta2.getValue() == this.Cmp.numero_tarjeta.getValue()) {
                                    Ext.Msg.show({
                                     title:'<h1 style="color:red"><center>AVISO</center></h1>',
                                     msg: '<b>El número de tarjeta y el codigo de tarjeta no pueden ser iguales en las formas de pago</b>',
                                     maxWidth : 400,
                                     width: 380,
                                     buttons: Ext.Msg.OK,
                                     scope:this
                                    });
                                  }
                               }
                            }
                          },this);
                          /*************************************************************************************************/


                          /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                          this.mostrarComponente(this.Cmp.nro_cupon);
                          this.mostrarComponente(this.Cmp.nro_cuota);
                          this.ocultarComponente(this.Cmp.id_venta);
                          this.Cmp.id_venta.reset();
                          this.Cmp.saldo_recibo.reset();
                          this.Cmp.numero_tarjeta.reset();
                          /***********************************************/
                          this.Cmp.numero_tarjeta.allowBlank = false;
                          this.Cmp.codigo_tarjeta.allowBlank = false;
                          this.Cmp.id_auxiliar.allowBlank = true;
                          this.Cmp.mco.allowBlank = true;

                          /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                          this.Cmp.nro_cupon.allowBlank = true;
                          this.Cmp.nro_cuota.allowBlank = true;
                          /***********************************************/

                          //tarjeta de credito
                      } else if (codigo_fp1.startsWith("CU")||codigo_fp1.startsWith("CT")) {
                          //cuenta corriente
                          this.ocultarComponente(this.Cmp.numero_tarjeta);
                          this.ocultarComponente(this.Cmp.mco);
                          this.ocultarComponente(this.Cmp.codigo_tarjeta);

                          /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                          this.ocultarComponente(this.Cmp.nro_cupon);
                          this.ocultarComponente(this.Cmp.nro_cuota);
                          /***********************************************/

                          this.mostrarComponente(this.Cmp.id_auxiliar);
                          this.ocultarComponente(this.Cmp.id_venta);
                          this.Cmp.id_venta.reset();
                          this.Cmp.numero_tarjeta.reset();
                          this.Cmp.codigo_tarjeta.reset();

                          /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                          this.Cmp.nro_cupon.reset();
                          this.Cmp.nro_cuota.reset();
                          this.Cmp.saldo_recibo.reset();
                          this.Cmp.nro_cupon.allowBlank = true;
                          this.Cmp.nro_cuota.allowBlank = true;
                          /***********************************************/
                          this.Cmp.id_auxiliar.reset();
                          this.Cmp.id_auxiliar.label.dom.innerHTML='Cuenta Corriente';
                          this.Cmp.id_auxiliar.store.baseParams.ro_activo='no';
                          this.Cmp.id_auxiliar.modificado = true;

                          this.Cmp.numero_tarjeta.allowBlank = true;
                          this.Cmp.mco2.allowBlank = true;
                          this.Cmp.codigo_tarjeta.allowBlank = true;
                          this.Cmp.id_auxiliar.allowBlank = false;
                      }else if (codigo_fp1.startsWith("RANT")) {
                        //cuenta corriente
                        this.ocultarComponente(this.Cmp.numero_tarjeta);
                        this.ocultarComponente(this.Cmp.mco);
                        this.ocultarComponente(this.Cmp.codigo_tarjeta);

                        /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                        this.ocultarComponente(this.Cmp.nro_cupon);
                        this.ocultarComponente(this.Cmp.nro_cuota);
                        this.Cmp.nro_cupon.allowBlank = true;
                        this.Cmp.nro_cuota.allowBlank = true;
                        /******************************************/
                        this.Cmp.id_auxiliar.reset();
                        this.Cmp.id_auxiliar.label.dom.innerHTML='Grupo';
                        this.Cmp.id_auxiliar.store.baseParams.ro_activo='si';
                        this.Cmp.id_auxiliar.modificado = true;
                        this.Cmp.id_venta.reset();
                        this.Cmp.id_venta.modificado = true;
                        this.mostrarComponente(this.Cmp.id_auxiliar);
                        this.mostrarComponente(this.Cmp.id_venta);
                        this.Cmp.numero_tarjeta.reset();
                        this.Cmp.codigo_tarjeta.reset();
                        this.Cmp.saldo_recibo.reset();
                        this.Cmp.numero_tarjeta.allowBlank = true;
                        this.Cmp.mco2.allowBlank = true;
                        this.Cmp.codigo_tarjeta.allowBlank = true;
                        this.Cmp.id_auxiliar.allowBlank = false;
                      }
                      else if (codigo_fp1.startsWith("MCO")) {
                          //mco
                          this.ocultarComponente(this.Cmp.numero_tarjeta);
                          this.ocultarComponente(this.Cmp.id_auxiliar);
                          this.Cmp.id_auxiliar.reset();
                          this.mostrarComponente(this.Cmp.mco);
                          this.ocultarComponente(this.Cmp.codigo_tarjeta);
                          this.ocultarComponente(this.Cmp.id_venta);
                          this.Cmp.id_venta.reset();
                          this.Cmp.saldo_recibo.reset();
                          this.Cmp.numero_tarjeta.reset();
                          this.Cmp.mco.allowBlank = false;
                          this.Cmp.codigo_tarjeta.allowBlank = true;
                          this.Cmp.id_auxiliar.allowBlank = true;
                          this.Cmp.numero_tarjeta.allowBlank = true;
                      }else {
                          this.ocultarComponente(this.Cmp.numero_tarjeta);
                          /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                          this.ocultarComponente(this.Cmp.nro_cupon);
                          this.ocultarComponente(this.Cmp.nro_cuota);
                          this.Cmp.nro_cupon.reset();
                          this.Cmp.nro_cuota.reset();
                          this.Cmp.nro_cupon.allowBlank = true;
                          this.Cmp.nro_cuota.allowBlank = true;
                          /******************************************/
                          this.ocultarComponente(this.Cmp.mco);
                          this.ocultarComponente(this.Cmp.codigo_tarjeta);
                          this.ocultarComponente(this.Cmp.id_auxiliar);
                          this.ocultarComponente(this.Cmp.id_venta);
                          this.Cmp.id_venta.reset();
                          this.Cmp.numero_tarjeta.reset();
                          this.Cmp.codigo_tarjeta.reset();
                          this.Cmp.id_auxiliar.reset();
                          this.Cmp.saldo_recibo.reset();
                          this.Cmp.numero_tarjeta.allowBlank = true;
                          this.Cmp.mco.allowBlank = true;
                          this.Cmp.codigo_tarjeta.allowBlank = true;
                          this.Cmp.id_auxiliar.allowBlank = true;
                      }
                  }
                } else {
                  if (id_fp1 == 0) {
                      this.Cmp.id_forma_pago.setDisabled(true);
                      this.Cmp.monto_forma_pago.setDisabled(true);
                      this.ocultarComponente(this.Cmp.numero_tarjeta);
                      this.ocultarComponente(this.Cmp.codigo_tarjeta);

                      /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                      this.ocultarComponente(this.Cmp.nro_cupon);
                      this.ocultarComponente(this.Cmp.nro_cuota);
                      this.Cmp.nro_cupon.allowBlank = true;
                      this.Cmp.nro_cuota.allowBlank = true;
                      /******************************************/

                      this.ocultarComponente(this.Cmp.mco);
                      this.ocultarComponente(this.Cmp.id_auxiliar);
                      this.ocultarComponente(this.Cmp.id_venta);

                      this.Cmp.numero_tarjeta.allowBlank = true;
                      this.Cmp.numero_tarjeta.allowBlank = true;
                      this.Cmp.mco.allowBlank = true;
                      this.Cmp.id_auxiliar.allowBlank = true;
                  } else {
                      this.Cmp.id_forma_pago.setDisabled(false);
                      this.Cmp.monto_forma_pago.setDisabled(true);
                      if (codigo_fp1.startsWith("CC") ||
                          codigo_fp1.startsWith("SF")) {
                          this.ocultarComponente(this.Cmp.id_auxiliar);
                          this.ocultarComponente(this.Cmp.mco);
                          this.Cmp.id_auxiliar.reset();
                          this.mostrarComponente(this.Cmp.numero_tarjeta);
                          this.mostrarComponente(this.Cmp.codigo_tarjeta);

                          /*Aumentando para solo dijitar los primeros y ultimos digitos de la tarjeta*/
                          this.Cmp.numero_tarjeta.on('keyup', function (cmp, e) {
                              //inserta guiones en codigo de contorl
                              var value = cmp.getValue(),
                              tmp = '',
                              tmp2 = '',
                              sw = 0;

                              if (this.codigo_medio_pago == 'AX') {
                                var campoX = 'XXXXXXX';
                              } else {
                                var campoX = 'XXXXXXXX';
                              }

                              tmp = value.replace(/-/g, '');
                              for (var i = 0; i < tmp.length; i++) {
                                  tmp2 = tmp2 + tmp[i];
                                  if ((i + 1) % 4 == 0 && tmp.length == 4) {
                                      tmp2 = tmp2 + campoX;
                                  }
                              }
                              cmp.setValue(tmp2.toUpperCase());
                          }, this);
                          /***************************************************************************/

                          /*Aumentando control en Interfaz para que el numero de tarjeta y codigo tarjeta no sean el mismo a
                          la primera forma de pago (Ismael Valdivia 16/09/2021)*/
                          this.Cmp.numero_tarjeta.on('change',function(field,newValue,oldValue){
                            if (this.Cmp.numero_tarjeta2.getValue() == this.Cmp.numero_tarjeta.getValue()) {

                               if (this.Cmp.codigo_tarjeta2.getValue() != '' && this.Cmp.codigo_tarjeta.getValue() != '') {
                                  if (this.Cmp.codigo_tarjeta2.getValue() == this.Cmp.codigo_tarjeta.getValue()) {
                                    Ext.Msg.show({
                                     title:'<h1 style="color:red"><center>AVISO</center></h1>',
                                     msg: '<b>El número de tarjeta y el codigo de tarjeta no pueden ser iguales en las formas de pago</b>',
                                     maxWidth : 400,
                                     width: 380,
                                     buttons: Ext.Msg.OK,
                                     scope:this
                                    });
                                  }
                               }
                            }
                          },this);

                          this.Cmp.codigo_tarjeta.on('change',function(field,newValue,oldValue){
                            if (this.Cmp.codigo_tarjeta2.getValue() == this.Cmp.codigo_tarjeta.getValue()) {
                               if (this.Cmp.numero_tarjeta2.getValue() != '' && this.Cmp.numero_tarjeta.getValue() != '') {
                                  if (this.Cmp.numero_tarjeta2.getValue() == this.Cmp.numero_tarjeta.getValue()) {
                                    Ext.Msg.show({
                                     title:'<h1 style="color:red"><center>AVISO</center></h1>',
                                     msg: '<b>El número de tarjeta y el codigo de tarjeta no pueden ser iguales en las formas de pago</b>',
                                     maxWidth : 400,
                                     width: 380,
                                     buttons: Ext.Msg.OK,
                                     scope:this
                                    });
                                  }
                               }
                            }
                          },this);
                          /*************************************************************************************************/

                          /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                          this.ocultarComponente(this.Cmp.nro_cupon);
                          this.ocultarComponente(this.Cmp.nro_cuota);
                          this.ocultarComponente(this.Cmp.id_venta);
                          this.Cmp.id_venta.reset();
                          this.Cmp.saldo_recibo.reset();
                          this.Cmp.numero_tarjeta.reset();
                          this.Cmp.nro_cupon.allowBlank = true;
                          this.Cmp.nro_cuota.allowBlank = true;
                          /******************************************/
                          this.Cmp.numero_tarjeta.allowBlank = false;
                          this.Cmp.codigo_tarjeta.allowBlank = false;
                          this.Cmp.id_auxiliar.allowBlank = true;
                          this.Cmp.mco.allowBlank = true;


                          //tarjeta de credito
                      } else if (codigo_fp1.startsWith("CT") || codigo_fp1.startsWith("CU")) {
                          //cuenta corriente
                          this.ocultarComponente(this.Cmp.numero_tarjeta);
                          this.ocultarComponente(this.Cmp.mco);
                          this.ocultarComponente(this.Cmp.codigo_tarjeta);
                          this.ocultarComponente(this.Cmp.id_venta);

                          /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                          this.ocultarComponente(this.Cmp.nro_cupon);
                          this.ocultarComponente(this.Cmp.nro_cuota);
                          this.Cmp.id_venta.reset();
                          this.Cmp.nro_cupon.allowBlank = true;
                          this.Cmp.nro_cuota.allowBlank = true;
                          /******************************************/
                          this.Cmp.id_auxiliar.reset();
                          this.Cmp.id_auxiliar.label.dom.innerHTML='Cuenta Corriente';
                          this.Cmp.id_auxiliar.store.baseParams.ro_activo='no';
                          this.Cmp.id_auxiliar.modificado = true;

                          this.mostrarComponente(this.Cmp.id_auxiliar);
                          this.Cmp.numero_tarjeta.reset();
                          this.Cmp.codigo_tarjeta.reset();
                          this.Cmp.saldo_recibo.reset();
                          this.Cmp.numero_tarjeta.allowBlank = true;
                          this.Cmp.mco2.allowBlank = true;
                          this.Cmp.codigo_tarjeta.allowBlank = true;
                          this.Cmp.id_auxiliar.allowBlank = false;
                      } else if (codigo_fp1.startsWith("RANT")) {
                        //cuenta corriente
                        this.ocultarComponente(this.Cmp.numero_tarjeta);
                        this.ocultarComponente(this.Cmp.mco);
                        this.ocultarComponente(this.Cmp.codigo_tarjeta);

                        /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                        this.ocultarComponente(this.Cmp.nro_cupon);
                        this.ocultarComponente(this.Cmp.nro_cuota);
                        this.Cmp.nro_cupon.allowBlank = true;
                        this.Cmp.nro_cuota.allowBlank = true;
                        /******************************************/
                        this.Cmp.id_auxiliar.reset();
                        this.Cmp.id_auxiliar.label.dom.innerHTML='Grupo';
                        this.Cmp.id_auxiliar.store.baseParams.ro_activo='si';
                        this.Cmp.id_auxiliar.modificado = true;
                        this.Cmp.id_venta.reset();
                        this.Cmp.id_venta.modificado = true;
                        this.mostrarComponente(this.Cmp.id_auxiliar);
                        this.mostrarComponente(this.Cmp.id_venta);
                        this.Cmp.numero_tarjeta.reset();
                        this.Cmp.codigo_tarjeta.reset();
                        this.Cmp.saldo_recibo.reset();

                        this.Cmp.numero_tarjeta.allowBlank = true;
                        this.Cmp.mco2.allowBlank = true;
                        this.Cmp.codigo_tarjeta.allowBlank = true;
                        this.Cmp.id_auxiliar.allowBlank = false;
                      }
                      else if (codigo_fp1.startsWith("MCO")) {
                          //mco
                          this.ocultarComponente(this.Cmp.numero_tarjeta);
                          this.ocultarComponente(this.Cmp.id_auxiliar);
                          this.Cmp.id_auxiliar.reset();
                          this.mostrarComponente(this.Cmp.mco);
                          this.ocultarComponente(this.Cmp.codigo_tarjeta);
                          this.ocultarComponente(this.Cmp.id_venta);
                          this.Cmp.id_venta.reset();
                          this.Cmp.saldo_recibo.reset();
                          this.Cmp.numero_tarjeta.reset();
                          this.Cmp.mco.allowBlank = false;
                          this.Cmp.codigo_tarjeta.allowBlank = true;
                          this.Cmp.id_auxiliar.allowBlank = true;
                          this.Cmp.numero_tarjeta.allowBlank = true;
                      }else {
                          this.ocultarComponente(this.Cmp.numero_tarjeta);
                          /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                          this.ocultarComponente(this.Cmp.nro_cupon);
                          this.ocultarComponente(this.Cmp.nro_cuota);
                          this.Cmp.nro_cupon.allowBlank = true;
                          this.Cmp.nro_cuota.allowBlank = true;
                          /******************************************/
                          this.ocultarComponente(this.Cmp.mco);
                          this.ocultarComponente(this.Cmp.codigo_tarjeta);
                          this.ocultarComponente(this.Cmp.id_auxiliar);
                          this.ocultarComponente(this.Cmp.id_venta);
                          this.Cmp.id_venta.reset();
                          this.Cmp.numero_tarjeta.reset();
                          this.Cmp.codigo_tarjeta.reset();
                          this.Cmp.id_auxiliar.reset();
                          this.Cmp.saldo_recibo.reset();
                          this.Cmp.numero_tarjeta.allowBlank = true;
                          this.Cmp.mco.allowBlank = true;
                          this.Cmp.codigo_tarjeta.allowBlank = true;
                          this.Cmp.id_auxiliar.allowBlank = true;
                      }
                  }

                }

                // seleccion de id_venta para controles con monto de recibo
                this.Cmp.id_venta.on('select', function(d, r, i){

                    var saldo = parseFloat(r.data.saldo).toFixed(2);
                    var imp1 = parseFloat(this.Cmp.monto_forma_pago.getValue()).toFixed(2);
                    var mon_sel = r.data.moneda;
                    var dif = imp1 - saldo;
                    this.Cmp.saldo_recibo.setValue(saldo);
                    if (imp1 > saldo){
                        Ext.Msg.show({
                         title:'<h1 style="color:red"><center>AVISO</center></h1>',
                         msg: '<b>El saldo del recibo es: <span style="color:red;"> '+mon_sel+ ' '+saldo+'</span> Falta un monto de <span style="color:red;">'+ mon_sel +' '+ dif +'</span> para la forma de pago recibo anticipo</b>',
                         maxWidth : 400,
                         width: 380,
                         buttons: Ext.Msg.OK,
                         scope:this
                        });
                    }

                },this)
            },
            manejoComponentesFp2 : function (id_fp2,codigo_fp2){
              if (this.store.baseParams.moneda_base == 'ARS') {
                if (id_fp2) {
                    //forma de pago 2
                    if (id_fp2 == 0) {
                        this.Cmp.id_forma_pago2.setDisabled(true);
                        this.Cmp.monto_forma_pago2.setDisabled(true);
                        this.ocultarComponente(this.Cmp.numero_tarjeta2);
                        this.ocultarComponente(this.Cmp.mco2);
                        this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                        this.ocultarComponente(this.Cmp.id_auxiliar2);
                        this.ocultarComponente(this.Cmp.id_venta_2);
                        this.Cmp.id_venta_2.reset();
                        this.Cmp.saldo_recibo_2.reset();
                        /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                        this.ocultarComponente(this.Cmp.nro_cupon_2);
                        this.ocultarComponente(this.Cmp.nro_cuota_2);
                        this.Cmp.nro_cupon_2.allowBlank = true;
                        this.Cmp.nro_cuota_2.allowBlank = true;
                        /**********************************************/

                        this.Cmp.numero_tarjeta2.allowBlank = true;
                        this.Cmp.mco2.allowBlank = true;
                        this.Cmp.codigo_tarjeta2.allowBlank = true;
                        this.Cmp.id_auxiliar2.allowBlank = true;
                        this.Cmp.numero_tarjeta2.reset();
                        this.Cmp.codigo_tarjeta2.reset();
                        this.Cmp.id_auxiliar2.reset();
                    } else {
                        this.Cmp.id_forma_pago2.setDisabled(false);
                        this.Cmp.monto_forma_pago2.setDisabled(false);
                        if (codigo_fp2.startsWith("CC") ||
                            codigo_fp2.startsWith("SF")) {
                            //tarjeta de credito
                            this.Cmp.id_auxiliar2.reset();
                            this.mostrarComponente(this.Cmp.numero_tarjeta2);
                            this.mostrarComponente(this.Cmp.codigo_tarjeta2);

                            /*Aumentando para solo dijitar los primeros y ultimos digitos de la tarjeta*/
                            this.Cmp.numero_tarjeta2.on('keyup', function (cmp, e) {
                                //inserta guiones en codigo de contorl
                                var value = cmp.getValue(),
                                tmp = '',
                                tmp2 = '',
                                sw = 0;

                                if (this.codigo_medio_pago_2 == 'AX') {
                                  var campoX = 'XXXXXXX';
                                } else {
                                  var campoX = 'XXXXXXXX';
                                }

                                tmp = value.replace(/-/g, '');
                                for (var i = 0; i < tmp.length; i++) {
                                    tmp2 = tmp2 + tmp[i];
                                    if ((i + 1) % 4 == 0 && tmp.length == 4) {
                                        tmp2 = tmp2 + campoX;
                                    }
                                }
                                cmp.setValue(tmp2.toUpperCase());
                            }, this);
                            /***************************************************************************/

                            /*Aumentando control en Interfaz para que el numero de tarjeta y codigo tarjeta no sean el mismo a
                            la primera forma de pago (Ismael Valdivia 16/09/2021)*/
                            this.Cmp.numero_tarjeta2.on('change',function(field,newValue,oldValue){
                              if (this.Cmp.numero_tarjeta.getValue() == this.Cmp.numero_tarjeta2.getValue()) {

                                 if (this.Cmp.codigo_tarjeta.getValue() != '' && this.Cmp.codigo_tarjeta2.getValue() != '') {
                                    if (this.Cmp.codigo_tarjeta.getValue() == this.Cmp.codigo_tarjeta2.getValue()) {
                                      Ext.Msg.show({
                                       title:'<h1 style="color:red"><center>AVISO</center></h1>',
                                       msg: '<b>El número de tarjeta y el codigo de tarjeta no pueden ser iguales en las formas de pago</b>',
                                       maxWidth : 400,
                                       width: 380,
                                       buttons: Ext.Msg.OK,
                                       scope:this
                                      });
                                    }
                                 }
                              }
                            },this);

                            this.Cmp.codigo_tarjeta2.on('change',function(field,newValue,oldValue){
                              if (this.Cmp.codigo_tarjeta.getValue() == this.Cmp.codigo_tarjeta2.getValue()) {
                                 if (this.Cmp.numero_tarjeta.getValue() != '' && this.Cmp.numero_tarjeta2.getValue() != '') {
                                    if (this.Cmp.numero_tarjeta.getValue() == this.Cmp.numero_tarjeta2.getValue()) {
                                      Ext.Msg.show({
                                       title:'<h1 style="color:red"><center>AVISO</center></h1>',
                                       msg: '<b>El número de tarjeta y el codigo de tarjeta no pueden ser iguales en las formas de pago</b>',
                                       maxWidth : 400,
                                       width: 380,
                                       buttons: Ext.Msg.OK,
                                       scope:this
                                      });
                                    }
                                 }
                              }
                            },this);
                            /*************************************************************************************************/




                            this.ocultarComponente(this.Cmp.id_auxiliar2);
                            this.ocultarComponente(this.Cmp.mco2);
                            this.ocultarComponente(this.Cmp.id_venta_2);
                            this.Cmp.id_venta_2.reset();
                            this.Cmp.saldo_recibo_2.reset();
                            this.Cmp.numero_tarjeta2.reset();
                            /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                            this.mostrarComponente(this.Cmp.nro_cupon_2);
                            this.mostrarComponente(this.Cmp.nro_cuota_2);
                            this.Cmp.nro_cupon_2.allowBlank = true;
                            this.Cmp.nro_cuota_2.allowBlank = true;
                            /**********************************************/

                            this.Cmp.numero_tarjeta2.allowBlank = false;
                            this.Cmp.codigo_tarjeta2.allowBlank = false;
                            this.Cmp.id_auxiliar2.allowBlank = true;
                            this.Cmp.mco2.allowBlank = true;

                        } else if (codigo_fp2.startsWith("CT") || codigo_fp2.startsWith("CU")) {
                            //cuenta corriente
                            this.ocultarComponente(this.Cmp.numero_tarjeta2);
                            this.ocultarComponente(this.Cmp.mco2);
                            this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                            this.mostrarComponente(this.Cmp.id_auxiliar2);

                            /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                            this.ocultarComponente(this.Cmp.nro_cupon_2);
                            this.ocultarComponente(this.Cmp.nro_cuota_2);
                            this.Cmp.nro_cupon_2.reset();
                            this.Cmp.nro_cuota_2.reset();
                            this.Cmp.nro_cupon_2.allowBlank = true;
                            this.Cmp.nro_cuota_2.allowBlank = true;
                            /**********************************************/
                            this.ocultarComponente(this.Cmp.id_venta_2);
                            this.Cmp.id_venta_2.reset();
                            this.Cmp.id_auxiliar2.reset();
                            this.Cmp.id_auxiliar2.label.dom.innerHTML='Cuenta Corriente';
                            this.Cmp.id_auxiliar2.store.baseParams.ro_activo='no';
                            this.Cmp.id_auxiliar2.modificado = true;

                            this.Cmp.numero_tarjeta2.reset();
                            this.Cmp.numero_tarjeta2.reset();
                            this.Cmp.saldo_recibo_2.reset();
                            this.Cmp.numero_tarjeta2.allowBlank = true;
                            this.Cmp.mco2.allowBlank = true;
                            this.Cmp.numero_tarjeta2.allowBlank = true;
                            this.Cmp.id_auxiliar2.allowBlank = false;
                        }else if (codigo_fp2.startsWith("RANT")) {
                          //cuenta corriente
                          this.ocultarComponente(this.Cmp.numero_tarjeta2);
                          this.ocultarComponente(this.Cmp.mco2);
                          this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                          this.Cmp.id_auxiliar2.reset();
                          this.Cmp.id_auxiliar2.label.dom.innerHTML='Grupo';
                          this.Cmp.id_auxiliar2.store.baseParams.ro_activo='si';
                          this.Cmp.id_auxiliar2.modificado = true;
                          this.Cmp.id_venta_2.reset();
                          this.Cmp.id_venta_2.modificado = true;
                          this.mostrarComponente(this.Cmp.id_auxiliar2);
                          this.mostrarComponente(this.Cmp.id_venta_2);
                          /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                          this.ocultarComponente(this.Cmp.nro_cupon_2);
                          this.ocultarComponente(this.Cmp.nro_cuota_2);
                          this.Cmp.nro_cupon_2.reset();
                          this.Cmp.saldo_recibo_2.reset();
                          this.Cmp.nro_cupon_2.allowBlank = true;
                          /**********************************************/

                          this.Cmp.numero_tarjeta2.reset();
                          this.Cmp.mco2.allowBlank = true;
                          this.Cmp.numero_tarjeta2.allowBlank = true;
                          this.Cmp.id_auxiliar2.allowBlank = false;

                        }
                        else if (codigo_fp2.startsWith("MCO")) {
                            //mco
                            this.ocultarComponente(this.Cmp.id_auxiliar2);
                            this.ocultarComponente(this.Cmp.numero_tarjeta2);
                            this.Cmp.id_auxiliar2.reset();
                            this.Cmp.saldo_recibo_2.reset();
                            this.mostrarComponente(this.Cmp.mco2);
                            this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                            this.ocultarComponente(this.Cmp.id_venta_2);
                            /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                            this.ocultarComponente(this.Cmp.nro_cupon_2);
                            this.ocultarComponente(this.Cmp.nro_cuota_2);
                            this.Cmp.nro_cupon_2.allowBlank = true;
                            this.Cmp.nro_cuota_2.allowBlank = true;
                            /**********************************************/


                            this.Cmp.mco2.allowBlank = false;
                            this.Cmp.codigo_tarjeta2.allowBlank = true;
                            this.Cmp.id_auxiliar2.allowBlank = true;
                            this.Cmp.numero_tarjeta2.allowBlank = true;
                        }else {
                            this.ocultarComponente(this.Cmp.numero_tarjeta2);
                            this.ocultarComponente(this.Cmp.mco2);
                            this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                            this.ocultarComponente(this.Cmp.id_auxiliar2);
                            this.ocultarComponente(this.Cmp.id_venta_2);
                            this.Cmp.id_venta_2.reset();
                            this.Cmp.saldo_recibo_2.reset();
                            /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                            this.ocultarComponente(this.Cmp.nro_cupon_2);
                            this.ocultarComponente(this.Cmp.nro_cuota_2);
                            this.Cmp.nro_cupon_2.reset();
                            this.Cmp.nro_cuota_2.reset();
                            this.Cmp.nro_cupon_2.allowBlank = true;
                            this.Cmp.nro_cuota_2.allowBlank = true;
                            /**********************************************/

                            this.Cmp.numero_tarjeta2.allowBlank = true;
                            this.Cmp.mco2.allowBlank = true;
                            this.Cmp.codigo_tarjeta2.allowBlank = true;
                            this.Cmp.id_auxiliar2.allowBlank = true;
                            this.Cmp.numero_tarjeta2.reset();
                            this.Cmp.codigo_tarjeta2.reset();
                            this.Cmp.id_auxiliar2.reset();
                        }
                    }
                } else {
                    this.ocultarComponente(this.Cmp.numero_tarjeta2);
                    this.ocultarComponente(this.Cmp.mco2);
                    this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                    this.ocultarComponente(this.Cmp.id_auxiliar2);
                    this.ocultarComponente(this.Cmp.id_venta_2);
                    this.Cmp.id_venta_2.reset();
                    this.Cmp.saldo_recibo_2.reset();
                    /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                    this.ocultarComponente(this.Cmp.nro_cupon_2);
                    this.ocultarComponente(this.Cmp.nro_cuota_2);
                    this.Cmp.nro_cupon_2.allowBlank = true;
                    this.Cmp.nro_cuota_2.allowBlank = true;
                    /**********************************************/

                    this.Cmp.numero_tarjeta2.allowBlank = true;
                    this.Cmp.mco2.allowBlank = true;
                    this.Cmp.codigo_tarjeta2.allowBlank = true;
                    this.Cmp.id_auxiliar2.allowBlank = true;
                    this.Cmp.id_forma_pago2.setDisabled(true);
                    this.Cmp.monto_forma_pago2.setDisabled(true);
                }
              } else {
                if (id_fp2) {
                    //forma de pago 2
                    if (id_fp2 == 0) {
                        this.Cmp.id_forma_pago2.setDisabled(true);
                        this.Cmp.monto_forma_pago2.setDisabled(true);
                        this.ocultarComponente(this.Cmp.numero_tarjeta2);
                        this.ocultarComponente(this.Cmp.mco2);
                        this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                        this.ocultarComponente(this.Cmp.id_auxiliar2);
                        this.ocultarComponente(this.Cmp.id_venta_2);
                        this.Cmp.id_venta_2.reset();
                        this.Cmp.saldo_recibo_2.reset();
                        /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                        this.ocultarComponente(this.Cmp.nro_cupon_2);
                        this.ocultarComponente(this.Cmp.nro_cuota_2);
                        this.Cmp.nro_cupon_2.reset();
                        this.Cmp.nro_cuota_2.reset();
                        this.Cmp.nro_cupon_2.allowBlank = true;
                        this.Cmp.nro_cuota_2.allowBlank = true;
                        /**********************************************/

                        this.Cmp.numero_tarjeta2.allowBlank = true;
                        this.Cmp.mco2.allowBlank = true;
                        this.Cmp.codigo_tarjeta2.allowBlank = true;
                        this.Cmp.id_auxiliar2.allowBlank = true;
                        this.Cmp.numero_tarjeta2.reset();
                        this.Cmp.codigo_tarjeta2.reset();
                        this.Cmp.id_auxiliar2.reset();
                    } else {
                        this.Cmp.id_forma_pago2.setDisabled(false);
                        this.Cmp.monto_forma_pago2.setDisabled(false);
                        if (codigo_fp2.startsWith("CC") ||
                            codigo_fp2.startsWith("SF")) {
                            //tarjeta de credito
                            this.Cmp.id_auxiliar2.reset();
                            this.mostrarComponente(this.Cmp.numero_tarjeta2);
                            this.mostrarComponente(this.Cmp.codigo_tarjeta2);

							/*Aumentando para solo dijitar los primeros y ultimos digitos de la tarjeta*/
                            this.Cmp.numero_tarjeta2.on('keyup', function (cmp, e) {
                                //inserta guiones en codigo de contorl
                                var value = cmp.getValue(),
                                tmp = '',
                                tmp2 = '',
                                sw = 0;

                                if (this.codigo_medio_pago_2 == 'AX') {
                                  var campoX = 'XXXXXXX';
                                } else {
                                  var campoX = 'XXXXXXXX';
                                }

                                tmp = value.replace(/-/g, '');
                                for (var i = 0; i < tmp.length; i++) {
                                    tmp2 = tmp2 + tmp[i];
                                    if ((i + 1) % 4 == 0 && tmp.length == 4) {
                                        tmp2 = tmp2 + campoX;
                                    }
                                }
                                cmp.setValue(tmp2.toUpperCase());
                            }, this);
                            /***************************************************************************/

                            /*Aumentando control en Interfaz para que el numero de tarjeta y codigo tarjeta no sean el mismo a
                            la primera forma de pago (Ismael Valdivia 16/09/2021)*/
                            this.Cmp.numero_tarjeta2.on('change',function(field,newValue,oldValue){
                              if (this.Cmp.numero_tarjeta.getValue() == this.Cmp.numero_tarjeta2.getValue()) {

                                 if (this.Cmp.codigo_tarjeta.getValue() != '' && this.Cmp.codigo_tarjeta2.getValue() != '') {
                                    if (this.Cmp.codigo_tarjeta.getValue() == this.Cmp.codigo_tarjeta2.getValue()) {
                                      Ext.Msg.show({
                                       title:'<h1 style="color:red"><center>AVISO</center></h1>',
                                       msg: '<b>El número de tarjeta y el codigo de tarjeta no pueden ser iguales en las formas de pago</b>',
                                       maxWidth : 400,
                                       width: 380,
                                       buttons: Ext.Msg.OK,
                                       scope:this
                                      });
                                    }
                                 }
                              }
                            },this);

                            this.Cmp.codigo_tarjeta2.on('change',function(field,newValue,oldValue){
                              if (this.Cmp.codigo_tarjeta.getValue() == this.Cmp.codigo_tarjeta2.getValue()) {
                                 if (this.Cmp.numero_tarjeta.getValue() != '' && this.Cmp.numero_tarjeta2.getValue() != '') {
                                    if (this.Cmp.numero_tarjeta.getValue() == this.Cmp.numero_tarjeta2.getValue()) {
                                      Ext.Msg.show({
                                       title:'<h1 style="color:red"><center>AVISO</center></h1>',
                                       msg: '<b>El número de tarjeta y el codigo de tarjeta no pueden ser iguales en las formas de pago</b>',
                                       maxWidth : 400,
                                       width: 380,
                                       buttons: Ext.Msg.OK,
                                       scope:this
                                      });
                                    }
                                 }
                              }
                            },this);
                            /*************************************************************************************************/

                            this.ocultarComponente(this.Cmp.id_auxiliar2);
                            this.ocultarComponente(this.Cmp.mco2);
                            this.ocultarComponente(this.Cmp.id_venta_2);
                            this.Cmp.id_venta_2.reset();
                            /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                            this.ocultarComponente(this.Cmp.nro_cupon_2);
                            this.ocultarComponente(this.Cmp.nro_cuota_2);
                            this.Cmp.nro_cupon_2.reset();
                            this.Cmp.nro_cuota_2.reset();
                            this.Cmp.saldo_recibo_2.reset();
                            this.Cmp.numero_tarjeta2.reset();
                            this.Cmp.nro_cupon_2.allowBlank = true;
                            this.Cmp.nro_cuota_2.allowBlank = true;
                            /**********************************************/

                            this.Cmp.numero_tarjeta2.allowBlank = false;
                            this.Cmp.codigo_tarjeta2.allowBlank = false;
                            this.Cmp.id_auxiliar2.allowBlank = true;
                            this.Cmp.mco2.allowBlank = true;

                        } else if (codigo_fp2.startsWith("CT") || codigo_fp2.startsWith("CU")) {
                            //cuenta corriente
                            this.ocultarComponente(this.Cmp.numero_tarjeta2);
                            this.ocultarComponente(this.Cmp.mco2);
                            this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                            this.ocultarComponente(this.Cmp.id_venta_2);
                            this.Cmp.id_venta_2.reset();
                            this.Cmp.id_auxiliar2.reset();
                            this.Cmp.id_auxiliar2.label.dom.innerHTML='Cuenta Corriente';
                            this.Cmp.id_auxiliar2.store.baseParams.ro_activo='no';
                            this.Cmp.id_auxiliar2.modificado = true;
                            this.mostrarComponente(this.Cmp.id_auxiliar2);

                            /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                            this.ocultarComponente(this.Cmp.nro_cupon_2);
                            this.ocultarComponente(this.Cmp.nro_cuota_2);
                            this.Cmp.nro_cupon_2.reset();
                            this.Cmp.nro_cuota_2.reset();
                            this.Cmp.saldo_recibo_2.reset();
                            this.Cmp.nro_cupon_2.allowBlank = true;
                            this.Cmp.nro_cuota_2.allowBlank = true;
                            /**********************************************/

                            this.Cmp.numero_tarjeta2.reset();
                            this.Cmp.numero_tarjeta2.reset();
                            this.Cmp.numero_tarjeta2.allowBlank = true;
                            this.Cmp.mco2.allowBlank = true;
                            this.Cmp.numero_tarjeta2.allowBlank = true;
                            this.Cmp.id_auxiliar2.allowBlank = false;
                        }  else if (codigo_fp2.startsWith("RANT")) {
                          //cuenta corriente
                          this.ocultarComponente(this.Cmp.numero_tarjeta2);
                          this.ocultarComponente(this.Cmp.mco2);
                          this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                          this.Cmp.id_auxiliar2.reset();
                          this.Cmp.id_auxiliar2.label.dom.innerHTML='Grupo';
                          this.Cmp.id_auxiliar2.store.baseParams.ro_activo='si';
                          this.Cmp.id_auxiliar2.modificado = true;
                          this.Cmp.id_venta_2.reset();
                          this.Cmp.id_venta_2.modificado = true;
                          this.mostrarComponente(this.Cmp.id_auxiliar2);
                          this.mostrarComponente(this.Cmp.id_venta_2);
                          /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                          this.ocultarComponente(this.Cmp.nro_cupon_2);
                          this.ocultarComponente(this.Cmp.nro_cuota_2);
                          this.Cmp.nro_cupon_2.reset();
                          this.Cmp.saldo_recibo_2.reset();
                          this.Cmp.nro_cuota_2.allowBlank = true;
                          /**********************************************/

                          this.Cmp.numero_tarjeta2.reset();
                          this.Cmp.mco2.allowBlank = true;
                          this.Cmp.numero_tarjeta2.allowBlank = true;
                          this.Cmp.id_auxiliar2.allowBlank = false;

                        }
                        else if (codigo_fp2.startsWith("MCO")) {
                            //mco
                            this.ocultarComponente(this.Cmp.id_auxiliar2);
                            this.ocultarComponente(this.Cmp.numero_tarjeta2);
                            this.Cmp.id_auxiliar2.reset();
                            this.mostrarComponente(this.Cmp.mco2);
                            this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                            this.ocultarComponente(this.Cmp.id_venta_2);
                            this.Cmp.id_venta_2.reset();
                            this.Cmp.saldo_recibo_2.reset();
                            /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                            this.ocultarComponente(this.Cmp.nro_cupon_2);
                            this.ocultarComponente(this.Cmp.nro_cuota_2);
                            this.Cmp.nro_cupon_2.reset();
                            this.Cmp.nro_cuota_2.reset();
                            this.Cmp.nro_cupon_2.allowBlank = true;
                            this.Cmp.nro_cuota_2.allowBlank = true;
                            /**********************************************/

                            this.Cmp.mco2.allowBlank = false;
                            this.Cmp.codigo_tarjeta2.allowBlank = true;
                            this.Cmp.id_auxiliar2.allowBlank = true;
                            this.Cmp.numero_tarjeta2.allowBlank = true;
                        }else {
                            this.ocultarComponente(this.Cmp.numero_tarjeta2);
                            this.ocultarComponente(this.Cmp.mco2);
                            this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                            this.ocultarComponente(this.Cmp.id_auxiliar2);
                            this.ocultarComponente(this.Cmp.id_venta_2);
                            this.Cmp.id_venta_2.reset();
                            /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                            this.ocultarComponente(this.Cmp.nro_cupon_2);
                            this.ocultarComponente(this.Cmp.nro_cuota_2);
                            this.Cmp.nro_cupon_2.reset();
                            this.Cmp.nro_cuota_2.reset();
                            this.Cmp.saldo_recibo_2.reset();
                            this.Cmp.nro_cupon_2.allowBlank = true;
                            this.Cmp.nro_cuota_2.allowBlank = true;
                            /**********************************************/

                            this.Cmp.numero_tarjeta2.allowBlank = true;
                            this.Cmp.mco2.allowBlank = true;
                            this.Cmp.codigo_tarjeta2.allowBlank = true;
                            this.Cmp.id_auxiliar2.allowBlank = true;
                            this.Cmp.numero_tarjeta2.reset();
                            this.Cmp.codigo_tarjeta2.reset();
                            this.Cmp.id_auxiliar2.reset();
                        }
                    }
                } else {
                    this.ocultarComponente(this.Cmp.numero_tarjeta2);
                    this.ocultarComponente(this.Cmp.mco2);
                    this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                    this.ocultarComponente(this.Cmp.id_auxiliar2);
                    this.ocultarComponente(this.Cmp.id_venta_2);
                    this.Cmp.id_venta_2.reset();
                    /*Aumentando estos dos campos para buenos aires*/ //Ismael Valdivia
                    this.ocultarComponente(this.Cmp.nro_cupon_2);
                    this.ocultarComponente(this.Cmp.nro_cuota_2);
                    this.Cmp.nro_cupon_2.reset();
                    this.Cmp.nro_cuota_2.reset();
                    this.Cmp.saldo_recibo_2.reset();
                    this.Cmp.nro_cupon_2.allowBlank = true;
                    this.Cmp.nro_cuota_2.allowBlank = true;
                    /**********************************************/

                    this.Cmp.numero_tarjeta2.allowBlank = true;
                    this.Cmp.mco2.allowBlank = true;
                    this.Cmp.codigo_tarjeta2.allowBlank = true;
                    this.Cmp.id_auxiliar2.allowBlank = true;
                    this.Cmp.id_forma_pago2.setDisabled(true);
                    this.Cmp.monto_forma_pago2.setDisabled(true);
                }
              }
              // seleccion de id_venta_2 para controles con monto de recibo
              this.Cmp.id_venta_2.on('select', function(d, r, i){
                  var saldo = parseFloat(r.data.saldo).toFixed(2);
                  var imp2 = parseFloat(this.Cmp.monto_forma_pago2.getValue()).toFixed(2);
                  var mon_sel = r.data.moneda;
                  var dif = parseFloat(imp2 - saldo).toFixed(2);
                  console.log("aqui data",saldo);
                  this.Cmp.saldo_recibo_2.setValue(saldo);
                  if (imp2 > saldo){
                      Ext.Msg.show({
                       title:'<h1 style="color:red"><center>AVISO</center></h1>',
                       msg: '<b>El saldo del recibo es: <span style="color:red;"> '+mon_sel+ ' '+saldo+'</span> Falta un monto de <span style="color:red;">'+ mon_sel +' '+ dif +'</span> para la forma de pago recibo anticipo</b>',
                       maxWidth : 400,
                       width: 380,
                       buttons: Ext.Msg.OK,
                       scope:this
                      });
                  }

              },this)
            },
            formViajeroFrecuente: function () {

                var dato = this.sm.getSelected().data;
                Phx.CP.loadWindows('../../../sis_obingresos/vista/boleto/FormViajeroFrecuente.php',
                    'Formulario Viajero Frecuente',
                    {
                        modal:true,
                        width:300,
                        height:200
                    },
                    dato,
                    this.idContenedor,
                    'FormViajeroFrecuente'
                );
            },
            onButtonVoucherCode:function() {
                var rec=this.sm.getSelected();
                console.log ('Data',rec.data);
                Phx.CP.loadWindows('../../../sis_obingresos/vista/boleto/ViajeroFrecuente.php',
                    'Voucher Code',
                    {
                        width:'50%',
                        height:'50%'
                    },
                    rec.data,
                    this.idContenedor,
                    'ViajeroFrecuente');
            },
            formFormualRevi: function (origen) {

                var ffid = new Ext.form.TextField(
                    {
                        name: 'ffid_consul',
                        msgTarget: 'title',
                        fieldLabel: 'FFID',
                        allowBlank: true,
                        anchor: '90%',
                        style: 'background-color:#9BF592 ; background-image: none;',
                        value: this.sm.getSelected().data['ffid_consul'],
                        maxLength:10
                    });

                var voucherCoide = new Ext.form.TextField(
                    {
                        name: 'voucher_consu',
                        msgTarget: 'title',
                        fieldLabel: 'Voucher Code',
                        allowBlank: true,
                        anchor: '90%',
                        value: this.sm.getSelected().data['voucher_consu'] ,
                        style: 'background-color: #9BF592; background-image: none;',
                        maxLength:10
                    });
                console.log(voucherCoide);
                var ticketNumber = new Ext.form.TextField(
                    {
                        name: 'ticketNumber',
                        msgTarget: 'title',
                        fieldLabel: 'Ticket Number',
                        allowBlank: true,
                        readOnly :true,
                        anchor: '90%',
                        maxLength: 10,
                        minLength: 10,
                        style: 'background-color: #E1F590; background-image: none;',
                        value: this.sm.getSelected().data['nro_boleto'] ,
                        maxLength:50
                    });
                var pnr = new Ext.form.TextField(
                    {
                        name: 'pnr',
                        msgTarget: 'title',
                        fieldLabel: 'PNR',
                        allowBlank: true,
                        readOnly :true,
                        anchor: '90%',
                        maxLength: 6,
                        minLength: 6,
                        style: 'background-color: #E1F590; background-image: none;',
                        value: this.sm.getSelected().data['localizador'] ,
                        maxLength:50
                    });

                var formularioInicio = new Ext.form.FormPanel({
                    items: [ffid,voucherCoide,ticketNumber,pnr],
                    padding: true,
                    bodyStyle:'padding:5px 5px 0',
                    border: false,
                    frame: false

                });

                var VentanaInicio = new Ext.Window({
                    title: 'Formulario Viajero Frecuente',
                    modal: true,
                    width: 300,
                    height: 200,
                    bodyStyle: 'padding:5px;',
                    layout: 'fit',
                    hidden: true,
                    closable: false,
                    buttons: [
                        {
                            text: '<i class="fa fa-check"></i> Aceptar',
                            handler: function () {
                              /*Aumentamos la condicion si se recupera el ffid y el voucher code mandamos el filtro Ismael Valdivia (11/12/2019)*/
                                this.ffid_recuperado = this.sm.getSelected().data['ffid_consul'] ;
                                this.vouchercode_recuperado = this.sm.getSelected().data['voucher_consu'];
                                if (this.ffid_recuperado == '' || this.vouchercode_recuperado == '') {
                                      this.llenado = 'vacio';
                                  } else {
                                      this.llenado = 'llenado';
                                  }
                              /**********************************************************************************/

                                if (formularioInicio.getForm().isValid()) {
                                    validado = true;
                                    this.ffid = ffid.getValue();
                                    this.voucher = voucherCoide.getValue();
                                    this.ticket = ticketNumber.getValue();
                                    this.pnr  = pnr.getValue();
                                    VentanaInicio.close();
                                    m = this;
                                    Ext.Ajax.request({
                                        url:'../../sis_obingresos/control/Boleto/viajeroFrecuente',
                                        params:{id_boleto_amadeus: m.sm.getSelected().data['id_boleto_amadeus'],
                                            ffid: this.ffid, voucherCode:this.voucher , ticketNumber: this.ticket,
                                            pnr:this.pnr ,bandera:'revisar',dato_llenado:this.llenado,id_punto_venta:this.id_punto_venta},
                                        success:function(resp){
                                            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                                            console.log('id',reg);
                                            this.Cmp.ffid.setValue(this.ffid);
                                            this.Cmp.voucher_code.setValue(this.voucher);
                                        },
                                        failure: this.conexionFailure,
                                        timeout:this.timeout,
                                        scope:this
                                    });
                                    this.Cmp.ffid.setValue(this.ffid);
                                    this.Cmp.voucher_code.setValue(this.voucher);
                                }
                            },
                            scope: this
                        },
                        {
                            text: '<i class="fa fa-check"></i> Ignorar',
                            handler: function () {

                                // VentanaInicio.close();
                                if (formularioInicio.getForm().isValid()) {
                                    validado = true;
                                    this.ffid = ffid.getValue();
                                    this.voucher = voucherCoide.getValue();
                                    this.ticket = ticketNumber.getValue();
                                    this.pnr  = pnr.getValue();
                                    m = this;
                                    Ext.Ajax.request({
                                        url:'../../sis_obingresos/control/Boleto/logViajeroFrecuente',
                                        params:{    id_boleto_amadeus: m.sm.getSelected().data['id_boleto_amadeus'],
                                            importe: m.sm.getSelected().data['neto'],
                                            moneda:m.sm.getSelected().data['moneda'] ,
                                            tickert_number: this.ticket,
                                            pnr:this.pnr },
                                        success:function(resp){
                                            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                                            console.log('respuesta',reg);
                                            var res= this.Cmp.ffid.setValue(this.ffid);
                                            this.Cmp.voucher_code.setValue(this.voucher);
                                            console.log('no muestra ',res);
                                        },
                                        failure: this.conexionFailure,
                                        timeout:this.timeout,
                                        scope:this
                                    });
                                    VentanaInicio.close();
                                    Phx.CP.loadingShow();
                                    Ext.Ajax.request({
                                        url:'../../sis_obingresos/control/Boleto/cambiarRevisionBoleto',
                                        params:{ id_boleto_amadeus: m.sm.getSelected().data['id_boleto_amadeus']},
                                        success: this.successRevision,
                                        failure: this.conexionFailure,
                                        timeout: this.timeout,
                                        scope: this
                                    });
                                }
                            },
                            scope: this
                        },
                        {
                            text: '<i class="fa fa-check"></i> Declinar',
                            handler : function() {
                                console.log(formularioInicio.getForm());
                                formularioInicio.getForm().reset();
                            },
                            scope: this
                        }
                    ],
                    items: formularioInicio,
                    autoDestroy: true,
                    closeAction: 'close'
                });
                VentanaInicio.show();
            },
            formFormual: function (origen) {
                //this.Cmp.voucher_consu.setReadOnly(false);
                console.log("lega aqui el dato",this);
                var ffid = new Ext.form.TextField(
                    {
                        name: 'ffid_consul',
                        msgTarget: 'title',
                        fieldLabel: 'FFID',
                        allowBlank: true,
                        anchor: '90%',
                        style: 'background-color:#9BF592 ; background-image: none;',
                        value: this.sm.getSelected().data['ffid_consul'] ,
                        maxLength:10,
                        minLenght:10,
                        Type: 'NumberField'
                    });

                var voucherCoide = new Ext.form.TextField(
                    {
                        name: 'voucher_consu',
                        msgTarget: 'title',
                        fieldLabel: 'Voucher Code',
                        allowBlank: true,
                        anchor: '90%',
                        style: 'background-color: #9BF592; background-image: none;',
                        value: this.sm.getSelected().data['voucher_consu'] ,
                        maxLength:10,
                        minLenght:10

                    });
                var ticketNumber = new Ext.form.TextField(
                    {
                        name: 'ticketNumber',
                        msgTarget: 'title',
                        fieldLabel: 'Ticket Number',
                        allowBlank: true,
                        readOnly :true,
                        maxLength: 10,
                        minLength: 10,
                        anchor: '90%',
                        style: 'background-color: #E1F590; background-image: none;',
                        value: this.sm.getSelected().data['nro_boleto'] ,
                        maxLength:50,
                    });
                var pnr = new Ext.form.TextField(
                    {
                        name: 'pnr',
                        msgTarget: 'title',
                        fieldLabel: 'PNR',
                        allowBlank: true,
                        readOnly :true,
                        maxLength: 6,
                        minLength: 6,
                        anchor: '90%',
                        style: 'background-color: #E1F590; background-image: none;',
                        value: this.sm.getSelected().data['localizador'] ,
                        maxLength:50
                    });

                var formularioInicio = new Ext.form.FormPanel({
                    items: [ffid,voucherCoide,ticketNumber,pnr],
                    padding: true,
                    bodyStyle:'padding:5px 5px 0',
                    border: false,
                    frame: false

                });

                var VentanaInicio = new Ext.Window({
                    title: 'Formulario Viajero Frecuente',
                    modal: true,
                    width: 300,
                    height: 200,
                    bodyStyle: 'padding:5px;',
                    layout: 'fit',
                    hidden: true,
                    closable: false,
                    buttons: [
                        {
                            text: '<i class="fa fa-check"></i> Aceptar',
                            handler: function () {
                              /*Aumentamos la condicion si se recupera el ffid y el voucher code mandamos el filtro Ismael Valdivia (11/12/2019)*/
                                this.ffid_recuperado = this.sm.getSelected().data['ffid_consul'] ;
                                this.vouchercode_recuperado = this.sm.getSelected().data['voucher_consu'];
                                if (this.ffid_recuperado == '' || this.vouchercode_recuperado == '') {
                                      this.llenado = 'vacio';
                                  } else {
                                      this.llenado = 'llenado';
                                  }
                                                                /**********************************************************************************/
                                if (formularioInicio.getForm().isValid()) {
                                    validado = true;
                                    this.ffid = ffid.getValue();
                                    this.voucher = voucherCoide.getValue();
                                    this.ticket = ticketNumber.getValue();
                                    this.pnr  = pnr.getValue();

                                    VentanaInicio.close();
                                    m = this;

                                    Ext.Ajax.request({
                                        url:'../../sis_obingresos/control/Boleto/viajeroFrecuente',
                                        params:{id_boleto_amadeus: m.sm.getSelected().data['id_boleto_amadeus'],
                                            ffid: this.ffid, voucherCode:this.voucher , ticketNumber: this.ticket,
                                            pnr:this.pnr,bandera:'form',dato_llenado:this.llenado, id_punto_venta:this.id_punto_venta},
                                        success:function(resp){
                                            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                                            console.log('id:prueba',reg);
                                            this.Cmp.ffid.setValue(this.ffid);
                                            this.Cmp.voucher_code.setValue(this.voucher);
                                        },
                                        failure: this.conexionFailure,
                                        timeout:this.timeout,
                                        scope:this
                                    });
                                    var aux1 = this.Cmp.ffid.setValue(this.ffid);
                                    this.Cmp.voucher_code.setValue(this.voucher);
                                    console.log(aux1);

                                }
                            },
                            scope: this
                        },
                        {
                            text: '<i class="fa fa-check"></i> Ignorar',
                            handler: function () {

                                // VentanaInicio.close();
                                //Ext.Msg.alert('<b> Se debe registrar el Voucher y FFID "Aceptar" </b>')
                                if (formularioInicio.getForm().isValid()) {
                                    validado = true;
                                    this.ffid = ffid.getValue();
                                    this.voucher = voucherCoide.getValue();
                                    this.ticket = ticketNumber.getValue();
                                    this.pnr  = pnr.getValue();
                                    m = this;
                                    Ext.Ajax.request({
                                        url:'../../sis_obingresos/control/Boleto/logViajeroFrecuente',
                                        params:{    id_boleto_amadeus: m.sm.getSelected().data['id_boleto_amadeus'],
                                            importe: m.sm.getSelected().data['neto'],
                                            moneda:m.sm.getSelected().data['moneda'] ,
                                            tickert_number: this.ticket,
                                            pnr:this.pnr },
                                        success:function(resp){
                                            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                                            console.log('respuesta',reg);
                                        },
                                        failure: this.conexionFailure,
                                        timeout:this.timeout,
                                        scope:this
                                    });
                                    if(m.sm.getSelected().data['ffid_consul'] == null || m.sm.getSelected().data['ffid_consul'] == ''){
                                        //if(this.ffid == null || this.voucher == null){Ext.Msg.alert('no se puede ignorar');}
                                        VentanaInicio.close();}
                                        else{Ext.Msg.alert('Importante','<b> Se debe registrar el Voucher / FFID  </b>');}

                                }
                            },
                            scope: this
                        },
                        {
                            text: '<i class="fa fa-check"></i> Declinar',
                            handler : function() {
                                //refresh source grid
                                console.log(formularioInicio.getForm());
                                formularioInicio.getForm().reset();
                            },
                            scope: this
                        }
                    ],
                    items: formularioInicio,
                    autoDestroy: true,
                    closeAction: 'close'
                });
                VentanaInicio.show();
            },

        }
    )
</script>
