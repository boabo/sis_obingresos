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
                            ['pago_linkser', 'Pagos LINKSER'],
                            ['pago_tigo', 'Pagos TIGO']

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
                    width : 179,
                    editable: false

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
                    width : 179,
                    editable: false

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

            this.addButton('btnObs',{
                grupo:[0,1,2],
                text :'Archivo Plano(ATC,LINKSER)',
                iconCls : 'bchecklist',
                disabled: false,
                handler : this.consultaArchivoPlano,
                tooltip : '<b>Archivo Plano</b><br/><b>Lista de pagos Administradora</b>'
            });

            this.addButton('btnReporte',{
                grupo:[0,1,2],
                text :'Archivo Generado',
                iconCls : 'bexcel',
                disabled: false,
                handler : this.consultaArchivoGenerado,
                tooltip : '<b>Archivo Generado</b><br/><b>Lista de Archivos Generados</b>'
            });

            this.init();
            this.current_date = new Date();
            this.diasMes = [31, new Date(this.current_date.getFullYear(), 2, 0).getDate(), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
            this.Cmp.fecha_desde.on('select', function (rec, date) {
                let fecha_max = new Date(date.getFullYear() ,date.getMonth(), this.diasMes[date.getMonth()])
                this.Cmp.fecha_hasta.setMaxValue(fecha_max);
                this.Cmp.fecha_hasta.setMinValue(fecha_max);
            },this);

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

        consultaArchivoPlano: function (){

            var record = {fecha_desde: this.Cmp.fecha_desde.getValue(), fecha_hasta: this.Cmp.fecha_hasta.getValue()};
            if ( this.Cmp.fecha_desde.getValue() != '' && this.Cmp.fecha_hasta.getValue() ) {
                var rec = {maestro: record};
                Phx.CP.loadWindows('../../../sis_obingresos/vista/reporte/DetallePagosAdministradora.php',
                    'Detalle Pagos Administradora',
                    {
                        width: 1200,
                        height: 500
                    },
                    rec,
                    this.idContenedor,
                    'DetallePagosAdministradora'
                );
            }

        },

        consultaArchivoGenerado: function (){
            console.log('consultaArchivoGenerado');
            var record = {fecha_desde: this.Cmp.fecha_desde.getValue(), fecha_hasta: this.Cmp.fecha_hasta.getValue()};
            if ( this.Cmp.fecha_desde.getValue() != '' && this.Cmp.fecha_hasta.getValue() ) {
                var rec = {maestro: record};
                Phx.CP.loadWindows('../../../sis_obingresos/vista/reporte/ListaDocumentoGenerado.php',
                    'Documentos Generados (xls)',
                    {
                        width: 1200,
                        height: 500
                    },
                    rec,
                    this.idContenedor,
                    'ListaDocumentoGenerado'
                );
            }

        },

        tipo : 'reporte',
        clsSubmit : 'bprint',
        agregarArgsExtraSubmit: function() {
            //this.argumentExtraSubmit.sucursal = this.Cmp.id_sucursal.getRawValue();
            this.argumentExtraSubmit.punto_venta = this.Cmp.id_punto_venta.getRawValue();

        },
        successSave :function(resp){
            Phx.CP.loadingHide();

            Ext.Msg.show({
                title: 'Información',
                msg: '<b>Estimado Funcionario: ' + '\n' + ' El Reporte se esta Generando, despues de un momento consulte en la opción del menú <span style="color: red;">Archivo Generado</span>.......</b>',
                buttons: Ext.Msg.OK,
                width: 512,
                icon: Ext.Msg.INFO
            });

        }
    })
</script>