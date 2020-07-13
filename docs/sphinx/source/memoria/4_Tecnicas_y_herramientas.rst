=======================
Tecnicas y herramientas
=======================

Metodologías
------------

Scrum
~~~~~
Scrum es un marco de trabajo donde se ejecutan procesos ágiles que contribuyen al desarrollo y mantenimiento de productos *software*. Por ello, está catalogado como una metodología ágil, la cual se caracteriza por trabajar con un ciclo de vida iterativo e incremental, donde se va liberando el producto *software* de forma periódica a través de `sprints` (iteraciones).

Cliente de control de versiones
-------------------------------
-  Herramientas consideradas: `Gitg <https://wiki.gnome.org/Apps/Gitg/>`__,
   `Smartgit <https://www.syntevo.com/smartgit/>`__,
   `Gitkraken <https://www.gitkraken.com/>`__ y
   `GitHub Desktop <https://desktop.github.com/>`__.

-  Herramienta elegida: `Gitkraken <https://www.gitkraken.com/>`__.

GitKraken es un cliente para el control de versiones `Git` que nos permite realizar todas y cada una de las tareas propias de `Git` a través de una intuitiva y elegante interfaz gráfica. Además, incorpora funciones adicionales como `GitFlow`, que nos permite gestionar las diferentes ramificaciones del proyecto. De entre todas las opciones posibles es, en mi opinión, la más competente tanto en diseño como en funcionalidad.


*Hosting* del repositorio
-------------------------
-  Herramientas consideradas: `GitHub <https://github.com/>`__ y
   `GitLab <https://gitlab.com/>`__.

-  Herramienta elegida: `GitHub <https://github.com/>`__.

Github es el servicio de `hosting` de Git más utilizado para albergar repositorios de código en la nube. El hecho de que cuente con una enorme comunidad de usuarios y que además ofrezca servicios exclusivos y gratuitos a estudiantes, lo convierte en la mejor opción posible. Alguno de estos servicios son: repositorios privados, cantidad ilimitada de colaboradores por repositorio y código propietario entre otros.

Gestor de contenidos
--------------------
-  Gestores de contenidos considerados: `DSpace <https://duraspace.org/dspace/>`__,
   `Archimede <https://www.bibl.ulaval.ca/archimede/index.en.html>`__,
   `MyCoRe <https://www.mycore.de/>`__,
   `Omeka Classic <https://omeka.org/classic/>`__,
   `Geonetwork <https://github.com/geonetwork/core-geonetwork/>`__ y
   `DCCD <https://github.com/DANS-KNAW/dccd-webui>`__.

-  Gestor elegido: `Omeka Classic <https://omeka.org/classic/>`__.

*Omeka Classic* es una plataforma de gestión de contenido libre, flexible y de código abierto. Su misión principal es la publicación de colecciones digitales provenientes de bibliotecas, museos o cualquier tipo de institución que pretenda difundir su patrimonio cultural, como es el caso del CENIEH. Los motivos principales por los que se ha decidido escoger esta aplicación son:

1. Se distribuye bajo una Licencia Pública General (GNU), con lo cual su distribución, uso y modificación es libre.
2. Utiliza un entorno PHP-MySQL (Fácil despliegue sobre el servidor)
3. Basado en estándares internacionalmente aceptados como *Dublin Core* o *W3C*.
4. Flexible, escalable y extensible.

    - *Zend framework* como arquitectura.
    - APIs documentadas.
    - Le respalda una gran comunidad de usuarios y desarrolladores.

5. Asistencia técnica gratuita gracias a la existencia de foros donde desarrolladores del proyecto oficial aportan soluciones.
6. Pensado para ser utilizado por usuarios no necesariamente expertos en el manejo de las TIC.

Servidor HTTP Apache
--------------------
El servidor `HTTP Apache <https://httpd.apache.org/>`__ es un servidor web HTTP gratuito, de código abierto y multi-plataforma (Unix, Linux, Windows y otras) que implementa el protocolo HTTP/1.1 y HTTP2. Alguna de sus características más importantes son: su configuración e instalación es bastante sencilla, puede ser extendido o adaptado a través de módulos, incorpora funciones para la autentificación y validación de usuarios, y da soporte a varios lenguajes de programación como PHP y Python.

MySQL
-----
`MySQL <https://www.mysql.com/>`__ es uno de los sistemas de gestión de base de datos más utilizados en la actualidad. Gestiona bases de datos relacionales, es decir, aquellas que siguen el modelo relacional. Es gratuito, multi-plataforma (disponible en Linux, Windows y Unix) y es utilizado con numerosos lenguajes de programación, siendo el más utilizado con diferencia PHP.

Librerías
---------

*Zend Framework*
~~~~~~~~~~~~~~~~
`Zend Framework <https://framework.zend.com/>`__ (ZF) es un *framework* de PHP desarrollado por la compañia *Zend Technologies*, principal responsable del mantenimiento del lenguaje de programación PHP. A través de ZF se pueden desarrollar aplicaciones web de una forma mucho más rápida y sencilla que utilizando PHP puro. Como características más destacables, aporta componentes que se pueden reutilizar en el código base de la aplicación, emplea el patrón de diseño MVP, con las ventajas que eso conlleva, y permite escalar la aplicación web con el concepto de módulos (*plugins*).

*PHPUnit*
~~~~~~~~~
`PHPUnit <https://phpunit.de/>`__ es un *framework* utilizado para desarrollar pruebas unitarias sobre aplicaciones basadas en PHP. Es un proyecto *open-source* creado por Sebastian Bergmann cuyo repositorio oficial se encuentra en `Github <https://github.com/sebastianbergmann/phpunit>`__.

*ZipStream*
~~~~~~~~~~~
`ZipStream <https://github.com/maennchen/ZipStream-PHP/tree/0.2.2>`__ es una librería de PHP que permite transmitir archivos zip de forma dinámica, sin necesidad de ocupar espacio en el servidor.

*Leaflet*
~~~~~~~~~
`Leaflet <https://github.com/Leaflet/Leaflet>`__ es una librería *open-source* de *JavaScript* utilizada para crear mapas interactivos sobre aplicaciones web. Sorprende lo ligera que es (39kB) para la cantidad de servicios que ofrece. Además de las funciones presentes en su versión original, se pueden añadir nuevas funciones gracias a los *plugins* desarrollados por la comunidad.

*Leaflet draw*
^^^^^^^^^^^^^^
`Leaflet draw <https://github.com/Leaflet/Leaflet.draw>`__ es una extensión de la librería *Leaflet* que provee herramientas para dibujar y editar figuras sobre los mapas creados por esta librería. Permite delimitar regiones con múltiples formas (rectangular, circular, etc.), bastante útil cuando el marcador por defecto no es suficiente para señalar una determinada ubicación.

*jQuery*
~~~~~~~~
`jQuery <https://jquery.com/>`__ es una de las librerías más conocidas de *Javascript*. Aporta multitud de funciones que facilitan el desarrollo de aplicaciones web enriquecidas del lado del cliente. Estas permiten llevar a cabo servicios tales como la manipulación de documentos HTML, el manejo de eventos, animaciones, ajax, etc.

*Notify*
^^^^^^^^
`Notify <https://notifyjs.jpillora.com/>`__ es una extensión de la librería *jQuery* que proporciona notificaciones simples altamente personalizables. Es un buen medio para mantener informado al usuario ante determinadas acciones o eventos.

*Sweet alert 2*
^^^^^^^^^^^^^^^
`Sweet alert 2 <https://github.com/sweetalert2/sweetalert2>`__ es una extensión de la librería *jQuery* que permite crear *popups* (ventanas emergentes) de alerta personalizados. Es una buena alternativa a los *popups* de alerta que Javascript ofrece por defecto ya que se pueden añadir nuevas funciones que mejores la calidad de información mostrada al cliente.

Patrón de diseño
----------------

MVP (Modelo-Vista-Controlador)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
MVP es un patrón de diseño muy utilizado en el desarrollo de aplicaciones web. Permite organizar el código en tres secciones diferentes: Modelo, Vista y Controlador. La primera se encarga del manejo de los datos y la lógica de negocio, la segunda está relacionada con el diseño y la presentación, y la tercera interactúa con las dos primeras.

.. figure:: ../_static/images/mvc.png
   :name: mvc
   :alt: Modelo-Vista-Controlador.
   :scale: 70%
   :align: center

   Diagrama que muestra la relación entre Modelo, Vista y Controlador

-  **Modelo**: modifica, gestiona y actualiza los datos de la aplicación. En el caso de contar con una única base de datos, sería la capa donde se encuentra el código relacionado con las consultas, búsquedas, filtros y actualizaciones.
-  **Vista**: muestra al usuario final la interfaz gráfica de la aplicación, es decir, las páginas, ventanas, formularios, etc. En términos de programación se correspondería con el *frontend*.
-  **Controlador**: gestiona, atiende y procesa las peticiones realizadas por parte de los usuarios. A través de esta capa se comunican el modelo y la vista. Como vemos en la :numref:`mvc`, el controlador solicita los datos necesarios al modelo, se manipulan acorde a la petición del usuario y se entregan a la vista de forma que el usuario pueda visualizar los resultados esperados.


Entorno de desarrollo integrado (IDE)
-------------------------------------

*PHP* | *CSS* | *JavaScript* | *XML*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
-  Herramientas consideradas: `NetBeans <https://netbeans.org/>`__,
   `Atom <https://atom.io/>`__,
   `Eclipse <https://eclipse.org/>`__,
   `Zend Studio <https://www.zend.com/products/zend-studio>`__ y
   `Komodo <https://www.activestate.com/products/komodo-ide/>`__.

-  Herramienta elegida: `NetBeans <https://netbeans.org/>`__.

NetBeans es un entorno de desarrollo muy completo escrito en Java. Contiene una gran cantidad de funcionalidades y da soporte a todos y cada uno de los lenguajes de programación utilizados en el desarrollo de la infraestructura *software*. Además, se pueden instalar complementos que permiten extender su compatibilidad con otros marcos de trabajo como *Zend Framework*.

LaTeX
~~~~~
-  Herramientas consideradas: `TeXstudio <https://www.texstudio.org/>`__ y
   `Texmaker <http://www.xm1math.net/texmaker/>`__.

-  Herramienta elegida: `Texmaker <http://www.xm1math.net/texmaker/>`__.

*Texmaker* es un editor libre y gratuito para LaTeX distribuido bajo la licencia GPL. Incluye múltiples herramientas necesarias para elaborar documentos tanto con LaTeX como BibText o Metapost. También incorpora funciones adicionales como la corrección ortográfica, el auto-completado y plegado de código o un visor de pdf compatible con SyncTeX y con modo de visualización continua. Además, es multi-plataforma, disponible tanto en UNIX como en MacOS y Windows.

Generador de documentación
--------------------------
-  Herramientas consideradas: `Sphinx <https://www.sphinx-doc.org/es/master/index.html>`__ y
   `Mkdocs <https://www.mkdocs.org/>`__.

-  Herramienta elegida: `Sphinx <https://www.sphinx-doc.org/es/master/index.html>`__.

He decidido utilizar el generador de documentación *Sphinx* ya que es mucho más completo que *MkDocs*. Además de soportar el lenguaje de marcado ligero *Markdown* es compatible con *reStructuredText*. Esta compatibilidad hace que sea posible usar ambos lenguajes en un mismo proyecto *Sphinx*. Además, con el uso del conversor `Pandoc <http://pandoc.org/>`__, toda la documentación generada a partir de ambos lenguajes se puede exportar a multitud de formatos, entre los que se encuentra LaTeX.

*Markdown* es un lenguaje muy conocido debido a que es utilizado en plataformas como *Github* o *StackOverflow*. Fue creado para generar contenido de una manera sencilla de escribir y fácil de leer. Permite además convertir el texto marcado en documentos *XHTML*.

*reStructuredText* presenta también una sintaxis sencilla y de fácil lectura. La principal ventaja respecto a *Markdown* es que permite elaborar expresiones más complejas sin el uso de librerías/aplicaciones externas.

*LaTeX* es el estándar de facto para la publicación de documentos científicos. Permite la creación de documentos con una alta calidad tipográfica. Utiliza *Tex* como motor a la hora de darle formato a los documentos.

Herramientas de integración continua
------------------------------------

Compilación y Despliegue
~~~~~~~~~~~~~~~~~~~~~~~~
-  Herramientas consideradas: `Github Actions <https://github.com/features/actions>`__,
   `Travis CI <https://travis-ci.org/>`__ y
   `Jenkins <https://jenkins.io/>`__.

-  Herramienta elegida: `Github Actions <https://github.com/features/actions>`__.

Para aplicar la integración continua al proyecto se ha dedicido utilizar *Github Actions*. El principal motivo de esta elección es que todas sus funciones se encuentran integradas en la propia interfaz de *Github*, lo que facilita en gran medida su uso. Además, permite reutilizar código elaborado por otros usuarios de la comunidad en los flujos de trabajo (*workflows*) personales.

Calidad del código
~~~~~~~~~~~~~~~~~~
-  Herramientas consideradas: `Codacy <https://codacy.com>`__,
   `Codecov <https://codecov.io/>`__ y
   `CodeClimate <https://codeclimate.com/>`__.

-  Herramienta elegida: `Codacy <https://codacy.com>`__.

La opción escogida ha sido *Codacy* ya que, de entre las tres propuestas, es la que está más enfocada a la revisión de código automatizada, que es lo se estaba buscando. Da soporte a todos los lenguajes que se han utilizado en el proyecto ( *PHP*, *HTML*, *JavaScript* y *CSS* ). Además, el proceso de configuración no se hace nada pesado gracias a que se puede llevar a cabo desde su propia interfaz gráfica. Entre sus configuraciones más utilizadas están la exclusión de ficheros, la activación/desactivación de patrones de código, la selección de ramas y la gestión de integraciones.

Documentación continua
~~~~~~~~~~~~~~~~~~~~~~
`Read the Docs <https://readthedocs.org/>`__ es una plataforma web que facilita la tarea de documentar productos *software* automatizando la compilación, versionado y hospedaje de los ficheros generados por la herramienta de documentación *Sphinx*. El proceso es muy sencillo, basta con alojar la documentación *Sphinx* en un repositorio, realizar un *commit* sobre este y, automáticamente, se actualizan los cambios en la documentación alojada en *readthedocs.org*. Presenta múltiples formatos de exportación y permite configurar múltiples aspectos (traducciones, variables de entorno, reglas de automatización, etc.). Todos estos servicios se ofrecen de forma gratuita.

Herramienta de diagramación
---------------------------
-  Herramientas consideradas: `Draw - LIbreOffice <https://es.libreoffice.org/descubre/draw/>`__,
   `SmartDraw <https://www.smartdraw.com/>`__ y
   `Draw.io <https://app.diagrams.net/>`__.

-  Herramienta elegida: `Draw.io <https://app.diagrams.net/>`__.

*Draw.io* es una herramienta gratuita de diseño que permite crear y compartir diagramas *on-line*, es decir, sin necesidad de instalar programa alguno. Presenta una interfaz elegante y fácil de utilizar desde la cual podemos hacer uso de sus múltiples funciones como, por ejemplo, importar imágenes, añadir objetos UML, exportar e importar proyectos en diversos formatos, etc.

Herramientas de comunicación
----------------------------

*Microsoft Teams*
~~~~~~~~~~~~~~~~~
A través de `Microsoft Teams <https://www.microsoft.com/es-es/education/products/teams>`__ se han llevado a cado las reuniones de cada *sprint*. *Teams* viene integrado en el paquete de *Microsoft Office 365*, por lo que es un servicio que puede ser adquirido por el personal de la UBU. Ofrece una gran cantidad de funcionalidades relacionadas con la comunicación como, por ejemplo, la creación de chats personalizados (individuales/grupales, públicos/privados, etc.), compartición de pantalla, integración de aplicaciones externas (Stream, Excel, etc.) o introducción de efectos de cámara (efectos de fondo, filtros, etc.).

*Zoom*
~~~~~~
`Zoom <https://zoom.us/>` es la herramienta de comunicación con la que se han llevado a cabo las reuniones tanto con el CENIEH como con ARIADNEplus. Al igual que la herramienta *Microsoft Teams*, permite realizar videollamadas y reuniones virtuales con multitud de funcionalidades extra.

Otras herramientas
------------------

*Docker*
~~~~~~~~
La tecnología `Docker <https://www.docker.com/>`__ permite desplegar una aplicación distribuida y empaquetarla junto a todas sus dependencias y librerías en un uno o varios "objetos" denominados contenedores o *containers*. Estos pueden ser ejecutados en cualquier servidor Linux, aumentando así la flexibilidad y portabilidad de nuestra aplicación.

*Google Cloud*
~~~~~~~~~~~~~~
`Google Cloud <https://cloud.google.com/>`__ es una plataforma creada por la compañía *Google* desde la que puedes acceder a numerosos servicios relacionados con el desarrollo web. Alguno de sus servicios son: *Cloud Computing*, *Networking*, *Data Storage*, *Data Analytics*, *Machine learning*, etc.

*GKE – Google Kubernetes Engine*
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
`Google Kubernetes Engine <https://cloud.google.com/kubernetes-engine>`__ (GKE) proporciona un entorno desde donde puedes implementar, administrar y escalar aplicaciones en contenedores mediante la infraestructura de *Google*. El entorno de GKE consta de varias máquinas (en particular, instancias de *Compute Engine*) que se agrupan para formar un clúster.

*Kubernetes*
~~~~~~~~~~~~
`Kubernetes <https://kubernetes.io/es/>`__ es una plataforma *open-source* que permite automatizar los procesos relacionados con la implementación, administración y escalabilidad de contenedores.  He decidido utilizar este orquestador (*orchestrator*) para desplegar mi aplicación en la nube (*Google Cloud*) por la gran cantidad de ventajas que ofrece como, por ejemplo, autoreparación de contenedores, utilización de *secrets* o despliegues y rollbacks automáticos.

*Kustomize*
~~~~~~~~~~~
`Kustomize <https://github.com/kubernetes-sigs/kustomize>`__ es una herramienta que permite operar sobre objetos de la plataforma *Kubernetes* a través de un archivo de personalización.

