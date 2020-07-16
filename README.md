<h1 align="center">
  <a href="https://ariadne-infrastructure.eu/" target="_blank">
    <img src="/docs/readme/images/readme-ariadne.png" alt="ARIADNEplus Logo" width="550"/>
  </a>
</h1>
<h2 align="center">
  <img src="/docs/readme/images/readme-title.png" width="430" alt="Title"/>
</h2>
<p align="center">
    <a href="https://github.com/gcm1001/TFG-CeniehAriadne/actions?query=workflow%3A%22Build+and+Deploy+to+GKE%22">
      <img src="https://github.com/gcm1001/TFG-CeniehAriadne/workflows/Build%20and%20Deploy%20to%20GKE/badge.svg" alt="GKECIDC" />
    </a>
    <a href='https://tfg-ceniehariadne.readthedocs.io/es/latest/?badge=latest'>
        <img src='https://readthedocs.org/projects/tfg-ceniehariadne/badge/?version=latest' alt='Documentation Status' />
    </a>
  <a href="https://www.codacy.com/manual/gcm1001/TFG-CeniehAriadne?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=gcm1001/TFG-CeniehAriadne&amp;utm_campaign=Badge_Grade"><img src="https://app.codacy.com/project/badge/Grade/5a86b32c970a40a981b82a1324254596"/></a>
</p>
<h4 align="center">TFG de la <a href="https://www.ubu.es/">UBU</a> en colaboración con el <a href="https://www.cenieh.es/" target="_blank">CENIEH</a>.</h4>
<h3 align="center">
  <img src="/docs/sphinx/source/_static/images/ubucenieh.png" alt="UBU & CENIEH Logo"/> 
</h3>
<p align="center">
  <b>Autor</b><br>
  <i>Gonzalo Cuesta Marín</i><br>
  <b>Tutores</b><br>
  <i>Carlos López Nozal</i><br>
  <i>Mario Juez Gil</i><br>
  <b>Colaboradores del CENIEH</b><br>
  <i>Javier Valladolid Aguinaga</i><br>
  <i>Joseba Rios Garaizar</i><br>
</p>

## 🚩 Tabla de contenidos

[**Descripción**](#-descripción)

[**Despliegue**](#-despliegue)

* [**1 - Manual**](#manual)
* [**2 - Docker**](#docker)
* [**3 - Kubernetes**](#kubernetes)

[**Plugins**](#plugins)

* [**Plugins propios**](#plugins-propios)
* [**Plugins modificados**](#plugins-modificados)
* [**Otros**](#otros)

[**Licencia**](#licencia)

<img align="right" src="/docs/readme/gifs/readme-desc.gif" width="450"/>

## 💬 Descripción

En el presente TFG se propone una infraestructura _software_ capaz de gestionar los conjuntos de datos del CENIEH para posteriormente ser integrados en la plataforma _ARIADNEplus_. La aplicación escogida para llevar a cabo este cometido ha sido [Omeka Classic](https://omeka.org/classic/). Sobre esta se han realizado una serie de [desarrollos propios](#plugins-propios) (_plugins_) con el fin de adaptar dicha aplicación a las necesidades del proyecto.

-----

## 🚀 Despliegue

Existen tres posibilidades distintas para desplegar la aplicación en tu servidor: [Manual](#manual), [Docker](#docker) o [Kubernetes](#kubernetes).

### Manual

Si escoges está opción deberás estar seguro de que tu servidor cumple con todos y cada uno de los siguientes **requisitos**:

  * Sistema Operativo Linux
  * Apache HTTP Server (con el módulo _rewrite_ activado)
  * MySQL / MariaDB v5.0 o superior.
  * PHP v5.4 o superior con las sisguientes extensiones instaladas:
    - mysqli
    - exif 
    - curl
    - mbstring

  * ImageMagick (Tratamiento de imágenes)

El siguiente consistirá en **configurar tu servidor**. Para ello, hay que seguir una serie de pasos:

1. **Crear la base de datos MySQL** desde un usuario con permisos suficientes como para poder realizar operaciones sobre ella.

   * Durante el proceso, conviene que apuntes por separado los siguientes datos:

      - _Hostname_.
      - Nombre de la BD.
      - Nombre del usuario de la BD.
      - Contraseña de usuario de la BD.

   * La base de datos ha de estar codificada en `utf8`. Actualmente la opción más recomendable para ello es mediante el siguiente comando:

   ```
   CREATE DATABASE mydatabase CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. **Descargar** la última version de **Omeka**, desde su [web oficial](https://omeka.org/classic/download/) o desde su [repositorio](http://github.com/omeka/Omeka) en GitHub.
3. **Descomprimir** el fichero `.zip` recién descargado sobre un directorio donde podamos trabajar.
4. Buscar el fichero `db.ini` y sustituir los valores 'XXXXX' por los datos de la base de datos (anotados en el paso 1).
   - No es necesario modificar los parámetros `prefix` o `port`.
5. Movemos todo el contenido a la carpeta al servidor.
6. **Dar permisos de escritura sobre la carpeta `files`**.

Desde este instante, la aplicación será accesible desde el navegador. El último paso consistiría en completar la instalación guiada desde el navegador, disponible en el directorio `/install/` de la aplicación (e.g. http://localhost/install).

[***Documentación Oficial de Omeka***](https://omeka.org/classic/docs/Installation/Installation/)

### Docker

<img align="right" src="/docs/readme/images/readme-docker.png" width="240">

He optado por desarrollar un entorno con tecnología Docker para facilitar el despliegue de la aplicación. En este caso, los **requisitos** son:

- Docker (Testado con la versión 19.03.6).
- Configurar el host como _swarm_.

Para proceder al despliegue **debes descargar**, de este repositorio, los siguientes ficheros:

- /Dockerfile
- /docker-compose.yml
- /ConfigFiles/*.modificar
- /omeka/plugins/*

**IMPORTANTE**: Mantén los subdirectorios intactos.

Opcionalmente modificar los ficheros `/ConfigFiles/php.ini.modificar` y `/ConfigFiles/config.ini.modificar` a tu gusto.

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

Si hemos publicado nuestra imagen en _DockerHub_, deberemos añadir además nuestro nombre de usuario (e.g. *username/nombre_de_mi_imagen:tag*).

Por último, debes crear los _secrets_ correspondientes a las contraseñas de la base de datos:

```
echo 'contraseña_usuario_db' | docker secret create omeka_db_password -
echo 'contraseña_root_db' | docker secret create omeka_db_root_password -
cp configFiles/db.ini.modificar configFiles/db.ini
cp configFiles/mail.ini.modificar configFiles/db.ini
```

**IMPORTANTE**: debes modificar los ficheros recién creados (`db.ini` y `mail.ini` con los datos relacionados con la base de datos y el protocolo _IMAP_. Ten en cuenta que la contraseña que introduzcas en el fichero tiene que coincidir con la del _secret_ `omeka_db_password`.

Por último, ejecuta el siguiente comando desde el directorio raiz (donde se encuentra `docker-compose.yml`).

```
docker stack deploy -c docker-compose.yml nombre_del_entorno
```

Desde este instante la aplicación debería ser accesible desde el navegador (puerto 80). El último paso consistiría en completar la instalación guiada desde el navegador, disponible en el directorio `/install/` de la aplicación (e.g. http://localhost/install).

<img align="right" src="/docs/readme/images/readme-kubernetes.png" width="280">

### Kubernetes

Requisitos:

- Kubernetes (probado en v1.18.2)
- Kustomize (probado en v3.1.0)
- Docker

El primer paso para desplegar la aplicación mediante _Kubernetes_ es montar nuestra imagen _Docker_ (Sigue los primeros pasos del punto anterior, **hasta la compilación de la imagen**).

El siguiente paso consiste en desplegar la aplicación. Para esta tarea utilizo el gestor de objetos _Kustomize_. Por ello, deberás contar con dicha herramienta. Además debes estar en posesión de los siguientes ficheros alojados en este repositorio:

- `/kustomization.yaml`
- `/patch.yaml`
- `/gke-mysql/*`
- `/gke-omeka/*`
- `/configFiles/db.ini.gke`
- `/configFiles/mail.ini.gke`
- `/configFiles/config.ini.gke`

Se deben definir en el servidor los _secrets_ y _configMaps_ utilizados por los ficheros de configuración `.yaml`.

Para ello se ejecutan los siguientes comandos:

- `omeka-db`: *secretos* relacionados con la base de datos.

```
   kubectl create secret generic omeka-db \
   --from-literal=user-password=<contraseña_db_usuario> \
   --from-literal=root-password=<contraseña_db_root> \
   --from-literal=username=<nombre_usuario>\
   --from-literal=database=<nombre_bd>

```
- `omeka-snmp`: *secretos* relacionados con el protocolo SNMP.

.. code-block::

   kubectl create secret generic omeka-snmp \
   --from-literal=host=<host_snmp> \
   --from-literal=username=<correo_electronico> \
   --from-literal=password=<contraseña_correo> \
   --from-literal=port=<puerto_snmp> \
   --from-literal=ssl=<protocolo_seguridad_snmp>

- `omeka-imap`: *secretos* relacionados con el protocolo IMAP.

.. code-block::

   kubectl create secret generic omeka-imap \
   --from-literal=host=<host_imap> \
   --from-literal=username=<correo_electronico> \
   --from-literal=password=<contraseña_correo> \
   --from-literal=port=<puerto_imap> \
   --from-literal=ssl=<protocolo_seguridad_imap>

- `db-config`: *mapa de configuración* para la base de datos.

.. code-block::

   kubectl create configmap db-config \
   --from-file=./configFiles/db.ini.gke

- `snmp-config`: *mapa de configuración* para el protocolo SNMP.

.. code-block::

   kubectl create configmap snmp-config \
   --from-file=./configFiles/config.ini.gke

- `imap-config`: *mapa de configuración* para el protocolo IMAP.

.. code-block::

   kubectl create configmap imap-config \
   --from-file=./configFiles/mail.ini.gke

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

Desde este instante la aplicación debería ser accesible desde el navegador (puerto 80). El último paso consistiría en completar la instalación guiada desde el navegador, disponible en el directorio `/install/` de la aplicación (e.g. http://localhost/install).

## 📦 *Plugins*

### *Plugins* propios

| Nombre | Descripción |
| --- | --- |
| [ARIADNEplus Tracking](/omeka/plugins/ARIADNEplusTracking/) | Lleva un seguimiento del proceso de importación a ARIADNEplus |
| [CENIEHExport](/omeka/plugins/CENIEHExport/) | Permite exportar los ítems en un formato XML compatible con ARIADNEplus |
| [Collection Files](/omeka/plugins/CollectionFiles/) | Permite asociar ficheros a colecciones |
| [Tags Manager](/omeka/plugins/TagsManager/) | Gestiona los tags existentes en la plataforma |
| [Admin Menu Design](/omeka/plugins/AdminMenuDesign/) | Cambia el diseño del menú y añade secciones a este |
| [Auto Dublin Core](/omeka/plugins/AutoDublinCore/) | Actualiza el campo "Is Part Of" y "Source" del modelo de metadatos "Dublin Core" de forma automática |

### *Plugins* de terceros modificados

| Nombre | Descripción de los cambios |
| --- | --- |
| [Geolocation](/omeka/plugins/Geolocation) | Nuevo formato de localización (*Bounding Box*), Sincronización con los metadatos. |
| [OAI-PMH Repository](/omeka/plugins/OaiPmhRepository) | Añadir una hoja de estilo (*Stylesheet*) a los documentos XML generados |
| [OAI-PMH Harvester](/omeka/plugins/OaipmhHarvester) | Convertir la codificación de los metadatos importados a UTF-8 |
| [CSV Import Plus](/omeka/plugins/CsvImportPlus) | PopUps de ayuda y actualización automática |

### *Plugins* de terceros utilizados

| Nombre | Descripción |
| --- | --- |
| [BulkMetadataEditor](https://omeka.org/classic/plugins/BulkMetadataEditor/) | Permite editar multitud de ítems a la vez |
| [CSVExport](https://omeka.org/classic/plugins/CsvExport/) | Exporta ítems en formato CSV |
| [CsvImportPlus](https://github.com/biblibre/omeka-plugin-CsvImportPlus) | Importa ítems en formato CSV |
| [DublinCoreExtended](https://omeka.org/classic/plugins/DublinCoreExtended/) | Añade el esquema de metadatos *Dublin Core Extended* a la plataforma  |
| [GettySuggest](https://github.com/UCSCLibrary/GettySuggest) | Sugiere términos del vocabulario Getty AAT a la hora de rellenar metadatos |
| [Hide Elements](https://omeka.org/classic/plugins/HideElements/) | Permite ocultar campos del esquema de metadatos |
| [HistoryLog](https://omeka.org/classic/plugins/HistoryLog/) | Genera registros en cada creación/modificación/eliminación de ítems |
| [OaiPmhRepository](https://omeka.org/classic/plugins/OaiPmhRepository/) | Permite que otros repositorios puedan importar metadatos existentes en tu repositorio a través del protocolo OAI-PMH |
| [OaiPmhHarvester](https://omeka.org/classic/plugins/OaipmhHarvester/) | Permite importar metadatos de otros repositorios a través del protocolo OAI-PMH |
| [SimplePages](https://omeka.org/classic/plugins/) | Permite añadir páginas al repositorio de una forma sencilla. |
| [SimpleVocab](https://omeka.org/classic/plugins/) | Añade vocabularios al gestor. |
| [SuperRss](https://omeka.org/classic/plugins/) | Permite compartir publicaciones en redes sociales |

## 🎨 Tema

| Nombre | Descripción |
| --- | --- |
| [Curatescape](https://github.com/CPHDH/Curatescape) | Diseño minimalista y elegante |

## 📜 Licencia

Todo el software desarrollado está bajo la licencia [GPLv3](https://www.gnu.org/licenses/gpl-3.0.html)
