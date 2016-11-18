<?php
/**
*@package pXP
*@file gen-BoletoImpuesto.php
*@author  (jrivera)
*@date 13-06-2016 20:42:17
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.BoletoImpuesto=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.BoletoImpuesto.superclass.constructor.call(this,config);
		this.init();
		var dataPadre = Phx.CP.getPagina(this.idContenedorPadre).getSelectedData()
          if(dataPadre){
             this.onEnablePanel(this, dataPadre);
          }
          else
          {
             this.bloquearMenus();
          }
		
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_boleto_impuesto'
			},
			type:'Field',
			form:true 
		},
		
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
			config:{
				name: 'codigo_impuesto',
				fieldLabel: 'Cóodigo',				
				anchor: '80%',
				gwidth: 150
				
			},
				type:'TextField',								
				grid:true,
				form:false
		},
		{
			config:{
				name: 'nombre_impuesto',
				fieldLabel: 'Impuesto - Tasa',				
				anchor: '80%',
				gwidth: 200
				
			},
				type:'TextField',								
				grid:true,
				form:false
		},
		{
			config:{
				name: 'importe',
				fieldLabel: 'Importe',	
				gwidth: 120,
			},
				type:'NumberField',
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
				filters:{pfiltro:'bit.estado_reg',type:'string'},
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
				filters:{pfiltro:'bit.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'bit.usuario_ai',type:'string'},
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
				filters:{pfiltro:'bit.fecha_reg',type:'date'},
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
				name: 'fecha_mod',
				fieldLabel: 'Fecha Modif.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'bit.fecha_mod',type:'date'},
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
	tam_pag:50,	
	arrayDefaultColumHidden:['estado_reg','usuario_ai',
    'fecha_reg','fecha_mod','usr_reg','usr_mod'],
	title:'Impuestos y Tasas',
	ActSave:'../../sis_obingresos/control/BoletoImpuesto/insertarBoletoImpuesto',
	ActDel:'../../sis_obingresos/control/BoletoImpuesto/eliminarBoletoImpuesto',
	ActList:'../../sis_obingresos/control/BoletoImpuesto/listarBoletoImpuesto',
	id_store:'id_boleto_impuesto',
	fields: [
		{name:'id_boleto_impuesto', type: 'numeric'},
		{name:'importe', type: 'numeric'},
		{name:'id_impuesto', type: 'numeric'},
		{name:'id_boleto', type: 'numeric'},
		{name:'codigo_impuesto', type: 'string'},
		{name:'nombre_impuesto', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		
	],
	sortInfo:{
		field: 'id_boleto_impuesto',
		direction: 'ASC'
	},
	loadValoresIniciales:function(){
		Phx.vista.BoletoFormaPago.superclass.loadValoresIniciales.call(this);
	    this.Cmp.id_boleto.setValue(this.maestro.id_boleto);
	},
	
	onReloadPage:function(m){
		this.maestro=m;
		this.store.baseParams.id_boleto = this.maestro.id_boleto;		
		this.load({params:{start:0, limit:50}})
		
	},
	bdel:false,
	bsave:false,
	bnew:false,
	bedit:false,
	
	}
)
</script>
		
		