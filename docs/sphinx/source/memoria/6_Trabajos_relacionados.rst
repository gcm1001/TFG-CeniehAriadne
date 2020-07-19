=====================
Trabajos relacionados
=====================

Algunos socios del proyecto ARIADNEplus han adoptado una solución muy similar a la propuesta en el presente proyecto, es decir, han hecho uso de aplicaciones *software* de terceros para la gestión de sus (meta)datos y las han adaptado según sus necesidades. A continuación, se muestran aquellos casos que guardan una mayor relación con el proyecto.

Casos similares
---------------

Fasti Online
~~~~~~~~~~~~
Fasti Online [#]_ es un proyecto liderado por la Asociación Internacional de Arqueología Clásica (AIAC) [#]_ y el *Center for the Study of Ancient Italy* (CSAI) [#]_  de la Universidad de Texas, Austin [#]_. Su principal objetivo es proporcionar una infraestructura *software* que permita almacenar, gestionar y publicar registros relacionados con la arqueología.

Para tal fin, han utilizado como base la aplicación *software* denominada `ARK <https://ark.lparchaeology.com/>`__. Esta es una aplicación web que provee servicios como la gestión, compartición y transformación (mapeo) de (meta)datos. Además, la aplicación es de código abierto, lo que significa que es personalizable y extensible.

La incorporación de *Fasti Online* al proyecto *ARIADNE* y, posteriormente, al proyecto *ARIADNEplus*, ha impulsado la implementación de nuevas funcionalidades sobre la aplicación *ARK* como, por ejemplo, la integración de datos espaciales, nuevos mecanismos de búsqueda y otros servicios web como, por ejemplo, el protocolo *OAI-PMH*.

CONICET
~~~~~~~
El Consejo Nacional de Investigaciones Científicas y Técnicas (CONICET) [#]_ es el principal organismo dedicado a la promoción de la ciencia y la tecnología en Argentina. Este, al igual que el CENIEH, es una de las nuevas incorporaciones al proyecto *ARIADNEplus* y, como tal, han tenido que adaptarse para satisfacer los requisitos del proyecto.

La solución planteada por este organismo es muy similar a la del presente proyecto. Están desarrollado una infraestructura *software* que permita a los operarios del CONICET gestionar y publicar sus conjuntos de datos adoptando un esquema de metadatos compatible con *ARIADNEPlus*. La aplicación *software* que han decidido adaptar ha sido `Dspace 5.5 <https://duraspace.org/dspace/>`__ . Se puede acceder a su infraestructura desde el siguiente `enlace <https://suquia.ffyh.unc.edu.ar/>`__ .

DANS
~~~~
DANS (*Data Archiving and Networked Services*) [#]_ es una institución de los Paises Bajos cuya misión principal es proporcionar las herramientas necesarias a investigadores para hacer que sus datos sean accesibles, interoperables y reutilizables.

La organización DANS es responsable del desarrollo y mantenimiento del repositorio digital `DCCD <https://dendro.dans.knaw.nl/>`__, el cual es considerado como la principal red de (meta)datos arqueológicos/históricos existente en Europa. Entró en funcionamiento en 2011. Dentro del *DCCD*, laboratorios belgas, daneses, holandeses, alemanes, letones, polacos y españoles publican contenido fruto de la investigación de, entre otros: sitios arqueológicos (incluidos paisajes antiguos), construcciones, pinturas, esculturas e instrumentos musicales.

Esta organización participó en el proyecto *ARIADNE* y, actualmente, forma parte del proyecto *ARIADNEplus*. Con el objetivo de mejorar la integración europea de datos dendrocronológicos ofrecen, de forma gratuita, la misma solución *software* empleada en su proyecto *DCCD*, la cual es compatible con el proyecto *ARIADNE*. Está disponible en `GitHub <https://github.com/DANS-KNAW/dccd-webui>`__ .


Comparativa entre soluciones *software*
---------------------------------------

.. table:: Comparativa de las principales características de las aplicaciones *software* escogidas por cada socio.
   :name: compsolsoft
   :widths: auto

   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Caraterísticas                                          | Omeka Classic (CENIEH) | ARK (Fasti Online) | DSpace (CONICET) | DCCD (DANS) |
   +=========================================================+========================+====================+==================+=============+
   | Tipo de aplicación                                      | Web                    | Web                | Web              | Web         |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Lenguaje de programación principal                      | PHP                    | PHP                | Java             | Java        |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Gestión de metadatos                                    | ✔                      | ✔                  | ✔                | ✔           |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Importación masiva de metadatos                         | ✔                      | ✔                  | ✔                | ✔           |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Exportación masiva de metadatos                         | ✔                      | ✔                  | ✔                | ✔           |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Edición masiva de metadatos                             | ✔                      | ✘                  | ✘                | ✘           |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Cobertura espacial                                      | ✔                      | ✔                  | ✔                | ✔           |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Cobertura temporal                                      | ✘                      | ✔                  | ✘                | ✔           |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Protocolo OAI-PMH                                       | ✔                      | ✔                  | ✔                | ✘           |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Herramientas de apoyo en la integración con ARIADNEplus | ✔                      | ✘                  | ✘                | ✘           |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Herramientas para la transformación de metadatos        | ✔                      | ✔                  | ✘                | ✘           |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Sistema de usuarios                                     | ✔                      | ✔                  | ✔                | ✔           |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Almacenamiento de ficheros                              | ✔                      | ✔                  | ✔                | ✘           |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Asistencia técnica gratuita                             | ✔                      | ✘                  | ✘                | ✘           |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Interfaz pública                                        | ✔                      | ✔                  | ✔                | ✔           |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Interfaz intuitiva                                      | ✔                      | ✘                  | ✔                | ✘           |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Sistema de *plugins*                                    | ✔                      | ✘                  | ✔ (*)            | ✘           |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Sistema de plantillas                                   | ✔                      | ✘                  | ✘                | ✘           |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Comunidad de usuarios activa                            | ✔                      | ✘                  | ✔                | ✘           |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Manuales de documentación detallados                    | ✔                      | ✘                  | ✘                | ✘           |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+
   | Última actualización                                    | 2020                   | 2018               | 2020             | 2015        |
   +---------------------------------------------------------+------------------------+--------------------+------------------+-------------+

*(\*) Servicio de pago.*

Basándonos en el contenido de la :numref:`compsolsoft`, se listarán los puntos fuertes y débiles que presenta la aplicación del proyecto frente a las propuestas de los otros socios.

Puntos fuertes
~~~~~~~~~~~~~~

* Gran parte de la configuración de la aplicación puede realizarse desde la interfaz gráfica, sin necesidad de modificar ficheros internos que requieran un mínimo de conocimiento de la estructura interna de la aplicación, como pasa en aplicaciones como *ARK* o *DCCD*. Esto facilita en gran medida las labores de configuración de la aplicación.
* Al requerir una infraestructura *LAMP* para su despliegue, la instalación de la aplicación es relativamente sencilla en comparación con las otras aplicaciones. Además, gracias al presente proyecto, es posible instalar la aplicación a través de tecnologías como *Docker* o *Kubernetes*, facilitando aún más su despliegue.
* De entre todas las soluciones mostradas es, sin duda, la más sencilla y segura de adaptar y personalizar. Esto es gracias al sistema de complementos (*plugins*) y plantillas (*themes*) que incorpora.
* Gracias a las labores de desarrollo llevadas a cabo en el presente proyecto, dispone de herramientas de apoyo para la integración de conjuntos de datos en ARIADNEplus.
* La comunidad de usuarios con la que cuenta *Omeka Classic* es superior a la de sus competidores. Muchos usuarios comparten sus propios desarrollos, tanto complementos como plantillas, de forma que estos pueden ser reutilizados o incluso mejorados por otros usuarios. Además, existe un foro desde donde los expertos de *Omeka*, incluídos los líderes del proyecto, brindan soporte técnico gratuito a otros usuarios de la aplicación.
* La documentación disponible es, tanto para usuarios como para desarrolladores, la más clara y detallada de todas las aplicaciones mostradas.
* Actualmente el proyecto *Omeka* continúa en desarrollo, es decir, siguen saliendo nuevas actualizaciones con mejoras y funcionalidades nuevas para la aplicación. Sin embargo, otros proyectos como *ARK* o *DCCD* están obsoletos.

Puntos débiles
~~~~~~~~~~~~~~

* Actualmente, no dispone de ningún mecanismo que identifique aquellos (meta)datos cuyo contenido sea un periodo temporal (e.g. "1190 BCE") y los procese de tal forma que estos sean mostrados dentro de una línea temporal y a su vez puedan ser un criterio aislado de búsqueda.
* No posee las ventajas que proporciona el lenguaje de programación *Java* utilizado tanto en *DSpace* como en *DCCD*. Este es más rápido y presenta un mejor rendimiento al ser un lenguaje compilado. Además, posee una estructura más ordenada y es mucho más seguro que PHP.


.. References

.. [#] "Fasti Online." http://www.fastionline.org/

.. [#] "AIAC – Associazione Internazionale di Archeologia Classica." http://www.aiac.org/

.. [#] "CSAI – Center for the Study of Ancient Italy." http://csaitx.org/

.. [#] "University of Texas at Austin." https://www.utexas.edu/

.. [#] "CONICET – Consejo Nacional de Investigaciones Científicas y Técnicas." https://www.conicet.gov.ar/

.. [#] "DANS – Data Archiving and Networked Services." https://dans.knaw.nl/en
