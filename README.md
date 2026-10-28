# Intranet Drupal local

Ambiente local de desenvolvimento com Drupal 11, Apache, PHP e MariaDB.

## Requisitos

- Docker Desktop com Docker Compose v2

## Iniciar

1. Opcionalmente, copie `.env.example` para `.env` e ajuste as credenciais.
2. Na pasta do projeto, execute:

   ```powershell
   docker compose up --build -d
   ```

3. Descubra a porta que o Docker reservou para o Drupal:

   ```powershell
   docker compose port drupal 80
   ```

   Acesse `http://localhost:<porta-exibida>` e conclua o instalador do Drupal. A porta é atribuída automaticamente para evitar conflito com outros serviços locais.

Na etapa do banco de dados, selecione **MySQL, MariaDB, Percona Server ou equivalente** e use:

| Campo | Valor padrão |
|---|---|
| Banco de dados | `drupal` |
| Usuário | `drupal` |
| Senha | `local_dev_password` |
| Host | `db` |
| Porta | `3306` |

Se você definiu valores em `.env`, use `DB_NAME`, `DB_USER` e `DB_PASSWORD` no instalador. O host do banco continua `db`.

## Ativar o tema e os recursos da intranet

Depois de concluir o instalador:

1. Acesse **Aparência** e instale o tema **Intranet MS**.
2. Defina **Intranet MS** como tema padrão.
3. Acesse **Estender** e habilite o módulo **Intranet MS - Recursos**.

O módulo instala o bloco **Intranet MS: acessos rápidos** na região
`Início - acessos rápidos`. Para editar seus itens, acesse **Estrutura > Layout
de blocos**, localize o bloco e selecione **Configurar**.

## Persistência e desenvolvimento

- `db_data`: banco de dados MariaDB.
- `drupal_sites`: configurações e arquivos do site.
- `web/modules/custom`: módulos personalizados, editáveis no host.
- `web/themes/custom`: temas personalizados, editáveis no host.
- O MariaDB não publica nenhuma porta no host; o Drupal o acessa pela rede privada do Compose.

Os volumes do banco e do site sobrevivem à recriação dos containers. Para parar o ambiente sem apagar dados:

```powershell
docker compose down
```

O projeto Compose tem o nome `intranet-drupal-local`, mantendo containers, rede e volumes separados de outros projetos. As senhas padrão são apenas para desenvolvimento local; defina credenciais próprias em `.env` antes de compartilhar o ambiente. Não use esta configuração como ambiente de produção.
