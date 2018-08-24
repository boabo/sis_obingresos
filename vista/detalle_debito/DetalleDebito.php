<?php
/**
 *@package pXP
 *@file gen-DetalleDebito.php
 *@author  (miguel.mamani)
 *@date 18-07-2018 16:54:10
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.DetalleDebito=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.maestro=config.maestro;
                //llama al constructor de la clase padre
                Phx.vista.DetalleDebito.superclass.constructor.call(this,config);
                this.init();
            },

            Atributos:[
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_detalle_boletos_web'
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
                        name: 'nro_boleto',
                        fieldLabel: 'Nro Boleto',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 200,
                        renderer:function (value,p,record){
                            if(record.data.nro_boleto != 'summary'){
                                return  String.format('{0}', value);
                            }
                            else{
                                return '<b><p align="right">Total: &nbsp;&nbsp; </p></b>';
                            }
                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'dbr.billeta_pnr',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'monto',
                        fieldLabel: 'Monto',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength : 20,
                        galign:'right',
                        renderer:function (value,p,record){
                            if(record.data.nro_boleto != 'summary'){
                                return  String.format('{0}', Ext.util.Format.number(record.data.monto,'0,000.00'));
                            }
                            else{
                                return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.monto,'0,000.00'));
                            }
                        }
                    },
                    type:'NumberField',
                    filters:{pfiltro:'dbr.monto',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'neto',
                        fieldLabel: 'neto',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength : 20,
                        galign:'right',
                        renderer:function (value,p,record){
                            if(record.data.nro_boleto != 'summary'){
                                return  String.format('{0}', Ext.util.Format.number(record.data.neto,'0,000.00'));
                            }
                            else{
                                return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.neto,'0,000.00'));
                            }
                        }
                    },
                    type:'NumberField',
                    filters:{pfiltro:'dbr.neto',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'comision',
                        fieldLabel: 'comision',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength : 20,
                        galign:'right',
                        renderer:function (value,p,record){
                            if(record.data.nro_boleto != 'summary'){
                                return  String.format('{0}', Ext.util.Format.number(record.data.comision,'0,000.00'));
                            }
                            else{
                                return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.comision,'0,000.00'));
                            }
                        }
                    },
                    type:'NumberField',
                    filters:{pfiltro:'dbr.comision',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'total_monto',
                        fieldLabel: 'Total Monto',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength : 20,
                        galign:'right',
                        renderer:function (value,p,record){
                            if(record.data.nro_boleto != 'summary'){
                                return  String.format('{0}', Ext.util.Format.number(record.data.total_monto,'0,000.00'));
                            }
                            else{
                                return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.total_monto,'0,000.00'));
                            }
                        }
                    },
                    type:'NumberField',
                    filters:{pfiltro:'dbr.importe',type:'numeric'},
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
                    filters:{pfiltro:'dbr.fecha',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:true
                }
            ],
            tam_pag:50,
            title:'Detalle Debito',
            ActList:'../../sis_obingresos/control/DetalleDebito/listarDetalleDebito',
            id_store:'id_detalle_boletos_web',
            fields: [
                {name:'id_detalle_boletos_web', type: 'numeric'},
                {name:'id_agencia', type: 'numeric'},
                {name:'id_periodo_venta', type: 'numeric'},
                {name:'nro_boleto', type: 'string'},
                {name:'fecha', type: 'date',dateFormat:'Y-m-d'},
                {name:'monto', type: 'numeric'},
                {name:'neto', type: 'numeric'},
                {name:'comision', type: 'numeric'},
                {name:'total_monto', type: 'numeric'}
            ],
            sortInfo:{
                field: 'id_detalle_boletos_web',
                direction: 'ASC'
            },
            bdel:false,
            bsave:false,
            bedit:false,
            bnew:false,
        onReloadPage:function(m){
            this.maestro=m;
            Ext.apply(this.store.baseParams,{id_agencia: this.maestro.id_agencia,
                                            id_debitos: this.maestro.id_debitos});
            this.load({params:{start:0, limit:this.tam_pag}});
        },
        loadValoresIniciales:function(){
            Phx.vista.DetalleDebito.superclass.loadValoresIniciales.call(this);
        }
        }
    )
</script>

