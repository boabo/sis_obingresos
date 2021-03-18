<?php
/**
 *@package pXP
 *@file DepositoCuentaCorriente.php
 *@author  (bvasquez)
 *@date 18-03-2021
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.DepositoCuentaCorriente=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.maestro=config.maestro;
                //llama al constructor de la clase padre
                Phx.vista.DepositoCuentaCorriente.superclass.constructor.call(this,config);

                this.store.baseParams.tipo_deposito = 'auxiliar';
                this.store.baseParams.tipo= 'cuenta_corriente';
                this.load({params:{start:0, limit:this.tam_pag}});
                this.init();
            },

            Atributos:[
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_deposito'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'tipo'
                    },
                    type:'Field',
                    form:true,
                    valorInicial : 'cuenta_corriente'
                },
                {
                    config:{
                        name: 'nro_deposito',
                        fieldLabel: 'No Deposito',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength:70
                    },
                    type:'TextField',
                    filters:{pfiltro:'dep.nro_deposito',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true,
                    bottom_filter : true
                },
                {
                    config:{
                        name: 'fecha',
                        fieldLabel: 'Fecha Deposito',
                        allowBlank: false,
                        width: 250,
                        gwidth: 120,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'dep.fecha',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name:'id_moneda_deposito',
                        origen:'MONEDA',
                        allowBlank:false,
                        fieldLabel:'Moneda Deposito',
                        gdisplayField:'desc_moneda',//mapea al store del grid
                        gwidth:100,
                        renderer:function (value, p, record){return String.format('{0}', record.data['desc_moneda']);}
                    },
                    type:'ComboRec',
                    id_grupo:1,
                    filters:{
                        pfiltro:'mon.codigo',
                        type:'string'
                    },
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'monto_deposito',
                        fieldLabel: 'Monto Total Deposito',
                        allowBlank: false,
                        width: 250,
                        gwidth: 150,
                        maxLength:1179650,
                        renderer:function (value,p,record){
                          return  String.format('<div style="float:right;"><b>{0}<b></div>', Ext.util.Format.number(record.data.monto_deposito,'0.000,00/i'));
                        }
                    },
                    type:'NumberField',
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config: {
                        name: 'id_auxiliar',
                        fieldLabel: 'Cuenta Corriente',
                        allowBlank: false,
                        emptyText: 'Cuenta Corriente...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_contabilidad/control/Auxiliar/listarAuxiliar',
                            id: 'id_auxiliar',
                            root: 'datos',
                            sortInfo: {
                                field: 'codigo_auxiliar',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_auxiliar', 'codigo_auxiliar','nombre_auxiliar'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'auxcta.codigo_auxiliar#auxcta.nombre_auxiliar',corriente:'si', estado_reg:'activo'}
                        }),
                        valueField: 'id_auxiliar',
                        displayField: 'nombre_auxiliar',
                        gdisplayField: 'nombre_auxiliar',
                        hiddenName: 'id_auxiliar',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre_auxiliar}</b></p><p style="color:blue;font-weight:bold;">Codigo:<span style="color:green;">{codigo_auxiliar}</span></p> </div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        width: 250,
                        gwidth: 350,
                        listWidth:450,
                        resizable:true,
                        minChars: 2,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['nombre_auxiliar']);
                        }
                    },
                    type: 'ComboBox',
                    bottom_filter : true,
                    filters: {pfiltro: 'aux.nombre_auxiliar#aux.codigo_auxiliar', type: 'string'},
                    id_grupo: 1,
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
                    filters:{pfiltro:'dep.estado_reg',type:'string'},
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
                        gwidth: 120,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'dep.fecha_reg',type:'date'},
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
                    filters:{pfiltro:'dep.id_usuario_ai',type:'numeric'},
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
                    filters:{pfiltro:'dep.usuario_ai',type:'string'},
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
                    filters:{pfiltro:'dep.fecha_mod',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:false
                }
            ],
            tam_pag:50,
            title:'Depositos',
            ActSave:'../../sis_obingresos/control/Deposito/insertarDeposito',
            ActDel:'../../sis_obingresos/control/Deposito/eliminarDeposito',
            ActList:'../../sis_obingresos/control/Deposito/listarDepositoCc',
            id_store:'id_deposito',
            fields: [
                {name:'id_deposito', type: 'numeric'},
                {name:'nro_deposito', type: 'string'},
                {name:'id_moneda_deposito', type:'numeric'},
                {name:'desc_moneda', type: 'string'},
                {name:'fecha', type: 'date',dateFormat:'Y-m-d'},
                {name:'monto_deposito', type: 'numeric'},
                {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'usr_reg', type: 'string'},
                {name:'usr_mod', type: 'string'},
                {name:'id_auxiliar', type: 'numeric'},
                {name:'codigo_auxiliar', type: 'string'},
                {name:'nombre_auxiliar', type: 'string'},
                {name:'estado_reg',type:'string'},
                {name:'tipo',type:'string'}
            ],
            sortInfo:{
                field: 'id_deposito',
                direction: 'DESC'
            },
            bdel:true,
            bsave:false,
        }
    )
</script>
