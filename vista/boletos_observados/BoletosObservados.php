<?php
/**
 *@package pXP
 *@file gen-BoletosObservados.php
 *@author  (admin)
 *@date 04-06-2019 19:39:16
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.BoletosObservados=Ext.extend(Phx.gridInterfaz,{

        constructor:function(config){
            this.maestro=config.maestro;
            //llama al constructor de la clase padre
            Phx.vista.BoletosObservados.superclass.constructor.call(this,config);
            this.init();
            //this.load({params:{start:0, limit:this.tam_pag}})
        },

        Atributos:[
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_boletos_observados'
                },
                type:'Field',
                form:true
            },

            {
                config:{
                    name: 'pnr',
                    fieldLabel: 'PNR',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10
                },
                type:'TextField',
                filters:{pfiltro:'bobs.pnr',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'nro_autorizacion',
                    fieldLabel: 'Nro. Autorización',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:256
                },
                type:'TextField',
                filters:{pfiltro:'bobs.nro_autorizacion',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'moneda',
                    fieldLabel: 'Moneda',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4
                },
                type:'TextField',
                filters:{pfiltro:'bobs.moneda',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'importe_total',
                    fieldLabel: 'Importe Total',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:1179650
                },
                type:'NumberField',
                filters:{pfiltro:'bobs.importe_total',type:'numeric'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'fecha_emision',
                    fieldLabel: 'Fecha  Emisión',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'bobs.fecha_emision',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'estado_p',
                    fieldLabel: 'Estado P.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10
                },
                type:'TextField',
                filters:{pfiltro:'bobs.estado_p',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'forma_pago',
                    fieldLabel: 'Forma Pago',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10
                },
                type:'TextField',
                filters:{pfiltro:'bobs.forma_pago',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'medio_pago',
                    fieldLabel: 'Medio Pago',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10
                },
                type:'TextField',
                filters:{pfiltro:'bobs.medio_pago',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'instancia_pago',
                    fieldLabel: 'Instancia Pago',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10
                },
                type:'TextField',
                filters:{pfiltro:'bobs.instancia_pago',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'office_id_emisor',
                    fieldLabel: 'Office Emisor ID',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10
                },
                type:'TextField',
                filters:{pfiltro:'bobs.office_id_emisor',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'pnr_prov',
                    fieldLabel: 'PNR Prov',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10
                },
                type:'TextField',
                filters:{pfiltro:'bobs.pnr_prov',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'nro_autorizacion_prov',
                    fieldLabel: 'Nro. autorizacion Prov.',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:256
                },
                type:'TextField',
                filters:{pfiltro:'bobs.nro_autorizacion_prov',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'office_id_emisor_prov',
                    fieldLabel: 'Office emisor Prov. ID',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10
                },
                type:'TextField',
                filters:{pfiltro:'bobs.office_id_emisor_prov',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'importe_prov',
                    fieldLabel: 'Importe Prov.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:1179650
                },
                type:'NumberField',
                filters:{pfiltro:'bobs.importe_prov',type:'numeric'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'moneda_prov',
                    fieldLabel: 'Moneda Prov.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4
                },
                type:'TextField',
                filters:{pfiltro:'bobs.moneda_prov',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'estado_prov',
                    fieldLabel: 'Estado Prov.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10
                },
                type:'TextField',
                filters:{pfiltro:'bobs.estado_prov',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'fecha_autorizacion_prov',
                    fieldLabel: 'Fecha Autorizacion Prov.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'bobs.fecha_autorizacion_prov',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'tipo_error',
                    fieldLabel: 'Tipo Error',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:100
                },
                type:'TextField',
                filters:{pfiltro:'bobs.tipo_error',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'tipo_validacion',
                    fieldLabel: 'Tipo Validación',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:50
                },
                type:'TextField',
                filters:{pfiltro:'bobs.tipo_validacion',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'prov_informacion',
                    fieldLabel: 'Prov. Informacion',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:256
                },
                type:'TextField',
                filters:{pfiltro:'bobs.prov_informacion',type:'string'},
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
                filters:{pfiltro:'bobs.estado_reg',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            /*{
                config: {
                    name: 'id_instancia_pago',
                    fieldLabel: 'id_instancia_pago',
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
                    hiddenName: 'id_instancia_pago',
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
                filters:{pfiltro:'bobs.fecha_reg',type:'date'},
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
                filters:{pfiltro:'bobs.id_usuario_ai',type:'numeric'},
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
                filters:{pfiltro:'bobs.usuario_ai',type:'string'},
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
                filters:{pfiltro:'bobs.fecha_mod',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            }
        ],
        tam_pag:50,
        title:'Boletos Observados',
        ActSave:'../../sis_obingresos/control/BoletosObservados/insertarBoletosObservados',
        ActDel:'../../sis_obingresos/control/BoletosObservados/eliminarBoletosObservados',
        ActList:'../../sis_obingresos/control/BoletosObservados/listarBoletosObservados',
        id_store:'id_boletos_observados',
        fields: [
            {name:'id_boletos_observados', type: 'numeric'},
            {name:'estado_reg', type: 'string'},
            {name:'pnr', type: 'string'},
            {name:'nro_autorizacion', type: 'string'},
            {name:'moneda', type: 'string'},
            {name:'importe_total', type: 'numeric'},
            {name:'fecha_emision', type: 'date',dateFormat:'Y-m-d'},
            {name:'estado_p', type: 'string'},
            {name:'forma_pago', type: 'string'},
            {name:'medio_pago', type: 'string'},
            {name:'instancia_pago', type: 'string'},
            {name:'office_id_emisor', type: 'string'},
            {name:'pnr_prov', type: 'string'},
            {name:'nro_autorizacion_prov', type: 'string'},
            {name:'office_id_emisor_prov', type: 'string'},
            {name:'importe_prov', type: 'numeric'},
            {name:'moneda_prov', type: 'string'},
            {name:'estado_prov', type: 'string'},
            {name:'fecha_autorizacion_prov', type: 'date',dateFormat:'Y-m-d'},
            {name:'tipo_error', type: 'string'},
            {name:'tipo_validacion', type: 'string'},
            {name:'prov_informacion', type: 'string'},
            //{name:'id_instancia_pago', type: 'numeric'},
            {name:'id_usuario_reg', type: 'numeric'},
            {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'id_usuario_ai', type: 'numeric'},
            {name:'usuario_ai', type: 'string'},
            {name:'id_usuario_mod', type: 'numeric'},
            {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'usr_reg', type: 'string'},
            {name:'usr_mod', type: 'string'},

        ],
        sortInfo:{
            field: 'id_boletos_observados',
            direction: 'ASC'
        },
        bdel:false,
        bsave:false,
        bedit:true,
        bnew:false,
        loadValoresIniciales: function(){
            this.Cmp.id_reclamo.setValue(this.maestro.id_reclamo);
            Phx.vista.Informe.superclass.loadValoresIniciales.call(this);
        },
        onReloadPage:function(param){
            this.maestro = param;
            //this.store.baseParams=param;
            this.store.baseParams = {codigo_instancia_pago: this.maestro.codigo ,codigo_forma_pago:this.maestro.codigo_forma_pago, codigo_medio_pago:this.maestro.codigo_medio_pago};
            this.load( { params: { start:0, limit: this.tam_pag } });
        }

    });
</script>
