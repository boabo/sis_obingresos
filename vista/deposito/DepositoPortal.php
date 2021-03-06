<?php
/**
 *@package pXP
 *@file gen-Deposito.php
 *@author  (jrivera)
 *@date 06-01-2016 22:42:28
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.DepositoPortal=Ext.extend(Phx.gridInterfaz, {

        constructor: function (config) {

            //llama al constructor de la clase padre
            Phx.vista.DepositoPortal.superclass.constructor.call(this, config);
            this.init();

            this.addButton('archivo', {
                grupo: [0,1],
                argument: {imprimir: 'archivo'},
                text: 'Archivos Digitales',
                iconCls:'blist' ,
                disabled: false,
                handler: this.archivo
            });

            this.addButton('btnValidar',
                {
                    grupo: [0],
                    text: 'Validar',
                    iconCls: 'bok',
                    disabled: true,
                    handler: this.onValidar,
                    tooltip: 'Valida el deposito registrado'
                }
            );
            this.finCons = true;
            this.store.baseParams.tipo = 'agencia';
            this.store.baseParams.estado = 'borrador';
            this.load({params: {start: 0, limit: this.tam_pag}});
            this.iniciarEventos();

        },
        gruposBarraTareas: [{
            name: 'borrador',
            title: '<H1 align="center"><i class="fa fa-eye"></i> Registrados</h1>',
            grupo: 0,
            height: 0
        }, {
            name: 'validado',
            title: '<H1 align="center"><i class="fa fa-eye"></i> Validados</h1>',
            grupo: 1,
            height: 0
            },
            {
                name: 'eliminado',
                title: '<H1 align="center"><i class="fa fa-eye"></i> Eliminados</h1>',
                grupo: 2,
                height: 0
            }

        ],
        actualizarSegunTab: function (name, indice) {
            if (this.finCons) {
                this.store.baseParams.estado = name;
                this.load({params: {start: 0, limit: this.tam_pag}});
            }
        },

        onValidar: function () {
            var rec = this.sm.getSelected();
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_obingresos/control/Deposito/cambiaEstadoDeposito',
                params: {
                    'id_deposito': rec.data.id_deposito,
                    'accion': 'validado'
                },
                success: this.successSave,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });

        },

        archivo : function (){
            var rec = this.getSelectedData();

            //enviamos el id seleccionado para cual el archivo se deba subir
            rec.datos_extras_id = rec.id_deposito;
            //enviamos el nombre de la tabla
            rec.datos_extras_tabla = 'obingresos.tdeposito';
            //enviamos el codigo ya que una tabla puede tener varios archivos diferentes como ci,pasaporte,contrato,slider,fotos,etc
            rec.datos_extras_codigo = 'ESCANDEP';

            Phx.CP.loadWindows('../../../sis_parametros/vista/archivo/Archivo.php',
                'Archivo',
                {
                    width: 900,
                    height: 400
                }, rec, this.idContenedor, 'Archivo');
        },
        bactGroups: [0,1,2],
        bexcelGroups: [0,1,2],

        Atributos: [
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'id_deposito'
                },
                type: 'Field',
                form: true
            },
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'saldo'
                },
                type: 'Field',
                form: true

            },
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'tipo'
                },
                type: 'Field',
                form: true,
                valorInicial : 'agencia'
            },
            {
                config: {
                    name: 'id_agencia',
                    fieldLabel: 'Agencia',
                    allowBlank: false,
                    emptyText: 'Agencia...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_obingresos/control/Agencia/listarAgencia',
                        id: 'id_agencia',
                        root: 'datos',
                        sortInfo: {
                            field: 'id_agencia',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_agencia', 'codigo', 'codigo_int', 'nombre'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'age.codigo#age.codigo_int#age.nombre'}
                    }),
                    valueField: 'id_agencia',
                    displayField: 'nombre',
                    gdisplayField: 'nombre_agencia',
                    hiddenName: 'id_agencia',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    gwidth: 150,
                    listWidth: 450,
                    resizable: true,
                    minChars: 2,
                    renderer: function (value, p, record) {
                        return String.format('{0}', record.data['nombre_agencia']);
                    },
                    tpl: '<tpl for="."><div class="x-combo-list-item"><p>{nombre}</p>Officeid: <strong>{codigo_int}</strong> </div></tpl>'
                },
                type: 'ComboBox',
                id_grupo: 1,
                filters: {pfiltro: 'age.nombre#age.codigo_int', type: 'string'},
                bottom_filter: true,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'nro_deposito',
                    fieldLabel: 'No Deposito',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength: 70
                },
                type: 'TextField',
                filters: {pfiltro: 'dep.nro_deposito', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter: true
            },
            {
                config: {
                    name: 'nro_deposito_boa',
                    fieldLabel: 'No Deposito Boa',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength: 70
                },
                type: 'TextField',
                filters: {pfiltro: 'dep.nro_deposito_boa', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter: true
            },
            {
                config: {
                    name: 'fecha',
                    fieldLabel: 'Fecha Deposito',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 120,
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y') : ''
                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'dep.fecha', type: 'date'},
                id_grupo: 1,
                grid: true,
                form: true
            },

            {
                config: {
                    name: 'id_moneda_deposito',
                    origen: 'MONEDA',
                    allowBlank: false,
                    fieldLabel: 'Moneda Deposito',
                    gdisplayField: 'desc_moneda',//mapea al store del grid
                    gwidth: 100,
                    renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', record.data['desc_moneda']);
						}
						else{
							return '<b><p align="right">Total: &nbsp;&nbsp; </p></b>';
						}
					}

                },
                type: 'ComboRec',
                id_grupo: 1,
                filters: {
                    pfiltro: 'mon.codigo',
                    type: 'string'
                },
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'monto_deposito',
                    fieldLabel: 'Monto',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength: 1179650,
                    galign:'right',
                    renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.total_deposito,'0,000.00'));
						}
					}
                },
                type: 'NumberField',
                filters: {pfiltro: 'dep.monto_deposito', type: 'numeric'},
                id_grupo: 1,
                grid: true,
                form: true
            },

            {
                config: {
                    name: 'id_periodo_venta',
                    fieldLabel: 'Periodo Venta',
                    allowBlank: true,
                    emptyText: 'Periodo...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_obingresos/control/PeriodoVenta/listarPeriodoVenta',
                        id: 'id_periodo_venta',
                        root: 'datos',
                        sortInfo: {
                            field: 'fecha_fin',
                            direction: 'DESC'
                        },
                        totalProperty: 'total',
                        fields: ['mes', 'fecha_ini', 'fecha_fin', 'tipo_periodo','tipo_cc','medio_pago', 'desc_periodo'],
                        remoteSort: true,
                        baseParams: {   tipo_periodo:'portal',
                                        medio_pago : 'cuenta_corriente'
                                    }
                    }),
                    valueField: 'id_periodo_venta',
                    displayField: 'desc_periodo',
                    gdisplayField: 'desc_periodo',
                    hiddenName: 'id_periodo_venta',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    gwidth: 150,
                    listWidth: 450,
                    resizable: true,
                    minChars: 2,
                    renderer: function (value, p, record) {
                        return String.format('{0}', record.data['desc_periodo']);
                    }
                },
                type: 'ComboBox',
                id_grupo: 1,
                grid: false,
                form: false
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
                filters: {pfiltro: 'dep.estado_reg', type: 'string'},
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
                filters: {pfiltro: 'dep.fecha_reg', type: 'date'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'id_usuario_ai',
                    fieldLabel: 'Fecha creación',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 4
                },
                type: 'Field',
                filters: {pfiltro: 'dep.id_usuario_ai', type: 'numeric'},
                id_grupo: 1,
                grid: false,
                form: false
            },
            {
                config: {
                    name: 'usuario_ai',
                    fieldLabel: 'Funcionaro AI',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 300
                },
                type: 'TextField',
                filters: {pfiltro: 'dep.usuario_ai', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'usr_mod',
                    fieldLabel: 'Modificado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 4
                },
                type: 'Field',
                filters: {pfiltro: 'usu2.cuenta', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'fecha_mod',
                    fieldLabel: 'Fecha Modif.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y H:i:s') : ''
                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'dep.fecha_mod', type: 'date'},
                id_grupo: 1,
                grid: true,
                form: false
            }
        ],
        tam_pag: 50,
        title: 'Depositos',
        ActSave: '../../sis_obingresos/control/Deposito/insertarDeposito',
        ActDel:'../../sis_obingresos/control/Deposito/eliminarDepositoPortal',
        ActList: '../../sis_obingresos/control/Deposito/listarDeposito',
        id_store: 'id_deposito',
        fields: [
            {name: 'id_deposito', type: 'numeric'},
            {name: 'estado_reg', type: 'string'},
            {name: 'nro_deposito', type: 'string'},
            {name: 'nro_deposito_boa', type: 'string'},
            {name: 'desc_moneda', type: 'string'},
            {name: 'tipo_reg', type: 'string'},
            {name: 'desc_periodo', type: 'string'},
            {name: 'medio_pago', type: 'string'},
            {name: 'monto_deposito', type: 'numeric'},
            {name: 'total_deposito', type: 'numeric'},
            {name: 'id_moneda_deposito', type: 'numeric'},
            {name: 'id_agencia', type: 'numeric'},
            {name: 'nombre_agencia', type: 'numeric'},
            {name: 'fecha', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'saldo', type: 'numeric'},
            {name: 'id_usuario_reg', type: 'numeric'},
            {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
            {name: 'id_usuario_ai', type: 'numeric'},
            {name: 'usuario_ai', type: 'string'},
            {name: 'id_usuario_mod', type: 'numeric'},
            {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
            {name: 'usr_reg', type: 'string'},
            {name: 'usr_mod', type: 'string'},
            {name: 'tipo', type: 'string'},
            {name: 'estado', type: 'string'},
            {name: 'id_apertura_cierre_caja', type: 'numeric'}

        ],
        sortInfo: {
            field: 'id_deposito',
            direction: 'DESC'
        },
        iniciarEventos: function () {

        },
        bdel: true,
        beditGroups:[0,1],
        bsave: false,
        bnew: false,
        preparaMenu: function () {
            var rec = this.sm.getSelected();
            Phx.vista.DepositoPortal.superclass.preparaMenu.call(this);
            this.getBoton('archivo').enable();
            if (rec.data.estado == 'borrador') {
                this.getBoton('btnValidar').enable();
            }
            else {
                this.getBoton('btnValidar').disable();
            }

        },

        liberaMenu: function () {
            var rec = this.sm.getSelected();
            this.getBoton('archivo').disable();
            Phx.vista.DepositoPortal.superclass.liberaMenu.call(this);
            this.getBoton('btnValidar').disable();

        },
        onButtonDel : function () {
        	Ext.MessageBox.prompt('Observaciones','Ingrese las observaciones para la agencia',
		        function (option,value) {
		        	if (option == 'ok') {
			        	this.argumentExtraSubmit = {
	                        'observaciones':value};
	                	Phx.vista.DepositoPortal.superclass.onButtonDel.call(this);
	                }
			    } ,this);

        },
        onButtonNew : function () {
        	this.Cmp.id_agencia.enable();
    		this.Cmp.monto_deposito.enable();
    		this.Cmp.id_moneda_deposito.enable();
            this.Cmp.fecha.enable();
    		//this.Cmp.id_periodo_venta.enable();
        	Phx.vista.DepositoPortal.superclass.onButtonNew.call(this);

        },
        onButtonEdit : function () {

        	var rec = this.sm.getSelected();
        	/*if (rec.data.estado == 'validado') {
        		this.Cmp.id_agencia.disable();
        		this.Cmp.monto_deposito.disable();
        		this.Cmp.id_moneda_deposito.disable();
        		this.Cmp.id_periodo_venta.disable();
        	} else {
        		this.Cmp.id_agencia.enable();
        		this.Cmp.monto_deposito.enable();
        		this.Cmp.id_moneda_deposito.enable();
                this.Cmp.id_periodo_venta.enable();
        	}*/
            this.Cmp.id_agencia.disable();
            this.Cmp.monto_deposito.disable();
            this.Cmp.nro_deposito.disable();
            this.Cmp.fecha.disable();
            this.Cmp.id_moneda_deposito.disable();
        	Phx.vista.DepositoPortal.superclass.onButtonEdit.call(this);
        	this.Cmp.tipo.setValue('agencia');


        }
    })
</script>
