<?php
/**
 * @package pXP
 * @file    RepCorrelativoFacturas.php
 * @author  Maylee Perez Pastor
 * @date    21/07/2020
 * @description Reporte Correlativo facturas
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.RepCorrelativoFacturas = Ext.extend(Phx.frmInterfaz, {
        Atributos: [
            {
                config: {
                    name: 'nivel',
                    fieldLabel: 'Nivel',
                    allowBlank: false,
                    emptyText: 'Nivel...',
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'local',
                    gwidth: 150,
                    disabled: true,
                    hidden: true,
                    store: ['sucursal', 'punto_venta']
                },
                type: 'ComboBox',
                id_grupo: 0,
                form: true
            },
            {
                config: {
                    name: 'id_lugar',
                    fieldLabel: 'Estación',
                    allowBlank: false,
                    emptyText: 'Elija un Punto...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_parametros/control/Lugar/listarLugar',
                        id: 'id_lugar',
                        root: 'datos',
                        sortInfo: {
                            field: 'nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_lugar', 'codigo'],
                        remoteSort: true,
                        baseParams: {tipo_usuario: 'todos', par_filtro: 'codigo', lugar_estacion: 'Bol', _adicionar: true}
                    }),
                    valueField: 'id_lugar',
                    displayField: 'codigo',
                    gdisplayField: 'codigo',
                    hiddenName: 'id_lugar',
                    tpl: '<tpl for="."><div class="x-combo-list-item"><p><b></b> {codigo}</p></div></tpl>',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    gwidth: 150,
                    width: 200,
                    resizable: true,
                    minChars: 2,
                    renderer: function (value, p, record) {
                        return String.format('{0}', record.data['codigo']);
                    },
                    hidden: false

                },
                type: 'ComboBox',
                id_grupo: 0,
                filters: {pfiltro: 'codigo', type: 'string'},
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'id_sucursal',
                    fieldLabel: 'Sucursal',
                    allowBlank: false,
                    emptyText: 'Elija una Sucursal...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_ventas_facturacion/control/Sucursal/listarSucursal',
                        id: 'id_sucursal',
                        root: 'datos',
                        sortInfo: {
                            field: 'nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_sucursal', 'nombre', 'codigo', 'id_lugar'],
                        remoteSort: true,
                        //baseParams: {tipo_usuario: 'todos', par_filtro: 'suc.nombre#suc.codigo#suc.id_lugar', _adicionar: true}
                        baseParams: {par_filtro: 'suc.nombre#suc.codigo#suc.id_lugar', _adicionar: true}
                    }),
                    valueField: 'id_sucursal',
                    gdisplayField: 'nombre_sucursal',
                    displayField: 'nombre',
                    hiddenName: 'id_sucursal',
                    //tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
                    tpl: new Ext.XTemplate([
                        '<tpl for=".">',
                        '<div class="x-combo-list-item">',
                        '<div class="awesomecombo-item {checked}">',
                        '<p><b>Código: {codigo}</b></p>',
                        '</div><p><b>Nombre: </b> <span style="color: green;">{nombre}</span></p>',
                        '</div></tpl>'
                    ]),
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    listWidth: 400,
                    mode: 'remote',
                    pageSize: 15,
                    width: 200,
                    queryDelay: 1000,
                    minChars: 2,
                    resizable: true,
                    hidden: false
                },
                type: 'ComboBox',
                id_grupo: 0,
                form: true
            },

            {
                config: {
                    name: 'id_punto_venta',
                    fieldLabel: 'Punto de Venta',
                    allowBlank: false,
                    emptyText: 'Elija un Punto...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
                        id: 'id_punto_venta',
                        root: 'datos',
                        sortInfo: {
                            field: 'nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_punto_venta', 'nombre', 'codigo', 'id_sucursal'],
                        remoteSort: true,
                        baseParams: {tipo_usuario: 'todos', par_filtro: 'puve.nombre#puve.codigo', _adicionar: true}
                    }),
                    valueField: 'id_punto_venta',
                    displayField: 'nombre',
                    gdisplayField: 'nombre_punto_venta',
                    hiddenName: 'id_punto_venta',
                    //tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
                    tpl: new Ext.XTemplate([
                        '<tpl for=".">',
                        '<div class="x-combo-list-item">',
                        '<div class="awesomecombo-item {checked}">',
                        '<p><b>Código: {codigo}</b></p>',
                        '</div><p><b>Nombre: </b> <span style="color: green;">{nombre}</span></p>',
                        '</div></tpl>'
                    ]),
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    gwidth: 150,
                    width: 200,
                    listWidth: 400,
                    resizable: true,
                    minChars: 2,
                    renderer: function (value, p, record) {
                        return String.format('{0}', record.data['nombre_punto_venta']);
                    },
                    hidden: false

                },
                type: 'ComboBox',
                id_grupo: 0,
                filters: {pfiltro: 'puve.nombre', type: 'string'},
                grid: true,
                form: true
            },

            {
                config: {
                    name: 'tipo_generacion',
                    fieldLabel: 'Tipo de Generación',
                    typeAhead: true,
                    allowBlank: false,
                    triggerAction: 'all',
                    emptyText: 'Tipo...',
                    selectOnFocus: true,
                    mode: 'local',
                    store: new Ext.data.ArrayStore({
                        fields: ['ID', 'valor'],
                        data: [['manual', 'Facturación Manual'],
                            ['computarizada', 'Facturación Computarizada'],
                            ['recibo', 'Recibo Oficial'],
                            ['mcos', 'MCOs']
                        ]
                    }),
                    valueField: 'ID',
                    displayField: 'valor',
                    width: 200,

                },
                type: 'ComboBox',
                id_grupo: 0,
                form: true
            },
            /*{
                config:{
                    name:'tipo_generacion',
                    fieldLabel:'Tipo de Generación',
                    allowBlank:true,
                    emptyText:'Tipo...',
                    triggerAction: 'all',
                    lazyRender:true,
                    gwidth: 150,
                    width:250,
                    mode: 'local',
                    store:['manual','computarizada']

                },
                type:'ComboBox',
                id_grupo:0,
                filters:{
                    type: 'list',
                    options: ['manual','computarizada'],
                },
                form:true
            },*/
            {
                config: {
                    name: 'fecha_desde',
                    fieldLabel: 'Fecha Desde',
                    allowBlank: false,
                    gwidth: 150,
                    width: 200,
                    format: 'd/m/Y'

                },
                type: 'DateField',
                id_grupo: 0,
                form: true
            },
            {
                config: {
                    name: 'fecha_hasta',
                    fieldLabel: 'Fecha Hasta',
                    allowBlank: false,
                    gwidth: 150,
                    width: 200,
                    format: 'd/m/Y'

                },
                type: 'DateField',
                id_grupo: 0,
                form: true
            }
        ],
        title: 'Generar Reporte',
        ActSave: '../../sis_obingresos/control/ReporteCorrelativoFacturas/reporteCorrelativoFacturas',
        topBar: true,
        botones: false,
        labelSubmit: 'Imprimir',
        tooltipSubmit: '<b>Generar Reporte</b>',
        constructor: function (config) {
            Phx.vista.RepCorrelativoFacturas.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
            //console.log("llega auqi el combo",this.Cmp.nivel);

            this.Cmp.id_sucursal.reset();
            this.Cmp.id_punto_venta.reset();

            this.getComponente('id_lugar').on('select', function (cmp, rec, indice) {
                console.log('llegaid_sucursalcmp', cmp)
                console.log('llegaid_sucursalrrec', rec)
                this.Cmp.id_sucursal.reset();
                this.Cmp.id_punto_venta.reset();
                this.Cmp.id_sucursal.store.baseParams.id_lugar = rec.data.id_lugar;
                this.Cmp.id_sucursal.modificado = true;
            }, this);

            this.getComponente('id_sucursal').on('select', function (cmp, rec, indice) {
                this.Cmp.id_punto_venta.reset();
                this.Cmp.id_punto_venta.store.baseParams.id_sucursal = rec.data.id_sucursal;
                this.Cmp.id_punto_venta.modificado = true;
            }, this);


            /*       this.Cmp.nivel.setValue('punto_venta');
                   this.Cmp.nivel.fireEvent('select', this.Cmp.nivel,'punto_venta',0);
                   this.Cmp.nivel.on('select',function(a,b,c) {
                       if (b.data.field1 == 'sucursal') {
                           this.Cmp.id_punto_venta.reset();
                           this.Cmp.id_punto_venta.allowBlank = true;
                           //this.ocultarComponente(this.Cmp.id_punto_venta);
                           this.Cmp.id_sucursal.allowBlank = false;
                           //this.mostrarComponente(this.Cmp.id_sucursal);
                       } else {
                           this.Cmp.id_sucursal.reset();
                           this.Cmp.id_sucursal.allowBlank = true;
                           //this.ocultarComponente(this.Cmp.id_sucursal);
                           this.Cmp.id_punto_venta.allowBlank = false;
                           this.mostrarComponente(this.Cmp.id_punto_venta);
                       }
                   },this);

                   this.Cmp.fecha_desde.on('valid',function(){
                       this.Cmp.fecha_hasta.setMinValue(this.Cmp.fecha_desde.getValue());
                   },this);

                   this.Cmp.fecha_hasta.on('valid',function(){
                       this.Cmp.fecha_desde.setMaxValue(this.Cmp.fecha_hasta.getValue());
                   },this);
       */

        },

        tipo: 'reporte',
        clsSubmit: 'bprint',
        /*agregarArgsExtraSubmit: function() {
            //this.argumentExtraSubmit.sucursal = this.Cmp.id_sucursal.getRawValue();
            this.argumentExtraSubmit.punto_venta = this.Cmp.id_punto_venta.getRawValue();

        },*/
    })
</script>
