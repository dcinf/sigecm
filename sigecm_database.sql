create database sigecm_database;
use sigecm_database;

select * from utilizadores;
select * from funcionarios_armamento;
#==========================================================================
# 		Area da administracao
#==========================================================================
#==========================================================
# Tabelas relacionadas ao Dept do Pessoal
#==========================================================
CREATE TABLE departamentos (
		codigo_departamento			   INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
        nome_departamento              NVARCHAR(255),
        descricao                      TEXT,
        #codigo_responsavel			   INT UNSIGNED,
		criado_em                      DATETIME,
        atualizado_em                  DATETIME,
        apagado_em					   DATETIME
);

select * from armamento;


CREATE TABLE cargos	(
		codigo_cargo				    INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
		nome_cargo					    NVARCHAR(255),
		descricao_cargo					TEXT,
		requisitos						TEXT,
		percentual_aumento 				DECIMAL(10,2),
		beneficios_inclusos 			NVARCHAR(255),
		criado_em                      	DATETIME,
		atualizado_em                  	DATETIME,
		apagado_em					   	DATETIME
);

INSERT INTO departamentos (nome_departamento, descricao, criado_em, atualizado_em)
		VALUES('Comunicacoes', 'Departamento das comunicacoes e Informatica', Now(), Now());

INSERT INTO cargos (nome_cargo, descricao_cargo, requisitos, percentual_aumento, beneficios_inclusos, criado_em, atualizado_em)
			VALUES ('Director de Departamento', 'Director de um departamento', 'Curso de lideranca e Patente de Coronel', 25.0, 'Viatura', now(), now());



/*CREATE TABLE funcionarios (
	codigo_funcionario 					INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nome_completo						NVARCHAR(255),
    data_nascimento						DATE,
    genero								NVARCHAR(100),
    documento_identidade				NVARCHAR(100),
    patente								NVARCHAR(100),
    endereco							NVARCHAR(255),
    celular								NVARCHAR(50),
    celular_alt							NVARCHAR(50),
    email								NVARCHAR(255),
    data_admissao						DATE,
    data_fim_comissao					DATE,
    codigo_departamento 				INT UNSIGNED,
    nome_departamento					NVARCHAR(255),
    codigo_cargo						INT UNSIGNED,
    cargo								NVARCHAR(255),
    cargo_descricao 					TEXT,
    salario_base						DECIMAL(10,2),
    fotografia							TEXT,
    criado_em                      		DATETIME,
	atualizado_em                  		DATETIME,
	apagado_em					   		DATETIME,
    FOREIGN KEY (codigo_departamento)	REFERENCES departamentos(codigo_departamento),
    FOREIGN KEY (codigo_cargo) 			REFERENCES cargos(codigo_cargo)
);

INSERT INTO funcionarios (
    nome_completo, 
    data_nascimento, 
    genero, 
    documento_identidade, 
    patente, 
    endereco, 
    celular, 
    celular_alt, 
    email, 
    data_admissao, 
    data_fim_comissao, 
    codigo_departamento, 
    nome_departamento, 
    codigo_cargo, 
    cargo, 
    cargo_descricao, 
    salario_base, 
    fotografia,
    criado_em,
    atualizado_em
) VALUES
('João da Silva', '1985-06-10', 'Masculino', '060123456789A', 'Coronel', 'Rua das Maguiguana, 123, Maputo', '(11) 98765-4321', '(11) 91234-5678', 'joao.silva@empresa.com', '2015-03-01', null, 1, 'Comunicacoes', 1, 'Director do Departamento', 'Director de um departamento', 50000.00, 'john.jpg', now(), now()),
('Maria Oliveira', '1990-12-25', 'Feminino', '020987654321B', 'Tenente', 'Avenida Karl Max, 456, Maputo', '(11) 99876-5432', '(11) 94567-8901', 'maria.oliveira@empresa.com', '2017-07-15', null, 1, 'Comunicacoes', 1, 'Director do Departamento', 'Director de um departamento', 30000.00, 'john.jpg', now(), now()),
('Carlos Pereira', '1982-02-14', 'Masculino', '040112233445J', 'Tenente-Coronel', 'Rua dos Limoeiros, 789, Maputo', '(21) 98654-3210', '(21) 93456-7890', 'carlos.pereira@empresa.com', null, '2025-09-01', 1, 'Comunicacoes', 1, 'Director do Departamento', 'Director de um departamento', 40000.00, 'john.jpg', now(), now());

select * from funcionarios;*/



#==========================================================================
# Fim das Tabelas relacionadas ao Dept do Pessoal
#==========================================================================

CREATE TABLE grupos(
        codigo_grupo                   INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
        nome_grupo					   NVARCHAR(200),	
        descricao                      TEXT, 
        permissoes                     NVARCHAR(100),
        criado_em                      DATETIME,
        atualizado_em                  DATETIME,
        apagado_em					   DATETIME
);
                
CREATE TABLE utilizadores (
        codigo_utilizador              INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT, 
		patente						   NVARCHAR(100),
        nome_utilizador                NVARCHAR(255),
        subunidade					   NVARCHAR(100),
        genero 					   	   ENUM('Masculino', 'Feminino'),
        numero_de_celular 			   NVARCHAR(20),
		celular_alternativo 		   NVARCHAR(20),
        codigo_departamento			   INT UNSIGNED,	
        utilizador                     NVARCHAR(100), 
        palavra_passe                  NVARCHAR(255),
        criado_em                      DATETIME,
        atualizado_em                  DATETIME,
        apagado_em					   DATETIME,
        grupos                         INT UNSIGNED,
        FOREIGN KEY(grupos) REFERENCES grupos(codigo_grupo),
        FOREIGN KEY(codigo_departamento) REFERENCES departamentos(codigo_departamento)
);


CREATE TABLE utilizadores_permissoes(
        codigo_utilizador              INT UNSIGNED PRIMARY KEY NOT NULL,
        utilizador                     NVARCHAR(50), 
        palavra_passe                  NVARCHAR(200), 
        nome_utilizador                NVARCHAR(255), 
        grupos                         INT UNSIGNED, 
        permissoes                     NVARCHAR(100),  
        descricao_grupo                NVARCHAR(255), 
        departamento 				   INT UNSIGNED,
        descricao_departamento		   TEXT,
        estado 						   ENUM('Activo', 'Inactivo') DEFAULT 'Inactivo',
        FOREIGN KEY(codigo_utilizador) REFERENCES utilizadores(codigo_utilizador),
        FOREIGN KEY(grupos) REFERENCES grupos(codigo_grupo),
        FOREIGN KEY(departamento) REFERENCES departamentos(codigo_departamento)
);





CREATE TABLE senha_config(
		codigo_senha_reset             INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
        descricao                      NVARCHAR(50), 
        palavra_passe                  NVARCHAR(200),
        criado_em                      DATETIME,
        atualizado_em                  DATETIME,
        apagado_em					   DATETIME
);

#=========================================================================
# TRIGGERS DA AREA ADMINISTRATIVA
#=========================================================================
DELIMITER #
CREATE TRIGGER credenciais
    AFTER INSERT ON utilizadores 
    FOR EACH ROW  
    BEGIN 
        DECLARE permition NVARCHAR(100);
        DECLARE description NVARCHAR(255);
        DECLARE depart_description TEXT;

        -- Buscando as permissões do grupo
        SELECT grupos.permissoes INTO permition 
        FROM grupos
        WHERE grupos.codigo_grupo = NEW.grupos
        LIMIT 1;

        -- Buscando a descrição do grupo
        SELECT grupos.descricao INTO description 
        FROM grupos
        WHERE grupos.codigo_grupo = NEW.grupos
        LIMIT 1;

        -- Buscando a descrição do departamento
        SELECT departamentos.descricao INTO depart_description 
        FROM departamentos
        WHERE departamentos.codigo_departamento = NEW.codigo_departamento
        LIMIT 1;

        -- Inserindo os dados na tabela utilizadores_permissoes
        INSERT INTO utilizadores_permissoes 
            (codigo_utilizador, utilizador, palavra_passe, nome_utilizador, 
            grupos, descricao_grupo, permissoes, departamento, descricao_departamento)
        VALUES 
            (NEW.codigo_utilizador, NEW.utilizador, NEW.palavra_passe, NEW.nome_utilizador, 
            NEW.grupos, description, permition, NEW.codigo_departamento, depart_description);
    END#

DELIMITER ;


#=============================================================
DELIMITER $$
CREATE TRIGGER atualizar_palavra_passe
AFTER UPDATE ON utilizadores
FOR EACH ROW
BEGIN
    -- Verifica se a palavra-passe foi modificada
    IF NEW.palavra_passe <> OLD.palavra_passe THEN
        -- Atualiza a palavra-passe correspondente em utilizadores_permissoes
        UPDATE utilizadores_permissoes
        SET palavra_passe = NEW.palavra_passe
        WHERE codigo_utilizador = NEW.codigo_utilizador;
    END IF;
END$$

DELIMITER ;


#==========================================================================
# Tabelas relacionadas a sistema de visitas
#==========================================================================
CREATE TABLE visitantes (
        codigo_visitante               INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT, 
        nome_completo			       NVARCHAR(255),
        imagem						   TEXT,
        criado_em                      DATETIME,
        atualizado_em                  DATETIME,
        apagado_em					   DATETIME
);

CREATE TABLE movimentacoes (
        codigo_movimentacoes           INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
        codigo_visitante			   INT UNSIGNED,
        codigo_atendente			   INT UNSIGNED,
        nome_atendente				   NVARCHAR(255),
        nome_completo			       NVARCHAR(255),
        visitado					   NVARCHAR(255),
        sector_visitado				   NVARCHAR(255),
        celular						   NVARCHAR(25),
		celular_alternativo			   NVARCHAR(25),
        portador_viatura			   VARCHAR(100),
        portador_arma				   NVARCHAR(255),
        obs_pertinentes				   TEXT,
        data_entrada				   DATETIME,
        data_saida				       DATETIME,
        criado_em                      DATETIME,
        atualizado_em                  DATETIME,
        apagado_em					   DATETIME,
        FOREIGN KEY(codigo_visitante) REFERENCES visitantes(codigo_visitante),
        FOREIGN KEY(codigo_atendente) REFERENCES utilizadores(codigo_utilizador)
);

#==========================================================================
# Fim das Tabelas relacionadas a sistema de visitas
#==========================================================================


#==============================================================================================================================

#==========================================================================
# Tabelas relacionadas a sistema Arsenal
#==========================================================================
CREATE TABLE status_operacional_armamento (
    codigo_status 				INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    status 						NVARCHAR(50) NOT NULL
);
-- Inserir os valores possíveis
INSERT INTO status_operacional_armamento (status) 
		VALUES ('Operacional'), ('Em Manutencao'), ('Fora de Servico');

CREATE TABLE disponibilidade_armamento (
    codigo_disponibilidade 		INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    disponibilidade 			NVARCHAR(50) NOT NULL
);

-- Inserir os valores possíveis
INSERT INTO disponibilidade_armamento (disponibilidade) 
		VALUES ('Disponivel'), ('Indisponivel');
        
        
CREATE TABLE tipos_armamentos(
	codigo_tipo_armamento				INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
	classificacao						NVARCHAR(255),
    tipo_armamento						NVARCHAR(255),
    tipo_uso							NVARCHAR(255),
    potencia							NVARCHAR(255),
    alcance_eficaz						NVARCHAR(255),
    tipo_municao						NVARCHAR(255),
    calibre_municao						NVARCHAR(50),
    pais_origem							NVARCHAR(255),
    finalidade							NVARCHAR(255),
    categoria_perigo					NVARCHAR(255),
    descricao							NVARCHAR(255),
    criado_em                      		DATETIME,
	atualizado_em                  		DATETIME,
	apagado_em					   		DATETIME
);

select * from municoes;


CREATE TABLE armamento (
    codigo_armamento 					INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    codigo_tipo_armamento 				INT UNSIGNED,
    nome_armamento 						NVARCHAR(255),
    numero_serie 						NVARCHAR(255),
    marca 								NVARCHAR(255),
    modelo 								NVARCHAR(255),
    calibre 							NVARCHAR(255),
    peso 								DECIMAL(10,2),
    local_armazenamento 				NVARCHAR(255),
    status_operacional 					INT UNSIGNED,
    disponibilidade 					INT UNSIGNED,
    data_aquisicao 						DATE,
    data_ultima_inspecao 				DATE,
    data_ultimo_uso 					DATE,
    responsavel_atual 					NVARCHAR(255),
    observacoes 						TEXT,
    cadastrado_por 						NVARCHAR(255),
    criado_em 							DATETIME,
    atualizado_em 						DATETIME,
    apagado_em 							DATETIME,
    FOREIGN KEY(codigo_tipo_armamento) REFERENCES tipos_armamentos(codigo_tipo_armamento),
    FOREIGN KEY(status_operacional) REFERENCES status_operacional_armamento(codigo_status),
    FOREIGN KEY(disponibilidade) REFERENCES disponibilidade_armamento(codigo_disponibilidade)
);

CREATE TABLE municoes (
    codigo_municao						INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nome 								NVARCHAR(255) NOT NULL,
    calibre 							NVARCHAR(100) NOT NULL,
    tipo 								NVARCHAR(100),
    peso 								NVARCHAR(255),
    velocidade_inicial 					NVARCHAR(255),
    capacidade_penetracao 				NVARCHAR(100),
    fabricante 							NVARCHAR(100),
    data_fabricacao 					DATE,
    quantidade_estoque 					INT,
    arma_compativel 					INT UNSIGNED,
    observacoes 						TEXT,
    criado_em 							DATETIME,
    atualizado_em 						DATETIME,
    apagado_em 							DATETIME,
    FOREIGN KEY (arma_compativel) REFERENCES tipos_armamentos(codigo_tipo_armamento)
);

CREATE TABLE log_retirada_municoes (
    codigo_log 							INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    codigo_municao 						INT UNSIGNED,
    quantidade 							INT,
    data_retirada 						DATETIME,
    responsavel 						NVARCHAR(255),
    FOREIGN KEY (codigo_municao) REFERENCES municoes(codigo_municao)
);

CREATE TABLE equipamentos (
    codigo_equipamento 					INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    tipo								NVARCHAR(255),
    nome 								NVARCHAR(255) NOT NULL,                  -- Nome do equipamento (ex: Carregador, Cartucheira, Capacete)
    material 							NVARCHAR(255),                           -- Material de fabricação (ex: Plástico, Metal, Kevlar, etc.)
    capacidade 							NVARCHAR(255),                     		 -- Capacidade de carga ou proteção (ex: número de cartuchos ou grau de proteção)
    peso 								NVARCHAR(100),                         	 -- Peso do equipamento (em kg)
    cor 								NVARCHAR(100),                           -- Cor do equipamento (ex: Preto, Camuflado, etc.)
    compatibilidade 					NVARCHAR(255),                			 -- Compatibilidade com armas ou outros equipamentos (ex: compatível com pistolas 9mm)
    finalidade 							NVARCHAR(255),                    		 -- Finalidade do equipamento (ex: Proteção, Armazenamento, Transporte, etc.)
    fabricante 							NVARCHAR(255),                     		 -- Fabricante ou marca do equipamento
    pais_origem 						NVARCHAR(255),                    		 -- País de origem do equipamento
    data_fabricacao 					DATE,                        			 -- Data de fabricação ou fabricação inicial do modelo
    estado 								NVARCHAR(50),                            -- Estado do equipamento (ex: Novo, Usado, Danificado)
    quantidade 							INT,                            		-- Quantidade
	descricao 							TEXT,                             		 -- Descrição detalhada do equipamento
    criado_em 							DATETIME,								 -- Data de criação do registro
    atualizado_em 						DATETIME, 								 -- Data da última atualização
    apagado_em 							DATETIME                         		 -- Data de remoção ou desativação (se aplicável)
);


CREATE TABLE log_retirada_equipamentos (
    codigo_log 							INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    codigo_equipamento 					INT UNSIGNED,
    quantidade 							INT,
    data_retirada 						DATETIME,
    responsavel 						NVARCHAR(255),
    FOREIGN KEY (codigo_equipamento) REFERENCES equipamentos(codigo_equipamento)
);



#==============================
# tabela de arrecadacao
#==============================
CREATE TABLE funcionarios_armamento (
	codigo_funcionario 					INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nome_completo						NVARCHAR(255),
    subunidade							NVARCHAR(100),
    genero								ENUM("Masculino", "Feminino"),
    patente								NVARCHAR(100),
    celular								NVARCHAR(50),
    codigo_departamento 				INT UNSIGNED,
    nome_departamento					NVARCHAR(255),
    fotografia							TEXT,
    criado_em                      		DATETIME,
	atualizado_em                  		DATETIME,
	apagado_em					   		DATETIME,
    FOREIGN KEY (codigo_departamento)	REFERENCES departamentos(codigo_departamento)
);

CREATE TABLE arrecadacao_armamento (
	codigo_arrecadacao				INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    codigo_funcionario				INT UNSIGNED,
    nome_funcionario				NVARCHAR(255) NOT NULL,
    patente_funcionario				NVARCHAR(100),
    subunidade						NVARCHAR(100),
    departamento					NVARCHAR(255),
    celular_funcionario				NVARCHAR(50),
    codigo_armamento				INT UNSIGNED,
    numero_de_serie_arma			NVARCHAR(255),
    tipo_armamento					NVARCHAR(255),
    modelo							NVARCHAR(255),
    status_operacional_arma			INT UNSIGNED,
    calibre_municao_arma			NVARCHAR(255),
    data_ultima_inspecao_arma		DATE,
    data_levantamento				DATE,
    data_devolucao					DATE,
    assinatura_arrecadacao			TEXT,
    assinatura_devolucao			TEXT,
    assinatura_fiel					TEXT,
    criado_em                      	DATETIME,
	atualizado_em                  	DATETIME,
	apagado_em					   	DATETIME,
    FOREIGN KEY (codigo_funcionario) REFERENCES funcionarios_armamento (codigo_funcionario),
	FOREIGN KEY (codigo_armamento) REFERENCES armamento(codigo_armamento),
    FOREIGN KEY	(status_operacional_arma) REFERENCES status_operacional_armamento(codigo_status)
);


#==============================================================
# Equipamentos e municoes da arecadacao
#==============================================================

CREATE TABLE arrecadacao_municoes (
	codigo_municao_arrecadacao		INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
	codigo_arrecadacao     			INT UNSIGNED,
    codigo_municao					INT UNSIGNED,
    nome_municao					NVARCHAR(255),
    quantidade_levantar				INT,
    quantidade_devolver				INT,
    criado_em                      	DATETIME,
	atualizado_em                  	DATETIME,
	apagado_em					   	DATETIME,
    FOREIGN KEY (codigo_arrecadacao) REFERENCES arrecadacao_armamento(codigo_arrecadacao),
    FOREIGN KEY (codigo_municao) REFERENCES municoes(codigo_municao)
);

CREATE TABLE arrecadacao_equipamentos (
	codigo_equipamento_arrecadacao	INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
	codigo_arrecadacao     			INT UNSIGNED,
    codigo_equipamento				INT UNSIGNED,
    nome_equipamentos				NVARCHAR(255),
	quantidade_levantar				INT,
    quantidade_devolver				INT,
    criado_em                      	DATETIME,
	atualizado_em                  	DATETIME,
	apagado_em					   	DATETIME,
    FOREIGN KEY (codigo_arrecadacao) REFERENCES arrecadacao_armamento(codigo_arrecadacao),
    FOREIGN KEY (codigo_equipamento) REFERENCES equipamentos(codigo_equipamento)
);

#consultas necessarias 
select * from funcionarios_armamento;

select * from arrecadacao_armamento;

select * from arrecadacao_equipamentos;

select * from arrecadacao_municoes;

select * from armamento;

#==============================================================
# FIM das Arrecadacoes
#==============================================================





#==============================================================
# Requisicoes
#==============================================================
CREATE TABLE requisicao (
	codigo_requisicao				INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
	armazem     					NVARCHAR(255) NOT NULL,
    nome_requerente					NVARCHAR(255) NOT NULL,
    numero_requisicao				NVARCHAR(100),
    data_requisicao					DATE,
    data_devolucao					DATE,
    assinatura_receptor_requisicao	TEXT,
    assinatura_fornecedor_requisicao TEXT,
    assinatura_receptor_devolucao	TEXT,
    assinatura_devolvedor			TEXT,
    criado_em                      	DATETIME,
	atualizado_em                  	DATETIME,
	apagado_em					   	DATETIME
);


CREATE TABLE arma_requisicao (
	codigo_armamento_requisitado	INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    codigo_requisicao				INT UNSIGNED NOT NULL,
    codigo_armamento				INT UNSIGNED,
    numero_de_serie_arma			NVARCHAR(255),
    tipo_armamento					NVARCHAR(255),
    modelo							NVARCHAR(255),
    status_operacional_arma			INT UNSIGNED,
    calibre							NVARCHAR(255),
    data_ultima_inspecao_arma		DATE,
    designacao						TEXT,
    criado_em                      	DATETIME,
	atualizado_em                  	DATETIME,
	apagado_em					   	DATETIME,
    FOREIGN KEY (codigo_requisicao) REFERENCES requisicao(codigo_requisicao),
	FOREIGN KEY (codigo_armamento) REFERENCES armamento(codigo_armamento),
    FOREIGN KEY	(status_operacional_arma) REFERENCES status_operacional_armamento(codigo_status)
);


CREATE TABLE municao_requisicao (
	codigo_municao_requisitado		INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    codigo_requisicao				INT UNSIGNED NOT NULL,
    codigo_municao					INT UNSIGNED,
    quantidade_municao				INT,
    designacao						TEXT,
    criado_em                      	DATETIME,
	atualizado_em                  	DATETIME,
	apagado_em					   	DATETIME,
    FOREIGN KEY (codigo_requisicao) REFERENCES requisicao(codigo_requisicao),
    FOREIGN KEY (codigo_municao) REFERENCES municoes(codigo_municao)
);


CREATE TABLE activos_requisicao (
	codigo_activo_requisitado		INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    codigo_requisicao				INT UNSIGNED NOT NULL,
    quantidade_activo				VARCHAR(100),
    designacao						TEXT,
    criado_em                      	DATETIME,
	atualizado_em                  	DATETIME,
	apagado_em					   	DATETIME,
    FOREIGN KEY (codigo_requisicao) REFERENCES requisicao(codigo_requisicao)
);

#==============================================================
# Fim das Requisicoes
#==============================================================
CREATE TABLE quantidades_equipamentos(
	codigo_quantidade 				INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    codigo_funcionario				INT UNSIGNED,
    nome_funcionario				NVARCHAR(255) NOT NULL,
    patente_funcionario				NVARCHAR(100),
    departamento					NVARCHAR(255),
    cargo							NVARCHAR(255),
	documento_identidade			NVARCHAR(100),
    celular_funcionario				NVARCHAR(50),
    celular_alt						NVARCHAR(50),
    fotografia						TEXT,
    codigo_equipamento				INT UNSIGNED,
    nome_equipamento				NVARCHAR(255), 
    quantidade						INT,
    data_levantamento				DATE,
    data_devolucao					DATE,
    estado_devolucao				NVARCHAR(255),
    assinatura_levantamento			TEXT,
    assinatura_devolucao			TEXT,
    criado_em                      	DATETIME,
	atualizado_em                  	DATETIME,
	apagado_em					   	DATETIME,
    FOREIGN KEY (codigo_equipamento) REFERENCES equipamentos (codigo_equipamento),
	FOREIGN KEY (codigo_funcionario) REFERENCES funcionarios(codigo_funcionario)
);



#=====================================================================================
# Triggers do arsenal
#=====================================================================================
#---------------------------------------
#Trigers de municoes  
#--------------------------------------
DELIMITER //
CREATE TRIGGER after_arrecadacao_municoes_insert
AFTER INSERT ON arrecadacao_municoes
FOR EACH ROW
BEGIN
    DECLARE estoque_municao INT;

    -- 1. Verifica se a quantidade de munição solicitada está disponível
    SELECT quantidade_estoque INTO estoque_municao
    FROM municoes
    WHERE codigo_municao = NEW.codigo_municao;

    -- 2. Se a quantidade solicitada (quantidade_levantar) for maior que o estoque, gera erro
    IF NEW.quantidade_levantar > estoque_municao THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Quantidade de munição insuficiente no estoque';
    END IF;

    -- 3. Atualiza o estoque de munições
    UPDATE municoes
    SET quantidade_estoque = quantidade_estoque - NEW.quantidade_levantar
    WHERE codigo_municao = NEW.codigo_municao;

    -- 4. Registra o histórico da retirada de munições
    INSERT INTO log_retirada_municoes (codigo_municao, quantidade, data_retirada, responsavel)
    VALUES (
        NEW.codigo_municao, 
        NEW.quantidade_levantar, 
        NOW(), 
        (SELECT nome_funcionario FROM arrecadacao_armamento WHERE codigo_arrecadacao = NEW.codigo_arrecadacao)
    );
END //
DELIMITER ;


DELIMITER //
CREATE TRIGGER after_arrecadacao_municoes_update
AFTER UPDATE ON arrecadacao_municoes
FOR EACH ROW
BEGIN
    -- Apenas executa se a quantidade_devolver foi alterada
    IF NEW.quantidade_devolver IS NOT NULL AND (OLD.quantidade_devolver IS NULL OR NEW.quantidade_devolver != OLD.quantidade_devolver) THEN

        -- Atualiza o estoque de munições (soma a devolução)
        UPDATE municoes
        SET quantidade_estoque = quantidade_estoque + NEW.quantidade_devolver
        WHERE codigo_municao = NEW.codigo_municao;

        -- (Opcional) Registrar a devolução no log
        INSERT INTO log_retirada_municoes (codigo_municao, quantidade, data_retirada, responsavel)
        VALUES (
            NEW.codigo_municao,
            NEW.quantidade_devolver,
            NOW(),
            (SELECT nome_funcionario FROM arrecadacao_armamento WHERE codigo_arrecadacao = NEW.codigo_arrecadacao)
        );

    END IF;
END //
DELIMITER ;
#---------------------------------------
#Trigers de municoes  
#--------------------------------------

#---------------------------------------
#Trigers de equipamentos  
#--------------------------------------
DELIMITER //
CREATE TRIGGER after_arrecadacao_equipamentos_insert
AFTER INSERT ON arrecadacao_equipamentos
FOR EACH ROW
BEGIN
    DECLARE estoque_equipamento INT;

    -- 1. Verifica o estoque atual do equipamento
    SELECT quantidade INTO estoque_equipamento
    FROM equipamentos
    WHERE codigo_equipamento = NEW.codigo_equipamento;

    -- 2. Verifica se há estoque suficiente
    IF NEW.quantidade_levantar > estoque_equipamento THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Quantidade de equipamento insuficiente no estoque';
    END IF;

    -- 3. Atualiza o estoque de equipamentos
    UPDATE equipamentos
    SET quantidade = quantidade - NEW.quantidade_levantar
    WHERE codigo_equipamento = NEW.codigo_equipamento;

    -- 4. (Opcional) Registra em log a retirada do equipamento
    INSERT INTO log_retirada_equipamentos (codigo_equipamento, quantidade, data_retirada, responsavel)
    VALUES (
        NEW.codigo_equipamento,
        NEW.quantidade_levantar,
        NOW(),
        (SELECT nome_funcionario FROM arrecadacao_armamento WHERE codigo_arrecadacao = NEW.codigo_arrecadacao)
    );
END //
DELIMITER ;


DELIMITER //
CREATE TRIGGER after_arrecadacao_equipamentos_update
AFTER UPDATE ON arrecadacao_equipamentos
FOR EACH ROW
BEGIN
    DECLARE devolucao_delta INT;

    -- Calcula a diferença entre novo e antigo valor de devolução
    SET devolucao_delta = IFNULL(NEW.quantidade_devolver, 0) - IFNULL(OLD.quantidade_devolver, 0);

    -- Se houve mudança na quantidade devolvida
    IF devolucao_delta != 0 THEN

        -- Atualiza o estoque de equipamentos
        UPDATE equipamentos
        SET quantidade = quantidade + devolucao_delta
        WHERE codigo_equipamento = NEW.codigo_equipamento;

        -- Registra em log a devolução do equipamento
        INSERT INTO log_retirada_equipamentos (codigo_equipamento, quantidade, data_retirada, responsavel)
        VALUES (
            NEW.codigo_equipamento,
            devolucao_delta,
            NOW(),
            (SELECT nome_funcionario FROM arrecadacao_armamento WHERE codigo_arrecadacao = NEW.codigo_arrecadacao)
        );
    END IF;
END //
DELIMITER ;
#---------------------------------------
#Trigers de equipamentos  
#--------------------------------------

#---------------------------------------
#Trigers de arrecadacao  
#--------------------------------------

#Trigger que actualiza a disponibilidade da arma na tabela armamentos
DELIMITER //

CREATE TRIGGER after_arrecadacao_armamento_insert
AFTER INSERT ON arrecadacao_armamento
FOR EACH ROW
BEGIN
    -- Atualiza o armamento com o responsável atual e define como indisponível
    UPDATE armamento
    SET 
        responsavel_atual = NEW.nome_funcionario,
        disponibilidade = (
            SELECT codigo_disponibilidade
            FROM disponibilidade_armamento
            WHERE disponibilidade = 'Indisponivel'
            LIMIT 1
        )
    WHERE codigo_armamento = NEW.codigo_armamento;
END //

DELIMITER ;


DELIMITER //
CREATE TRIGGER after_arrecadacao_armamento_update
AFTER UPDATE ON arrecadacao_armamento
FOR EACH ROW
BEGIN
    -- Executa somente se a data_devolucao foi preenchida ou alterada
    IF NEW.data_devolucao IS NOT NULL AND (OLD.data_devolucao IS NULL OR NEW.data_devolucao != OLD.data_devolucao) THEN

        -- Atualiza o armamento: define como disponível e remove o responsável
        UPDATE armamento
        SET 
            responsavel_atual = NULL,
            disponibilidade = (
                SELECT codigo_disponibilidade
                FROM disponibilidade_armamento
                WHERE disponibilidade = 'Disponivel'
                LIMIT 1
            )
        WHERE codigo_armamento = NEW.codigo_armamento;

    END IF;
END //
DELIMITER ;

#---------------------------------------
#Trigers de arrecadacao  
#--------------------------------------
#==========================================================================
# Fim das Tabelas relacionadas ao Arsenal
#==========================================================================

#==========================================================================
# Tabelas relacionadas aos Fardamentos
#==========================================================================
CREATE TABLE funcionarios_fardamentos (
	codigo_funcionario 					INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nome_completo						NVARCHAR(255),
    subunidade							NVARCHAR(100),
    genero								ENUM("Masculino", "Feminino"),
    patente								NVARCHAR(100),
    celular								NVARCHAR(50),
    codigo_departamento 				INT UNSIGNED,
    nome_departamento					NVARCHAR(255),
    tamanho_calca						NVARCHAR(50),
    tamanho_camisa						NVARCHAR(50),
    tamanho_bota						NVARCHAR(50),
    fotografia							TEXT,
    criado_em                      		DATETIME,
	atualizado_em                  		DATETIME,
	apagado_em					   		DATETIME,
    FOREIGN KEY (codigo_departamento)	REFERENCES departamentos(codigo_departamento)
);


# Tabela que vai armazenar os fardamentos
CREATE TABLE fardamentos (
	codigo_fardamento 					INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
	tipo_fardamento						NVARCHAR(255),
    material_fabrico					NVARCHAR(255),
	cor_material						NVARCHAR(255),
    finalidade							NVARCHAR(255),
	durabilidade						NVARCHAR(255),
    instrucoes							TEXT,
	tamanho								NVARCHAR(255),
    quantidade							INT,
    fornecedor							NVARCHAR(255),
    adquirido_em                      	DATE,
    criado_em                      		DATETIME,
	atualizado_em                  		DATETIME,
	apagado_em					   		DATETIME
);


select * from fardamentos;



#==========================================================================
# Fim das tabelas relacionadas aos Fardamentos
#==========================================================================


#==================================================================================================================================





#===============================================================================================================================
# Inputs 
#===============================================================================================================================

INSERT INTO grupos (nome_grupo, descricao, permissoes, criado_em, atualizado_em) 
         VALUES ('Administradores', 'Grupo de gestao da Administradores', '1111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111', Now(), Now());

INSERT INTO utilizadores(nome_utilizador, codigo_departamento, utilizador, palavra_passe, criado_em, atualizado_em, grupos) 
		VALUES ('Super Administrador', 1, 'admin', md5('11admin11'), now(), now(), 1);
        
INSERT INTO utilizadores(nome_utilizador, codigo_departamento, utilizador, palavra_passe, criado_em, atualizado_em, grupos) 
		VALUES ('Supervisor Visitantes', 1, 'visitas', md5('11admin11'), now(), now(), 2);
        
INSERT INTO utilizadores(nome_utilizador, codigo_departamento, utilizador, palavra_passe, criado_em, atualizado_em, grupos) 
		VALUES ('Supervisor do Armamento', 1, 'armamento', md5('11admin11'), now(), now(), 3);
        
select * from grupos;
        
#===============================================================================================================================
