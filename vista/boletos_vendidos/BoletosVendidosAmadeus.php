<?php
/**
 *@package pXP
 *@file BoletosVendidosAmadeus.php
 *@author  Gonzalo Sarmiento Sejas
 *@date 23-09-2017
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.BoletosVendidosAmadeus=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.maestro=config.maestro;
                //this.grupo = 'no';
                //this.tipo_usuario = 'vendedor';

                Ext.Ajax.request({
                    url:'../../sis_ventas_facturacion/control/Venta/getVariablesBasicas',
                    params: {'prueba':'uno'},
                    success:this.successGetVariables,
                    failure: this.conexionFailure,
                    arguments:config,
                    timeout:this.timeout,
                    scope:this
                });

                //Phx.vista.BoletosVendidosAmadeus.superclass.constructor.call(this);
                //this.init();

            },
            successGetVariables : function (response,request) {
                //llama al constructor de la clase padre
                Phx.vista.BoletosVendidosAmadeus.superclass.constructor.call(this,request.arguments);
                this.init();
                this.recuperarBase();

/*
                this.addButton('btnImprimir',
                    {
                        text: 'Imprimir',
                        iconCls: 'bpdf32',
                        disabled: true,
                        handler: this.imprimirBoleto,
                        tooltip: '<b>Imprimir Boleto</b><br/>Imprime el boleto'
                    }
                );*/

                this.store.baseParams.estado = 'borrador';

            },
            /*imprimirBoleto: function(){

                var rec = this.sm.getSelected();
                var data = rec.data;
                if (data) {
                    Phx.CP.loadingShow();
                    Ext.Ajax.request({
                        url : '../../sis_obingresos/control/Boleto/reporteBoleto',
                        params : {
                            'id_boleto_amadeus' : data.id_boleto_amadeus
                        },
                        success : this.successExport,
                        failure : this.conexionFailure,
                        timeout : this.timeout,
                        scope : this
                    });
                }
            },*/
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
                        name: 'codigo_iata',
                        fieldLabel: 'Cod. Iata',
                        gwidth: 100
                    },
                    type:'TextField',

                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'officeid',
                        fieldLabel: 'officeID',
                        anchor: '40%',
                        gwidth: 130
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.officeID',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true,
                    bottom_filter: true
                },
                {
                    config:{
                        name: 'nro_boleto',
                        fieldLabel: 'Billete: 930-',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 120,
                        maxLength:10,
                        minLength:10,
                        enableKeyEvents:true,
                        renderer : function(value, p, record) {
                            if (record.data['mensaje_error'] != '') {
                                return String.format('<div title="Error"><b><font color="red">{0}</font></b></div>', value);

                            } else {
                                return String.format('{0}', value);
                            }


                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.nro_boleto',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true,
                    bottom_filter: true
                },
                {
                    config:{
                        name: 'localizador',
                        fieldLabel: 'Pnr',
                        anchor: '40%',
                        gwidth: 130
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.localizador',type:'string'},
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
                        gwidth: 130,
                        readOnly:true
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.pasajero',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true,
                    bottom_filter: true
                },
                {
                    config:{
                        name: 'fecha_emision',
                        fieldLabel: 'Fecha Emision',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'bol.fecha_emision',type:'date'},
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'total',
                        fieldLabel: 'Total Boleto',
                        anchor: '80%',
                        gwidth: 125	,
                        readOnly:true
                    },
                    type:'NumberField',
                    filters:{pfiltro:'bol.total',type:'numeric'},
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'neto',
                        fieldLabel: 'Neto',
                        gwidth: 100
                    },
                    type:'NumberField',
                    filters:{pfiltro:'bol.neto',type:'numeric'},
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'moneda',
                        fieldLabel: 'Moneda de Emision',
                        anchor: '80%',
                        gwidth: 150,
                        readOnly:true

                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.moneda',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'forma_pag_amadeus',
                        fieldLabel: 'Forma Pago Amadeus',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength:50
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'voided',
                        fieldLabel: 'Estado',
                        anchor: '60%',
                        gwidth: 80,
                        readOnly:true,
                        renderer : function(value, p, record) {
                            if (record.data['voided'] != 'si') {
                                return String.format('<div title="Valido"><b><font color="green">{0}</font></b></div>', 'ok');

                            } else {
                                return String.format('<div title="Anulado"><b><font color="red">{0}</font></b></div>', 'void');
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
                        name: 'estado',
                        fieldLabel: 'Estado',
                        gwidth: 100,
                        readOnly:true
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.estado',type:'string'},
                    grid:true,
                    id_grupo:0,
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
                    filters:{pfiltro:'bol.estado_reg',type:'string'},
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
                    filters:{pfiltro:'bol.id_usuario_ai',type:'numeric'},
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
                    filters:{pfiltro:'bol.fecha_reg',type:'date'},
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
                    filters:{pfiltro:'bol.usuario_ai',type:'string'},
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
            fwidth: '70%',
            title:'Boletos Vendidos Amadeus',
            //ActSave:'../../sis_obingresos/control/Boleto/modificarBoletoVenta',
            //ActDel:'../../sis_obingresos/control/Boleto/eliminarBoleto',
            ActList:'../../sis_obingresos/control/Boleto/traerBoletosAgenciaAmadeus',
            id_store:'id_boleto_amadeus',
            fields: [
                {name:'id_boleto_amadeus', type: 'numeric'},
                {name:'fecha_emision', type: 'date',dateFormat:'Y-m-d'},
                {name:'codigo_iata', type: 'string'},
                {name:'estado', type: 'string'},
                {name:'id_agencia', type: 'numeric'},
                {name:'moneda', type: 'string'},
                {name:'total', type: 'numeric'},
                {name:'pasajero', type: 'string'},
                {name:'id_moneda_boleto', type: 'numeric'},
                {name:'estado_reg', type: 'string'},
                {name:'codigo_agencia', type: 'string'},
                {name:'neto', type: 'numeric'},
                {name:'localizador', type: 'string'},
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
                {name:'forma_pag_amadeus', type: 'string'},
                {name:'officeid', type: 'string'},
                {name:'voided', type: 'string'}
            ],
            sortInfo:{
                field: 'id_boleto_amadeus',
                direction: 'DESC'
            },

            bdel:false,
            bnew:false,
            bedit:false,
            bsave:false,

            /*tabsouth:[
                {
                    url:'../../../sis_obingresos/vista/boleto_amadeus_forma_pago/BoletoFormaPagoAmadeus.php',
                    title:'Formas de Pago',
                    height:'40%',
                    cls:'BoletoFormaPago'
                }

            ],*/

            onReloadPage:function(param){
                //Se obtiene la gestión en función de la fecha del comprobante para filtrar partidas, cuentas, etc.
                var me = this;
                this.initFiltro(param);
            },

            initFiltro: function(param){
                this.store.baseParams=param;
                this.load( { params: { start:0, limit: this.tam_pag } });
            },

        }
    )
</script>
