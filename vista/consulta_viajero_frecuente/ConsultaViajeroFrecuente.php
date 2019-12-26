<?php
/**
 *@package pXP
 *@file gen-ConsultaViajeroFrecuente.php
 *@author  (miguel.mamani)
 *@date 15-12-2017 14:59:25
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
include_once ('../../media/stylesVoucherElevate.php');
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ConsultaViajeroFrecuente=Ext.extend(Phx.gridInterfaz,{
        /*Aumentando para maquillar interfaz 29/11/2019 (Ismael Valdivia)*/
        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                if(record.data.estado == 'Verificado'){
                    return 'Verificado';
                } else if (record.data.estado == 'Canjeado') {
                    return 'Canjeado';
                } else if (record.data.estado == 'No Canjeado') {
                    return 'No_Canjeado';
                }
            }
        },
        /******************************************************************/


        codSist : 'PXP',
        constructor:function(config){
            this.maestro = config.maestro;
            //llama al constructor de la clase padre
            Phx.vista.ConsultaViajeroFrecuente.superclass.constructor.call(this, config);
            this.init();
            this.bbar.el.dom.style.background='#7FB3D5';
        		this.tbar.el.dom.style.background='#7FB3D5';
        		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#EBF5FB';
        		this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#AED6F1';

            //this.load({params:{start:0, limit:this.tam_pag}})
              this.addButton('btnBoleto',
                {
                    text: 'Canjear Boleto',
                    iconCls: 'blist',
                    disabled: true,
                    handler: this.onButtonBoVendido,
                    tooltip: '<b>Asociar Boleto a voucher</b>'
                }
            );
            this.bloquearOrdenamientoGrid();
            /*Aumentando en la cabezera el estado*/
            this.mostrar_estado = new Ext.form.Label({
                name: 'estado',
                //grupo: this.bactGroups,
                fieldLabel: 'Estado',
                readOnly:true,
                anchor: '150%',
                gwidth: 150,
                format: 'd/m/Y',
                hidden : false,
                //style: 'font-size: 170%; font-weight: bold; background-image: none;'
                style: {
                  fontSize:'40px',
                  fontWeight:'bold',
                  color:'blue',
                  textShadow: '0.5px 0.5px 0px #FFFFFF, 1px 0px 0px rgba(0,0,0,0.15)',
                  marginLeft:'20px'
                }
            });

            this.tbar.addField(this.mostrar_estado);
            this.cmbVoucher.on('select', function () {
                if (this.validarFiltros()) {
                    this.capturaFiltros();
                }
            }, this);

            this.load({params: {start: 0, limit: this.tam_pag}})
        },

        cmbVoucher: new Ext.form.ComboBox({
            fieldLabel: 'Voucher',
            allowBlank: false,
            emptyText: 'Prueba...',//({
            store: new Ext.data.JsonStore({

                url: '../../sis_obingresos/control/ConsultaViajeroFrecuente/listarConsultaViajeroFrecuente',
                id: 'id_consulta_viajero_frecuente',
                root: 'datos',
                sortInfo: {
                    field: 'Voucher',
                    direction: 'VIF'
                },
                totalProperty: 'total',
                fields: [
                    {name: 'id_consulta_viajero_frecuente'},
                    {name: 'ffid', type: 'string'},
                    {name: 'voucher_code', type: 'string'},
                    {name: 'nro_boleto', type: 'string'},
                    {name: 'pnr', type: 'string'},
                    {name: 'status', type: 'string'}
                ],
                remoteSort: true,
                baseParams: {start: 0, limit: 10}
            }),
            displayField: 'voucher',
            valueField: 'id_consulta_viajero_frecuente',
            //typeAhead: false,
            hiddenName: 'id_consulta_viajero_frecuente',
            mode: 'remote',
            triggerAction: 'all',
            //emptyText:'Entidad...',
            //selectOnFocus:true,
            width: 135,
            resizable: true
        }),

        onbuttonAct: function () {
            //if (!this.validarFiltros()) {
            //  alert('especifique el Voucher')
            //}
            //else {
            this.store.baseParams.id_consulta_viajero_frecuente = this.cmbVoucher.getValue();
            Phx.vista.ConsultaViajeroFrecuente.superclass.onButtonEdit.call(this);
            //}
        },



        Atributos: [
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'id_consulta_viajero_frecuente'
                },
                type: 'Field',
                form: true
            },
            {
                config: {
                    name: 'ffid',
                    fieldLabel: 'FFID',
                    allowBlank: true,
                    width: 150,
                    gwidth: 150,
                    maxLength: 10,
                    minLength: 10,
                    style: 'background-color: #F39E8C; background-image: none;'
                },
                type:'NumberField',
                filters:{pfiltro:'vif.ffid',type:'numeric'},
                id_grupo:1,
                grid:true,
                form:true,
                bottom_filter : true
            },
            {
                config: {
                    name: 'voucher_code',
                    fieldLabel: 'Voucher Code',
                    allowBlank: true,
                    width: 150,
                    gwidth: 200,
                    maxLength: 10,
                    minLength: 10,
                    style: 'background-color: #F39E8C; background-image: none;'
                },
                type: 'TextField',
                filters: {pfiltro: 'vif.voucher_code', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter : true
            },
            {
                config: {
                    name: 'message',
                    fieldLabel: 'Mensaje Verificación',
                    allowBlank: true,
                    width: '80%',
                    gwidth: 200,
                    maxLength: 200
                },
                type: 'TextField',
                filters: {pfiltro: 'vif.message', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'status',
                    fieldLabel: 'Estado de Verificación',
                    allowBlank: true,
                    width: '80%',
                    gwidth: 200,
                    maxLength: 20,
                    renderer: function (value, p, record) {
                        if (record.data['status'] == 'NOK') {
                            return String.format('<div title="Anulado"><b><font color="red"><i class="fa fa-times-circle" aria-hidden="true" style="font-size:12px;"></i> {0}</font></b></div>', value);

                        } else {
                            return String.format('<div title="Activo"><b><font color="green"><i class="fa fa-thumbs-up" aria-hidden="true" style="font-size:12px;"></i> {0}</font></b></div>', value);
                        }
                    }
                },
                type: 'TextField',
                filters: {pfiltro: 'vif.status', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            /*Aumentando estos campos para verificar si se hace el canje Isamel Valdivia (2/12/2019)*/
            {
                config: {
                    name: 'message_canjeado',
                    fieldLabel: 'Mensaje Canjeado',
                    allowBlank: true,
                    width: '80%',
                    gwidth: 200,
                    maxLength: 200
                },
                type: 'TextField',
                filters: {pfiltro: 'vif.message_canjeado', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'status_canjeado',
                    fieldLabel: 'Estado Canjeado',
                    allowBlank: true,
                    width: '80%',
                    gwidth: 200,
                    maxLength: 20,
                    renderer: function (value, p, record) {
                        console.log("recor data irbva",record.data);
                        if (record.data['status_canjeado'] == 'NOK' && record.data['status_canjeado'] != '' ) {
                            return String.format('<div title="Anulado"><b><font color="red"><i class="fa fa-times-circle" aria-hidden="true" style="font-size:12px;"></i> {0}</font></b></div>', value);

                        } else if (record.data['status_canjeado'] == 'OK' && record.data['status_canjeado'] != '') {
                            return String.format('<div title="Activo"><b><font color="green"><i class="fa fa-thumbs-up" aria-hidden="true" style="font-size:12px;"></i> {0}</font></b></div>', value);
                        } else {
                          return String.format('<div title="Activo"><b><font color="green"> {0}</font></b></div>', value);

                        }
                    }
                },
                type: 'TextField',
                filters: {pfiltro: 'vif.status_canjeado', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            /**********************************************************************************/
            {
                config: {
                    name: 'nro_boleto',
                    fieldLabel: 'Boleto (930-)',
                    //emptyText: '...',
                    allowBlank: true,
                    Text: '930-',
                    width: 150,
                    gwidth: 100,
                    maxLength: 10,
                    minLength: 10,
                    style: 'background-color:#9BF592 ; background-image: none;',
                    //style: 'background-color: #F39E8C; background-image: none;',
                    renderer: function (value, p, record) {
                        if (record.data['estado_reg'] == 'activo') {
                            return String.format('<div title="Activo"><b><font color="blue">{0}</font></b></div>', value);

                        } else {
                            return String.format('<div title="Anulado"><b><font color="red">{0}</font></b></div>', value);
                        }
                    }
                },
                type: 'NumberField',
                filters: {pfiltro: 'vif.nro_boleto', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter : true
            },
            /*Aumentando el PNR 28/11/2019 (Ismael Valdivia)*/
            {
                config: {
                    name: 'pnr',
                    fieldLabel: 'PNR',
                    allowBlank: true,
                    width: 150,
                    gwidth: 100,
                    maxLength: 6,
                    style: 'background-color:#9BF592 ; background-image: none;',
                    renderer: function (value, p, record) {
                        if (record.data['estado_reg'] == 'activo') {
                            return String.format('<div title="Activo"><b><font color="blue">{0}</font></b></div>', value);

                        } else {
                            return String.format('<div title="Anulado"><b><font color="red">{0}</font></b></div>', value);
                        }
                    }
                },
                type: 'TextField',
                filters: {pfiltro: 'vif.pnr', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter : true
            },
            {
                config: {
                    name: 'estado',
                    fieldLabel: 'Estado Voucher',
                    allowBlank: true,
                    width: 150,
                    gwidth: 100,
                    maxLength: 6,
                    style: 'background-color:#9BF592 ; background-image: none;',
                    renderer: function (value, p, record) {
                        if (record.data['estado'] == 'Verificado') {
                            return String.format('<div title="Verificado"><b><i class="fa fa-check-circle-o" aria-hidden="true" style="font-size:12px;"></i> {0}</b></div>', value);

                        } else if (record.data['estado'] == 'Canjeado') {
                            return String.format('<div title="Verificado"><b style="color:green;"><i class="fa fa-refresh" aria-hidden="true" style="font-size:12px;"></i> {0}</b></div>', value);

                        } else if (record.data['estado'] == 'Encontrado') {
                            return String.format('<div title="Encontrado"><b style="color:green;"><i class="fa fa-refresh" aria-hidden="true" style="font-size:12px;"></i> {0}</b></div>', value);

                        } else if (record.data['estado'] == 'Inexistente') {
                            return String.format('<div title="Inexistente"><b style="color:red;"><i class="fa fa-times-circle" aria-hidden="true" style="font-size:12px;"></i> {0}</b></div>', value);

                        }else if (record.data['estado'] == 'Expirado') {
                            return String.format('<div title="Expirado"><b style="color:red;"><i class="fa fa-times-circle" aria-hidden="true" style="font-size:12px;"></i> {0}</b></div>', value);

                        }else if (record.data['estado'] == 'Consumido') {
                            return String.format('<div title="Consumido"><b style="color:red;"><i class="fa fa-times-circle" aria-hidden="true" style="font-size:12px;"></i> {0}</b></div>', value);
                        }

                        else {
                            return String.format('<div title="Anulado"><b><font color="red">{0}</font></b></div>', value);
                        }
                    }
                },
                type: 'TextField',
                filters: {pfiltro: 'vif.estado', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            /************************************************************/
            {
                config: {
                    name: 'estado_reg',
                    fieldLabel: 'Estado Reg.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 10
                },
                type: 'TextField',
                filters: {pfiltro: 'vif.estado_reg', type: 'string'},
                id_grupo: 1,
                grid: false,
                form: false
            },
            {
                config: {
                    name: 'usr_reg',
                    fieldLabel: 'Creado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 4
                },
                type: 'Field',
                filters: {pfiltro: 'usu1.cuenta', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'fecha_reg',
                    fieldLabel: 'Fecha creación',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    //hidden:true,
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y H:i:s') : ''
                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'vif.fecha_reg', type: 'date'},
                id_grupo: 1,
                grid: true,
                form: true
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
                filters:{pfiltro:'vif.usuario_ai',type:'string'},
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
                filters:{pfiltro:'vif.id_usuario_ai',type:'numeric'},
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
                filters:{pfiltro:'vif.fecha_mod',type:'date'},
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
        tam_pag: 50,
        title: 'Consultar Voucher',
        fheight: 260,
        fwidth: 420,
        ActSave: '../../sis_obingresos/control/ConsultaViajeroFrecuente/insertarConsultaViajeroFrecuente',
        ActDel: '../../sis_obingresos/control/ConsultaViajeroFrecuente/eliminarConsultaViajeroFrecuente',
        ActList: '../../sis_obingresos/control/ConsultaViajeroFrecuente/listarConsultaViajeroFrecuente',
        id_store: 'id_consulta_viajero_frecuente',
        fields: [
            {name: 'id_consulta_viajero_frecuente', type: 'numeric'},
            {name: 'ffid', type: 'numeric'},
            {name: 'estado_reg', type: 'string'},
            {name: 'message', type: 'string'},
            {name: 'message_canjeado', type: 'string'},
            {name: 'voucher_code', type: 'string'},
            {name: 'status', type: 'string'},
            {name: 'status_canjeado', type: 'string'},
            {name: 'nro_boleto', type: 'string'},
            {name: 'pnr', type: 'string'},
            {name: 'estado', type: 'string'},
            {name: 'id_usuario_reg', type: 'numeric'},
            {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
            {name: 'usuario_ai', type: 'string'},
            {name: 'id_usuario_ai', type: 'numeric'},
            {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
            {name: 'id_usuario_mod', type: 'numeric'},
            {name: 'usr_reg', type: 'string'},
            {name: 'usr_mod', type: 'string'}

        ],
        sortInfo: {
            field: 'id_consulta_viajero_frecuente',
            direction: 'DESC'
        },
        btest:false,
        bdel: false,
        bedit: false,
        bsave: false,

        preparaMenu: function () {
            var rec = this.sm.getSelected();
            /*********************************************************/
            this.mostrar_estado.setText(rec.data.estado.toUpperCase());
            if (rec.data.estado == 'Canjeado') {
              this.mostrar_estado.el.dom.style.color = 'green';
              this.mostrar_estado.setText(rec.data.estado.toUpperCase());
            } else {
              this.mostrar_estado.el.dom.style.color = 'blue';
              this.mostrar_estado.setText(rec.data.estado.toUpperCase());
            }

            /*********************************************************/
            if (rec.data.status == 'OK') {
                this.getBoton('btnBoleto').enable();
                Phx.vista.ConsultaViajeroFrecuente.superclass.preparaMenu.call(this);
            }
        },
        liberaMenu : function(){
            this.getBoton('btnBoleto').disable();
            Phx.vista.ConsultaViajeroFrecuente.superclass.liberaMenu.call(this);
        },
        onButtonBoVendido: function () {
            var global = this;
            var rec = this.sm.getSelected();
            var data = rec.data;
            //var msg = 'ok';

            if (data.status == 'OK' ) {
                //global.onButtonEdit();
                if (data.nro_boleto == '') {
                    global.formAdicionBoleto();
                    //global.onButtonEdit();
                }
                else {
                    Ext.Msg.confirm('Confirmacion', 'Desea Editar el Boleto del Voucher Seleccionado', function (btn) {
                        if (btn == 'yes') {
                            global.formAdicionBoleto();
                            //global.onButtonEdit();
                        }
                        else {
                        }
                    });
                }
            }
            else {
                Ext.Msg.alert('<b> el voucher no esta habilitado </b>')
            }

        },

        formAdicionBoleto : function(){
          var rec=this.sm.getSelected();
          var datos_boletos = new Ext.FormPanel({
           labelWidth: 75, // label settings here cascade unless overridden
           frame:true,
           bodyStyle:'padding:5px 5px 0; background:linear-gradient(45deg, #a7cfdf 0%,#a7cfdf 100%,#23538a 100%);',
           width: 300,
           height:200,
           defaultType: 'textfield',
           items: [
                     new Ext.form.NumberField({
                                         name: 'id_consulta_viajero_frecuente',
                                         //msgTarget: 'title',
                                         fieldLabel: 'id viajero',
                                         allowBlank: false,
                                         hidden:true,
                                         disabled:true,
                                         style:{
                                           background: '#F39E8C',
                                           backgroundImage: 'none'
                                         },

                                 }),

                    new Ext.form.NumberField({
                                        name: 'ffid',
                                        //msgTarget: 'title',
                                        fieldLabel: 'FFID',
                                        allowBlank: false,
                                        disabled:true,
                                        style:{
                                          background: '#F39E8C',
                                          backgroundImage: 'none'
                                        },

                                }),
                    new Ext.form.TextField({
                                        name: 'voucher_code',
                                        //msgTarget: 'title',
                                        fieldLabel: 'Voucher Code',
                                        allowBlank: false,
                                        disabled:true,
                                        style:{
                                          background: '#F39E8C',
                                          backgroundImage: 'none'
                                        },

                                }),
                    new Ext.form.NumberField({
                                        name: 'nro_boleto',
                                        //msgTarget: 'title',
                                        fieldLabel: 'Boleto (930-)',
                                        allowBlank: false,
                                        maxLength: 10,
                                        minLength: 10,
                                        style:{
                                          background: '#9BF592',
                                          backgroundImage: 'none'
                                        },

                                }),
                    new Ext.form.TextField({
                                        name: 'pnr',
                                        //msgTarget: 'title',
                                        fieldLabel: 'PNR',
                                        allowBlank: false,
                                        maxLength: 6,
                                        minLength: 6,
                                        style:{
                                          background: '#9BF592',
                                          backgroundImage: 'none',
                                          textTransform:'uppercase'
                                        },

                                }),
                    new Ext.form.TextField({
                                        name: 'fecha_reg',
                                        //msgTarget: 'title',
                                        fieldLabel: 'Fecha reg',
                                        allowBlank: false,
                                        hidden:true,
                                        disabled:true,

                                }),
                    ]

                });
            this.formulario_boletos = datos_boletos;

          var win = new Ext.Window({
            title: '<center><h1 style="font-size:15px; color:#0E00B7; text-shadow: -1px -1px 1px rgba(255,255,255,.1), 1px 1px 1px rgba(0,0,0,.5);"> <img src="../../../lib/imagenes/icono_dibu/dibu_zoom.png" height="20px" style="float:center; vertical-align: middle;"> Canjear Voucher</h1></center>',
            width:315,
            height:290,
            //closeAction:'hide',
            modal:true,
            plain: true,
            items:datos_boletos,
            buttons: [{
                        text:'<i class="fa fa-floppy-o fa-lg"></i> Guardar',
                        scope:this,
                        handler: function(){
                            this.registrar_boleto(win);
                        }
                    },{
                        text: '<i class="fa fa-times-circle fa-lg"></i> Cancelar',
                        handler: function(){
                            win.hide();
                        }
                    }]

          });
          win.show();

          var fecha_registro = rec.data.fecha_reg.dateFormat('d/m/Y');

          this.formulario_boletos.items.items[0].setValue(rec.data.id_consulta_viajero_frecuente);
          this.formulario_boletos.items.items[1].setValue(rec.data.ffid);
          this.formulario_boletos.items.items[2].setValue(rec.data.voucher_code);
          this.formulario_boletos.items.items[3].setValue(rec.data.nro_boleto);
          this.formulario_boletos.items.items[4].setValue(rec.data.pnr);
          this.formulario_boletos.items.items[5].setValue(fecha_registro);

        },


        registrar_boleto : function(win){
          var rec=this.sm.getSelected();
          /*Recuperamos de la venta detalle si existe algun concepto con excento*/
          Ext.Ajax.request({
              url : '../../sis_obingresos/control/ConsultaViajeroFrecuente/insertarConsultaViajeroFrecuente',
              params : {
                'id_consulta_viajero_frecuente' : this.formulario_boletos.items.items[0].getValue(),
                'ffid': this.formulario_boletos.items.items[1].getValue(),
                'voucher_code': this.formulario_boletos.items.items[2].getValue(),
                'nro_boleto': this.formulario_boletos.items.items[3].getValue(),
                'pnr': this.formulario_boletos.items.items[4].getValue(),
                'fecha_reg': this.formulario_boletos.items.items[5].getValue(),
              },
              success : this.successRegistro(win),
              failure : this.conexionFailure,
              timeout : this.timeout,
              scope : this
            });
            // this.reload();
            // win.hide();
          /**********************************************************************/
        },

        successRegistro : function (win) {        
          this.reload();
          win.hide();
        },


        onButtonEdit : function() {

            var rec= this.sm.getSelected();
            var aux ;
            //this.cmb.id_consulta_viajero_frecuente.disable();
            this.Cmp.ffid.disable();
            this.Cmp.voucher_code.disable();
            this.Cmp.nro_boleto.enable();
            this.Cmp.nro_boleto.show();
            //this.Cmp.fecha_reg.hide();

            /*Incluimos el PNR 28/11/2019 (Ismael Valdivia)*/
            this.Cmp.pnr.enable();
            this.Cmp.pnr.show();
            this.Cmp.nro_boleto.allowBlank = false;
            this.Cmp.pnr.allowBlank = false;
            this.Cmp.fecha_reg.hide();
            /***********************************************/
            Phx.vista.ConsultaViajeroFrecuente.superclass.onButtonEdit.call(this);
            this.form.el.dom.firstChild.childNodes[0].style.background = '#7FB3D5';
            //aux = this.Cmp.nro_boleto.getValue();
            //aux = aux.toString();
            //var res = aux.substr();
            var aux = this.Cmp.id_consulta_viajero_frecuente.getValue();

        },
        onButtonNew : function () {
            var aux = this.Cmp.id_consulta_viajero_frecuente.getValue();


            this.Cmp.ffid.enable();
            this.Cmp.voucher_code.enable();
            this.Cmp.nro_boleto.hide();
            this.Cmp.fecha_reg.hide();
            /*Incluimos el PNR 28/11/2019 (Ismael Valdivia)*/
            this.Cmp.pnr.hide();
            this.Cmp.nro_boleto.allowBlank = true;
            this.Cmp.pnr.allowBlank = true;
            /**********************************************/
            //this.Cmp.id_periodo_venta.enable();
            Phx.vista.ConsultaViajeroFrecuente.superclass.onButtonNew.call(this);
            this.form.el.dom.firstChild.childNodes[0].style.background = '#7FB3D5';

            //this.Cmp.id_consulta_viajero_frecuente.reset();
            this.Cmp.id_consulta_viajero_frecuente.setValue();

        },
    })
</script>
