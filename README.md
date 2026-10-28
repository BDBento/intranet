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

3. Acesse o Drupal em:

   `http://localhost:8088`

   Conclua o instalador do Drupal. Se a porta `8088` estiver ocupada, defina
   outro valor para `DRUPAL_PORT` no arquivo `.env` e use essa porta no acesso.

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

## Autenticação Gov.br

O projeto inclui uma preparação desabilitada para autenticação OpenID Connect
com o Gov.br. As credenciais não ficam no Git e a integração não deve ser
ativada sem cadastro, homologação e uma regra institucional que confirme o
vínculo do usuário com o Governo de Mato Grosso do Sul.

Consulte [docs/autenticacao-govbr.md](docs/autenticacao-govbr.md) antes de
habilitar o módulo **Intranet MS - Autenticação Gov.br**.

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
