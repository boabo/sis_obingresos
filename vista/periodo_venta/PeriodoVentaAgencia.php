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
Phx.vista.PeriodoVentaAgencia=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;		
    	//llama al constructor de la clase padre
		Phx.vista.PeriodoVentaAgencia.superclass.constructor.call(this,config);
		this.init();
		this.addButton('btnDetalle',
            {
                text: 'Detalle',
                iconCls: 'blist',
                disabled: true,                
                handler: this.onDetalle,
                tooltip: 'Detalle de ventas agencia por periodo'
            }
        );
        
        this.addButton('btnTkt',
                {
                    text: 'Tkts',
                    iconCls: 'blist',
                    disabled: true,
                    handler: this.onTkts,
                    tooltip: 'Billetes emitidos de la agencia corporativa'
                }
            );
        
		this.iniciarEventos();
		this.store.baseParams.id_periodo_venta = this.maestro.id_periodo_venta;
		this.load({params:{start:0, limit:this.tam_pag}});
		
	},	
	
	onDetalle : function () {
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
    onTkts : function () {
            var rec = {maestro: this.sm.getSelected().data};

            Phx.CP.loadWindows('../../../sis_obingresos/vista/detalle_boletos_web/DetalleBoletosWeb.php',
                'Boletos',
                {
                    width:800,
                    height:'90%'
                },
                rec,
                this.idContenedor,
                'DetalleBoletosWeb');

    },      
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_periodo_venta_agencia'
			},
			type:'Field',
			form:true 
		},
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
                name: 'estado',
                fieldLabel: 'Estado Per.',
                gwidth: 80,
                renderer:function (value,p,record){
                    if(record.data.estado == 'cerrado'){
                        return  String.format('{0}', value);
                    }
                    else{
                        return  String.format('<b><font size=2 color="red">{0}</font><b>', value);
                    }
                }
            },
            type:'Field',
            filters:{pfiltro:'pva.estado',type:'string'},
            bottom_filter : false,
            grid:true,
            form:false
        },
		{
            config:{
                name: 'codigo_int',
                fieldLabel: 'Officeid',
                gwidth: 100,
                renderer:function (value,p,record){
                    if(record.data.estado == 'cerrado'){
                        return  String.format('{0}', value);
                    }
                    else{
                        return  String.format('<b><font size=2 color="red">{0}</font><b>', value);
                    }
                }
            },
            type:'Field',
            filters:{pfiltro:'age.codigo_int',type:'string'},
            bottom_filter : true,
            grid:true,
            form:false
        },
        {
            config:{
                name: 'nombre',
                fieldLabel: 'Agencia',
                gwidth: 130,
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
            grid:true,
            filters:{pfiltro:'age.nombre',type:'string'},
            bottom_filter : true,
            form:false
        },
        {
            config:{
                name: 'moneda_restrictiva',
                fieldLabel: 'Mon. Rest',
                gwidth: 70
            },
            type:'Field',
            filters:{pfiltro:'pva.moneda_restrictiva',type:'string'},
            bottom_filter : true,
            grid:true,
            form:false
        },
        
        {
            config:{
                name: 'total_credito_mb',
                fieldLabel: 'Total Credito MB',
                gwidth: 130,
                galign:'right',
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
            grid:true,
            form:false
        },
        {
            config:{
                name: 'total_debito_mb',
                fieldLabel: 'Total Debito MB',
                gwidth: 130,
                galign:'right',
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
            grid:true,
            form:false
        },
        {
            config:{
                name: 'total_credito_me',
                fieldLabel: 'Total Credito USD',
                gwidth: 130,
                galign:'right',
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
            grid:true,
            form:false
        },
        {
            config:{
                name: 'total_debito_usd',
                fieldLabel: 'Total Debito USD',
                gwidth: 130,
                galign:'right',
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
            grid:true,
            form:false
        },
        {
            config:{
                name: 'monto_mb',
                fieldLabel: 'Saldo a pagar MB',
                gwidth: 130,
                galign:'right',
                renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number((value*-1),'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number((value*-1),'0,000.00'));
						}
					}
            },
            type:'NumberField',
            grid:true,
            form:false
        },
        {
            config:{
                name: 'monto_usd',
                fieldLabel: 'Saldo a pagar USD',
                gwidth: 130,
                galign:'right',
                renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number((value*-1),'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number((value*-1),'0,000.00'));
						}
					}
            },
            type:'NumberField',
            grid:true,
            form:false
        },
        {
            config:{
                name: 'total_boletos_mb',
                fieldLabel: 'Total Boletos MB',
                gwidth: 130,
                galign:'right',
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
            grid:true,
            form:false
        },
        {
            config:{
                name: 'total_boletos_usd',
                fieldLabel: 'Total Boletos USD',
                gwidth: 130,
                galign:'right',
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
            grid:true,
            form:false
        },
        {
            config:{
                name: 'total_comision_mb',
                fieldLabel: 'Total Comis. MB',
                gwidth: 130,
                galign:'right',
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
            grid:true,
            form:false
        },
        {
            config:{
                name: 'total_comision_usd',
                fieldLabel: 'Total Comis. USD',
                gwidth: 130,
                galign:'right',
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
            grid:true,
            form:false
        },
        
	],
	tam_pag:25,	
	title:'Agencias',
	
	ActList:'../../sis_obingresos/control/PeriodoVenta/listarTotalesPeriodoAgencia',
	id_store:'id_periodo_venta_agencia',
	fields: [
		{name:'id_periodo_venta_agencia', type: 'numeric'},
		{name:'id_periodo_venta', type: 'numeric'},
		{name:'id_agencia', type: 'numeric'},
		{name:'codigo_int', type: 'string'},
        {name:'estado', type: 'string'},
		{name:'nombre', type: 'string'},
		{name:'tipo_reg', type: 'string'},
        {name:'moneda_restrictiva', type: 'string'},
		{name:'total_credito_mb', type: 'numeric'},
		{name:'total_credito_me', type: 'numeric'},
		{name:'total_debito_mb', type: 'numeric'},
		{name:'total_debito_usd', type: 'numeric'},
		{name:'total_boletos_mb', type: 'numeric'},
		{name:'total_boletos_usd', type: 'numeric'},
		{name:'total_comision_mb', type: 'numeric'},
		{name:'total_comision_usd', type: 'numeric'},
		{name:'monto_mb', type: 'numeric'},
		{name:'monto_usd', type: 'numeric'}		
		
	],
	sortInfo:{
		field: 'nombre',
		direction: 'ASC'
	},
	preparaMenu:function()
    {	var rec = this.sm.getSelected();        
        Phx.vista.PeriodoVentaAgencia.superclass.preparaMenu.call(this); 
        if (rec.data.tipo_reg != 'summary') {
        	this.getBoton('btnDetalle').enable();
        	this.getBoton('btnTkt').enable();
        } else {
        	this.getBoton('btnDetalle').disable(); 
        	this.getBoton('btnTkt').disable();
        }  
    },
    liberaMenu:function()
    {	
               
        Phx.vista.PeriodoVentaAgencia.superclass.liberaMenu.call(this);
        this.getBoton('btnDetalle').disable();  
        this.getBoton('btnTkt').disable();
    },
	bdel:false,
	bsave:false,
    bnew:false,
    bedit:false
	}
)
</script>
		
		