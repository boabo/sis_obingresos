<?php
/**
*@package pXP
*@file gen-ArchivoAcmDet.php
*@author  (admin)
*@date 05-09-2018 20:36:49
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.ArchivoAcmDet=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.ArchivoAcmDet.superclass.constructor.call(this,config);
		this.init();
		this.iniciarEventos();
		this.addButton('btnVentana',
            {
			    text: 'Detalle Acm',
				iconCls: 'blist',
				disabled: true,
				handler: this.onButtonAcm,
				tooltip: '<b>Detalle Acm</b></br>Muestra el Detalle ACM generado.'
			}
		);


		//this.load({params:{start:0, limit:this.tam_pag}})

	},
    iniciarEventos: function(){
	    this.Cmp.id_archivo_acm.on('select', function(cmb, record, index){},this);
    },


		preparaMenu: function () {
            var tb = this.tbar;
				var rec = this.sm.getSelected();
				console.log('que es',rec.data.estado);
				if (rec !== ''){
				    if(rec.data.neto_total_mb !== null && rec.data.importe_total_mb !== null && rec.data.cant_bol_mb !== null){
                        this.getBoton('btnVentana').enable();
                    }
                    if(rec.data.neto_total_mt !== null && rec.data.importe_total_mt !== null && rec.data.cant_bol_mt !== null){
                        this.getBoton('btnVentana').enable();
                    }
                    Phx.vista.ArchivoAcmDet.superclass.preparaMenu.call(this);
                    if (rec.data['estado']== 'cargado' ){
                        //Phx.vista.MovimientoEntidad.superclass.preparaMenu.call(this);
                        tb.items.get('b-edit-' + this.idContenedor).enable();
                        tb.items.get('b-new-' + this.idContenedor).enable();
                    }else{
                        tb.items.get('b-edit-' + this.idContenedor).disable();
                        tb.items.get('b-new-' + this.idContenedor).disable();
                    }
                }

            // this.getBoton('btnReporteArchivoAcm').enable();
		},

		liberaMenu : function(){
				var rec = this.sm.getSelected();
			this.getBoton('btnVentana').disable();
            // this.getBoton('btnReporteArchivoAcm').disable();
			Phx.vista.ArchivoAcmDet.superclass.liberaMenu.call(this);

		},

	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_archivo_acm_det'
			},
			type:'Field',
			form:true
		},
		{
			config: {
				name: 'id_archivo_acm',
				fieldLabel: 'Archivo ACM',
				allowBlank: false,
				emptyText: 'archivo...',
				store: new Ext.data.JsonStore({
					url: '../../sis_obingresos/control/ArchivoAcm/listarArchivoAcm',
					id: 'id_archivo_acm',
					root: 'datos',
					sortInfo: {
						field: 'id_archivo_acm',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_archivo_acm', 'nombre', 'codigo'],
					remoteSort: true,
					baseParams: {par_filtro: 'aad.id_archivo_acm#aad.nombre'}
				}),
				valueField: 'id_archivo_acm',
				displayField: 'nombre',
				gdisplayField: 'id_archivo_acm',
				hiddenName: 'id_archivo_acm',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '100%',
				gwidth: 150,
				minChars: 2,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['id_archivo_acm']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'aad.nombre',type: 'string'},
			grid: false,
			form: true
		},
        {
            config:{
                name: 'officce_id',
                fieldLabel: 'Office ID',
                allowBlank: false,
                anchor: '80%',
                gwidth: 200,
                renderer: function(value, p, record){
                    return String.format('<b style="color:teal; ">{0}</b>', record.data['officce_id']);
                },
                maxLength:50
            },
            type:'TextField',
            filters:{pfiltro:'aad.officce_id',type:'string'},
            id_grupo:1,
            grid:true,
            form:true,
            bottom_filter : true
        },
        {
            config: {
                name: 'id_agencia',
                fieldLabel: 'AGENCIA',
                allowBlank: true,
                emptyText: 'Agencia...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_obingresos/control/Agencia/listarAgencia',
                    id: 'id_agencia',
                    root: 'datos',
                    sortInfo: {
                        field: 'id_agencia',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_agencia', 'nombre', 'codigo', 'codigo_int'],
                    remoteSort: true,
                    baseParams: {par_filtro: 'age.nombre#age.codigo_int'}
                }),
                valueField: 'id_agencia',
                displayField: 'nombre',
                gdisplayField: 'agencia',
                hiddenName: 'id_agencia',
                forceSelection: true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'remote',
                pageSize: 15,
                queryDelay: 1000,
                anchor: '100%',
                gwidth: 150,
                minChars: 2,
                renderer: function(value, p, record){
                    return String.format('<b style="color:blue; ">{0}</b>', record.data['agencia']);
                },
                tpl: '<tpl for="."><div class="x-combo-list-item"><p>Agencia: <strong>{nombre}</strong></p>Officeid: <strong>{codigo_int}</strong> </div></tpl>'
            },
            type: 'ComboBox',
            id_grupo: 0,
            filters: {pfiltro: 'aad.id_agencia',type: 'string'},
            grid: true,
            form: true,
            bottom_filter : true
        },
				{
            config:{
                name: 'tipo_agencia',
                fieldLabel: 'Tipo Agencia',
                allowBlank: false,
                anchor: '80%',
                gwidth: 100,
                renderer:function (value,p,record){

                        return  String.format('<div style="color:red; text-align:center; font-weight:bold;"><b>{0}</b></div>', record.data['tipo_agencia']);

                },
                maxLength:4
            },
            type:'NumberField',
            filters:{pfiltro:'aad.porcentaje',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:false
        },
        {
            config:{
                name: 'porcentaje',
                fieldLabel: 'Porcentaje (%)',
                allowBlank: false,
                anchor: '80%',
                gwidth: 100,
                renderer:function (value,p,record){
                    if(record.data.tipo_reg != 'summary'){
                        return  String.format('<div style="color:darkred; text-align:center; font-weight:bold;"><b>{0}</b></div>', record.data['porcentaje']);
                    }
                    else{
                        return '<b><p style="font-size:15px; color:red; text-align:right; text-decoration: border-top:2px;">Total(BOB): &nbsp;&nbsp; </p></b>';
                    }
                },
                maxLength:4
            },
            type:'NumberField',
            filters:{pfiltro:'aad.porcentaje',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'neto_total_mb',
                fieldLabel: 'Total Neto(Bs.)',
                allowBlank: true,
                anchor: '80%',
                gwidth: 150,
                renderer:function (value,p,record){
                    if(record.data.tipo_reg != 'summary'){
                        return  String.format('<div style="color:green; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
                    }
                    else{
                        return  String.format('<div style="font-size:20px; text-align:center; color:black;"><b>{0}<b></div>', Ext.util.Format.number(record.data.sum_neto_b,'0,000.00'));
                    }
                },
                maxLength:1179650
            },
            type:'NumberField',
            filters:{pfiltro:'aad.neto_total_mb',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:false
        },
        {
            config:{
                name: 'importe_total_mb',
                fieldLabel: 'Importe Total (Bs.)',
                allowBlank: true,
                anchor: '80%',
                gwidth: 150,
                renderer:function (value,p,record){
                    if(record.data.tipo_reg != 'summary'){
                        return  String.format('<div style="color:green; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
                    }
                    else{
                        return  String.format('<div style="font-size:20px; text-align:center; color:black;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_importe_b,'0,000.00'));
                    }
                },
                maxLength:1179650
            },
            type:'NumberField',
            filters:{pfiltro:'aad.importe_total_mb',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'cant_bol_mb',
                fieldLabel: 'Cantidad Boletos (Bs.)',
                allowBlank: true,
                anchor: '80%',
                gwidth: 150,
                renderer:function (value,p,record){
                    if(record.data.tipo_reg != 'summary'){
                        return  String.format('<div style="color:black; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0'));
                    }
                    else{
                        return  String.format('<div style="font-size:20px; text-align:center; color:black;"><b>{0}<b></div>', Ext.util.Format.number(record.data.cantidad_boletosmb,'0'));
                    }
                },
                maxLength:1179650
            },
            type:'NumberField',
            filters:{pfiltro:'aad.cant_bol_mb',type:'numeric'},
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
                renderer:function (value,p,record){
                    if(record.data.tipo_reg != 'summary'){
                        return  String.format('<div style="color:black; text-align:right; font-weight:bold;"><b>{0}</b></div>', record.data['usr_reg']);
                    }
                    else{
                        return '<b><p style="font-size:15px; color:red; text-align:right; text-decoration: border-top:2px;">Total(USD): &nbsp;&nbsp; </p></b>';
                    }
                },
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
                name: 'neto_total_mt',
                fieldLabel: 'Total Neto ($us.)',
                allowBlank: true,
                anchor: '80%',
                gwidth: 150,
                renderer:function (value,p,record){
                    if(record.data.tipo_reg != 'summary'){
                        return  String.format('<div style="color:navy; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
                    }
                    else{
                        return  String.format('<div style="font-size:20px; text-align:center; color:black;"><b>{0}<b></div>', Ext.util.Format.number(record.data.sum_neto,'0,000.00'));
                    }
                },
                maxLength:1179650
            },
            type:'NumberField',
            filters:{pfiltro:'aad.neto_total_mt',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:false
        },
		{
			config:{
				name: 'importe_total_mt',
				fieldLabel: 'Importe Total ($us.)',
				allowBlank: true,
				anchor: '80%',
				gwidth: 150,
                renderer:function (value,p,record){
                    if(record.data.tipo_reg != 'summary'){
                        return  String.format('<div style="color:navy; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
                    }
                    else{
                        return  String.format('<div style="font-size:20px; text-align:center; color:black;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_importe,'0,000.00'));
                    }
                },
				maxLength:1179650
			},
				type:'NumberField',
				filters:{pfiltro:'aad.importe_total_mt',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
        {
            config:{
                name: 'cant_bol_mt',
                fieldLabel: 'Cantidad Boletos ($us.)',
                allowBlank: true,
                anchor: '80%',
                gwidth: 150,
                renderer:function (value,p,record){
                    if(record.data.tipo_reg != 'summary'){
                        return  String.format('<div style="color:black; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0'));
                    }
                    else{
                        return  String.format('<div style="font-size:20px; text-align:center; color:black;"><b>{0}<b></div>', Ext.util.Format.number(record.data.cantidad_boletosmt,'0'));
                    }
                },
                maxLength:1179650
            },
            type:'NumberField',
            filters:{pfiltro:'aad.cant_bol_mt',type:'numeric'},
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
				filters:{pfiltro:'aad.estado_reg',type:'string'},
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
				filters:{pfiltro:'aad.usuario_ai',type:'string'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'fecha_reg',
				fieldLabel: 'Fecha Creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 150,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'aad.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'aad.id_usuario_ai',type:'numeric'},
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
				name: 'fecha_mod',
				fieldLabel: 'Fecha Modif.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 150,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'aad.fecha_mod',type:'date'},
				id_grupo:1,
				grid:false,
				form:false
		}
	],
	tam_pag:50,
	title:'Archivo ACM Detalle',
	ActSave:'../../sis_obingresos/control/ArchivoAcmDet/insertarArchivoAcmDet',
	ActDel:'../../sis_obingresos/control/ArchivoAcmDet/eliminarArchivoAcmDet',
	ActList:'../../sis_obingresos/control/ArchivoAcmDet/listarArchivoAcmDet',
	id_store:'id_archivo_acm_det',
	fields: [
		{name:'id_archivo_acm_det', type: 'numeric'},
		{name:'id_archivo_acm', type: 'numeric'},
		{name:'importe_total_mb', type: 'numeric'},
        {name:'total_importe_b', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'porcentaje', type: 'numeric'},
		{name:'importe_total_mt', type: 'numeric'},
        {name:'total_importe', type: 'numeric'},
		{name:'id_agencia', type: 'numeric'},
		{name:'officce_id', type: 'string'},
        {name:'tipo_reg', type: 'string'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
        {name:'neto_total_mb', type: 'numeric'},
        {name:'sum_neto_b', type: 'numeric'},
		{name:'cant_bol_mb', type: 'numeric'},
        {name:'cantidad_boletosmb', type: 'numeric'},
		{name:'neto_total_mt', type: 'numeric'},
		{name:'sum_neto', type: 'numeric'},
		{name:'cant_bol_mt', type: 'numeric'},
        {name:'cantidad_boletosmt', type: 'numeric'},


		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'agencia', type: 'string'},
        {name:'tipo_agencia', type: 'string'},
        {name:'estado', type: 'string'},


	],
    /*arrayDefaultColumHidden:[
        'id_archivo_acm','estado_reg', 'usuario_ai', 'fecha_mod', 'usr_mod'],*/

	sortInfo:{
		field: 'id_archivo_acm_det',
		direction: 'ASC'
	},
	bdel:true,
	bsave:true,
    onButtonNew : function () {
	    Phx.vista.ArchivoAcmDet.superclass.onButtonNew.call(this);
        this.Cmp.id_agencia.enable();
        this.Cmp.id_archivo_acm.hide();
    },
    onButtonEdit : function () {
	    //var res = this.sm.getSelected();
        //var aux = res.data.id_archivo_acm;
        //this.store.baseParams.id_archivo_acm.reload();
        //aux = aux.toString();
        //console.log('es:',aux);
        //this.Cmp.id_archivo_acm.load();
	    this.Cmp.id_agencia.disable();
        this.Cmp.id_archivo_acm.hide();
	    Phx.vista.ArchivoAcmDet.superclass.onButtonEdit.call(this);


    },
    onReloadPage: function(m){
	    this.maestro=m;
	    this.store.baseParams={id_archivo_acm:this.maestro.id_archivo_acm};
	    this.load({params:{start:0, limit:50}});//this.bloquearMenus();
    },
    loadValoresIniciales: function(){
        this.Cmp.id_archivo_acm.setValue(this.maestro.id_archivo_acm);
	    Phx.vista.ArchivoAcmDet.superclass.loadValoresIniciales.call(this);

    },

	onButtonAcm: function(){

			//Phx.vista.ArchivoAcmDet.superclass.onButtonAcm.call(this);
	            var rec = {maestro: this.sm.getSelected().data}
	            rec.acm='especifico';
	            console.log('VALOR',	rec.acm);

	            Phx.CP.loadWindows('../../../sis_obingresos/vista/acm/Acm.php',
	                'Detalle del ACM generado',
	                {
	                    width:1200,
	                    height:600
	                },
	                rec,
	                this.idContenedor,
	                'Acm');

	        },
	}
)
</script>
