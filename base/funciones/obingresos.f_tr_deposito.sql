CREATE OR REPLACE FUNCTION obingresos.f_tr_deposito (
)
RETURNS trigger AS
$body$
DECLARE
  v_agencia				record;
  v_filtro				varchar;
  v_periodo_venta		record;
  v_saldo				numeric;
  v_monto_a_usar		numeric;
  v_fecha_conversion	date;
  v_saldo_menor_boleto	boolean;
  v_tc					numeric;
  v_boleto_completo		boolean;
  v_id_agencia			integer;
  v_id_moneda_base		integer;
  v_id_moneda_usd		integer;
  v_consulta			varchar;
  v_monto_mb			numeric;
BEGIN
	v_id_agencia = NEW.id_agencia;
   
    if (v_id_agencia is null) then
    	
    	return NEW;
    elsif (v_id_agencia is not null and NEW.estado = 'validado' and OLD.estado = 'borrador') then
    	v_id_moneda_base = (select param.f_get_moneda_base()); 
        select m.id_moneda into v_id_moneda_usd
        from param.tmoneda m
        where m.codigo_internacional = 'USD';
         if (NEW.id_moneda_deposito != v_id_moneda_base and NEW.id_moneda_deposito != v_id_moneda_usd) then
         	raise exception 'Solo se permite depositos en moneda base o USD';
         end if;
    	if (NEW.id_periodo_venta is not null) then
        	select * into v_periodo_venta
            from obingresos.tperiodo_venta_agencia pv
            where pv.id_periodo_venta = NEW.id_periodo_venta and id_agencia = v_id_agencia;
            
            if (v_periodo_venta.estado = 'cerrado') then
            	raise exception 'El periodo de  venta esta cerrado y no se puede registrar depositos en este periodo';
            end if;
            
            --si es moneda restrictiva
            if (v_periodo_venta.moneda_restrictiva = 'si' ) then
            	
            	--si el deposito es en la moneda base
                if (NEW.id_moneda_deposito = v_id_moneda_base) then                	
                    --actualizamos el periodo de la agencia
                    update obingresos.tperiodo_venta_agencia
                    set monto_mb = (case when NEW.monto_deposito >= (monto_mb*-1) then 0 else monto_mb + NEW.monto_deposito END),
                    monto_credito_mb = monto_credito_mb + (case when NEW.monto_deposito >= (monto_mb*-1) then (monto_mb*-1) else NEW.monto_deposito END)
                    where id_periodo_venta = NEW.id_periodo_venta and id_agencia = v_id_agencia;
                	--si hay un saldo lo actualizamos en el siguiente periodo
                    if (NEW.monto_deposito > (v_periodo_venta.monto_mb*-1)) then
                    	INSERT INTO 
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,                        
                        id_usuario_ai,
                        usuario_ai,                        
                        tipo,
                        pnr,
                        fecha,
                        apellido,
                        monto,
                        id_moneda,
                        autorizacion__nro_deposito,
                        garantia,
                        ajuste,
                        id_periodo_venta,
                        id_agencia,
                        monto_total
                      )
                      VALUES (
                        NEW.id_usuario_reg,                        
                         NEW.id_usuario_ai,
              		  	 NEW.usuario_ai,
                        'credito',
                        NULL,
                        NEW.fecha,
                        NULL,
                        (NEW.monto_deposito + v_periodo_venta.monto_mb),
                        v_id_moneda_base,
                        NEW.nro_deposito,
                        'no',
                        'no',
                        NULL,
                        v_id_agencia,
                        (NEW.monto_deposito + v_periodo_venta.monto_mb)
                      );
                    end if;
                    --insertamos el movimiento en el periodo actual
                    INSERT INTO 
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,                        
                        id_usuario_ai,
                        usuario_ai,                        
                        tipo,
                        pnr,
                        fecha,
                        apellido,
                        monto,
                        id_moneda,
                        autorizacion__nro_deposito,
                        garantia,
                        ajuste,
                        id_periodo_venta,
                        id_agencia,
                        monto_total
                      )
                      VALUES (
                        NEW.id_usuario_reg,                        
                         NEW.id_usuario_ai,
              		  	 NEW.usuario_ai,
                        'credito',
                        NULL,
                        NEW.fecha,
                        NULL,
                        (case when (NEW.monto_deposito >= (v_periodo_venta.monto_mb*-1)) then
                        	(v_periodo_venta.monto_mb*-1)
                        ELSE
                        	NEW.monto_deposito
                        end),
                        v_id_moneda_base,
                        NEW.nro_deposito,
                        'no',
                        'no',
                        NEW.id_periodo_venta,
                        v_id_agencia,
                        (case when (NEW.monto_deposito >= (v_periodo_venta.monto_mb*-1)) then
                        	(v_periodo_venta.monto_mb*-1)
                        ELSE
                        	NEW.monto_deposito
                        end)
                      );
                else --en dolares
                	--actualizamos el periodo de la agencia
                    update obingresos.tperiodo_venta_agencia
                    set monto_usd = (case when NEW.monto_deposito >= (monto_usd*-1) then 0 else monto_usd + NEW.monto_deposito END),
                    monto_credito_usd = monto_credito_usd + (case when NEW.monto_deposito >= (monto_usd*-1) then (monto_usd*-1) else NEW.monto_deposito END) 
                    where id_periodo_venta = NEW.id_periodo_venta and id_agencia = v_id_agencia;
                	--si hay un saldo lo actualizamos en el siguiente periodo
                    if (NEW.monto_deposito > (v_periodo_venta.monto_usd *-1)) then
                    	INSERT INTO 
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,                        
                        id_usuario_ai,
                        usuario_ai,                        
                        tipo,
                        pnr,
                        fecha,
                        apellido,
                        monto,
                        id_moneda,
                        autorizacion__nro_deposito,
                        garantia,
                        ajuste,
                        id_periodo_venta,
                        id_agencia,
                        monto_total
                      )
                      VALUES (
                        NEW.id_usuario_reg,                        
                         NEW.id_usuario_ai,
              		  	 NEW.usuario_ai,
                        'credito',
                        NULL,
                        NEW.fecha,
                        NULL,
                        (NEW.monto_deposito + v_periodo_venta.monto_usd),
                        v_id_moneda_base,
                        NEW.nro_deposito,
                        'no',
                        'no',
                        NULL,
                        v_id_agencia,
                        (NEW.monto_deposito + v_periodo_venta.monto_usd)
                      );
                    end if;
                    --insertamos el movimiento en el periodo actual
                    INSERT INTO 
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,                        
                        id_usuario_ai,
                        usuario_ai,                        
                        tipo,
                        pnr,
                        fecha,
                        apellido,
                        monto,
                        id_moneda,
                        autorizacion__nro_deposito,
                        garantia,
                        ajuste,
                        id_periodo_venta,
                        id_agencia,
                        monto_total
                      )
                      VALUES (
                        NEW.id_usuario_reg,                        
                         NEW.id_usuario_ai,
              		  	 NEW.usuario_ai,
                        'credito',
                        NULL,
                        NEW.fecha,
                        NULL,
                        (case when (NEW.monto_deposito >= (v_periodo_venta.monto_usd*-1)) then
                        	(v_periodo_venta.monto_usd*-1)
                        ELSE
                        	NEW.monto_deposito
                        end),
                        v_id_moneda_base,
                        NEW.nro_deposito,
                        'no',
                        'no',
                        NEW.id_periodo_venta,
                        v_id_agencia,
                        (case when (NEW.monto_deposito >= (v_periodo_venta.monto_usd*-1)) then
                        	(v_periodo_venta.monto_usd*-1)
                        ELSE
                        	NEW.monto_deposito
                        end)
                      );
                
                end if;
            else --si la moneda no es restrictiva
            	--actualizamos el periodo de la agencia
                	v_monto_mb = (case when NEW.id_moneda_deposito = v_id_moneda_base then
                    					NEW.monto_deposito
                    				else
                                    	param.f_convertir_moneda(v_id_moneda_usd,v_id_moneda_base,NEW.monto_deposito,NEW.fecha)
                                    end);  
                    update obingresos.tperiodo_venta_agencia
                    set monto_mb = (case when v_monto_mb >= (monto_mb*-1) then 0 else monto_mb + v_monto_mb end),
                    monto_credito_mb = monto_credito_mb + (case when v_monto_mb >= (monto_mb*-1) then (monto_mb*-1) else v_monto_mb END)
                    where id_periodo_venta = NEW.id_periodo_venta and id_agencia = v_id_agencia;
                	--si hay un saldo lo actualizamos en el siguiente periodo
                    if (v_monto_mb > (v_periodo_venta.monto_mb*-1)) then
                    	INSERT INTO 
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,                        
                        id_usuario_ai,
                        usuario_ai,                        
                        tipo,
                        pnr,
                        fecha,
                        apellido,
                        monto,
                        id_moneda,
                        autorizacion__nro_deposito,
                        garantia,
                        ajuste,
                        id_periodo_venta,
                        id_agencia,
                        monto_total
                      )
                      VALUES (
                        NEW.id_usuario_reg,                        
                         NEW.id_usuario_ai,
              		  	 NEW.usuario_ai,
                        'credito',
                        NULL,
                        NEW.fecha,
                        NULL,
                        (v_monto_mb + v_periodo_venta.monto_mb),
                        v_id_moneda_base,
                        NEW.nro_deposito,
                        'no',
                        'no',
                        NULL,
                        v_id_agencia,
                        (v_monto_mb + v_periodo_venta.monto_mb)
                      );
                    end if;
                    INSERT INTO 
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,                        
                        id_usuario_ai,
                        usuario_ai,                        
                        tipo,
                        pnr,
                        fecha,
                        apellido,
                        monto,
                        id_moneda,
                        autorizacion__nro_deposito,
                        garantia,
                        ajuste,
                        id_periodo_venta,
                        id_agencia,
                        monto_total
                      )
                      VALUES (
                        NEW.id_usuario_reg,                        
                         NEW.id_usuario_ai,
              		  	 NEW.usuario_ai,
                        'credito',
                        NULL,
                        NEW.fecha,
                        NULL,
                        (case when (v_monto_mb >= (v_periodo_venta.monto_mb*-1)) then
                        	(v_periodo_venta.monto_mb*-1)
                        ELSE
                        	v_monto_mb
                        end),
                       
                        v_id_moneda_base,
                        NEW.nro_deposito,
                        'no',
                        'no',
                        NEW.id_periodo_venta,
                        v_id_agencia,
                        (case when (v_monto_mb >= (v_periodo_venta.monto_mb*-1)) then
                        	(v_periodo_venta.monto_mb*-1)
                        ELSE
                        	v_monto_mb
                        end)
                      );                    
            	             
            end if;
            
            --modificar el estado del periodo de venta si los monto_mb y monto_usd son 0 o null
            update obingresos.tperiodo_venta_agencia
            set estado = 'cerrado'
            where  id_periodo_venta = NEW.id_periodo_venta and id_agencia = v_id_agencia and
            (monto_mb = 0 or monto_mb is null) and (monto_usd = 0 or monto_usd is null);
        else -- no tiene id_periodo_venta añadir al periodo actual
        	INSERT INTO 
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,                        
                        id_usuario_ai,
                        usuario_ai,                        
                        tipo,
                        pnr,
                        fecha,
                        apellido,
                        monto,
                        id_moneda,
                        autorizacion__nro_deposito,
                        garantia,
                        ajuste,
                        id_periodo_venta,
                        id_agencia,
                        monto_total
                      )
                      VALUES (
                        NEW.id_usuario_reg,                        
                         NEW.id_usuario_ai,
              		  	 NEW.usuario_ai,
                        'credito',
                        NULL,
                        NEW.fecha,
                        NULL,
                        NEW.monto_deposito,
                        NEW.id_moneda_deposito,
                        NEW.nro_deposito,
                        'no',
                        'no',
                        NULL,
                        v_id_agencia,
                        NEW.monto_deposito
                      );
        
        end if;
    	return NEW;
    else
    	return NEW;
    end if;
    
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;