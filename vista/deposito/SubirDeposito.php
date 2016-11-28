<?php
/**
 *@package pXP
 *@file    ItemEntRec.php
 *@author  RCM
 *@date    07/08/2013
 *@description Reporte Material Entregado/Recibido
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.SubirDeposito = Ext.extend(Phx.frmInterfaz,{
    Atributos: [
        {
            config:{
                name: 'tipo',
                fieldLabel: 'Tipo',
                allowBlank:false,
                emptyText:'Obtener de...',
                triggerAction: 'all',
                lazyRender:true,
                mode: 'local',
                store:['ogone']
            },
            type:'ComboBox',
            id_grupo:0,
            form:true
        },
        {
            config:{
                fieldLabel: "Documento (archivo csv separado por |)",
                gwidth: 130,
                inputType:'file',
                name: 'archivo',
                buttonText: '',
                maxLength:150,
                anchor:'100%'
            },
            type:'Field',
            form:true
        }
    ],
    title : 'Subir Deposito',
    ActSave : '../../sis_obingresos/control/Deposito/subirCSVDeposito',
    topBar : true,
    botones : false,
    labelSubmit : 'Subir',
    tooltipSubmit : '<b>Subir CSV</b>',
    constructor : function(config) {
        Phx.vista.SubirDeposito.superclass.constructor.call(this, config);
        this.init();
    },
    clsSubmit : 'bupload',
    fileUpload:true
})
</script>
