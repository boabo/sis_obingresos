<?php
/**
 *@package pXP
 *@file    FormFacturaNoUtilizada.php
 *@author  Maylee Perez Pastor
 *@date    20/05/2020
 *@description permites subir archivos a la tabla de documento_sol
 */
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
    Phx.vista.FormFacturaNoUtilizada=Ext.extend(Phx.frmInterfaz,{
        ActSave:'../../sis_obingresos/control/FacturaNoUtilizada/insertarFacturaNoUtilizada',
        tam_pag: 10,
        layout: 'fit',
        tabEnter: true,
        autoScroll: false,
        breset: true,
        bsubmit:true,
        //storeFormaPago : false,
        fwidth : '9%',
        labelSubmit: '<i class="fa fa-check"></i> Guardar',
        cantidadAllowDecimals: false,

        Grupos: [
            {
                layout: 'column',
                border: false,
                defaults: {
                    border: false
                },
                items: [
                    {
                        bodyStyle: 'padding-right:5px;',
                        items: [{
                            xtype: 'fieldset',
                            title: 'Datos Dosificación',
                            autoHeight: true,
                            items: [],
                            id_grupo: 0
                        }]
                    },
                    {
                        bodyStyle: 'padding-right:5px;',
                        items: [{
                            xtype: 'fieldset',
                            title: 'Datos Factura Anulada',
                            autoHeight: true,
                            items: [],
                            id_grupo: 1
                        }]
                    }
                ]
            }
        ],

        constructor:function(config)
        {
            Ext.apply(this,config);
            //this.data.objPadre.tipo_factura = 'manual';
            //console.log('llegaconstructor', this.data.objPadre.variables_globales.vef_tiene_punto_venta)



            this.addEvents('beforesave');
            this.addEvents('successsave');


            //this.labelReset = '<div style = "font-size:25px; font-weight:bold; color:#0435FF; text-shadow: -1px -1px 1px rgba(255,255,255,.1), 1px 1px 1px rgba(0,0,0,.5);"><i style="color:#00AC0D;" class="fa fa-check-circle"></i> Generar</div>';
            this.labelSubmit = '<div style = "font-size:20px; font-weight:bold; color:#0435FF; text-shadow: -1px -1px 1px rgba(255,255,255,.1), 1px 1px 1px rgba(0,0,0,.5);"><i style="color:#00AC0D;" class="fa fa-check-circle"></i>Guardar</div>';
            Phx.vista.FormFacturaNoUtilizada.superclass.constructor.call(this,config);

            /****Obtenemos el tipo de cambio****/
            this.tipo_cambio = 0;

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
                    this.tipo_cambio = reg.ROOT.datos.v_tipo_cambio;
                    this.moneda_base = reg.ROOT.datos.v_codigo_moneda;
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
            /*************fin tipo de cambio*****************/


            this.init();
            this.iniciarEventos();

            if(this.data.tipo_form == 'new'){
                this.onNew();
                //this.onEdit();
            }else{
                this.onEdit();
            }

            /*if(this.data.readOnly===true){
                for(var index in this.Cmp) {
                    if( this.Cmp[index].setReadOnly){
                        this.Cmp[index].setReadOnly(true);
                    }
                }

                if (this.data.objPadre.mycls == 'VentaCaja'){
                    this.readOnlyGroup(2,false);
                    this.blockGroup(0);
                }

                this.megrid.getTopToolbar().disable();

            }*/

            if (this.data.objPadre.variables_globales.vef_tiene_punto_venta === 'true') {
                this.Cmp.id_sucursal.allowBlank = true;
                this.Cmp.id_sucursal.setDisabled(true);
            }

        },





        iniciarEventos : function () {
            //this.Cmp.cambio.setValue(0);
            //this.Cmp.cambio_moneda_extranjera.setValue(0);
            /*Filtramos la lista de paquetes por la sucursal seleccionada*/
            //this.Cmp.id_formula.store.baseParams.tipo_punto_venta = this.variables_globales.tipo_pv;
            //this.Cmp.id_formula.store.baseParams.id_punto_venta = this.data.objPadre.variables_globales.id_punto_venta;
            /*************************************************************/
            //console.log('llega padre punto venta', this.data.objPadre.variables_globales.id_punto_venta)
            this.Cmp.id_sucursal.store.load({params:{start:0,limit:50},
                callback : function (r) {

                    this.Cmp.id_sucursal.setValue(this.data.objPadre.variables_globales.id_sucursal);
                    if (this.data.objPadre.variables_globales.vef_tiene_punto_venta != 'true') {
                        //console.log("lelga aqui comprobante",r)
                        //this.detCmp.id_producto.store.baseParams.id_sucursal = this.Cmp.id_sucursal.getValue();
                    }


                    this.Cmp.id_sucursal.fireEvent('select',this.Cmp.id_sucursal, this.Cmp.id_sucursal.store.getById(this.data.objPadre.variables_globales.id_sucursal));

                    }, scope : this
            });



            /******************************************************/
            //console.log('llega padre punto venta 2',this.data.objPadre.variables_globales.vef_tiene_punto_venta)
            if (this.data.objPadre.variables_globales.vef_tiene_punto_venta === 'true') {

                /*******************cambiaremos el estilo del boton guardar *********************/
                //if (this.data.tipo_form == 'new'){
                    /*this.megrid.topToolbar.items.items[0].container.dom.style.width="75px";
                    this.megrid.topToolbar.items.items[0].container.dom.style.height="35px";
                    this.megrid.topToolbar.items.items[0].btnEl.dom.style.height="35px";

                    this.megrid.topToolbar.el.dom.style.background="#89CBE0";
                    this.megrid.topToolbar.el.dom.style.borderRadius="2px";
                    this.megrid.body.dom.childNodes[0].firstChild.children[0].firstChild.style.background='#FFF4EB';*/
               // }

                /********************************************************************************/

                this.Cmp.id_punto_venta.store.baseParams.id_punto_venta = this.data.objPadre.variables_globales.id_punto_venta;
                this.Cmp.id_punto_venta.store.load({params:{start:0,limit:this.tam_pag},
                    callback : function (r) {
                        this.Cmp.id_punto_venta.setValue(this.data.objPadre.variables_globales.id_punto_venta);
                        //this.detCmp.id_producto.store.baseParams.id_punto_venta = this.Cmp.id_punto_venta.getValue();

                        //console.log('llega id_sucursal 444', this.Cmp.id_sucursal.getValue())
                        this.Cmp.id_dosificacion.store.baseParams.id_sucursal= this.Cmp.id_sucursal.getValue();

                        this.Cmp.id_punto_venta.fireEvent('select',this.Cmp.id_punto_venta, this.Cmp.id_punto_venta.store.getById(this.data.objPadre.variables_globales.id_punto_venta));

                    }, scope : this
                });
            }

            //dosificacion
            //this.Cmp.id_centro_costo.store.baseParams.id_depto = this.maestro.id_depto;

            // fecha factura no utilizada
            var fecha = new Date();
            this.Cmp.fecha.setValue(fecha);

            // tipo de cambio
            //console.log('llega tipo de cambio', this.getTipoCambio('si') );
            this.Cmp.id_moneda.on('select', function () {
                this.getTipoCambio('si');
            }, this);
            this.Cmp.fecha.on('select', function (value, date) {
                this.getTipoCambio('si');
            }, this);

            //observaciones
            this.Cmp.observaciones.setValue('TALONARIO NO UTILIZADO');

            //nombre
            this.Cmp.nombre.setValue('NO UTILIZADA');

            //fecha
            //console.log('llega fechaaa', this.getComponente('fecha') );
            this.cmpFecha = this.getComponente('fecha');


            //completa datos de la dosificacion
            this.Cmp.id_dosificacion.on('select', function (cmb, rec, i) {
                console.log('llega rec',rec )

                this.Cmp.nro_tramite.setValue(rec.data.nro_tramite);
                this.Cmp.fecha_dosificacion.setValue(rec.data.fecha_dosificacion);
                this.Cmp.nroaut.setValue(rec.data.nroaut);
                //this.Cmp.inicial.setValue(rec.data.inicial);
                this.Cmp.final.setValue(rec.data.final);
                this.Cmp.tipo.setValue(rec.data.tipo);
                this.getNroFacInicial('si');

                //console.log('llega id_moneda get',this.Cmp.id_moneda.getValue() )
                //this.Cmp.id_moneda.setValue('BOB');

                /* id_moneda por defecto BOB*/
                //this.Cmp.id_moneda.setValue('BOB');
                //this.Cmp.id_moneda.fireEvent('select', this.Cmp.id_moneda,1,0);


            }, this);

            //moneda
            this.Cmp.id_moneda.store.load({params:{start:0,limit:50},
                callback : function (r) {
                    if (r.length == 1 ) {
                        this.Cmp.id_moneda.setValue(r[0].data.id_moneda);
                        this.Cmp.id_moneda.fireEvent('select', this.Cmp.id_moneda,r[0],0);
                    }
                    this.Cmp.id_moneda.fireEvent('select', this.Cmp.id_moneda,this.Cmp.id_moneda.store.getById(this.Cmp.id_moneda.getValue()),0);
                    this.Cmp.id_moneda.store.baseParams.filtrar_base = 'no';

                }, scope : this
            });


           // this.moneda_2 = r.data.desc_moneda;
          //  this.Cmp.moneda_tarjeta_2.setValue(this.moneda_2);

            /*
            if (this.data.objPadre.tipo_factura == 'manual') {
                    this.Cmp.id_dosificacion.reset();
                    this.Cmp.id_dosificacion.store.baseParams.id_sucursal = this.Cmp.id_sucursal.getValue();
                    this.Cmp.id_dosificacion.modificado = true;
                }*/







           /* this.Cmp.fecha.on('blur',function(c) {


                    this.Cmp.id_dosificacion.reset();
                    this.Cmp.id_dosificacion.modificado = true;
                    this.Cmp.id_dosificacion.setDisabled(false);
                    this.Cmp.id_dosificacion.store.baseParams.fecha = this.Cmp.fecha.getValue().format('d/m/Y');
                    this.Cmp.id_dosificacion.store.baseParams.tipo = 'manual';

            },this);
            */


        },


        //this.tipo_cambio



        successWizard:function(resp){
            // var rec=this.sm.getSelected();
            var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            console.log("llega aqui pasar estado",this.tipo_factura);
            if (objRes.ROOT.datos.estado == 'finalizado' && this.tipo_factura != 'manual') {
                this.id_venta = objRes.ROOT.datos.id_venta;
                //this.imprimirNota();
            }
            Phx.CP.loadingHide();
            resp.argument.wizard.panel.destroy();
            this.panel.destroy();
            this.reload();
        },

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
           /* {
                config:{
                    name: 'estacion',
                    fieldLabel: 'Estación',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 100,
                    maxLength:150,
                    readOnly: true
                },
                type:'TextField',
                filters:{pfiltro:'lu.codigo',type:'string'},
                id_grupo:0,
                grid:false,
                form:true
            },*/
            {
                config: {
                    name: 'id_punto_venta',
                    fieldLabel: 'Punto de Venta',
                    allowBlank: true,
                    emptyText: '...',
                    readOnly: true,
                    style: {
                        background: '#DAE7E7',
                        color: 'black',
                        fontWeight:'bold'
                    },
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
                id_grupo: 0,
                filters: {pfiltro: 'su.nombre', type: 'string'},
                form: true,
                grid: true,
                bottom_filter: true
            },
            {
                config: {
                    name: 'id_sucursal',
                    fieldLabel: 'Sucursal',
                    allowBlank: true,
                    emptyText: 'Elija una Suc...',
                    readOnly: true,
                    style: {
                        background: '#DAE7E7',
                        fontWeight:'bold'
                    },
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
                config: {
                    name: 'id_dosificacion',
                    fieldLabel: 'Dosificación',
                    allowBlank: false,
                    emptyText: 'Elija un Dosi...',
                    store: new Ext.data.JsonStore({
                        //url: '../../sis_ventas_facturacion/control/Dosificacion/listarDosificacion',
                        url: '../../sis_ventas_facturacion/control/Dosificacion/listarDosificacionInte',
                        id: 'id_dosificacion',
                        root: 'datos',
                        sortInfo: {
                            field: 'nroaut',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_dosificacion', 'nroaut', 'desc_actividad_economica','inicial','final','fecha_dosificacion', 'nro_tramite','tipo', 'tipo_generacion', 'id_sucursal','fecha_limite'],
                        remoteSort: true,
                        baseParams: {filtro_usuario: 'si',par_filtro: 'dos.nroaut', tipo_generacion : 'manual', controlfecha: 'si'}
                    }),
                    valueField: 'id_dosificacion',
                    displayField: 'desc_actividad_economica',
                    hiddenName: 'id_dosificacion',
                    tpl: new Ext.XTemplate([
                        '<tpl for=".">',
                        '<div class="x-combo-list-item">',
                        '<p><b>N° Autorización:</b><span style="color: green; font-weight:bold;"> {nroaut}</span></p></p>',
                        '<p><b>Actividad Económica:</b> <span style="color: blue; font-weight:bold;">{desc_actividad_economica}</span></p>',
                        '<p><b>N° Inicial:</b> <span style="color: #D35000; font-weight:bold;">{inicial}</span></p>',
                        '<p><b>N° Final:</b> <span style="color: red; font-weight:bold;">{final}</span></p>',
                        '<p><b>Fecha Vencimiento:</b> <span type="date" style="color: red; font-weight:bold;" required pattern="[0-9]{2}-[0-9]{2}-[0-9]{4}" >{fecha_limite}</span></p>',
                        '</div></tpl>'
                    ]),
                    //tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Autorizacion:</b> {nroaut}</p><p><b>Actividad:</b> {desc_actividad_economica}</p></p><p><b>No Inicio:</b> {inicial}</p><p><b>No Final:</b> {final}</p></div></tpl>',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    resizable:true,
                    pageSize: 15,
                    queryDelay: 1000,
                    width: 250,
                    gwidth: 150,
                    minChars: 2,
                    listWidth:'550'
                },
                type: 'ComboBox',
                id_grupo: 0,
                grid: false,
                form: true
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
                    style: {
                        background: '#DAE7E7'
                    },
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
                grid:false,
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
                    style: {
                        background: '#DAE7E7'
                    },
                    readOnly: true
                },
                type:'NumberField',
                filters:{pfiltro:'dos.nro_tramite',type:'numeric'},
                id_grupo:0,
                grid:false,
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
                    style: {
                        background: '#DAE7E7'
                    },
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'dos.fecha_dosificacion',type:'date'},
                id_grupo:0,
                grid:false,
                form:true
            },
            {
                config:{
                    name: 'nroaut',
                    fieldLabel: 'No Autorización',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 120,
                    style: {
                        background: '#DAE7E7'
                    },
                    maxLength:150,
                    readOnly: true
                },
                type:'TextField',
                filters:{pfiltro:'dos.nroaut',type:'string'},
                id_grupo:0,
                grid:false,
                form:true,
                bottom_filter:true
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
                form: false,
                grid: true,
                bottom_filter: true
            },*/
            {
                config:{
                    name: 'id_estado_factura',
                    fieldLabel: 'Estado',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 120,
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
                id_grupo:0,
                form: false,
                grid: true,
            },

            {
                config:{
                    name: 'fecha',
                    fieldLabel: 'Fecha',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 100,
                    maxValue: new Date(),
                    style: {
                        background: '#EFFFD6'
                    },
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'dos.fecha',type:'date'},
                id_grupo:1,
                grid:false,
                form:true
            },
            {
                config: {
                    name: 'id_moneda',
                    fieldLabel: 'Moneda',
                    allowBlank: false,
                    anchor: '100%',
                    //width:150,
                    listWidth:250,
                    resizable:true,
                    style: {
                        background: '#EFFFD6',
                        color: 'red',
                        fontWeight:'bold'
                    },
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
                        baseParams: {filtrar: 'si',filtrar_base: 'si' , par_filtro: 'moneda.moneda#moneda.codigo#moneda.codigo_internacional'}
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
                //valorInicial: 'BOB',
                form: true
            },

            {
                config: {
                    name: 'tipo_cambio',
                    fieldLabel: 'Tipo de Cambio',
                    allowBlank: true,
                    width: 250,
                    maxLength: 100,
                    allowDecimals: true,
                    style: {
                        background: '#EFFFD6'
                    },
                    decimalPrecision: 15
                },
                type: 'NumberField',
                //valorInicial: 1,
                id_grupo: 1,
                form: true
            },
            /*{
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
            },*/

            {
                config: {
                    name: 'nombre',
                    fieldLabel: 'Nombre',
                    allowBlank: true,
                    width: 250,
                    gwidth: 200,
                    readOnly: true,
                    style: {
                        background: '#EFFFD6'
                    },
                    maxLength: 200
                },
                type: 'TextField',
                filters: {pfiltro: 'fam.nombre', type: 'string'},
                id_grupo: 1,
                grid: false,
                form: true
            },
            {
                config: {
                    name: 'nit',
                    fieldLabel: 'NIT',
                    allowBlank: true,
                    width: 250,
                    gwidth: 120,
                    readOnly: true,
                    style: {
                        background: '#EFFFD6'
                    },
                    maxLength: 150
                },
                type: 'TextField',
                filters: {pfiltro: 'fam.nit', type: 'string'},
                valorInicial : 0,
                id_grupo: 1,
                grid: false,
                form: true,
                bottom_filter: true
            },
            {
                config:{
                    name: 'inicial',
                    fieldLabel: 'Nro. Fac. Inicial',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 100,
                    maxLength:150,
                    style: {
                        background: '#EFFFD6'
                    },
                    allowDecimals:false,
                    allowNegative:false
                },
                type:'TextField',
                filters:{pfiltro:'dos.inicial',type:'numeric'},
                id_grupo:1,
                grid:false,
                form:true
            },
            {
                config:{
                    name: 'final',
                    fieldLabel: 'Nro. Fac. Final',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 100,
                    maxLength:150,
                    //readOnly: true,
                    style: {
                        background: '#DAE7E7'
                    },
                    allowDecimals:false,
                    allowNegative:false
                },
                type:'NumberField',
                filters:{pfiltro:'dos.final',type:'numeric'},
                id_grupo:1,
                grid:false,
                form:true
            },
            {
                config: {
                    name: 'observaciones',
                    fieldLabel: 'Observaciones',
                    allowBlank: true,
                    width: 250,
                    gwidth: 120,
                    style: {
                        background: '#EFFFD6'
                    },
                    maxLength: 100
                },
                type: 'TextArea',
                filters: {pfiltro: 'fam.observaciones', type: 'string'},
                id_grupo: 1,
                grid: false,
                form: true,
                bottom_filter: true
            }


        ],
        title: 'Formulario Facturas No Utilizadas',
        onEdit:function(){
            this.accionFormulario = 'EDIT';
            this.loadForm(this.data.datos_originales);
            this.mestore.baseParams.id_venta = this.Cmp.id_venta.getValue();
            this.Cmp.id_forma_pago.store.baseParams.defecto = 'si';
            this.mestore.load();
            this.Cmp.id_forma_pago.reset();
            this.iniciarEventos();

        },
        onNew: function(){
            this.accionFormulario = 'NEW';
        },

        onSubmit: function(o) {
            //  validar formularios
            console.log("que es esto",o);
            /*var arra = [], i, me = this;
            var formapa = [];
            for (i = 0; i < me.megrid.store.getCount(); i++) {
                var record = me.megrid.store.getAt(i);
                arra[i] = record.data;
            }
            if (me.storeFormaPago) {
                for (i = 0; i < me.storeFormaPago.getCount(); i++) {
                    var record = me.storeFormaPago.getAt(i);
                    formapa[i] = record.data;
                }
            }*/
          /*  me.argumentExtraSubmit = { 'json_new_records': JSON.stringify(arra,
                    function replacer(key, value) {
                        if (typeof value === 'string') {
                            return String(value).replace(/&/g, "%26")
                        }
                        return value;
                    }),
                'formas_pago' :  JSON.stringify(formapa,
                    function replacer(key, value) {
                        if (typeof value === 'string') {
                            return String(value).replace(/&/g, "%26")
                        }
                        return value;
                    }),
                'tipo_factura':this.data.objPadre.tipo_factura};*/

            //  if( i > 0 &&  !this.editorDetail.isVisible()){
            Phx.vista.FormFacturaNoUtilizada.superclass.onSubmit.call(this,o);
            // }
            // else{
            //     alert('La venta no tiene registrado ningun detalle');
            //     console.log("llega aqui falla",this);
            // }
        },

        successSave:function(resp)
        {
            var datos_respuesta = JSON.parse(resp.responseText);
            Phx.CP.loadingHide();
            /*if (this.generar == 'generar') {
                //Phx.CP.loadingShow();
                var d = datos_respuesta.ROOT.datos;
                console.log("datos respuesta es",d);
                console.log("datos respuesta2 es",this);
                Ext.Ajax.request({
                    url:'../../sis_ventas_facturacion/control/Cajero/finalizarFacturaManual',
                    params:{id_estado_wf_act:d.id_estado_wf,
                        id_proceso_wf_act:d.id_proceso_wf,
                        tipo:'manual'},
                    success:this.successWizard,
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });
            }*/

            Phx.CP.getPagina(this.idContenedorPadre).reload();
            this.panel.close();
        },

        getTipoCambio : function() {
            //Verifica que la fecha y la moneda hayan sido elegidos
            if (this.Cmp.fecha.getValue() && this.Cmp.id_moneda.getValue()) {
                Ext.Ajax.request({
                    url : '../../sis_parametros/control/TipoCambio/obtenerTipoCambio',
                    params : {
                        fecha : this.Cmp.fecha.getValue(),
                        id_moneda : this.Cmp.id_moneda.getValue(),
                        tipo : 'O'
                    },
                    success : function(resp) {
                        Phx.CP.loadingHide();
                        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                        if (reg.ROOT.error) {
                            Ext.Msg.alert('Error', 'Validación no realizada: ' + reg.ROOT.error)
                        } else {
                            console.log('llega tipo de cambio22', reg.ROOT.datos.tipo_cambio );
                            this.Cmp.tipo_cambio.setValue(reg.ROOT.datos.tipo_cambio);
                        }
                    },
                    failure : this.conexionFailure,
                    timeout : this.timeout,
                    scope : this
                });
            }

        },
        getNroFacInicial : function() {
            //Verifica que la fecha y la moneda hayan sido elegidos
            if (this.Cmp.id_dosificacion.getValue()) {
                Ext.Ajax.request({
                    url : '../../sis_obingresos/control/FacturaNoUtilizada/obtenerNroFacInicial',
                    params : {
                        id_dosificacion : this.Cmp.id_dosificacion.getValue(),
                        final : this.Cmp.final.getValue()
                    },
                    success : function(resp) {
                        Phx.CP.loadingHide();
                        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                        if (reg.ROOT.error) {
                            Ext.Msg.alert('Error', 'Validación no realizada: ' + reg.ROOT.error)
                        } else {
                            console.log('llega ntofac inicial', reg.ROOT.datos.inicial );
                            this.Cmp.inicial.setValue(reg.ROOT.datos.inicial);
                        }
                    },
                    failure : this.conexionFailure,
                    timeout : this.timeout,
                    scope : this
                });
            }

        },

    })
</script>
