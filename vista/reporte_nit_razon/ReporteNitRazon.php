<?Php
/**
 *@package pXP
 *@file   PagosSinFacturasAsociadas.php
 *@author  MAM
 *@date    09-11-2016
 *@description Archivo con la interfaz para generaci�n de reporte
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ReporteNitRazon = Ext.extend(Phx.frmInterfaz, {
        Atributos : [
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

        title : 'Reporte Nit Razon',
        ActSave : '../../sis_obingresos/control/DetalleBoletosWeb/ReporteNitRazon',

        topBar : true,
        botones : false,
        labelSubmit : 'Imprimir',
        tooltipSubmit : '<b>Reporte Nit Razon</b>',

        constructor : function(config) {
            Phx.vista.ReporteNitRazon.superclass.constructor.call(this, config);
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
