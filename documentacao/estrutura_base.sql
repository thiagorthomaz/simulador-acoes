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

create table Tab_hist_provento (
  cod_ativo char(5),
  data date,
  descricao varchar(100)
);


update Tab_preco set abertura=abertura/4, maxima=maxima/4, minima=minima/4, medio=medio/4, fechamento=fechamento/4 where cod_ativo = 'PETR4' and data_pregao <= '2005-08-31';
update Tab_preco set abertura=abertura/2, maxima=maxima/2, minima=minima/2, medio=medio/2, fechamento=fechamento/2 where cod_ativo = 'PETR4' and data_pregao <= '2008-04-25';
