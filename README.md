# PoupaSYS - Sistema de Gestão de Poupanças e Empréstimos

O **PoupaSYS** é uma aplicação web desenvolvida em Laravel para gerenciar grupos de poupança (Xitique), empréstimos e fundos sociais. O sistema oferece um painel administrativo completo para controle de membros, ciclos de poupança, distribuição de juros e liquidação de dívidas.

## 🚀 Funcionalidades Principais

### 👥 Gestão de Membros
- Cadastro e edição de usuários.
- Perfis de acesso: Administrador e Membro.
- Controle de status (Ativo/Inativo).

### 💰 Gestão de Poupanças
- **Ciclos de Poupança**: Criação e gerenciamento de ciclos com datas de início e fim.
- **Depósitos**: Registro de poupanças mensais ou esporádicas.
- **Distribuição**: Funcionalidade para distribuir o valor acumulado ao final do ciclo.
  - **Liquidação Automática**: O sistema desconta automaticamente dívidas de empréstimos do valor a receber.
- **Relatórios**: Extratos detalhados de poupança por membro e por ciclo.

### 💸 Gestão de Empréstimos
- **Solicitação**: Registro de pedidos de empréstimo com taxa de juros e data de vencimento.
- **Aprovação**: Fluxo de aprovação (Pendente -> Aprovado/Rejeitado).
- **Pagamentos**: Registro de pagamentos parciais ou totais.
- **Cálculo de Juros**: Gestão de taxas e cálculo automático.
- **Visualização**: Acompanhamento do saldo devedor ("Valor em Falta") em tempo real.

### 🤝 Fundo Social
- Gestão de contribuições para o fundo social do grupo.
- Controle de penalidades e multas.

### 📊 Painel Administrativo
- Dashboard com estatísticas gerais.
- Interface amigável baseada no **AdminLTE**.
- Gráficos e resumos financeiros.

## 🛠️ Tecnologias Utilizadas

- **PHP 8.x**
- **Laravel 10.x**
- **MySQL**
- **AdminLTE 3** (Interface Administrativa)
- **Bootstrap 4/5**

## ⚙️ Instalação e Configuração

Siga os passos abaixo para rodar o projeto localmente:

1. **Clone o repositório**
   ```bash
   git clone https://github.com/seu-usuario/gepoupa.git
   cd gepoupa
   ```

2. **Instale as dependências**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Configure o ambiente**
   - Copie o arquivo de exemplo `.env`:
     ```bash
     cp .env.example .env
     ```
   - Configure as credenciais do banco de dados no arquivo `.env`.

4. **Gere a chave da aplicação**
   ```bash
   php artisan key:generate
   ```

5. **Execute as migrações e seeders**
   Isso criará as tabelas e populará o banco com dados iniciais (incluindo o admin).
   ```bash
   php artisan migrate --seed
   ```

6. **Inicie o servidor**
   ```bash
   php artisan serve
   ```

## 🔐 Acesso ao Sistema

Após rodar os seeders, você pode acessar o sistema com as seguintes credenciais padrão:

- **URL**: `http://localhost:8000/painel/login`
- **E-mail**: `admin@example.com`
- **Senha**: `password`

> **Nota**: Recomenda-se alterar a senha do administrador após o primeiro acesso.

## 📄 Licença

Este projeto é open-source e está licenciado sob a [MIT license](https://opensource.org/licenses/MIT).
