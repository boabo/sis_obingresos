<?php
/**
 *@package pXP
 *@file    FormFiltroBoletos.php
 *@author  Gonzalo Sarmiento Sejas
 *@date    10-09-2017
 *@description permite filtrar boletos por Oficina y por fecha
 */
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
    Phx.vista.FormFiltroBoletos=Ext.extend(Phx.frmInterfaz,{
        constructor:function(config)
        {
            this.panelResumen = new Ext.Panel({html:''});
            this.Grupos = [{

                xtype: 'fieldset',
                border: false,
                autoScroll: true,
                layout: 'form',
                items: [],
                id_grupo: 0

            },
                this.panelResumen
            ];

            Phx.vista.FormFiltroBoletos.superclass.constructor.call(this,config);
            this.init();
            this.iniciarEventos();
        },

        Atributos:[
            {
                config:{
                    name:'id_punto_venta',
                    fieldLabel:'Punto de Venta',
                    allowBlank:true,
                    emptyText:'Punto de Venta...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
                        id: 'id_punto_venta',
                        root: 'datos',
                        sortInfo:{
                            field: 'nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_punto_venta','nombre','codigo'],
                        // turn on remote sorting
                        remoteSort: true,
                        baseParams:{tipo_usuario: 'administrador',par_filtro:'puve.nombre#puve.codigo'}
                    }),
                    valueField: 'id_punto_venta',
                    displayField: 'nombre',
                    tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p><p><b>IATA:</b> {codigo}</p><p><b>OfficeID:</b>: {codigo_int}</p></div></tpl>',
                    hiddenName: 'id_punto_venta',
                    forceSelection:true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode:'remote',
                    pageSize:10,
                    queryDelay:1000,
                    listWidth:600,
                    resizable:true,
                    anchor:'80%',
                    /*renderer : function(value, p, record) {
                        //return String.format(record.data['nombre_finalidad']);
                        return String.format('{0}', '<FONT COLOR="'+record.data['color']+'"><b>'+record.data['nombre_finalidad']+'</b></FONT>');
                    }*/
                },
                type:'ComboBox',
                form:true
            },
            {
                config:{
                    name:'officeId_agencia',
                    fieldLabel:'Agencia',
                    allowBlank:true,
                    emptyText:'Agencia...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_obingresos/control/Agencia/listarAgencia',
                        id: 'id_agencia',
                        root: 'datos',
                        sortInfo:{
                            field: 'nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_agencia','nombre','codigo','codigo_int'],
                        // turn on remote sorting
                        remoteSort: true,
                        baseParams:{par_filtro:'age.nombre#age.codigo'}
                    }),
                    valueField: 'codigo_int',
                    displayField: 'nombre',
                    tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p><p><b>IATA:</b> {codigo}</p><p><b>OfficeID:</b>: {codigo_int}</p></div></tpl>',
                    hiddenName: 'id_agencia',
                    forceSelection:true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode:'remote',
                    pageSize:10,
                    queryDelay:1000,
                    listWidth:600,
                    resizable:true,
                    anchor:'80%',
                    /*renderer : function(value, p, record) {
                     //return String.format(record.data['nombre_finalidad']);
                     return String.format('{0}', '<FONT COLOR="'+record.data['color']+'"><b>'+record.data['nombre_finalidad']+'</b></FONT>');
                     }*/
                },
                type:'ComboBox',
                form:true
            },
            {
                config:{
                    name: 'fecha',
                    fieldLabel: 'Fecha',
                    allowBlank: false,
                    format: 'Ymd',
                    width: 150
                },
                type: 'DateField',
                id_grupo: 0,
                form: true
            },
            {
                config:{
                    labelSeparator: '',
                    name: 'reporte',
                    inputType:'hidden',

                },
                valorInicial:'reporte',
                type:'Field',
                form:true
            },
        ],
        labelSubmit: '<i class="fa fa-check"></i> Aplicar Filtro',
        east: {
            url: '../../../sis_obingresos/vista/boletos_vendidos/BoletosVendidos.php',
            title: 'Boleto',
            width: '70%',
            cls: 'BoletosVendidos',
            params: {'filtro':'si'}
        },

        title: 'Filtros Para el Reporte de Boletos',
        // Funcion guardar del formulario
        onSubmit: function(o) {
            var me = this;
            if (me.form.getForm().isValid()) {

                var parametros = me.getValForm()

                console.log('parametros ....', parametros);

                this.onEnablePanel(this.idContenedor + '-east', parametros)
            }
        },
        iniciarEventos:function(){
            //this.Cmp.id_gestion.on('select', function(cmb, rec, ind){

                //Ext.apply(this.Cmp.id_cuenta.store.baseParams,{id_gestion: rec.data.id_gestion})
                //Ext.apply(this.Cmp.id_partida.store.baseParams,{id_gestion: rec.data.id_gestion})
                //Ext.apply(this.Cmp.id_centro_costo.store.baseParams,{id_gestion: rec.data.id_gestion})

            //},this);
        }
    })
</script>