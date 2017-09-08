<?Php
/**
 *@package PXP
 *@file   ReporteDeposito.php
 *@author  MAM
 *@date    09-11-2016
 *@description Reportes de deposito
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ConciliacionBancaInterRes = Ext.extend(Phx.frmInterfaz, {
        Atributos : [
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'periodo'
                },
                type:'Field',
                form:true
            },
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'gestion'
                },
                type:'Field',
                form:true
            },
            {
                config:{
                    name : 'id_gestion',
                    origen : 'GESTION',
                    fieldLabel : 'Gestion',
                    allowBlank : false,
                    gdisplayField : 'gestion',//mapea al store del grid
                    gwidth : 100,
                    renderer : function (value, p, record){return String.format('{0}', record.data['gestion']);}
                },
                type : 'ComboRec',
                id_grupo : 0,
                form : true
            },
            {
                config:{
                    name : 'id_periodo',
                    origen : 'PERIODO',
                    fieldLabel : 'Periodo',
                    allowBlank : true,
                    gdisplayField : 'periodo',//mapea al store del grid
                    gwidth : 100,
                    renderer : function (value, p, record){return String.format('{0}', record.data['periodo']);}
                },
                type : 'ComboRec',
                id_grupo : 0,
                form : true
            }
            ],

        title : 'Reporte Deposito',
        ActSave : '../../sis_obingresos/control/DetalleBoletosWeb/conciliacionBancaInterRes',

        topBar : true,
        botones : false,
        labelSubmit : 'Imprimir',
        tooltipSubmit : '<b>Reporte Deposito</b>',

        constructor : function(config) {
            Phx.vista.ConciliacionBancaInterRes.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
        },

        iniciarEventos:function(){
            this.Cmp.id_periodo.on('select', function(c,r,i){
                this.Cmp.periodo.setValue(this.Cmp.id_periodo.getRawValue());
            },this);

            this.Cmp.id_gestion.on('select', function(c,r,i){
                this.Cmp.gestion.setValue(this.Cmp.id_gestion.getRawValue());
                this.Cmp.id_periodo.reset();
                this.Cmp.id_periodo.store.baseParams.id_gestion = r.data.id_gestion;
            },this);
        },
        tipo : 'reporte',
        clsSubmit : 'bprint'

    })
</script>
