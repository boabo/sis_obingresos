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
    v_error					varchar;
    v_id_alarma					integer;
    v_id_detalle_boletos_web	integer;
    v_procesado					varchar;
    v_id_moneda				integer;
    v_existe_autorizacion		integer;

	v_existe_billete		integer;
    v_existe_billete_anulado	integer;


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
              v_error = 'El campo endoso no esta bien definido para el boleto ' || v_registros."Billete";
            else
              v_nit = trim(both ' ' from split_part(v_aux,';',2));
              v_razon_social = trim(both ' ' from split_part(v_aux,';',3));

            end if;

          --separado por |
          elsif (position('|' in v_aux) > 0) then
            if (array_length(string_to_array(v_aux,'|'),1) != 3) then
              v_error = 'El campo endoso no esta bien definido para el boleto ' || v_registros."Billete";
            else
              v_nit = trim(both ' ' from split_part(v_aux,'|',2));
              v_razon_social = trim(both ' ' from split_part(v_aux,'|',3));
            end if;
          --separado por espacio
          elsif (position(' ' in v_aux) > 0) then
            if (array_length(string_to_array(v_aux,' '),1) < 3) then
              v_error =  'El campo endoso no esta bien definido para el boleto ' ||v_registros."Billete";
            else
              v_aux = substr(v_aux,position(' ' in v_aux) + 1);
              v_nit = trim(both ' ' from split_part(v_aux,' ',1));
              v_aux = substr(v_aux,position(' ' in v_aux) + 1);
              v_razon_social = trim(both ' ' from v_aux);
            end if;

          ELSE
          	if v_registros."Entidad" = 'BFS' then
            	v_razon_social = NULL;
            	v_nit = NULL;
            else
            	v_error = 'No existe un caracter de separacion en el campo endoso para el boleto ' || v_registros."Billete";
          	end if;
          end if;

          if (v_error != '') then
            --v_id_alarma = (select param.f_inserta_alarma_dblink (p_id_usuario,'Error al actualizar desde https://ef.boa.bo/Servicios/ServicioInterno.svc/DetalleDiario',v_error,'miguel.mamani@boa.bo,aldo.zeballos@boa.bo'));

            insert into obingresos.tdetalle_diario_error_vw(
            	id_usuario,
                asunto,
                desc_error,
                correos,
                fecha_venta
            )values(
            	coalesce(p_id_usuario, 1),
                'Error al actualizar desde https://ef.boa.bo/Servicios/ServicioInterno.svc/DetalleDiario',
                v_error,
                'aldo.zeballos@boa.bo, franklin.espinoza@boa.bo',
               	v_parametros.fecha
            );
            raise exception '%, Fecha : %',v_error,v_parametros.fecha;
          end if;

          select d.id_detalle_boletos_web,d.procesado into  v_id_detalle_boletos_web,v_procesado
          from obingresos.tdetalle_boletos_web d
          where d.billete = v_registros."Billete"::varchar;

          if ((v_id_detalle_boletos_web is not null and v_registros."MedioDePago" = 'COMPLETAR-CC') or v_procesado = 'no') then
                      update obingresos.tdetalle_boletos_web
                      set procesado = 'no',
                      endoso = v_registros."endoso",
                      nit = v_nit,
                      razon_social =  (case when v_razon_social = '' then NULL else upper(v_razon_social) END),
                      fecha_mod = to_date(v_parametros.fecha,'MM/DD/YYYY')
                      where  id_detalle_boletos_web = v_id_detalle_boletos_web;
          --if (v_id_detalle_boletos_web is null) then
            elsif (v_id_detalle_boletos_web is null) then
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
              (case when v_razon_social = '' then NULL else upper(v_razon_social) END)
            );
          end if;


        end loop;
        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma Pago almacenado(a) con exito (id_forma_pago'||v_id_forma_pago||')');
        v_resp = pxp.f_agrega_clave(v_resp,'id_forma_pago',v_id_forma_pago::varchar);

        --Devuelve la respuesta
        return v_resp;

      end;

	/*********************************
     #TRANSACCION:  'OBING_DETBOPOR_INS'
     #DESCRIPCION:	Insercion de registros Protal corporativo
     #AUTOR:		jrivera
     #FECHA:		10-06-2016 20:37:45
    ***********************************/

    elsif(p_transaccion='OBING_DETBOPOR_INS')then

      begin

          select d.id_detalle_boletos_web,d.procesado into  v_id_detalle_boletos_web,v_procesado
          from obingresos.tdetalle_boletos_web d
          where d.billete = v_parametros.billete and d.estado_reg = 'activo';

          if (v_id_detalle_boletos_web is not null) then
          		raise exception 'El billete % ya esta registrado en el ERP', v_parametros.billete;
            elsif (v_id_detalle_boletos_web is null) then

            select m.id_moneda into v_id_moneda
            from param.tmoneda m
            where m.codigo_internacional = v_parametros.moneda;

            if (trim(v_parametros.numero_autorizacion) = '' or trim(v_parametros.numero_autorizacion) = 'null' or trim(v_parametros.numero_autorizacion) = 'Null' or trim(v_parametros.numero_autorizacion) = 'NULL') then
            	raise exception 'El nro de Autorización es vacio, Favor verifique';
            else
              /*Control para verificar si el codigo de control que envian es el correcto Ismael Valdivia 09/08/2021*/
              select count (mv.id_movimiento_entidad) into v_existe_autorizacion
              from obingresos.tmovimiento_entidad mv
              where mv.autorizacion__nro_deposito = trim(v_parametros.numero_autorizacion);

              if (v_existe_autorizacion = 0) then
                  raise exception 'El número de autorización enviado: % no existe en los movimientos de la entidad, favor verifique',v_parametros.numero_autorizacion;
              end if;
              /**************************************************************************/

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
                razon_social,
                fecha_pago,
                id_agencia,
                comision,
                numero_tarjeta,
                numero_autorizacion,
                id_moneda,
                neto
              )
            VALUES (
              p_id_usuario,
               v_parametros.billete,
               NULL,
               v_parametros.medio_pago,--CUENTA-CORRI,PBANCA-ELECTRONI
               v_parametros.entidad,--NINGUNA
               v_parametros.moneda,
               v_parametros.importe,
              '',--endoso
              'portal',
              v_parametros.fecha_emision,
              v_parametros.nit,
              v_parametros.razon_social,
              v_parametros.fecha_pago,
              v_parametros.id_entidad,
              v_parametros.comision,
              NULL,
              v_parametros.numero_autorizacion,
              v_id_moneda,
              v_parametros.neto
            )returning id_detalle_boletos_web into v_id_detalle_boletos_web;
          end if;

        --actualizar neto y fecha de emision
        with aux as (
        select dbw.numero_autorizacion, sum(neto) as neto
        from obingresos.tdetalle_boletos_web dbw
        where dbw.estado_reg = 'activo' and
        	dbw.origen = 'portal' and dbw.medio_pago = 'CUENTA-CORRI' and
            dbw.numero_autorizacion =  v_parametros.numero_autorizacion
        group by dbw.numero_autorizacion)
        update obingresos.tmovimiento_entidad
        set neto = a.neto,
        fecha = v_parametros.fecha_emision
        from aux a
        where a.numero_autorizacion = obingresos.tmovimiento_entidad.autorizacion__nro_deposito;


        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle Boletos insertado correctamente (id_detalle_boletos_web'||v_id_detalle_boletos_web||')');
        v_resp = pxp.f_agrega_clave(v_resp,'id_detalle_boletos_web',v_id_detalle_boletos_web::varchar);

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
        select pxp.list(to_char(i::date,'MM/DD/YYYY')) into v_fecha_text
        from generate_series('01/08/2017'::date,
        --from generate_series('01/04/2019'::date,
                             --'02/04/2019', '1 day'::interval) i;
                             now()::date - interval '1 day', '1 day'::interval) i;


        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Fecha maxima obtenida');
        v_resp = pxp.f_agrega_clave(v_resp,'fecha',v_fecha_text::varchar);

        --Devuelve la respuesta
        return v_resp;

      end;

    /*********************************
 	#TRANSACCION:  'OBING_BOWEBPROC_MOD'
 	#DESCRIPCION:	Procesa boletos de la tabla q esten con procesado no
 	#AUTOR:		jrivera
 	#FECHA:		10-06-2016 20:37:45
	***********************************/

    elsif(p_transaccion='OBING_BOWEBPROC_MOD')then

      begin
       /*COMENTAR EL LLAMADO PARA QUE NO EJECUTE LA ACTUALIZACION A FUTURO DESCOMENTAR*/
      /*	for v_fecha in select i::date
        from generate_series('01/08/2017'::date,
        --from generate_series('01/04/2019'::date,

                             --'01/04/2019', '1 day'::interval) i loop
                             now()::date - interval '1 day', '1 day'::interval) i loop


          if (exists (select 1
          		from obingresos.tboleto b
                where b.fecha_emision = v_fecha and b.estado_reg = 'activo')) then
              for v_registros in
              select  *
              from obingresos.tdetalle_boletos_web d
              where origen = 'web' and procesado = 'no' and fecha = v_fecha
              --limit 1
              loop

                --execute ('select informix.f_modificar_datos_web('''||v_registros.billete::varchar||''')');




              end loop;
          end if;
        end loop;*/

       /* for v_registros in
        select  *
        from obingresos.tventa_web_modificaciones vwm
        where tipo = 'reemision' and procesado = 'no' loop

          execute ('select informix.f_modificar_datos_web_reemision(''' || v_registros.nro_boleto_reemision || ''',''' || v_registros.nro_boleto || ''')');
        end loop;*/
 /*****************************************************************************************/


        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Cambios procesados');


        --Devuelve la respuesta
        return v_resp;

      end;

      /*********************************
     #TRANSACCION:  'OBING_ANUBILLETE_INS'
     #DESCRIPCION:	Anular Billetes desde el portal
     #AUTOR:		Ismael.Valdivia
     #FECHA:		10-09-2021 08:17:00
    ***********************************/

    elsif(p_transaccion='OBING_ANUBILLETE_INS')then

      begin

        select count(bolweb.billete) into v_existe_billete_anulado
        from obingresos.tdetalle_boletos_web bolweb
        where trim(bolweb.billete) = trim(v_parametros.billete)
        and trim(bolweb.numero_autorizacion) = trim(v_parametros.numero_autorizacion)
        and bolweb.estado_reg = 'inactivo';

        if (v_existe_billete_anulado > 0) then
        	raise exception 'El Boleto: %, con número de autorización: %, ya se encuentra Anulado.',trim(v_parametros.billete),trim(v_parametros.numero_autorizacion);
        end if;


        select count(bolweb.billete) into v_existe_billete
        from obingresos.tdetalle_boletos_web bolweb
        where trim(bolweb.billete) = trim(v_parametros.billete)
        and trim(bolweb.numero_autorizacion) = trim(v_parametros.numero_autorizacion)
        and bolweb.estado_reg = 'activo';

		if (v_existe_billete = 0) then
        	raise exception 'No existe el Boleto: %, con número de autorización: %, favor verifique.',trim(v_parametros.billete),trim(v_parametros.numero_autorizacion);
        end if;

      	update obingresos.tdetalle_boletos_web set
      	estado_reg = 'inactivo',
        fecha_mod = now(),
        id_usuario_mod = p_id_usuario
        where trim(billete) = trim(v_parametros.billete)
        and trim(numero_autorizacion) = trim(v_parametros.numero_autorizacion);

        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Anulación Exitosa');
        v_resp = pxp.f_agrega_clave(v_resp,'Respuesta','Se anuló el Boleto: '||trim(v_parametros.billete)::varchar||', con número de autorización: '||trim(v_parametros.numero_autorizacion)||' correctamente.');

        --Devuelve la respuesta
        return v_resp;

      end;


    else

      raise exception 'Transaccion inexistente: %',p_transaccion;

    end if;

    EXCEPTION

    WHEN OTHERS THEN
      if(p_transaccion='OBING_DETBOWEB_INS')then
      	--v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Error al leer informacion desde venta web para la fecha ' || v_parametros.fecha,SQLERRM,'miguel.mamani@boa.bo,aldo.zeballos@boa.bo'));
        insert into obingresos.tdetalle_diario_error_vw(
            	id_usuario,
                asunto,
                desc_error,
                correos,
                fecha_venta
            )values(
            	coalesce(p_id_usuario, 1),
                'Error al leer informacion desde venta web para la fecha ' || v_parametros.fecha,
                SQLERRM,
                'aldo.zeballos@boa.bo, franklin.espinoza@boa.bo, ismael.valdivia@boa.bo',
               	v_parametros.fecha
        );
      end if;
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

ALTER FUNCTION obingresos.ft_detalle_boletos_web_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
