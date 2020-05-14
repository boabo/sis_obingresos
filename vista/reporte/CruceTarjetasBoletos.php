<?php
/**
 *@package BoA
 *@file    CruceTarjetasBoletos.php
 *@author  franklin.espinoza
 *@date    11/04/2020
 *@description Reporte Cruce Tarjetas
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.CruceTarjetasBoletos = Ext.extend(Phx.frmInterfaz, {
        Atributos : [
            {
                config : {
                    name : 'tipo_reporte',
                    fieldLabel : 'Fuente Pago',
                    allowBlank : false,
                    emptyText:'Fuente Pago.............',
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'local',
                    store : new Ext.data.ArrayStore({
                        fields : ['tipo', 'valor'],
                        data : [
                            ['pago_atc', 'Pagos ATC'],
                            ['pago_linkser', 'Pagos LINKSER']

                        ]
                    }),
                    width:250,
                    valueField : 'tipo',
                    displayField : 'valor',
                    msgTarget: 'side'
                },
                type : 'ComboBox',
                id_grupo : 0,
                form : true
            },
            {
                config: {
                    name: 'id_punto_venta',
                    fieldLabel: 'Punto de Venta',
                    allowBlank: false,
                    emptyText: 'Elija un Punto de Venta..............',
                    store: new Ext.data.JsonStore({
                        //url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
                        url: '../../sis_obingresos/control/Reportes/listarAgencias',
                        id: 'id_punto_venta',
                        root: 'datos',
                        sortInfo: {
                            field: 'nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_punto_venta', 'nombre', 'codigo', 'office_id'],
                        remoteSort: true,
                        baseParams: {tipo_usuario : 'todos',par_filtro: 'puve.nombre#puve.codigo#tag.codigo_int', _adicionar: true}
                    }),
                    valueField: 'office_id',
                    displayField: 'nombre',
                    gdisplayField: 'nombre_punto_venta',
                    hiddenName: 'id_punto_venta',
                    //tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    gwidth: 150,
                    width:250,
                    resizable:true,
                    minChars: 2,
                    msgTarget: 'side',
                    tpl: new Ext.XTemplate([
                        '<tpl for=".">',
                        '<div class="x-combo-list-item">',
                        '<div class="awesomecombo-item {checked}">',
                        '<p><b>Código: {codigo} - {office_id}</b></p>',
                        '</div><p><b>Nombre: </b> <span style="color: green;">{nombre}</span></p>',
                        '</div></tpl>'
                    ]),
                    renderer : function(value, p, record) {
                        return String.format('{0}', record.data['nombre_punto_venta']);
                    },
                    hidden : false,
                    enableMultiSelect: true

                },
                type: 'AwesomeCombo',
                id_grupo: 0,
                filters: {pfiltro: 'puve.nombre',type: 'string'},
                grid: true,
                form: true
            },
            {
                config:{
                    name: 'fecha_desde',
                    fieldLabel: 'Fecha Desde',
                    allowBlank: false,
                    format: 'd/m/Y',
                    msgTarget: 'side',
                    width : 179

                },
                type:'DateField',
                id_grupo:0,
                form:true
            },
            {
                config:{
                    name: 'fecha_hasta',
                    fieldLabel: 'Fecha Hasta',
                    allowBlank: false,
                    format: 'd/m/Y',
                    msgTarget: 'side',
                    width : 179

                },
                type:'DateField',
                id_grupo:0,
                form:true
            }
        ],
        title : 'Generar Reporte',
        ActSave : '../../sis_obingresos/control/Reportes/generarCruceTarjetasBoletos',
        topBar : true,
        botones : false,
        labelSubmit : 'Generar Cruce',
        tooltipSubmit : '<b>Generar Reporte</b>',
        constructor : function(config) {
            Phx.vista.CruceTarjetasBoletos.superclass.constructor.call(this, config);

            this.init();

            this.Cmp.fecha_desde.on('valid',function(){
                //this.Cmp.fecha_hasta.setMinValue(this.Cmp.fecha_desde.getValue());
                this.Cmp.fecha_hasta.setMinValue(this.Cmp.fecha_desde.getValue());
            },this);

            /*this.Cmp.fecha_hasta.on('valid',function(){
                //this.Cmp.fecha_desde.setMaxValue(this.Cmp.fecha_hasta.getValue());
                this.Cmp.fecha_hasta.setMinValue(this.Cmp.fecha_desde.getValue());
            },this);*/

            this.Cmp.id_punto_venta.store.load({params:{start:0, limit:50}, scope:this, callback: function (param,op,suc) {
                    this.Cmp.id_punto_venta.checkRecord(param[0]);
                    this.Cmp.id_punto_venta.collapse();
                    this.Cmp.tipo_reporte.focus(false,  5);
                }});


        },

        tipo : 'reporte',
        clsSubmit : 'bprint',
        agregarArgsExtraSubmit: function() {
            //this.argumentExtraSubmit.sucursal = this.Cmp.id_sucursal.getRawValue();
            this.argumentExtraSubmit.punto_venta = this.Cmp.id_punto_venta.getRawValue();

        },
    })
</script>