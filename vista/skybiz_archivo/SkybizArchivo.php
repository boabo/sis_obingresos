<?php
/**
*@package pXP
*@file gen-SkybizArchivo.php
*@author  (admin)
*@date 15-02-2017 15:18:39
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.SkybizArchivo=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.SkybizArchivo.superclass.constructor.call(this,config);
		this.tbar.addField(this.cmbBanco);
		this.bloquearOrdenamientoGrid();

		this.cmbBanco.on('clearcmb', function () {
				this.DisableSelect();
				this.store.removeAll();
		}, this);


		this.cmbBanco.on('select', function () {
				if (this.validarFiltros()) {
						this.capturaFiltros();
				}
		}, this);
		this.init();
		this.load({params:{start:0, limit:this.tam_pag}})
	},
	capturaFiltros: function (combo, record, index) {
			this.desbloquearOrdenamientoGrid();
			this.store.baseParams.bancos = this.cmbBanco.getValue();
			console.log('LLEGA EL DATO',this.store.baseParams);
			this.load();
	},
	validarFiltros: function () {
			console.log('values....', this.cmbBanco.getValue())
			if (this.cmbBanco.getValue() != '' && this.cmbBanco.validate() ) {
					return true;
			} else {
					return false;
			}
	},
	onButtonAct: function () {
			if (!this.validarFiltros()) {
					alert('Especifique los Bancos a listar')
			}
			else {
					this.capturaFiltros();
			}
	},


	Atributos:[
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
				name: 'fecha',
				fieldLabel: 'fecha',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'skybiz.fecha',type:'date'},
				id_grupo:1,
				grid:true,
				form:true,
				bottom_filter : true
		},
		{
			config:{
				name: 'banco',
				fieldLabel: 'Banco',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
			type:'TextField',
			filters:{pfiltro:'skybiz.banco',type:'string'},
			id_grupo:1,
			grid:true,
			form:true,
			bottom_filter : true
		},
		{
			config:{
				name: 'moneda',
				fieldLabel: 'Moneda',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
			type:'TextField',
			filters:{pfiltro:'skybiz.moneda',type:'string'},
			id_grupo:1,
			grid:true,
			form:true,
			bottom_filter : true
		},

		{
			config:{
				name: 'total',
				fieldLabel: 'total',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:655362
			},
			type:'NumberField',
			//filters:{pfiltro:'skydet.total_amount',type:'numeric'},
			id_grupo:1,
			grid:true,
			form:true,
		},


		{
			config:{
				name: 'nombre_archivo',
				fieldLabel: 'nombre_archivo',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
			type:'TextField',
			filters:{pfiltro:'skybiz.nombre_archivo',type:'string'},
			id_grupo:1,
			grid:true,
			form:true,
			bottom_filter : true
		},

		{
			config:{
				name: 'subido',
				fieldLabel: 'subido',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'skybiz.subido',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'comentario',
				fieldLabel: 'comentario',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'skybiz.comentario',type:'string'},
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
				filters:{pfiltro:'skybiz.estado_reg',type:'string'},
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
				filters:{pfiltro:'skybiz.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'skybiz.usuario_ai',type:'string'},
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
				filters:{pfiltro:'skybiz.fecha_reg',type:'date'},
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
				filters:{pfiltro:'skybiz.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	title:'Skybiz Archivo',
	ActSave:'../../sis_obingresos/control/SkybizArchivo/insertarSkybizArchivo',
	ActDel:'../../sis_obingresos/control/SkybizArchivo/eliminarSkybizArchivo',
	ActList:'../../sis_obingresos/control/SkybizArchivo/listarSkybizArchivo',
	id_store:'id_skybiz_archivo',
	fields: [
		{name:'id_skybiz_archivo', type: 'numeric'},
		{name:'fecha', type: 'date',dateFormat:'Y-m-d'},
		{name:'subido', type: 'string'},
		{name:'comentario', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'nombre_archivo', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'banco', type: 'string'},
		{name:'moneda', type: 'string'},
		{name:'total', type: 'string'},

	],
	sortInfo:{
		field: 'id_skybiz_archivo',
		direction: 'ASC'
	},
	bdel:false,
	bsave:false,
	bnew:false,
	bedit:false,
	cmbBanco : new Ext.form.AwesomeCombo({
			name: 'agt',
			fieldLabel: 'Seleccione Bancos...',
			emptyText:'Seleccione los Bancos',
			typeAhead: true,
			triggerAction: 'all',
			lazyRender:true,
			forceSelection: true,
			mode: 'local',
			gwidth: 50,
			anchor: "10%",
			store:['TODOS','BCO','BCR','BEC','BIS','BME','BNB','BUN','BPM','ECF','TMY','QRB','QRK','BCK'],
			enableMultiSelect: true,
	}),

	south: {
		url: '../../../sis_obingresos/vista/skybiz_archivo_detalle/SkybizArchivoDetalle.php',
		title: 'SkybizArchivoDetalle',
		height: '50%',
		cls: 'SkybizArchivoDetalle'
	},
	}
)
</script>
