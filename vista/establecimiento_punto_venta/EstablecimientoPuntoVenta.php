<?php
/**
 *@package pXP
 *@file gen-EstablecimientoPuntoVenta.php
 *@author  (admin)
 *@date 17-03-2021 11:14:41
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.EstablecimientoPuntoVenta=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.maestro=config.maestro;
                //llama al constructor de la clase padre
                Phx.vista.EstablecimientoPuntoVenta.superclass.constructor.call(this,config);
                this.init();
                this.load({params:{start:0, limit:this.tam_pag}})
            },

            Atributos:[
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_establecimiento_punto_venta'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config:{
                        name: 'comercio',
                        fieldLabel: 'Comercio',
                        allowBlank: false,
                        anchor: '60%',
                        gwidth: 100,
                        maxLength:20,
                        msgTarget: 'side'
                    },
                    type:'TextField',
                    filters:{pfiltro:'estpven.codigo_estable',type:'string'},
                    bottom_filter:true,
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'tipo_estable',
                        fieldLabel: 'Tipo Establecimiento',
                        editable: false,
                        allowBlank: false,
                        emptyText:'Tipo...',
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender:true,
                        mode: 'local',
                        anchor: '60%',
                        gwidth: 150,
                        store:['propia','externa'],
                        msgTarget: 'side'
                    },
                    type:'ComboBox',
                    filters:{
                        type: 'list',
                        options: ['propia','externa'],
                    },
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'codigo_estable',
                        fieldLabel: 'Cod. Establecimiento',
                        allowBlank: false,
                        anchor: '60%',
                        gwidth: 130,
                        maxLength:20,
                        msgTarget: 'side'
                    },
                    type:'TextField',
                    filters:{pfiltro:'estpven.codigo_estable',type:'string'},
                    bottom_filter:true,
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'nombre_estable',
                        fieldLabel: 'Nombre Establecimiento',
                        allowBlank: false,
                        anchor: '60%',
                        gwidth: 200,
                        maxLength:256,
                        msgTarget: 'side'
                    },
                    type:'TextField',
                    filters:{pfiltro:'estpven.nombre_estable',type:'string'},
                    bottom_filter:true,
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'direccion_estable',
                        fieldLabel: 'Dirección Establecimiento',
                        allowBlank: false,
                        anchor: '60%',
                        gwidth: 200,
                        maxLength:256,
                        msgTarget: 'side'
                    },
                    type:'TextArea',
                    filters:{pfiltro:'estpven.nombre_estable',type:'string'},
                    bottom_filter:true,
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'id_lugar',
                        fieldLabel: 'Lugar Establecimiento',
                        allowBlank: false,
                        emptyText:'Lugar...',
                        resizable:true,
                        msgTarget: 'side',
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
                                fields: ['id_lugar','nombre'],
                                // turn on remote sorting
                                remoteSort: true,
                                baseParams:{tipos:"''departamento'',''pais'',''localidad''",par_filtro:'nombre'}
                            }),
                        valueField: 'id_lugar',
                        displayField: 'nombre',
                        gdisplayField:'id_lugar',
                        hiddenName: 'id_lugar',
                        triggerAction: 'all',
                        lazyRender:true,
                        mode:'remote',
                        pageSize:50,
                        queryDelay:500,
                        anchor:"60% ",
                        gwidth:170,
                        forceSelection:true,
                        minChars:2,
                        renderer:function (value, p, record){return String.format('{0}', record.data['nombre_lugar']);}
                    },
                    type:'ComboBox',
                    filters:{pfiltro:'tl.nombre',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config: {
                        name: 'id_punto_venta',
                        fieldLabel: 'Office Id.',
                        allowBlank: true,
                        emptyText: 'Elija una opción...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
                            id: 'id_punto_venta',
                            root: 'datos',
                            sortInfo: {
                                field: 'office_id',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_punto_venta','office_id', 'nombre_amadeus','nombre', 'codigo'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'puve.nombre#puve.nombre_amadeus#puve.office_id',tipo_usuario:'todos'}
                        }),
                        valueField: 'id_punto_venta',
                        displayField: 'nombre',
                        gdisplayField: 'nombre_office',
                        hiddenName: 'id_punto_venta',
                        forceSelection: true,
                        typeAhead: false,
                        editable: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        anchor: '60%',
                        /*width: 200,*/
                        gwidth: 230,
                        minChars: 2,
                        resizable:true,
                        listWidth:'270',
                        msgTarget: 'side',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:green;"><b style="color:black;">Office ID:</b> <b>{office_id}</b></p><p style="color:red;"><b style="color:black;">Nombre Amadeus:</b> <b>{nombre}</b></p></div></tpl>',
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['nombre_office']);
                        }
                    },
                    type: 'ComboBox',
                    bottom_filter:true,
                    id_grupo: 1,
                    filters: {pfiltro: 'tpv.office_id#tpv.nombre', type: 'numeric'},
                    grid: true,
                    form: true
                },
                {
                    config:{
                        name: 'iata_code',
                        fieldLabel: 'Iata Code',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:10
                    },
                    type:'TextField',
                    filters:{pfiltro:'estpven.estado_reg',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config: {
                        name: 'id_stage_pv',
                        fieldLabel: 'Nombre Iata',
                        allowBlank: true,
                        emptyText: 'Elija un Codigo IATA...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_obingresos/control/EstablecimientoPuntoVenta/listarPuntoVentaStage',
                            id: 'id_stage_pv',
                            root: 'datos',
                            sortInfo: {
                                field: 'id_stage_pv',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_stage_pv','iata_zone_name','contry_name','city_name','sale_channel','iata_code','iata_status','name_pv','nit'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'spv.name_pv#spv.iata_code#spv.city_name'}
                        }),
                        valueField: 'id_stage_pv',
                        displayField: 'name_pv',
                        gdisplayField: 'name_pv',
                        hiddenName: 'id_stage_pv',
                        forceSelection: true,
                        typeAhead: false,
                        editable: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        anchor: '60%',
                        /*width: 200,*/
                        gwidth: 200,
                        minChars: 2,
                        resizable:true,
                        listWidth:'270',
                        msgTarget: 'side',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:green;"><b style="color:black;">Cod. IATA:</b> <b>{iata_code}</b></p><p style="color:red;"><b style="color:black;">Nombre :</b> <b>{name_pv}</b></p></div></tpl>',
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['nombre_iata']);
                        }
                    },
                    type: 'ComboBox',
                    bottom_filter:true,
                    id_grupo: 1,
                    filters: {pfiltro: 'spv.name_pv', type: 'string'},
                    grid: true,
                    form: true
                },
                {
                    config:{
                        name: 'estado_reg',
                        fieldLabel: 'Estado Reg.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:10
                    },
                    type:'TextField',
                    filters:{pfiltro:'estpven.estado_reg',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'usr_reg',
                        fieldLabel: 'Creado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:4
                    },
                    type:'Field',
                    filters:{pfiltro:'usu1.cuenta',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'fecha_reg',
                        fieldLabel: 'Fecha creación',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'estpven.fecha_reg',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'id_usuario_ai',
                        fieldLabel: 'Fecha creación',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:4
                    },
                    type:'Field',
                    filters:{pfiltro:'estpven.id_usuario_ai',type:'numeric'},
                    id_grupo:1,
                    grid:false,
                    form:false
                },
                {
                    config:{
                        name: 'usuario_ai',
                        fieldLabel: 'Funcionaro AI',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'estpven.usuario_ai',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'usr_mod',
                        fieldLabel: 'Modificado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:4
                    },
                    type:'Field',
                    filters:{pfiltro:'usu2.cuenta',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'fecha_mod',
                        fieldLabel: 'Fecha Modif.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'estpven.fecha_mod',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:false
                }
            ],
            tam_pag:50,
            title:'Establecimiento Punto Venta',
            ActSave:'../../sis_obingresos/control/EstablecimientoPuntoVenta/insertarEstablecimientoPuntoVenta',
            ActDel:'../../sis_obingresos/control/EstablecimientoPuntoVenta/eliminarEstablecimientoPuntoVenta',
            ActList:'../../sis_obingresos/control/EstablecimientoPuntoVenta/listarEstablecimientoPuntoVenta',
            id_store:'id_establecimiento_punto_venta',
            fields: [
                {name:'id_establecimiento_punto_venta', type: 'numeric'},
                {name:'estado_reg', type: 'string'},
                {name:'codigo_estable', type: 'string'},
                {name:'nombre_estable', type: 'string'},
                {name:'id_punto_venta', type: 'numeric'},
                {name:'id_usuario_reg', type: 'numeric'},
                {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'id_usuario_ai', type: 'numeric'},
                {name:'usuario_ai', type: 'string'},
                {name:'id_usuario_mod', type: 'numeric'},
                {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'usr_reg', type: 'string'},
                {name:'usr_mod', type: 'string'},
                {name:'nombre_office', type: 'string'},
                {name:'tipo_estable', type: 'string'},
                {name:'comercio', type: 'string'},
                {name:'nombre_iata', type: 'string'},
                {name:'nombre_lugar', type: 'string'},
                {name:'id_stage_pv', type: 'numeric'},
                {name:'iata_code', type: 'string'},
                {name:'direccion_estable', type: 'string'}

            ],
            sortInfo:{
                field: 'id_establecimiento_punto_venta',
                direction: 'ASC'
            },
            bdel:true,
            bsave:false
        }
    )
</script>

