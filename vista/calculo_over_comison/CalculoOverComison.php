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
            }/*,
            listeners:{
                itemkeydown:function(view, record, item, index, e){
                    alert('The press key is' + e.getKey());
                }
            }*/
        },
        btest:false,
        constructor: function(config) {
            this.maestro = config;

            Phx.vista.CalculoOverComison.superclass.constructor.call(this,config);
            //console.log('nombreVista',this.nombreVista);
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
                disabled:false,
                editable : false
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
                disabled:false,
                editable : false
            });


            this.tbar.addField(this.etiqueta_ini);
            this.tbar.addField(this.fecha_ini);
            this.tbar.addField(this.etiqueta_fin);
            this.tbar.addField(this.fecha_fin);

            this.tbar.addField(this.etiqueta_tipo);
            this.tbar.addField(this.cmbTipo);

            this.tbar.addField(this.etiqueta_fechas);
            this.tbar.addField(this.cmbFechas);

            this.addButton('btnExcluir', {
                text : 'Excluir Agencia',//Generar
                grupo: [0,1],
                iconCls : 'block',
                disabled : false,
                hidden : false,
                handler : this.onExcluirAgencia
            });

            this.addButton('btnValidar', {
                text : 'Validar Calculo',//Generar
                grupo: [0,1],
                iconCls : 'bassign',
                disabled : false,
                hidden : true,
                handler : this.onValidarMovimientoEntidad
            });

            this.addButton('btnGenerar', {
                text : 'Generar ACMs',//Generar
                grupo: [0,1],
                iconCls : 'bassign',
                disabled : false,
                hidden : true,
                handler : this.onGenerarMovimientoEntidad
            });

            this.addButton('btnAbonar', {
                text : 'Abonar',//Generar
                grupo: [0,1],
                iconCls : 'bpagar',
                disabled : false,
                hidden : true,
                handler : this.onAbonarMovimientoEntidad//this.onBtnBuscar
            });

            this.addButton('btnRevertir', {
                text : 'Revertir Abono',
                grupo: [0,1],
                iconCls : 'breload',
                disabled : true,
                hidden : true,
                handler : this.onRevertirMovimientoEntidad
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
                text : 'Rep. Credito P. NO IATA',
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
            //this.grid.addListener('keydown', this.controlarEscape,this);

            this.fecha_ini.on('select', function (rec, date) {
                let fecha_max = new Date(date.getFullYear() ,date.getMonth(), this.diasMes[date.getMonth()])
                this.fecha_fin.setMaxValue(fecha_max);
                this.fecha_fin.setMinValue(fecha_max);
            },this);

            this.init();

        },

        /*controlarEscape: function(e){ console.log('evento',e);
            //if(e.escKey && e.getKey()==27) {
                this.reload();
            //}
        },*/

        onExcluirAgencia: function (){
            //console.log('campos ', this.fecha_ini.getValue(), this.fecha_fin.getValue(), this.store.data.items[0].data.status);
            if ( this.fecha_ini.getValue() != '' && this.fecha_fin.getValue() != '' ) {
                let rec = {};
                if (this.getSelectedData()) {
                    rec = this.getSelectedData();
                    rec.fecha = this.fecha_ini.getValue();
                } else {
                    rec = {selector: 'generico', fecha: this.fecha_ini.getValue(), fecha_fin: this.fecha_fin.getValue()};
                }

                if ( this.store.data.items[0] != undefined ) {
                    if (this.store.data.items[0].data.status == 'generado' || this.store.data.items[0].data.status == 'abonado' || this.store.data.items[0].data.status == 'enviado') {
                        Ext.Msg.show({
                            title: 'Información',
                            msg: '<b>Estimado Usuario:<br>Ya no puede excluir agencias para este periodo, por que ya se genero sus correspondientes ACMs. para el periodo ' + this.cmbFechas.getRawValue() + '.</b>',
                            buttons: Ext.Msg.OK,
                            width: 512,
                            icon: Ext.Msg.WARNING
                        });
                    } else {
                        Phx.CP.loadWindows('../../../sis_obingresos/vista/calculo_over_comison/ExcluirAgencia.php',
                            'Agencias Excluidas',
                            {
                                width: 900,
                                height: 600
                            },
                            rec,
                            this.idContenedor,
                            'ExcluirAgencia'
                        );
                    }
                }else{
                    Phx.CP.loadWindows('../../../sis_obingresos/vista/calculo_over_comison/ExcluirAgencia.php',
                        'Agencias Excluidas',
                        {
                            width: 900,
                            height: 600
                        },
                        rec,
                        this.idContenedor,
                        'ExcluirAgencia'
                    );
                }
            }else{
                if ( this.fecha_ini.getValue() == '' && this.fecha_fin.getValue() == '' && this.cmbFechas.getRawValue() == '') {
                    Ext.Msg.show({
                        title: 'Información',
                        msg: '<b>Estimado Usuario:<br>Debe definir una fecha inicio y fecha fin, para excluir agencias.</b>',
                        buttons: Ext.Msg.OK,
                        width: 512,
                        icon: Ext.Msg.WARNING
                    });
                }else if ( this.cmbFechas.getRawValue() != '' ){
                    Ext.Msg.show({
                        title: 'Información',
                        msg: '<b>Estimado Usuario:<br>Ya no puede excluir agencias para este periodo, por que ya se genero sus correspondientes ACMs. para el periodo '+this.cmbFechas.getRawValue()+'.</b>',
                        buttons: Ext.Msg.OK,
                        width: 512,
                        icon: Ext.Msg.WARNING
                    });
                }
            }
        },

        onValidarMovimientoEntidad : function(){


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

            Ext.Ajax.request({
                url:'../../sis_obingresos/control/CalculoOverComison/verificarPeriodoGenerado',
                params:{
                    fecha_ini : this.store.baseParams.fecha_desde,
                    fecha_fin : this.store.baseParams.fecha_hasta,
                    tipo      : this.store.baseParams.tipo
                },
                success:function(resp){
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    //console.log('reg Validacion',reg);

                    if (reg.ROOT.datos.estado_generado == 'validado'){
                        Ext.Msg.show({
                            title: 'Información',
                            msg: '<b style="text-align: justify;"> Estimado Usuario: <br><br> Ya se hizo la validación correspondiente para el periodo que se esta intentando Validar.</b>',
                            buttons: Ext.Msg.OK,
                            width: 512,
                            icon: Ext.Msg.INFO
                        });
                    }else{

                        this.store.baseParams.momento = 1;
                        Phx.CP.loadingShow();
                        Ext.Ajax.request({
                            url: '../../sis_obingresos/control/CalculoOverComison/generarMovimientoEntidad',
                            params: {
                                fecha_desde: this.store.baseParams.fecha_desde,
                                fecha_hasta: this.store.baseParams.fecha_hasta,
                                tipo: this.store.baseParams.tipo,
                                momento: this.store.baseParams.momento,
                                accion: 'validar'
                            },
                            success: function (resp) {
                                //var reg = Ext.decode(Ext.util.Format.trim(resp.responseText));
                                //console.log('reg Validar generarMovimientoEntidad', reg.ROOT.datos);
                                Phx.CP.loadingHide();

                                var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText)).ROOT.datos;
                                console.log('onValidarMovimientoEntidad objRes', objRes);

                                /*if ( objRes[0].Result ) {
                                    Ext.Msg.show({
                                        title: 'Información',
                                        msg: '<b>'+objRes[0].Message+'</b>',
                                        buttons: Ext.Msg.OK,
                                        width: 512,
                                        icon: Ext.Msg.INFO
                                    });
                                }else {*/
                                    Ext.Msg.show({
                                        title: 'Información',
                                        msg: '<b style="text-align: justify;"> Estimado Usuario: <br><br> Se Valido exitosamente el periodo seleccionado.</b>',
                                        fn: function (btn) {
                                            if (btn == 'ok') {
                                                this.getBoton('btnValidar').setVisible(false);
                                                if (this.cmbTipo.getValue() == 'NO-IATA') {
                                                    if ( this.nombreVista == 'calculoComercial' ) {
                                                        this.getBoton('btnGenerar').setVisible(false);
                                                    }else {
                                                        this.getBoton('btnGenerar').setVisible(true);
                                                    }
                                                }else{
                                                    if ( this.nombreVista == 'calculoComercial' ) {
                                                        this.getBoton('btnGenerar').setVisible(false);
                                                    }else{
                                                        this.getBoton('btnGenerar').setVisible(true);
                                                    }
                                                    this.getBoton('btnCreditoPortal').setVisible(false);
                                                }
                                                this.store.baseParams.momento = 1;
                                                this.load({params: {start: 0, limit: 50}});
                                            }
                                        },
                                        buttons: Ext.Msg.OK,
                                        width: 512,
                                        maxWidth: 1024,
                                        icon: Ext.Msg.INFO,
                                        scope: this
                                    });
                                //}
                            },
                            failure: this.conexionFailure,
                            timeout: this.timeout,
                            scope: this
                        });
                    }
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },

        onGenerarMovimientoEntidad : function(){

            var thas = this;
            fecha_desde = this.fecha_ini.getValue();
            dia =  fecha_desde.getDate();
            dia = dia < 10 ? "0"+dia : dia;
            mes = fecha_desde.getMonth() + 1;
            mes = mes < 10 ? "0"+mes : mes;
            anio = fecha_desde.getFullYear();
            thas.store.baseParams.fecha_desde = dia + "/" + mes + "/" + anio;

            fecha_hasta = this.fecha_fin.getValue();
            dia =  fecha_hasta.getDate();
            dia = dia < 10 ? "0"+dia : dia;
            mes = fecha_hasta.getMonth() + 1;
            mes = mes < 10 ? "0"+mes : mes;
            anio = fecha_hasta.getFullYear();
            thas.store.baseParams.fecha_hasta = dia + "/" + mes + "/" + anio;

            thas.store.baseParams.tipo = this.cmbTipo.getValue();

            Ext.Ajax.request({
                url:'../../sis_obingresos/control/CalculoOverComison/verificarPeriodoGenerado',
                params:{
                    fecha_ini : thas.store.baseParams.fecha_desde,
                    fecha_fin : thas.store.baseParams.fecha_hasta,
                    tipo      : thas.store.baseParams.tipo
                },
                success:function(resp){
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    console.log('reg Verificacion',reg);

                    if (reg.ROOT.datos.estado_generado == 'generado'){
                        /*this.store.baseParams.momento = 0;
                        this.load({params: {start: 0, limit: 50}});*/

                        Ext.Msg.show({
                            title: 'Información',
                            msg: '<b style="text-align: justify;"> Estimado Usuario: <br><br> Ya se tiene generado los ACMs correspondiente para el periodo que se esta intentando Generar.</b>',
                            buttons: Ext.Msg.OK,
                            width: 512,
                            icon: Ext.Msg.INFO
                        });

                    }else{
                        this.store.baseParams.momento = 1;
                        //this.load({params: {start: 0, limit: 50}});
                        Phx.CP.loadingShow();
                        Ext.Ajax.request({
                            url:'../../sis_obingresos/control/CalculoOverComison/generarMovimientoEntidad',
                            params:{
                                fecha_desde : thas.store.baseParams.fecha_desde,
                                fecha_hasta : thas.store.baseParams.fecha_hasta,
                                tipo        : thas.store.baseParams.tipo,
                                momento     : thas.store.baseParams.momento,
                                accion      : 'generar'
                            },
                            success:function(resp){
                                //var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                                //console.log('reg Abonar',reg.ROOT.datos);
                                Phx.CP.loadingHide();

                                var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText)).ROOT.datos;
                                console.log('onGenerarMovimientoEntidad objRes', objRes);

                                /*if ( objRes[0].Result ) {
                                    Ext.Msg.show({
                                        title: 'Información',
                                        msg: '<b>'+objRes[0].Message+'</b>',
                                        buttons: Ext.Msg.OK,
                                        width: 512,
                                        icon: Ext.Msg.INFO
                                    });
                                }else {*/
                                    if (objRes.validacion_inicio == 'activo') {
                                        Ext.Msg.show({
                                            title: 'Información',
                                            msg: '<b style="text-align: justify;"> Estimado Usuario: <br><br> Se Genero los ACMs exitosamente para el periodo seleccionado.</b>',
                                            fn: function (btn) {
                                                if (btn == 'ok') {

                                                    this.getBoton('btnValidar').setVisible(false);
                                                    this.getBoton('btnGenerar').setVisible(false);

                                                    if ( thas.store.baseParams.tipo == 'IATA'){
                                                        this.getBoton('btnAbonar').setVisible(false);
                                                        if ( this.nombreVista == 'calculoComercial' ) {
                                                            this.getBoton('btnFileBSP').setVisible(false);
                                                        }else{
                                                            this.getBoton('btnFileBSP').setVisible(true);
                                                        }
                                                    }else{
                                                        if ( this.nombreVista == 'calculoComercial' ) {
                                                            this.getBoton('btnAbonar').setVisible(false);
                                                        }else{
                                                            this.getBoton('btnAbonar').setVisible(true);
                                                        }
                                                    }

                                                    this.store.baseParams.momento = 2;
                                                    this.load({params: {start: 0, limit: 50}});
                                                }
                                            },
                                            buttons: Ext.Msg.OK,
                                            width: 512,
                                            icon: Ext.Msg.INFO,
                                            scope: this
                                        });
                                    } else {
                                        Ext.Msg.show({
                                            title: 'Información',
                                            msg: '<b style="text-align: justify;"> Estimado Usuario: <br><br> No se pudo realizar el Abono correspondiente para el periodo seleccionado.</b>',
                                            buttons: Ext.Msg.OK,
                                            width: 512,
                                            icon: Ext.Msg.INFO
                                        });
                                    }
                                //}

                            },
                            failure: this.conexionFailure,
                            timeout:this.timeout,
                            scope:this
                        });
                    }
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },

        onAbonarMovimientoEntidad : function(){

            var thas = this;
            fecha_desde = this.fecha_ini.getValue();
            dia =  fecha_desde.getDate();
            dia = dia < 10 ? "0"+dia : dia;
            mes = fecha_desde.getMonth() + 1;
            mes = mes < 10 ? "0"+mes : mes;
            anio = fecha_desde.getFullYear();
            thas.store.baseParams.fecha_desde = dia + "/" + mes + "/" + anio;

            fecha_hasta = this.fecha_fin.getValue();
            dia =  fecha_hasta.getDate();
            dia = dia < 10 ? "0"+dia : dia;
            mes = fecha_hasta.getMonth() + 1;
            mes = mes < 10 ? "0"+mes : mes;
            anio = fecha_hasta.getFullYear();
            thas.store.baseParams.fecha_hasta = dia + "/" + mes + "/" + anio;

            thas.store.baseParams.tipo = this.cmbTipo.getValue();

            Ext.Ajax.request({
                url:'../../sis_obingresos/control/CalculoOverComison/verificarPeriodoGenerado',
                params:{
                    fecha_ini : thas.store.baseParams.fecha_desde,
                    fecha_fin : thas.store.baseParams.fecha_hasta,
                    tipo      : thas.store.baseParams.tipo
                },
                success:function(resp){
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    console.log('reg Verificacion',reg);

                    if (reg.ROOT.datos.estado_generado == 'abonado'){
                        /*this.store.baseParams.momento = 0;
                        this.load({params: {start: 0, limit: 50}});*/

                        Ext.Msg.show({
                            title: 'Información',
                            msg: '<b style="text-align: justify;"> Estimado Usuario: <br><br> Ya se tiene el Abono correspondiente para el periodo que se esta intentando Abonar.</b>',
                            buttons: Ext.Msg.OK,
                            width: 512,
                            icon: Ext.Msg.INFO
                        });

                    }else{
                        this.store.baseParams.momento = 1;
                        //this.load({params: {start: 0, limit: 50}});
                        Phx.CP.loadingShow();
                        Ext.Ajax.request({
                            url:'../../sis_obingresos/control/CalculoOverComison/generarMovimientoEntidad',
                            params:{
                                fecha_desde : thas.store.baseParams.fecha_desde,
                                fecha_hasta : thas.store.baseParams.fecha_hasta,
                                tipo        : thas.store.baseParams.tipo,
                                momento     : thas.store.baseParams.momento,
                                accion      : 'abonar'
                            },
                            success:function(resp){
                                //var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                                //console.log('reg Abonar',reg.ROOT.datos);
                                Phx.CP.loadingHide();

                                var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText)).ROOT.datos;
                                //console.log('onGenerarMovimientoEntidad objRes', objRes);

                                if (objRes.validacion_inicio == 'activo'){
                                    Ext.Msg.show({
                                        title: 'Información',
                                        msg: '<b style="text-align: justify;"> Estimado Usuario: <br><br> Se Abono exitosamente para el periodo seleccionado.</b>',
                                        fn: function (btn){
                                            if(btn == 'ok'){
                                                this.getBoton('btnValidar').setVisible(false);
                                                this.getBoton('btnGenerar').setVisible(false);
                                                this.getBoton('btnAbonar').setVisible(false);
                                                this.store.baseParams.momento = 3;
                                                this.load({params: {start: 0, limit: 50}});
                                            }
                                        },
                                        buttons: Ext.Msg.OK,
                                        width: 512,
                                        icon: Ext.Msg.INFO,
                                        scope:this
                                    });
                                }else{
                                    Ext.Msg.show({
                                        title: 'Información',
                                        msg: '<b style="text-align: justify;"> Estimado Usuario: <br><br> No se pudo realizar el Abono correspondiente para el periodo seleccionado.</b>',
                                        buttons: Ext.Msg.OK,
                                        width: 512,
                                        icon: Ext.Msg.INFO
                                    });
                                }

                            },
                            failure: this.conexionFailure,
                            timeout:this.timeout,
                            scope:this
                        });
                    }
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },

        preparaMenu: function(n) {

            var rec = this.getSelectedData();
            var tb =this.tbar;
            Phx.vista.CalculoOverComison.superclass.preparaMenu.call(this,n);
            if (rec.status == 'abonado'){
                this.getBoton('btnRevertir').enable();
            }

        },

        liberaMenu:function(){
            var tb = Phx.vista.CalculoOverComison.superclass.liberaMenu.call(this);
            if(tb){
                this.getBoton('btnRevertir').disable();
            }
            return tb
        },

        onRevertirMovimientoEntidad : function (){

            let record = this.getSelectedData();
            console.log('record', record);
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_obingresos/control/CalculoOverComison/revertirMovimientoEntidad',
                params:{
                    AcmKey : record.AcmKey,
                    DocumentNumber : record.DocumentNumber
                },
                success:function(resp){
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    console.log('reg Revertir',reg);
                    Phx.CP.loadingHide();
                    Ext.Msg.show({
                        title: 'Información',
                        msg: '<b style="text-align: justify;"> Estimado Usuario: <br><br> Se Revirtio exitosamente el abono para el Nro. ACM <span style="color: green">'+record.DocumentNumber+'</span>.</b>',
                        buttons: Ext.Msg.OK,
                        width: 512,
                        icon: Ext.Msg.INFO
                    });
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
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
                    params: {
                        fecha_desde   : fecha_ini,
                        fecha_hasta   : fecha_fin,
                        tipo        : tipo,
                        momento     : 1,
                        accion      : 'enviar'
                    },
                    success: function (resp) {
                        var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                        console.log('envio over',reg);
                        Ext.Msg.show({
                            title: 'Información',
                            msg: '<b style="text-align: justify;">Estimado Usuario: <br><br> El Reporte se esta Generando, una vez concluido el proceso se le enviara a su correo la información correspondiente.</b>',
                            fn: function (btn){
                                if(btn == 'ok'){
                                    this.getBoton('btnFileBSP').setVisible(false);
                                    this.store.baseParams.momento = 3;
                                    this.load({params: {start: 0, limit: 50}});
                                }
                            },
                            buttons: Ext.Msg.OK,
                            width: 512,
                            icon: Ext.Msg.INFO,
                            scope:this
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
                            msg: '<b style="text-align: justify;">Estimado Usuario: ' + '<br><br>' + ' El reporte se esta generando, una vez concluido el proceso se le enviara a su correo la información correspondiente.</b>',
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
            console.log('onBtnBuscar FEA');
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

            Ext.Ajax.request({
                url:'../../sis_obingresos/control/CalculoOverComison/verificarPeriodoGenerado',
                params:{
                    fecha_ini : this.store.baseParams.fecha_desde,
                    fecha_fin : this.store.baseParams.fecha_hasta,
                    tipo      : this.store.baseParams.tipo
                },
                success:function(resp){
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    console.log('reg FEA',reg);
                    if (reg.ROOT.datos.estado_generado == 'generado'){
                        this.store.baseParams.momento = 0;
                        //this.onBtnBuscar();
                        this.load({params: {start: 0, limit: 50}});
                    }else{
                        //this.load({params: {start: 0, limit: 50}});
                        this.store.baseParams.momento = 1;
                        //this.onBtnBuscar();
                        this.load({params: {start: 0, limit: 50}});
                    }
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });

            //this.load({params: {start: 0, limit: 50}});
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
            }else if(fieldName == 'habilitado'){
                /*let rec = {usuario : 'generico'};
                Phx.CP.loadWindows('../../../sis_obingresos/vista/calculo_over_comison/ExcluirAgencia.php',
                    'Agencias Excluidas',
                    {
                        width:900,
                        height:600
                    },
                    rec,
                    this.idContenedor,
                    'ExcluirAgencia'
                );*/
                let row = this.getSelectedData();
                if( row.status == 'generado' || row.status == 'abonado'){
                    Ext.Msg.show({
                        title: 'Información',
                        msg: '<b>Estimado Usuario:<br>Ya no puede excluir agencias para este periodo, por que ya se genero sus correspondientes ACMs. para el periodo '+this.cmbFechas.getRawValue()+'.</b>',
                        fn: function (btn) {
                            if (btn == 'ok') {
                                this.reload();
                            }
                        },
                        buttons: Ext.Msg.OK,
                        width: 512,
                        icon: Ext.Msg.WARNING,
                        scope: this
                    });

                }else {
                    this.onFormExcluirAgencia();
                }
            }

        },

        onFormExcluirAgencia:  function (){
            var record = this.getSelectedData();
            this.formExcluirAgencia();
            this.formExcluir.getForm().findField('iata_code').setValue(record.IataCode);
            this.formExcluir.getForm().findField('office_id').setValue(record.OfficeId);
            this.formExcluir.getForm().findField('f_ini').setValue(this.fecha_ini.getValue());
            this.formExcluir.getForm().findField('f_fin').setValue(this.fecha_fin.getValue());
            this.formExcluir.getForm().findField('obs').setValue('Id. '+record.AcmKey+' [ '+record.TypePOS+' ( '+record.CommissionDescription+' ) ]');
            this.formExcluir.getForm().findField('estado').setValue('A');
            this.windowExcluir.show();
        },
        formExcluirAgencia: function () {

            this.formExcluir = new Ext.form.FormPanel({
                id: this.idContenedor + '_EXCAGE',
                items: [
                    new Ext.form.ComboBox({
                        name: 'iata_code',
                        fieldLabel: 'Codigo Iata',
                        allowBlank: false,
                        disabled: false,
                        emptyText: '',
                        msgTarget:'side',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_ventas_facturacion/control/ReporteVentas/listarCodigoIataStage',
                            id: 'iata_code',
                            root: 'datos',
                            sortInfo: {
                                field: 'iata_code',
                                direction: 'DESC'
                            },
                            totalProperty: 'total',
                            fields: ['iata_code'],
                            remoteSort: true,
                            baseParams: {_adicionar : 'si', par_filtro: 'iata_code'}
                        }),
                        valueField: 'iata_code',
                        displayField: 'iata_code',
                        gdisplayField: 'iata_code',
                        hiddenName: 'iata_code',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><p><b><span style="color: #B066BB;">{iata_code}</span></b></p></div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 25,
                        queryDelay: 1000,
                        gwidth: 250,
                        resizable:true,
                        minChars: 2,
                        anchor: '95%',
                        hidden : false,
                        style:'margin-bottom: 10px;',
                        style : {fontWeight : 'bolder', color : '#00B167'},
                        editable: false,
                        disabled: true
                    }),
                    new Ext.form.ComboBox({
                        name: 'office_id',
                        msgTarget:'side',
                        fieldLabel: 'Office ID',
                        allowBlank: false,
                        disabled: false,
                        emptyText: '',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_ventas_facturacion/control/ReporteVentas/listarPuntoVentaOfficeIdStage',
                            id: 'office_id',
                            root: 'datos',
                            sortInfo: {
                                field: 'office_id',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['office_id','name_pv'],
                            remoteSort: true,
                            baseParams: {_adicionar : 'si', par_filtro:'office_id#name_pv'}
                        }),
                        valueField: 'office_id',
                        displayField: 'office_id',
                        gdisplayField: 'office_id',
                        hiddenName: 'office_id',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><p><b><span style="color: #B066BB;">{office_id}</span> <span style="color: #00B167;"> ({name_pv})</span> </b></p></div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 25,
                        anchor: '95%',
                        queryDelay: 1000,
                        gwidth: 250,
                        resizable:true,
                        minChars: 2,
                        hidden : false,
                        style : {fontWeight : 'bolder', color : '#00B167'},
                        editable: false,
                        disabled: true
                    }),
                    new Ext.form.DateField({
                        msgTarget:'side',
                        name : 'f_ini',
                        fieldLabel : 'Habilitado Desde',
                        allowBlank : false,
                        width : 177,
                        gwidth : 125,
                        format : 'd/m/Y',
                        editable: false,
                        disabled: true,
                        style : {fontWeight : 'bolder', color : 'red'},
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    }),
                    new Ext.form.DateField({
                        msgTarget:'side',
                        name: 'f_fin',
                        fieldLabel: 'Habilitado Hasta',
                        allowBlank: false,
                        width : 177,
                        gwidth: 125,
                        format: 'd/m/Y',
                        editable: false,
                        disabled: true,
                        style : {fontWeight : 'bolder', color : 'red'},
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    }),
                    new Ext.form.TextArea({
                        msgTarget:'side',
                        name: 'obs',
                        fieldLabel: 'Observaciones',
                        allowBlank: false,
                        anchor: '95%',
                        gwidth: 300,
                        maxLength:2046,
                        style : {fontWeight : 'bolder', color : '#B066BB'}
                    }),
                    new Ext.form.TextField({
                        msgTarget:'side',
                        name: 'estado',
                        fieldLabel: 'Estado',
                        allowBlank: true,
                        width : 177,
                        gwidth: 100,
                        maxLength:20,
                        style : {fontWeight : 'bolder', color : '#B066BB'}
                    })
                ],
                autoScroll: false,
                autoDestroy: true,
                autoScroll: true
            });


            // Definicion de la ventana que contiene al formulario
            this.windowExcluir = new Ext.Window({
                // id:this.idContenedor+'_W',
                title: 'Formulario Exclusión Agencia',
                modal: true,
                width: 500,
                height: 300,
                bodyStyle: 'padding:5px;',
                layout: 'fit',
                hidden: true,
                autoScroll: false,
                maximizable: true,
                buttons: [
                    {
                        text: 'Guardar',
                        arrowAlign: 'bottom',
                        handler: this.onSubmitExcluir,
                        argument: {
                            'news': false
                        },
                        scope: this
                    },
                    {
                        text: 'Declinar',
                        handler: this.onDeclinarExcluir,
                        scope: this
                    }
                ],
                items: this.formExcluir,
                // autoShow:true,
                autoDestroy: true,
                closeAction: 'hide'
            });

            this.windowExcluir.on('hide', function (p) {
               this.reload();
            },this);
        },

        onSubmitExcluir: function () {
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_obingresos/control/ExcluirAgencia/insertarExcluirAgencia',
                success: this.successExcluir,
                failure: this.failureExcluir,
                params: {
                    'iataCode'  : this.formExcluir.getForm().findField('iata_code').getValue(),
                    'officeId'  : this.formExcluir.getForm().findField('office_id').getValue(),
                    'f_ini'     : this.formExcluir.getForm().findField('f_ini').getValue(),
                    'f_fin'     : this.formExcluir.getForm().findField('f_fin').getValue(),
                    'obs'       : this.formExcluir.getForm().findField('obs').getValue()
                },
                timeout: this.timeout,
                scope: this
            });

        },

        successExcluir: function (resp) {

            this.windowExcluir.hide();
            Phx.vista.CalculoOverComison.superclass.successSave.call(this, resp);

            var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText)).ROOT.datos;
            Ext.Msg.show({
                title: 'Información',
                msg: '<b>'+objRes[0].Message+'</b>',
                buttons: Ext.Msg.OK,
                width: 512,
                icon: Ext.Msg.INFO
            });

            var rec = this.getSelectedData();

            if ( objRes[0].Result == 1 ) {
                Ext.Ajax.request({
                    url: '../../sis_obingresos/control/ExcluirAgencia/registrarExcluirAgencia',
                    success: this.successExcluir,
                    failure: this.failureExcluir,
                    params: {
                        'id_acm_key'    : rec.AcmKey,
                        'iata_code'     : this.formExcluir.getForm().findField('iata_code').getValue(),
                        'office_id'     : this.formExcluir.getForm().findField('office_id').getValue(),
                        'fecha_desde'   : this.formExcluir.getForm().findField('f_ini').getValue(),
                        'fecha_hasta'   : this.formExcluir.getForm().findField('f_fin').getValue(),
                        'observacion'   : this.formExcluir.getForm().findField('obs').getValue()
                    },
                    timeout: this.timeout,
                    scope: this
                });
            }
        },

        failureExcluir: function (resp) {
            Phx.CP.loadingHide();
            Phx.vista.CalculoOverComison.superclass.conexionFailure.call(this, resp);
        },

        onDeclinarExcluir: function () {
            this.reload();
            this.windowExcluir.hide();
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

            var self = this;
            this.cmbTipo.on('select', function (combo, record, index) {

                this.fecha_ini.allowBlank = false;
                this.fecha_fin.allowBlank = false;


                this.cmbFechas.setValue('');
                this.modificado = true;
                if ( this.fecha_ini.getValue() != '' &&  this.fecha_fin.getValue() != '' ) {

                    if (record.data.tipo == "IATA") {
                        this.getBoton('btnFileBSP').setVisible(true);
                        this.getBoton('btnCreditoPortal').setVisible(false);
                        this.getBoton('btnAbonar').setVisible(false);
                        this.getBoton('btnRevertir').setVisible(false);
                    } else {
                        this.getBoton('btnFileBSP').setVisible(false);
                        this.getBoton('btnCreditoPortal').setVisible(true);
                        this.getBoton('btnAbonar').setVisible(true);
                        if ( this.nombreVista == 'calculoComercial' ) {
                            this.getBoton('btnRevertir').setVisible(false);
                        }else{
                            this.getBoton('btnRevertir').setVisible(true);
                        }
                    }
                    this.getBoton('btnAbonar').setVisible(false);
                    this.store.baseParams.tipo = record.data.tipo;
                    this.store.baseParams.momento = 0;
                    Ext.Ajax.request({
                        url: '../../sis_obingresos/control/CalculoOverComison/verificarPeriodoGenerado',
                        params: {
                            fecha_ini: this.store.baseParams.fecha_desde,
                            fecha_fin: this.store.baseParams.fecha_hasta,
                            tipo: this.store.baseParams.tipo
                        },
                        success: function (resp) {
                            var reg = Ext.decode(Ext.util.Format.trim(resp.responseText));
                            console.log('reg select Cmb Tipo', reg);
                            if (reg.ROOT.datos.estado_generado == 'elaborado') {
                                if ( this.nombreVista == 'calculoComercial' ) {
                                    this.getBoton('btnValidar').setVisible(true);
                                }else{
                                    this.getBoton('btnValidar').setVisible(false);
                                }
                                this.getBoton('btnGenerar').setVisible(false);
                                this.getBoton('btnAbonar').setVisible(false);

                                if (record.data.tipo == "IATA") {
                                    this.getBoton('btnFileBSP').setVisible(false);
                                }
                                this.store.baseParams.momento = 0;
                                this.load({params: {start: 0, limit: 50}});
                            } else if (reg.ROOT.datos.estado_generado == 'validado'){
                                this.getBoton('btnValidar').setVisible(false);
                                if ( this.nombreVista == 'calculoComercial' ) {
                                    this.getBoton('btnGenerar').setVisible(false);
                                }else{
                                    this.getBoton('btnGenerar').setVisible(true);
                                }
                                this.getBoton('btnAbonar').setVisible(false);
                                if (record.data.tipo == "IATA") {
                                    this.getBoton('btnFileBSP').setVisible(false);
                                }
                                /*if ( this.cmbTipo.getValue() == 'NO-IATA' ){
                                    this.getBoton('btnAbonar').setVisible(true);
                                }*/
                                this.store.baseParams.momento = 1;
                                this.load({params: {start: 0, limit: 50}});
                            } else if (reg.ROOT.datos.estado_generado == 'generado'){
                                this.getBoton('btnValidar').setVisible(false);
                                this.getBoton('btnGenerar').setVisible(false);

                                if (record.data.tipo == "IATA") {
                                    if ( this.nombreVista == 'calculoComercial' ) {
                                        this.getBoton('btnFileBSP').setVisible(false);
                                    }else{
                                        this.getBoton('btnFileBSP').setVisible(true);
                                    }
                                }
                                if ( this.cmbTipo.getValue() == 'NO-IATA' ){
                                    if ( this.nombreVista == 'calculoComercial' ) {
                                        this.getBoton('btnAbonar').setVisible(false);
                                    }else{
                                        this.getBoton('btnAbonar').setVisible(true);
                                    }
                                }
                                this.store.baseParams.momento = 2;
                                this.load({params: {start: 0, limit: 50}});
                            } else if (reg.ROOT.datos.estado_generado == 'abonado'){
                                this.getBoton('btnValidar').setVisible(false);
                                this.getBoton('btnGenerar').setVisible(false);
                                this.getBoton('btnAbonar').setVisible(false);

                                if (record.data.tipo == "IATA") {
                                    this.getBoton('btnFileBSP').setVisible(false);
                                }
                                this.store.baseParams.momento = 3;
                                this.load({params: {start: 0, limit: 50}});
                            } else if (reg.ROOT.datos.estado_generado == 'enviado'){
                                this.getBoton('btnValidar').setVisible(false);
                                this.getBoton('btnGenerar').setVisible(false);
                                this.getBoton('btnAbonar').setVisible(false);

                                if (record.data.tipo == "IATA") {
                                    this.getBoton('btnFileBSP').setVisible(false);
                                }
                                this.store.baseParams.momento = 4;
                                this.load({params: {start: 0, limit: 50}});
                            }
                        },
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });
                }else{
                    Ext.Msg.show({
                        title: 'Información',
                        msg: '<b style="text-align: justify;">Estimado Usuario: ' + '<br><br>' + ' Debe seleccionar los campos <span style="color: #00B167; ">Fecha Inicio</span> y <span style="color: #FF8F85; ">Fecha Fin</span> para poder listar los registros correspondientes al periodo que desea.</b>',
                        buttons: Ext.Msg.OK,
                        width: 512,
                        icon: Ext.Msg.INFO
                    });
                }
                /*console.log('boliviar', self.store.data.items);
                if ( self.store.data.items[0].data.status == 'generado' || self.store.data.items[0].data.status == 'abonado' ){
                    this.getBoton('btnFileBSP').setVisible(true);
                }else{
                    this.getBoton('btnFileBSP').setVisible(false);
                }*/

            }, this);

            this.cmbFechas.on('select', function (combo, record, index) {

                this.fecha_ini.allowBlank = true;
                this.fecha_ini.setValue('');
                this.fecha_ini.modificado = true;

                this.fecha_fin.allowBlank = true;
                this.fecha_fin.setValue('');
                this.fecha_fin.modificado = true;

                this.cmbTipo.allowBlank = true;
                this.cmbTipo.setValue('');
                this.cmbTipo.modificado = true;


                if(record.data.tipo == "IATA"){
                    this.getBoton('btnFileBSP').setVisible(false);
                    this.getBoton('btnCreditoPortal').setVisible(false);
                    //this.getBoton('btnAbonar').setVisible(true);
                    this.getBoton('btnRevertir').setVisible(false);
                }else{
                    this.getBoton('btnFileBSP').setVisible(false);
                    this.getBoton('btnCreditoPortal').setVisible(true);
                    //this.getBoton('btnAbonar').setVisible(true);
                    this.getBoton('btnRevertir').setVisible(true);
                }

                this.getBoton('btnAbonar').setVisible(false);
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

                this.getBoton('btnValidar').setVisible(false);
                this.getBoton('btnAbonar').setVisible(false);
                if(record.data.tipo == "IATA") {
                    this.store.baseParams.momento = 4;
                }else{
                    this.store.baseParams.momento = 3;
                }
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
                config: {
                    name: 'habilitado',
                    fieldLabel: 'Habilitado',
                    allowBlank: true,
                    anchor: '70%',
                    gwidth: 85,

                    renderer: function (value, p, record, rowIndex, colIndex) {
                        //console.log('value', value == 'true', value === 'true');
                        if (value === 'true') {
                            var checked = 'checked';
                        }
                        return String.format('<div style="vertical-align:middle;text-align:center;"><input style="height:30px;width:30px;"  type="checkbox"  {0}></div>', checked);
                    }
                },
                type: 'Checkbox',
                id_grupo: 1,
                grid: true,
                form: true
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
                    name: 'fecha_acm',
                    fieldLabel: 'Fecha ACM',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                id_grupo:1,
                grid:true,
                form:true
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
            },
            {
                config:{
                    fieldLabel: "% Comición",
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
                    fieldLabel: "Estado",
                    gwidth: 100,
                    name: 'status',
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
            {name:'OfficeId', type: 'string'},
            {name:'status', type: 'string'},
            {name:'habilitado', type: 'string'}
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
