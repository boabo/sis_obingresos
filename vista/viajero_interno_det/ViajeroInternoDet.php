<?php
/**
*@package pXP
*@file gen-ViajeroInternoDet.php
*@author  (rzabala)
*@date 21-12-2018 14:21:07
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.ViajeroInternoDet=Ext.extend(Phx.gridInterfaz,{

    viewConfig: {
        stripeRows: false,
        getRowClass: function(record) {
            console.log('registro viajero interno det', record.data);
            if(record.data.estado_voucher == 'EMITIDO'){
                //this.editorgridpanel.on('afteredit', function (e) {
                    //if (e.shiftKey === true && e.getKey() === e.DOWN) {
                        //return  e.stopEvent(); //this will stop the shift+down keypress event from proceeding.
                    //}
                //});
            }
        }/*,
        listener: {
            render: this.createTooltip
        },*/

    },

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.ViajeroInternoDet.superclass.constructor.call(this,config);
		this.init();
		this.iniciarEventos();

	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_viajero_interno_det'
			},
			type:'Field',
			form:true 
		},
		{
			config:{
				name: 'nombre',
				fieldLabel: 'Nombre',
				allowBlank: true,
				anchor: '80%',
				gwidth: 200,
				maxLength:100,
                renderer: function(value, p, record){
                    return String.format('<b style="color:black; ">{0}</b>', record.data['nombre']);
                }
			},
				type:'TextField',
				filters:{pfiltro:'dvi.nombre',type:'string'},
				id_grupo:1,
				grid:true,
				form:true,
                bottom_filter : true
		},
		{
			config:{
				name: 'pnr',
				fieldLabel: 'PNR',
                selectOnFocus: true,
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
                sortable: false,
                maxLength:6,
                minLength:6,
                growMin: 6,
                maxLengthText : 'el maximo de caracteres es {6}',
                style: 'background-color:#9BF592 ; background-image: none;',
                renderer: function(value, p, record){
                    return String.format('<b style="color:darkcyan; ">{0}</b>', record.data['pnr']);
                }
			},
				type:'TextField',
				filters:{pfiltro:'dvi.pnr',type:'string'},
				id_grupo:1,
                egrid:true,
				grid:true,
				form:true,
                bottom_filter : true
		},
		{
			config:{
				name: 'num_boleto',
				fieldLabel: 'Boleto (930-)',
                selectOnFocus: false,
                //emptyText:'...',
				allowBlank: true,
                text:'930-',
				anchor: '80%',
				gwidth: 100,
                sortable: true,
				maxLength:10,
                minLength:10,
                style: 'background-color:#9BF592 ; background-image: none;',
                renderer: function (value, p, record) {
                    return String.format('<b style="color:blue; ">{0}</b>', record.data['num_boleto']);
                }
			},
				type:'NumberField',
				filters:{pfiltro:'dvi.num_boleto',type:'numeric'},
				id_grupo:1,
                egrid:true,
				grid:true,
				form:true,
                bottom_filter : true
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
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'dvi.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'dvi.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
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
				filters:{pfiltro:'dvi.usuario_ai',type:'string'},
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
                maxLength:10
            },
            type:'TextField',
            filters:{pfiltro:'dvi.estado_reg',type:'string'},
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
				filters:{pfiltro:'dvi.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
        {
            config:{
                name: 'id_viajero_interno',
                fieldLabel: 'id_viajero_interno',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:10
            },
            type:'NumberField',
            filters:{pfiltro:'dvi.id_viajero_interno',type:'numeric'},
            id_grupo:1,
            grid:false,
            form:false
        },
        {
            config:{
                name: 'solicitud',
                fieldLabel: 'Solicitud',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:10
            },
            type:'NumberField',
            filters:{pfiltro:'dvi.solicitud',type:'numeric'},
            id_grupo:1,
            grid:false,
            form:false
        },
        {
            config:{
                name: 'num_documento',
                fieldLabel: 'Nro. Documento',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:10
            },
            type:'NumberField',
            filters:{pfiltro:'dvi.num_documento',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:false
        },
        {
            config:{
                name: 'estado_voucher',
                fieldLabel: 'Estado de Solicitud',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:10,
                renderer: function (value, p, record) {
                    if (record.data['estado_voucher'] == 'EMITIDO') {
                        return String.format('<div title="Deshabilitado"><b><font color="red">{0}</font></b></div>', value);

                    } else {
                        return String.format('<div title="Habilitado"><b><font color="green">{0}</font></b></div>', value);
                    }
                }
            },
            type:'NumberField',
            filters:{pfiltro:'dvi.estado_voucher',type:'string'},
            id_grupo:1,
            grid:true,
            form:false
        },
        {
            config:{
                name: 'tarifa',
                fieldLabel: 'Tarifa',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:10,
                renderer: function(value, p, record){
                    return String.format('<b style="color:blueviolet; ">{0}</b>', record.data['tarifa']);
                }
            },
            type:'NumberField',
            filters:{pfiltro:'dvi.tarifa',type:'string'},
            id_grupo:1,
            grid:true,
            form:false
        },
	],
	tam_pag:50,	
	title:'Detalle Viajero Interno',
	ActSave:'../../sis_obingresos/control/ViajeroInternoDet/insertarViajeroInternoDet',
	ActDel:'../../sis_obingresos/control/ViajeroInternoDet/eliminarViajeroInternoDet',
	ActList:'../../sis_obingresos/control/ViajeroInternoDet/listarViajeroInternoDet',
	id_store:'id_viajero_interno_det',
	fields: [
		{name:'id_viajero_interno_det', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'nombre', type: 'string'},
		{name:'pnr', type: 'string'},
		{name:'num_boleto', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
        {name:'id_viajero_interno', type: 'numeric'},
        {name:'solicitud', type: 'string'},
        {name:'num_documento', type: 'numeric'},
        {name:'estado_voucher', type: 'string'},
        {name:'tarifa', type: 'string'},
		
	],
	sortInfo:{
		field: 'id_viajero_interno_det',
		direction: 'DESC'
	},
	bdel:false,
    bsave:true,
    btest:false,
    bnew:false,
    bedit:false,

    onReloadPage: function (m) {
        this.maestro = m;
        this.store.baseParams = {id_viajero_interno:this.maestro.id_viajero_interno};
        this.bloquearMenus();
        this.load({params: {start: 0, limit: 50}});
    },

    loadValoresIniciales: function () {
        this.Cmp.id_viajero_interno.setValue(this.maestro.id_viajero_interno);
        Phx.vista.ViajeroInternoDet.superclass.loadValoresIniciales.call(this);
    },
    iniciarEventos:function(){
        // this.Cmp.id_viajero_interno.on('select', function(cmb, record, index){},this);
        //var d = this.sm.getSelected().data;
        //console.log('data:',d.estado_voucher);
        this.grid.on('afteredit',function(e){
            e.record.set( 'num_boleto', parseInt(e.record.data.num_boleto));
            e.record.set( 'pnr', String(e.record.data.pnr));
            //e.record.set( 'solicitud',parseInt(e.record.data.solicitud));
            this.enviaTodo(e);
        }, this);

    },
    onButtonSave: function(){
        var d = this.sm.getSelected().data;
        console.log('data:',d.estado_voucher);
        var codVaucher = Phx.CP.getPagina(this.idContenedorPadre).getSelectedData();
        array_detalles = [];
        var detalle = {};
        var detalles='[{listaDatosVoucher:[';
        var filas=this.grid.getStore().getModifiedRecords();
        //if(filas.length>0) {
        console.log('FILAS',filas);
        //if (confirm("Está seguro de guardar los cambios?")) {
        //prepara una matriz para guardar los datos de la grilla
        var data = {};
        for (var i = 0; i < filas.length; i++) {
            //rac 29/10/11 buscar & para remplazar por su valor codificado
            data[i] = filas[i].data;
            //var dataChanges = filas[i].getChanges();
            detalle = {
                'id_viajero_interno_det' : data[i]['id_viajero_interno_det'],
                'codigoVoucher' : codVaucher['codigo_voucher'],
                'pnr' : data[i]['pnr'],
                'numBoleto' : data[i]['num_boleto'],
                'solicitudID' : data[i]['solicitud']
            };
            array_detalles.push(detalle);

        }
        //detalles += ']}]';
        //array_detalles=this.removeDuplicates(array_detalles);
        console.log('datos modificados: ', array_detalles);

        //this.grid.getStore().reload();
        //this.grid.getView().refresh();
        //}
        //}

        objeto = ({'listaDatosVoucher': array_detalles
                 });
        console.log('objeto', JSON.stringify(detalles));
        Ext.Ajax.request({
            url:'../../sis_obingresos/control/ViajeroInternoDet/validacionViajeroInternoDet',
            params:{
                obj:Ext.util.JSON.encode(objeto)
            },
            success: this.successAnularDet,
            failure: this.conexionFailure,
            timeout:this.timeout,
            scope:this
        });

        //Phx.vista.ViajeroInternoDet.superclass.onButtonSave.call(this);
        // console.log('DATOS:', this.Cmp.num_boleto);

    },
    removeDuplicates(array_detalles){
        var arrayOut = [];
        array_detalles.forEach(item=> {
            try {
                if (JSON.stringify(arrayOut[arrayOut.length-1].zone) !== JSON.stringify(item.zone)) {
                    arrayOut.push(item);
                }
            } catch(err) {
                arrayOut.push(item);
            }
        })
        return arrayOut;
        },
    successSave:function(resp){

        Phx.vista.MemoriaDet.superclass.successSave.call(this,resp);
        Phx.CP.getPagina(this.idContenedorPadre).reload();
        Phx.CP.getPagina(Phx.CP.getPagina(this.idContenedorPadre).idContenedorPadre).reload();
    },
    enviaTodo: function(e){
        //var tot = Number(e.record.data.cantidad_mem) * Number(e.record.data.importe_unitario);
        //e.record.set( 'importe', tot );
        e.record.markDirty();
    },
    successAnularDet:function(resp){
        Phx.CP.loadingHide();
        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
        if(!reg.ROOT.error){
            this.reload();
        }
    },

    preparaMenu: function () {
        var tb = this.tbar;
        var rec = this.sm.getSelected();
        var estadoPadre = Phx.CP.getPagina(this.idContenedorPadre).getSelectedData();
        console.log('que es',rec.data.estado_voucher);
            Phx.vista.ViajeroInternoDet.superclass.preparaMenu.call(this);
        if (rec.data['estado_voucher']== 'EMITIDO' || estadoPadre['estado_reg']=='inactivo'){
            tb.items.get('b-save-' + this.idContenedor).disable();
            //rec.data['num_boleto'].disable();
        }else{
            tb.items.get('b-save-' + this.idContenedor).enable();
        }
    },
    liberaMenu : function(){
        var rec = this.sm.getSelected();
/*        if (rec.data['estado_voucher']== 'EMITIDO' ){
            tb.items.get('b-save-' + this.idContenedor).disable();
        }else{
            tb.items.get('b-save-' + this.idContenedor).enable();
        }*/
        Phx.vista.ViajeroInternoDet.superclass.liberaMenu.call(this);

    },
	}
)
</script>
		
		