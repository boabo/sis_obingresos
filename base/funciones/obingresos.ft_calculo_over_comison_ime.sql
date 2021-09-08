CREATE OR REPLACE FUNCTION obingresos.ft_calculo_over_comison_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_calculo_over_comison_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.ft_calculo_over_comison_ime'
 AUTOR: 		(franklin.espinoza)
 FECHA:	        31-07-2021
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_parametros           	record;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;

	v_id_agencia_excluida	integer;

BEGIN

    v_nombre_funcion = 'obingresos.ft_calculo_over_comison_ime';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
 	#TRANSACCION:  'OBING_REG_EXC_AGE'
 	#DESCRIPCION:	Inserta Agencias Excluidas para un periodo
 	#AUTOR		:	franklin.espinoza
 	#FECHA		:	31-07-2021
	***********************************/

	if(p_transaccion='OBING_REG_EXC_AGE')then

		begin

        	 insert into obingresos.tagencia_excluida(
                  	id_usuario_reg,
                    estado_reg,
                    fecha_reg,

                    id_acm_key,
                    iata_code,
                    office_id,
                    fecha_desde,
                    fecha_hasta,
                    observacion
                  ) values(
                  	p_id_usuario,
                    'activo',
                    now(),

                    v_parametros.id_acm_key,
                    v_parametros.iata_code,
                    v_parametros.office_id,
                    v_parametros.fecha_desde,
                    v_parametros.fecha_hasta,
                    v_parametros.observacion
                  )RETURNING id_agencia_excluida into v_id_agencia_excluida;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Registro creado exitosamente');
            v_resp = pxp.f_agrega_clave(v_resp,'id_agencia_excluida',v_id_agencia_excluida::varchar);

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