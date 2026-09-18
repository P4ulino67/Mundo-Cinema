# Mundo do Cinema

Site de e-commerce para compra de filmes, desenvolvido em PHP com banco de dados MySQL/MariaDB. Permite que o usuário navegue pelo catálogo, crie uma conta, faça login, monte um carrinho de compras e finalize o pedido.

## Funcionalidades

- **Catálogo de filmes** na página inicial, com opção de comprar
- **Página de gêneros** (Ação, Comédia, Romance, Terror, Ficção Científica, Romance, Fantasia, Animação)
- **Cadastro de usuário em duas etapas**: dados pessoais primeiro, depois login e senha
- **Login** com autenticação e mensagem de erro em caso de senha incorreta
- **Carrinho de compras**: adicionar filme, remover item, ver total
- **Finalização de compra**: escolha da forma de pagamento (Pix, Cartão, Boleto) e registro da venda no banco
- Se o usuário tentar comprar um filme sem estar logado, ele é levado para o login e, assim que loga, o filme é adicionado ao carrinho automaticamente

Script para criar o banco (rodar no phpMyAdmin, aba SQL):

```sql
CREATE DATABASE IF NOT EXISTS carteira;
USE carteira;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255),
  cpf VARCHAR(20),
  endereco VARCHAR(255),
  bairro VARCHAR(100),
  cidade VARCHAR(100),
  estado VARCHAR(50),
  cep VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS login (
  id INT AUTO_INCREMENT PRIMARY KEY,
  login VARCHAR(100),
  senha VARCHAR(64),
  cpf VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS vendas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  numero VARCHAR(10),
  login VARCHAR(100),
  cpf VARCHAR(20),
  produto VARCHAR(255),
  valor DECIMAL(10,2),
  data VARCHAR(20),
  hora VARCHAR(10),
  pagamento VARCHAR(50)
);
```
