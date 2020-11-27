<?php
/**
 *@package pXP
 *@file gen-InstanciaPago.php
 *@author  (admin)
 *@date 04-06-2019 19:31:28
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.InstanciaPago=Ext.extend(Phx.gridInterfaz,{

        constructor:function(config){
            this.maestro=config.maestro;
            //llama al constructor de la clase padre
            Phx.vista.InstanciaPago.superclass.constructor.call(this,config);
            this.init();
            this.load({params:{start:0, limit:this.tam_pag}});
        },


        Atributos:[
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_instancia_pago'
                },
                type:'Field',
                form:true
            },

            /*{
                config: {
                    name: 'id_medio_pago',
                    fieldLabel: 'Medio Pago ID',
                    allowBlank: true,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_/control/Clase/Metodo',
                        id: 'id_',
                        root: 'datos',
                        sortInfo: {
                            field: 'nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_', 'nombre', 'codigo'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'movtip.nombre#movtip.codigo'}
                    }),
                    valueField: 'id_',
                    displayField: 'nombre',
                    gdisplayField: 'desc_',
                    hiddenName: 'id_medio_pago',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    anchor: '100%',
                    gwidth: 150,
                    minChars: 2,
                    renderer : function(value, p, record) {
                        return String.format('{0}', record.data['desc_']);
                    }
                },
                type: 'ComboBox',
                id_grupo: 0,
                filters: {pfiltro: 'movtip.nombre',type: 'string'},
                grid: true,
                form: true
            },*/
            // {
            //     config:{
            //         name: 'instancia_pago_id',
            //         fieldLabel: 'Instancia Pago ID',
            //         allowBlank: false,
            //         anchor: '80%',
            //         gwidth: 100,
            //         maxLength:40
            //     },
            //     type:'TextField',
            //     filters: {pfiltro: 'movtip.nombre',type: 'string'},
            //     id_grupo:1,
            //     grid:true,
            //     form:true
            // },
            // {
            //     config:{
            //         name: 'id_medio_pago',
            //         fieldLabel: 'Medio Pago ID',
            //         allowBlank: false,
            //         anchor: '80%',
            //         gwidth: 100,
            //         maxLength:40
            //     },
            //     type:'TextField',
            //     filters: {pfiltro: 'movtip.nombre',type: 'string'},
            //     id_grupo:1,
            //     grid:true,
            //     form:true
            // },
            {
                config: {
                    name: 'id_medio_pago',
                    fieldLabel: 'Medio de Pago',
                    allowBlank: true,
                    width:200,
                    anchor: '80%',
                    emptyText: 'Elija la forma de pago relacionada...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_obingresos/control/MedioPagoPw/listarMedioPagoPw',
                        id: 'id_forma_pago_pw',
                        root: 'datos',
                        sortInfo: {
                            field: 'id_medio_pago_pw',
                            direction: 'DESC'
                        },
                        totalProperty: 'total',
                        fields: ['id_medio_pago_pw', 'name','mop_code'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'mppw.name'}
                    }),
                    valueField: 'id_medio_pago_pw',
                    gdisplayField : 'name',
                    displayField: 'name',
                    hiddenName: 'id_medio_pago_pw',
                    tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:red; font-weight:bold;"><b style="color:Black">Nombre:</b> {name}</p><p style="color:green; font-weight:bold;"><b style="color:Black">Cod:</b> {mop_code}</p></div></tpl>',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    disabled:false,
                    minChars: 2,
                    gwidth: 200,
                    listWidth:'500',
                    // renderer: function(value, p, record){
                    //     return String.format('<b style="color:blue; ">{0}</b>', record.data['name']);
                    // },
                },
                type: 'ComboBox',
                id_grupo: 1,
                form: true,
                grid:true
            },
            {
                config:{
                    name: 'nombre',
                    fieldLabel: 'Nombre',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:40
                },
                type:'TextField',
                filters:{pfiltro:'insp.nombre',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            // {
            //     config:{
            //         name: 'codigo',
            //         fieldLabel: 'Codigo',
            //         allowBlank: false,
            //         anchor: '80%',
            //         gwidth: 100,
            //         maxLength:10
            //     },
            //     type:'TextField',
            //     filters:{pfiltro:'insp.codigo',type:'string'},
            //     id_grupo:1,
            //     grid:true,
            //     form:true
            // },
            {
                config:{
                    name: 'codigo_fp',
                    fieldLabel: 'Codigo FP',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10
                },
                type:'TextField',
                filters:{pfiltro:'fp.fop_code',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'codigo_mp',
                    fieldLabel: 'Codigo MP',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:20
                },
                type:'TextField',
                filters:{pfiltro:'insp.codigo_medio_pago',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            // {
            //     config:{
            //         name: 'fp_code',
            //         fieldLabel: 'Codigo FP',
            //         allowBlank: true,
            //         anchor: '80%',
            //         gwidth: 100,
            //         maxLength:20
            //     },
            //     type:'TextField',
            //     filters:{pfiltro:'insp.fp_code',type:'string'},
            //     id_grupo:1,
            //     grid:true,
            //     form:true
            // },
            {
                config:{
                    name: 'ins_code',
                    fieldLabel: 'Codigo INS',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:20
                },
                type:'TextField',
                filters:{pfiltro:'insp.ins_code',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
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
                filters:{pfiltro:'insp.estado_reg',type:'string'},
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
                filters:{pfiltro:'insp.fecha_reg',type:'date'},
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
                filters:{pfiltro:'insp.id_usuario_ai',type:'numeric'},
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
                filters:{pfiltro:'insp.usuario_ai',type:'string'},
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
                filters:{pfiltro:'insp.fecha_mod',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            }
        ],
        tam_pag:50,
        title:'Instancia Pago',
        ActSave:'../../sis_obingresos/control/InstanciaPago/insertarInstanciaPago',
        ActDel:'../../sis_obingresos/control/InstanciaPago/eliminarInstanciaPago',
        ActList:'../../sis_obingresos/control/InstanciaPago/listarInstanciaPago',
        id_store:'id_instancia_pago',
        fields: [
            {name:'id_instancia_pago', type: 'numeric'},
            {name:'estado_reg', type: 'string'},
            {name:'id_medio_pago', type: 'numeric'},
            //{name:'instancia_pago_id', type: 'numeric'},
            {name:'nombre', type: 'string'},
            // {name:'codigo', type: 'string'},
            // {name:'codigo_forma_pago', type: 'string'},
            // {name:'codigo_medio_pago', type: 'string'},
            {name:'id_usuario_reg', type: 'numeric'},
            {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'id_usuario_ai', type: 'numeric'},
            {name:'usuario_ai', type: 'string'},
            {name:'id_usuario_mod', type: 'numeric'},
            {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'usr_reg', type: 'string'},
            {name:'usr_mod', type: 'string'},
            {name:'fp_code', type: 'string'},
            {name:'ins_code', type: 'string'},
            {name:'name', type: 'string'},

            {name:'codigo', type: 'string'},
            {name:'codigo_fp', type: 'string'},
            {name:'codigo_mp', type: 'string'},

        ],
        sortInfo:{
            field: 'id_instancia_pago',
            direction: 'ASC'
        },
        bdel:true,
        bsave:false,
        bedit:true,
        //bnew:false,
        tabsouth :[
            {
                url:'../../../sis_obingresos/vista/boletos_observados/BoletosObservados.php',
                title:'Boletos Observados',
                height:'50%',
                cls:'BoletosObservados'
            }
        ],
    });
</script>
