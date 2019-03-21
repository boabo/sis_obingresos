CREATE OR REPLACE FUNCTION obingresos.ft_archivo_acm_det_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_archivo_acm_det_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tarchivo_acm_det'
 AUTOR: 		 RZABALA
 FECHA:	        05-09-2018 20:36:49
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				05-09-2018 20:36:49								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tarchivo_acm_det'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_archivo_acm_det	integer;
    v_id_agencia			integer;

BEGIN

    v_nombre_funcion = 'obingresos.ft_archivo_acm_det_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_AAD_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		RZABALA
 	#FECHA:		05-09-2018 20:36:49
	***********************************/

	if(p_transaccion='OBING_AAD_INS')then

        begin

        	select ag.id_agencia
            into v_id_agencia
            from obingresos.tagencia ag
            where ag.codigo_int=v_parametros.officce_id
            and ag.estado_reg='activo';

            if v_id_agencia is null then
            	raise exception 'El Office ID: %, no esta registrado en la base de datos, revise el codigo.',v_id_agencia;
            end if;


            --validar porcenajes permitidos



        	--Sentencia de la insercion
        	insert into obingresos.tarchivo_acm_det(
			id_archivo_acm,
			importe_total_mt,
			estado_reg,
			porcentaje,
			importe_total_mb,
			id_agencia,
			officce_id,
			usuario_ai,
			fecha_reg,
			id_usuario_reg,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			v_parametros.id_archivo_acm,
			v_parametros.importe_total_mt,
			'activo',
			v_parametros.porcentaje,
			v_parametros.importe_total_mb,
			v_id_agencia,
			v_parametros.officce_id,
			v_parametros._nombre_usuario_ai,
			now(),
			p_id_usuario,
			v_parametros._id_usuario_ai,
			null,
			null



			)RETURNING id_archivo_acm_det into v_id_archivo_acm_det;
            update obingresos.tarchivo_acm ac set
            estado = 'cargado'
            where ac.id_archivo_acm = v_parametros.id_archivo_acm;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Archivo ACM Detalle almacenado(a) con exito (id_archivo_acm_det'||v_id_archivo_acm_det||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_archivo_acm_det',v_id_archivo_acm_det::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_AAD_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		RZABALA
 	#FECHA:		05-09-2018 20:36:49
	***********************************/

	elsif(p_transaccion='OBING_AAD_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tarchivo_acm_det set
			id_archivo_acm = v_parametros.id_archivo_acm,
			importe_total_mt = v_parametros.importe_total_mt,
			porcentaje = v_parametros.porcentaje,
			importe_total_mb = v_parametros.importe_total_mb,
			id_agencia = v_parametros.id_agencia,
			officce_id = v_parametros.officce_id,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
            neto_total_mt = v_parametros.neto_total_mt,
            cant_bol_mt = v_parametros.cant_bol_mt,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_archivo_acm_det=v_parametros.id_archivo_acm_det;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Archivo ACM Detalle modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_archivo_acm_det',v_parametros.id_archivo_acm_det::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_AAD_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		RZABALA
 	#FECHA:		05-09-2018 20:36:49
	***********************************/

	elsif(p_transaccion='OBING_AAD_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tarchivo_acm_det
            where id_archivo_acm_det=v_parametros.id_archivo_acm_det;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Archivo ACM Detalle eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_archivo_acm_det',v_parametros.id_archivo_acm_det::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
/*********************************
 	#TRANSACCION:  'OBING_AA_ELI'
 	#DESCRIPCION:	Eliminacion de registros Det Acm
 	#AUTOR:		RZABALA
 	#FECHA:		05-09-2018 20:36:49
	***********************************/

	elsif(p_transaccion='OBING_AA_ELI')then

		begin
        --raise exception 'llega bd %';
        	update obingresos.tarchivo_acm ac set
            estado = 'borrador'
            where ac.id_archivo_acm = v_parametros.id_archivo_acm;
			--Sentencia de la eliminacion
			delete from obingresos.tarchivo_acm_det
            where id_archivo_acm=v_parametros.id_archivo_acm;
            --and id_archivo_acm_det=v_parametros.id_archivo_acm_det;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Archivo ACM Detalle eliminado(a)');
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