<?Php
/**
 *@package PXP
 *@file   FormReporteVentasCorporativas.php
 *@author  MAM
 *@date    09-11-2016
 *@description Reportes de deposito
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.FormReporteVentasCorporativas = Ext.extend(Phx.frmInterfaz, {
        Atributos : [
            
            {
                config:{
                    name: 'tipo_agencia',
                    fieldLabel: 'Tipo Agencia',
                    allowBlank:false,
                    emptyText:'Tipo...',
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local',
                    gwidth: 150,
                    store:['corporativa','noiata','todas']
                },
                type:'ComboBox',
                id_grupo:1,
                form:true
            },
            {
                config:{
                    name: 'forma_pago',
                    fieldLabel: 'Forma Pago',
                    allowBlank:false,
                    emptyText:'Forma...',
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local',
                    gwidth: 150,
                    store:['prepago','postpago','todas']
                },
                type:'ComboBox',
                id_grupo:1,
                form:true
            },
            {
			config:{
				name: 'id_lugar',
				fieldLabel: 'Lugar',
				allowBlank: true,
				emptyText:'Lugar...',
				store:new Ext.data.JsonStore(
				{
					url: '../../sis_parametros/control/Lugar/listarLugar',
					id: 'id_lugar',
					root: 'datos',
					sortInfo:{
						field: 'nombre',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_lugar','id_lugar_fk','codigo','nombre','tipo','sw_municipio','sw_impuesto','codigo_largo'],
					// turn on remote sorting
					remoteSort: true,
					baseParams:{par_filtro:'lug.nombre',es_regional:'si'}
				}),
				valueField: 'id_lugar',
				displayField: 'nombre',				
				hiddenName: 'id_lugar',
    			triggerAction: 'all',
    			lazyRender:true,
				mode:'remote',
				pageSize:50,
				queryDelay:500,
				anchor:"35%",				
				minChars:2,
				enableMultiSelect:true
			},
			type:'AwesomeCombo',			
			form:true
		},
            {
                config:{
                    name:'id_moneda',
                    origen:'MONEDA',
                    allowBlank:false,
                    fieldLabel:'Moneda'
                },
                type:'ComboRec',
                id_grupo:1,
                form:true
            },

            {
                config:{
                    name: 'fecha_ini',
                    fieldLabel: 'Fecha Inicio',
                    allowBlank: false,
                    anchor: '30%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'fecha_ini',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'fecha_fin',
                    fieldLabel: 'Fecha Fin',
                    allowBlank: false,
                    anchor: '30%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'fecha_fin',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
            }],

        title : 'Reporte Deposito',
        ActSave : '../../sis_obingresos/control/DetalleBoletosWeb/conciliacionBancaInter',

        topBar : true,
        botones : false,
        labelSubmit : 'Generar',
        tooltipSubmit : '<b>Reporte Ventas Agencias</b>',

        constructor : function(config) {
            Phx.vista.FormReporteVentasCorporativas.superclass.constructor.call(this, config);
            this.init();
        },
        onSubmit: function(){
			if (this.form.getForm().isValid()) {
				var data={};
				data.fecha_ini=this.getComponente('fecha_ini').getValue();				
				data.fecha_fin=this.getComponente('fecha_fin').getValue();
				data.id_moneda=this.getComponente('id_moneda').getValue();
				data.moneda=this.getComponente('id_moneda').getRawValue();
				data.id_lugar=this.getComponente('id_lugar').getValue();
				data.lugar=this.getComponente('id_lugar').getRawValue();
				data.forma_pago=this.getComponente('forma_pago').getValue();
				data.tipo_agencia=this.getComponente('tipo_agencia').getValue();
				
				Phx.CP.loadWindows('../../../sis_obingresos/vista/detalle_boletos_web/GridReporteVentasCorporativas.php', 
						'Reporte Ventas Corporativas: del '+data.fecha_ini.dateFormat('d/m/Y') + ' al '+data.fecha_fin.dateFormat('d/m/Y') ,
					{
						width : '90%',
						height : '80%'
					}, data	, this.idContenedor, 'GridReporteVentasCorporativas')
			}
		},

        
        tipo : 'reporte',
        clsSubmit : 'bprint'

    })
</script>
