CREATE OR REPLACE FUNCTION obingresos.ft_recuperar_datos_voucher_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_recuperar_datos_voucher_sel
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tforma_pago'
 AUTOR: 		 (ivaldivia)
 FECHA:	        19-12-2019 18:00:00
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION: SERVICIO PARA RECUPERAR LOS DATOS DEL VOUCHER CANJEADO
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

    v_consulta    		varchar;
    v_parametros  		record;
    v_nombre_funcion   	text;
    v_resp				varchar;

	v_mensaje_error         text;
	v_record				record;
	v_respuesta 			varchar;
    v_json_result			varchar;
    v_existencia			integer;

BEGIN

    v_nombre_funcion = 'obingresos.ft_recuperar_datos_voucher_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_RECUVOUCH_SEL'
 	#DESCRIPCION:	CONSULTA DE REGISTRO
 	#AUTOR:		ivaldivia
 	#FECHA:		19-12-2019 16:05:00
	***********************************/

	if(p_transaccion='OBING_RECUVOUCH_SEL')then

        begin
        	select count (*) into v_existencia
            from (select  'Counter'::varchar as responsable,
                    fre.nro_boleto::varchar,
                    fre.pnr::varchar,
                    fre.status::varchar,
                    fre.status_canjeado::varchar,
                    fre.message::varchar,
                    fre.message_canjeado::varchar,
                    fre.ffid::integer,
                    fre.voucher_code::varchar,
                    per.nombre_completo2::varchar
                    from obingresos.tconsulta_viajero_frecuente fre
                    inner join segu.tusuario usu on usu.id_usuario = fre.id_usuario_reg
                    inner join segu.vpersona2 per on per.id_persona = usu.id_persona
                    where fre.voucher_code = v_parametros.voucher_code

            UNION

            select  'Cajero'::varchar as responsable,
                    via.ticket_number::varchar,
                    via.pnr::varchar,
                    via.status::varchar,
                    ''::varchar as status_canjeado,
                    via.mensaje::varchar,
                    ''::varchar as  mensaje_canjeado,
                    via.ffid::integer,
                    via.voucher_code::varchar,
                    per.nombre_completo2::varchar
            from obingresos.tviajero_frecuente via
            inner join segu.tusuario usu on usu.id_usuario = via.id_usuario_reg
            inner join segu.vpersona2 per on per.id_persona = usu.id_persona
            where via.voucher_code = v_parametros.voucher_code) as datos;


            if (v_existencia > 0 ) then

        	v_consulta = '
             (select  ''Counter''::varchar as responsable,
                    fre.nro_boleto::varchar,
                    fre.pnr::varchar,
                    fre.status::varchar,
                    fre.status_canjeado::varchar,
                    fre.message::varchar,
                    fre.message_canjeado::varchar,
                    fre.ffid::integer,
                    fre.voucher_code::varchar,
                    per.nombre_completo2::varchar
                    from obingresos.tconsulta_viajero_frecuente fre
                    inner join segu.tusuario usu on usu.id_usuario = fre.id_usuario_reg
                    inner join segu.vpersona2 per on per.id_persona = usu.id_persona
                    where fre.voucher_code = '''||v_parametros.voucher_code||'''

            UNION

            select  ''Cajero''::varchar as responsable,
                    via.ticket_number::varchar,
                    via.pnr::varchar,
                    via.status::varchar,
                    ''''::varchar as status_canjeado,
                    via.mensaje::varchar,
                    ''''::varchar as  mensaje_canjeado,
                    via.ffid::integer,
                    via.voucher_code::varchar,
                    per.nombre_completo2::varchar
            from obingresos.tviajero_frecuente via
            inner join segu.tusuario usu on usu.id_usuario = via.id_usuario_reg
            inner join segu.vpersona2 per on per.id_persona = usu.id_persona
            where via.voucher_code = '''||v_parametros.voucher_code||''')';

            else

            v_consulta = '
             select  	''''::varchar as responsable,
                        ''''::varchar as nro_boleto,
                        ''''::varchar as pnr,
                        ''''::varchar as status,
                        ''No consultado ni canjeado por el counter o el cajero''::varchar as status_canjeado,
                        ''No consultado ni canjeado por el counter o el cajero''::varchar as message,
                        ''No consultado ni canjeado por el counter o el cajero''::varchar as message_canjeado,
                        NULL::integer as ffid,
                        ''''::varchar as voucher_code,
                        ''''::varchar as nombre_completo2
                        ';

            end if;
			raise notice 'llega aqui la conulsta %',v_consulta;
            return v_consulta;


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
