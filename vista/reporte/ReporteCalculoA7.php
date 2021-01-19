<?php
/**
 *@package pXP
 *@file ReporteCalculoA7.php
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
    Phx.vista.ReporteCalculoA7=Ext.extend(Phx.gridInterfaz,{
        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                return "x-selectable";
            }
        },
        constructor: function(config) {
            this.maestro = config;

            Phx.vista.ReporteCalculoA7.superclass.constructor.call(this,config);

            this.current_date = new Date();
            this.diasMes = [31, new Date(this.current_date.getFullYear(), 2, 0).getDate(), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

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
                style: 'font-size: 170%; font-weight: bold; background-image: none;color: green;'
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
                style: 'font-size: 170%; font-weight: bold; background-image: none; color: red;'
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
            this.iniciarEventos();
            this.bandera_alta = 0;
            this.bandera_baja = 0;

            this.init();

        },
        bactGroups:[0,1],
        bexcelGroups:[0,1],
        gruposBarraTareas: [
            {name:  'altas', title: '<h1 style="text-align: center; color: green;"><i class="fa fa-user fa-2x" aria-hidden="true"></i>NORMAL</h1>',grupo: 0, height: 0} ,
            {name: 'bajas', title: '<h1 style="text-align: center; color: red;"><i class="fa fa-user-times fa-2x" aria-hidden="true"></i>EXCEPCIÓN</h1>', grupo: 1, height: 1}
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

                if(this.tabtbar.getActiveTab().name == 'bajas'){
                    this.store.baseParams.estado_func = 'bajas';
                    var fecha = this.fecha_fin.getValue();
                    this.current_date = new Date(fecha.getFullYear(),fecha.getMonth()+1,1);
                }else{
                    this.store.baseParams.estado_func = 'altas';
                    this.current_date = this.store.baseParams.fecha_fin;
                }

                this.load({params: {start: 0, limit: 50}});
            },this);
        },
        actualizarSegunTab: function(name, indice){

            if(name == 'altas' ){
                this.fecha_ini.setValue(new Date(this.current_date.getFullYear(),this.current_date.getMonth(),1));
                this.fecha_fin.setValue(new Date(this.current_date.getFullYear(),this.current_date.getMonth()+1,0));
                this.bandera_alta = 1;
            }else if(name == 'bajas' ){
                this.fecha_ini.setValue(new Date(this.current_date.getFullYear(),this.current_date.getMonth()-1,1));
                this.fecha_fin.setValue(new Date(this.current_date.getFullYear(),this.current_date.getMonth(),0));
                this.bandera_baja = 1;
            }

            this.store.baseParams.estado_func = name;

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
                    gwidth: 200,
                    name: 'vuelo_id',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: green; font-weight: bold;">{0}</div>', value);
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
                    gwidth: 200,
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
                    fieldLabel: "Nro. Vuelo",
                    gwidth: 200,
                    name: 'nro_vuelo',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: green; font-weight: bold;">{0}</div>', value);
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
                    fieldLabel: "Ruta Vl",
                    gwidth: 200,
                    name: 'ruta_vl',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: green; font-weight: bold;">{0}</div>', value);
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
                    fieldLabel: "Nro Pax BoA",
                    gwidth: 200,
                    name: 'nro_pax_boa',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: green; font-weight: bold;">{0}</div>', value);
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
                    fieldLabel: "Importe Boa",
                    gwidth: 100,
                    name: 'importe_boa',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: green; font-weight: bold;">{0}</div>', value);
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
                    fieldLabel: "Nro Pax Sabsa",
                    gwidth: 200,
                    name: 'nro_pax_sabsa',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: green; font-weight: bold;">{0}</div>', value);
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
                    fieldLabel: "Importe Sabsa",
                    gwidth: 100,
                    name: 'importe_sabsa',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: green; font-weight: bold;">{0}</div>', value);
                    }
                },
                type:'NumberField',
                filters:{pfiltro:'tca.nombre',type:'string'},
                bottom_filter : true,
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
            {name:'importe_sabsa', type: 'numeric'}
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
