<script>
/**
*@package pXP
*@file ReporteMcos.php
*@author  (breydi vasquez)
*@date 15-04-2020
*@description Archivo con filtros para sacar reporte de tramites aprobados por funcionario
*/
Phx.vista.ReporteMcos = Ext.extend(Phx.frmInterfaz, {

        constructor : function(config) {
			Phx.vista.ReporteMcos.superclass.constructor.call(this, config);
			this.init();
            var fecha = new Date();
            Ext.Ajax.request({
                url:'../../sis_parametros/control/Gestion/obtenerGestionByFecha',
                params:{fecha:fecha.getDate()+'/'+(fecha.getMonth()+1)+'/'+fecha.getFullYear()},
                success:function(resp){
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    this.Cmp.id_gestion.setValue(reg.ROOT.datos.id_gestion);
                    this.Cmp.id_gestion.setRawValue(reg.ROOT.datos.anho);
                    this.Cmp.fecha_ini.setValue('01/01/'+reg.ROOT.datos.anho);
                    this.Cmp.fecha_fin.setValue('31/12/'+reg.ROOT.datos.anho);
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });

			this.iniciarEventos();
		},

		Atributos : [

		{
            config:{
                name:'id_gestion',
                fieldLabel:'Gestión',
                allowBlank: false,
                emptyText:'Gestión...',
                store: new Ext.data.JsonStore({
                         url: '../../sis_parametros/control/Gestion/listarGestion',
                         id: 'id_gestion',
                         root: 'datos',
                         sortInfo:{
                            field: 'gestion',
                            direction: 'DESC'
                    },
                    totalProperty: 'total',
                    fields: ['id_gestion','gestion','moneda','codigo_moneda'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams:{par_filtro:'gestion'}
                    }),
                valueField: 'id_gestion',
                displayField: 'gestion',
                hiddenName: 'id_gestion',
                forceSelection:true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender:true,
                mode:'remote',
                pageSize:5,
                queryDelay:1000,
                listWidth:200,
                resizable:true,
				width:150

            },
            type:'ComboBox',
            id_grupo:0,
            filters:{
                        pfiltro:'gestion',
                        type:'string'
                    },
            grid:true,
            form:true
        },
        {
            config: {
                name: 'filtro_dia',
                fieldLabel: 'Dia',
                gwidth: 50,

            },
            type: 'Checkbox',
            id_grupo: 0,
            form: true
        }, {
            config: {
                name: 'filtro_mes',
                fieldLabel: 'Mes',
                gwidth: 50,
                checked:'checked'

            },
            type: 'Checkbox',
            id_grupo: 0,
            form: true
        },
        {
            config:{
                    name: 'fecha_ini',
                    fieldLabel: 'Desde',
                    allowBlank: true,
                    format: 'd/m/Y',
                    width: 150,
                },
                type: 'DateField',
                id_grupo: 0,
                form: true
		},
		{
            config:{
                    name: 'fecha_fin',
                    fieldLabel: 'Hasta',
                    allowBlank: true,
                    format: 'd/m/Y',
                    width: 150
                },
                type: 'DateField',
                id_grupo: 0,
                form: true
        },
        {
        config: {
            name: 'id_sucursal',
            fieldLabel: 'Sucursal',
            allowBlank: true,
            emptyText: 'Elija una Suc...',
            store: new Ext.data.JsonStore({
                url: '../../sis_ventas_facturacion/control/Sucursal/listarSucursal',
                id: 'id_sucursal',
                root: 'datos',
                sortInfo: {
                    field: 'nombre',
                    direction: 'ASC'
                },
                totalProperty: 'total',
                fields: ['id_sucursal', 'nombre', 'codigo'],
                remoteSort: true,
                baseParams: {tipo_usuario : 'todos',par_filtro: 'suc.nombre#suc.codigo'}
            }),
            valueField: 'id_sucursal',
            gdisplayField : 'nombre_sucursal',
            displayField: 'nombre',
            hiddenName: 'id_sucursal',
            tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
            forceSelection: true,
            typeAhead: false,
            triggerAction: 'all',
            lazyRender: true,
            mode: 'remote',
            pageSize: 15,
            width:250,
            queryDelay: 1000,
            minChars: 2,
            resizable:true,
            hidden : true
        },
        type: 'ComboBox',
        id_grupo: 0,
        form: true
    },
        {
            config: {
              name: 'id_punto_venta',
              fieldLabel: 'Punto Venta',
              allowBlank: false,
              emptyText:'Punto de Venta...',
              blankText: 'Año',
              store: new Ext.data.JsonStore({
                  url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
                  id: 'id_punto_venta',
                  root: 'datos',
                  sortInfo: {
                      field: 'nombre',
                      direction: 'ASC'
                  },
                  totalProperty: 'total',
                  fields: ['id_punto_venta', 'id_sucursal','nombre', 'codigo','habilitar_comisiones','formato_comprobante'],
                  remoteSort: true,
                  baseParams: {par_filtro: 'puve.nombre#puve.codigo', tipo_factura: this.tipo_factura}
              }),
              tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
              valueField: 'id_punto_venta',
              triggerAction: 'all',
              displayField: 'nombre',
              hiddenName: 'id_punto_venta',
              mode:'remote',
              pageSize:50,
              queryDelay:500,
              listWidth:'300',
              hidden:false,
              width:300
                },
            type: 'ComboBox',
            id_grupo:0,
            grid: true,
            form: true
        },
        /*{
                config : {
                    name : 'id_mco',
                    fieldLabel: 'TKT-MCO',
                    allowBlank: false,
                    emptyText: 'Tkt-mco...',
                    store: new Ext.data.JsonStore(
                        {
                            url: '../../sis_obingresos/control/McoS/listarTktFiltro',
                            id: 'id_mco',
                            root: 'datos',
                            sortInfo: {
                                field: 'nro_mco',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_mco','nro_mco', 'total', 'moneda'],
                            baseParams: {_adicionar:'si'},
                            remoteSort: true
                        }),
                    valueField: 'id_mco',
                    hiddenValue: 'nro_mco',
                    displayField: 'nro_mco',
                    gdisplayField: 'nro_mco',
                    queryParam: 'nro_mco',
                    tpl:'<tpl for="."><div class="x-combo-list-item"><b>MCO: <span style="color:#274d80;">{nro_mco}</span></b></div></tpl>',
                    autoSelect: true,
                    typeAhead: false,
                    typeAheadDelay: 75,
                    hideTrigger: true,
                    triggerAction: 'query',
                    lazyRender: false,
                    mode: 'remote',
                    pageSize: 20,
                    queryDelay: 500,
                    width:300,
                    minChars: 4,
                    listWidth: '240',
                    value:'930'

                },
                type : 'ComboBox',
                id_grupo : 1,
                form : true,
                grid : false
            },*/
            {
                config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'punto_venta_nombre'
                },
                type:'Field',
                form:true
            },

            {
                config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'codigo_punto_venta'
                },
                type:'Field',
                form:true
            },
		],


    topBar : true,
    botones : false,
    tipo : 'reporte',
    clsSubmit : 'bprint',
    labelSubmit : 'Generar',
    title : 'Reporte Mcos',
    tooltipSubmit : '<b>Reporte Mcos</b>',
    ActSave : '../../sis_obingresos/control/McoS/reporteMcos',

		Grupos : [{
			layout : 'column',
            bodyStyle: 'padding: 5px;',
			items : [{
				xtype : 'fieldset',
				layout : 'form',
				border : true,
				title : 'Datos para el reporte',
				bodyStyle : 'padding:0 10px 0;',
				columnWidth : '500px',
				items : [],
				id_grupo : 0,
				collapsible : true
			}]
		}],

		iniciarEventos:function(){

            // captura gestion evento de seleccion
            this.Cmp.id_gestion.on('select', function(o, r) {
                this.Cmp.fecha_ini.setValue('01/01/'+r.data.gestion);
                this.Cmp.fecha_fin.setValue('31/12/'+r.data.gestion);
            }, this);

            this.Cmp.id_punto_venta.on('select', function(o, r) {
                this.Cmp.codigo_punto_venta.setRawValue(r.data.codigo);
                this.Cmp.punto_venta_nombre.setRawValue(r.data.nombre);
                // this.Cmp.id_mco.reset();
                // this.Cmp.id_mco.modificado = true;
                // this.Cmp.id_mco.store.baseParams.id_punto_venta=r.data.id_punto_venta;
            }, this);

            this.Cmp.id_sucursal.on('select', function(o, r) {
              this.Cmp.id_punto_venta.reset();
              this.Cmp.id_punto_venta.modificado = true;
              this.Cmp.id_punto_venta.store.baseParams.id_sucursal=r.data.id_sucursal;
            }, this);

            this.Cmp.filtro_dia.on('check', function(o, c){
              if (c){
                this.Cmp.fecha_fin.reset();
                this.Cmp.fecha_fin.modificado = true;
                this.Cmp.fecha_fin.allowBlank = true;
                this.Cmp.fecha_fin.setVisible(false);
                this.Cmp.filtro_mes.reset();
                this.Cmp.filtro_mes.modificado = true;
                this.Cmp.filtro_mes.setValue(false);
              }
            },this);

            this.Cmp.filtro_mes.on('check', function(o, c){
              if (c){
                this.Cmp.fecha_fin.reset();
                this.Cmp.fecha_fin.modificado = true;
                this.Cmp.fecha_fin.setVisible(true);
                this.Cmp.fecha_fin.allowBlank = false;
                this.Cmp.filtro_dia.reset();
                this.Cmp.filtro_dia.modificado = true;
                this.Cmp.filtro_dia.setValue(false);
              }
            },this);
		}

})
</script>
