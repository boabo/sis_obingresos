<?php
/**
*@package pXP
*@file gen-PeriodoVenta.php
*@author  (jrivera)
*@date 08-04-2016 22:44:37
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.PeriodoVenta=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		this.initButtons=[this.combo_gestion,this.combo_tipo];
    	//llama al constructor de la clase padre
		Phx.vista.PeriodoVenta.superclass.constructor.call(this,config);
		this.init();
		this.iniciarEventos();
		this.addButton('btnGenerar',
            {
                text: 'Generar Periodo',
                iconCls: 'blist',
                disabled: true,                
                handler: this.onGenerarPeriodo,
                tooltip: 'Generar periodo'
            }
        );
        
        this.addButton('btnAgencias',
            {
                text: 'Agencias',
                iconCls: 'blist',
                disabled: true,                
                handler: this.onAgencias,
                tooltip: 'Totales del periodo por agencia'
            }
        );
	},	
	onAgencias : function() {
    	var rec = {maestro: this.sm.getSelected().data};
						      
            Phx.CP.loadWindows('../../../sis_obingresos/vista/periodo_venta/PeriodoVentaAgencia.php',
                    'Totales por agencia',
                    {
                        width:800,
                        height:'90%'
                    },
                    rec,
                    this.idContenedor,
                    'PeriodoVentaAgencia');
    },
	
	agregarArgsExtraSubmit: function() {
    	this.argumentExtraSubmit.id_gestion = this.combo_gestion.getValue();
    	this.argumentExtraSubmit.id_tipo_periodo = this.combo_tipo.getValue();
    },
	
	combo_gestion : new Ext.form.ComboBox({
	        store: new Ext.data.JsonStore({

	    		url: '../../sis_parametros/control/Gestion/listarGestion',
	    		id: 'id_gestion',
	    		root: 'datos',
	    		sortInfo:{
	    			field: 'gestion',
	    			direction: 'DESC'
	    		},
	    		totalProperty: 'total',
	    		fields: [
					{name:'id_gestion'},
					{name:'gestion', type: 'string'},
					{name:'estado_reg', type: 'string'}
				],
	    		remoteSort: true,
	    		baseParams:{start:0,limit:10}
	    	}),
	        displayField: 'gestion',
	        valueField: 'id_gestion',
	        typeAhead: true,
	        mode: 'remote',
	        triggerAction: 'all',
	        emptyText:'Gestión...',
	        selectOnFocus:true,
	        width:100
	    }),

        combo_tipo : new Ext.form.ComboBox({
            store: new Ext.data.JsonStore({

                url: '../../sis_obingresos/control/TipoPeriodo/listarTipoPeriodo',
                id: 'id_tipo_periodo',
                root: 'datos',
                sortInfo:{
                    field: 'id_tipo_periodo',
                    direction: 'DESC'
                },
                totalProperty: 'total',
                fields: [
                    {name:'id_tipo_periodo'},
                    {name:'tipo', type: 'string'},
                    {name:'tiempo', type: 'string'},
                    {name:'medio_pago', type: 'string'},
                    {name:'tipo_cc', type: 'string'},
                    {name:'estado', type: 'string'}


                ],
                remoteSort: true,
                baseParams:{start:0,limit:10}
            }),
            displayField: 'tipo',
            tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Tipo:</b> {tipo}</p><p><b>Tiempo:</b> {tiempo}</p><p><b>Medio Pago:</b> {medio_pago}</p></div></tpl>',
            valueField: 'id_tipo_periodo',
            typeAhead: true,
            listWidth : 150,
            resizable : true,
            mode: 'remote',
            triggerAction: 'all',
            emptyText:'TipoPer...',
            selectOnFocus:true,
            width:100
        }),
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_periodo_venta'
			},
			type:'Field',
			form:true 
		},
		{
            config:{
                name: 'tipo_periodo',
                fieldLabel: 'Tipo Periodo',
                gwidth: 130
            },
            type:'Field',
            grid:true,
            form:false
        },
        {
            config:{
                name: 'medio_pago',
                fieldLabel: 'Medio Pago',
                gwidth: 130
            },
            type:'Field',
            grid:true,
            form:false
        },       

        {
            config:{
                name: 'mes',
                fieldLabel: 'Mes',
                gwidth: 130
            },
            type:'Field',
            grid:true,
            form:false
        },

        /*{
            config:{
                name: 'nro_periodo_mes',
                fieldLabel: '# en el mes',
                gwidth: 130
            },
            type:'Field',
            grid:true,
            form:false
        },*/

		

		{
			config:{
				name: 'fecha_ini',
				fieldLabel: 'Fecha Inicio',
				gwidth: 100,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'perven.fecha_ini',type:'date'},
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_fin',
				fieldLabel: 'Fecha Fin',
				gwidth: 100,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'perven.fecha_fin',type:'date'},
				grid:true,
				form:false
		},
        {
            config:{
                name: 'fecha_pago',
                fieldLabel: 'Fecha Pago',
                gwidth: 100,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
            type:'DateField',
            filters:{pfiltro:'perven.fecha_pago',type:'date'},
            grid:true,
            form:true
        },
		{
			config:{
				name: 'estado',
				fieldLabel: 'Estado',
				gwidth: 100
			},
				type:'TextField',
				filters:{pfiltro:'perven.estado',type:'string'},
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
				filters:{pfiltro:'perven.estado_reg',type:'string'},
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
				filters:{pfiltro:'perven.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'usu1.cuenta',type:'string'},
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
				filters:{pfiltro:'perven.usuario_ai',type:'string'},
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
				filters:{pfiltro:'perven.fecha_reg',type:'date'},
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
				filters:{pfiltro:'perven.fecha_mod',type:'date'},
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
	title:'Periodo de Venta',
	ActSave:'../../sis_obingresos/control/PeriodoVenta/modificarPeriodoVenta',
	ActDel:'../../sis_obingresos/control/PeriodoVenta/eliminarPeriodoVenta',
	ActList:'../../sis_obingresos/control/PeriodoVenta/listarPeriodoVenta',
	id_store:'id_periodo_venta',
	fields: [
		{name:'id_periodo_venta', type: 'numeric'},
		{name:'id_pais', type: 'numeric'},
		{name:'id_gestion', type: 'numeric'},
		{name:'mes', type: 'string'},
		{name:'estado', type: 'string'},
		{name:'nro_periodo_mes', type: 'numeric'},
		{name:'fecha_fin', type: 'date',dateFormat:'Y-m-d'},
		{name:'fecha_ini', type: 'date',dateFormat:'Y-m-d'},
        {name:'fecha_pago', type: 'date',dateFormat:'Y-m-d'},
		{name:'tipo_periodo', type: 'string'},        
        {name:'medio_pago', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		
	],
	sortInfo:{
		field: 'id_periodo_venta',
		direction: 'ASC'
	},
	iniciarEventos : function () {	    
	    
        this.combo_gestion.on('select', function(c,r,i) {            
            this.store.baseParams.id_gestion = this.combo_gestion.getValue();
            if (this.combo_gestion.getValue() &&  this.combo_tipo.getValue())
            {
            	this.load({params:{start:0, limit:this.tam_pag}});
            	this.getBoton('btnGenerar').enable();
            }
        } , this);
        
        this.combo_tipo.on('select', function(c,r,i) {            
            this.store.baseParams.tipo = this.combo_tipo.getValue();
            if (this.combo_gestion.getValue() && this.combo_tipo.getValue())
            {
            	this.load({params:{start:0, limit:this.tam_pag}});
            	this.getBoton('btnGenerar').enable();
            }
            
        } , this);
	},
    onGenerarPeriodo : function () {
        var rec = this.sm.getSelected();
        Ext.MessageBox.prompt('Cuidado!!!','Para generar un periodo desde la interfaz ingrese el codigo secreto',
        function (option,value) { 
        	if (value == '666') {
        		
        		
	        	Phx.CP.loadingShow();
		        Ext.Ajax.request({
		            url:'../../sis_obingresos/control/PeriodoVenta/insertarPeriodoVenta',
		            params: {'id_tipo_periodo':this.combo_tipo.getValue(),
		                'id_gestion':this.combo_gestion.getValue()},
		            success:this.successSave,
		            failure: this.conexionFailure,
		            timeout:this.timeout,
		            scope:this
		        });
		    }
	    } ,this);        

    },
    preparaMenu:function()
    {	var rec = this.sm.getSelected();        
        Phx.vista.PeriodoVenta.superclass.preparaMenu.call(this); 
        this.getBoton('btnAgencias').enable();  
    },
    liberaMenu:function()
    {	
               
        Phx.vista.PeriodoVenta.superclass.liberaMenu.call(this);
        this.getBoton('btnAgencias').disable();  
    },
	bdel:false,
	bsave:false,
    bnew:false,
    bedit:true
	}
)
</script>
		
		