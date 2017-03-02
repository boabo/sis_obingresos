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
    Phx.vista.ReporteDepositoBancaInter = Ext.extend(Phx.frmInterfaz, {
        Atributos : [
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'moneda'
                },
                type:'Field',
                form:true
            },
            {
                config:{
                    name:'id_moneda',
                    origen:'MONEDA',
                    allowBlank:false,
                    fieldLabel:'Moneda'
                },
                type:'ComboRec',
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

        title : 'Reporte Deposito',
        ActSave : '../../sis_obingresos/control/Deposito/reporteDepositoBancaInternet',

        topBar : true,
        botones : false,
        labelSubmit : 'Imprimir',
        tooltipSubmit : '<b>Reporte Deposito</b>',

        constructor : function(config) {
            Phx.vista.ReporteDepositoBancaInter.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
        },

        iniciarEventos:function(){
            this.Cmp.id_moneda.on('select', function(c,r,i){
                this.cmp.moneda.setValue(this.Cmp.id_moneda.getRawValue());
            },this);
        },
        tipo : 'reporte',
        clsSubmit : 'bprint'

    })
</script>
