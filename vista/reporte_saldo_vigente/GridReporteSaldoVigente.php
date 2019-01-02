<?php
/**
 * @package pxP
 * @file 	repkardex.php
 * @author 	RCM
 * @date	10/07/2013
 * @description	Archivo con la interfaz de usuario que permite la ejecucion de las funcionales del sistema
 */
include_once ('../../media/styles.php');
header("content-type:text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.GridReporteSaldoVigente = Ext.extend(Phx.gridInterfaz, {
        viewConfig: {
            getRowClass: function(record) {
                if(record.data.saldo_con_boleto < 0){
                    return 'prioridad_importanteA';
                }
            }
        },
        constructor : function(config) {
            this.maestro = config;
            //this.initButtons=[this.tipo_agencia];
            this.description = this.maestro.tipo_agencia;
            Phx.vista.GridReporteSaldoVigente.superclass.constructor.call(this, config);
            this.init();
            this.bbar.add(this.lugar);
            this.addButton('ReporteGeneral',{
                text: 'Reporte General <br> de Saldos',
                iconCls: 'bexcel',
                disabled: true,
                handler: this.reporteGeneral,
                tooltip: '<b>Reporte General de Saldos</b>',
                scope:this
            });
            this.addButton('Reporte',{
                text: 'Reporte Estado de <br> Cuentas',
                iconCls: 'blist',
                disabled: true,
                handler: this.onButtonReporte,
                tooltip: '<b>Estado de cuenta depositos vs boletos</b>',
                scope:this
            });
            // this.addButton('Movimientos',{
            //     text: 'Reporte de Movimientos <br> por Periodo',
            //     iconCls: 'bexcel',
            //     disabled: true,
            //     handler: this.onButtonMovimientos,
            //     tooltip: '<b>Estado de cuenta credito vs debito</b>',
            //     scope:this
            // });
            this.addButton('Report',{
                text :'Estado de Cuenta <br>Detallado',
                iconCls : 'bprint',
                disabled: true,
                handler : this.onButtonReporteVi,
                tooltip : '<b>Estado de Cuenta Detallado</b>'
            });
            this.addButton('Depositos',{
                text: 'Reporte de Depositos <br> Detallado',
                iconCls: 'bdocuments',
                disabled: true,
                handler: this.onButtonDeposito,
                tooltip: '<b>Reporte de Depositos Detallado</b>',
                scope:this
            });
            this.tbar.addField(this.fecha_ini);
            this.tbar.addField(this.fecha_fin);
            this.tbar.addField(this.tipo_agencia);
            this.tbar.addField(this.forma_pago);
           // this.tbar.addField(this.lugar);
           //this.fecha_ini.setValue(new Date().dateFormat('d/m/Y'));
           //this.fecha_fin.setValue(new Date().dateFormat('d/m/Y'));

           this.fecha_ini.on('select',function(value){
               this.capturaFiltros();
           },this);

            this.fecha_fin.on('select',function(value){
                this.capturaFiltros();
            },this);

            this.tipo_agencia.on('select', function( combo, record, index){
                this.capturaFiltros();
            },this);
            this.forma_pago.on('select', function( combo, record, index){
                this.capturaFiltros();
            },this);
            this.lugar.on('select', function( combo, record, index){
                this.capturaFiltros();
            },this);
            this.cmbPeriodo.on('select',this.capturarEventos, this);
            this.cmbPeriodo.setValue(null);
            this.cmbPeriodo.setRawValue('Periodo Vigente');
        },

        preparaMenu: function () {
            var tb = this.tbar;
            var rec = this.sm.getSelected();
                this.getBoton('ReporteGeneral').enable();
                this.getBoton('Reporte').enable();
                //this.getBoton('Movimientos').enable();
                this.getBoton('Report').enable();
                this.getBoton('Depositos').enable();
            },

        liberaMenu : function(){
            var rec = this.sm.getSelected();
          this.getBoton('ReporteGeneral').disable();
          this.getBoton('Reporte').disable();
          //this.getBoton('Movimientos').disable();
          this.getBoton('Report').disable();
          this.getBoton('Depositos').disable();
          Phx.vista.GridReporteSaldoVigente.superclass.liberaMenu.call(this);

        },




        tam_pag:1000,
        Atributos : [
            {
            config : {
                labelSeparator : '',
                inputType : 'hidden',
                name : 'id_agencia'
            },
            type : 'Field',
            form : true
           },
            {
                config : {
                    labelSeparator : '',
                    inputType : 'hidden',
                    name : 'id_periodo_venta'
                },
                type : 'Field',
                form : true
            },
            {
                config : {
                    labelSeparator : '',
                    inputType : 'hidden',
                    name : 'id_creditos'
                },
                type : 'Field',
                form : true
            },
            {
                config : {
                    labelSeparator : '',
                    inputType : 'hidden',
                    name : 'id_debitos'
                },
                type : 'Field',
                form : true
            },
            {
                config : {
                    name : 'nombre',
                    fieldLabel : 'Nombre Agencia',
                    allowBlank : false,
                    anchor : '100%',
                    gwidth : 300,
                    maxLength : 20


                },
                type : 'Field',
                filters : {
                    pfiltro : 'ag.nombre',
                    type : 'string'
                },
                id_grupo : 1,
                bottom_filter:true,
                grid : true,
                form : false
            },
            {
                config : {
                    name : 'codigo_int',
                    fieldLabel : 'OfficeId',
                    allowBlank : false,
                    anchor : '100%',
                    gwidth : 100,
                    maxLength : 20


                },
                type : 'Field',
                filters : {
                    pfiltro : 'ag.codigo_int',
                    type : 'string'
                },
                id_grupo : 1,
                grid : true,
                form : false
            },

            {
                config : {
                    name : 'tipo_agencia',
                    fieldLabel : 'Tipo Agencia',
                    allowBlank : false,
                    anchor : '200%',
                    gwidth : 100,
                    maxLength : 20


                },
                type : 'Field',
                filters : {
                    pfiltro : 'tipo_agencia',
                    type : 'string'
                },
                id_grupo : 1,
                grid : true,
                form : false
            },
            {
                config : {
                    name : 'formas_pago',
                    fieldLabel : 'Forma Pago',
                    allowBlank : false,
                    anchor : '100%',
                    gwidth : 100,
                    maxLength : 20


                },
                type : 'Field',
                filters : {
                    pfiltro : 'formas_pago',
                    type : 'string'
                },
                id_grupo : 1,
                grid : true,
                form : false
            },
            {
                config : {
                    name : 'codigo_ciudad',
                    fieldLabel : 'Ciudad',
                    allowBlank : false,
                    anchor : '100%',
                    gwidth : 80,
                    maxLength : 20,
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', record.data['codigo_ciudad']);
                        }
                        else{
                            return '<b><p align="right">Total: &nbsp;&nbsp; </p></b>';
                        }
                    }


                },
                type : 'Field',
                filters : {
                    pfiltro : 'l.codigo',
                    type : 'string'
                },
                id_grupo : 1,
                grid : true,
                form : false
            },


            {
                config : {
                    name : 'monto_credito',
                    fieldLabel : 'Creditos',
                    allowBlank : false,
                    anchor : '100%',
                    gwidth : 130,
                    maxLength : 20,
                    galign:'right',
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(record.data.monto_credito,'0,000.00'));
                        }
                        else{
                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.monto_credito,'0,000.00'));
                        }
                    }
                },
                type : 'NumberField',

                id_grupo : 1,
                grid : true,
                form : false
            },
            {
                config : {
                    name : 'garantia',
                    fieldLabel : 'Garantia',
                    allowBlank : false,
                    anchor : '100%',
                    gwidth : 130,
                    maxLength : 20,
                    galign:'right',
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(record.data.garantia,'0,000.00'));
                        }
                        else{
                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.garantia,'0,000.00'));
                        }
                    }
                },
                type : 'NumberField',

                id_grupo : 1,
                grid : true,
                form : false
            },
            {
                config : {
                    name : 'monto_debito',
                    fieldLabel : 'Debitos',
                    allowBlank : false,
                    anchor : '100%',
                    gwidth : 130,
                    maxLength : 20,
                    galign:'right',
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(record.data.monto_debito,'0,000.00'));
                        }
                        else{
                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.monto_debito,'0,000.00'));
                        }
                    }
                },
                type : 'NumberField',

                id_grupo : 1,
                grid : true,
                form : false
            },
            {
                config : {
                    name : 'monto_ajustes',
                    fieldLabel : 'Ajustes',
                    allowBlank : false,
                    anchor : '100%',
                    gwidth : 130,
                    maxLength : 20,
                    galign:'right',
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(record.data.monto_ajustes,'0,000.00'));
                        }
                        else{
                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.monto_ajustes,'0,000.00'));
                        }
                    }
                },
                type : 'NumberField',

                id_grupo : 1,
                grid : true,
                form : false
            },
            {
                config : {
                    name : 'saldo_sin_boleto',
                    fieldLabel : 'Saldo Sin Boleta',
                    allowBlank : false,
                    anchor : '100%',
                    gwidth : 130,
                    maxLength : 20,
                    galign:'right',
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(record.data.saldo_sin_boleto,'0,000.00'));
                        }
                        else{
                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.saldo_sin_boleto,'0,000.00'));
                        }
                    }
                },
                type : 'NumberField',

                id_grupo : 1,
                grid : true,
                form : false
            },
            {
                config : {
                    name : 'saldo_con_boleto',
                    fieldLabel : 'Saldo Con Boleta',
                    allowBlank : false,
                    anchor : '100%',
                    gwidth : 130,
                    maxLength : 20,
                    galign:'right',
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(record.data.saldo_con_boleto,'0,000.00'));
                        }
                        else{
                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(record.data.saldo_con_boleto,'0,000.00'));
                        }
                    }
                },
                type : 'NumberField',

                id_grupo : 1,
                grid : true,
                form : false
            }



        ],
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
        title : 'Reporte Saldo Vigente',
        ActList : '../../sis_obingresos/control/ReporteCuenta/reporteSaldoVigente',
        id_store : 'id_agencia',
        fields : [  { name : 'id_agencia'},
                    { name : 'id_periodo_venta'},
                    { name : 'id_creditos'},
                    { name : 'id_debitos'},
                    { name : 'nombre', type : 'string'
        }, {
            name : 'codigo_int',
            type : 'string'
        }, {
            name : 'tipo_agencia',
            type : 'string'
        }, {
            name : 'codigo_ciudad',
            type : 'string'
        }, {
            name : 'tipo_reg',
            type : 'string'
        }, {
            name : 'monto_credito',
            type : 'numeric'
        }, {
            name : 'monto_debito',
            type : 'numeric'
        }, {
            name : 'monto_ajustes',
            type : 'numeric'
        }, {
            name : 'saldo_con_boleto',
            type : 'numeric'
        },{
            name : 'saldo_sin_boleto',
            type : 'numeric'
        },{
            name : 'garantia',
            type : 'numeric'
        },{
            name : 'formas_pago',
            type : 'string'
        }],
        sortInfo : {
            field : 'nombre',
            direction : 'ASC'
        },
        bdel : false,
        bnew: false,
        bedit: false,
        fwidth : '90%',
        fheight : '80%',
        onButtonDeposito:function() {
          var rec = {fechaIni: this.fecha_ini.getValue().dateFormat('d/m/Y'),
                     fechaFin: this.fecha_fin.getValue().dateFormat('d/m/Y'),
                     agencia: this.sm.getSelected().data.id_agencia
                     }

            console.log ('MUESTRA',this);
            Phx.CP.loadWindows('../../../sis_obingresos/vista/reporte_saldo_vigente/DepositosPeriodo.php',
                'Reporte de Depositos Detallado',
                {
                    width:'62%',
                    height:'90%'
                },
                rec,
                this.idContenedor,
                'DepositosPeriodo');
        },
        onButtonReporte: function () {
            Phx.CP.loadingShow();
            var rec=this.sm.getSelected();
            console.log('PAGO',rec.data.formas_pago);
            Ext.Ajax.request({
                url:'../../sis_obingresos/control/ReporteCuenta/listarReporteCuentaIng',
                params:{'id_agencia':rec.data.id_agencia,
                        nombre: rec.data.nombre,
                        fecha_ini: this.fecha_ini.getValue().dateFormat('d/m/Y'),
                        fecha_fin: this.fecha_fin.getValue().dateFormat('d/m/Y'),
                        formas_pago: rec.data.formas_pago,
                        año_ini:this.fecha_ini.getValue().dateFormat('Y')

              },

                success: this.successExport,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },
        onButtonReporteVi:function(){
            Phx.CP.loadingShow();
            var rec=this.sm.getSelected();
            Ext.Ajax.request({
                url:'../../sis_obingresos/control/ReporteCuenta/listarReporteCuenta',
                params:{'id_agencia':rec.data.id_agencia,
                fecha_ini: this.fecha_ini.getValue().dateFormat('d/m/Y'),
                fecha_fin: this.fecha_fin.getValue().dateFormat('d/m/Y')

              },
                success: this.successExport,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },
        onButtonMovimientos:function(){
            Phx.CP.loadingShow();
            var rec=this.sm.getSelected();
            Ext.Ajax.request({
                url:'../../sis_obingresos/control/ReporteCuenta/listarReporteMovimientos',
                params:{'id_agencia':rec.data.id_agencia,
                        fecha_ini: this.fecha_ini.getValue().dateFormat('d/m/Y'),
                        fecha_fin: this.fecha_fin.getValue().dateFormat('d/m/Y'),
                        mes_ini:this.fecha_ini.getValue().dateFormat('m'),
                        dia_ini:this.fecha_ini.getValue().dateFormat('d'),
                        año_ini:this.fecha_ini.getValue().dateFormat('Y'),
                       },
                success: this.successExport,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },
        reporteGeneral:function () {
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_obingresos/control/ReporteCuenta/reporteGeneralEstadoCuenta',
                params:{fecha_ini: this.fecha_ini.getValue().dateFormat('d/m/Y'),
                        fecha_fin: this.fecha_fin.getValue().dateFormat('d/m/Y'),
                        id_lugar:this.lugar.getValue(),
                        tipo_agencia:this.tipo_agencia.getValue(),
                        forma_pago:this.forma_pago.getValue()},
                success: this.successExport,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },
        capturaFiltros:function(combo, record, index){
            this.desbloquearOrdenamientoGrid();
            console.log(this.tipo_agencia.getValue());
            //if(this.validarFiltros()){
                this.load({
                    params : {
                        start: 0,
                        limit: 1000,
                        fecha_ini: this.fecha_ini.getValue().dateFormat('d/m/Y'),
                        fecha_fin: this.fecha_fin.getValue().dateFormat('d/m/Y'),
                        id_lugar:this.lugar.getValue(),
                        tipo_agencia:this.tipo_agencia.getValue(),
                        forma_pago:this.forma_pago.getValue()
                    }
                });
           // }

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
        tipo_agencia : new Ext.form.ComboBox({
            name: 'tipo_agencia',
            fieldLabel: 'Tipo Agencia',
            emptyText:'Tipo Agencia',
            typeAhead: true,
            triggerAction: 'all',
            lazyRender:true,
            mode: 'local',
            gwidth: 50,
            anchor: "10%",
            store:['corporativa','noiata','todas']
        }),
        forma_pago : new Ext.form.ComboBox({
            name: 'forma_pago',
            fieldLabel: 'Forma Pago',
            emptyText:'Forma Pago',
            typeAhead: true,
            triggerAction: 'all',
            lazyRender:true,
            mode: 'local',
            gwidth: 150,
            store:['prepago','postpago','todas']
        }),
        lugar : new Ext.form.AwesomeCombo({
            name: 'id_lugar',
            fieldLabel: 'Lugar',
            emptyText:'Lugar...',
            store:new Ext.data.JsonStore(
                {
                    url: '../../sis_parametros/control/Lugar/listarLugar',
                    id: 'id_lugar',
                    root: 'datos',
                    sortInfo:{
                        field: 'nombre',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_lugar','id_lugar_fk','codigo','nombre','tipo','sw_municipio','sw_impuesto','codigo_largo'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams:{par_filtro:'lug.nombre',es_regional:'si'}
                }),
            valueField: 'id_lugar',
            displayField: 'nombre',
            hiddenName: 'id_lugar',
            triggerAction: 'all',
            lazyRender:true,
            mode:'remote',
            gwidth: 100,
            pageSize:50,
            queryDelay:500,
            anchor:"35%",
            minChars:2,
            enableMultiSelect:true
        }),
        cmbPeriodo: new Ext.form.ComboBox({
            name: 'periodo',
            id: 'id_periodo',
            fieldLabel: 'Periodo',
            allowBlank: true,
            emptyText:'Periodo...',
            store: new Ext.data.JsonStore({
                url: '../../sis_obingresos/control/ReporteCuenta/listarPeriodo',
                id: 'id_periodo_venta',
                root: 'datos',
                sortInfo: {
                    field: 'id_periodo_venta',
                    direction: 'DESC'
                },
                totalProperty: 'total',
                fields: ['id_periodo_venta', 'id_gestion','periodo' ,'mes','fecha_fin'],
                remoteSort: true,
                baseParams: {par_filtro: 'perven.mes'}
            }),
            valueField: 'id_periodo_venta',
            displayField: 'periodo',
            gdisplayField: 'id_periodo_venta',
            hiddenName: 'id_periodo_venta',
            anchor: "50%",
            tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{periodo}</b></p></div></tpl>',
            forceSelection: true,
            typeAhead: false,
            triggerAction: 'all',
            lazyRender: true,
            mode: 'remote',
            pageSize: 15,
            queryDelay: 1000,
            gwidth: 200,
            listWidth:200,
            resizable:true,
            minChars: 2
        })
    });
</script>
