CREATE TABLE anuncios (
    anu_codigo INT AUTO_INCREMENT PRIMARY KEY,
    anu_titulo VARCHAR(255) NOT NULL,
    anu_foto VARCHAR(255) NOT NULL,
    anu_descricao TEXT
);

CREATE TABLE usuarios (
    usu_codigo INT AUTO_INCREMENT PRIMARY KEY,
    usu_nome VARCHAR(255) NOT NULL,
    usu_email VARCHAR(255) NOT NULL UNIQUE,
    usu_senha VARCHAR(255) NOT NULL,
    usu_telefone VARCHAR(15) NOT NULL
);

CREATE TABLE funcionarios (
    fun_codigo INT AUTO_INCREMENT PRIMARY KEY,
    fun_nome VARCHAR(255) NOT NULL,
    fun_email VARCHAR(255) NOT NULL UNIQUE,
    fun_telefone VARCHAR(15) NOT NULL,
    fun_data_contratacao DATE NOT NULL,
    fun_cnh VARCHAR(20) NOT NULL,
    fun_ativo BOOLEAN NOT NULL DEFAULT TRUE,
    fun_senha VARCHAR(255) NOT NULL
);

CREATE TABLE solicitacoes (
    sol_codigo INT AUTO_INCREMENT PRIMARY KEY,
    sol_origem VARCHAR(255) NOT NULL,
    sol_destino VARCHAR(255) NOT NULL,
    sol_valor DECIMAL(10, 2) NOT NULL,
    sol_formapagamento VARCHAR(50) NOT NULL,
    sol_distancia DECIMAL(10, 2) NOT NULL,
    sol_data DATETIME NOT NULL,
    usu_codigo INT NOT NULL,
    sol_largura DECIMAL(10, 2),
    sol_comprimento DECIMAL(10, 2),
    sol_peso DECIMAL(10, 2),
    sol_status VARCHAR(50) NOT NULL,
    sol_servico VARCHAR(50) NOT NULL,
    FOREIGN KEY (usu_codigo) REFERENCES usuarios(usu_codigo)
);

CREATE TABLE viagens (
    via_codigo INT AUTO_INCREMENT PRIMARY KEY,
    fun_codigo INT NOT NULL,
    sol_codigo INT NOT NULL,
    usu_codigo INT NOT NULL,
    via_origem VARCHAR(255) NOT NULL,
    via_destino VARCHAR(255) NOT NULL,
    via_valor DECIMAL(10, 2) NOT NULL,
    via_formapagamento VARCHAR(50) NOT NULL,
    via_data DATETIME NOT NULL,
    via_servico VARCHAR(50) NOT NULL,
    via_status VARCHAR(50) NOT NULL,
    FOREIGN KEY (fun_codigo) REFERENCES funcionarios(fun_codigo),
    FOREIGN KEY (sol_codigo) REFERENCES solicitacoes(sol_codigo),
    FOREIGN KEY (usu_codigo) REFERENCES usuarios(usu_codigo)
);
