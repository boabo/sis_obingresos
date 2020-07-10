<?php
/**
*@package pXP
*@file McoS.php
*@author  (breydi.vasquez)
*@date 28-04-2020 15:25:04
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.McoS=Ext.extend(Phx.gridInterfaz,{
    solicitarPuntoVenta: true,
	constructor:function(config){

		this.maestro=config.maestro;

        Ext.Ajax.request({
                url:'../../sis_ventas_facturacion/control/Cajero/getTipoUsuario',
                params: {'vista':'cajero'},
                success: function(resp){
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    this.tipo_usuario = reg.ROOT.datos.v_tipo_usuario;
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
            var that = this;
            Ext.Ajax.request({
                url:'../../sis_ventas_facturacion/control/VentaFacturacion/obtenerApertura',
                params:{
                    id_punto_venta:'0',
                    id_sucursal:'0'
                },
                success: function(resp){
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    
                    if (reg.ROOT.datos.v_id_apertura_cierre =='') {
                      Ext.Ajax.request({
                                          url:'../../sis_ventas_facturacion/control/Venta/getVariablesBasicas',
                                          params: {'prueba':'uno'},
                                          success:this.successGetVariables,
                                          failure: this.conexionFailure,
                                          arguments:config,
                                          timeout:this.timeout,
                                          scope:this
                          });
                    }else{
                      this.otrafora(reg,config);
                    }
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });

	},

otrafora:function(data,config){
    console.log('datasss',data);


            Phx.vista.McoS.superclass.constructor.call(this,config);
            // this.store.baseParams.tipo_usuario = this.tipo_usuario;

            var fecha = new Date();
                Ext.Ajax.request({
                    url:'../../sis_parametros/control/Gestion/obtenerGestionByFecha',
                    params:{fecha:fecha.getDate()+'/'+(fecha.getMonth()+1)+'/'+fecha.getFullYear()},
                    success:function(resp){
                        var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                        this.Cmp.id_gestion.setValue(reg.ROOT.datos.id_gestion);
                    },
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });
            this.init();
            this.campo_fecha = new Ext.form.DateField({
                name: 'fecha_r',
                fieldLabel: 'Fecha',
                allowBlank: false,
                anchor: '80%',
                gwidth: 100,
                format: 'd/m/Y',
                hidden : false
            });

            this.punto_venta = new Ext.form.Label({
                        name: 'punto_venta',
                        fieldLabel: 'P.V.',
                        readOnly:true,
                        anchor: '150%',
                        gwidth: 150,
                        format: 'd/m/Y',
                        hidden : false,
                        style: {
                                fontSize:'170%',
                                fontWeight:'bold',
                                color:'black',
                                textShadow: '0.5px 0.5px 0px #FFFFFF, 1px 0px 0px rgba(0,0,0,0.15)',
                                marginLeft:'20px'
                        }
            });
            this.tbar.addField(this.campo_fecha);
            this.tbar.addField(this.punto_venta);

            this.campo_fecha.setValue(new Date());

            this.variables_globales = data.ROOT.datos;
            this.store.baseParams.tipo_usuario = this.tipo_usuario;
            this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('d/m/Y');
            this.punto_venta.setText(data.ROOT.datos.v_punto_venta);
            this.variables_globales.id_punto_venta = data.ROOT.datos.v_id_apertura_cierre;
            this.store.baseParams.id_punto_venta = data.ROOT.datos.v_id_apertura_cierre;
            this.load({params:{start:0, limit:this.tam_pag}});
            this.iniciarEventos();

},

    // capturaFiltros:function(combo, record, index){
    //         this.desbloquearOrdenamientoGrid();
    //         this.store.baseParams.id_punto_venta = this.Cmp.id_punto_venta.getValue();
    //         this.load();
    // },
    successGetVariables: function(response,request) {
        var respuesta = JSON.parse(response.responseText);
        console.log('respuesta', respuesta);
            if('datos' in respuesta){
                        this.variables_globales = respuesta.datos;
            }
            if(this.solicitarPuntoVenta){
                        this.seleccionarPuntoVentaSucursal();

            }

        Phx.vista.McoS.superclass.constructor.call(this,request.arguments);
        this.store.baseParams.tipo_usuario = this.tipo_usuario;

        var fecha = new Date();
            Ext.Ajax.request({
                url:'../../sis_parametros/control/Gestion/obtenerGestionByFecha',
                params:{fecha:fecha.getDate()+'/'+(fecha.getMonth()+1)+'/'+fecha.getFullYear()},
                success:function(resp){
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    this.Cmp.id_gestion.setValue(reg.ROOT.datos.id_gestion);
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });

        this.init();
        this.campo_fecha = new Ext.form.DateField({
            name: 'fecha_r',
            fieldLabel: 'Fecha',
            allowBlank: false,
            anchor: '80%',
            gwidth: 100,
            format: 'd/m/Y',
            hidden : false
        });

        this.punto_venta = new Ext.form.Label({
                    name: 'punto_venta',
                    fieldLabel: 'P.V.',
                    readOnly:true,
                    anchor: '150%',
                    gwidth: 150,
                    format: 'd/m/Y',
                    hidden : false,
                    style: {
                            fontSize:'170%',
                            fontWeight:'bold',
                            color:'black',
                            textShadow: '0.5px 0.5px 0px #FFFFFF, 1px 0px 0px rgba(0,0,0,0.15)',
                            marginLeft:'20px'
                    }
        });
        this.tbar.addField(this.campo_fecha);
        this.tbar.addField(this.punto_venta);

        var datos_respuesta = JSON.parse(response.responseText);
        var fecha_array = datos_respuesta.datos.fecha.split('/');
        this.campo_fecha.setValue(new Date(fecha_array[2],parseInt(fecha_array[1]) - 1,fecha_array[0]));
    },


    seleccionarPuntoVentaSucursal : function () {
        var validado = false;
        var title;
        var value;
        if (this.variables_globales.vef_tiene_punto_venta === 'true') {
            title = 'Seleccione el punto de venta con el que trabajara';
            value = 'id_punto_venta';
            var storeCombo = new Ext.data.JsonStore({
                url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
                id: 'id_punto_venta',
                root: 'datos',
                sortInfo: {
                    field: 'nombre',
                    direction: 'ASC'
                },
                totalProperty: 'total',
                fields: ['id_punto_venta', 'id_sucursal','nombre', 'codigo','habilitar_comisiones','formato_comprobante'],
                remoteSort: true,
                baseParams: {tipo_usuario: this.tipo_usuario,par_filtro: 'puve.nombre#puve.codigo', tipo_factura: this.tipo_factura, tipo : this.tipo}
            });
        } else {
            title = 'Seleccione la sucursal con la que trabajara';
            value = 'id_sucursal';
            var storeCombo = new Ext.data.JsonStore({
                url: '../../sis_ventas_facturacion/control/Sucursal/listarSucursal',
                id: 'id_sucursal',
                root: 'datos',
                sortInfo: {
                    field: 'nombre',
                    direction: 'ASC'
                },
                totalProperty: 'total',
                fields: ['id_sucursal', 'nombre', 'codigo','habilitar_comisiones','formato_comprobante','id_entidad'],
                remoteSort: true,
                baseParams: {tipo_usuario: this.tipo_usuario,par_filtro: 'suc.nombre#suc.codigo', tipo_factura: this.tipo_factura}
            });
        }

        storeCombo.load({params:{start: 0, limit: this.tam_pag},
            callback : function (r) {
              console.log('calllback', this);
                if (this.variables_globales.vef_tiene_punto_venta === 'false' ) {
                    if (this.variables_globales.vef_tiene_punto_venta === 'true') {
                        this.variables_globales.id_punto_venta = r[0].data.id_punto_venta;
                        this.variables_globales.habilitar_comisiones = r[0].data.habilitar_comisiones;
                        this.variables_globales.formato_comprobante = r[0].data.formato_comprobante;
                        this.store.baseParams.id_punto_venta = this.variables_globales.id_punto_venta;
                        this.store.baseParams.tipo_usuario = this.tipo_usuario;
                    } else {
                        this.variables_globales.id_sucursal = r[0].data.id_sucursal;
                        this.variables_globales.id_entidad = r[0].data.id_entidad;
                        this.variables_globales.habilitar_comisiones = r[0].data.habilitar_comisiones;
                        this.variables_globales.formato_comprobante = r[0].data.formato_comprobante;
                        this.store.baseParams.id_sucursal = this.variables_globales.id_sucursal;
                        this.store.baseParams.tipo_usuario = this.tipo_usuario;
                    }
                    this.store.baseParams.tipo_factura = this.tipo_factura;
                    this.load({params:{start:0, limit:this.tam_pag}});
                } else {

                    var combo2 = new Ext.form.ComboBox(
                        {
                            typeAhead: false,
                            fieldLabel: title,
                            allowBlank : false,
                            store: storeCombo,
                            mode: 'remote',
                            pageSize: 15,
                            triggerAction: 'all',
                            valueField : value,
                            displayField : 'nombre',
                            forceSelection: true,
                            tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
                            allowBlank : false,
                            anchor: '100%',
                            resizable : true
                        });

                    var formularioInicio = new Ext.form.FormPanel({
                        items: [combo2],
                        padding: true,
                        bodyStyle:'padding:5px 5px 0',
                        border: false,
                        frame: false
                    });

                    var VentanaInicio = new Ext.Window({
                        title: 'Punto de Venta / Sucursal',
                        modal: true,
                        width: 400,
                        height: 160,
                        bodyStyle: 'padding:5px;',
                        layout: 'fit',
                        hidden: true,
                        buttons: [
                            {
                                text: '<i class="fa fa-check"></i> Aceptar',
                                handler: function () {
                                    if (formularioInicio.getForm().isValid()) {
                                        validado = true;
                                        this.variables_globales.habilitar_comisiones = combo2.getStore().getById(combo2.getValue()).data.habilitar_comisiones;
                                        this.variables_globales.formato_comprobante = combo2.getStore().getById(combo2.getValue()).data.formato_comprobante;
                                        VentanaInicio.close();

                                        if (this.variables_globales.vef_tiene_punto_venta === 'true') {
                                            this.variables_globales.id_punto_venta = combo2.getValue();
                                            this.variables_globales.id_sucursal = storeCombo.getById(combo2.getValue()).data.id_sucursal;
                                            this.store.baseParams.id_punto_venta = this.variables_globales.id_punto_venta;
                                        } else {
                                            this.variables_globales.id_sucursal = combo2.getValue();
                                            this.store.baseParams.id_sucursal = this.variables_globales.id_sucursal;
                                        }

                                        this.store.baseParams.tipo_usuario = this.tipo_usuario;
                                        this.store.baseParams.tipo_factura = 'manual';
                                        this.store.baseParams.usuario_filtro = true;
                                        this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('d/m/Y');
                                        this.punto_venta.setText(combo2.lastSelectionText)
                                        this.load({params:{start:0, limit:this.tam_pag}});
                                        this.iniciarEventos();
                                    }
                                },
                                scope: this
                            }],
                        items: formularioInicio,
                        autoDestroy: true,
                        closeAction: 'close'
                    });
                    VentanaInicio.show();
                    VentanaInicio.mask.dom.style.background='black';
                    VentanaInicio.body.dom.childNodes[0].firstChild.firstChild.style.background='#A3C9F7';
                    VentanaInicio.on('beforeclose', function (){
                        if (!validado) {
                            alert('Debe seleccionar el punto de venta o sucursal de trabajo');
                            return false;
                        }
                    },this)
                }

            }, scope : this
        });



    },



	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_mco'
			},
			type:'Field',
			form:true
		},
        {
            config:{
                labelSeparator:'',
                inputType:'hidden',
                name: 'id_punto_venta'
            },
            type:'Field',
            form:true
        },
        {
            config:{
                labelSeparator:'',
                inputType:'hidden',
                name: 'concepto_codigo'
            },
            type:'Field',
            form:true
        },

        {
            config:{
                name: 'id_concepto_ingas',
                fieldLabel: 'Concepto',
                allowBlank: true,
                emptyText : 'Concepto...',
                store : new Ext.data.JsonStore({
                            url:'../../sis_parametros/control/ConceptoIngas/listarConceptoIngas',
                            id : 'id_concepto_ingas',
                            root: 'datos',
                            sortInfo:{
                                    field: 'desc_ingas',
                                    direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_concepto_ingas','tipo','desc_ingas','movimiento','desc_partida','id_grupo_ots','filtro_ot','requiere_ot', 'codigo'],
                            remoteSort: true,
                            baseParams:{par_filtro:'desc_ingas',codigo:'MCO'}
                }),
               valueField: 'id_concepto_ingas',
               displayField: 'codigo',
               gdisplayField: 'codigo',
               hiddenName: 'id_concepto_ingas',
               forceSelection:true,
               typeAhead: false,
               triggerAction: 'all',
               tpl:'<tpl for="."><div class="x-combo-list-item"><b>Codigo: </b><span style="color:green;font-weight:bold;">{codigo}</span><br><b>Descripcion: </b><span style="color:green;font-weight:bold;">{desc_ingas}</span></div></tpl>',
               listWidth:350,
               resizable:true,
               lazyRender:true,
               mode:'remote',
               pageSize:10,
               queryDelay:1000,
               anchor:'110%',
               gwidth:100,
               minChars:1
            },
            type:'ComboBox',
            id_grupo:1,
            form:true,
            grid:true
        },
		// {
		// 	config: {
		// 		name: 'id_concepto_global',
		// 		fieldLabel: 'T-Concepto',
		// 		allowBlank: true,
		// 		emptyText: 'Elija una opción...',
		// 		store: new Ext.data.JsonStore({
		// 			url: '../../sis_obingresos/control/ConceptoGlobal/listarConceptoGlobal',
		// 			id: 'id_concepto_global',
		// 			root: 'datos',
		// 			sortInfo: {
		// 				field: 'codigo',
		// 				direction: 'ASC'
		// 			},
		// 			totalProperty: 'total',
		// 			fields: ['id_concepto_global', 'codigo', 'descripcion'],
		// 			remoteSort: true,
		// 			baseParams: {par_filtro: 'incoglob.codigo#incoglob.descripcion'}
		// 		}),
        //         valueField: 'id_concepto_global',
        //         displayField: 'codigo',
        //         gdisplayField: 'codigo',
        //         tpl:'<tpl for="."><div class="x-combo-list-item"><b>Codigo: </b><span style="color:green;font-weight:bold;">{codigo}</span><br><b>Descripcion: </b><span style="color:green;font-weight:bold;">{descripcion}</span></div></tpl>',
        //         hiddenName: 'id_concepto_global',
        //         forceSelection: true,
        //         autoSelect: true,
        //         typeAhead: false,
        //         typeAheadDelay: 75,
        //         triggerAction: 'all',
        //         lazyRender: false,
        //         mode: 'remote',
        //         pageSize: 5,
        //         queryDelay: 500,
        //         anchor: '110%',
        //         gwidth: 150,
        //         minChars: 1,
        //         listWidth: '300',
        //         resizable:true,
        //         renderer : function(value, p, record) {
        //             return String.format('{0}', record.data['codigo']);
        //         }
        //     },
		// 	type: 'ComboBox',
		// 	id_grupo: 1,
		// 	filters: {pfiltro: 'incoglob.codigo',type: 'string'},
		// 	grid: true,
		// 	form: true
        // },
		{
			config:{
				name: 'estado',
				fieldLabel: 'Estado',
				allowBlank: false,
				anchor: '80%',
				gwidth: 90,
                renderer: function(val) {
                    if(val == 1){
                        return String.format('<span style="color:green;font-weight:bold;">{0}</span>', 'VÁLIDO');
                    }else {
                        return String.format('<span style="color:red;font-weight:bold;">{0}</span>', 'ANULADO');
                    }
                }
			},
				type:'NumberField',
				id_grupo: 2,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'fecha_emision',
				fieldLabel: 'Fecha Emisión',
				allowBlank: false,
				anchor: '90%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'imcos.fecha_emision',type:'date'},
				id_grupo: 3,
				grid:true,
				form:true
		},
        {
            config: {
                name: 'id_moneda',
                origen: 'MONEDA',
                allowBlank: false,
                fieldLabel: 'Monenda',
                gdisplayField: 'desc_moneda', //mapea al store del grid
                gwidth: 80,
                anchor:'100%',
                renderer: function (value, p, record) {
                    return String.format('{0}', record.data['desc_moneda']);
                }
            },
            type: 'ComboRec',
            id_grupo: 4,
            grid: true,
            form: true
        },
		{
			config:{
				name: 'tipo_cambio',
				fieldLabel: 'T-C',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
			},
				type:'TextField',
				id_grupo: 5,
				grid:false,
				form:true
		},
        /*
		{
			config:{
				name: 'codigo_internacional',
				fieldLabel: 'Mon',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
			},
				type:'TextField',
				id_grupo: 5,
				grid:true,
				form:true
		},*/
		{
			config:{
				name: 'mco',
				fieldLabel: 'Numero',
				allowBlank: true,
				anchor: '100%',
				gwidth: 100,
			},
				type:'TextField',
				id_grupo: 6,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'nro_mco',
				fieldLabel: '',
				allowBlank: true,
				anchor: '140%',
				gwidth: 100,
        maxLength:14
			},
				type:'TextField',
				id_grupo: 7,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'pax',
				fieldLabel: 'PAX',
				allowBlank: false,
				anchor: '110%',
				gwidth: 100,
			},
				type:'TextField',
				id_grupo: 8,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'motivo',
				fieldLabel: 'Motivo',
				allowBlank: false,
				anchor: '100%',
				gwidth: 100,
        maxLength:200
			},
				type:'TextArea',
				filters:{pfiltro:'imcos.motivo',type:'string'},
                bottom_filter: true,
				id_grupo: 9,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'valor_total',
				fieldLabel: 'Valor-Total',
				allowBlank: true,
				anchor: '100%',
				gwidth: 100
			},
				type:'NumberField',
				filters:{pfiltro:'imcos.valor_total',type:'numeric'},
				id_grupo: 10,
				grid:true,
				form:true
		},

    {
        config: {
            name: 'id_funcionario_emisor',
            fieldLabel: 'Emitido por',
            allowBlank: false,
            emptyText: 'Elija una opción...',
            store: new Ext.data.JsonStore({
                url: '../../sis_organigrama/control/Funcionario/listarFuncionarioCargo',
                id: 'id_funcionario',
                root: 'datos',
                sortInfo: {
                    field: 'desc_funcionario1',
                    direction: 'ASC'
                },
                totalProperty: 'total',
                fields: ['id_funcionario','desc_funcionario1','email_empresa','nombre_cargo','lugar_nombre','oficina_nombre'],
                remoteSort: true,
                baseParams: {par_filtro: 'FUNCAR.desc_funcionario1'}//#FUNCAR.nombre_cargo
            }),
            valueField: 'id_funcionario',
            displayField: 'desc_funcionario1',
            gdisplayField: 'desc_funcionario1',//corregit materiaesl
            tpl:'<tpl for="."><div class="x-combo-list-item" style="color: black"><p><b>{desc_funcionario1}</b></p><p style="color: #80251e">{nombre_cargo}<br>{email_empresa}</p><p style="color:green">{oficina_nombre} - {lugar_nombre}</p></div></tpl>',
            hiddenName: 'id_funcionario',
            forceSelection: true,
            typeAhead: false,
            triggerAction: 'all',
            lazyRender: true,
            mode: 'remote',
            pageSize: 20,
            queryDelay: 1000,
            anchor: '100%',
            gwidth: 100,
            minChars: 2,
            resizable:true,
            listWidth:'280',
            renderer: function (value, p, record) {
                return String.format('{0}', record.data['desc_funcionario1']);
            }
        },
        type: 'ComboBox',
        bottom_filter:true,
        id_grupo:11,
        grid: false,
        form: true
    },
		// {
		// 	config:{
		// 		name: 'desc_usu_emitido',
		// 		fieldLabel: 'Emitido por',
		// 		allowBlank: true,
		// 		anchor: '100%',
		// 		gwidth: 100
		// 	},
		// 		type:'TextField',
		// 		id_grupo: 11,
		// 		grid:false,
		// 		form:true
		// },
		{
			config: {
				name: 'id_documento_original',
				fieldLabel: 'Documento Original',
				allowBlank: true,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_/control/Clase/Metodo',
					id: 'id_',
					root: 'datos',
					sortInfo: {
						field: 'nombre',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_', 'nombre', 'codigo'],
					remoteSort: true,
					baseParams: {par_filtro: 'movtip.nombre#movtip.codigo'}
				}),
				valueField: 'id_',
				displayField: 'nombre',
				gdisplayField: 'desc_',
				hiddenName: 'id_documento_original',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '100%',
				gwidth: 150,
				minChars: 2,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['desc_']);
				}
			},
			type: 'ComboBox',
			id_grupo: 110,
			filters: {pfiltro: 'movtip.nombre',type: 'string'},
			grid: false,
			form: true
		},
		{
            config:{
                name:'id_gestion',
                fieldLabel:'Gestión',
                allowBlank:true,
                emptyText:'Gestión...',
                store: new Ext.data.JsonStore({
                         url: '../../sis_parametros/control/Gestion/listarGestion',
                         id: 'id_gestion',
                         root: 'datos',
                         sortInfo:{
                            field: 'gestion',
                            direction: 'DESC'
                    },
                    totalProperty: 'total',
                    fields: ['id_gestion','gestion','moneda','codigo_moneda'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams:{par_filtro:'gestion'}
                    }),
                valueField: 'id_gestion',
                displayField: 'gestion',
                gdisplayField: 'gestion',
                hiddenName: 'id_gestion',
                forceSelection:true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender:true,
                mode:'remote',
                pageSize:5,
                queryDelay:1000,
                listWidth:200,
                resizable:true,
				width:150

            },
            type:'ComboBox',
            id_grupo: 12,
            filters:{
                        pfiltro:'gestion',
                        type:'string'
                    },
            grid:true,
            form:true
        },
		{
			config:{
				name: 'usr_reg',
				fieldLabel: 'Usr/Cajero',
				allowBlank: true,
				anchor: '100%',
				gwidth: 100
			},
				type:'Field',
				filters:{pfiltro:'usu1.cuenta',type:'string'},
				id_grupo: 13,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'fecha_reg',
				fieldLabel: 'Fecha-Reg',
				allowBlank: true,
				anchor: '100%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'imcos.fecha_reg',type:'date'},
				id_grupo: 14,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'hora_reg',
				fieldLabel: 'Hora-Reg',
				allowBlank: true,
				anchor: '100%',
				gwidth: 100,
				format: 'H:i:s',
			    //value.dateFormat('H:i:s'):''}
			},
				type:'Field',
				id_grupo: 15,
				form:true
		},
        ///Documentos originales
        {
            config: {
                name: 'id_boleto',
                fieldLabel: 'TKT-MCO',
                allowBlank: false,
                emptyText: 'Tkt-mco...',
                store: new Ext.data.JsonStore(
                    {
                        url: '../../sis_obingresos/control/McoS/listarTkts',
                        id: 'id_boleto',
                        root: 'datos',
                        sortInfo: {
                            field: 'tkt',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_boleto','tkt', 'tkt_estac', 'tkt_pais','fecha_emision', 'total', 'moneda','val_conv', 'tipo_cambio'],
                        remoteSort: true
                    }),
                valueField: 'id_boleto',
                hiddenValue: 'tkt',
                displayField: 'tkt',
                gdisplayField: 'tkt',
                queryParam: 'tkt',
                tpl:'<tpl for="."><div class="x-combo-list-item"><b>{tkt}</b><p>Valor total: {total}</p><p>Moneda: {moneda}</p></div></tpl>',
                listWidth: '180',
                forceSelection: false,
                autoSelect: true,
                typeAhead: false,
                typeAheadDelay: 75,
                hideTrigger: true,
                triggerAction: 'query',
                lazyRender: false,
                mode: 'remote',
                pageSize: 20,
                queryDelay: 500,
                anchor: '0%',
                minChars: 4,
                listWidth: '250'
            },
            type: 'ComboBox',
            id_grupo: 16,
            form: true,
            grid: true
        },
		{
			config:{
				name: 'pais_doc_or',
				fieldLabel: 'Pais',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 17,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'estacion_doc_or',
				fieldLabel: 'Estac',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 18,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'fecha_doc_or',
				fieldLabel: 'Fecha',
				allowBlank: true,
				anchor: '21%',
				gwidth: 100,
                format: 'd/m/Y'
			},
				type:'DateField',
				id_grupo: 19,
				grid:false,
				form:true
		},
		{
			config:{
				name: 't_c_doc_or',
				fieldLabel: 'T/C',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 20,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'moneda_doc_or',
				fieldLabel: 'Moneda',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 21,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'val_total_doc_or',
				fieldLabel: 'Valor-Total',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 22,
				grid:false,
				form:true
		},
        {
			config:{
				name: 'val_conv_doc_or',
				fieldLabel: 'Valor-Conv',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 23,
				grid:false,
				form:true
		},
        ///fin
        ///Cabecera
		{
			config:{
				name: 'pais_head',
				fieldLabel: 'Pais',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 24,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'estacion_head',
				fieldLabel: 'Est',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 25,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'agt_tv_head',
				fieldLabel: 'Agt/PV',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 26,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'city_head',
				fieldLabel: '',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 27,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'suc_head',
				fieldLabel: 'Suc',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 28,
				grid:false,
				form:true
		},
        //fin
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
				filters:{pfiltro:'imcos.estado_reg',type:'string'},
				id_grupo: 30,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100
			},
				type:'Field',
				filters:{pfiltro:'imcos.id_usuario_ai',type:'numeric'},
				id_grupo:30,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'usuario_ai',
				fieldLabel: 'Funcionaro AI',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100
			},
				type:'TextField',
				filters:{pfiltro:'imcos.usuario_ai',type:'string'},
				id_grupo:30,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'usr_mod',
				fieldLabel: 'Modificado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100
			},
				type:'Field',
				filters:{pfiltro:'usu2.cuenta',type:'string'},
				id_grupo: 30,
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
				filters:{pfiltro:'imcos.fecha_mod',type:'date'},
				id_grupo: 30,
				grid:false,
				form:false
		}
	],
	tam_pag:50,
	title:'MCOs',
	ActSave:'../../sis_obingresos/control/McoS/insertarMcoS',
	ActDel:'../../sis_obingresos/control/McoS/eliminarMcoS',
	ActList:'../../sis_obingresos/control/McoS/listarMcoS',
	id_store:'id_mco',
	fields: [
		{name:'id_mco', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'estado', type: 'string'},
		{name:'fecha_emision', type: 'date',dateFormat:'Y-m-d'},
		{name:'id_moneda', type: 'numeric'},
		{name:'motivo', type: 'string'},
		{name:'valor_total', type: 'numeric'},
		{name:'id_documento_original', type: 'numeric'},
		{name:'id_gestion', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'id_boleto', type: 'numeric'},
		{name:'codigo', type: 'string'},
		{name:'desc_ingas', type: 'string'},
		{name:'codigo_internacional', type: 'string'},
		{name:'gestion', type: 'numeric'},
		{name:'tkt', type: 'string'},
		{name:'fecha_doc_or', type: 'date'},
		{name:'val_total_doc_or', type: 'numeric'},
		{name:'moneda_doc_or', type: 'string'},
		{name:'estacion_doc_or', type: 'string'},
		{name:'pais_doc_or', type: 'string'},
    {name:'id_punto_venta', type: 'numeric'},
    {name:'agt_tv_head', type: 'string'},
    {name:'estacion_head', type: 'string'},
    {name:'suc_head', type: 'string'},
    {name:'city_head', type: 'string'},
    {name:'pais_head', type: 'string'},
    {name:'desc_moneda', type: 'string'},
    {name:'concepto_codigo', type: 'string'},
    {name:'id_concepto_ingas', type: 'numeric'},
    {name:'tipo_cambio', type: 'numeric'},
    {name:'nro_mco', type: 'string'},
    {name:'pax', type: 'string'},
    {name:'id_funcionario_emisor', type: 'numeric'},
    {name:'desc_funcionario1', type: 'string'},
    {name:'t_c_doc_or', type: 'numeric'},
    {name:'val_conv_doc_or', type: 'numeric'},



	],
	sortInfo:{
		field: 'id_mco',
		direction: 'ASC'
	},
	bdel:true,
	bsave:false,
  btest:false,

    iniciarEventos:function(){

      this.campo_fecha.on('select',function(value){
    			this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('d/m/Y');
    			this.load();
    		},this);

        this.Cmp.id_concepto_ingas.on('select', function(cmb, rec, i){
            // this.Cmp.concepto_codigo.setValue(rec.data.codigo);
            // this.store.baseParams.concepto_codigo = rec.data.codigo;
            this.Cmp.id_boleto.store.baseParams.concepto_codigo=rec.data.codigo;
            console.log('bbb', this);
        }, this);

        this.Cmp.id_boleto.on('select', function (cmb, rec, i) {
                console.log('data', rec);
                this.Cmp.estacion_doc_or.setValue(rec.data.tkt_estac);
                this.Cmp.pais_doc_or.setValue(rec.data.tkt_pais);
                this.Cmp.fecha_doc_or.setValue(rec.data.fecha_emision);
                this.Cmp.moneda_doc_or.setValue(rec.data.moneda);
                this.Cmp.val_total_doc_or.setValue(rec.data.total);
                this.Cmp.val_conv_doc_or.setValue(rec.data.val_conv);
                this.Cmp.val_conv_doc_or.setValue(rec.data.val_conv);
                this.Cmp.t_c_doc_or.setValue(rec.data.tipo_cambio);
            }, this);

    },
    carac:function(me){
        console.log('meeeee',me);
        /*Reduccion de spacios de componentes de formulario*/
        //MCO
		me.Cmp.estado.itemCt.dom.childNodes[0].style.width='65px';
        me.Cmp.estado.itemCt.dom.childNodes[0].style.fontWeight='bold';
		me.Cmp.estado.itemCt.dom.childNodes[1].style.paddingLeft='60px';
        //COCEPTO GLOBAL
        me.Cmp.id_concepto_ingas.itemCt.dom.childNodes[0].style.width='79px';
        me.Cmp.id_concepto_ingas.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.id_concepto_ingas.itemCt.dom.childNodes[1].style.paddingLeft='60px';

        //FECHA EMISION
        me.Cmp.fecha_emision.itemCt.dom.childNodes[0].style.width='100px';
        me.Cmp.fecha_emision.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.fecha_emision.itemCt.dom.childNodes[1].style.paddingLeft='90px';

        //MONEDA
        me.Cmp.id_moneda.itemCt.dom.childNodes[0].style.width='65px';
        me.Cmp.id_moneda.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.id_moneda.itemCt.dom.childNodes[1].style.paddingLeft='35px';

        //Tipo de cambio INTERNACIONAL
        me.Cmp.tipo_cambio.itemCt.dom.childNodes[0].style.width='41px';
        me.Cmp.tipo_cambio.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.tipo_cambio.itemCt.dom.childNodes[0].style.paddingLeft='5px';
        me.Cmp.tipo_cambio.itemCt.dom.childNodes[1].style.paddingLeft='30px';

        // //MCO
        me.Cmp.mco.itemCt.dom.childNodes[0].style.width='0px';
        me.Cmp.mco.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.mco.itemCt.dom.childNodes[1].style.paddingLeft='87px';

        // //nro_mco
        me.Cmp.nro_mco.itemCt.dom.childNodes[0].style.width='5px';
        me.Cmp.nro_mco.itemCt.dom.childNodes[1].style.paddingLeft='5px';
        // me.Cmp.nro_mco.itemCt.dom.childNodes[1].style.paddingTop='14px';

        // //Pax
        me.Cmp.pax.itemCt.dom.childNodes[0].style.width='40px';
        me.Cmp.pax.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.pax.itemCt.dom.childNodes[1].style.paddingLeft='5px';

        // //Motivo
        me.Cmp.motivo.itemCt.dom.childNodes[0].style.width='100px';
        me.Cmp.motivo.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.motivo.itemCt.dom.childNodes[1].style.paddingLeft='5px';

        // //ValorTotal
        me.Cmp.valor_total.itemCt.dom.childNodes[0].style.width='95px';
        me.Cmp.valor_total.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.valor_total.itemCt.dom.childNodes[1].style.paddingLeft='80px';

        // //Emitido por
        me.Cmp.id_funcionario_emisor.itemCt.dom.childNodes[0].style.width='93px';
        me.Cmp.id_funcionario_emisor.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.id_funcionario_emisor.itemCt.dom.childNodes[1].style.paddingLeft='80px';
        me.Cmp.id_funcionario_emisor.itemCt.dom.childNodes[1].style.position='absolute';
        me.Cmp.id_funcionario_emisor.itemCt.dom.childNodes[1].style.top='1px';
        me.Cmp.id_funcionario_emisor.itemCt.dom.childNodes[1].style.left='16px';

        // //Ususario/Cajero
        me.Cmp.usr_reg.itemCt.dom.childNodes[0].style.width='95px';
        me.Cmp.usr_reg.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.usr_reg.itemCt.dom.childNodes[1].style.paddingLeft='80px';
        me.Cmp.usr_reg.itemCt.dom.childNodes[1].style.position='absolute';
        me.Cmp.usr_reg.itemCt.dom.childNodes[1].style.top='3px';
        me.Cmp.usr_reg.itemCt.dom.childNodes[1].style.left='18px';

        // //Fecha Reg
        me.Cmp.fecha_reg.itemCt.dom.childNodes[0].style.width='93px';
        me.Cmp.fecha_reg.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.fecha_reg.itemCt.dom.childNodes[1].style.paddingLeft='70px';

        // //Hora Reg
        me.Cmp.hora_reg.itemCt.dom.childNodes[0].style.width='78px';
        me.Cmp.hora_reg.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.hora_reg.itemCt.dom.childNodes[1].style.paddingLeft='60px';

        // // title dodumentos
        // console.log('doc',me.window.body);
        me.window.body.dom.childNodes[0].childNodes[0].childNodes[0][62].childNodes[0].childNodes[0].style.letterSpacing='10px';
        me.window.body.dom.childNodes[0].childNodes[0].childNodes[0][62].childNodes[0].childNodes[0].style.paddingLeft='300px';

        // //TKT -MCO
        me.Cmp.id_boleto.itemCt.dom.childNodes[0].style.width='86px';
        me.Cmp.id_boleto.itemCt.dom.childNodes[0].style.left='40px';
        me.Cmp.id_boleto.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.id_boleto.itemCt.dom.childNodes[1].style.paddingLeft='5px';
        me.Cmp.id_boleto.itemCt.dom.childNodes[1].style.position='absolute';
        me.Cmp.id_boleto.itemCt.dom.childNodes[1].style.top='50px';
        me.Cmp.id_boleto.itemCt.dom.childNodes[1].childNodes[0].childNodes[0].style.width='220px';

        // //Pais DOC-O
        me.Cmp.pais_doc_or.itemCt.dom.childNodes[0].style.width='30px';
        me.Cmp.pais_doc_or.itemCt.dom.childNodes[0].style.position='absolute';
        me.Cmp.pais_doc_or.itemCt.dom.childNodes[0].style.top='25px';
        me.Cmp.pais_doc_or.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.pais_doc_or.itemCt.dom.childNodes[0].style.left='240px';
        me.Cmp.pais_doc_or.itemCt.dom.childNodes[1].style.paddingLeft='0px';
        me.Cmp.pais_doc_or.itemCt.dom.childNodes[1].style.position='absolute';
        me.Cmp.pais_doc_or.itemCt.dom.childNodes[1].style.top='50px';
        me.Cmp.pais_doc_or.itemCt.dom.childNodes[1].style.left='230px';
        me.Cmp.pais_doc_or.itemCt.dom.childNodes[1].childNodes[0].style.width='60px';

        // //Estacion DOC-O
        me.Cmp.estacion_doc_or.itemCt.dom.childNodes[0].style.width='30px';
        me.Cmp.estacion_doc_or.itemCt.dom.childNodes[0].style.position='absolute';
        me.Cmp.estacion_doc_or.itemCt.dom.childNodes[0].style.top='25px';
        me.Cmp.estacion_doc_or.itemCt.dom.childNodes[0].style.left='310px';
        me.Cmp.estacion_doc_or.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.estacion_doc_or.itemCt.dom.childNodes[1].style.paddingLeft='0px';
        me.Cmp.estacion_doc_or.itemCt.dom.childNodes[1].style.position='absolute';
        me.Cmp.estacion_doc_or.itemCt.dom.childNodes[1].style.top='50px';
        me.Cmp.estacion_doc_or.itemCt.dom.childNodes[1].style.left='300px';
        me.Cmp.estacion_doc_or.itemCt.dom.childNodes[1].childNodes[0].style.width='75px';

        // //Fecha DOC-O
        me.Cmp.fecha_doc_or.itemCt.dom.childNodes[0].style.width='40px';
        me.Cmp.fecha_doc_or.itemCt.dom.childNodes[0].style.position='absolute';
        me.Cmp.fecha_doc_or.itemCt.dom.childNodes[0].style.top='25px';
        me.Cmp.fecha_doc_or.itemCt.dom.childNodes[0].style.left='400px';
        me.Cmp.fecha_doc_or.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.fecha_doc_or.itemCt.dom.childNodes[1].style.paddingLeft='0px';
        me.Cmp.fecha_doc_or.itemCt.dom.childNodes[1].style.position='absolute';
        me.Cmp.fecha_doc_or.itemCt.dom.childNodes[1].style.top='50px';
        me.Cmp.fecha_doc_or.itemCt.dom.childNodes[1].style.left='390px';
        me.Cmp.fecha_doc_or.itemCt.dom.childNodes[1].childNodes[0].style.width='100px';

        // //T/C DOC-O
        me.Cmp.t_c_doc_or.itemCt.dom.childNodes[0].style.width='30px';
        me.Cmp.t_c_doc_or.itemCt.dom.childNodes[0].style.position='absolute';
        me.Cmp.t_c_doc_or.itemCt.dom.childNodes[0].style.top='25px';
        me.Cmp.t_c_doc_or.itemCt.dom.childNodes[0].style.left='520px';
        me.Cmp.t_c_doc_or.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.t_c_doc_or.itemCt.dom.childNodes[1].style.paddingLeft='0px';
        me.Cmp.t_c_doc_or.itemCt.dom.childNodes[1].style.position='absolute';
        me.Cmp.t_c_doc_or.itemCt.dom.childNodes[1].style.top='50px';
        me.Cmp.t_c_doc_or.itemCt.dom.childNodes[1].style.left='500px';
        me.Cmp.t_c_doc_or.itemCt.dom.childNodes[1].childNodes[0].style.width='100px';

        // //Moneda DOC-O
        me.Cmp.moneda_doc_or.itemCt.dom.childNodes[0].style.width='50px';
        me.Cmp.moneda_doc_or.itemCt.dom.childNodes[0].style.position='absolute';
        me.Cmp.moneda_doc_or.itemCt.dom.childNodes[0].style.top='25px';
        me.Cmp.moneda_doc_or.itemCt.dom.childNodes[0].style.left='610px';
        me.Cmp.moneda_doc_or.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.moneda_doc_or.itemCt.dom.childNodes[1].style.paddingLeft='0px';
        me.Cmp.moneda_doc_or.itemCt.dom.childNodes[1].style.position='absolute';
        me.Cmp.moneda_doc_or.itemCt.dom.childNodes[1].style.top='50px';
        me.Cmp.moneda_doc_or.itemCt.dom.childNodes[1].style.left='605px';
        me.Cmp.moneda_doc_or.itemCt.dom.childNodes[1].childNodes[0].style.width='100px';

        // //Valor Total DOC-O
        me.Cmp.val_total_doc_or.itemCt.dom.childNodes[0].style.width='90px';
        me.Cmp.val_total_doc_or.itemCt.dom.childNodes[0].style.position='absolute';
        me.Cmp.val_total_doc_or.itemCt.dom.childNodes[0].style.top='25px';
        me.Cmp.val_total_doc_or.itemCt.dom.childNodes[0].style.left='720px';
        me.Cmp.val_total_doc_or.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.val_total_doc_or.itemCt.dom.childNodes[1].style.position='absolute';
        me.Cmp.val_total_doc_or.itemCt.dom.childNodes[1].style.paddingLeft='0px';
        me.Cmp.val_total_doc_or.itemCt.dom.childNodes[1].style.position='absolute';
        me.Cmp.val_total_doc_or.itemCt.dom.childNodes[1].style.top='50px';
        me.Cmp.val_total_doc_or.itemCt.dom.childNodes[1].style.left='710px';
        me.Cmp.val_total_doc_or.itemCt.dom.childNodes[1].childNodes[0].style.width='115px';

        // //Valor conv DOC-O
        me.Cmp.val_conv_doc_or.itemCt.dom.childNodes[0].style.width='89px';
        me.Cmp.val_conv_doc_or.itemCt.dom.childNodes[0].style.position='absolute';
        me.Cmp.val_conv_doc_or.itemCt.dom.childNodes[0].style.top='25px';
        me.Cmp.val_conv_doc_or.itemCt.dom.childNodes[0].style.left='840px';
        me.Cmp.val_conv_doc_or.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.val_conv_doc_or.itemCt.dom.childNodes[1].style.paddingLeft='0px';
        me.Cmp.val_conv_doc_or.itemCt.dom.childNodes[1].style.position='absolute';
        me.Cmp.val_conv_doc_or.itemCt.dom.childNodes[1].style.top='50px';
        me.Cmp.val_conv_doc_or.itemCt.dom.childNodes[1].style.left='830px';
        me.Cmp.val_conv_doc_or.itemCt.dom.childNodes[1].childNodes[0].style.width='90px';


        ///**********head */
        // //Pais head
        me.Cmp.pais_head.itemCt.dom.childNodes[0].style.width='30px';
        me.Cmp.pais_head.itemCt.dom.childNodes[0].style.position='absolute';
        me.Cmp.pais_head.itemCt.dom.childNodes[0].style.top='5px';
        me.Cmp.pais_head.itemCt.dom.childNodes[0].style.lef='5px';
        me.Cmp.pais_head.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.pais_head.itemCt.dom.childNodes[1].style.paddingLeft='35px';
        me.Cmp.pais_head.itemCt.dom.childNodes[1].style.position='absolute';
        me.Cmp.pais_head.itemCt.dom.childNodes[1].style.top='5px';
        me.Cmp.pais_head.itemCt.dom.childNodes[1].childNodes[0].style.width='110px';

        // //Estacion head
        me.Cmp.estacion_head.itemCt.dom.childNodes[0].style.width='30px';
        me.Cmp.estacion_head.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.estacion_head.itemCt.dom.childNodes[1].style.paddingLeft='30px';
        me.Cmp.estacion_head.itemCt.dom.childNodes[1].childNodes[0].style.width='100px';

        // //agtv head
        me.Cmp.agt_tv_head.itemCt.dom.childNodes[0].style.width='60px';
        me.Cmp.agt_tv_head.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.agt_tv_head.itemCt.dom.childNodes[1].style.paddingLeft='30px';
        me.Cmp.agt_tv_head.itemCt.dom.childNodes[1].childNodes[0].style.width='200px';

        // //City head
        me.Cmp.city_head.itemCt.dom.childNodes[0].style.width='0px';
        me.Cmp.city_head.itemCt.dom.childNodes[1].style.paddingLeft='10px';
        me.Cmp.city_head.itemCt.dom.childNodes[1].childNodes[0].style.width='250px';

        // //Suc head
        me.Cmp.suc_head.itemCt.dom.childNodes[0].style.width='35px';
        me.Cmp.suc_head.itemCt.dom.childNodes[0].style.fontWeight='bold';
        me.Cmp.suc_head.itemCt.dom.childNodes[1].style.paddingLeft='25px';
        me.Cmp.suc_head.itemCt.dom.childNodes[1].childNodes[0].style.width='70px';

    },
    onButtonNew: function() {
console.log('nuevo',this);

        var date = new Date();
        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();
        var hour = date.getHours();
        var min = date.getMinutes();
        var sec = date.getSeconds();
        var fdate = (month < 10)? day+'/'+0+month+'/'+year: day+'/'+month+'/'+year;
        var ftime = (sec < 10)? hour+':'+min+':'+0+sec:hour+':'+min+':'+sec;

        this.window.title='<span style="font-size:12pt;padding-left: 100;">Boliviana de Aviacion &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; INGRESO DE DOCUMENTOS MCO  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; FECHA: '+fdate+'</span>';
        this.window.setSize(1000, 500);
        // this.window.modal=false;
        this.window.collapsible=true;
        Phx.vista.McoS.superclass.onButtonNew.call(this);
        var me = this;

        this.carac(me);

        Ext.Ajax.request({
                    url:'../../sis_obingresos/control/McoS/getDatatoFormRegMcoS',
                    params:{id_punto_venta: this.variables_globales.id_punto_venta},
                    success:function(resp){
                        var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                        console.log('value=> ',reg);
                        this.Cmp.pais_head.setRawValue(reg.ROOT.datos.pais);
                        this.Cmp.usr_reg.setRawValue(reg.ROOT.datos.usr_reg);
                        this.Cmp.tipo_cambio.setRawValue(reg.ROOT.datos.tipo_cambio);
                        this.Cmp.id_moneda.setValue(reg.ROOT.datos.id_moneda);
                        this.Cmp.id_moneda.setRawValue(reg.ROOT.datos.moneda);
                        this.Cmp.estacion_head.setRawValue(reg.ROOT.datos.estacion);
                        this.Cmp.agt_tv_head.setRawValue(reg.ROOT.datos.agt_pv);
                        this.Cmp.city_head.setRawValue(reg.ROOT.datos.pv_nombre);
                        this.Cmp.suc_head.setRawValue(reg.ROOT.datos.cod_suc);
                        this.Cmp.id_punto_venta.setValue(reg.ROOT.datos.id_punto_venta);
                    },
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });

        // datos autocompletados
        this.Cmp.fecha_emision.setValue(new Date());
        this.Cmp.fecha_reg.setValue(new Date());
        this.Cmp.hora_reg.setValue(ftime);
        this.Cmp.mco.setValue('MCO');
        this.Cmp.estado.setValue(1);
        this.Cmp.nro_mco.setValue('930');
        this.Cmp.id_boleto.setValue('930');
        this.Cmp.id_mco.setValue(null);
        this.Cmp.fecha_reg.setDisabled(true);
        this.Cmp.pais_head.setDisabled(true);
        this.Cmp.usr_reg.setDisabled(true);
        this.Cmp.estacion_head.setDisabled(true);
        this.Cmp.agt_tv_head.setDisabled(true);
        this.Cmp.city_head.setDisabled(true);
        this.Cmp.suc_head.setDisabled(true);
        this.Cmp.hora_reg.setDisabled(true);

    },
    onButtonEdit: function() {
        var rec = this.sm.getSelected();
        console.log('edit',rec);
        var date = new Date();
        var day = rec.data.fecha_reg.getDate();
        var month = rec.data.fecha_reg.getMonth() + 1;
        var year = rec.data.fecha_reg.getFullYear();
        var hour = rec.data.fecha_reg.getHours();
        var min = rec.data.fecha_reg.getMinutes();
        var sec = rec.data.fecha_reg.getSeconds();
        var fdate = (month < 10)? day+'/'+0+month+'/'+year: day+'/'+month+'/'+year;
        var ftime = (sec < 10)? hour+':'+min+':'+0+sec:hour+':'+min+':'+sec;

        this.window.title='<span style="font-size:12pt;padding-left: 100;">Boliviana de Aviacion &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; INGRESO DE DOCUMENTOS MCO  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; FECHA: '+fdate+'</span>';
        this.window.setSize(1000, 500);
        // this.window.modal=false;
        this.window.collapsible=true;

        Phx.vista.McoS.superclass.onButtonEdit.call(this);
        this.Cmp.mco.setValue('MCO');
        this.Cmp.hora_reg.setValue(ftime);
        this.Cmp.fecha_reg.setDisabled(true);
        this.Cmp.pais_head.setDisabled(true);
        this.Cmp.usr_reg.setDisabled(true);
        this.Cmp.estacion_head.setDisabled(true);
        this.Cmp.agt_tv_head.setDisabled(true);
        this.Cmp.city_head.setDisabled(true);
        this.Cmp.suc_head.setDisabled(true);
        this.Cmp.hora_reg.setDisabled(true);
        var me = this;
        this.carac(me);
    },

    Grupos: [
        {
            layout: 'column',
            border: false,
            xtype: 'fieldset',
            autoScroll: false,
            defaults: {
                border: false
            },

            items: [
                {
                    style:{
                        width:'940px',
                        position: 'absolute',
                        top: '15',
                        height:'35px',
                        border:'1px solid green',
                    },
                    items:[
                        {
                            xtype: 'fieldset',
                            border: false,
                            style:{
                                    marging:'0',
                                    padding:'0'
                                },
                                items: [
                                        /********************************************24 */
                                        {
                                        xtype: 'fieldset',
                                        border: false,
                                        style:{
                                                width:'200px',
                                                padding:'0',
                                            },
                                        items: [
                                                {
                                                    xtype: 'fieldset',
                                                    style:{
                                                        padding: '0',
                                                        marginTop:'15px'
                                                    },
                                                    border:false,
                                                    items: [],
                                                    id_grupo: 24
                                                }
                                            ]
                                        },
                                        /********************************************25 */
                                        {
                                        xtype: 'fieldset',
                                        border: false,
                                        style:{
                                                width:'200px',
                                                padding:'0',
                                            },
                                        items: [
                                                {
                                                    xtype: 'fieldset',
                                                    style:{
                                                        padding: '0',
                                                        left:'150px',
                                                        position:'absolute',
                                                        top:'5px'
                                                    },
                                                    border:false,
                                                    items: [],
                                                    id_grupo: 25
                                                }
                                            ]
                                        },
                                        /********************************************26 */
                                        {
                                        xtype: 'fieldset',
                                        border: false,
                                        style:{
                                                width:'200px',
                                                padding:'0',
                                            },
                                        items: [
                                                {
                                                    xtype: 'fieldset',
                                                    style:{
                                                        padding: '0',
                                                        position:'absolute',
                                                        left:'300px',
                                                        top:'5px'
                                                    },
                                                    border:false,
                                                    items: [],
                                                    id_grupo: 26
                                                }
                                            ]
                                        },
                                        /********************************************27 */
                                        {
                                        xtype: 'fieldset',
                                        border: false,
                                        style:{
                                                width:'200px',
                                                padding:'0',
                                            },
                                        items: [
                                                {
                                                    xtype: 'fieldset',
                                                    style:{
                                                        padding: '0',
                                                        position:'absolute',
                                                        left:'560px',
                                                        top:'5px'
                                                    },
                                                    border:false,
                                                    items: [],
                                                    id_grupo: 27
                                                }
                                            ]
                                        },
                                        /********************************************28 */
                                        {
                                        xtype: 'fieldset',
                                        border: false,
                                        style:{
                                                width:'200px',
                                                padding:'0',
                                            },
                                        items: [
                                                {
                                                    xtype: 'fieldset',
                                                    style:{
                                                        padding: '0',
                                                        position:'absolute',
                                                        left:'825px',
                                                        top:'5px'
                                                    },
                                                    border:false,
                                                    items: [],
                                                    id_grupo: 28
                                                }
                                            ]
                                        },
                                    ],
                        },
                    ],
                    id_grupo: 33
                },
                {
                style:{
                        //border:'1px solid green',
                        borderRadius:'2px',
                        width: '940px',
                        height:'250px',
                        position:'absolute',
                        top:'60px'
                        },
                items:[
                        /********************************************1 */
                        {
                        xtype: 'fieldset',
                        border: false,
                        style:{
                                width:'250px',
                                marginLeft:'-15px',
                            },
                        items: [

                                {
                                    xtype: 'fieldset',
                                    border: false,

                                    items: [],
                                    id_grupo: 1
                                }

                            ]
                        },
                        /********************************************2 */
                        {
                        xtype: 'fieldset',
                        border: false,
                        style:{
                                width:'200px',
                                marginLeft:'240px',
                                position:'absolute',
                                top: '1px'
                            },
                        items: [
                                {
                                    xtype: 'fieldset',
                                    border: false,
                                    items: [],
                                    id_grupo: 2
                                }
                            ]
                        },
                        /********************************************3 */
                        {
                        xtype: 'fieldset',
                        border: false,
                        style:{
                                width:'250px',
                                marginLeft:'350px',
                                position:'absolute',
                                top: '1px'
                            },
                        items: [
                                {
                                    xtype: 'fieldset',
                                    border:false,
                                    items: [],
                                    id_grupo: 3
                                }
                            ]
                        },
                        /********************************************4 */
                        {
                        xtype: 'fieldset',
                        border: false,
                        style:{
                                width:'250px',
                                marginLeft:'600px',
                                //marginTop: '-85px',
                                position:'absolute',
                                top: '1px',
                                left: '-20px'
                            },
                        items: [
                                {
                                    xtype: 'fieldset',
                                    border:false,
                                    items: [],
                                    id_grupo: 4
                                }
                            ]
                        },
                        /********************************************5 */
                        {
                        xtype: 'fieldset',
                        border: false,
                        style:{
                                width:'250px',
                                marginLeft:'790px',
                                position:'absolute',
                                top: '1px'
                            },
                        items: [
                                {
                                    xtype: 'fieldset',
                                    border:false,
                                    items: [],
                                    id_grupo: 5
                                }
                            ]
                        },
                        /********************************************6 */
                        {
                        xtype: 'fieldset',
                        border: false,
                        style:{
                                width:'80px',
                                marginLeft:'15px',
                                padding:'0',
                                position:'absolute',
                                top: '55px'
                            },
                        items: [
                                {
                                    xtype: 'fieldset',
                                    style:{
                                        width:'200px',
                                        marginRight:'10px' ,
                                        padding:'0'
                                    },
                                    border:false,
                                    items: [],
                                    id_grupo: 6
                                }
                            ]
                        },

                        /********************************************7 */
                        {
                        xtype: 'fieldset',
                        border: false,
                        style:{
                                width:'250px',
                                marginLeft:'220px',
                                padding:'0',
                                position:'absolute',
                                top: '49px'
                            },
                        items: [
                                {
                                    xtype: 'fieldset',
                                    style:{

                                        padding: '0'
                                    },
                                    border:false,
                                    items: [],
                                    id_grupo: 7
                                }
                            ]
                        },
                        /********************************************8 */
                        {
                        xtype: 'fieldset',
                        border: false,
                        style:{
                                width:'415px',
                                marginLeft:'540px',
                                padding:'0',
                                position:'absolute',
                                top: '55px'
                            },
                        items: [
                                {
                                    xtype: 'fieldset',
                                    style:{
                                        width:'400px',
                                        padding: '0'
                                    },
                                    border:false,
                                    items: [],
                                    id_grupo: 8
                                }
                            ]
                        },
                        /********************************************9 */
                        {
                        xtype: 'fieldset',
                        border: false,
                        style:{
                                width:'937px',
                                padding:'0',
                                height:'65px',
                                top: '90px'
                            },
                        items: [
                                {
                                    xtype: 'fieldset',
                                    style:{
                                        padding: '0',
                                        height:'65px'
                                    },
                                    border:false,
                                    items: [],
                                    id_grupo: 9
                                }
                            ]
                        },
                        /********************************************10 */
                        {
                        xtype: 'fieldset',
                        border: false,
                        style:{
                                width:'300px',
                                padding:'0',
                                marginLeft:'5px',
                                height:'65px',
                                top: '120px'
                            },
                        items: [
                                {
                                    xtype: 'fieldset',
                                    style:{
                                        padding: '0',
                                        height:'65px'
                                    },
                                    border:false,
                                    items: [],
                                    id_grupo: 10
                                }
                            ]
                        },
                        /********************************************11 */
                        {
                        xtype: 'fieldset',
                        border: false,
                        style:{
                                width:'435px',
                                padding:'0',
                                marginLeft:'350px',
                                position:'absolute',
                                top: '160px'
                            },
                        items: [
                                {
                                    xtype: 'fieldset',
                                    style:{
                                        padding: '0',
                                    },
                                    border:false,
                                    items: [],
                                    id_grupo: 11
                                }
                            ]
                        },
                        /********************************************13 */
                        {
                        xtype: 'fieldset',
                        border: false,
                        style:{
                                width:'300px',
                                padding:'0',
                                marginLeft:'5px',
                                position:'absolute',
                                top: '190px'
                            },
                        items: [
                                {
                                    xtype: 'fieldset',
                                    style:{
                                        padding: '0',
                                    },
                                    border:false,
                                    items: [],
                                    id_grupo: 13
                                }
                            ]
                        },
                        /********************************************14 */
                        {
                        xtype: 'fieldset',
                        border: false,
                        style:{
                                width:'250px',
                                padding:'0',
                                marginLeft:'350px',
                                position:'absolute',
                                top: '190px'
                            },
                        items: [
                                {
                                    xtype: 'fieldset',
                                    style:{
                                        padding: '0',
                                    },
                                    border:false,
                                    items: [],
                                    id_grupo: 14
                                }
                            ]
                        },
                        /********************************************15 */
                        {
                        xtype: 'fieldset',
                        border: false,
                        style:{
                                width:'200px',
                                padding:'0',
                                marginLeft:'600px',
                                position:'absolute',
                                top: '190px'
                            },
                        items: [
                                {
                                    xtype: 'fieldset',
                                    style:{
                                        padding: '0',
                                    },
                                    border:false,
                                    items: [],
                                    id_grupo: 15
                                }
                            ]
                        },

                ]},

                {
                    style:{
                        width:'940px',
                        position: 'absolute',
                        top: '320',
                        left: '15',
                        height:'90px',
                        border:'1px solid green',
                    },
                    items:[
                        {
                            xtype: 'fieldset',
                            border: false,
                            title:'DOCUMENTOS ORIGINALES',
                            style:{

                                    marging:'0',
                                    padding:'0'
                                },
                                items: [
                                        /********************************************16 */
                                        {
                                        xtype: 'fieldset',
                                        border: false,
                                        style:{
                                                width:'200px',
                                                padding:'10px 0 0 0',
                                                marginRight:'700px',
                                            },
                                        items: [
                                                {
                                                    xtype: 'fieldset',
                                                    style:{
                                                        padding: '0',
                                                    },
                                                    border:false,
                                                    items: [],
                                                    id_grupo: 16
                                                }
                                            ]
                                        },
                                        /********************************************17 */
                                        {
                                        xtype: 'fieldset',
                                        border: false,
                                        style:{
                                                width:'200px',
                                                padding:'0',
                                                marginLeft:'180px',
                                                marginRight:'600px',
                                            },
                                        items: [
                                                {
                                                    xtype: 'fieldset',
                                                    style:{
                                                        padding: '0',
                                                    },
                                                    border:false,
                                                    items: [],
                                                    id_grupo: 17
                                                }
                                            ]
                                        },
                                        /********************************************18 */
                                        {
                                        xtype: 'fieldset',
                                        border: false,
                                        style:{
                                                width:'200px',
                                                padding:'0',
                                                marginLeft:'280px',
                                                marginRight:'500px',
                                                marginTop:'-50px',
                                            },
                                        items: [
                                                {
                                                    xtype: 'fieldset',
                                                    style:{
                                                        padding: '0',
                                                    },
                                                    border:false,
                                                    items: [],
                                                    id_grupo: 18
                                                }
                                            ]
                                        },
                                        /********************************************19 */
                                        {
                                        xtype: 'fieldset',
                                        border: false,
                                        style:{
                                                width:'200px',
                                                padding:'0',
                                                marginLeft:'400px',
                                                marginRight:'600px',
                                                marginTop:'-20px',
                                            },
                                        items: [
                                                {
                                                    xtype: 'fieldset',
                                                    style:{
                                                        padding: '0',
                                                    },
                                                    border:false,
                                                    items: [],
                                                    id_grupo: 19
                                                }
                                            ]
                                        },
                                        /********************************************20 */

                                        {
                                        xtype: 'fieldset',
                                        border: false,
                                        style:{
                                                width:'200px',
                                                padding:'0',
                                                marginLeft:'450px',
                                                marginRight:'700px',
                                                marginTop:'-20px',
                                            },
                                        items: [
                                                {
                                                    xtype: 'fieldset',
                                                    style:{
                                                        padding: '0',
                                                    },
                                                    border:false,
                                                    items: [],
                                                    id_grupo: 20
                                                }
                                            ]
                                        },
                                        // /********************************************21 */
                                        {
                                        xtype: 'fieldset',
                                        border: false,
                                        style:{
                                                width:'200px',
                                                padding:'0',
                                                marginLeft:'550px',
                                                marginRight:'800px',
                                                marginTop:'-20px',
                                            },
                                        items: [
                                                {
                                                    xtype: 'fieldset',
                                                    style:{
                                                        padding: '0',
                                                    },
                                                    border:false,
                                                    items: [],
                                                    id_grupo: 21
                                                }
                                            ]
                                        },
                                        // /********************************************22 */
                                        {
                                        xtype: 'fieldset',
                                        border: false,
                                        style:{
                                                width:'200px',
                                                padding:'0',
                                                marginLeft:'650px',
                                                marginRight:'900px',
                                                marginTop:'-20px',
                                            },
                                        items: [
                                                {
                                                    xtype: 'fieldset',
                                                    style:{
                                                        padding: '0',
                                                    },
                                                    border:false,
                                                    items: [],
                                                    id_grupo: 22
                                                }
                                            ]
                                        },
                                        // /********************************************23 */
                                        {
                                        xtype: 'fieldset',
                                        border: false,
                                        style:{
                                                width:'200px',
                                                padding:'0',
                                                marginLeft:'780px',
                                                marginRight:'1000px',
                                                marginTop:'-20px',
                                            },
                                        items: [
                                                {
                                                    xtype: 'fieldset',
                                                    style:{
                                                        padding: '0',
                                                    },
                                                    border:false,
                                                    items: [],
                                                    id_grupo: 23
                                                }
                                            ]
                                        },
                                    ],
                        },
                    ],
                    id_grupo: 31
                },

            ]
        }
    ],

})
</script>
