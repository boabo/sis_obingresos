CREATE OR REPLACE FUNCTION obingresos.ft_deposito_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
  /**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_deposito_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tdeposito'
 AUTOR: 		 (jrivera)
 FECHA:	        06-01-2016 22:42:28
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_deposito			integer;
    v_id_agencia			integer;
    v_id_moneda				integer;
    v_pnr					varchar;
    v_estado				record;
    v_agencia				record;
    v_id_alarma				integer;
    v_monto_total			numeric;
    v_moneda				varchar;
    v_deposito				record;
    v_aux					varchar;
    v_deposito_boa			varchar;
    v_verificar_existencia  record;
    v_control_deposito_boa	record;
    v_num_deposito			varchar;
    v_num_deposito_boa		varchar;
    v_monto_deposito		numeric;

    v_nro_cuenta			varchar;
    v_nombre_departamento	varchar;
    v_tabla					record;
    v_id_cuenta_bancaria	integer;
    v_id_departamento		integer;
    v_lugar					varchar;
    v_id_libro_bancos		integer;
    v_libro_bancos			record;

    v_id_proceso_wf			integer;
    v_codigo_estado			varchar;
    v_origen				varchar;
    v_nro_deposito			varchar;
    v_tipo					varchar;
    g_fecha					date;
    g_indice				numeric;
    v_id_estado_wf			integer;
	v_id_tipo_estado		integer;
    v_pedir_obs				varchar;
    v_codigo_estado_siguiente	varchar;
    v_id_estado_depositado	integer;
    v_acceso_directo		varchar;
    v_clase					varchar;
    v_parametros_ad			varchar;
    v_tipo_noti				varchar;
    v_titulo				varchar;
    v_id_depto				integer;
    v_obs					text;
    v_id_funcionario		integer;
    v_id_estado_actual		integer;
    v_id_finalidad			integer;
    v_cajero				varchar;
    v_id_forma_pago			integer;



BEGIN

    v_nombre_funcion = 'obingresos.ft_deposito_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_DEP_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	if(p_transaccion='OBING_DEP_INS')then
        begin

        	if (pxp.f_existe_parametro(p_tabla,'id_moneda_deposito')) then

            	v_id_moneda = v_parametros.id_moneda_deposito;
                select m.codigo_internacional into v_moneda
                from param.tmoneda m
                where m.id_moneda = v_parametros.id_moneda_deposito;

            else
            	select m.id_moneda into v_id_moneda
                from param.tmoneda m
                where m.codigo_internacional = v_parametros.moneda;
                v_moneda = v_parametros.moneda;
            end if;

       		SELECT per.nombre_completo1,
                   count(per.nombre) as existe,
                   depo.estado
                   into v_verificar_existencia
            FROM obingresos.tdeposito depo
            inner join segu.tusuario usu on usu.id_usuario = depo.id_usuario_reg
            inner join segu.vpersona per on per.id_persona = usu.id_persona
            WHERE
            depo.nro_deposito = v_parametros.nro_deposito and
            depo.fecha = v_parametros.fecha --and
            --depo.monto_deposito = v_parametros.monto_deposito
            group by per.nombre_completo1, depo.estado;

            /*CONTROL PARA NUM DE DEPOSITO Y LA FECHA*/
            SELECT per.nombre_completo1,
                   count(per.nombre) as existe,
                   depo.estado
                   into v_verificar_existencia
            FROM obingresos.tdeposito depo
            inner join segu.tusuario usu on usu.id_usuario = depo.id_usuario_reg
            inner join segu.vpersona per on per.id_persona = usu.id_persona
            WHERE
            depo.nro_deposito = v_parametros.nro_deposito and
            depo.fecha = v_parametros.fecha --and
            --depo.monto_deposito = v_parametros.monto_deposito
            group by per.nombre_completo1, depo.estado;
            /*----------------------------------------------*/



   /*AUMENTANDO CONDICION*/
    if (v_verificar_existencia.existe <> 0 and v_verificar_existencia.estado <> 'eliminado') THEN
    	raise exception 'El Registro con No Deposito = % y Fecha de Deposito = % ya se encuentra registrado por el Usuario: % por favor elimine el registro existente para registrar el actual',v_parametros.nro_deposito,to_char(v_parametros.fecha::date, 'DD/MM/YYYY'),/*v_parametros.monto_deposito,*/v_verificar_existencia.nombre_completo1;
    else
        	if (v_parametros.tipo = 'banca') then
            	insert into obingresos.tdeposito(
                estado_reg,
                nro_deposito,
                monto_deposito,
                id_moneda_deposito,
                fecha,
                agt,
                id_usuario_reg,
                fecha_reg,
                id_usuario_ai,
                usuario_ai,
                id_usuario_mod,
                fecha_mod,
                tipo,
                fecha_venta,
                monto_total
                ) values(
                'activo',
                v_parametros.nro_deposito,
                v_parametros.monto_deposito,
                v_id_moneda,
                v_parametros.fecha,
                v_parametros.agt,
                p_id_usuario,
                now(),
                v_parametros._id_usuario_ai,
                v_parametros._nombre_usuario_ai,
                null,
                null,
                v_parametros.tipo,
                v_parametros.fecha_venta,
                v_parametros.monto_total
                )RETURNING id_deposito into v_id_deposito;

        elsif(v_parametros.tipo = 'venta_propia') then

        /****************************Recuperamos el numero de cuenta en cuenta bancaria***********************************/
        select cuenta.nro_cuenta,
        	   pv.nombre,
               cuenta.id_cuenta_bancaria,
               cuen.id_depto,
               lu.codigo into v_nro_cuenta, v_nombre_departamento, v_id_cuenta_bancaria, v_id_departamento, v_lugar
        from vef.tpunto_venta pv
        inner join vef.tsucursal su on su.id_sucursal = pv.id_sucursal
        inner join tes.tdepto_cuenta_bancaria cuen on cuen.id_depto = su.id_depto
        inner join tes.tcuenta_bancaria cuenta on cuenta.id_cuenta_bancaria = cuen.id_cuenta_bancaria
        inner join param.tlugar lu on lu.id_lugar = su.id_lugar
        where pv.id_punto_venta = v_parametros.id_punto_venta and cuenta.id_moneda = v_id_moneda;

        SELECT per.nombre_completo2 into v_cajero
        from segu.tusuario usu
        inner join segu.vpersona2 per on per.id_persona = usu.id_persona
        where usu.id_usuario = v_parametros.id_usuario_cajero;


        /*---------------------------------------------------------------------------------------------*/

        if (v_nro_cuenta is null) then
        	raise exception 'No existe el numero de cuenta para la sucursal %, consulte con el departamento de ventas.',v_nombre_departamento;
        end if;

           insert into obingresos.tdeposito(
                estado_reg,
                nro_deposito,
                monto_deposito,
                id_moneda_deposito,
                fecha,
                id_usuario_reg,
                fecha_reg,
                id_usuario_ai,
                usuario_ai,
                id_usuario_mod,
                fecha_mod,
                tipo,
                id_apertura_cierre_caja,
                monto_total
                ) values(
                'activo',
                v_parametros.nro_deposito,
                v_parametros.monto_deposito,
                v_id_moneda,
                v_parametros.fecha,
                p_id_usuario,
                now(),
                v_parametros._id_usuario_ai,
                v_parametros._nombre_usuario_ai,
                null,
                null,
                v_parametros.tipo,
                v_parametros.id_apertura_cierre_caja,
                v_parametros.monto_deposito
                )RETURNING id_deposito into v_id_deposito;

           /*recuperamos el id_finalidad para venta_propia*/
           select fi.id_finalidad into v_id_finalidad
           from tes.tfinalidad fi
           where fi.nombre_finalidad = 'Venta Propia';

           select pa.id_forma_pago into v_id_forma_pago
                 from param.tforma_pago pa
                 where pa.desc_forma_pago = 'Deposito';

           /*************Creamos los parametros para enviar a la funcion que insertara al libro de bancos*****************/
                select 'deposito'::varchar as tipo,
                       v_parametros.nro_deposito as nro_deposito,
                       v_parametros.fecha::date as fecha_pago,
                       v_id_cuenta_bancaria as id_cuenta_bancaria,
                       v_id_departamento as id_depto,
                       v_parametros.monto_deposito as importe_deposito,
                       'Boliviana de Aviacion(BoA)'::varchar as a_favor,
                       ('Punto de venta: '||v_nombre_departamento||' Cajero: '||v_cajero)::varchar as detalle,
                       v_lugar as origen,
                       v_id_finalidad::integer as id_finalidad,
                       0::numeric as importe_cheque,
                       v_parametros.fecha::date as fecha,
                       v_id_forma_pago::integer as id_forma_pago,
                       v_id_deposito::integer as id_deposito
                 into v_tabla;
        	v_id_libro_bancos = tes.f_inserta_libro_bancos(p_administrador,p_id_usuario,hstore(v_tabla));

    		/*Cambio de estado*/
                select
                  lb.id_proceso_wf,
                  lb.id_estado_wf,
                  lb.sistema_origen,
                  lb.nro_cheque,
                  lb.tipo,
                  lb.id_cuenta_bancaria,
                  lb.fecha,
                  lb.indice
              into
                  v_id_proceso_wf,
                  v_id_estado_wf,
                  v_origen,
                  v_nro_deposito,
                  v_tipo,
                  v_id_cuenta_bancaria,
                  g_fecha,
                  g_indice
              from tes.tts_libro_bancos  lb
              --inner  join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
              where lb.id_libro_bancos  = v_id_libro_bancos;


              select
                ew.id_tipo_estado,
                te.pedir_obs,
                ew.id_funcionario
               into
                v_id_tipo_estado,
                v_pedir_obs,
                v_id_funcionario
              from wf.testado_wf ew
              inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
              where ew.id_estado_wf =  v_id_estado_wf;

              /*Obtenemos el id del estado finalizado*/
              v_id_estado_depositado = (v_id_tipo_estado+1);
              /****************************************/

              /*Obtenemnos el codigo depositado*/
                select te.codigo into v_codigo_estado
                from wf.ttipo_estado te
                where te.id_tipo_estado=v_id_estado_depositado;
                /******************************************/

              IF  pxp.f_existe_parametro(p_tabla,'id_depto_wf') THEN

               v_id_depto = v_parametros.id_depto_wf;

             END IF;



             IF  pxp.f_existe_parametro(p_tabla,'obs') THEN
                  v_obs=v_parametros.obs;
             ELSE
                   v_obs='---';

             END IF;

             --configurar acceso directo para la alarma
             v_acceso_directo = '';
             v_clase = '';
             v_parametros_ad = '';
             v_tipo_noti = '';
             v_titulo  = '';

             v_id_estado_actual =  wf.f_registra_estado_wf(v_id_tipo_estado,
                                                             v_id_funcionario,
                                                             v_id_estado_wf,
                                                             v_id_proceso_wf,
                                                             p_id_usuario,
                                                             v_parametros._id_usuario_ai,
                                                             v_parametros._nombre_usuario_ai,
                                                             v_id_depto,
                                                             ' Obs:'||v_obs,
                                                             --NULL,
                                                             v_acceso_directo,
                                                             --NULL,
                                                             v_clase,
                                                             --NULL,
                                                             v_parametros_ad,
                                                             --NULL,
                                                             v_tipo_noti,
                                                             --NULL);
                                                             v_titulo);

              /************************************************************/

              update tes.tts_libro_bancos  t set
               id_estado_wf =  v_id_estado_actual,
               estado = v_codigo_estado,
               id_usuario_mod=p_id_usuario,
               fecha_mod=now()
               where id_libro_bancos = v_id_libro_bancos;


               --Obtenemos el numero de indice que sera asignado al nuevo registro
                    Select max(lb.indice)
                    Into g_indice
                    From tes.tts_libro_bancos lb
                    Where lb.id_cuenta_bancaria = v_id_cuenta_bancaria
                    and lb.fecha = g_fecha;

                    If(g_indice is null )Then
                        g_indice = 0;
                    end if;

                    UPDATE tes.tts_libro_bancos SET
                        indice = g_indice + 1
                    WHERE tes.tts_libro_bancos.id_libro_bancos= v_id_libro_bancos;




        elsif(v_parametros.tipo = 'venta_agencia') then
           insert into obingresos.tdeposito(
                estado_reg,
                nro_deposito,
                monto_deposito,
                id_moneda_deposito,
                fecha,
                id_usuario_reg,
                fecha_reg,
                id_usuario_ai,
                usuario_ai,
                id_usuario_mod,
                fecha_mod,
                tipo,
                id_apertura_cierre_caja,
                monto_total
                ) values(
                'activo',
                v_parametros.nro_deposito,
                v_parametros.monto_deposito,
                v_id_moneda,
                v_parametros.fecha,
                p_id_usuario,
                now(),
                v_parametros._id_usuario_ai,
                v_parametros._nombre_usuario_ai,
                null,
                null,
                v_parametros.tipo,
                v_parametros.id_apertura_cierre_caja,
                v_parametros.monto_deposito
                )RETURNING id_deposito into v_id_deposito;

            else

            /****************************Recuperamos el numero de cuenta en cuenta bancaria para el tipo AGENCIA***********************************/
                select cuenta.nro_cuenta,
                   ag.nombre,
                   cuenta.id_cuenta_bancaria,
                   cuen.id_depto,
                   lu.codigo into v_nro_cuenta, v_nombre_departamento, v_id_cuenta_bancaria, v_id_departamento, v_lugar
            from obingresos.tagencia ag
            inner join vef.tsucursal su on su.id_lugar = ag.id_lugar
            inner join tes.tdepto_cuenta_bancaria cuen on cuen.id_depto = su.id_depto
            inner join tes.tcuenta_bancaria cuenta on cuenta.id_cuenta_bancaria = cuen.id_cuenta_bancaria
            inner join param.tlugar lu on lu.id_lugar = su.id_lugar
            where ag.id_agencia = v_parametros.id_agencia and cuenta.id_moneda = v_id_moneda;
            /*****************************************************************************************************************************************/
            /*Controlamos que el nro de cuenta exista*/
             if (v_nro_cuenta is null) then
        		raise exception 'No existe el numero de cuenta para la sucursal %, consulte con el departamento de ventas.',v_nombre_departamento;
              end if;
            /**************************************/
                insert into obingresos.tdeposito(
                estado_reg,
                nro_deposito,
                monto_deposito,
                id_moneda_deposito,
                id_agencia,
                fecha,
                saldo,
                id_usuario_reg,
                fecha_reg,
                id_usuario_ai,
                usuario_ai,
                id_usuario_mod,
                fecha_mod,
                estado
                ) values(
                'activo',
                v_parametros.nro_deposito,
                v_parametros.monto_deposito,
                v_id_moneda,
               	v_parametros.id_agencia,
                v_parametros.fecha,
                v_parametros.saldo,
                p_id_usuario,
                now(),
                v_parametros._id_usuario_ai,
                v_parametros._nombre_usuario_ai,
                null,
                null,
                'borrador'
                )RETURNING id_deposito into v_id_deposito;

                /*recuperamos el id_finalidad para venta_propia*/
                 select fi.id_finalidad into v_id_finalidad
                 from tes.tfinalidad fi
                 where fi.nombre_finalidad = 'Venta Agencia';

                 /*Recuperamos el id_forma_pago*/
                 select pa.id_forma_pago into v_id_forma_pago
                 from param.tforma_pago pa
                 where pa.desc_forma_pago = 'Deposito';

                /*************Creamos los parametros para enviar a la funcion que insertara al libro de bancos*****************/
                select 'deposito'::varchar as tipo,
                       v_parametros.nro_deposito as nro_deposito,
                       v_parametros.fecha::date as fecha_pago,
                       v_id_cuenta_bancaria as id_cuenta_bancaria,
                       v_id_departamento as id_depto,
                       v_parametros.monto_deposito as importe_deposito,
                       'Boliviana de Aviacion(BoA)'::varchar as a_favor,
                       ('Agencia: '||v_nombre_departamento)::varchar as detalle,
                       v_lugar as origen,
                       v_id_finalidad::integer as id_finalidad,
                       0::numeric as importe_cheque,
                       v_parametros.fecha::date as fecha,
                       v_id_forma_pago::integer as id_forma_pago,
                       v_id_deposito::integer as id_deposito
                 into v_tabla;
        		v_id_libro_bancos = tes.f_inserta_libro_bancos(p_administrador,p_id_usuario,hstore(v_tabla));
              /***************************************************************************************************************/

                if (v_parametros.id_agencia is not null) then
                	--generar alerta para ingresos

                    select a.*,lu.codigo as ciudad into v_agencia
                    from obingresos.tagencia a
                    inner join param.tlugar lu on lu.id_lugar = a.id_lugar
                    where id_agencia = v_parametros.id_agencia;

                    if (v_agencia.tipo_pago = 'prepago' and exists(select 1 from pxp.variable_global va where va.variable = 'obingresos_notidep_prep'))  then
                        v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Nuevo deposito agencia prepago','La Agencia ' || replace(v_agencia.nombre,'''',' ')  || ' ha registrado un nuevo deposito por '
                        			|| v_parametros.monto_deposito || ' ' || v_moneda || ' . Ingrese al Sistema ERP BOA para verificarlo.',
                        				pxp.f_get_variable_global('obingresos_notidep_prep')));

                	end if;

                    if (v_agencia.tipo_agencia = 'corporativa' and exists(select 1 from pxp.variable_global va where va.variable = 'obingresos_notidep_corp'))  then
                    	v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Nuevo deposito agencia prepago','La Agencia ' || replace(v_agencia.nombre,'''',' ') || ' ha registrado un nuevo deposito por '
                        			|| v_parametros.monto_deposito || ' ' || v_moneda || ' . Ingrese al Sistema ERP BOA para verificarlo.',
                        				pxp.f_get_variable_global('obingresos_notidep_corp')));
                	end if;
                    if (v_agencia.tipo_pago = 'postpago' and exists(select 1 from pxp.variable_global va where va.variable = 'obingresos_notidep_prep')) then
                    	v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Nuevo deposito agencia postpago','La Agencia ' || replace(v_agencia.nombre,'''',' ') || ' ha registrado un nuevo deposito por '
                        			|| v_parametros.monto_deposito || ' ' || v_moneda || ' . Ingrese al Sistema ERP BOA para verificarlo.',
                        				pxp.f_get_variable_global('obingresos_notidep_prep')));
                	end if;
                    if (v_agencia.tipo_pago = 'postpago' and exists(select 1 from pxp.variable_global va where va.variable = 'obingresos_notidep_posp_'||v_agencia.ciudad))  then
                    	v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Nuevo deposito agencia postpago','La Agencia ' || replace(v_agencia.nombre,'''',' ') || ' ha registrado un nuevo deposito por '
                        			|| v_parametros.monto_deposito || ' ' || v_moneda || ' . Ingrese al Sistema ERP BOA para verificarlo.',
                        				pxp.f_get_variable_global('obingresos_notidep_posp_'||v_agencia.ciudad)));
                	end if;

                    if (exists(select 1 from pxp.variable_global va where va.variable = 'obingresos_notidep'))  then
                    	v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Nuevo deposito de agencia','La Agencia ' || replace(v_agencia.nombre,'''',' ') || ' ha registrado un nuevo deposito por '
                        			|| v_parametros.monto_deposito || ' ' || v_moneda || ' . Ingrese al Sistema ERP BOA para verificarlo.',
                        				pxp.f_get_variable_global('obingresos_notidep')));
                	end if;
                end if;
            end if;

    /*Finalizando Condicion*/   end if;


            if (pxp.f_existe_parametro(p_tabla,'id_periodo_venta')) then

            	update obingresos.tdeposito
                set id_periodo_venta = v_parametros.id_periodo_venta
                where id_deposito = v_id_deposito;
            end if;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos almacenado(a) con exito (id_deposito'||v_id_deposito||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito',v_id_deposito::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_libro_bancos',v_id_libro_bancos::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_DEP_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	elsif(p_transaccion='OBING_DEP_MOD')then


		begin
        	    SELECT * into v_estado
                from obingresos.tdeposito
                where id_deposito = v_parametros.id_deposito;

        	if (v_parametros.tipo = 'agencia') then
                SELECT * into v_deposito
                from obingresos.tdeposito
                where id_deposito = v_parametros.id_deposito;
                --raise exception '%,%,%,%',v_deposito.id_agencia,v_deposito.nro_deposito,v_deposito.fecha,v_deposito.monto_deposito;

               /* update obingresos.tmovimiento_entidad
                set  fecha =  v_parametros.fecha,
                autorizacion__nro_deposito =  v_parametros.nro_deposito
                where id_agencia = v_deposito.id_agencia and   autorizacion__nro_deposito = v_deposito.nro_deposito
                and estado_reg = 'activo' and  fecha = v_deposito.fecha and monto = v_deposito.monto_deposito and tipo = 'credito';
               */

            end if;

            if (pxp.f_existe_parametro(p_tabla,'id_moneda_deposito')) then

            	v_id_moneda = v_parametros.id_moneda_deposito;
            else
            	select m.id_moneda into v_id_moneda
                from param.tmoneda m
                where m.codigo_internacional = v_parametros.moneda;
            end if;

            if (v_parametros.tipo = 'banca') then
                --Sentencia de la modificacion
                update obingresos.tdeposito set
                nro_deposito = v_parametros.nro_deposito,
                monto_deposito = v_parametros.monto_deposito,
                id_moneda_deposito = v_id_moneda,
                id_agencia =  v_parametros.id_agencia,
                fecha = v_parametros.fecha,
                saldo = v_parametros.saldo,
                id_usuario_mod = p_id_usuario,
                fecha_mod = now(),
                id_usuario_ai = v_parametros._id_usuario_ai,
                usuario_ai = v_parametros._nombre_usuario_ai,
                fecha_venta = v_parametros.fecha_venta,
                monto_total = v_parametros.monto_total,
                agt = v_parametros.agt
                where id_deposito=v_parametros.id_deposito;
            elsif (v_parametros.tipo = 'venta_propia')then

            select nro_deposito
            into
            v_aux
            from obingresos.tdeposito
            where id_deposito= v_parametros.id_deposito;

             update obingresos.tdeposito set
                nro_deposito = v_parametros.nro_deposito,
                nro_deposito_aux = v_aux,
                monto_deposito = v_parametros.monto_deposito,
                id_moneda_deposito = v_id_moneda,
                fecha = v_parametros.fecha,
                id_usuario_mod = p_id_usuario,
                fecha_mod = now(),
                id_usuario_ai = v_parametros._id_usuario_ai,
                usuario_ai = v_parametros._nombre_usuario_ai
                where id_deposito=v_parametros.id_deposito;

            /*Modificaremos el dato en el libro de ventas tambien se controlara la cuenta bancaria si es dolar o bs*/

            /****************************Recuperamos el numero de cuenta en cuenta bancaria***********************************/
              select cuenta.nro_cuenta,
                     pv.nombre,
                     cuenta.id_cuenta_bancaria,
                     cuen.id_depto,
                     lu.codigo into v_nro_cuenta, v_nombre_departamento, v_id_cuenta_bancaria, v_id_departamento, v_lugar
              from vef.tpunto_venta pv
              inner join vef.tsucursal su on su.id_sucursal = pv.id_sucursal
              inner join tes.tdepto_cuenta_bancaria cuen on cuen.id_depto = su.id_depto
              inner join tes.tcuenta_bancaria cuenta on cuenta.id_cuenta_bancaria = cuen.id_cuenta_bancaria
              inner join param.tlugar lu on lu.id_lugar = su.id_lugar
              where pv.id_punto_venta = v_parametros.id_punto_venta and cuenta.id_moneda = v_id_moneda;
            /*---------------------------------------------------------------------------------------------*/

              update tes.tts_libro_bancos set
                nro_deposito = v_parametros.nro_deposito,
                importe_deposito = v_parametros.monto_deposito,
                fecha = v_parametros.fecha,
                fecha_pago = v_parametros.fecha,
                id_cuenta_bancaria = v_id_cuenta_bancaria,
                id_usuario_mod = p_id_usuario,
                fecha_mod = now(),
                id_usuario_ai = v_parametros._id_usuario_ai,
                usuario_ai = v_parametros._nombre_usuario_ai
                where id_deposito=v_parametros.id_deposito;
            /***********************************************************************************************************************************/

           elsif (v_parametros.tipo = 'venta_agencia')then
        		 update obingresos.tdeposito set
                nro_deposito = v_parametros.nro_deposito,
                modificarDeposito = v_parametros.modificarDeposito,
                monto_deposito = v_parametros.monto_deposito,
                id_moneda_deposito = v_id_moneda,
                fecha = v_parametros.fecha,
                id_usuario_mod = p_id_usuario,
                fecha_mod = now(),
                id_usuario_ai = v_parametros._id_usuario_ai,
                usuario_ai = v_parametros._nombre_usuario_ai
                where id_deposito=v_parametros.id_deposito;
            else

            if (pxp.f_existe_parametro(p_tabla,'nro_deposito_boa')) then
                  v_deposito_boa = v_parametros.nro_deposito_boa;
              else
                  v_deposito_boa = '';
            end if;
            	--Sentencia de la modificacion

                --IF (v_estado.estado = 'borrador') then

                /*AUMENTANDO LA CONDICION*/


            /*CONTROL PARA NUM DE DEPOSITO BOA Y LA FECHA*/
                SELECT per.nombre_completo1,
                 count(depo.nro_deposito_boa) as existe,
                 depo.estado
                 into v_control_deposito_boa
                FROM obingresos.tdeposito depo
                inner join segu.tusuario usu on usu.id_usuario = depo.id_usuario_reg
                inner join segu.vpersona per on per.id_persona = usu.id_persona
                WHERE
                depo.nro_deposito_boa = v_deposito_boa and
                depo.fecha = v_parametros.fecha
                group by per.nombre_completo1, depo.estado;
            /*-----------------------------------------------------*/

              select depo.nro_deposito,
                     depo.nro_deposito_boa
              into v_num_deposito,
              	   v_num_deposito_boa
              from obingresos.tdeposito depo
              where depo.id_deposito = v_parametros.id_deposito;



            /*CONTROL PARA NUM DE DEPOSITO Y LA FECHA*/
            SELECT per.nombre_completo1,
                   count(per.nombre) as existe,
                   depo.estado
                   into v_verificar_existencia
            FROM obingresos.tdeposito depo
            inner join segu.tusuario usu on usu.id_usuario = depo.id_usuario_reg
            inner join segu.vpersona per on per.id_persona = usu.id_persona
            WHERE
            depo.nro_deposito = v_parametros.nro_deposito and
            depo.fecha = v_parametros.fecha --and
            --depo.monto_deposito = v_parametros.monto_deposito
            group by per.nombre_completo1, depo.estado;
            /*----------------------------------------------*/

            IF (v_num_deposito = v_parametros.nro_deposito )  then
            		v_verificar_existencia.existe = 0;
            end if;

            IF (v_num_deposito_boa = v_deposito_boa )  then
            		v_control_deposito_boa.existe = 0;
            end if;

            IF (v_deposito_boa <> '' and v_control_deposito_boa.existe <> 0 and v_control_deposito_boa.estado <> 'eliminado') then

                raise exception 'El Registro con No Deposito Boa = % y Fecha de Deposito = % ya se encuentra registrado por el Usuario: % por favor elimine el registro existente para registrar el actual',v_deposito_boa,to_char(v_parametros.fecha::date, 'DD/MM/YYYY'),/*v_parametros.monto_deposito,*/v_control_deposito_boa.nombre_completo1;

            else

            if (v_verificar_existencia.existe <> 0 and v_verificar_existencia.estado <> 'eliminado') THEN
                raise exception 'El Registro con No Deposito = % y Fecha de Deposito = % ya se encuentra registrado por el Usuario: % por favor elimine el registro existente para registrar el actual',v_parametros.nro_deposito,to_char(v_parametros.fecha::date, 'DD/MM/YYYY'),/*v_parametros.monto_deposito,*/v_verificar_existencia.nombre_completo1;
            else


            /****************************Recuperamos el numero de cuenta en cuenta bancaria para el tipo AGENCIA***********************************/
                select cuenta.nro_cuenta,
                   ag.nombre,
                   cuenta.id_cuenta_bancaria,
                   cuen.id_depto,
                   lu.codigo into v_nro_cuenta, v_nombre_departamento, v_id_cuenta_bancaria, v_id_departamento, v_lugar
            from obingresos.tagencia ag
            inner join vef.tsucursal su on su.id_lugar = ag.id_lugar
            inner join tes.tdepto_cuenta_bancaria cuen on cuen.id_depto = su.id_depto
            inner join tes.tcuenta_bancaria cuenta on cuenta.id_cuenta_bancaria = cuen.id_cuenta_bancaria
            inner join param.tlugar lu on lu.id_lugar = su.id_lugar
            where ag.id_agencia = v_parametros.id_agencia and cuenta.id_moneda = v_id_moneda;
            /*****************************************************************************************************************************************/
          	/*Actualizamos el libro de bancos*/
        	 update tes.tts_libro_bancos set
                nro_deposito = v_parametros.nro_deposito,
                importe_deposito = v_parametros.monto_deposito,
                fecha = v_parametros.fecha,
                fecha_pago = v_parametros.fecha,
                id_cuenta_bancaria = v_id_cuenta_bancaria,
                id_usuario_mod = p_id_usuario,
                fecha_mod = now(),
                id_usuario_ai = v_parametros._id_usuario_ai,
                usuario_ai = v_parametros._nombre_usuario_ai
                where id_deposito=v_parametros.id_deposito;
            /*-------------------------------------------------*/

                update obingresos.tdeposito set
                nro_deposito = v_parametros.nro_deposito,
                monto_deposito = v_parametros.monto_deposito,
                id_moneda_deposito = v_id_moneda,
                id_agencia =  v_parametros.id_agencia,
                fecha = v_parametros.fecha,
                saldo = v_parametros.saldo,
                id_usuario_mod = p_id_usuario,
                fecha_mod = now(),
                id_usuario_ai = v_parametros._id_usuario_ai,
                usuario_ai = v_parametros._nombre_usuario_ai,
                nro_deposito_boa = v_deposito_boa
                where id_deposito=v_parametros.id_deposito;
            end if;
           end if;





                /*ELSE

                raise exception 'NO SE PUEDE MODIFICAR DEPOSITOS QUE YA FUERON VALIDADOS!';

                end if;*/

            end if;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito',v_parametros.id_deposito::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
    /*********************************
 	#TRANSACCION:  'OBING_DEP_SUB'
 	#DESCRIPCION:	Subir datos
 	#AUTOR:		mam
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	elsif(p_transaccion='OBING_DEP_SUB')then
   			begin

            if (trim(both ' ' from v_parametros.estado) = 'Payment requested') then

            	if (to_date(v_parametros.fecha,'DD/MM/YYYY')>'09/09/2017') then
                	v_parametros.pnr = substring(v_parametros.pnr from 1 for 6);
                else
            		v_parametros.pnr = substring(v_parametros.pnr from 1 for 5);
                end if;
                select id_moneda into v_id_moneda
                from param.tmoneda m
                where m.codigo_internacional = trim(both ' ' from v_parametros.moneda);

                if (v_id_moneda is null)then
                    raise exception 'No existe la moneda % en la base de datos',v_parametros.moneda;
                end if;

                if (exists(	select nro_deposito
                            from obingresos.tdeposito
                            where nro_deposito = trim(both ' ' from v_parametros.nro_deposito))) then
                    raise exception 'El deposito % ya esta registrado',v_parametros.nro_deposito;
                end if;

                insert into obingresos.tdeposito(
                nro_deposito,
                id_agencia,
                monto_deposito,
                moneda,
                descripcion,
                pnr,
                id_moneda_deposito,
                saldo,
                fecha,
                tipo,
                observaciones
                ) values(
                trim(both ' ' from v_parametros.nro_deposito),
                v_parametros.id_agencia,
                v_parametros.monto_deposito,
                trim(both ' ' from v_parametros.moneda),
                trim(both ' ' from v_parametros.descripcion),
                trim(both ' ' from v_parametros.pnr),
                v_id_moneda,
                v_parametros.monto_deposito,
                to_date(v_parametros.fecha,'DD/MM/YYYY'),
                v_parametros.tipo,
                trim(both ' ' from v_parametros.observaciones)

                )RETURNING id_deposito into v_id_deposito;
            end if;

            --Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos almacenado(a) con exito (id_deposito'||v_id_deposito||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito',v_id_deposito::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_DEPWP_SUB'
 	#DESCRIPCION:	Subir datos World Pay
 	#AUTOR:		jrr
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	elsif(p_transaccion='OBING_DEPWP_SUB')then
   			begin

            if (trim(both ' ' from v_parametros.estado) in ('SETTLED','CAPTURED')) then

                if (to_date(v_parametros.fecha,'YYYY.MM.DD')>'09/09/2017') then
                	v_pnr = substring(v_parametros.order_code from 1 for 6);
                else
            		v_pnr = substring(v_parametros.order_code from 1 for 5);
                end if;
                select id_moneda into v_id_moneda
                from param.tmoneda m
                where m.codigo_internacional = trim(both ' ' from v_parametros.moneda);

                if (v_id_moneda is null)then
                    raise exception 'No existe la moneda % en la base de datos',v_parametros.moneda;
                end if;

                if (not exists(	select nro_deposito
                            from obingresos.tdeposito
                            where nro_deposito = trim(both ' ' from v_parametros.order_code))) then

                insert into obingresos.tdeposito(
                id_usuario_reg,
                  nro_deposito,
                  --id_agencia,
                  monto_deposito,
                  moneda,
                  descripcion,
                  pnr,
                  id_moneda_deposito,
                  saldo,
                  fecha,
                  tipo,
                  observaciones
                  ) values(
                  p_id_usuario,
                  trim(both ' ' from v_parametros.order_code),
                  --v_parametros.id_agencia,
                  v_parametros.monto,
                  trim(both ' ' from v_parametros.moneda),
                  trim(both ' ' from v_parametros.tarjeta),
                  v_pnr,
                  v_id_moneda,
                  v_parametros.monto,
                  to_date(v_parametros.fecha,'YYYY.MM.DD'),
                  'ogone',
                  trim(both ' ' from v_parametros.metodo_pago)

                  )RETURNING id_deposito into v_id_deposito;
                --raise exception 'llega';
                end if;
            end if;

            --Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos almacenado(a) con exito (id_deposito'||v_id_deposito||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito',v_id_deposito::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_DEP_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	elsif(p_transaccion='OBING_DEP_ELI')then

		begin

        select de.tipo
        into
        v_deposito
        from obingresos.tdeposito de
        where de.id_deposito =  v_parametros.id_deposito;

        if   (v_deposito.tipo  = 'agencia') then

            update obingresos.tdeposito
            set
            estado = 'eliminado',
            id_usuario_mod = p_id_usuario,
            fecha_mod = now()
            where id_deposito=v_parametros.id_deposito;

            delete from tes.tts_libro_bancos
            where id_deposito = v_parametros.id_deposito;

          else
			/*Recuperamos el estado del libro de bancos*/
			select  libro.estado,
                    libro.id_libro_bancos into v_libro_bancos
            from tes.tts_libro_bancos libro
            where  libro.id_deposito = v_parametros.id_deposito;
			/*-----------------------------------------*/

            /*Si el deposito no esta insertado en el libro de bancos se puede eliminar*/
            if (v_libro_bancos.id_libro_bancos is null) then
            	delete from obingresos.tdeposito
            	where id_deposito=v_parametros.id_deposito;
            else --Si el deposito se encuentra en el libro de ventas controlamos que el estado este en borrador para eliminar
            	--if (v_libro_bancos.estado <> 'borrador') then
                 --raise exception 'El deposito seleccionado no se puede eliminar ya que se encuentra en estado: % en el libro de bancos. Solo se pueden eliminar depositos en estado borrador',v_libro_bancos.estado;
                 --ELSE
                 		delete from tes.tts_libro_bancos
                        where id_deposito = v_parametros.id_deposito;

                 		delete from obingresos.tdeposito
                        where id_deposito=v_parametros.id_deposito;
            	--end if;

            end if;

        end if;


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito',v_parametros.id_deposito::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
    elsif(p_transaccion='OBING_VALIDEPO_UPD')then

		begin
			--Sentencia de la eliminacion
            select de.monto_deposito into v_monto_deposito
            from obingresos.tdeposito de
            where de.id_deposito = v_parametros.id_deposito;

            if (v_monto_deposito > 0) then
                update obingresos.tdeposito
                set
                estado = 'validado',
                id_usuario_mod = p_id_usuario,
                fecha_mod = now()
                where id_deposito=v_parametros.id_deposito;


                /*Cambio de estado*/
                select
                  lb.id_libro_bancos,
                  lb.id_proceso_wf,
                  lb.id_estado_wf,
                  lb.sistema_origen,
                  lb.nro_cheque,
                  lb.tipo,
                  lb.id_cuenta_bancaria,
                  lb.fecha,
                  lb.indice
              into
                  v_id_libro_bancos,
                  v_id_proceso_wf,
                  v_id_estado_wf,
                  v_origen,
                  v_nro_deposito,
                  v_tipo,
                  v_id_cuenta_bancaria,
                  g_fecha,
                  g_indice
              from tes.tts_libro_bancos  lb
              --inner  join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
              where lb.id_deposito  = v_parametros.id_deposito;

              if (v_id_libro_bancos is not null) then

              select
                ew.id_tipo_estado,
                te.pedir_obs,
                ew.id_funcionario
               into
                v_id_tipo_estado,
                v_pedir_obs,
                v_id_funcionario
              from wf.testado_wf ew
              inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
              where ew.id_estado_wf =  v_id_estado_wf;

              /*Obtenemos el id del estado finalizado*/
              v_id_estado_depositado = (v_id_tipo_estado+1);
              /****************************************/

              /*Obtenemnos el codigo depositado*/
                select te.codigo into v_codigo_estado
                from wf.ttipo_estado te
                where te.id_tipo_estado=v_id_estado_depositado;
                /******************************************/

              IF  pxp.f_existe_parametro(p_tabla,'id_depto_wf') THEN

               v_id_depto = v_parametros.id_depto_wf;

             END IF;



             IF  pxp.f_existe_parametro(p_tabla,'obs') THEN
                  v_obs=v_parametros.obs;
             ELSE
                   v_obs='---';

             END IF;

             --configurar acceso directo para la alarma
             v_acceso_directo = '';
             v_clase = '';
             v_parametros_ad = '';
             v_tipo_noti = '';
             v_titulo  = '';

             v_id_estado_actual =  wf.f_registra_estado_wf(v_id_tipo_estado,
                                                             v_id_funcionario,
                                                             v_id_estado_wf,
                                                             v_id_proceso_wf,
                                                             p_id_usuario,
                                                             v_parametros._id_usuario_ai,
                                                             v_parametros._nombre_usuario_ai,
                                                             v_id_depto,
                                                             ' Obs:'||v_obs,
                                                             --NULL,
                                                             v_acceso_directo,
                                                             --NULL,
                                                             v_clase,
                                                             --NULL,
                                                             v_parametros_ad,
                                                             --NULL,
                                                             v_tipo_noti,
                                                             --NULL);
                                                             v_titulo);

              /************************************************************/

              update tes.tts_libro_bancos  t set
               id_estado_wf =  v_id_estado_actual,
               estado = v_codigo_estado,
               id_usuario_mod=p_id_usuario,
               fecha_mod=now()
               where id_proceso_wf = v_id_proceso_wf;


               --Obtenemos el numero de indice que sera asignado al nuevo registro
                    Select max(lb.indice)
                    Into g_indice
                    From tes.tts_libro_bancos lb
                    Where lb.id_cuenta_bancaria = v_id_cuenta_bancaria
                    and lb.fecha = g_fecha;

                    If(g_indice is null )Then
                        g_indice = 0;
                    end if;

                    UPDATE tes.tts_libro_bancos SET
                        indice = g_indice + 1
                    WHERE tes.tts_libro_bancos.id_libro_bancos= v_id_libro_bancos;
               end if;

            else
            	raise exception 'No se puede Validar un Deposito con monto 0, Verifique!';
            end if;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos validado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito',v_parametros.id_deposito::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
    /*********************************
 	#TRANSACCION:  'OBING_DEP_MI'
 	#DESCRIPCION:	Recuperar datos
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	elsif(p_transaccion='OBING_DEP_MI')then

		begin


        IF  pxp.f_existe_parametro(p_tabla,'nro_deposito') THEN
           if v_parametros.nro_deposito = null or v_parametros.nro_deposito = '' then
           raise exception 'error';
           end if;
        END IF;
          IF  pxp.f_existe_parametro(p_tabla,'codigo') THEN
           if v_parametros.codigo = null or v_parametros.codigo = '' then
           raise exception 'error';
           end if;
        END IF;
         IF  pxp.f_existe_parametro(p_tabla,'fecha_venta') THEN
           if v_parametros.fecha_venta = null then
           raise exception 'error';
           end if;
        END IF;
          IF  pxp.f_existe_parametro(p_tabla,'monto_deposito') THEN
           if v_parametros.monto_deposito = null  then
           raise exception 'error';
           end if;
        END IF;
         IF  pxp.f_existe_parametro(p_tabla,'desc_moneda') THEN
           if v_parametros.desc_moneda = null  or v_parametros.desc_moneda = '' then
           raise exception 'error';
           end if;
        END IF;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'nro_deposito',v_parametros.nro_deposito::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
	else

    	raise exception 'Transaccion inexistente: %',p_transaccion;

	end if;

EXCEPTION

	WHEN OTHERS THEN
		v_resp='';
		v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
		v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
		v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
		raise exception '%',v_resp;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION obingresos.ft_deposito_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
