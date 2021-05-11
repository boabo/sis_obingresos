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
    Phx.vista.DetalleConciliacionAdministradora=Ext.extend(Phx.gridInterfaz,{
        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                return "x-selectable";
            }
        },
        constructor: function(config) {
            this.maestro = config;

            Phx.vista.DetalleConciliacionAdministradora.superclass.constructor.call(this,config);

            this.tipo_reporte = new Ext.form.ComboBox({
                name : 'tipo_reporte',
                grupo: [0,1,2,3],
                fieldLabel : 'Fuente Pago',
                allowBlank : false,
                emptyText:'Fuente Pago.............',
                triggerAction : 'all',
                lazyRender : true,
                mode : 'local',
                store : new Ext.data.ArrayStore({
                    fields : ['tipo', 'valor'],
                    data : [
                        ['pago_atc', 'Pagos ATC'],
                        ['pago_linkser', 'Pagos LINKSER'],
                        ['pago_tigo', 'Pagos TIGO']
                    ]
                }),
                width:150,
                valueField : 'tipo',
                displayField : 'valor',
                msgTarget: 'side'
            });

            this.etiqueta_ini = new Ext.form.Label({
                name: 'etiqueta_ini',
                grupo: [0,1,2,3],
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
                grupo: [0,1,2,3],
                fieldLabel: 'Fecha',
                allowBlank: false,
                anchor: '60%',
                gwidth: 100,
                format: 'd/m/Y',
                hidden : false,
                disabled:false,
                msgTarget: 'side'
            });

            this.etiqueta_fin = new Ext.form.Label({
                name: 'etiqueta_fin',
                grupo: [0,1,2,3],
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
                grupo: [0,1,2,3],
                fieldLabel: 'Fecha',
                allowBlank: false,
                anchor: '60%',
                gwidth: 100,
                format: 'd/m/Y',
                hidden : false,
                disabled:false,
                msgTarget: 'side'
            });


            this.tbar.addField(this.tipo_reporte);
            this.tbar.addField(this.etiqueta_ini);
            this.tbar.addField(this.fecha_ini);
            this.tbar.addField(this.etiqueta_fin);
            this.tbar.addField(this.fecha_fin);
            this.iniciarEventos();
            this.bandera_alta = 0;
            this.bandera_baja = 0;

            this.grid.addListener('cellclick', this.mostrarDetalleVuelo,this);

            this.init();

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

        bactGroups:[0,1,2,3],
        bexcelGroups:[0,1,2,3],
        gruposBarraTareas: [
            {name: 'pago_administradora', title: '<h1 style="text-align: center; color: #FF8F85;"><i aria-hidden="true"></i>ADMINISTRADORA</h1>',grupo: 0, height: 1} ,
            {name: 'pago_ret', title: '<h1 style="text-align: center; color: #4682B4;"><i aria-hidden="true"></i>BOA</h1>', grupo: 1, height: 1},
            {name: 'pago_both', title: '<h1 style="text-align: center; color: #00B167;"><i aria-hidden="true"></i>CONCILIACIÓN</h1>', grupo: 2, height: 1},
            {name: 'pago_both_dis', title: '<h1 style="text-align: center; color: #B066BB;"><i aria-hidden="true"></i>CONCILIACIÓN OBS</h1>', grupo: 3, height: 1}
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

                this.store.baseParams.tipo_reporte = this.tipo_reporte.getValue();

                this.load({params: {start: 0, limit: 50}});
            },this);
        },
        actualizarSegunTab: function(name, indice){

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

                this.store.baseParams.tipo_reporte = this.tipo_reporte.getValue();
                this.load({params: {start: 0, limit: 50}});
            }
        },


        Atributos:[
            {
                // configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_conciliacion'
                },
                type:'Field',
                form:true

            },

            {
                config:{
                    fieldLabel: "Tipo",
                    gwidth: 130,
                    name: 'tipo',
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
                    fieldLabel: "DETALLE PAGO",
                    gwidth: 750,
                    name: 'dataPago',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: true,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        console.log('record', JSON.parse(record.json.datapago));
                        let conciliacion = JSON.parse(record.json.datapago);
                        let show = '';
                        if (conciliacion != null && conciliacion.tipo == 'pago_administradora'){
                            let admin = conciliacion.administradora;
                            show += `
                                    <table border="1">
                                        <caption>Pago Administradora</caption>
                                        <tr>
                                            <th>Establecimiento</th><th>Tipo</th><th>Nombre Punto</th><th>Fecha</th><th>Ticket</th><th>Moneda</th><th>N° Autorización</th><th>N° Tarjeta</th><th>Monto Cobrado</th>
                                        </tr>
                                        <tr>
                                            <td>${admin.establecimiento}</td><td>${admin.tipo_estable}</td><td>${admin.nombre_estable}</td><td>${admin.fecha_pago}</td><td>${admin.pago_ticket}</td><td>${admin.moneda_adm}</td><td>${admin.auth_codigo_adm}</td><td>${admin.num_tarjeta_adm}</td><td>${admin.monto_pago}</td>
                                        </tr>
                                    </table>
                                `;


                        } else if (conciliacion != null && conciliacion.tipo == 'pago_ret') {
                            let boa = conciliacion.boa;
                            show += `
                                    <table border="1">
                                        <caption>Venta BoA</caption>
                                        <tr>
                                            <th>Agencia</th><th>Tipo</th><th>Nombre Punto</th><th>Fecha</th><th>Boleto/Factura/RO</th><th>Moneda</th><th>N° Autorización</th><th>N° Tarjeta</th><th>Monto Venta</th>
                                        </tr>
                                        <tr>
                                            <td>${boa.codigo_iata}</td><td>${boa.tipo_punto}</td><td>${boa.nombre_punto}</td><td>${boa.fecha_venta}</td><td>${boa.numero_documento}</td><td>${boa.moneda_boa}</td><td>${boa.auth_codigo_boa}</td><td>${boa.num_tarjeta_boa}</td><td>${boa.monto_venta}</td>
                                        </tr>
                                    </table>
                                `;
                        } else if (conciliacion != null && conciliacion.tipo == 'pago_both') {
                            let admin = conciliacion.administradora;
                            show += `
                                    <table border="1">
                                        <caption>Pago Administradora</caption>
                                        <tr>
                                            <td>Establecimiento</td><td>Tipo</td><td>Nombre Punto</td><td>Fecha</td><td>Ticket</td><td>Moneda</td><td>N° Autorización</td><td>N° Tarjeta</td><td>Monto Cobrado</td>
                                        </tr>
                                        <tr>
                                            <td>${admin.establecimiento}</td><td>${admin.tipo_estable}</td><td>${admin.nombre_estable}</td><td>${admin.fecha_pago}</td><td>${admin.pago_ticket}</td><td>${admin.moneda_adm}</td><td>${admin.auth_codigo_adm}</td><td>${admin.num_tarjeta_adm}</td><td>${admin.monto_pago}</td>
                                        </tr>
                                    </table>
                                `;
                            show += `<br>
                                    <table border="1">
                                        <caption>Venta BoA</caption>
                                        <tr>
                                            <td>Agencia</td><td>Tipo</td><td>Nombre Punto</td><td>Fecha</td><td>Boleto/Factura/RO</td><td>Moneda</td><td>N° Autorización</td><td>N° Tarjeta</td><td>Monto Venta</td>
                                        </tr>`;

                            let boa = conciliacion.boa;
                            /*if (admin.auth_codigo_adm == '490103'){
                                console.log('boa 4', boa);
                            }*/
                            if (Array.isArray(boa)) {
                                boa.map(function (rec) {
                                    show += `<tr>
                                                <td>${rec.codigo_iata}</td><td>${rec.tipo_punto}</td><td>${boa.nombre_punto}</td><td>${rec.fecha_venta}</td><td>${rec.numero_documento}</td><td>${rec.moneda_boa}</td><td>${rec.auth_codigo_boa}</td><td>${rec.num_tarjeta_boa}</td><td>${rec.monto_venta}</td>
                                            </tr>`;
                                });
                            }else{
                                show += `<tr>
                                            <td>${boa.codigo_iata}</td><td>${boa.tipo_punto}</td><td>${boa.nombre_punto}</td><td>${boa.fecha_venta}</td><td>${boa.numero_documento}</td><td>${boa.moneda_boa}</td><td>${boa.auth_codigo_boa}</td><td>${boa.num_tarjeta_boa}</td><td>${boa.monto_venta}</td>
                                        </tr>`;
                            }

                            show += `</table>`;
                        }
                        return show;
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
                    fieldLabel: "OBSERVACIONES",
                    gwidth: 250,
                    name: 'observaciones',
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

            /*{
                config:{
                    fieldLabel: "Discrepancia",
                    gwidth: 130,
                    name: 'discrepancia',
                    allowBlank:true,
                    maxLength:100,
                    minLength:1,
                    anchor:'100%',
                    disabled: false,
                    style: 'color: blue; background-color: orange;',
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #586E7E; font-weight: bold;">{0}</div>', value);
                    }
                },
                type:'TextField',
                //filters:{pfiltro:'tca.nombre',type:'string'},
                //bottom_filter : true,
                id_grupo : 1,
                grid : true,
                form : true,
                egrid : true
            },*/

            {
                config:{
                    name: 'discrepancia',
                    fieldLabel: 'Discrepancia',
                    allowBlank: false,
                    emptyText:'Tipo...',
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local',
                    anchor: '100%',
                    gwidth: 100,
                    store:['autorizacion','moneda','monto','tarjeta']
                },
                type:'ComboBox',
                filters:{
                    type: 'list',
                    options: ['autorizacion','moneda','monto','tarjeta'],
                },
                id_grupo:0,
                grid:true,
                form:true,
                egrid : true
            },

            {
                config: {
                    name: 'id_relacion',
                    fieldLabel: 'ID. RELACIÓN',
                    typeAhead: false,
                    forceSelection: false,
                    hiddenName: 'id_tipo_planilla',
                    allowBlank: false,
                    emptyText: 'Lista de Planillas...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_obingresos/control/Reportes/generarIdentificadorAleatorio',
                        id: 'id_aleatorio',
                        root: 'datos',
                        sortInfo: {
                            field: 'codigo',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_aleatorio', 'numero'],
                        // turn on remote sorting
                        remoteSort: true,
                        baseParams: {par_filtro: 'numero'}
                    }),
                    valueField: 'id_aleatorio',
                    displayField: 'numero',
                    gdisplayField: 'numero',
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 20,
                    queryDelay: 200,
                    width: 270,
                    listWidth:270,
                    minChars: 2,
                    gwidth: 100,

                    msgTarget: 'side',
                    tpl: '<tpl for="."><div class="x-combo-list-item"><strong>{numero}</strong></div></tpl>'
                },
                type: 'ComboBox',
                id_grupo: 0,
                grid: true,
                form: true,
                //egrid : true
            }
            /*{
                config:{
                    fieldLabel: "ID. RELACIÓN",
                    gwidth: 130,
                    name: 'id_relacion',
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
            }*/
        ],
        title:'Detalle Conciliación',
        ActList:'../../sis_obingresos/control/Reportes/getDetalleConciliacionAdministradora',
        id_store:'id_conciliacion',
        fields: [
            {name:'id_conciliacion'},
            {name:'dataPago', type: 'string'},
            {name:'observaciones', type: 'string'},
            {name:'id_relacion', type: 'string'},
            {name:'tipo', type: 'string'}
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
