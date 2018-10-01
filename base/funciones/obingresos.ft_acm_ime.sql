CREATE OR REPLACE FUNCTION obingresos.ft_acm_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_acm_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tacm'
 AUTOR: 		 (ivaldivia)
 FECHA:	        05-09-2018 20:34:32
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				05-09-2018 20:34:32								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tacm'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_acm_bs				integer;
    v_id_acm_sus			integer;
    v_id_acm				integer;
    g_indice				numeric;
    v_id_archivo_acm_det	integer;

    v_contador				integer;

    v_editar				integer;
    contador				record;
    v_registros				record;
  	v_arreglo				INTEGER[];
    v_length				integer;
    v_boletos_bs			record;
    v_correlativo			varchar;
    v_suma_importe 			FLOAT;
	v_suma_neto 			FLOAT;
    v_contador_boletos		integer;
    v_suma_total			record;
    v_boletos_sus			record;
    v_ultimo_numero			integer;
    v_resetear				integer;
    v_datos_boletos			record;
    v_porcentaje			integer;
    v_suma_bsp				FLOAT;

BEGIN

    v_nombre_funcion = 'obingresos.ft_acm_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_acm_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		05-09-2018 20:34:32
	***********************************/

	if(p_transaccion='OBING_acm_INS')then



        begin


                                        --Sentencia de la insercion
                                        insert into obingresos.tacm(
                                        id_moneda,
                                        id_archivo_acm_det,
                                        fecha,
                                        numero,
                                        ruta,
                                        estado_reg,
                                        importe,
                                        id_usuario_ai,
                                        id_usuario_reg,
                                        fecha_reg,
                                        usuario_ai,
                                        id_usuario_mod,
                                        fecha_mod
                                        ) values(
                                        v_parametros.id_moneda,
                                        v_parametros.id_archivo_acm_det,
                                        v_parametros.fecha,
                                        v_parametros.numero,
                                        v_parametros.ruta,
                                        'activo',
                                        v_parametros.importe,
                                        v_parametros._id_usuario_ai,
                                        p_id_usuario,
                                        now(),
                                        v_parametros._nombre_usuario_ai,
                                        null,
                                        null



                                        )RETURNING id_acm into v_id_acm;





			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','ACM almacenado(a) con exito (id_acm'||v_id_acm||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_acm',v_id_acm::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_INSERTAR_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		05-09-2018 20:34:32
	***********************************/

	elsif(p_transaccion='OBING_INSERTAR_INS')then



        begin



        v_arreglo = string_to_array(v_parametros.id_archivo_acm,',');
                      v_length = array_length(v_arreglo,1);

        update obingresos.tarchivo_acm ac set
            estado = 'generado'
            where ac.id_archivo_acm = v_parametros.id_archivo_acm::integer;

       --raise exception 'El ID del Archivo ACM es: %', v_parametros.id_archivo_acm;


FOR 	v_registros in (select 	arch.id_agencia,
                                ar.fecha_ini,
                                ar.fecha_fin,
                                arch.porcentaje,
                                arch.id_archivo_acm_det,
                                agen.tipo_agencia,
                                agen.nombre
                                from obingresos.tarchivo_acm_det arch
                                inner join obingresos.tarchivo_acm ar on ar.id_archivo_acm = arch.id_archivo_acm
                                inner join obingresos.tagencia agen on agen.id_agencia = arch.id_agencia
                        		where ar.id_archivo_acm = v_parametros.id_archivo_acm::integer) LOOP


      --##########OBTENEMOS TODOS LOS BOLETOS EN DOLARES E INSERTAMOS EN TACM Y TEACMDET#########--

        for v_boletos_sus in (select
                          bole.id_detalle_boletos_web,
                          bole.billete,
                          bole.id_agencia,
                          bole.neto,
                          boletos.comision ,
                          boletos.ruta,
                          boletos.moneda,
                          boletos.tipdoc
                          --acmd.id_agencia
                          from obingresos.tdetalle_boletos_web bole
                          inner join obingresos.tmovimiento_entidad mov on mov.autorizacion__nro_deposito = bole.numero_autorizacion
                          inner join obingresos.tboleto boletos on boletos.id_agencia = mov.id_agencia
                          where mov.id_agencia = v_registros.id_agencia
                          and mov.fecha between v_registros.fecha_ini and v_registros.fecha_fin
                          and mov.id_moneda=2
                          and bole.billete = boletos.nro_boleto)  LOOP




           Select acm.id_acm
           into v_id_acm_sus
           from obingresos.tacm acm
           where acm.id_archivo_acm_det=v_registros.id_archivo_acm_det
           and acm.id_moneda=2;

           select
                          archivo.porcentaje
                          into v_porcentaje
                          from obingresos.tarchivo_acm_det archivo  ;


           if  v_id_acm_sus is null then

            --OBTENEMOS EL CORRELATIVO DEL CAMPO NUMERO
             SELECT *
              into v_correlativo
              FROM param.f_obtener_correlativo(
                        'ACM', --codigo documento
                         NULL,-- par_id,
                        NULL, --id_uo
                        1, --depto
                        1, --usuario
                        'OBINGRESOS', --codigo depto
                        NULL,--formato
                        1); --id_empresa

           --INSERTAMOS DATOS EN ACM
              --Sentencia de la insercion
              if v_registros.tipo_agencia = 'noiata' THEN
              insert into obingresos.tacm(
              id_moneda,
              id_archivo_acm_det,
              fecha,
              numero,
              ruta,
              estado_reg,
              id_usuario_reg,
              fecha_reg
              ) values(
              1,
              v_registros.id_archivo_acm_det,
              now(),
              v_correlativo,--generar correlativo
              'ruta',--recuperar de tablas
              --v_suma_importe.comision_total,
              'activo',
              p_id_usuario,
              now()
              )RETURNING id_acm into v_id_acm_sus;

          --OBTENEMOS EL ULTIMO NUMERO PARA LA INSERCION CUANDO SE REVIERTA
          Select corre.num_actual
          into
          v_ultimo_numero
          from param.tcorrelativo corre
          inner join param.tdocumento d on d.id_documento = corre.id_documento
          inner join segu.tsubsistema s on s.id_subsistema = d.id_subsistema
          and s.codigo = 'OBINGRESOS'
          WHERE d.estado_reg='activo' and d.codigo = 'ACM';

          UPDATE obingresos.tarchivo_acm ac SET
		  ultimo_numero = (v_ultimo_numero)
		  Where ac.id_archivo_acm = v_parametros.id_archivo_acm::INTEGER;
          --------------------------------------------------------------------
      	  ELSE
          raise exception 'La Agencia: % es del tipo: % el ACM se genera solo con Agencias del Tipo NOIATA',v_registros.nombre, v_registros.tipo_agencia;
          end if;

         -- raise exception 'EL ultimo numero es: %', v_ultimo_numero;
          end if;

          --INSERTAMOS DATOS RECUPERADOS EN ACM DET
              if v_porcentaje BETWEEN 2 and 2 or 4 and 4 then
              insert into obingresos.tacm_det(
              id_acm,
              id_detalle_boletos_web,
              neto,
              over_comision,
              fecha_reg,
              id_usuario_reg,
              com_bsp,
              moneda,
              td,
              porcentaje_over
              )
              VALUES(
              v_id_acm_sus,
              v_boletos_sus.id_detalle_boletos_web,
              v_boletos_sus.neto,
              (v_boletos_sus.neto * v_registros.porcentaje)/100,
              now(),
              p_id_usuario,
              v_boletos_sus.comision*(-1),
              v_boletos_sus.moneda,
              v_boletos_sus.tipdoc,
              v_porcentaje
              );
          --OBTENEMOS LA SUMA DE LA COMISION Y LO ALMACENAMOS EN EL CAMPO IMPORTE DE ACM
          select round (sum(acmdet.over_comision),2)
           						into v_suma_importe
								from obingresos.tacm_det acmdet
                        		where id_acm = v_id_acm_sus;

           UPDATE obingresos.tacm SET
		   importe = v_suma_importe
		   Where id_acm = v_id_acm_sus;
          -----------------------------------------------------------------------------

           ---ACTUALIZAMOS EL CAMPO IMPORTE TOTAL MT EN LA TABLA ARCHIVO ACM DET
           UPDATE obingresos.tarchivo_acm_det SET
		   importe_total_mt = v_suma_importe
		   Where id_archivo_acm_det=v_registros.id_archivo_acm_det;

           select round (sum(acmdet.neto),2)
           						into v_suma_neto
								from obingresos.tacm_det acmdet
                        		where id_acm = v_id_acm_sus;
          ------------------------------------------------------------------------------

           --ACTUALIZAMOS EL NETO TOTAL MT EN LA TABLA ARCHIVO ACM DET
           UPDATE obingresos.tarchivo_acm_det SET
		   neto_total_mt = v_suma_neto
		   Where id_archivo_acm_det=v_registros.id_archivo_acm_det;
           -------------------------------------------------------------------------------

           --OBTENEMOS LA CANTIDAD DE BOLETOS Y ACTUALIZAMOS EN EL CAMPO CAT_BOL_MT EN ARCHIVO ACM DET
           select count(acmdet.id_acm)
           into v_contador_boletos
           from obingresos.tacm_det acmdet
           where acmdet.id_acm = v_id_acm_sus;

           UPDATE obingresos.tarchivo_acm_det SET
		   cant_bol_mt = v_contador_boletos
		   Where id_archivo_acm_det=v_registros.id_archivo_acm_det;
          ------------------------------------------------------------------------------------------
          -- raise exception 'Los datos son: %', v_contador_boletos;
          ELSE
          raise exception 'El porcentaje es: %  y no se encuentra en el rango de porcentajes (2 y 4) ',v_porcentaje ;
          end if;

         end loop;
	--############################################################################--



    --##########OBTENEMOS TODOS LOS BOLETOS EN BOLIVIANOS E INSERTAMOS EN TACM Y TEACMDET#########--
        for v_boletos_bs in (select
                          bole.id_detalle_boletos_web,
                          bole.billete,
                          bole.id_agencia,
                          bole.neto,
                          boletos.comision ,
                          boletos.ruta,
                          boletos.moneda,
                          boletos.tipdoc
                          --acmd.id_agencia
                          from obingresos.tdetalle_boletos_web bole
                          inner join obingresos.tmovimiento_entidad mov on mov.autorizacion__nro_deposito = bole.numero_autorizacion
                          inner join obingresos.tboleto boletos on boletos.id_agencia = mov.id_agencia
                          where mov.id_agencia = v_registros.id_agencia
                          and mov.fecha between v_registros.fecha_ini and v_registros.fecha_fin
                          and mov.id_moneda=1
                          and bole.billete = boletos.nro_boleto)  LOOP




           Select acm.id_acm
           into v_id_acm_bs
           from obingresos.tacm acm
           where acm.id_archivo_acm_det=v_registros.id_archivo_acm_det
           and acm.id_moneda=1;

           select
                          archivo.porcentaje
                          into v_porcentaje
                          from obingresos.tarchivo_acm_det archivo
                         where archivo.id_archivo_acm_det= v_registros.id_archivo_acm_det;

   			/*raise exception 'EL ultimo numero es: %', v_porcentaje;*/

           if  v_id_acm_bs is null then

           	  --OBTENEMOS EL CORRELATIVO DE NUMERO
              SELECT *
              into v_correlativo
              FROM param.f_obtener_correlativo(
                        'ACM', --codigo documento
                         NULL,-- par_id,
                        NULL, --id_uo
                        1, --depto
                        1, --usuario
                        'OBINGRESOS', --codigo depto
                        NULL,--formato
                        1); --id_empresa

              --INSERTAMOS DATOS EN ACM
               --Sentencia de la insercion
              if v_registros.tipo_agencia = 'noiata' THEN
              insert into obingresos.tacm(
              id_moneda,
              id_archivo_acm_det,
              fecha,
              numero,
              ruta,
              estado_reg,
              id_usuario_reg,
              fecha_reg
              ) values(
              1,
              v_registros.id_archivo_acm_det,
              now(),
              v_correlativo,--generar correlativo
              v_boletos_bs.ruta,--recuperar de tablas
              --v_suma_importe.comision_total,
              'activo',
              p_id_usuario,
              now()
              )RETURNING id_acm into v_id_acm_bs;



           /*UPDATE obingresos.tacm SET
		   numero = v_correlativo
		   Where id_acm = v_id_acm_bs;*/

          Select corre.num_actual
          into
          v_ultimo_numero
          from param.tcorrelativo corre
          inner join param.tdocumento d on d.id_documento = corre.id_documento
          inner join segu.tsubsistema s on s.id_subsistema = d.id_subsistema
          and s.codigo = 'OBINGRESOS'
          WHERE d.estado_reg='activo' and d.codigo = 'ACM';

          UPDATE obingresos.tarchivo_acm ac SET
		  ultimo_numero = (v_ultimo_numero)
		  Where ac.id_archivo_acm = v_parametros.id_archivo_acm::INTEGER;
          ELSE
          raise exception 'La Agencia: % es del tipo: % el ACM se genera solo con Agencias del Tipo NOIATA',v_registros.nombre, v_registros.tipo_agencia;
          end if;




         -- raise exception 'EL ultimo numero es: %', v_ultimo_numero;
          end if;

              if v_porcentaje BETWEEN 2 and 4 then
              insert into obingresos.tacm_det(
              id_acm,
              id_detalle_boletos_web,
              neto,
              over_comision,
              fecha_reg,
              id_usuario_reg,
              com_bsp,
              moneda,
              td,
              porcentaje_over
              )
              VALUES(
              v_id_acm_bs,
              v_boletos_bs.id_detalle_boletos_web,
              v_boletos_bs.neto,
              (v_boletos_bs.neto * v_registros.porcentaje)/100,
              now(),
              p_id_usuario,
              v_boletos_bs.comision*(-1),
              v_boletos_bs.moneda,
              v_boletos_bs.tipdoc,
              v_porcentaje
              );

          select round (sum(acmdet.over_comision),2)
           						into v_suma_importe
								from obingresos.tacm_det acmdet
                        		where id_acm = v_id_acm_bs;

           UPDATE obingresos.tacm SET
		   importe = v_suma_importe
		   Where id_acm = v_id_acm_bs;

           UPDATE obingresos.tarchivo_acm_det SET
		   importe_total_mb = v_suma_importe
		   Where id_archivo_acm_det=v_registros.id_archivo_acm_det;

           select round (sum(acmdet.neto),2)
           						into v_suma_neto
								from obingresos.tacm_det acmdet
                        		where id_acm = v_id_acm_bs;

           UPDATE obingresos.tarchivo_acm_det SET
		   neto_total_mb = v_suma_neto
		   Where id_archivo_acm_det=v_registros.id_archivo_acm_det;

           select count(acmdet.id_acm)
           into v_contador_boletos
           from obingresos.tacm_det acmdet
           where acmdet.id_acm = v_id_acm_bs;

           UPDATE obingresos.tarchivo_acm_det SET
		   cant_bol_mb = v_contador_boletos
		   Where id_archivo_acm_det=v_registros.id_archivo_acm_det;

          -- raise exception 'Los datos son: %', v_contador_boletos;


          select round (sum(acmdet.com_bsp),2)
           						into v_suma_bsp
								from obingresos.tacm_det acmdet
                        		where id_acm = v_id_acm_bs;

           UPDATE obingresos.tacm SET
		   total_bsp = v_suma_bsp
		   Where id_acm = v_id_acm_bs;
           ELSE
           raise exception 'El porcentaje es: %  y no se encuentra en el rango de porcentajes (2 y 4) ',v_porcentaje ;
           end if;


         end loop;

         --################################################################################--

end loop;






			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','ACM almacenado(a) con exito (id_acm'||v_id_acm||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_acm',v_id_acm::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_acm_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		05-09-2018 20:34:32
	***********************************/

	elsif(p_transaccion='OBING_acm_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tacm set
			id_moneda = v_parametros.id_moneda,
			id_archivo_acm_det = v_parametros.id_archivo_acm_det,
			fecha = v_parametros.fecha,
			numero = v_parametros.numero,
			ruta = v_parametros.ruta,
			importe = v_parametros.importe,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_acm=v_parametros.id_acm;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','ACM modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_acm',v_parametros.id_acm::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_acm_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		05-09-2018 20:34:32
	***********************************/

	elsif(p_transaccion='OBING_acm_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tacm
            where id_acm=v_parametros.id_acm;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','ACM eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_acm',v_parametros.id_acm::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;


/*********************************
 	#TRANSACCION:  'OBING_ACM_LIMPIO_ELI'
 	#DESCRIPCION:	Eliminacion de Archivos ACM
 	#AUTOR:		RZABALA
 	#FECHA:		05-09-2018 20:36:49
	***********************************/

	elsif(p_transaccion='OBING_ACM_LIMPIO_ELI')then

		begin
          --v_arreglo = string_to_array(v_parametros.id_archivo_acm,',');
                      v_length = array_length(v_arreglo,1);

        	update obingresos.tarchivo_acm_det ac set
            importe_total_mb = null
            where ac.id_archivo_acm = v_parametros.id_archivo_acm;

            update obingresos.tarchivo_acm_det ac set
            neto_total_mb = null
            where ac.id_archivo_acm = v_parametros.id_archivo_acm;

            update obingresos.tarchivo_acm_det ac set
            cant_bol_mb = null
            where ac.id_archivo_acm = v_parametros.id_archivo_acm;

            update obingresos.tarchivo_acm ac set
            estado = 'cargado'
            where ac.id_archivo_acm = v_parametros.id_archivo_acm::integer;

            ---RESETEAR CORRELATIVO
             Select
            arch.ultimo_numero
            into v_resetear
            from obingresos.tarchivo_acm arch
            where arch.id_archivo_acm = (v_parametros.id_archivo_acm-1);

            if v_resetear is null then
            v_resetear = 0;

            UPDATE param.tcorrelativo corre
 			SET 		num_actual = v_resetear,
            			num_siguiente=(v_resetear+1)

          	WHERE id_documento=41;

            end if;

            UPDATE param.tcorrelativo corre
 			SET 		num_actual = v_resetear,
            			num_siguiente=(v_resetear+1)
            WHERE id_documento=41;
            -----------------------------------------





        --raise exception 'El valor de id_archivo_acm es: %', v_parametros.id_archivo_acm::integer;


FOR 	v_registros in (select 	arch.id_agencia, ar.fecha_ini, ar.fecha_fin, arch.porcentaje, arch.id_archivo_acm_det
                        from obingresos.tarchivo_acm_det arch
                        inner join obingresos.tarchivo_acm ar on ar.id_archivo_acm = arch.id_archivo_acm
                        where ar.id_archivo_acm = v_parametros.id_archivo_acm::integer) LOOP

		  for v_boletos_sus in ( select
                          bole.id_detalle_boletos_web,
                          bole.billete,
                          bole.id_agencia,
                          bole.neto
                          --acmd.id_agencia
                          from obingresos.tdetalle_boletos_web bole
                          inner join obingresos.tmovimiento_entidad mov on mov.autorizacion__nro_deposito = bole.numero_autorizacion
                          where mov.id_agencia = v_registros.id_agencia
                          and mov.fecha between v_registros.fecha_ini and v_registros.fecha_fin
                          and mov.id_moneda=2)  LOOP

           Select acm.id_acm
           into v_boletos_sus
           from obingresos.tacm acm
           where acm.id_archivo_acm_det=v_registros.id_archivo_acm_det
           and acm.id_moneda=2;

           if  v_boletos_sus is not null then
           --Sentencia de la eliminacion
              delete from obingresos.tacm_det
              where id_acm=v_boletos_sus;




           end if;

              --Sentencia de la eliminacion



			delete from obingresos.tacm
            where id_archivo_acm_det=v_registros.id_archivo_acm_det;






	end loop;


         for v_boletos_bs in ( select
                          bole.id_detalle_boletos_web,
                          bole.billete,
                          bole.id_agencia,
                          bole.neto
                          --acmd.id_agencia
                          from obingresos.tdetalle_boletos_web bole
                          inner join obingresos.tmovimiento_entidad mov on mov.autorizacion__nro_deposito = bole.numero_autorizacion
                          where mov.id_agencia = v_registros.id_agencia
                          and mov.fecha between v_registros.fecha_ini and v_registros.fecha_fin
                          and mov.id_moneda=1)  LOOP

           Select acm.id_acm
           into v_id_acm_bs
           from obingresos.tacm acm
           where acm.id_archivo_acm_det=v_registros.id_archivo_acm_det
           and acm.id_moneda=1;

           if  v_id_acm_bs is not null then
           --Sentencia de la eliminacion
              delete from obingresos.tacm_det
              where id_acm=v_id_acm_bs;




           end if;

              --Sentencia de la eliminacion



			delete from obingresos.tacm
            where id_archivo_acm_det=v_registros.id_archivo_acm_det;



	end loop;
     end loop;


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Archivo ACM Detalle eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_archivo_acm',v_parametros.id_archivo_acm::varchar);

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