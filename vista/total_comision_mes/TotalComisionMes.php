<?php
/**
*@package pXP
*@file gen-TotalComisionMes.php
*@author  (jrivera)
*@date 17-08-2017 21:28:24
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.TotalComisionMes=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
        this.initButtons=[this.combo_gestion,this.combo_periodo];
    	//llama al constructor de la clase padre
		Phx.vista.TotalComisionMes.superclass.constructor.call(this,config);
		this.init();
        this.iniciarEventos();
        this.addButton('btnValidar',
            {
                text: 'Validado',
                iconCls: 'bok',
                disabled: true,
                handler: this.onValidar,
                tooltip: 'Factura Validada'
            }
        );

        this.addButton('archivo', {
            grupo: [0,1],
            argument: {imprimir: 'archivo'},
            text: 'Factura Digital',
            iconCls:'blist' ,
            disabled: false,
            handler: this.archivo
        });
        this.finCons = true;

        this.store.baseParams.estado = 'pendiente';

		this.load({params:{start:0, limit:this.tam_pag}})
	},

        gruposBarraTareas: [{
            name: 'pendiente',
            title: '<H1 align="center"><i class="fa fa-eye"></i> Pendientes</h1>',
            grupo: 0,
            height: 0
        },
            {
                name: 'validada',
                title: '<H1 align="center"><i class="fa fa-eye"></i> Validadas</h1>',
                grupo: 1,
                height: 0
            }

        ],
        actualizarSegunTab: function (name, indice) {
            if (this.finCons) {
                this.store.baseParams.estado = name;
                this.load({params: {start: 0, limit: this.tam_pag}});
            }
        },
        onValidar : function () {
            var rec = this.sm.getSelected();
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_obingresos/control/TotalComisionMes/validarComisionMes',
                params: {'id_total_comision_mes':rec.data.id_total_comision_mes},
                success:this.successSave,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });

        },
        archivo : function (){
            var rec = this.getSelectedData();

            //enviamos el id seleccionado para cual el archivo se deba subir
            rec.datos_extras_id = rec.id_total_comision_mes;
            //enviamos el nombre de la tabla
            rec.datos_extras_tabla = 'obingresos.ttotal_comision_mes';
            //enviamos el codigo ya que una tabla puede tener varios archivos diferentes como ci,pasaporte,contrato,slider,fotos,etc
            rec.datos_extras_codigo = 'ESCANFAC';

            Phx.CP.loadWindows('../../../sis_parametros/vista/archivo/Archivo.php',
                'Archivo',
                {
                    width: 900,
                    height: 400
                }, rec, this.idContenedor, 'Archivo');
        },

        combo_gestion : new Ext.form.ComboBox({
            store: new Ext.data.JsonStore({

                url: '../../sis_parametros/control/Gestion/listarGestion',
                id: 'gestion',
                root: 'datos',
                sortInfo:{
                    field: 'gestion',
                    direction: 'DESC'
                },
                totalProperty: 'total',
                fields: [
                    {name:'id_gestion'},
                    {name:'gestion', type: 'string'},
                    {name:'estado_reg', type: 'string'}
                ],
                remoteSort: true,
                baseParams:{start:0,limit:10}
            }),
            displayField: 'gestion',
            valueField: 'gestion',
            typeAhead: true,
            mode: 'remote',
            triggerAction: 'all',
            emptyText:'Gestión...',
            grupo: [0,1],
            selectOnFocus:true,
            width:100
        }),
        combo_periodo : new Ext.form.ComboBox({
                store:['1','2','3','4','5','6','7','8','9','10','11','12'],
                typeAhead: true,
                mode: 'local',
                triggerAction: 'all',
                emptyText:'Periodo...',
                selectOnFocus:true,
                width:135,
            grupo: [0,1],
            }),
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_total_comision_mes'
			},
			type:'Field',
			form:true 
		},
        {
            config:{
                name: 'medio_pago',
                fieldLabel: 'Medio Pago',
                gwidth: 130
            },
            type:'TextField',
            filters:{pfiltro:'tp.medio_pago',type:'string'},
            id_grupo:1,
            bottom_filter:true,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'codigo_int',
                fieldLabel: 'Officeid',
                gwidth: 130
            },
            type:'TextField',
            filters:{pfiltro:'age.codigo_int',type:'string'},
            id_grupo:1,
            bottom_filter:true,
            grid:true,
            form:true
        },

        {
            config:{
                name: 'agencia',
                fieldLabel: 'Agencia',
                gwidth: 200
            },
            type:'TextField',
            filters:{pfiltro:'age.nombre',type:'string'},
            id_grupo:1,
            bottom_filter:true,
            grid:true,
            form:true
        },


		{
			config:{
				name: 'total_comision',
				fieldLabel: 'Comision Calculada MB',
				allowBlank: false,
				anchor: '80%',
				gwidth: 180,
				maxLength:1179650
			},
				type:'NumberField',
				filters:{pfiltro:'totfac.total_comision',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		}


	],
	tam_pag:50,	
	title:'Facturas por Mes',
	ActSave:'../../sis_obingresos/control/TotalComisionMes/insertarTotalComisionMes',
	ActDel:'../../sis_obingresos/control/TotalComisionMes/eliminarTotalComisionMes',
	ActList:'../../sis_obingresos/control/TotalComisionMes/listarTotalComisionMes',
	id_store:'id_total_comision_mes',
	fields: [
		{name:'id_total_comision_mes', type: 'numeric'},
		{name:'gestion', type: 'numeric'},
		{name:'estado', type: 'string'},
        {name:'codigo_int', type: 'string'},
        {name:'agencia', type: 'string'},
        {name:'medio_pago', type: 'string'},
		{name:'max_fecha_fin_periodo', type: 'date',dateFormat:'Y-m-d'},
		{name:'periodo', type: 'numeric'},
		{name:'total_comision', type: 'numeric'},
		{name:'id_periodos', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'id_tipo_periodo', type: 'numeric'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		
	],
	sortInfo:{
		field: 'id_total_comision_mes',
		direction: 'ASC'
	},
        iniciarEventos : function () {

            this.combo_gestion.on('select', function(c,r,i) {
                this.store.baseParams.gestion = this.combo_gestion.getValue();
                if (this.combo_gestion.getValue() && this.combo_periodo.getValue())
                {
                    this.load({params:{start:0, limit:this.tam_pag}});

                }

            } , this);

            this.combo_periodo.on('select', function(c,r,i) {
                this.store.baseParams.periodo = this.combo_periodo.getValue();
                if (this.combo_gestion.getValue() && this.combo_periodo.getValue())
                {
                    this.load({params:{start:0, limit:this.tam_pag}});

                }

            } , this);
        },
	bdel:false,
	bsave:false,
        bnew:false,
        bedit:false,
        preparaMenu: function () {
            var rec = this.sm.getSelected();
            Phx.vista.TotalComisionMes.superclass.preparaMenu.call(this);
            this.getBoton('archivo').enable();
            if (rec.data.estado == 'pendiente') {
                this.getBoton('btnValidar').enable();
            }
            else {
                this.getBoton('btnValidar').disable();
            }

        },

        liberaMenu: function () {
            var rec = this.sm.getSelected();
            this.getBoton('archivo').disable();
            Phx.vista.TotalComisionMes.superclass.liberaMenu.call(this);
            this.getBoton('btnValidar').disable();

        }
	}
)
</script>
		
		