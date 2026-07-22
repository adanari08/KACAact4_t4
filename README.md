#  API de Música con Autenticación — KACAact4t4

Este es el proyecto de la **Actividad 4 (Tema 4)**: construir una API REST en Laravel, con autenticación real usando tokens (Sanctum), probada con una herramienta llamada Bruno.

## ¿Cómo funciona la API?

Aquí lo que construí es una **API**, que es básicamente un "servicio" al que otras aplicaciones (como una app de celular, una página hecha en React, etc.) le pueden preguntar cosas y él les responde, pero en vez de responder con una página bonita, responde con puro **texto en formato JSON** (que es como un objeto de información ordenado, fácil de leer para una computadora).

Esta API está construida en **Laravel** siguiendo el patrón MVC, con autenticación por tokens usando **Laravel Sanctum**. El flujo general es:

1. El cliente (en este caso, **Bruno**, una herramienta para probar APIs) envía una petición HTTP a una URL como `http://187.127.254.39/KACAact4t4/api/...`.
2. **Nginx**, en el VPS, recibe la petición y la enruta hacia `public/index.php` de Laravel.
3. Laravel revisa **`routes/api.php`** para encontrar qué controlador debe atender esa ruta.
4. El **Controlador** valida los datos recibidos (si algo falta, responde con error 422) y, si todo está correcto, usa **Eloquent** (el ORM de Laravel) para leer o escribir en la base de datos **MySQL**.
5. La respuesta se transforma con un **API Resource**, que decide exactamente qué campos se devuelven en JSON (por ejemplo, nunca se expone el `password` del usuario).
6. Bruno recibe la respuesta en formato JSON y la muestra.

Las rutas protegidas (crear, actualizar, eliminar canciones) requieren un **token Bearer** que se obtiene al hacer login. Ese token se guarda en la tabla `personal_access_tokens` y se manda en cada petición dentro del header `Authorization`.

Para poder probar la API tanto en el entorno local como en el VPS sin cambiar cada URL a mano, se configuraron **Environments** en Bruno con una variable `base_url`, que apunta a `http://187.127.254.39/KACAact4t4/api` cuando se selecciona el environment "VPS".

Por ejemplo, en vez de ver una tabla de canciones en el navegador, si le pido a mi API "dame todas las canciones", me responde algo así:

```json
{
  "data": [
    {
      "id": 1,
      "titulo": "Canción de Prueba",
      "album": { "titulo": "Horizonte eterno" }
    }
  ]
}
```

Y otro punto importante: aquí **no cualquiera puede usar la API**. Antes tienes que "iniciar sesión" y la API te da una especie de "llave" (un token) que tienes que enseñar cada vez que le pides algo. Si no traes esa llave, te rechaza.

## El tema es la Musica

Uso las mismas 3 entidades relacionadas: **Álbum**, **Canción** y **Género** (las relaciones: un Álbum tiene muchas Canciones, y una Canción puede tener varios Géneros). La diferencia es que aquí todo el CRUD (crear, ver, editar, borrar canciones) se hace a través de la API, no de páginas web.

##  Herramientas que usé y para qué sirve cada una

| Herramienta | Para qué la usé, en palabras sencillas |
|---|---|
| **Laravel 12** | El framework de PHP con el que está hecho todo. Me ahorra tener que programar desde cero cosas como las rutas, la conexión a la base de datos, etc. |
| **Composer** | El programa que instala Laravel y todas las demás "piezas" (paquetes) que necesita el proyecto para funcionar, como Sanctum. |
| **MySQL** | Donde se guardan de verdad los datos: los álbumes, las canciones, los géneros, y también los usuarios y sus tokens. |
| **Laravel Sanctum** | Es el paquete que le da a mi proyecto la capacidad de manejar "logins" con tokens. Sin Sanctum, cualquiera podría crear, editar o borrar canciones sin haberse identificado. Lo tuve que investigar por mi cuenta porque el video que vimos en clase no lo explicaba a fondo. |
| **API Resources** | Son como "filtros" que decido yo mismo, que controlan exactamente qué información sale en el JSON de respuesta. Por ejemplo, así me aseguro de que la contraseña del usuario JAMÁS aparezca en ninguna respuesta, aunque internamente sí esté guardada (encriptada) en la base de datos. |
| **Bruno** | Es un programa (parecido a Postman, pero gratis) que sirve para "simular" que soy una aplicación externa y probar mi API a mano: mandarle peticiones, ver qué me responde, con o sin token, etc. Sin una herramienta así sería muy difícil probar una API, porque no se puede probar completa solo desde el navegador. |
| **Git y GitHub** | Para subir mi código a un repositorio en línea, incluyendo las pruebas que hice con Bruno como evidencia. |
| **Nginx + PHP-FPM** | El servidor que hace que mi proyecto esté disponible en internet, ya no solo en mi computadora, sino en el VPS (una especie de "computadora rentada" que usamos como equipo). |

##  Cómo se conecta todo 

1. **Alguien (o algo) manda una petición** a una URL de mi API, por ejemplo `POST /api/register` para registrarse.
2. **Laravel revisa las rutas** en el archivo `routes/api.php` (que es distinto al `routes/web.php` que usé en Act3 — este es especial solo para peticiones de API, no para páginas web) y decide a qué "controlador" mandar esa petición.
3. Si la ruta está protegida (como crear una canción), **Sanctum revisa si la petición trae un token válido** en sus cabeceras. Si no lo trae, ahí mismo la rechaza con un error 401 y ni siquiera deja que el código siga corriendo.
4. Si todo está bien, el **controlador** hace lo que le pedían (buscar, crear, editar o borrar algo), hablando con la base de datos a través de los **modelos** (Álbum, Canción, Género).
5. Antes de responder, pasa los datos por un **API Resource**, que decide exactamente qué información mandar de vuelta (y qué esconder, como la contraseña).
6. La respuesta final sale en formato **JSON**.

##  Cómo funciona la parte de autenticación (Sanctum)

1. Un usuario nuevo manda sus datos a `/api/register` (nombre, correo, contraseña).
2. Laravel guarda ese usuario en la base de datos, pero **la contraseña nunca se guarda tal cual la escribió** — se guarda "encriptada" (con una función llamada `Hash::make()`), para que ni siquiera alguien con acceso a la base de datos pueda leerla directamente.
3. Justo después de registrarse, Sanctum le genera un **token**: un texto largo y único, como una contraseña temporal especial solo para usar la API.
4. De ahí en adelante, cada vez que ese usuario quiera hacer algo (crear una canción, por ejemplo), tiene que mandar ese token en sus peticiones (en Bruno, esto se configura en la pestaña "Auth" como "Bearer Token").
5. Si alguien intenta usar un endpoint protegido **sin** ese token, Laravel automáticamente lo rechaza con un código de error **401** y un mensaje `{"message": "Unauthenticated."}` — sin llegar siquiera a tocar la base de datos. Esto es justo lo que se ve al entrar desde el navegador normal a una ruta protegida, porque el navegador no tiene forma de mandar un token.

##  Validación de datos

Si alguien manda datos incompletos o mal formados al crear o editar una canción (por ejemplo, sin título), Laravel automáticamente responde con código **422** y un JSON explicando exactamente qué campo falló, por ejemplo:

```json
{
  "message": "The titulo field is required.",
  "errors": {
    "titulo": ["The titulo field is required."]
  }
}
```

##  Cómo probé todo (con Bruno)

Armé una colección de pruebas en Bruno (guardada en la carpeta `KACAact4t4-API` de este mismo repositorio) con una petición por cada cosa que debía probar:

1. Registro de usuario
2. Login
3. Crear una canción (con token)
4. Listar canciones (con paginación)
5. Ver una canción específica
6. Actualizar una canción
7. Probar que falle la validación a propósito (mandando datos incompletos)
8. Eliminar una canción

También probé que, sin mandar el token, cualquier intento de usar el CRUD es rechazado.

##  Cómo se desplegó en el VPS

1. Subí el proyecto a GitHub (sin subir el `.env` ni la carpeta `vendor/`, que están en `.gitignore`).
2. Me conecté al VPS por SSH y cloné el repositorio ahí.
3. Instalé las dependencias con Composer (tuve que ajustar algunas versiones porque el VPS tiene una versión de PHP distinta a mi computadora).
4. Creé una base de datos y un usuario de MySQL exclusivos para este proyecto en el servidor.
5. Configuré un `.env` nuevo en el servidor (distinto al de mi computadora, con los datos de la base de datos del VPS).
6. Corrí las migraciones y el seeder ahí (`php artisan migrate --seed`).
7. Configuré Nginx para que reconociera la carpeta de mi proyecto y la sirviera correctamente, apuntando a la carpeta `public/`.


## Endpoints

| Método | Endpoint | Protegido | Descripción |
|--------|----------|-----------|-------------|
| POST | `/api/register` | No | Registra un nuevo usuario y devuelve sus datos junto con un token |
| POST | `/api/login` | No | Autentica a un usuario existente y devuelve un token nuevo |
| GET | `/api/canciones` | Sí | Lista todas las canciones (paginado), con su álbum y géneros |
| GET | `/api/canciones/{id}` | Sí | Muestra el detalle de una canción específica |
| POST | `/api/canciones` | Sí | Crea una canción nueva (valida que `titulo` sea obligatorio) |
| PUT | `/api/canciones/{id}` | Sí | Actualiza los datos de una canción existente |
| DELETE | `/api/canciones/{id}` | Sí | Elimina una canción |

## Prueba en vivo: Bruno ↔ VPS ↔ MySQL

Para comprobar que las operaciones desde Bruno se reflejan realmente en la base de datos del VPS (y no solo en local), se hizo la siguiente prueba con el environment **VPS** activo en Bruno:

### 1. Login (`POST /api/login`)
Se autenticó con un usuario ya registrado y la API devolvió el token real generado por Sanctum.
<img width="1920" height="1020" alt="image" src="https://github.com/user-attachments/assets/25f44d35-e98a-4d4b-b176-82a9a1491a84" />


### 2. Crear canción (`POST /api/canciones`)
Usando el token del login como Bearer Token, se creó la canción "titi me pregunto", asociada a un álbum y dos géneros existentes.
<img width="1920" height="1020" alt="image" src="https://github.com/user-attachments/assets/909a69d4-01dc-45b8-876d-726a863d1ac0" />


### 3. Actualizar canción (`PUT /api/canciones/3`)
Se actualizaron la duración, el año y los géneros de la canción creada.
<img width="1920" height="1020" alt="image" src="https://github.com/user-attachments/assets/b399918e-01ae-4469-b82f-e0baec97163d" />


### 4. Eliminar canción (`DELETE /api/canciones/2`)
Se eliminó una canción de prueba anterior.
<img width="1920" height="1020" alt="image" src="https://github.com/user-attachments/assets/ae8bca4f-6b64-4cfb-98b9-99f59e4f0f94" />


### 5. Verificación directa en MySQL (VPS, por SSH)
Se entró por SSH al VPS y se consultó directamente la base de datos `kaca_musica_api_prod`, confirmando que los cambios hechos desde Bruno sí quedaron guardados ahí:

```sql
USE kaca_musica_api_prod;
SELECT * FROM canciones;
```

Resultado: la canción creada y actualizada desde Bruno aparece con los mismos datos en la tabla `canciones` del VPS, y la eliminada ya no aparece — confirmando que la API en producción lee y escribe correctamente en la base de datos real del servidor.


