<?php
/**
 *@package pXP
 *@file gen-MovimientoEntidad.php
 *@author  (jrivera)
 *@date 17-05-2017 15:53:35
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.MovimientoEntidad=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.maestro=config.maestro;

                //llama al constructor de la clase padre
                Phx.vista.MovimientoEntidad.superclass.constructor.call(this,config);
                this.init();
                this.addButton('archivo', {
                    grupo: [0,1],
                    argument: {imprimir: 'archivo'},
                    text: 'Respaldos',
                    iconCls:'blist' ,
                    disabled: false,
                    handler: this.archivo
                });
                this.addButton('Report',{
                    grupo:[0,1],
                    text :'Estado Cuenta',
                    iconCls : 'bpdf32',
                    disabled: false,
                    handler : this.onButtonReporte,
                    tooltip : '<b>Resumen Estado Cuenta Corriente</b>'
                });
                this.store.baseParams.id_entidad = this.maestro.id_agencia;
                if('id_periodo_venta' in this.maestro){
                    this.store.baseParams.id_periodo_venta = this.maestro.id_periodo_venta;
                    this.Cmp.fk_id_movimiento_entidad.store.baseParams.id_periodo_venta = this.maestro.id_periodo_venta;
                }
                this.load({params:{start:0, limit:this.tam_pag}})
            },
            nombreVista: 'MovimientoEntidad',
            archivo : function (){
                var rec = this.getSelectedData();

                console.log('deposito moe:', rec.autorizacion__nro_deposito);
                console.log('deposito depo:', rec.nro_deposito);
                /*//enviamos el id seleccionado para cual el archivo se deba subir
                rec.datos_extras_id = rec.id_movimiento_entidad;
                //enviamos el nombre de la tabla
                rec.datos_extras_tabla = 'obingresos.tmovimiento_entidad';
                //enviamos el codigo ya que una tabla puede tener varios archivos diferentes como ci,pasaporte,contrato,slider,fotos,etc
                rec.datos_extras_codigo = 'ESCANMOVRESP';*/
                if (rec.autorizacion__nro_deposito = rec.nro_deposito){
                    //enviamos el id seleccionado para cual el archivo se deba subir
                    rec.datos_extras_id = rec.id_deposito;
                    //enviamos el nombre de la tabla
                    rec.datos_extras_tabla = 'obingresos.tdeposito';
                    //enviamos el codigo ya que una tabla puede tener varios archivos diferentes como ci,pasaporte,contrato,slider,fotos,etc
                    rec.datos_extras_codigo = 'ESCANDEP';
                }else {
                    //enviamos el id seleccionado para cual el archivo se deba subir
                    rec.datos_extras_id = rec.id_movimiento_entidad;
                    //enviamos el nombre de la tabla
                    rec.datos_extras_tabla = 'obingresos.tmovimiento_entidad';
                    //enviamos el codigo ya que una tabla puede tener varios archivos diferentes como ci,pasaporte,contrato,slider,fotos,etc
                    rec.datos_extras_codigo = 'ESCANMOVRESP';
                }

                Phx.CP.loadWindows('../../../sis_parametros/vista/archivo/Archivo.php',
                    'Archivo',
                    {
                        width: 900,
                        height: 600
                    }, rec, this.idContenedor, 'Archivo');
            },
            onButtonReporte:function(){
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url:'../../sis_obingresos/control/ReporteCuenta/listarReporteCuenta',
                    params:{'id_agencia':this.maestro.id_agencia},
                    success: this.successExport,
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });
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
                    config:{
                        store:['debito','credito'],
                        typeAhead: false,
                        allowBlank : false,
                        name: 'tipo',
                        fieldLabel: 'Tipo',
                        mode: 'local',
                        emptyText:'Tipo...',
                        triggerAction: 'all',
                        lazyRender:true,
                        width:135
                    },
                    type:'ComboBox',
                    filters:{
                        pfiltro:'moe.tipo',
                        type: 'list',
                        options: ['debito','credito']
                    },
                    id_grupo:1,
                    grid:false,
                    form:true
                },

                {
                    config:{
                        store:['si','no'],
                        typeAhead: false,
                        allowBlank : false,
                        name: 'garantia',
                        fieldLabel: 'Es garantia',
                        mode: 'local',
                        emptyText:'Tipo...',
                        triggerAction: 'all',
                        lazyRender:true,
                        gwidth:80,
                        readOnly:true
                    },
                    type:'ComboBox',
                    filters:{
                        pfiltro:'moe.garantia',
                        type: 'list',
                        options: ['si','no']
                    },
                    id_grupo:1,
                    grid:true,
                    form:true,
                    valorInicial : 'no'
                },

                {
                    config:{
                        store:['si','no'],
                        typeAhead: false,
                        allowBlank : false,
                        name: 'ajuste',
                        fieldLabel: 'Es ajuste',
                        mode: 'local',
                        emptyText:'Tipo...',
                        triggerAction: 'all',
                        lazyRender:true,
                        gwidth:80
                    },
                    type:'ComboBox',
                    filters:{
                        pfiltro:'moe.ajuste',
                        type: 'list',
                        options: ['si','no']
                    },
                    id_grupo:1,
                    grid:true,
                    form:false
                },


                {
                    config:{
                        name: 'fecha',
                        fieldLabel: 'Fecha',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 85,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'moe.fecha',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'autorizacion__nro_deposito',
                        fieldLabel: '#Aut o #Depo',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:200,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('{0}', value);
                            }
                            else{
                                return '<b><p align="right">Total: &nbsp;&nbsp; </p></b>';
                            }
                        }
                    },
                    type:'TextArea',
                    filters:{pfiltro:'moe.autorizacion__nro_deposito',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'credito_mb',
                        fieldLabel: 'Credito MB',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:1179650,
                        galign: 'right',
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                            }
                            else{
                                return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
                            }
                        }
                    },
                    type:'NumberField',
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'debito_mb',
                        fieldLabel: 'Debito MB',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:1179650,
                        galign: 'right',
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                            }
                            else{
                                return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
                            }
                        }
                    },
                    type:'NumberField',
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'pnr',
                        fieldLabel: 'Pnr',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 120,
                        maxLength:8,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('{0}', value);
                            }
                            //else{
                            //    return '<b><p align="right">'+record.data.pnr+': </p></b>';
                            //}
                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'moe.pnr',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'apellido',
                        fieldLabel: 'Apellido',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 120,
                        maxLength:200,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('{0}', value);
                            } //else if (record.data.debito < 0) {
                              //  return  String.format('<p align="right"><b><font size=2 color="red">{0}</font><b></p>', Ext.util.Format.number(record.data.debito*-1,'0,000.00'));
                          //  }
                            //else{
                              //  return  String.format('<p align="right"><b><font size=2>{0}</font><b></p>', Ext.util.Format.number(record.data.debito,'0,000.00'));
                          //  }
                        },
                        /*renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary') {
                                return  String.format('{0}', value);
                            } else if (record.data.deudas*-1 > 0) {
                                return  String.format('<p align="right"><b><font size=2 color="red">{0}</font><b></p>', Ext.util.Format.number(record.data.deudas*-1,'0,000.00'));
                            }
                            else{
                                return  String.format('<p align="right"><b><font size=2>{0}</font><b></p>', Ext.util.Format.number(record.data.deudas*-1,'0,000.00'));
                            }
                        },*/
                        scope:this
                    },
                    type:'TextField',
                    filters:{pfiltro:'moe.apellido',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },

                {
                    config:{
                        name:'id_moneda',
                        origen:'MONEDA',
                        allowBlank: false ,
                        width: '80%',
                        fieldLabel: 'Moneda',
                        gdisplayField : 'moneda',
                        renderer:function (value, p, record){
                            if(record.data.tipo_reg != 'summary') {
                                return String.format('{0}', record.data['moneda']);
                             }//else{
                            //     return '<b><p align="right">Deuda P. Anterior: &nbsp;&nbsp; </p></b>';
                            // }
                        }

                    },
                    type: 'ComboRec',
                    id_grupo: 0,
                    filters:{pfiltro:'mon.codigo_internacional',type:'string'},
                    form: true,
                    grid:true
                },
                {
                    config:{
                        name: 'credito',
                        fieldLabel: 'Credito',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:1179650,
                        galign: 'right',
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary') {
                                return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                            } else if (record.data.deudas*-1 > 0) {
                                return  String.format('<p align="right"><b><font size=2 color="red">{0}</font><b></p>', Ext.util.Format.number(record.data.deudas*-1,'0,000.00'));
                            }
                            // else{
                            //     return  String.format('<p align="right"><b><font size=2>{0}</font><b></p>', Ext.util.Format.number(record.data.deudas*-1,'0,000.00'));
                            // }
                        },
                        scope:this
                        /*renderer:function (value,p,record){
                                if(record.data.tipo_reg != 'summary'){
                                    return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                                }
                            }*/
                    },
                    type:'NumberField',
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'debito',
                        fieldLabel: 'Debito',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:1179650,
                        galign: 'right',
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                            } else{
                                return '<b><p align="right">Saldo General: &nbsp;&nbsp; </p></b>';
                            }
                        }
                    },
                    type:'NumberField',
                    id_grupo:1,
                    grid:true,
                    form:false
                },



                {
                    config:{
                        name: 'monto_total',
                        fieldLabel: 'Monto Total',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:1179650,
                        galign: 'right',
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('{0}', value);
                            }
                            else{
                                return  String.format('<p align="right"><b><font size=2 >{0}</font><b></p>', Ext.util.Format.number((record.data.credito_mb - record.data.debito_mb - (record.data.deudas*-1)) ,'0,000.00'));
                            }
                        }
                    },
                    type:'NumberField',
                    filters:{pfiltro:'moe.monto_total',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'monto',
                        fieldLabel: 'Monto',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:1179650
                    },
                    type:'NumberField',
                    filters:{pfiltro:'moe.monto',type:'numeric'},
                    id_grupo:1,
                    grid:false,
                    form:true
                },
                {
            				config: {
            						name: 'fk_id_movimiento_entidad',
            						fieldLabel: 'Movimiento Relacionado',
            						allowBlank: true,
            						width:200,
                        anchor: '80%',
            						emptyText: 'Elija un Movimiento...',
            						store: new Ext.data.JsonStore({
            								url: '../../sis_obingresos/control/MovimientoEntidad/listarMovimientoEntidadAsociar',
            								id: 'id_movimiento_entidad',
            								root: 'datos',
            								sortInfo: {
            										field: 'id_movimiento_entidad',
            										direction: 'DESC'
            								},
            								totalProperty: 'total',
            								fields: ['id_movimiento_entidad', 'tipo','autorizacion__nro_deposito', 'desc_asociar', 'monto', 'nro_deposito_boa'],
            								remoteSort: true,
            								baseParams: {par_filtro: 'moe.autorizacion__nro_deposito#depo.nro_deposito_boa'
                                        }
            						}),
            						valueField: 'id_movimiento_entidad',
            						gdisplayField : 'desc_asociar',
            						displayField: 'autorizacion__nro_deposito',
            						hiddenName: 'id_movimiento_entidad',
            						tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:red; font-weight:bold;"><b style="color:Black">ID:</b> {id_movimiento_entidad}</p><p style="color:green; font-weight:bold;"><b style="color:Black">TIPO:</b> {tipo}</p><p style="color:blue; font-weight:bold;"><b style="color:black; font-weight:bold;">Nro Déposito:</b> {autorizacion__nro_deposito}</p><p style="color:blue; font-weight:bold;"><b style="color:black; font-weight:bold;">Nro Déposito BOA:</b> {nro_deposito_boa}</p><p style="color:green; font-weight:bold;"><b style="color:black; ">Monto:</b> {monto}</p></div></tpl>',
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
                        renderer: function(value, p, record){
                          if (record.data['fk_id_movimiento_entidad'] != null && record.data['fk_id_movimiento_entidad'] != '') {
                            return String.format('<b style="color:blue; ">{0}</b>', record.data['desc_asociar']);
                          } else {
                            return String.format('<b>{0}</b>', '');
                          }

                        },
            				},
            				type: 'ComboBox',
            				id_grupo: 1,
            				form: true,
                    grid:true
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
                    filters:{pfiltro:'moe.estado_reg',type:'string'},
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
                    filters:{pfiltro:'moe.fecha_reg',type:'date'},
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
                    filters:{pfiltro:'moe.usuario_ai',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'id_usuario_ai',
                        fieldLabel: 'Funcionaro AI',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:4
                    },
                    type:'Field',
                    filters:{pfiltro:'moe.id_usuario_ai',type:'numeric'},
                    id_grupo:1,
                    grid:false,
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
                    filters:{pfiltro:'moe.fecha_mod',type:'date'},
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
                }
            ],
            tam_pag:50,
            title:'Movimientos',
            ActSave:'../../sis_obingresos/control/MovimientoEntidad/insertarMovimientoEntidad',
            ActDel:'../../sis_obingresos/control/MovimientoEntidad/eliminarMovimientoEntidad',
            ActList:'../../sis_obingresos/control/MovimientoEntidad/listarMovimientoEntidad',
            id_store:'id_movimiento_entidad',
            fields: [
                {name:'id_movimiento_entidad', type: 'numeric'},
                {name:'id_moneda', type: 'numeric'},
                {name:'id_periodo_venta', type: 'numeric'},
                {name:'id_agencia', type: 'numeric'},
                {name:'garantia', type: 'string'},
                {name:'moneda', type: 'string'},
                {name:'monto_total', type: 'numeric'},
                {name:'tipo', type: 'string'},
                {name:'autorizacion__nro_deposito', type: 'string'},
                {name:'estado_reg', type: 'string'},
                {name:'credito', type: 'numeric'},
                {name:'debito', type: 'numeric'},
                {name:'credito_mb', type: 'numeric'},
                {name:'debito_mb', type: 'numeric'},
                {name:'deudas', type: 'numeric'},
                {name:'ajuste', type: 'string'},
                {name:'fecha', type: 'date',dateFormat:'Y-m-d'},
                {name:'pnr', type: 'string'},
                {name:'apellido', type: 'string'},
                {name:'tipo_reg', type: 'numeric'},
                {name:'id_usuario_reg', type: 'numeric'},
                {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'usuario_ai', type: 'string'},
                {name:'id_usuario_ai', type: 'numeric'},
                {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'id_usuario_mod', type: 'numeric'},
                {name:'usr_reg', type: 'string'},
                {name:'usr_mod', type: 'string'},
                {name:'monto', type: 'numeric'},
                {name:'saldo_actual', type: 'numeric'},
                {name:'nro_deposito', type: 'string'},
                {name:'id_deposito', type: 'numeric'},
                {name:'desc_asociar', type: 'string'},
                {name:'fk_id_movimiento_entidad', type: 'numeric'},


            ],
            preparaMenu: function () {
              //this.getBoton('archivo').disable();
                Phx.vista.MovimientoEntidad.superclass.preparaMenu.call(this);
                var tb = this.tbar;
                //console.log('rec',rec,this.idContenedor,this.tbar,Ext.getCmp('b-edit-' + this.idContenedor));
                if (this.sm.getSelected().data['ajuste'] == 'si'){
                    //Phx.vista.MovimientoEntidad.superclass.preparaMenu.call(this);
                    tb.items.get('b-edit-' + this.idContenedor).enable();}//else{tb.items.get('b-edit-' + this.idContenedor).disable();}

            },

            liberaMenu: function () {
                //this.getBoton('archivo').enable();
                Phx.vista.MovimientoEntidad.superclass.liberaMenu.call(this);
            },
            arrayDefaultColumHidden:[
                'fecha_mod','usr_reg','estado_reg','usr_mod','usuario_ai'],

            sortInfo:{
                field: 'fecha,id_movimiento_entidad',
                direction: 'ASC'
            },
            bdel:false,
            bedit:true,
            bsave:false,
            bnew:true,
            loadValoresIniciales:function(){
                this.Cmp.id_agencia.setValue(this.maestro.id_agencia);
                this.Cmp.garantia.setValue('no');
                this.Cmp.fk_id_movimiento_entidad.store.baseParams.id_entidad = this.maestro.id_agencia;

            },
            onButtonNew: function(){
                //this.Cmp.fecha.getDate(fecha.now());
                var currentDay = new Date().format('d/m/Y');
                //var aux = [currentDay.getDay(), currentDay.getMonth(), currentDay.getFullYear()].join('/');
                //var res = toString(aux);
                // aux = currentDay(;
                console.log(currentDay);

                //this.Cmp.fecha.datepicker('setDate', currentDay);
                this.Cmp.fecha.disable();
                Phx.vista.MovimientoEntidad.superclass.onButtonNew.call(this);
                this.Cmp.fecha.setValue(currentDay);
                this.Cmp.id_movimiento_entidad.setValue();
            },
            onButtonEdit: function(){
                var rec = this.sm.getSelected();
                var data = rec.data;
                //this.Cmp.fecha.disable();
                if(data.ajuste == 'si'){
                    this.Cmp.fecha.disable();
                    Phx.vista.MovimientoEntidad.superclass.onButtonEdit.call(this);

                    /*Para editar si es que existe*/
                    //this.Cmp.fk_id_movimiento_entidad.setValue(data.fk_id_movimiento_entidad);
                      console.log('es ajuste', data);
                      this.Cmp.fk_id_movimiento_entidad.setValue(data.fk_id_movimiento_entidad);
                      this.Cmp.fk_id_movimiento_entidad.store.load({params:{start:0,limit:50},
                         callback : function (r) {
                            this.Cmp.fk_id_movimiento_entidad.fireEvent('select',this.Cmp.fk_id_movimiento_entidad, this.Cmp.fk_id_movimiento_entidad.store.getById(data.fk_id_movimiento_entidad));
                          }, scope : this
                      });
                    /*******************************************/
                    console.log('es ajuste', data.ajuste);

                }else
                {console.log(data.ajuste, 'es ajuste');
                }
                //this.Cmp.fecha.enable();
            },
            south:{
                url:'../../../sis_obingresos/vista/detalle_boletos_web/DetalleBoletosWeb.php',
                title:'Billetes',
                height:'35%',
                cls:'DetalleBoletosWeb',
                collapsed:false
            }
        }
    )
</script>
