<?php
/**
 *@package pXP
 *@file CalculoOverComison.php
 *@author franklin.espinoza
 *@date 10-05-2021
 *@description  Vista para generar Calculo Over Comison
 */

header("content-type: text/javascript; charset=UTF-8");
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
    Phx.vista.CalculoOverComison=Ext.extend(Phx.gridInterfaz,{
        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                return "x-selectable";
            }
        },
        btest:false,
        constructor: function(config) {
            this.maestro = config;

            Phx.vista.CalculoOverComison.superclass.constructor.call(this,config);

            this.current_date = new Date();
            this.diasMes = [31, new Date(this.current_date.getFullYear(), 2, 0).getDate(), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

            /*this.txtSearch = new Ext.form.TextField();
            this.txtSearch.enableKeyEvents = true;
            this.txtSearch.maxLength = 13;
            this.txtSearch.maxLengthText = 'Ha exedido el numero de caracteres permitidos';
            this.txtSearch.msgTarget = 'under';*/

            this.etiqueta_tipo = new Ext.form.Label({
                name: 'label_tipo',
                grupo: [0,1],
                fieldLabel: 'Tipo:',
                text: 'Tipo:',
                //style: {color: 'green', font_size: '12pt'},
                readOnly:true,
                anchor: '150%',
                gwidth: 150,
                format: 'd/m/Y',
                hidden : false,
                style: 'font-size: 170%; font-weight: bold; background-image: none;color: #00B167;'
            });

            this.cmbTipo = new Ext.form.ComboBox({
                name : 'campo_tipo',
                grupo : [0,1],
                fieldLabel : 'Tipo',
                msgTarget : 'side',
                hidden : false,
                allowBlank : false,
                emptyText :'Tipo...',
                typeAhead : true,
                triggerAction : 'all',
                lazyRender : true,
                mode : 'local',
                anchor : '70%',
                width : 90,
                gwidth : 200,
                editable : false,
                store : new Ext.data.ArrayStore({
                    fields : ['tipo', 'valor'],
                    data : [
                        ['IATA', 'Detalle IATA'],
                        ['NO-IATA', 'Detalle No IATA']
                    ]
                }),
                valueField : 'tipo',
                displayField : 'valor'
            });

            this.etiqueta_fechas = new Ext.form.Label({
                name: 'label_fechas',
                grupo: [0,1],
                fieldLabel: 'Generado:',
                text: 'Generado:',
                //style: {color: 'green', font_size: '12pt'},
                readOnly:true,
                anchor: '150%',
                gwidth: 150,
                format: 'd/m/Y',
                hidden : false,
                style: 'font-size: 170%; font-weight: bold; background-image: none;color: #00B167;'
            });
            this.cmbFechas = new Ext.form.ComboBox({

                name: 'id_proveedor',
                hiddenName: 'id_proveedor',
                fieldLabel: 'Proveedor',

                forceSelection: true,
                allowBlank: true,
                msgTarget : 'side',
                emptyText: 'Rango Generado...',
                editable : false,
                store: new Ext.data.JsonStore({
                    url:'../../sis_obingresos/control/Reportes/getFechasGeneradasOverComison',
                    id: 'id_calculo_over_comison',
                    root: 'datos',
                    sortInfo:{
                        field: 'tipo',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_calculo_over_comison','tipo','intervalo','calculo_generado','documento','fecha_ini_calculo','fecha_fin_calculo'],
                    // turn on remote sorting
                    remoteSort: true,
                    //baseParams:Ext.apply({par_filtro:'desc_proveedor#codigo#nit#rotulo_comercial'})
                }),
                valueField: 'id_calculo_over_comison',
                displayField: 'intervalo',
                gdisplayField: 'intervalo',
                triggerAction: 'all',
                lazyRender: true,
                resizable: true,
                mode: 'remote',
                pageSize: 10,
                queryDelay: 1000,
                listWidth: 230,
                minChars: 2,
                gwidth: 100,
                width: 230,
                anchor: '80%',
                tpl: '<tpl for="."><div class="x-combo-list-item"><p><b >Rango: {intervalo}</b></p><p><b style="text-align: center; color: #00B167;">Tipo: [ {tipo} ]</b></p></div></tpl>',
                id_grupo: 0
            });

            this.etiqueta_ini = new Ext.form.Label({
                name: 'etiqueta_ini',
                grupo: [0,1],
                fieldLabel: 'Fecha Inicio:',
                text: 'Fecha Inicio:',
                //style: {color: 'green', font_size: '12pt'},
                readOnly:true,
                anchor: '150%',
                gwidth: 150,
                format: 'd/m/Y',
                hidden : false,
                style: 'font-size: 170%; font-weight: bold; background-image: none;color: #00B167;'
            });
            this.fecha_ini = new Ext.form.DateField({
                name: 'fecha_ini',
                grupo: [0,1],
                msgTarget : 'side',
                fieldLabel: 'Fecha',
                allowBlank: false,
                anchor: '60%',
                gwidth: 100,
                format: 'd/m/Y',
                hidden : false,
                disabled:false
            });

            this.etiqueta_fin = new Ext.form.Label({
                name: 'etiqueta_fin',
                grupo: [0,1],
                fieldLabel: 'Fecha Fin',
                text: 'Fecha Fin:',
                //style: {color: 'red', font_size: '12pt'},
                readOnly:true,
                anchor: '150%',
                gwidth: 150,
                format: 'd/m/Y',
                hidden : false,
                style: 'font-size: 170%; font-weight: bold; background-image: none; color: #FF8F85;'
            });
            this.fecha_fin = new Ext.form.DateField({
                name: 'fecha_fin',
                grupo: [0,1],
                msgTarget : 'side',
                fieldLabel: 'Fecha',
                allowBlank: false,
                anchor: '60%',
                gwidth: 100,
                format: 'd/m/Y',
                hidden : false,
                disabled:false
            });


            this.tbar.addField(this.etiqueta_ini);
            this.tbar.addField(this.fecha_ini);
            this.tbar.addField(this.etiqueta_fin);
            this.tbar.addField(this.fecha_fin);

            this.tbar.addField(this.etiqueta_tipo);
            this.tbar.addField(this.cmbTipo);

            this.tbar.addField(this.etiqueta_fechas);
            this.tbar.addField(this.cmbFechas);

            this.addButton('btnGenerar', {
                text : 'Generar',
                grupo: [0,1],
                iconCls : 'bengine',
                disabled : false,
                hidden : true,
                handler : this.onBtnBuscar
            });

            this.addButton('btnFileBSP', {
                text : 'Generar File BSP',
                grupo: [0,1],
                iconCls : 'bexcel',
                disabled : false,
                hidden : true,
                handler : this.onGenerarFileBSP
            });

            this.addButton('btnCreditoPortal', {
                text : 'Generar Credito P. NO IATA',
                grupo: [0,1],
                iconCls : 'bexcel',
                disabled : false,
                hidden : true,
                handler : this.onGenerarCreditoPortal
            });


            //this.store.baseParams.momento = 0;
            this.iniciarEventos();
            this.bandera_alta = 0;
            this.bandera_baja = 0;

            this.grid.addListener('cellclick', this.mostrarDetalleACM,this);
            this.fecha_ini.on('select', function (rec, date) {
                let fecha_max = new Date(date.getFullYear() ,date.getMonth(), this.diasMes[date.getMonth()])
                this.fecha_fin.setMaxValue(fecha_max);
            },this);

            this.init();

        },

        onGenerarFileBSP : function (){

            var fechas = this.cmbFechas.getRawValue();
            Phx.CP.loadingShow();
            if (fechas == '') {

                fecha_desde = this.fecha_ini.getValue();
                dia =  fecha_desde.getDate();
                dia = dia < 10 ? "0"+dia : dia;
                mes = fecha_desde.getMonth() + 1;
                mes = mes < 10 ? "0"+mes : mes;
                anio = fecha_desde.getFullYear();
                let fecha_ini = dia + "/" + mes + "/" + anio;

                fecha_hasta = this.fecha_fin.getValue();
                dia =  fecha_hasta.getDate();
                dia = dia < 10 ? "0"+dia : dia;
                mes = fecha_hasta.getMonth() + 1;
                mes = mes < 10 ? "0"+mes : mes;
                anio = fecha_hasta.getFullYear();
                let fecha_fin = dia + "/" + mes + "/" + anio;

                let tipo = this.cmbTipo.getValue();

                Ext.Ajax.request({
                    url: '../../sis_obingresos/control/CalculoOverComison/reporteFileBSP',
                    params: {fecha_ini: fecha_ini, fecha_fin: fecha_fin},
                    success: function (resp) {
                        var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                        console.log('envio over',reg);
                        Ext.Msg.show({
                            title: 'Información',
                            msg: '<b>Estimado Funcionario: ' + '\n' + ' El Reporte se esta Generando, una vez concluido se le enviara a su correo.</b>',
                            buttons: Ext.Msg.OK,
                            width: 512,
                            icon: Ext.Msg.INFO
                        });
                        Phx.CP.loadingHide();
                    },
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });

            }else{

                var fechas = fechas.split('-');

                Ext.Ajax.request({
                    url: '../../sis_obingresos/control/CalculoOverComison/reporteFileBSP',
                    params: {fecha_ini: fechas[0].trim(), fecha_fin: fechas[1].trim()},
                    success: function (resp) {
                        var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                        console.log('envio over',reg);
                        Ext.Msg.show({
                            title: 'Información',
                            msg: '<b>Estimado Funcionario: ' + '<br>' + ' El reporte se esta generando, una vez concluido el proceso se le enviara a su correo la información correspondiente.</b>',
                            buttons: Ext.Msg.OK,
                            width: 512,
                            icon: Ext.Msg.INFO
                        });
                        Phx.CP.loadingHide();
                    },
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });

            }
        },

        onGenerarCreditoPortal : function () {
            //var data = this.getSelectedData();
            var fechas = this.cmbFechas.getRawValue();
            Phx.CP.loadingShow();
            if (fechas == '') {

                fecha_desde = this.fecha_ini.getValue();
                dia =  fecha_desde.getDate();
                dia = dia < 10 ? "0"+dia : dia;
                mes = fecha_desde.getMonth() + 1;
                mes = mes < 10 ? "0"+mes : mes;
                anio = fecha_desde.getFullYear();
                let fecha_ini = dia + "/" + mes + "/" + anio;

                fecha_hasta = this.fecha_fin.getValue();
                dia =  fecha_hasta.getDate();
                dia = dia < 10 ? "0"+dia : dia;
                mes = fecha_hasta.getMonth() + 1;
                mes = mes < 10 ? "0"+mes : mes;
                anio = fecha_hasta.getFullYear();
                let fecha_fin = dia + "/" + mes + "/" + anio;

                let tipo = this.cmbTipo.getValue();

                Ext.Ajax.request({
                    url: '../../sis_obingresos/control/CalculoOverComison/reporteCalculoOverComison',
                    params: {fecha_ini: fecha_ini, fecha_fin: fecha_fin, tipo: tipo, momento: 0},
                    success: this.successExport,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });

            }else{

                var fechas = fechas.split('-');

                Ext.Ajax.request({
                    url: '../../sis_obingresos/control/CalculoOverComison/reporteCalculoOverComison',
                    params: {fecha_ini: fechas[0].trim(), fecha_fin: fechas[1].trim(), tipo: 'NO-IATA', momento: 0},
                    success: this.successExport,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });

            }
        },

        onBtnBuscar : function() {

            fecha_desde = this.fecha_ini.getValue();
            dia =  fecha_desde.getDate();
            dia = dia < 10 ? "0"+dia : dia;
            mes = fecha_desde.getMonth() + 1;
            mes = mes < 10 ? "0"+mes : mes;
            anio = fecha_desde.getFullYear();
            this.store.baseParams.fecha_desde = dia + "/" + mes + "/" + anio;

            fecha_hasta = this.fecha_fin.getValue();
            dia =  fecha_hasta.getDate();
            dia = dia < 10 ? "0"+dia : dia;
            mes = fecha_hasta.getMonth() + 1;
            mes = mes < 10 ? "0"+mes : mes;
            anio = fecha_hasta.getFullYear();
            this.store.baseParams.fecha_hasta = dia + "/" + mes + "/" + anio;

            this.store.baseParams.tipo = this.cmbTipo.getValue();
            //this.store.baseParams.momento = momento ? momento : 0;

            this.load({params: {start: 0, limit: 50}});
        },

        mostrarDetalleACM : function(grid, rowIndex, columnIndex, e) {

            var record = this.store.getAt(rowIndex);
            var fieldName = grid.getColumnModel().getDataIndex(columnIndex); // Get field name



            if (fieldName == 'DocumentNumber') {

                var rec = this.getSelectedData();
                rec.fecha_desde = this.store.baseParams.fecha_desde;
                rec.fecha_hasta = this.store.baseParams.fecha_hasta;
                Phx.CP.loadWindows('../../../sis_obingresos/vista/calculo_over_comison/DetalleCalculoACM.php',
                    'Detalle Calculo ACM',
                    {
                        width: 1200,
                        height:600
                    },
                    rec,
                    this.idContenedor,
                    'DetalleCalculoACM'
                );
            }

        },

        bactGroups:[0,1],
        bexcelGroups:[0,1],
        /*gruposBarraTareas: [
            {name:  'normal', title: '<h1 style="text-align: center; color: #00B167;"><i class="fa fa-file-o fa-2x" aria-hidden="true"></i>C. LIQUIDACIÓN</h1>',grupo: 0, height: 0} ,
            {name: 'existencia', title: '<h1 style="text-align: center; color: #FF8F85;"><i class="fa fa-file-o fa-2x" aria-hidden="true"></i>CONTROL VUELOS</h1>', grupo: 1, height: 1}
        ],*/
        iniciarEventos: function(){


            this.fecha_ini.on('select', function (combo,rec,index) {
                this.fecha_fin.allowBlank = false;
                this.cmbTipo.allowBlank = false;
            },this);

            this.fecha_fin.on('select', function (combo,rec,index) {

                this.fecha_ini.allowBlank = false;
                this.cmbTipo.allowBlank = false;
                this.cmbTipo.setValue('');
                this.modificado = true;

                fecha_desde = this.fecha_ini.getValue();
                dia =  fecha_desde.getDate();
                dia = dia < 10 ? "0"+dia : dia;
                mes = fecha_desde.getMonth() + 1;
                mes = mes < 10 ? "0"+mes : mes;
                anio = fecha_desde.getFullYear();
                this.store.baseParams.fecha_desde = dia + "/" + mes + "/" + anio;

                fecha_hasta = this.fecha_fin.getValue();
                dia =  fecha_hasta.getDate();
                dia = dia < 10 ? "0"+dia : dia;
                mes = fecha_hasta.getMonth() + 1;
                mes = mes < 10 ? "0"+mes : mes;
                anio = fecha_hasta.getFullYear();
                this.store.baseParams.fecha_hasta = dia + "/" + mes + "/" + anio;

            },this);

            this.cmbTipo.on('select', function (combo, record, index) {

                this.fecha_ini.allowBlank = false;
                this.fecha_fin.allowBlank = false;


                this.cmbFechas.setValue('');
                this.modificado = true;
                if(record.data.tipo == "IATA"){
                    this.getBoton('btnFileBSP').setVisible(true);
                    this.getBoton('btnCreditoPortal').setVisible(false);
                    this.getBoton('btnGenerar').setVisible(true);
                }else{
                    this.getBoton('btnFileBSP').setVisible(false);
                    this.getBoton('btnCreditoPortal').setVisible(true);
                    this.getBoton('btnGenerar').setVisible(true);
                }
                this.store.baseParams.tipo = record.data.tipo;
                this.store.baseParams.momento = 0;

                Ext.Ajax.request({
                    url:'../../sis_obingresos/control/CalculoOverComison/verificarPeriodoGenerado',
                    params:{
                        fecha_ini : this.store.baseParams.fecha_desde,
                        fecha_fin : this.store.baseParams.fecha_hasta,
                        tipo      : this.store.baseParams.tipo
                    },
                    success:function(resp){
                        var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                        if (reg.ROOT.datos.estado_generado == 'generado'){
                            this.onBtnBuscar();
                            this.store.baseParams.momento = 0;
                        }else{
                            //this.load({params: {start: 0, limit: 50}});
                            this.store.baseParams.momento = 1;
                        }
                    },
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });


            }, this);

            this.cmbFechas.on('select', function (combo, record, index) {

                this.fecha_ini.allowBlank = true;
                this.fecha_ini.setValue('');
                this.modificado = true;

                this.fecha_fin.allowBlank = true;
                this.fecha_fin.setValue('');
                this.modificado = true;

                this.cmbTipo.allowBlank = true;
                this.cmbTipo.setValue('');
                this.modificado = true;


                if(record.data.tipo == "IATA"){
                    this.getBoton('btnFileBSP').setVisible(true);
                    this.getBoton('btnCreditoPortal').setVisible(false);
                    //this.getBoton('btnGenerar').setVisible(true);
                }else{
                    this.getBoton('btnFileBSP').setVisible(false);
                    this.getBoton('btnCreditoPortal').setVisible(true);
                    //this.getBoton('btnGenerar').setVisible(true);
                }
                this.getBoton('btnGenerar').setVisible(false);
                this.store.baseParams.tipo = record.data.tipo;

                fecha_desde = new Date(record.data.fecha_ini_calculo);
                fecha_desde = new Date(fecha_desde.setDate( fecha_desde.getDate() + 1));
                dia =  fecha_desde.getDate();
                dia = dia < 10 ? "0"+dia : dia;
                mes = fecha_desde.getMonth() + 1;
                mes = mes < 10 ? "0"+mes : mes;
                anio = fecha_desde.getFullYear();
                this.store.baseParams.fecha_desde = dia + "/" + mes + "/" + anio;

                fecha_hasta = new Date(record.data.fecha_fin_calculo);
                fecha_hasta = new Date(fecha_hasta.setDate( fecha_hasta.getDate() + 1));
                dia =  fecha_hasta.getDate();
                dia = dia < 10 ? "0"+dia : dia;
                mes = fecha_hasta.getMonth() + 1;
                mes = mes < 10 ? "0"+mes : mes;
                anio = fecha_hasta.getFullYear();
                this.store.baseParams.fecha_hasta = dia + "/" + mes + "/" + anio;

                this.load({params: {start: 0, limit: 50}});

            }, this);
        },
        actualizarSegunTab: function(name, indice){

            /*if(name == 'normal' || name == 'existencia' ){
                this.fecha_ini.setValue(new Date(this.current_date.getFullYear(),this.current_date.getMonth(),1));
                this.fecha_fin.setValue(new Date(this.current_date.getFullYear(),this.current_date.getMonth()+1,0));
                this.bandera_alta = 1;
            }else if(name == 'existencia' ){
                this.fecha_ini.setValue(new Date(this.current_date.getFullYear(),this.current_date.getMonth()-1,1));
                this.fecha_fin.setValue(new Date(this.current_date.getFullYear(),this.current_date.getMonth(),0));
                this.bandera_baja = 1;
            }*/
            this.store.baseParams.tipo_rep = name;
            if ( (this.fecha_ini.getValue() != '' && this.fecha_ini.getValue() != undefined) || (this.fecha_fin.getValue() != '' && this.fecha_fin.getValue() != undefined) ) {


                fecha_desde = this.fecha_ini.getValue();
                dia = fecha_desde.getDate();
                dia = dia < 10 ? "0" + dia : dia;
                mes = fecha_desde.getMonth() + 1;
                mes = mes < 10 ? "0" + mes : mes;
                anio = fecha_desde.getFullYear();

                this.store.baseParams.fecha_desde = dia + "/" + mes + "/" + anio;

                fecha_hasta = this.fecha_fin.getValue();
                dia = fecha_hasta.getDate();
                dia = dia < 10 ? "0" + dia : dia;
                mes = fecha_hasta.getMonth() + 1;
                mes = mes < 10 ? "0" + mes : mes;
                anio = fecha_hasta.getFullYear();
                this.store.baseParams.fecha_hasta = dia + "/" + mes + "/" + anio;

                this.load({params: {start: 0, limit: 50}});
            }
        },


        Atributos:[
            {
                // configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'AcmKey'
                },
                type:'Field',
                form:true

            },
            {
                // configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'DocumentType'
                },
                type:'Field',
                form:true

            },
            {
                config:{
                    fieldLabel: "Numero ACM",
                    gwidth: 150,
                    name: 'DocumentNumber',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        if (value!= '0'){
                            return String.format('<div style="color: #00B167; font-weight: bold; cursor:pointer;">{0} <i class="fa fa-eye fa-2x"></i> </div>', value);
                        }

                    }
                },
                type:'TextField',
                //filters:{pfiltro:'tca.nombre',type:'string'},
                //bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:false
            },

            {
                config:{
                    fieldLabel: "Agencia",
                    gwidth: 250,
                    name: 'PointOfSale',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #586E7E; font-weight: bold;">{0}</div>', value);
                    }
                },
                type:'TextField',
                //filters:{pfiltro:'tca.nombre',type:'string'},
                //bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:true
            },

            {
                config:{
                    fieldLabel: "Estación",
                    gwidth: 100,
                    name: 'IataCode',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #586E7E; font-weight: bold;">{0}</div>', value);
                    }
                },
                type:'TextField',
                //filters:{pfiltro:'NroVuelo',type:'string'},
                //bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    fieldLabel: "Office Id",
                    gwidth: 100,
                    name: 'OfficeId',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #586E7E; font-weight: bold;">{0}</div>', value);
                    }
                },
                type:'TextField',
                //filters:{pfiltro:'NroVuelo',type:'string'},
                //bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:true
            },

            {
                config:{
                    fieldLabel: "Tipo",
                    gwidth: 70,
                    name: 'TypePOS',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #586E7E; font-weight: bold;">{0}</div>', value);
                    }
                },
                type:'TextField',
                //filters:{pfiltro:'NroVuelo',type:'string'},
                //bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    fieldLabel: "Desde",
                    gwidth: 100,
                    name: 'From',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    format:'d/m/Y',
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #00B167; font-weight: bold;">{0}</div>', value.dateFormat('d/m/Y'));
                    }
                },
                type:'DateField',
                //filters:{pfiltro:'tca.nombre',type:'string'},
                //bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    fieldLabel: "Hasta",
                    gwidth: 100,
                    name: 'To',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    format:'d/m/Y',
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #00B167; font-weight: bold;">{0}</div>', value.dateFormat('d/m/Y'));
                    }
                },
                type:'DateField',
                //filters:{pfiltro:'tca.nombre',type:'string'},
                //bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    fieldLabel: "Cantidad Doc.",
                    gwidth: 100,
                    name: 'CommissionPercent',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #586E7E; font-weight: bold;">{0}</div>', value);
                    }
                },
                type:'TextField',
                //filters:{pfiltro:'RutaVl',type:'string'},
                //bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:true
            },

            {
                config:{
                    fieldLabel: "Moneda",
                    gwidth: 100,
                    name: 'Currency',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #586E7E; font-weight: bold;">{0}</div>', value);
                    }
                },
                type:'TextField',
                //filters:{pfiltro:'tca.nombre',type:'string'},
                //bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:true
            },

            {
                config:{
                    fieldLabel: "Descripción",
                    gwidth: 250,
                    name: 'CommissionDescription',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #586E7E; font-weight: bold;">{0}</div>', value);
                    }
                },
                type:'TextField',
                //filters:{pfiltro:'tca.nombre',type:'string'},
                //bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    fieldLabel: "Comisión",
                    gwidth: 100,
                    name: 'CommssionAmount',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record) {

                        Number.prototype.formatDinero = function (c, d, t) {
                            var n = this,
                                c = isNaN(c = Math.abs(c)) ? 2 : c,
                                d = d == undefined ? "." : d,
                                t = t == undefined ? "," : t,
                                s = n < 0 ? "-" : "",
                                i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                                j = (j = i.length) > 3 ? j % 3 : 0;
                            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
                        };
                        if(record.data.tipo_reg != 'summary'){

                            return  String.format('<div style="color: #00B167; font-weight: bold; vertical-align:middle;text-align:right;"><span >{0}</span></div>',(parseFloat(value)).formatDinero(2, ',', '.'));
                        }
                        else{

                            return  String.format('<div style="color: #00B167; font-weight: bold; vertical-align:middle;text-align:right;"><span ><b>{0}</b></span></div>',(parseFloat(value)).formatDinero(2, ',', '.'));

                        }
                    }
                },
                type:'NumberField',
                //filters:{pfiltro:'tca.nombre',type:'string'},
                //bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:false
            }
        ],
        title:'Calculo Over Comison',
        ActList:'../../sis_obingresos/control/CalculoOverComison/generarCalculoOverComison',
        id_store:'id_iata',
        fields: [
            {name:'AcmKey'},
            {name:'DocumentNumber', type: 'string'},
            {name:'PointOfSale', type: 'string'},
            {name:'IataCode', type: 'string'},
            {name:'TypePOS', type: 'string'},
            {name:'From', type: 'date'},
            {name:'To', type: 'date'},

            {name:'CommissionPercent', type: 'string'},
            {name:'Currency', type: 'string'},
            {name:'CommissionDescription', type: 'string'},
            {name:'CommssionAmount', type: 'numeric'},
            {name:'DocumentType', type: 'string'},
            {name:'OfficeId', type: 'string'}
        ],
        /*sortInfo:{
            field: 'PERSON.nombre_completo2',
            direction: 'ASC'
        },*/
        bedit:false,
        bnew:false,
        bdel:false,
        bsave:false,
        fwidth: '90%',
        fheight: '95%'
    });
</script>
