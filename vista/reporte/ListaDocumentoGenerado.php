<?php
/**
 *@package pXP
 *@file ListaDocumentoGenerado.php
 *@author franklin.espinoza
 *@date 25-01-2021
 *@description  Vista para modificar Nit, Razon Social
 */

header("content-type: text/javascript; charset=UTF-8");
?>

<style type="text/css" rel="stylesheet">
    .x-selectable,
    .x-selectable * {
        -moz-user-select: text !important;
        -khtml-user-select: text !important;
        -webkit-user-select: text !important;
    }

    .x-grid-row td,
    .x-grid-summary-row td,
    .x-grid-cell-text,
    .x-grid-hd-text,
    .x-grid-hd,
    .x-grid-row,

    .x-grid-row,
    .x-grid-cell,
    .x-unselectable
    {
        -moz-user-select: text !important;
        -khtml-user-select: text !important;
        -webkit-user-select: text !important;
    }


    .new {
        -moz-user-select: text !important;
        -khtml-user-select: text !important;
        -webkit-user-select: text !important;

        background-color: #bdffb2;
        color: #090;
    }

    .old{
        background-color: #EAA8A8;//#ffe2e2
    color: #900;
    }

</style>

<script>
    Phx.vista.ListaDocumentoGenerado=Ext.extend(Phx.gridInterfaz,{
        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                if (record.data.estado_reg == 'NEW') {
                    return "new";
                }else {
                    return "x-selectable";
                }
            }
        },
        constructor: function(config) {

            Phx.vista.ListaDocumentoGenerado.superclass.constructor.call(this,config);

            this.maestro = config;
            this.store.baseParams.formato = 'xls';

            //this.grid.addListener('cellclick', this.mostrarDocumentoGenerado,this);
            this.grid.on('cellclick', this.mostrarDocumentoGenerado,this);
            this.init();
            //this.load({params: {start: 0, limit: 50}});
        },

        bactGroups:[0,1],
        bexcelGroups:[0,1],

        gruposBarraTareas: [
            {name: 'xls', title: '<h1 style="text-align: center; color: #00B167;"><i class="fa fa-file-o fa-2x" aria-hidden="true"></i> EXCEL</h1>', grupo: 1, height: 1}
        ],

        actualizarSegunTab: function(name, indice){
            this.store.baseParams.formato = name;
            this.load({params: {start: 0, limit: 50}});
        },

        mostrarDocumentoGenerado: function (grid, rowIndex, columnIndex, e){

            var fieldName = grid.getColumnModel().getDataIndex(columnIndex);

            if (fieldName == 'ver_documento') {

                var data = this.getSelectedData();
                this.name_file = data.url_documento;


                this.formPanel = new Ext.form.FormPanel({

                    baseCls: 'x-plain',
                    autoHeight: true,
                    autoWidth: true,
                    layout: "form",
                    autoDestroy: true,
                    border: false,
                    items: [

                        {
                            region: 'south',
                            xtype: 'box',
                            autoEl: {
                                tag: 'embed',
                                id: '3',
                                name: '3',
                                style: 'height: 100%; width: 100%',
                                type: 'application/pdf',
                                src: this.name_file
                            }
                        }

                    ]
                });

                this.xlsDialog = new Ext.Window({
                    title: "Visor XLS",
                    layout: "fit",
                    width: 800,
                    height: 500,
                    minWidth: 1000,
                    minHeight: 500,
                    closeAction: "hide",
                    closable: true,
                    resizable: false,

                    maximizable: true,
                    autoDestroy: true,

                    plain: true,
                    modal: true,
                    items: this.formPanel
                });

                this.xlsDialog.show();
            }

        },

        Atributos:[
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_documento_generado'
                },
                type:'Field',
                form:true
            },


            {
                config:{
                    name: 'ver_documento',
                    fieldLabel: 'Ver. Documento',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    scope: this,
                    renderer:function (value, p, record, rowIndex, colIndex){ console.log('record', record);
                        if (record.data.formato == 'pdf') {
                            return String.format('{0}', "<div style='text-align:center; color: #00B167;'><img border='0' style='-webkit-user-select:auto;cursor:pointer; color: #00B167;' title='Ver Documento' src = '../../../lib/imagenes/icono_awesome/awe_pdf.png' align='center' width='30' height='30'/></div>");
                        }else{
                            return String.format('{0}', "<div style='text-align:center; color: #00B167;'><img border='0' style='-webkit-user-select:auto;cursor:pointer; color: #00B167;' title='Ver Documento' src = '../../../lib/imagenes/icono_awesome/awe_excel.png' align='center' width='30' height='30'/></div>");
                        }
                    },
                },
                type:'Checkbox',
                filters:{pfiltro:'dwf.chequeado',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },

            {
                config:{
                    name: 'fecha_ini',
                    fieldLabel: 'Fecha Inicio',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 70,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){
                        return value?value.dateFormat('d/m/Y'):'';
                        //return String.format('<div style="color: #586E7E; font-weight: bold;">{0}</div>', value.dateFormat('d/m/Y'));
                        //return <div style="color: #586E7E; font-weight: bold;">value</div>?value.dateFormat('d/m/Y H:i:s'):''
                    }
                },
                type:'DateField',
                filters:{pfiltro:'dwf.fecha_mod',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            },

            {
                config:{
                    name: 'fecha_fin',
                    fieldLabel: 'Fecha Fin',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 70,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){
                        return value?value.dateFormat('d/m/Y'):'';
                        //return String.format('<div style="color: #586E7E; font-weight: bold;">{0}</div>', value.dateFormat('d/m/Y'));
                        //return <div style="color: #586E7E; font-weight: bold;">value</div>?value.dateFormat('d/m/Y H:i:s'):''
                    }
                },
                type:'DateField',
                filters:{pfiltro:'dwf.fecha_mod',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            },

            {
                config:{
                    name: 'nombre_documento',
                    fieldLabel: 'Nombre Documento.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 368,
                    maxLength:250,
                    renderer:function(value,p,record){
                        return String.format('<div style="color: #586E7E; font-weight: bold; -moz-user-select: text !important;\n' +
                            '        -khtml-user-select: text !important;\n' +
                            '        -webkit-user-select: text !important;">{0} </div>', value);
                    },

                },
                type:'TextField',
                filters:{pfiltro:'td.nombre',type:'string'},
                id_grupo:1,
                grid:true,
                form:false,
                bottom_filter : true
            },

            {
                config:{
                    name: 'size',
                    fieldLabel: 'Tamaño (KB, MG, GB).',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 130,
                    maxLength:250,
                    renderer:function(value,p,record){
                        return String.format('<div style="color: #586E7E; font-weight: bold; -moz-user-select: text !important;\n' +
                            '        -khtml-user-select: text !important;\n' +
                            '        -webkit-user-select: text !important;">{0} </div>', value);
                    },

                },
                type:'TextField',
                filters:{pfiltro:'td.nombre',type:'string'},
                id_grupo:1,
                grid:true,
                form:false,
                bottom_filter : false
            },

            {
                config:{
                    name: 'usr_reg',
                    fieldLabel: 'Generado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 200,
                    maxLength:4,
                    renderer:function(value,p,record){
                        return String.format('<div style="color: #586E7E; font-weight: bold; -moz-user-select: text !important;\n' +
                            '        -khtml-user-select: text !important;\n' +
                            '        -webkit-user-select: text !important;">{0} </div>', value);
                    }
                },
                type:'NumberField',
                filters:{pfiltro:'usu1.cuenta',type:'string'},
                id_grupo:1,
                grid:true,
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
                filters:{pfiltro:'dwf.fecha_mod',type:'date'},
                id_grupo:1,
                grid:true,
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
                type:'NumberField',
                filters:{pfiltro:'usu2.cuenta',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'fecha_generacion',
                    fieldLabel: 'Fecha Generado',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 120,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){
                        return String.format('<div style="color: #586E7E; font-weight: bold; -moz-user-select: text !important; -khtml-user-select: text !important; -webkit-user-select: text !important;">{0}</div>', value.dateFormat('d/m/Y H:i:s'));
                        //return <div style="color: #586E7E; font-weight: bold;">value</div>?value.dateFormat('d/m/Y H:i:s'):''
                    }
                },
                type:'DateField',
                filters:{pfiltro:'dwf.fecha_mod',type:'date'},
                id_grupo:1,
                grid:true,
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
                filters:{pfiltro:'dwf.fecha_reg',type:'date'},
                id_grupo:1,
                grid:false,
                form:false
            },
            {
                config:{
                    name: 'estado_reg',
                    fieldLabel: 'Estado Reg.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10,
                    renderer:function(value,p,record){
                        return String.format('<div style="color: #586E7E; font-weight: bold; -moz-user-select: text !important;\n' +
                            '        -khtml-user-select: text !important;\n' +
                            '        -webkit-user-select: text !important;">{0} </div>', value);
                    }
                },
                type:'TextField',
                filters:{pfiltro:'dwf.estado_reg',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            }
        ],
        title:'Listado Documentos',
        ActList:'../../sis_obingresos/control/Reportes/listaDocumentoGenerado',
        id_store:'id_documento_generado',
        fields: [

            {name:'id_documento_generado', type: 'numeric'},
            {name:'url', type: 'string'},
            {name:'ver_documento', type: 'string'},
            {name:'url_documento', type: 'string'},
            {name:'nombre_documento', type: 'string'},
            {name:'size', type: 'string'},

            {name:'estado_reg', type: 'string'},
            {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'usr_reg', type: 'string'},
            {name:'fecha_generacion', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'fecha_ini', type: 'date',dateFormat:'Y-m-d'},
            {name:'fecha_fin', type: 'date',dateFormat:'Y-m-d'},
            {name:'formato', type: 'string'}

        ],


        bedit:false,
        bnew:false,
        bdel:false,
        bsave:false,
        fwidth: '90%',
        fheight: '95%'
    });
</script>
