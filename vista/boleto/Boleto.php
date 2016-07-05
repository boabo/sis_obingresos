<?php
/**
*@package pXP
*@file gen-Boleto.php
*@author  (jrivera)
*@date 07-06-2016 18:52:34
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.Boleto=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		this.grupo = 'no';
		Ext.Ajax.request({
                url:'../../sis_ventas_facturacion/control/Venta/getVariablesBasicas',                
                params: {'prueba':'uno'},
                success:this.successGetVariables,
                failure: this.conexionFailure,
                arguments:config,
                timeout:this.timeout,
                scope:this
            });	
    			
	},
	successGetVariables : function (response,request) {
		//llama al constructor de la clase padre
		Phx.vista.Boleto.superclass.constructor.call(this,request.arguments);
		this.init();		
		this.addButton('btnCaja',
            {
                text: 'Caja',
                iconCls: 'btransfer',
                disabled: true,
                handler: this.onCaja,
                tooltip: 'Envia el boleto para pago en caja'
            }
        );
        this.addButton('btnPagado',
            {
                text: 'Pagado',
                iconCls: 'bmoney',
                disabled: true,
                handler: this.onPagado,
                tooltip: 'Marca el boleto como pagado'
            }
        );
        
        
		this.store.baseParams.estado = 'borrador';
		this.iniciarEventos();
		this.seleccionarPuntoVentaSucursal();
				
	},
	
	
	seleccionarPuntoVentaSucursal : function () {
		
			var storeCombo = new Ext.data.JsonStore({
	                    url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
	                    id: 'id_punto_venta',
	                    root: 'datos',
	                    sortInfo: {
	                        field: 'nombre',
	                        direction: 'ASC'
	                    },
	                    totalProperty: 'total',
	                    fields: ['id_punto_venta', 'nombre', 'codigo','habilitar_comisiones'],
	                    remoteSort: true,
	                    baseParams: {par_filtro: 'puve.nombre#puve.codigo'}
	        });
			
	    
	    storeCombo.load({params:{start:0,limit:this.tam_pag}, 
	           callback : function (r) {
	                if (r.length == 1 ) {	                	                
	                    	this.id_punto_venta = r[0].data.id_punto_venta;	                    	
	                    	this.store.baseParams.id_punto_venta = r[0].data.id_punto_venta;
	                    	this.Cmp.id_forma_pago.store.baseParams.id_punto_venta = this.id_punto_venta;
	                    	this.Cmp.id_forma_pago2.store.baseParams.id_punto_venta = this.id_punto_venta;
	                    	this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('d/m/Y');	                    
	                    	this.load({params:{start:0, limit:this.tam_pag}});  	                    
	                } else {
	                	
	                	var combo2 = new Ext.form.ComboBox(
						    {
						        typeAhead: false,
						        fieldLabel: 'Punto de Venta',
						        allowBlank : false,						        
						        store: storeCombo,
						        mode: 'remote',
                				pageSize: 15,
						        triggerAction: 'all',
						        valueField : 'id_punto_venta',
                				displayField : 'nombre', 
						        forceSelection: true,
						        tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
						        allowBlank : false,
						        anchor: '100%'
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
					            width: 550,
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
						                		VentanaInicio.close(); 
							                    this.id_punto_venta  = combo2.getValue();
							                    this.Cmp.id_forma_pago.store.baseParams.id_punto_venta = this.id_punto_venta;
							                    this.Cmp.id_forma_pago2.store.baseParams.id_punto_venta = this.id_punto_venta;
							                    this.store.baseParams.id_punto_venta = combo2.getValue();							                    
							                    this.load({params:{start:0, limit:this.tam_pag}});
						                	}
						                },
										scope: this
					               }],
					            items: formularioInicio,
					            autoDestroy: true,
					            closeAction: 'close'
					        });
					      VentanaInicio.show();
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
					name: 'id_boleto'
			},
			type:'Field',
			form:true 
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'moneda_sucursal'
			},
			type:'Field',
			form:true 
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'ids_seleccionados'
			},
			type:'Field',
			form:true 
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'tc'
			},
			type:'NumberField',
			form:true 
		},
		
		
		
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'moneda_fp1'
			},
			type:'TextField',
			form:true 
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'moneda_fp2'
			},
			type:'TextField',
			form:true 
		},
		{
			config:{
				name: 'boletos',
				fieldLabel: 'Boletos a Pagar',				
				anchor: '80%',
				gwidth: 80,
				readOnly:true
				
			},
				type:'TextArea',				
				id_grupo:2,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'nro_boleto',
				fieldLabel: 'Billete: 930-',
				allowBlank: false,
				anchor: '80%',
				gwidth: 120,
				maxLength:10,
				minLength:10,
				enableKeyEvents:true
			},
				type:'TextField',
				filters:{pfiltro:'bol.nro_boleto',type:'string'},
				id_grupo:0,
				grid:true,
				form:true,
				bottom_filter: true
		},
		
		{
			config:{
				name: 'pasajero',
				fieldLabel: 'Pasajero',				
				anchor: '100%',
				gwidth: 130,				
				readOnly:true
			},
				type:'TextField',
				filters:{pfiltro:'bol.pasajero',type:'string'},
				id_grupo:0,
				grid:true,
				form:true,
				bottom_filter: true
		},
		{
			config:{
				name: 'total',
				fieldLabel: 'Total Boleto',				
				anchor: '80%',
				gwidth: 125	,
				readOnly:true			
			},
				type:'NumberField',
				filters:{pfiltro:'bol.total',type:'numeric'},
				id_grupo:0,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'moneda',
				fieldLabel: 'Moneda de Emision',				
				anchor: '80%',
				gwidth: 150,
				readOnly:true
				
			},
				type:'TextField',
				filters:{pfiltro:'bol.moneda',type:'string'},
				id_grupo:0,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'origen',
				fieldLabel: 'Origen',				
				anchor: '80%',
				gwidth: 80,
				readOnly:true
				
			},
				type:'TextField',
				filters:{pfiltro:'bol.origen',type:'string'},
				id_grupo:0,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'destino',
				fieldLabel: 'Destino',				
				anchor: '80%',
				gwidth: 80,
				readOnly:true
			},
				type:'TextField',
				filters:{pfiltro:'bol.destino',type:'string'},
				id_grupo:0,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'estado',
				fieldLabel: 'Estado',				
				gwidth: 100,
				readOnly:true				
			},
				type:'TextField',
				filters:{pfiltro:'bol.estado',type:'string'},				
				grid:true,
				id_grupo:0,
				form:true
		},
		{
			config:{
				name: 'comision',
				fieldLabel: 'Comisión AGT',
				allowBlank:true,				
				anchor: '80%',
				allowDecimals:true,
				decimalPrecision:2,
				allowNegative : false,
				disabled:true,
				gwidth: 125				
			},
				type:'NumberField',				
				id_grupo:0,
				grid:true,
				form:true
		},
		{
            config: {
                name: 'id_forma_pago',
                fieldLabel: 'Forma de Pago1',
                allowBlank: false,
                emptyText: 'Forma de Pago...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_obingresos/control/FormaPago/listarFormaPago',
                    id: 'id_forma_pago',
                    root: 'datos',
                    sortInfo: {
                        field: 'nombre',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_forma_pago', 'nombre', 'moneda','pais','codigo','forma_pago'],
                    remoteSort: true,
                    baseParams: {par_filtro: 'fop.nombre#mon.codigo_internacional'}
                }),
                valueField: 'id_forma_pago',
                displayField: 'forma_pago',
                gdisplayField: 'forma_pago',
                hiddenName: 'id_forma_pago',
                tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre}</p><p>Moneda:{moneda}</p> </div></tpl>',
                forceSelection: true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'remote',
                pageSize: 15,
                queryDelay: 1000,               
                gwidth: 150,
                minChars: 2,
                disabled:true,
                renderer : function(value, p, record) {
                    return String.format('{0}', record.data['forma_pago']);
                }
            },
            type: 'ComboBox',
            id_grupo: 1,
            grid: true,
            form: true
        },
        {
			config:{
				name: 'monto_forma_pago',
				fieldLabel: 'Monto a Pagar 1',
				allowBlank:false,				
				anchor: '80%',
				allowDecimals:true,
				decimalPrecision:2,
				allowNegative : false,
				disabled:true,
				gwidth: 125				
			},
				type:'NumberField',				
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'numero_tarjeta',
				fieldLabel: 'No Tarjeta 1',
				allowBlank: true,
				anchor: '80%',
				gwidth: 150,
				maxLength:50
			},
				type:'TextField',				
				id_grupo:1,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'ctacte',
				fieldLabel: 'Cta. Corriente 1',
				allowBlank: true,
				anchor: '80%',
				gwidth: 150,
				maxLength:20
			},
				type:'TextField',				
				id_grupo:1,
				grid:false,
				form:true
		},
		
		{
            config: {
                name: 'id_forma_pago2',
                fieldLabel: 'Forma de Pago 2',
                allowBlank: true,
                emptyText: 'Forma de Pago...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_obingresos/control/FormaPago/listarFormaPago',
                    id: 'id_forma_pago',
                    root: 'datos',
                    sortInfo: {
                        field: 'nombre',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_forma_pago', 'nombre', 'moneda','pais','codigo','forma_pago'],
                    remoteSort: true,
                    baseParams: {par_filtro: 'fop.nombre#mon.codigo_internacional'}
                }),
                valueField: 'id_forma_pago',
                displayField: 'forma_pago',
                gdisplayField: 'forma_pago2',
                hiddenName: 'id_forma_pago',
                tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre}</p><p>Moneda:{moneda}</p> </div></tpl>',
                forceSelection: true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'remote',
                pageSize: 15,
                queryDelay: 1000,               
                gwidth: 150,
                minChars: 2,
                disabled:true,
                renderer : function(value, p, record) {
                    return String.format('{0}', record.data['forma_pago2']);
                }
            },
            type: 'ComboBox',
            id_grupo: 1,
            grid: true,
            form: true
        },
        {
			config:{
				name: 'monto_forma_pago2',
				fieldLabel: 'Monto a Pagar 2',
				allowBlank:true,				
				anchor: '80%',
				allowDecimals:true,
				decimalPrecision:2,
				allowNegative : false,
				disabled:true,
				gwidth: 125				
			},
				type:'NumberField',				
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'numero_tarjeta2',
				fieldLabel: 'No Tarjeta 2',
				allowBlank: true,
				anchor: '80%',
				gwidth: 150,
				maxLength:50
			},
				type:'TextField',				
				id_grupo:1,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'ctacte2',
				fieldLabel: 'Cta. Corriente 2',
				allowBlank: true,
				anchor: '80%',
				gwidth: 150,
				maxLength:20
			},
				type:'TextField',				
				id_grupo:1,
				grid:false,
				form:true
		},
		
		
		{
			config:{
				name: 'fecha_emision',
				fieldLabel: 'Fecha Emision',				
				gwidth: 100,
				format: 'd/m/Y', 
				renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'bol.fecha_emision',type:'date'},				
				grid:true,
				form:false
		},
		{
			config:{
				name: 'cupones',
				fieldLabel: 'Cupones',				
				gwidth: 100
				
			},
				type:'NumberField',
				filters:{pfiltro:'bol.cupones',type:'numeric'},				
				grid:true,
				form:false
		},
		{
			config:{
				name: 'codigo_noiata',
				fieldLabel: 'Cod. Noiata',				
				gwidth: 100				
			},
				type:'TextField',
				
				grid:true,
				form:false
		},
		
		{
			config:{
				name: 'codigo_agencia',
				fieldLabel: 'agt',				
				gwidth: 100
			},
				type:'TextField',								
				grid:true,
				form:false
		},
		{
			config:{
				name: 'nombre_agencia',
				fieldLabel: 'Agencia',				
				gwidth: 120
			},
				type:'TextField',							
				grid:true,
				form:false
		},
		{
			config:{
				name: 'neto',
				fieldLabel: 'Neto',					
				gwidth: 100				
			},
				type:'NumberField',
				filters:{pfiltro:'bol.neto',type:'numeric'},				
				grid:true,
				form:false
		},
		{
			config:{
				name: 'tipopax',
				fieldLabel: 'Tipo Pasajero',					
				gwidth: 110
			},
				type:'TextField',
				filters:{pfiltro:'bol.tipopax',type:'string'},				
				grid:true,
				form:false
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
				filters:{pfiltro:'bol.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},		
		
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: '',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'bol.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
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
				filters:{pfiltro:'bol.fecha_reg',type:'date'},
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
				filters:{pfiltro:'bol.usuario_ai',type:'string'},
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
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,	
	fwidth: '70%',
	title:'Boleto',
	ActSave:'../../sis_obingresos/control/Boleto/modificarBoletoVenta',
	ActDel:'../../sis_obingresos/control/Boleto/eliminarBoleto',
	ActList:'../../sis_obingresos/control/Boleto/listarBoleto',
	id_store:'id_boleto',
	fields: [
		{name:'id_boleto', type: 'numeric'},
		{name:'fecha_emision', type: 'date',dateFormat:'Y-m-d'},
		{name:'codigo_noiata', type: 'string'},		
		{name:'cupones', type: 'numeric'},
		{name:'ruta', type: 'string'},
		{name:'estado', type: 'string'},
		{name:'id_agencia', type: 'numeric'},
		{name:'moneda', type: 'string'},
		{name:'total', type: 'numeric'},
		{name:'pasajero', type: 'string'},
		{name:'id_moneda_boleto', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'gds', type: 'string'},
		{name:'comision', type: 'numeric'},
		{name:'codigo_agencia', type: 'string'},
		{name:'neto', type: 'numeric'},
		{name:'tipopax', type: 'string'},
		{name:'origen', type: 'string'},
		{name:'destino', type: 'string'},
		{name:'retbsp', type: 'string'},
		{name:'monto_pagado_moneda_boleto', type: 'numeric'},
		{name:'tipdoc', type: 'string'},
		{name:'liquido', type: 'numeric'},
		{name:'nro_boleto', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'nombre_agencia', type: 'string'},
		{name:'id_forma_pago', type: 'numeric'},
		{name:'forma_pago', type: 'string'},
		{name:'numero_tarjeta', type: 'string'},
		{name:'ctacte', type: 'string'},
		{name:'codigo_forma_pago', type: 'string'},
		{name:'monto_forma_pago', type: 'numeric'},
		
		{name:'id_forma_pago2', type: 'numeric'},
		{name:'forma_pago2', type: 'string'},
		{name:'numero_tarjeta2', type: 'string'},
		{name:'ctacte2', type: 'string'},
		{name:'codigo_forma_pago2', type: 'string'},
		{name:'monto_forma_pago2', type: 'numeric'},
		{name:'pais', type: 'string'},
		{name:'moneda_sucursal', type: 'string'},		
		{name:'tc', type: 'numeric'},
		{name:'moneda_fp1', type: 'string'},
		{name:'moneda_fp2', type: 'string'},
		
	],
	sortInfo:{
		field: 'id_boleto',
		direction: 'DESC'
	},
	arrayDefaultColumHidden:['estado_reg','usuario_ai',
    'fecha_reg','fecha_mod','usr_reg','usr_mod','fecha_emision','cupones','codigo_noiata','codigo_agencia','neto','tipopax','nombre_agencia','comision'],
	rowExpander: new Ext.ux.grid.RowExpander({
            tpl : new Ext.Template(
                '<br>',   
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha de Emision:&nbsp;&nbsp;</b> {fecha_emision:date("d/m/Y")}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b># Cupones:&nbsp;&nbsp;</b> {cupones}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Nombre Agencia:&nbsp;&nbsp;</b> {nombre_agencia}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Codigo NoIata:&nbsp;&nbsp;</b> {codigo_noiata}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Codigo Agencia:&nbsp;&nbsp;</b> {codigo_agencia}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Neto:&nbsp;&nbsp;</b> {neto}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Comision AGT:&nbsp;&nbsp;</b> {comision}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Tipo de Pasajero:&nbsp;&nbsp;</b> {tipopax}</p>',             
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha de Registro:&nbsp;&nbsp;</b> {fecha_reg:date("d/m/Y")}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha Ult. Modificación:&nbsp;&nbsp;</b> {fecha_mod:date("d/m/Y")}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Creado por:&nbsp;&nbsp;</b> {usr_reg}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Modificado por:&nbsp;&nbsp;</b> {usr_mod}</p><br>'
            )
    }),
	bdel:true,
	bsave:false,
	iniciarEventos : function () {
		this.Cmp.nro_boleto.on('keyup',function(){
			
			if (this.Cmp.nro_boleto.getValue().length == 10) {
				Phx.CP.loadingShow();
				Ext.Ajax.request({
	                url:'../../sis_obingresos/control/Boleto/getBoletoServicio',                
	                params: {'nro_boleto':this.Cmp.nro_boleto.getValue(),
	                		'id_punto_venta':this.id_punto_venta},
	                success:this.successGetBoletoServicio,
	                failure: this.conexionFailure,	                
	                timeout:this.timeout,
	                scope:this
	            });
			}
			
		},this);
		
		this.Cmp.monto_forma_pago.on('change',function(field,newValue,oldValue){
			if (newValue < oldValue) {
				this.Cmp.id_forma_pago2.setDisabled(false);
				this.Cmp.monto_forma_pago2.setDisabled(false);
			}			
		},this);
		
		this.Cmp.id_forma_pago.on('select', function (combo,record,index){
			
			this.Cmp.moneda_fp1.setValue(record.data.moneda);
			this.manejoComponentesFp1(record.data.id_forma_pago,record.data.codigo);
			if (this.grupo == 'no') {
				var monto_pagado_fp2 = this.getMontoMonBol(this.Cmp.monto_forma_pago2.getValue(),this.Cmp.moneda_fp2.getValue());
				
				if (monto_pagado_fp2 > -1) {
					//Si la forma de pago y el boleto estan en la misma moneda
					if (this.Cmp.moneda.getValue() == record.data.moneda){				
						this.Cmp.monto_forma_pago.setValue(this.Cmp.total.getValue() - monto_pagado_fp2 - this.Cmp.comision.getValue());
						
					}
					//Si el boleto esta en usd y la forma de pago es distinta a usd y la forma de pago es igual a la moneda de la sucursal
					else if (this.Cmp.moneda.getValue() == 'USD' && record.data.moneda == this.Cmp.moneda_sucursal.getValue()) {
						//convertir de  dolares a moneda sucursal(multiplicar)				
						this.Cmp.monto_forma_pago.setValue(this.round(((this.Cmp.total.getValue()- monto_pagado_fp2 - this.Cmp.comision.getValue())*this.Cmp.tc.getValue()),2));
						
					//Si el boleto esta en moneda sucursal y la forma de pago es usd y la moneda de la sucursales distinta a usd
					} else if (this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && record.data.moneda == 'USD') {
						//convertir de  moneda sucursal a dolares(dividir)				
						this.Cmp.monto_forma_pago.setValue(this.round(((this.Cmp.total.getValue()- monto_pagado_fp2 - this.Cmp.comision.getValue())/this.Cmp.tc.getValue()),2));
						
					} else {
						this.Cmp.monto_forma_pago.setValue(0);
						
					}
				} else {
					this.Cmp.monto_forma_pago.setValue(0);
				}
			} else {
				this.calculoFp1Grupo(record);
			}
		},this);
		
		this.Cmp.id_forma_pago2.on('select', function (combo,record,index){
			this.Cmp.moneda_fp2.setValue(record.data.moneda);
			
			this.manejoComponentesFp2(record.data.id_forma_pago,record.data.codigo);
			if (this.grupo == 'no') {
				var monto_pagado_fp1 = this.getMontoMonBol(this.Cmp.monto_forma_pago.getValue(),this.Cmp.moneda_fp1.getValue());	
				
				if (monto_pagado_fp1 > -1) {
					//Si la forma de pago y el boleto estan en la misma moneda
					if (this.Cmp.moneda.getValue() == record.data.moneda){				
						this.Cmp.monto_forma_pago2.setValue(this.Cmp.total.getValue() - this.Cmp.comision.getValue()- monto_pagado_fp1);
					}
					//Si el boleto esta en usd y la forma de pago es distinta a usd y la forma de pago es igual a la moneda de la sucursal
					else if (this.Cmp.moneda.getValue() == 'USD' && record.data.moneda == this.Cmp.moneda_sucursal.getValue()) {
						//convertir de  dolares a moneda sucursal(multiplicar)				
						this.Cmp.monto_forma_pago2.setValue(this.round(((this.Cmp.total.getValue()- monto_pagado_fp1 - this.Cmp.comision.getValue())*this.Cmp.tc.getValue()),2));
					//Si el boleto esta en moneda sucursal y la forma de pago es usd y la moneda de la sucursales distinta a usd
					} else if (this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && record.data.moneda == 'USD') {
						//convertir de  moneda sucursal a dolares(dividir)				
						this.Cmp.monto_forma_pago2.setValue(this.round(((this.Cmp.total.getValue()-monto_pagado_fp1 - this.Cmp.comision.getValue())/this.Cmp.tc.getValue()),2));
					} else {
						this.Cmp.monto_forma_pago2.setValue(0);
					}
				} else {
					this.Cmp.monto_forma_pago2.setValue(0);
				}
			} else {
				this.calculoFp2Grupo(record);
			}
		},this);
		
		
	},
	//devuelve el monto en la moenda del boleto
	getMontoMonBol : function (monto, moneda_fp) {
		//Si la forma de pago y el boleto estan en la misma moneda
		if (monto == 0) {
			return 0;
		} else if (this.Cmp.moneda.getValue() == moneda_fp){				
			return monto;
		} //Si el boleto esta en usd y la forma de pago es distinta a usd y la forma de pago es igual a la moneda de la sucursal
		else if (this.Cmp.moneda.getValue() == 'USD' && moneda_fp == this.Cmp.moneda_sucursal.getValue()) {
			//convertir a dolares(dividir)				
			return this.roundMenor(monto/this.Cmp.tc.getValue(),2);
		//Si el boleto esta en moneda sucursal y la forma de pago es usd y la moneda de la sucursales distinta a usd
		} else if (this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && moneda_fp == 'USD') {
			//convertir a moneda sucursal(mutiplicar)				
			return this.roundMenor(monto*this.Cmp.tc.getValue(),2);
		} else {
			return -1;
		}
	},
	successGetBoletoServicio : function (response,request) {
		
		
		var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(response.responseText));
		
		if (objRes.ROOT) {
			if (objRes.ROOT.error == true){
				this.conexionFailure(response);
			}
			//alert (objRes.ROOT.detalle.mensaje);
		} else {
				Phx.CP.loadingHide();
				this.Cmp.id_boleto.setValue(objRes.datos[0].id_boleto);
				this.Cmp.total.setValue(objRes.datos[0].total);
				this.Cmp.pasajero.setValue(objRes.datos[0].pasajero);
				this.Cmp.moneda.setValue(objRes.datos[0].moneda);
				this.Cmp.origen.setValue(objRes.datos[0].origen);
				this.Cmp.destino.setValue(objRes.datos[0].destino);
				this.Cmp.comision.setValue(objRes.datos[0].comision);
				
				this.Cmp.id_forma_pago.setValue(objRes.datos[0].id_forma_pago);
				this.Cmp.id_forma_pago.setRawValue(objRes.datos[0].forma_pago);
				this.Cmp.monto_forma_pago.setValue(objRes.datos[0].monto_forma_pago);
				this.Cmp.moneda_fp1.setValue(objRes.datos[0].moneda_fp1);
				
				this.Cmp.id_forma_pago2.setValue(objRes.datos[0].id_forma_pago2);
				this.Cmp.id_forma_pago2.setRawValue(objRes.datos[0].forma_pago2);
				this.Cmp.monto_forma_pago2.setValue(objRes.datos[0].monto_forma_pago2);
				this.Cmp.moneda_fp2.setValue(objRes.datos[0].moneda_fp2);
				
				this.Cmp.moneda_sucursal.setValue(objRes.datos[0].moneda_sucursal);
				this.Cmp.tc.setValue(objRes.datos[0].tc);
				this.Cmp.comision.setDisabled(false);
				
				this.manejoComponentesFp1(objRes.datos[0].id_forma_pago,objRes.datos[0].codigo_forma_pago);
				this.manejoComponentesFp2(objRes.datos[0].id_forma_pago2,objRes.datos[0].codigo_forma_pago2);
							
		}
	},
	onButtonEdit : function () {
		Phx.vista.Boleto.superclass.onButtonEdit.call(this);
		this.Cmp.ids_seleccionados.reset();
		this.ocultarGrupo(2);
		this.mostrarGrupo(0);
		this.grupo = 'no';
		this.Cmp.nro_boleto.allowBlank = false;
		this.Cmp.comision.setDisabled(false);
		this.Cmp.nro_boleto.setDisabled(true);
		this.manejoComponentesFp1(this.sm.getSelected().data['id_forma_pago'],this.sm.getSelected().data['codigo_forma_pago']);
		this.manejoComponentesFp2(this.sm.getSelected().data['id_forma_pago2'],this.sm.getSelected().data['codigo_forma_pago2']);
				
	},
	onButtonNew : function () {
		Phx.vista.Boleto.superclass.onButtonNew.call(this);
		this.Cmp.ids_seleccionados.reset();
		this.grupo = 'no';
		this.Cmp.comision.setDisabled(true);
		this.Cmp.nro_boleto.allowBlank = false;
		this.ocultarGrupo(2);
		this.mostrarGrupo(0);
		this.Cmp.nro_boleto.setDisabled(false);
		this.ocultarComponente(this.Cmp.numero_tarjeta);
		this.ocultarComponente(this.Cmp.ctacte);
		
	},
	
	onPagado : function () {
		var rec = this.sm.getSelected();
		Phx.CP.loadingShow();
		Ext.Ajax.request({
	                url:'../../sis_obingresos/control/Boleto/cambiaEstadoBoleto',                
	                params: {'id_boleto':rec.data.id_boleto,
	                		'accion':'pagado'},
	                success:this.successSave,
	                failure: this.conexionFailure,	                
	                timeout:this.timeout,
	                scope:this
	            });
		
	},
	onGrupo : function () {
		Phx.vista.Boleto.superclass.onButtonNew.call(this);
		this.grupo = 'si';
		var seleccionados = this.sm.getSelections();
		this.total_grupo = new Object;
		this.total_grupo['USD'] = 0;
		this.total_grupo[seleccionados[0].data.moneda_sucursal] = 0;
		
		
		for (var i = 0 ; i< seleccionados.length;i++) {
			if (i == 0) {
				this.Cmp.ids_seleccionados.setValue(seleccionados[i].data.id_boleto);
				this.Cmp.boletos.setValue('930' + seleccionados[i].data.nro_boleto);
			} else {
				this.Cmp.ids_seleccionados.setValue(this.Cmp.ids_seleccionados.getValue() + ',' + seleccionados[i].data.id_boleto);
		    	this.Cmp.boletos.setValue(this.Cmp.boletos.getValue() + ', 930' + seleccionados[i].data.nro_boleto);
			}
			if (seleccionados[i].data.moneda_sucursal == seleccionados[i].data.moneda) {				
				this.total_grupo[seleccionados[0].data.moneda_sucursal] += (parseFloat(seleccionados[i].data.total) - parseFloat(seleccionados[i].data.comision));
				this.total_grupo['USD'] += this.round((seleccionados[i].data.total - seleccionados[i].data.comision) / seleccionados[i].data.tc , 2);
			} else if (seleccionados[i].data.moneda == 'USD') {
				
				this.total_grupo[seleccionados[0].data.moneda_sucursal] += this.round((seleccionados[i].data.total - seleccionados[i].data.comision)* seleccionados[i].data.tc , 2);
				this.total_grupo['USD'] += (parseFloat(seleccionados[i].data.total) - parseFloat(seleccionados[i].data.comision));
			} else {
				alert('No se puede calcular la forma de pago ya que la moneda de un boleto no es la moenda de la sucursal ni dolares americanos');
				return;
			}
		}	
		
		//habilitamos el formulario
		this.mostrarGrupo(2);	
		this.ocultarGrupo(0);	
		this.Cmp.id_forma_pago.setDisabled(false);
		this.Cmp.monto_forma_pago.setDisabled(false);
		this.Cmp.nro_boleto.allowBlank = true;
		this.moneda_grupo_fp1 = '';
		this.moneda_grupo_fp2 = '';
		this.tc_grupo = seleccionados[0].data.tc;
		
	},
	calculoFp1Grupo : function (record) {
		this.moneda_grupo_fp1 = record.data.moneda;
		if (this.moneda_grupo_fp2 == '') {
			this.Cmp.monto_forma_pago.setValue(this.total_grupo[record.data.moneda]);
		} else if (this.moneda_grupo_fp2 == this.moneda_grupo_fp1) {
			this.Cmp.monto_forma_pago.setValue(this.total_grupo[record.data.moneda] - this.Cmp.monto_forma_pago2.getValue());
		} else {
			if (this.moneda_grupo_fp2 == 'USD') {
				this.Cmp.monto_forma_pago.setValue(this.total_grupo[record.data.moneda] - this.roundMenor(this.Cmp.monto_forma_pago2.getValue() * this.tc_grupo , 2));
			} else {
				this.Cmp.monto_forma_pago.setValue(this.total_grupo[record.data.moneda] - this.roundMenor(this.Cmp.monto_forma_pago2.getValue() / this.tc_grupo , 2));
			}
		}
		
	},
	calculoFp2Grupo : function (record) {
		
		this.moneda_grupo_fp2 = record.data.moneda;
		if (this.moneda_grupo_fp1 == '') {
			this.Cmp.monto_forma_pago2.setValue(this.total_grupo[record.data.moneda]);
		} else if (this.moneda_grupo_fp2 == this.moneda_grupo_fp1) {
			this.Cmp.monto_forma_pago2.setValue(this.total_grupo[record.data.moneda] - this.Cmp.monto_forma_pago.getValue());
		} else {
			if (this.moneda_grupo_fp1 == 'USD') {
				this.Cmp.monto_forma_pago2.setValue(this.total_grupo[record.data.moneda] - this.roundMenor(this.Cmp.monto_forma_pago.getValue() * this.tc_grupo , 2));
			} else {
				alert(this.total_grupo[record.data.moneda]);
				this.Cmp.monto_forma_pago2.setValue(this.total_grupo[record.data.moneda] - this.roundMenor(this.Cmp.monto_forma_pago.getValue() / this.tc_grupo , 2));
			}
		}
	},
	onCaja : function () {
		var rec = this.sm.getSelected();
		Phx.CP.loadingShow();
		Ext.Ajax.request({
	                url:'../../sis_obingresos/control/Boleto/cambiaEstadoBoleto',                
	                params: {'id_boleto':rec.data.id_boleto,
	                		'accion':'caja'},
	                success:this.successSave,
	                failure: this.conexionFailure,	                
	                timeout:this.timeout,
	                scope:this
	            });
		
	},
	tabsouth:[
         {
          url:'../../../sis_obingresos/vista/boleto_forma_pago/BoletoFormaPago.php',
          title:'Formas de Pago', 
          height:'40%',
          cls:'BoletoFormaPago'
         },
         {
          url:'../../../sis_obingresos/vista/boleto_impuesto/BoletoImpuesto.php',
          title:'Impuestos - Tasas', 
          height:'40%',
          cls:'BoletoImpuesto'
         }
          
        ],
    Grupos:[{ 
        layout: 'column',
        items:[
            {
                xtype:'fieldset',
                layout: 'form',
                border: true,
                title: 'Datos Boleto/Comision',
                bodyStyle: 'padding:0 10px 0;',
                columnWidth: 0.5,
                items:[],
                id_grupo:0,
                collapsible:true
            },
            {
                xtype:'fieldset',
                layout: 'form',
                border: true,
                title: 'Boletos',
                bodyStyle: 'padding:0 10px 0;',
                columnWidth: 0.5,
                items:[],
                id_grupo:2,
                collapsible:true
            },
            {
                xtype:'fieldset',
                layout: 'form',
                border: true,
                title: 'Formas de Pago',
                bodyStyle: 'padding:0 10px 0;',
                columnWidth: 0.5,
                items:[],
                id_grupo:1,
                collapsible:true,
                collapsed:false
            }
            ]
        }],
    preparaMenu:function()
    {	var rec = this.sm.getSelected();
          
        Phx.vista.Boleto.superclass.preparaMenu.call(this); 
        if (rec.data.estado == 'borrador') {
        	this.getBoton('btnCaja').enable(); 
        } else {
        	this.getBoton('btnCaja').disable(); 
        }   
           
        this.getBoton('btnPagado').enable();                 
        
    },
    liberaMenu:function()
    {	
                
        Phx.vista.Boleto.superclass.liberaMenu.call(this);
        this.getBoton('btnPagado').disable(); 
        this.getBoton('btnCaja').disable();         
    },
    round : function(value, decimals) {
    	return Math.ceil(value*100)/100;    	
	},
	roundMenor : function(value, decimals) {
    	return Math.floor(value*100)/100;    	
	},
	manejoComponentesFp1 : function (id_fp1,codigo_fp1){
		//forma de pago 1		
		if (id_fp1 == 0) {
			this.Cmp.id_forma_pago.setDisabled(true);
			this.Cmp.monto_forma_pago.setDisabled(true);
			this.ocultarComponente(this.Cmp.numero_tarjeta);
			this.ocultarComponente(this.Cmp.ctacte);
			this.Cmp.numero_tarjeta.allowBlank = true;
			this.Cmp.ctacte.allowBlank = true;
		} else {
			this.Cmp.id_forma_pago.setDisabled(false);
			this.Cmp.monto_forma_pago.setDisabled(false);
			if (codigo_fp1.startsWith("CC") || 
				codigo_fp1.startsWith("SF")) {
				this.ocultarComponente(this.Cmp.ctacte);
				this.Cmp.ctacte.reset();
				this.mostrarComponente(this.Cmp.numero_tarjeta);
				this.Cmp.numero_tarjeta.allowBlank = false;
				this.Cmp.ctacte.allowBlank = true;
				//tarjeta de credito		
			} else if (codigo_fp1.startsWith("CT")) {
				//cuenta corriente
				this.ocultarComponente(this.Cmp.numero_tarjeta);
				this.Cmp.numero_tarjeta.reset();
				this.mostrarComponente(this.Cmp.ctacte);
				this.Cmp.numero_tarjeta.allowBlank = true;
				this.Cmp.ctacte.allowBlank = false;
			} else {
				this.ocultarComponente(this.Cmp.numero_tarjeta);
				this.ocultarComponente(this.Cmp.ctacte);
				this.Cmp.numero_tarjeta.reset();
				this.Cmp.ctacte.reset();
				this.Cmp.numero_tarjeta.allowBlank = true;
				this.Cmp.ctacte.allowBlank = true;
			}
		}
	},
	manejoComponentesFp2 : function (id_fp2,codigo_fp2){
		if (id_fp2) {	
			//forma de pago 2
			if (id_fp2 == 0) {
				this.Cmp.id_forma_pago2.setDisabled(true);
				this.Cmp.monto_forma_pago2.setDisabled(true);
				this.ocultarComponente(this.Cmp.numero_tarjeta2);
				this.ocultarComponente(this.Cmp.ctacte2);
				this.Cmp.numero_tarjeta2.allowBlank = true;
				this.Cmp.ctacte2.allowBlank = true;
				this.Cmp.numero_tarjeta2.reset();
				this.Cmp.ctacte2.reset();
			} else {
				this.Cmp.id_forma_pago2.setDisabled(false);
				this.Cmp.monto_forma_pago2.setDisabled(false);
				if (codigo_fp2.startsWith("CC") || 
					codigo_fp2.startsWith("SF")) {
					//tarjeta de credito	
					this.ocultarComponente(this.Cmp.ctacte2);
					this.Cmp.ctacte2.reset();
					this.mostrarComponente(this.Cmp.numero_tarjeta2);
					this.Cmp.numero_tarjeta2.allowBlank = false;
					this.Cmp.ctacte2.allowBlank = true;
						
				} else if (codigo_fp2.startsWith("CT")) {
					//cuenta corriente
					this.ocultarComponente(this.Cmp.numero_tarjeta2);
					this.Cmp.numero_tarjeta2.reset();
					this.mostrarComponente(this.Cmp.ctacte2);
					this.Cmp.numero_tarjeta2.allowBlank = true;
					this.Cmp.ctacte2.allowBlank = false;
				} else {
					this.ocultarComponente(this.Cmp.numero_tarjeta2);
					this.ocultarComponente(this.Cmp.ctacte2);
					this.Cmp.numero_tarjeta2.allowBlank = true;
					this.Cmp.ctacte2.allowBlank = true;
					this.Cmp.numero_tarjeta2.reset();
					this.Cmp.ctacte2.reset();
				}
			}
		} else {
			this.ocultarComponente(this.Cmp.numero_tarjeta2);
			this.ocultarComponente(this.Cmp.ctacte2);
			this.Cmp.numero_tarjeta2.allowBlank = true;
			this.Cmp.ctacte2.allowBlank = true;
			this.Cmp.id_forma_pago2.setDisabled(true);
			this.Cmp.monto_forma_pago2.setDisabled(true);
		}
		
	}
    
	}
)
</script>
		
		