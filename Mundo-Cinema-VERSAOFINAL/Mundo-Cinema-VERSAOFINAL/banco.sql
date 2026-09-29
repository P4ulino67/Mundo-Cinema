CREATE DATABASE IF NOT EXISTS carteira;
USE carteira;

CREATE TABLE IF NOT EXISTS usuarios (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    nome     VARCHAR(255) NOT NULL,
    cpf      VARCHAR(20)  NOT NULL,
    endereco VARCHAR(255),
    bairro   VARCHAR(100),
    cidade   VARCHAR(100),
    estado   VARCHAR(50),
    cep      VARCHAR(20),
    UNIQUE KEY uk_usuarios_cpf (cpf)
);

CREATE TABLE IF NOT EXISTS login (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    login      VARCHAR(100) NOT NULL,
    senha      VARCHAR(64)  NOT NULL,
    usuario_id INT NOT NULL,
    UNIQUE KEY uk_login_login (login),
    CONSTRAINT fk_login_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS produtos (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    nome      VARCHAR(255) NOT NULL,
    descricao VARCHAR(500),
    preco     DECIMAL(10,2) NOT NULL,
    imagem    VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS carrinho (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    CONSTRAINT fk_carrinho_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    CONSTRAINT fk_carrinho_produto FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

CREATE TABLE IF NOT EXISTS vendas (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    numero    VARCHAR(10) NOT NULL,
    login     VARCHAR(100) NOT NULL,
    cpf       VARCHAR(20) NOT NULL,
    produto   VARCHAR(255) NOT NULL,
    valor     DECIMAL(10,2) NOT NULL,
    data      VARCHAR(20) NOT NULL,
    hora      VARCHAR(10) NOT NULL,
    pagamento VARCHAR(50),
    CONSTRAINT fk_vendas_usuario FOREIGN KEY (cpf) REFERENCES usuarios(cpf)
);

INSERT INTO produtos (nome, descricao, preco, imagem) VALUES
('Rambo 4', 'John Rambo em uma missao brutal para salvar refens em meio a uma guerra extremamente violenta.', 20.00, 'img/acao2.png'),
('Como Eu Era Antes de Voce', 'Uma emocionante historia de amor, superacao e escolhas dificeis.', 15.00, 'img/drama.jpg'),
('Gente Grande', 'Cinco amigos revivem momentos hilarios em uma divertida viagem.', 10.00, 'img/comedia.jpg'),
('Diario de uma Paixao', 'Um romance emocionante que atravessa os anos e marca geracoes.', 18.00, 'img/romance.jpg'),
('Invocacao do Mal', 'Investigadores paranormais enfrentam forcas malignas assustadoras.', 12.00, 'img/terror2.png'),
('Jurassic Park', 'Dinossauros ganham vida em um parque tematico que foge do controle.', 20.00, 'img/ficcao.jpg'),
('Gato de Botas 2', 'Uma aventura divertida, emocionante e cheia de acao para toda familia.', 14.00, 'img/animacao.jpg');
