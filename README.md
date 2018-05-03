# Pialab-backend

## Oauth usage

### Create Oauth client

You must include thes grant_types :

- password
- token
- refresh_token

```bash
bin/console \
    fos:oauth-server:create-client \
    --redirect-uri="http://localhost:4200" \
    --grant-type="password" \
    --grant-type="token" \
    --grant-type="refresh_token"
```

### Create a standard user

```bash
bin/console pia:user:create --email=api@pia.io --password=pia
```

### Request a token 

```http
GET http://localhost:8000/oauth/v2/token
    ?client_id=3_3vyy0lw26x6o84kgowc48kc4s4oc0gk0g888c0k4gwsko8g08w
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