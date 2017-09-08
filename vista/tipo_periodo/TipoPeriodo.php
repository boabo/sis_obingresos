<?php
/**
*@package pXP
*@file gen-TipoPeriodo.php
*@author  (jrivera)
*@date 08-05-2017 20:02:14
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.TipoPeriodo=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.TipoPeriodo.superclass.constructor.call(this,config);
		this.init();
        this.addButton('btnInactivar',
            {
                text: 'Inactivar',
                iconCls: 'block',
                disabled: true,
                handler: this.onInactivar,
                tooltip: 'Envia el boleto para pago en caja'
            }
        );
        this.iniciarEventos();
        this.finCons = true;
        this.store.baseParams.estado = 'activo';
        this.load({params:{start:0, limit:this.tam_pag}})
	},
    gruposBarraTareas:[{name:'activo',title:'<H1 align="center"><i class="fa fa-eye"></i> Activos</h1>',grupo:0,height:0},
        {name:'inactivo',title:'<H1 align="center"><i class="fa fa-eye"></i> Inactivos</h1>',grupo:1,height:0}

    ],
    actualizarSegunTab: function(name, indice){
        if(this.finCons) {
            this.store.baseParams.estado = name;
            this.load({params:{start:0, limit:this.tam_pag}});
        }
    },
    bactGroups:  [0,1],
    bexcelGroups: [0,1],

	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_tipo_periodo'
			},
			type:'Field',
			form:true 
		},



        {
            config:{
                store:['portal','venta_propia'],
                typeAhead: false,
                allowBlank : false,
                name: 'tipo',
                fieldLabel: 'Tipo',
                mode: 'local',
                emptyText:'Tipo...',
                triggerAction: 'all',
                lazyRender:true,
                width:135
            },
            type:'ComboBox',
            filters:{
                type: 'list',
                options: ['portal','venta_propia']
            },
            id_grupo:1,
            grid:true,
            form:true
        },

        {
            config:{
                store:['banca_internet','cuenta_corriente'],
                typeAhead: false,
                allowBlank : false,
                name: 'medio_pago',
                fieldLabel: 'Medio Pago',
                mode: 'local',
                emptyText:'Med...',
                triggerAction: 'all',
                lazyRender:true,
                width:135
            },
            type:'ComboBox',
            filters:{
                type: 'list',
                pfiltro:'tiper.medio_pago',
                options: ['banca_internet','cuenta_corriente']
            },
            id_grupo:1,
            grid:true,
            form:true
        },

        {
            config:{
                store:['bsp','1d','2d','5d','lun_mie_vie'],
                typeAhead: false,
                allowBlank : false,
                name: 'tiempo',
                fieldLabel: 'Tiempo',
                mode: 'local',
                emptyText:'Tiem...',
                triggerAction: 'all',
                lazyRender:true,
                width:135
            },
            type:'ComboBox',
            filters:{
                type: 'list',
                pfiltro:'tiper.tiempo',
                options: ['bsp','1d','2d','5d','lun_mie_vie']
            },
            id_grupo:1,
            grid:true,
            form:true
        },        

        {
            config:{
                store:['si','no'],
                typeAhead: false,
                allowBlank : false,
                name: 'pago_comision',
                fieldLabel: 'Pago Comision',
                mode: 'local',
                emptyText:'Pago...',
                triggerAction: 'all',
                lazyRender:true,
                width:135
            },
            type:'ComboBox',
            filters:{
                type: 'list',
                pfiltro:'tiper.pago_comision',
                options: ['si','no']
            },
            id_grupo:1,
            grid:true,
            form:true
        },

        {
            config:{
                name: 'fecha_ini_primer_periodo',
                fieldLabel: 'Fecha Ini Primer Per.',
                allowBlank: false,
                anchor: '80%',
                gwidth: 120,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
            type:'DateField',
            filters:{pfiltro:'tiper.fecha_ini_primer_periodo',type:'date'},
            id_grupo:1,
            grid:true,
            form:true
        },

		{
			config:{
				name: 'estado',
				fieldLabel: 'estado',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'tiper.estado',type:'string'},
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
				filters:{pfiltro:'tiper.fecha_reg',type:'date'},
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
				filters:{pfiltro:'tiper.usuario_ai',type:'string'},
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
				filters:{pfiltro:'tiper.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'tiper.fecha_mod',type:'date'},
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
	title:'Tipo Periodo',
	ActSave:'../../sis_obingresos/control/TipoPeriodo/insertarTipoPeriodo',
	ActDel:'../../sis_obingresos/control/TipoPeriodo/eliminarTipoPeriodo',
	ActList:'../../sis_obingresos/control/TipoPeriodo/listarTipoPeriodo',
	id_store:'id_tipo_periodo',
	fields: [
		{name:'id_tipo_periodo', type: 'numeric'},
		{name:'pago_comision', type: 'string'},
		{name:'tipo', type: 'string'},
		{name:'estado', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'medio_pago', type: 'string'},
		{name:'tiempo', type: 'string'},		
		{name:'id_usuario_reg', type: 'numeric'},

        {name:'fecha_ini_primer_periodo', type: 'date',dateFormat:'Y-m-d'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		
	],
	sortInfo:{
		field: 'id_tipo_periodo',
		direction: 'ASC'
	},
    iniciarEventos : function () {
        this.Cmp.tipo.on('select',function(c,r,i){
            this.manejoComponentesTipo(this.Cmp.tipo.getValue());
        },this);

        this.Cmp.medio_pago.on('select',function(c,r,i){
            this.manejoComponentesMedio(this.Cmp.medio_pago.getValue());
        },this);

    },
    onInactivar : function () {
        Phx.vista.TipoPeriodo.superclass.onButtonDel.call(this);
    },
    onButtonEdit : function () {
        Phx.vista.TipoPeriodo.superclass.onButtonEdit.call(this);
        this.manejoComponentesTipo(this.Cmp.tipo.getValue());        
    },

    onButtonNew : function () {
        Phx.vista.TipoPeriodo.superclass.onButtonNew.call(this);
        this.ocultarComponente(this.Cmp.medio_pago);        
        this.ocultarComponente(this.Cmp.pagar_comision);
    },

    manejoComponentesTipo : function (tipo) {
        if (tipo == 'portal') {

            this.Cmp.medio_pago.allowBlank = false;            
            this.Cmp.pago_comision.allowBlank = false;

            this.mostrarComponente(this.Cmp.medio_pago);            
            this.mostrarComponente(this.Cmp.pago_comision);
        } else {
            this.Cmp.medio_pago.reset();            
            this.Cmp.pago_comision.reset();

            this.Cmp.medio_pago.allowBlank = true;            
            this.Cmp.pago_comision = true;

            this.ocultarComponente(this.Cmp.medio_pago);            
            this.ocultarComponente(this.Cmp.pago_comision);
        }

    },
    
        preparaMenu:function()
        {   var rec = this.sm.getSelected();

            if (rec.data.estado == 'activo') {
                this.getBoton('btnInactivar').enable();
            }

            Phx.vista.TipoPeriodo.superclass.preparaMenu.call(this);
        },
        liberaMenu:function()
        {   this.getBoton('btnInactivar').disable();
            Phx.vista.TipoPeriodo.superclass.liberaMenu.call(this);
        },

	bdel:false,
	bsave:false,
    bedit:false
	}
)
</script>
		
		