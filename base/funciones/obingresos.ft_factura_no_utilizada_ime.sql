CREATE OR REPLACE FUNCTION obingresos.ft_factura_no_utilizada_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_factura_no_utilizada_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.ft_factura_no_utilizada_ime'
 AUTOR: 		 Maylee Perez Pastor
 FECHA:	        08-05-2020 20:37:45
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
	v_id_factura_no_utilizada		integer;

    --30-06-2020 (may)
    v_id_venta				integer;
    v_registros_venta		record;
    v_nro_factura			integer;
    v_id_gestion			integer;
    v_codigo_proceso		varchar;
    v_id_funcionario_inicio	integer;
    v_num_tramite			varchar;
    v_id_proceso_wf			integer;
    v_id_estado_wf			integer;
    v_codigo_estado			varchar;
    v_id_periodo			integer;
    v_codigo_tabla			varchar;
    v_num_ven				varchar;

    v_inicial				integer;
    va_id_tipo_estado 		integer[];
    va_codigo_estado 		varchar[];
    v_id_tipo_estado		integer;
    v_id_estado_actual      integer;
    v_nro_final				integer;

    v_cadena_cnx			varchar;
    v_conexion				varchar;
    v_id_factura			integer;
    v_cajero				varchar;
    v_tipo_pv				varchar;
    v_consulta				varchar;
    v_res_cone				varchar;
    v_tipo_generacion		varchar;
    v_inicial_dosificacion	integer;

BEGIN

    v_nombre_funcion = 'obingresos.ft_factura_no_utilizada_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_FACMAN_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		Maylee Perez Pastor
 	#FECHA:		08-05-2020 20:37:45
	***********************************/

	if(p_transaccion='OBING_FACMAN_INS')then

        begin
        	--Sentencia de la insercion
        	insert into obingresos.tfactura_no_utilizada(
            --id_lugar_pais,
            --id_lugar_depto,

            id_punto_venta,
            id_sucursal,
            id_dosificacion,
            nro_inicial,
            nro_final,
            id_estado_factura,
            fecha,
            id_moneda,
            tipo_cambio,
            nombre,
            nit,
            observaciones,
           --id_concepto_ingas,

			usuario_ai,
			fecha_reg,
			id_usuario_reg,
			id_usuario_ai,
			fecha_mod,
			id_usuario_mod

          	) values(
            /*v_parametros.id_lugar_pais,
			v_parametros.id_lugar_depto,*/
            v_parametros.id_punto_venta,
            v_parametros.id_sucursal,
            v_parametros.id_dosificacion,
            v_parametros.inicial,
            v_parametros.final,
            5, --v_parametros.id_estado_factura, 1=FACTURA VÀLIDA, 9=FACTURA ANULADA, 5=FACTURA NO UTILIZADA
            v_parametros.fecha,
            v_parametros.id_moneda,
            v_parametros.tipo_cambio,
            v_parametros.nombre,
            v_parametros.nit,
            v_parametros.observaciones,
            --v_parametros.id_concepto_ingas,

			v_parametros._nombre_usuario_ai,
			now(),
			p_id_usuario,
			v_parametros._id_usuario_ai,
			null,
			null


			)RETURNING id_factura_no_utilizada into v_id_factura_no_utilizada;



            --INSERTA VENTAS

            -- obtener periodo venta

              select id_periodo
              into v_id_periodo
              from param.tperiodo per
              where per.fecha_ini <= now()::date
              and per.fecha_fin >=  now()::date
              limit 1 offset 0;
            --

            --wf

			  --obtener gestion a partir de la fecha actual
              select id_gestion
              into v_id_gestion
              from param.tgestion
              where gestion = extract(year from now())::integer;

              select nextval('vef.tventa_id_venta_seq') into v_id_venta;

              v_codigo_proceso = 'VEN-' || v_id_venta;

              -- inicia el tramite en el sistema de WF
              select f.id_funcionario
              into  v_id_funcionario_inicio
              from segu.tusuario u
              inner join orga.tfuncionario f on f.id_persona = u.id_persona
              where u.id_usuario = p_id_usuario;

              select *
              into v_registros_venta
              from vef.tdosificacion dos
              where dos.id_dosificacion = v_parametros.id_dosificacion;

            --

            --validaciones

            --validar que no exista el mismo nro para la dosificacion
            if (exists(	select 1
                         from vef.tventa ven
                         where ven.nro_factura = v_parametros.inicial::integer and ven.id_dosificacion = v_parametros.id_dosificacion)) then
              raise exception 'Ya existe el mismo Número de Factura en otra venta y con la misma dosificación. Por favor revise los datos.';
            end if;

            --validar que factura inicial no este vacio
            IF(v_parametros.inicial is null) THEN
            	raise exception 'El el campo Nro de Factura Inicial falta completar para esta dosificación';
            END IF;

            --validar que el nro de factura no supere el maximo nro de factura de la dosificacion
            if (exists(	select 1
                         from vef.tdosificacion dos
                         where v_parametros.inicial::integer > v_parametros.final and dos.id_dosificacion = v_parametros.id_dosificacion)) then
              raise exception 'El Número de Factura supera el máximo permitido para esta dosificación';
            end if;


            --insertar a la cabecera ventas el rango de nro de faturas descritas en la dosificacion
            v_nro_factura := v_parametros.inicial;


            WHILE (v_nro_factura <= v_parametros.final)
              LOOP

                  --WF
                  SELECT
                    ps_num_tramite,
                    ps_id_proceso_wf ,
                    ps_id_estado_wf ,
                    ps_codigo_estado
                  INTO
                    v_num_tramite,
                    v_id_proceso_wf,
                    v_id_estado_wf,
                    v_codigo_estado

                  FROM wf.f_inicia_tramite(
                      p_id_usuario,
                      NULL,
                      v_parametros._nombre_usuario_ai,
                      v_id_gestion,
                      'VEN',
                      v_id_funcionario_inicio,
                      NULL,
                      'Ventas',
                      v_codigo_proceso,
                      v_registros_venta.nro_tramite
                      );
                      --raise exception 'llega %',v_codigo_estado;

                      SELECT es.id_tipo_estado
                      INTO v_id_tipo_estado
                      FROM wf.testado_wf es
                      WHERE es.id_estado_wf = v_id_estado_wf;


                  --

                  --obtener correlativo
                  if (pxp.f_existe_parametro(p_tabla,'id_punto_venta')) then
                    select pv.codigo
                    into v_codigo_tabla
                    from vef.tpunto_venta pv
                    where id_punto_venta = v_parametros.id_punto_venta;
                  else
                    select pv.codigo
                    into v_codigo_tabla
                    from vef.tsucursal pv
                    where id_sucursal = v_parametros.id_sucursal;
                  end if;

                  v_num_ven =   param.f_obtener_correlativo(
                      'VEN',
                      v_id_periodo,-- par_id,
                      NULL, --id_uo
                      NULL,    -- id_depto
                      p_id_usuario,
                      'VEF',
                      NULL,
                      0,
                      0,
                      'vef.tsucursal',
                      v_parametros.id_sucursal,
                      v_codigo_tabla
                  );
                  --

                  --validar que no exista el mismo nro para la dosificacion
                  if (exists(	select 1
                               from vef.tventa ven
                               where ven.nro_factura = v_nro_factura::integer and ven.id_dosificacion = v_parametros.id_dosificacion)) then
                    raise exception 'Ya existe el mismo Número de Factura en otra venta y con la misma dosificación. Por favor revise los datos.';
                  end if;
                  --

                      --inserta las ventas
                      insert into vef.tventa(
                        id_sucursal,
                        id_cliente,
                        id_proceso_wf,
                        id_estado_wf,
                        estado_reg,
                        nro_tramite,
                        a_cuenta,
                        fecha_estimada_entrega,
                        usuario_ai,
                        fecha_reg,
                        id_usuario_reg,
                        id_usuario_ai,
                        id_usuario_mod,
                        fecha_mod,
                        estado,
                        id_punto_venta,
                        observaciones,
                        correlativo_venta,
                        tipo_factura,
                        fecha,

                        nro_factura,

                        id_dosificacion,
                        excento,
                        id_moneda,

                        transporte_fob,
                        seguros_fob,
                        otros_fob,
                        transporte_cif,
                        seguros_cif,
                        otros_cif,
                        tipo_cambio_venta,
                        valor_bruto,
                        descripcion_bulto,

                        nit,
                        nombre_factura,
                        id_cliente_destino,
                        tiene_formula,
                        forma_pedido,
                        anulado,
                        contabilizable


                      ) values(
                        v_parametros.id_sucursal,
                        103,--nombre de factura SIN NOMBRE--12, ---id_cliente
                        v_id_proceso_wf,
                        v_id_estado_wf,
                        'activo',
                        v_registros_venta.nro_tramite,
                        0,
                        v_parametros.fecha,
                        v_parametros._nombre_usuario_ai,
                        now(),
                        p_id_usuario,
                        v_parametros._id_usuario_ai,
                        null,
                        null,
                        v_codigo_estado,
                        v_parametros.id_punto_venta,
                        upper(v_parametros.observaciones),
                        v_num_ven,
                        v_registros_venta.tipo_generacion,
                        v_parametros.fecha,

                        v_nro_factura, --v_nro_factura,

                        v_parametros.id_dosificacion,
                        0,	--excento
                        v_parametros.id_moneda, --1

                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,

                        v_parametros.nit,
                        v_parametros.nombre,
                        null, --v_id_cliente_destino,
                        'no',
                        'cajero',
                        'NOUTI',
                        'no' --v_anulado??


                      ) returning id_venta into v_id_venta;


                       --   para un estado siguiente
                       SELECT  ps_id_tipo_estado,
                               ps_codigo_estado

                           into
                              va_id_tipo_estado,
                              va_codigo_estado

                          FROM wf.f_obtener_estado_wf(
                           v_id_proceso_wf,
                           NULL,
                           v_id_tipo_estado,
                           'siguiente',
                           p_id_usuario);


                       v_id_estado_actual =  wf.f_registra_estado_wf(  va_id_tipo_estado[1],
                                                                       null,
                                                                       v_id_estado_wf,
                                                                       v_id_proceso_wf,
                                                                       p_id_usuario,
                                                                       null,
                                                                       null,
                                                                       null,
                                                                       '');

                          update vef.tventa   set
                             id_estado_wf =  v_id_estado_actual,
                             estado = va_codigo_estado[1]
                          where id_proceso_wf = v_id_proceso_wf;

                     --nro factura
                     v_nro_factura := v_nro_factura + 1;


					  --para registro en tipo factura
                      IF(pxp.f_get_variable_global('migrar_facturas') ='true')THEN
                      /*Establecemos la conexion con la base de datos*/
                      v_cadena_cnx = vef.f_obtener_cadena_conexion_facturacion();
                      v_conexion = (SELECT dblink_connect(v_cadena_cnx));
                      /*************************************************/


                      select * FROM dblink(v_cadena_cnx,'select nextval(''sfe.tfactura_id_factura_seq'')',TRUE)AS t1(resp integer)
                      into v_id_factura;



                      /*Recuperamos el nombre del cajero que esta finalizando la factura*/
                      SELECT per.nombre_completo2 into v_cajero
                      from segu.tusuario usu
                      inner join segu.vpersona2 per on per.id_persona = usu.id_persona
                      where usu.id_usuario = p_id_usuario;
                      /******************************************************************/



                      v_tipo_pv= 'FAC.BOL.NO UTILIZADAS.CONTABLE ';

                      SELECT dos.tipo_generacion
                      INTO v_tipo_generacion
                      FROM vef.tdosificacion dos
                      WHERE dos.id_dosificacion = v_parametros.id_dosificacion;

                      v_consulta = '
                      INSERT INTO sfe.tfactura(
                      id_factura,
                      fecha_factura,
                      nro_factura,
                      nro_autorizacion,
                      estado,
                      nit_ci_cli,
                      razon_social_cli,
                      importe_total_venta,
                      codigo_control,
                      usuario_reg,
                      tipo_factura,
                      id_origen,
                      sistema_origen,
                      desc_ruta
                      )
                      values(
                      '||v_id_factura||',
                      '''||v_parametros.fecha||''',
                      '''||v_nro_factura::varchar||''',
                      '' '',
                      ''NO UTILIZADA'',
                      ''0'',
                      ''NO UTILIZADA'',
                      '||0::numeric||',
                      '' '',
                      '''||v_cajero||''',
                      '''||v_tipo_generacion||''',
                      '||v_id_venta||',
                      ''ERP'',
                      '''||v_tipo_pv::varchar||'''
                      );';




                        IF(v_conexion!='OK') THEN
                        raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
                        ELSE



                        perform dblink_exec(v_cadena_cnx,v_consulta,TRUE);



                        v_res_cone=(select dblink_disconnect());



                        END IF;
                      end if;


            END LOOP;



			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Factura No Utilizada almacenado(a) con exito (id_factura_no_utilizada'||v_id_factura_no_utilizada||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_factura_no_utilizada',v_id_factura_no_utilizada::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_FACMAN_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		Maylee Perez Pastor
 	#FECHA:		08-05-2020 20:37:45
	***********************************/

	elsif(p_transaccion='OBING_FACMAN_MOD')then

		begin

        --raise exception 'llegaedit %',v_parametros.id_sucursal;

			--Sentencia de la modificacion
			update obingresos.tfactura_no_utilizada set
            /* id_lugar_pais = v_parametros.id_lugar_pais,
			id_lugar_depto = v_parametros.id_lugar_depto,*/
            id_punto_venta = v_parametros.id_punto_venta,
            id_sucursal = v_parametros.id_sucursal,
            id_dosificacion = v_parametros.id_dosificacion,
            nro_inicial = v_parametros.inicial,
            nro_final = v_parametros.final,
            fecha = v_parametros.fecha,
            id_moneda = v_parametros.id_moneda,
            tipo_cambio = v_parametros.tipo_cambio,
            nombre = v_parametros.nombre,
            nit= v_parametros.nit,
            observaciones = v_parametros.observaciones,
            --id_concepto_ingas = v_parametros.id_concepto_ingas,

			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai

			where id_factura_no_utilizada=v_parametros.id_factura_no_utilizada;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje',' Factura No Utilizada modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_factura_no_utilizada',v_parametros.id_factura_no_utilizada::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_FACMAN_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		Maylee Perez Pastor
 	#FECHA:		08-05-2020 20:37:45
	***********************************/

	elsif(p_transaccion='OBING_FACMAN_ELI')then

		begin

            SELECT fnu.nro_inicial, fnu.nro_final
            INTO v_nro_factura, v_nro_final
            FROM obingresos.tfactura_no_utilizada fnu
            WHERE  fnu.id_factura_no_utilizada= v_parametros.id_factura_no_utilizada;

            --ELIMINAR FATURAS QUE ESTAN EN TABLA VENTA
            WHILE (v_nro_factura <= v_nro_final)
              LOOP

                    delete
                    from vef.tventa v
           			where v.nro_factura =v_nro_factura;


                     --nro factura
                     v_nro_factura := v_nro_factura + 1;



            END LOOP;



			--Sentencia de la eliminacion
			delete from obingresos.tfactura_no_utilizada
            where id_factura_no_utilizada=v_parametros.id_factura_no_utilizada;



            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Factura No Utilizada eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_factura_no_utilizada',v_parametros.id_factura_no_utilizada::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_NFACINI_GET'
 	#DESCRIPCION:	Permite recuperar dede la interface el numero de factura inicial de una dosificacion
    #AUTOR:	     Maylee Perez Pastor
 	#FECHA:		01-07-2020 15:30:14
	***********************************/

	elsif(p_transaccion='OBING_NFACINI_GET')then

		begin

           SELECT max(ven.nro_factura)
           INTO v_inicial
           FROM vef.tventa ven
           WHERE ven.id_dosificacion = v_parametros.id_dosificacion;

          -- raise exception 'llega %',v_parametros.id_dosificacion;

           IF (v_inicial is null)THEN
           		SELECT dos.inicial
                 INTO v_inicial_dosificacion
                 FROM vef.tdosificacion dos
                 WHERE dos.id_dosificacion = v_parametros.id_dosificacion;

                v_inicial = v_inicial_dosificacion;

           ELSE

           		v_inicial = COALESCE(v_inicial, 0) + 1 ;

           END IF;



            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp, 'mensaje', 'Nro de factura Inicial obtenido)');
            v_resp = pxp.f_agrega_clave(v_resp, 'inicial' ,v_inicial::varchar);

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
