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
                this.grupo = 'no';
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
                this.store.baseParams.pes_estado = 'no_revisados';
                this.store.baseParams.todos = 'no';
                this.init();

                this.addButton('btnAnularBoleto',
                    {
                        //text: 'Anular Boleto',
                        //iconCls: 'block',
                        text: '<i class="fa fa-file-excel-o fa-3x"></i> Anular', /*iconCls:'' ,*/
                        grupo: this.grupoDateFin,
                        disabled: true,
                        handler: this.anularBoleto,
                        tooltip: '<b>Anular</b><br/>Anular Boleto'
                    }
                );

                this.addButton('btnPagarGrupo',
                    {
                        text: 'Pagar Grupo',
                        iconCls: 'bmoney',
                        disabled: true,
                        handler: this.onGrupo,
                        tooltip: 'Paga todos los boletos seleccionados'
                    }
                );

                this.addButton('btnBoletosTodos',
                    {
                        text: 'Traer Todos Boletos',
                        iconCls: 'breload2',
                        disabled: false,
                        handler: this.onTraerBoletosTodos,
                        tooltip: 'Traer todos boletos vendidos'
                    }
                );

                this.campo_fecha = new Ext.form.DateField({
                    name: 'fecha_reg',
                    grupo: this.grupoDateFin,
                    fieldLabel: 'Fecha',
                    allowBlank: false,
                    anchor: '60%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    hidden : false
                });

                this.punto_venta = new Ext.form.Label({
                    name: 'punto_venta',
                    grupo: this.grupoDateFin,
                    fieldLabel: 'P.V.',
                    readOnly:true,
                    anchor: '150%',
                    gwidth: 150,
                    format: 'd/m/Y',
                    hidden : false,
                    style: 'font-size: 170%; font-weight: bold; background-image: none;'
                });

                this.addButton('btnVoucherCode',
                    {
                        grupo: [1],
                        text: 'Voucher Code',
                        iconCls: 'bdocuments',
                        disabled: true,
                        handler: this.onButtonVoucherCode,
                        tooltip: 'Voucher Code'
                    }
                );
                this.getBoton('btnVoucherCode').setVisible(false);
                this.tbar.addField(this.campo_fecha);
                this.tbar.addField(this.punto_venta);
                var datos_respuesta = JSON.parse(response.responseText);
                var fecha_array = datos_respuesta.datos.fecha.split('/');
                this.campo_fecha.setValue(new Date(fecha_array[2],parseInt(fecha_array[1]) - 1,fecha_array[0]));
                this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('Ymd');

                this.campo_fecha.on('select',function(value){
                    this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('Ymd');
                    this.load();
                },this);

                //this.addButton('cerrar',{grupo:[0],text:'Cerrar Caja',iconCls: 'block',disabled:false,handler:this.preparaCerrarCaja,tooltip: '<b>Cerrar la Caja</b>'});

                //this.store.baseParams.estado = 'borrador';
                this.iniciarEventos();
                this.finCons = true;
                this.seleccionarPuntoVentaSucursal();
                this.grid.addListener('cellclick', this.oncellclick,this);
                this.bloquearOrdenamientoGrid();

            },

            gruposBarraTareas:[{name:'no_revisados',title:'<H1 align="center"><i class="fa fa-eye"></i> No Revisados</h1>',grupo:0,height:0},
                {name:'revisados',title:'<H1 align="center"><i class="fa fa-eye"></i> Revisados</h1>',grupo:1,height:0}
            ],

            actualizarSegunTab: function(name, indice){
                if(this.finCons){
                    this.store.baseParams.pes_estado = name;
                    this.load({params:{start:0, limit:this.tam_pag}});
                }
            },

            beditGroups: [0],
            bdelGroups:  [0],
            bactGroups:  [0,1],
            btestGroups: [0],
            bexcelGroups: [0,1],
            grupoDateFin: [0,1],

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
                            this.punto_venta.setText(r[0].data.nombre);
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
                                                this.punto_venta.setText(combo2.lastSelectionText)
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
                        name: 'id_boleto_amadeus'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'ids_seleccionados'
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
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'tc'
                    },
                    type:'TextField',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'ffid'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'voucher_code'
                    },
                    type:'Field',
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
                    id_grupo: 3,
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
                    id_grupo: 3,
                    grid: true,
                    form: true
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
                        anchor: '100%',
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
                        name: 'moneda_boletos',
                        fieldLabel: 'Moneda Boletos',
                        anchor: '100%',
                        gwidth: 80,
                        readOnly:true
                    },
                    type:'TextField',
                    id_grupo:2,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'monto_total_boletos',
                        fieldLabel: 'Importe Total',
                        anchor: '100%',
                        gwidth: 80,
                        readOnly:true
                    },
                    type:'TextField',
                    id_grupo:2,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'monto_total_neto',
                        fieldLabel: 'Importe Neto',
                        anchor: '100%',
                        disabled : true,
                        gwidth: 80,
                        readOnly:true
                    },
                    type:'TextField',
                    id_grupo:3,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'monto_total_comision',
                        fieldLabel: 'Importe Comision',
                        anchor: '100%',
                        gwidth: 80,
                        readOnly:true
                    },
                    type:'TextField',
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
                        gwidth: 60,
                        renderer : function(value, p, record) {
                            if ((record.data['neto'] == 30 && record.data['moneda'] == 'BOB'  || record.data['neto'] == 60 && record.data['moneda'] == 'BOB' ) ||
                                (record.data['neto'] == 40 && record.data['moneda'] == 'USD'  || record.data['neto'] == 80 && record.data['moneda'] == 'USD' )||
                                (record.data['neto'] == 70 && record.data['moneda'] == 'USD'  || record.data['neto'] == 140 && record.data['moneda'] == 'USD')||
                                (record.data['neto'] == 130 && record.data['moneda'] == 'USD' || record.data['neto'] == 260 && record.data['moneda'] == 'USD')){
                                return '<tpl for="."><p><font color="black">' + record.data['localizador'] + '</font><p><b><font color="#8b008b">Voucher</font></p></tpl>';
                            }else{
                                return '<tpl for="."><p><font color="black">' + record.data['localizador'] + '</tpl>';

                            }
                            return '<tpl for="."><p><font color="black">' + record.data['localizador'] + '</tpl>';
                        }
                    },
                    type:'TextField',
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

                            return '<tpl for="."><p><font color="red">' + record.data['nro_boleto'] + '</tpl>';
                        }
                    },
                    type:'TextField',
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
                        anchor: '90%',
                        gwidth: 130,
                        readOnly:true
                    },
                    type:'TextField',
                    id_grupo:0,
                    grid:true,
                    form:true,
                    bottom_filter: true
                },
                {
                    config:{
                        name: 'forma_pago_amadeus',
                        fieldLabel: 'Pago Amadeus',
                        gwidth: 120,
                        anchor: '90%',
                        disabled: true,
                        readOnly:true
                    },
                    type:'TextField',
                    grid:true,
                    id_grupo:0,
                    form:true
                },
                {
                    config:{
                        name: 'moneda',
                        fieldLabel: 'Moneda',
                        disabled: true,
                        anchor: '90%',
                        gwidth: 70,
                        readOnly:true

                    },
                    type:'TextField',
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'total',
                        fieldLabel: 'Total M/L',
                        disabled: true,
                        anchor: '90%',
                        gwidth: 70	,
                        readOnly:true
                    },
                    type:'NumberField',
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'comision',
                        fieldLabel: 'Comision',
                        disabled: true,
                        anchor: '90%',
                        gwidth: 70	,
                        readOnly:true
                    },
                    type:'NumberField',
                    id_grupo:0,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'total_moneda_extranjera',
                        fieldLabel: 'Total M/E',
                        disabled: true,
                        anchor: '90%',
                        gwidth: 70	,
                        readOnly:true
                    },
                    type:'NumberField',
                    id_grupo:0,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'neto',
                        fieldLabel: 'Neto',
                        disabled: true,
                        anchor: '90%',
                        gwidth: 70
                    },
                    type:'NumberField',
                    id_grupo: 3,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'comision',
                        fieldLabel: 'Comisión AGT M/L',
                        allowBlank:true,
                        anchor: '90%',
                        disabled:true,
                        gwidth: 40
                    },
                    type:'NumberField',
                    valor_inicial:0,
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'comision_moneda_extranjera',
                        fieldLabel: 'Comisión AGT M/E',
                        allowBlank:true,
                        anchor: '90%',
                        disabled:true,
                        gwidth: 40
                    },
                    type:'NumberField',
                    valor_inicial:0,
                    id_grupo:0,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'agente_venta',
                        fieldLabel: 'Agente Venta',
                        disabled: true,
                        anchor: '100%',
                        gwidth: 270
                    },
                    type:'TextField',
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'codigo_agente',
                        fieldLabel: 'Codigo Agente',
                        anchor: '70%',
                        disabled: true,
                        gwidth: 100
                    },
                    type:'TextField',
                    id_grupo:3,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'tipo_comision',
                        fieldLabel: 'Tipo Comision',
                        qtip: 'Si tiene comision',
                        allowBlank: false,
                        emptyText: 'Tipo...',
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'local',
                        gwidth: 100,
                        anchor: '100%',
                        store: ['ninguno','nacional','internacional']
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    filters:{
                        type: 'list',
                        pfiltro:'bol.tipo_comision',
                        options: ['ninguno','nacional','internacional'],
                    },
                    valorInicial: 'ninguno',
                    grid:false,
                    form:true
                },
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
                        tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p><b><p>Codigo:<font color="green">{codigo}</font></b></p><p><b>Moneda:<font color="red">{desc_moneda}</font></b></p> </div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        gwidth: 150,
                        listWidth:250,
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
                        name: 'cambio',
                        fieldLabel: 'Cambio M/L',
                        allowBlank:true,
                        anchor: '80%',
                        allowDecimals:true,
                        decimalPrecision:2,
                        allowNegative : false,
                        readOnly:true,
                        gwidth: 110,
                        style: 'background-color: #3cf251;  background-image: none;'
                    },
                    type:'NumberField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'cambio_moneda_extranjera',
                        fieldLabel: 'Cambio M/E',
                        allowBlank:true,
                        anchor: '80%',
                        allowDecimals:true,
                        decimalPrecision:2,
                        allowNegative : false,
                        readOnly:true,
                        gwidth: 110,
                        style: 'background-color: #3cf251; background-image: none;'
                    },
                    type:'NumberField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'monto_recibido_forma_pago',
                        fieldLabel: 'Importe Recibido Forma Pago',
                        allowBlank:false,
                        anchor: '80%',
                        allowDecimals:true,
                        decimalPrecision:2,
                        allowNegative : false,
                        disabled:false,
                        gwidth: 110,
                        style: 'background-color: #f2f23c;  background-image: none;'
                    },
                    type:'NumberField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'monto_forma_pago',
                        fieldLabel: 'Importe Forma Pago',
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
                ///modificado
                {
                    config:{
                        name: 'numero_tarjeta',
                        fieldLabel: 'No Tarjeta',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        minLength:15,
                        maxLength:20
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                ///nuevo
                {
                    config:{
                        name: 'mco',
                        fieldLabel: 'MCO',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        minLength:15,
                        maxLength:20
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'codigo_tarjeta',
                        fieldLabel: 'Codigo de Autorización 1',
                        allowBlank: true,
                        anchor: '80%',
                        minLength:6,
                        maxLength:6

                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:true,
                    form:true
                },
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
                            baseParams: {par_filtro: 'forpa.nombre#forpa.codigo#mon.codigo_internacional',sw_tipo_venta:'boletos'}
                        }),
                        valueField: 'id_forma_pago',
                        displayField: 'nombre',
                        gdisplayField: 'forma_pago2',
                        hiddenName: 'id_forma_pago',
                        anchor: '100%',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p><b><p>Codigo:<font color="green">{codigo}</font></b></p><p><b>Moneda:<font color="red">{desc_moneda}</font></b></p> </div></tpl>',
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
                        disabled:true,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['forma_pago2']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 4,
                    grid: true,
                    form: true
                },
                {
                    config:{
                        name: 'monto_forma_pago2',
                        fieldLabel: 'Importe Forma de Pago 2',
                        allowBlank:true,
                        anchor: '80%',
                        allowDecimals:true,
                        decimalPrecision:2,
                        allowNegative : false,
                        disabled:true,
                        gwidth: 125,
                        style: 'background-color: #f2f23c;  background-image: none;'
                    },
                    type:'NumberField',
                    id_grupo:4,
                    grid:true,
                    form:true
                },
                //modifcado
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
                    id_grupo:4,
                    grid:false,
                    form:true
                },
                ///nuevo
                {
                    config:{
                        name: 'mco2',
                        fieldLabel: 'MCO 2',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        minLength:15,
                        maxLength:20
                    },
                    type:'TextField',
                    id_grupo:4,
                    grid:true,
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
                    id_grupo:4,
                    grid:false,
                    form:true
                },
                {
                    config: {
                        name: 'id_auxiliar2',
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
                        gdisplayField: 'nombre_auxiliar2',
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
                            return String.format('{0}', record.data['nombre_auxiliar2']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 4,
                    grid: true,
                    form: true
                },
                {
                    config:{
                        name: 'fecha_emision',
                        fieldLabel: 'Fecha Emision',
                        anchor: '80%',
                        gwidth: 100,
                        disabled: true,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type:'DateField',
                    id_grupo:0,
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
                    grid:false,
                    form:false
                },
                {
                    config:{
                        name: 'nombre_agencia',
                        fieldLabel: 'Agencia',
                        gwidth: 120
                    },
                    type:'TextField',
                    grid:false,
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
            tam_pag:50,
        fwidth: '85%',
        fheight: '70%',
            title:'Boleto',
            ActSave:'../../sis_obingresos/control/Boleto/modificarBoletoAmadeusVenta',
            //ActDel:'../../sis_obingresos/control/Boleto/eliminarBoleto',
            ActList:'../../sis_obingresos/control/Boleto/traerBoletosJson',

            id_store:'id_boleto_amadeus',
            fields: [
                {name:'id_boleto_amadeus', type: 'numeric'},
                {name:'fecha_emision', type: 'date',dateFormat:'Y-m-d'},
                {name:'tipo_comision', type: 'string'},
                {name:'estado', type: 'string'},
                {name:'id_agencia', type: 'numeric'},
                {name:'moneda', type: 'string'},
                {name:'total', type: 'numeric'},
                {name:'total_moneda_extranjera', type: 'numeric'},
                {name:'pasajero', type: 'string'},
                {name:'id_moneda_boleto', type: 'numeric'},
                {name:'estado_reg', type: 'string'},
                {name:'codigo_agencia', type: 'string'},
                {name:'neto', type: 'numeric'},
                {name:'tc', type: 'numeric'},
                {name:'localizador', type: 'string'},
                {name:'monto_pagado_moneda_boleto', type: 'numeric'},
                {name:'monto_total_fp', type: 'numeric'},
                {name:'liquido', type: 'numeric'},
                {name:'comision', type: 'numeric'},
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
                {name:'codigo_tarjeta', type: 'string'},
                {name:'id_auxiliar', type: 'numeric'},
                {name:'nombre_auxiliar', type: 'string'},
                {name:'monto_forma_pago', type: 'numeric'},
                {name:'id_forma_pago2', type: 'numeric'},
                {name:'forma_pago2', type: 'string'},
                {name:'codigo_forma_pago2', type: 'string'},
                {name:'numero_tarjeta2', type: 'string'},
                {name:'codigo_tarjeta2', type: 'string'},
                {name:'id_auxiliar2', type: 'numeric'},
                {name:'nombre_auxiliar2', type: 'string'},
                {name:'monto_forma_pago2', type: 'numeric'},
                {name:'pais', type: 'string'},
                {name:'agente_venta', type: 'string'},
                {name:'codigo_agente', type: 'string'},
                {name:'moneda_sucursal', type: 'string'},
                {name:'moneda_fp1', type: 'string'},
                {name:'moneda_fp2', type: 'string'},
                {name:'voided', type: 'string'},
                {name:'ffid', type: 'string'},
                {name:'voucher_code', type: 'string'},
                {name:'mco', type: 'string'},
                {name:'mco2', type: 'string'}

            ],
            sortInfo:{
                field: 'nro_boleto',
                direction: 'DESC'
            },
            arrayDefaultColumHidden:['estado_reg','usuario_ai',
                'fecha_reg','fecha_mod','usr_reg','usr_mod','codigo_agencia','nombre_agencia','neto','comision'],

            bdel:false,
            bsave:false,
            bnew:false,
            bedit:false,

            iniciarEventos : function () {

                this.Cmp.monto_forma_pago.on('change',function(field,newValue,oldValue){
                    var valueOld = this.getMontoMonBol(oldValue, this.Cmp.moneda_fp1.getValue());
                    var valueNew = this.getMontoMonBol(newValue, this.Cmp.moneda_fp1.getValue());
                    if (valueNew < valueOld) {
                        this.Cmp.id_forma_pago2.setDisabled(false);
                        this.Cmp.monto_forma_pago2.setDisabled(false);
                    }
                },this);

                this.Cmp.monto_recibido_forma_pago.on('change',function(field,newValue,oldValue){
                    if(this.grupo=='no') {
                        if (this.Cmp.moneda.getValue() == this.Cmp.moneda_fp1.getValue()) {
                            console.log('monto_recibido, misma moneda de boleto, moneda forma de pago');
                            if (newValue > (this.Cmp.total.getValue() - this.Cmp.comision.getValue())) {
                                this.Cmp.monto_forma_pago.setValue(this.Cmp.total.getValue() - this.Cmp.comision.getValue());
                                if(this.Cmp.moneda_sucursal.getValue()=='USD') {
                                    this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                }else{
                                    if(this.Cmp.moneda_fp1.getValue()=='USD'){
                                        this.Cmp.cambio.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) * this.Cmp.tc.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                    }else{
                                        this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) / this.Cmp.tc.getValue());
                                    }
                                }
                            } else {
                                this.Cmp.monto_forma_pago.setValue(newValue);
                                this.Cmp.cambio.setValue(0);
                            }
                        } else if(this.Cmp.moneda.getValue() == 'USD' && this.Cmp.moneda_fp1.getValue() == this.Cmp.moneda_sucursal.getValue()){
                            console.log('monto_recibido, misma moneda sucursal y moneda forma de pago, boleto en usd');
                            if (newValue > (this.Cmp.total_moneda_extranjera.getValue() * this.Cmp.tc.getValue() - this.Cmp.comision_moneda_extranjera.getValue() * this.Cmp.tc.getValue())) {
                                this.Cmp.monto_forma_pago.setValue((this.Cmp.total_moneda_extranjera.getValue()* this.Cmp.tc.getValue() - this.Cmp.comision_moneda_extranjera.getValue()) * this.Cmp.tc.getValue());
                                //this.Cmp.monto_recibido_forma_pago.setValue((this.Cmp.total_moneda_extranjera.getValue() * this.Cmp.tc.getValue() - this.Cmp.comision_moneda_extranjera.getValue()) * this.Cmp.tc.getValue());
                                //this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());

                                if(this.Cmp.moneda_sucursal.getValue()=='USD') {
                                    this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                }else{
                                    if(this.Cmp.moneda_fp1.getValue()=='USD'){
                                        this.Cmp.cambio.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) * this.Cmp.tc.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                    }else{
                                        this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) / this.Cmp.tc.getValue());
                                    }
                                }

                            } else {
                                this.Cmp.monto_forma_pago.setValue(newValue);
                                this.Cmp.cambio.setValue(0);
                            }
                        }
                        else if(this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && this.Cmp.moneda_fp1.getValue() == 'USD'){
                            console.log('monto_recibido, misma moneda sucursal y moneda boleto, moneda forma de pago en usd');
                            if (newValue > (this.Cmp.total_moneda_extranjera.getValue() - this.Cmp.comision_moneda_extranjera.getValue())) {
                                this.Cmp.monto_forma_pago.setValue((this.Cmp.total.getValue() - this.Cmp.comision.getValue())/ this.Cmp.tc.getValue());
                                //this.Cmp.monto_recibido_forma_pago.setValue((this.Cmp.total.getValue() - this.Cmp.comision.getValue())/ this.Cmp.tc.getValue());
                                //this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                if(this.Cmp.moneda_sucursal.getValue()=='USD') {
                                    this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                }else{
                                    if(this.Cmp.moneda_fp1.getValue()=='USD'){
                                        this.Cmp.cambio.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) * this.Cmp.tc.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                    }else{
                                        this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) / this.Cmp.tc.getValue());
                                    }
                                }
                            } else {
                                this.Cmp.monto_forma_pago.setValue(newValue);
                                this.Cmp.cambio.setValue(0);
                            }
                        }
                        else {
                            this.Cmp.monto_forma_pago.setValue(0);
                            this.Cmp.cambio.setValue(0);
                        }
                        //this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                    }else{
                        var valueOld = this.Cmp.monto_forma_pago.getValue();
                        if (this.Cmp.moneda_fp1.getValue() !== 'USD') {
                            if (newValue > (this.total_grupo['total_boletos_'+this.Cmp.moneda_fp1.getValue()] - this.total_grupo['total_comision_'+this.Cmp.moneda_fp1.getValue()])) {
                                this.Cmp.monto_forma_pago.setValue(this.total_grupo['total_boletos_'+this.Cmp.moneda_fp1.getValue()] - this.total_grupo['total_comision_'+this.Cmp.moneda_fp1.getValue()]);
                                //this.Cmp.monto_recibido_forma_pago.setValue(this.total_grupo['total_boletos_'+this.Cmp.moneda_fp1.getValue()] - this.total_grupo['total_comision_'+this.Cmp.moneda_fp1.getValue()]);
                                //this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                if(this.Cmp.moneda_sucursal.getValue()=='USD') {
                                    this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                }else{
                                    if(this.Cmp.moneda_fp1.getValue()=='USD'){
                                        this.Cmp.cambio.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) * this.Cmp.tc.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                    }else{
                                        this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) / this.Cmp.tc.getValue());
                                    }
                                }
                            } else {
                                this.Cmp.monto_forma_pago.setValue(newValue);
                                this.Cmp.cambio.setValue(0);
                            }
                        } else {
                            if (newValue > (this.total_grupo['total_boletos_USD'] - this.total_grupo['total_comision_USD'])) {
                                this.Cmp.monto_forma_pago.setValue(this.total_grupo['total_boletos_USD'] - this.total_grupo['total_comision_USD']);
                                //this.Cmp.monto_recibido_forma_pago.setValue(this.total_grupo['total_boletos_USD'] - this.total_grupo['total_comision_USD']);
                                //this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                if(this.Cmp.moneda_sucursal.getValue()=='USD') {
                                    this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                }else{
                                    if(this.Cmp.moneda_fp1.getValue()=='USD'){
                                        this.Cmp.cambio.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) * this.Cmp.tc.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                    }else{
                                        this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                                        this.Cmp.cambio_moneda_extranjera.setValue((newValue - this.Cmp.monto_forma_pago.getValue()) / this.Cmp.tc.getValue());
                                    }
                                }
                            } else {
                                this.Cmp.monto_forma_pago.setValue(newValue);
                                this.Cmp.cambio.setValue(0);
                            }
                        }
                        var valueNew = this.Cmp.monto_forma_pago.getValue();
                        if (valueNew < valueOld) {
                            this.Cmp.id_forma_pago2.setDisabled(false);
                            this.Cmp.monto_forma_pago2.setDisabled(false);
                        }
                        //this.Cmp.cambio.setValue(newValue - this.Cmp.monto_forma_pago.getValue());
                    }
                },this);
                /*
                 this.Cmp.monto_forma_pago2.on('blur',function(field){
                 console.log(this);
                 console.log(this.Cmp);
                 this.Cmp.cambio.setValue((this.Cmp.monto_recibido_forma_pago.getValue() - this.Cmp.monto_forma_pago.getValue()) - newValue);
                 });*/

                this.Cmp.id_forma_pago.on('select', function (combo,record){
                    if (record) {
                        this.Cmp.moneda_fp1.setValue(record.data.desc_moneda);
                        this.manejoComponentesFp1(record.data.id_forma_pago,record.data.codigo);
                    } else {
                        this.manejoComponentesFp1(this.Cmp.id_forma_pago.getValue(),this.Cmp.codigo_forma_pago.getValue());
                    }
                    if (this.grupo == 'no') {

                        var monto_pagado_fp2 = this.getMontoMonBol(this.Cmp.monto_forma_pago2.getValue(), this.Cmp.moneda_fp2.getValue());

                        if (monto_pagado_fp2 > -1) {
                            //Si la forma de pago y el boleto estan en la misma moneda
                            if (this.Cmp.moneda.getValue() == this.Cmp.moneda_fp1.getValue()) {
                                console.log('id_forma_pago, misma moneda de boleto, moneda forma de pago');
                                this.Cmp.monto_forma_pago.setValue(this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2);
                                this.Cmp.monto_recibido_forma_pago.setValue(this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2);
                                //this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue()-this.Cmp.monto_forma_pago.getValue());
                            }
                            //Si el boleto esta en usd y la forma de pago es distinta a usd y la forma de pago es igual a la moneda de la sucursal
                            else if (this.Cmp.moneda.getValue() == 'USD' && this.Cmp.moneda_fp1.getValue() == this.Cmp.moneda_sucursal.getValue()) {
                                console.log('id_forma_pago, misma moneda sucursal y moneda forma de pago, boleto en usd');
                                //convertir de  dolares a moneda sucursal(multiplicar)
                                //convertir de  moneda sucursal a dolares(dividir)
                                this.Cmp.monto_forma_pago.setValue(this.round(((this.Cmp.total.getValue() -this.Cmp.comision.getValue() - monto_pagado_fp2) * this.Cmp.tc.getValue()), 2));
                                this.Cmp.monto_recibido_forma_pago.setValue(this.round(((this.Cmp.total.getValue() -this.Cmp.comision.getValue() - monto_pagado_fp2) * this.Cmp.tc.getValue()), 2));
                                //this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue()-this.Cmp.monto_forma_pago.getValue());

                                //Si el boleto esta en moneda sucursal y la forma de pago es usd y la moneda de la sucursales distinta a usd
                            } else if (this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && this.Cmp.moneda_fp1.getValue() == 'USD') {
                                console.log('id_forma_pago, misma moneda sucursal y moneda boleto, moneda forma de pago en usd');
                                this.Cmp.monto_forma_pago.setValue(this.round(((this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2) / this.Cmp.tc.getValue()), 2));
                                this.Cmp.monto_recibido_forma_pago.setValue(this.round(((this.Cmp.total.getValue() - this.Cmp.comision.getValue() - monto_pagado_fp2) / this.Cmp.tc.getValue()), 2));
                                //this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue()-this.Cmp.monto_forma_pago.getValue());

                            } else {
                                this.Cmp.monto_forma_pago.setValue(0);
                                this.Cmp.monto_recibido_forma_pago.setValue(0);
                            }
                        } else {
                            this.Cmp.monto_forma_pago.setValue(0);
                        }
                    }else {
                        this.calculoFp1Grupo(record);
                    }

                },this);

                this.Cmp.id_forma_pago2.on('select', function (combo,record) {
                    if (record) {
                        this.Cmp.moneda_fp2.setValue(record.data.desc_moneda);
                        this.manejoComponentesFp2(record.data.id_forma_pago,record.data.codigo);
                    } else {
                        this.manejoComponentesFp2(this.Cmp.id_forma_pago2.getValue(),this.Cmp.codigo_forma_pago2.getValue());
                    }
                    if (this.grupo == 'no') {
                        var monto_pagado_fp1 = this.getMontoMonBol(this.Cmp.monto_forma_pago.getValue(), this.Cmp.moneda_fp1.getValue());

                        if (monto_pagado_fp1 > -1) {
                            //Si la forma de pago y el boleto estan en la misma moneda
                            if (this.Cmp.moneda.getValue() == this.Cmp.moneda_fp2.getValue()) {
                                this.Cmp.monto_forma_pago2.setValue(this.Cmp.total.getValue() - monto_pagado_fp1);
                            }
                            //Si el boleto esta en usd y la forma de pago es distinta a usd y la forma de pago es igual a la moneda de la sucursal
                            else if (this.Cmp.moneda.getValue() == 'USD' && this.Cmp.moneda_fp2.getValue() == this.Cmp.moneda_sucursal.getValue()) {
                                //convertir de  dolares a moneda sucursal(multiplicar)
                                this.Cmp.monto_forma_pago2.setValue(this.round(((this.Cmp.total.getValue() - monto_pagado_fp1) * this.Cmp.tc.getValue()), 2));
                                //Si el boleto esta en moneda sucursal y la forma de pago es usd y la moneda de la sucursales distinta a usd
                            } else if (this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && this.Cmp.moneda_fp2.getValue() == 'USD') {
                                //convertir de  moneda sucursal a dolares(dividir)
                                this.Cmp.monto_forma_pago2.setValue(this.round(((this.Cmp.total.getValue() - monto_pagado_fp1) / this.Cmp.tc.getValue()), 2));
                            } else {
                                this.Cmp.monto_forma_pago2.setValue(0);
                            }
                        } else {
                            this.Cmp.monto_forma_pago2.setValue(0);
                        }
                    }else {
                        this.calculoFp2Grupo(record);
                    }
                },this);

                this.Cmp.tipo_comision.on('select', function (combo,record) {
                    if(this.grupo=='no') {
                        if (record['json'][0] == 'nacional') {
                           /// fecha_emision
                            var f1 = new Date('02/01/2018');
                            var f2 = new Date(this.Cmp.fecha_emision.getValue());
                            console.log('f1',f1.dateFormat('d/m/Y'));
                            console.log('f2',f2.dateFormat('d/m/Y'));

                            /*if (f2 >= f1 ){
                                    console.log('mayor');
                                this.Cmp.comision.setValue((this.Cmp.neto.getValue() * 0.06).toFixed(2));
                            }else{
                                    console.log('menor');
                                this.Cmp.comision.setValue((this.Cmp.neto.getValue() * 0.1).toFixed(2));
                            }*/


                        this.Cmp.comision.setValue((this.Cmp.neto.getValue() * 0.06).toFixed(2));
                            if(this.Cmp.moneda.getValue()!=='USD') {
                                this.Cmp.comision_moneda_extranjera.setValue(this.Cmp.comision.getValue() / this.Cmp.tc.getValue());
                            }else{
                                this.Cmp.comision_moneda_extranjera.setValue(this.Cmp.comision.getValue());
                            }
                        } else {
                            if (record['json'][0] == 'internacional') {
                                this.Cmp.comision.setValue((this.Cmp.neto.getValue() * 0.06).toFixed(2));
                                if(this.Cmp.moneda.getValue()!=='USD') {
                                    this.Cmp.comision_moneda_extranjera.setValue(this.Cmp.comision.getValue() / this.Cmp.tc.getValue());
                                }else{
                                    this.Cmp.comision_moneda_extranjera.setValue(this.Cmp.comision.getValue());
                                }
                            } else {
                                this.Cmp.comision.setValue(0);
                                this.Cmp.comision_moneda_extranjera.setValue(0);
                            }
                        }
                        if(this.Cmp.moneda_fp1.getValue()!=='USD') {
                            this.Cmp.monto_forma_pago.setValue(this.Cmp.total.getValue() - this.Cmp.comision.getValue());
                            this.Cmp.monto_recibido_forma_pago.setValue(this.Cmp.total.getValue() - this.Cmp.comision.getValue());
                            this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue() - (this.Cmp.total.getValue() - this.Cmp.comision.getValue()));
                        }else{
                            this.Cmp.monto_forma_pago.setValue(this.Cmp.total_moneda_extranjera.getValue() - this.Cmp.comision_moneda_extranjera.getValue());
                            this.Cmp.monto_recibido_forma_pago.setValue(this.Cmp.total_moneda_extranjera.getValue() - this.Cmp.comision_moneda_extranjera.getValue());
                            this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue() - (this.Cmp.total_moneda_extranjera.getValue() - this.Cmp.comision_moneda_extranjera.getValue()));
                        }
                    }else{
                        if (record['json'][0] == 'nacional') {

                             var f1 = new Date('02/01/2018');
                            var f2 = new Date(this.Cmp.fecha_emision.getValue());
                            console.log('f1',f1.dateFormat('d/m/Y'));
                            console.log('f2',f2.dateFormat('d/m/Y'));
                            /*if (f2 >= f1 ){
                                console.log('mayor');
                                this.Cmp.monto_total_comision.setValue((this.Cmp.monto_total_neto.getValue() * 0.06).toFixed(2));
                            }else{
                                console.log('menor');
                                this.Cmp.monto_total_comision.setValue((this.Cmp.monto_total_neto.getValue() * 0.1).toFixed(2));
                            }*/
                            this.Cmp.monto_total_comision.setValue((this.Cmp.monto_total_neto.getValue() * 0.06).toFixed(2));
                            this.Cmp.monto_forma_pago.setValue(this.Cmp.monto_total_boletos.getValue() - this.Cmp.monto_total_comision.getValue());
                            this.Cmp.monto_recibido_forma_pago.setValue(this.Cmp.monto_total_boletos.getValue() - this.Cmp.monto_total_comision.getValue());
                        } else {
                            if (record['json'][0] == 'internacional') {
                                this.Cmp.monto_total_comision.setValue((this.Cmp.monto_total_neto.getValue() * 0.06).toFixed(2));
                                this.Cmp.monto_forma_pago.setValue(this.Cmp.monto_total_boletos.getValue() - this.Cmp.monto_total_comision.getValue());
                                this.Cmp.monto_recibido_forma_pago.setValue(this.Cmp.monto_total_boletos.getValue() - this.Cmp.monto_total_comision.getValue());
                            } else {
                                this.Cmp.monto_total_comision.setValue(0);
                                this.Cmp.monto_forma_pago.setValue(this.Cmp.monto_total_neto.getValue() - this.Cmp.monto_total_comision.getValue());
                                this.Cmp.monto_recibido_forma_pago.setValue(this.Cmp.monto_total_neto.getValue() - this.Cmp.monto_total_comision.getValue());
                            }
                        }
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
                    return (monto/this.Cmp.tc.getValue()).toFixed(2);
                    //return this.roundMenor(monto/this.Cmp.tc.getValue(),2);
                    //Si el boleto esta en moneda sucursal y la forma de pago es usd y la moneda de la sucursales distinta a usd
                } else if (this.Cmp.moneda.getValue() == this.Cmp.moneda_sucursal.getValue() && moneda_fp == 'USD') {
                    //convertir a moneda sucursal(mutiplicar)
                    return (monto*this.Cmp.tc.getValue()).toFixed(2);
                    //return this.roundMenor(monto*this.Cmp.tc.getValue(),2);
                } else {
                    return -1;
                }
            },
            /*
             onTraerBoletos : function () {
             Phx.CP.loadingShow();
             Ext.Ajax.request({
             url:'../../sis_obingresos/control/Boleto/traerBoletosJson',
             params: {id_punto_venta: this.id_punto_venta,start:0,limit:this.tam_pag,sort:'id_boleto_amadeus',dir:'DESC',fecha:this.campo_fecha.getValue().dateFormat('Ymd'), todos:'no'},
             success:this.successSinc,
             failure: this.conexionFailure,
             timeout:this.timeout,
             scope:this
             });
             },*/

            onTraerBoletosTodos : function () {
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url:'../../sis_obingresos/control/Boleto/traerBoletosJson',
                    params: {id_punto_venta: this.id_punto_venta,start:0,limit:this.tam_pag,sort:'id_boleto_amadeus',dir:'DESC',fecha:this.campo_fecha.getValue().dateFormat('Ymd'), todos:'si'},
                    success:this.successSinc,
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });
            },

            successSinc: function(resp) {
                Phx.CP.loadingHide();
                var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                this.reload();
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
                this.ocultarGrupo(3);
                this.mostrarGrupo(0);
                //this.ocultarGrupo(0);
                this.grupo = 'no';
                this.Cmp.nro_boleto.allowBlank = false;
                this.Cmp.nro_boleto.setDisabled(true);
                this.Cmp.monto_recibido_forma_pago.setValue(this.Cmp.monto_forma_pago.getValue());
                this.Cmp.cambio.setValue(this.Cmp.monto_recibido_forma_pago.getValue()-this.Cmp.monto_forma_pago.getValue());
                this.manejoComponentesFp1(this.sm.getSelected().data['id_forma_pago'],this.sm.getSelected().data['codigo_forma_pago']);
                this.manejoComponentesFp2(this.sm.getSelected().data['id_forma_pago2'],this.sm.getSelected().data['codigo_forma_pago2']);

                if (this.sm.getSelected().data['monto_total_fp'] < (this.sm.getSelected().data['total']) ) {
                    this.Cmp.id_forma_pago2.setDisabled(false);
                    this.Cmp.monto_forma_pago2.setDisabled(false);
                }
                if (this.sm.getSelected().data['ffid'] == '' && this.sm.getSelected().data['voucher_code']  == '' && this.sm.getSelected().data['estado'] == 'borrador' )
                {
                    if ((this.sm.getSelected().data['moneda'] == 'BOB' && this.sm.getSelected().data['neto'] == 30 && this.sm.getSelected().data['estado'] == 'borrador'|| this.sm.getSelected().data['moneda'] == 'BOB' && this.sm.getSelected().data['neto'] == 60 && this.sm.getSelected().data['estado'] == 'borrador') ||
                        (this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] == 40 && this.sm.getSelected().data['estado'] == 'borrador'||this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] ==  80 && this.sm.getSelected().data['estado'] == 'borrador') ||
                        (this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] == 70 && this.sm.getSelected().data['estado'] == 'borrador'||this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] ==  140&& this.sm.getSelected().data['estado'] == 'borrador') ||
                        (this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] == 130 && this.sm.getSelected().data['estado'] == 'borrador'||this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] == 260 && this.sm.getSelected().data['estado'] == 'borrador')) {

                        this.formFormual();


                    }
                }

            },


            oncellclick : function(grid, rowIndex, columnIndex, e) {
                var record = this.store.getAt(rowIndex),
                    fieldName = grid.getColumnModel().getDataIndex(columnIndex); // Get field name

                if(fieldName == 'estado') {

                    if (this.sm.getSelected().data['ffid'] != '' && this.sm.getSelected().data['voucher_code']  != '' && this.sm.getSelected().data['estado'] == 'borrador' ){
                       if(record.data.tipo_reg != 'summary'){
                           this.cambiarRevision(record);
                       }
                   }  else if((this.sm.getSelected().data['moneda'] == 'BOB' && this.sm.getSelected().data['neto'] == 30 && this.sm.getSelected().data['estado'] == 'borrador'||this.sm.getSelected().data['moneda'] == 'BOB' && this.sm.getSelected().data['neto'] == 60 && this.sm.getSelected().data['estado'] == 'borrador') ||
                        (this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] == 40 && this.sm.getSelected().data['estado'] == 'borrador'||      this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] ==  80 && this.sm.getSelected().data['estado'] == 'borrador') ||
                        (this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] == 70 && this.sm.getSelected().data['estado'] == 'borrador'||      this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] ==  140&& this.sm.getSelected().data['estado'] == 'borrador') ||
                        (this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] == 130 && this.sm.getSelected().data['estado'] == 'borrador'||     this.sm.getSelected().data['moneda'] == 'USD' && this.sm.getSelected().data['neto'] == 260 && this.sm.getSelected().data['estado'] == 'borrador')) {
                        this.formFormualRevi();
                    }
                   else {
                        if(record.data.tipo_reg != 'summary'){
                            this.cambiarRevision(record);
                        }
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
                var d = record.data;
                Ext.Ajax.request({
                    url:'../../sis_obingresos/control/Boleto/cambiarRevisionBoleto',
                    params:{ id_boleto_amadeus: d.id_boleto_amadeus},
                    success: this.successRevision,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });


            },

            successRevision: function(resp){

                    Phx.CP.loadingHide();
                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                    if (!reg.ROOT.error) {
                        this.reload();
                    }

            },

            anularBoleto:function(){
                var filas=this.sm.getSelections(),
                    total= 0,tmp='',me = this;
                for(var i=0;i<this.sm.getCount();i++){
                    aux={};
                    if(total == 0){
                        tmp = filas[i].data[this.id_store];
                    }
                    else{
                        tmp = tmp + ','+ filas[i].data[this.id_store];
                    }
                    total = total + 1;
                }
                if(total != 0){
                    if(confirm("¿Esta seguro de Anular los boletos?") ){

                        Phx.CP.loadingShow();
<<<<<<< HEAD
                        //var d = this.sm.getSelected().data;
                        Ext.Ajax.request({
                            url:'../../sis_obingresos/control/Boleto/anularBoleto',
                            params:{id_boleto_amadeus:tmp},
                            success:this.successAnularBoleto,
                           /* success : function(resp) {
                                Phx.CP.loadingHide();
                                this.reload();
                            },*/
                            failure: this.conexionFailure,
                            timeout:this.timeout,
                            scope:this
                        });
=======
                //var d = this.sm.getSelected().data;
                Ext.Ajax.request({
                    url:'../../sis_obingresos/control/Boleto/anularBoleto',
                    params:{id_boleto_amadeus:tmp},
                //success:this.successAnularBoleto,
                    success : function(resp) {
                        Phx.CP.loadingHide();
                        alert('Los Boletos fueron Anulados: '+ total);
                        this.reload();

                    },
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });
>>>>>>> 896209f9b47bfe3ee98b0f239ba7d78a9ec9a031
                    }
                }
                else{
                    alert ('No selecciono ningun boleto');
                }

            },

            successAnularBoleto:function(resp){
                Phx.CP.loadingHide();
                var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                if(!reg.ROOT.error){
                    this.reload();
                }
            },

            calculoFp1Grupo : function (record) {
                this.moneda_grupo_fp1 = record.data.desc_moneda;
                if (this.moneda_grupo_fp2 == '') {
                    console.log('moneda grupo 2 vacio');
                    this.Cmp.monto_forma_pago.setValue(this.total_grupo['total_boletos_'+record.data.desc_moneda]);
                } else if (this.moneda_grupo_fp2 == this.moneda_grupo_fp1) {
                    console.log('moneda grupo 2 igual moneda grupo 1');
                    this.Cmp.monto_forma_pago.setValue(this.total_grupo['total_boletos_'+record.data.desc_moneda] - this.Cmp.monto_forma_pago2.getValue());
                } else {
                    if (this.moneda_grupo_fp2 == 'USD') {
                        console.log('monedas distintas grupo 2 usd');
                        this.Cmp.monto_forma_pago.setValue(this.total_grupo['total_boletos_'+record.data.desc_moneda] - this.roundMenor(this.Cmp.monto_forma_pago2.getValue() * this.tc_grupo , 2));
                    } else {
                        console.log('monedas distintas grupo 2 bob');
                        this.Cmp.monto_forma_pago.setValue(this.total_grupo['total_boletos_'+record.data.desc_moneda] - this.roundMenor(this.Cmp.monto_forma_pago2.getValue() / this.tc_grupo , 2));
                    }
                }

            },
            calculoFp2Grupo : function (record) {
                this.moneda_grupo_fp2 = record.data.desc_moneda;
                if (this.moneda_grupo_fp1 == '') {
                    console.log('sin moneda fp 1');
                    this.Cmp.monto_forma_pago2.setValue(this.total_grupo['total_boletos_'+record.data.desc_moneda]);
                } else if (this.moneda_grupo_fp2 == this.moneda_grupo_fp1) {
                    console.log('con misma moneda grupos');
                    this.Cmp.monto_forma_pago2.setValue(this.total_grupo['total_boletos_'+record.data.desc_moneda] - this.Cmp.monto_forma_pago.getValue());
                } else {
                    console.log('moneda grupos diferentes');
                    if (this.moneda_grupo_fp1 == 'USD') {
                        this.Cmp.monto_forma_pago2.setValue(this.total_grupo['total_boletos_'+record.data.desc_moneda] - this.roundMenor(this.Cmp.monto_forma_pago.getValue() * this.tc_grupo , 2));
                    } else {
                        this.Cmp.monto_forma_pago2.setValue(this.total_grupo['total_boletos_'+record.data.desc_moneda] - this.roundMenor(this.Cmp.monto_forma_pago.getValue() / this.tc_grupo , 2));
                    }
                }
            },
            onGrupo : function () {
                Phx.vista.BoletoAmadeus.superclass.onButtonEdit.call(this);
                this.grupo = 'si';
                var seleccionados = this.sm.getSelections();
                this.total_grupo = new Object;
                //this.total_grupo['USD'] = 0;
                //this.total_grupo[seleccionados[0].data.moneda_sucursal] = 0;
                this.total_grupo['total_boletos_USD'] = 0;
                this.total_grupo['total_neto_USD'] = 0;
                this.total_grupo['total_comision_USD'] = 0;
                this.total_grupo['total_boletos_'+seleccionados[0].data.moneda_sucursal] = 0;
                this.total_grupo['total_neto_'+seleccionados[0].data.moneda_sucursal] = 0;
                this.total_grupo['total_comision_'+seleccionados[0].data.moneda_sucursal] = 0;

                for (var i = 0 ; i< seleccionados.length;i++) {
                    if (i == 0) {
                        this.Cmp.ids_seleccionados.setValue(seleccionados[i].data.id_boleto_amadeus);
                        this.Cmp.boletos.setValue('930'+seleccionados[i].data.nro_boleto + ' ('+ seleccionados[i].data.total +' '+seleccionados[i].data.moneda+')');
                    } else {
                        this.Cmp.ids_seleccionados.setValue(this.Cmp.ids_seleccionados.getValue() + ',' + seleccionados[i].data.id_boleto_amadeus);
                        this.Cmp.boletos.setValue(this.Cmp.boletos.getValue() + ', 930' + seleccionados[i].data.nro_boleto + ' ('+ seleccionados[i].data.total +' '+seleccionados[i].data.moneda+')');
                    }
                    if (seleccionados[i].data.moneda_sucursal == 'USD') {
                        /*this.total_grupo['total_boletos_'+seleccionados[0].data.moneda_sucursal] += parseFloat(seleccionados[i].data.total);
                         this.total_grupo['total_neto_'+seleccionados[0].data.moneda_sucursal] += parseFloat(seleccionados[i].data.neto);
                         this.total_grupo['total_comision_'+seleccionados[0].data.moneda_sucursal] += parseFloat(seleccionados[i].data.comision);*/
                        this.total_grupo['total_boletos_USD'] += this.round(seleccionados[i].data.total , 2);
                        this.total_grupo['total_neto_USD'] += this.round(seleccionados[i].data.neto , 2);
                        this.total_grupo['total_comision_USD'] += this.round(seleccionados[i].data.comision , 2);
                        this.Cmp.moneda_boletos.setValue(seleccionados[i].data.moneda);
                        this.Cmp.monto_total_boletos.setValue(this.total_grupo['total_boletos_USD']);// +seleccionados[0].data.moneda_sucursal].toFixed(2));
                        this.Cmp.monto_total_neto.setValue(this.total_grupo['total_neto_USD']); //+seleccionados[0].data.moneda_sucursal].toFixed(2));
                        this.Cmp.monto_total_comision.setValue(this.total_grupo['total_comision_USD']); //+seleccionados[0].data.moneda_sucursal].toFixed(2));

                        this.Cmp.monto_forma_pago.setValue(this.total_grupo['total_boletos_USD']); //+seleccionados[0].data.moneda_sucursal].toFixed(2));
                        this.Cmp.monto_recibido_forma_pago.setValue(this.total_grupo['total_boletos_USD']); //+seleccionados[0].data.moneda_sucursal].toFixed(2));
                    } else if (seleccionados[i].data.moneda == 'USD') {
                        this.Cmp.moneda_boletos.setValue(seleccionados[i].data.moneda);
                        this.total_grupo['total_boletos_'+seleccionados[0].data.moneda_sucursal] += parseFloat(seleccionados[i].data.total * seleccionados[i].data.tc);
                        this.total_grupo['total_neto_'+seleccionados[0].data.moneda_sucursal] += parseFloat(seleccionados[i].data.neto * seleccionados[i].data.tc);
                        this.total_grupo['total_comision_'+seleccionados[0].data.moneda_sucursal] += parseFloat(seleccionados[i].data.comision * seleccionados[i].data.tc);
                        //this.total_grupo['total_boletos_' + seleccionados[0].data.moneda_sucursal] += this.round(seleccionados[i].data.total * seleccionados[i].data.tc , 2);
                        this.total_grupo['total_boletos_USD'] += parseFloat(seleccionados[i].data.total);
                        this.total_grupo['total_neto_USD'] += parseFloat(seleccionados[i].data.neto);
                        this.total_grupo['total_comision_USD'] += parseFloat(seleccionados[i].data.comision);

                        this.Cmp.monto_total_boletos.setValue(this.total_grupo['total_boletos_USD'].toFixed(2));
                        this.Cmp.monto_total_neto.setValue(this.total_grupo['total_neto_USD'].toFixed(2));
                        this.Cmp.monto_forma_pago.setValue(this.total_grupo['total_boletos_USD'].toFixed(2));
                        this.Cmp.monto_recibido_forma_pago.setValue(this.total_grupo['total_boletos_USD'].toFixed(2));
                    } else if (seleccionados[i].data.moneda !== 'USD') {
                        this.Cmp.moneda_boletos.setValue(seleccionados[i].data.moneda);
                        //this.total_grupo['total_boletos_USD'] += this.round(seleccionados[i].data.total / seleccionados[i].data.tc , 2);
                        this.total_grupo['total_boletos_'+seleccionados[0].data.moneda_sucursal] += parseFloat(seleccionados[i].data.total);
                        this.total_grupo['total_neto_'+seleccionados[0].data.moneda_sucursal] += parseFloat(seleccionados[i].data.neto);
                        this.total_grupo['total_comision_'+seleccionados[0].data.moneda_sucursal] += parseFloat(seleccionados[i].data.comision);
                        this.total_grupo['total_boletos_USD'] += Math.ceil(parseFloat(seleccionados[i].data.total / seleccionados[i].data.tc)*100)/100;
                        this.total_grupo['total_neto_USD'] += Math.ceil(parseFloat(seleccionados[i].data.neto / seleccionados[i].data.tc)*100)/100;
                        this.total_grupo['total_comision_USD'] += Math.ceil(parseFloat(seleccionados[i].data.comision / seleccionados[i].data.tc)*100)/100;

                        this.Cmp.monto_total_boletos.setValue(this.total_grupo['total_boletos_'+seleccionados[0].data.moneda_sucursal].toFixed(2));
                        this.Cmp.monto_total_neto.setValue(this.total_grupo['total_neto_'+seleccionados[0].data.moneda_sucursal].toFixed(2));
                        this.Cmp.monto_total_comision.setValue(this.total_grupo['total_comision_'+seleccionados[0].data.moneda_sucursal].toFixed(2));
                        this.Cmp.monto_forma_pago.setValue(this.total_grupo['total_boletos_'+seleccionados[0].data.moneda_sucursal].toFixed(2));
                        this.Cmp.monto_recibido_forma_pago.setValue(this.total_grupo['total_boletos_'+seleccionados[0].data.moneda_sucursal].toFixed(2));
                    }else {
                        alert('No se puede calcular la forma de pago ya que la moneda de un boleto no es la moneda de la sucursal ni dolares americanos');
                        return;
                    }
                }

                //habilitamos el formulario
                this.ocultarGrupo(0);
                this.ocultarGrupo(3);
                this.mostrarGrupo(2);

                this.Cmp.id_forma_pago.setDisabled(false);

                this.Cmp.monto_forma_pago.setDisabled(true);
                //this.Cmp.monto_forma_pago.reset();
                this.Cmp.nro_boleto.allowBlank = true;
                this.manejoComponentesFp1(this.Cmp.id_forma_pago.getValue(),this.Cmp.codigo_forma_pago.getValue());
                this.manejoComponentesFp2(this.Cmp.id_forma_pago2.getValue(),this.Cmp.codigo_forma_pago2.getValue());
                this.moneda_grupo_fp1 = '';
                this.moneda_grupo_fp2 = '';
                this.tc_grupo = seleccionados[0].data.tc;

            },

            preparaMenu:function(tb){
                Phx.vista.BoletoAmadeus.superclass.preparaMenu.call(this,tb);
                this.getBoton('btnPagarGrupo').enable();
                var data = this.getSelectedData();
                if(data['voided']== 'no'){
                    this.getBoton('btnAnularBoleto').setDisabled(false);
                }
                else{
                    this.getBoton('btnAnularBoleto').setDisabled(false);
                }
               if (data['ffid'] != '' && data['voucher_code'] != '' ){
                    this.getBoton('btnVoucherCode').enable();
                }else{
                    this.getBoton('btnVoucherCode').disable();
                }

            },

            liberaMenu:function(tb){
                Phx.vista.BoletoAmadeus.superclass.liberaMenu.call(this,tb);
                this.getBoton('btnPagarGrupo').disable();
                this.getBoton('btnAnularBoleto').setDisabled(true);

            },

            tabsouth:[{
                url:'../../../sis_obingresos/vista/boleto_amadeus_forma_pago/BoletoAmadeusFormaPago.php',
                title:'Formas de Pago',
                height:'40%',
                cls:'BoletoAmadeusFormaPago'
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
                                title: 'Forma de Pago',
                                bodyStyle: 'padding:0 10px 0;',
                                columnWidth: 0.5,
                                items:[],
                                id_grupo:1,
                                collapsible:true,
                                collapsed:false
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
                                title: 'Otros',
                                bodyStyle: 'padding:0 10px 0;',
                                columnWidth: 0.5,
                                items:[],
                                id_grupo:3,
                                collapsible:true,
                                collapsed:false
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
                                title: 'Segunda Forma de Pago',
                                bodyStyle: 'padding:0 10px 0;',
                                columnWidth: 0.5,
                                items:[],
                                id_grupo:4,
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
                    this.ocultarComponente(this.Cmp.mco);
                    this.ocultarComponente(this.Cmp.id_auxiliar);
                    this.Cmp.numero_tarjeta.allowBlank = true;
                    this.Cmp.numero_tarjeta.allowBlank = true;
                    this.Cmp.mco.allowBlank = true;
                    this.Cmp.id_auxiliar.allowBlank = true;
                } else {
                    this.Cmp.id_forma_pago.setDisabled(false);
                    this.Cmp.monto_forma_pago.setDisabled(true);
                    if (codigo_fp1.startsWith("CC") ||
                        codigo_fp1.startsWith("SF")) {
                        this.ocultarComponente(this.Cmp.id_auxiliar);
                        this.ocultarComponente(this.Cmp.mco);
                        this.Cmp.id_auxiliar.reset();
                        this.mostrarComponente(this.Cmp.numero_tarjeta);
                        this.mostrarComponente(this.Cmp.codigo_tarjeta);
                        this.Cmp.numero_tarjeta.allowBlank = false;
                        this.Cmp.codigo_tarjeta.allowBlank = false;
                        this.Cmp.id_auxiliar.allowBlank = true;
                        this.Cmp.mco.allowBlank = true;
                        //tarjeta de credito
                    } else if (codigo_fp1.startsWith("CT")) {
                        //cuenta corriente
                        this.ocultarComponente(this.Cmp.numero_tarjeta);
                        this.ocultarComponente(this.Cmp.mco);
                        this.ocultarComponente(this.Cmp.codigo_tarjeta);
                        this.mostrarComponente(this.Cmp.id_auxiliar);
                        this.Cmp.numero_tarjeta.reset();
                        this.Cmp.codigo_tarjeta.reset();
                        this.Cmp.numero_tarjeta.allowBlank = true;
                        this.Cmp.mco2.allowBlank = true;
                        this.Cmp.codigo_tarjeta.allowBlank = true;
                        this.Cmp.id_auxiliar.allowBlank = false;
                    } else if (codigo_fp1.startsWith("MCO")) {
                        //mco
                        this.ocultarComponente(this.Cmp.numero_tarjeta);
                        this.ocultarComponente(this.Cmp.id_auxiliar);
                        this.Cmp.id_auxiliar.reset();
                        this.mostrarComponente(this.Cmp.mco);
                        this.ocultarComponente(this.Cmp.codigo_tarjeta);
                        this.Cmp.mco.allowBlank = false;
                        this.Cmp.codigo_tarjeta.allowBlank = true;
                        this.Cmp.id_auxiliar.allowBlank = true;
                        this.Cmp.numero_tarjeta.allowBlank = true;
                    }else {
                        this.ocultarComponente(this.Cmp.numero_tarjeta);
                        this.ocultarComponente(this.Cmp.mco);
                        this.ocultarComponente(this.Cmp.codigo_tarjeta);
                        this.ocultarComponente(this.Cmp.id_auxiliar);
                        this.Cmp.numero_tarjeta.reset();
                        this.Cmp.codigo_tarjeta.reset();
                        this.Cmp.id_auxiliar.reset();
                        this.Cmp.numero_tarjeta.allowBlank = true;
                        this.Cmp.mco.allowBlank = true;
                        this.Cmp.codigo_tarjeta.allowBlank = true;
                        this.Cmp.id_auxiliar.allowBlank = true;
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
                        this.ocultarComponente(this.Cmp.mco2);
                        this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                        this.ocultarComponente(this.Cmp.id_auxiliar2);
                        this.Cmp.numero_tarjeta2.allowBlank = true;
                        this.Cmp.mco2.allowBlank = true;
                        this.Cmp.codigo_tarjeta2.allowBlank = true;
                        this.Cmp.id_auxiliar2.allowBlank = true;
                        this.Cmp.numero_tarjeta2.reset();
                        this.Cmp.codigo_tarjeta2.reset();
                        this.Cmp.id_auxiliar2.reset();
                    } else {
                        this.Cmp.id_forma_pago2.setDisabled(false);
                        this.Cmp.monto_forma_pago2.setDisabled(false);
                        if (codigo_fp2.startsWith("CC") ||
                            codigo_fp2.startsWith("SF")) {
                            //tarjeta de credito
                            this.Cmp.id_auxiliar2.reset();
                            this.mostrarComponente(this.Cmp.numero_tarjeta2);
                            this.mostrarComponente(this.Cmp.codigo_tarjeta2);
                            this.ocultarComponente(this.Cmp.id_auxiliar2);
                            this.ocultarComponente(this.Cmp.mco2);
                            this.Cmp.numero_tarjeta2.allowBlank = false;
                            this.Cmp.codigo_tarjeta2.allowBlank = false;
                            this.Cmp.id_auxiliar2.allowBlank = true;
                            this.Cmp.mco2.allowBlank = true;

                        } else if (codigo_fp2.startsWith("CT")) {
                            //cuenta corriente
                            this.ocultarComponente(this.Cmp.numero_tarjeta2);
                            this.ocultarComponente(this.Cmp.mco2);
                            this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                            this.mostrarComponente(this.Cmp.id_auxiliar2);
                            this.Cmp.numero_tarjeta2.reset();
                            this.Cmp.numero_tarjeta2.reset();
                            this.Cmp.numero_tarjeta2.allowBlank = true;
                            this.Cmp.mco2.allowBlank = true;
                            this.Cmp.numero_tarjeta2.allowBlank = true;
                            this.Cmp.id_auxiliar2.allowBlank = false;
                        } else if (codigo_fp2.startsWith("MCO")) {
                            //mco
                            this.ocultarComponente(this.Cmp.id_auxiliar2);
                            this.ocultarComponente(this.Cmp.numero_tarjeta2);
                            this.Cmp.id_auxiliar2.reset();
                            this.mostrarComponente(this.Cmp.mco2);
                            this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                            this.Cmp.mco2.allowBlank = false;
                            this.Cmp.codigo_tarjeta2.allowBlank = true;
                            this.Cmp.id_auxiliar2.allowBlank = true;
                            this.Cmp.numero_tarjeta2.allowBlank = true;
                        }else {
                            this.ocultarComponente(this.Cmp.numero_tarjeta2);
                            this.ocultarComponente(this.Cmp.mco2);
                            this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                            this.ocultarComponente(this.Cmp.id_auxiliar2);
                            this.Cmp.numero_tarjeta2.allowBlank = true;
                            this.Cmp.mco2.allowBlank = true;
                            this.Cmp.codigo_tarjeta2.allowBlank = true;
                            this.Cmp.id_auxiliar2.allowBlank = true;
                            this.Cmp.numero_tarjeta2.reset();
                            this.Cmp.codigo_tarjeta2.reset();
                            this.Cmp.id_auxiliar2.reset();
                        }
                    }
                } else {
                    this.ocultarComponente(this.Cmp.numero_tarjeta2);
                    this.ocultarComponente(this.Cmp.mco2);
                    this.ocultarComponente(this.Cmp.codigo_tarjeta2);
                    this.ocultarComponente(this.Cmp.id_auxiliar2);
                    this.Cmp.numero_tarjeta2.allowBlank = true;
                    this.Cmp.mco2.allowBlank = true;
                    this.Cmp.codigo_tarjeta2.allowBlank = true;
                    this.Cmp.id_auxiliar2.allowBlank = true;
                    this.Cmp.id_forma_pago2.setDisabled(true);
                    this.Cmp.monto_forma_pago2.setDisabled(true);
                }

            },
        formViajeroFrecuente: function () {

            var dato = this.sm.getSelected().data;
            Phx.CP.loadWindows('../../../sis_obingresos/vista/boleto/FormViajeroFrecuente.php',
                'Formulario Viajero Frecuente',
                {
                    modal:true,
                    width:300,
                    height:200
                },
                dato,
                this.idContenedor,
                'FormViajeroFrecuente'
            );
        },
        onButtonVoucherCode:function() {
            var rec=this.sm.getSelected();
            console.log ('Data',rec.data);
            Phx.CP.loadWindows('../../../sis_obingresos/vista/boleto/ViajeroFrecuente.php',
                'Voucher Code',
                {
                    width:'50%',
                    height:'50%'
                },
                rec.data,
                this.idContenedor,
                'ViajeroFrecuente');
        },
        formFormualRevi: function (origen) {

            var ffid = new Ext.form.TextField(
                {
                    name: 'ffid',
                    msgTarget: 'title',
                    fieldLabel: 'FFID',
                    allowBlank: true,
                    anchor: '90%',
                    style: 'background-color:#9BF592 ; background-image: none;',
                    maxLength:50
                });

            var voucherCoide = new Ext.form.TextField(
                {
                    name: 'voucherCode',
                    msgTarget: 'title',
                    fieldLabel: 'Voucher Code',
                    allowBlank: true,
                    anchor: '90%',
                    style: 'background-color: #9BF592; background-image: none;',
                    maxLength:50
                });
            var ticketNumber = new Ext.form.TextField(
                {
                    name: 'ticketNumber',
                    msgTarget: 'title',
                    fieldLabel: 'Ticket Number',
                    allowBlank: true,
                    readOnly :true,
                    anchor: '90%',
                    style: 'background-color: #E1F590; background-image: none;',
                    value: this.sm.getSelected().data['nro_boleto'] ,
                    maxLength:50
                });
            var pnr = new Ext.form.TextField(
                {
                    name: 'pnr',
                    msgTarget: 'title',
                    fieldLabel: 'PNR',
                    allowBlank: true,
                    readOnly :true,
                    anchor: '90%',
                    style: 'background-color: #E1F590; background-image: none;',
                    value: this.sm.getSelected().data['localizador'] ,
                    maxLength:50
                });

            var formularioInicio = new Ext.form.FormPanel({
                items: [ffid,voucherCoide,ticketNumber,pnr],
                padding: true,
                bodyStyle:'padding:5px 5px 0',
                border: false,
                frame: false

            });

            var VentanaInicio = new Ext.Window({
                title: 'Formulario Viajero Frecuente',
                modal: true,
                width: 300,
                height: 200,
                bodyStyle: 'padding:5px;',
                layout: 'fit',
                hidden: true,
                closable: false,
                buttons: [
                    {
                        text: '<i class="fa fa-check"></i> Aceptar',
                        handler: function () {
                            if (formularioInicio.getForm().isValid()) {
                                validado = true;
                                this.ffid = ffid.getValue();
                                this.voucher = voucherCoide.getValue();
                                this.ticket = ticketNumber.getValue();
                                this.pnr  = pnr.getValue();
                                VentanaInicio.close();
                                m = this;
                                Ext.Ajax.request({
                                    url:'../../sis_obingresos/control/Boleto/viajeroFrecuente',
                                    params:{id_boleto_amadeus: m.sm.getSelected().data['id_boleto_amadeus'],
                                        ffid: this.ffid, voucherCode:this.voucher , ticketNumber: this.ticket,
                                        pnr:this.pnr ,bandera:'revisar'},
                                    success:function(resp){
                                        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                                        console.log('id',reg);
                                    },
                                    failure: this.conexionFailure,
                                    timeout:this.timeout,
                                    scope:this
                                });


                            }
                        },
                        scope: this
                    },
                    {
                        text: '<i class="fa fa-check"></i> Ignorar',
                        handler: function () {

                           // VentanaInicio.close();
                            if (formularioInicio.getForm().isValid()) {
                                validado = true;
                                this.ffid = ffid.getValue();
                                this.voucher = voucherCoide.getValue();
                                this.ticket = ticketNumber.getValue();
                                this.pnr  = pnr.getValue();
                                m = this;
                                Ext.Ajax.request({
                                    url:'../../sis_obingresos/control/Boleto/logViajeroFrecuente',
                                    params:{    id_boleto_amadeus: m.sm.getSelected().data['id_boleto_amadeus'],
                                                importe: m.sm.getSelected().data['neto'],
                                                moneda:m.sm.getSelected().data['moneda'] ,
                                        tickert_number: this.ticket,
                                                pnr:this.pnr },
                                    success:function(resp){
                                        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                                        console.log('respuesta',reg);
                                    },
                                    failure: this.conexionFailure,
                                    timeout:this.timeout,
                                    scope:this
                                });
                                VentanaInicio.close();
                                Phx.CP.loadingShow();
                                Ext.Ajax.request({
                                    url:'../../sis_obingresos/control/Boleto/cambiarRevisionBoleto',
                                    params:{ id_boleto_amadeus: m.sm.getSelected().data['id_boleto_amadeus']},
                                    success: this.successRevision,
                                    failure: this.conexionFailure,
                                    timeout: this.timeout,
                                    scope: this
                                });
                            }
                        },
                        scope: this
                    },
                    {
                        text: '<i class="fa fa-check"></i> Declinar',
                        handler : function() {
                            //refresh source grid
                            console.log(formularioInicio.getForm());
                            formularioInicio.getForm().reset();
                        },
                        scope: this
                    }
                ],
                items: formularioInicio,
                autoDestroy: true,
                closeAction: 'close'
            });
            VentanaInicio.show();
        },
        formFormual: function (origen) {

            var ffid = new Ext.form.TextField(
                {
                    name: 'ffid',
                    msgTarget: 'title',
                    fieldLabel: 'FFID',
                    allowBlank: true,
                    anchor: '90%',
                    style: 'background-color:#9BF592 ; background-image: none;',
                    maxLength:50
                });

            var voucherCoide = new Ext.form.TextField(
                {
                    name: 'voucherCode',
                    msgTarget: 'title',
                    fieldLabel: 'Voucher Code',
                    allowBlank: true,
                    anchor: '90%',
                    style: 'background-color: #9BF592; background-image: none;',
                    maxLength:50
                });
            var ticketNumber = new Ext.form.TextField(
                {
                    name: 'ticketNumber',
                    msgTarget: 'title',
                    fieldLabel: 'Ticket Number',
                    allowBlank: true,
                    readOnly :true,
                    anchor: '90%',
                    style: 'background-color: #E1F590; background-image: none;',
                    value: this.sm.getSelected().data['nro_boleto'] ,
                    maxLength:50
                });
            var pnr = new Ext.form.TextField(
                {
                    name: 'pnr',
                    msgTarget: 'title',
                    fieldLabel: 'PNR',
                    allowBlank: true,
                    readOnly :true,
                    anchor: '90%',
                    style: 'background-color: #E1F590; background-image: none;',
                    value: this.sm.getSelected().data['localizador'] ,
                    maxLength:50
                });

            var formularioInicio = new Ext.form.FormPanel({
                items: [ffid,voucherCoide,ticketNumber,pnr],
                padding: true,
                bodyStyle:'padding:5px 5px 0',
                border: false,
                frame: false

            });

            var VentanaInicio = new Ext.Window({
                title: 'Formulario Viajero Frecuente',
                modal: true,
                width: 300,
                height: 200,
                bodyStyle: 'padding:5px;',
                layout: 'fit',
                hidden: true,
                closable: false,
                buttons: [
                    {
                        text: '<i class="fa fa-check"></i> Aceptar',
                        handler: function () {
                            if (formularioInicio.getForm().isValid()) {
                                validado = true;
                                this.ffid = ffid.getValue();
                                this.voucher = voucherCoide.getValue();
                                this.ticket = ticketNumber.getValue();
                                this.pnr  = pnr.getValue();
                                VentanaInicio.close();
                                m = this;

                                Ext.Ajax.request({
                                    url:'../../sis_obingresos/control/Boleto/viajeroFrecuente',
                                    params:{id_boleto_amadeus: m.sm.getSelected().data['id_boleto_amadeus'],
                                        ffid: this.ffid, voucherCode:this.voucher , ticketNumber: this.ticket,
                                        pnr:this.pnr,bandera:'form' },
                                    success:function(resp){
                                        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                                        console.log('id',reg);
                                    },
                                    failure: this.conexionFailure,
                                    timeout:this.timeout,
                                    scope:this
                                });

                            }
                        },
                        scope: this
                    },
                    {
                        text: '<i class="fa fa-check"></i> Ignorar',
                        handler: function () {

                            // VentanaInicio.close();
                            if (formularioInicio.getForm().isValid()) {
                                validado = true;
                                this.ffid = ffid.getValue();
                                this.voucher = voucherCoide.getValue();
                                this.ticket = ticketNumber.getValue();
                                this.pnr  = pnr.getValue();
                                m = this;
                                Ext.Ajax.request({
                                    url:'../../sis_obingresos/control/Boleto/logViajeroFrecuente',
                                    params:{    id_boleto_amadeus: m.sm.getSelected().data['id_boleto_amadeus'],
                                        importe: m.sm.getSelected().data['neto'],
                                        moneda:m.sm.getSelected().data['moneda'] ,
                                        tickert_number: this.ticket,
                                        pnr:this.pnr },
                                    success:function(resp){
                                        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                                        console.log('respuesta',reg);
                                    },
                                    failure: this.conexionFailure,
                                    timeout:this.timeout,
                                    scope:this
                                });
                                VentanaInicio.close();

                            }
                        },
                        scope: this
                    },
                    {
                        text: '<i class="fa fa-check"></i> Declinar',
                        handler : function() {
                            //refresh source grid
                            console.log(formularioInicio.getForm());
                            formularioInicio.getForm().reset();
                        },
                        scope: this
                    }
                ],
                items: formularioInicio,
                autoDestroy: true,
                closeAction: 'close'
            });
            VentanaInicio.show();
        }

        }
    )
</script>
