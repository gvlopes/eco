# Transportes Ecolub Br - Rastreio de Cargas (Exemplo)

Este é um site completo (frontend + backend em PHP) básico para gerar códigos de rastreio e permitir que clientes acompanhem atualizações de rota.

## Conteúdo
- index.html — página pública para o cliente consultar rastreio.
- admin.html — painel administrativo para gerar códigos, iniciar rota e adicionar atualizações.
- api.php — endpoints PHP que gerenciam geração de código, atualizações e consulta.
- config.php — configurações (senha admin, nome do site, timezone). **Altere a senha antes de usar.**
- data.json — armazenamento simples em JSON (persistência simples).

## Requisitos para hospedar
- Servidor com PHP 7.0+ e permissão de escrita no diretório (para `data.json`).
- Coloque todo o conteúdo deste zip na raiz pública do seu host (ou em um subdiretório).

## Segurança / Produção
- Esta é uma solução simples para uso rápido. Para produção, recomenda-se:
  - Usar banco de dados (MySQL/SQLite) em vez de JSON plano.
  - Implementar autenticação segura com sessions e senha hasheada.
  - Validar e sanitizar todas entradas.
  - Usar HTTPS.

## Como usar
1. Extraia os arquivos no servidor com PHP.
2. Edite `config.php` e altere `'admin_password'` para uma senha forte.
3. Acesse `admin.html` e faça login com a senha definida.
4. No painel admin, clique **Gerar Rastreio** para criar um código e envie ao cliente.
5. Clique em **Iniciar Rota** (opcional) e adicione atualizações de localização. O cliente verá essas atualizações ao consultar o código em `index.html`.

