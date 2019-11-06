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
            this.load({params:{start:0, limit:this.tam_pag}})
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
            {
                config:{
                    name: 'instancia_pago_id',
                    fieldLabel: 'Instancia Pago ID',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:40
                },
                type:'TextField',
                filters: {pfiltro: 'movtip.nombre',type: 'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'id_medio_pago',
                    fieldLabel: 'Medio Pago ID',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:40
                },
                type:'TextField',
                filters: {pfiltro: 'movtip.nombre',type: 'string'},
                id_grupo:1,
                grid:true,
                form:true
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
            {
                config:{
                    name: 'codigo',
                    fieldLabel: 'Codigo',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10
                },
                type:'TextField',
                filters:{pfiltro:'insp.codigo',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'codigo_forma_pago',
                    fieldLabel: 'Codigo FP',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10
                },
                type:'TextField',
                filters:{pfiltro:'insp.codigo_forma_pago',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'codigo_medio_pago',
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
                form:true
            },
            {
                config:{
                    name: 'fp_code',
                    fieldLabel: 'Codigo FP',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:20
                },
                type:'TextField',
                filters:{pfiltro:'insp.fp_code',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
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
            {name:'instancia_pago_id', type: 'numeric'},
            {name:'nombre', type: 'string'},
            {name:'codigo', type: 'string'},
            {name:'codigo_forma_pago', type: 'string'},
            {name:'codigo_medio_pago', type: 'string'},
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

        ],
        sortInfo:{
            field: 'id_instancia_pago',
            direction: 'ASC'
        },
        bdel:false,
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

		
