<?php
/**
 *@package pXP
 *@file gen-MedioPagoPw.php
 *@author  (admin)
 *@date 04-06-2019 22:47:38
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.MedioPagoPw=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.maestro=config.maestro;
                //llama al constructor de la clase padre
                Phx.vista.MedioPagoPw.superclass.constructor.call(this,config);
                this.init();
                this.load({params:{start:0, limit:this.tam_pag}})

                /*Aqui aumentando para configurar autorizaciones*/
                this.crearFormAuto();
                this.addButton('inserAuto',{
                                              text: 'Configurar Autorizaciones',
                                              iconCls: 'bengineadd',
                                              disabled: true,
                                              handler: this.mostarFormAuto,
                                              tooltip: '<b>Configurar autorizaciones</b><br/>Permite seleccionar desde que modulos  puede selecionarse el concepto'
                                            }
                               );
                /***********************************************/


            },

            preparaMenu: function () {
          			var rec = this.sm.getSelected();
          			this.getBoton('inserAuto').enable();
          			Phx.vista.MedioPagoPw.superclass.preparaMenu.call(this);
          		},

          		liberaMenu : function(){
          				var rec = this.sm.getSelected();
          				Phx.vista.MedioPagoPw.superclass.liberaMenu.call(this);
          		},

            crearFormAuto:function(){
          		  this.formAuto = new Ext.form.FormPanel({
                      baseCls: 'x-plain',
                      autoDestroy: true,

                      border: false,
                      layout: 'form',
                       autoHeight: true,


                      items: [
          							{
          								 name:'sw_autorizacion',
          								 xtype:"awesomecombo",
          								 fieldLabel:'Autorizaciones',
          								 allowBlank: true,
          								 emptyText:'Autorizaciones...',
          								 store : new Ext.data.JsonStore({
          									 url : '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
          									 id : 'id_catalogo',
          									 root : 'datos',
          									 sortInfo : {
          										 field : 'codigo',
          										 direction : 'ASC'
          									 },
          									 totalProperty : 'total',
          									 fields: ['codigo','descripcion'],
          									 remoteSort : true,
          									 baseParams:{
          										cod_subsistema:'VEF',
          										catalogo_tipo:'autorizaciones_concepto_ventas'
          									},
          								 }),
          								 valueField: 'codigo',
          								 displayField: 'descripcion',
          								 mode: 'remote',
          								 forceSelection:true,
          								 typeAhead: true,
          								 triggerAction: 'all',
          								 lazyRender: true,
          								 queryDelay: 1000,
          								 width: 250,
          								 minChars: 2 ,
          							   enableMultiSelect: true,
          								 pageSize: 200,
          	 							 queryDelay: 100
          							},

          							{
          								 name:'regionales',
          								 xtype:"awesomecombo",
          								 fieldLabel:'Regionales',
          								 allowBlank: true,
          								 emptyText:'Regionales...',
          								 store : new Ext.data.JsonStore({
          									 url : '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
          									 id : 'id_catalogo',
          									 root : 'datos',
          									 sortInfo : {
          										 field : 'codigo',
          										 direction : 'ASC'
          									 },
          									 totalProperty : 'total',
          									 fields: ['codigo','descripcion'],
          									 remoteSort : true,
          									 baseParams:{
          										cod_subsistema:'PARAM',
          										catalogo_tipo:'regionales_conceptos'
          									},
          								 }),
          								 valueField: 'codigo',
          								 displayField: 'descripcion',
          								 mode: 'remote',
          								 forceSelection:true,
          								 typeAhead: true,
          								 triggerAction: 'all',
          								 lazyRender: true,
          								 queryDelay: 1000,
          								 width: 250,
          								 minChars: 2 ,
          							   enableMultiSelect: true,
          								 pageSize: 200,
          	 							 queryDelay: 100
          							},
          					]
                  });



          		this.wAuto = new Ext.Window({
                      title: 'Configuracion',
                      collapsible: true,
                      maximizable: true,
                      autoDestroy: true,
                      width: 380,
                      height: 170,
                      layout: 'fit',
                      plain: true,
                      bodyStyle: 'padding:5px;',
                      buttonAlign: 'center',
                      items: this.formAuto,
                      modal:true,
                       closeAction: 'hide',
                      buttons: [{
                          text: 'Guardar',
                          handler: this.saveAuto,
                          scope: this

                      },
                       {
                          text: 'Cancelar',
                          handler: function(){ this.wAuto.hide() },
                          scope: this
                      }]
                  });

          					this.cmpAuto = this.formAuto.getForm().findField('sw_autorizacion');
          				 this.cmpRegionales = this.formAuto.getForm().findField('regionales');


          	},

            mostarFormAuto:function(){
          		var data = this.getSelectedData();
              if (data) {
                  if(data.sw_autorizacion != '' && data.sw_autorizacion != null){
              			this.cmpAuto.setValue(data.sw_autorizacion);
              		} else {
                    this.cmpAuto.reset();
                  }
                  if (data.regionales != '' && data.regionales != null) {
                    this.cmpRegionales.setValue(data.regionales);
                  } else {
                    this.cmpRegionales.reset();
                  }
                this.wAuto.show();
              }
          	},

            saveAuto: function(){
          		    var d = this.getSelectedData();
          		    Phx.CP.loadingShow();
                      Ext.Ajax.request({
                          url: '../../sis_obingresos/control/MedioPagoPw/editarAutorizaciones',
                          params: {
          												sw_autorizacion: this.cmpAuto.getValue(),
                          	      regionales: this.cmpRegionales.getValue(),
                          	      id_medio_pago_pw: d.id_medio_pago_pw
                          	    },
                          success: this.successSinc,
                          failure: this.conexionFailure,
                          timeout: this.timeout,
                          scope: this
                      });

          	},

            successSinc:function(resp){
                      Phx.CP.loadingHide();
                      var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                      if(!reg.ROOT.error){
                      	if(this.wOt){
                      		this.wOt.hide();
                      	}
                      	if(this.wAuto){
                      		this.wAuto.hide();
                      	}

                          this.reload();
                       }else{
                          alert('ocurrio un error durante el proceso')
                      }
              },

            Atributos:[
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_medio_pago_pw'
                    },
                    type:'Field',
                    form:true
                },

                // {
                //     config:{
                //         name: 'medio_pago_id',
                //         fieldLabel: 'Medio Pago ID',
                //         allowBlank: true,
                //         anchor: '80%',
                //         gwidth: 100,
                //         maxLength:4
                //     },
                //     type:'NumberField',
                //     filters:{pfiltro:'mppw.medio_pago_id',type:'numeric'},
                //     id_grupo:1,
                //     grid:true,
                //     form:false
                // },
                // {
                //     config:{
                //         name: 'forma_pago_id',
                //         fieldLabel: 'Forma Pago ID',
                //         allowBlank: true,
                //         anchor: '80%',
                //         gwidth: 100,
                //         maxLength:4
                //     },
                //     type:'NumberField',
                //     filters:{pfiltro:'mppw.forma_pago_id',type:'numeric'},
                //     id_grupo:1,
                //     grid:true,
                //     form:true
                // },

                {
            				config: {
            						name: 'forma_pago_id',
            						fieldLabel: 'Forma Pago',
            						allowBlank: true,
            						width:200,
                        anchor: '80%',
            						emptyText: 'Elija la forma de pago relacionada...',
            						store: new Ext.data.JsonStore({
            								url: '../../sis_obingresos/control/FormaPagoPw/listarFormaPagoPw',
            								id: 'id_forma_pago_pw',
            								root: 'datos',
            								sortInfo: {
            										field: 'id_forma_pago_pw',
            										direction: 'DESC'
            								},
            								totalProperty: 'total',
            								fields: ['id_forma_pago_pw', 'name','fop_code'],
            								remoteSort: true,
            								baseParams: {par_filtro: 'mppw.name'}
            						}),
            						valueField: 'id_forma_pago_pw',
            						gdisplayField : 'nombre_fp',
            						displayField: 'name',
            						hiddenName: 'id_forma_pago_pw',
            						tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:red; font-weight:bold;"><b style="color:Black">Nombre:</b> {name}</p><p style="color:green; font-weight:bold;"><b style="color:Black">Cod:</b> {fop_code}</p></div></tpl>',
            						forceSelection: true,
            						typeAhead: false,
                        triggerAction: 'all',
            						lazyRender: true,
            						mode: 'remote',
            						pageSize: 15,
            						queryDelay: 1000,
            						disabled:false,
            						minChars: 2,
                        gwidth: 200,
                        listWidth:'500',
                        // renderer: function(value, p, record){
                        //   if (record.data['fk_id_movimiento_entidad'] != null && record.data['fk_id_movimiento_entidad'] != '') {
                        //     return String.format('<b style="color:blue; ">{0}</b>', record.data['desc_asociar']);
                        //   } else {
                        //     return String.format('<b>{0}</b>', '');
                        //   }
                        //
                        // },
            				},
            				type: 'ComboBox',
            				id_grupo: 1,
            				form: true,
                    grid:true
            		},


                {
                    config:{
                        name: 'name',
                        fieldLabel: 'Nombre',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:100
                    },
                    type:'TextField',
                    filters:{pfiltro:'mppw.name',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'mop_code',
                        fieldLabel: 'Codigo MOP',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:20
                    },
                    type:'TextField',
                    filters:{pfiltro:'mppw.mop_code',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'code',
                        fieldLabel: 'Codigo',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:15
                    },
                    type:'TextField',
                    filters:{pfiltro:'mppw.code',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                /*Aumentando para las autorizaciones*/
                {
            			config:{
            				name: 'sw_autorizacion',
            				fieldLabel: 'Autorizaciones',
            				allowBlank: true,
            				anchor: '80%',
            				gwidth: 200,
            				maxLength:500
            			},
            			type:'TextArea',
            			filters: {pfiltro:'mppw.sw_autorizacion', type:'string'},

            			id_grupo:1,
            			grid:true,
            			form:false
            		 },
            		 {
            		 config:{
            			 name: 'regionales',
            			 fieldLabel: 'Regionales',
            			 allowBlank: true,
            			 anchor: '80%',
            			 gwidth: 200,
            			 maxLength:500
            		 },
            		 type:'TextArea',
            		 filters: {pfiltro:'mppw.regionales', type:'string'},

            		 id_grupo:1,
            		 grid:true,
            		 form:false
            		},
                /***********************************/
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
                    filters:{pfiltro:'mppw.estado_reg',type:'string'},
                    id_grupo:1,
                    grid:true,
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
                    filters:{pfiltro:'usu1.cuenta',type:'string'},
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
                    filters:{pfiltro:'mppw.fecha_reg',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'id_usuario_ai',
                        fieldLabel: 'Fecha creación',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:4
                    },
                    type:'Field',
                    filters:{pfiltro:'mppw.id_usuario_ai',type:'numeric'},
                    id_grupo:1,
                    grid:false,
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
                    filters:{pfiltro:'mppw.usuario_ai',type:'string'},
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
                    filters:{pfiltro:'usu2.cuenta',type:'string'},
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
                    filters:{pfiltro:'mppw.fecha_mod',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:false
                }
            ],
            tam_pag:50,
            title:'Medio Pago P W',
            ActSave:'../../sis_obingresos/control/MedioPagoPw/insertarMedioPagoPw',
            ActDel:'../../sis_obingresos/control/MedioPagoPw/eliminarMedioPagoPw',
            ActList:'../../sis_obingresos/control/MedioPagoPw/listarMedioPagoPw',
            id_store:'id_medio_pago_pw',
            fields: [
                {name:'id_medio_pago_pw', type: 'numeric'},
                {name:'estado_reg', type: 'string'},
                //{name:'medio_pago_id', type: 'numeric'},
                {name:'forma_pago_id', type: 'numeric'},
                {name:'name', type: 'string'},
                {name:'mop_code', type: 'string'},
                {name:'code', type: 'string'},
                {name:'id_usuario_reg', type: 'numeric'},
                {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'id_usuario_ai', type: 'numeric'},
                {name:'usuario_ai', type: 'string'},
                {name:'id_usuario_mod', type: 'numeric'},
                {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'usr_reg', type: 'string'},
                {name:'usr_mod', type: 'string'},
                {name:'regionales', type: 'string'},
                {name:'sw_autorizacion', type: 'string'},
                {name:'nombre_fp', type: 'string'},

            ],
            sortInfo:{
                field: 'id_medio_pago_pw',
                direction: 'ASC'
            },
            bdel:true,
            bsave:true
        }
    )
</script>
