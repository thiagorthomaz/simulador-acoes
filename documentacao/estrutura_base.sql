create table Tab_preco (
  data_pregao date,
  cod_ativo char(5),
  abertura decimal(15,2),
  maxima decimal(15,2),
  minima decimal(15,2),
  medio decimal(15,2),
  fechamento decimal(15,2),
  negocios integer,
  volume_financeiro integer,
  data_importacao datetime
);