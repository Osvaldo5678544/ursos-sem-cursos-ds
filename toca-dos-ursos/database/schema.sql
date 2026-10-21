-- Toca dos Ursos: estrutura e dados iniciais (MySQL 5.7+ / MariaDB 10.3+)
CREATE DATABASE IF NOT EXISTS ursos_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ursos_db;

CREATE TABLE IF NOT EXISTS usuarios (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    usuario    VARCHAR(50)  NOT NULL UNIQUE,
    nome       VARCHAR(100) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    criado_em  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ursos (
    id                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome               VARCHAR(100) NOT NULL,
    especie            VARCHAR(100) NOT NULL,
    habitat            VARCHAR(150) NULL,
    peso_kg            DECIMAL(6,2) NULL,
    idade_anos         TINYINT UNSIGNED NULL,
    cor_pelagem        VARCHAR(60)  NULL,
    dieta              ENUM('onivora','carnivora','herbivora') NOT NULL DEFAULT 'onivora',
    status_conservacao ENUM('LC','NT','VU','EN','CR') NOT NULL DEFAULT 'LC',
    descricao          TEXT NULL,
    criado_em          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nome (nome),
    INDEX idx_especie (especie)
) ENGINE=InnoDB;

-- Login inicial: usuario "admin" / senha "urso123" (troque depois de entrar)
INSERT INTO usuarios (usuario, nome, senha_hash) VALUES
('admin', 'Administrador', '$2y$10$cFzP5sNFuFv3NuLdSU.lbu4ywGLhNVrb2YZhRGNrhN5flVHliDSk2');

INSERT INTO ursos (nome, especie, habitat, peso_kg, idade_anos, cor_pelagem, dieta, status_conservacao, descricao) VALUES
('Bruno',   'Urso-pardo (Ursus arctos)',               'Florestas e montanhas da Europa e da Ásia',  250.00, 12, 'Marrom',            'onivora',   'LC', 'Forrageia bagas e peixes e hiberna no inverno.'),
('Aurora',  'Urso-polar (Ursus maritimus)',            'Gelo marinho do Ártico',                     420.00, 9,  'Branco-amarelado',  'carnivora', 'VU', 'Caça focas nas bordas do gelo e nada longas distâncias.'),
('Otto',    'Urso-negro-americano (Ursus americanus)', 'Florestas da América do Norte',              135.00, 7,  'Preto',             'onivora',   'LC', 'Ótimo escalador de árvores, come frutos, insetos e raízes.'),
('Mimi',    'Panda-gigante (Ailuropoda melanoleuca)',  'Florestas de bambu da China',                110.00, 6,  'Preto e branco',    'herbivora', 'VU', 'Passa até 14 horas por dia comendo bambu.'),
('Frida',   'Urso-de-óculos (Tremarctos ornatus)',     'Florestas nubladas dos Andes',               90.00,  10, 'Preto com manchas claras no rosto', 'herbivora', 'VU', 'Único urso nativo da América do Sul.'),
('Nino',    'Urso-malaio (Helarctos malayanus)',       'Florestas tropicais do Sudeste Asiático',    45.00,  5,  'Preto',             'onivora',   'VU', 'O menor dos ursos, com língua longa para pegar mel.'),
('Teodoro', 'Urso-preguiça (Melursus ursinus)',        'Florestas secas do subcontinente indiano',   120.00, 11, 'Preto desgrenhado', 'onivora',   'VU', 'Aspira cupins com os lábios móveis.'),
('Kira',    'Urso-negro-asiático (Ursus thibetanus)',  'Florestas montanhosas da Ásia',              110.00, 8,  'Preto com V branco no peito', 'onivora', 'VU', 'Também chamado de urso-lunar por causa da marca no peito.'),
('Pipo',    'Urso-kodiak (Ursus arctos middendorffi)', 'Arquipélago de Kodiak, Alasca',              500.00, 14, 'Marrom-escuro',     'onivora',   'LC', 'Uma das maiores subespécies de urso-pardo.'),
('Cacau',   'Urso-grizzly (Ursus arctos horribilis)',  'Montanhas do oeste da América do Norte',     300.00, 13, 'Marrom com pontas claras', 'onivora', 'LC', 'Pescador habilidoso nos rios durante a desova do salmão.'),
('Alba',    'Urso-polar (Ursus maritimus)',            'Baía de Hudson, Canadá',                     380.00, 4,  'Branco',            'carnivora', 'VU', 'Filhote criado pela mãe em uma toca de neve.'),
('Tobias',  'Urso-de-peito-branco (Ursus thibetanus)', 'Florestas do Japão',                         95.00,  6,  'Preto',             'onivora',   'VU', 'Hiberna em cavernas e troncos ocos.');
