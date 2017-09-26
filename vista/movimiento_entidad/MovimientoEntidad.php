<?php
/**
*@package pXP
*@file gen-MovimientoEntidad.php
*@author  (jrivera)
*@date 17-05-2017 15:53:35
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.MovimientoEntidad=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;

    	//llama al constructor de la clase padre
		Phx.vista.MovimientoEntidad.superclass.constructor.call(this,config);
		this.init();
        this.store.baseParams.id_entidad = this.maestro.id_agencia;
        if('id_periodo_venta' in this.maestro){
		    this.store.baseParams.id_periodo_venta = this.maestro.id_periodo_venta;
		}
		this.load({params:{start:0, limit:this.tam_pag}})
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_movimiento_entidad'
			},
			type:'Field',
			form:true 
		},
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
                store:['debito','credito'],
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
                pfiltro:'moe.tipo',
                type: 'list',
                options: ['debito','credito']
            },
            id_grupo:1,
            grid:false,
            form:true
        },

        {
            config:{
                store:['si','no'],
                typeAhead: false,
                allowBlank : false,
                name: 'garantia',
                fieldLabel: 'Es garantia',
                mode: 'local',
                emptyText:'Tipo...',
                triggerAction: 'all',
                lazyRender:true,
                width:135,
                readOnly:true
            },
            type:'ComboBox',
            filters:{
                pfiltro:'moe.garantia',
                type: 'list',
                options: ['si','no']
            },
            id_grupo:1,
            grid:true,
            form:true,
            valorInicial : 'no'
        },

        {
            config:{
                store:['si','no'],
                typeAhead: false,
                allowBlank : false,
                name: 'ajuste',
                fieldLabel: 'Es ajuste',
                mode: 'local',
                emptyText:'Tipo...',
                triggerAction: 'all',
                lazyRender:true,
                width:135
            },
            type:'ComboBox',
            filters:{
                pfiltro:'moe.ajuste',
                type: 'list',
                options: ['si','no']
            },
            id_grupo:1,
            grid:true,
            form:false
        },


        {
            config:{
                name: 'fecha',
                fieldLabel: 'Fecha',
                allowBlank: false,
                anchor: '80%',
                gwidth: 100,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
            type:'DateField',
            filters:{pfiltro:'moe.fecha',type:'date'},
            id_grupo:1,
            grid:true,
            form:false
        },
        {
            config:{
                name: 'autorizacion__nro_deposito',
                fieldLabel: '#Aut o #Depo',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:200,
                renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', value);
						}
						else{
							return '<b><p align="right">Total: &nbsp;&nbsp; </p></b>';
						}
					}                
            },
            type:'TextField',
            filters:{pfiltro:'moe.autorizacion__nro_deposito',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'credito_mb',
                fieldLabel: 'Credito MB',
                allowBlank: false,
                anchor: '80%',
                gwidth: 100,
                maxLength:1179650,
                galign: 'right',
                renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
					}
            },
            type:'NumberField',            
            id_grupo:1,
            grid:true,
            form:false
        },
       {
            config:{
                name: 'debito_mb',
                fieldLabel: 'Debito MB',
                allowBlank: false,
                anchor: '80%',
                gwidth: 100,
                maxLength:1179650,
                galign: 'right',
                renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
					}
            },
            type:'NumberField',            
            id_grupo:1,
            grid:true,
            form:false
        },
        {
            config:{
                name: 'pnr',
                fieldLabel: 'Pnr',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:8,
                renderer:function (value,p,record){
                    if(record.data.tipo_reg != 'summary'){
                        return  String.format('{0}', value);
                    }
                    else{
                        return '<b><p align="right">Saldo: &nbsp;&nbsp; </p></b>';
                    }
                }
            },
            type:'TextField',
            filters:{pfiltro:'moe.pnr',type:'string'},
            id_grupo:1,
            grid:true,
            form:false
        },
        {
            config:{
                name: 'apellido',
                fieldLabel: 'Apellido',
                allowBlank: true,
                anchor: '80%',
                gwidth: 200,
                maxLength:200,
                renderer:function (value,p,record){
                    if(record.data.tipo_reg != 'summary'){
                        return  String.format('{0}', value);
                    }
                    else{
                        return  String.format('<p align="right"><b><font size=2 >{0}</font><b></p>', Ext.util.Format.number(record.data.credito_mb - record.data.debito_mb,'0,000.00'));
                    }
                }
            },
            type:'TextField',
            filters:{pfiltro:'moe.apellido',type:'string'},
            id_grupo:1,
            grid:true,
            form:false
        },

        {
            config:{
                name:'id_moneda',
                origen:'MONEDA',
                allowBlank: false ,
                width: '80%',
                fieldLabel: 'Moneda',
                gdisplayField : 'moneda',
                renderer:function (value, p, record){return String.format('{0}', record.data['moneda']);}
            },
            type: 'ComboRec',
            id_grupo: 0,
            filters:{pfiltro:'mon.codigo_internacional',type:'string'},
            form: true,
            grid:true
        },
        {
            config:{
                name: 'credito',
                fieldLabel: 'Credito',
                allowBlank: false,
                anchor: '80%',
                gwidth: 100,
                maxLength:1179650,
                galign: 'right',
                renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}

					}
            },
            type:'NumberField',            
            id_grupo:1,
            grid:true,
            form:false
        },
       {
            config:{
                name: 'debito',
                fieldLabel: 'Debito',
                allowBlank: false,
                anchor: '80%',
                gwidth: 100,
                maxLength:1179650,
                galign: 'right',
                renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
					}
            },
            type:'NumberField',            
            id_grupo:1,
            grid:true,
            form:false
        },



        {
            config:{
                name: 'monto_total',
                fieldLabel: 'Monto Total',
                allowBlank: false,
                anchor: '80%',
                gwidth: 100,
                maxLength:1179650,
                galign: 'right',
                renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
					}
            },
            type:'NumberField',
            filters:{pfiltro:'moe.monto_total',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:false
        },
        {
            config:{
                name: 'monto',
                fieldLabel: 'Monto',
                allowBlank: false,
                anchor: '80%',
                gwidth: 100,
                maxLength:1179650
            },
            type:'NumberField',
            filters:{pfiltro:'moe.monto',type:'numeric'},
            id_grupo:1,
            grid:false,
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
				filters:{pfiltro:'moe.estado_reg',type:'string'},
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
				filters:{pfiltro:'moe.fecha_reg',type:'date'},
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
				filters:{pfiltro:'moe.usuario_ai',type:'string'},
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
				filters:{pfiltro:'moe.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'moe.fecha_mod',type:'date'},
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
	title:'Movimientos',
	ActSave:'../../sis_obingresos/control/MovimientoEntidad/insertarMovimientoEntidad',
	ActDel:'../../sis_obingresos/control/MovimientoEntidad/eliminarMovimientoEntidad',
	ActList:'../../sis_obingresos/control/MovimientoEntidad/listarMovimientoEntidad',
	id_store:'id_movimiento_entidad',
	fields: [
		{name:'id_movimiento_entidad', type: 'numeric'},
		{name:'id_moneda', type: 'numeric'},
		{name:'id_periodo_venta', type: 'numeric'},
		{name:'id_agencia', type: 'numeric'},
		{name:'garantia', type: 'string'},
        {name:'moneda', type: 'string'},
		{name:'monto_total', type: 'numeric'},
		{name:'tipo', type: 'string'},
		{name:'autorizacion__nro_deposito', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'credito', type: 'numeric'},
		{name:'debito', type: 'numeric'},
		{name:'credito_mb', type: 'numeric'},
		{name:'debito_mb', type: 'numeric'},
		{name:'ajuste', type: 'string'},
		{name:'fecha', type: 'date',dateFormat:'Y-m-d'},
		{name:'pnr', type: 'string'},
		{name:'apellido', type: 'string'},
		{name:'tipo_reg', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		
	],
	arrayDefaultColumHidden:[
	'fecha_mod','usr_reg','estado_reg','fecha_reg','usr_mod','usuario_ai'],
	
	sortInfo:{
		field: 'fecha',
		direction: 'ASC'
	},
	bdel:false,
    bedit:true,
	bsave:false,
	bnew:true,
    loadValoresIniciales:function(){
        this.Cmp.id_agencia.setValue(this.maestro.id_agencia);
        this.Cmp.garantia.setValue('no');

    }
	}
)
</script>
		
		