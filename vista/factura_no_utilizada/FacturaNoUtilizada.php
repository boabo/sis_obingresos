<?php
/**
 *@package pXP
 *@file gen-FacturaNoUtilizada.php
 *@author  Maylee Perez Pastor
 *@date 20-05-2020 19:08:47
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.FacturaNoUtilizada=Ext.extend(Phx.gridInterfaz,{
            mosttar:'',
            solicitarPuntoVenta: true,

            formUrl: '../../../sis_obingresos/vista/factura_no_utilizada/FormFacturaNoUtilizada.php',
            formClass : 'FormFacturaNoUtilizada',
            nombreVista: 'FacturaNoUtilizada',
            //tipo_factura: 'manual',
            solicitarSucursal: true, //para indicar si es forzoso o no indicar la sucrsal al iniciar
//	tipo_usuario : 'cajero',


            constructor:function(config){
                this.maestro=config.maestro;
                //this.tipo_usuario = 'cajero';
                //console.log("lelga aqui tipo",this);

                Ext.Ajax.request({
                    url:'../../sis_ventas_facturacion/control/Cajero/getTipoUsuario',
                    params: {'vista':'cajero'},
                    success: function(resp){
                        var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                        this.tipo_usuario = reg.ROOT.datos.v_tipo_usuario;
                    },
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });

                Ext.Ajax.request({
                    url:'../../sis_ventas_facturacion/control/Venta/getVariablesBasicas',
                    params: {'prueba':'uno'},
                    success:this.successGetVariables,
                    failure: this.conexionFailure,
                    arguments:config,
                    timeout:this.timeout,
                    scope:this
                });
                this.cmbPuntoV.on('select', function( combo, record, index){
                    this.capturaFiltros();
                },this);



            },

            successGetVariables : function (response,request) {

                var respuesta = JSON.parse(response.responseText);
                if('datos' in respuesta){
                    this.variables_globales = respuesta.datos;
                }
                if(this.solicitarPuntoVenta){
                    this.seleccionarPuntoVentaSucursal();

                }
                Phx.vista.FacturaNoUtilizada.superclass.constructor.call(this,request.arguments);
                this.store.baseParams.tipo_usuario = this.tipo_usuario;
                //this.store.baseParams.pes_estado = 'borrador';
                //	this.bbar.add(this.cmbPuntoV);
                /*this.addButton('completar_pago_2',{
                    grupo:[0],
                    text :'Completar Pago',
                    iconCls : 'bmoney',
                    disabled: true,
                    handler : this.completar_pago,
                    tooltip : '<b>Formulario para completar el pago</b>'
                });*/

                /*this.addButton('asociar_boletos',
                    {   grupo:[2],
                        text: 'Asociar Boletos',
                        iconCls: 'bchecklist',
                        disabled: true,
                        handler: this.AsociarBoletos,
                        tooltip: '<b>Asociar Boletos</b><br/>Asocia Boletos a la factura emitida.'
                    }
                );*/

                this.init();

                this.campo_fecha = new Ext.form.DateField({
                    name: 'fecha_reg',
                    grupo: this.bactGroups,
                    fieldLabel: 'Fecha',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    hidden : true
                });


                this.punto_venta = new Ext.form.Label({
                    name: 'punto_venta',
                    grupo: this.bactGroups,
                    fieldLabel: 'P.V.',
                    readOnly:true,
                    anchor: '150%',
                    gwidth: 150,
                    format: 'd/m/Y',
                    hidden : false,
                    //style: 'font-size: 170%; font-weight: bold; background-image: none;'
                    style: {
                        fontSize:'170%',
                        fontWeight:'bold',
                        color:'black',
                        textShadow: '0.5px 0.5px 0px #FFFFFF, 1px 0px 0px rgba(0,0,0,0.15)',
                        marginLeft:'20px'
                    }
                });



                this.tbar.addField(this.campo_fecha);
                this.tbar.addField(this.punto_venta);
                //this.bbar.addField(this.apertura);

                var datos_respuesta = JSON.parse(response.responseText);
                var fecha_array = datos_respuesta.datos.fecha.split('/');
                this.campo_fecha.setValue(new Date(fecha_array[2],parseInt(fecha_array[1]) - 1,fecha_array[0]));


                this.campo_fecha.on('select',function(value){
                    this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('d/m/Y');
                    //console.log("LLEGA FECHA SELEC",this.store);
                    this.load();
                },this);



                this.finCons = true;
                this.bbar.el.dom.style.background='#A3C9F7';
                this.tbar.el.dom.style.background='#A3C9F7';
                this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#FEFFF4';
                this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#FFF4EB';


            },


           /* gruposBarraTareas:[{name:'borrador',title:'<H1 style="font-size:12px;" align="center"><i style="color:#FFAE00; font-size:15px;" class="fa fa-eraser"></i> Borrador</h1>',grupo:0,height:0},
                {name:'caja',title:'<H1 style="font-size:12px;" align="center"><i style="color:green; font-size:15px;" class="fa fa-usd"></i> En Caja</h1>',grupo:1,height:0},
                {name:'finalizado',title:'<H1 style="font-size:12px;" align="center"><i style="color:#B61BFF; font-size:15px;" class="fa fa-check-circle"></i> Emitidos</h1>',grupo:2,height:0},
                {name:'anulado',title:'<H1 style="font-size:12px;" align="center"><i style="color:red; font-size:15px;" class="fa fa-ban"></i> Anulados</h1>',grupo:3,height:0}
            ],*/

            /*actualizarSegunTab: function(name, indice){
                if(this.finCons){
                    this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('d/m/Y');
                    this.store.baseParams.pes_estado = name;
                    this.store.baseParams.interfaz = 'vendedor';

                    this.load({params:{start:0, limit:this.tam_pag}});
                }
            },*/

            preparaMenu: function () {
                var rec = this.sm.getSelected();


                Phx.vista.FacturaNoUtilizada.superclass.preparaMenu.call(this);
            },

            liberaMenu : function(){
                var rec = this.sm.getSelected();

                Phx.vista.FacturaNoUtilizada.superclass.liberaMenu.call(this);
            },


            bactGroups:  [0,1,2,3],
            btestGroups: [0],
            bexcelGroups: [0,1,2],
            bnewGroups: [0],
            bdelGroups:[0,1],

            seleccionarPuntoVentaSucursal : function () {
                var validado = false;
                var title;
                var value;
                if (this.variables_globales.vef_tiene_punto_venta === 'true') {
                    title = 'Seleccione el punto de venta con el que trabajara';
                    value = 'id_punto_venta';
                    var storeCombo = new Ext.data.JsonStore({
                        url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
                        //url: '../../sis_obingresos/control/Boleto/obtenerPuntosVentasCounter',
                        id: 'id_punto_venta',
                        root: 'datos',
                        sortInfo: {
                            field: 'nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_punto_venta', 'id_sucursal','nombre', 'codigo','habilitar_comisiones','formato_comprobante'],
                        remoteSort: true,
                        baseParams: {tipo_usuario: this.tipo_usuario,par_filtro: 'puve.nombre#puve.codigo', tipo_factura: this.tipo_factura, tipo : this.tipo}
                    });
                } else {
                    title = 'Seleccione la sucursal con la que trabajara';
                    value = 'id_sucursal';
                    var storeCombo = new Ext.data.JsonStore({
                        url: '../../sis_ventas_facturacion/control/Sucursal/listarSucursal',
                        id: 'id_sucursal',
                        root: 'datos',
                        sortInfo: {
                            field: 'nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_sucursal', 'nombre', 'codigo','habilitar_comisiones','formato_comprobante','id_entidad'],
                        remoteSort: true,
                        baseParams: {tipo_usuario: this.tipo_usuario,par_filtro: 'suc.nombre#suc.codigo', tipo_factura: this.tipo_factura}
                    });
                }

                storeCombo.load({params:{start: 0, limit: this.tam_pag},
                    callback : function (r) {
                        /*if (r.length == 0 ) {*///comentando para que liste vacio
                        if (this.variables_globales.vef_tiene_punto_venta === 'false' ) {
                            if (this.variables_globales.vef_tiene_punto_venta === 'true') {
                                this.variables_globales.id_punto_venta = r[0].data.id_punto_venta;
                                this.variables_globales.habilitar_comisiones = r[0].data.habilitar_comisiones;
                                this.variables_globales.formato_comprobante = r[0].data.formato_comprobante;
                                this.store.baseParams.id_punto_venta = this.variables_globales.id_punto_venta;
                                this.store.baseParams.tipo_usuario = this.tipo_usuario;
                            } else {
                                this.variables_globales.id_sucursal = r[0].data.id_sucursal;
                                this.variables_globales.id_entidad = r[0].data.id_entidad;
                                this.variables_globales.habilitar_comisiones = r[0].data.habilitar_comisiones;
                                this.variables_globales.formato_comprobante = r[0].data.formato_comprobante;
                                this.store.baseParams.id_sucursal = this.variables_globales.id_sucursal;
                                this.store.baseParams.tipo_usuario = this.tipo_usuario;
                            }
                            this.store.baseParams.tipo_factura = this.tipo_factura;
                            this.load({params:{start:0, limit:this.tam_pag}});
                        } else {

                            var combo2 = new Ext.form.ComboBox(
                                {
                                    typeAhead: false,
                                    fieldLabel: title,
                                    allowBlank : false,
                                    store: storeCombo,
                                    mode: 'remote',
                                    pageSize: 15,
                                    triggerAction: 'all',
                                    valueField : value,
                                    displayField : 'nombre',
                                    forceSelection: true,
                                    tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
                                    allowBlank : false,
                                    anchor: '100%',
                                    resizable : true
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
                                width: 400,
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
                                                this.variables_globales.habilitar_comisiones = combo2.getStore().getById(combo2.getValue()).data.habilitar_comisiones;
                                                this.variables_globales.formato_comprobante = combo2.getStore().getById(combo2.getValue()).data.formato_comprobante;
                                                VentanaInicio.close();

                                                if (this.variables_globales.vef_tiene_punto_venta === 'true') {
                                                    this.variables_globales.id_punto_venta = combo2.getValue();
                                                    this.variables_globales.id_sucursal = storeCombo.getById(combo2.getValue()).data.id_sucursal;
                                                    this.store.baseParams.id_punto_venta = this.variables_globales.id_punto_venta;
                                                } else {
                                                    this.variables_globales.id_sucursal = combo2.getValue();
                                                    this.store.baseParams.id_sucursal = this.variables_globales.id_sucursal;
                                                }

                                                this.store.baseParams.tipo_usuario = this.tipo_usuario;
                                                this.store.baseParams.tipo_factura = 'manual';
                                                this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('d/m/Y');
                                                this.punto_venta.setText(combo2.lastSelectionText)
                                                this.load({params:{start:0, limit:this.tam_pag}});
                                                this.iniciarEventos();
                                            }
                                        },
                                        scope: this
                                    }],
                                items: formularioInicio,
                                autoDestroy: true,
                                closeAction: 'close'
                            });
                            VentanaInicio.show();
                            VentanaInicio.mask.dom.style.background='black';
                            VentanaInicio.body.dom.childNodes[0].firstChild.firstChild.style.background='#A3C9F7';
                            //VentanaInicio.body.dom.childNodes.style.background='black';
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

            onDestroy: function() {
                //Phx.baseInterfaz.superclass.destroy.call(this,c);
                this.store.baseParams.id_punto_venta = '';
                this.fireEvent('closepanel',this);

                if (this.window) {
                    this.window.destroy();
                }
                if (this.form) {
                    this.form.destroy();
                }

                Phx.CP.destroyPage(this.idContenedor);
                delete this;

            },

            iniciarEventos:function(){


            },

            onButtonAct:function () {
                this.iniciarEventos();
                this.reload();
            },

            openForm : function (tipo, record) {
                var me = this;
                me.objSolForm = Phx.CP.loadWindows(this.formUrl,
                    '<div style="height:30px;"><img src="../../../lib/imagenes/logos/boa_mini_logo.png" style="position:absolute;"><h1 style=" text-align:center; font-size:25px; color:#0E00B7; text-shadow: -1px -1px 1px rgba(255,255,255,.1), 1px 1px 1px rgba(0,0,0,.5);"><i style="color:red; font-size:30px;" class="fa fa-pencil" aria-hidden="true"></i> Facturas No Utilizadas</h1></div>',
                    {
                        modal:true,
                        width:'75%',
                        height:'60%',
                        onEsc: function() {
                            var me = this;
                            Ext.Msg.confirm(
                                'Mensaje de Confirmación',
                                'Quiere cerrar el Formulario?, se perderán los datos que no han sido Guardados',
                                function(btn) {
                                    if (btn == 'yes')
                                        me.hide();
                                }
                            );
                        },
                    }, {data:{objPadre : me,
                            tipo_form : tipo,
                            datos_originales: record,
                            readOnly : this.readOnly}
                    },
                    this.idContenedor,
                    this.formClass,
                    {
                        config:[{
                            event:'successsave',
                            delegate: this.onSaveForm,

                        }],

                        scope:this
                    });
            },


           /* completar_pago : function () {
                //abrir formulario de solicitud
                this.openForm('edit', this.sm.getSelected());

            },*/

           /* sigEstado:function(){
                //Phx.CP.loadingShow();
                var d = this.sm.getSelected().data;
                //console.log("llega aqui el id y el proceso",d);
                Ext.Ajax.request({
                    url:'../../sis_ventas_facturacion/control/VentaFacturacion/siguienteEstadoRecibo',
                    params:{id_estado_wf_act:d.id_estado_wf,
                        id_proceso_wf_act:d.id_proceso_wf,
                        tipo:'recibo'},
                    success:this.successWizard,
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });

            },*/

            /*failureWizard:function(resp1,resp2,resp3,resp4,resp5){
                var resp = resp1;// error conexion
                var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                if (reg.ROOT.detalle.mensaje.indexOf('insuficientes')!=-1) {
                    var mensaje = reg.ROOT.detalle.mensaje;
                    mensaje = mensaje.replace(/#/g, "");
                    mensaje = mensaje.replace("*", "");
                    mensaje = mensaje.replace("*", "");
                    mensaje = mensaje.replace("{", "");
                    mensaje = mensaje.replace("}", "");
                    alert(mensaje);
                    Phx.CP.loadingHide();

                } else {
                    Phx.vista.ReciboLista.superclass.conexionFailure.call(this,resp1,resp2,resp3,resp4,resp5);
                }

            },*/
            /*
            successWizard:function(resp){
                var rec=this.sm.getSelected();
                var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                Phx.CP.getPagina(this.idContenedor).reload();
                resp.argument.wizard.panel.destroy();
                //console.log("ventana",panel);
                //console.log("this",resp);

                //

            },*/

            onButtonNew : function () {
                //abrir formulario de solicitud
                this.openForm('new');
            },

           /* anular : function () {
                Phx.CP.loadingShow();
                var rec=this.sm.getSelected();

                Ext.Ajax.request({
                    url:'../../sis_ventas_facturacion/control/Cajero/anularFactura',
                    params:{
                        id_venta:  rec.data.id_venta
                    },
                    success:this.successSave,
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });
            },*/

           /* AsociarBoletos: function(){

                var rec = {maestro: this.sm.getSelected().data}
                console.log('VALOR',	rec);
                Phx.CP.loadWindows('../../../sis_ventas_facturacion/vista/venta/AsociarBoletos.php',
                    '<center><h1 style="font-size:25px; color:#0E00B7; text-shadow: -1px -1px 1px rgba(255,255,255,.1), 1px 1px 1px rgba(0,0,0,.5);"> <img src="../../../lib/imagenes/icono_dibu/dibu_zoom.png" style="float:center; vertical-align: middle;"> Asociar Boletos</h1></center>',
                    {
                        width:1200,
                        height:600
                    },
                    rec,
                    this.idContenedor,
                    'AsociarBoletos');

            },*/

            /*imprimirNota: function(){
                var rec = this.sm.getSelected();
                //console.log("llega para imprimir",this);
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url : '../../sis_ventas_facturacion/control/Cajero/reporteFactura',
                    params : {
                        'id_venta' : rec.data.id_venta ,
                        'id_punto_venta' : rec.data.id_punto_venta,
                        'formato_comprobante' : this.variables_globales.formato_comprobante,
                        'tipo_factura': this.store.baseParams.tipo_factura
                    },
                    success : this.successExportHtml,
                    failure : this.conexionFailure,
                    timeout : this.timeout,
                    scope : this
                });

            },*/

            /*successExportHtml: function (resp) {
                Phx.CP.loadingHide();
                var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                var objetoDatos = (objRes.ROOT == undefined)?objRes.datos:objRes.ROOT.datos;
                var wnd = window.open("about:blank", "", "_blank");
                wnd.document.write(objetoDatos.html);
            },*/
            //loadMask :false,
            Atributos:[
                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_factura_no_utilizada'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_dosificacion'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config:{
                        name: 'estacion',
                        fieldLabel: 'Estación',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 90,
                        maxLength:150,
                        readOnly: true
                    },
                    type:'TextField',
                    filters:{pfiltro:'lu.codigo',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'tipo',
                        fieldLabel: 'Tipo Documento',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 120,
                        triggerAction: 'all',
                        lazyRender:true,
                        mode: 'local',
                        readOnly: true,
                        store: new Ext.data.ArrayStore({
                            id: 0,
                            fields: [
                                'id',
                                'display'
                            ],
                            data: [['F', 'Factura'], ['N', 'Nota de Credito/Debito']]
                        }),
                        valueField: 'id',
                        displayField: 'display',
                        renderer:function (value, p, record){
                            if (value == 'F') {
                                return 'Factura';
                            } else {
                                return 'Nota de Credito/Debito'
                            }
                        }
                    },
                    type:'ComboBox',
                    filters:{ type: 'list',
                        options: ['F','N']
                    },
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'nro_tramite',
                        fieldLabel: 'Nro Trámite',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 150,
                        maxLength:150,
                        allowDecimals:false,
                        allowNegative:false,
                        readOnly: true
                    },
                    type:'NumberField',
                    filters:{pfiltro:'dos.nro_tramite',type:'numeric'},
                    id_grupo:0,
                    grid:true,
                    form:true
                },

                {
                    config: {
                        name: 'id_sucursal',
                        fieldLabel: 'Sucursal',
                        allowBlank: true,
                        emptyText: 'Elija una Suc...',
                        readOnly: true,
                        store: new Ext.data.JsonStore({
                            url: '../../sis_ventas_facturacion/control/Sucursal/listarSucursal',
                            id: 'id_sucursal',
                            root: 'datos',
                            sortInfo: {
                                field: 'nombre',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_sucursal', 'nombre', 'codigo'],
                            remoteSort: true,
                            baseParams: {tipo_usuario: 'cajero', par_filtro: 'suc.nombre#suc.codigo'}
                        }),
                        valueField: 'id_sucursal',
                        gdisplayField: 'nombre_sucursal',
                        displayField: 'nombre',
                        hiddenName: 'id_sucursal',
                        tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        minChars: 2,
                        width: 250,
                        gwidth: 230,
                        resizable: true,
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['nom_sucursal']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
                    filters: {pfiltro: 'su.nombre', type: 'string'},
                    form: true,
                    grid: true,
                    bottom_filter: true
                },
                {
                    config:{
                        name: 'fecha',
                        fieldLabel: 'Fecha',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'fam.fecha',type:'date'},
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'fecha_dosificacion',
                        fieldLabel: 'Fecha de Dosificación',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 120,
                        readOnly: true,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'dos.fecha_dosificacion',type:'date'},
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'nroaut',
                        fieldLabel: 'No Autorización',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 120,
                        maxLength:150,
                        readOnly: true
                    },
                    type:'TextField',
                    filters:{pfiltro:'dos.nroaut',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true,
                    bottom_filter:true
                },

                {
                    config:{
                        name: 'inicial',
                        fieldLabel: 'Nro. Fac. Inicial',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:150,
                        allowDecimals:false,
                        allowNegative:false
                    },
                    type:'TextField',
                    filters:{pfiltro:'dos.inicial',type:'numeric'},
                    id_grupo:0,
                    grid:true,
                    form:true
                },

                {
                    config:{
                        name: 'final',
                        fieldLabel: 'Nro. Fac. Final',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:150,
                        allowDecimals:false,
                        allowNegative:false
                    },
                    type:'NumberField',
                    filters:{pfiltro:'dos.final',type:'numeric'},
                    id_grupo:0,
                    grid:true,
                    form:true
                },

                {
                    config: {
                        name: 'id_punto_venta',
                        fieldLabel: 'Agt / PV',
                        allowBlank: true,
                        emptyText: '...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
                            id: 'id_punto_venta',
                            root: 'datos',
                            sortInfo: {
                                field: 'nombre',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_punto_venta', 'nombre', 'codigo', 'habilitar_comisiones'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'puve.nombre#puve.codigo'}
                        }),
                        valueField: 'id_punto_venta',
                        gdisplayField: 'nom_punto_venta',
                        displayField: 'nombre',
                        hiddenName: 'id_punto_venta',
                        tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        minChars: 2,
                        width: 250,
                        gwidth: 230,
                        resizable: true,
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['nom_punto_venta']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    filters: {pfiltro: 'su.nombre', type: 'string'},
                    form: true,
                    grid: true,
                    bottom_filter: true
                },
                /*{
                    config: {
                        name: 'id_estado_factura',
                        fieldLabel: 'Estado',
                        allowBlank: true,
                        emptyText: 'Estado Factura ...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_obingresos/control/EstadoFactura/listarEstadoFactura',
                            id: 'id_estado_factura',
                            root: 'datos',
                            sortInfo: {
                                field: 'codigo',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_estado_factura','codigo', 'nombre'],
                            remoteSort: true
                        }),
                        valueField: 'id_estado_factura',
                        gdisplayField: 'codigo_estado',
                        displayField: 'codigo',
                        hiddenName: 'id_estado_factura',
                        tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        minChars: 2,
                        width: 250,
                        gwidth: 230,
                        resizable: true,
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['codigo_estado']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    filters: {pfiltro: 'su.codigo', type: 'string'},
                    form: true,
                    grid: true,
                    bottom_filter: true
                },*/
                {
                    config:{
                        name: 'id_estado_factura',
                        fieldLabel: 'Estado',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 140,
                        triggerAction: 'all',
                        lazyRender:true,
                        mode: 'local',
                        store: new Ext.data.ArrayStore({
                            id: 0,
                            fields: [
                                'id',
                                'display'
                            ],
                            data: [['5', 'FACTURA NO UTILIZADA']]
                        }),
                        valueField: 'id',
                        displayField: 'display',
                        renderer:function (value, p, record){
                            if (value == '5') {
                                return 'FACTURA NO UTILIZADA';
                            }
                        }
                    },
                    type:'ComboBox',
                    filters:{ type: 'list',
                        options: ['5']
                    },
                    id_grupo:1,
                    form: false,
                    grid: true,
                },
                {
                    config: {
                        name: 'tipo_cambio',
                        fieldLabel: 'Tipo de Cambio',
                        allowBlank: true,
                        width: 250,
                        maxLength: 100,
                        allowDecimals: true,
                        decimalPrecision: 15
                    },
                    type: 'NumberField',
                    valorInicial: 1,
                    id_grupo: 1,
                    form: true
                },
                {
                    config: {
                        name: 'id_moneda',
                        origen: 'MONEDA',
                        allowBlank: true,
                        //02-09-2019, se comenta poque se tiene que ver las demas monedas para los pagos
                        //baseParams: {id_moneda_defecto: me.id_moneda_defecto},
                        fieldLabel: 'Moneda',
                        gdisplayField: 'desc_moneda',
                        gwidth: 100,
                        width: 250
                    },
                    type: 'ComboRec',
                    id_grupo: 1,
                    form: true
                },

                {
                    config: {
                        name: 'nombre',
                        fieldLabel: 'Nombre',
                        allowBlank: true,
                        width: 250,
                        gwidth: 200,
                        maxLength: 200
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'fam.nombre', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'nit',
                        fieldLabel: 'NIT',
                        allowBlank: true,
                        width: 250,
                        gwidth: 120,
                        maxLength: 150
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'fam.nit', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true,
                    bottom_filter: true
                },
                {
                    config: {
                        name: 'observaciones',
                        fieldLabel: 'Observaciones',
                        allowBlank: true,
                        width: 250,
                        gwidth: 120,
                        maxLength: 150
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'fam.observaciones', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true,
                    bottom_filter: true
                },
                {
                    config: {
                        name: 'fecha_reg',
                        fieldLabel: 'Fecha creación',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y H:i:s') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'fam.fecha_reg', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'usr_reg',
                        fieldLabel: 'Creado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'Field',
                    filters: {pfiltro: 'usu1.cuenta', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'id_usuario_ai',
                        fieldLabel: 'Creado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'Field',
                    filters: {pfiltro: 'fam.id_usuario_ai', type: 'numeric'},
                    id_grupo: 1,
                    grid: false,
                    form: false
                },
                {
                    config: {
                        name: 'fecha_mod',
                        fieldLabel: 'Fecha Modif.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y H:i:s') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'fam.fecha_mod', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'usr_mod',
                        fieldLabel: 'Modificado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'Field',
                    filters: {pfiltro: 'usu2.cuenta', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'estado_reg',
                        fieldLabel: 'Estado Reg.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 10
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'fam.estado_reg', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'usuario_ai',
                        fieldLabel: 'Funcionaro AI',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 300
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'fam.usuario_ai', type: 'string'},
                    id_grupo: 1,
                    grid: false,
                    form: false
                }
            ],
            tam_pag:50,
            fheight:'40%',
            fwidth:'30%',
            ActSave: '../../sis_obingresos/control/FacturaNoUtilizada/insertarFacturaNoUtilizada',
            ActDel: '../../sis_obingresos/control/FacturaNoUtilizada/eliminarFacturaNoUtilizada',
            ActList: '../../sis_obingresos/control/FacturaNoUtilizada/listarFacturaNoUtilizada',
            id_store: 'id_factura_no_utilizada',
            fields: [
                {name: 'id_factura_no_utilizada', type: 'numeric'},
                //{name: 'id_lugar_pais', type: 'numeric'},
                //{name: 'id_lugar_depto', type: 'numeric'},
                {name: 'id_punto_venta', type: 'numeric'},
                {name: 'id_estado_factura', type: 'numeric'},
                //{name: 'fecha', type: 'date'},
                {name: 'tipo_cambio', type: 'numeric'},
                {name: 'id_moneda', type: 'numeric'},
                /* {name: 'nro_autorizacion', type: 'string'},
                 {name: 'nro_inicial', type: 'numeric'},
                 {name: 'nro_final', type: 'numeric'},*/
                {name: 'nombre', type: 'string'},
                {name: 'nit', type: 'string'},
                {name: 'observaciones', type: 'string'},
                {name: 'id_concepto_ingas', type: 'numeric'},

                {name: 'estado_reg', type: 'string'},
                {name: 'id_usuario_reg', type: 'numeric'},
                {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'id_usuario_mod', type: 'numeric'},
                {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'usr_reg', type: 'string'},
                {name: 'usr_mod', type: 'string'},

                //{name: 'lugar', type: 'string'},
                //{name: 'lugar_depto', type: 'string'},

                {name: 'nom_punto_venta', type: 'string'},
                //{name: 'nom_sucursal', type: 'string'},
                {name: 'codigo_estado', type: 'string'},

                {name:'id_dosificacion', type: 'numeric'},
                {name:'estacion', type: 'string'},
                {name:'tipo', type: 'string'},
                {name:'nro_tramite', type: 'string'},
                {name:'id_sucursal', type: 'numeric'},
                {name:'nom_sucursal', type: 'string'},
                {name:'nombre_sucursal', type: 'string'},
                {name: 'fecha', type: 'date',dateFormat:'Y-m-d'},
                {name:'fecha_dosificacion', type: 'date',dateFormat:'Y-m-d'},
                {name:'nroaut', type: 'string'},
                {name:'inicial', type: 'numeric'},
                {name:'final', type: 'numeric'},
                {name:'desc_moneda', type: 'string'}

            ],
            sortInfo:{
                field: 'id_factura_no_utilizada',
                direction: 'DESC'
            },

            bdel:true,
            bsave:false,
            bnew:true,
            bexcel:false,
            btest:false,
            bedit:false,

            cmbPuntoV: new Ext.form.ComboBox({
                name: 'punto_venta',
                id: 'id_punto_venta',
                fieldLabel: 'Punto Venta',
                allowBlank: true,
                emptyText:'Punto de Venta...',
                blankText: 'Año',
                store: new Ext.data.JsonStore({
                    url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
                    //url: '../../sis_obingresos/control/Boleto/obtenerPuntosVentasCounter',
                    id: 'id_punto_venta',
                    root: 'datos',
                    sortInfo: {
                        field: 'nombre',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_punto_venta', 'id_sucursal','nombre', 'codigo','habilitar_comisiones','formato_comprobante'],
                    remoteSort: true,
                    baseParams: {par_filtro: 'puve.nombre#puve.codigo', tipo_factura: this.tipo_factura}
                }),
                tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
                valueField: 'id_punto_venta',
                triggerAction: 'all',
                displayField: 'nombre',
                hiddenName: 'id_punto_venta',
                mode:'remote',
                pageSize:50,
                queryDelay:500,
                listWidth:'300',
                hidden:false,
                width:300
            }),

        }
    )
</script>
