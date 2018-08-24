<?php
/**
 * @package pxP
 * @file 	repkardex.php
 * @author 	RCM
 * @date	10/07/2013
 * @description	Archivo con la interfaz de usuario que permite la ejecucion de las funcionales del sistema
 */
include_once ('../../media/styles.php');
header("content-type:text/javascript; charset=UTF-8");
?>
<script>
	Phx.vista.GridReporteVentasCorporativas = Ext.extend(Phx.gridInterfaz, {
        viewConfig: {
            getRowClass: function(record) {
                if(record.data.saldo < 0){
                    return 'prioridad_importanteA';
                }
            }
        },
		constructor : function(config) {
			this.maestro = config;
			this.description = this.maestro.tipo_agencia;
			Phx.vista.GridReporteVentasCorporativas.superclass.constructor.call(this, config);
			this.init();
			this.load({
				params : {
					start: 0,
					limit: 1000,
					fecha_ini:this.maestro.fecha_ini.dateFormat('d/m/Y'),
					fecha_fin:this.maestro.fecha_fin.dateFormat('d/m/Y'),
					//id_moneda:this.maestro.id_moneda,
					id_lugar:this.maestro.id_lugar,
					tipo_agencia:this.maestro.tipo_agencia,
					forma_pago:this.forma_pago
				}
			});
		},
		tam_pag:1000,
		Atributos : [{
			config : {
				labelSeparator : '',
				inputType : 'hidden',
				name : 'id_agencia'
			},
			type : 'Field',
			form : true
		}, 		
		{
			config : {
				name : 'nombre',
				fieldLabel : 'Nombre Agencia',
				allowBlank : false,
				anchor : '100%',
				gwidth : 300,
				maxLength : 20
				
				
			},
			type : 'Field',
			filters : {
			    pfiltro : 'a.nombre',
				type : 'string'
			},
			id_grupo : 1,
            bottom_filter:true,
			grid : true,
			form : false
		},
		{
			config : {
				name : 'codigo_int',
				fieldLabel : 'OfficeId',
				allowBlank : false,
				anchor : '100%',
				gwidth : 100,
				maxLength : 20
				
				
			},
			type : 'Field',
			filters : {
			    pfiltro : 'a.codigo_int',
				type : 'string'
			},
			id_grupo : 1,
			grid : true,
			form : false
		},
		
		{
			config : {
				name : 'tipo_agencia',
				fieldLabel : 'Tipo Agencia',
				allowBlank : false,
				anchor : '100%',
				gwidth : 100,
				maxLength : 20
				
				
			},
			type : 'Field',
			filters : {
			    pfiltro : 'tipo_agencia',
				type : 'string'
			},
			id_grupo : 1,
			grid : true,
			form : false
		},
		{
			config : {
				name : 'codigo_ciudad',
				fieldLabel : 'Ciudad',
				allowBlank : false,
				anchor : '100%',
				gwidth : 80,
				maxLength : 20,
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', record.data['codigo_ciudad']);
						}
						else{
							return '<b><p align="right">Total: &nbsp;&nbsp; </p></b>';
						}
					}   
				
				
			},
			type : 'Field',
			filters : {
			    pfiltro : 'l.codigo',
				type : 'string'
			},
			id_grupo : 1,
			grid : true,
			form : false
		},
		
		
		{
			config : {
				name : 'monto_creditos',
				fieldLabel : 'Creditos',
				allowBlank : false,
				anchor : '100%',
				gwidth : 130,
				maxLength : 20,
				galign:'right',
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(record.data.monto_creditos,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.monto_creditos,'0,000.00'));
						}
					} 
			},
			type : 'NumberField',
			
			id_grupo : 1,
			grid : true,
			form : false
		},
		{
			config : {
				name : 'monto_debitos',
				fieldLabel : 'Debitos',
				allowBlank : false,
				anchor : '100%',
				gwidth : 130,
				maxLength : 20,
				galign:'right',
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(record.data.monto_debitos,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.monto_debitos,'0,000.00'));
						}
					} 
			},
			type : 'NumberField',
			
			id_grupo : 1,
			grid : true,
			form : false
		},
		{
			config : {
				name : 'monto_ajustes',
				fieldLabel : 'Ajustes',
				allowBlank : false,
				anchor : '100%',
				gwidth : 130,
				maxLength : 20,
				galign:'right',
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(record.data.monto_ajustes,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.monto_ajustes,'0,000.00'));
						}
					} 
			},
			type : 'NumberField',
			
			id_grupo : 1,
			grid : true,
			form : false
		},
		{
			config : {
				name : 'saldo',
				fieldLabel : 'Saldo',
				allowBlank : false,
				anchor : '100%',
				gwidth : 130,
				maxLength : 20,
				galign:'right',
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(record.data.saldo,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.saldo,'0,000.00'));
						}
					} 
			},
			type : 'NumberField',
			
			id_grupo : 1,
			grid : true,
			form : false
		}	
		
		],
		title : 'Reporte Ventas Corporativas',
		ActList : '../../sis_obingresos/control/DetalleBoletosWeb/reporteVentasCorporativas',
		id_store : 'id_agencia',
		fields : [{
			name : 'id_agencia'
		}, {
			name : 'nombre',
			type : 'string'
		}, {
			name : 'codigo_int',
			type : 'string'
		}, {
			name : 'tipo_agencia',
			type : 'string'
		}, {
			name : 'codigo_ciudad',
			type : 'string'
		}, {
			name : 'tipo_reg',
			type : 'string'
		}, {
			name : 'monto_creditos',
			type : 'numeric'
		}, {
			name : 'monto_debitos',
			type : 'numeric'
		}, {
			name : 'monto_ajustes',
			type : 'numeric'
		}, {
			name : 'saldo',
			type : 'numeric'
		}],
		sortInfo : {
			field : 'nombre',
			direction : 'ASC'
		},
		bdel : false,
		bnew: false,
		bedit: false,
		fwidth : '90%',
		fheight : '80%'
	}); 
</script>
