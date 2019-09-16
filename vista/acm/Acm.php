<?php
/**
*@package pXP
*@file gen-Acm.php
*@author  (jrivera)
*@date 05-09-2018 20:34:32
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

include_once ('../../media/styles.php');
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.Acm=Ext.extend(Phx.gridInterfaz,{

    viewConfig: {
        stripeRows: false,
        getRowClass: function(record) {
            console.log('registro', record.data.id_movimiento_entidad);
            if(record.data.id_movimiento_entidad == null){
                return 'prioridad_importanteA';
            }
        }/*,
        listener: {
            render: this.createTooltip
        },*/

    },
    stateId:'Acm',

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.Acm.superclass.constructor.call(this,config);
		this.init();
		var that = this;
		this.iniciarEventos();
    console.log("llega auqi",that.mov_ent);
    console.log("llega auqi",that);
		this.addButton('btnReporteAcm',
					{
							text: 'Reporte Excel',
							iconCls: 'bexcel',
							disabled: true,
							handler: this.onButtonReporte,
							tooltip: '<b>Generar Reporte</b><br/>Generar Reporte de ACM.'
					}
			);
      //if (that.mov_ent == 'SI' ) {
        this.addButton('btnEliminarAcm',
              {
                  text: 'Eliminar ACM',
                  iconCls: 'bdel',
                  disabled: true,
                  handler: this.onButtonEliminarACM,
                  tooltip: '<b>Eliminar</b><br/>ACM seleccionado.'
              }
          );
      //}

        if(that.acm==undefined){
            this.store.baseParams.acm = 'general';
            //this.load({params:{start:0, limit:this.tam_pag}});
        }else {
            this.store.baseParams.acm = that.acm;
            this.store.baseParams.id_archivo_acm_det = that.maestro.id_archivo_acm_det;
            // console.log('config',that.acm, that.maestro.id_archivo_acm_det);
        }
        if(that.acm == 'funcional' && that.maestro.id_archivo_acm_det ==undefined){
            this.store.baseParams.agencia = that.maestro.codigo_int;
            // console.log('config',that.acm, that.maestro.id_movimiento_entidad,  this.store.baseParams.agencia);
        }
		this.load({params:{start:0, limit:this.tam_pag}})
	},

	preparaMenu: function () {
			var rec = this.sm.getSelected();
      this.getBoton('btnReporteAcm').enable();
			this.getBoton('btnEliminarAcm').enable();
			Phx.vista.Acm.superclass.preparaMenu.call(this);
		},

		liberaMenu : function(){
				var rec = this.sm.getSelected();
        this.getBoton('btnReporteAcm').disable();
				this.getBoton('btnEliminarAcm').disable();
				Phx.vista.Acm.superclass.liberaMenu.call(this);
		},

    onButtonReporte: function() {
			Phx.CP.loadingShow();
			var d = this.sm.getSelected().data;
			console.log('codigo:',d.id_acm);

			Ext.Ajax.request({
							url:'../../sis_obingresos/control/Acm/reporteGeneralACM',
							params:{id_acm:d.id_acm,
											nombre:d.nombre,
											numero:d.numero,
											fecha:d.fecha.dateFormat('d/m/Y'),
											fecha_ini:d.fecha_ini.dateFormat('d/m/Y'),
											fecha_fin:d.fecha_fin.dateFormat('d/m/Y'),
											codigo:d.codigo,
											ruta:d.ruta,
											office_id:d.office_id,
											codigo_largo:d.codigo_largo

										},
							success: this.successExport,
							failure: this.conexionFailure,
							timeout:this.timeout,
							scope:this
			});
				console.log('EL OFFICE ID:',d.office_id);
		},

		onButtonEliminarACM: function() {
			//Phx.CP.loadingShow();
			var d = this.sm.getSelected().data;
      console.log("llega auqi ACM",d);
      //console.log("llega auqi dsadasd",Phx.CP.getPagina(this.idContenedorPadre));
			Ext.Ajax.request({
							url:'../../sis_obingresos/control/Acm/eliminarAcmMV',
							params:{id_acm:d.id_acm,
                      id_movimiento_entidad:d.id_movimiento_entidad,
                      id_archivo_acm_det:d.id_archivo_acm_det,
                      id_agencia:d.id_agencia,
                      fecha:d.fecha.dateFormat('d/m/Y'),
              	      },
							success: this.successEliminacion,
							failure: this.successEliminacion,
							timeout:this.timeout,
							scope:this
			});

		},

    successEliminacion:function(resp){
        Phx.CP.loadingHide();
        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
        if(!reg.ROOT.error){
            Ext.Msg.show({
             width:'100%',
             height:'100%',
             title:'<b style="color:Blue; font-size:15px;"><i style="color:green; font-size:20px;" class="fa fa-check" aria-hidden="true"></i> Eliminacion ACM</b>',
             msg: '<b style="color:green; font-size:12px;">El ACM se eliminó correctamente</b>',
             buttons: Ext.Msg.OK,
             fn: function () {
                Phx.CP.getPagina(this.idContenedorPadre).reload();
                this.panel.close();
             },
             scope:this
          });
          } else {
            Ext.Msg.show({
              width:'100%',
              height:'100%',
             title:'<center><p style="color:Blue; font-size:15px;"><i style="color:red; font-size:20px;" class="fa fa-exclamation-triangle" aria-hidden="true"></i> Eliminacion ACM</p></center>',
             msg: '<b style="color:black; font-size:12px;">'+reg.ROOT.detalle.mensaje+'</b>',
             buttons: Ext.Msg.OK,
             fn: function () {
                Phx.CP.getPagina(this.idContenedorPadre).reload();
                this.panel.close();
             },
             scope:this
          });
             // Phx.CP.getPagina(this.idContenedorPadre).reload();
             // this.panel.close();
          };

    },


	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_acm'
			},
			type:'Field',
			form:true
		},
		{
			config:{
				name: 'nombre',
				fieldLabel: 'Agencia',
				allowBlank: false,
				anchor: '100%',
				gwidth: 200,
				renderer: function(value, p, record){
						return String.format('<b style="color:blue; ">{0}</b>', record.data['nombre']);
				},
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'agen.nombre',type:'string'},
				id_grupo:1,
				grid:true,
				form:false,
				bottom_filter:true
		},
		{
			config:{
				name: 'numero',
				fieldLabel: 'Número',
				allowBlank: false,
				anchor: '80%',
				gwidth: 150,
				renderer: function(value, p, record){
						return String.format('<b style="color:red;vertical-align:middle;text-align:right;">{0}</b>', record.data['numero']);
				},
				maxLength:20
			},
				type:'TextField',
				filters:{pfiltro:'acm.numero',type:'string'},
				id_grupo:1,
				grid:true,
				form:true,
				bottom_filter:true
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
				filters:{pfiltro:'acm.fecha',type:'date'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'ruta',
				fieldLabel: 'Ruta',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				renderer: function(value, p, record){
						return String.format('<b style="color:#FC0050;vertical-align:middle;text-align:right;">{0}</b>', record.data['ruta']);
				},
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'acm.ruta',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},


		{
			config:{
				name: 'neto_total_mb',
				fieldLabel: 'Total Neto (Bs)',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
								return  String.format('<div style="color:#003473; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
								 return  String.format('<div style="vertical-align:middle;text-align:right;color:#003473;font-weight:bold;"><span ><b>{0}</b></span></div>', Ext.util.Format.number(record.data.sum_neto_b,'0,000.00'));
						}
				},
/*
				renderer: function(value, p, record){
						return String.format('<b style="color:green;">{0}</b>', record.data['importe']);
				},*/
				maxLength:1179650
			},
				type:'NumberField',
				filters:{pfiltro:'acmdet.neto_total_mb',type:'numeric'},
				id_grupo:1,
				grid:false,
				form:false
		},

		{
			config:{
				name: 'neto_total_mt',
				fieldLabel: 'Total Neto ($us)',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
								return  String.format('<div style="color:#00B25C; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
								return  String.format('<div style="vertical-align:middle;text-align:right;color:#00B25C;font-weight:bold;"><span ><b>{0}</b></span></div>', Ext.util.Format.number(record.data.sum_neto_b,'0,000.00'));
						}
				},

			/*
				renderer: function(value, p, record){
						return String.format('<b style="color:green;">{0}</b>', record.data['importe']);
				},*/
				maxLength:1179650
			},
				type:'NumberField',
				filters:{pfiltro:'acmdet.neto_total_mt',type:'numeric'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
		config:{
			name: 'total_bsp',
			fieldLabel: 'SUM/Com-BSP',
			allowBlank: true,
			anchor: '80%',
			gwidth: 100,
			renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
							return  String.format('<div style="color:#4D00B5; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
					}
					else{
							 return  String.format('<div style="vertical-align:middle;text-align:right;color:#4D00B5;font-weight:bold;"><span ><b>{0}</b></span></div>', Ext.util.Format.number(record.data.sum_neto_b,'0,000.00'));
					}
			},

		/*	renderer: function(value, p, record){
					return String.format('<b style="color:green;">{0}</b>', record.data['importe']);
			},*/
			maxLength:1179650
		},
			type:'NumberField',
			filters:{pfiltro:'acm.importe',type:'numeric'},
			id_grupo:1,
			grid:true,
			form:true
	},

		{
			config:{
				name: 'importe',
				fieldLabel: 'Total Importe (%)',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
								return  String.format('<div style="color:green; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
								 return  String.format('<div style="vertical-align:middle;text-align:right;color:green;font-weight:bold;"><span ><b>{0}</b></span></div>', Ext.util.Format.number(record.data.sum_neto_b,'0,000.00'));
						}
				},

			/*	renderer: function(value, p, record){
						return String.format('<b style="color:green;">{0}</b>', record.data['importe']);
				},*/
				maxLength:1179650
			},
				type:'NumberField',
				filters:{pfiltro:'acm.importe',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},




		{
			config:{
				name: 'codigo',
				fieldLabel: 'Moneda',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				renderer: function(value, p, record){
						return String.format('<b style="color:#940000;vertical-align:middle;text-align:right;">{0}</b>', record.data['codigo']);
				},
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'mone.codigo',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},

		{
			config: {
				name: 'id_moneda',
				fieldLabel: 'Moneda',
				allowBlank: true,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_parametros/control/Moneda/listarMoneda',
					id: 'id_moneda',
					root: 'datos',
					sortInfo: {
						field: 'id_moneda',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_moneda', 'codigo_internacional','moneda'],
					remoteSort: true,
					baseParams: {par_filtro: 'moneda.codigo_internacional#moneda.id_moneda'}
				}),
				valueField: 'id_moneda',
				displayField: 'codigo_internacional',
				gdisplayField: 'id_moneda',
				hiddenName: 'id_moneda',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '50%',
				gwidth: 150,
				minChars: 2,
				resizable:true,
				tpl: new Ext.XTemplate([
					'<tpl for=".">',
					'<div class="x-combo-list-item">',
					'<div class="awesomecombo-item {checked}">',
					'<p><b>Codigo:</b> <span style="color: blue; font-weight: bold;">{codigo_internacional}</span></p>',
					'</div><p><b>Descripcion:</b> <span style="color: green; font-weight: bold;">{moneda}</span></p>',
					'</div></tpl>'
				]),
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['id_moneda']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'moneda.codigo_internacional',type: 'string'},
			grid: false,
			form: true
		},
		{
			config: {
				name: 'id_archivo_acm_det',
				fieldLabel: 'Archivo ACM Det',
				allowBlank: true,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_obingresos/control/ArchivoAcmDet/listarArchivoAcmDet',
					id: 'id_archivo_acm_det',
					root: 'datos',
					sortInfo: {
						field: 'id_archivo_acm_det',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_archivo_acm_det', 'officce_id'],
					remoteSort: true,
					baseParams: {par_filtro: 'aad.officce_id#aad.id_archivo_acm_det'}
				}),
				valueField: 'id_archivo_acm_det',
				displayField: 'officce_id',
				gdisplayField: 'id_archivo_acm_det',
				hiddenName: 'id_archivo_acm_det',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '50%',
				gwidth: 150,
				minChars: 2,
				tpl: new Ext.XTemplate([
					'<tpl for=".">',
					'<div class="x-combo-list-item">',
					'<div class="awesomecombo-item {checked}">',
					'<p><b>Cod Acm Det:</b> <span style="color: blue; font-weight: bold;">{id_archivo_acm_det}</span></p>',
					'</div><p><b>Office ID:</b> <span style="color: green; font-weight: bold;">{officce_id}</span></p>',
					'</div></tpl>'
				]),
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['id_archivo_acm_det']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'aad.officce_id',type: 'string'},
			grid: false,
			form: true
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
				filters:{pfiltro:'acm.estado_reg',type:'string'},
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
				filters:{pfiltro:'acm.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'acm.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_movimiento_entidad',
				fieldLabel: 'Id Movimiento Entidad',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				renderer: function(value, p, record){
                    return String.format('<b style="color:#FF7400;vertical-align:middle;text-align:right;">{0}</b>', record.data['id_movimiento_entidad']);
				},
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'acm.id_movimiento_entidad',type:'string'},
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
				filters:{pfiltro:'acm.usuario_ai',type:'string'},
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
				filters:{pfiltro:'acm.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_ini',
				fieldLabel: 'Fecha Inicio.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'archi.fecha_ini',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_fin',
				fieldLabel: 'Fecha Final.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'archi.fecha_fin',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	title:'ACM',
	ActSave:'../../sis_obingresos/control/Acm/insertarAcm',
	ActDel:'../../sis_obingresos/control/Acm/eliminarAcm',
	ActList:'../../sis_obingresos/control/Acm/listarAcm',
	id_store:'id_acm',
	fields: [
		{name:'id_acm', type: 'numeric'},
		{name:'id_moneda', type: 'numeric'},
		{name:'id_archivo_acm_det', type: 'numeric'},
		{name:'fecha', type: 'date',dateFormat:'Y-m-d'},
		{name:'numero', type: 'string'},
		{name:'ruta', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'importe', type: 'numeric'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'desc_moneda', type: 'string'},
		{name:'nombre', type: 'string'},
		{name:'codigo', type: 'string'},
		{name:'id_movimiento_entidad', type: 'numeric'},
		{name:'fecha_ini', type: 'date',dateFormat:'Y-m-d'},
		{name:'fecha_fin', type: 'date',dateFormat:'Y-m-d'},
		{name:'neto_total_mb', type: 'numeric'},
		{name:'neto_total_mt', type: 'numeric'},
		{name:'total_bsp', type: 'numeric'},
		{name:'office_id', type: 'string'},
    {name:'codigo_largo', type: 'string'},
		{name:'id_agencia', type: 'numeric'},


	],
	sortInfo:{
		field: 'id_acm',
		direction: 'ASC'
	},

	bdel:false,
	bsave:false,
    bnew:false,
    bedit:false,

	tabsouth :[
		{
			url:'../../../sis_obingresos/vista/acm_det/AcmDet.php',
			title:'Detalle ACM',
			height:'50%',
			cls:'AcmDet'
		}
	],


	}
)
</script>
