<?php
/**
*@package pXP
*@file gen-DepositoBoleto.php
*@author  (jrivera)
*@date 06-01-2016 22:42:31
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.DepositoBoleto=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		if (config.padre == 'Boleto') {
			this.arrayDefaultColumHidden = ['id_fecha_reg','id_fecha_mod','fecha_mod','usr_reg','usr_mod','boleto','moneda_boleto'];
		} else {
			this.arrayDefaultColumHidden = ['id_fecha_reg','id_fecha_mod','fecha_mod','usr_reg','usr_mod','deposito','moneda_deposito']
		}
    	//llama al constructor de la clase padre
		Phx.vista.DepositoBoleto.superclass.constructor.call(this,config);
		this.init();		
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_deposito_boleto'
			},
			type:'Field',
			form:true 
		},
		{
			config:{
				name: 'boleto',
				fieldLabel: 'Boleto',				
				gwidth: 140
			},
				type:'TextField',
				filters:{pfiltro:'bol.nro_boleto',type:'numeric'},				
				grid:true
		},
		{
			config:{
				name: 'moneda_boleto',
				fieldLabel: 'Moneda Boleto',				
				gwidth: 140
			},
				type:'TextField',
				filters:{pfiltro:'monbol.codigo_internacional',type:'numeric'},				
				grid:true
		},
		{
			config:{
				name: 'monto_moneda_boleto',
				fieldLabel: 'Monto Boleto',				
				gwidth: 100
			},
				type:'NumberField',
				filters:{pfiltro:'depbol.monto_moneda_boleto',type:'numeric'},				
				grid:true
		},
		{
			config:{
				name: 'deposito',
				fieldLabel: 'Deposito',				
				gwidth: 140
			},
				type:'TextField',
				filters:{pfiltro:'dep.nro_deposito',type:'numeric'},				
				grid:true
		},
		
		
		{
			config:{
				name: 'moneda_deposito',
				fieldLabel: 'Moneda Deposito',				
				gwidth: 140
			},
				type:'TextField',
				filters:{pfiltro:'mondep.codigo_internacional',type:'numeric'},				
				grid:true
		},
		
		{
			config:{
				name: 'monto_moneda_deposito',
				fieldLabel: 'Monto Deposito',				
				gwidth: 100
			},
				type:'NumberField',
				filters:{pfiltro:'depbol.monto_moneda_deposito',type:'numeric'},				
				grid:true
		},
		
		
		{
			config:{
				name: 'tc',
				fieldLabel: 'TC',				
				gwidth: 100
			},
				type:'NumberField',
				filters:{pfiltro:'depbol.tc',type:'numeric'},				
				grid:true
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
				filters:{pfiltro:'depbol.estado_reg',type:'string'},
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
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'depbol.fecha_reg',type:'date'},
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
				filters:{pfiltro:'depbol.usuario_ai',type:'string'},
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
				filters:{pfiltro:'depbol.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'depbol.fecha_mod',type:'date'},
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
	title:'Depositos - Boletos',
	ActSave:'../../sis_obingresos/control/DepositoBoleto/insertarDepositoBoleto',
	ActDel:'../../sis_obingresos/control/DepositoBoleto/eliminarDepositoBoleto',
	ActList:'../../sis_obingresos/control/DepositoBoleto/listarDepositoBoleto',
	id_store:'id_deposito_boleto',
	fields: [
		{name:'id_deposito_boleto', type: 'numeric'},
		{name:'id_boleto', type: 'numeric'},
		{name:'id_deposito', type: 'numeric'},
		{name:'tc', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'boleto', type: 'string'},
		{name:'deposito', type: 'string'},
		{name:'moneda_boleto', type: 'string'},
		{name:'moneda_deposito', type: 'string'},
		{name:'monto_moneda_boleto', type: 'numeric'},
		{name:'monto_moneda_deposito', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		
	],
	sortInfo:{
		field: 'id_deposito_boleto',
		direction: 'ASC'
	},
	bdel:false,
	bnew:false,
	bedit:false,
	bsave:false,
	onReloadPage:function(m, x){  		 
		this.maestro=m;
		this.store.baseParams.id_boleto = this.maestro.id_boleto;
		this.store.baseParams.id_deposito = this.maestro.id_deposito;
		this.load({params:{start:0, limit:this.tam_pag}});


	}
	}
)
</script>
		
		