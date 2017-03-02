<?php
/**
 *@package pXP
 *@file gen-Deposito.php
 *@author  (jrivera)
 *@date 06-01-2016 22:42:28
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.DepositoVentaWeb=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.maestro=config.maestro;
                //llama al constructor de la clase padre
                Phx.vista.DepositoVentaWeb.superclass.constructor.call(this,config);
                this.init();
                this.store.baseParams.tipo = 'banca';
                this.load({params:{start:0, limit:this.tam_pag}});
                this.iniciarEventos();

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
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'tipo'
                    },
                    type:'Field',
                    form:true,
                    valorInicial : 'banca'
                },

                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_agencia'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'saldo'
                    },
                    type:'Field',
                    form:true
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
                        anchor: '80%',
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
                        name: 'fecha_venta',
                        fieldLabel: 'Fecha Venta (Arc. Excel)',
                        allowBlank: false,
                        anchor: '80%',
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
                        fieldLabel: 'Monto Venta',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength:1179650
                    },
                    type:'NumberField',
                    filters:{pfiltro:'dep.monto_deposito',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },

                {
                    config:{
                        name: 'monto_total',
                        fieldLabel: 'Monto Total Deposito',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength:1179650
                    },
                    type:'NumberField',
                    filters:{pfiltro:'dep.monto_total',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },

                {
                    config:{
                        name: 'agt',
                        fieldLabel: 'Banco',
                        allowBlank:false,
                        emptyText:'Banco...',
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender:true,
                        mode: 'local',
                        gwidth: 150,
                        store:['BIS','BUN','BNB','BME','TMY','BEC','BCR','BCO','ECF']
                    },
                    type:'ComboBox',
                    filters:{
                        type: 'list',
                        options: ['BIS','BUN','BNB','BME','TMY','BEC','BCR','BCO','ECF'],
                    },
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
                        gwidth: 100,
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
            ActList:'../../sis_obingresos/control/Deposito/listarDeposito',
            id_store:'id_deposito',
            fields: [
                {name:'id_deposito', type: 'numeric'},
                {name:'estado_reg', type: 'string'},
                {name:'nro_deposito', type: 'string'},
                {name:'desc_moneda', type: 'string'},
                {name:'monto_deposito', type: 'numeric'},
                {name:'id_moneda_deposito', type: 'numeric'},
                {name:'agt', type: 'varchar'},

                {name:'fecha', type: 'date',dateFormat:'Y-m-d'},
                {name:'fecha_venta', type: 'date',dateFormat:'Y-m-d'},
                {name:'monto_total', type: 'numeric'},
                {name:'saldo', type: 'numeric'},
                {name:'id_usuario_reg', type: 'numeric'},
                {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'id_usuario_ai', type: 'numeric'},
                {name:'usuario_ai', type: 'string'},
                {name:'id_usuario_mod', type: 'numeric'},
                {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'usr_reg', type: 'string'},
                {name:'usr_mod', type: 'string'},
                {name:'tipo', type: 'string'},

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

