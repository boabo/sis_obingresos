<?php
/**
*@package pXP
*@file gen-ViajeroInterno.php
*@author  (rzabala)
*@date 21-12-2018 14:21:03
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

include_once ('../../media/styles.php');
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.ViajeroInterno=Ext.extend(Phx.gridInterfaz,{
    viewConfig: {
        stripeRows: false,
        getRowClass: function(record) {
            console.log('registro', record.data);
            if(record.data.estado_reg == 'inactivo'){
                return 'prioridad_importanteA';
            }
        }/*,
        listener: {
            render: this.createTooltip
        },*/

    },
	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.ViajeroInterno.superclass.constructor.call(this,config);
		this.init();
		this.load({params:{start:0, limit:this.tam_pag}})

        /*this.addButton('btnSincronizar',
            {
                text: 'Sincronizar',
                iconCls: 'breload2',
                disabled: false,
                handler: this.onButtonBoVendido,
                tooltip: '<b>Asociar Boleto a voucher</b>'
            }
        );*/
	},
    preparaMenu: function () {
        var rec = this.sm.getSelected();
        //this.getBoton('btnBoleto').enable();
         Phx.vista.ViajeroInterno.superclass.preparaMenu.call(this);
    },
    liberaMenu : function(){
        var d = this.sm.getSelected.data;
        Phx.vista.ViajeroInterno.superclass.liberaMenu.call(this);
    },
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_viajero_interno'
			},
			type:'Field',
			form:true 
		},
		{
			config:{
				name: 'codigo_voucher',
				fieldLabel: 'Codigo Voucher',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10,
                minLength:10,
                style: 'background-color: #9BF592; background-image: none;',
                renderer: function(value, p, record){
                    return String.format('<b style="color:blue; ">{0}</b>', record.data['codigo_voucher']);
                }
			},
				type:'TextField',
				filters:{pfiltro:'cvi.codigo_voucher',type:'string'},
				id_grupo:1,
				grid:true,
				form:true,
                bottom_filter : true
		},
		{
			config:{
				name: 'mensaje',
				fieldLabel: 'Mensaje',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:200,
                renderer: function(value, p, record){
                    return String.format('<b style="color:darkslategray; ">{0}</b>', record.data['mensaje']);
                }
			},
				type:'TextField',
				filters:{pfiltro:'cvi.mensaje',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'estado',
				fieldLabel: 'Estado Voucher',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:20,
                renderer: function (value, p, record) {
                    if (record.data['estado'] == 'OK') {
                        return String.format('<div title="OK"><b><font color="green">{0}</font></b></div>', value);

                    } else {
                        return String.format('<div title="NOK"><b><font color="red">{0}</font></b></div>', value);
                    }
                }
			},
				type:'TextField',
				filters:{pfiltro:'cvi.estado',type:'string'},
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
				filters:{pfiltro:'cvi.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'cvi.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
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
				filters:{pfiltro:'cvi.usuario_ai',type:'string'},
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
                maxLength:10,
                renderer: function (value, p, record) {
                    if (record.data['estado_reg'] == 'activo') {
                        return String.format('<div title="OK"><b><font color="green">{0}</font></b></div>', value);

                    } else {
                        return String.format('<div title="NOK"><b><font color="red">{0}</font></b></div>', value);
                    }
                }
            },
            type:'TextField',
            filters:{pfiltro:'cvi.estado_reg',type:'string'},
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
				filters:{pfiltro:'cvi.fecha_mod',type:'date'},
				id_grupo:1,
				grid:false,
				form:false
		}
	],
	tam_pag:50,	
	title:'Consulta Viajero Interno',
	ActSave:'../../sis_obingresos/control/ViajeroInterno/insertarViajeroInterno',
	ActDel:'../../sis_obingresos/control/ViajeroInterno/eliminarViajeroInterno',
	ActList:'../../sis_obingresos/control/ViajeroInterno/listarViajeroInterno',
	id_store:'id_viajero_interno',
	fields: [
		{name:'id_viajero_interno', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'codigo_voucher', type: 'string'},
		{name:'mensaje', type: 'string'},
		{name:'estado', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		
	],
	sortInfo:{
		field: 'id_viajero_interno',
		direction: 'DESC'
	},
	bdel:false,
	bsave:false,
    btest:false,
    //bdel: false,
    bedit: false,
    //bsave: false,

    tabsouth :[
        {
            url:'../../../sis_obingresos/vista/viajero_interno_det/ViajeroInternoDet.php',
            title:'Detalle Viajero Interno',
            height:'50%',
            cls:'ViajeroInternoDet'
        }
    ],
	}
)
</script>
		
		