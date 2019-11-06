<?php
/**
 *@package pXP
 *@file gen-FormaPagoPw.php
 *@author  (admin)
 *@date 04-06-2019 21:58:00
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.FormaPagoPw=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.maestro=config.maestro;
                //llama al constructor de la clase padre
                Phx.vista.FormaPagoPw.superclass.constructor.call(this,config);
                this.init();
                this.load({params:{start:0, limit:this.tam_pag}})
            },

            Atributos:[
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_forma_pago_pw'
                    },
                    type:'Field',
                    form:true
                },

                {
                    config:{
                        name: 'name',
                        fieldLabel: 'Nombre',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 250,
                        maxLength:100
                    },
                    type:'TextField',
                    filters:{pfiltro:'fppw.name',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'country_code',
                        fieldLabel: 'Codigo Ciudad',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:5
                    },
                    type:'TextField',
                    filters:{pfiltro:'fppw.country_code',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'erp_code',
                        fieldLabel: 'Codigo ERP',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength:50
                    },
                    type:'TextField',
                    filters:{pfiltro:'fppw.erp_code',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'fop_code',
                        fieldLabel: 'Codigo FP',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:15
                    },
                    type:'TextField',
                    filters:{pfiltro:'fppw.fop_code',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'manage_account',
                        fieldLabel: 'Monto Admin.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:1179650
                    },
                    type:'NumberField',
                    filters:{pfiltro:'fppw.manage_account',type:'numeric'},
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
                    filters:{pfiltro:'fppw.estado_reg',type:'string'},
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
                    filters:{pfiltro:'fppw.fecha_reg',type:'date'},
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
                    filters:{pfiltro:'fppw.id_usuario_ai',type:'numeric'},
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
                    filters:{pfiltro:'fppw.usuario_ai',type:'string'},
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
                    filters:{pfiltro:'fppw.fecha_mod',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:false
                }
            ],
            tam_pag:50,
            title:'Forma Pago PW',
            ActSave:'../../sis_obingresos/control/FormaPagoPw/insertarFormaPagoPw',
            ActDel:'../../sis_obingresos/control/FormaPagoPw/eliminarFormaPagoPw',
            ActList:'../../sis_obingresos/control/FormaPagoPw/listarFormaPagoPw',
            id_store:'id_forma_pago_pw',
            fields: [
                {name:'id_forma_pago_pw', type: 'numeric'},
                {name:'estado_reg', type: 'string'},
                {name:'name', type: 'string'},
                {name:'country_code', type: 'string'},
                {name:'erp_code', type: 'string'},
                {name:'fop_code', type: 'string'},
                {name:'manage_account', type: 'numeric'},
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
                field: 'id_forma_pago_pw',
                direction: 'ASC'
            },
            bdel:true,
            bsave:true
        }
    )
</script>

		
