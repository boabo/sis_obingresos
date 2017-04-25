<?Php
/**
 *@package PXP
 *@file   ReporteBoletoResiberVentasWeb.php
 *@author  Gonzalo Sarmiento
 *@date    07-12-2016
 *@description Reportes de deposito
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ReporteBoletoResiberVentasWeb = Ext.extend(Phx.frmInterfaz, {
        Atributos : [
            /*{

                config:{
                    name: 'por',
                    fieldLabel: 'Obtener diferencias por',
                    allowBlank:false,
                    emptyText:'Por...',
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local',
                    store:['boleto_monto']
                },
                type:'ComboBox',
                id_grupo:1,
                form:true
            },*/

            {
                config:{
                    name: 'fecha_ini',
                    fieldLabel: 'Fecha Inicio',
                    allowBlank: true,
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
                    allowBlank: true,
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

        title : 'Reporte Deposito',
        ActSave : '../../sis_obingresos/control/Boleto/reporteBoletoResiberVentasWeb',

        topBar : true,
        botones : false,
        labelSubmit : 'Imprimir',
        tooltipSubmit : '<b>Reporte Deposito</b>',

        constructor : function(config) {
            Phx.vista.ReporteBoletoResiberVentasWeb.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
        },

        iniciarEventos:function(){
            this.cmpFechaIni = this.getComponente('fecha_ini');
            this.cmpFechaFin = this.getComponente('fecha_fin');
        },
        tipo : 'reporte',
        clsSubmit : 'bprint'

    })

</script>

