CREATE OR REPLACE FUNCTION obingresos.ft_movimiento_entidad_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_movimiento_entidad_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tmovimiento_entidad'
 AUTOR: 		 (jrivera)
 FECHA:	        17-05-2017 15:53:35
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
	v_id_movimiento_entidad	integer;
    v_codigo_autorizacion	varchar;
    v_id_periodo_venta		integer;
    v_monto					numeric;
    v_id_alarma				integer;
    v_moneda				varchar;
    v_agencia				varchar;
    v_usuario				varchar;


    /*Aumentando para obtener el maximo*/
    v_periodo_maximo		integer;
    v_monto_arrastre		numeric;
    v_existencia			integer;
    v_fecha_arrastre		date;
    v_id_moneda				integer;
	v_existencia_vigente	numeric;

BEGIN

    v_nombre_funcion = 'obingresos.ft_movimiento_entidad_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_MOE_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera
 	#FECHA:		17-05-2017 15:53:35
	***********************************/

	if(p_transaccion='OBING_MOE_INS')then

        begin

        	if (v_parametros.tipo = 'debito') then
            	select po_autorizacion into v_codigo_autorizacion
            	from obingresos.f_verificar_saldo_agencia(v_parametros.id_agencia,
                							v_parametros.monto,v_parametros.id_moneda::varchar,p_id_usuario,NULL,NULL,'no',v_parametros.monto);

            end if;

            select m.codigo_internacional into v_moneda
            from param.tmoneda m
            where id_moneda = v_parametros.id_moneda;

            select a.nombre into v_agencia
            from obingresos.tagencia a
            where a.id_agencia = v_parametros.id_agencia;

        	select u.cuenta into v_usuario
            from segu.tusuario u
            where u.id_usuario = p_id_usuario;

        	--Sentencia de la insercion
        	insert into obingresos.tmovimiento_entidad(
			id_moneda,
			id_periodo_venta,
			id_agencia,
			garantia,
			monto_total,
			tipo,
			autorizacion__nro_deposito,
			estado_reg,
			monto,
			ajuste,
			fecha,
			pnr,
			apellido,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			id_usuario_ai,
			fecha_mod,
			id_usuario_mod,
            fk_id_movimiento_entidad
          	) values(
			v_parametros.id_moneda,
			NULL,
			v_parametros.id_agencia,
			v_parametros.garantia,
			v_parametros.monto,
			v_parametros.tipo,
			v_parametros.autorizacion__nro_deposito,
			'activo',
			v_parametros.monto,
			'si',
			v_parametros.fecha::date,
			NULL,
			NULL,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			v_parametros._id_usuario_ai,
			null,
			null,
            v_parametros.fk_id_movimiento_entidad


			)RETURNING id_movimiento_entidad into v_id_movimiento_entidad;



            if (exists(select 1 from pxp.variable_global va where va.variable = 'obingresos_notidep'))  then
                v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Nuevo '|| v_parametros.tipo || 'registrado para la agencia ' || v_agencia,'El usuario ' || v_usuario || ' ha registrado un ajuste de tipo '
                            || v_parametros.tipo || ' para la agencia ' || v_agencia || ' por un monto de ' || v_parametros.monto || ' ' || v_moneda || ' . Ingrese al ERP para verificarlo',
                                pxp.f_get_variable_global('obingresos_notidep')));
            end if;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Movimientos almacenado(a) con exito (id_movimiento_entidad'||v_id_movimiento_entidad||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_movimiento_entidad',v_id_movimiento_entidad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_MOE_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		17-05-2017 15:53:35
	***********************************/

	elsif(p_transaccion='OBING_MOE_MOD')then

		begin
        	if (exists(select 1
            	from obingresos.tmovimiento_entidad me
                where me.id_movimiento_entidad =v_parametros.id_movimiento_entidad and id_periodo_venta is not null  ))then

            	raise exception 'No se puede modificar el movimiento de un preiodo cerrado';
            end if;
        	select m.codigo_internacional into v_moneda
            from param.tmoneda m
            where id_moneda = v_parametros.id_moneda;

            select a.nombre into v_agencia
            from obingresos.tagencia a
            where a.id_agencia = v_parametros.id_agencia;

        	select u.cuenta into v_usuario
            from segu.tusuario u
            where u.id_usuario = p_id_usuario;

			--Sentencia de la modificacion
			update obingresos.tmovimiento_entidad set
			id_moneda = v_parametros.id_moneda,
			id_periodo_venta = NULL,
			id_agencia = v_parametros.id_agencia,
			garantia = v_parametros.garantia,
			monto_total = v_parametros.monto,
			tipo = v_parametros.tipo,
			autorizacion__nro_deposito = v_parametros.autorizacion__nro_deposito,
			monto = v_parametros.monto,
			ajuste = 'si',
			fecha = v_parametros.fecha::date,
			pnr = NULL,
			apellido = NULL,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,

            fk_id_movimiento_entidad = v_parametros.fk_id_movimiento_entidad

			where id_movimiento_entidad=v_parametros.id_movimiento_entidad;

            if (exists(select 1 from pxp.variable_global va where va.variable = 'obingresos_notidep'))  then
                v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Modificacion a movimiento de tipo '|| v_parametros.tipo || ' para la agencia ' || v_agencia,'El usuario ' || v_usuario || ' ha modificado un '
                            || v_parametros.tipo || ' de la agencia ' || v_agencia || ' por un monto de ' || v_parametros.monto || ' ' || v_moneda || ' . Ingrese al ERP para verificarlo',
                                pxp.f_get_variable_global('obingresos_notidep')));
            end if;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Movimientos modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_movimiento_entidad',v_parametros.id_movimiento_entidad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_MOE_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		17-05-2017 15:53:35
	***********************************/

	elsif(p_transaccion='OBING_MOE_ELI')then

		begin
        	if (exists(select 1
            	from obingresos.tmovimiento_entidad me
                where me.id_movimiento_entidad =v_parametros.id_movimiento_entidad and id_periodo_venta is not null  ))then

            	raise exception 'No se puede modificar el movimiento de un periodo cerrado';
            end if;
			--Sentencia de la eliminacion
			delete from obingresos.tmovimiento_entidad
            where id_movimiento_entidad=v_parametros.id_movimiento_entidad;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Movimientos eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_movimiento_entidad',v_parametros.id_movimiento_entidad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
    /*********************************
 	#TRANSACCION:  'OBING_ANUAUTO_MOD'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		17-05-2017 15:53:35
	***********************************/

	elsif(p_transaccion='OBING_ANUAUTO_MOD')then

		begin
        	select id_movimiento_entidad, id_periodo_venta into v_id_movimiento_entidad, v_id_periodo_venta
            from obingresos.tmovimiento_entidad me
            where pnr is not null and estado_reg = 'activo'
            				and autorizacion__nro_deposito = v_parametros.autorizacion;

        	if (v_id_movimiento_entidad is null) then
            	raise exception 'No se encontro un pnr activo con el numero de autorizacion indicado';
            end if;

            if (v_id_periodo_venta is not null) then
            	raise exception 'No es posible anular el pnr/tkt debido a que el mismo se encuentra en un periodo cerrado';
            end if;

            if (pxp.f_existe_parametro(p_tabla,'billete')) then
            	if (v_parametros.billete is not null) then
                	if (not exists (
                    	select 1
                        from obingresos.tdetalle_boletos_web dbw
                    	where dbw.billete = v_parametros.billete and void = 'no' ))then
                    	raise exception 'El boleto %, no ha sido reportado por el Portal corporativo o ya ha sido anulado',v_parametros.billete;
                    end if;



                    --raise exception 'llega%,%',v_id_movimiento_entidad,v_parametros.billete;
                	update obingresos.tmovimiento_entidad
                    set monto = (case when comision_terciarizada is null or comision_terciarizada = 0 then
                    				monto - (dbw.importe - dbw.comision)
                    			else
                                	monto - dbw.importe
                                END),
                    monto_total = monto_total - dbw.importe,
                    fecha_mod = now(),
                    neto = obingresos.tmovimiento_entidad.neto - dbw.neto,
                    comision_terciarizada =
                    			(case when comision_terciarizada is null or comision_terciarizada = 0 then
                    				NULL
                    			else
                                	comision_terciarizada - dbw.comision
                                END),
                    id_usuario_mod = p_id_usuario
                    from obingresos.tdetalle_boletos_web dbw
                    where dbw.billete = v_parametros.billete and id_movimiento_entidad = v_id_movimiento_entidad
                    returning monto into v_monto;


                    update obingresos.tdetalle_boletos_web
                    set void = 'si'
                    where billete = v_parametros.billete;

                    --si el saldo es 0 inactivar el movimiento
                    if (v_monto <= 0) then
                    	update obingresos.tmovimiento_entidad
                        set estado_reg = 'inactivo'
                        where id_movimiento_entidad = v_id_movimiento_entidad;
                    end if;
                end if;
            end if;

            if (not pxp.f_existe_parametro(p_tabla,'billete')) then
            	--Sentencia de la anulacion
                update obingresos.tmovimiento_entidad
                set estado_reg = 'inactivo',
                fecha_mod = now(),
                id_usuario_mod = p_id_usuario
                where id_movimiento_entidad = v_id_movimiento_entidad;
            end if;






            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Movimientos anulado(a) o modificado');
            v_resp = pxp.f_agrega_clave(v_resp,'id_movimiento_entidad',v_id_movimiento_entidad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

        /*********************************
 	#TRANSACCION:  'OBING_ARRASTRAR_IME'
 	#DESCRIPCION:	Arrastre de Saldo
 	#AUTOR:		ivaldivia
 	#FECHA:		21-10-2019 11:30:35
	***********************************/

	elsif(p_transaccion='OBING_ARRASTRAR_IME')then

		begin
        	select
            MAX (distinct me.id_periodo_venta::integer) into v_periodo_maximo
            from obingresos.tmovimiento_entidad me
            where me.id_agencia = v_parametros.id_agencia and me.estado_reg = 'activo' and me.cierre_periodo = 'si';


            SELECT count(me.id_movimiento_entidad) into v_existencia
            from obingresos.tmovimiento_entidad me
            where me.id_agencia = v_parametros.id_agencia and me.id_periodo_venta = v_periodo_maximo and me.tipo = 'credito' and me.estado_reg = 'activo'
            and me.cierre_periodo = 'si';

            if (v_existencia > 0) then
                    SELECT me.monto,
                    	   me.fecha,
                           me.id_moneda into
                           v_monto_arrastre,
                           v_fecha_arrastre,
                           v_id_moneda
                    from obingresos.tmovimiento_entidad me
                    where me.id_agencia = v_parametros.id_agencia and me.id_periodo_venta = v_periodo_maximo and me.tipo = 'credito' and me.estado_reg = 'activo'
                    and me.cierre_periodo = 'si';
            end if;


            IF (v_monto_arrastre > 0) then
            	insert into obingresos.tmovimiento_entidad(
                            id_usuario_reg,
                            fecha_reg,
                            estado_reg,
                            tipo,
                            fecha,
                            monto,
                            id_moneda,
                            garantia,
                            ajuste,
                            id_agencia,
                            monto_total,
                            id_periodo_venta,
                            cierre_periodo
                            ) values(
                            p_id_usuario,
                            now(),
                            'activo',
                            'debito',
                            v_fecha_arrastre,
                            v_monto_arrastre::numeric,
                            v_id_moneda::integer,
                            'no',
                            'no',
                            v_parametros.id_agencia::integer,
                            v_monto_arrastre::numeric,
                            v_periodo_maximo,
                            'si'
                );

                insert into obingresos.tmovimiento_entidad(
                            id_usuario_reg,
                            fecha_reg,
                            estado_reg,
                            tipo,
                            fecha,
                            monto,
                            id_moneda,
                            garantia,
                            ajuste,
                            id_agencia,
                            monto_total,
                            cierre_periodo
                            ) values(
                            p_id_usuario,
                            now(),
                            'activo',
                            'credito',
                            v_fecha_arrastre,
                            v_monto_arrastre::numeric,
                            v_id_moneda::integer,
                            'no',
                            'no',
                            v_parametros.id_agencia::integer,
                            v_monto_arrastre::numeric,
                            'si'
                )RETURNING id_movimiento_entidad into v_id_movimiento_entidad;

            else

            	Raise exception 'El saldo anterior de la agencia es negativo, no se pudo Arrastrar el saldo';

            end if;



            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Movimientos anulado(a) o modificado');
            v_resp = pxp.f_agrega_clave(v_resp,'id_movimiento_entidad',v_id_movimiento_entidad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;


        /*********************************
 	#TRANSACCION:  'OBING_VERISALDO_IME'
 	#DESCRIPCION:	Verificacion de saldo
 	#AUTOR:		ivaldivia
 	#FECHA:		21-10-2019 15:33:35
	***********************************/

	elsif(p_transaccion='OBING_VERISALDO_IME')then

		begin

        	/*Aumentando dato para no arrastrar el saldo (Ismael Valdivia 23/11/2020)*/
            select count (mo.id_movimiento_entidad)
            		into
                    v_existencia_vigente
            from obingresos.tmovimiento_entidad mo
            where mo.id_agencia = v_parametros.id_agencia  and mo.garantia = 'no' and mo.id_periodo_venta is null and
            mo.estado_reg = 'activo' and mo.cierre_periodo = 'si' and mo.tipo = 'credito';
        	/*************************************************************************/

            if (v_existencia_vigente = 0) then

        	select
            MAX (distinct me.id_periodo_venta::integer) into v_periodo_maximo
            from obingresos.tmovimiento_entidad me
            where me.id_agencia = v_parametros.id_agencia and me.estado_reg = 'activo' and me.cierre_periodo = 'si' and me.garantia = 'no';


            SELECT count(me.id_movimiento_entidad) into v_existencia
            from obingresos.tmovimiento_entidad me
            where me.id_agencia = v_parametros.id_agencia and me.id_periodo_venta = v_periodo_maximo and me.tipo = 'credito' and me.estado_reg = 'activo'
            and me.cierre_periodo = 'si';

                if (v_existencia > 0) then
                        SELECT me.monto,
                               me.fecha,
                               me.id_moneda into
                               v_monto_arrastre,
                               v_fecha_arrastre,
                               v_id_moneda
                        from obingresos.tmovimiento_entidad me
                        where me.id_agencia = v_parametros.id_agencia and me.id_periodo_venta = v_periodo_maximo and me.tipo = 'credito' and me.estado_reg = 'activo'
                        and me.cierre_periodo = 'si';
                end if;

            end if;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Movimientos anulado(a) o modificado');
            v_resp = pxp.f_agrega_clave(v_resp,'id_movimiento_entidad',v_id_movimiento_entidad::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'v_monto_arrastre',v_monto_arrastre::varchar);

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

ALTER FUNCTION obingresos.ft_movimiento_entidad_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
