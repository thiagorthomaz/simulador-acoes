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
  tipo varchar(20),
  data date,
  descricao varchar(100)
);

create table Tab_feriados (
  data date,
  dia_semana varchar(100),
  descricao varchar(100)
);

create table Tab_hist_importacao (
  arquivo varchar(20),
  importado boolean,
  date date
);
