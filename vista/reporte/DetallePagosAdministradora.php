<?php
/**
 *@package pXP
 *@file DocumentoVentaTipo.php
 *@author franklin.espinoza
 *@date 20-01-2021
 *@description  Vista para registrar modificacion Nit, Razon Social de una Factura
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
    Phx.vista.DetallePagosAdministradora=Ext.extend(Phx.gridInterfaz,{
        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                return "x-selectable";
            }
        },
        constructor: function(config) {
            this.maestro = config;

            Phx.vista.DetallePagosAdministradora.superclass.constructor.call(this,config);

            this.tipo_administrador = 'ATC';
            var fecha_desde = this.maestro.fecha_desde;
            var fecha_hasta = this.maestro.fecha_hasta;
            this.etiqueta_ini = new Ext.form.Label({
                name: 'etiqueta_ini',
                grupo: [0,1,2,3,4,5,6,7,8,9],
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
                grupo: [0,1,2,3,4,5,6,7,8,9],
                fieldLabel: 'Fecha',
                allowBlank: false,
                anchor: '60%',
                gwidth: 100,
                format: 'd/m/Y',
                hidden : false,
                disabled:false,
                value: fecha_desde
            });

            this.etiqueta_fin = new Ext.form.Label({
                name: 'etiqueta_fin',
                grupo: [0,1,2,3,4,5,6,7,8,9],
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
                grupo: [0,1,2,3,4,5,6,7,8,9],
                fieldLabel: 'Fecha',
                allowBlank: false,
                anchor: '60%',
                gwidth: 100,
                format: 'd/m/Y',
                hidden : false,
                disabled:false,
                value: fecha_hasta
            });


            this.tbar.addField(this.etiqueta_ini);
            this.tbar.addField(this.fecha_ini);
            this.tbar.addField(this.etiqueta_fin);
            this.tbar.addField(this.fecha_fin);
            this.iniciarEventos();

            this.init();

        },


        bactGroups:[0,1,2,3,4,5,6,7,8,9],
        bexcelGroups:[0,1,2,3,4,5,6,7,8,9],

        gruposBarraTareas: [
            {name: 'ATC', title: '<h1 style="text-align: center; color: #00B167;"><i class="fa fa-file-o fa-2x" aria-hidden="true"></i>ATC</h1>', grupo: 0, height: 1, width: 1},
            {name: 'LINKSER', title: '<h1 style="text-align: center; color: #B066BB;"><i class="fa fa-file-o fa-2x" aria-hidden="true"></i>LINKSER</h1>', grupo: 1, height: 1, width: 1},
            {name: 'TIGO', title: '<h1 style="text-align: center; color: #4682B4;"><i class="fa fa-file-o fa-2x" aria-hidden="true"></i>TIGO</h1>', grupo: 2, height: 1, width: 1}
        ],

        onButtonAct:function(){
            Phx.vista.DetallePagosAdministradora.superclass.onButtonAct.call(this);

        },

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

                this.load({params: {start: 0, limit: 50}});
            },this);
        },

        actualizarSegunTab: function(name, indice){

            this.store.baseParams.tipo_administrador = name;

            this.tipo_administrador = name;

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
                    name: 'PaymentKey'
                },
                type:'Field',
                form:true

            },

            {
                config:{
                    fieldLabel: "Nombre Archivo",
                    gwidth: 130,
                    name: 'NombreArchivo',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: #00B167;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #00B167; font-weight: bold;">{0}</div>', value);
                    }
                },
                type:'TextField',
                //filters:{pfiltro:'tfa.nro_factura',type:'string'},
                bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:true
            },

            {
                config:{
                    fieldLabel: "Fecha/Hora Carga Archivo",
                    gwidth: 180,
                    name: 'FechaHoraCargaArchivo',
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

            /*{
                config:{
                    fieldLabel: "Fecha/Hora Carga Archivo",
                    gwidth: 180,
                    name: 'FechaHoraCargaArchivo',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    format: 'd/m/Y H:i',
                    disabled: true,
                    style: 'color: blue; background-color: #00B167;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #00B167; font-weight: bold;">{0}</div>', dateFormat('d/m/Y H:i A'));
                    }
                },
                type:'DateField',
                //filters:{pfiltro:'tca.nombre',type:'string'},
                //bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:true
            },*/

            {
                config:{
                    fieldLabel: "Archivo Id",
                    gwidth: 70,
                    name: 'ArchivoId',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: false,
                    style: 'color: blue; background-color: #FF8F85;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #FF8F85; font-weight: bold;">{0}</div>', value);
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
                    fieldLabel: "Codigo Establecimiento",
                    gwidth: 130,
                    name: 'EstablishmentCode',
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
                    fieldLabel: "Numero Terminal",
                    gwidth: 100,
                    name: 'TerminalNumber',
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
                    fieldLabel: "Numero Lote",
                    gwidth: 100,
                    name: 'LotNumber',
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
                    fieldLabel: "Numero Ticket",
                    gwidth: 100,
                    name: 'TicketNumber',
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
                    fieldLabel: "Fecha Pago",
                    gwidth: 90,
                    name: 'PaymentDate',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    format:'d/m/Y',
                    disabled: true,
                    style: 'color: blue; background-color: #00B167;',
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

            /*{
                config:{
                    fieldLabel: "Fecha Pago",
                    gwidth: 150,
                    name: 'PaymentDate',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: false,
                    style: 'color: blue; background-color: #FF8F85;',
                    renderer: function (value, p, record){
                        //return String.format('<div style="color: #FF8F85; font-weight: bold; font-size:15px; float:right;">{0}</div>', value);
                        return String.format('<div style="color: #FF8F85; font-weight: bold;">{0}</div>', value);
                    }
                },
                type:'TextField',
                //filters:{pfiltro:'tca.nombre',type:'string'},
                //bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:true
            },*/

            {
                config:{
                    fieldLabel: "Hora Pago",
                    gwidth: 70,
                    name: 'PaymentHour',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: false,
                    style: 'color: blue; background-color: #FF8F85;',
                    renderer: function (value, p, record){
                        //return String.format('<hr><div style="color: #FF8F85; font-weight: bold; font-size:15px; float:right;">{0}</div>', value);
                        return String.format('<div style="color: #FF8F85; font-weight: bold;">{0}</div>', value);
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
                    fieldLabel: "Numero Tarjeta",
                    gwidth: 120,
                    name: 'CreditCardNumber',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: false,
                    style: 'color: blue; background-color: #FF8F85;',
                    renderer: function (value, p, record){
                        //return String.format('<hr><div style="color: #FF8F85; font-weight: bold; font-size:15px; float:right;">{0}</div>', value);
                        return String.format('<div style="color: #FF8F85; font-weight: bold;">{0}</div>', value);
                    }
                },
                type:'TextField',
                //filters:{pfiltro:'tca.nombre',type:'string'},
                bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:true
            },

            {
                config:{
                    fieldLabel: "Importe Pago",
                    gwidth: 90,
                    name: 'PaymentAmmount',
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
                        } else{

                            return  String.format('<hr><div style="color: #00B167; font-weight: bold; vertical-align:middle;text-align:right;"><span ><b>{0}</b></span></div>',(parseFloat(value)).formatDinero(2, ',', '.'));

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
                    fieldLabel: "Moneda",
                    gwidth: 70,
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
                form:false
            },

            {
                config:{
                    fieldLabel: "Codigo Autorización",
                    gwidth: 120,
                    name: 'AuthorizationCode',
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
                bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:false
            }

        ],
        title:'Listado Por Tipo Documento',
        ActList:'../../sis_obingresos/control/Reportes/getDetallePagosAdministradora',
        id_store:'PaymentKey',
        fields: [
            {name:'PaymentKey', type: 'numeric'},

            {name:'Formato', type: 'string'},
            {name:'NombreArchivo', type: 'string'},
            {name:'FechaHoraCargaArchivo', type: 'string'/*, dateFormat: 'Y-m-d H:i:s.m'*/},
            {name:'ArchivoId', type: 'string'},
            {name:'EstablishmentCode', type: 'string'},
            {name:'TerminalNumber', type: 'string'},
            {name:'LotNumber', type: 'string'},
            {name:'TicketNumber', type: 'string'},
            {name:'PaymentDate', type: 'date'},
            {name:'PaymentHour', type: 'string'},
            {name:'CreditCardNumber', type: 'string'},
            {name:'PaymentAmmount', type: 'numeric'},
            {name:'Currency', type: 'string'},
            {name:'AuthorizationCode', type: 'string'}
        ],
        /*sortInfo:{
            field: 'PERSON.nombre_completo2',
            direction: 'ASC'
        },*/
        bedit:false,
        bnew:false,
        bdel:false,
        bsave:false,
        fwidth: '60%',
        fheight: '40%'
    });
</script>
