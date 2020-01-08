<?php
/**
 *@package pXP
 *@file    SubirArchivo.php
 *@author  Grover Velasquez Colque
 *@date    22-03-2012
 *@description permite subir archivos csv con el extracto bancario de una cuenta en la tabla de ts_libro_bancos_extracto
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ConsumoACM=Ext.extend(Phx.frmInterfaz,{

            constructor:function(config)
            {
                Phx.vista.ConsumoACM.superclass.constructor.call(this,config);
                this.init();
                this.loadValoresIniciales();
            },

            loadValoresIniciales:function()
            {
                Phx.vista.ConsumoACM.superclass.loadValoresIniciales.call(this);
                this.getComponente('id_archivo_acm').setValue(this.id_archivo_acm);
               // this.getComponente('EXTACM').setValue(this.id_plantilla_archivo_excel);
            },

            successSave:function(resp)
            {
                console.log("irva respuesta",resp);
                Phx.CP.loadingHide();
                Phx.CP.getPagina(this.idContenedorPadre).reload();
                this.panel.close();
            },

            conexionFailure:function(resp){
              var datos_respuesta = JSON.parse(resp.responseText);
              Phx.CP.loadingHide();
              this.panel.close();
              if (datos_respuesta.ROOT.error == true) {
                  Ext.Msg.show({
                    width:'100%',
                    height:'100%',
                   title:'<h1 style="color:red; font-size:15px;"><center><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ALERTA</center></h1>',
                   msg: '<p style="font-size:14px;">'+datos_respuesta.ROOT.detalle.mensaje+'</p>',
                   buttons: Ext.Msg.OK,
                   fn: function () {
                      Phx.CP.getPagina(this.idContenedorPadre).reload();
                      this.panel.close();
                   },
                   scope:this
                });
                }
            },

            Atributos:[
                {
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
                        name:'codigo',
                        fieldLabel:'Codigo Archivo',
                        allowBlank:false,
                        emptyText:'Codigo Archivo...',
                        store: new Ext.data.JsonStore({
                            //if (this.store.baseParams.id_plantilla_archivo_excel = '14'){
                            url: '../../sis_obingresos/control/ArchivoAcm/listarPlantillaArchivoExcel',
                            // url: '../../sis_parametros/control/PlantillaArchivoExcel/listarPlantillaArchivoExcel',
                            id: 'id_plantilla_archivo_excel',
                            root: 'datos',
                            sortInfo:{
                                field: 'codigo',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_plantilla_archivo_excel','nombre','codigo'],
                            //turn on remote sorting
                            remoteSort: true,
                            baseParams:{par_filtro:'codigo', vista:'vista', archivoAcm: 'EXTACM'}
                        }),
                        valueField: 'codigo',
                        displayField: 'codigo',
                        //tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Nombre: {nombre}</b></p><p>{codigo}</p></div></tpl>',
                        hiddenName: 'codigo',
                        forceSelection:true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender:true,
                        mode:'remote',
                        pageSize:10,
                        queryDelay:1000,
                        listWidth:260,
                        resizable:true,
                        anchor:'90%',
                        tpl: new Ext.XTemplate([
                            '<tpl for=".">',
                            '<div class="x-combo-list-item">',
                            '<p><b>Nombre:</b> <span style="color: blue; font-weight: bold;">{nombre}</span></p>',
                            '<p><b>Codigo:</b> <span style="color: green; font-weight: bold;">{codigo}</span></p>',
                            '</div></tpl>'
                        ])
                    },
                    type:'ComboBox',
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        fieldLabel: "Documento",
                        gwidth: 130,
                        inputType:'file',
                        name: 'archivo',
                        buttonText: '',
                        maxLength:150,
                        anchor:'100%'
                    },
                    type:'Field',
                    form:true
                }
            ],
            title:'Subir Archivo',
            fileUpload:true,
            ActSave:'../../sis_obingresos/control/ArchivoAcm/cargarArchivoACMExcel'
        }
    )
</script>
