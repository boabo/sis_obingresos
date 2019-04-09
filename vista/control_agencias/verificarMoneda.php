<?php
/**
 *@package pXP
 *@file gen-DetalleCredito.php
 *@author  (miguel.mamani)
 *@date 18-07-2018 16:53:28
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.verificarMoneda=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.agencia = config.agencia;

                console.log('LLEGA AGENCIA AQUI',this.agencia);
                var agencia  = this.agencia;

                Phx.vista.verificarMoneda.superclass.constructor.call(this,config);
                this.init(); 
                  this.store.baseParams={
                    id_agencia:agencia

                  };
                  this.load({params: {start: 0, limit: 50}});

            },

            Atributos:[


                {
                    config:{
                        name: 'nombre',
                        fieldLabel: 'Agencia',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 500
                    },
                    type:'TextField',
                    filters:{pfiltro:'ag.nombre',type:'string'},
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
                        gwidth: 200
                    },
                    type:'TextField',
                    filters:{pfiltro:'mone.moneda',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'codigo',
                        fieldLabel: 'Tipo de Moneda',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 90
                    },
                    type:'TextField',
                    filters:{pfiltro:'mone.codigo',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },


            ],
            tam_pag:50,
            title:'Deposito',
            ActList:'../../sis_obingresos/control/ControlAgencias/listarverificarMoneda',
            id_store:'id_moneda',
            fields: [


              {name:'id_moneda', type: 'numeric'},
              {name:'nombre', type: 'string'},
              {name:'moneda', type: 'string'},
              {name:'codigo', type: 'string'},

            ],
            sortInfo:{
                field: 'id_moneda',
                direction: 'DESC'
            },
            bdel:false,
            bsave:false,
            bedit: false,
            bnew : false

        }
    )
</script>
