CREATE OR REPLACE FUNCTION obingresos.ft_boleto_retweb_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_boleto_retweb_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tboleto'
 AUTOR: 		 (jrivera)
 FECHA:	        06-01-2016 22:42:25
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
	v_id_boleto_retweb	integer;
    v_id_agencia			integer;
    v_fecha					date;
    v_id_moneda				integer;
    v_registros				record;


BEGIN

    v_nombre_funcion = 'obingresos.ft_boleto_retweb_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_BOL_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	if(p_transaccion='OBING_BOLRW_INS')then

        begin

        	for v_registros in (select *
            					from json_populate_recordset(null::obingresos.detalle_boletos_ret,v_parametros.detalle::json))loop

                select id_moneda into v_id_moneda
                from param.tmoneda m
                where m.codigo_internacional = v_registros.moneda;

                select id_agencia into v_id_agencia
                from obingresos.tagencia a
                where a.codigo_int = v_registros.officeid;

                INSERT INTO
                    obingresos.tboleto_retweb
                  (
                    id_usuario_reg,
                    nro_boleto,
                    pasajero,
                    fecha_emision,
                    total,
                    moneda,
                    estado,
                    id_moneda,
                    comision,
                    pnr,
                    id_agencia,
                    forma_pago,
                    neto

                  )
                  VALUES (
                    p_id_usuario,
                    v_registros.billete,
                    v_registros.pasajero,
                    v_registros.fecha_emision,
                    v_registros.importe_total,
                    v_registros.moneda,
                    '1',
                    v_id_moneda,
                    v_registros.comision,
                    v_registros.pnr,
                    v_id_agencia,
                    v_registros.forma_pago,
                    v_registros.importe_neto
                  );
            end loop;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos retweb almacenados con exito para la fecha '||v_parametros.fecha_emision::varchar||')');
            v_resp = pxp.f_agrega_clave(v_resp,'fecha_emision',v_parametros.fecha_emision::varchar);

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