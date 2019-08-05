<?php
/**
 *@package pXP
 *@file gen-BoletoFormaPago.php
 *@author  (jrivera)
 *@date 13-06-2016 20:42:15
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.BoletoAmadeusFormaPago=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){

                this.maestro=config.maestro;
                //llama al constructor de la clase padre
                Phx.vista.BoletoAmadeusFormaPago.superclass.constructor.call(this,config);
                this.init();
                this.iniciarEventos();
                this.monto_fp = 0;


                var dataPadre = Phx.CP.getPagina(this.idContenedorPadre).getSelectedData()
                if(dataPadre){
                    this.onEnablePanel(this, dataPadre);
                }
                else
                {
                    this.bloquearMenus();
                }
            },

            Atributos:[
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_boleto_amadeus_forma_pago'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_boleto_amadeus'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config: {
                        name: 'id_forma_pago',
                        fieldLabel: 'Forma de Pago',
                        allowBlank: false,
                        emptyText: 'Forma de Pago...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_ventas_facturacion/control/FormaPago/listarFormaPago',
                            id: 'id_forma_pago',
                            root: 'datos',
                            sortInfo: {
                                field: 'nombre',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_forma_pago', 'nombre', 'desc_moneda','registrar_tarjeta','registrar_cc','codigo'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'forpa.nombre#forpa.codigo#mon.codigo_internacional',sw_tipo_venta:'boletos'}
                        }),
                        valueField: 'id_forma_pago',
                        displayField: 'nombre',
                        gdisplayField: 'forma_pago',
                        hiddenName: 'id_forma_pago',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p><b><p>Codigo:<font color="green">{codigo}</font></b></p><p><b>Moneda:<font color="red">{desc_moneda}</font></b></p> </div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        anchor: '60%',
                        gwidth: 350,
                        listWidth:'20%',
                        resizable:true,
                        minChars: 2,
                        disabled:false,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['forma_pago']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config:{
                        name: 'codigo',
                        fieldLabel: 'Cod. Forma Pago',
                        allowBlank: true,
                        anchor: '60%',
                        gwidth: 150,
                        minLength:16,
                        maxLength:20
                    },
                    type:'codigo',

                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'importe',
                        fieldLabel: 'Monto a Pagar',
                        allowBlank:false,
                        anchor: '60%',
                        allowDecimals:true,
                        decimalPrecision:2,
                        allowNegative : false,
                        gwidth: 125,
                        style: 'background-color: #F1F894;  background-image: none;'
                    },
                    type:'NumberField',
                    id_grupo:1,
                    grid:true,
                    form:true
                },

                {
                    config:{
                        name: 'numero_tarjeta',
                        fieldLabel: 'No Tarjeta',
                        allowBlank: true,
                        anchor: '60%',
                        gwidth: 150,
                        minLength:16,
                        maxLength:20
                    },
                    type:'TextField',

                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'codigo_tarjeta',
                        fieldLabel: 'Codigo de Autorización',
                        allowBlank: true,
                        anchor: '60%',
                        maxLength:6

                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'nro_cupon',
                        fieldLabel: 'Nro Cupon',
                        allowBlank: true,
                        anchor: '60%',
                        gwidth: 150,
                        minLength:15,
                        maxLength:20,
                        style: 'background-color: #A6F5C2;  background-image: none;'
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'nro_cuota',
                        fieldLabel: 'Nro Cuota',
                        allowBlank: true,
                        anchor: '60%',
                        gwidth: 150,
                        minLength:15,
                        maxLength:20,
                        style: 'background-color: #A6F5C2;  background-image: none;'
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config: {
                        name: 'id_auxiliar',
                        fieldLabel: 'Cuenta Corriente',
                        allowBlank: true,
                        emptyText: 'Cuenta Corriente...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_contabilidad/control/Auxiliar/listarAuxiliar',
                            id: 'id_auxiliar',
                            root: 'datos',
                            sortInfo: {
                                field: 'codigo_auxiliar',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_auxiliar', 'codigo_auxiliar','nombre_auxiliar'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'auxcta.codigo_auxiliar#auxcta.nombre_auxiliar',corriente:'si'}
                        }),
                        valueField: 'id_auxiliar',
                        displayField: 'nombre_auxiliar',
                        gdisplayField: 'codigo_auxiliar',
                        hiddenName: 'id_auxiliar',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre_auxiliar}</p><p>Codigo:{codigo_auxiliar}</p> </div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        anchor: '60%',
                        gwidth: 150,
                        listWidth:350,
                        resizable:true,
                        minChars: 2,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['nombre_auxiliar']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 2,
                    grid: true,
                    form: true
                },
                {
                    config:{
                        name: 'mco',
                        fieldLabel: 'MCO',
                        allowBlank: true,
                        anchor: '60%',
                        gwidth: 150,
                        minLength:15,
                        maxLength:20,
                        style: 'background-color: #A6F5C2;  background-image: none;'
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'id_usuario_ai',
                        fieldLabel: '',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:4
                    },
                    type:'Field',
                    filters:{pfiltro:'bfp.id_usuario_ai',type:'numeric'},
                    id_grupo:1,
                    grid:false,
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
                        name: 'usuario_ai',
                        fieldLabel: 'Funcionaro AI',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'bfp.usuario_ai',type:'string'},
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
                    filters:{pfiltro:'bfp.fecha_reg',type:'date'},
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
                    type:'Field',
                    filters:{pfiltro:'usu2.cuenta',type:'string'},
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
                    filters:{pfiltro:'bfp.fecha_mod',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:false
                }
            ],
            arrayDefaultColumHidden:['estado_reg','usuario_ai',
                'fecha_reg','fecha_mod','usr_reg','usr_mod'],
            tam_pag:50,
            title:'Forma de Pago',
            ActSave:'../../sis_obingresos/control/BoletoFormaPago/insertarBoletoAmadeusFormaPago',
            ActDel:'../../sis_obingresos/control/BoletoFormaPago/eliminarBoletoAmadeusFormaPago',
            ActList:'../../sis_obingresos/control/BoletoFormaPago/listarBoletoAmadeusFormaPago',
            id_store:'id_boleto_amadeus_forma_pago',
            fields: [
                {name:'id_boleto_amadeus_forma_pago', type: 'numeric'},
                {name:'tipo', type: 'string'},
                {name:'id_forma_pago', type: 'numeric'},
                {name:'id_boleto_amadeus', type: 'numeric'},
                {name:'estado_reg', type: 'string'},
                {name:'tarjeta', type: 'string'},
                //{name:'ctacte', type: 'string'},
                {name:'importe', type: 'numeric'},
                {name:'numero_tarjeta', type: 'string'},
                {name:'codigo_tarjeta', type: 'string'},
                {name:'forma_pago', type: 'string'},
                {name:'nombre_auxiliar', type: 'string'},
                //{name:'forma_pago_amadeus', type: 'string'},
                //{name:'fp_amadeus_corregido', type: 'string'},
                {name:'codigo_forma_pago', type: 'string'},
                {name:'id_usuario_ai', type: 'numeric'},
                {name:'id_usuario_reg', type: 'numeric'},
                {name:'usuario_ai', type: 'string'},
                {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'id_usuario_mod', type: 'numeric'},
                {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'usr_reg', type: 'string'},
                {name:'usr_mod', type: 'string'},
                {name:'moneda', type: 'string'},
                {name:'mco', type: 'string'} ,
                {name:'codigo', type: 'string'},
                {name:'nro_cuota', type: 'string'},
                {name:'nro_cupon', type: 'string'}

            ],
            sortInfo:{
                field: 'id_boleto_amadeus_forma_pago',
                direction: 'ASC'
            },
            bdel:true,
            bsave:false,
            bedit:true,
            onButtonEdit : function () {

                Phx.vista.BoletoAmadeusFormaPago.superclass.onButtonEdit.call(this);
                this.manejoComponentesFP(this.sm.getSelected().data['codigo']);
            },
            iniciarEventos : function () {
                this.Cmp.id_forma_pago.on('select', function (combo,record,index){
                    this.manejoComponentesFP(record.data.codigo);
                    if (this.maestro.moneda == record.data.desc_moneda){
                        this.Cmp.importe.setValue(this.monto_fp);
                    }
                    //Si el boleto esta en usd y la forma de pago es distinta a usd y la forma de pago es igual a la moneda de la sucursal
                    else if (this.maestro.moneda == 'USD' && record.data.desc_moneda == this.maestro.moneda_sucursal) {
                        //convertir de  dolares a moneda sucursal(multiplicar)
                        this.Cmp.importe.setValue(this.round((this.monto_fp*this.maestro.tc),2));

                        //Si el boleto esta en moneda sucursal y la forma de pago es usd y la moneda de la sucursales distinta a usd
                    } else if (this.maestro.moneda == this.maestro.moneda_sucursal && record.data.desc_moneda == 'USD') {
                        //convertir de  moneda sucursal a dolares(dividir)
                        this.Cmp.importe.setValue(this.round((this.monto_fp/this.maestro.tc),2));

                    }else {
                        this.Cmp.importe.setValue(0);

                    }

                },this)
            },
            manejoComponentesFP : function(codigoFp) {
                if (codigoFp.startsWith("CC") || codigoFp.startsWith("SF")) {
                    this.mostrarComponente(this.Cmp.numero_tarjeta);
                    this.mostrarComponente(this.Cmp.codigo_tarjeta);
                    this.ocultarComponente(this.Cmp.id_auxiliar);
                    this.ocultarComponente(this.Cmp.mco);
                    this.Cmp.numero_tarjeta.allowBlank = false;
                    this.Cmp.codigo_tarjeta.allowBlank = false;
                    //tarjeta de credito
                } else if (codigoFp.startsWith("CT")) {
                    //cuenta corriente
                    this.ocultarComponente(this.Cmp.numero_tarjeta);
                    this.ocultarComponente(this.Cmp.codigo_tarjeta);
                    this.Cmp.numero_tarjeta.reset();
                    this.Cmp.codigo_tarjeta.reset();
                    this.ocultarComponente(this.Cmp.mco);
                    this.mostrarComponente(this.Cmp.id_auxiliar);
                    this.Cmp.numero_tarjeta.allowBlank = true;
                    this.Cmp.codigo_tarjeta.allowBlank = true;
                    this.Cmp.id_auxiliar.allowBlank = false;
                }
                else if (codigoFp.startsWith("MCO")) {
                    //mco
                    console.log('hola');
                    this.ocultarComponente(this.Cmp.numero_tarjeta);
                    this.ocultarComponente(this.Cmp.id_auxiliar);
                    this.Cmp.id_auxiliar.reset();
                    this.mostrarComponente(this.Cmp.mco);
                    this.ocultarComponente(this.Cmp.codigo_tarjeta);
                    this.Cmp.mco.allowBlank = false;
                    this.Cmp.codigo_tarjeta.allowBlank = true;
                    this.Cmp.id_auxiliar.allowBlank = true;
                    this.Cmp.numero_tarjeta.allowBlank = true;
                }
                else {
                    this.ocultarComponente(this.Cmp.numero_tarjeta);
                    this.ocultarComponente(this.Cmp.codigo_tarjeta);
                    this.ocultarComponente(this.Cmp.id_auxiliar);
                    this.ocultarComponente(this.Cmp.mco);
                    this.Cmp.numero_tarjeta.reset();
                    this.Cmp.codigo_tarjeta.reset();
                    this.Cmp.numero_tarjeta.allowBlank = true;
                    this.Cmp.codigo_tarjeta.allowBlank = true;
                }
            },
            loadValoresIniciales:function(){
                Phx.vista.BoletoAmadeusFormaPago.superclass.loadValoresIniciales.call(this);
                this.Cmp.id_boleto_amadeus.setValue(this.maestro.id_boleto_amadeus);
            },

            onReloadPage:function(m){
                this.Cmp.id_forma_pago.store.baseParams.id_punto_venta = Phx.CP.getPagina(this.idContenedorPadre).id_punto_venta;
                this.maestro=m;
                this.store.baseParams.id_boleto_amadeus = this.maestro.id_boleto_amadeus;
                this.load({params:{start:0, limit:50}});

            },
            reload:function(p){
                Phx.CP.getPagina(this.idContenedorPadre).reload()
            },
            onButtonNew : function () {

                console.log('punto venta', Phx.CP.getPagina(this.idContenedorPadre).id_punto_venta);
                Phx.vista.BoletoAmadeusFormaPago.superclass.onButtonNew.call(this);
                //Si no hay ningun registro el monto_fp es el total del boleto
                if (this.store.getTotalCount() == 0) {
                    this.monto_fp = this.maestro.total - this.maestro.comision;
                } else {
                    //Si hay mas de un registro el monto_fp es el saldo a pagar del padre
                    this.monto_fp =  (this.maestro.total - this.maestro.comision) - this.maestro.monto_total_fp;
                }

                this.ocultarComponente(this.Cmp.numero_tarjeta);
                this.ocultarComponente(this.Cmp.codigo_tarjeta);
                this.ocultarComponente(this.Cmp.id_auxiliar);
                this.ocultarComponente(this.Cmp.mco)
            },

            round : function(value, decimals) {
                return Math.ceil(value*100)/100;
            },

            preparaMenu:function(n){

                Phx.vista.BoletoAmadeusFormaPago.superclass.preparaMenu.call(this,n);
                if(this.maestro.estado ==  'revisado'){
                    this.getBoton('edit').disable();
                    this.getBoton('new').disable();
                    this.getBoton('del').disable();
                }
                else{
                    this.getBoton('edit').enable();
                    this.getBoton('new').enable();
                    this.getBoton('del').enable();
                }
            },

            liberaMenu: function() {
                Phx.vista.BoletoAmadeusFormaPago.superclass.liberaMenu.call(this);
                if(this.maestro.estado !=  'revisado'){
                    var NumSelect=this.sm.getCount();
                    if(NumSelect != 0) {
                        this.getBoton('edit').enable();
                        this.getBoton('del').enable();
                    }else {
                        this.getBoton('edit').disable();
                        this.getBoton('del').disable();
                    }
                }else{
                    this.getBoton('new').disable();
                    this.getBoton('new').disable();
                    this.getBoton('new').disable();
                }

            }

        }
    )
</script>
