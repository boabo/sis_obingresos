<?php
/**
 *@package pXP
 *@file DetallePasajeroA7.php
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
    Phx.vista.DetallePasajeroA7=Ext.extend(Phx.gridInterfaz,{
        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                return "x-selectable";
            }
        },
        constructor: function(config) {

            Phx.vista.DetallePasajeroA7.superclass.constructor.call(this,config);
            this.maestro = config;
            this.store.baseParams.pax_id = this.maestro.maestro.pax_id;
            this.store.baseParams.std_date = this.maestro.maestro.std_date;
            this.init();
            this.load({params: {start: 0, limit: 50}});
        },


        Atributos:[
            {
                // configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_pasajero'
                },
                type:'Field',
                form:true

            },

            {
                config:{
                    fieldLabel: "Cobro A7",
                    gwidth: 70,
                    name: 'here_a7',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        if (value == 'true') {
                            return String.format('<div style="color: #00B167; font-weight: bold; text-align: center;"> <i class="fa fa-money fa-2x"></i></div> ', value);
                        }else {
                            return String.format('<div style="color: #00B167; font-weight: bold;"></div> ', value);
                        }
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
                    fieldLabel: "SABSA",
                    gwidth: 100,
                    name: 'is_sabsa',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){

                        if (value == 'true') {
                            return String.format('<div style="color: #00B167; font-weight: bold; text-align: center;"><input type="checkbox" checked></div> ', value);
                        }else {
                            return String.format('<div style="color: #00B167; font-weight: bold; text-align: center;"><input type="checkbox"></div> ', value);
                        }
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
                    fieldLabel: "Origen",
                    gwidth: 70,
                    name: 'origen',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #586E7E; font-weight: bold; cursor:pointer;">{0}</div>', value);
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
                    fieldLabel: "Destino",
                    gwidth: 70,
                    name: 'destino',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #586E7E; font-weight: bold; cursor:pointer;">{0}</div>', value);
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
                    fieldLabel: "Ticket",
                    gwidth: 90,
                    name: 'ticket',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #586E7E; font-weight: bold; cursor:pointer;">{0}</div>', value);
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
                    fieldLabel: "Salida",
                    gwidth: 120,
                    name: 'std_show',
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
                    fieldLabel: "Llegada",
                    gwidth: 120,
                    name: 'sta_show',
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
            }

        ],
        title:'Detalle Pasajero',
        ActList:'../../sis_obingresos/control/Reportes/detallePasajeroCalculoA7',
        id_store:'id_pasajero',
        fields: [
            {name:'id_pasajero'},
            {name:'passenger_id', type: 'string'},
            {name:'is_current', type: 'string'},
            {name:'posicion', type: 'string'},
            {name:'fecha_salida', type: 'string'},
            {name:'fecha_salida_show', type: 'date', dateFormat:'Y-m-d'},
            {name:'origen', type: 'string'},
            {name:'destino', type: 'string'},
            {name:'ticket', type: 'string'},
            {name:'std', type: 'string'},
            {name:'std_show', type: 'string'},
            {name:'sta', type: 'string'},
            {name:'sta_show', type: 'string'},
            {name:'here_a7', type: 'string'},
            {name:'is_sabsa', type: 'string'}
        ],

        bedit:false,
        bnew:false,
        bdel:false,
        bsave:false,
        fwidth: '90%',
        fheight: '95%'
    });
</script>
