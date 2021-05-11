CREATE OR REPLACE FUNCTION obingresos.ft_establecimiento_punto_venta_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_establecimiento_punto_venta_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.testablecimiento_punto_venta'
 AUTOR: 		 (admin)
 FECHA:	        17-03-2021 11:14:41
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-03-2021 11:14:41								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.testablecimiento_punto_venta'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_establecimiento_punto_venta	integer;

BEGIN

    v_nombre_funcion = 'obingresos.ft_establecimiento_punto_venta_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_ESTPVEN_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin
 	#FECHA:		17-03-2021 11:14:41
	***********************************/

	if(p_transaccion='OBING_ESTPVEN_INS')then

        begin
        	--Sentencia de la insercion
        	insert into obingresos.testablecimiento_punto_venta(
			estado_reg,
			codigo_estable,
			nombre_estable,
			id_punto_venta,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod,
            tipo_estable,
            id_stage_pv,
            id_lugar
          	) values(
			'activo',
			v_parametros.codigo_estable,
			v_parametros.nombre_estable,
			v_parametros.id_punto_venta,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null,
			v_parametros.tipo_estable,
            v_parametros.id_stage_pv,
            v_parametros.id_lugar

			)RETURNING id_establecimiento_punto_venta into v_id_establecimiento_punto_venta;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Establecimiento Punto Venta almacenado(a) con exito (id_establecimiento_punto_venta'||v_id_establecimiento_punto_venta||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_establecimiento_punto_venta',v_id_establecimiento_punto_venta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_ESTPVEN_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin
 	#FECHA:		17-03-2021 11:14:41
	***********************************/

	elsif(p_transaccion='OBING_ESTPVEN_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.testablecimiento_punto_venta set
			codigo_estable = v_parametros.codigo_estable,
			nombre_estable = v_parametros.nombre_estable,
			id_punto_venta = v_parametros.id_punto_venta,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            tipo_estable = v_parametros.tipo_estable,
            id_stage_pv = v_parametros.id_stage_pv,
            id_lugar = v_parametros.id_lugar
			where id_establecimiento_punto_venta=v_parametros.id_establecimiento_punto_venta;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Establecimiento Punto Venta modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_establecimiento_punto_venta',v_parametros.id_establecimiento_punto_venta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_ESTPVEN_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin
 	#FECHA:		17-03-2021 11:14:41
	***********************************/

	elsif(p_transaccion='OBING_ESTPVEN_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.testablecimiento_punto_venta
            where id_establecimiento_punto_venta=v_parametros.id_establecimiento_punto_venta;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Establecimiento Punto Venta eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_establecimiento_punto_venta',v_parametros.id_establecimiento_punto_venta::varchar);

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

ALTER FUNCTION obingresos.ft_establecimiento_punto_venta_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;