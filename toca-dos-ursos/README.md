# Toca dos Ursos

Sistema web em PHP 8.1+ (POO + PDO + MySQL/MariaDB) para cadastrar ursos.

## Instalação

1. Requisitos: PHP 8.1 ou superior com as extensões `pdo_mysql`, `mbstring` e `fileinfo`; MySQL 5.7+ ou MariaDB 10.3+.
2. Crie o banco, as tabelas, o usuário inicial e 12 ursos:
   ```
   mysql -u root -p < database/schema.sql
   ```
3. Ajuste usuário e senha do banco em `config/config.php`.
4. Suba o servidor apontando para a pasta `public/`:
   ```
   php -S localhost:8000 -t public
   ```
5. Abra http://localhost:8000 e entre com **admin** / **urso123**. Troque essa senha depois do primeiro acesso.

Em Apache ou Nginx, aponte o DocumentRoot para `public/` para que `config/`, `src/` e `database/` não fiquem acessíveis pela web.

## Para trocar a senha do admin

```
php -r 'echo password_hash("NOVA_SENHA", PASSWORD_DEFAULT);'
```
Copie o resultado e rode: `UPDATE usuarios SET senha_hash = '<hash>' WHERE usuario = 'admin';`

## Estrutura

```
config/config.php        dados de conexão e nome do app
database/schema.sql      tabelas usuarios e ursos + dados iniciais
src/                     classes: Database, Auth, Csrf, Flash, Preferencias, Urso, UrsoRepository, CsvImporter
templates/               header.php e footer.php
public/                  páginas (login, ursos, urso_form, urso_excluir, importar, preferencias), CSS e CSV de exemplo
```

## Recursos

- **Login com sessões**: senha com `password_hash`, `session_regenerate_id` e logout por POST.
- **Cookie de preferências** (`ursos_prefs`): tema claro/escuro, tamanho do texto e ursos por página, válido por 1 ano.
- **CRUD de ursos** com busca, paginação, validação e confirmação antes de excluir.
- **Importação CSV**: cabeçalho obrigatório com `nome` e `especie`; aceita `,` ou `;` como separador, UTF-8 ou ISO-8859-1 (Excel); ignora duplicados (mesmo nome e espécie); limite de 2 MB e 1000 linhas. Veja `public/modelo_ursos.csv`.
- **Segurança**: consultas preparadas, token CSRF em todos os formulários POST e saída escapada com `htmlspecialchars`.
