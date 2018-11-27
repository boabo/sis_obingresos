<?php
/**
 *@package pXP
 *@file gen-DetalleCredito.php
 *@author  (miguel.mamani)
 *@date 18-07-2018 16:53:28
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.DepositosPeriodo=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.agencia = config.agencia;
                this.fechaIni = config.fechaIni;
                this.fechaFin = config.fechaFin;

                console.log('LLEGA AGENCIA AQUI',this.agencia);
                var agencia  = this.agencia;
                var fechaInicio = this.fechaIni;
                var fechaFinal = this.fechaFin;
                Phx.vista.DepositosPeriodo.superclass.constructor.call(this,config);
                this.init();
                  this.store.baseParams={
                    id_agencia:agencia,
                    fecha_ini:fechaInicio,
                    fecha_fin:fechaFinal
                  };
                  this.load({params: {start: 0, limit: 50}});

            },

            Atributos:[
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_movimiento_entidad'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_agencia'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_periodo_venta'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config:{
                        name: 'fecha_ini',
                        fieldLabel: 'Fecha Inicio',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'mo.fecha_ini',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'fecha_fin',
                        fieldLabel: 'Fecha Fin',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'mo.fecha_fin',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'mes',
                        fieldLabel: 'Mes',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 50
                    },
                    type:'TextField',
                    filters:{pfiltro:'mo.mes',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'autorizacion__nro_deposito',
                        fieldLabel: 'Número de Comprobante Depósito',
                        allowBlank: true,
                        anchor : '150%',
                        gwidth : 200,
                        maxLength : 20,
                        galign:'right',
                        renderer:function (value,p,record){
                            if(record.data.autorizacion__nro_deposito != 'summary'){
                                return  String.format('{0}', value);
                            }
                            else{
                                return '<b><p align="right">Total: &nbsp;&nbsp; </p></b>';
                            }
                        }
                    },
                    type:'NumberField',
                    filters:{pfiltro:'mo.autorizacion__nro_deposito',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    bottom_filter:true,
                    form:true
                },
                {
                    config:{
                        name: 'nro_deposito',
                        fieldLabel: 'Número de Comprobante de Depósitos BoA',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 220
                    },
                    type:'TextField',
                    filters:{pfiltro:'mo.mes',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'fecha',
                        fieldLabel: 'Fecha Deposito',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'mo.fecha',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'monto_total',
                        fieldLabel: 'Monto',
                        allowBlank: true,
                        anchor : '100%',
                        gwidth : 110,
                        maxLength : 20,
                        galign:'right',
                        renderer:function (value,p,record){
                            if(record.data.autorizacion__nro_deposito != 'summary'){
                                return  String.format('{0}', Ext.util.Format.number(record.data.monto_total,'0,000.00'));
                            }
                            else{
                                return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.monto_total,'0,000.00'));
                            }
                        }
                    },
                    type:'NumberField',
                    filters:{pfiltro:'mo.monto_total',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'gestion',
                        fieldLabel: 'Gestion',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 155
                    },
                    type:'TextField',
                    filters:{pfiltro:'mo.gestion',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                }

            ],
            tam_pag:50,
            title:'Deposito',
            ActList:'../../sis_obingresos/control/DepositoPeriodo/listarDepositosPeriodo',
            id_store:'id_movimiento_entidad',
            fields: [
                {name:'id_movimiento_entidad', type: 'numeric'},
                {name:'id_agencia', type: 'numeric'},
                {name:'id_periodo_venta', type: 'numeric'},
                {name:'gestion', type: 'string'},
                {name:'mes', type: 'string'},
                {name:'fecha_ini', type: 'date',dateFormat:'Y-m-d'},
                {name:'fecha_fin', type: 'date',dateFormat:'Y-m-d'},
                {name:'fecha', type: 'date',dateFormat:'Y-m-d'},
                {name:'autorizacion__nro_deposito', type: 'string'},
                {name:'monto_total', type: 'numeric'},
                {name:'nro_deposito', type: 'string'}
            ],
            sortInfo:{
                field: 'id_movimiento_entidad',
                direction: 'ASC'
            },
            bdel:false,
            bsave:false,
            bedit: false,
            bnew : false

        }
    )
</script>
