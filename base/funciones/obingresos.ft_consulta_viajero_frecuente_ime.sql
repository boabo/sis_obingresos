CREATE OR REPLACE FUNCTION obingresos.ft_consulta_viajero_frecuente_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_consulta_viajero_frecuente_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tconsulta_viajero_frecuente'
 AUTOR: 		 (miguel.mamani)
 FECHA:	        15-12-2017 14:59:25
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				15-12-2017 14:59:25								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tconsulta_viajero_frecuente'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_consulta_viajero_frecuente	integer;
    v_exist					numeric;
    v_existe				numeric;

    v_canjeado 				varchar;
    v_mensaje				varchar;
    v_encontrado			varchar;
    v_inexistente			varchar;
    v_expirado				varchar;
	v_consumido				varchar;

BEGIN

    v_nombre_funcion = 'obingresos.ft_consulta_viajero_frecuente_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_VIF_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		miguel.mamani
 	#FECHA:		15-12-2017 14:59:25
	***********************************/

	if(p_transaccion='OBING_VIF_INS')then

        begin
        --raise exception 'p_transaccion %', p_transaccion;
        IF(v_parametros.ffid != '' AND v_parametros.voucher_code !='')THEN
                    /*select count( v.id_consulta_viajero_frecuente)::numeric into v_existe
                    from obingresos.tconsulta_viajero_frecuente v
                    where v.voucher_code = 'OB.FF.VO'||v_parametros.voucher_code and v.ffid = v_parametros.ffid;
                    raise exception 'El numero % %',v_existe,v_parametros.ffidw;*/
            CASE

                WHEN (select 1
                	from obingresos.tconsulta_viajero_frecuente v
                    where v.voucher_code = 'OB.FF.VO'||v_parametros.voucher_code and v.ffid = v_parametros.ffid)
                    THEN v_existe = '1';
                ELSE v_existe = '0';
            END CASE;

            IF(v_existe = 1)
             THEN
            	raise exception 'El numero de voucher que intenta registrar ya existe.';
            ELSE
            --THEN
        	--Sentencia de la insercion
        	insert into obingresos.tconsulta_viajero_frecuente(
			ffid,
			estado_reg,
			message,
			voucher_code,
			status,
            --nro_boleto,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			id_usuario_ai,
			fecha_mod,
			id_usuario_mod,
            estado
          	) values(
			v_parametros.ffid,
			'activo',
			v_parametros.message,
			'OB.FF.VO'||v_parametros.voucher_code,
			v_parametros.status,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			v_parametros._id_usuario_ai,
			null,
			null,
            'Verificado'

			)RETURNING id_consulta_viajero_frecuente into v_id_consulta_viajero_frecuente;
            END IF;


			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','consulta viajero frecuente almacenado(a) con exito (id_consulta_viajero_frecuente'||v_id_consulta_viajero_frecuente||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_consulta_viajero_frecuente',v_id_consulta_viajero_frecuente::varchar);

            --Devuelve la respuesta
            return v_resp;
            ELSE
            raise exception 'los campos FFID / Voucher no pueden ser vacios.';
        --end if;
            end if;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_VIF_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		miguel.mamani
 	#FECHA:		15-12-2017 14:59:25
	***********************************/

	elsif(p_transaccion='OBING_VIF_MOD')then

		begin
          if (v_parametros.nro_boleto != '' AND v_parametros.pnr != '') then
                --select count( v.id_consulta_viajero_frecuente)::numeric into v_exist
                      --from obingresos.tconsulta_viajero_frecuente v
                     -- where v.nro_boleto = '930'||v_parametros.nro_boleto;
                      --raise exception 'El numero % %',            v_exist, v_parametros.nro_boleto;
                  CASE
                          WHEN (select 1
                              from obingresos.tconsulta_viajero_frecuente v
                              where v.nro_boleto = '930'||v_parametros.nro_boleto)
                          THEN v_exist = '1';

                          ELSE v_exist = '0';
                      END CASE;

                          IF(v_exist = 1)
                          THEN
                              raise exception 'El numero de boleto que intenta registrar ya se encuentra asociado con un voucher.';
                          ELSE

                          if (v_parametros.status = 'OK') then
                          	v_canjeado = 'Canjeado';
                          else
                          	v_canjeado = 'No Canjeado';
                          end if;


                          if (pxp.f_existe_parametro(p_tabla,'message')) then

                          	v_mensaje = v_parametros.message;

                          	SELECT replace(replace(regexp_matches(v_parametros.message::varchar, 'encontrado')::VARCHAR,'{',''),'}','')::varchar into v_encontrado;
                            SELECT replace(replace(regexp_matches(v_parametros.message::varchar, 'inexistente')::VARCHAR,'{',''),'}','')::varchar into v_inexistente;
                            SELECT replace(replace(regexp_matches(v_parametros.message::varchar, 'expirado')::VARCHAR,'{',''),'}','')::varchar into v_expirado;
                            SELECT replace(replace(regexp_matches(v_parametros.message::varchar, 'consumido')::VARCHAR,'{',''),'}','')::varchar into v_consumido;

                               if (v_encontrado <> '') then
                                v_canjeado = 'Encontrado';
                               end if;

                               if (v_inexistente <> '') then
                                v_canjeado = 'Inexistente';
                               end if;

                               if (v_expirado <> '') then
                                v_canjeado = 'Expirado';
                               end if;

                               if (v_consumido <> '') then
                                v_canjeado = 'Consumido';
                               end if;

                          else
                          	v_mensaje = 'Voucher Canjeado Existosamente';
                      	  end if;



                            update obingresos.tconsulta_viajero_frecuente set

                            /*Aumentando campos para el estado y mensaje al momento de canjear*/
                            status_canjeado = v_parametros.status,
                            message_canjeado = v_mensaje,
                            /*****************************************************************/

                            nro_boleto = '930'||v_parametros.nro_boleto,
                            pnr = upper(v_parametros.pnr),
                            fecha_mod = now(),
                            id_usuario_mod = p_id_usuario,
                            id_usuario_ai = v_parametros._id_usuario_ai,
                            usuario_ai = v_parametros._nombre_usuario_ai,
                            estado = v_canjeado
                            where id_consulta_viajero_frecuente=v_parametros.id_consulta_viajero_frecuente;


                      --ELSE raise exception 'cadena vacia';
                      end if;
              --Definicion de la respuesta
              v_resp = pxp.f_agrega_clave(v_resp,'mensaje','consulta viajero frecuente modificado(a)');
              v_resp = pxp.f_agrega_clave(v_resp,'id_consulta_viajero_frecuente',v_parametros.id_consulta_viajero_frecuente::varchar);

              --Devuelve la respuesta
              return v_resp;
              ELSE
              		raise exception 'El campo Boleto o PNR no pueden ser vacios';
           END IF;


		end;

	/*********************************
 	#TRANSACCION:  'OBING_VIF_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		miguel.mamani
 	#FECHA:		15-12-2017 14:59:25
	***********************************/

	elsif(p_transaccion='OBING_VIF_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tconsulta_viajero_frecuente
            where id_consulta_viajero_frecuente=v_parametros.id_consulta_viajero_frecuente;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','consulta viajero frecuente eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_consulta_viajero_frecuente',v_parametros.id_consulta_viajero_frecuente::varchar);

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

ALTER FUNCTION obingresos.ft_consulta_viajero_frecuente_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
