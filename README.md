
# 📸 Teste MotorEZ - Instruções para Rodar o Projeto

Bem-vindo ao meu teste técnico para posição de Desenvolvedor!\
Desde já agradeço pela oportunidade e espero que tenha feito um bom teste.\
Vamos ao passo a passo:

## 🛠 Pré-requisitos

- [Docker](https://www.docker.com/get-started)

## 🚀 Passos para Configuração e Execução

### 1. Clonar o Repositório

Clone o repositório para sua máquina local:

```bash
git clone https://github.com/Bubex/teste-motorez.git
cd teste-motorez
```

### 2. Construir e Iniciar os Contêineres

Use o Docker Compose para construir e iniciar os contêineres. Esse comando executará ```docker-compose up --build -d```.

```bash
docker-compose up --build -d
```

### 3. Rodar as migrations e seedar o banco

```bash
docker-compose exec app bash
composer install
php artisan migrate
php artisan db:seed
php artisan queue:work
```

### 4. Acessar a Aplicação

A aplicação estará disponível em `http://localhost:8080`.

E-mail: ```motorez@teste.com```\
Senha: ```1234```

## 📝 Notas Adicionais

- **.env**: Apenas para fins de validação do teste, deixei o arquivo .env incluso no projeto.
- **Job/Broadcast**: Como a performance também estava sendo validada no teste, fiz o serviço de importação através de Jobs que rodam em segundo plano e fornecem feedback ao front através de Broadcast. Apenas para fins de teste, adicionei um ```sleep(5)``` entre um Job e outro para que o feedback pudesse ser visto no front.

Mais uma vez agradeço pela oportunidade e sigo á disposição!
