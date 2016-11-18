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
		this.initButtons=[this.combo_gestion,this.combo_pais,this.combo_tipo];
    	//llama al constructor de la clase padre
		Phx.vista.PeriodoVenta.superclass.constructor.call(this,config);
		this.init();
		this.iniciarEventos();
		this.addButton('btnGenerar',
            {
                text: 'Generar Periodo',
                iconCls: 'blist',
                disabled: true,                
                handler: this.onButtonNew,
                tooltip: 'Generar periodos para la gestion seleccionada'                
            }
        );
	},	
	
	agregarArgsExtraSubmit: function() {
    	this.argumentExtraSubmit.id_pais = this.combo_pais.getValue();
    	this.argumentExtraSubmit.id_gestion = this.combo_gestion.getValue();
    	this.argumentExtraSubmit.tipo = this.combo_tipo.getValue();
    },
	onButtonNew: function(b) {
		this.Cmp.tipo_periodo.reset(); 
        //es apra generar el periodo
        if (b.id == 'b-btnGenerar-docs-PERVEN') {
        	this.mostrarComponente(this.Cmp.tipo_periodo);
        	this.Cmp.tipo_periodo.allowBlank = false;
        	
        	this.ocultarComponente(this.Cmp.mes);
        	this.Cmp.mes.allowBlank = true;
        	
        	this.ocultarComponente(this.Cmp.nro_periodo_mes);
        	this.Cmp.nro_periodo_mes.allowBlank = true;
        	
        	this.ocultarComponente(this.Cmp.fecha_ini);
        	this.Cmp.fecha_ini.allowBlank = true;
        	
        	this.ocultarComponente(this.Cmp.fecha_fin);
        	this.Cmp.fecha_fin.allowBlank = true;
        	
        //es para insertar periodos	
        } else {
        	this.ocultarComponente(this.Cmp.tipo_periodo);
        	this.Cmp.tipo_periodo.allowBlank = true;
        	
        	this.mostrarComponente(this.Cmp.mes);
        	this.Cmp.mes.allowBlank = false;
        	
        	this.mostrarComponente(this.Cmp.nro_periodo_mes);
        	this.Cmp.nro_periodo_mes.allowBlank = false;
        	
        	this.mostrarComponente(this.Cmp.fecha_ini);
        	this.Cmp.fecha_ini.allowBlank = false;
        	
        	this.mostrarComponente(this.Cmp.fecha_fin);
        	this.Cmp.fecha_fin.allowBlank = false;
        }
        Phx.vista.PeriodoVenta.superclass.onButtonNew.call(this);
        
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
	combo_pais : new Ext.form.ComboBox({				
				allowBlank: false,
				emptyText:'Pais...',
				store:new Ext.data.JsonStore(
				{
					url: '../../sis_parametros/control/Lugar/listarLugar',
					id: 'id_lugar',
					root: 'datos',
					sortInfo:{
						field: 'nombre',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_lugar','id_lugar_fk','codigo','nombre','tipo','sw_municipio','sw_impuesto','codigo_largo'],
					// turn on remote sorting
					remoteSort: true,
					baseParams:{par_filtro:'lug.nombre',tipo:'pais'}
				}),
				valueField: 'id_lugar',
				displayField: 'nombre',				
				hiddenName: 'id_lugar',
    			triggerAction: 'all',
    			lazyRender:true,
				mode:'remote',
				pageSize:50,
				queryDelay:500,
				anchor:"100%",
				minChars:2,
				width:130
				}),
	combo_tipo : new Ext.form.ComboBox({  			
			allowBlank:false,
			emptyText:'Tipo...',
			store: new Ext.data.JsonStore({
				url: '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
				id: 'id_catalogo',
				root: 'datos',
				sortInfo:{
					field: 'descripcion',
					direction: 'ASC'
				},
				totalProperty: 'total',
				fields: ['id_catalogo','codigo','descripcion'],
				// turn on remote sorting
				remoteSort: true,
				baseParams: Ext.apply({par_filtro:'descripcion'},{
							cod_subsistema : 'OBINGRESOS',
							catalogo_tipo : 'tipo_periodo'
						})
			}),    				
			valueField: 'codigo',
			displayField: 'descripcion',		   				
			hiddenName: 'catalogo',
			forceSelection:true,
			typeAhead: false,
   			triggerAction: 'all',
   			lazyRender:true,
			mode:'remote',
			pageSize:10,
			queryDelay:1000,
			width:130,
			minChars:2
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
                allowBlank: false,
                anchor: '40%',
                gwidth: 130,
                maxLength:20,
                emptyText:'tipo...',                   
                typeAhead: true,
                triggerAction: 'all',
                lazyRender:true,
                mode: 'local',
                store:['diario','8_dias_bsp']
            },
            type:'ComboBox',            
            id_grupo:1,            
            grid:false,
            form:true
        },
		
		{
			config:{
		        store:['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		        typeAhead: false,
		        allowBlank : false,
		        name: 'mes',
		        fieldLabel: 'Mes',
		        mode: 'local',		        
		        emptyText:'Periodo...',
		        triggerAction: 'all',
                lazyRender:true,                
		        width:135
		    },
				type:'ComboBox',
				filters:{   
                         type: 'list',
                         pfiltro:'perven.mes',
                         options: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']
                    },				
				id_grupo:1,
				grid:true,
				form:true
		},
		
		{
			config:{
				name: 'nro_periodo_mes',
				fieldLabel: '# en el mes',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'NumberField',
				filters:{pfiltro:'perven.nro_periodo_mes',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'fecha_ini',
				fieldLabel: 'Fecha Inicio',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'perven.fecha_ini',type:'date'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'fecha_fin',
				fieldLabel: 'Fecha Fin',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'perven.fecha_fin',type:'date'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'estado',
				fieldLabel: 'Estado',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:15
			},
				type:'TextField',
				filters:{pfiltro:'perven.estado',type:'string'},
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
	ActSave:'../../sis_obingresos/control/PeriodoVenta/insertarPeriodoVenta',
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
		{name:'tipo', type: 'string'},
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
            if (this.combo_gestion.getValue() && this.combo_pais.getValue() && this.combo_tipo.getValue())
            {
            	this.load({params:{start:0, limit:this.tam_pag}});
            	this.getBoton('btnGenerar').enable();
            }
        } , this);
        
        this.combo_pais.on('select', function(c,r,i) {            
            this.store.baseParams.id_lugar = this.combo_pais.getValue();
            if (this.combo_gestion.getValue() && this.combo_pais.getValue() && this.combo_tipo.getValue())
            {
            	this.load({params:{start:0, limit:this.tam_pag}});
            	this.getBoton('btnGenerar').enable();
            }
            
        } , this);
        
        this.combo_tipo.on('select', function(c,r,i) {            
            this.store.baseParams.tipo = this.combo_tipo.getValue();
            if (this.combo_gestion.getValue() && this.combo_pais.getValue() && this.combo_tipo.getValue())
            {
            	this.load({params:{start:0, limit:this.tam_pag}});
            	this.getBoton('btnGenerar').enable();
            }
            
        } , this);
	},
	bdel:true,
	bsave:true
	}
)
</script>
		
		