CREATE OR REPLACE FUNCTION obingresos.ftrig_partition_boleto (
)
RETURNS trigger AS
$body$
  DECLARE
    nombre_tabla   varchar;
    consulta	   varchar;
    valores			varchar;
    fecha1			date;
    fecha2 			date;
    crear_tabla		text;
    v_rol 			varchar;
    campos			varchar;
    v_id_boleto		integer;


  BEGIN

    /***************************************************************************
     XPHS - PARTICIONAMIENTO LOGS
    ***************************************************************************
     SCRITP: 		segu.ftrig_partition_boleto
     DESCRIPCION: 	Ingreso de registro de boleto		(tablas particionadas)
     AUTOR: 		KPLIAN(jrr)
     FECHA:			02/02/2011
     COMENTARIOS:
    ***************************************************************************
      1) Se obtiene el nombre de tabla que corresponde para la fecha del boleto
        2) IF: existe la tabla
          2.1) Se registra el boleto en la tabla particionada correspondiente
        3) ELSE no existe la tabla
          3.1) Se define el rango de fechas el que se creara la tabla (anual)
            3.2) Se crea la tabla con el nombre y el rango de fechas que corresponde
            3.4) Se inserta el boleto en la tabla particionada correspondiente



    */
    IF (TG_OP='INSERT')then
      BEGIN
        nombre_tabla='tboleto_'||to_char(NEW.fecha_emision,'YYYY');

        if(not exists (select 1 from pg_class where relname like nombre_tabla))then

          fecha1:=('01/01/' || to_char(NEW.fecha_emision,'YYYY'))::date;
          fecha2:= ('31/12/' || to_char(NEW.fecha_emision,'YYYY'))::date;

          crear_tabla:='CREATE TABLE "obingresos"."'||nombre_tabla||'" (
      			  CHECK ((fecha_emision >= '''||fecha1||'''::date) AND (fecha_emision <= '''||fecha2||'''::date)),
  				  CONSTRAINT "'||nombre_tabla||'_id_boleto_key" UNIQUE("id_boleto")
  				) INHERITS ("obingresos"."tboleto");
                CREATE UNIQUE INDEX "'||nombre_tabla||'_nro_idx" ON "obingresos"."'||nombre_tabla||'"
  				USING btree ("nro_boleto")
                WHERE estado_reg = ''activo'';               
                CREATE INDEX "'||nombre_tabla||'_fecha_idx" ON "obingresos"."'||nombre_tabla||'"
  				USING btree ("fecha_emision");
                CREATE INDEX "'||nombre_tabla||'_localizador_idx" ON "obingresos"."'||nombre_tabla||'"
  				USING btree ("localizador");                
                ';

          execute(crear_tabla);

        end if;
        campos = '
      id_usuario_reg,  
      fecha_reg,  
      estado_reg,  
      id_boleto,
      nro_boleto,
      pasajero,
      fecha_emision,
      total, 
      neto,
      comision,
      liquido,      
      id_moneda_boleto,  
      id_agencia,
      moneda,
      agt,
      agtnoiata,
      gds,
      tipdoc,
      retbsp,
      monto_pagado_moneda_boleto,
      ruta,
      cupones,
      origen,
      destino,
      endoso,
      moneda_sucursal,
      tc,
      id_punto_venta,
      ruta_completa,
      localizador,
      identificacion,
      tipopax,
      voided,
      nit,
      razon,
      medio_pago,
      agente_venta,
      forma_pago,
      fare_calc
      ';
		 valores:=NEW.id_usuario_reg ||','''||
                 NEW.fecha_reg||''','''||
                 NEW.estado_reg||''','||
                 NEW.id_boleto||','''||
                 NEW.nro_boleto||''','||
                 coalesce ('''' || NEW.pasajero || '''','NULL')||','''||
                 NEW.fecha_emision||''','||
                 NEW.total||','||
                 NEW.neto||','||
                 NEW.comision||','||
                 NEW.liquido||','||
                 coalesce ( NEW.id_moneda_boleto::text,'NULL')||','||
                 NEW.id_agencia||','||
                 coalesce ('''' || NEW.moneda || '''','NULL')||','||
                 coalesce ('''' || NEW.agt||'''','NULL')||','||
                 coalesce ('''' || NEW.agtnoiata || '''','NULL')||','||
                 coalesce ('''' || NEW.gds || '''','NULL')||','||
                 coalesce ('''' || NEW.tipdoc || '''','NULL')||','||
                 coalesce ('''' || NEW.retbsp || '''', 'NULL')||',
  0,'||
                 coalesce ('''' || NEW.ruta || '''','NULL')||','||
                 coalesce ('''' || NEW.cupones || '''', 'NULL') || ','||
                 coalesce ('''' || NEW.origen || '''','NULL')||','||
                 coalesce ('''' || NEW.destino || '''','NULL')||','||
                 coalesce ('''' || NEW.endoso || '''','NULL')||','||
                 coalesce ('''' || NEW.moneda_sucursal || '''','NULL')||','||
                 coalesce ( NEW.tc::text,'NULL')||','||
                 coalesce ( NEW.id_punto_venta::text,'NULL')||','||
                 coalesce ('''' || NEW.ruta_completa || '''','NULL')||','||
                 coalesce ('''' || NEW.localizador || '''','NULL')||','||
                 coalesce ('''' || NEW.identificacion || '''','NULL')||','||
                 coalesce ('''' || NEW.tipopax || '''','NULL')||','||

                 coalesce ('''' || NEW.voided || '''','NULL')||','||
                 coalesce ('''' || NEW.nit || '''','NULL')||','||
                 coalesce ('''' || NEW.razon || '''','NULL')||','||
                 coalesce ('''' || NEW.medio_pago || '''','NULL')||','||
                 coalesce ('''' || NEW.agente_venta || '''','NULL')||','||
                 coalesce ('''' || NEW.forma_pago || '''','NULL')||','||
                 coalesce ('''' || NEW.fare_calc || '''','NULL');
        raise notice 'valores %',valores;
        consulta='INSERT INTO obingresos.'||nombre_tabla||' (' || campos || ') VALUES ('||valores||');';

        EXECUTE(consulta);
        if ( exists (
            select 1
            from segu.tsubsistema s
            where s.codigo like 'VEF')) then


          update vef.tventa_detalle
          set id_boleto = v_id_boleto
          where   descripcion is not null and descripcion != '' and
                  id_boleto is null and descripcion = NEW.nro_boleto;

        end if;


      END;
    end if;
    RETURN NULL;
  END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY DEFINER
COST 100;