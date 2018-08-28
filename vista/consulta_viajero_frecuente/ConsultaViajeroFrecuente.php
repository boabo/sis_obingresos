<?php
/**
 *@package pXP
 *@file gen-ConsultaViajeroFrecuente.php
 *@author  (miguel.mamani)
 *@date 15-12-2017 14:59:25
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ConsultaViajeroFrecuente=Ext.extend(Phx.gridInterfaz,{
        codSist : 'PXP',
        constructor:function(config){
            this.maestro = config.maestro;
            //llama al constructor de la clase padre
            Phx.vista.ConsultaViajeroFrecuente.superclass.constructor.call(this, config);
            this.init();
            //this.load({params:{start:0, limit:this.tam_pag}})
            this.addButton('btnBoleto',
                {
                    text: 'Adicionar Boleto',
                    iconCls: 'blist',
                    disabled: true,
                    handler: this.onButtonBoVendido,
                    tooltip: '<b>Asociar Boleto a voucher</b>'
                }
            );
            this.bloquearOrdenamientoGrid();
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
                    anchor: '80%',
                    gwidth: 150,
                    maxLength: 10,
                    minLength: 10,
                    style: 'background-color: #F39E8C; background-image: none;'
                },
                type:'NumberField',
                filters:{pfiltro:'vif.ffid',type:'numeric'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config: {
                    name: 'voucher_code',
                    fieldLabel: 'Voucher Code',
                    allowBlank: true,
                    anchor: '80%',
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
                    fieldLabel: 'Message',
                    allowBlank: true,
                    anchor: '80%',
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
                    fieldLabel: 'Status',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 20,
                    renderer: function (value, p, record) {
                        if (record.data['status'] == 'NOK') {
                            return String.format('<div title="Anulado"><b><font color="red">{0}</font></b></div>', value);

                        } else {
                            return String.format('<div title="Activo"><b><font color="green">{0}</font></b></div>', value);
                        }
                    }
                },
                type: 'TextField',
                filters: {pfiltro: 'vif.status', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'nro_boleto',
                    fieldLabel: 'Boleto (930-)',
                    emptyText: '...',
                    allowBlank: true,
                    Text: '930-',
                    anchor: '80%',
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
                grid: true,
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
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y H:i:s') : ''
                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'vif.fecha_reg', type: 'date'},
                id_grupo: 1,
                grid: true,
                form: false
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
        title: 'consulta viajero frecuente',
        ActSave: '../../sis_obingresos/control/ConsultaViajeroFrecuente/insertarConsultaViajeroFrecuente',
        ActDel: '../../sis_obingresos/control/ConsultaViajeroFrecuente/eliminarConsultaViajeroFrecuente',
        ActList: '../../sis_obingresos/control/ConsultaViajeroFrecuente/listarConsultaViajeroFrecuente',
        id_store: 'id_consulta_viajero_frecuente',
        fields: [
            {name: 'id_consulta_viajero_frecuente', type: 'numeric'},
            {name: 'ffid', type: 'numeric'},
            {name: 'estado_reg', type: 'string'},
            {name: 'message', type: 'string'},
            {name: 'voucher_code', type: 'string'},
            {name: 'status', type: 'string'},
            {name: 'nro_boleto', type: 'string'},
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
        Grupos: [
            {
                layout: 'column',
                border: false,
                defaults: {
                    border: false
                },
                items: [
                    {
                        bodyStyle: 'padding-right:10px;',
                        items: [
                            {
                                xtype: 'fieldset',
                                columnWidth: 1,
                                defaults: {
                                    anchor: '-2' // leave room for error icon
                                },
                                title: 'Datos Voucher',
                                items: [],
                                id_grupo: 1
                            }
                        ]
                    }
                ]
            }
        ],
        fheight: 200,
        fwidth: 400,
        preparaMenu: function () {
            var rec = this.sm.getSelected();
            //this.getBoton('btnBoleto').enable();
            if (rec.data.status == 'OK') {
                this.getBoton('btnBoleto').enable();
                Phx.vista.ConsultaViajeroFrecuentesuperclass.preparaMenu.call(this);
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
                    //global.formAdicionBoleto();
                    global.onButtonEdit();
                }
                else {
                    Ext.Msg.confirm('Confirmacion', 'Desea Editar el Boleto del Voucher Seleccionado', function (btn) {
                        if (btn == 'yes') {
                            global.onButtonEdit();
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
            var global = this;
            var rec = this.sm.getSelected();
            var data = rec.data;
            Phx.CP.loadWindows('../../../sis_obingresos/vista/consulta_viajero_frecuente/FomRegistroBoleto.php',
                'Formulario de Registro Boleto',
                {
                    modal:true,
                    width: 450,
                    height:200
                }, data,this.id_consulta_viajero_frecuente,'FomRegistroBoleto'
            )

        },
        onButtonEdit : function() {

            var rec= this.sm.getSelected();
            var aux ;
            //this.cmb.id_consulta_viajero_frecuente.disable();
            this.Cmp.ffid.disable();
            this.Cmp.voucher_code.disable();
            this.Cmp.nro_boleto.enable();
            this.Cmp.nro_boleto.show('930');
            Phx.vista.ConsultaViajeroFrecuente.superclass.onButtonEdit.call(this);
            aux = this.Cmp.nro_boleto.getValue();
            aux = aux.toString();
            var res = aux.substr();
            this.Cmp.nro_boleto.setValue(res);
            console.log(res);
        },
        onButtonNew : function () {
            this.Cmp.ffid.enable();
            this.Cmp.voucher_code.enable();
            this.Cmp.nro_boleto.hide();
            //this.Cmp.id_periodo_venta.enable();
            Phx.vista.ConsultaViajeroFrecuente.superclass.onButtonNew.call(this);

        },
    })
</script>