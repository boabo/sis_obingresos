<?php
/**
*@package pXP
*@file gen-DetalleBoletosWeb.php
*@author  (jrivera)
*@date 28-09-2017 18:47:46
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.DetalleBoletosWeb=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		if (this.maestro) {
			if('id_agencia' in this.maestro){			    
			    this.initButtons=[this.fecha_ini,this.fecha_fin];
			}
		}
		
    	//llama al constructor de la clase padre
		Phx.vista.DetalleBoletosWeb.superclass.constructor.call(this,config);
		this.init();
		
		if (this.maestro) {
			if('id_agencia' in this.maestro){
			    this.store.baseParams.id_agencia = this.maestro.id_agencia;
			    
			    if('id_periodo_venta' in this.maestro) { 
			    	this.store.baseParams.id_periodo_venta = this.maestro.id_periodo_venta;
			    }
			    this.load({params:{start:0, limit:this.tam_pag}});
			    this.initButtons=[this.fecha_ini,this.fecha_fin];
			    this.iniciarEventos();
			}
		}
		
	},
	primeraCarga : true,
	fecha_ini : new Ext.form.DateField({
        format: 'd/m/Y',
        fieldLabel: 'Fecha Ini',
        width:125,
        emptyText:'Fecha Ini...'
    }), 
    fecha_fin : new Ext.form.DateField({
        format: 'd/m/Y',
        fieldLabel: 'Fecha Fin',
        width:125,
        emptyText:'Fecha Fin...'
    }),  
			
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
			config:{
				name: 'billete',
				fieldLabel: 'Billete',
				allowBlank: false,
				anchor: '80%',
				gwidth: 110,
				maxLength:15
			},
				type:'TextField',
				filters:{pfiltro:'detbol.billete',type:'string'},
				id_grupo:1,
				grid:true,
				form:false,
            	bottom_filter: true
		},	
		
		{
			config:{
				name: 'fecha',
				fieldLabel: 'Fecha Emision',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'detbol.fecha',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'moneda',
				fieldLabel: 'Moneda',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:3,
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', value);
						}
						else{
							return '<b><p align="right">Total: &nbsp;&nbsp; </p></b>';
						}
					} 
			},
				type:'TextField',
				filters:{pfiltro:'detbol.moneda',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'importe',
				fieldLabel: 'Total',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650,
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
				filters:{pfiltro:'detbol.importe',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		
		{
			config:{
				name: 'neto',
				fieldLabel: 'Neto',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650,
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
				filters:{pfiltro:'detbol.neto',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		
		{
			config:{
				name: 'comision',
				fieldLabel: 'Comision',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650,
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
				filters:{pfiltro:'detbol.comision',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'pnr',
				fieldLabel: 'PNR',
				allowBlank: false,
				anchor: '80%',
				gwidth: 110,
				maxLength:15,
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', value);
						}
						else{
							return '<b><p align="right">Total Debitos: &nbsp;&nbsp; </p></b>';
						}
					} 
			},
				type:'TextField',
				filters:{pfiltro:'me.pnr',type:'string'},
				id_grupo:1,
				grid:true,
				form:false,
            	bottom_filter: true
		},	
		{
			config:{
				name: 'void',
				fieldLabel: 'Void',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:3,
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', value);
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number((record.data.importe-record.data.comision),'0,000.00'));
						}
					} 
			},
				type:'TextField',
				filters:{pfiltro:'detbol.void',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		}
		
	],
	tam_pag:50,	
	title:'Detalle Boletos',	
	ActList:'../../sis_obingresos/control/DetalleBoletosWeb/listarDetalleBoletosWeb',
	id_store:'id_detalle_boletos_web',
	fields: [
		{name:'id_detalle_boletos_web', type: 'numeric'},
		{name:'billete', type: 'string'},
		{name:'id_agencia', type: 'numeric'},
		{name:'id_periodo_venta', type: 'numeric'},
		{name:'id_moneda', type: 'numeric'},
		{name:'procesado', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'void', type: 'string'},
		{name:'importe', type: 'numeric'},
		{name:'nit', type: 'string'},
		{name:'fecha_pago', type: 'date',dateFormat:'Y-m-d'},
		{name:'razon_social', type: 'string'},
		{name:'numero_tarjeta', type: 'string'},
		{name:'comision', type: 'numeric'},
		{name:'neto', type: 'numeric'},
		{name:'entidad_pago', type: 'string'},
		{name:'fecha', type: 'date',dateFormat:'Y-m-d'},
		{name:'medio_pago', type: 'string'},
		{name:'moneda', type: 'string'},
		{name:'razon_ingresos', type: 'string'},
		{name:'origen', type: 'string'},
		{name:'nit_ingresos', type: 'string'},
		{name:'tipo_reg', type: 'string'},
		{name:'endoso', type: 'string'},
		{name:'conjuncion', type: 'string'},
		{name:'numero_autorizacion', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'pnr', type: 'string'},
		
	],
	sortInfo:{
		field: 'fecha',
		direction: 'DESC'
	},
	onReloadPage:function(m){       
		this.maestro=m;
		if (!this.store.baseParams.numero_autorizacion) {
			this.store.baseParams.numero_autorizacion = '$$$$____$$$$';
		} else {
			this.store.baseParams.numero_autorizacion = this.maestro.autorizacion__nro_deposito;
		}
		
		this.load({params:{start:0, limit:this.tam_pag}});


	},
	iniciarEventos : function () {
    	this.fecha_ini.on('change',function (field, newValue, oldValue) {
    		this.store.baseParams.fecha_inicio = newValue.dateFormat('d/m/Y');
    		if (this.fecha_fin.getValue() && this.primeraCarga)  {
    			this.primeraCarga = false;
    			this.load({params:{start:0, limit:this.tam_pag}});
    		} else if (this.fecha_fin.getValue() && !this.primeraCarga) {
    			this.onButtonAct();
    		}
    		
    	},this);
    	this.fecha_fin.on('change',function (field, newValue, oldValue) {
    		this.store.baseParams.fecha_fin = newValue.dateFormat('d/m/Y');
    		if (this.fecha_ini.getValue() && this.primeraCarga) {
    			this.primeraCarga = false;
    			this.load({params:{start:0, limit:this.tam_pag}})
    		} else if (this.fecha_ini.getValue() && !this.primeraCarga) {
    			this.onButtonAct();
    		}
    		
    	},this);
    },
	 
	bdel:false,
	bedit:false,
	bsave:false,
	bnew:false,
	bdel:false
	}
)
</script>
		
		