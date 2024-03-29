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
        if (config.vista) {
            this.store.baseParams.vista = config.vista;
        }
		this.load({params:{start:0, limit:this.tam_pag}});
		this.addButton('btnMovimientos',
            {
								grupo:[1,2],
                text: 'Movimientos',
                iconCls: 'bchecklist',
                disabled: true,
                handler: this.openMovimientos,
                tooltip: '<b>Movimientos</b><br/>Lista de movimientos por agencia.'
            }
        );

		this.store.baseParams.pes_estado = 'activo';

        /*this.addButton('btnDepositos',
            {
                text: 'Depositos',
                iconCls: 'bchecklist',
                disabled: true,
                handler: this.openDepositos,
                tooltip: '<b>Depositos</b><br/>Lista los depositos realizados por esta agencia.'
            }
        );*/
	},

	onButtonEdit:function () {
		Phx.vista.Agencia.superclass.onButtonEdit.call(this);

		var rec = this.sm.getSelected();
		console.log("aqui llega seleccoinado",rec);
		this.Cmp.id_lugar.store.load({params:{start:0,limit:50},
					 callback : function (r) {
						 console.log("aqui llega lugar",r);
						 for (var i = 0; i < r.length; i++) {
							 if (r[i].data.id_lugar == rec.data.id_lugar) {
								 this.Cmp.id_lugar.setValue(r[i].data.id_lugar);
								 this.Cmp.id_lugar.fireEvent('select', this.Cmp.id_lugar,this.Cmp.id_lugar.store.getById(r[i].data.id_lugar));
							 }
						 }
						}, scope : this
				});

	},

	gruposBarraTareas:[
										  {name:'activo',title:'<H1 style="font-size:12px;" align="center">Activos</h1>',grupo:1,height:0},
											{name:'inactivo',title:'<H1 style="font-size:12px;" align="center">Inactivos</h1>',grupo:2,height:0},
										],

 actualizarSegunTab: function(name, indice){

					 this.store.baseParams.pes_estado = name;
					 this.load({params:{start:0, limit:this.tam_pag}});

	},

	bnewGroups: [1,2],
	beditGroups: [1,2],
	bdelGroups:  [1,2],
	bactGroups:  [1,2],
	btestGroups: [1,2],
	bexcelGroups: [1,2],

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
				fieldLabel: 'Officeid',
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

		/*Aumentando para el lugar*/
		{
				config:{
						name: 'id_lugar',
						fieldLabel: 'Lugar',
						allowBlank: false,
						emptyText:'Lugar...',
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
										baseParams:{par_filtro:'lug.nombre',es_regional:'si'}
								}),
						valueField: 'id_lugar',
						displayField: 'nombre',
						gdisplayField:'nombre_lugar',
						hiddenName: 'id_lugar',
						triggerAction: 'all',
						lazyRender:true,
						mode:'remote',
						pageSize:50,
						queryDelay:500,
						anchor:"80%",
						gwidth:150,
						minChars:2,
						renderer:function (value, p, record){return String.format('{0}', record.data['nombre_lugar']);}
				},
				type:'ComboBox',
				filters:{pfiltro:'lug.nombre',type:'string'},
				id_grupo:1,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'boaagt',
				fieldLabel: 'Boa Agt',
				allowBlank:false,
				emptyText:'Tipo...',
	       		typeAhead: true,
	       		triggerAction: 'all',
	       		lazyRender:true,
	       		mode: 'local',
				gwidth: 150,
				store:['A','B']
			},
				type:'ComboBox',
				filters:{
	       		         type: 'list',
	       				 options: ['A','B'],
	       		 	},
				id_grupo:1,
				grid:false,
				form:true
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
				store:['noiata','iata']
			},
				type:'ComboBox',
				filters:{
	       		         type: 'list',
	       				 options: ['noiata','iata'],
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
         },

         {
			config:{
				name: 'bloquear_emision',
				fieldLabel: 'Bloquear emisión',
				allowBlank:false,
				emptyText:'Tipo...',
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
				name: 'validar_boleta',
				fieldLabel: 'Validar boleta',
				allowBlank:false,
				emptyText:'Tipo...',
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
				name: 'controlar_periodos_pago',
				fieldLabel: 'Controlar periodos de pago',
				allowBlank:false,
				emptyText:'Tipo...',
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

		/*Auemtnando para inactivar Agencias*/
		{
		    config:{
		        name: 'estado_reg',
		        fieldLabel: 'Estado Agencia',
		        allowBlank:true,
		        emptyText:'Estado...',
		        typeAhead: true,
		        triggerAction: 'all',
		        lazyRender:true,
		        mode: 'local',
		        gwidth: 150,
		        store:['activo','inactivo']
		    },
		    type:'ComboBox',
		    id_grupo:1,
		    form:true
		},



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

		{name:'bloquear_emision', type: 'string'},
		{name:'validar_boleta', type: 'string'},
		{name:'controlar_periodos_pago', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'id_lugar', type: 'numeric'},
		{name:'boaagt', type: 'varchar'},

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
        this.getBoton('btnMovimientos').enable();
        //this.getBoton('btnDepositos').enable();
        Phx.vista.Agencia.superclass.preparaMenu.call(this);
    },
    liberaMenu:function()
    {
        this.getBoton('btnMovimientos').disable();
        //this.getBoton('btnDepositos').disable();

        Phx.vista.Agencia.superclass.liberaMenu.call(this);
    },
        openMovimientos : function () {
    	var rec = {maestro: this.sm.getSelected().data};

            Phx.CP.loadWindows('../../../sis_obingresos/vista/movimiento_entidad/MovimientoEntidad.php',
                    'Boletos',
                    {
                        width:'95%',
                        height:'95%'
                    },
                    rec,
                    this.idContenedor,
                    'MovimientoEntidad'
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
