CREATE OR REPLACE FUNCTION obingresos.ft_mco_s_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_mco_s_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tmco_s'
 AUTOR: 		 (breydi.vasquez)
 FECHA:	        28-04-2020 15:25:04
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				28-04-2020 15:25:04								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tmco_s'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_mco				integer;


    ---
    v_estacion				varchar;
	v_usr_reg				varchar;
    v_moneda				varchar;
    v_id_moneda	    		int4;
    v_tipo_cambio			numeric;
	v_agt_pv				varchar;
	v_pv_nombre				varchar;
	v_suc				    varchar;
	v_est					varchar;
	v_pais    				varchar;

	v_cajero				varchar;
    v_fecha_emision			date;
    v_rec_periodo			record;
    v_tmp_resp				varchar;
    v_depto					record;
BEGIN

    v_nombre_funcion = 'obingresos.ft_mco_s_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_IMCOS_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		breydi.vasquez
 	#FECHA:		28-04-2020 15:25:04
	***********************************/

	if(p_transaccion='OBING_IMCOS_INS')then

              begin

    		select depto.id_depto_destino
            	into v_depto
            from vef.tpunto_venta pv
            inner join vef.tsucursal suc on suc.id_sucursal = pv.id_sucursal
            inner join param.tdepto dep on dep.id_depto = suc.id_depto
            inner join param.tdepto_depto depto on depto.id_depto_origen = dep.id_depto
    		where pv.id_punto_venta =  v_parametros.id_punto_venta ;

             v_rec_periodo = param.f_get_periodo_gestion(v_parametros.fecha_emision);
             v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_depto.id_depto_destino, v_rec_periodo.po_id_periodo);

            if v_parametros.estado != 9 then
                if ((char_length(regexp_replace(v_parametros.motivo, '[^a-zA-Z0-9]+', '','g'))) = 0) then
                        raise 'El campo motivo no debe estar vacio';
                elsif ((char_length(regexp_replace(v_parametros.pax, '[^a-zA-Z0-9]+', '','g'))) = 0)then
                        raise 'El campo pax no debe estar vacio';
                end if;
            end if; 

            if exists (select 1 from obingresos.tmco_s where trim(nro_mco) = trim(v_parametros.nro_mco))then
           	    raise 'El Nro % de Mco ya se encuentra registrado', v_parametros.nro_mco;
            end if;

          if v_parametros.fecha_emision::date <= now()::date then
                  --Sentencia de la insercion
                  insert into obingresos.tmco_s(
                  estado_reg,
                  estado,
                  fecha_emision,
                  id_moneda,
                  motivo,
                  valor_total,
                  id_gestion,
                  id_usuario_reg,
                  fecha_reg,
                  id_usuario_ai,
                  usuario_ai,
                  id_usuario_mod,
                  fecha_mod,
                  id_concepto_ingas,
                  --id_boleto,
                  nro_tkt_mco,
                  id_punto_venta,
                  tipo_cambio,
                  nro_mco,
                  pax,
                  id_funcionario_emisor,
                  pais_doc_orig,
                  estacion_doc_orig,
                  fecha_doc_orig,
                  t_c_doc_orig,
                  moneda_doc_orig,
                  valor_total_doc_orig,
                  valor_conv_doc_orig
                  ) values(
                  'activo',
                  v_parametros.estado,
                  v_parametros.fecha_emision,
                  v_parametros.id_moneda,
                  v_parametros.motivo,
                  v_parametros.valor_total,
                  v_parametros.id_gestion,
                  p_id_usuario,
                  now(),
                  v_parametros._id_usuario_ai,
                  v_parametros._nombre_usuario_ai,
                  null,
                  null,
                  v_parametros.id_concepto_ingas,
                  v_parametros.id_boleto,
                  v_parametros.id_punto_venta,
                  v_parametros.tipo_cambio,
                  v_parametros.nro_mco,
                  upper(v_parametros.pax),
                  v_parametros.id_funcionario_emisor,

                  upper(v_parametros.pais_doc_or),
                  upper(v_parametros.estacion_doc_or),
                  v_parametros.fecha_doc_or,
                  v_parametros.t_c_doc_or,
                  upper(v_parametros.moneda_doc_or),
                  v_parametros.val_total_doc_or,
                  v_parametros.val_conv_doc_or

                  )RETURNING id_mco into v_id_mco;

	    else
            	select desc_persona into v_cajero
                from segu.vusuario
                where id_usuario = p_id_usuario;

            	raise exception 'Estimad@: % no puede realizar un registro con una fecha mayor a la de hoy: %', v_cajero, to_char(CURRENT_DATE,'DD/MM/YYYY');
         end if;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','MCOs almacenado(a) con exito (id_mco'||v_id_mco||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_mco',v_id_mco::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_IMCOS_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		breydi.vasquez
 	#FECHA:		28-04-2020 15:25:04
	***********************************/

	elsif(p_transaccion='OBING_IMCOS_MOD')then

		begin


		 	select fecha_emision into v_fecha_emision
         	from obingresos.tmco_s
         	where id_mco = v_parametros.id_mco;

			if ((char_length(regexp_replace(v_parametros.motivo, '[^a-zA-Z0-9]+', '','g'))) = 0) then
					raise 'El campo motivo no debe estar vacio';
            elsif ((char_length(regexp_replace(v_parametros.pax, '[^a-zA-Z0-9]+', '','g'))) = 0)then
					raise 'El campo pax no debe estar vacio';
			end if;

    		select depto.id_depto_destino
            	into v_depto
            from vef.tpunto_venta pv
            inner join vef.tsucursal suc on suc.id_sucursal = pv.id_sucursal
            inner join param.tdepto dep on dep.id_depto = suc.id_depto
            inner join param.tdepto_depto depto on depto.id_depto_origen = dep.id_depto
    		where pv.id_punto_venta =  v_parametros.id_punto_venta ;

             v_rec_periodo = param.f_get_periodo_gestion(v_parametros.fecha_emision);
             v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_depto.id_depto_destino, v_rec_periodo.po_id_periodo);

        if  ((v_fecha_emision = v_parametros.fecha_emision) and (now()::date = v_fecha_emision)) then

			--Sentencia de la modificacion
			update obingresos.tmco_s set
			estado = v_parametros.estado,
			fecha_emision = v_parametros.fecha_emision,
			id_moneda = v_parametros.id_moneda,
			motivo = v_parametros.motivo,
			valor_total = v_parametros.valor_total,
/*			id_documento_original = v_parametros.id_documento_original,*/
			id_gestion = v_parametros.id_gestion,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            tipo_cambio = v_parametros.tipo_cambio,
            nro_mco  = v_parametros.nro_mco,
            pax  = upper(v_parametros.pax),
            id_funcionario_emisor = v_parametros.id_funcionario_emisor,
            nro_tkt_mco = v_parametros.id_boleto,
            pais_doc_orig = upper(v_parametros.pais_doc_or),
            estacion_doc_orig = upper(v_parametros.estacion_doc_or),
            fecha_doc_orig = v_parametros.fecha_doc_or,
            t_c_doc_orig = v_parametros.t_c_doc_or,
            moneda_doc_orig = upper(v_parametros.moneda_doc_or),
            valor_total_doc_orig = v_parametros.val_total_doc_or,
            valor_conv_doc_orig = v_parametros.val_conv_doc_or
			where id_mco=v_parametros.id_mco;
	     else
            select desc_persona into v_cajero
                from segu.vusuario
                where id_usuario = p_id_usuario;

            	raise exception 'Estimad@: % no puede realizar la modificacion del registro. Favor comuniquece con su supervisor', v_cajero;
         end if;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','MCOs modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_mco',v_parametros.id_mco::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_IMCOS_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		breydi.vasquez
 	#FECHA:		28-04-2020 15:25:04
	***********************************/

	elsif(p_transaccion='OBING_IMCOS_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tmco_s
            where id_mco=v_parametros.id_mco;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','MCOs eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_mco',v_parametros.id_mco::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_GPTFIN_IME'
 	#DESCRIPCION:	Captura de datos iniciales para registro MCOs
 	#AUTOR:		breydi.vasquez
 	#FECHA:		04-05-2020
	***********************************/

	elsif(p_transaccion='OBING_GPTFIN_IME')then

		begin
        	-- seleccion de estacion


            -- usuario
            select cuenta into v_usr_reg
            from segu.vusuario
            where id_usuario = p_id_usuario;

            -- tipo de cambio actual
            select tc.oficial
            into v_tipo_cambio
            from param.ttipo_cambio tc
            inner join param.tmoneda tm on tm.id_moneda = tc.id_moneda
            where tm.codigo_internacional = 'USD' and tc.fecha = current_date;

			-- moneda base
            select
            mon.moneda, mon.id_moneda
            into v_moneda, v_id_moneda
            from param.tmoneda mon
            where tipo_moneda='base';

            select vpv.codigo, vpv.nombre, vsu.codigo, pl.codigo, plf.nombre
            into
                  v_agt_pv, v_pv_nombre, v_suc, v_est, v_pais
            from vef.tpunto_venta vpv
            inner join vef.tsucursal vsu on vsu.id_sucursal = vpv.id_sucursal
            inner join param.tlugar pl on pl.id_lugar = vsu.id_lugar
            inner join param.tlugar plf on plf.id_lugar = pl.id_lugar_fk
            where id_punto_venta = v_parametros.id_punto_venta;


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'RESPUESTA', 'RESP'::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'usr_reg', v_usr_reg::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'tipo_cambio', v_tipo_cambio::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'moneda', v_moneda::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_moneda', v_id_moneda::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_punto_venta', v_parametros.id_punto_venta::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'agt_pv', v_agt_pv::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'pv_nombre', v_pv_nombre::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'cod_suc', v_suc::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'estacion', v_est::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'pais', v_pais::varchar);
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

ALTER FUNCTION obingresos.ft_mco_s_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
