<?php
/**
 *@package pXP
 *@file BoletoAmadeus.php
 *@author  Gonzalo Sarmiento
 *@date 07-06-2016 18:52:34
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.BoletoAmadeus=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.maestro=config.maestro;
                //this.grupo = 'no';
                this.tipo_usuario = 'cajero';

                Ext.Ajax.request({
                    url:'../../sis_ventas_facturacion/control/Venta/getVariablesBasicas',
                    params: {'prueba':'uno'},
                    success:this.successGetVariables,
                    failure: this.conexionFailure,
                    arguments:config,
                    timeout:this.timeout,
                    scope:this
                });
            },
            successGetVariables : function (response,request) {
                //llama al constructor de la clase padre
                Phx.vista.BoletoAmadeus.superclass.constructor.call(this,request.arguments);
                this.init();

                this.addButton('btnBoletos',
                    {
                        text: 'Traer Boletos',
                        iconCls: 'breload2',
                        disabled: false,
                        handler: this.onTraerBoletos,
                        tooltip: 'Traer boletos vendidos'
                    }
                );

                //this.addButton('cerrar',{grupo:[0],text:'Cerrar Caja',iconCls: 'block',disabled:false,handler:this.preparaCerrarCaja,tooltip: '<b>Cerrar la Caja</b>'});

                //this.store.baseParams.estado = 'borrador';
                this.iniciarEventos();
                this.seleccionarPuntoVentaSucursal();
                this.grid.addListener('cellclick', this.oncellclick,this);
            },

            seleccionarPuntoVentaSucursal : function () {

                var storeCombo = new Ext.data.JsonStore({
                    url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
                    id: 'id_punto_venta',
                    root: 'datos',
                    sortInfo: {
                        field: 'nombre',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_punto_venta', 'nombre', 'codigo'],
                    remoteSort: true,
                    baseParams: {tipo_usuario: this.tipo_usuario,par_filtro: 'puve.nombre#puve.codigo'}
                });


                storeCombo.load({params:{start:0,limit:this.tam_pag},
                    callback : function (r) {
                        if (r.length == 1 ) {
                            this.id_punto_venta = r[0].data.id_punto_venta;
                            this.store.baseParams.id_punto_venta = r[0].data.id_punto_venta;
                            this.Cmp.id_forma_pago.store.baseParams.id_punto_venta = this.id_punto_venta;
                            this.Cmp.id_forma_pago2.store.baseParams.id_punto_venta = this.id_punto_venta;
                            //this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('d/m/Y');
                            this.argumentExtraSubmit.id_punto_venta = this.id_punto_venta;
                            this.load({params:{start:0, limit:this.tam_pag}});
                        } else {

                            var combo2 = new Ext.form.ComboBox(
                                {
                                    typeAhead: false,
                                    fieldLabel: 'Punto de Venta',
                                    allowBlank : false,
                                    store: storeCombo,
                                    mode: 'remote',
                                    pageSize: 15,
                                    triggerAction: 'all',
                                    valueField : 'id_punto_venta',
                                    displayField : 'nombre',
                                    forceSelection: true,
                                    tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
                                    allowBlank : false,
                                    anchor: '100%'
                                });

                            var formularioInicio = new Ext.form.FormPanel({
                                items: [combo2],
                                padding: true,
                                bodyStyle:'padding:5px 5px 0',
                                border: false,
                                frame: false
                            });

                            var VentanaInicio = new Ext.Window({
                                title: 'Punto de Venta / Sucursal',
                                modal: true,
                                width: 550,
                                height: 160,
                                bodyStyle: 'padding:5px;',
                                layout: 'fit',
                                hidden: true,
                                buttons: [
                                    {
                                        text: '<i class="fa fa-check"></i> Aceptar',
                                        handler: function () {
                                            if (formularioInicio.getForm().isValid()) {
                                                validado = true;
                                                VentanaInicio.close();
                                                this.id_punto_venta  = combo2.getValue();
                                                this.Cmp.id_forma_pago.store.baseParams.id_punto_venta = this.id_punto_venta;
                                                this.Cmp.id_forma_pago2.store.baseParams.id_punto_venta = this.id_punto_venta;
                                                this.store.baseParams.id_punto_venta = combo2.getValue();
                                                this.argumentExtraSubmit.id_punto_venta = this.id_punto_venta;
                                                this.load({params:{start:0, limit:this.tam_pag}});
                                            }
                                        },
                                        scope: this
                                    }],
                                items: formularioInicio,
                                autoDestroy: true,
                                closeAction: 'close'
                            });
                            VentanaInicio.show();
                            VentanaInicio.on('beforeclose', function (){
                                if (!validado) {
                                    alert('Debe seleccionar el punto de venta o sucursal de trabajo');
                                    return false;
                                }
                            },this)
                        }

                    }, scope : this
                });



            },

            Atributos:[
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_boleto'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'moneda_sucursal'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'moneda_fp1'
                    },
                    type:'TextField',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'moneda_fp2'
                    },
                    type:'TextField',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'codigo_forma_pago'
                    },
                    type:'TextField',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'codigo_forma_pago2'
                    },
                    type:'TextField',
                    form:true
                },
                {
                    config: {
                        name: 'id_boleto_vuelo',
                        fieldLabel: 'Vuelo Ini Retorno',
                        allowBlank: true,
                        emptyText: 'Vuelo...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_obingresos/control/BoletoVuelo/listarBoletoVuelo',
                            id: 'id_boleto_vuelo',
                            root: 'datos',
                            sortInfo: {
                                field: 'cupon',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_boleto_vuelo', 'boleto_vuelo'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'bvu.aeropuerto_origen#bvu.aeropuerto_destino'}
                        }),
                        valueField: 'id_boleto_vuelo',
                        displayField: 'boleto_vuelo',
                        gdisplayField: 'vuelo_retorno',
                        hiddenName: 'id_boleto_vuelo',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        gwidth: 150,
                        listWidth:450,
                        resizable:true,
                        minChars: 2,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['vuelo_retorno']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 2,
                    grid: false,
                    form: true
                },
                {
                    config:{
                        name: 'estado',
                        fieldLabel: 'Revisado',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 80,
                        maxLength:3,
                        renderer: function (value, p, record, rowIndex, colIndex){

                            //check or un check row
                            var checked = '',
                                state = '',
                                momento = 'no';
                            if(value == 'revisado'){
                                checked = 'checked';
                            }
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('<div style="vertical-align:middle;text-align:center;"><input style="height:37px;width:37px;" type="checkbox"  {0} {1}></div>',checked, state);
                            }
                            else{
                                return '';
                            }
                        }
                    },
                    type: 'TextField',
                    filters: { pfiltro:'nr.estado',type:'string'},
                    id_grupo: 2,
                    grid: true,
                    form: true
                },
                {
                    config:{
                        name: 'comision',
                        fieldLabel: 'Comisión AGT',
                        allowBlank:true,
                        anchor: '80%',
                        allowDecimals:true,
                        decimalPrecision:2,
                        allowNegative : false,
                        disabled:true,
                        gwidth: 125
                    },
                    type:'NumberField',
                    id_grupo:2,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'voided',
                        fieldLabel: 'Anulado',
                        anchor: '60%',
                        gwidth: 60,
                        readOnly:true,
                        renderer : function(value, p, record) {
                            if (record.data['voided'] != 'si') {
                                return String.format('<div title="Anulado"><b><font color="green">{0}</font></b></div>', value);

                            } else {
                                return String.format('<div title="Anulado"><b><font color="red">{0}</font></b></div>', value);
                            }
                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.voided',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'boletos',
                        fieldLabel: 'Boletos a Pagar',
                        anchor: '80%',
                        gwidth: 80,
                        readOnly:true

                    },
                    type:'TextArea',
                    id_grupo:2,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'localizador',
                        fieldLabel: 'Pnr',
                        anchor: '80%',
                        disabled: true,
                        gwidth: 60
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.localizador',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true,
                    bottom_filter: true
                },
                {
                    config:{
                        name: 'nro_boleto',
                        fieldLabel: 'Billete: 930-',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:10,
                        minLength:10,
                        enableKeyEvents:true,
                        renderer : function(value, p, record) {
                            if (record.data['mensaje_error'] != '') {
                                return String.format('<div title="Error"><b><font color="red">{0}</font></b></div>', value);

                            } else {
                                return String.format('{0}', value);
                            }


                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.nro_boleto',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true,
                    bottom_filter: true
                },
                {
                    config:{
                        name: 'pasajero',
                        fieldLabel: 'Pasajero',
                        anchor: '100%',
                        disabled: true,
                        gwidth: 130,
                        readOnly:true
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.pasajero',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true,
                    bottom_filter: true
                },
                {
                    config:{
                        name: 'moneda',
                        fieldLabel: 'Moneda',
                        disabled: true,
                        anchor: '80%',
                        gwidth: 70,
                        readOnly:true

                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.moneda',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'total',
                        fieldLabel: 'Total',
                        disabled: true,
                        anchor: '80%',
                        gwidth: 70	,
                        readOnly:true
                    },
                    type:'NumberField',
                    filters:{pfiltro:'bol.total',type:'numeric'},
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'neto',
                        fieldLabel: 'Neto',
                        disabled: true,
                        gwidth: 70
                    },
                    type:'NumberField',
                    filters:{pfiltro:'bol.neto',type:'numeric'},
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'agente_venta',
                        fieldLabel: 'Agente Venta',
                        disabled: true,
                        anchor: '40%',
                        gwidth: 160
                    },
                    type:'TextField',
                    filters:{pfiltro:'nr.agente_venta',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true,
                    bottom_filter: true
                },
                {
                    config:{
                        name: 'codigo_agente',
                        fieldLabel: 'Codigo Agente',
                        anchor: '40%',
                        disabled: true,
                        gwidth: 100
                    },
                    type:'TextField',
                    filters:{pfiltro:'nr.agente_venta',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'forma_pago_amadeus',
                        fieldLabel: 'Pago Amadeus',
                        gwidth: 100,
                        disabled: true,
                        readOnly:true
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.forma_pago_amadeus',type:'string'},
                    grid:true,
                    id_grupo:0,
                    form:true
                },
                /*{
                    config:{
                        name: 'fp_amadeus_corregido',
                        fieldLabel: 'FP Amadeus Corregido',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength:50
                    },
                    type:'TextField',

                    id_grupo:1,
                    grid:true,
                    form:true
                },*/
                {
                    config: {
                        name: 'id_forma_pago',
                        fieldLabel: 'Forma de Pago',
                        allowBlank: false,
                        emptyText: 'Forma de Pago...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_ventas_facturacion/control/FormaPago/listarFormaPago',
                            id: 'id_forma_pago',
                            root: 'datos',
                            sortInfo: {
                                field: 'nombre',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_forma_pago', 'nombre', 'desc_moneda','registrar_tarjeta','registrar_cc','codigo'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'forpa.nombre#forpa.codigo#mon.codigo_internacional',sw_tipo_venta:'boletos'}
                        }),
                        valueField: 'id_forma_pago',
                        displayField: 'nombre',
                        gdisplayField: 'forma_pago',
                        hiddenName: 'id_forma_pago',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre}</p><p>Moneda:{desc_moneda}</p> </div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        gwidth: 150,
                        listWidth:450,
                        resizable:true,
                        minChars: 2,
                        disabled:true,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['forma_pago']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config:{
                        name: 'monto_forma_pago',
                        fieldLabel: 'Monto a Pagar',
                        allowBlank:false,
                        anchor: '80%',
                        allowDecimals:true,
                        decimalPrecision:2,
                        allowNegative : false,
                        disabled:true,
                        gwidth: 110
                    },
                    type:'NumberField',
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'numero_tarjeta',
                        fieldLabel: 'No Tarjeta 1',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength:50
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'codigo_tarjeta',
                        fieldLabel: 'Codigo de Autorización 1',
                        allowBlank: true,
                        anchor: '80%',
                        maxLength:20

                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },
                /*{
                    config:{
                        name: 'ctacte',
                        fieldLabel: 'Cta. Corriente 1',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength:20
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },*/
                {
                    config: {
                        name: 'id_auxiliar',
                        fieldLabel: 'Cuenta Corriente',
                        allowBlank: true,
                        emptyText: 'Cuenta Corriente...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_contabilidad/control/Auxiliar/listarAuxiliar',
                            id: 'id_auxiliar',
                            root: 'datos',
                            sortInfo: {
                                field: 'codigo_auxiliar',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_auxiliar', 'codigo_auxiliar','nombre_auxiliar'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'auxcta.codigo_auxiliar#auxcta.nombre_auxiliar',corriente:'si'}
                        }),
                        valueField: 'id_auxiliar',
                        displayField: 'nombre_auxiliar',
                        gdisplayField: 'codigo_auxiliar',
                        hiddenName: 'id_auxiliar',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre_auxiliar}</p><p>Codigo:{codigo_auxiliar}</p> </div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        gwidth: 150,
                        listWidth:350,
                        resizable:true,
                        minChars: 2,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['nombre_auxiliar']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config:{
                        name: 'forma_pago_amadeus2',
                        fieldLabel: 'Pago Amadeus 2',
                        gwidth: 100,
                        readOnly:true
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.forma_pago_amadeus2',type:'string'},
                    grid:true,
                    id_grupo:1,
                    form:false
                },
                /*{
                    config:{
                        name: 'fp_amadeus_corregido2',
                        fieldLabel: 'FP Amadeus Corregido2',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength:50
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:true,
                    form:true
                },*/
                {
                    config: {
                        name: 'id_forma_pago2',
                        fieldLabel: 'Forma de Pago 2',
                        allowBlank: true,
                        emptyText: 'Forma de Pago...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_ventas_facturacion/control/FormaPago/listarFormaPago',
                            id: 'id_forma_pago',
                            root: 'datos',
                            sortInfo: {
                                field: 'nombre',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_forma_pago', 'nombre', 'desc_moneda','registrar_tarjeta','registrar_cc','codigo'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'forpa.nombre#mon.codigo_internacional',fp_ventas:'si'}
                        }),
                        valueField: 'id_forma_pago',
                        displayField: 'nombre',
                        gdisplayField: 'forma_pago2',
                        hiddenName: 'id_forma_pago',
                        anchor: '90%',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre}</p><p>Moneda:{desc_moneda}</p> </div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        gwidth: 150,
                        listWidth:450,
                        resizable:true,
                        minChars: 2,
                        disabled:true,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['forma_pago2']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config:{
                        name: 'monto_forma_pago2',
                        fieldLabel: 'Monto a Pagar 2',
                        allowBlank:true,
                        anchor: '80%',
                        allowDecimals:true,
                        decimalPrecision:2,
                        allowNegative : false,
                        disabled:true,
                        gwidth: 125
                    },
                    type:'NumberField',
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'numero_tarjeta2',
                        fieldLabel: 'No Tarjeta 2',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength:50
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'codigo_tarjeta2',
                        fieldLabel: 'Codigo de Autorización 2',
                        allowBlank: true,
                        anchor: '80%',
                        maxLength:20

                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },
                /*{
                    config:{
                        name: 'ctacte2',
                        fieldLabel: 'Cta. Corriente 2',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength:20
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },*/
                {
                    config:{
                        name: 'fecha_emision',
                        fieldLabel: 'Fecha Emision',
                        gwidth: 100,
                        disabled: true,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'bol.fecha_emision',type:'date'},
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'codigo_agencia',
                        fieldLabel: 'agt',
                        gwidth: 100
                    },
                    type:'TextField',
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'nombre_agencia',
                        fieldLabel: 'Agencia',
                        gwidth: 120
                    },
                    type:'TextField',
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
                    filters:{pfiltro:'bol.estado_reg',type:'string'},
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
                    filters:{pfiltro:'bol.id_usuario_ai',type:'numeric'},
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
                    filters:{pfiltro:'bol.fecha_reg',type:'date'},
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
                    filters:{pfiltro:'bol.usuario_ai',type:'string'},
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
            tam_pag:50,
            fwidth: '70%',
            title:'Boleto',
            ActSave:'../../sis_obingresos/control/Boleto/modificarBoletoVenta',
            //ActDel:'../../sis_obingresos/control/Boleto/eliminarBoleto',
            ActList:'../../sis_obingresos/control/Boleto/listarBoletosEmitidosAmadeus',

            id_store:'id_boleto',
            fields: [
                {name:'id_boleto', type: 'numeric'},
                {name:'fecha_emision', type: 'date',dateFormat:'Y-m-d'},
                {name:'estado', type: 'string'},
                {name:'id_agencia', type: 'numeric'},
                {name:'moneda', type: 'string'},
                {name:'total', type: 'numeric'},
                {name:'pasajero', type: 'string'},
                {name:'id_moneda_boleto', type: 'numeric'},
                {name:'estado_reg', type: 'string'},
                {name:'codigo_agencia', type: 'string'},
                {name:'neto', type: 'numeric'},
                {name:'localizador', type: 'string'},
                {name:'monto_pagado_moneda_boleto', type: 'numeric'},
                {name:'monto_total_fp', type: 'numeric'},
                {name:'liquido', type: 'numeric'},
                {name:'nro_boleto', type: 'string'},
                {name:'id_usuario_ai', type: 'numeric'},
                {name:'id_usuario_reg', type: 'numeric'},
                {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'usuario_ai', type: 'string'},
                {name:'id_usuario_mod', type: 'numeric'},
                {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'usr_reg', type: 'string'},
                {name:'usr_mod', type: 'string'},
                {name:'nombre_agencia', type: 'string'},
                {name:'id_forma_pago', type: 'numeric'},
                {name:'forma_pago', type: 'string'},
                {name:'codigo_forma_pago', type: 'string'},
                {name:'forma_pago_amadeus', type: 'string'},
                {name:'numero_tarjeta', type: 'string'},
                //{name:'ctacte', type: 'string'},
                {name:'codigo_forma_pago', type: 'string'},
                {name:'nombre_auxiliar', type: 'string'},
                {name:'monto_forma_pago', type: 'numeric'},
                //{name:'fp_amadeus_corregido', type: 'string'},
                {name:'id_forma_pago2', type: 'numeric'},
                {name:'forma_pago2', type: 'string'},
                {name:'codigo_forma_pago2', type: 'string'},
                {name:'forma_pago_amadeus2', type: 'string'},
                {name:'numero_tarjeta2', type: 'string'},
                //{name:'ctacte2', type: 'string'},
                {name:'codigo_forma_pago2', type: 'string'},
                {name:'codigo_tarjeta2', type: 'string'},
                {name:'monto_forma_pago2', type: 'numeric'},
                {name:'pais', type: 'string'},
                {name:'agente_venta', type: 'string'},
                {name:'codigo_agente', type: 'string'},
                {name:'moneda_sucursal', type: 'string'},
                {name:'moneda_fp1', type: 'string'},
                {name:'moneda_fp2', type: 'string'},
                {name:'voided', type: 'string'}

            ],
            sortInfo:{
                field: 'id_boleto',
                direction: 'DESC'
            },
            arrayDefaultColumHidden:['estado_reg','usuario_ai',
                'fecha_reg','fecha_mod','usr_reg','usr_mod','codigo_agencia','nombre_agencia'],

            bdel:false,
            bsave:false,
            bnew:false,
            bedit:false,

            iniciarEventos : function () {

                this.Cmp.monto_forma_pago.on('change',function(field,newValue,oldValue){
                    if (newValue < oldValue) {
                        this.Cmp.id_forma_pago2.setDisabled(false);
                        this.Cmp.monto_forma_pago2.setDisabled(false);
                    }
                },this);

                this.Cmp.id_forma_pago.on('select', function (combo,record){
                    if (record) {
                        this.Cmp.moneda_fp1.setValue(record.data.desc_moneda);
                        this.manejoComponentesFp1(record.data.id_forma_pago,record.data.codigo);
                    } else {
                        this.manejoComponentesFp1(this.Cmp.id_forma_pago.getValue(),this.Cmp.codigo_forma_pago.getValue());
                    }

                    var monto_pagado_fp2 = this.getMontoMonBol(this.Cmp.monto_forma_pago2.getValue(),this.Cmp.moneda_fp2.getValue());

                    if (monto_pagado_fp2 > -1) {

                        //Si la forma de pago y el boleto estan en la misma moneda
                        if (this.Cmp.moneda.getValue() == this.Cmp.moneda_fp1.getValue()){
                            this.Cmp.monto_forma_pago.setValue(this.Cmp.total.getValue() - monto_pagado_fp2);

                        }
                        //Si el boleto esta en usd y la forma de pago es distinta a usd y la forma de pago es igual a la moneda de la sucursal
                        else if (this.Cmp.moneda.getValue() == 'USD' && this.Cmp.moneda_fp1.getValue() == this.Cmp.moneda_sucursal.getValue()) {
                            //convertir de  dolares a moneda sucursal(multiplicar)
                            this.Cmp.monto_forma_pago.setValue(this.round(((this.Cmp.total.getValue()- monto_pagado_fp2)*this.Cmp.tc.getValue()),2));

                            //Si el boleto esta en moneda sucursal y la forma de pago es usd y la moneda de la sucursales distinta a usd
                        } else if (this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && this.Cmp.moneda_fp1.getValue() == 'USD') {
                            //convertir de  moneda sucursal a dolares(dividir)
                            this.Cmp.monto_forma_pago.setValue(this.round(((this.Cmp.total.getValue()- monto_pagado_fp2)/this.Cmp.tc.getValue()),2));

                        } else {
                            this.Cmp.monto_forma_pago.setValue(0);

                        }
                    } else {
                        this.Cmp.monto_forma_pago.setValue(0);
                    }

                },this);

                this.Cmp.id_forma_pago2.on('select', function (combo,record) {
                    if (record) {
                        this.Cmp.moneda_fp2.setValue(record.data.desc_moneda);
                        this.manejoComponentesFp2(record.data.id_forma_pago,record.data.codigo);
                    } else {
                        this.manejoComponentesFp2(this.Cmp.id_forma_pago2.getValue(),this.Cmp.codigo_forma_pago2.getValue());
                    }

                    var monto_pagado_fp1 = this.getMontoMonBol(this.Cmp.monto_forma_pago.getValue(),this.Cmp.moneda_fp1.getValue());

                    if (monto_pagado_fp1 > -1) {
                        //Si la forma de pago y el boleto estan en la misma moneda
                        if (this.Cmp.moneda.getValue() == this.Cmp.moneda_fp2.getValue()){
                            this.Cmp.monto_forma_pago2.setValue(this.Cmp.total.getValue() - monto_pagado_fp1);
                        }
                        //Si el boleto esta en usd y la forma de pago es distinta a usd y la forma de pago es igual a la moneda de la sucursal
                        else if (this.Cmp.moneda.getValue() == 'USD' && this.Cmp.moneda_fp2.getValue() == this.Cmp.moneda_sucursal.getValue()) {
                            //convertir de  dolares a moneda sucursal(multiplicar)
                            this.Cmp.monto_forma_pago2.setValue(this.round(((this.Cmp.total.getValue()- monto_pagado_fp1)*this.Cmp.tc.getValue()),2));
                            //Si el boleto esta en moneda sucursal y la forma de pago es usd y la moneda de la sucursales distinta a usd
                        } else if (this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && this.Cmp.moneda_fp2.getValue() == 'USD') {
                            //convertir de  moneda sucursal a dolares(dividir)
                            this.Cmp.monto_forma_pago2.setValue(this.round(((this.Cmp.total.getValue()-monto_pagado_fp1)/this.Cmp.tc.getValue()),2));
                        } else {
                            this.Cmp.monto_forma_pago2.setValue(0);
                        }
                    } else {
                        this.Cmp.monto_forma_pago2.setValue(0);
                    }

                },this);


            },
            //devuelve el monto en la moenda del boleto
            getMontoMonBol : function (monto, moneda_fp) {
                //Si la forma de pago y el boleto estan en la misma moneda
                if (monto == 0) {
                    return 0;
                } else if (this.Cmp.moneda.getValue() == moneda_fp){
                    return monto;
                } //Si el boleto esta en usd y la forma de pago es distinta a usd y la forma de pago es igual a la moneda de la sucursal
                else if (this.Cmp.moneda.getValue() == 'USD' && moneda_fp == this.Cmp.moneda_sucursal.getValue()) {
                    //convertir a dolares(dividir)
                    return this.roundMenor(monto/this.Cmp.tc.getValue(),2);
                    //Si el boleto esta en moneda sucursal y la forma de pago es usd y la moneda de la sucursales distinta a usd
                } else if (this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && moneda_fp == 'USD') {
                    //convertir a moneda sucursal(mutiplicar)
                    return this.roundMenor(monto*this.Cmp.tc.getValue(),2);
                } else {
                    return -1;
                }
            },

            onTraerBoletos : function () {
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url:'../../sis_obingresos/control/Boleto/traerBoletos',
                    params: {id_punto_venta: this.id_punto_venta,start:0,limit:this.tam_pag,sort:'id_boleto',dir:'DESC'},
                    success:this.successSinc,
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });
            },

            successSinc: function(resp) {
                Phx.CP.loadingHide();
                var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                if (reg.ROOT.error) {
                    Ext.Msg.alert('Error','Boletos no recuperados: se ha producido un error inesperado. Comuníquese con el Administrador del Sistema.')
                } else {
                    Ext.Msg.alert('Mensaje','Boletos Recuperados')
                    this.reload();
                }
            },

        /* preparaCerrarCaja:function(){
            Phx.CP.loadWindows('../../../sis_ventas_facturacion/vista/apertura_cierre_caja/FormCierreCaja.php',
                'Cerrar Caja',
                {
                    modal:true,
                    width:1000,
                    height:400
                }, {data:this}, this.idContenedor,'FormCierreCaja',
                {
                    config:[{
                        event:'beforesave',
                        delegate: this.cerrarCaja,
                    }
                    ],
                    scope:this
                })
        },

        cerrarCaja:function(wizard,resp){
            var me=this;
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_ventas_facturacion/control/AperturaCierreCaja/insertarAperturaCierreCaja',
                params:{
                    id_apertura_cierre_caja: resp.id_apertura_cierre_caja,
                    id_sucursal: resp.id_sucursal,
                    id_punto_venta: resp.id_punto_venta,
                    obs_cierre: resp.obs_cierre,
                    arqueo_moneda_local: resp.arqueo_moneda_local,
                    arqueo_moneda_extranjera: resp.arqueo_moneda_extranjera,
                    accion :'cerrar',
                    monto_inicial: resp.monto_inicial,
                    obs_apertura: resp.obs_apertura,
                    monto_inicial_moneda_extranjera: resp.monto_inicial_moneda_extranjera
                },
                argument:{wizard:wizard},
                success:this.successWizard,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });

        },

        successWizard:function(resp){
            Phx.CP.loadingHide();
            resp.argument.wizard.panel.destroy()
            this.reload();
        },*/

            onButtonEdit : function () {
                Phx.vista.BoletoAmadeus.superclass.onButtonEdit.call(this);
                this.ocultarGrupo(2);
                //this.ocultarGrupo(0);
                this.grupo = 'si';
                this.Cmp.nro_boleto.allowBlank = false;
                this.Cmp.nro_boleto.setDisabled(true);
                this.manejoComponentesFp1(this.sm.getSelected().data['id_forma_pago'],this.sm.getSelected().data['codigo_forma_pago']);
                this.manejoComponentesFp2(this.sm.getSelected().data['id_forma_pago2'],this.sm.getSelected().data['codigo_forma_pago2']);

                if (this.sm.getSelected().data['monto_total_fp'] < (this.sm.getSelected().data['total']) ) {
                    this.Cmp.id_forma_pago2.setDisabled(false);
                    this.Cmp.monto_forma_pago2.setDisabled(false);
                }

            },

            oncellclick : function(grid, rowIndex, columnIndex, e) {
                var record = this.store.getAt(rowIndex),
                    fieldName = grid.getColumnModel().getDataIndex(columnIndex); // Get field name

                if(fieldName == 'estado') {
                    if(record.data.tipo_reg != 'summary'){
                        this.cambiarRevision(record);
                    }
                }
                if(fieldName == 'nro_boleto') {
                    if(record.data.tipo_reg != 'summary'){
                        this.onButtonEdit(this);
                    }
                }
            },

            cambiarRevision: function(record){
                Phx.CP.loadingShow();
                var d = record.data
                Ext.Ajax.request({
                    url:'../../sis_obingresos/control/Boleto/cambiarRevisionBoleto',
                    params:{ id_boleto: d.id_boleto},
                    success: this.successRevision,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });
            },

            successRevision: function(resp){
                Phx.CP.loadingHide();
                var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                if(!reg.ROOT.error){
                    this.reload();
                }
            },

            tabsouth:[{
                url:'../../../sis_obingresos/vista/boleto_forma_pago/BoletoFormaPago.php',
                title:'Formas de Pago',
                height:'40%',
                cls:'BoletoFormaPago'
            }],

            Grupos:[{
                layout: 'column',
                items:[
                    {
                    bodyStyle: 'padding-right:10px;',
                    items:[
                        {
                            xtype:'fieldset',
                            layout: 'form',
                            border: true,
                            title: 'Datos Boleto',
                            bodyStyle: 'padding:0 10px 0;',
                            columnWidth: 0.5,
                            items:[],
                            id_grupo:0,
                            collapsible:true
                        }
                        ]
                    },
                    {
                        bodyStyle: 'padding-right:10px;',
                        items: [
                            {
                                xtype: 'fieldset',
                                layout: 'form',
                                border: true,
                                title: 'Boletos',
                                bodyStyle: 'padding:0 10px 0;',
                                columnWidth: 0.5,
                                items: [],
                                id_grupo: 2,
                                collapsible: true
                            }
                        ]
                    },
                    {
                        bodyStyle: 'padding-right:10px;',
                        items: [
                        {
                            xtype:'fieldset',
                            layout: 'form',
                            border: true,
                            title: 'Formas de Pago',
                            bodyStyle: 'padding:0 10px 0;',
                            columnWidth: 0.5,
                            items:[],
                            id_grupo:1,
                            collapsible:true,
                            collapsed:false
                        }
                        ]
                    }
                ]
            }],

            round : function(value, decimals) {
                return Math.ceil(value*100)/100;
            },
            roundMenor : function(value, decimals) {
                return Math.floor(value*100)/100;
            },
            manejoComponentesFp1 : function (id_fp1,codigo_fp1){
                //forma de pago 1
                if (id_fp1 == 0) {
                    this.Cmp.id_forma_pago.setDisabled(true);
                    this.Cmp.monto_forma_pago.setDisabled(true);
                    this.ocultarComponente(this.Cmp.numero_tarjeta);
                    this.ocultarComponente(this.Cmp.codigo_tarjeta);
                    //this.ocultarComponente(this.Cmp.ctacte);
                    this.ocultarComponente(this.Cmp.id_auxiliar);
                    this.Cmp.numero_tarjeta.allowBlank = true;
                    this.Cmp.codigo_tarjeta.allowBlank = true;
                    //this.Cmp.ctacte.allowBlank = true;
                } else {
                    this.Cmp.id_forma_pago.setDisabled(false);
                    this.Cmp.monto_forma_pago.setDisabled(false);
                    if (codigo_fp1.startsWith("CC") ||
                        codigo_fp1.startsWith("SF")) {
                        //this.ocultarComponente(this.Cmp.ctacte);
                        this.ocultarComponente(this.Cmp.id_auxiliar);
                        //this.Cmp.ctacte.reset();
                        this.mostrarComponente(this.Cmp.numero_tarjeta);
                        this.mostrarComponente(this.Cmp.codigo_tarjeta);
                        this.Cmp.numero_tarjeta.allowBlank = false;
                        this.Cmp.codigo_tarjeta.allowBlank = false;
                        //this.Cmp.ctacte.allowBlank = true;
                        //tarjeta de credito
                    } else if (codigo_fp1.startsWith("CT")) {
                        //cuenta corriente
                        this.ocultarComponente(this.Cmp.numero_tarjeta);
                        this.ocultarComponente(this.Cmp.codigo_tarjeta);
                        this.mostrarComponente(this.Cmp.id_auxiliar);
                        this.Cmp.numero_tarjeta.reset();
                        this.Cmp.codigo_tarjeta.reset();
                        //this.mostrarComponente(this.Cmp.ctacte);
                        this.Cmp.numero_tarjeta.allowBlank = true;
                        this.Cmp.codigo_tarjeta.allowBlank = true;
                        //this.Cmp.ctacte.allowBlank = false;
                    } else {
                        this.ocultarComponente(this.Cmp.numero_tarjeta);
                        this.ocultarComponente(this.Cmp.codigo_tarjeta);
                        //this.ocultarComponente(this.Cmp.ctacte);
                        this.ocultarComponente(this.Cmp.id_auxiliar);
                        this.Cmp.numero_tarjeta.reset();
                        this.Cmp.codigo_tarjeta.reset();
                        this.Cmp.id_auxiliar.reset();
                        //this.Cmp.ctacte.reset();
                        this.Cmp.numero_tarjeta.allowBlank = true;
                        this.Cmp.codigo_tarjeta.allowBlank = true;
                        //this.Cmp.ctacte.allowBlank = true;
                    }
                }
            },
            manejoComponentesFp2 : function (id_fp2,codigo_fp2){
                if (id_fp2) {
                    //forma de pago 2
                    if (id_fp2 == 0) {
                        this.Cmp.id_forma_pago2.setDisabled(true);
                        this.Cmp.monto_forma_pago2.setDisabled(true);
                        this.ocultarComponente(this.Cmp.numero_tarjeta2);
                        this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                        //this.ocultarComponente(this.Cmp.ctacte2);
                        this.Cmp.numero_tarjeta2.allowBlank = true;
                        this.Cmp.codigo_tarjeta2.allowBlank = true;
                        //this.Cmp.ctacte2.allowBlank = true;
                        this.Cmp.numero_tarjeta2.reset();
                        this.Cmp.codigo_tarjeta2.reset();
                        //this.Cmp.ctacte2.reset();
                    } else {
                        this.Cmp.id_forma_pago2.setDisabled(false);
                        this.Cmp.monto_forma_pago2.setDisabled(false);
                        if (codigo_fp2.startsWith("CC") ||
                            codigo_fp2.startsWith("SF")) {
                            //tarjeta de credito
                            //this.ocultarComponente(this.Cmp.ctacte2);
                            //this.Cmp.ctacte2.reset();
                            this.mostrarComponente(this.Cmp.numero_tarjeta2);
                            this.mostrarComponente(this.Cmp.codigo_tarjeta2);
                            this.Cmp.numero_tarjeta2.allowBlank = false;
                            this.Cmp.codigo_tarjeta2.allowBlank = false;
                            //this.Cmp.ctacte2.allowBlank = true;

                        } else if (codigo_fp2.startsWith("CT")) {
                            //cuenta corriente
                            this.ocultarComponente(this.Cmp.numero_tarjeta2);
                            //this.ocultarComponente(this.Cmp.numero_tarjeta2);
                            this.Cmp.numero_tarjeta2.reset();
                            this.Cmp.numero_tarjeta2.reset();
                            //this.mostrarComponente(this.Cmp.ctacte2);
                            this.Cmp.numero_tarjeta2.allowBlank = true;
                            this.Cmp.numero_tarjeta2.allowBlank = true;
                            //this.Cmp.ctacte2.allowBlank = false;
                        } else {
                            this.ocultarComponente(this.Cmp.numero_tarjeta2);
                            this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                            //this.ocultarComponente(this.Cmp.ctacte2);
                            this.Cmp.numero_tarjeta2.allowBlank = true;
                            this.Cmp.codigo_tarjeta2.allowBlank = true;
                            //this.Cmp.ctacte2.allowBlank = true;
                            this.Cmp.numero_tarjeta2.reset();
                            this.Cmp.codigo_tarjeta2.reset();
                            //this.Cmp.ctacte2.reset();
                        }
                    }
                } else {
                    this.ocultarComponente(this.Cmp.numero_tarjeta2);
                    this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                    //this.ocultarComponente(this.Cmp.ctacte2);
                    this.Cmp.numero_tarjeta2.allowBlank = true;
                    this.Cmp.codigo_tarjeta2.allowBlank = true;
                    //this.Cmp.ctacte2.allowBlank = true;
                    this.Cmp.id_forma_pago2.setDisabled(true);
                    this.Cmp.monto_forma_pago2.setDisabled(true);
                }

            }

        }
    )
</script>

