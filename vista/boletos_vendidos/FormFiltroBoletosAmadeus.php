<?php
/**
 *@package pXP
 *@file    FormFiltroBoletosAmadeus.php
 *@author  Gonzalo Sarmiento Sejas
 *@date    23-09-2017
 *@description permite filtrar boletos por Oficina y por fecha
 */
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
    Phx.vista.FormFiltroBoletosAmadeus=Ext.extend(Phx.frmInterfaz,{
        constructor:function(config)
        {
            this.panelResumen = new Ext.Panel({html:''});
            this.Grupos = [{

                xtype: 'fieldset',
                border: false,
                autoScroll: true,
                layout: 'form',
                items: [],
                id_grupo: 0

            },
                this.panelResumen
            ];

            Phx.vista.FormFiltroBoletosAmadeus.superclass.constructor.call(this,config);
            this.init();
        },

        Atributos:[
            {
                config:{
                    name: 'fecha',
                    fieldLabel: 'Fecha',
                    allowBlank: false,
                    format: 'Ymd',
                    width: 150
                },
                type: 'DateField',
                id_grupo: 0,
                form: true
            },
            {
                config:{
                    labelSeparator: '',
                    name: 'reporte',
                    inputType:'hidden',

                },
                valorInicial:'reporte',
                type:'Field',
                form:true
            },
        ],
        labelSubmit: '<i class="fa fa-check"></i> Aplicar Filtro',
        east: {
            url: '../../../sis_obingresos/vista/boletos_vendidos/BoletosVendidosAmadeus.php',
            title: 'Boletos Amadeus',
            width: '70%',
            cls: 'BoletosVendidosAmadeus',
            params: {'filtro':'si'}
        },

        title: 'Filtros Para el Reporte de Boletos Amadeus',
        // Funcion guardar del formulario
        onSubmit: function(o) {
            var me = this;
            if (me.form.getForm().isValid()) {

                var parametros = me.getValForm()

                console.log('parametros ....', parametros);

                this.onEnablePanel(this.idContenedor + '-east', parametros)
            }
        }
    })
</script>