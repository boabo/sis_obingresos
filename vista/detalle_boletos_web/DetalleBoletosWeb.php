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
    	//llama al constructor de la clase padre
		Phx.vista.DetalleBoletosWeb.superclass.constructor.call(this,config);
		this.init();
		if (this.maestro) {
			if('id_agencia' in this.maestro){
			    this.store.baseParams.id_agencia = this.maestro.id_agencia;
			    this.load({params:{start:0, limit:this.tam_pag}});
			}
		}
		
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
				maxLength:3
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
				maxLength:1179650
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
				maxLength:1179650
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
				maxLength:1179650
			},
				type:'NumberField',
				filters:{pfiltro:'detbol.comision',type:'numeric'},
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
	 
	bdel:false,
	bedit:false,
	bsave:false,
	bnew:false,
	bdel:false
	}
)
</script>
		
		