CREATE OR REPLACE FUNCTION obingresos.ft_boleto_forma_pago_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_boleto_forma_pago_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tboleto_forma_pago'
 AUTOR: 		 (jrivera)
 FECHA:	        13-06-2016 20:42:15
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
	v_id_boleto_forma_pago	integer;
	v_codigo_fp				varchar;
    v_localizador			varchar;
    v_id_forma_pago			integer;
    v_importe				numeric;
    v_id_fpago     			integer;
    v_id_forma_mov			integer;
    v_importe_mov			numeric;
    v_datos 				record;
    v_replicar				record;
    v_cont					integer;

BEGIN

    v_nombre_funcion = 'obingresos.ft_boleto_forma_pago_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_BFP_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera
 	#FECHA:		13-06-2016 20:42:15
	***********************************/

	if(p_transaccion='OBING_BFP_INS')then

        begin
        	select fp.codigo into v_codigo_fp
        	from obingresos.tforma_pago fp
        	where fp.id_forma_pago = v_parametros.id_forma_pago;

            select localizador into v_localizador
            from obingresos.tboleto
            where id_boleto=v_parametros.id_boleto;

        	--Sentencia de la insercion
        	insert into obingresos.tboleto_forma_pago(
			id_forma_pago,
			id_boleto,
			estado_reg,
			tarjeta,
			importe,
			numero_tarjeta,
            codigo_tarjeta,
            id_auxiliar,
			id_usuario_ai,
			id_usuario_reg,
			usuario_ai,
			fecha_reg,
			id_usuario_mod,
			fecha_mod
          	) values(

			v_parametros.id_forma_pago,
			v_parametros.id_boleto,
			'activo',
			(case when v_codigo_fp like 'CC%' or v_codigo_fp like 'SF%' then
					substring(v_codigo_fp from 3 for 2)
				else
					NULL
			end),
			v_parametros.importe,
			v_parametros.numero_tarjeta,
            v_parametros.codigo_tarjeta,
            v_parametros.id_auxiliar,
			v_parametros._id_usuario_ai,
			p_id_usuario,
			v_parametros._nombre_usuario_ai,
			now(),
			null,
			null



			)RETURNING id_boleto_forma_pago into v_id_boleto_forma_pago;


			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma de Pago almacenado(a) con exito (id_boleto_forma_pago'||v_id_boleto_forma_pago||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto_forma_pago',v_id_boleto_forma_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_BFPAMA_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		Gonzalo Sarmiento
 	#FECHA:		27-10-2017 20:42:15
	***********************************/

	elsif(p_transaccion='OBING_BFPAMA_INS')then

        begin

        	select fp.codigo into v_codigo_fp
        	from obingresos.tforma_pago fp
        	where fp.id_forma_pago = v_parametros.id_forma_pago;

            select localizador into v_localizador
            from obingresos.tboleto_amadeus
            where id_boleto_amadeus=v_parametros.id_boleto_amadeus;

             select a.id_forma_pago, a.importe into v_id_fpago , v_importe_mov
             from obingresos.tboleto_amadeus_forma_pago a
             where a.id_boleto_amadeus = v_parametros.id_boleto_amadeus;

              select  count(a.id_boleto_amadeus)
           			into
        		v_cont
               from obingresos.tboleto_amadeus_forma_pago a
               where a.id_forma_pago = v_parametros.id_forma_pago and a.id_boleto_amadeus = v_parametros.id_boleto_amadeus;

              if (left (v_parametros.mco,3)  <> '930' and v_parametros.mco <> '')then
            raise exception 'El numero del MCO tiene que empezar con 930';
            end if;

          	if (char_length(v_parametros.mco::varchar) <> 15 and v_parametros.mco <> '' ) then
            raise exception 'El numero del MCO debe tener 15 digitos obligatorios, 930000000012345';
            end if;

            if ((v_cont = 0 )  or
               (v_id_fpago = v_parametros.id_forma_pago and v_importe_mov <> v_parametros.importe))  then
                select obingresos.f_forma_pago_amadeus_mod(v_parametros.id_boleto_amadeus,v_parametros.id_forma_pago,
                                                        v_parametros.numero_tarjeta::varchar,v_parametros.id_auxiliar,
                                                        p_id_usuario,v_parametros.codigo_tarjeta,v_parametros.importe,
                                                         v_parametros.mco)
            into
                v_id_forma_mov;
            end if;

        	--Sentencia de la insercion
        	insert into obingresos.tboleto_amadeus_forma_pago(
			id_forma_pago,
			id_boleto_amadeus,
			estado_reg,
			tarjeta,
			importe,
			numero_tarjeta,
            codigo_tarjeta,
            id_auxiliar,
			id_usuario_ai,
			id_usuario_reg,
			usuario_ai,
			fecha_reg,
			id_usuario_mod,
			fecha_mod,
            mco
          	) values(

			v_parametros.id_forma_pago,
			v_parametros.id_boleto_amadeus,
			'activo',
			(case when v_codigo_fp like 'CC%' or v_codigo_fp like 'SF%' then
					substring(v_codigo_fp from 3 for 2)
				else
					NULL
			end),
			v_parametros.importe,
			v_parametros.numero_tarjeta,
            v_parametros.codigo_tarjeta,
            v_parametros.id_auxiliar,
			v_parametros._id_usuario_ai,
			p_id_usuario,
			v_parametros._nombre_usuario_ai,
			now(),
			null,
			null,
            v_parametros.mco



			)RETURNING id_boleto_amadeus_forma_pago into v_id_boleto_forma_pago;


			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma de Pago almacenado(a) con exito (id_boleto_amadeus_forma_pago'||v_id_boleto_forma_pago||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto_amadeus_forma_pago',v_id_boleto_forma_pago::varchar);
			v_resp = pxp.f_agrega_clave(v_resp,'v_id_forma_mov',v_id_forma_mov::varchar);
            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_BFP_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		13-06-2016 20:42:15
	***********************************/

	elsif(p_transaccion='OBING_BFP_MOD')then

		begin

        	IF EXISTS(select 1
            		  from obingresos.tboleto_forma_pago bfp
                      inner join obingresos.tboleto bol on bol.id_boleto=bfp.id_boleto
                      where bfp.id_boleto_forma_pago=v_parametros.id_boleto_forma_pago
                      and bol.estado='revisado')THEN
            	raise exception 'No es posible modificar la forma de pago de un boleto revisado';
            END IF;

			select fp.codigo into v_codigo_fp
        	from obingresos.tforma_pago fp
        	where fp.id_forma_pago = v_parametros.id_forma_pago;


			--Sentencia de la modificacion
			update obingresos.tboleto_forma_pago set
			id_forma_pago = v_parametros.id_forma_pago,
			id_boleto = v_parametros.id_boleto,
			tarjeta = (case when v_codigo_fp like 'CC%' or v_codigo_fp like 'SF%' then
								substring(v_codigo_fp from 3 for 2)
							else
								NULL
						end),
			importe = v_parametros.importe,
			numero_tarjeta = v_parametros.numero_tarjeta,
            codigo_tarjeta = v_parametros.codigo_tarjeta,
            id_auxiliar = v_parametros.id_auxiliar,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_boleto_forma_pago=v_parametros.id_boleto_forma_pago;
            /*
            if (pxp.f_existe_parametro(p_tabla,'fp_amadeus_corregido')) then
            	UPDATE obingresos.tboleto_forma_pago set
                fp_amadeus_corregido = v_parametros.fp_amadeus_corregido,
                id_usuario_fp_amadeus_corregido = p_id_usuario
                where id_boleto_forma_pago=v_parametros.id_boleto_forma_pago;
            end if;*/

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma de Pago modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto_forma_pago',v_parametros.id_boleto_forma_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_BFPAMA_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		Gonzalo Sarmiento
 	#FECHA:		27-10-2017 20:42:15
	***********************************/

	elsif(p_transaccion='OBING_BFPAMA_MOD')then

		begin

        	IF EXISTS(select 1
            		  from obingresos.tboleto_amadeus_forma_pago bfp
                      inner join obingresos.tboleto_amadeus bol on bol.id_boleto_amadeus=bfp.id_boleto_amadeus
                      where bfp.id_boleto_amadeus_forma_pago=v_parametros.id_boleto_amadeus_forma_pago
                      and bol.estado='revisado')THEN
            	raise exception 'No es posible modificar la forma de pago de un boleto revisado';
            END IF;

			select fp.codigo into v_codigo_fp
        	from obingresos.tforma_pago fp
        	where fp.id_forma_pago = v_parametros.id_forma_pago;

            select a.id_forma_pago, a.importe into v_id_fpago , v_importe_mov
             from obingresos.tboleto_amadeus_forma_pago a

             where a.id_boleto_amadeus = v_parametros.id_boleto_amadeus;

              select  count(a.id_boleto_amadeus)
           				into
        		v_cont
               from obingresos.tboleto_amadeus_forma_pago a
               where a.id_forma_pago = v_parametros.id_forma_pago and a.id_boleto_amadeus = v_parametros.id_boleto_amadeus;

             --limpiar tabla replicacion


              if (left (v_parametros.mco,3)  <> '930' and v_parametros.mco <> '')then
            raise exception 'El numero del MCO tiene que empezar con 930';
            end if;

          	if (char_length(v_parametros.mco::varchar) <> 15 and v_parametros.mco <> '' ) then
            raise exception 'El numero del MCO debe tener 15 digitos obligatorios, 930000000012345';
            end if;

              select 	b.nro_boleto,
             			a.importe,
                        p.codigo
                        into
                        v_replicar
             from obingresos.tboleto_amadeus b
             inner join obingresos.tboleto_amadeus_forma_pago a on a.id_boleto_amadeus = b.id_boleto_amadeus
             inner join obingresos.tforma_pago p on p.id_forma_pago = a.id_forma_pago
             where a.id_boleto_amadeus_forma_pago = v_parametros.id_boleto_amadeus_forma_pago;

            if ((v_cont = 0 )  or
               (v_id_fpago = v_parametros.id_forma_pago and v_importe_mov <> v_parametros.importe)) then
                delete from obingresos.tmod_forma_pago m
              where m.billete = v_replicar.nro_boleto::numeric and m.importe = v_replicar.importe and m.forma = v_replicar.codigo ;

                select obingresos.f_forma_pago_amadeus_mod(v_parametros.id_boleto_amadeus,v_parametros.id_forma_pago,
                                                        v_parametros.numero_tarjeta::varchar,v_parametros.id_auxiliar,
                                                        p_id_usuario,v_parametros.codigo_tarjeta,v_parametros.importe,
                                                         v_parametros.mco)
            into
                v_id_forma_mov;
            end if;



			--Sentencia de la modificacion
			update obingresos.tboleto_amadeus_forma_pago set
			id_forma_pago = v_parametros.id_forma_pago,
			id_boleto_amadeus = v_parametros.id_boleto_amadeus,
			tarjeta = (case when v_codigo_fp like 'CC%' or v_codigo_fp like 'SF%' then
								substring(v_codigo_fp from 3 for 2)
							else
								NULL
						end),
			importe = v_parametros.importe,
			numero_tarjeta = v_parametros.numero_tarjeta,
            codigo_tarjeta = v_parametros.codigo_tarjeta,
            id_auxiliar = v_parametros.id_auxiliar,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            mco = v_parametros.mco
			where id_boleto_amadeus_forma_pago=v_parametros.id_boleto_amadeus_forma_pago;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma de Pago modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto_amadeus_forma_pago',v_parametros.id_boleto_amadeus_forma_pago::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'v_id_forma_mov',v_id_forma_mov::varchar);
            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_BFP_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		13-06-2016 20:42:15
	***********************************/

	elsif(p_transaccion='OBING_BFP_ELI')then

		begin
        	select bol.localizador, bfp.id_forma_pago, bfp.importe
            into v_localizador, v_id_forma_pago, v_importe
            from obingresos.tboleto_forma_pago bfp
            inner join obingresos.tboleto bol on bol.id_boleto=bfp.id_boleto
            where bfp.id_boleto_forma_pago=v_parametros.id_boleto_forma_pago;

			--Sentencia de la eliminacion
			delete from obingresos.tboleto_forma_pago
            where id_boleto_forma_pago=v_parametros.id_boleto_forma_pago;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma de Pago eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto_forma_pago',v_parametros.id_boleto_forma_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_BFPAMA_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		27-10-2017 20:42:15
	***********************************/

	elsif(p_transaccion='OBING_BFPAMA_ELI')then

		begin
        	select bol.localizador, bfp.id_forma_pago, bfp.importe
            into v_localizador, v_id_forma_pago, v_importe
            from obingresos.tboleto_amadeus_forma_pago bfp
            inner join obingresos.tboleto_amadeus bol on bol.id_boleto_amadeus=bfp.id_boleto_amadeus
            where bfp.id_boleto_amadeus_forma_pago=v_parametros.id_boleto_amadeus_forma_pago;

            --Eliminar en tabla replicacion

            select  b.nro_boleto,
            		f.codigo,
                    p.importe
            into
            v_datos
            from obingresos.tboleto_amadeus b
            inner join obingresos.tboleto_amadeus_forma_pago p on p.id_boleto_amadeus = b.id_boleto_amadeus
            inner join obingresos.tforma_pago f on f.id_forma_pago = p.id_forma_pago
            where p.id_boleto_amadeus_forma_pago = v_parametros.id_boleto_amadeus_forma_pago;

            delete from obingresos.tmod_forma_pago m
            where m.billete = v_datos.nro_boleto::numeric and m.forma = v_datos.codigo and m.importe = v_datos.importe;

			--Sentencia de la eliminacion
			delete from obingresos.tboleto_amadeus_forma_pago
            where id_boleto_amadeus_forma_pago=v_parametros.id_boleto_amadeus_forma_pago;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma de Pago eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto_amadeus_forma_pago',v_parametros.id_boleto_amadeus_forma_pago::varchar);

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