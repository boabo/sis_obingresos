<?php
/**
 * @package pxP
 * @file    Clasificacion.php
 * @author  Ismael Valdivia
 * @date    26/02/2020
 * @description Archivo con la interfaz de usuario que permite la ejecucion de las funcionales del sistema
 */
header("content-type:text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ResumenVentas = Ext.extend(Phx.gridInterfaz, {

        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                return "x-selectable";
            }
        },
        constructor : function(config) {

            this.maestro = config.maestro;
            Phx.vista.ResumenVentas.superclass.constructor.call(this, config);
            this.init();
            this.addButton('ReporteResumenVentas',{
                text: 'Reporte Resumen <br> de Ventas',
                iconCls: 'bexcel',
                disabled: false,
                handler: this.onButtonReporteResumenVentas,
                tooltip: '<b>Resumen de Ventas</b>',
                scope:this
            });

            this.addButton('BtnDetalleVentaCounter',
                    {
        			    text: 'Detalle Ventas',
        				iconCls: 'blist',
        				disabled: true,
        				handler: this.DetalleVentaCounter,
        				tooltip: '<b>Detalle Venta</b></br>Muestra el Detalle de Venta por Counter.'
        			}
        		);

            this.tbar.addField(this.fecha_ini);
            this.tbar.addField(this.fecha_fin);
            this.tbar.addField(this.punto_venta);

            this.fecha_ini.on('select',function(value){
                this.punto_venta.reset();
            },this);

             this.fecha_fin.on('select',function(value){
                 this.punto_venta.reset();
             },this);

             this.punto_venta.on('select', function( combo, record, index){
                 this.capturaFiltros();
             },this);


        },

        onButtonReporteResumenVentas: function () {
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_obingresos/control/Boleto/ReporteResumenVentasCounter',
                params:{
                        fecha_ini : this.fecha_ini.getValue().dateFormat('d/m/Y'),
                        fecha_fin : this.fecha_fin.getValue().dateFormat('d/m/Y'),
                        punto_venta : this.punto_venta.getValue(),
                        nombre_pv : this.tbar.items.items[4].lastSelectionText,
              },

                success: this.successExport,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },


        DetalleVentaCounter: function(){
      	            var rec = {maestro: this.sm.getSelected().data,
                               principal: this.store.baseParams}
      	            rec.counter='especifico';

      	            Phx.CP.loadWindows('../../../sis_obingresos/vista/boleto/VentasDiaCounter.php',
      	                'Detalle Ventas',
      	                {
      	                    width:1200,
      	                    height:600
      	                },
      	                rec,
      	                this.idContenedor,
      	                'VentasDiaCounter');

      	        },

          preparaMenu: function () {
            var rec = this.sm.getSelected();
            //if(rec.data.neto_total_mt !== null || rec.data.importe_total_mt !== null || rec.data.cant_bol_mt !== null){
                this.getBoton('BtnDetalleVentaCounter').enable();
            //}
            Phx.vista.ResumenVentas.superclass.preparaMenu.call(this);
          },

          liberaMenu : function(){
      				var rec = this.sm.getSelected();
      			this.getBoton('BtnDetalleVentaCounter').disable();
      			Phx.vista.ResumenVentas.superclass.liberaMenu.call(this);

      		},

        //tam_pag:10000,
        Atributos:[
            {
                config:{
                    name: 'agente_venta',
                    fieldLabel: 'Cod. Agente',
                    anchor: '100%',
                    disabled: true,
                    gwidth: 100,
                    readOnly:true
                },
                type:'TextField',
                filters:{pfiltro:'agente_venta',type:'string'},
                id_grupo:0,
                grid:true,
                form:true,
                bottom_filter : true
            },
            {
                config:{
                    name: 'counter',
                    fieldLabel: 'Counter',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 250,
                    maxLength:10,
                    minLength:10,
                    enableKeyEvents:true
                },
                type:'TextField',
                id_grupo:0,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'monto_ml',
                    fieldLabel: 'Total M/L',
                    disabled: true,
                    anchor: '90%',
                    gwidth: 150	,
                    readOnly:true,
                    galign:'right',
                    renderer:function (value,p,record){
            					if(record.data.tipo_reg != 'summary'){
            						return  String.format('<div style="font-size:12px; color:blue; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
            					}

            					else{
            						return  String.format('<div style="font-size:15px; text-align:right; color:blue;"><b>{0}<b></div>', Ext.util.Format.number(record.data.precio_total_ml_t,'0,000.00'));
            					}
            				},
                },
                type:'NumberField',
                id_grupo:0,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'monto_me',
                    fieldLabel: 'Total M/E',
                    disabled: true,
                    anchor: '90%',
                    gwidth: 150	,
                    readOnly:true,
                    galign:'right',
                    renderer:function (value,p,record){
            					if(record.data.tipo_reg != 'summary'){
            						return  String.format('<div style="font-size:12px; color:#E86A00; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
            					}

            					else{
            						return  String.format('<div style="font-size:15px; text-align:right; color:#E86A00;"><b>{0}<b></div>', Ext.util.Format.number(record.data.precio_total_me_t,'0,000.00'));
            					}
            				}
                },
                type:'NumberField',
                id_grupo:0,
                grid:true,
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
                id_grupo:1,
                grid:true,
                form:false
            }
        ],

        fwidth: '85%',
        fheight: '70%',

        onEnablePanel: function(idPanel, data) {
            var myPanel;
            if (typeof idPanel == 'object') {
                myPanel = idPanel
            } else {
                myPanel = Phx.CP.getPagina(idPanel);
            }

            if (idPanel && myPanel) {
                //Accede al panel derecho
                //console.log(myPanel);
                //console.log(idPanel+'-east-0');
                myPanelEast = Phx.CP.getPagina(idPanel+'-east-0');
                //console.log(myPanelEast);

                //Carga los datos de ambos paneles
                myPanel.onReloadPage(data);
                myPanelEast.onReloadPage(data);

            }

            delete myPanel;
            delete myPanelEast;

        },


        title:'Buscador Boleto Amadeus',
        ActList:'../../sis_obingresos/control/Boleto/listarResumenVentasCounter',
        id_store:'id_boleto_amadeus',
        fields: [
            {name:'agente_venta', type: 'string'},
            {name:'counter', type: 'string'},
            {name:'monto_ml', type: 'numeric'},
            {name:'monto_me', type: 'numeric'},
        ],
        sortInfo:{
            field: 'counter',
            direction: 'ASC'
        },
        arrayDefaultColumHidden:['estado_reg','usuario_ai',
            'fecha_reg','fecha_mod','usr_reg','usr_mod','codigo_agencia','nombre_agencia','neto','comision'],

        bdel:false,
        bsave:false,
        bnew:false,
        bedit:false,
        bexcel:false,
      	btest:false,


        capturaFiltros:function(combo, record, index){
            // this.desbloquearOrdenamientoGrid();
            this.store.baseParams.fecha_ini = this.fecha_ini.getValue().dateFormat('d/m/Y');
            this.store.baseParams.fecha_fin = this.fecha_fin.getValue().dateFormat('d/m/Y');
            this.store.baseParams.punto_venta = this.punto_venta.getValue();
            this.load({params:{start:0, limit:this.tam_pag}});

        },

        fecha_ini : new Ext.form.DateField({
            name: 'fecha_reg',
            fieldLabel: 'Fecha',
            emptyText:'Fecha Inicial',
            anchor: '60%',
            gwidth: 100,
            format: 'd/m/Y'
        }),
        fecha_fin : new Ext.form.DateField({
            name: 'fecha_reg',
            fieldLabel: 'Fecha',
            emptyText:'Fecha Final',
            anchor: '60%',
            gwidth: 100,
            format: 'd/m/Y'
        }),
        punto_venta : new Ext.form.ComboBox({
            name: 'id_punto_venta',
            fieldLabel: 'Punto Venta',
            emptyText:'Punto de Venta...',
            store:new Ext.data.JsonStore(
                {
                    url: '../../sis_obingresos/control/Boleto/obtenerPuntosVentasCounter',
                    id: 'id_punto_venta',
                    root: 'datos',
                    sortInfo:{
                        field: 'nombre',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_punto_venta','tipo','id_sucursal','nombre','codigo','habilitar_comisiones','formato_comprobante'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams:{par_filtro:'puve.nombre#puve.codigo'}
                }),
            valueField: 'id_punto_venta',
            displayField: 'nombre',
            hiddenName: 'id_punto_venta',
            typeAhead: true,
            triggerAction: 'all',
            lazyRender:true,
            mode:'remote',
            gwidth: 100,
            pageSize:50,
            queryDelay:500,
            anchor:"35%",
            minChars:2
        }),

    });
</script>
