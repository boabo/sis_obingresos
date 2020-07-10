<?php
/**
*@package pXP
*@file ConsultMcosGrilla.php
*@author  (breydi.vasquez)
*@date 28-04-2020 15:25:04
*@description Archivo con la interfaz de usuario que permite la consulta
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.ConsultMcosGrilla=Ext.extend(Phx.gridInterfaz,{
	constructor:function(config){
		this.maestro=config.maestro;
    this.initButtons = ['-', '-',' <span style="font-size:12px;font-weight:bold;">Seleccione un punto de venta &nbsp;&nbsp;&nbsp;&nbsp;</span>',this.cmbPuntoV,'-','-'];
    Phx.vista.ConsultMcosGrilla.superclass.constructor.call(this,config);
    this.cmbPuntoV.on('select',this.capturaFiltros,this);
    this.init();
	},
  capturaFiltros:function(combo, record, index){
    this.store.baseParams.id_punto_venta=this.cmbPuntoV.getValue();    
    this.store.load({params:{start:0, limit:this.tam_pag}});
  },
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_mco'
			},
			type:'Field',
			form:true
		},
        {
            config:{
                labelSeparator:'',
                inputType:'hidden',
                name: 'id_punto_venta'
            },
            type:'Field',
            form:true
        },
        {
            config:{
                labelSeparator:'',
                inputType:'hidden',
                name: 'concepto_codigo'
            },
            type:'Field',
            form:true
        },

        {
            config:{
                name: 'id_concepto_ingas',
                fieldLabel: 'Concepto',
                allowBlank: true,
                emptyText : 'Concepto...',
                store : new Ext.data.JsonStore({
                            url:'../../sis_parametros/control/ConceptoIngas/listarConceptoIngas',
                            id : 'id_concepto_ingas',
                            root: 'datos',
                            sortInfo:{
                                    field: 'desc_ingas',
                                    direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_concepto_ingas','tipo','desc_ingas','movimiento','desc_partida','id_grupo_ots','filtro_ot','requiere_ot', 'codigo'],
                            remoteSort: true,
                            baseParams:{par_filtro:'desc_ingas',codigo:'MCO'}
                }),
               valueField: 'id_concepto_ingas',
               displayField: 'codigo',
               gdisplayField: 'codigo',
               hiddenName: 'id_concepto_ingas',
               forceSelection:true,
               typeAhead: false,
               triggerAction: 'all',
               tpl:'<tpl for="."><div class="x-combo-list-item"><b>Codigo: </b><span style="color:green;font-weight:bold;">{codigo}</span><br><b>Descripcion: </b><span style="color:green;font-weight:bold;">{desc_ingas}</span></div></tpl>',
               listWidth:350,
               resizable:true,
               lazyRender:true,
               mode:'remote',
               pageSize:10,
               queryDelay:1000,
               anchor:'110%',
               gwidth:100,
               minChars:1
            },
            type:'ComboBox',
            id_grupo:1,
            form:false,
            grid:true
        },

		{
			config:{
				name: 'estado',
				fieldLabel: 'Estado',
				allowBlank: false,
				anchor: '80%',
				gwidth: 90,
                renderer: function(val) {
                    if(val == 1){
                        return String.format('<span style="color:green;font-weight:bold;">{0}</span>', 'VÁLIDO');
                    }else {
                        return String.format('<span style="color:red;font-weight:bold;">{0}</span>', 'ANULADO');
                    }
                }
			},
				type:'NumberField',
				id_grupo: 1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_emision',
				fieldLabel: 'Fecha Emisión',
				allowBlank: false,
				anchor: '90%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'imcos.fecha_emision',type:'date'},
				id_grupo: 1,
				grid:true,
				form:false
		},
        {
            config: {
                name: 'id_moneda',
                origen: 'MONEDA',
                allowBlank: false,
                fieldLabel: 'Monenda',
                gdisplayField: 'desc_moneda', //mapea al store del grid
                gwidth: 80,
                anchor:'100%',
                renderer: function (value, p, record) {
                    return String.format('{0}', record.data['desc_moneda']);
                }
            },
            type: 'ComboRec',
            id_grupo: 1,
            grid: true,
            form: false
        },
		{
			config:{
				name: 'tipo_cambio',
				fieldLabel: 'T-C',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
			},
				type:'TextField',
				id_grupo: 1,
				grid:false,
				form:false
		},

		{
			config:{
				name: 'mco',
				fieldLabel: 'Numero',
				allowBlank: true,
				anchor: '100%',
				gwidth: 100,
			},
				type:'TextField',
				id_grupo: 1,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'nro_mco',
				fieldLabel: 'Numero MCO',
				allowBlank: true,
				anchor: '140%',
				gwidth: 100,
        maxLength:14
			},
				type:'TextField',
				id_grupo: 1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'pax',
				fieldLabel: 'PAX',
				allowBlank: false,
				anchor: '110%',
				gwidth: 100,
			},
				type:'TextField',
				id_grupo: 1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'motivo',
				fieldLabel: 'Motivo',
				allowBlank: false,
				anchor: '100%',
				gwidth: 100,
        maxLength:200
			},
				type:'TextArea',
				filters:{pfiltro:'imcos.motivo',type:'string'},
                bottom_filter: true,
				id_grupo: 1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'valor_total',
				fieldLabel: 'Valor-Total',
				allowBlank: true,
				anchor: '100%',
				gwidth: 100
			},
				type:'NumberField',
				filters:{pfiltro:'imcos.valor_total',type:'numeric'},
				id_grupo: 1,
				grid:true,
				form:false
		},

    {
        config: {
            name: 'id_funcionario_emisor',
            fieldLabel: 'Emitido por',
            allowBlank: false,
            emptyText: 'Elija una opción...',
            store: new Ext.data.JsonStore({
                url: '../../sis_organigrama/control/Funcionario/listarFuncionarioCargo',
                id: 'id_funcionario',
                root: 'datos',
                sortInfo: {
                    field: 'desc_funcionario1',
                    direction: 'ASC'
                },
                totalProperty: 'total',
                fields: ['id_funcionario','desc_funcionario1','email_empresa','nombre_cargo','lugar_nombre','oficina_nombre'],
                remoteSort: true,
                baseParams: {par_filtro: 'FUNCAR.desc_funcionario1'}//#FUNCAR.nombre_cargo
            }),
            valueField: 'id_funcionario',
            displayField: 'desc_funcionario1',
            gdisplayField: 'desc_funcionario1',//corregit materiaesl
            tpl:'<tpl for="."><div class="x-combo-list-item" style="color: black"><p><b>{desc_funcionario1}</b></p><p style="color: #80251e">{nombre_cargo}<br>{email_empresa}</p><p style="color:green">{oficina_nombre} - {lugar_nombre}</p></div></tpl>',
            hiddenName: 'id_funcionario',
            forceSelection: true,
            typeAhead: false,
            triggerAction: 'all',
            lazyRender: true,
            mode: 'remote',
            pageSize: 20,
            queryDelay: 1000,
            anchor: '100%',
            gwidth: 100,
            minChars: 2,
            resizable:true,
            listWidth:'280',
            renderer: function (value, p, record) {
                return String.format('{0}', record.data['desc_funcionario1']);
            }
        },
        type: 'ComboBox',
        bottom_filter:true,
        id_grupo:1,
        grid: true,
        form: false
    },
		{
            config:{
                name:'id_gestion',
                fieldLabel:'Gestión',
                allowBlank:true,
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
                gdisplayField: 'gestion',
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
            id_grupo: 1,
            filters:{
                        pfiltro:'gestion',
                        type:'string'
                    },
            grid:true,
            form:false
        },
		{
			config:{
				name: 'usr_reg',
				fieldLabel: 'Usr/Cajero',
				allowBlank: true,
				anchor: '100%',
				gwidth: 100
			},
				type:'Field',
				filters:{pfiltro:'usu1.cuenta',type:'string'},
				id_grupo: 1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_reg',
				fieldLabel: 'Fecha-Reg',
				allowBlank: true,
				anchor: '100%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'imcos.fecha_reg',type:'date'},
				id_grupo: 1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'hora_reg',
				fieldLabel: 'Hora-Reg',
				allowBlank: true,
				anchor: '100%',
				gwidth: 100,
				format: 'H:i:s'
			},
				type:'Field',
				id_grupo: 1,
				grid:false
		},
        ///Documentos originales
        {
            config: {
                name: 'id_boleto',
                fieldLabel: 'TKT-MCO',
                allowBlank: false,
                emptyText: 'Tkt-mco...',
                store: new Ext.data.JsonStore(
                    {
                        url: '../../sis_obingresos/control/McoS/listarTkts',
                        id: 'id_boleto',
                        root: 'datos',
                        sortInfo: {
                            field: 'tkt',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_boleto','tkt', 'tkt_estac', 'tkt_pais','fecha_emision', 'total', 'moneda','val_conv', 'tipo_cambio'],
                        remoteSort: true
                    }),
                valueField: 'id_boleto',
                hiddenValue: 'tkt',
                displayField: 'tkt',
                gdisplayField: 'tkt',
                queryParam: 'tkt',
                tpl:'<tpl for="."><div class="x-combo-list-item"><b>{tkt}</b><p>Valor total: {total}</p><p>Moneda: {moneda}</p></div></tpl>',
                listWidth: '180',
                forceSelection: false,
                autoSelect: true,
                typeAhead: false,
                typeAheadDelay: 75,
                hideTrigger: true,
                triggerAction: 'query',
                lazyRender: false,
                mode: 'remote',
                pageSize: 20,
                queryDelay: 500,
                anchor: '0%',
                minChars: 4,
                listWidth: '250'
            },
            type: 'ComboBox',
            id_grupo: 1,
            form: true,
            grid: true
        },
		{
			config:{
				name: 'pais_doc_or',
				fieldLabel: 'Pais',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'estacion_doc_or',
				fieldLabel: 'Estac',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_doc_or',
				fieldLabel: 'Fecha',
				allowBlank: true,
				anchor: '21%',
				gwidth: 100,
                format: 'd/m/Y'
			},
				type:'DateField',
				id_grupo: 1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 't_c_doc_or',
				fieldLabel: 'T/C',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'moneda_doc_or',
				fieldLabel: 'Moneda',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'val_total_doc_or',
				fieldLabel: 'Valor-Total',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 1,
				grid:true,
				form:false
		},
        {
			config:{
				name: 'val_conv_doc_or',
				fieldLabel: 'Valor-Conv',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 1,
				grid:true,
				form:false
		},
        ///fin
        ///Cabecera
		{
			config:{
				name: 'pais_head',
				fieldLabel: 'Pais',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'estacion_head',
				fieldLabel: 'Est',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 25,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'agt_tv_head',
				fieldLabel: 'Agt/PV',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'city_head',
				fieldLabel: '',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'suc_head',
				fieldLabel: 'Suc',
				allowBlank: true,
				anchor: '0%',
				gwidth: 100,
			},
				type:'Field',
				id_grupo: 1,
				grid:true,
				form:false
		},
        //fin
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
				filters:{pfiltro:'imcos.estado_reg',type:'string'},
				id_grupo: 1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'usuario ai',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100
			},
				type:'Field',
				filters:{pfiltro:'imcos.id_usuario_ai',type:'numeric'},
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
				gwidth: 100
			},
				type:'TextField',
				filters:{pfiltro:'imcos.usuario_ai',type:'string'},
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
				gwidth: 100
			},
				type:'Field',
				filters:{pfiltro:'usu2.cuenta',type:'string'},
				id_grupo: 1,
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
				filters:{pfiltro:'imcos.fecha_mod',type:'date'},
				id_grupo: 1,
				grid:false,
				form:false
		}
	],
	tam_pag:50,
	title:'MCOs',
	ActList:'../../sis_obingresos/control/McoS/listarMcoS',
	id_store:'id_mco',
	fields: [
		{name:'id_mco', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'estado', type: 'string'},
		{name:'fecha_emision', type: 'date',dateFormat:'Y-m-d'},
		{name:'id_moneda', type: 'numeric'},
		{name:'motivo', type: 'string'},
		{name:'valor_total', type: 'numeric'},
		{name:'id_documento_original', type: 'numeric'},
		{name:'id_gestion', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'id_boleto', type: 'numeric'},
		{name:'codigo', type: 'string'},
		{name:'desc_ingas', type: 'string'},
		{name:'codigo_internacional', type: 'string'},
		{name:'gestion', type: 'numeric'},
		{name:'tkt', type: 'string'},
		{name:'fecha_doc_or', type: 'date'},
		{name:'val_total_doc_or', type: 'numeric'},
		{name:'moneda_doc_or', type: 'string'},
		{name:'estacion_doc_or', type: 'string'},
		{name:'pais_doc_or', type: 'string'},
    {name:'id_punto_venta', type: 'numeric'},
    {name:'agt_tv_head', type: 'string'},
    {name:'estacion_head', type: 'string'},
    {name:'suc_head', type: 'string'},
    {name:'city_head', type: 'string'},
    {name:'pais_head', type: 'string'},
    {name:'desc_moneda', type: 'string'},
    {name:'concepto_codigo', type: 'string'},
    {name:'id_concepto_ingas', type: 'numeric'},
    {name:'tipo_cambio', type: 'numeric'},
    {name:'nro_mco', type: 'string'},
    {name:'pax', type: 'string'},
    {name:'id_funcionario_emisor', type: 'numeric'},
    {name:'desc_funcionario1', type: 'string'},
    {name:'t_c_doc_or', type: 'numeric'},
    {name:'val_conv_doc_or', type: 'numeric'},



	],
	sortInfo:{
		field: 'id_mco',
		direction: 'ASC'
	},
	bdel:false,
	bsave:false,
  btest:false,
  bnew: false,
  bedit:false,

  cmbPuntoV: new Ext.form.ComboBox({
      name: 'punto_venta',
      id: 'id_punto_venta',
      fieldLabel: 'Punto Venta',
      allowBlank: true,
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
  }),
})
</script>
