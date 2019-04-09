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
                config: {
                    name: 'id_gestion',
                    fieldLabel: 'Gestion',
                    allowBlank: true,
                    emptyText: 'Gestion...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_parametros/control/Gestion/listarGestion',
                        id: 'id_gestion',
                        root: 'datos',
                        sortInfo: {
                            field: 'gestion',
                            direction: 'DESC'
                        },
                        totalProperty: 'total',
                        fields: ['id_gestion', 'gestion'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'gestion'}
                    }),
                    valueField: 'id_gestion',
                    triggerAction: 'all',
                    displayField: 'gestion',
                    hiddenName: 'id_gestion',
                    mode: 'remote',
                    pageSize: 50,
                    queryDelay: 500,
                    listWidth:'280',
                    width:200
                },
                type: 'ComboBox',
                grid: false,
                form: true
            },
            {
                config: {
                    name: 'id_periodo',
                    fieldLabel: 'Periodo',
                    allowBlank: true,
                    emptyText: 'Periodo...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_parametros/control/Periodo/listarPeriodo',
                        id: 'id_periodo',
                        root: 'datos',
                        sortInfo: {
                            field: 'periodo',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_periodo','periodo','id_gestion','literal'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'gestion'}
                    }),
                    valueField: 'id_periodo',
                    triggerAction: 'all',
                    displayField: 'literal',
                    hiddenName: 'id_periodo',
                    mode: 'remote',
                    pageSize: 50,
                    disabled: true,
                    queryDelay: 500,
                    listWidth:'280',
                    width:200,
                    /*Resetea el TriggerAction Para reload comboBox*/
                    listeners: {
                      beforequery: function(qe){
                        delete qe.combo.lastQuery;
                    }
                }
                /******************************************************/
                },
                type: 'ComboBox',
                grid: false,
                form: true
            },
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
                  //console.log("LLEGA AQUI PERIODO",this.Cmp.id_periodo.value);
              },this);

              this.Cmp.id_gestion.on('select', function(c,r,i){
                  this.Cmp.gestion.setValue(this.Cmp.id_gestion.getRawValue());
                  this.Cmp.id_periodo.enable();
                  this.Cmp.id_periodo.reset();
                  //this.Cmp.periodo.reset();
                  this.Cmp.id_periodo.store.baseParams.id_gestion = r.data.id_gestion;
                //  console.log("LLEGA AQUI GESTION ",this.Cmp.periodo);
              },this);
          },
        tipo : 'reporte',
        clsSubmit : 'bprint'

    })
</script>
