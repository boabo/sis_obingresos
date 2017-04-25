<?php
/**
*@package pXP
*@file gen-VentaWebModificaciones.php
*@author  (jrivera)
*@date 11-01-2017 19:44:28
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.VentaWebModificaciones=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.VentaWebModificaciones.superclass.constructor.call(this,config);
		this.init();
        this.finCons = true;
        this.iniciarEventos();
        this.store.baseParams.pes_estado = 'pendientes';
        this.load({params:{start:0, limit:this.tam_pag}})
	},
    beditGroups: [0],
    bdelGroups:  [0],
    bactGroups:  [0,1],
    btestGroups: [0],
    bexcelGroups: [0,1],
    gruposBarraTareas:[{name:'pendientes',title:'<H1 align="center"><i class="fa fa-eye"></i> Pendientes Proceso</h1>',grupo:0,height:0},
        {name:'finalizados',title:'<H1 align="center"><i class="fa fa-eye"></i> Procesados</h1>',grupo:1,height:0}
    ],
    actualizarSegunTab: function(name, indice){
        if(this.finCons) {
            this.store.baseParams.pes_estado = name;
            this.load({params:{start:0, limit:this.tam_pag}});
        }
    },
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_venta_web_modificaciones'
			},
			type:'Field',
			form:true 
		},
        {
            config:{
                name: 'tipo',
                fieldLabel: 'Tipo',
                allowBlank: false,
                emptyText:'Tipo...',
                typeAhead: true,
                triggerAction: 'all',
                lazyRender:true,
                mode: 'local',
                gwidth: 100,
                store:['anulado','reemision','emision_manual']
            },
            type:'ComboBox',
            filters:{
                type: 'list',
                options: ['anulado','reemision','emision_manual'],
            },
            id_grupo:1,
            grid:true,
            form:true
        },
		{
			config:{
				name: 'nro_boleto',
				fieldLabel: 'Boleto Original',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
                maxLength:13,
                minLength:13
			},
				type:'NumberField',
				filters:{pfiltro:'vwebmod.nro_boleto',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},

        {
            config:{
                name: 'nro_boleto_reemision',
                fieldLabel: 'Boleto Reemitido-Manual',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:13,
                minLength:13,
                allowDecimals:false
            },
            type:'NumberField',
            filters:{pfiltro:'vwebmod.nro_boleto_reemision',type:'string'},
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
            id_grupo:1,
            form:true
        },

        {
            config:{
                name: 'pnr_antiguo',
                fieldLabel: 'PNR no emitido',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:13,
                minLength:13,
                allowDecimals:false
            },
            type:'TextField',
            filters:{pfiltro:'vwebmod.pnr_antiguo',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },

        {
            config:{
                name: 'fecha_reserva_antigua',
                fieldLabel: 'Fecha PNR no emitido',
                allowBlank: false,
                anchor: '80%',
                gwidth: 100,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
            type:'DateField',
            filters:{pfiltro:'perven.fecha_reserva_antigua',type:'date'},
            id_grupo:1,
            grid:true,
            form:true
        },

		{
			config:{
				name: 'motivo',
				fieldLabel: 'Motivo',
				allowBlank: false,
				anchor: '100%',
				gwidth: 200,

			},
				type:'TextArea',
				filters:{pfiltro:'vwebmod.motivo',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},


        {
            config:{
                name: 'used',
                fieldLabel: 'Usado',
                allowBlank: true,
                emptyText:'Tipo...',
                typeAhead: true,
                triggerAction: 'all',
                lazyRender:true,
                mode: 'local',
                gwidth: 100,
                store:['SI','NO']
            },
            type:'ComboBox',
            filters:{
                type: 'list',
                options: ['SI','NO'],
            },
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'procesado',
                fieldLabel: 'Reemision procesada',
                gwidth: 220,
                maxLength:10
            },
            type:'TextField',
            filters:{pfiltro:'vwebmod.procesado',type:'string'},
            grid:true,
            form:false
        },
        {
            config:{
                name: 'anulado',
                fieldLabel: 'Boleto Ori. Anulado',
                gwidth: 200,
                maxLength:10
            },
            type:'TextField',
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
				filters:{pfiltro:'vwebmod.estado_reg',type:'string'},
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
				filters:{pfiltro:'vwebmod.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
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
				filters:{pfiltro:'vwebmod.fecha_reg',type:'date'},
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
				filters:{pfiltro:'vwebmod.fecha_mod',type:'date'},
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
	title:'Modificaciones Venta Web',
	ActSave:'../../sis_obingresos/control/VentaWebModificaciones/insertarVentaWebModificaciones',
	ActDel:'../../sis_obingresos/control/VentaWebModificaciones/eliminarVentaWebModificaciones',
	ActList:'../../sis_obingresos/control/VentaWebModificaciones/listarVentaWebModificaciones',
	id_store:'id_venta_web_modificaciones',
	fields: [
		{name:'id_venta_web_modificaciones', type: 'numeric'},
		{name:'nro_boleto', type: 'string'},
		{name:'tipo', type: 'string'},
		{name:'motivo', type: 'string'},
		{name:'nro_boleto_reemision', type: 'string'},
		{name:'used', type: 'string'},
        {name:'procesado', type: 'string'},
        {name:'pnr_antiguo', type: 'string'},
        {name:'banco', type: 'string'},
        {name:'anulado', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
        {name:'fecha_reserva_antigua', type: 'date',dateFormat:'Y-m-d'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		
	],
	sortInfo:{
		field: 'id_venta_web_modificaciones',
		direction: 'DESC'
	},
    iniciarEventos : function () {
        this.Cmp.tipo.on('select',function(c,r) {
            if (r.data.field1 == 'reemision') {
                this.mostrarComponente(this.Cmp.nro_boleto);
                this.Cmp.nro_boleto.allowBlank = false;

                this.mostrarComponente(this.Cmp.nro_boleto_reemision);
                this.Cmp.nro_boleto_reemision.allowBlank = false;

                this.ocultarComponente(this.Cmp.fecha_reserva_antigua);
                this.Cmp.fecha_reserva_antigua.allowBlank = true;
                this.Cmp.fecha_reserva_antigua.reset();

                this.ocultarComponente(this.Cmp.pnr_antiguo);
                this.Cmp.pnr_antiguo.allowBlank = true;
                this.Cmp.pnr_antiguo.reset();

                this.ocultarComponente(this.Cmp.banco);
                this.Cmp.banco.allowBlank = true;
                this.Cmp.banco.reset();
            }
            else if (r.data.field1 == 'emision_manual') {
                this.mostrarComponente(this.Cmp.nro_boleto_reemision);
                this.Cmp.nro_boleto_reemision.allowBlank = false;

                this.mostrarComponente(this.Cmp.fecha_reserva_antigua);
                this.Cmp.fecha_reserva_antigua.allowBlank = false;

                this.mostrarComponente(this.Cmp.pnr_antiguo);
                this.Cmp.pnr_antiguo.allowBlank = false;

                this.ocultarComponente(this.Cmp.nro_boleto);
                this.Cmp.nro_boleto.allowBlank = true;
                this.Cmp.nro_boleto.reset();

                this.mostrarComponente(this.Cmp.banco);
                this.Cmp.banco.allowBlank = false;

            } else {
                this.mostrarComponente(this.Cmp.nro_boleto);
                this.Cmp.nro_boleto.allowBlank = false;

                this.ocultarComponente(this.Cmp.nro_boleto_reemision);
                this.Cmp.nro_boleto_reemision.allowBlank = true;
                this.Cmp.nro_boleto_reemision.reset();

                this.ocultarComponente(this.Cmp.fecha_reserva_antigua);
                this.Cmp.fecha_reserva_antigua.allowBlank = true;
                this.Cmp.fecha_reserva_antigua.reset();

                this.ocultarComponente(this.Cmp.pnr_antiguo);
                this.Cmp.pnr_antiguo.allowBlank = true;
                this.Cmp.pnr_antiguo.reset();

                this.ocultarComponente(this.Cmp.banco);
                this.Cmp.banco.allowBlank = true;
                this.Cmp.banco.reset();
            }
        },this);
    },
    preparaMenu:function() {
        var rec = this.sm.getSelected();

        Phx.vista.VentaWebModificaciones.superclass.preparaMenu.call(this);
        if (rec.data.procesado == 'si') {
            this.getBoton('del').disable();
            this.getBoton('edit').disable();
        } else {
            this.getBoton('del').enable();
            this.getBoton('edit').enable();
        }
    },

    onButtonEdit:function(){


        Phx.vista.VentaWebModificaciones.superclass.onButtonEdit.call(this);

        if (this.Cmp.tipo_dato.getValue() == 'reemision') {
            this.mostrarComponente(this.Cmp.nro_boleto);
            this.Cmp.nro_boleto.allowBlank = false;

            this.mostrarComponente(this.Cmp.nro_boleto_reemision);
            this.Cmp.nro_boleto_reemision.allowBlank = false;

            this.ocultarComponente(this.Cmp.fecha_reserva_antigua);
            this.Cmp.fecha_reserva_antigua.allowBlank = true;
            this.Cmp.fecha_reserva_antigua.reset();

            this.ocultarComponente(this.Cmp.pnr_antiguo);
            this.Cmp.pnr_antiguo.allowBlank = true;
            this.Cmp.pnr_antiguo.reset();

            this.ocultarComponente(this.Cmp.banco);
            this.Cmp.banco.allowBlank = true;
            this.Cmp.banco.reset();
        }
        else if (this.Cmp.tipo_dato.getValue() == 'emision_manual') {
            this.mostrarComponente(this.Cmp.nro_boleto_reemision);
            this.Cmp.nro_boleto_reemision.allowBlank = false;

            this.mostrarComponente(this.Cmp.fecha_reserva_antigua);
            this.Cmp.fecha_reserva_antigua.allowBlank = false;

            this.mostrarComponente(this.Cmp.pnr_antiguo);
            this.Cmp.pnr_antiguo.allowBlank = false;

            this.mostrarComponente(this.Cmp.banco);
            this.Cmp.banco.allowBlank = false;

            this.ocultarComponente(this.Cmp.nro_boleto);
            this.Cmp.nro_boleto.allowBlank = true;
            this.Cmp.nro_boleto.reset();

        } else {
            this.mostrarComponente(this.Cmp.nro_boleto);
            this.Cmp.nro_boleto.allowBlank = false;

            this.ocultarComponente(this.Cmp.nro_boleto_reemision);
            this.Cmp.nro_boleto_reemision.allowBlank = true;
            this.Cmp.nro_boleto_reemision.reset();

            this.ocultarComponente(this.Cmp.fecha_reserva_antigua);
            this.Cmp.fecha_reserva_antigua.allowBlank = true;
            this.Cmp.fecha_reserva_antigua.reset();

            this.ocultarComponente(this.Cmp.banco);
            this.Cmp.banco.allowBlank = true;
            this.Cmp.banco.reset();

            this.ocultarComponente(this.Cmp.pnr_antiguo);
            this.Cmp.pnr_antiguo.allowBlank = true;
            this.Cmp.pnr_antiguo.reset();
        }

    },

    onButtonNew:function(){


        Phx.vista.VentaWebModificaciones.superclass.onButtonNew.call(this);

        this.mostrarComponente(this.Cmp.nro_boleto);
        this.Cmp.nro_boleto.allowBlank = false;

        this.ocultarComponente(this.Cmp.nro_boleto_reemision);
        this.Cmp.nro_boleto_reemision.allowBlank = true;
        this.Cmp.nro_boleto_reemision.reset();

        this.ocultarComponente(this.Cmp.fecha_reserva_antigua);
        this.Cmp.fecha_reserva_antigua.allowBlank = true;
        this.Cmp.fecha_reserva_antigua.reset();

        this.ocultarComponente(this.Cmp.pnr_antiguo);
        this.Cmp.pnr_antiguo.allowBlank = true;
        this.Cmp.pnr_antiguo.reset();

        this.ocultarComponente(this.Cmp.banco);
        this.Cmp.banco.allowBlank = true;
        this.Cmp.banco.reset();

    },
	bdel:true,
	bsave:false
	}
)
</script>
		
		