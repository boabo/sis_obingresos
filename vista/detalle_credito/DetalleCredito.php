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
    Phx.vista.DetalleCredito=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.maestro=config.maestro;
                Phx.vista.DetalleCredito.superclass.constructor.call(this,config);
                this.init();

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
                        name: 'nro_deposito',
                        fieldLabel: 'Saldo/Nro Deposito',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 155,
                        renderer:function (value,p,record){
                            if(record.data.nro_deposito != 'summary'){
                                return  String.format('{0}', value);
                            }
                            else{
                                return '<b><p align="right">Total: &nbsp;&nbsp; </p></b>';
                            }
                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'rdc.autorizacion__nro_deposito',type:'string'},
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
                            if(record.data.nro_deposito != 'summary'){
                                return  String.format('{0}', Ext.util.Format.number(record.data.monto_total,'0,000.00'));
                            }
                            else{
                                return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.monto_total,'0,000.00'));
                            }
                        }
                    },
                    type:'NumberField',
                    filters:{pfiltro:'rdc.monto_total',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:true
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
                    filters:{pfiltro:'rdc.fecha',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:true
                }

            ],
            tam_pag:50,
            title:'Detalle Credito',
            ActList:'../../sis_obingresos/control/DetalleCredito/listarDetalleCredito',
            id_store:'id_movimiento_entidad',
            fields: [
                {name:'id_movimiento_entidad', type: 'numeric'},
                {name:'id_agencia', type: 'numeric'},
                {name:'id_periodo_venta', type: 'numeric'},
                {name:'nro_deposito', type: 'string'},
                {name:'monto_total', type: 'numeric'},
                {name:'fecha', type: 'date',dateFormat:'Y-m-d'}

            ],
            sortInfo:{
                field: 'id_movimiento_entidad',
                direction: 'ASC'
            },
            bdel:false,
            bsave:false,
            bedit: false,
            bnew : false,
            loadValoresIniciales: function () {
                Phx.vista.DetalleCredito.superclass.loadValoresIniciales.call(this);
            },
            onReloadPage: function (m) {
                this.maestro = m;
                console.log(m);
                Ext.apply(this.store.baseParams,{id_agencia: this.maestro.id_agencia ,
                    id_creditos: this.maestro.id_creditos});
                this.load({params: {start: 0, limit: 50}});
            },

            tabeast:[
            {
                url:'../../../sis_obingresos/vista/detalle_debito/DetalleDebito.php',
                title:'Debito',
                width:'62%',
                cls:'DetalleDebito',
                collapsed:false
            }
            ]

        }
    )
</script>

		