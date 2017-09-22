CREATE OR REPLACE FUNCTION obingresos.ft_boleto_amadeus_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_boleto_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tboleto'
 AUTOR: 		Gonzalo Sarmiento Sejas
 FECHA:	        10-09-2017
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
	v_id_boleto	integer;
    v_id_agencia			integer;
    v_fecha					date;
    v_id_moneda				integer;
    v_id_lugar_sucursal		integer;
    v_id_lugar_pais			integer;
    v_registros				record;
    v_id_impuesto			integer;
    v_tipdoc				varchar;
    v_rutas					varchar[];
    v_fp					varchar[];
    v_moneda_fp				varchar[];
    v_valor_fp				varchar[];
    v_forma_pago			varchar;
    v_posicion				integer;
    v_id_forma_pago			integer;
    v_agt					varchar;
    v_codigo_fp				varchar;
    v_res					varchar;
    v_id_moneda_sucursal	integer;
    v_id_moneda_usd			integer;
    v_cod_moneda_sucursal	varchar;
    v_tc					numeric;
    v_codigo_tarjeta		varchar;
    v_saldo_fp1				numeric;
    v_valor					numeric;
    v_saldo_fp2				numeric;
    v_ids					INTEGER[];
    v_boleto				record;
    v_suma_impuestos		numeric;
    v_vuelo					varchar;
    v_vuelos				varchar[];
    v_vuelo_fields			varchar[];
    v_mensaje				varchar;
    v_suma_tasas			numeric;
    v_cupon					integer;
    v_aux_separacion		VARCHAR[];
    v_codigo_pais			varchar;
    v_aux_string			varchar;
    v_fecha_llegada			date;
    v_fecha_hora_origen		timestamp;
    v_fecha_hora_destino	timestamp;
    v_fecha_hora_destino_ant timestamp;
    v_aeropuertos 			varchar[];
    v_retorno				varchar;
    v_id_boleto_vuelo		integer;

    v_valor_forma_pago		numeric;


    v_autorizacion_fp		varchar[];
    v_tarjeta_fp			varchar[];



BEGIN
    v_nombre_funcion = 'obingresos.ft_boleto_amadeus_ime';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
 	#TRANSACCION:  'OBING_BOLREPSERV_INS'
 	#DESCRIPCION:	Insercion de boletos desde servicio REST de Amadeus
 	#AUTOR:		Gonzalo Sarmiento
 	#FECHA:		19-07-2016
	***********************************/

	if(p_transaccion='OBING_BOLREPSERV_INS')then

        begin
            IF NOT EXISTS(SELECT 1
            			  FROM obingresos.tboleto_amadeus
            			  WHERE nro_boleto=v_parametros.nro_boleto)THEN

                SELECT id_moneda into v_id_moneda
                FROM param.tmoneda
                WHERE codigo_internacional=v_parametros.moneda;

                select nextval('obingresos.tboleto_amadeus_id_boleto_amadeus_seq'::regclass) into v_id_boleto;

                INSERT INTO obingresos.tboleto_amadeus
                (nro_boleto,
                total,
                voided,
                estado,
                id_punto_venta,
                localizador,
                fecha_emision,
                id_moneda_boleto,
                pasajero,
                liquido,
                neto,
                id_usuario_reg,
                id_boleto_amadeus
                )VALUES(v_parametros.nro_boleto::varchar,
                v_parametros.total::numeric,
                v_parametros.voided::varchar,
                'borrador',
                v_parametros.id_punto_venta,
                v_parametros.localizador::varchar,
                v_parametros.fecha_emision::date,
                v_id_moneda,
                v_parametros.pasajero::varchar,
                v_parametros.liquido::numeric,
                v_parametros.neto::numeric,
                p_id_usuario,
                v_id_boleto
                );

                if(trim(v_parametros.fp)='')then
                	v_forma_pago='CA';
            	else
                	v_forma_pago=v_parametros.fp;
                end if;

                SELECT id_forma_pago into v_id_forma_pago
                FROM obingresos.tforma_pago
                WHERE codigo=v_forma_pago AND id_moneda=v_id_moneda;

				if(trim(v_parametros.fp)='')then
                	v_valor_forma_pago=0;
            	else
                	v_valor_forma_pago=v_parametros.valor_fp;
                end if;

                INSERT INTO obingresos.tboleto_amadeus_forma_pago
                (id_usuario_reg,
                id_boleto_amadeus,
                id_forma_pago,
                importe,
                forma_pago_amadeus
                )
                VALUES(
                p_id_usuario,
                v_id_boleto,
                v_id_forma_pago,
                v_valor_forma_pago,
                v_parametros.forma_pago_amadeus
                );
                /*
                IF EXISTS(
                SELECT 1
                FROM obingresos.tpnr_forma_pago
                WHERE pnr=v_parametros.localizador
                AND id_forma_pago=v_id_forma_pago
                )THEN
                	UPDATE
                    obingresos.tpnr_forma_pago
                    SET
                    importe=importe+v_valor_forma_pago
                    WHERE pnr=v_parametros.localizador and
                    id_forma_pago=v_id_forma_pago;
                ELSE
                	INSERT INTO obingresos.tpnr_forma_pago
                    (pnr, id_forma_pago, importe)
                    VALUES(v_parametros.localizador, v_id_forma_pago, v_valor_forma_pago);
                END IF;
                */
                raise notice 'llega5';
            	--Definicion de la respuesta
				v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos almacenado(a) con exito (id_boleto'||v_id_boleto||')');
            	v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_id_boleto::varchar);
            ELSE
            	--Definicion de la respuesta
				v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boleto '||v_parametros.nro_boleto||' ya se encuentraba registrado');
            END IF;

            --Devuelve la respuesta
            return v_resp;

        end;

    /*********************************
 	#TRANSACCION:  'OBING_BOL_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	elsif(p_transaccion='OBING_BOLAMA_ELI')then

		begin
			--Sentencia de la eliminacion
            TRUNCATE obingresos.tboleto_amadeus_forma_pago;
			TRUNCATE obingresos.tboleto_amadeus;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos eliminado(a)s');

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