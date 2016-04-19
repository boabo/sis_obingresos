<?php
/**
*@package pXP
*@file gen-Boleto.php
*@author  (jrivera)
*@date 06-01-2016 22:42:25
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.Boleto=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.Boleto.superclass.constructor.call(this,config);
		this.init();
		this.finCons = true;		
		
		this.store.baseParams.id_agencia = this.maestro.id_agencia;
		this.actualizarSegunTab('sin_pago',0);
	},
	gruposBarraTareas:[{name:'sin_pago',title:'<H1 align="center"><i class="fa fa-eye"></i> Pago Pendiente</h1>',grupo:0,height:0},
                       {name:'pagados',title:'<H1 align="center"><i class="fa fa-eye"></i> Pagados</h1>',grupo:1,height:0},
                       
                       ],
    actualizarSegunTab: function(name, indice){
        if(this.finCons){
        	 if (name == 'pagados'){
        	 	this.store.baseParams.estado = 'pagado';
        	 } else {
        	 	this.store.baseParams.estado = 'no_pagado';
        	 }
             
             this.load({params:{start:0, limit:this.tam_pag}});
           }
    },
    bnewGroups: [0],
    beditGroups: [0],
    bdelGroups:  [0],
    bactGroups:  [0,1],
    btestGroups: [0],
    bexcelGroups: [0,1],
			
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
					name: 'id_agencia'
			},
			type:'Field',
			form:true 
		},
		
		{
			config:{
				name: 'nro_boleto',
				fieldLabel: 'No Boleto',
				allowBlank: false,
				anchor: '80%',
				gwidth: 130,
				maxLength:50
			},
				type:'TextField',
				filters:{pfiltro:'bol.nro_boleto',type:'string'},
				id_grupo:1,
				grid:true,
				form:true,
				bottom_filter : true
		},
		
		{
			config:{
				name: 'pasajero',
				fieldLabel: 'Pasajero',
				allowBlank: false,
				anchor: '80%',
				gwidth: 180,
				maxLength:100
			},
				type:'TextField',
				filters:{pfiltro:'bol.pasajero',type:'string'},
				id_grupo:1,
				grid:true,
				form:true,
				bottom_filter : true
		},
		{
			config:{
				name: 'fecha_emision',
				fieldLabel: 'Fecha Emisión',
				allowBlank: false,
				anchor: '80%',
				gwidth: 120,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'bol.fecha_emision',type:'date'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
            config:{
                name:'id_moneda_boleto',
                origen:'MONEDA',
                 allowBlank:false,
                fieldLabel:'Moneda Emision',
                gdisplayField:'desc_moneda',//mapea al store del grid
                gwidth:100,
                 renderer:function (value, p, record){return String.format('{0}', record.data['desc_moneda']);}
             },
            type:'ComboRec',
            id_grupo:1,
            filters:{   
                pfiltro:'mon.codigo',
                type:'string'
            },
            grid:true,
            form:true
         },		
		
		{
			config:{
				name: 'total',
				fieldLabel: 'Total',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650
			},
				type:'NumberField',
				filters:{pfiltro:'bol.total',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		
		{
			config:{
				name: 'comision',
				fieldLabel: 'Comisión',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650
			},
				type:'NumberField',
				filters:{pfiltro:'bol.comision',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'liquido',
				fieldLabel: 'Líquido a Cobrar',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650
			},
				type:'NumberField',
				filters:{pfiltro:'bol.liquido',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		
		
		
		{
			config:{
				name: 'monto_pagado_moneda_boleto',
				fieldLabel: 'Monto Pagado',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650
			},
				type:'NumberField',
				filters:{pfiltro:'bol.monto_pagado_moneda_boleto',type:'numeric'},
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
				filters:{pfiltro:'bol.estado_reg',type:'string'},
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
				name: 'id_usuario_ai',
				fieldLabel: 'Funcionaro AI',
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
				filters:{pfiltro:'bol.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,	
	title:'Boletos',
	ActSave:'../../sis_obingresos/control/Boleto/insertarBoleto',
	ActDel:'../../sis_obingresos/control/Boleto/eliminarBoleto',
	ActList:'../../sis_obingresos/control/Boleto/listarBoleto',
	id_store:'id_boleto',
	fields: [
		{name:'id_boleto', type: 'numeric'},
		{name:'id_agencia', type: 'numeric'},
		{name:'id_moneda_boleto', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'desc_moneda', type: 'string'},
		{name:'comision', type: 'numeric'},
		{name:'fecha_emision', type: 'date',dateFormat:'Y-m-d'},
		{name:'total', type: 'numeric'},
		{name:'pasajero', type: 'string'},
		{name:'monto_pagado_moneda_boleto', type: 'numeric'},
		{name:'liquido', type: 'numeric'},
		{name:'nro_boleto', type: 'string'},
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
		field: 'id_boleto',
		direction: 'DESC'
	},
	east:{
		  url:'../../../sis_obingresos/vista/deposito_boleto/DepositoBoleto.php',
		  title:'Boleto-Deposito', 
		  width:'40%',
		  cls:'DepositoBoleto',
		  collapsed:true,
		  params : { padre : 'Boleto' }
	},
	bdel:true,
	bsave:true,
	loadValoresIniciales:function()
    {	
        this.Cmp.id_agencia.setValue(this.maestro.id_agencia);   
        this.Cmp.monto_pagado_moneda_boleto.setValue(0);    
        Phx.vista.Boleto.superclass.loadValoresIniciales.call(this);
    }
	}
)
</script>
		
		