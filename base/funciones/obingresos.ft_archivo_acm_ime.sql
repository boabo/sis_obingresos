CREATE OR REPLACE FUNCTION obingresos.ft_archivo_acm_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_archivo_acm_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tarchivo_acm'
 AUTOR: 		 (RZABALA)
 FECHA:	        05-09-2018 20:09:45
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				05-09-2018 20:09:45								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tarchivo_acm'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_archivo_acm		integer;
    v_last_ini				date;
    v_last_fin				date;

	v_id_movimiento_entidad	integer;
    v_codigo_autorizacion	varchar;
    v_id_periodo_venta		integer;
    v_monto					numeric;
    v_id_alarma				integer;
    v_moneda				varchar;
    v_agencia				varchar;
    v_usuario				varchar;

    v_arreglo				INTEGER[];
    v_length				integer;
    v_boletos_bs			record;
    v_correlativo			varchar;
    v_suma_importe 			FLOAT;
    v_suma_total			record;
    v_boletos_sus			record;
    v_ultimo_numero			integer;
    v_resetear				integer;
    v_registros				record;
	v_movimiento			integer;
    v_movimiento_enti		record;
    v_periodo				integer;
    v_porcentaje			integer;
    v_bloqueado				varchar;

BEGIN

    v_nombre_funcion = 'obingresos.ft_archivo_acm_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_taa_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		RZABALA
 	#FECHA:		05-09-2018 20:09:45
	***********************************/

	if(p_transaccion='OBING_taa_INS')then

        begin
        if v_parametros.fecha_fin <= v_parametros.fecha_ini then
        	raise exception 'Fecha Fin debe ser mayor a la Fecha Inicio.';
        end if;
        v_last_ini = (SELECT max(ac.fecha_ini)
						FROM obingresos.tarchivo_acm ac);
        v_last_fin = (SELECT max(ac.fecha_fin)
						FROM obingresos.tarchivo_acm ac);
         if (exists(select 1
            	from obingresos.tarchivo_acm ac
                where v_parametros.fecha_ini between v_last_ini and v_last_fin  ))then

            	raise exception 'En el rango de fechas, ya existen Acm Generados!!';
            end if;
         if v_parametros.fecha_ini < v_last_ini and v_parametros.fecha_fin < v_last_fin THEN
         	raise exception 'NO SE PUEDEN INGRESAR FECHAS POSTERIORES AL ULTIMO REGISTRO';
         end if;
         --raise exception 'llega,%,%',v_last_ini,v_last_fin;
        	--Sentencia de la insercion
        	insert into obingresos.tarchivo_acm(
			estado_reg,
			fecha_fin,
			nombre,
			fecha_ini,
			usuario_ai,
			fecha_reg,
			id_usuario_reg,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod,
            estado
          	) values(
			'activo',
			v_parametros.fecha_fin,
			v_parametros.nombre,
			v_parametros.fecha_ini,
			v_parametros._nombre_usuario_ai,
			now(),
			p_id_usuario,
			v_parametros._id_usuario_ai,
			null,
			null,
            'borrador'



			)RETURNING id_archivo_acm into v_id_archivo_acm;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Archivo ACM almacenado(a) con exito (id_archivo_acm'||v_id_archivo_acm||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_archivo_acm',v_id_archivo_acm::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_taa_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		RZABALA
 	#FECHA:		05-09-2018 20:09:45
	***********************************/

	elsif(p_transaccion='OBING_taa_MOD')then

		begin
        if v_parametros.fecha_fin <= v_parametros.fecha_ini then
        	raise exception 'Fecha Fin debe ser mayor a la Fecha Inicio.';
        end if;
			--Sentencia de la modificacion
			update obingresos.tarchivo_acm set
			fecha_fin = v_parametros.fecha_fin,
			nombre = v_parametros.nombre,
			fecha_ini = v_parametros.fecha_ini,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
            --estado = 'cargado',
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_archivo_acm=v_parametros.id_archivo_acm;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Archivo ACM modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_archivo_acm',v_parametros.id_archivo_acm::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
        /*********************************
 	#TRANSACCION:  'OBING_taa_habilitar'
 	#DESCRIPCION:	habilita la opcion de validar
 	#AUTOR:		RZABALA
 	#FECHA:		27-09-2018 17:09:45
	***********************************/

	elsif(p_transaccion='OBING_taa_habilitar')then

		begin

			--Sentencia para actualizar estado
			update obingresos.tarchivo_acm set
			id_usuario_mod = p_id_usuario,
            estado = 'validado',
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_archivo_acm=v_parametros.id_archivo_acm;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Estado Archivo ACM modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_archivo_acm',v_parametros.id_archivo_acm::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
 /*********************************
 	#TRANSACCION:  'OBING_VALIDACION_INS'
 	#DESCRIPCION:	Insercion de validacion ACM
 	#AUTOR:		ivaldivia/rzabala
 	#FECHA:		19-09-2018 11:34:32
	***********************************/

	elsif(p_transaccion='OBING_VALIDACION_INS')then



        begin

        v_arreglo = string_to_array(v_parametros.id_archivo_acm,',');
                      v_length = array_length(v_arreglo,1);

        /*update obingresos.tarchivo_acm ac set
            estado = 'finalizado'
            where ac.id_archivo_acm = v_parametros.id_archivo_acm::integer;*/

      -- raise exception 'El ID del Archivo ACM es: %', v_parametros.id_archivo_acm;



FOR 	v_registros in (select 	arch.id_archivo_acm_det, arch.id_agencia, acm.importe, acm.numero, acm.id_moneda
                        from obingresos.tarchivo_acm_det arch
                        inner join obingresos.tarchivo_acm ar on ar.id_archivo_acm = arch.id_archivo_acm
                        inner join obingresos.tacm acm on acm.id_archivo_acm_det = arch.id_archivo_acm_det
                        where ar.id_archivo_acm = v_parametros.id_archivo_acm::integer) LOOP
 /*if v_registros.importe is null then
 raise exception 'No se tienen Registros de Importe para realizar la Validacion, Registros: %', v_registros.importe;
else */
select
                          archivo.porcentaje
                          into v_porcentaje
                          from obingresos.tarchivo_acm_det archivo
                         where archivo.id_archivo_acm_det= v_registros.id_archivo_acm_det;
select
                          age.bloquear_emision
                          into v_bloqueado
                          from obingresos.tagencia age
                          inner join obingresos.tarchivo_acm_det adet on adet.id_agencia = age.id_agencia
                         where adet.id_archivo_acm_det= v_registros.id_archivo_acm_det;


			if v_bloqueado = 'no' then
            if (v_porcentaje = 2 or v_porcentaje = 4) then
             ---Sentencia de la insercion
        	insert into obingresos.tmovimiento_entidad(
			id_moneda,
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
			id_usuario_mod
          	) values(
			v_registros.id_moneda,
			v_registros.id_agencia,
			'no',
			v_registros.importe,
			'credito',
			v_registros.numero,
			'activo',
			v_registros.importe,
			'no',
			now(),
			NULL,
			NULL,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			v_parametros._id_usuario_ai,
			null,
			null



			)RETURNING id_movimiento_entidad into v_id_movimiento_entidad;


        	select mov.id_movimiento_entidad
            into v_movimiento
            from obingresos.tmovimiento_entidad mov
            inner join obingresos.tacm acm on acm.numero = mov.autorizacion__nro_deposito
            where acm.numero = v_registros.numero;

			--raise exception 'El ID del Movimiento es: %', v_movimiento;

            UPDATE obingresos.tacm acm set
            id_movimiento_entidad = v_movimiento
            where acm.numero = v_registros.numero;
            ELSE
            raise exception 'El porcentaje es: %  solo se pueden realizar calculos con los porcentajes (2 y 4) ',v_porcentaje ;
            end if;
             --else
           -- raise notice 'no se puede realizar el abono porque no se encuentra habilitada la agencia',v_registros.id_agencia;
          	end if;


 end loop;
			update obingresos.tarchivo_acm ac set
            estado = 'finalizado'
            where ac.id_archivo_acm = v_parametros.id_archivo_acm::integer;



			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','ACM almacenado(a) con exito (id_movimiento_entidad'||v_id_movimiento_entidad||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_movimiento_entidad',v_id_movimiento_entidad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;


 /*********************************
 	#TRANSACCION:  'OBING_VALILIMPIO_ELI'
 	#DESCRIPCION:	Eliminacion de ACM Validado
 	#AUTOR:		ivaldivia/rzabala
 	#FECHA:		19-09-2018 11:34:32
	***********************************/

	elsif(p_transaccion='OBING_VALILIMPIO_ELI')then



        begin

        --v_arreglo = string_to_array(v_parametros.id_archivo_acm,',');
                      v_length = array_length(v_arreglo,1);



      --raise exception 'El ID del Archivo ACM es: %', v_parametros.id_archivo_acm;


FOR 	v_registros in (select 	arch.id_agencia, acm.importe, acm.numero, acm.id_moneda
                        from obingresos.tarchivo_acm_det arch
                        inner join obingresos.tarchivo_acm ar on ar.id_archivo_acm = arch.id_archivo_acm
                        left join obingresos.tacm acm on acm.id_archivo_acm_det = arch.id_archivo_acm_det
                        where ar.id_archivo_acm = v_parametros.id_archivo_acm::integer) LOOP

			--raise exception 'El ID del Archivo ACM es: %', v_parametros.id_archivo_acm;

			select mov.id_movimiento_entidad
            into v_movimiento
            from obingresos.tmovimiento_entidad mov
            inner join obingresos.tacm acm on acm.numero = mov.autorizacion__nro_deposito
            where acm.numero = v_registros.numero;

			--raise exception 'El ID del Movimiento es: %', v_movimiento;

            UPDATE obingresos.tacm acm set
            id_movimiento_entidad = null
            where acm.numero = v_registros.numero;

			update obingresos.tarchivo_acm ac set
            estado = 'generado'
            where ac.id_archivo_acm = v_parametros.id_archivo_acm;

			--raise exception 'El ID del Archivo ACM es: %', v_registros.numero;

            select mov.id_periodo_venta
            into v_periodo
            from obingresos.tmovimiento_entidad mov
            inner join obingresos.tacm acm on acm.numero = mov.autorizacion__nro_deposito
            where acm.numero = v_registros.numero;


		  if v_periodo is not null then
          raise exception 'No se puede Eliminar el registro %', v_registros.numero ||'porque ya fue validado';
          end if;

           delete from obingresos.tmovimiento_entidad ent
           where ent.autorizacion__nro_deposito=v_registros.numero;



 end loop;




			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','ACM almacenado(a) con exito (id_movimiento_entidad'||v_id_movimiento_entidad||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_movimiento_entidad',v_id_movimiento_entidad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_taa_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		RZABALA
 	#FECHA:		05-09-2018 20:09:45
	***********************************/

	elsif(p_transaccion='OBING_taa_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tarchivo_acm
            where id_archivo_acm=v_parametros.id_archivo_acm;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Archivo ACM eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_archivo_acm',v_parametros.id_archivo_acm::varchar);

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