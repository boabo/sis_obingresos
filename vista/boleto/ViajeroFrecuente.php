<?php
/**
*@package pXP
*@file gen-ViajeroFrecuente.php
*@author  (miguel.mamani)
*@date 12-12-2017 19:32:55
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.ViajeroFrecuente=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
        this.idContenedor = config.idContenedor;
        this.maestro=config;
        console.log('data',this.maestro);
        this.id_boleto_amadeus=this.maestro.id_boleto_amadeus;
		Phx.vista.ViajeroFrecuente.superclass.constructor.call(this,config);
		this.init();
        this.store.baseParams={id_boleto_amadeus:this.id_boleto_amadeus};
		this.load({params:{start:0, limit:this.tam_pag}})
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_viajero_frecuente'
			},
			type:'Field',
			form:true 
		},
        {
            //configuracion del componente
            config:{
                labelSeparator:'',
                inputType:'hidden',
                name: 'id_boleto_amadeus'
            },
            type:'Field',
            form:true
        },
        {
            //configuracion del componente
            config:{
                labelSeparator:'',
                inputType:'hidden',
                name: 'id_pasajero_frecuente'
            },
            type:'Field',
            form:true
        },
		{
			config:{
				name: 'nombre_completo',
				fieldLabel: 'Nombre Completo',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:100
			},
				type:'TextField',
				filters:{pfiltro:'vfb.nombre_completo',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
        {
            config:{
                name: 'ticket_number',
                fieldLabel: 'Ticket Number',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:50
            },
            type:'TextField',
            filters:{pfiltro:'vfb.ticket_number',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'ffid',
                fieldLabel: 'FFID',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:50
            },
            type:'TextField',
            filters:{pfiltro:'vfb.ffid',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
		{
			config:{
				name: 'voucher_code',
				fieldLabel: 'Voucher Code',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:50
			},
				type:'TextField',
				filters:{pfiltro:'vfb.voucher_code',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'pnr',
				fieldLabel: 'PNR',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:30
			},
				type:'TextField',
				filters:{pfiltro:'vfb.pnr',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'status',
				fieldLabel: 'status',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:50
			},
				type:'TextField',
				filters:{pfiltro:'vfb.status',type:'string'},
				id_grupo:1,
				grid:false,
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
            filters:{pfiltro:'vfb.estado_reg',type:'string'},
            id_grupo:1,
            grid:true,
            form:false
        },
		{
			config:{
				name: 'mensaje',
				fieldLabel: 'mensaje',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:200
			},
				type:'TextField',
				filters:{pfiltro:'vfb.mensaje',type:'string'},
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
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'vfb.fecha_reg',type:'date'},
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
				filters:{pfiltro:'vfb.usuario_ai',type:'string'},
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
				filters:{pfiltro:'vfb.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'vfb.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,	
	title:'Viajero Frecuente',
	ActSave:'../../sis_obingresos/control/ViajeroFrecuente/insertarViajeroFrecuente',
	ActDel:'../../sis_obingresos/control/ViajeroFrecuente/eliminarViajeroFrecuente',
	ActList:'../../sis_obingresos/control/ViajeroFrecuente/listarViajeroFrecuente',
	id_store:'id_viajero_frecuente',
	fields: [
		{name:'id_viajero_frecuente', type: 'numeric'},
		{name:'nombre_completo', type: 'string'},
		{name:'voucher_code', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'pnr', type: 'string'},
		{name:'status', type: 'string'},
		{name:'ffid', type: 'string'},
		{name:'ticket_number', type: 'string'},
		{name:'mensaje', type: 'string'},
		{name:'id_pasajero_frecuente', type: 'numeric'},
		{name:'id_boleto_amadeus', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'}
		
	],
	sortInfo:{
		field: 'id_viajero_frecuente',
		direction: 'ASC'
	},
	bdel:false,
	bsave:false,
    bnew:false,
    bedit:false,
    fwidth: 350,
    fheight: 220
	}
)
</script>
		
		