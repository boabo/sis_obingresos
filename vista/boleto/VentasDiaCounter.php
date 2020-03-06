<?php
/**
 * @package pxP
 * @file    Clasificacion.php
 * @author  Ariel Ayaviri Omonte
 * @date    21-09-2012
 * @description Archivo con la interfaz de usuario que permite la ejecucion de las funcionales del sistema
 */
header("content-type:text/javascript; charset=UTF-8");
?>
<style type="text/css" rel="stylesheet">
    .x-selectable,
    .x-selectable * {
        -moz-user-select: text !important;
        -khtml-user-select: text !important;
        -webkit-user-select: text !important;
    }

    .x-grid-row td,
    .x-grid-summary-row td,
    .x-grid-cell-text,
    .x-grid-hd-text,
    .x-grid-hd,
    .x-grid-row,

    .x-grid-row,
    .x-grid-cell,
    .x-unselectable
    {
        -moz-user-select: text !important;
        -khtml-user-select: text !important;
        -webkit-user-select: text !important;
    }
</style>
<script>
    Phx.vista.VentasDiaCounter = Ext.extend(Phx.gridInterfaz, {

        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                return "x-selectable";
            }/*,
            listeners:{
                itemkeydown:function(view, record, item, index, e){
                    alert('The press key is' + e.getKey());
                }
            }*/

        },
        constructor : function(config) {

            this.maestro = config.maestro;

            Phx.vista.VentasDiaCounter.superclass.constructor.call(this, config);
            this.init();
            var that = this;

            if (this.counter==undefined) {
              this.campo_fecha = new Ext.form.DateField({
                  name: 'fecha_reg',
                  grupo: this.grupoDateFin,
                  fieldLabel: 'Fecha',
                  allowBlank: false,
                  anchor: '60%',
                  gwidth: 100,
                  format: 'd/m/Y',
                  value: new Date(),
                  hidden : false
              });

              this.tbar.addField(this.campo_fecha);
              this.store.baseParams.tipo_interfaz='VentasDiaCounter';
              this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('Ymd');
              this.campo_fecha.on('select',function(value){
                  this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('Ymd');
                  this.load({params: {start: 0, limit: this.tam_pag}});
              },this);
            } else {
              console.log("llega aqui el dato",that);
              this.store.baseParams.tipo_interfaz='ResumenDetalle';
              this.store.baseParams.fecha_ini=this.principal.fecha_ini;
              this.store.baseParams.fecha_fin=this.principal.fecha_fin;
              this.store.baseParams.agente_venta=this.maestro.agente_venta;
              this.store.baseParams.punto_venta=this.principal.punto_venta;

            }


            this.load({params: {start: 0, limit: this.tam_pag}});

        },



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
                config:{
                    name: 'pasajero',
                    fieldLabel: 'Pasajero',
                    anchor: '100%',
                    disabled: true,

                    gwidth: 200,
                    readOnly:true
                },
                type:'TextField',
                filters:{pfiltro:'pasajero',type:'string'},
                id_grupo:0,
                grid:true,
                form:true,
                bottom_filter : true
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
                filters:{pfiltro:'localizador',type:'string'},
                id_grupo:0,
                grid:true,
                form:true,
                bottom_filter : true
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
                form:false
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
                form:false
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
                filters:{pfiltro:'nro_boleto',type:'string'},
                id_grupo:0,
                grid:true,
                form:true,
                bottom_filter : true
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
                    name: 'precio_total_ml',
                    fieldLabel: 'Total M/L',
                    disabled: true,
                    anchor: '90%',
                    gwidth: 150	,
                    readOnly:true,
                    galign:'right',
                    renderer:function (value,p,record){
            					if(record.data.tipo_reg != 'summary'){
            						return  String.format('<div style="font-size:12px; color:blue; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
            					}

            					else{
            						return  String.format('<div style="font-size:15px; text-align:right; color:blue;"><b>{0}<b></div>', Ext.util.Format.number(record.data.precio_total_ml_t,'0,000.00'));
            					}
            				},
                },
                type:'NumberField',
                id_grupo:0,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'precio_total_me',
                    fieldLabel: 'Total M/E',
                    disabled: true,
                    anchor: '90%',
                    gwidth: 150	,
                    readOnly:true,
                    galign:'right',
                    renderer:function (value,p,record){
            					if(record.data.tipo_reg != 'summary'){
            						return  String.format('<div style="font-size:12px; color:#E86A00; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
            					}

            					else{
            						return  String.format('<div style="font-size:15px; text-align:right; color:#E86A00;"><b>{0}<b></div>', Ext.util.Format.number(record.data.precio_total_me_t,'0,000.00'));
            					}
            				}
                },
                type:'NumberField',
                id_grupo:0,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'neto_ml',
                    fieldLabel: 'Total Neto M/L',
                    allowBlank:false,
                    anchor: '80%',
                    allowDecimals:true,
                    decimalPrecision:2,
                    allowNegative : false,
                    disabled:true,
                    galign:'right',
                    renderer:function (value,p,record){
            					if(record.data.tipo_reg != 'summary'){
            						return  String.format('<div style="font-size:12px; color:#007826; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
            					}

            					else{
            						return  String.format('<div style="font-size:15px; text-align:right; color:#007826;"><b>{0}<b></div>', Ext.util.Format.number(record.data.neto_total_ml,'0,000.00'));
            					}
            				},
                    gwidth: 150
                },
                type:'NumberField',
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'neto_me',
                    fieldLabel: 'Total Neto M/E',
                    allowBlank:false,
                    anchor: '80%',
                    allowDecimals:true,
                    decimalPrecision:2,
                    allowNegative : false,
                    disabled:true,
                    galign:'right',
                    renderer:function (value,p,record){
            					if(record.data.tipo_reg != 'summary'){
            						return  String.format('<div style="font-size:12px; color:#550089; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
            					}

            					else{
            						return  String.format('<div style="font-size:15px; text-align:right; color:#550089;"><b>{0}<b></div>', Ext.util.Format.number(record.data.neto_total_me,'0,000.00'));
            					}
            				},
                    gwidth: 150
                },
                type:'NumberField',
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'nombre',
                    fieldLabel: 'Punto Venta',
                    disabled: true,
                    anchor: '100%',
                    gwidth: 200,
                    renderer : function(value, p, record) {
                        return String.format('<div title="Punto Venta"><b><font color="green">{0}</font></b></div>', record.data['punto_venta']);
                    }
                },
                type:'TextField',
                filters:{pfiltro:'nombre',type:'string'},
                id_grupo:0,
                grid:true,
                form:true,
                bottom_filter : true
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
                        fields: ['id_forma_pago', 'nombre', 'desc_moneda','registrar_tarjeta','registrar_cc','codigo'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'forpa.nombre#forpa.codigo#mon.codigo_internacional',sw_tipo_venta:'boletos'}
                    }),
                    valueField: 'id_forma_pago',
                    displayField: 'nombre',
                    gdisplayField: 'forma_pago',
                    hiddenName: 'id_forma_pago',
                    tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p><b><p>Codigo:<font color="green">{codigo}</font></b></p><p><b>Moneda:<font color="red">{desc_moneda}</font></b></p> </div></tpl>',
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
            // {
            //     config:{
            //         name: 'monto_forma_pago_ml',
            //         fieldLabel: 'Importe Forma Pago M/L',
            //         allowBlank:false,
            //         anchor: '80%',
            //         allowDecimals:true,
            //         decimalPrecision:2,
            //         allowNegative : false,
            //         disabled:true,
            //         galign:'right',
            //         renderer:function (value,p,record){
            // 					if(record.data.tipo_reg != 'summary'){
            // 						return  String.format('<div style="font-size:12px; color:blue; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
            // 					}
            //
            // 					else{
            // 						return  String.format('<div style="font-size:15px; text-align:right; color:blue;"><b>{0}<b></div>', Ext.util.Format.number(record.data.monto_forma_pago_ml_t,'0,000.00'));
            // 					}
            // 				},
            //         gwidth: 150
            //     },
            //     type:'NumberField',
            //     id_grupo:1,
            //     grid:true,
            //     form:true
            // },
            // {
            //     config:{
            //         name: 'monto_forma_pago_me',
            //         fieldLabel: 'Importe Forma Pago M/E',
            //         allowBlank:false,
            //         anchor: '80%',
            //         allowDecimals:true,
            //         decimalPrecision:2,
            //         allowNegative : false,
            //         disabled:true,
            //         galign:'right',
            //         renderer:function (value,p,record){
            // 					if(record.data.tipo_reg != 'summary'){
            // 						return  String.format('<div style="font-size:12px; color:#E86A00; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
            // 					}
            //
            // 					else{
            // 						return  String.format('<div style="font-size:15px; text-align:right; color:#E86A00;"><b>{0}<b></div>', Ext.util.Format.number(record.data.monto_forma_pago_me_t,'0,000.00'));
            // 					}
            // 				},
            //         gwidth: 150
            //     },
            //     type:'NumberField',
            //     id_grupo:1,
            //     grid:true,
            //     form:true
            // },
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
        fwidth: '85%',
        fheight: '70%',
        title:'Buscador Boleto Amadeus',
        ActList:'../../sis_obingresos/control/Boleto/listarVentasCounter',
        id_store:'id_boleto_amadeus',
        fields: [
            {name:'id_boleto_amadeus', type: 'numeric'},
            {name:'pasajero', type: 'string'},
            {name:'localizador', type: 'string'},

            {name:'nro_boleto', type: 'string'},
            {name:'forma_pago_amadeus', type: 'string'},
            {name:'moneda', type: 'string'},
            {name:'precio_total_ml', type: 'numeric'},
            {name:'precio_total_me', type: 'numeric'},

            {name:'precio_total_me_t', type: 'numeric'},
            {name:'precio_total_ml_t', type: 'numeric'},

            {name:'codigo_agente', type: 'string'},
            {name:'id_forma_pago', type: 'numeric'},
            {name:'monto_forma_pago_ml', type: 'numeric'},
            {name:'monto_forma_pago_me', type: 'numeric'},

            {name:'neto_total_ml', type: 'numeric'},
            {name:'neto_total_me', type: 'numeric'},

            {name:'forma_pago', type: 'string'},
            {name:'fecha_emision', type: 'date',dateFormat:'Y-m-d'},
            {name:'trans_code', type: 'string'},
            {name:'trans_issue_indicator', type: 'string'},
            {name:'punto_venta', type: 'string'},
            {name:'trans_code_exch', type: 'string'},
            {name:'impreso', type: 'string'},
            {name:'tipo_reg', type: 'string'},
            {name:'neto_me', type: 'numeric'},
            {name:'neto_ml', type: 'numeric'},
        ],
        sortInfo:{
            field: 'nro_boleto',
            direction: 'DESC'
        },
        arrayDefaultColumHidden:['estado_reg','usuario_ai',
            'fecha_reg','fecha_mod','usr_reg','usr_mod','codigo_agencia','nombre_agencia','neto','comision'],

        bdel:false,
        bsave:false,
        bnew:false,
        bedit:false,




    });
</script>
