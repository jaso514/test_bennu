## Test Bennu

Este proyecto consiste en el desarrollo de dos servicios para suscripción y cancelar suscripciones.

Adicional se debe realizar un comando para obtener las suscripciones.

Para iniciar el proyecto se debe ejecutar el comando: 

´´´
php artisan migrate
´´´

Esto ejecuta el archivo de migración: 2019_05_08_022611_create_schema, que a la vez corre el script encontrado en database/scripts/test_bennu.sql.

Luego se debe ejecutar el comando para cargar los usuarios:

´´´
php artisan db:seed --class=UsersSeeders
´´´


### Base de datos

Se crearon cuatro (4) tablas, para resolver el test. Estas tablas son:
- __users__: tiene los usuarios clientes del sistema. Estos tienen como nro_cliente 5 digitos, siempre empezando por ceros (0), ejemplo 00001, 00012. Estos son agregados con el Seeder de usuarios.
- __services__: tiene los servicios que se ofrecen, se usaran los siguientes códigos: premium, premium_hd, classic, classic_hd
- __subscriptions__: tiene el estado de subscripción
- __status__: tiene los estados de subscripción: subscribe (crea la suscribirse), unsubscribe (cancela la suscripción), pause (opcional: pausa la suscripción).


## Servicios:

Primero se debe correr el servidor con el comando:

´´´
php artisan serve
´´´

### Servicio de suscripción:

URL: http://localhost:8000/api/subscription
Method: POST
Request:

´´´
{
"customer": "00001",
"date": "2019-05-08",
"service": "classic"
}
´´´

Response:
404: Customer not found
404: Service not found
406: Not Acceptable. Ej.: ya tiene el servicio
201: 

´´´
{
    "user_id": 56,
    "service_id": 3,
    "status_id": 1,
    "status_change": "2019-05-08 00:00:00",
    "updated_at": "2019-05-11 02:00:50",
    "created_at": "2019-05-11 02:00:50",
    "id": 5,
    "user": {
        "id": 56,
        "nro_cliente": "000000000006",
        "nro_documento": 23811421
    },
    "service": {
        "id": 3,
        "name": "Classic",
        "code": "classic"
    },
    "status": {
        "id": 1,
        "status": "subscribe"
    }
}
´´´

### Servicio de cancelación de suscripción:

URL: http://localhost:8000/api/subscription/<nro_cliente>/<codigo_servicio>
Ej: http://localhost:8000/api/subscription/00001/classic
Method: DELETE
Response:
404: Customer not found
404: Service not found
406: Not Acceptable. Ej.: ya no tiene el servicio
204: Success


## Comando de resumen de suscripciones:

Muestra un resumen de las sucripciones creadas y canceladas en una fecha dada, además de todas las activas hasta la fecha.
Se debe correr el comando así:

´´´
php artisan report:subscriptions 2019-05-08
´´´

Si hay algún error en la fecha muestra un error como el siguiente:

´´´
Error: Invalid date format, must be YYYY-MM-DD.
´´´

En caso de éxito el resumen queda así:

´´´
Resumen de suscripciones para el día: 2019-05-08
- Cantidad de nuevas suscripciones: 1
- Cantidad de suscripciones canceladas: 0
- Cantidad de suscripciones activas: 1
´´´

## Clases creadas

- SubscriptionController: class de los servicio REST.
- SubscriptionsReport: Es la clase del comando de consola
- Modelos: Todos se ubican en la ruta: App\Models
- SubscriptionRepository: clase en el namespace App\Repositories, se encarga de realizar el conjunto de queries relacionados a las suscripciones.

## Notas:

- Instalar con composer install
- Recordar configurar el ./.env con los datos de la BD con nombre: test_bennu
- crear key de seguridad con el comando: php artisan key:generate