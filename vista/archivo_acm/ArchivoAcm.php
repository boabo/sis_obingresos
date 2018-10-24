<?php
/**
*@package pXP
*@file gen-ArchivoAcm.php
*@author  (admin)
*@date 05-09-2018 20:09:45
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.ArchivoAcm=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config) {
        this.maestro = config.maestro;
        //llama al constructor de la clase padre
        Phx.vista.ArchivoAcm.superclass.constructor.call(this, config);


        this.init();
        //this.addBotones();

        this.addButton('btnsubir_archivo',
            {
            //grupo: [0],
            text: 'Cargar Archivo',
            iconCls: 'blist',
            disabled: true,
            handler: this.onButtonUpload,
            tooltip: '<b>Cargar Archivo</b><br/>Carga un Archivo del tipo Excel.'
            }
        );

        this.addButton('btnborrar_archivo', {
            //grupo: [0],
            text: 'Borrar Archivo',
            iconCls: 'bundo',
            disabled: true,
            handler:this.borrarDetalle,
            tooltip: '<b>Borrar Archivo</b><br/>Revierte archivos ACM Cargados'
        });

        this.addButton('btngenerar_acm', {
            //grupo: [0],
            text: 'Generar ACM',
            iconCls: 'bdocuments',
            disabled: true,
            handler:this.onButtonGenerar,
            tooltip: '<b>Generar ACM</b><br/>Genera OVER COMISION del archivo ACM.'
        });

        this.addButton('btnrevertir_acm', {
            //grupo: [0],
            text: 'Revertir',
            iconCls: 'breload2',
            disabled: true,
            handler:this.onButtonRevertir,
            tooltip: '<b>Revertir</b><br/>Revierte archivos ACM Generados'
        });

        this.addButton('btnvalidar_acm', {
            //grupo: [0],
            text: 'Validar',
            iconCls: 'bok',
            disabled: true,
            handler:this.alerta,
            tooltip: '<b>Validar</b><br/>Valida y Registra archivos ACM Generados en Entidades'
        });

        this.addButton('btnhabilitar', {
            //grupo: [0],
            text: 'Habilitar',
            iconCls: 'bball_green',
            disabled: true,
            handler:this.alertaHabilitar,
            tooltip: '<b>Habilitar</b><br/>Sirve como habilitacion para revertir lo validado y retornar a estado generado'
        });

        this.addButton('btnrevertir_validar', {
            //grupo: [0],
            text: 'Revertir Validacion',
            iconCls: 'batras',
            disabled: true,
            handler:this.alertaRevertirVal,
            tooltip: '<b>Revertir Validacion</b><br/>Revierte el Registro realizado en entidades y retorna al estado generado'
        });

        this.addButton('btnReporteArchivoAcm',
            {
                text: 'Reporte',
                iconCls: 'bexcel',
                disabled: true,
                handler: this.onButtonReporte,
                tooltip: '<b>Generar Reporte</b><br/>Generar Reporte del Detalle de archivos ACM.'
            }
        );


        this.load({params: {start: 0, limit: this.tam_pag}})


        //this.iniciarEventos();
        //this.cmbGestion.on('select',this.capturarEventos,this);
	},

    iniciarEventos: function(){
	    /*this.Cmb.id_archivo_acm.on('focus', function(){

        });*/
    },
    preparaMenu: function () {
        var rec = this.sm.getSelected();
        var tb = this.tbar;
        //this.getBoton('btnBoleto').enable();
        if(rec !== '') {
            if(rec.data.estado == 'borrador'){
                this.getBoton('btnsubir_archivo').enable();
                this.getBoton('btngenerar_acm').disable();
                this.getBoton('btnborrar_archivo').disable();
                this.getBoton('btnrevertir_acm').disable();
                this.getBoton('btnvalidar_acm').disable();
                this.getBoton('btnrevertir_validar').disable();
                this.getBoton('btnReporteArchivoAcm').disable();
                this.getBoton('btnhabilitar').disable();

                //tb.items.get('b-edit-' + this.idContenedor).enable();
                //Phx.vista.ArchivoAcm.superclass.preparaMenu.call(this);
            }
            if(rec.data.estado == 'cargado'){
                this.getBoton('btngenerar_acm').enable();
                this.getBoton('btnborrar_archivo').enable();
                this.getBoton('btnsubir_archivo').disable();
                this.getBoton('btnrevertir_acm').disable();
                this.getBoton('btnvalidar_acm').disable();
                this.getBoton('btnrevertir_validar').disable();
                this.getBoton('btnReporteArchivoAcm').disable();
                this.getBoton('btnhabilitar').disable();

                //this.getBoton('btnborrar_archivo').disable();
            }
            if(rec.data.estado == 'generado'){
                this.getBoton('btnrevertir_acm').enable();
                this.getBoton('btnvalidar_acm').enable();
                this.getBoton('btnsubir_archivo').disable();
                this.getBoton('btngenerar_acm').disable();
                this.getBoton('btnborrar_archivo').disable();
                this.getBoton('btnrevertir_validar').disable();
                this.getBoton('btnReporteArchivoAcm').enable();
                this.getBoton('btnhabilitar').disable();
                //this.getBoton('btnborrar_archivo').disable();
            }
            if(rec.data.estado == 'validado'){
                this.getBoton('btnrevertir_acm').disable();
                this.getBoton('btnvalidar_acm').disable();
                this.getBoton('btnsubir_archivo').disable();
                this.getBoton('btngenerar_acm').disable();
                this.getBoton('btnborrar_archivo').disable();
                this.getBoton('btnrevertir_validar').enable();
                this.getBoton('btnReporteArchivoAcm').enable();
                this.getBoton('btnhabilitar').disable();

                //this.getBoton('btnborrar_archivo').disable();
            }
            if(rec.data.estado == 'finalizado'){
                this.getBoton('btnrevertir_acm').disable();
                this.getBoton('btnvalidar_acm').disable();
                this.getBoton('btnsubir_archivo').disable();
                this.getBoton('btngenerar_acm').disable();
                this.getBoton('btnborrar_archivo').disable();
                this.getBoton('btnrevertir_validar').disable();
                this.getBoton('btnReporteArchivoAcm').enable();
                this.getBoton('btnhabilitar').enable();

                //this.getBoton('btnborrar_archivo').disable();
            }
            /*this.getBoton('btnrevertir_acm').enable();
                this.getBoton('btnvalidar_acm').enable();
                this.getBoton('btnsubir_archivo').enable();
                this.getBoton('btngenerar_acm').enable();
                this.getBoton('btnborrar_archivo').enable();*/
            Phx.vista.ArchivoAcm.superclass.preparaMenu.call(this);
            if (this.sm.getSelected().data['estado']== 'borrador'){
                //Phx.vista.MovimientoEntidad.superclass.preparaMenu.call(this);
                tb.items.get('b-edit-' + this.idContenedor).enable();
                tb.items.get('b-del-' + this.idContenedor).enable();
            }else{
                tb.items.get('b-edit-' + this.idContenedor).disable();
                tb.items.get('b-del-' + this.idContenedor).disable();
            }
        }
    },
    liberaMenu : function(){
        var d = this.sm.getSelected.data;
        /*console.log('codigo:',d.user_reg);
        if (this.sm.getSelected().data['usr_reg']== 'admin'){

        }*/
        //this.getBoton('btnBoleto').enable();
            /*this.getBoton('btnsubir_archivo').disable();
            this.getBoton('btngenerar_acm').disable();
            this.getBoton('btnborrar_archivo').disable();
            this.getBoton('btnvalidar_acm').disable();
            this.getBoton('btnrevertir_acm').disable();*/
            Phx.vista.ArchivoAcm.superclass.liberaMenu.call(this);
    },

    capturarEventos: function (){
	    this.store.baseParams.id_archivo_acm=this.cmbGestion.getValue();
	    this.load({params:{start:0, limit:this.tam_pag}});
    },

    onButtonUpload: function () {
        var rec=this.sm.getSelected();
        Phx.CP.loadWindows('../../../sis_obingresos/vista/archivo_acm/AcmExcel.php',
            'Subir Archivo ACM Excel',
            {
                modal:true,
                width:450,
                height:200
            },rec.data,this.idContenedor,'ConsumoACM')
    },
    borrarDetalle: function(){
        Phx.CP.loadingShow();
        var d = this.sm.getSelected().data;
        Ext.Ajax.request({
                url:'../../sis_obingresos/control/ArchivoAcm/eliminarArchivoACMExcel',
                params:{id_archivo_acm:d.id_archivo_acm},
                success:this.successAnularDetAcm,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
        });
    },
    successAnularDetAcm:function(resp){
        Phx.CP.loadingHide();
        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
        if(!reg.ROOT.error){
            this.reload();
        }
    },
		onButtonGenerar: function() {
			Phx.CP.loadingShow();
			var d = this.sm.getSelected().data;
			console.log('codigo:',d.id_archivo_acm);
			d.estado = 'cargado';
			Ext.Ajax.request({
							url:'../../sis_obingresos/control/Acm/generarACM',
							params:{id_archivo_acm:d.id_archivo_acm},
							success:this.successAnularDetAcm,
							failure: this.conexionFailure,
							timeout:this.timeout,
							scope:this
			});
		},
		onButtonRevertir: function() {
			Phx.CP.loadingShow();
			var d = this.sm.getSelected().data;
			console.log('codigo:',d.id_archivo_acm);
			d.estado = 'cargado';
			Ext.Ajax.request({
							url:'../../sis_obingresos/control/Acm/eliminarAcmGenerado',
							params:{id_archivo_acm:d.id_archivo_acm},
							success:this.successAnularDetAcm,
							failure: this.conexionFailure,
							timeout:this.timeout,
							scope:this
			});
		},

    alerta: function(){
        var mensaje;
        var global = this;
        Ext.Msg.confirm('Confirmacion', 'Esta Seguro que desea <b>Validar ACMs</b> Generados?', function (btn) {
            if (btn == 'yes') {
                global.onButtonvalidar();
            }
            else {
            }
        });
    },
    onButtonvalidar: function(){
        Phx.CP.loadingShow();
        var d = this.sm.getSelected().data;
        console.log('codigo:',d.id_archivo_acm);
        Ext.Ajax.request({
            url:'../../sis_obingresos/control/Acm/validarAcm',
            params:{id_archivo_acm:d.id_archivo_acm},
            success:this.successAnularDetAcm,
            failure: this.conexionFailure,
            timeout:this.timeout,
            scope:this
            });
    },
    alertaRevertirVal: function(){
        var mensaje;
        var global = this;
        Ext.Msg.confirm('Confirmacion', 'Esta Seguro que desea <b>Revertir Validacion?</b>', function (btn) {
            if (btn == 'yes') {
                global.onButtonRevertirVal();
            }
            else {
            }
        });
    },
    onButtonRevertirVal: function(){
        Phx.CP.loadingShow();
        var d = this.sm.getSelected().data;
        console.log('codigo:',d.id_archivo_acm);
        Ext.Ajax.request({
            url:'../../sis_obingresos/control/Acm/eliminarAcmValidado',
            params:{id_archivo_acm:d.id_archivo_acm},
            success:this.successAnularDetAcm,
            failure: this.conexionFailure,
            timeout:this.timeout,
            scope:this
        });
    },
    onButtonReporte: function() {
        Phx.CP.loadingShow();
        var d = this.sm.getSelected().data;
        Ext.Ajax.request({
            url:'../../sis_obingresos/control/ArchivoAcm/reporteArchivoACM',
            params:{id_archivo_acm:d.id_archivo_acm,
                    fecha_ini: d.fecha_ini.dateFormat('d/m/Y'),
                    fecha_fin: d.fecha_fin.dateFormat('d/m/Y')},
            success:this.successExport,
            failure: this.conexionFailure,
            timeout:this.timeout,
            scope:this
        });
        console.log('EL DATO ES:',d.id_archivo_acm);
    },
    alertaHabilitar: function(){
        var mensaje;
        var global = this;
        Ext.Msg.confirm('Confirmacion', 'Esta Seguro que desea <b>Habilitar Revertir Validación?</b>', function (btn) {
            if (btn == 'yes') {
                global.onButtonHabilitar();
            }
            else {
            }
        });
    },
    onButtonHabilitar: function() {
        Phx.CP.loadingShow();
        var d = this.sm.getSelected().data;
        Ext.Ajax.request({
            url:'../../sis_obingresos/control/ArchivoAcm/habilitarValidacion',
            params:{id_archivo_acm:d.id_archivo_acm},
            success:this.successAnularDetAcm,
            failure: this.conexionFailure,
            timeout:this.timeout,
            scope:this
        });
        console.log('EL DATO ES:',d.id_archivo_acm);
    },

	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_archivo_acm'
			},
			type:'Field',
			form:true
		},
		{
			config:{
				name: 'estado_reg',
				fieldLabel: 'Estado',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'taa.estado_reg',type:'string'},
				id_grupo:1,
				grid:false,
				form:false
		},
        {
            config:{
                name: 'fecha_ini',
                fieldLabel: 'Fecha Inicio',
                allowBlank: false,
                anchor: '80%',
                gwidth: 100,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
            type:'DateField',
            filters:{pfiltro:'taa.fecha_ini',type:'date'},
            id_grupo:1,
            grid:true,
            form:true,
            bottom_filter : true
        },
		{
			config:{
				name: 'fecha_fin',
				fieldLabel: 'Fecha Fin',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'taa.fecha_fin',type:'date'},
				id_grupo:1,
				grid:true,
				form:true,
                bottom_filter : true
		},
		{
			config:{
				name: 'nombre',
				fieldLabel: 'Nombre',
				allowBlank: false,
				anchor: '80%',
				gwidth: 300,
				maxLength:500
			},
				type:'TextField',
				filters:{pfiltro:'taa.nombre',type:'string'},
				id_grupo:1,
				grid:true,
				form:true,
                bottom_filter : true
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
				filters:{pfiltro:'taa.usuario_ai',type:'string'},
				id_grupo:1,
				grid:false,
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
				filters:{pfiltro:'taa.fecha_reg',type:'date'},
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
                name: 'estado',
                fieldLabel: 'Proceso de ACM',
                allowBlank: true,
                anchor: '80%',
                gwidth: 150,
                maxLength:3,
                renderer: function (value, p, record) {
                    if (record.data['estado'] == 'borrador') {
                        return String.format('<div title="borrador"><b><font color="orange">{0}</font></b></div>', value);

                    } else if (record.data['estado'] == 'cargado') {
                        return String.format('<div title="cargado"><b><font color="blue">{0}</font></b></div>', value);
                    } else if (record.data['estado'] == 'generado') {
                        return String.format('<div title="generado"><b><font color="purple">{0}</font></b></div>', value);
                    } else if (record.data['estado'] == 'validado'){
                        return String.format('<div title="validado"><b><font color="green">{0}</font></b></div>', value);
                    }else if (record.data['estado'] == 'finalizado'){
                        return String.format('<div title="validado"><b><font color="red">{0}</font></b></div>', value);
                    }
                }
            },
            type: 'TextField',
            filters: { pfiltro:'taa.estado',type:'string'},
            id_grupo: 1,
            grid: true,
            form: false
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
				filters:{pfiltro:'taa.id_usuario_ai',type:'numeric'},
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
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'taa.fecha_mod',type:'date'},
				id_grupo:1,
				grid:false,
				form:false
		}
	],
	tam_pag:50,
	title:'Archivo ACM',
	ActSave:'../../sis_obingresos/control/ArchivoAcm/insertarArchivoAcm',
	ActDel:'../../sis_obingresos/control/ArchivoAcm/eliminarArchivoAcm',
	ActList:'../../sis_obingresos/control/ArchivoAcm/listarArchivoAcm',
	id_store:'id_archivo_acm',
	fields: [
		{name:'id_archivo_acm', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'fecha_fin', type: 'date',dateFormat:'Y-m-d'},
		{name:'nombre', type: 'string'},
		{name:'fecha_ini', type: 'date',dateFormat:'Y-m-d'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
        {name:'estado', type: 'string'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
	//	{name:'ultimo_numero', type: 'numeric'},

	],
    /*arrayDefaultColumHidden:[
        'estado_reg'],*/
	sortInfo:{
		field: 'id_archivo_acm',
		direction: 'DESC'
	},


	bdel:true,
	bsave:false,
    btest:false,
    bnew: true,

    tabsouth:[
        {
            url:'../../../sis_obingresos/vista/archivo_acm_det/ArchivoAcmDet.php',
            title:'Detalle Archivos ACM',
            //width:'40%',
            height: '50%',
            cls:'ArchivoAcmDet'
        }
    ]

})
</script>
