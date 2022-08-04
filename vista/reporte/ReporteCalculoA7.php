<?php
/**
 *@package pXP
 *@file ReporteCalculoA7.php
 *@author franklin.espinoza
 *@date 20-12-2020
 *@description  Vista para registrar los datos de un funcionario
 */

include_once('../../media/styles.php');
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
    Phx.vista.ReporteCalculoA7=Ext.extend(Phx.gridInterfaz,{
        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {

                if (record.data.nro_pax_boa == '0') {
                    return 'cero';
                }else {
                    return "x-selectable";
                }
            }
        },
        constructor: function(config) {
            this.maestro = config;

            Phx.vista.ReporteCalculoA7.superclass.constructor.call(this,config);

            this.current_date = new Date();
            this.diasMes = [31, new Date(this.current_date.getFullYear(), 2, 0).getDate(), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

            /*this.txtSearch = new Ext.form.TextField();
            this.txtSearch.enableKeyEvents = true;
            this.txtSearch.maxLength = 13;
            this.txtSearch.maxLengthText = 'Ha exedido el numero de caracteres permitidos';
            this.txtSearch.msgTarget = 'under';*/

            this.etiqueta_vuelo = new Ext.form.Label({
                name: 'label_vuelo',
                grupo: [0,1],
                fieldLabel: 'Nro. Vuelo:',
                text: 'Nro. Vuelo:',
                //style: {color: 'green', font_size: '12pt'},
                readOnly:true,
                anchor: '150%',
                gwidth: 150,
                format: 'd/m/Y',
                hidden : false,
                style: 'font-size: 170%; font-weight: bold; background-image: none;color: #00B167;'
            });

            this.txtSearch = new Ext.form.TextField({
                name: 'campo_search',
                grupo: [0,1],
                fieldLabel: 'Fecha Inicio:',
                enableKeyEvents: true,
                maxLength : 13,
                maxLengthText : 'Ha exedido el numero de caracteres permitidos',
                gwidth: 150,
                msgTarget : 'under',
                hidden : false
            });

            this.txtSearch.on('specialkey', this.onTxtSearchSpecialkey, this);

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
            this.tbar.addField(this.etiqueta_vuelo);
            this.tbar.addField(this.txtSearch);


            this.addButton('btnBuscar', {
                text : 'Buscar',
                grupo: [0,1],
                iconCls : 'bzoom',
                disabled : false,
                handler : this.onBtnBuscar
            });
            this.addButton('btn_excel_a7',
                {
                    text: 'Resumen A7',
                    iconCls: 'bpagar',
                    style: 'color : #00B167; ',
                    grupo: [0,1,2,3,4,5,6],
                    disabled: false,
                    handler: this.onBtnRepResumenCalculoA7,
                    tooltip: 'Reporte Resumen A7'
                }
            );
            this.iniciarEventos();
            this.bandera_alta = 0;
            this.bandera_baja = 0;

            this.grid.addListener('cellclick', this.mostrarDetalleVuelo,this);

            this.init();

        },

        onBtnRepResumenCalculoA7 : function (){
            let items = [];
            this.store.data.items.forEach(item => {
                if(item.json.ruta_vl != 'TOTAL')
                    items.push(item.json);
            });

            let fecha_desde = this.fecha_ini.getValue();
            dia =  fecha_desde.getDate();
            dia = dia < 10 ? "0"+dia : dia;
            mes = fecha_desde.getMonth() + 1;
            mes = mes < 10 ? "0"+mes : mes;
            anio = fecha_desde.getFullYear();
            fecha_desde = dia + "/" + mes + "/" + anio;

            let fecha_hasta = this.fecha_fin.getValue();
            dia =  fecha_hasta.getDate();
            dia = dia < 10 ? "0"+dia : dia;
            mes = fecha_hasta.getMonth() + 1;
            mes = mes < 10 ? "0"+mes : mes;
            anio = fecha_hasta.getFullYear();
            fecha_hasta = dia + "/" + mes + "/" + anio;

            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_obingresos/control/Reportes/reporteResumenCalculoA7',
                params: {
                    records: JSON.stringify(items),
                    fecha_ini : fecha_desde,
                    fecha_fin : fecha_hasta

                },
                success: this.successExport,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });

        },
        onTxtSearchSpecialkey : function(field, e) {

            if (e.getKey() == e.ENTER) {
                this.onBtnBuscar();
            }
        },

        onBtnBuscar : function() {
            this.store.baseParams.nro_vuelo = (this.txtSearch.getValue()).trim();

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

            this.load({params: {start: 0, limit: 50}});
        },

        mostrarDetalleVuelo : function(grid, rowIndex, columnIndex, e) {

            var record = this.store.getAt(rowIndex);
            var fieldName = grid.getColumnModel().getDataIndex(columnIndex); // Get field name



            if (fieldName == 'vuelo_id') {

                var rec = {maestro: this.getSelectedData()};

                Phx.CP.loadWindows('../../../sis_obingresos/vista/reporte/DetalleVueloA7.php',
                    'Detalle Vuelo A7',
                    {
                        width: 1200,
                        height:600
                    },
                    rec,
                    this.idContenedor,
                    'DetalleVueloA7'
                );
            }

        },

        bactGroups:[0,1],
        bexcelGroups:[0,1],
        gruposBarraTareas: [
            {name:  'normal', title: '<h1 style="text-align: center; color: #00B167;"><i class="fa fa-file-o fa-2x" aria-hidden="true"></i>C. LIQUIDACIÓN</h1>',grupo: 0, height: 0} ,
            {name: 'existencia', title: '<h1 style="text-align: center; color: #FF8F85;"><i class="fa fa-file-o fa-2x" aria-hidden="true"></i>CONTROL VUELOS</h1>', grupo: 1, height: 1}
        ],
        iniciarEventos: function(){

            this.fecha_fin.on('select', function (combo,rec,index) {

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

                /*if(this.tabtbar.getActiveTab().name == 'bajas'){
                    this.store.baseParams.estado_func = 'bajas';
                    var fecha = this.fecha_fin.getValue();
                    this.current_date = new Date(fecha.getFullYear(),fecha.getMonth()+1,1);
                }else{
                    this.store.baseParams.estado_func = 'altas';
                    this.current_date = this.store.baseParams.fecha_fin;
                }*/

                this.load({params: {start: 0, limit: 50}});
            },this);
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
                    name: 'id_vuelo'
                },
                type:'Field',
                form:true

            },
            {
                config:{
                    fieldLabel: "Vuelo ID",
                    gwidth: 100,
                    name: 'vuelo_id',
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
                    fieldLabel: "Fecha Vuelo",
                    gwidth: 90,
                    name: 'fecha_vuelo',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    format:'d/m/Y',
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return value ? value.dateFormat('d/m/Y') : ''
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
                    fieldLabel: "Nro. Vuelo",
                    gwidth: 100,
                    name: 'nro_vuelo',
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
                    fieldLabel: "Status Vuelo",
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
                //filters:{pfiltro:'NroVuelo',type:'string'},
                //bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:true
            },

            {
                config:{
                    fieldLabel: "Factor Demora",
                    gwidth: 100,
                    name: 'factor_demora',
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
                    fieldLabel: "Ruta BoA",
                    gwidth: 100,
                    name: 'ruta_vl',
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
                    fieldLabel: "Ruta PAX",
                    gwidth: 100,
                    name: 'ruta_sabsa',
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
                    fieldLabel: "Matricula BoA",
                    gwidth: 100,
                    name: 'matricula_boa',
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
                    fieldLabel: "Matricula NAABOL",
                    gwidth: 100,
                    name: 'matricula_sabsa',
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
                    fieldLabel: "A7 Nacional",
                    gwidth: 100,
                    name: 'total_nac',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #FF8F85; font-weight: bold;">{0}</div>', value);
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
                    fieldLabel: "A7 Internacional",
                    gwidth: 90,
                    name: 'total_inter',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #FF8F85; font-weight: bold;">{0}</div>', value);
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
                    fieldLabel: "Sin A7",
                    gwidth: 90,
                    name: 'total_cero',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #FF8F85; font-weight: bold;">{0}</div>', value);
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
                    fieldLabel: "Total Pax BoA",
                    gwidth: 110,
                    name: 'nro_pax_boa',
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
                    fieldLabel: "Importe BoA (Bs.)",
                    gwidth: 107,
                    name: 'importe_boa',
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
                    fieldLabel: "Total Pax Naabol",
                    gwidth: 110,
                    name: 'nro_pax_sabsa',
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
                    fieldLabel: "Importe Naabol (Bs.)",
                    gwidth: 120,
                    name: 'importe_sabsa',
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
                    fieldLabel: "(Imp. BoA - Imp. Naabol) (Bs.)",
                    gwidth: 200,
                    name: 'diferencia',
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
        title:'Calculo A7',
        ActList:'../../sis_obingresos/control/Reportes/generarReporteCalculoA7',
        id_store:'id_vuelo',
        fields: [
            {name:'id_vuelo'},
            {name:'vuelo_id'},
            {name:'fecha_vuelo', type: 'date', dateFormat:'Y-m-d'},
            {name:'nro_vuelo', type: 'string'},
            {name:'ruta_vl', type: 'string'},
            {name:'nro_pax_boa', type: 'string'},
            {name:'nro_pax_sabsa', type: 'string'},
            {name:'importe_boa', type: 'numeric'},
            {name:'importe_sabsa', type: 'numeric'},
            {name:'diferencia', type: 'numeric'},
            {name:'total_nac', type: 'numeric'},
            {name:'total_inter', type: 'numeric'},
            {name:'total_cero', type: 'numeric'},

            {name:'matricula_boa', type: 'string'},
            {name:'matricula_sabsa', type: 'string'},
            {name:'ruta_sabsa', type: 'string'},
            {name:'status', type: 'string'},
            {name:'factor_demora', type: 'string'},
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
