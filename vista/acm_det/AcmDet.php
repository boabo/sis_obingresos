<?php
/**
*@package pXP
*@file gen-AcmDet.php
*@author  (jrivera)
*@date 05-09-2018 20:52:05
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.AcmDet=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.AcmDet.superclass.constructor.call(this,config);
		this.init();
		//this.load({params:{start:0, limit:this.tam_pag}})
	},

	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_acm_det'
			},
			type:'Field',
			form:true
		},
		{
			config: {
				name: 'id_acm',
				fieldLabel: 'ACM',
				allowBlank: false,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_obingresos/control/Acm/listarAcm',
					id: 'id_acm',
					root: 'datos',
					sortInfo: {
						field: 'id_acm',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_acm', 'numero','nombre'],
					remoteSort: true,
					baseParams: {par_filtro: 'agen.nombre#acm.id_acm'}
				}),
				valueField: 'id_acm',
				displayField: 'nombre',
				gdisplayField: 'id_acm',
				hiddenName: 'id_acm',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '50%',
				gwidth: 150,
				minChars: 2,
				tpl: new Ext.XTemplate([
					'<tpl for=".">',
					'<div class="x-combo-list-item">',
					'<div class="awesomecombo-item {checked}">',
					'<p><b>ID ACM:</b> <span style="color: red; font-weight: bold;">{id_acm}</span></p>',
					'</div><p><b>Agencia:</b> <span style="color: blue; font-weight: bold;">{nombre}</span></p>',
					'</div></tpl>'
				]),
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['id_acm']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'agen.nombre',type: 'string'},
			grid: false,
			form: true
		},
		{
			config:{
				name: 'billete',
				fieldLabel: 'Billete',
				allowBlank: false,
				anchor: '80%',
				gwidth: 150,
				renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
						return  String.format('<b style="color:blue; ">{0}</b>', record.data['billete']);
					}
					else{
						return '<b><p style="font-size:20px; color:red; text-decoration: border-top:2px;">Totales: &nbsp;&nbsp; </p></b>';
					}
			},
				maxLength:1179650
			},
				type:'NumberField',
				filters:{pfiltro:'bole.billete',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:false,
                bottom_filter : true
		},
		{
			config: {
				name: 'id_detalle_boletos_web',
				fieldLabel: 'Billete',
				allowBlank: false,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_obingresos/control/DetalleBoletosWeb/listarDetalleBoletosWeb',
					id: 'id_detalle_boletos_web',
					root: 'datos',
					sortInfo: {
						field: 'id_detalle_boletos_web',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_detalle_boletos_web', 'billete'],
					remoteSort: true,
					baseParams: {par_filtro: 'detbol.billete#detbol.id_detalle_boletos_web'}
				}),
				valueField: 'id_detalle_boletos_web',
				displayField: 'billete',
				gdisplayField: 'id_detalle_boletos_web',
				hiddenName: 'id_detalle_boletos_web',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '50%',
				gwidth: 150,
				minChars: 2,
				tpl: new Ext.XTemplate([
					'<tpl for=".">',
					'<div class="x-combo-list-item">',
					'<div class="awesomecombo-item {checked}">',
					'<p><b>Id Boletos:</b> <span style="color: red; font-weight: bold;">{id_detalle_boletos_web}</span></p>',
					'</div><p><b>Boleto:</b> <span style="color: blue; font-weight: bold;">{billete}</span></p>',
					'</div></tpl>'
				]),
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['id_detalle_boletos_web']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'detbol.billete',type: 'string'},
			grid: false,
			form: true
		},
		{
			config:{
				name: 'neto',
				fieldLabel: 'Neto',
				allowBlank: false,
				anchor: '50%',
				gwidth: 150,
				galign:'right',
				renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
						return  String.format('<div style="color:#005E7A; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
					}

					else{
						return  String.format('<div style="font-size:20px; text-align:rigth; color:#005E7A;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_neto,'0,000.00'));
					}
				},
				maxLength:1179650
			},
				type:'NumberField',
				filters:{pfiltro:'acmdet.neto',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'com_bsp',
				fieldLabel: 'SUM/Com-BSP',
				allowBlank: false,
				anchor: '50%',
				gwidth: 150,
				galign:'right',
				renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
						return  String.format('<div style="color:#4D00B5; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
					}

					else{
						return  String.format('<div style="font-size:20px; text-align:rigth; color:#4D00B5;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_bsp,'0,000.00'));
					}

		},
				maxLength:1179650
			},
				type:'NumberField',
				filters:{pfiltro:'acmdet.com_bsp',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'over_comision',
				fieldLabel: 'Over Comision',
				allowBlank: false,
				anchor: '50%',
				gwidth: 150,
				galign:'right',
				renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
						return  String.format('<div style="color:green; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
					}

					else{
						return  String.format('<div style="font-size:20px; text-align:rigth; color:green;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_over_comision,'0,000.00'));
					}

		},
				maxLength:1179650
			},
				type:'NumberField',
				filters:{pfiltro:'acmdet.over_comision',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'porcentaje_over',
				fieldLabel: '% OVER',
				allowBlank: false,
				anchor: '50%',
				gwidth: 150,
				galign:'right',
				renderer:function (value,p,record){

						return  String.format('<b style="color:#6A0000; ">{0}</b>', record.data['porcentaje_over']);

			},
				maxLength:1179650
			},
				type:'NumberField',
				filters:{pfiltro:'acmdet.porcentaje_over',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true,
                bottom_filter : true
		},
		{
			config:{
				name: 'moneda',
				fieldLabel: 'Mon',
				allowBlank: false,
				anchor: '80%',
				gwidth: 150,
				renderer:function (value,p,record){

						return  String.format('<b style="color:#940000; ">{0}</b>', record.data['moneda']);


			},
				maxLength:1179650
			},
				type:'NumberField',
				filters:{pfiltro:'acmdet.moneda',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'td',
				fieldLabel: 'T/D',
				allowBlank: false,
				anchor: '80%',
				gwidth: 150,
				renderer:function (value,p,record){

						return  String.format('<b style="color:#007619; ">{0}</b>', record.data['td']);

			},
				maxLength:1179650
			},
				type:'NumberField',
				filters:{pfiltro:'acmdet.td',type:'numeric'},
				id_grupo:1,
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
				filters:{pfiltro:'acmdet.estado_reg',type:'string'},
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
				filters:{pfiltro:'acmdet.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'acmdet.usuario_ai',type:'string'},
				id_grupo:1,
				grid:false,
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
				filters:{pfiltro:'acmdet.fecha_reg',type:'date'},
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
				filters:{pfiltro:'acmdet.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	title:'ACM det',
	ActSave:'../../sis_obingresos/control/AcmDet/insertarAcmDet',
	ActDel:'../../sis_obingresos/control/AcmDet/eliminarAcmDet',
	ActList:'../../sis_obingresos/control/AcmDet/listarAcmDet',
	id_store:'id_acm_det',
	fields: [
		{name:'id_acm_det', type: 'numeric'},
		{name:'id_acm', type: 'numeric'},
		{name:'id_detalle_boletos_web', type: 'numeric'},
		{name:'neto', type: 'numeric'},
		{name: 'total_neto', type: 'numeric'},
		{name:'over_comision', type: 'numeric'},
		{name: 'total_over_comision', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'billete', type: 'string'},
		{name: 'tipo_reg', type: 'string'},
		{name: 'com_bsp', type: 'numeric'},
		{name: 'total_bsp', type: 'numeric'},
		{name: 'moneda', type: 'string'},
		{name: 'td', type: 'string'},
		{name: 'porcentaje_over', type: 'numeric'},

	],
	sortInfo:{
		field: 'billete',
		direction: 'ASC'
	},
	bdel:false,
	bsave:false,
    bnew:false,

	onReloadPage: function (m) {
		this.maestro = m;
		this.store.baseParams = {id_acm:this.maestro.id_acm};
		// this.bloquearMenus();
	  this.load({params: {start: 0, limit: 50}});
	},

	loadValoresIniciales: function () {
		this.Cmp.id_acm.setValue(this.maestro.id_acm);
		Phx.vista.AcmDet.superclass.loadValoresIniciales.call(this);
	}
	}
)
</script>
