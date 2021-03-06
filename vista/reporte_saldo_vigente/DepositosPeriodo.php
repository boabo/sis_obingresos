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
                        name: 'id_deposito'
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
                        name: 'id_apertura_cierre_caja'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config:{
                        name: 'nombre',
                        fieldLabel: 'Agencia',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 430
                    },
                    type:'TextField',
                    filters:{pfiltro:'age.nombre',type:'string'},
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
                        name: 'nro_deposito',
                        fieldLabel: '<center>Número de Comprobante <br> Depósito</center>',
                        allowBlank: true,
                        anchor : '150%',
                        gwidth : 150,
                        maxLength : 20,
                        galign:'left',
                        renderer:function (value,p,record){
                            if(record.data.nro_deposito != 'summary'){
                                return  String.format('{0}', value);
                            }
                            else{
                                return '<b><p align="right">Total: &nbsp;&nbsp; </p></b>';
                            }
                        }
                    },
                    type:'NumberField',
                    filters:{pfiltro:'dep.nro_deposito',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    bottom_filter:true,
                    form:true
                },
                {
                    config:{
                        name: 'nro_deposito_boa',
                        fieldLabel: '<center>Número de Comprobante <br>Depósitos BOA</center>',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        galign:'left',
                    },
                    type:'TextField',
                    filters:{pfiltro:'dep.nro_deposito_boa',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },

                {
                    config:{
                        name: 'monto_deposito',
                        fieldLabel: 'Monto',
                        allowBlank: true,
                        anchor : '100%',
                        gwidth : 110,
                        maxLength : 20,
                        galign:'right',
                        renderer:function (value,p,record){
                            if(record.data.nro_deposito != 'summary'){
                                return  String.format('{0}', Ext.util.Format.number(record.data.monto_deposito,'0,000.00'));
                            }
                            else{
                                return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.monto_deposito,'0,000.00'));
                            }
                        }
                    },
                    type:'NumberField',
                    filters:{pfiltro:'dep.monto_deposito',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:true
                }


            ],
            tam_pag:50,
            title:'Deposito',
            ActList:'../../sis_obingresos/control/DepositoPeriodo/listarDepositosPeriodo',
            id_store:'id_deposito',
            fields: [
                {name:'id_deposito', type: 'numeric'},
                {name:'nombre', type: 'string'},
                {name:'estado_reg', type: 'string'},
                {name:'nro_deposito', type: 'string'},
                {name:'nro_deposito_boa', type: 'string'},
                {name:'monto_deposito', type: 'numeric'},
                {name:'id_agencia', type: 'numeric'},
                {name:'fecha', type: 'date',dateFormat:'Y-m-d'},
                {name:'id_apertura_cierre_caja', type: 'numeric'}



            ],
            sortInfo:{
                field: 'id_deposito',
                direction: 'DESC'
            },
            bdel:false,
            bsave:false,
            bedit: false,
            bnew : false

        }
    )
</script>
