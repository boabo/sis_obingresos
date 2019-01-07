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
    Phx.vista.listaAgencias = Ext.extend(Phx.gridInterfaz, {
        viewConfig: {
            getRowClass: function(record) {
                if(record.data.saldo_con_boleto < 0){
                    return 'prioridad_importanteA';
                }
            }
        }, 
        constructor : function(config) {
            this.maestro = config;
            this.description = this.maestro.tipo_agencia;
            Phx.vista.listaAgencias.superclass.constructor.call(this, config);
            this.init();
            //this.bbar.add(this.lugar);
            this.addButton('Moneda',
                {
                //grupo: [0],
                text: 'Mostrar <br> Tipo de Monedas',
                iconCls: 'bmoney',
                disabled: true,
                handler: this.verificarMoneda,
                tooltip: '<b>Verificar Moneda</b><br/>'
                }
            );

            this.addButton('ReporteGeneral',{
                text: 'Reporte Control <br> de Agencias',
                iconCls: 'bexcel',
                disabled: true,
                handler: this.reporteGeneral,
                tooltip: '<b>Reporte Control de Agencias</b>',
                scope:this
            });


            this.tbar.addField(this.fecha_fin);




            this.fecha_fin.on('select',function(value){
                this.capturaFiltros();
            },this);
        },

        preparaMenu: function () {
            var tb = this.tbar;
            var rec = this.sm.getSelected();
            this.getBoton('ReporteGeneral').enable();
            this.getBoton('Moneda').enable();
            },

        liberaMenu : function(){
            var rec = this.sm.getSelected();
            this.getBoton('ReporteGeneral').disable();
            this.getBoton('Moneda').disable();
          Phx.vista.listaAgencias.superclass.liberaMenu.call(this);

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
                myPanelEast = Phx.CP.getPagina(idPanel+'-east-0');
                myPanel.onReloadPage(data);
                myPanelEast.onReloadPage(data);

            }

            delete myPanel;
            delete myPanelEast;

        },
        title : 'Reporte Saldo Vigente',
        ActList : '../../sis_obingresos/control/ControlAgencias/reporteSaldoVigente',
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

        reporteGeneral:function () {
            Phx.CP.loadingShow();
            var rec=this.sm.getSelected();
            console.log('ESTA AQUI',rec);
            Ext.Ajax.request({
                url:'../../sis_obingresos/control/ControlAgencias/reporteGeneralEstadoCuenta',

                params:{'id_agencia':rec.data.id_agencia,
                        nombre:rec.data.nombre,
                        fecha_fin: this.fecha_fin.getValue().dateFormat('d/m/Y')
                        },
                success: this.successExport,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },
        verificarMoneda: function() {
      	 //var titulo = 'Anexo 1';
         var rec = {
                    agencia: this.sm.getSelected().data.id_agencia
                    }
      	 Phx.CP.loadWindows('../../../sis_obingresos/vista/control_agencias/verificarMoneda.php',
      			 '<center><i class="fa fa-money fa-2x" aria-hidden="true"> Moneda de la Agencia</i> </center>',
      			 {
      					 width:900,
      					 height:400
      			 },

      			 rec,
      			 this.idContenedor,
      			 'verificarMoneda');
      	},
        capturaFiltros:function(combo, record, index){
            this.desbloquearOrdenamientoGrid();
            console.log(this.tipo_agencia.getValue());
                this.load({
                    params : {
                        start: 0,
                        limit: 1000,
                        fecha_fin: this.fecha_fin.getValue().dateFormat('d/m/Y')

                    }
                });
        },
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

    });
</script>
