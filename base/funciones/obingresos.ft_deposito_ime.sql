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
    v_estado				varchar;
    v_agencia				record;
    v_id_alarma				integer;


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
        	
        	if (pxp.f_existe_parametro(p_tabla,'id_moneda_deposito')) then
            
            	v_id_moneda = v_parametros.id_moneda_deposito;
            else
            	select m.id_moneda into v_id_moneda
                from param.tmoneda m
                where m.codigo_internacional = v_parametros.moneda;
            end if;
            
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
                 COALESCE( v_parametros.id_agencia,null),
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
                    	v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Nuevo deposito agencia prepago','La Agencia ' || v_agencia.nombre || ' ha registrado un nuevo deposito por ' 
                        			|| v_parametros.monto_deposito || ' ' || v_parametros.moneda || ' . Ingrese al ERP para verificarlo',
                        				pxp.f_get_variable_global('obingresos_notidep_prep')));
                	end if;
                    
                    if (v_agencia.tipo_agencia = 'corporativa' and exists(select 1 from pxp.variable_global va where va.variable = 'obingresos_notidep_corp'))  then
                    	v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Nuevo deposito agencia prepago','La Agencia ' || v_agencia.nombre || ' ha registrado un nuevo deposito por ' 
                        			|| v_parametros.monto_deposito || ' ' || v_parametros.moneda || ' . Ingrese al ERP para verificarlo',
                        				pxp.f_get_variable_global('obingresos_notidep_corp')));
                	end if;
                    if (v_agencia.tipo_pago = 'postpago' and exists(select 1 from pxp.variable_global va where va.variable = 'obingresos_notidep_prep')) then 
                    	v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Nuevo deposito agencia postpago','La Agencia ' || v_agencia.nombre || ' ha registrado un nuevo deposito por ' 
                        			|| v_parametros.monto_deposito || ' ' || v_parametros.moneda || ' . Ingrese al ERP para verificarlo',
                        				pxp.f_get_variable_global('obingresos_notidep_prep')));
                	end if;
                    if (v_agencia.tipo_pago = 'postpago' and exists(select 1 from pxp.variable_global va where va.variable = 'obingresos_notidep_posp_'||v_agencia.ciudad))  then
                    	v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Nuevo deposito agencia postpago','La Agencia ' || v_agencia.nombre || ' ha registrado un nuevo deposito por ' 
                        			|| v_parametros.monto_deposito || ' ' || v_parametros.moneda || ' . Ingrese al ERP para verificarlo',
                        				pxp.f_get_variable_global('obingresos_notidep_posp_'||v_agencia.ciudad)));
                	end if;
                    
                    if (exists(select 1 from pxp.variable_global va where va.variable = 'obingresos_notidep'))  then
                    	v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Nuevo deposito de agencia','La Agencia ' || v_agencia.nombre || ' ha registrado un nuevo deposito por ' 
                        			|| v_parametros.monto_deposito || ' ' || v_parametros.moneda || ' . Ingrese al ERP para verificarlo',
                        				pxp.f_get_variable_global('obingresos_notidep')));
                	end if;
                end if;
            end if;
            
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
        	if (v_parametros.tipo = 'agencia') then
            	SELECT estado into v_estado
                from obingresos.tdeposito
                where id_deposito = v_parametros.id_deposito;
                
                if (v_estado != 'borrador') then
                	raise exception 'No es posible modificar depositos validados';
                end if;
                
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
            else
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
                usuario_ai = v_parametros._nombre_usuario_ai
                --fecha_venta = v_parametros.fecha_venta,
                --monto_total = v_parametros.monto_total,
                --agt = v_parametros.agt
                where id_deposito=v_parametros.id_deposito;
            	
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
            	v_parametros.pnr = substring(v_parametros.pnr from 1 for 5);
                
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
                trim(both ' ' from v_parametros.nro_deposito),
                --v_parametros.id_agencia,
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
            	v_pnr = substring(v_parametros.order_code from 1 for 5);
                
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
			--Sentencia de la eliminacion
			delete from obingresos.tdeposito
            where id_deposito=v_parametros.id_deposito;

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
            set estado = 'validado'
            where id_deposito=v_parametros.id_deposito;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos validado(a)');
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