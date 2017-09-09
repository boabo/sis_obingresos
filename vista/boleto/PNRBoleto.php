<?php
/**
 *@package pXP
 *@file PNRBoleto.php
 *@author  (jrivera)
 *@date 07-06-2016 18:52:34
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.PNRBoleto=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.maestro=config.maestro;
                this.grupo = 'no';
                this.tipo_usuario = 'vendedor';
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
                Phx.vista.PNRBoleto.superclass.constructor.call(this,request.arguments);
                this.init();
                /*this.addButton('btnCaja',
                    {
                        text: 'Caja',
                        iconCls: 'btransfer',
                        disabled: true,
                        handler: this.onCaja,
                        tooltip: 'Envia el boleto para pago en caja'
                    }
                );*/
                this.addButton('btnPagado',
                    {
                        text: 'Pagado',
                        iconCls: 'bmoney',
                        disabled: false,
                        handler: this.onPagado,
                        tooltip: 'Marca el boleto como pagado'
                    }
                );
                this.addButton('btnBoletos',
                    {
                        text: 'Traer Boletos',
                        iconCls: 'breload2',
                        disabled: false,
                        handler: this.onTraerBoletos,
                        tooltip: 'Traer boletos vendidos'
                    }
                );
                /*this.addButton('btnImprimir',
                    {
                        text: 'Imprimir',
                        iconCls: 'bpdf32',
                        disabled: true,
                        handler: this.imprimirBoleto,
                        tooltip: '<b>Imprimir Boleto</b><br/>Imprime el boleto'
                    }
                );*/


                this.store.baseParams.estado = 'borrador';
                this.iniciarEventos();
                this.seleccionarPuntoVentaSucursal();

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
                    fields: ['id_punto_venta', 'nombre', 'codigo','habilitar_comisiones'],
                    remoteSort: true,
                    baseParams: {tipo_usuario: this.tipo_usuario,par_filtro: 'puve.nombre#puve.codigo'}
                });


                storeCombo.load({params:{start:0,limit:this.tam_pag},
                    callback : function (r) {
                        if (r.length == 1 ) {
                            this.id_punto_venta = r[0].data.id_punto_venta;
                            this.store.baseParams.id_punto_venta = r[0].data.id_punto_venta;
                            this.Cmp.id_forma_pago.store.baseParams.id_punto_venta = this.id_punto_venta;
                            //this.Cmp.id_forma_pago2.store.baseParams.id_punto_venta = this.id_punto_venta;
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
                                                //this.Cmp.id_forma_pago2.store.baseParams.id_punto_venta = this.id_punto_venta;
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
                /*{
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_boleto'
                    },
                    type:'Field',
                    form:true
                },*/
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
                /*{
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'ids_seleccionados'
                    },
                    type:'Field',
                    form:true
                },*/
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'tc'
                    },
                    type:'NumberField',
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
                    form:false
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
                    form:false
                },
                {
                    config:{
                        name: 'localizador',
                        fieldLabel: 'PNR',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 120,
                        maxLength:10,
                        minLength:10,
                        enableKeyEvents:true,
                        renderer : function(value, p, record) {
                            if (record.data['mensaje_error'] != '') {
                                return String.format('<div title="Error"><b><font color="blue">{0}</font></b></div>', value);

                            } else {
                                return String.format('{0}', value);
                            }


                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'localizador',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true,
                    bottom_filter: true
                },

                {
                    config:{
                        name: 'boletos',
                        fieldLabel: 'Boletos a Pagar',
                        anchor: '80%',
                        gwidth: 200,
                        readOnly:true,
                        renderer : function(value, p, record) {
                            if (record.data['mensaje_error'] != '') {
                                return String.format('<div title="Error"><b><font color="green">{0}</font></b></div>', value);

                            } else {
                                return String.format('{0}', value);
                            }


                        }
                    },
                    type:'TextArea',
                    id_grupo:2,
                    filters:{pfiltro:'boletos',type:'string'},
                    bottom_filter: true,
                    grid:true,
                    form:true
                },
                /*{
                    config:{
                        name: 'tiene_conjuncion',
                        fieldLabel: 'Tiene Conjuncion',
                        anchor: '80%',
                        checked:false

                    },
                    type:'Checkbox',
                    id_grupo:0,
                    grid:false,
                    form:true
                },*/

                /*{
                    config:{
                        name: 'nro_boleto_conjuncion',
                        fieldLabel: 'Conjuncion : 930-',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 120,
                        maxLength:10,
                        minLength:10
                    },
                    type:'TextField',
                    id_grupo:0,
                    grid:false,
                    form:true
                },*/

                {
                    config:{
                        name: 'pasajeros',
                        fieldLabel: 'Pasajeros',
                        anchor: '100%',
                        gwidth: 250,
                        readOnly:true
                    },
                    type:'TextField',
                    filters:{pfiltro:'pasajeros',type:'string'},
                    id_grupo:0,
                    bottom_filter: true,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'total',
                        fieldLabel: 'Total PNR',
                        anchor: '80%',
                        gwidth: 125	,
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
                        name: 'moneda',
                        fieldLabel: 'Moneda de Emision',
                        anchor: '80%',
                        gwidth: 150,
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
                        name: 'fecha_emision',
                        fieldLabel: 'Fecha Emision',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'bol.fecha_emision',type:'date'},
                    grid:true,
                    form:false
                },
                /*{
                    config:{
                        name: 'ruta_completa',
                        fieldLabel: 'Ruta',
                        anchor: '80%',
                        gwidth: 120,
                        readOnly:true

                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.ruta_completa',type:'string'},
                    id_grupo:0,
                    grid:false,
                    form:true
                },*/

                /*{
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
                    id_grupo: 0,
                    grid: false,
                    form: true
                },*/

                /*{
                    config:{
                        name: 'estado',
                        fieldLabel: 'Estado',
                        gwidth: 100,
                        readOnly:true
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.estado',type:'string'},
                    grid:true,
                    id_grupo:0,
                    form:true
                },*/
                /*{
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
                    id_grupo:0,
                    grid:true,
                    form:true
                },*/
                {
                    config: {
                        name: 'id_forma_pago',
                        fieldLabel: 'Forma de Pago1',
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
                            baseParams: {par_filtro: 'forpa.nombre#mon.codigo_internacional',fp_ventas:'si'}
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
                        fieldLabel: 'Monto a Pagar 1',
                        allowBlank:false,
                        anchor: '80%',
                        allowDecimals:true,
                        decimalPrecision:2,
                        allowNegative : false,
                        disabled:true,
                        gwidth: 125
                    },
                    type:'NumberField',
                    id_grupo:1,
                    valorInicial:0,
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
                {
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
                },

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
                    form: false
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
                    form:false
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
                    form:false
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
                    form:false
                },
                {
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
                    form:false
                },
                /*{
                    config:{
                        name: 'cupones',
                        fieldLabel: 'Cupones',
                        gwidth: 100

                    },
                    type:'NumberField',
                    filters:{pfiltro:'bol.cupones',type:'numeric'},
                    grid:true,
                    form:false
                },*/
                /*{
                    config:{
                        name: 'codigo_noiata',
                        fieldLabel: 'Cod. Noiata',
                        gwidth: 100
                    },
                    type:'TextField',

                    grid:true,
                    form:false
                },*/

                /*{
                    config:{
                        name: 'codigo_agencia',
                        fieldLabel: 'agt',
                        gwidth: 100
                    },
                    type:'TextField',
                    grid:true,
                    form:false
                },*/
                /*{
                    config:{
                        name: 'nombre_agencia',
                        fieldLabel: 'Agencia',
                        gwidth: 120
                    },
                    type:'TextField',
                    grid:true,
                    form:false
                },*/
                {
                    config:{
                        name: 'neto',
                        fieldLabel: 'Neto',
                        gwidth: 100
                    },
                    type:'NumberField',
                    filters:{pfiltro:'bol.neto',type:'numeric'},
                    grid:true,
                    form:false
                },
                /*{
                    config:{
                        name: 'tipopax',
                        fieldLabel: 'Tipo Pasajero',
                        gwidth: 110
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.tipopax',type:'string'},
                    grid:true,
                    form:false
                },*/


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
            ActSave:'../../sis_obingresos/control/Boleto/modificarFpPNRBoleto',
            //ActDel:'../../sis_obingresos/control/Boleto/eliminarBoleto',
            ActList:'../../sis_obingresos/control/Boleto/listarPNRBoleto',
            id_store:'id_boleto',
            fields: [
                {name:'localizador', type: 'varchar'},
                {name:'total', type: 'numeric'},
                {name:'comision', type: 'numeric'},
                {name:'liquido', type: 'numeric'},
                {name:'id_moneda_boleto', type: 'numeric'},
                {name:'moneda', type: 'string'},
                {name:'neto', type: 'numeric'},
                {name:'origen', type: 'string'},
                {name:'destino', type: 'string'},
                {name:'estado', type: 'string'},
                {name:'fecha_emision', type: 'date',dateFormat:'Y-m-d'},
                {name:'vuelo_retorno', type: 'string'},
                {name:'id_forma_pago', type: 'numeric'},
                {name:'forma_pago', type: 'string'},
                {name:'monto_forma_pago', type: 'string'},
                {name:'id_forma_pago2', type: 'string'},
                {name:'forma_pago2', type: 'string'},
                {name:'monto_forma_pago2', type: 'string'},
                {name:'boletos', type: 'string'},
                {name:'pasajeros', type: 'string'}
            ],
            sortInfo:{
                field: 'fecha_emision',
                direction: 'DESC'
            },
            arrayDefaultColumHidden:['estado_reg','usuario_ai', 'fecha_reg','fecha_mod','usr_reg','usr_mod','estado','neto'],
            /*rowExpander: new Ext.ux.grid.RowExpander({
                tpl : new Ext.Template(
                    '<br>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha de Emision:&nbsp;&nbsp;</b> {fecha_emision:date("d/m/Y")}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b># Cupones:&nbsp;&nbsp;</b> {cupones}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Nombre Agencia:&nbsp;&nbsp;</b> {nombre_agencia}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Codigo NoIata:&nbsp;&nbsp;</b> {codigo_noiata}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Codigo Agencia:&nbsp;&nbsp;</b> {codigo_agencia}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Neto:&nbsp;&nbsp;</b> {neto}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Comision AGT:&nbsp;&nbsp;</b> {comision}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Tipo de Pasajero:&nbsp;&nbsp;</b> {tipopax}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha de Registro:&nbsp;&nbsp;</b> {fecha_reg:date("d/m/Y")}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha Ult. Modificación:&nbsp;&nbsp;</b> {fecha_mod:date("d/m/Y")}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Creado por:&nbsp;&nbsp;</b> {usr_reg}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Modificado por:&nbsp;&nbsp;</b> {usr_mod}</p><br>'
                )
            }),*/
            bsave:false,
            bnew:false,
            bedit:false,
            bdel:false,

            manejoComponentesFp1 : function (id_fp1,codigo_fp1){
                console.log(id_fp1);
                //forma de pago 1
                if (id_fp1 == 0) {
                    this.Cmp.id_forma_pago.setDisabled(true);
                    this.Cmp.monto_forma_pago.setDisabled(true);
                    this.ocultarComponente(this.Cmp.numero_tarjeta);
                    this.ocultarComponente(this.Cmp.codigo_tarjeta);
                    this.ocultarComponente(this.Cmp.ctacte);
                    this.Cmp.numero_tarjeta.allowBlank = true;
                    this.Cmp.codigo_tarjeta.allowBlank = true;
                    this.Cmp.ctacte.allowBlank = true;
                } else {
                    this.Cmp.id_forma_pago.setDisabled(false);
                    this.Cmp.monto_forma_pago.setDisabled(false);
                    if (codigo_fp1.startsWith("CC") ||
                        codigo_fp1.startsWith("SF")) {
                        this.ocultarComponente(this.Cmp.ctacte);
                        this.Cmp.ctacte.reset();
                        this.mostrarComponente(this.Cmp.numero_tarjeta);
                        this.mostrarComponente(this.Cmp.codigo_tarjeta);
                        this.Cmp.numero_tarjeta.allowBlank = false;
                        this.Cmp.codigo_tarjeta.allowBlank = false;
                        this.Cmp.ctacte.allowBlank = true;
                        //tarjeta de credito
                    } else if (codigo_fp1.startsWith("CT")) {
                        //cuenta corriente
                        this.ocultarComponente(this.Cmp.numero_tarjeta);
                        this.ocultarComponente(this.Cmp.codigo_tarjeta);
                        this.Cmp.numero_tarjeta.reset();
                        this.Cmp.codigo_tarjeta.reset();
                        this.mostrarComponente(this.Cmp.ctacte);
                        this.Cmp.numero_tarjeta.allowBlank = true;
                        this.Cmp.codigo_tarjeta.allowBlank = true;
                        this.Cmp.ctacte.allowBlank = false;
                    } else {
                        this.ocultarComponente(this.Cmp.numero_tarjeta);
                        this.ocultarComponente(this.Cmp.codigo_tarjeta);
                        this.ocultarComponente(this.Cmp.ctacte);
                        this.Cmp.numero_tarjeta.reset();
                        this.Cmp.codigo_tarjeta.reset();
                        this.Cmp.ctacte.reset();
                        this.Cmp.numero_tarjeta.allowBlank = true;
                        this.Cmp.codigo_tarjeta.allowBlank = true;
                        this.Cmp.ctacte.allowBlank = true;
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
                        this.ocultarComponente(this.Cmp.ctacte2);
                        this.Cmp.numero_tarjeta2.allowBlank = true;
                        this.Cmp.codigo_tarjeta2.allowBlank = true;
                        this.Cmp.ctacte2.allowBlank = true;
                        this.Cmp.numero_tarjeta2.reset();
                        this.Cmp.codigo_tarjeta2.reset();
                        this.Cmp.ctacte2.reset();
                    } else {
                        this.Cmp.id_forma_pago2.setDisabled(false);
                        this.Cmp.monto_forma_pago2.setDisabled(false);
                        if (codigo_fp2.startsWith("CC") ||
                            codigo_fp2.startsWith("SF")) {
                            //tarjeta de credito
                            this.ocultarComponente(this.Cmp.ctacte2);
                            this.Cmp.ctacte2.reset();
                            this.mostrarComponente(this.Cmp.numero_tarjeta2);
                            this.mostrarComponente(this.Cmp.codigo_tarjeta2);
                            this.Cmp.numero_tarjeta2.allowBlank = false;
                            this.Cmp.codigo_tarjeta2.allowBlank = false;
                            this.Cmp.ctacte2.allowBlank = true;

                        } else if (codigo_fp2.startsWith("CT")) {
                            //cuenta corriente
                            this.ocultarComponente(this.Cmp.numero_tarjeta2);
                            this.ocultarComponente(this.Cmp.numero_tarjeta2);
                            this.Cmp.numero_tarjeta2.reset();
                            this.Cmp.numero_tarjeta2.reset();
                            this.mostrarComponente(this.Cmp.ctacte2);
                            this.Cmp.numero_tarjeta2.allowBlank = true;
                            this.Cmp.numero_tarjeta2.allowBlank = true;
                            this.Cmp.ctacte2.allowBlank = false;
                        } else {
                            this.ocultarComponente(this.Cmp.numero_tarjeta2);
                            this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                            this.ocultarComponente(this.Cmp.ctacte2);
                            this.Cmp.numero_tarjeta2.allowBlank = true;
                            this.Cmp.codigo_tarjeta2.allowBlank = true;
                            this.Cmp.ctacte2.allowBlank = true;
                            this.Cmp.numero_tarjeta2.reset();
                            this.Cmp.codigo_tarjeta2.reset();
                            this.Cmp.ctacte2.reset();
                        }
                    }
                } else {
                    this.ocultarComponente(this.Cmp.numero_tarjeta2);
                    this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                    this.ocultarComponente(this.Cmp.ctacte2);
                    this.Cmp.numero_tarjeta2.allowBlank = true;
                    this.Cmp.codigo_tarjeta2.allowBlank = true;
                    this.Cmp.ctacte2.allowBlank = true;
                    this.Cmp.id_forma_pago2.setDisabled(true);
                    this.Cmp.monto_forma_pago2.setDisabled(true);
                }
            },

            iniciarEventos : function () {


                /*this.Cmp.nro_boleto.on('keyup',function(){
                    console.log('llega');
                    if (this.Cmp.nro_boleto.getValue().length == 10) {
                        Phx.CP.loadingShow();

                        Ext.Ajax.request({
                            url:'../../sis_obingresos/control/Boleto/getBoletoServicio',
                            params: {'nro_boleto':this.Cmp.nro_boleto.getValue(),
                                'id_punto_venta':this.id_punto_venta},
                            success:this.successGetBoletoServicio,
                            failure: this.conexionFailure,
                            timeout:this.timeout,
                            scope:this
                        });
                    }

                },this);*/

                this.Cmp.monto_forma_pago.on('change',function(field,newValue,oldValue){
                    console.log('gonzalo');
                    console.log(oldValue);
                    console.log(newValue);
                    if (newValue > oldValue) {
                        this.Cmp.id_forma_pago2.setDisabled(false);
                        this.Cmp.monto_forma_pago2.setDisabled(false);
                    }
                },this);

                /*this.Cmp.comision.on('change',function(field,newValue,oldValue) {


                    if (this.Cmp.id_forma_pago2.getValue() && this.getMontoMonBol(this.Cmp.monto_forma_pago2.getValue(),this.Cmp.moneda_fp2.getValue()) > newValue) {

                        this.Cmp.id_forma_pago2.fireEvent('select', {   combo:this.Cmp.id_forma_pago2});

                    } else if (this.Cmp.id_forma_pago.getValue() && this.getMontoMonBol(this.Cmp.monto_forma_pago.getValue(),this.Cmp.moneda_fp1.getValue()) > newValue) {

                        this.Cmp.id_forma_pago.fireEvent('select', {   combo:this.Cmp.id_forma_pago});
                    }

                },this);*/

                this.Cmp.id_forma_pago.on('select', function (combo,record){
                    if (record) {
                        this.Cmp.moneda_fp1.setValue(record.data.desc_moneda);
                        this.manejoComponentesFp1(record.data.id_forma_pago,record.data.codigo);
                    } else {
                        this.manejoComponentesFp1(this.Cmp.id_forma_pago.getValue(),this.Cmp.codigo_forma_pago.getValue());
                    }


                    if (this.grupo == 'no') {
                        var monto_pagado_fp2 = this.getMontoMonBol(this.Cmp.monto_forma_pago2.getValue(),this.Cmp.moneda_fp2.getValue());

                        if (monto_pagado_fp2 > -1) {

                            //Si la forma de pago y el boleto estan en la misma moneda
                            if (this.Cmp.moneda.getValue() == this.Cmp.moneda_fp1.getValue()){
                                this.Cmp.monto_forma_pago.setValue(this.Cmp.total.getValue() - monto_pagado_fp2 - this.Cmp.comision.getValue());

                            }
                            //Si el boleto esta en usd y la forma de pago es distinta a usd y la forma de pago es igual a la moneda de la sucursal
                            else if (this.Cmp.moneda.getValue() == 'USD' && this.Cmp.moneda_fp1.getValue() == this.Cmp.moneda_sucursal.getValue()) {
                                //convertir de  dolares a moneda sucursal(multiplicar)
                                this.Cmp.monto_forma_pago.setValue(this.round(((this.Cmp.total.getValue()- monto_pagado_fp2 - this.Cmp.comision.getValue())*this.Cmp.tc.getValue()),2));

                                //Si el boleto esta en moneda sucursal y la forma de pago es usd y la moneda de la sucursales distinta a usd
                            } else if (this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && this.Cmp.moneda_fp1.getValue() == 'USD') {
                                //convertir de  moneda sucursal a dolares(dividir)
                                this.Cmp.monto_forma_pago.setValue(this.round(((this.Cmp.total.getValue()- monto_pagado_fp2 - this.Cmp.comision.getValue())/this.Cmp.tc.getValue()),2));

                            } else {
                                this.Cmp.monto_forma_pago.setValue(0);

                            }
                        } else {
                            this.Cmp.monto_forma_pago.setValue(0);
                        }
                    } else {
                        this.calculoFp1Grupo(record);
                    }
                },this);

                /*this.Cmp.id_forma_pago2.on('select', function (combo,record) {
                    if (record) {
                        this.Cmp.moneda_fp2.setValue(record.data.desc_moneda);
                        this.manejoComponentesFp2(record.data.id_forma_pago,record.data.codigo);
                    } else {
                        this.manejoComponentesFp2(this.Cmp.id_forma_pago2.getValue(),this.Cmp.codigo_forma_pago2.getValue());
                    }

                    if (this.grupo == 'no') {
                        var monto_pagado_fp1 = this.getMontoMonBol(this.Cmp.monto_forma_pago.getValue(),this.Cmp.moneda_fp1.getValue());

                        if (monto_pagado_fp1 > -1) {
                            //Si la forma de pago y el boleto estan en la misma moneda
                            if (this.Cmp.moneda.getValue() == this.Cmp.moneda_fp2.getValue()){
                                this.Cmp.monto_forma_pago2.setValue(this.Cmp.total.getValue() - this.Cmp.comision.getValue()- monto_pagado_fp1);
                            }
                            //Si el boleto esta en usd y la forma de pago es distinta a usd y la forma de pago es igual a la moneda de la sucursal
                            else if (this.Cmp.moneda.getValue() == 'USD' && this.Cmp.moneda_fp2.getValue() == this.Cmp.moneda_sucursal.getValue()) {
                                //convertir de  dolares a moneda sucursal(multiplicar)
                                this.Cmp.monto_forma_pago2.setValue(this.round(((this.Cmp.total.getValue()- monto_pagado_fp1 - this.Cmp.comision.getValue())*this.Cmp.tc.getValue()),2));
                                //Si el boleto esta en moneda sucursal y la forma de pago es usd y la moneda de la sucursales distinta a usd
                            } else if (this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && this.Cmp.moneda_fp2.getValue() == 'USD') {
                                //convertir de  moneda sucursal a dolares(dividir)
                                this.Cmp.monto_forma_pago2.setValue(this.round(((this.Cmp.total.getValue()-monto_pagado_fp1 - this.Cmp.comision.getValue())/this.Cmp.tc.getValue()),2));
                            } else {
                                this.Cmp.monto_forma_pago2.setValue(0);
                            }
                        } else {
                            this.Cmp.monto_forma_pago2.setValue(0);
                        }
                    } else {
                        this.calculoFp2Grupo(record);
                    }
                },this);*/


            },

            calculoFp1Grupo : function (record) {
                this.moneda_grupo_fp1 = record.data.desc_moneda;

                if (this.moneda_grupo_fp2 == '') {
                    this.Cmp.monto_forma_pago.setValue(this.total_grupo[record.data.desc_moneda]);
                } else if (this.moneda_grupo_fp2 == this.moneda_grupo_fp1) {
                    this.Cmp.monto_forma_pago.setValue(this.total_grupo[record.data.desc_moneda] - this.Cmp.monto_forma_pago2.getValue());
                } else {
                    if (this.moneda_grupo_fp2 == 'USD') {
                        this.Cmp.monto_forma_pago.setValue(this.total_grupo[record.data.desc_moneda] - this.roundMenor(this.Cmp.monto_forma_pago2.getValue() * this.tc_grupo , 2));
                    } else {
                        this.Cmp.monto_forma_pago.setValue(this.total_grupo[record.data.desc_moneda] - this.roundMenor(this.Cmp.monto_forma_pago2.getValue() / this.tc_grupo , 2));
                    }
                }

            },

            onPagado : function () {
                //this.onButtonEdit();
                /*this.ocultarGrupo(0);
                this.ocultarGrupo(2);*/
                this.onGrupo();
                /*var rec = this.sm.getSelected();
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url:'../../sis_obingresos/control/Boleto/cambiaEstadoBoleto',
                    params: {'id_boleto':rec.data.id_boleto,
                        'accion':'pagado'},
                    success:this.successSave,
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });*/

            },

            onTraerBoletos : function () {
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url:'../../sis_obingresos/control/Boleto/traerBoletos',
                    params: {id_punto_venta: this.id_punto_venta},
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

            successSave:function(resp){
                var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));

                Phx.vista.Boleto.superclass.successSave.call(this, resp);
                if (objRes.ROOT.datos.alertas) {
                    Ext.Msg.alert('Error en boleto', objRes.ROOT.datos.alertas);
                }

            },

            onGrupo : function () {
                Phx.vista.PNRBoleto.superclass.onButtonEdit.call(this);
                /*this.grupo = 'si';
                var seleccionados = this.sm.getSelections();
                this.total_grupo = new Object;
                this.total_grupo['USD'] = 0;
                this.total_grupo[seleccionados[0].data.moneda_sucursal] = 0;
                */

                /*
                for (var i = 0 ; i< seleccionados.length;i++) {
                    if (i == 0) {
                        this.Cmp.ids_seleccionados.setValue(seleccionados[i].data.id_boleto);
                        this.Cmp.boletos.setValue('930' + seleccionados[i].data.nro_boleto);
                    } else {
                        this.Cmp.ids_seleccionados.setValue(this.Cmp.ids_seleccionados.getValue() + ',' + seleccionados[i].data.id_boleto);
                        this.Cmp.boletos.setValue(this.Cmp.boletos.getValue() + ', 930' + seleccionados[i].data.nro_boleto);
                    }
                    if (seleccionados[i].data.moneda_sucursal == seleccionados[i].data.moneda) {
                        this.total_grupo[seleccionados[0].data.moneda_sucursal] += (parseFloat(seleccionados[i].data.total) - parseFloat(seleccionados[i].data.comision));
                        this.total_grupo['USD'] += this.round((seleccionados[i].data.total - seleccionados[i].data.comision) / seleccionados[i].data.tc , 2);
                    } else if (seleccionados[i].data.moneda == 'USD') {

                        this.total_grupo[seleccionados[0].data.moneda_sucursal] += this.round((seleccionados[i].data.total - seleccionados[i].data.comision)* seleccionados[i].data.tc , 2);
                        this.total_grupo['USD'] += (parseFloat(seleccionados[i].data.total) - parseFloat(seleccionados[i].data.comision));
                    } else {
                        alert('No se puede calcular la forma de pago ya que la moneda de un boleto no es la moenda de la sucursal ni dolares americanos');
                        return;
                    }
                }
                */
                //habilitamos el formulario
                this.ocultarGrupo(2);
                this.ocultarGrupo(0);
                this.Cmp.id_forma_pago.setDisabled(false);
                this.Cmp.monto_forma_pago.setDisabled(false);
                //this.Cmp.nro_boleto.allowBlank = true;
                this.moneda_grupo_fp1 = '';
                this.moneda_grupo_fp2 = '';
                //this.tc_grupo = seleccionados[0].data.tc;

            },

            tabsouth:[
                {
                    url:'../../../sis_obingresos/vista/boleto/PNRBoletoDetalle.php',
                    title:'Boletos',
                    height:'40%',
                    cls:'PNRBoletoDetalle'
                }

            ],
            Grupos:[{
                layout: 'column',
                items:[
                    {
                        xtype:'fieldset',
                        layout: 'form',
                        border: true,
                        title: 'Datos Boleto/Comision',
                        bodyStyle: 'padding:0 10px 0;',
                        columnWidth: 0.5,
                        items:[],
                        id_grupo:0,
                        collapsible:true
                    },
                    {
                        xtype:'fieldset',
                        layout: 'form',
                        border: true,
                        title: 'Boletos',
                        bodyStyle: 'padding:0 10px 0;',
                        columnWidth: 0.5,
                        items:[],
                        id_grupo:2,
                        collapsible:true
                    },
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
            }],
            preparaMenu:function()
            {	var rec = this.sm.getSelected();

                Phx.vista.PNRBoleto.superclass.preparaMenu.call(this);

            },
            liberaMenu:function()
            {

                Phx.vista.PNRBoleto.superclass.liberaMenu.call(this);
            },
            round : function(value, decimals) {
                return Math.ceil(value*100)/100;
            },
            roundMenor : function(value, decimals) {
                return Math.floor(value*100)/100;
            }
        }
    )
</script>

