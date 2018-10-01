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
	v_id_archivo_acm	integer;
    v_last_ini			date;
    v_last_fin			date;

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