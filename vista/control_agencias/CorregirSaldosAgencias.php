<?php
/**
 *@package pXP
 *@file gen-DetalleCredito.php
 *@author  (ivaldivia)
 *@date 22-10-2019 08:54:00
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
include_once ('../../media/styles.php');
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.CorregirSaldosAgencias=Ext.extend(Phx.gridInterfaz,{

      viewConfig: {
          stripeRows: false,
          getRowClass: function(record) {
              if(record.data.diferencia != 0){
                  return 'prioridad_importanteA';
              }
          }/*,
          listener: {
              render: this.createTooltip
          },*/

      },
      stateId:'CorregirSaldosAgencias',



            constructor:function(config){
                this.agencia = config.agencia;
                var agencia  = this.agencia;
                Phx.vista.CorregirSaldosAgencias.superclass.constructor.call(this,config);
                this.init();
                this.iniciarEventos();
                  this.store.baseParams={
                    id_agencia:agencia

                  };
                  this.load({params: {start: 0, limit: 50}});
                  console.log("llega aqui el this",this);
            },


            successSave:function(resp){
          			Phx.vista.CorregirSaldosAgencias.superclass.successSave.call(this,resp);
          			Phx.CP.getPagina(this.idContenedorPadre).reload();
          	},

            Atributos:[
                 {
                    config : {
                        labelSeparator : '',
                        inputType : 'hidden',
                        name : 'id_agencia'
                    },
                    type : 'Field',
                    form : true
                },
                {
                    config:{
                        name: 'periodo',
                        fieldLabel: 'Periodo',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 200
                    },
                    type:'TextField',
                    filters:{pfiltro:'cr.periodo',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'id_periodo_venta',
                        fieldLabel: 'Id P.V',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 90
                    },
                    type:'TextField',
                    filters:{pfiltro:'cr.id_periodo_venta',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },

                {
                    config:{
                        name: 'tipo',
                        fieldLabel: 'Tipo',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 90
                    },
                    type:'TextField',
                    filters:{pfiltro:'cr.tipo',type:'string'},
                    id_grupo:1,
                    grid:false,
                    form:false
                },

                {
                    config:{
                        name: 'depositos_con_saldos',
                        fieldLabel: 'Depositos con Saldos',
                        allowBlank: true,
                        anchor: '80%',
                        renderer:function (value,p,record){
                          return  String.format('<div style="text-align:right; font-size:12px;">{0}</div>', Ext.util.Format.number(value,'0,000.00'));

                        },
                        gwidth: 150
                    },
                    type:'NumberField',
                    filters:{pfiltro:'cr.depositos_con_saldos',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },

                {
                    config:{
                        name: 'depositos',
                        fieldLabel: 'Depositos',
                        allowBlank: true,
                        anchor: '80%',
                        renderer:function (value,p,record){
                          return  String.format('<div style="text-align:right; font-size:12px;">{0}</div>', Ext.util.Format.number(value,'0,000.00'));

                        },
                        gwidth: 150
                    },
                    type:'NumberField',
                    filters:{pfiltro:'cr.depositos',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },

                {
                    config:{
                        name: 'debitos',
                        fieldLabel: 'Debitos',
                        allowBlank: true,
                        anchor: '80%',
                        renderer:function (value,p,record){
                          return  String.format('<div style="text-align:right; font-size:12px;">{0}</div>', Ext.util.Format.number(value,'0,000.00'));

                        },
                        gwidth: 150
                    },
                    type:'NumberField',
                    filters:{pfiltro:'cr.debitos',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },

                {
                    config:{
                        name: 'saldo_calculado',
                        fieldLabel: 'Saldo Calculado',
                        allowBlank: true,
                        anchor: '80%',
                        renderer:function (value,p,record){
                          return  String.format('<div style="text-align:right; font-size:12px;">{0}</div>', Ext.util.Format.number(value,'0,000.00'));

                        },
                        gwidth: 150
                    },
                    type:'NumberField',
                    filters:{pfiltro:'cr.saldo_calculado',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },

                {
                    config:{
                        name: 'saldo_arrastrado',
                        fieldLabel: 'Saldo Arrastrado',
                        allowBlank: true,
                        anchor: '80%',
                        renderer:function (value,p,record){
                          return  String.format('<div style="text-align:right; font-size:12px;">{0}</div>', Ext.util.Format.number(value,'0,000.00'));

                        },
                        gwidth: 150
                    },
                    type:'NumberField',
                    filters:{pfiltro:'cr.saldo_arrastrado',type:'string'},
                    id_grupo:1,
                    grid:true,
                    egrid:true,
                    form:true
                },

                {
                    config:{
                        name: 'diferencia',
                        fieldLabel: 'Diferencia',
                        allowBlank: true,
                        anchor: '80%',
                        galign:'right',
                        renderer:function (value,p,record){
                					 if(record.data.diferencia != 0){
                					 	      return  String.format('<div style="color:red; font-size:12px; text-align:right; font-weight:bold;">{0}</div>', Ext.util.Format.number(value,'0,000.00'));
                					   }
                					else{
                                  return  String.format('<div style="text-align:right; font-weight:bold;">{0}</div>', Ext.util.Format.number(value,'0,000.00'));

                					}
                				},
                        gwidth: 90
                    },
                    type:'NumberField',
                    filters:{pfiltro:'cr.diferencia',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },


            ],
            tam_pag:200,
            title:'Deposito',
            ActList:'../../sis_obingresos/control/ControlAgencias/corregirSaldos',
            ActSave:'../../sis_obingresos/control/ControlAgencias/modificarSaldoAgencia',
            id_store:'id_periodo_venta',
            fields: [
              {name:'id_agencia', type: 'numeric'},
              {name:'id_periodo_venta', type: 'numeric'},
              {name:'depositos_con_saldos', type: 'numeric'},
              {name:'depositos', type: 'numeric'},
              {name:'debitos', type: 'numeric'},
              {name:'saldo_calculado', type: 'numeric'},
              {name:'saldo_arrastrado', type: 'numeric'},
              {name:'periodo', type: 'string'},
              {name:'diferencia', type: 'numeric'},
            ],
            sortInfo:{
                field: 'id_periodo_venta',
                direction: 'ASC'
            },
            bdel:false,
            bsave:true,
            bedit: false,
            bnew : false

        }
    )
</script>
