<?php
/**
 * @package pxP
 * @file 	ReporteBancaBoletos.php
 * @author 	Ismael Valdivia
 * @date	02/01/2020
 * @description	Archivo con la interfaz de usuario que lista el detalle de los boletos de banca
 */
include_once ('../../media/styles.php');
header("content-type:text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ReporteBancaBoletos = Ext.extend(Phx.gridInterfaz, {
        viewConfig: {
            getRowClass: function(record) {
                if(record.data.saldo_con_boleto < 0){
                    return 'prioridad_importanteA';
                }
            }
        },
        constructor : function(config) {
            this.maestro = config;
            console.log("llega auqi el dato",this);
            Phx.vista.ReporteBancaBoletos.superclass.constructor.call(this, config);
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
            this.addButton('ReporteEstadoCuentas',{
                text: 'Reporte Estado de <br> Cuentas',
                iconCls: 'bexcel',
                disabled: true,
                handler: this.onButtonReporteEstadoCuentas,
                tooltip: '<b>Estado de Cuentas</b>',
                scope:this
            });


            this.tbar.addField(this.fecha_ini);
            this.tbar.addField(this.fecha_fin);
            this.tbar.addField(this.tipo_agencia);

            this.fecha_ini.on('select',function(value){
               console.log("la fecha fin es",this.fecha_fin.getValue());
               if (this.fecha_fin.getValue() != '') {
                 this.capturaFiltros();
               }
            },this);

            this.fecha_fin.on('select',function(value){
                this.capturaFiltros();
            },this);

            this.tipo_agencia.on('select', function( combo, record, index){
                this.capturaFiltros();
            },this);


            this.lugar.on('select', function( combo, record, index){
                this.capturaFiltros();
            },this);

            this.bbar.el.dom.style.background='#8AC5D2';
        		this.tbar.el.dom.style.background='#8AC5D2';
        		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#EEFCFF';
        		this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#A7D6E0';

        },

        preparaMenu: function () {
            var tb = this.tbar;
            var rec = this.sm.getSelected();
                this.getBoton('ReporteGeneral').enable();
                this.getBoton('ReporteEstadoCuentas').enable();
                //this.getBoton('Report').enable();

            },

        liberaMenu : function(){
            var rec = this.sm.getSelected();
          this.getBoton('ReporteGeneral').disable();
          this.getBoton('ReporteEstadoCuentas').disable();


          Phx.vista.ReporteBancaBoletos.superclass.liberaMenu.call(this);

        },




        tam_pag:1000,
        Atributos : [
            {
            config : {
                labelSeparator : '',
                inputType : 'hidden',
                name : 'agencia_id'
            },
            type : 'Field',
            form : true
           },
            {
                config : {
                    name : 'nombre',
                    fieldLabel : '<b style="font-size:12px;"><i style="font-size:15px; color:green;" class="fa fa-home" aria-hidden="true"></i> Nombre Agencia</b>',
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
                    fieldLabel : '<b style="font-size:12px;"><i style="font-size:15px; color:red;" class="fa fa-asterisk" aria-hidden="true"></i> OfficeId</b>',
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
                    fieldLabel : '<b style="font-size:12px;"><i style="font-size:15px; color:blue;" class="fa fa-tags" aria-hidden="true"></i> Tipo Agencia</b>',
                    allowBlank : false,
                    anchor : '200%',
                    gwidth : 150,
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
                    name : 'nombre_lugar',
                    fieldLabel : '<b style="font-size:12px;"><i style="font-size:15px; color:#DA8100;" class="fa fa-building" aria-hidden="true"></i> Ciudad</b>',
                    allowBlank : false,
                    anchor : '100%',
                    gwidth : 80,
                    maxLength : 20,
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', record.data['nombre_lugar']);
                        }
                        else{
                            return '<b style = "font-size:20px; color:red;"><p align="right">Totales: &nbsp;&nbsp; </p></b>';
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

            // {
            //     config : {
            //         name : 'fecha_pago_banco',
            //         fieldLabel : '<b style="font-size:12px;"><i style="font-size:15px; color:blue;" class="fa fa-calendar" aria-hidden="true"></i> Fecha Pago Banco</b>',
            //         allowBlank : false,
            //         anchor : '100%',
            //         gwidth : 150,
            //         maxLength : 20,
            //         format: 'd/m/Y',
      			// 				renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
      			// },
      			// 	type:'DateField',
            //     filters : {
            //         pfiltro : 'l.codigo',
            //         type : 'string'
            //     },
            //     id_grupo : 1,
            //     grid : true,
            //     form : false
            // },

            {
                config : {
                    name : 'monto_boa',
                    fieldLabel : '<b style="font-size:12px;"><i style="font-size:15px; color:green;" class="fa fa-money" aria-hidden="true"></i> Monto Boa</b>',
                    allowBlank : false,
                    anchor : '100%',
                    gwidth : 200,
                    maxLength : 20,
                    galign:'right',
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(record.data.monto_boa,'0,000.00'));
                        }
                        else{
                            return  String.format('<b  style = "font-size:20px"><font>{0}</font><b>', Ext.util.Format.number(record.data.monto_boa_general,'0,000.00'));
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
                    name : 'monto_agencia',
                    fieldLabel : '<b style="font-size:12px;"><i style="font-size:15px; color:green;" class="fa fa-money" aria-hidden="true"></i> Monto Agencia</b>',
                    allowBlank : false,
                    anchor : '100%',
                    gwidth : 200,
                    maxLength : 20,
                    galign:'right',
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(record.data.monto_agencia,'0,000.00'));
                        }
                        else{
                            return  String.format('<b  style = "font-size:20px"><font>{0}</font><b>', Ext.util.Format.number(record.data.monto_agencia_general,'0,000.00'));
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
                    name : 'total_debito',
                    fieldLabel : '<b style="font-size:12px;"><i style="font-size:15px; color:red;" class="fa fa-usd" aria-hidden="true"></i> Total Debito',
                    allowBlank : false,
                    anchor : '100%',
                    gwidth : 200,
                    maxLength : 20,
                    galign:'right',
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(record.data.total_debito,'0,000.00'));
                        }
                        else{
                            return  String.format('<b style = "font-size:20px"><font>{0}</font><b>', Ext.util.Format.number(record.data.monto_debito_general,'0,000.00'));
                        }
                    }
                },
                type : 'NumberField',
                id_grupo : 1,
                grid : true,
                form : false
            }

        ],

        title : 'Reporte Banca Express',
        ActList : '../../sis_obingresos/control/ReporteBancaBoletos/listarDatosBanca',
        id_store : 'agencia_id',
        fields : [

          { name : 'agencia_id'},
          { name : 'nombre', type : 'string'},
          { name : 'codigo_int', type : 'string'},
          { name : 'tipo_agencia', type : 'string'},
          { name : 'nombre_lugar',type : 'string' },
          { name : 'tipo_reg', type : 'string'},
          { name : 'monto_boa', type : 'numeric'},
          { name : 'monto_agencia', type : 'numeric'},
          { name : 'total_debito', type : 'numeric'},
          { name : 'monto_boa_general', type : 'numeric'},
          { name : 'monto_agencia_general', type : 'numeric'},
          { name : 'monto_debito_general', type : 'numeric'},
          //{ name : 'fecha_pago_banco', type : 'date',dateFormat:'Y-m-d'}

        ],
        sortInfo : {
            field : 'nombre',
            direction : 'ASC'
        },

        bdel : false,
        bnew: false,
        bedit: false,
        bexcel: false,
        btest:false,

        fwidth : '90%',
        fheight : '80%',

        onButtonReporteEstadoCuentas: function () {
            Phx.CP.loadingShow();
            var rec=this.sm.getSelected();
            console.log("llega aqui el dato agencia",rec);
            Ext.Ajax.request({
                url:'../../sis_obingresos/control/ReporteBancaBoletos/ReporteEstadoCuentas',
                params:{
                        id_agencia:rec.data.agencia_id,
                        nombre: rec.data.nombre,
                        fecha_ini: this.fecha_ini.getValue().dateFormat('d/m/Y'),
                        fecha_fin: this.fecha_fin.getValue().dateFormat('d/m/Y'),


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
                url:'../../sis_obingresos/control/ReporteBancaBoletos/ReporteDatosBanca',
                params:{fecha_ini: this.fecha_ini.getValue().dateFormat('d/m/Y'),
                        fecha_fin: this.fecha_fin.getValue().dateFormat('d/m/Y'),
                        id_lugar:this.lugar.getValue(),
                        tipo_agencia:this.tipo_agencia.getValue()
                      },
                success: this.successExport,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },
        capturaFiltros:function(combo, record, index){
            this.desbloquearOrdenamientoGrid();
            //if(this.validarFiltros()){
                this.load({
                    params : {
                        start: 0,
                        limit: 1000,
                        fecha_ini: this.fecha_ini.getValue().dateFormat('d/m/Y'),
                        fecha_fin: this.fecha_fin.getValue().dateFormat('d/m/Y'),
                        id_lugar:this.lugar.getValue(),
                        tipo_agencia:this.tipo_agencia.getValue()
                    }
                });
//            }

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
        })
    });
</script>
