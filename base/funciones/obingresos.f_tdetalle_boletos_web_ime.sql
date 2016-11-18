CREATE OR REPLACE FUNCTION obingresos.ft_detalle_boletos_web_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
  RETURNS varchar AS
  $body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_detalle_boletos_web_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tforma_pago'
 AUTOR: 		 (jrivera)
 FECHA:	        10-06-2016 20:37:45
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
	v_id_forma_pago	integer;
    v_fecha					date;
    v_fecha_text			text;
    v_registros				record;
    v_razon_social			varchar;
    v_nit					varchar;
    v_aux					varchar;

BEGIN

    v_nombre_funcion = 'obingresos.ft_detalle_boletos_web_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_DETBOWEB_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera
 	#FECHA:		10-06-2016 20:37:45
	***********************************/

	if(p_transaccion='OBING_DETBOWEB_INS')then

        begin
        	for v_registros in (select *
            					from json_populate_recordset(null::obingresos.detalle_boletos,v_parametros.detalle_boletos::json))loop

                v_aux = v_registros."endoso";

                if (v_aux is null or trim(both from v_aux, ' ') = '')THEN
                	v_razon_social = NULL;
                    v_nit = NULL;

                --separado por ;
                elsif (position(';' in v_aux) > 0) then
                	if (array_length(string_to_array(v_aux,';'),1) != 3) then
                    	--raise exception 'El campo endoso no esta bien definido para el boleto %',v_registros."Billete";
                    else
                    	v_nit = trim(both ' ' from split_part(v_aux,';',2));
                        v_razon_social = trim(both ' ' from split_part(v_aux,';',3));

                    end if;

                --separado por |
                elsif (position('|' in v_aux) > 0) then
                	if (array_length(string_to_array(v_aux,'|'),1) != 3) then
                    	--raise exception 'El campo endoso no esta bien definido para el boleto %',v_registros."Billete";
                    else
                    	v_nit = trim(both ' ' from split_part(v_aux,'|',2));
                        v_razon_social = trim(both ' ' from split_part(v_aux,'|',3));
                    end if;
                 --separado por espacio
                elsif (position(' ' in v_aux) > 0) then
                	if (array_length(string_to_array(v_aux,' '),1) < 3) then
                    	--raise exception 'El campo endoso no esta bien definido para el boleto %',v_registros."Billete";
                    else
                    	v_aux = substr(v_aux,position(' ' in v_aux) + 1);
                        v_nit = trim(both ' ' from split_part(v_aux,' ',1));
                        v_aux = substr(v_aux,position(' ' in v_aux) + 1);
                        v_razon_social = trim(both ' ' from v_aux);
                    end if;

                ELSE
                	raise exception 'No existe un caracter de separacion en el campo endoso para el boleto %',v_registros."Billete";
                end if;




                INSERT INTO
                  obingresos.tdetalle_boletos_web
                (
                  id_usuario_reg,
                  billete,
                  conjuncion,
                  medio_pago,
                  entidad_pago,
                  moneda,
                  importe,
                  endoso,
                  origen,
                  fecha,
                  nit,
                  razon_social
                )
                VALUES (
                  p_id_usuario,
                  v_registros."Billete",
                  v_registros."CNJ",
                  v_registros."MedioDePago",
                  v_registros."Entidad",
                  v_registros."Moneda",
                  v_registros."ImportePasaje",
                  v_registros."endoso",
                  'web',
                  to_date(v_parametros.fecha,'MM/DD/YYYY'),
                  v_nit,
                  v_razon_social
                );


            end loop;
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma Pago almacenado(a) con exito (id_forma_pago'||v_id_forma_pago||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_forma_pago',v_id_forma_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_BOWEBFEC_MOD'
 	#DESCRIPCION:	Obtiene la maxima fecha en la tabla detalle_boletos_web
 	#AUTOR:		jrivera
 	#FECHA:		10-06-2016 20:37:45
	***********************************/

	elsif(p_transaccion='OBING_BOWEBFEC_MOD')then

		begin
			select max(dbw.fecha) into v_fecha
            from obingresos.tdetalle_boletos_web dbw
            where origen = 'web';

            if (v_fecha is null) then
            	v_fecha = '05/10/2016'::date;
            else
            	v_fecha = v_fecha +  interval '1 day';
            end if;
            --raise exception '%',v_fecha;
            select pxp.list(to_char(i::date,'MM/DD/YYYY')) into v_fecha_text
            from generate_series(v_fecha,
  			now()::date - interval '1 day', '1 day'::interval) i;


			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Fecha maxima obtenida');
            v_resp = pxp.f_agrega_clave(v_resp,'fecha',v_fecha_text::varchar);

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