# Arquitetura de autenticação Gov.br

## Estado

A integração está **preparada e desabilitada**. O projeto não contém Client ID,
Client Secret, CPF, tokens ou dados pessoais.

O módulo `intranet_govbr` usa o fluxo Authorization Code do OpenID Connect e os
endpoints publicados pelo documento de descoberta do Gov.br:

| Ambiente | Issuer |
|---|---|
| Homologação | `https://sso.staging.acesso.gov.br/` |
| Produção | `https://sso.acesso.gov.br/` |

Os endpoints de autorização, token e UserInfo são derivados desse host e ficam
fixos no código para impedir alteração administrativa acidental.

## Dados e credenciais

- Credenciais entram apenas por variáveis de ambiente.
- `GOVBR_ENABLED` começa como `false`.
- O cliente Drupal começa desabilitado.
- Apenas os escopos mínimos `openid profile email` são solicitados.
- O código não grava Client Secret na configuração exportável.
- O identificador externo `sub` deve ser tratado como dado pessoal restrito.

## Autenticação nao e autorização

Uma conta Gov.br valida a identidade do cidadão. Ela não confirma vínculo ativo
como servidor, lotação ou permissão para sistemas internos. Antes da produção,
o projeto precisa integrar uma fonte institucional de autorização e definir:

1. quem pode criar a primeira conta na intranet;
2. como vínculos desligados serão bloqueados;
3. quais perfis Drupal serão atribuídos;
4. período de retenção de dados e trilha de auditoria;
5. procedimento de contingência e acesso administrativo local.

## Ativação futura

1. Cadastrar a aplicação no processo oficial do Gov.br.
2. Cadastrar exatamente `https://<host>/openid-connect/govbr` como retorno.
3. Definir as credenciais no cofre de segredos do ambiente.
4. Testar primeiro com `GOVBR_ENVIRONMENT=staging`.
5. Implementar e validar a regra institucional de autorização de servidores.
6. Executar testes de estado, replay, sessão, logout, bloqueio e auditoria.
7. Somente então definir `GOVBR_ENABLED=true` e habilitar o cliente no Drupal.

## Limite atual

O suporte a Drupal 11 do módulo `openid_connect` está na versão
`3.0.0-alpha9`. O projeto fixa essa versão e descarta o ID token retornado,
porque essa versão decodifica suas claims sem validar diretamente a assinatura.
A identidade utilizada para provisionamento vem do endpoint UserInfo do Gov.br,
consultado com o access token obtido no endpoint de token confiável.

Essa mitigação não equivale à homologação de segurança. A versão do módulo e o
fluxo completo devem ser reavaliados antes de qualquer uso em produção.
