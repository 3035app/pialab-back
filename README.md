<img src="https://raw.githubusercontent.com/pia-lab/pialab/master/src/assets/images/pia-lab.png">

# Pialab-backend

## Installation

Installation process (not for production environments) :

```bash
git clone https://github.com/pia-lab/pialab-back
cd pialab-back
```

**Note:**

The informations about the database connection and the SMTP server are stored as environment variables called DATABASE_URL and MAILER_URL respectively. For development, you can find and customize them inside .env.

```bash
composer install

bin/console doctrine:database:create
bin/console doctrine:migrations:migrate --no-interaction

bin/console server:start
```

## User creation

Create your super admin user :

```bash
bin/console pia:user:create your@email.address yourSecretPassword
bin/console pia:user:promote your@email.address --role=ROLE_SUPER_ADMIN
```

Note: You can demote a user for a specific role :

```bash
bin/console pia:user:demote your@email.address --role=ROLE_SUPER_ADMIN
```

## Backend admin UI

With a super admin user (has role `ROLE_SUPER_ADMIN`), you can login into the admin panel here `http://localhost:8000/manageUsers` and into `http://localhost:8000/manageApplications`.

## Oauth usage

### Create Oauth application

The following  grant_types are implicitly included :

- password
- token
- refresh_token

```bash
bin/console \
    pia:application:create \
    --name="Default App"
    --url="http://localhost:4200" \
```

Note: You can create this Oauth application in the backend admin UI.

### Create a standard user

```bash
bin/console pia:user:create api@pia.io pia
```

Note: You can create this user in the backend admin UI.

### Request a token

```http
POST http://localhost:8000/oauth/v2/token

Content-Type: application/x-www-form-urlencoded

client_id=3_3vyy0lw26x6o84kgowc48kc4s4oc0gk0g888c0k4gwsko8g08w
&client_secret=4lfse5e5wc2s408sss4sgw440kc84kc4ocwo80os0owgkskk4w
&grant_type=password
&username=api@pia.io
&password=pia
```

Should response something like

```json
{
    "access_token": "NmJjOGFkNzE1NDY5YTY3NjRkZDVlNTM3MzNkYzFhNWFmNGQxYTlhY2NkNzA1ZWIwNjc0ZDFhYWEwMDJiMzdmMQ",
    "expires_in": 3600,
    "token_type": "bearer",
    "scope": null,
    "refresh_token": "NzhhOWZkMmViYzczYjcyMTBkNjY0OTE5NjcyM2RiZjlhZmIxYzA3MmVmZDVmMGM0ZGMwODU2MWI1MWExZDI5OQ"
}
```

You can now request the api as :

```http
GET http://localhost:8000/pias
    Authorization: Bearer NmJjOGFkNzE1NDY5YTY3NjRkZDVlNTM3MzNkYzFhNWFmNGQxYTlhY2NkNzA1ZWIwNjc0ZDFhYWEwMDJiMzdmMQ
```

### Refresh a token

GET http://localhost:8000/oauth/v2/token
    ?client_id=3_3vyy0lw26x6o84kgowc48kc4s4oc0gk0g888c0k4gwsko8g08w
    &client_secret=4lfse5e5wc2s408sss4sgw440kc84kc4ocwo80os0owgkskk4w
    &refresh_token=NzhhOWZkMmViYzczYjcyMTBkNjY0OTE5NjcyM2RiZjlhZmIxYzA3MmVmZDVmMGM0ZGMwODU2MWI1MWExZDI5OQ
    &grant_type=refresh_token

Should response something like

```json
{
    "access_token": "M2U0NzIwOThiNTVhODNkZDFmNDIxZTg5ZDAzMjQ4OTdjMGUwZjMyMzA1NTVhYWRiYTM4Yzc5MDY4ZGI0NzdiMw",
    "expires_in": 3600,
    "token_type": "bearer",
    "scope": null,
    "refresh_token": "YjRhZjZjODRlZGI3Y2IwYTQxMzQ5MjYxNzc3YTExNDk0YmFkY2RmMDQxODEwYzU2ZmNjNDE1OTg0NGQwY2UwYw"
}
```
