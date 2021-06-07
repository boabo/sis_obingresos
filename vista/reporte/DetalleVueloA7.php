<?php
/**
 *@package pXP
 *@file DetalleVueloA7.php
 *@author franklin.espinoza
 *@date 20-12-2020
 *@description  Vista para registrar los datos de un funcionario
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
    Phx.vista.DetalleVueloA7=Ext.extend(Phx.gridInterfaz,{
        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                return "x-selectable";
            }
        },
        constructor: function(config) {

            Phx.vista.DetalleVueloA7.superclass.constructor.call(this,config);
            this.maestro = config;
            this.store.baseParams.vuelo_id = this.maestro.maestro.vuelo_id;

            this.grid.addListener('cellclick', this.mostrarDetallePasajero,this);
            this.init();
            this.load({params: {start: 0, limit: 50}});
        },

        mostrarDetallePasajero : function(grid, rowIndex, columnIndex, e) {

            var record = this.store.getAt(rowIndex);
            var fieldName = grid.getColumnModel().getDataIndex(columnIndex); // Get field name



            if (fieldName == 'nro_vuelo') {
                var rec = {maestro: this.getSelectedData()};

                Phx.CP.loadWindows('../../../sis_obingresos/vista/reporte/DetallePasajeroA7.php',
                    'Detalle Pasajero A7',
                    {
                        width: 900,
                        height: 500
                    },
                    rec,
                    this.idContenedor,
                    'DetallePasajeroA7'
                );
            }


        },


        Atributos:[
            {
                // configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_detalle'
                },
                type:'Field',
                form:true

            },

            {
                config:{
                    fieldLabel: "Ato. Origen",
                    gwidth: 70,
                    name: 'ato_origen',
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
                form:false
            },

            {
                config:{
                    fieldLabel: "Ruta Completa",
                    gwidth: 100,
                    name: 'ruta_completa',
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
                form:false
            },

            {
                config:{
                    fieldLabel: "Nombre Pasajero",
                    gwidth: 250,
                    name: 'nombre_pasajero',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        if (value != 'ZZZZZZZZZZZZZZZ') {
                            return String.format('<div style="color: green; font-weight: bold;">{0}</div>', value);
                        }
                    }
                },
                type:'TextField',
                //filters:{pfiltro:'tca.nombre',type:'string'},
                bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:false
            },

            {
                config:{
                    fieldLabel: "Nro. Vuelo",
                    gwidth: 120,
                    name: 'nro_vuelo',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        if (value != '') {
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
                    fieldLabel: "Nro. Asiento",
                    gwidth: 70,
                    name: 'nro_asiento',
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
                form:false
            },

            {
                config:{
                    fieldLabel: "Fecha Vuelo",
                    gwidth: 80,
                    name: 'fecha_vuelo',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    format:'d/m/Y',
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return value?value.dateFormat('d/m/Y'):''
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
                    fieldLabel: "PNR",
                    gwidth: 65,
                    name: 'pnr',
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
                    fieldLabel: "Nro. Boleto",
                    gwidth: 100,
                    name: 'nro_boleto',
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
                    fieldLabel: "Hora Prog.",
                    gwidth: 70,
                    name: 'hora_vuelo',
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
                    fieldLabel: "Estado Vuelo",
                    gwidth: 96,
                    name: 'estado_vuelo',
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
                type:'TextField',
                //filters:{pfiltro:'tca.nombre',type:'string'},
                //bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:false
            },


            {
                config:{
                    fieldLabel: "A7",
                    gwidth: 70,
                    name: 'valor_a7',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        if (value == 25) {
                            return String.format('<div style="color: #00B167; font-weight: bold;">{0} $us</div> ', value);
                        }else {
                            if (value != null) {
                                return String.format('<div style="color: #00B167; font-weight: bold;">{0} Bs.</div> ', value);
                            }
                        }
                    }
                },
                type:'NumberField',
                //filters:{pfiltro:'tca.nombre',type:'string'},
                //bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:true
            }
            ,


            {
                config:{
                    fieldLabel: "Calculo A7 (Bs.)",
                    gwidth: 100,
                    name: 'calculo_a7',
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
                form:true
            }

        ],
        title:'Detalle Vuelo',
        ActList:'../../sis_obingresos/control/Reportes/detalleVueloCalculoA7',
        id_store:'id_detalle',
        fields: [
            {name:'id_detalle'},
            {name:'ato_origen', type: 'string'},
            {name:'ruta_completa', type: 'string'},
            {name:'nombre_pasajero', type: 'string'},
            {name:'nro_vuelo', type: 'string'},
            {name:'nro_asiento', type: 'string'},

            {name:'fecha_vuelo', type: 'date', dateFormat:'Y-m-d'},
            {name:'pnr', type: 'string'},
            {name:'nro_boleto', type: 'string'},
            {name:'hora_vuelo', type: 'string'},
            {name:'estado_vuelo', type: 'string'},
            {name:'valor_a7', type: 'numeric'},
            {name:'calculo_a7', type: 'numeric'},
            {name:'pax_id', type: 'string'},
            {name:'std_date', type: 'string'}
        ],

        bedit:false,
        bnew:false,
        bdel:false,
        bsave:false,
        fwidth: '90%',
        fheight: '95%'
    });
</script>
