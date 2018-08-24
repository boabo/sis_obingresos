<?Php
/**
 *@package PXP
 *@file   FormReporteSaldoVigente.php
 *@author  MAM
 *@date    09-11-2016
 *@description Reportes de deposito
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.FormReporteSaldoVigente = Ext.extend(Phx.frmInterfaz, {
        Atributos : [

            {
                config:{
                    name: 'tipo_agencia',
                    fieldLabel: 'Tipo Agencia',
                    allowBlank:false,
                    emptyText:'Tipo...',
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local',
                    gwidth: 150,
                    store:['corporativa','noiata','todas']
                },
                type:'ComboBox',
                id_grupo:1,
                form:true
            },
            {
                config:{
                    name: 'forma_pago',
                    fieldLabel: 'Forma Pago',
                    allowBlank:false,
                    emptyText:'Forma...',
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local',
                    gwidth: 150,
                    store:['prepago','postpago','todas']
                },
                type:'ComboBox',
                id_grupo:1,
                form:true
            },
            {
                config:{
                    name: 'id_lugar',
                    fieldLabel: 'Lugar',
                    allowBlank: true,
                    emptyText:'Lugar...',
                    store:new Ext.data.JsonStore(
                        {
                            url: '../../sis_parametros/control/Lugar/listarLugar',
                            id: 'id_lugar',
                            root: 'datos',
                            sortInfo:{
                                field: 'nombre',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_lugar','id_lugar_fk','codigo','nombre','tipo','sw_municipio','sw_impuesto','codigo_largo'],
                            // turn on remote sorting
                            remoteSort: true,
                            baseParams:{par_filtro:'lug.nombre',es_regional:'si'}
                        }),
                    valueField: 'id_lugar',
                    displayField: 'nombre',
                    hiddenName: 'id_lugar',
                    triggerAction: 'all',
                    lazyRender:true,
                    mode:'remote',
                    pageSize:50,
                    queryDelay:500,
                    anchor:"35%",
                    minChars:2,
                    enableMultiSelect:true
                },
                type:'AwesomeCombo',
                form:true
            }
        ],
        title : 'Reporte Saldo Vigente',
        ActSave : '../../sis_obingresos/control/DetalleBoletosWeb/conciliacionBancaInter',

        topBar : true,
        botones : false,
        labelSubmit : 'Generar',
        tooltipSubmit : '<b>Reporte Ventas Agencias</b>',

        constructor : function(config) {
            Phx.vista.FormReporteSaldoVigente.superclass.constructor.call(this, config);
            this.init();
        },
        onSubmit: function(){
            if (this.form.getForm().isValid()) {
                var data={};
                data.id_lugar=this.getComponente('id_lugar').getValue();
                data.lugar=this.getComponente('id_lugar').getRawValue();
                data.forma_pago=this.getComponente('forma_pago').getValue();
                data.tipo_agencia=this.getComponente('tipo_agencia').getValue();

                Phx.CP.loadWindows('../../../sis_obingresos/vista/reporte_saldo_vigente/GridReporteSaldoVigente.php',
                    'Reporte Saldo Vigente',
                    {
                        width : '83%',
                        height : '100%'
                    }, data	, this.idContenedor, 'GridReporteSaldoVigente')
            }
        },


        tipo : 'reporte',
        clsSubmit : 'bprint'

    })
</script>
