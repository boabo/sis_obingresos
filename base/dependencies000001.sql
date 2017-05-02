/********************************************I-DEP-JRR-OBINGRESOS-0-10/12/2015********************************************/

ALTER TABLE ONLY obingresos.tagencia
    ADD CONSTRAINT fk_agencia__id_moneda_control
    FOREIGN KEY (id_moneda_control) REFERENCES param.tmoneda(id_moneda);

ALTER TABLE ONLY obingresos.tboleto
    ADD CONSTRAINT fk_boleto__id_agencia
    FOREIGN KEY (id_agencia) REFERENCES obingresos.tagencia(id_agencia);
    
ALTER TABLE ONLY obingresos.tboleto
    ADD CONSTRAINT fk_boleto__id_moneda_boleto
    FOREIGN KEY (id_moneda_boleto) REFERENCES param.tmoneda(id_moneda);
    
ALTER TABLE ONLY obingresos.tdeposito
    ADD CONSTRAINT fk_deposito__id_moneda_deposito
    FOREIGN KEY (id_moneda_deposito) REFERENCES param.tmoneda(id_moneda);

ALTER TABLE ONLY obingresos.tdeposito
    ADD CONSTRAINT fk_deposito__id_agencia
    FOREIGN KEY (id_agencia) REFERENCES obingresos.tagencia(id_agencia);
    
ALTER TABLE ONLY obingresos.tdeposito_boleto
    ADD CONSTRAINT fk_deposito_boleto__id_boleto
    FOREIGN KEY (id_boleto) REFERENCES obingresos.tboleto(id_boleto);
    
ALTER TABLE ONLY obingresos.tdeposito_boleto
    ADD CONSTRAINT fk_deposito_boleto__id_deposito
    FOREIGN KEY (id_deposito) REFERENCES obingresos.tdeposito(id_deposito);
    
CREATE TRIGGER tdeposito_tr
  AFTER INSERT 
  ON obingresos.tdeposito FOR EACH ROW 
  EXECUTE PROCEDURE obingresos.f_tr_deposito();

    

/********************************************F-DEP-JRR-OBINGRESOS-0-10/12/2015********************************************/


/********************************************I-DEP-JRR-OBINGRESOS-0-08/04/2016********************************************/

ALTER TABLE ONLY obingresos.tperiodo_venta
    ADD CONSTRAINT fk_tperiodo_venta__id_pais
    FOREIGN KEY (id_pais) REFERENCES param.tlugar(id_lugar);
    
ALTER TABLE ONLY obingresos.tperiodo_venta
    ADD CONSTRAINT fk_tperiodo_venta__id_gestion
    FOREIGN KEY (id_gestion) REFERENCES param.tgestion(id_gestion);
    
ALTER TABLE ONLY obingresos.tagencia
    ADD CONSTRAINT fk_tagencia__id_lugar
    FOREIGN KEY (id_lugar) REFERENCES param.tlugar(id_lugar);

ALTER TABLE ONLY obingresos.tforma_pago
    ADD CONSTRAINT fk_tforma_pago__id_lugar
    FOREIGN KEY (id_lugar) REFERENCES param.tlugar(id_lugar);

CREATE TRIGGER trig_partition_boleto
  BEFORE INSERT 
  ON obingresos.tboleto FOR EACH ROW 
  EXECUTE PROCEDURE obingresos.ftrig_partition_boleto();
  
ALTER TABLE ONLY obingresos.taeropuerto
    ADD CONSTRAINT fk_taeropuerto__id_lugar
    FOREIGN KEY (id_lugar) REFERENCES param.tlugar(id_lugar);

/********************************************F-DEP-JRR-OBINGRESOS-0-08/04/2016********************************************/


/********************************************I-DEP-JRR-OBINGRESOS-0-19/07/2016********************************************/
ALTER TABLE ONLY obingresos.tboleto_vuelo
    ADD CONSTRAINT fk_tboleto_vuelo__id_aeropuerto_origen
    FOREIGN KEY (id_aeropuerto_origen) REFERENCES obingresos.taeropuerto(id_aeropuerto);
    
ALTER TABLE ONLY obingresos.tboleto_vuelo
    ADD CONSTRAINT fk_tboleto_vuelo__id_aeropuerto_destino
    FOREIGN KEY (id_aeropuerto_destino) REFERENCES obingresos.taeropuerto(id_aeropuerto);

/********************************************F-DEP-JRR-OBINGRESOS-0-19/07/2016********************************************/

/********************************************I-DEP-JRR-OBINGRESOS-0-07/11/2016********************************************/

ALTER TABLE vef.tventa_detalle
ADD COLUMN id_boleto INTEGER;

/********************************************F-DEP-JRR-OBINGRESOS-0-07/11/2016********************************************/

/************************************I-DEP-JRR-OBINGRESOS-0-16/06/2016*************************************************/

CREATE TRIGGER tforma_pago_tr
AFTER INSERT OR UPDATE
ON obingresos.tforma_pago FOR EACH ROW
EXECUTE PROCEDURE obingresos.f_tr_forma_pago();

/************************************F-DEP-JRR-OBINGRESOS-0-16/06/2016*************************************************/


/************************************I-DEP-JRR-OBINGRESOS-0-17/03/2017*************************************************/
select pxp.f_insert_testructura_gui ('REGBOL', 'VEF');
select pxp.f_insert_testructura_gui ('BOLCAJ', 'VEF');
select pxp.f_insert_testructura_gui ('VENBOLVEN', 'VEF');
/************************************F-DEP-JRR-OBINGRESOS-0-17/03/2017*************************************************/

/************************************I-DEP-JRR-OBINGRESOS-0-16/03/2017*************************************************/
ALTER TABLE obingresos.tforma_pago ENABLE ALWAYS TRIGGER tforma_pago_tr;
/************************************F-DEP-JRR-OBINGRESOS-0-16/03/2017*************************************************/

/********************************************I-DEP-JRR-OBINGRESOS-0-28/04/2016********************************************/
ALTER TABLE ONLY obingresos.tcomision_agencia
    ADD CONSTRAINT fk_tcomision__id_contrato
    FOREIGN KEY (id_contrato) REFERENCES leg.tcontrato(id_contrato);

ALTER TABLE ONLY obingresos.tcomision_agencia
    ADD CONSTRAINT fk_tcomision__id_agencia
    FOREIGN KEY (id_agencia) REFERENCES obingresos.tagencia(id_agencia);

/********************************************F-DEP-JRR-OBINGRESOS-0-28/04/2016********************************************/

/********************************************I-DEP-FFP-OBINGRESOS-0-02/05/2017********************************************/

ALTER TABLE ONLY obingresos.tskybiz_archivo_detalle
  ADD CONSTRAINT fk_skybiz_archivo_detalle__id_skybiz_archivo
FOREIGN KEY (id_skybiz_archivo) REFERENCES obingresos.tskybiz_archivo(id_skybiz_archivo);

/********************************************F-DEP-FFP-OBINGRESOS-0-02/05/2017********************************************/