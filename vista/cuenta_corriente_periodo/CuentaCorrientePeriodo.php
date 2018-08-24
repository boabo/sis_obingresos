<?Php
/**
 *@package PXP
 *@file   FormReporteVentasCorporativas.php
 *@author  MAM
 *@date    09-11-2016
 *@description Reportes de deposito
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.CuentaCorrientePeriodo = Ext.extend(Phx.frmInterfaz, {
        Atributos : [
            {
                config: {
                    name: 'id_agencia',
                    fieldLabel: 'Agencia',
                    allowBlank: true,
                    emptyText: 'Agencia...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_obingresos/control/Agencia/listarAgencia',
                        id: 'id_agencia',
                        root: 'datos',
                        sortInfo: {
                            field: 'nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_agencia', 'nombre', 'codigo_int','tipo_agencia','codigo'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'age.nombre',comision :'si'}
                    }),
                    valueField: 'id_agencia',
                    displayField: 'nombre',
                    gdisplayField: 'id_agencia',
                    hiddenName: 'id_agencia',
                    anchor: "35%",
                    tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p><b><p>Codigo:<font color="green">{codigo_int}</font></b></p></div></tpl>',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    gwidth: 150,
                    listWidth:285,
                    resizable:true,
                    minChars: 2
                },
                type: 'ComboBox',
                grid: false,
                form: true
            },
            {
                config: {
                    name: 'id_periodo_venta',
                    fieldLabel: 'Periodos',
                    allowBlank: true,
                    emptyText: 'Periodo...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_obingresos/control/PeriodoVenta/listarPeriodoVenta',
                        id: 'id_periodo_venta',
                        root: 'datos',
                        sortInfo: {
                            field: 'mes',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_periodo_venta', 'id_gestion', 'mes','fecha_ini','fecha_fin'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'perven.mes'}
                    }),
                    valueField: 'id_periodo_venta',
                    displayField: 'mes',
                    gdisplayField: 'id_periodo_venta',
                    hiddenName: 'id_periodo_venta',
                    anchor: "35%",
                    tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Periodo: {mes}</b></p><b><p>De: <font color="blue">{fecha_ini}</font> Al: <font color="blue">{fecha_fin}</font></b></p></div></tpl>',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    gwidth: 150,
                    listWidth:285,
                    resizable:true,
                    minChars: 2
                },
                type: 'ComboBox',
                grid: false,
                form: true
            }
            ],
        title : 'Cuenta Corriente Peridodo',
        ActSave : '../../sis_obingresos/control/ReporteCuenta/listarReporteCuenta',
        topBar : true,
        botones : false,
        labelSubmit : 'Generar',
        tooltipSubmit : '<b>Cuenta Corriente Peridodo</b>',

        constructor : function(config) {
            Phx.vista.CuentaCorrientePeriodo.superclass.constructor.call(this, config);
            this.init();
        },
        tipo : 'reporte',
        clsSubmit : 'bprint'
    })
</script>
