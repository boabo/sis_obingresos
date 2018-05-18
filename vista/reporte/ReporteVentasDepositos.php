<?Php
/**
 *@package PXP
 *@file   FormReporteVentasCorporativas.php
 *@author  MAM
 *@date    09-11-2016
 *@description Reportes de deposito
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ReporteVentasDepositos = Ext.extend(Phx.frmInterfaz, {
        Atributos : [

            {
                config:{
                    name: 'tipo_agencia',
                    fieldLabel: 'Tipo Agencia',
                    allowBlank:false,
                    emptyText:'Tipo...',
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local',
                    gwidth: 150,
<<<<<<< HEAD
                    anchor: '100%',
=======
>>>>>>> 896209f9b47bfe3ee98b0f239ba7d78a9ec9a031
                    store:['corporativa','noiata','todas']
                },
                type:'ComboBox',
                id_grupo:1,
                form:true
            },
            {
                config:{
                    name: 'forma_pago',
                    fieldLabel: 'Forma Pago',
                    allowBlank:false,
                    emptyText:'Forma...',
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local',
                    gwidth: 150,
<<<<<<< HEAD
                    anchor: '100%',
=======
>>>>>>> 896209f9b47bfe3ee98b0f239ba7d78a9ec9a031
                    store:['prepago','postpago','todas']
                },
                type:'ComboBox',
                id_grupo:1,
                form:true
            },
            {
                config:{
                    name: 'id_lugar',
                    fieldLabel: 'Lugar',
                    allowBlank: true,
                    emptyText:'Lugar...',
                    store:new Ext.data.JsonStore(
                        {
                            url: '../../sis_parametros/control/Lugar/listarLugar',
                            id: 'id_lugar',
                            root: 'datos',
                            sortInfo:{
                                field: 'nombre',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_lugar','id_lugar_fk','codigo','nombre','tipo','sw_municipio','sw_impuesto','codigo_largo'],
                            // turn on remote sorting
                            remoteSort: true,
                            baseParams:{par_filtro:'lug.nombre',es_regional:'si'}
                        }),
                    valueField: 'id_lugar',
                    displayField: 'nombre',
                    hiddenName: 'id_lugar',
                    triggerAction: 'all',
                    lazyRender:true,
                    mode:'remote',
                    pageSize:50,
                    queryDelay:500,
<<<<<<< HEAD
                    anchor: '100%',
=======
                    anchor:"35%",
>>>>>>> 896209f9b47bfe3ee98b0f239ba7d78a9ec9a031
                    minChars:2,
                    enableMultiSelect:true
                },
                type:'AwesomeCombo',
<<<<<<< HEAD
                id_grupo:1,
=======
>>>>>>> 896209f9b47bfe3ee98b0f239ba7d78a9ec9a031
                form:true
            },
            {
                config:{
                    name: 'fecha_ini',
                    fieldLabel: 'Fecha Inicio',
                    allowBlank: false,
<<<<<<< HEAD
                    anchor: '95%',
=======
                    anchor: '30%',
>>>>>>> 896209f9b47bfe3ee98b0f239ba7d78a9ec9a031
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'fecha_ini',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
            },
<<<<<<< HEAD

=======
>>>>>>> 896209f9b47bfe3ee98b0f239ba7d78a9ec9a031
            {
                config:{
                    name: 'fecha_fin',
                    fieldLabel: 'Fecha Fin',
                    allowBlank: false,
<<<<<<< HEAD
                    anchor: '95%',
=======
                    anchor: '30%',
>>>>>>> 896209f9b47bfe3ee98b0f239ba7d78a9ec9a031
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'fecha_fin',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
<<<<<<< HEAD
            },
           /* {
                config:{
                    name: 'nro_deposito',
                    fieldLabel: 'No Deposito',
                    allowBlank: true,
                    anchor: '95%',
                    gwidth: 150,
                    maxLength:70
                },
                type:'TextField',
                id_grupo:2,
                grid:true,
                form:true
            },*/
            {
                config:{
                    name: 'fecha_ini_de',
                    fieldLabel: 'Fecha Inicio Deposito',
                    allowBlank: false,
                    anchor: '105%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'fecha_ini',type:'date'},
                id_grupo:2,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'fecha_fin_de',
                    fieldLabel: 'Fecha Fin Deposito',
                    allowBlank: false,
                    anchor: '105%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'fecha_fin',type:'date'},
                id_grupo:2,
                grid:true,
                form:true
            }
            ],

        Grupos: [
            {
                layout: 'column',
                border: true,
                defaults: {
                    border: false
                },

                items: [
                    {
                        bodyStyle: 'padding-right:10px;',
                        items: [

                            {
                                xtype: 'fieldset',
                                title: '  Datos Cuentas por Pagar ',
                                autoHeight: true,
                                items: [/*this.compositeFields()*/],
                                id_grupo: 1
                            }

                        ]
                    },
                    {
                        bodyStyle: 'padding-left:10px;',
                        items: [
                            {
                                xtype: 'fieldset',
                                title: ' Datos  de Deposito ',
                                autoHeight: true,
                                items: [],
                                id_grupo: 2
                            }


                        ]
                    }
                ]
            }
        ],
=======
            }],

>>>>>>> 896209f9b47bfe3ee98b0f239ba7d78a9ec9a031
        title : 'Reporte Deposito',
        ActSave : '../../sis_obingresos/control/DetalleBoletosWeb/reporteVentasCorporativasDepositos',

        topBar : true,
        botones : false,
        labelSubmit : 'Generar',
        tooltipSubmit : '<b>Reporte Ventas Agencias</b>',

        constructor : function(config) {
            Phx.vista.ReporteVentasDepositos.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
        },

        iniciarEventos:function(){
            this.cmpFechaIni = this.getComponente('fecha_ini');
            this.cmpFechaFin = this.getComponente('fecha_fin');
        },
        tipo : 'reporte',
        clsSubmit : 'bprint'
    })
</script>
