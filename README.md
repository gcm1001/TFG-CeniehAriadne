<h1 align="center">
  <a href="https://ariadne-infrastructure.eu/" target="_blank">
    <img src="/docs/readme/images/readme-ariadne.png" alt="ARIADNEplus Logo" width="550"/>
  </a>
</h1>
<h2 align="center">
  <img src="/docs/readme/images/readme-title.png" width="430" alt="Title"/>
</h2>
<h4 align="center">TFG de la <a href="https://www.ubu.es/">UBU</a> en colaboración con el <a href="https://www.cenieh.es/" target="_blank">CENIEH</a>.</h4>
<h3 align="center">
  <img src="/docs/sphinx/source/_static/images/ubucenieh.png" alt="UBU & CENIEH Logo"/> 
</h3>
<p align="center">
    <a href="https://github.com/gcm1001/TFG-CeniehAriadne/actions?query=workflow%3A%22Build+and+Deploy+to+GKE%22">
      <img src="https://github.com/gcm1001/TFG-CeniehAriadne/workflows/Build%20and%20Deploy%20to%20GKE/badge.svg" alt="GKECIDC" />
    </a>
    <a href='https://tfg-ceniehariadne.readthedocs.io/es/latest/?badge=latest'>
        <img src='https://readthedocs.org/projects/tfg-ceniehariadne/badge/?version=latest' alt='Documentation Status' />
    </a>
  <a href="https://www.codacy.com/manual/gcm1001/TFG-CeniehAriadne?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=gcm1001/TFG-CeniehAriadne&amp;utm_campaign=Badge_Grade"><img src="https://app.codacy.com/project/badge/Grade/5a86b32c970a40a981b82a1324254596"/></a>
</p>
<p align="center">
  <a href="#descripción">Descripción</a> •
  <a href="#despliegue">Despliegue</a> •
  <a href="#plugins">Plugins</a> •
  <a href="#licencia">Licencia</a>
</p>

## Tabla de contenidos

[**Descripción**](#descripción)

[**Despliegue**](#descripción)
  * [**1 - Manual**](#manual)
  * [**2 - Docker**](#docker)
  * [**3 - Kubernetes**](#kubernetes)

[**Plugins**](#plugins)
  * [**Plugins propios**](#plugins-propios)
  * [**Plugins modificados**](#plugins-modificados)
  * [**Otros**](#otros)

[**Licencia**](#licencia)

<img align="right" src="/docs/readme/gifs/readme-desc.gif" width="450"/>

## Descripción

En el presente TFG se propone una infraestructura software capaz de gestionar los conjuntos de datos del CENIEH para posteriormente ser integrados en la plataforma Ariadne+. La aplicación escogida para llevar a cabo este cometido ha sido [Omeka Classic](https://omeka.org/classic/). Sobre esta se han realizado una serie de desarrollos propios (_plugins_) con el fin de adaptar dicha aplicación a las necesidades del proyecto.


## Despliegue

Existen tres posibilidades distintas para desplegar la aplicación a tu servidor.

### Manual

Si escoges está opción deberás estar seguro de que tu servidor cumple con todos y cada uno de los siguientes **requisitos**:

- Sistema Operativo Linux
- Apache HTTP Server (con el módulo ***mod_rewrite*** activado)
- MySQL / MariaDB v5.0 o superior.
- PHP v5.4 o superior con las sisguientes extensiones instaladas:
    - mysqli
    - exif 
    - curl
    - pdo
- ImageMagick (Tratamiento de imágenes)

El siguiente consistirá en **configurar tu servidor**. Para ello, hay que seguir una serie de pasos:

1. **Crear la base de datos MySQL** desde un usuario con permisos suficientes como para poder realizar operaciones sobre ella.
    - Conviene que apuntes por separado los siguientes datos:
        - Hostname.
        - Nombre de la BD.
        - Nombre del usuario de la BD.
        - Contraseña de usuario de la BD.
    - La base de datos ha de estar codificada en `utf8`. Actualmente la opción más recomendable para ello es mediante el siguiente comando:
    ```
    CREATE DATABASE mydatabase CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ```
2. **Descargar** la última version de **Omeka**, desde su [web oficial](https://omeka.org/classic/download/) o desde su [repositorio](http://github.com/omeka/Omeka) en GitHub.
3. **Descomprimir** el fichero `.zip` recién descargado sobre un directorio donde podamos trabajar.
4. Buscar el fichero `db.ini` y sustituir los valores 'XXXXX' por los datos de la base de datos (anotados en el paso 1).
    - No es necesario modificar los parámetros `prefix` o `port`.
5. Movemos todo el contenido a la carpeta al servidor.
6. **Dar permisos de escritura sobre la carpeta `files`**.

Desde este instante, la aplicación será accesible desde el navegador. El último paso consistiría en completar la instalación guiada desde el navegador, disponible a través de tu dirección URL (e.g. http://mydomain.org/install).

[***Documentación Oficial de Omeka***](https://omeka.org/classic/docs/Installation/Installation/)

### Docker

<img align="right" src="/docs/readme/images/readme-docker.png" width="240">

He optado por desarrollar un entorno con tecnología Docker para facilitar el despliegue de la aplicación. En este caso, los **requisitos** son:

 - Docker (Testado con la versión 19.03.6).
 - Configurar el host como _swarm_.

Para proceder al despliegue **debes descargar**, de este repositorio, los siguientes ficheros:

- /Dockerfile
- /docker-compose.yml
- /ConfigFiles/*
- /omeka/plugins/*

**IMPORTANTE**: Mantén los subdirectorios intactos.

A continuación debes **compilar la imagen**. Para ello, desde el directorio raiz (donde tengas el fichero _Dockerfile_), ejecuta el siguiente comando:
```
docker build -t nombre_imagen:tag .
```
Recuerda muy bien el nombre de la imagen y el tag que pongas ya que será necesario para el siguiente paso, que consiste en configurar el `docker-compose.yml`.

En él, solo tenemos que cambiar la etiqueta `image` del servicio `omeka_app`:
```
...
  omeka_app:
    image: nombre_imagen:tag
```

Si hemos publicado nuestra imagen en _DockerHub_, deberemos añadir además nuestro nombre de usuario (e.g. username/nombre_de_mi_imagen:tag).

**IMPORTANTE:** elimina el servicio `omeka-db-admin` si tu servidor está destinado a producción. Este servicio implementa la herramienta PhpMyAdmin, con un alto grado de vulnerabilidades.

Por último, debes crear los _secrets_ correspondientes a las contraseñas de la base de datos:

```
echo 'contraseña_usuario_db' | docker secret create omeka_db_password -
echo 'contraseña_root_db' | docker secret create omeka_db_root_password -
cp configFiles/db.ini.example configFiles/db.ini
```

**IMPORTANTE**: debes modificar el fichero recién creado `db.ini` con los datos de la base de datos. Ten en cuenta que la contraseña que introduzcas en el fichero tiene que coincidir con la del  `secret` creado anteriormente.

Por último, ejecuta el siguiente comando desde el directorio raiz (donde se encuentra `docker-compose.yml`).

```
docker stack deploy -c docker-compose.yml nombre_del_entorno
```

Desde este instante la aplicación debería ser accesible desde el navegador (puerto 80).

<img align="right" src="/docs/readme/images/readme-kubernetes.png" width="280">

### Kubernetes

Requisitos:

- Kubernetes (probado en v1.18.2)
- Kustomize (probado en v3.1.0)
- Docker

El primer paso para desplegar la aplicación mediante _Kubernetes_ es montar nuestra imagen _Docker_ (Sigue los primeros pasos del punto anterior, **hasta la compilación de la imagen**).

El siguiente paso consiste en desplegar la aplicación. Para esta tarea utilizo el gestor de objetos _Kustomize_. Por ello, deberás contar con dicha herramienta. Además debes estar en posesión de los siguientes ficheros alojados en este repositorio:

- /kustomization.yaml
- /patch.yaml
- /gke-mysql/*
- /gke-omeka/*
- /configFiles/db.ini

Ahora, debes crear el `secret` que contendrá todos los datos privados necesarios para crear la la base de datos (nombre de la base de datos, nombre de usuario, contraseña de usuario y contraseña root). 

**IMPORTANTE**: Antes de ejecutar los siguientes comandos debes crear las _variables de entorno_ que se están utilizando.

```
 kubectl create secret omeka-db \
--from-literal=user-password=$DB_PASSWORD \
--from-literal=root-password=$DB_ROOT_PASSWORD \
--from-literal=username=$DB_USERNAME \
--from-literal=database=$DB_DATABASE

```

Además debemos crear el `configmap` que almacenará todo el contenido del fichero de configuración `db.ini` (no necesitas modificarlo ya que este emplea las variables de entorno utilizadas en los comandos anteriores).

```
kubectl create configmap db-config \
--from-file ./configFiles/db.ini
```

Por último, debemos indicar el identificador de nuestra imagen _Docker_ en el fichero `/gke-omeka/deployment.yaml`. 

```
...
    spec:
      containers:
      - image: nombre_imagen:tag
...
```

Tras esto, solo faltaría ejecutar, desde el directorio raíz, el siguiente comando:

```
kustomize build . | kubectl apply -f -
```

Desde este instante la aplicación debería ser accesible desde el navegador (puerto 80).

## Plugins

### Plugins propios

#### [ARIADNEplus Tracking](/omeka/plugins/ARIADNEplusTracking/)

#### [CIRCeniehAriadne](/omeka/plugins/CIRCeniehAriadne/)

#### [Collection Files](/omeka/plugins/CollectionFiles/)

#### [Tags Manager](/omeka/plugins/TagsManager/)

#### [Admin Nav Style](/omeka/plugins/AdminNavStyle/)

#### [IsPartOfCollection](/omeka/plugins/IsPartOfCollection/)

### Plugins modificados

#### [Geolocation](/omeka/plugins/Geolocation)

### Otros

#### [BulkMetadataEditor](https://omeka.org/classic/plugins/)

#### [CSVExport](https://omeka.org/classic/plugins/)

#### [CsvImportPlus](https://omeka.org/classic/plugins/)

#### [DublinCoreExtended](https://omeka.org/classic/plugins/)

#### [GettySuggest](https://omeka.org/classic/plugins/)

#### [GuestUser](https://omeka.org/classic/plugins/)

#### [Hide Elements](https://omeka.org/classic/plugins/)

#### [HistoryLog](https://omeka.org/classic/plugins/)

#### [OaiPmhRepository](https://omeka.org/classic/plugins/)

#### [OaiPmhHarvester](https://omeka.org/classic/plugins/)

#### [SimplePages](https://omeka.org/classic/plugins/)

#### [SimpleVocab](https://omeka.org/classic/plugins/)

#### [SuperRss](https://omeka.org/classic/plugins/)


## Licencia