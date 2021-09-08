<?php
/**
 *@package      BoA
 *@file         Cargo.php
 *@author       (franklin.espinoza)
 *@date         11-08-2021 09:16:06
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.ExcluirAgencia=Ext.extend(Phx.gridInterfaz,{

    constructor:function(config){
        this.maestro=config;
        //llama al constructor de la clase padre
        Phx.vista.ExcluirAgencia.superclass.constructor.call(this,config);

        this.label_gestion = new Ext.form.Label({
            name: 'label_gestion',
            grupo: [0,1,2,3,4,5,6],
            fieldLabel: 'Gestión',
            text: ' Gestión:',
            //style: {color: 'green', font_size: '12pt'},
            readOnly:true,
            anchor: '150%',
            gwidth: 150,
            format: 'd/m/Y',
            hidden : false,
            style: 'font-size: 170%; font-weight: bold; background-image: none;color: #CD6155;'
        });
        this.gestion = new Ext.form.ComboBox({
            name: 'gestion',
            fieldLabel: 'Gestion',
            allowBlank: true,
            emptyText:'...........',
            blankText: 'Año',
            grupo: [0,1,2,3,4,5,6],
            store:new Ext.data.JsonStore(
                {
                    url: '../../sis_parametros/control/Gestion/listarGestion',
                    id: 'id_gestion',
                    root: 'datos',
                    sortInfo:{
                        field: 'gestion',
                        direction: 'DESC'
                    },
                    totalProperty: 'total',
                    fields: ['id_gestion','gestion'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams:{par_filtro:'gestion'}
                }),
            valueField: 'id_gestion',
            triggerAction: 'all',
            displayField: 'gestion',
            hiddenName: 'id_gestion',
            mode:'remote',
            pageSize:12,
            queryDelay:500,
            listWidth:'100',
            hidden:false,
            editable : false,
            disabled: true,
            width:100,
            resizable:true,
            style : {fontWeight : 'bolder', color : '#00B167'},
            value:this.maestro.fecha.getFullYear()
        });

        this.label_periodo = new Ext.form.Label({
            name: 'label_periodo',
            grupo: [0,1,2,3,4,5],
            fieldLabel: 'Fecha Fin',
            text: ' Periodo:',
            //style: {color: 'red', font_size: '12pt'},
            readOnly:true,
            anchor: '150%',
            gwidth: 150,
            format: 'd/m/Y',
            hidden : false,
            style: 'font-size: 170%; font-weight: bold; background-image: none; color: #CD6155;'
        });

        this.periodo = new Ext.form.ComboBox({
            fieldLabel: 'Periodo',
            allowBlank: false,
            blankText: 'Mes',
            emptyText: '...........',
            grupo: [0,1,2,3,4,5,6],
            msgTarget:'side',
            /*store: new Ext.data.JsonStore(
                {
                    url: '../../sis_parametros/control/Periodo/listarPeriodo',
                    id: 'id_periodo',
                    root: 'datos',
                    sortInfo: {
                        field: 'periodo',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_periodo', 'periodo', 'id_gestion', 'literal'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams: {par_filtro: 'gestion'}
                }),*/
            store:['1','2','3','4','5','6','7','8','9','10','11','12'],
            //valueField: 'periodo',
            triggerAction: 'all',
            /*displayField: 'periodo',
            hiddenName: 'id_periodo',*/
            mode: 'local',
            //pageSize: 12,
            //queryDelay: 500,
            lazyRender:true,
            listWidth: '100',
            width: 100,
            resizable:true,
            editable : false,
            //disabled: true,
            style : {fontWeight : 'bolder', color : '#00B167'},
            value : this.maestro.fecha.getMonth()+1
        });

        this.tbar.addField(this.label_gestion);
        this.tbar.addField(this.gestion);
        this.tbar.addField(this.label_periodo);
        this.tbar.addField(this.periodo);

        this.init();

        if ( this.gestion.getRawValue() != '' && this.periodo.getRawValue() != '' ){
            this.store.baseParams.gestion = this.gestion.getRawValue();
            this.store.baseParams.periodo = this.periodo.getRawValue();
            this.load({params:{start:0, limit:50}});
        }
        this.iniciarEventos();

    },

    successSave:function(resp){
        Phx.vista.ExcluirAgencia.superclass.successSave.call(this,resp);
        var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText)).ROOT.datos;
        console.log('successSave', objRes);
        Ext.Msg.show({
            title: 'Información',
            msg: '<b>'+objRes[0].Message+'</b>',
            buttons: Ext.Msg.OK,
            width: 512,
            icon: Ext.Msg.INFO
        });
    },

    /*successSave:function(resp){
        Phx.vista.ExcluirAgencia.superclass.successSave.call(this,resp);
        var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText)).ROOT.datos;

        Ext.Msg.show({
            title: 'Información',
            msg: '<b>'+objRes[0].Message+'</b>',
            buttons: Ext.Msg.OK,
            width: 512,
            icon: Ext.Msg.INFO
        });
    },*/

    successDel:function(resp){
        Phx.vista.ExcluirAgencia.superclass.successDel.call(this,resp);
        var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText)).ROOT.datos;
        Ext.Msg.show({
            title: 'Información',
            msg: '<b>'+objRes[0].Message+'</b>',
            buttons: Ext.Msg.OK,
            width: 512,
            icon: Ext.Msg.INFO
        });
    },

    Atributos:[

        {
            //configuracion del componente
            config:{
                labelSeparator:'',
                inputType:'hidden',
                name: 'id_exclude_agencie'
            },
            type:'Field',
            form:true
        },
        /*{
            config:{
                msgTarget:'side',
                name: 'iataCode',
                fieldLabel: 'Codigo Iata',
                allowBlank: false,
                anchor: '95%',
                gwidth: 100,
                maxLength:20
            },
            type:'TextField',
            id_grupo:1,
            grid:true,
            form:true
        },*/
        {
            config: {
                name: 'iataCode',
                fieldLabel: 'Codigo Iata',
                allowBlank: false,
                disabled: false,
                emptyText: '',
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
                gwidth: 100,
                resizable:true,
                minChars: 2,
                anchor: '95%',
                hidden : false,
                style:'margin-bottom: 10px;',
                renderer : function(value, p, record) {
                    return String.format('<b><span style="color: #B066BB;">{0}</span></b>', record.data['iataCode']);
                }

            },
            type: 'ComboBox',
            //valorInicial: 'TODOS',
            id_grupo: 1,
            filters: {pfiltro: 'puve.nombre',type: 'string'},
            form: true,
            grid: true
        },
        /*{
            config:{
                msgTarget:'side',
                name: 'officeId',
                fieldLabel: 'Office ID',
                allowBlank: true,
                anchor: '95%',
                gwidth: 100,
                maxLength:20
            },
            type:'TextField',
            id_grupo:1,
            grid:true,
            form:true
        },*/
        {
            config: {
                name: 'officeId',
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
                gwidth: 100,
                resizable:true,
                minChars: 2,
                hidden : false,
                renderer : function(value, p, record) {
                    return String.format('<b><span style="color: #B066BB;">{0}</span></b>', record.data['officeId']);
                }
            },
            type: 'ComboBox',
            //valorInicial: 'TODOS',
            id_grupo: 1,
            form: true,
            grid: true
        },

        {
            config:{
                msgTarget:'side',
                name : 'f_ini',
                fieldLabel : 'Habilitado Desde',
                allowBlank : false,
                width : 177,
                gwidth : 125,
                format : 'd/m/Y',
                renderer:function (value,p,record){
                    return value?'<span style="color: #00B167;">'+value.dateFormat('d/m/Y')+'</span>':''
                }
            },
            type:'DateField',
            id_grupo:1,
            grid:true,
            form:true
        },

        {
            config:{
                msgTarget:'side',
                name: 'f_fin',
                fieldLabel: 'Habilitado Hasta',
                allowBlank: false,
                width : 177,
                gwidth: 125,
                format: 'd/m/Y',
                renderer:function (value,p,record){
                    return value?'<span style="color: #00B167;">'+value.dateFormat('d/m/Y')+'</span>':''
                }
            },
            type:'DateField',
            id_grupo:1,
            grid:true,
            form:true
        },

        {
            config:{
                msgTarget:'side',
                name: 'obs',
                fieldLabel: 'Observaciones',
                allowBlank: false,
                anchor: '95%',
                gwidth: 300,
                maxLength:2046,
                renderer : function(value, p, record) {
                    return String.format('<b><span style="color: #FF8F85;">{0}</span></b>', value);
                }

            },
            type:'TextArea',
            id_grupo:1,
            grid:true,
            form:true
        },

        {
            config:{
                msgTarget:'side',
                name: 'estado',
                fieldLabel: 'Estado',
                allowBlank: true,
                width: '177',
                gwidth: 100,
                maxLength:20,
                renderer : function(value, p, record) {
                    return String.format('<b><span style="color: #FF8F85;">{0}</span></b>', value);
                }
            },
            type:'TextField',
            id_grupo:1,
            grid:true,
            form:true
        }
    ],
    bodyStyle: 'padding:0 10px 0;',
    fwidth: '40%',
    fheight : '45%',
    tam_pag:50,
    title:'Excluir Agencia',
    ActSave:'../../sis_obingresos/control/ExcluirAgencia/insertarExcluirAgencia',
    ActDel:'../../sis_obingresos/control/ExcluirAgencia/eliminarExcluirAgencia',
    ActList:'../../sis_obingresos/control/ExcluirAgencia/listarExcluirAgencia',
    id_store:'id_exclude_agencie',
    fields: [
        {name:'id_exclude_agencie', type: 'numeric'},
        {name:'f_ini', type: 'date',dateFormat:'d/m/Y'},
        {name:'f_fin', type: 'date',dateFormat:'d/m/Y'},
        {name:'iataCode', type: 'string'},
        {name:'officeId', type: 'string'},
        {name:'acefalo', type: 'string'},
        {name:'estado', type: 'string'},
        {name:'obs', type: 'string'}
    ],
    sortInfo:{
        field: 'id_exclude_agencie',
        direction: 'ASC'
    },
    bdel:true,
    bsave:false,
    bedit:true,
    iniciarEventos : function() {
        //inicio de eventos
        this.gestion.on('select', function (combo,rec,index) {
            //this.periodo.store.baseParams.id_gestion = this.gestion.getValue();
            //this.periodo.store.reload();
            if(this.periodo.getRawValue() != ''){
                this.store.baseParams.gestion = this.gestion.getRawValue();
                this.store.baseParams.periodo = this.periodo.getRawValue();
                this.load({params:{start:0, limit:50}});
            }
        },this);

        this.periodo.on('expand', function (combo) {

            if(this.gestion.getValue() == ''){
                this.periodo.collapse( );
                Ext.Msg.show({
                    title: 'Información',
                    msg: '<b><span style="color: red;">Estimado Usuario:</span><br>Debe seleccionar una gestión.</b>',
                    buttons: Ext.Msg.OK,
                    width: 400,
                    icon: Ext.Msg.WARNING
                });
            }
        },this);

        this.periodo.on('select', function ( combo, record, index ) {

            this.store.baseParams.gestion = this.gestion.getRawValue();
            this.store.baseParams.periodo = record.data.field1;
            this.load({params:{start:0, limit:50}});
        },this);


    },

    preparaMenu:function()
    {
        //this.getBoton('btnCostos').enable();
        Phx.vista.ExcluirAgencia.superclass.preparaMenu.call(this);
    },

    liberaMenu:function()
    {
        //this.getBoton('btnCostos').disable();
        Phx.vista.ExcluirAgencia.superclass.liberaMenu.call(this);
    },

    onButtonEdit : function () {
        //this.ocultarComponente(this.Cmp.id_escala_salarial);
        //this.ocultarComponente(this.Cmp.id_tipo_contrato);

        Phx.vista.ExcluirAgencia.superclass.onButtonEdit.call(this);
    },
    onButtonNew : function () {
        //this.mostrarComponente(this.Cmp.id_escala_salarial);
        //this.mostrarComponente(this.Cmp.id_tipo_contrato);
        Phx.vista.ExcluirAgencia.superclass.onButtonNew.call(this);
    }
});
</script>