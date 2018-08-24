<?Php
/**
 *@package PXP
 *@file   ConciliacionPortalCC.php
 *@author  JRR
 *@date    09-11-2016
 *@description Reportes de deposito
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ConciliacionPortalCC = Ext.extend(Phx.frmInterfaz, {
        Atributos : [            
            {
                config:{
                    name: 'reporte',
                    fieldLabel: 'Reporte',
                    allowBlank:false,
                    emptyText:'Reporte...',
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local',
                    gwidth: 150,
                    store:['diferencia_anulacion','diferencia_pnr_vs_boletos','boletos_sin_declarar_portal','boletos_sin_declarar_ingresos','boletos_sin_voidear_portal','boletos_sin_voidear_ingresos','diferencia_montos']
                },
                type:'ComboBox',
                id_grupo:1,
                form:true
            },            

            {
                config:{
                    name: 'fecha_ini',
                    fieldLabel: 'Fecha Inicio',
                    allowBlank: false,
                    anchor: '30%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'fecha_ini',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'fecha_fin',
                    fieldLabel: 'Fecha Fin',
                    allowBlank: false,
                    anchor: '30%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'fecha_fin',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
            }],

        title : 'Reporte Conciliacion Portal Cuenta Corriente',
        ActSave : '../../sis_obingresos/control/MovimientoEntidad/conciliacionPortalCC',

        topBar : true,
        botones : false,
        labelSubmit : 'Imprimir',
        tooltipSubmit : '<b>Reporte Conciliacion</b>',

        constructor : function(config) {
            Phx.vista.ConciliacionPortalCC.superclass.constructor.call(this, config);
            this.init();
            
        },
        
        tipo : 'reporte',
        clsSubmit : 'bprint'

    })
</script>
