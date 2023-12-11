# Processo seletivo Ultralinks 🚀

### Diagramas de classes

![UML User](_readme/img.png)

### Recursos da API

#### Auth
- **Login**
    - Método: `POST`
    - Rota: `{{baseUrl}}/auth/login`

- **Logout**
    - Método: `POST`
    - Rota: `{{baseUrl}}/auth/logout`

- **Refresh**
    - Método: `POST`
    - Rota: `{{baseUrl}}/auth/refresh`

- **Me**
    - Método: `POST`
    - Rota: `{{baseUrl}}/auth/me`

- **Register**
    - Método: `POST`
    - Rota: `{{baseUrl}}/auth/register`

---

#### Users
- **Get Users**
    - Método: `GET`
    - Rota: `{{baseUrl}}/users/`

- **Get User by Id**
    - Método: `GET`
    - Rota: `{{baseUrl}}/users/:userId`

- **Get User by Document**
    - Método: `GET`
    - Rota: `{{baseUrl}}/users/document/:userDocument`

- **Create Users**
    - Método: `POST`
    - Rota: `{{baseUrl}}/users`

---

#### Deposits
- **Get Deposits**
    - Método: `GET`
    - Rota: `{{baseUrl}}/deposits/`

- **Get Deposit by Id**
    - Método: `GET`
    - Rota: `{{baseUrl}}/deposits/id/:depositId`

- **Get Deposit by Cod**
    - Método: `GET`
    - Rota: `{{baseUrl}}/deposits/cod/:depositCod`

- **Make a Deposit**
    - Método: `POST`
    - Rota: `{{baseUrl}}/deposits`

---

#### Transfers
- **Get Transfers**
    - Método: `GET`
    - Rota: `{{baseUrl}}/transfers/`

- **Get Transfer by Id**
    - Método: `GET`
    - Rota: `{{baseUrl}}/transfers/id/:transferId`

- **Get Transfer by Cod**
    - Método: `GET`
    - Rota: `{{baseUrl}}/transfers/cod/:depositCod`

- **Make a Transfer**
    - Método: `POST`
    - Rota: `{{baseUrl}}/transfers`


Para ver a collection do postman [clique aqui](_readme/UltralinksProcessoSeletivo.postman_collection.json)

