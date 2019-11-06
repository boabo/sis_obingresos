<?php
/**
 *@package pXP
 *@file gen-MedioPagoPw.php
 *@author  (admin)
 *@date 04-06-2019 22:47:38
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.MedioPagoPw=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.maestro=config.maestro;
                //llama al constructor de la clase padre
                Phx.vista.MedioPagoPw.superclass.constructor.call(this,config);
                this.init();
                this.load({params:{start:0, limit:this.tam_pag}})
            },

            Atributos:[
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_medio_pago_pw'
                    },
                    type:'Field',
                    form:true
                },

                {
                    config:{
                        name: 'medio_pago_id',
                        fieldLabel: 'Medio Pago ID',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:4
                    },
                    type:'NumberField',
                    filters:{pfiltro:'mppw.medio_pago_id',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'forma_pago_id',
                        fieldLabel: 'Forma Pago ID',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:4
                    },
                    type:'NumberField',
                    filters:{pfiltro:'mppw.forma_pago_id',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'name',
                        fieldLabel: 'Nombre',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:100
                    },
                    type:'TextField',
                    filters:{pfiltro:'mppw.name',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'mop_code',
                        fieldLabel: 'Codigo MOP',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:20
                    },
                    type:'TextField',
                    filters:{pfiltro:'mppw.mop_code',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'code',
                        fieldLabel: 'Codigo',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:15
                    },
                    type:'TextField',
                    filters:{pfiltro:'mppw.code',type:'string'},
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
                    filters:{pfiltro:'mppw.estado_reg',type:'string'},
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
                    filters:{pfiltro:'mppw.fecha_reg',type:'date'},
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
                    filters:{pfiltro:'mppw.id_usuario_ai',type:'numeric'},
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
                    filters:{pfiltro:'mppw.usuario_ai',type:'string'},
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
                    filters:{pfiltro:'mppw.fecha_mod',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:false
                }
            ],
            tam_pag:50,
            title:'Medio Pago P W',
            ActSave:'../../sis_obingresos/control/MedioPagoPw/insertarMedioPagoPw',
            ActDel:'../../sis_obingresos/control/MedioPagoPw/eliminarMedioPagoPw',
            ActList:'../../sis_obingresos/control/MedioPagoPw/listarMedioPagoPw',
            id_store:'id_medio_pago_pw',
            fields: [
                {name:'id_medio_pago_pw', type: 'numeric'},
                {name:'estado_reg', type: 'string'},
                {name:'medio_pago_id', type: 'numeric'},
                {name:'forma_pago_id', type: 'numeric'},
                {name:'name', type: 'string'},
                {name:'mop_code', type: 'string'},
                {name:'code', type: 'string'},
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
                field: 'id_medio_pago_pw',
                direction: 'ASC'
            },
            bdel:true,
            bsave:true
        }
    )
</script>
