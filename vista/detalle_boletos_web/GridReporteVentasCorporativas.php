<?php
/**
 * @package pxP
 * @file 	repkardex.php
 * @author 	RCM
 * @date	10/07/2013
 * @description	Archivo con la interfaz de usuario que permite la ejecucion de las funcionales del sistema
 */
header("content-type:text/javascript; charset=UTF-8");
?>
<script>
	Phx.vista.GridReporteVentasCorporativas = Ext.extend(Phx.gridInterfaz, {
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
					id_moneda:this.maestro.id_moneda,
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
				gwidth : 180,
				maxLength : 20
				
				
			},
			type : 'Field',
			filters : {
			    pfiltro : 'a.nombre',
				type : 'string'
			},
			id_grupo : 1,
			grid : true,
			form : false
		},
		{
			config : {
				name : 'codigo_int',
				fieldLabel : 'OfficeId',
				allowBlank : false,
				anchor : '100%',
				gwidth : 180,
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
				gwidth : 180,
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
				gwidth : 180,
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
				name : 'monto_total',
				fieldLabel : 'Total Ventas',
				allowBlank : false,
				anchor : '100%',
				gwidth : 150,
				maxLength : 20,
				galign:'right',
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(record.data.monto_total,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.monto_total,'0,000.00'));
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
			name : 'monto_total',
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
