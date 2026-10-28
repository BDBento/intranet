# Autenticação Gov.br

Este módulo prepara um cliente OpenID Connect para o Gov.br, mas o instala
desabilitado. Nenhuma tentativa de autenticação externa ocorre sem as três
condições abaixo:

1. credenciais presentes somente nas variáveis `GOVBR_CLIENT_ID` e
   `GOVBR_CLIENT_SECRET`;
2. `GOVBR_ENABLED=true`;
3. cliente **Gov.br** habilitado explicitamente no painel do Drupal.

O ambiente padrão é `staging`. Use `GOVBR_ENVIRONMENT=production` somente após
homologação e autorização formal.

A URL de retorno é `https://<host>/openid-connect/govbr`. Ela precisa ser
cadastrada exatamente no Gov.br e exige HTTPS em homologação e produção.

O Gov.br autentica a identidade da pessoa, mas não comprova por si só vínculo
funcional com o Governo de Mato Grosso do Sul. A liberação de acesso à intranet
deve depender de uma fonte institucional adicional, como cadastro de servidores
ou uma lista autorizada, antes da ativação em produção.
