CREATE OR REPLACE FUNCTION obingresos.ft_deposito_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
  RETURNS varchar AS
  $body$
  /**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_deposito_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tdeposito'
 AUTOR: 		 (jrivera)
 FECHA:	        06-01-2016 22:42:28
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
	v_id_deposito			integer;
    v_id_agencia			integer;
    v_id_moneda				integer;


BEGIN

    v_nombre_funcion = 'obingresos.ft_deposito_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_DEP_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	if(p_transaccion='OBING_DEP_INS')then

        begin
       		insert into obingresos.tdeposito(
			estado_reg,
			nro_deposito,
			monto_deposito,
			id_moneda_deposito,
			id_agencia,
			fecha,
			saldo,
            moneda,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod,
            descripcion,
            pnr
          	) values(
			'activo',
			v_parametros.nro_deposito,
			v_parametros.monto_deposito,
			v_parametros.id_moneda_deposito,
			v_parametros.id_agencia,
			v_parametros.fecha,
			v_parametros.saldo,
            v_parametros.moneda,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null,
            v_parametros.descripcion,
			v_parametros.pnr



			)RETURNING id_deposito into v_id_deposito;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos almacenado(a) con exito (id_deposito'||v_id_deposito||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito',v_id_deposito::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_DEP_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	elsif(p_transaccion='OBING_DEP_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tdeposito set
			nro_deposito = v_parametros.nro_deposito,
			monto_deposito = v_parametros.monto_deposito,
			id_moneda_deposito = v_parametros.id_moneda_deposito,
			id_agencia = v_parametros.id_agencia,
			fecha = v_parametros.fecha,
			saldo = v_parametros.saldo,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_deposito=v_parametros.id_deposito;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito',v_parametros.id_deposito::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
    /*********************************
 	#TRANSACCION:  'OBING_DEP_SUB'
 	#DESCRIPCION:	Subir datos
 	#AUTOR:		mam
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	elsif(p_transaccion='OBING_DEP_SUB')then
   			begin
            
            if (v_parametros.estado = 'Payment requested') then 
            	v_parametros.pnr = substring(v_parametros.pnr from 1 for 5);
                
                select id_moneda into v_id_moneda
                from param.tmoneda m
                where m.codigo_internacional = v_parametros.moneda;
                
                if (v_id_moneda is null)then
                    raise exception 'No existe la moneda % en la base de datos',v_parametros.moneda;
                end if;
                
                if (exists(	select nro_deposito 
                            from obingresos.tdeposito 
                            where nro_deposito = v_parametros.nro_deposito)) then
                    raise exception 'El deposito % ya esta registrado',v_parametros.nro_deposito;            
                end if;
                
                insert into obingresos.tdeposito(
                nro_deposito,
                --id_agencia,
                monto_deposito,
                moneda,
                descripcion,
                pnr,
                id_moneda_deposito,
                saldo,
                fecha,
                tipo,
                observaciones
                ) values(
                v_parametros.nro_deposito,
                --v_parametros.id_agencia,
                v_parametros.monto_deposito,
                v_parametros.moneda,
                v_parametros.descripcion,
                v_parametros.pnr,
                v_id_moneda,
                v_parametros.monto_deposito,
                to_date(v_parametros.fecha,'DD/MM/YYYY'),
                v_parametros.tipo,
                v_parametros.observaciones
                
                )RETURNING id_deposito into v_id_deposito;
            end if;

            --Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos almacenado(a) con exito (id_deposito'||v_id_deposito||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito',v_id_deposito::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_DEP_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	elsif(p_transaccion='OBING_DEP_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tdeposito
            where id_deposito=v_parametros.id_deposito;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito',v_parametros.id_deposito::varchar);

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