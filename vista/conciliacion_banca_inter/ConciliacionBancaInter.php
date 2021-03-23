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
    Phx.vista.ConciliacionBancaInter = Ext.extend(Phx.frmInterfaz, {
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
            // {
            //     config:{
            //         name: 'banco',
            //         fieldLabel: 'Banco',
            //         allowBlank:false,
            //         emptyText:'Banco...',
            //         typeAhead: true,
            //         triggerAction: 'all',
            //         lazyRender:true,
            //         mode: 'local',
            //         gwidth: 150,
            //         store:['BIS','BUN','BNB','BME','BPM','TMY','BEC','BCR','BCO','ECF','QRB','QRK','BCK']
            //     },
            //     type:'ComboBox',
            //     id_grupo:1,
            //     form:true
            // },
            {
                config: {
                    name: 'banco',
                    fieldLabel: 'Banco',
                    allowBlank: true,
                    emptyText: 'Banco...',
                    store : new Ext.data.JsonStore({
   									 url : '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
   									 id : 'id_catalogo',
   									 root : 'datos',
   									 sortInfo : {
   										 field : 'codigo',
   										 direction : 'ASC'
   									 },
   									 totalProperty : 'total',
   									 fields: ['codigo','descripcion'],
   									 remoteSort : true,
   									 baseParams:{
   										cod_subsistema:'OBINGRESOS',
   										catalogo_tipo:'bancos_skybiz'
   									},
   								 }),
                    valueField: 'codigo',
                    gdisplayField : 'descripcion',
                    displayField: 'descripcion',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    width:250,
                    queryDelay: 1000,
                    minChars: 2,
                    resizable:true
                },
                type: 'ComboBox',
                id_grupo: 0,
                form: true
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
                    width:250,
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
                    width:250,
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
        ActSave : '../../sis_obingresos/control/DetalleBoletosWeb/conciliacionBancaInter',

        topBar : true,
        botones : false,
        labelSubmit : 'Imprimir',
        tooltipSubmit : '<b>Reporte Deposito</b>',

        constructor : function(config) {
            Phx.vista.ConciliacionBancaInter.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
        },

        iniciarEventos:function(){
            this.Cmp.id_moneda.on('select', function(c,r,i){
                this.Cmp.moneda.setValue(this.Cmp.id_moneda.getRawValue());
            },this);
        },
        tipo : 'reporte',
        clsSubmit : 'bprint'

    })
</script>
