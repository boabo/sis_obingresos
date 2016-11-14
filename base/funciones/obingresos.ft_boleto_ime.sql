CREATE OR REPLACE FUNCTION obingresos.ft_boleto_ime (
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
			    
BEGIN

    v_nombre_funcion = 'obingresos.ft_boleto_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_BOL_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	if(p_transaccion='OBING_BOL_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into obingresos.tboleto(
			id_agencia,
			id_moneda_boleto,
			estado_reg,
			comision,
			fecha_emision,
			total,
			pasajero,
			monto_pagado_moneda_boleto,
			liquido,
			nro_boleto,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			v_parametros.id_agencia,
			v_parametros.id_moneda_boleto,
			'activo',
			v_parametros.comision,
			v_parametros.fecha_emision,
			v_parametros.total,
			v_parametros.pasajero,
			v_parametros.monto_pagado_moneda_boleto,
			v_parametros.liquido,
			v_parametros.nro_boleto,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			v_parametros._id_usuario_ai,
			null,
			null
							
			
			
			)RETURNING id_boleto into v_id_boleto;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos almacenado(a) con exito (id_boleto'||v_id_boleto||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_id_boleto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
        
    /*********************************    
 	#TRANSACCION:  'OBING_BOLVEN_UPD'
 	#DESCRIPCION:	Insercion de boleto por counter
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	elsif(p_transaccion='OBING_BOLVEN_UPD')then
					
        begin
        	select fp.codigo into v_codigo_fp
        	from obingresos.tforma_pago fp
        	where fp.id_forma_pago = v_parametros.id_forma_pago;
            
            select * into v_boleto
            from obingresos.tboleto
            where id_boleto = v_parametros.id_boleto;
            v_mensaje = '';
            if (v_parametros.estado is null or v_parametros.estado = '') then          
            	if (exists (select 1 
                	from obingresos.tboleto
                	where id_boleto = v_parametros.id_boleto and estado is not null)) then
                	raise exception 'El boleto ya fue registrado';
                end if;  
                
                v_mensaje = v_boleto.mensaje_error;  
            
                update obingresos.tboleto set estado = 'borrador'
                where id_boleto = v_parametros.id_boleto and estado is null;
                
                
            end if;
            
            update obingresos.tboleto set comision = v_parametros.comision
            where id_boleto = v_parametros.id_boleto;
            
            if (v_parametros.id_forma_pago is not null and v_parametros.id_forma_pago != 0) then
                delete from obingresos.tboleto_forma_pago
                where id_boleto = v_parametros.id_boleto;
                
                select fp.codigo into v_codigo_tarjeta
                from obingresos.tforma_pago fp
                where fp.id_forma_pago = v_parametros.id_forma_pago;
                
                v_codigo_tarjeta = (case when v_codigo_tarjeta like 'CC%' or v_codigo_tarjeta like 'SF%' then 
                                        substring(v_codigo_tarjeta from 3 for 2)				
                                else
                                      NULL
                              end);
                             
                if (v_codigo_tarjeta is not null) then
                	v_res = pxp.f_valida_numero_tarjeta_credito(v_parametros.numero_tarjeta,v_codigo_tarjeta);
                end if;
                 
                INSERT INTO 
                  obingresos.tboleto_forma_pago
                (
                  id_usuario_reg,                    
                  importe,
                  id_forma_pago,
                  id_boleto,
                  ctacte,
                  numero_tarjeta,
                  codigo_tarjeta,
                  tarjeta
                )
                VALUES (
                  p_id_usuario,                   
                  v_parametros.monto_forma_pago,
                  v_parametros.id_forma_pago,
                  v_parametros.id_boleto,
                  v_parametros.ctacte,
                  v_parametros.numero_tarjeta,
                  v_parametros.codigo_tarjeta,
                  v_codigo_tarjeta
                );    
                
                if (v_parametros.id_forma_pago2 is not null and v_parametros.id_forma_pago2 != 0) then
                    select fp.codigo into v_codigo_tarjeta
                    from obingresos.tforma_pago fp
                    where fp.id_forma_pago = v_parametros.id_forma_pago2;
                    
                    v_codigo_tarjeta = (case when v_codigo_tarjeta like 'CC%' or v_codigo_tarjeta like 'SF%' then 
                                            substring(v_codigo_tarjeta from 3 for 2)				
                                    else
                                          NULL
                                  end);
                    if (v_codigo_tarjeta is not null) then
                        v_res = pxp.f_valida_numero_tarjeta_credito(v_parametros.numero_tarjeta2,v_codigo_tarjeta);
                    end if;
                    INSERT INTO 
                      obingresos.tboleto_forma_pago
                    (
                      id_usuario_reg,                    
                      importe,
                      id_forma_pago,
                      id_boleto,
                      ctacte,
                      numero_tarjeta,
                      codigo_tarjeta,
                      tarjeta
                    )
                    VALUES (
                      p_id_usuario,                   
                      v_parametros.monto_forma_pago2,
                      v_parametros.id_forma_pago2,
                      v_parametros.id_boleto,
                      v_parametros.ctacte2,
                      v_parametros.numero_tarjeta2,
                      v_parametros.codigo_tarjeta2,
                      v_codigo_tarjeta
                    );  
                end if; 
            end if;  
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos almacenado(a) con exito (id_boleto'||v_id_boleto||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_id_boleto::varchar);
			if (v_mensaje != '') then
            	v_resp = pxp.f_agrega_clave(v_resp,'alertas',v_mensaje::varchar);
            end if;
            --Devuelve la respuesta
            return v_resp;

		end;
        
    /*********************************    
 	#TRANSACCION:  'OBING_MODFPGRUPO_UPD'
 	#DESCRIPCION:	Modifica la forma de pago de un grupo de boletos
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	elsif(p_transaccion='OBING_MODFPGRUPO_UPD')then
					
        begin
        	
        	v_saldo_fp1 = v_parametros.monto_forma_pago;
            v_saldo_fp2 = 	(case when v_parametros.monto_forma_pago2 is null then 
            					0 
            				else 
                            	v_parametros.monto_forma_pago2 
                            end);
            
            v_ids = string_to_array(v_parametros.ids_seleccionados,',');
            FOREACH v_id_boleto IN ARRAY v_ids
            LOOP
              delete from obingresos.tboleto_forma_pago 
              where id_boleto = v_id_boleto;        
              
            	if (v_saldo_fp1 > 0) then
              		v_valor = obingresos.f_monto_pagar_boleto(v_id_boleto,v_saldo_fp1,v_parametros.id_forma_pago );
             		
                    v_saldo_fp1 = v_saldo_fp1 - v_valor;
                    
                    select fp.codigo into v_codigo_tarjeta
                    from obingresos.tforma_pago fp
                    where fp.id_forma_pago = v_parametros.id_forma_pago;
                    
                    v_codigo_tarjeta = (case when v_codigo_tarjeta like 'CC%' or v_codigo_tarjeta like 'SF%' then 
                                            substring(v_codigo_tarjeta from 3 for 2)				
                                    else
                                          NULL
                                  end);
                    if (v_codigo_tarjeta is not null) then
                        v_res = pxp.f_valida_numero_tarjeta_credito(v_parametros.numero_tarjeta,v_codigo_tarjeta);
                    end if;
                    
                    INSERT INTO 
                      obingresos.tboleto_forma_pago
                    (
                      id_usuario_reg,                    
                      importe,
                      id_forma_pago,
                      id_boleto,
                      ctacte,
                      numero_tarjeta,
                      tarjeta
                    )
                    VALUES (
                      p_id_usuario,                   
                      v_valor,
                      v_parametros.id_forma_pago,
                      v_id_boleto,
                      v_parametros.ctacte,
                      v_parametros.numero_tarjeta,
                      v_codigo_tarjeta
                    );  
            	end if;
                if (v_saldo_fp2 > 0) then
              		v_valor = obingresos.f_monto_pagar_boleto(v_id_boleto,v_saldo_fp2,v_parametros.id_forma_pago2 );
             		v_saldo_fp2 = v_saldo_fp2 - v_valor;
                    select fp.codigo into v_codigo_tarjeta
                    from obingresos.tforma_pago fp
                    where fp.id_forma_pago = v_parametros.id_forma_pago2;
                    
                    v_codigo_tarjeta = (case when v_codigo_tarjeta like 'CC%' or v_codigo_tarjeta like 'SF%' then 
                                            substring(v_codigo_tarjeta from 3 for 2)				
                                    else
                                          NULL
                                  end);
                    if (v_codigo_tarjeta is not null) then
                        v_res = pxp.f_valida_numero_tarjeta_credito(v_parametros.numero_tarjeta2,v_codigo_tarjeta);
                    end if;
                    
                    INSERT INTO 
                      obingresos.tboleto_forma_pago
                    (
                      id_usuario_reg,                    
                      importe,
                      id_forma_pago,
                      id_boleto,
                      ctacte,
                      numero_tarjeta,
                      tarjeta
                    )
                    VALUES (
                      p_id_usuario,                   
                      v_valor,
                      v_parametros.id_forma_pago2,
                      v_id_boleto,
                      v_parametros.ctacte2,
                      v_parametros.numero_tarjeta2,
                      v_codigo_tarjeta
                    );  
            	end if;
                select obingresos.f_valida_boleto_fp(v_id_boleto) into v_res; 
        	            
                
               update obingresos.tboleto 
               set id_usuario_cajero = p_id_usuario,
               estado = 'pagado'
               where id_boleto=v_id_boleto;
                    
               select * into v_boleto
               from obingresos.tboleto
               where id_boleto = v_id_boleto;
               
                --Si el usuario que cambia el estado del boleto a estado pagado no es cajero
                  --lanzamos excepcion
                  if (exists(	select 1
                                  from vef.tapertura_cierre_caja acc
                                  where acc.id_usuario_cajero = p_id_usuario and 
                                  	acc.fecha_apertura_cierre = v_boleto.fecha_reg::date and 
                                    acc.estado_reg = 'activo' and acc.estado = 'cerrado' and 
                                    acc.id_punto_venta = v_boleto.id_punto_venta)) then
                      raise exception 'La caja ya fue cerrada, necesita tener la caja abierta para poder finalizar la venta del boleto';
                  end if;
                  
                  if (not exists(	select 1
                                  from vef.tapertura_cierre_caja acc
                                  where acc.id_usuario_cajero = p_id_usuario and 
                                  	acc.fecha_apertura_cierre = v_boleto.fecha_reg::date and 
                                    acc.estado_reg = 'activo' and acc.estado = 'abierto' and 
                                    acc.id_punto_venta = v_boleto.id_punto_venta)) then
                      raise exception 'Antes de emitir un boleto debe realizar una apertura de caja';
                  end if;
                
            END LOOP;
        	
            if (v_saldo_fp1 > 0 or v_saldo_fp2 > 0) then
            	raise exception 'El monto total de las formas de pago es superior al monto de los boletos seleccionados:%,%',v_saldo_fp1,v_saldo_fp2;
            end if;  
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma de pago de Boletos modificado con exito (id_boletos'||v_parametros.ids_seleccionados||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_id_boleto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
        
    /*********************************    
 	#TRANSACCION:  'OBING_BOLSERV_INS'
 	#DESCRIPCION:	Insercion de boletos desde servicio REST de Resiber
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	elsif(p_transaccion='OBING_BOLSERV_INS')then
					
        begin
        	
        	
        	select ag.id_agencia,ag.codigo,sm.id_moneda,suc.id_lugar,mon.codigo_internacional 
            	into v_id_agencia,v_agt,v_id_moneda_sucursal,v_id_lugar_sucursal,v_cod_moneda_sucursal
            from vef.tpunto_venta pv
            inner join obingresos.tagencia ag on ag.codigo = pv.codigo
            inner join vef.tsucursal suc on suc.id_sucursal = pv.id_sucursal
            inner join vef.tsucursal_moneda sm on sm.id_sucursal = suc.id_sucursal
            									and sm.tipo_moneda = 'moneda_base'
            inner join param.tmoneda mon on mon.id_moneda = sm.id_moneda
            where pv.id_punto_venta = v_parametros.id_punto_venta;
            
            select m.id_moneda into v_id_moneda
            from param.tmoneda m
            where m.codigo_internacional = v_parametros.moneda;
            
            select m.id_moneda into v_id_moneda_usd
            from param.tmoneda m
            where m.codigo_internacional = 'USD';
            
            if (length(v_parametros.fecha_emision) = 5) then
            	v_parametros.fecha_emision = v_parametros.fecha_emision || to_char(now(),'YY');
            end if;
                        
            select to_date(v_parametros.fecha_emision, 'DDMONYY') into v_fecha;
            
            v_tc = (select param.f_get_tipo_cambio_v2(v_id_moneda_sucursal,v_id_moneda_usd,v_fecha,'O'));
            
            if (v_tc is null) then
            	raise exception 'No existe tipo de cambio para la moneda USD,  en la fecha %',v_fecha;
            end if;
                       
            if (v_id_lugar_sucursal is null) then
            	raise exception 'El punto de venta con el que esta logueado en este momento no tiene un lugar asignado. Comuniquese con el administrador';
            end if;
            
            v_id_lugar_pais = (select param.f_get_id_lugar_pais(v_id_lugar_sucursal));
            
            if (v_id_lugar_pais is null) then
            	raise exception 'El punto de venta con el que esta logueado en este momento no tiene un pais relacionado. Comuniquese con el administrador';
            end if;
            
            v_rutas = string_to_array(v_parametros.rutas,'#');
            v_tipdoc = 'ETN';
            
            if (pxp.f_existe_parametro(p_tabla,'id_boleto') = TRUE) then
            	if (	(select tipdoc 
                		from obingresos.tboleto 
                        where id_boleto = v_parametros.id_boleto) = 'ETI') then
            		v_tipdoc = 'ETI';
            	end if;
            end if;
            if (exists (select 1
            			from obingresos.taeropuerto a
                        where a.codigo = ANY(v_rutas) and a.estado_reg = 'activo' and a.tipo_nalint ='I')) then
            	
                v_tipdoc = 'ETI';
            end if;
           select nextval('obingresos.tboleto_id_boleto_seq'::regclass) into v_id_boleto;        
        	
        	--Sentencia de la insercion
        	insert into obingresos.tboleto(
            id_boleto,
			id_agencia,
			id_moneda_boleto,
			estado_reg,
			comision,
			fecha_emision,
			total,
			pasajero,
			monto_pagado_moneda_boleto,
			liquido,
			nro_boleto,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod,
            neto,
            endoso,
            origen,
            destino,
            cupones,
            tipdoc,
            moneda,
            agt,
            retbsp,
            tc,
            moneda_sucursal,
            id_punto_venta,
            ruta_completa,
            localizador,
            identificacion
          	) values(
            v_id_boleto,
			v_id_agencia,
			v_id_moneda,
			'activo',
			0,
			v_fecha,
			v_parametros.total,
			v_parametros.pasajero,
			0,
			v_parametros.total,
			v_parametros.nro_boleto,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			v_parametros._id_usuario_ai,
			null,
			null,
            v_parametros.neto,
            v_parametros.endoso,
            v_parametros.origen,
            v_parametros.destino,
            v_parametros.cupones,		
			v_tipdoc,
            v_parametros.moneda,
            v_agt,
            'RET',
            v_tc,
            v_cod_moneda_sucursal,
            v_parametros.id_punto_venta,
            v_parametros.ruta_completa,
            v_parametros.localizador,
            v_parametros.identificacion
			);
            
            v_mensaje = '';
            v_suma_impuestos = 0;
            for v_registros in (select out_impuesto,out_valor 
            					from obingresos.f_get_impuestos_from_cadena(v_parametros.impuestos))LOOP
            	
                if (v_registros.out_impuesto = 'ERROR') THEN
                	v_mensaje = v_mensaje  || 'Error en la definicion del calculo tarifario XT <br>';
                end if;
                
                v_id_impuesto = NULL;
                select id_impuesto into v_id_impuesto
                from obingresos.timpuesto i
                where i.codigo = v_registros.out_impuesto and i.id_lugar = v_id_lugar_pais
                and i.tipodoc = v_tipdoc;
                
                if (v_id_impuesto is null) THEN
                	v_mensaje = v_mensaje  || 'No se encontro un impuesto parametrizado para : ' || v_registros.out_impuesto || ' ,pais:' || v_id_lugar_pais || ' , tipdoc,' || v_tipdoc || '<br>';
                end if;
                
                INSERT INTO 
                  obingresos.tboleto_impuesto
                (
                  id_usuario_reg,                  
                  importe,
                  id_impuesto,
                  id_boleto,
                  calculo_tarifa
                )
                VALUES (
                  p_id_usuario,                  
                  v_registros.out_valor,
                  v_id_impuesto,
                  v_id_boleto,
                  'si'
                );
                v_suma_impuestos = v_suma_impuestos + v_registros.out_valor;
                
            end loop;
            
             v_suma_tasas = 0;
            for v_registros in (select out_impuesto,out_valor 
            					from obingresos.f_get_impuestos_from_cadena(v_parametros.tasas))LOOP
            	
                v_id_impuesto = NULL;
                select id_impuesto into v_id_impuesto
                from obingresos.timpuesto i
                where i.codigo = v_registros.out_impuesto and i.id_lugar = v_id_lugar_pais
                and i.tipodoc = v_tipdoc;
                
                if (v_id_impuesto is null and v_registros.out_impuesto != 'XT') THEN
                	raise exception 'No se encontro un impuesto parametrizado para : % ,pais:% , tipdoc,%',v_registros.out_impuesto,v_id_lugar_pais,v_tipdoc;
                end if;
                if (v_registros.out_impuesto != 'XT') then
                    INSERT INTO 
                      obingresos.tboleto_impuesto
                    (
                      id_usuario_reg,                  
                      importe,
                      id_impuesto,
                      id_boleto
                    )
                    VALUES (
                      p_id_usuario,                  
                      v_registros.out_valor,
                      v_id_impuesto,
                      v_id_boleto
                    );
                else
                	update obingresos.tboleto 
                    set xt = v_registros.out_valor
                    where id_boleto = v_id_boleto;
                    
                    if (v_suma_impuestos != v_registros.out_valor) THEN
                		v_mensaje = v_mensaje  || 'La suma de las tasas/impuestos definidos en el calculo tarifario XT : ' || v_suma_impuestos || ' ,no es igual al valor de la tasa XT :' || v_registros.out_valor || '<br>';
                	end if;
                end if;
                v_suma_tasas = v_suma_tasas + v_registros.out_valor;
                
            end loop;
            
            if (v_suma_tasas + v_parametros.neto != v_parametros.total) then
            	raise exception 'El importe total del boleto no es igual a la suma del neto y las tasas/impuestos%',v_suma_tasas;
            end if;
            
            v_fp = string_to_array(substring(v_parametros.fp from 2),'#');
            v_moneda_fp = string_to_array(substring(v_parametros.moneda_fp from 2),'#');
            v_valor_fp = string_to_array(substring(v_parametros.valor_fp from 2),'#');
            v_posicion = 1;
            FOREACH v_forma_pago IN ARRAY v_fp
            LOOP
            	v_id_forma_pago = NULL;
                select id_forma_pago into v_id_forma_pago
                from obingresos.tforma_pago fp
                inner join param.tmoneda m on m.id_moneda = fp.id_moneda 
                where fp.codigo = v_forma_pago and 
                	m.codigo_internacional = v_moneda_fp[v_posicion] and 
                    fp.id_lugar = v_id_lugar_pais;
                    
                if (v_id_forma_pago is null) then
                	raise exception 'No existe la forma de pago:%',v_forma_pago;
                end if;
                    
            	 INSERT INTO 
                    obingresos.tboleto_forma_pago
                  (
                    id_usuario_reg,                    
                    importe,
                    id_forma_pago,
                    id_boleto
                  )
                  VALUES (
                    p_id_usuario,                   
                    v_valor_fp[v_posicion]::numeric,
                    v_id_forma_pago,
                    v_id_boleto
                  );                  
                  v_posicion = v_posicion + 1;
            END LOOP;    
            
            v_vuelos = string_to_array(v_parametros.vuelos,'$$$'); 
            
            FOREACH v_vuelo IN ARRAY v_vuelos
            LOOP
            	 v_vuelo_fields = string_to_array(v_vuelo,'|');
                 
                                    
            	 INSERT INTO 
                    obingresos.tboleto_vuelo
                  (
                    id_usuario_reg,  
                    id_boleto,
                    fecha,
                    vuelo,
                    hora_origen,
                    id_aeropuerto_origen,
                    id_aeropuerto_destino,
                    tarifa,
                    equipaje,
                    status
                  )
                  VALUES (
                    p_id_usuario,    
                    v_id_boleto,
                    to_date(v_vuelo_fields[1] || to_char(now(),'YY') , 'DDMONYY'),
                    v_vuelo_fields[2],
                    to_timestamp(v_vuelo_fields[3],'HH24MI')::time,
                    (select id_aeropuerto from obingresos.taeropuerto a where a.codigo = v_vuelo_fields[4]),
                    (select id_aeropuerto from obingresos.taeropuerto a where a.codigo = v_vuelo_fields[5]),
                    v_vuelo_fields[6],
                    v_vuelo_fields[7],
                    v_vuelo_fields[8]
                  );  
                  
            END LOOP; 
            
            --Boleto en conjuncion
            if (pxp.f_existe_parametro(p_tabla,'id_boleto') = TRUE) then
            	if (v_parametros.id_boleto is not null) then
                	if not EXISTS(	select 1 
                    				from obingresos.tboleto b
                                    where id_boleto = v_parametros.id_boleto and 
                                    	b.total = v_parametros.total and b.pasajero = v_parametros.pasajero) then
                    	raise exception 'No se encontro un boleto en conjuncion para este billete';	
                    else
                    	update obingresos.tboleto set tiene_conjuncion = 'si',
                        destino = v_parametros.destino,
                        ruta_completa = ruta_completa || '-' || substr (v_parametros.ruta_completa,5)
                        where id_boleto = v_parametros.id_boleto;
                        
                        update obingresos.tboleto_vuelo 
                        	set id_boleto_conjuncion = v_parametros.id_boleto
                        where id_boleto = v_id_boleto;
                        
                        update obingresos.tboleto
                        	set id_boleto_conjuncion = v_parametros.id_boleto                            
                        where id_boleto = v_id_boleto;
                    end if;
                end if;
            end if;    
            
            if (v_mensaje != '') then
            	update obingresos.tboleto
                    set mensaje_error = v_mensaje                            
                where id_boleto = v_id_boleto;
            end if;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos almacenado(a) con exito (id_boleto'||v_id_boleto||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_id_boleto::varchar);
            

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'OBING_BOL_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	elsif(p_transaccion='OBING_BOL_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tboleto set
			id_agencia = v_parametros.id_agencia,
			id_moneda_boleto = v_parametros.id_moneda_boleto,
			comision = v_parametros.comision,
			fecha_emision = v_parametros.fecha_emision,
			total = v_parametros.total,
			pasajero = v_parametros.pasajero,
			monto_pagado_moneda_boleto = v_parametros.monto_pagado_moneda_boleto,
			liquido = v_parametros.liquido,
			nro_boleto = v_parametros.nro_boleto,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_boleto=v_parametros.id_boleto;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_parametros.id_boleto::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_BOL_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	elsif(p_transaccion='OBING_BOL_ELI')then

		begin
			--Sentencia de la eliminacion
			update obingresos.tboleto set estado = NULL
            where id_boleto=v_parametros.id_boleto;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_parametros.id_boleto::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;
    /*********************************    
 	#TRANSACCION:  'OBING_BOLEST_MOD'
 	#DESCRIPCION:	Cambia el estado del boleto y valida que la forma de pago sea igual al total del boleto
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	elsif(p_transaccion='OBING_BOLEST_MOD')then

		begin
        	select * into v_boleto
            from obingresos.tboleto
            where id_boleto = v_parametros.id_boleto;
			
        	select obingresos.f_valida_boleto_fp(v_parametros.id_boleto) into v_res; 
        	
			update obingresos.tboleto 
            	set estado = v_parametros.accion
            where id_boleto=v_parametros.id_boleto;
            
            IF (v_parametros.accion = 'pagado') then
            	update obingresos.tboleto 
            	set id_usuario_cajero = p_id_usuario
            	where id_boleto=v_parametros.id_boleto;
                
                 --Si el usuario que cambia el estado del boleto a estado pagado no es cajero
                  --lanzamos excepcion
                  
                  if (exists(	select 1
                                  from vef.tapertura_cierre_caja acc
                                  where acc.id_usuario_cajero = p_id_usuario and 
                                  	acc.fecha_apertura_cierre = v_boleto.fecha_reg::date and 
                                    acc.estado_reg = 'activo' and acc.estado = 'cerrado' and 
                                    acc.id_punto_venta = v_boleto.id_punto_venta)) then
                      raise exception 'La caja ya fue cerrada, necesita tener la caja abierta para poder finalizar la venta del boleto';
                  end if;
                  
                  
                  if (not exists(	select 1
                                  from vef.tapertura_cierre_caja acc
                                  where acc.id_usuario_cajero = p_id_usuario and 
                                  	acc.fecha_apertura_cierre = v_boleto.fecha_reg::date and 
                                    acc.estado_reg = 'activo' and acc.estado = 'abierto' and 
                                    acc.id_punto_venta = v_boleto.id_punto_venta)) then
                      raise exception 'Antes de emitir un boleto debe realizar una apertura de caja';
                  end if;
            end if;            
              
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos Cambiado de estadocon exito'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_parametros.id_boleto::varchar);
              
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