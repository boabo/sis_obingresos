<?php
/**
 *@package pXP
 *@file ConsultaArchivoAcm.php
 *@author  (RZABALA)
 *@date 24-10-2018 14:09:45
 *@description Consulta Archivo Acm permite que se realicen consultas over comision sin permitir acciones
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ConsultaArchivoAcm=Ext.extend(Phx.gridInterfaz,{

        constructor:function(config) {
            this.maestro = config.maestro;
            //llama al constructor de la clase padre
            Phx.vista.ConsultaArchivoAcm.superclass.constructor.call(this, config);


            this.init();


            this.addButton('btnReporteArchivoAcm',
                {
                    text: 'Reporte',
                    iconCls: 'bexcel',
                    disabled: true,
                    handler: this.onButtonReporte,
                    tooltip: '<b>Generar Reporte</b><br/>Generar Reporte del Detalle de archivos ACM.'
                }
            );


            this.load({params: {start: 0, limit: this.tam_pag}})


            //this.iniciarEventos();
            //this.cmbGestion.on('select',this.capturarEventos,this);
        },

        iniciarEventos: function(){
            /*this.Cmb.id_archivo_acm.on('focus', function(){

            });*/
        },
        preparaMenu: function () {
            var rec = this.sm.getSelected();
            var tb = this.tbar;
            //this.getBoton('btnBoleto').enable();
            if(rec !== '') {
                if(rec.data.estado == 'generado'){
                    this.getBoton('btnReporteArchivoAcm').enable();
                }
                if(rec.data.estado == 'validado'){
                    this.getBoton('btnReporteArchivoAcm').enable();
                }
                if(rec.data.estado == 'finalizado'){
                    this.getBoton('btnReporteArchivoAcm').enable();
                }
                Phx.vista.ConsultaArchivoAcm.superclass.preparaMenu.call(this);
            }
        },
        liberaMenu : function(){
            var d = this.sm.getSelected.data;
            Phx.vista.ConsultaArchivoAcm.superclass.liberaMenu.call(this);
        },

        /*apturarEventos: function (){
            this.store.baseParams.id_archivo_acm=this.cmbGestion.getValue();
            this.load({params:{start:0, limit:this.tam_pag}});
        },
*/

        onButtonReporte: function() {
            Phx.CP.loadingShow();
            var d = this.sm.getSelected().data;
            Ext.Ajax.request({
                url:'../../sis_obingresos/control/ArchivoAcm/reporteArchivoACM',
                params:{id_archivo_acm:d.id_archivo_acm,
                    fecha_ini: d.fecha_ini.dateFormat('d/m/Y'),
                    fecha_fin: d.fecha_fin.dateFormat('d/m/Y')},
                success:this.successExport,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
            console.log('EL DATO ES:',d.id_archivo_acm);
        },

        Atributos:[
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_archivo_acm'
                },
                type:'Field',
                form:true
            },
            {
                config:{
                    name: 'estado_reg',
                    fieldLabel: 'Estado',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10
                },
                type:'TextField',
                filters:{pfiltro:'taa.estado_reg',type:'string'},
                id_grupo:1,
                grid:false,
                form:false
            },
            {
                config:{
                    name: 'fecha_ini',
                    fieldLabel: 'Fecha Inicio',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'taa.fecha_ini',type:'date'},
                id_grupo:1,
                grid:true,
                form:true,
                bottom_filter : true
            },
            {
                config:{
                    name: 'fecha_fin',
                    fieldLabel: 'Fecha Fin',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'taa.fecha_fin',type:'date'},
                id_grupo:1,
                grid:true,
                form:true,
                bottom_filter : true
            },
            {
                config:{
                    name: 'nombre',
                    fieldLabel: 'Nombre',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 300,
                    maxLength:500
                },
                type:'TextField',
                filters:{pfiltro:'taa.nombre',type:'string'},
                id_grupo:1,
                grid:true,
                form:true,
                bottom_filter : true
            },
            {
                config:{
                    name: 'usuario_ai',
                    fieldLabel: 'Funcionaro AI',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:300
                },
                type:'TextField',
                filters:{pfiltro:'taa.usuario_ai',type:'string'},
                id_grupo:1,
                grid:false,
                form:false
            },
            {
                config:{
                    name: 'fecha_reg',
                    fieldLabel: 'Fecha creación',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                filters:{pfiltro:'taa.fecha_reg',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'usr_reg',
                    fieldLabel: 'Creado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4
                },
                type:'Field',
                filters:{pfiltro:'usu1.cuenta',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'estado',
                    fieldLabel: 'Proceso de ACM',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength:3,
                    renderer: function (value, p, record) {
                        if (record.data['estado'] == 'borrador') {
                            return String.format('<div title="borrador"><b><font color="orange">{0}</font></b></div>', value);

                        } else if (record.data['estado'] == 'cargado') {
                            return String.format('<div title="cargado"><b><font color="blue">{0}</font></b></div>', value);
                        } else if (record.data['estado'] == 'generado') {
                            return String.format('<div title="generado"><b><font color="purple">{0}</font></b></div>', value);
                        } else if (record.data['estado'] == 'validado'){
                            return String.format('<div title="validado"><b><font color="green">{0}</font></b></div>', value);
                        }else if (record.data['estado'] == 'finalizado'){
                            return String.format('<div title="validado"><b><font color="red">{0}</font></b></div>', value);
                        }
                    }
                },
                type: 'TextField',
                filters: { pfiltro:'taa.estado',type:'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config:{
                    name: 'id_usuario_ai',
                    fieldLabel: 'Creado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4
                },
                type:'Field',
                filters:{pfiltro:'taa.id_usuario_ai',type:'numeric'},
                id_grupo:1,
                grid:false,
                form:false
            },
            {
                config:{
                    name: 'usr_mod',
                    fieldLabel: 'Modificado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4
                },
                type:'Field',
                filters:{pfiltro:'usu2.cuenta',type:'string'},
                id_grupo:1,
                grid:false,
                form:false
            },
            {
                config:{
                    name: 'fecha_mod',
                    fieldLabel: 'Fecha Modif.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                filters:{pfiltro:'taa.fecha_mod',type:'date'},
                id_grupo:1,
                grid:false,
                form:false
            }
        ],
        tam_pag:50,
        title:'Consulta Archivo ACM',
        ActSave:'../../sis_obingresos/control/ArchivoAcm/insertarArchivoAcm',
        ActDel:'../../sis_obingresos/control/ArchivoAcm/eliminarArchivoAcm',
        ActList:'../../sis_obingresos/control/ArchivoAcm/listarArchivoAcm',
        id_store:'id_archivo_acm',
        fields: [
            {name:'id_archivo_acm', type: 'numeric'},
            {name:'estado_reg', type: 'string'},
            {name:'fecha_fin', type: 'date',dateFormat:'Y-m-d'},
            {name:'nombre', type: 'string'},
            {name:'fecha_ini', type: 'date',dateFormat:'Y-m-d'},
            {name:'usuario_ai', type: 'string'},
            {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'id_usuario_reg', type: 'numeric'},
            {name:'id_usuario_ai', type: 'numeric'},
            {name:'id_usuario_mod', type: 'numeric'},
            {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'estado', type: 'string'},
            {name:'usr_reg', type: 'string'},
            {name:'usr_mod', type: 'string'},
            //	{name:'ultimo_numero', type: 'numeric'},

        ],

        sortInfo:{
            field: 'id_archivo_acm',
            direction: 'DESC'
        },


        bdel:false,
        bsave:false,
        btest:false,
        bnew: false,
        bedit:false,

        tabsouth:[
            {
                url:'../../../sis_obingresos/vista/archivo_acm_det/ArchivoAcmDet.php',
                title:'Detalle Archivos ACM',
                //width:'40%',
                height: '50%',
                cls:'ArchivoAcmDet'
            }
        ]

    })
</script>
