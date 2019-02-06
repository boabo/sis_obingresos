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
    v_pnr					varchar;
    v_estado				record;
    v_agencia				record;
    v_id_alarma				integer;
    v_monto_total			numeric;
    v_moneda				varchar;
    v_deposito				record;
    v_aux					varchar;
    v_deposito_boa			varchar;
    v_verificar_existencia  record;




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
        	   --raise exception 'tipo %',v_parametros.tipo;

        	if (pxp.f_existe_parametro(p_tabla,'id_moneda_deposito')) then

            	v_id_moneda = v_parametros.id_moneda_deposito;
                select m.codigo_internacional into v_moneda
                from param.tmoneda m
                where m.id_moneda = v_parametros.id_moneda_deposito;

            else
            	select m.id_moneda into v_id_moneda
                from param.tmoneda m
                where m.codigo_internacional = v_parametros.moneda;
                v_moneda = v_parametros.moneda;
            end if;
       -- raise exception 'LLEGA AQUI %',v_parametros.monto_deposito;
       		SELECT per.nombre ||' '|| per.apellido_paterno ||'  '|| per.apellido_materno as nombre_completo,
                   count(per.nombre) as existe
                   into v_verificar_existencia
            FROM obingresos.tdeposito depo
            inner join segu.tusuario usu on usu.id_usuario = depo.id_usuario_reg
            inner join segu.tpersona per on per.id_persona = usu.id_persona
            WHERE
            depo.nro_deposito = v_parametros.nro_deposito and
            depo.fecha = v_parametros.fecha and
            depo.monto_deposito = v_parametros.monto_deposito
            group by per.nombre, per.apellido_paterno,per.apellido_materno;




   /*AUMENTANDO CONDICION*/
    if (v_verificar_existencia.existe <> 0) THEN
    	raise exception 'El Registro con No Deposito = % , Fecha de Deposito = % y Monto = % ya se encuentra registrado por el Usuario: % porfavor elimine el registro existente para registrar el actual',v_parametros.nro_deposito,v_parametros.fecha,v_parametros.monto_deposito,v_verificar_existencia.nombre_completo;
    else

        	if (v_parametros.tipo = 'banca') then
            	insert into obingresos.tdeposito(
                estado_reg,
                nro_deposito,
                monto_deposito,
                id_moneda_deposito,
                fecha,
                agt,
                id_usuario_reg,
                fecha_reg,
                id_usuario_ai,
                usuario_ai,
                id_usuario_mod,
                fecha_mod,
                tipo,
                fecha_venta,
                monto_total
                ) values(
                'activo',
                v_parametros.nro_deposito,
                v_parametros.monto_deposito,
                v_id_moneda,
                v_parametros.fecha,
                v_parametros.agt,
                p_id_usuario,
                now(),
                v_parametros._id_usuario_ai,
                v_parametros._nombre_usuario_ai,
                null,
                null,
                v_parametros.tipo,
                v_parametros.fecha_venta,
                v_parametros.monto_total
                )RETURNING id_deposito into v_id_deposito;

        	elsif(v_parametros.tipo = 'venta_propia') then
           insert into obingresos.tdeposito(
                estado_reg,
                nro_deposito,
                monto_deposito,
                id_moneda_deposito,
                fecha,
                id_usuario_reg,
                fecha_reg,
                id_usuario_ai,
                usuario_ai,
                id_usuario_mod,
                fecha_mod,
                tipo,
                id_apertura_cierre_caja,
                monto_total
                ) values(
                'activo',
                v_parametros.nro_deposito,
                v_parametros.monto_deposito,
                v_id_moneda,
                v_parametros.fecha,
                p_id_usuario,
                now(),
                v_parametros._id_usuario_ai,
                v_parametros._nombre_usuario_ai,
                null,
                null,
                v_parametros.tipo,
                v_parametros.id_apertura_cierre_caja,
                v_parametros.monto_deposito
                )RETURNING id_deposito into v_id_deposito;

                elsif(v_parametros.tipo = 'venta_agencia') then
           insert into obingresos.tdeposito(
                estado_reg,
                nro_deposito,
                monto_deposito,
                id_moneda_deposito,
                fecha,
                id_usuario_reg,
                fecha_reg,
                id_usuario_ai,
                usuario_ai,
                id_usuario_mod,
                fecha_mod,
                tipo,
                id_apertura_cierre_caja,
                monto_total
                ) values(
                'activo',
                v_parametros.nro_deposito,
                v_parametros.monto_deposito,
                v_id_moneda,
                v_parametros.fecha,
                p_id_usuario,
                now(),
                v_parametros._id_usuario_ai,
                v_parametros._nombre_usuario_ai,
                null,
                null,
                v_parametros.tipo,
                v_parametros.id_apertura_cierre_caja,
                v_parametros.monto_deposito
                )RETURNING id_deposito into v_id_deposito;

            else
                insert into obingresos.tdeposito(
                estado_reg,
                nro_deposito,
                monto_deposito,
                id_moneda_deposito,
                id_agencia,
                fecha,
                saldo,
                id_usuario_reg,
                fecha_reg,
                id_usuario_ai,
                usuario_ai,
                id_usuario_mod,
                fecha_mod,
                estado
                ) values(
                'activo',
                v_parametros.nro_deposito,
                v_parametros.monto_deposito,
                v_id_moneda,
               v_parametros.id_agencia,
                v_parametros.fecha,
                v_parametros.saldo,
                p_id_usuario,
                now(),
                v_parametros._id_usuario_ai,
                v_parametros._nombre_usuario_ai,
                null,
                null,
                'borrador'
                )RETURNING id_deposito into v_id_deposito;

                if (v_parametros.id_agencia is not null) then
                	--generar alerta para ingresos

                    select a.*,lu.codigo as ciudad into v_agencia
                    from obingresos.tagencia a
                    inner join param.tlugar lu on lu.id_lugar = a.id_lugar
                    where id_agencia = v_parametros.id_agencia;

                    if (v_agencia.tipo_pago = 'prepago' and exists(select 1 from pxp.variable_global va where va.variable = 'obingresos_notidep_prep'))  then
                        v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Nuevo deposito agencia prepago','La Agencia ' || replace(v_agencia.nombre,'''',' ')  || ' ha registrado un nuevo deposito por '
                        			|| v_parametros.monto_deposito || ' ' || v_moneda || ' . Ingrese al Sistema ERP BOA para verificarlo.',
                        				pxp.f_get_variable_global('obingresos_notidep_prep')));

                	end if;

                    if (v_agencia.tipo_agencia = 'corporativa' and exists(select 1 from pxp.variable_global va where va.variable = 'obingresos_notidep_corp'))  then
                    	v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Nuevo deposito agencia prepago','La Agencia ' || replace(v_agencia.nombre,'''',' ') || ' ha registrado un nuevo deposito por '
                        			|| v_parametros.monto_deposito || ' ' || v_moneda || ' . Ingrese al Sistema ERP BOA para verificarlo.',
                        				pxp.f_get_variable_global('obingresos_notidep_corp')));
                	end if;
                    if (v_agencia.tipo_pago = 'postpago' and exists(select 1 from pxp.variable_global va where va.variable = 'obingresos_notidep_prep')) then
                    	v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Nuevo deposito agencia postpago','La Agencia ' || replace(v_agencia.nombre,'''',' ') || ' ha registrado un nuevo deposito por '
                        			|| v_parametros.monto_deposito || ' ' || v_moneda || ' . Ingrese al Sistema ERP BOA para verificarlo.',
                        				pxp.f_get_variable_global('obingresos_notidep_prep')));
                	end if;
                    if (v_agencia.tipo_pago = 'postpago' and exists(select 1 from pxp.variable_global va where va.variable = 'obingresos_notidep_posp_'||v_agencia.ciudad))  then
                    	v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Nuevo deposito agencia postpago','La Agencia ' || replace(v_agencia.nombre,'''',' ') || ' ha registrado un nuevo deposito por '
                        			|| v_parametros.monto_deposito || ' ' || v_moneda || ' . Ingrese al Sistema ERP BOA para verificarlo.',
                        				pxp.f_get_variable_global('obingresos_notidep_posp_'||v_agencia.ciudad)));
                	end if;

                    if (exists(select 1 from pxp.variable_global va where va.variable = 'obingresos_notidep'))  then
                    	v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Nuevo deposito de agencia','La Agencia ' || replace(v_agencia.nombre,'''',' ') || ' ha registrado un nuevo deposito por '
                        			|| v_parametros.monto_deposito || ' ' || v_moneda || ' . Ingrese al Sistema ERP BOA para verificarlo.',
                        				pxp.f_get_variable_global('obingresos_notidep')));
                	end if;
                end if;
            end if;

    /*Finalizando Condicion*/   end if;


            if (pxp.f_existe_parametro(p_tabla,'id_periodo_venta')) then

            	update obingresos.tdeposito
                set id_periodo_venta = v_parametros.id_periodo_venta
                where id_deposito = v_id_deposito;
            end if;

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
        	    SELECT * into v_estado
                from obingresos.tdeposito
                where id_deposito = v_parametros.id_deposito;


        	if (v_parametros.tipo = 'agencia') then
            	SELECT * into v_deposito
                from obingresos.tdeposito
                where id_deposito = v_parametros.id_deposito;
                --raise exception '%,%,%,%',v_deposito.id_agencia,v_deposito.nro_deposito,v_deposito.fecha,v_deposito.monto_deposito;

               /* update obingresos.tmovimiento_entidad
                set  fecha =  v_parametros.fecha,
                autorizacion__nro_deposito =  v_parametros.nro_deposito
                where id_agencia = v_deposito.id_agencia and   autorizacion__nro_deposito = v_deposito.nro_deposito
                and estado_reg = 'activo' and  fecha = v_deposito.fecha and monto = v_deposito.monto_deposito and tipo = 'credito';
               */

            end if;

            if (pxp.f_existe_parametro(p_tabla,'id_moneda_deposito')) then

            	v_id_moneda = v_parametros.id_moneda_deposito;
            else
            	select m.id_moneda into v_id_moneda
                from param.tmoneda m
                where m.codigo_internacional = v_parametros.moneda;
            end if;

            if (v_parametros.tipo = 'banca') then
                --Sentencia de la modificacion
                update obingresos.tdeposito set
                nro_deposito = v_parametros.nro_deposito,
                monto_deposito = v_parametros.monto_deposito,
                id_moneda_deposito = v_id_moneda,
                id_agencia =  v_parametros.id_agencia,
                fecha = v_parametros.fecha,
                saldo = v_parametros.saldo,
                id_usuario_mod = p_id_usuario,
                fecha_mod = now(),
                id_usuario_ai = v_parametros._id_usuario_ai,
                usuario_ai = v_parametros._nombre_usuario_ai,
                fecha_venta = v_parametros.fecha_venta,
                monto_total = v_parametros.monto_total,
                agt = v_parametros.agt
                where id_deposito=v_parametros.id_deposito;
            elsif (v_parametros.tipo = 'venta_propia')then

            select nro_deposito
            into
            v_aux
            from obingresos.tdeposito
            where id_deposito= v_parametros.id_deposito;

             update obingresos.tdeposito set
                nro_deposito = v_parametros.nro_deposito,
                nro_deposito_aux = v_aux,
                monto_deposito = v_parametros.monto_deposito,
                id_moneda_deposito = v_id_moneda,
                fecha = v_parametros.fecha,
                id_usuario_mod = p_id_usuario,
                fecha_mod = now(),
                id_usuario_ai = v_parametros._id_usuario_ai,
                usuario_ai = v_parametros._nombre_usuario_ai
                where id_deposito=v_parametros.id_deposito;
           elsif (v_parametros.tipo = 'venta_agencia')then
        		 update obingresos.tdeposito set
                nro_deposito = v_parametros.nro_deposito,
                modificarDeposito = v_parametros.modificarDeposito,
                monto_deposito = v_parametros.monto_deposito,
                id_moneda_deposito = v_id_moneda,
                fecha = v_parametros.fecha,
                id_usuario_mod = p_id_usuario,
                fecha_mod = now(),
                id_usuario_ai = v_parametros._id_usuario_ai,
                usuario_ai = v_parametros._nombre_usuario_ai
                where id_deposito=v_parametros.id_deposito;
            else

            if (pxp.f_existe_parametro(p_tabla,'nro_deposito_boa')) then
                  v_deposito_boa = v_parametros.nro_deposito_boa;
              else
                  v_deposito_boa = '';
            end if;
            	--Sentencia de la modificacion
                IF (v_estado.estado = 'borrador') then
                update obingresos.tdeposito set
                nro_deposito = v_parametros.nro_deposito,
                monto_deposito = v_parametros.monto_deposito,
                id_moneda_deposito = v_id_moneda,
                id_agencia =  v_parametros.id_agencia,
                fecha = v_parametros.fecha,
                saldo = v_parametros.saldo,
                id_usuario_mod = p_id_usuario,
                fecha_mod = now(),
                id_usuario_ai = v_parametros._id_usuario_ai,
                usuario_ai = v_parametros._nombre_usuario_ai,
                nro_deposito_boa = v_deposito_boa
                where id_deposito=v_parametros.id_deposito;

                ELSE

                raise exception 'NO SE PUEDE MODIFICAR DEPOSITOS QUE YA FUERON VALIDADOS!';

                end if;

            end if;

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

            if (trim(both ' ' from v_parametros.estado) = 'Payment requested') then

            	if (to_date(v_parametros.fecha,'DD/MM/YYYY')>'09/09/2017') then
                	v_parametros.pnr = substring(v_parametros.pnr from 1 for 6);
                else
            		v_parametros.pnr = substring(v_parametros.pnr from 1 for 5);
                end if;
                select id_moneda into v_id_moneda
                from param.tmoneda m
                where m.codigo_internacional = trim(both ' ' from v_parametros.moneda);

                if (v_id_moneda is null)then
                    raise exception 'No existe la moneda % en la base de datos',v_parametros.moneda;
                end if;

                if (exists(	select nro_deposito
                            from obingresos.tdeposito
                            where nro_deposito = trim(both ' ' from v_parametros.nro_deposito))) then
                    raise exception 'El deposito % ya esta registrado',v_parametros.nro_deposito;
                end if;

                insert into obingresos.tdeposito(
                nro_deposito,
                id_agencia,
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
                trim(both ' ' from v_parametros.nro_deposito),
                v_parametros.id_agencia,
                v_parametros.monto_deposito,
                trim(both ' ' from v_parametros.moneda),
                trim(both ' ' from v_parametros.descripcion),
                trim(both ' ' from v_parametros.pnr),
                v_id_moneda,
                v_parametros.monto_deposito,
                to_date(v_parametros.fecha,'DD/MM/YYYY'),
                v_parametros.tipo,
                trim(both ' ' from v_parametros.observaciones)

                )RETURNING id_deposito into v_id_deposito;
            end if;

            --Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos almacenado(a) con exito (id_deposito'||v_id_deposito||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito',v_id_deposito::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_DEPWP_SUB'
 	#DESCRIPCION:	Subir datos World Pay
 	#AUTOR:		jrr
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	elsif(p_transaccion='OBING_DEPWP_SUB')then
   			begin

            if (trim(both ' ' from v_parametros.estado) in ('SETTLED','CAPTURED')) then

                if (to_date(v_parametros.fecha,'YYYY.MM.DD')>'09/09/2017') then
                	v_pnr = substring(v_parametros.order_code from 1 for 6);
                else
            		v_pnr = substring(v_parametros.order_code from 1 for 5);
                end if;
                select id_moneda into v_id_moneda
                from param.tmoneda m
                where m.codigo_internacional = trim(both ' ' from v_parametros.moneda);

                if (v_id_moneda is null)then
                    raise exception 'No existe la moneda % en la base de datos',v_parametros.moneda;
                end if;

                if (not exists(	select nro_deposito
                            from obingresos.tdeposito
                            where nro_deposito = trim(both ' ' from v_parametros.order_code))) then

                insert into obingresos.tdeposito(
                id_usuario_reg,
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
                  p_id_usuario,
                  trim(both ' ' from v_parametros.order_code),
                  --v_parametros.id_agencia,
                  v_parametros.monto,
                  trim(both ' ' from v_parametros.moneda),
                  trim(both ' ' from v_parametros.tarjeta),
                  v_pnr,
                  v_id_moneda,
                  v_parametros.monto,
                  to_date(v_parametros.fecha,'YYYY.MM.DD'),
                  'ogone',
                  trim(both ' ' from v_parametros.metodo_pago)

                  )RETURNING id_deposito into v_id_deposito;
                --raise exception 'llega';
                end if;
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

        select de.tipo
        into
        v_deposito
        from obingresos.tdeposito de
        where de.id_deposito =  v_parametros.id_deposito;

        if   (v_deposito.tipo  = 'agencia') then

            update obingresos.tdeposito
            set
            estado = 'eliminado',
            id_usuario_mod = p_id_usuario,
            fecha_mod = now()
            where id_deposito=v_parametros.id_deposito;
          else

            delete from obingresos.tdeposito
            where id_deposito=v_parametros.id_deposito;
        end if;


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito',v_parametros.id_deposito::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
    elsif(p_transaccion='OBING_VALIDEPO_UPD')then

		begin
			--Sentencia de la eliminacion
			update obingresos.tdeposito
            set
            estado = 'validado',
            id_usuario_mod = p_id_usuario,
            fecha_mod = now()
            where id_deposito=v_parametros.id_deposito;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos validado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito',v_parametros.id_deposito::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
    /*********************************
 	#TRANSACCION:  'OBING_DEP_MI'
 	#DESCRIPCION:	Recuperar datos
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	elsif(p_transaccion='OBING_DEP_MI')then

		begin


        IF  pxp.f_existe_parametro(p_tabla,'nro_deposito') THEN
           if v_parametros.nro_deposito = null or v_parametros.nro_deposito = '' then
           raise exception 'error';
           end if;
        END IF;
          IF  pxp.f_existe_parametro(p_tabla,'codigo') THEN
           if v_parametros.codigo = null or v_parametros.codigo = '' then
           raise exception 'error';
           end if;
        END IF;
         IF  pxp.f_existe_parametro(p_tabla,'fecha_venta') THEN
           if v_parametros.fecha_venta = null then
           raise exception 'error';
           end if;
        END IF;
          IF  pxp.f_existe_parametro(p_tabla,'monto_deposito') THEN
           if v_parametros.monto_deposito = null  then
           raise exception 'error';
           end if;
        END IF;
         IF  pxp.f_existe_parametro(p_tabla,'desc_moneda') THEN
           if v_parametros.desc_moneda = null  or v_parametros.desc_moneda = '' then
           raise exception 'error';
           end if;
        END IF;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'nro_deposito',v_parametros.nro_deposito::varchar);

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
