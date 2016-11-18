<?php
/**
*@package pXP
*@file gen-Agencia.php
*@author  (jrivera)
*@date 06-01-2016 21:30:12
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.Agencia=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.Agencia.superclass.constructor.call(this,config);
		this.init();
		this.iniciarEventos();
		this.load({params:{start:0, limit:this.tam_pag}});
		this.addButton('btnBoletos',
            {
                text: 'Boletos',
                iconCls: 'bchecklist',
                disabled: true,
                handler: this.openBoletos,
                tooltip: '<b>Boletos</b><br/>Lista los boletos vendidos por esta agencia.'
            }
        );
        
        this.addButton('btnDepositos',
            {
                text: 'Depositos',
                iconCls: 'bchecklist',
                disabled: true,
                handler: this.openDepositos,
                tooltip: '<b>Depositos</b><br/>Lista los depositos realizados por esta agencia.'
            }
        );
	},
			
	Atributos:[
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
				name: 'codigo',
				fieldLabel: 'Código',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:20
			},
				type:'TextField',
				filters:{pfiltro:'age.codigo',type:'string'},
				id_grupo:1,
				grid:true,
				form:true,
				bottom_filter : true
		},
		
		{
			config:{
				name: 'codigo_int',
				fieldLabel: 'Código Internacional',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:20
			},
				type:'TextField',
				filters:{pfiltro:'age.codigo_int',type:'string'},
				id_grupo:1,
				grid:true,
				form:true,
				bottom_filter : true
		},
		
		{
			config:{
				name: 'nombre',
				fieldLabel: 'Nombre Agencia',
				allowBlank: false,
				anchor: '80%',
				gwidth: 150,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'age.nombre',type:'string'},
				id_grupo:1,
				grid:true,
				form:true,
				bottom_filter : true
		},
		
		
		{
			config:{
				name: 'tipo_agencia',
				fieldLabel: 'Tipo Agencia',
				allowBlank:false,
				emptyText:'Tipo...',
	       		typeAhead: true,
	       		triggerAction: 'all',
	       		lazyRender:true,
	       		mode: 'local',
				gwidth: 150,
				store:['noiata']
			},
				type:'ComboBox',
				filters:{	
	       		         type: 'list',
	       				 options: ['noiata'],	
	       		 	},
				id_grupo:1,
				grid:true,
				form:true
		},
		
		{
			config:{
				name: 'tipo_pago',
				fieldLabel: 'Tipo Pago',
				allowBlank:false,
				emptyText:'Tipo...',
	       		typeAhead: true,
	       		triggerAction: 'all',
	       		lazyRender:true,
	       		mode: 'local',
				gwidth: 150,
				store:['prepago','postpago']
			},
				type:'ComboBox',
				filters:{	
	       		         type: 'list',
	       				 options: ['prepago','postpago'],	
	       		 	},
				id_grupo:1,
				grid:true,
				form:true
		},
		
		{
			config:{
				name: 'monto_maximo_deuda',
				fieldLabel: 'Monto Maximo Deuda',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650
			},
				type:'NumberField',
				filters:{pfiltro:'age.monto_maximo_deuda',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'depositos_moneda_boleto',
				fieldLabel: 'Controlar Depositos por Moneda de Venta?',
				allowBlank:false,
				emptyText:'Controlar...',
	       		typeAhead: true,
	       		triggerAction: 'all',
	       		lazyRender:true,
	       		mode: 'local',
				gwidth: 150,
				store:['si','no']
			},
				type:'ComboBox',
				filters:{	
	       		         type: 'list',
	       				 options: ['si','no'],	
	       		 	},
				id_grupo:1,
				grid:true,
				form:true
		},	
		{
			config:{
				name: 'tipo_cambio',
				fieldLabel: 'Tipo Cambio Control',
				allowBlank:false,
				emptyText:'Tipo...',
	       		typeAhead: true,
	       		triggerAction: 'all',
	       		lazyRender:true,
	       		mode: 'local',
				gwidth: 150,
				store:['venta','deposito']
			},
				type:'ComboBox',
				filters:{	
	       		         type: 'list',
	       				 options: ['venta','deposito'],	
	       		 	},
				id_grupo:1,
				grid:true,
				form:true
		},				
		
		{
            config:{
                name:'id_moneda_control',
                origen:'MONEDA',
                 allowBlank:false,
                fieldLabel:'Moneda de Control',
                gdisplayField:'desc_moneda',//mapea al store del grid
                gwidth:50,
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
          }
		
		
		
	],
	tam_pag:50,	
	title:'Agencias',
	ActSave:'../../sis_obingresos/control/Agencia/insertarAgencia',
	ActDel:'../../sis_obingresos/control/Agencia/eliminarAgencia',
	ActList:'../../sis_obingresos/control/Agencia/listarAgencia',
	id_store:'id_agencia',
	fields: [
		{name:'id_agencia', type: 'numeric'},
		{name:'id_moneda_control', type: 'numeric'},
		{name:'desc_moneda', type: 'string'},
		{name:'depositos_moneda_boleto', type: 'string'},
		{name:'tipo_pago', type: 'string'},
		{name:'nombre', type: 'string'},
		{name:'monto_maximo_deuda', type: 'numeric'},
		{name:'tipo_cambio', type: 'string'},
		{name:'codigo_int', type: 'string'},
		{name:'codigo', type: 'string'},
		{name:'tipo_agencia', type: 'string'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		
	],
	sortInfo:{
		field: 'id_agencia',
		direction: 'ASC'
	},
	iniciarEventos : function () {
		this.Cmp.codigo.on ('blur',function(){
			if (this.Cmp.codigo_int.getValue() == '') {
				this.Cmp.codigo_int.setValue(this.Cmp.codigo.getValue());
			}
		},this)
	},
	preparaMenu:function()
    {	   
        this.getBoton('btnBoletos').enable(); 
        this.getBoton('btnDepositos').enable();  
        Phx.vista.Agencia.superclass.preparaMenu.call(this);
    },
    liberaMenu:function()
    {	
        this.getBoton('btnBoletos').disable();
        this.getBoton('btnDepositos').disable();
        
        Phx.vista.Agencia.superclass.liberaMenu.call(this);
    },
    openBoletos : function () {
    	var rec = {maestro: this.sm.getSelected().data};
            
            Phx.CP.loadWindows('../../../sis_obingresos/vista/boleto/Boleto.php',
                    'Boletos',
                    {
                        width:'95%',
                        height:'95%'
                    },
                    rec,
                    this.idContenedor,
                    'Boleto'
        )
    },
    
    openDepositos : function () {
    	var rec = {maestro: this.sm.getSelected().data};
            
            Phx.CP.loadWindows('../../../sis_obingresos/vista/deposito/Deposito.php',
                    'Depositos',
                    {
                        width:'95%',
                        height:'95%'
                    },
                    rec,
                    this.idContenedor,
                    'Deposito'
        )
    },
	bdel:true,
	bsave:true
	}
)
</script>
		
		