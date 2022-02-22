<?php
/**
 * @package pxP
 * @file    Clasificacion.php
 * @author  Ariel Ayaviri Omonte
 * @date    21-09-2012
 * @description Archivo con la interfaz de usuario que permite la ejecucion de las funcionales del sistema
 */
header("content-type:text/javascript; charset=UTF-8");
?>

<style type="text/css" rel="stylesheet">
    .x-selectable,
    .x-selectable * {
        -moz-user-select: text !important;
        -khtml-user-select: text !important;
        -webkit-user-select: text !important;
    }

    .x-grid-row td,
    .x-grid-summary-row td,
    .x-grid-cell-text,
    .x-grid-hd-text,
    .x-grid-hd,
    .x-grid-row,

    .x-grid-row,
    .x-grid-cell,
    .x-unselectable
    {
        -moz-user-select: text !important;
        -khtml-user-select: text !important;
        -webkit-user-select: text !important;
    }
</style>
<script>
    Phx.vista.BuscarBoletoAmadeus = Ext.extend(Phx.gridInterfaz, {

        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                return "x-selectable";
            }
        },
        constructor : function(config) {
            this.id_movimiento=config.id_movimiento;

            this.maestro = config.maestro;

            Phx.vista.BuscarBoletoAmadeus.superclass.constructor.call(this, config);

            this.txtSearch = new Ext.form.TextField();
            this.txtSearch.enableKeyEvents = true;
            this.txtSearch.maxLength = 13;
            this.txtSearch.maxLengthText = 'Ha exedido el numero de caracteres permitidos';
            this.txtSearch.msgTarget = 'under';

            this.txtSearch.on('specialkey', this.onTxtSearchSpecialkey, this);
            //this.txtSearch.on('keydown', this.onCombinacion,this);
            this.txtSearch.on('keyup', function (field, e) {

                if(this.txtSearch.getValue().length == 13) {
                    this.store.baseParams.nro_boleto = field.getValue();
                    this.load({params: {start: 0, limit: this.tam_pag}});
                    //this.grid.getSelectionModel().selectFirstRow();
                }
                //this.grid.getSelectionModel().selectFirstRow();

            }, this);

            this.tbar.add(this.txtSearch);

            this.init();
            this.addButton('btnBuscar', {
                text : 'Buscar',
                iconCls : 'bzoom',
                disabled : false,
                handler : this.onBtnBuscar
            });
            this.addButton('btnImprimir',
                {
                    text: 'Imprimir',
                    iconCls: 'bpdf32',
                    grupo: [0,1],
                    disabled: true,
                    handler: this.imprimirBoleto,
                    tooltip: '<b>Imprimir Boleto</b><br/>Imprime el boleto'
                }
            );
            this.getBoton('btnImprimir').setVisible(true);
            this.getBoton('btnImprimir').disable();
        },
        preparaMenu:function(tb){
            Phx.vista.BuscarBoletoAmadeus.superclass.preparaMenu.call(this,tb);

            var data = this.getSelectedData();

            //if (data.trans_code_exch == 'EXCH' && data.trans_code_exch != null) {
                /*if(data.impreso == 'si'){
                    this.getBoton('btnImprimir').setVisible(true);
                    this.getBoton('btnImprimir').disable();
                }else {*/
                if(data.trans_code !='EMDS'){
                    this.getBoton('btnImprimir').setVisible(true);
                    this.getBoton('btnImprimir').enable();
                }

                //}
            /*}else if(data.trans_code != 'EMDS'){
                if(data.trans_code_exch != 'ORIG'){
                    var records = this.grid.getSelectionModel().getSelections();
                    var rec = '';
                    Ext.each(records, function (record, index) {
                        if (rec != '') {
                            rec = rec + ',' + record.id;
                        } else {
                            rec = record.id
                        }
                    });

                    //f.e.a verificar si es exchange
                    Ext.Ajax.request({
                        url: '../../sis_obingresos/control/Boleto/verificarBoletoExch',
                        params: {
                            'pnr': data.localizador,
                            'id_boletos_amadeus': rec,
                            'fecha_emision': data.fecha_emision,
                            'data_field': this.txtSearch.getValue()
                        },
                        success: function (resp) {
                            var reg = Ext.decode(Ext.util.Format.trim(resp.responseText));
                            //reg.ROOT.datos[0].exchange
                            if (JSON.parse(reg.ROOT.datos.exchange) == true && reg.ROOT.datos.tipo_emision=='R') {
                                this.getBoton('btnImprimir').setVisible(true);
                                this.getBoton('btnImprimir').enable();
                            } else {
                                this.getBoton('btnImprimir').setVisible(false);
                                this.getBoton('btnImprimir').disable();
                                Ext.Msg.show({
                                    title: 'Alerta',
                                    msg: '<div><b>Estimado Usuario:</b> <br> Informarle que el boleto que acaba de buscar no corresponde a un Exchange. </div>',
                                    buttons: Ext.Msg.OK,
                                    width: 600,
                                    maxWidth:1024,
                                    icon: Ext.Msg.WARNING
                                });
                            }
                            this.store.reload();


                        },
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });
                }
            }*/
        },

        liberaMenu:function(tb){
            Phx.vista.BuscarBoletoAmadeus.superclass.liberaMenu.call(this,tb);
            this.getBoton('btnImprimir').disable();
            this.getBoton('btnImprimir').setVisible(true);

        },
        imprimirBoleto: function(){
            //Ext.Msg.confirm('Confirmación','¿Está seguro de Imprimir el Comprobante?',function(btn){

            var rec = this.sm.getSelected().data;

            var records = this.grid.getSelectionModel().getSelections();
            var cad = '';
            Ext.each(records, function(record, index) {
                if(cad != ''){
                    cad = cad +','+record.id;
                }else{
                    cad = record.id
                }
            });

            if (rec) {
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url : '../../sis_obingresos/control/Boleto/traerReservaBoletoExch',
                    params : {
                        'pnr' : rec.localizador,
                        'id_boletos_amadeus': cad,
                        'nro_boleto' : rec.nro_boleto
                    },
                    success : this.successExport,
                    failure : this.conexionFailure,
                    timeout : this.timeout,
                    scope : this
                });
            }

        },

        successExport: function (resp) {

            this.onButtonAct();
            Phx.CP.loadingHide();
            var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            //console.log('objRes',objRes);
            var objetoDatos = (objRes.ROOT == undefined)?objRes.datos:objRes.ROOT.datos;
            var objetoDetalle = (objRes.ROOT == undefined)?objRes.detalle:objRes.ROOT.detalle;
            //console.log('objRes', objetoDatos);
            if (objRes.ROOT.datos.tipo_emision == 'R') { //"archivo_generado" in objetoDetalle
                window.open('../../../lib/lib_control/Intermediario.php?r=' + objetoDetalle.archivo_generado + '&t='+new Date().toLocaleTimeString())
            } else {
                /*var wnd = window.open("about:blank", "", "_blank");
                wnd.document.write(objetoDatos.html);*/
                //Phx.CP.loadingHide();
                if(objetoDatos.tipo_emision == 'normal') {
                    Ext.Msg.show({
                        title: 'Sistema AMADEUS',
                        msg: '<div style="text-align: justify;"><b style="color: red;">Estimado Usuario:</b> <br><br><b>Informarle que el servicio de AMADEUS no devuelve información para el boleto seleccionado.</b></div>',
                        buttons: Ext.Msg.OK,
                        width: 500,
                        maxWidth: 1024,
                        icon: Ext.Msg.WARNING
                    });
                }else if(objetoDatos.tipo_emision == 'consulta') {
                        Ext.Msg.show({
                            title: 'Sistema ERP',
                            msg: '<div style="text-align: justify;"><b style="color: red;">Estimado Usuario:</b> <br><br><b>Se ha producido un problema al recuperar la información del sistema ERP, reportelo a Sistemas (Cel.: 71721380).</b></div>',
                            buttons: Ext.Msg.OK,
                            width: 500,
                            maxWidth: 1024,
                            icon: Ext.Msg.WARNING
                        });
                }else if(objRes.ROOT.datos.tipo_emision == 'F') {
                    Ext.Msg.show({
                        title: 'Sistema AMADEUS',
                        msg: '<div style="text-align: justify;"><b  style="color: red;">Estimado Usuario:</b> <br><br><b> Se ha producido un problema respecto al tipo de emision, El tipo de emisión del boleto es (First Issue), posiblemente al agregar un nuevo boleto, se generaron para todos nuevos TSTs, favor de verificar tu estructura actual; si ese no es el motivo del problema, reportelo a Sistemas (Cel.: 71721380).</b> </div>',
                        buttons: Ext.Msg.OK,
                        width: 500,
                        maxWidth: 1024,
                        icon: Ext.Msg.WARNING
                    });
                }else{
                    Ext.Msg.show({
                        title: 'Sistema AMADEUS',
                        //msg: '<div style="text-align: justify;"><b  style="color: red;">Estimado Usuario:</b> <br><br><b> Se ha producido un problema al recuperar la información del sistema Amadeus, posiblemente la ultima emisión del boleto exchange este mal o existio algun problema con el Servicio Web. Revise la construcción del boleto; si ese no es el motivo del problema, reportelo a Sistemas (Cel.: 71721380).</b> </div>',
                        msg: '<div style="text-align: justify;"><b  style="color: red;">Estimado Usuario:</b> <br><br><b> '+objRes.ROOT.datos.error+'.</b> </div>',
                        buttons: Ext.Msg.OK,
                        width: 500,
                        maxWidth: 1024,
                        icon: Ext.Msg.WARNING
                    });
                }
            }



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
                form:true
            },

            {
                config:{
                    name: 'trans_code',
                    fieldLabel: 'Code',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 50,
                    maxLength:10,
                    minLength:10,
                    enableKeyEvents:true,
                    renderer : function(value, p, record) {

                        return '<tpl for="."><p><font color="#20b2aa">' + record.data['trans_code'] + '</tpl>';
                    }
                },
                type:'TextField',
                id_grupo:0,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'trans_code_exch',
                    fieldLabel: 'Tipo',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 50,
                    maxLength:10,
                    minLength:10,
                    enableKeyEvents:true,
                    renderer : function(value, p, record) {
                        return '<tpl for="."><p><font color="green">' + record.data['trans_code_exch'] + '</tpl>';
                    }
                },
                type:'TextField',
                id_grupo:0,
                grid:true,
                form:false
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
                form:true
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
                form:true
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
                    name: 'punto_venta',
                    fieldLabel: 'Punto Venta',
                    disabled: true,
                    anchor: '100%',
                    gwidth: 200,
                    renderer : function(value, p, record) {
                        return String.format('<div title="Punto Venta"><b><font color="green">{0}</font></b></div>', value);
                    }
                },
                type:'TextField',
                id_grupo:0,
                grid:true,
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
        title:'Buscador Boleto Amadeus',
        ActList:'../../sis_obingresos/control/Boleto/buscarBoletoAmadeus',
        ActSearch : '../../sis_obingresos/control/Boleto/buscarBoletoAmadeus',
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
            {name:'mco2', type: 'string'},
            {name:'ffid_consul', type: 'string'},
            {name:'voucher_consu', type: 'string'},
            {name:'trans_code', type: 'string'},
            {name:'trans_issue_indicator', type: 'string'},
            {name:'punto_venta', type: 'string'},
            {name:'trans_code_exch', type: 'string'},
            {name:'impreso', type: 'string'}

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

        onBtnBuscar : function() {
            this.store.baseParams.nro_boleto = ((this.txtSearch.getValue()).trim()).toUpperCase();
            this.store.baseParams.fecha_actual =  new Date();;
            this.load({params: {start: 0, limit: this.tam_pag}});
            //this.grid.getSelectionModel().selectFirstRow();
        },

        onTxtSearchSpecialkey : function(field, e) {

            if (e.getKey() == e.ENTER) {
                this.onBtnBuscar();
            }
        },

        onCombinacion: function(e) {

            if (e.getKey() == 17) {
                console.log('especial');
                this.onBtnBuscar();
            }
        }
    });
</script>
