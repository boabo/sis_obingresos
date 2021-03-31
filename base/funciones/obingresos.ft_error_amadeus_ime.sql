CREATE OR REPLACE FUNCTION obingresos.ft_error_amadeus_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_error_amadeus_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tmco_s'
 AUTOR: 		 (Ismael Valdivia)
 FECHA:	        30-03-2021 09:40:04
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				30-03-2021 09:40:04								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tmco_s'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
    v_num_error				numeric;
    v_actulizar_automatico	varchar;
BEGIN

    v_nombre_funcion = 'obingresos.ft_error_amadeus_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_ERR_AMA_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		30-03-2021 09:40:04
	***********************************/

	if(p_transaccion='OBING_ERR_AMA_INS')then

        begin


        	update obingresos.terror_amadeus set
			nro_errores = (nro_errores + 1);

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Error Actualizado');

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_VERI_AMA_INS'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		30-03-2021 09:40:04
	***********************************/

	elsif(p_transaccion='OBING_VERI_AMA_INS')then

		begin

		 	select err.nro_errores
                    INTO
                    v_num_error
            from obingresos.terror_amadeus err;


            --v_actulizar_automatico = pxp.f_get_variable_global('traida_boletos_amadeus_automatico');




			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Actualizado');
            v_resp = pxp.f_agrega_clave(v_resp,'error',v_num_error::varchar);
            --v_resp = pxp.f_agrega_clave(v_resp,'automatico',v_actulizar_automatico::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;


    /*********************************
 	#TRANSACCION:  'OBING_VG_UPD_INS'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		30-03-2021 09:40:04
	***********************************/

	elsif(p_transaccion='OBING_VG_UPD_INS')then

		begin


            v_actulizar_automatico = pxp.f_get_variable_global('traida_boletos_amadeus_automatico');

            if (v_actulizar_automatico = 'si') then

              update obingresos.terror_amadeus set
              nro_errores = 0;

            end if;


			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Actualizado');
            v_resp = pxp.f_agrega_clave(v_resp,'automatico',v_actulizar_automatico::varchar);

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

ALTER FUNCTION obingresos.ft_error_amadeus_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
