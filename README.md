# Research Unit of Robotics Web Page + Store + Conekta

Este proyecto quedó extendido con una zona privada de tienda en PHP/MySQL para XAMPP o hosting PHP tradicional.

## Lo que ya trae

- Login y registro.
- Productos protegidos por sesión.
- API de carrito.
- Header con badge y mini-carrito.
- Compras recientes.
- Ticket imprimible.
- Solicitud de factura.
- Checkout hospedado de Conekta.
- Webhook de Conekta.
- Configuración centralizada por `.env`.

## Base de datos

Importa este archivo en MySQL:

- `database/rur_store.sql`

Credenciales demo sembradas:

- Admin: `[email protected]` / `admin123`
- Cliente: `[email protected]` / `cliente123`

## Variables `.env`

Edita:

- `APP_URL`
- `APP_BASE_PATH`
- `DB_*`
- `CONEKTA_PRIVATE_KEY`
- `CONEKTA_PUBLIC_KEY`

## Configuración mínima de Conekta

1. Entra a tu panel de Conekta.
2. Copia tu **Private Key** y pégala en `CONEKTA_PRIVATE_KEY`.
3. Si vas a usar webhook con verificación, crea tu **webhook key** y pega la `public_key` en `CONEKTA_WEBHOOK_PUBLIC_KEY_PEM`.
4. Configura un webhook hacia:
   - `APP_URL/api/webhooks/conekta.php`
5. Eventos recomendados:
   - `order.paid`
   - `order.pending_payment`
   - `order.declined`
   - `order.expired`
   - `order.canceled`
   - `order.voided`

## Importante sobre facturas

En esta entrega la parte de **ticket** queda funcional y la parte de **facturas** registra la solicitud fiscal dentro del sistema.
No genera CFDI timbrado automático porque eso requiere otra integración fiscal aparte.

## Flujo

1. El usuario inicia sesión.
2. Agrega productos al carrito.
3. Se crea una orden local pendiente.
4. Se crea una orden hospedada en Conekta.
5. El usuario paga en Checkout Conekta.
6. El sistema confirma por retorno o webhook.
7. Se descuenta stock cuando la orden queda pagada.
8. La compra aparece en compras recientes y se puede abrir el ticket.

## Deploy en Railway

Este repositorio ya incluye un `Dockerfile` para Railway.

### Antes de subirlo

1. Importa `database/rur_store.sql` en tu base MySQL.
2. No subas tu `.env` real. Usa `.env.example` como referencia.
3. Configura las variables directamente en Railway.

### Variables para Railway

Puedes usar cualquiera de estas opciones:

- `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`
- o las variables nativas de Railway MySQL: `MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD`

Variables recomendadas:

- `APP_ENV=production`
- `APP_URL=https://TU-DOMINIO.up.railway.app`
- `APP_BASE_PATH=`
- `CONEKTA_PRIVATE_KEY`
- `CONEKTA_PUBLIC_KEY`
- `FACTURAMA_USERNAME`
- `FACTURAMA_PASSWORD`
- `FACTURAMA_EXPEDITION_PLACE`

### Arranque

Railway construira la imagen con Docker y arrancara la app usando:

- `php -S 0.0.0.0:$PORT router.php`

### Webhook de Conekta

Cuando Railway te asigne dominio, configura el webhook en:

- `https://TU-DOMINIO.up.railway.app/api/webhooks/conekta.php`
