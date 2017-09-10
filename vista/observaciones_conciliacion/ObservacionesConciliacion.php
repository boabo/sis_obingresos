<?php
/**
*@package pXP
*@file gen-ObservacionesConciliacion.php
*@author  (jrivera)
*@date 01-06-2017 21:16:45
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.ObservacionesConciliacion=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.ObservacionesConciliacion.superclass.constructor.call(this,config);
		this.init();
        this.iniciarEventos();
		this.load({params:{start:0, limit:this.tam_pag}})
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_observaciones_conciliacion'
			},
			type:'Field',
			form:true 
		},

        {
            config:{
                store:['skybiz','portal'],
                typeAhead: false,
                allowBlank : false,
                name: 'tipo_observacion',
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
                options: ['skybiz','portal']
            },
            id_grupo:1,
            grid:true,
            form:true
        },


		{
			config:{
				name: 'fecha_observacion',
				fieldLabel: 'Fecha',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'obc.fecha_observacion',type:'date'},
				id_grupo:1,
				grid:true,
				form:true
		},
        {
            config:{
                name: 'banco',
                fieldLabel: 'Banco',
                allowBlank:false,
                emptyText:'Banco...',
                typeAhead: true,
                triggerAction: 'all',
                lazyRender:true,
                mode: 'local',
                gwidth: 150,
                store:['BIS','BUN','BNB','BME','TMY','BEC','BCR','BCO','ECF']
            },
            type:'ComboBox',
            filters:{
                type: 'list',
                options: ['BIS','BUN','BNB','BME','TMY','BEC','BCR','BCO','ECF'],
            },
            id_grupo:1,
            grid:true,
            form:true,
            bottom_filter : true
        },
        {
            config:{
                name: 'observacion',
                fieldLabel: 'Observacion',
                allowBlank: false,
                anchor: '100%',
                gwidth: 250
            },
            type:'TextArea',
            filters:{pfiltro:'obc.observacion',type:'string'},
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
            filters:{pfiltro:'obc.estado_reg',type:'string'},
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
				filters:{pfiltro:'obc.fecha_reg',type:'date'},
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
				filters:{pfiltro:'obc.usuario_ai',type:'string'},
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
				filters:{pfiltro:'obc.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'obc.fecha_mod',type:'date'},
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
	title:'Observaciones Conciliacion',
	ActSave:'../../sis_obingresos/control/ObservacionesConciliacion/insertarObservacionesConciliacion',
	ActDel:'../../sis_obingresos/control/ObservacionesConciliacion/eliminarObservacionesConciliacion',
	ActList:'../../sis_obingresos/control/ObservacionesConciliacion/listarObservacionesConciliacion',
	id_store:'id_observaciones_conciliacion',
	fields: [
		{name:'id_observaciones_conciliacion', type: 'numeric'},
		{name:'tipo_observacion', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'fecha_observacion', type: 'date',dateFormat:'Y-m-d'},
		{name:'banco', type: 'string'},
		{name:'observacion', type: 'string'},
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
		field: 'id_observaciones_conciliacion',
		direction: 'ASC'
	},
    iniciarEventos: function () {
        this.Cmp.tipo_observacion.on('select',function(c,r){
            if (this.Cmp.tipo_observacion.getValue() == 'skybiz') {
                this.mostrarComponente(this.Cmp.banco);
                this.Cmp.banco.allowBlank = false;

            } else {
                this.ocultarComponente(this.Cmp.banco);
                this.Cmp.banco.allowBlank = true;
                this.Cmp.banco.reset();
            }
        }, this)
    },
    onButtonEdit : function () {
        var rec = this.sm.getSelected();
        if (rec.data.tipo_observacion == 'skybiz') {
            this.mostrarComponente(this.Cmp.banco);
            this.Cmp.banco.allowBlank = false;
        } else {
            this.ocultarComponente(this.Cmp.banco);
            this.Cmp.banco.allowBlank = true;
            this.Cmp.banco.reset();
        }
        Phx.vista.Planilla.superclass.onButtonEdit.call(this);

    },
	bdel:true,
	bsave:true
	}
)
</script>
		
		