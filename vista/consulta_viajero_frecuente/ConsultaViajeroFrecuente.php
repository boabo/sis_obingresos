<?php
/**
*@package pXP
*@file gen-ConsultaViajeroFrecuente.php
*@author  (miguel.mamani)
*@date 15-12-2017 14:59:25
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.ConsultaViajeroFrecuente=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.ConsultaViajeroFrecuente.superclass.constructor.call(this,config);
		this.init();
		this.load({params:{start:0, limit:this.tam_pag}})
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_consulta_viajero_frecuente'
			},
			type:'Field',
			form:true 
		},
		{
			config:{
				name: 'ffid',
				fieldLabel: 'FFID',
				allowBlank: false,
				anchor: '80%',
				gwidth: 150,
                style: 'background-color: #F39E8C; background-image: none;'
			},
				type:'NumberField',
				filters:{pfiltro:'vif.ffid',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
        {
            config:{
                name: 'voucher_code',
                fieldLabel: 'Voucher Code',
                allowBlank: false,
                anchor: '80%',
                gwidth: 200,
                maxLength:60,
                style: 'background-color: #F39E8C; background-image: none;'
            },
            type:'TextField',
            filters:{pfiltro:'vif.voucher_code',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },

		{
			config:{
				name: 'message',
				fieldLabel: 'Message',
				allowBlank: true,
				anchor: '80%',
				gwidth: 200,
				maxLength:200
			},
				type:'TextField',
				filters:{pfiltro:'vif.message',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'status',
				fieldLabel: 'Status',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:20,
                renderer : function(value, p, record) {
                    if (record.data['status'] == 'NOK') {
                        return String.format('<div title="Anulado"><b><font color="red">{0}</font></b></div>', value);

                    } else {
                        return String.format('<div title="Bien"><b><font color="green">{0}</font></b></div>', value);
                    }
                }
			},
				type:'TextField',
				filters:{pfiltro:'vif.status',type:'string'},
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
            filters:{pfiltro:'vif.estado_reg',type:'string'},
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
				filters:{pfiltro:'vif.fecha_reg',type:'date'},
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
				filters:{pfiltro:'vif.usuario_ai',type:'string'},
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
				filters:{pfiltro:'vif.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'vif.fecha_mod',type:'date'},
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
	title:'consulta viajero frecuente',
	ActSave:'../../sis_obingresos/control/ConsultaViajeroFrecuente/insertarConsultaViajeroFrecuente',
	ActDel:'../../sis_obingresos/control/ConsultaViajeroFrecuente/eliminarConsultaViajeroFrecuente',
	ActList:'../../sis_obingresos/control/ConsultaViajeroFrecuente/listarConsultaViajeroFrecuente',
	id_store:'id_consulta_viajero_frecuente',
	fields: [
		{name:'id_consulta_viajero_frecuente', type: 'numeric'},
		{name:'ffid', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'message', type: 'string'},
		{name:'voucher_code', type: 'string'},
		{name:'status', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'}
		
	],
	sortInfo:{
		field: 'id_consulta_viajero_frecuente',
		direction: 'DESC'
	},
	bdel:true,
	bsave:false,

    Grupos: [
        {
            layout: 'column',
            border: false,
            defaults: {
                border: false
            },

            items: [
                {
                    bodyStyle: 'padding-right:10px;',
                    items: [
                        {
                            xtype: 'fieldset',
                            columnWidth: 1,
                            defaults: {
                                anchor: '-2' // leave room for error icon
                            },
                            title: 'Datos Voucher',
                            items: [],
                            id_grupo: 1
                        }
                    ]
                }
            ]
        }
    ],
    fheight: 200,
    fwidth: 400


    /*,
    onButtonNew:function(){
        Phx.vista.ConsultaViajeroFrecuente.superclass.onButtonNew.call(this);
        this.getComponente('voucher_code').setValue('OB.FF.VO');
    }*/
	}
)
</script>
		
		