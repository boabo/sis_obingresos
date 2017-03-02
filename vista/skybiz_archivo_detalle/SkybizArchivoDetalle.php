<?php
/**
*@package pXP
*@file gen-SkybizArchivoDetalle.php
*@author  (admin)
*@date 15-02-2017 19:08:58
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.SkybizArchivoDetalle=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.SkybizArchivoDetalle.superclass.constructor.call(this,config);
		this.init();
		//this.load({params:{start:0, limit:this.tam_pag}})
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_skybiz_archivo_detalle'
			},
			type:'Field',
			form:true 
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_skybiz_archivo'
			},
			type:'Field',
			form:true
		},
		{
			config:{
				name: 'entity',
				fieldLabel: 'entity',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'skydet.entity',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'ip',
				fieldLabel: 'ip',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
			type:'TextField',
			filters:{pfiltro:'skydet.ip',type:'string'},
			id_grupo:1,
			grid:true,
			form:true
		},

		{
			config:{
				name: 'request_date_time',
				fieldLabel: 'request_date_time',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'skydet.request_date_time',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'issue_date_time',
				fieldLabel: 'issue_date_time',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
			type:'TextField',
			filters:{pfiltro:'skydet.issue_date_time',type:'string'},
			id_grupo:1,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'pnr',
				fieldLabel: 'pnr',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
			type:'TextField',
			filters:{pfiltro:'skydet.pnr',type:'string'},
			id_grupo:1,
			grid:true,
			form:true,
			bottom_filter : true
		},
		{
			config:{
				name: 'identifier_pnr',
				fieldLabel: 'identifier_pnr',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
			type:'TextField',
			filters:{pfiltro:'skydet.identifier_pnr',type:'string'},
			id_grupo:1,
			grid:true,
			form:true,
			bottom_filter : true
		},


		{
			config:{
				name: 'authorization_',
				fieldLabel: 'authorization_',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
			type:'TextField',
			filters:{pfiltro:'skydet.authorization_',type:'string'},
			id_grupo:1,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'total_amount',
				fieldLabel: 'total_amount',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:655362
			},
			type:'NumberField',
			filters:{pfiltro:'skydet.total_amount',type:'numeric'},
			id_grupo:1,
			grid:true,
			form:true,
			bottom_filter : true
		},

		{
			config:{
				name: 'currency',
				fieldLabel: 'currency',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'skydet.currency',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},


		{
			config:{
				name: 'status',
				fieldLabel: 'status',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'skydet.status',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
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
				filters:{pfiltro:'skydet.estado_reg',type:'string'},
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
				filters:{pfiltro:'skydet.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'skydet.usuario_ai',type:'string'},
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
				filters:{pfiltro:'skydet.fecha_reg',type:'date'},
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
				filters:{pfiltro:'skydet.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,	
	title:'Skybiz Archivo Detalle',
	ActSave:'../../sis_obingresos/control/SkybizArchivoDetalle/insertarSkybizArchivoDetalle',
	ActDel:'../../sis_obingresos/control/SkybizArchivoDetalle/eliminarSkybizArchivoDetalle',
	ActList:'../../sis_obingresos/control/SkybizArchivoDetalle/listarSkybizArchivoDetalle',
	id_store:'id_skybiz_archivo_detalle',
	fields: [
		{name:'id_skybiz_archivo_detalle', type: 'numeric'},
		{name:'entity', type: 'string'},
		{name:'request_date_time', type: 'string'},
		{name:'currency', type: 'string'},
		{name:'total_amount', type: 'numeric'},
		{name:'ip', type: 'string'},
		{name:'status', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'issue_date_time', type: 'string'},
		{name:'identifier_pnr', type: 'string'},
		{name:'id_skybiz_archivo', type: 'numeric'},
		{name:'pnr', type: 'string'},
		{name:'authorization_', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		
	],
	sortInfo:{
		field: 'id_skybiz_archivo_detalle',
		direction: 'ASC'
	},
	bdel:false,
	bsave:false,
	bnew:false,
	bedit:false,
	preparaMenu: function (tb) {
		// llamada funcion clace padre
		Phx.vista.SkybizArchivoDetalle.superclass.preparaMenu.call(this, tb)
	},
	onButtonNew: function () {
		Phx.vista.SkybizArchivoDetalle.superclass.onButtonNew.call(this);
		this.getComponente('id_skybiz_archivo').setValue(this.maestro.id_skybiz_archivo);
	},
	onReloadPage: function (m) {
		this.maestro = m;
		this.store.baseParams = {id_skybiz_archivo: this.maestro.id_skybiz_archivo};
		this.load({params: {start: 0, limit: 50}})
	},
	}
)
</script>
		
		