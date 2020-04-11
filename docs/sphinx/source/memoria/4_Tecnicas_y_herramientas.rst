#######################
Tecnicas y herramientas
#######################

************
Metodologías
************

=====
Scrum
=====
Scrum es un marco de trabajo donde se ejecutan procesos ágiles que contribuyen al desarrollo y mantenimiento de productos `software`. Por ello, está catalogado como una metodología ágil, la cual se caracteriza por trabajar con un ciclo de vida iterativo e incremental, donde se va liberando el producto software de forma periódica a través de `sprints` (iteraciones).

====================
Integración continua
====================

La Integración Continua (CI) es una práctica utilizada en el desarrollo de software mediante la cual es posible automatizar operaciones tales como la compilación o ejecución de tests. Aplicando esta metodología en el proyecto conseguimos detectar fallos con mayor rapidez, mejorar la calidad del software y reducir el tiempo empleado en validar y publicar nuevas actualizaciones de software. 

*******************************
Cliente de control de versiones
*******************************

-  Herramientas consideradas: `Gitg <https://wiki.gnome.org/Apps/Gitg/>`__,
   `Smartgit <https://www.syntevo.com/smartgit/>`__,
   `Gitkraken <https://www.gitkraken.com/>`__ y
   `GitHub Desktop <https://desktop.github.com/>`__.

-  Herramienta elegida: `Gitkraken <https://www.gitkraken.com/>`__.

GitKraken es un cliente para el control de versiones `Git` que nos permite realizar todas y cada una de las tareas propias de `Git` a través de una intuitiva y elegante interfaz gráfica. Además, incorpora funciones adicionales como `GitFlow`, que nos permite gestionar las diferentes ramificaciones del proyecto. De entre todas las opciones posibles es, en mi opinión, la más competente tanto en diseño como en funcionalidad.

*************************
`Hosting` del repositorio
*************************

-  Herramientas consideradas: `GitHub <https://github.com/>`__ y
   `GitLab <https://gitlab.com/>`__.

-  Herramienta elegida: `GitHub <https://github.com/>`__.

Github es el servicio de `hosting` de Git más utilizado para albergar repositorios de código en la nube. El hecho de que cuente con una enorme comunidad de usuarios y que además ofrezca servicios exclusivos y gratuitos a estudiantes lo convierte en la mejor opción posible. Alguno de estos servicios son: privatización de repositorios, cantidad ilimitada de colaboradores por repositorio, código propiertario... 

**************************
Gestor de contenidos (CMS)
**************************

-  Gestores de contenidos considerados: `DSpace <https://duraspace.org/dspace/>`__,
   `Archimede <https://www.bibl.ulaval.ca/archimede/index.en.html>`__,
   `MyCoRe <https://www.mycore.de/>`__,
   `Omeka Classic <https://omeka.org/classic/>`__ y
   `DCCD <https://github.com/DANS-KNAW/dccd-webui>`__.

-  Gestor elegido: `Omeka Classic <https://omeka.org/classic/>`__.

Omeka Classic es una plataforma de gestión de contenidos libre, flexible y de código abierto. Su misión principal es la publicación de colecciones digitales provenientes de bibliotecas, museos o cualquier tipo de institución que pretenda difundir su patrimonio cultural, como es el caso del CENIEH. Los motivos principales por los que he decidido escoger este CMS son:

1. Se distribuye bajo una Licencia Pública General1 (GNU), con lo cual su distribución, uso y modificación es libre. Esto me permitirá amoldar la plataforma a los requisitos impuestos por la integración.
2. Utiliza un entorno PHP-MySQL (Fácil despliegue sobre el servidor)
3. Basado en estándares internacionalmente aceptados como Dublin Core o W3C. 
4. Flexible, escalable y extensible.

    - Zend framework como arquitectura.
    - APIs documentadas.
    - Le respalda una gran comunidad de desarrolladores.
    
5. Asistencia técnica gratuita gracias a la existencia de foros donde desarrolladores del proyecto oficial aportan soluciones.
6. Pensado para ser utilizado por usuarios no necesariamente expertos en el manejo de las TIC (personal del CENIEH).

*************************************
Entorno de desarrollo integrado (IDE)
*************************************

====================================
`PHP` | `CSS` | `JavaScript` | `XML`
====================================

-  Herramientas consideradas: `Atom <https://atom.io/>`__,
   `Eclipse <https://eclipse.org/>`__,
   `Zend Studio <https://www.zend.com/products/zend-studio>`__ y
   `Komodo <https://www.activestate.com/products/komodo-ide/>`__.

-  Herramienta elegida: `Zend Studio <https://www.zend.com/products/zend-studio>`__.

Zend Studio es un IDE para PHP que ha sido construido tomando como base Eclipse. Considero que es la opción ideal ya que da soporte a todos y cada uno de los lenguajes de programación utilizados por la infraestructura software escogida. Además, permite la instalación de `plugins` externos de Eclipse e incluye herramientas tales como `Docker` y `Gitflow`. 

=====
LaTeX
=====

-  Herramientas consideradas: `TeXstudio <https://www.texstudio.org/>`__ y
   `Texmaker <http://www.xm1math.net/texmaker/>`__.

-  Herramienta elegida: `Texmaker <http://www.xm1math.net/texmaker/>`__.

`Texmaker` es un editor libre y gratuito para LaTeX distribuido bajo la licencia GPL. Además, es multi-plataforma, es decir, trabaja tanto en UNIX como en MacOS y Windows. Incluye múltiples herramientas necesarias para elaborar documentos con LaTeX como BibText o Metapost. Incorpora funciones adicionales como la corrección ortográfica, el auto-completado y plegado de código o un visor de pdf compatible con SyncTeX y con modo de visualización continua. 

**************************
Generador de documentación
**************************

-  Herramientas consideradas: `Sphinx <https://www.sphinx-doc.org/es/master/index.html>`__ y
   `Mkdocs <https://www.mkdocs.org/>`__.

-  Herramienta elegida: `Sphinx <https://www.sphinx-doc.org/es/master/index.html>`__.

He decidido utilizar el generador de documentación Sphinx ya que es mucho más completo que MkDocs. Además de soportar el lenguaje de marcado ligero `Markdown` es compatible con `reStructuredText`. Esta compatibilidad hace que sea posible usar ambos lenguajes en un mismo proyecto Sphinx. Además, con el uso del conversor `Pandoc <http://pandoc.org/>`__, toda la documentación generada a partir de ambos lenguajes se puede exportar a multitud de formatos, entre los que se encuentra LaTeX.

`Markdown` es un lenguaje muy conocido debido a que es utilizado en plataformas como Github o StackOverflow. Fue creado para generar contenido de una manera sencilla de escribir y fácil de leer. Permite además convertir el texto marcado en documentos XHTML.

`reStructuredText` presenta también una sintaxis sencilla y de fácil lectura. La principal ventaja respecto a `Markdown` es que permite elaborar expresiones más complejas sin el uso de librerías/aplicaciones externas.

`LaTeX` es el estándar de facto para la publicación de documentos científicos. Permite la creación de documentos con una alta calidad tipográfica. Utiliza Tex como motor a la hora de darle formato a los documentos.

******
Docker
******

La tecnología `Docker <https://www.docker.com/>`__ permite desplegar una aplicación distribuida y empaquetarla junto a todas sus dependencias y librerías en un uno o varios "objetos" denominados contenedores o `containers`. Estos pueden ser ejecutados en cualquier servidor Linux, aumentando así la flexibilidad y portabilidad de nuestra aplicación. 

***********************************
Herramienta de integración continua
***********************************

-  Herramientas consideradas: `Github Actions <https://github.com/features/actions>`__,
   `Travis CI <https://travis-ci.org/>`__ y
   `Jenkins <https://jenkins.io/>`__.

-  Herramienta elegida: `Github Actions <https://github.com/features/actions>`__.

Para aplicar la integración continua al proyecto he dedicido utilizar `Github Actions`. El principal motivo es que todas sus funciones se encuentran integradas en la propia interfaz de Github, lo que facilita en gran medida su uso. Además, permite reutilizar código, elaborado por otros usuarios de la comunidad, para desarrollar nuestro propio entorno de trabajo (`workflow`).

***************************
Herramienta de diagramación
***************************

-  Herramientas consideradas: `Draw - LIbreOffice <https://es.libreoffice.org/descubre/draw/>`__,
   `SmartDraw <https://www.smartdraw.com/>`__ y
   `Draw.io <https://app.diagrams.net/>`__.

-  Herramienta elegida: `Draw.io <https://app.diagrams.net/>`__.

`Draw.io` es una herramienta gratuita de diseño que permite crear y compartir diagramas de forma `online`, es decir, sin necesidad de instalar programa alguno. Presenta una interfaz elegante y fácil de utilizar desde la cual podemos hacer uso de sus múltiples funciones como, por ejemplo, importar imágenes, añadir objetos UML, exportar e importar proyectos en diversos formatos, etc.

