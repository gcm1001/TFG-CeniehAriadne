==================
Conceptos teóricos
==================
A lo largo de este apartado se van a exponer los conceptos teóricos relacionados con cada una de las fases en las que se divide el proyecto, que son:

 1. Investigación.
 2. Desarrollo.
 3. Integración.

Conceptos teóricos relativos a la investigación
-----------------------------------------------

Ariadne
~~~~~~~

ARIADNE [#]_ es un programa fundado por la Comisión Europea en febrero de 2013. Nace con el propósito de estimular la investigación en áreas relacionadas con la arqueología mediante la integración de diversas infraestructuras de datos arqueológicas situadas en Europa. Fruto de este proyecto surge un catálogo on-line de metadatos de conjuntos de datos que incluyen reportes no publicados, imágenes, mapas, bases de datos, y otros tipos de información arqueológica.

ARIADNEPlus
~~~~~~~~~~~

ARIADNEplus [#]_ es la continuación del proyecto ARIADNE. Forma parte del programa H2020 fundado por la Comisión Europea. El proyecto se encuentra en desarrollo desde enero de 2019 y tiene una duración total de 48 meses. A través de ARIADNE plus se actualizarán y extenderán los datos del catálogo on-line añadiendo a los mismos dimensión geográfica y temporal. Además se van a incorporar más organizaciones arqueológicas Europeas (entre ellas el CENIEH). ARIADNE plus también proveerá servicios en la nube para procesar y re-utilizar los datos incluidos en su portal.

CENIEH
~~~~~~

Las siglas se corresponden con la denominación: Centro Nacional de Investigación sobre la Evolución Humana. El CENIEH es una Infraestructura Científica y Técnica Singular (ICTS) abierta al uso de la comunidad científica y tecnológica, en la que se desarrollan investigaciones en el ámbito de la evolución humana durante el Neógeno superior y Cuaternario, promoviendo la sensibilización y transferencia de conocimientos a la sociedad e impulsando y apoyando la realización y colaboración en excavaciones de yacimientos de estos periodos, tanto españoles como de otros países.

Además, el CENIEH es responsable de la conservación, restauración, gestión y registro de las colecciones paleontológicas y arqueológicas procedentes de las excavaciones de Atapuerca y otros yacimientos tanto nacionales como internacionales de similares características que lleguen a acuerdos con el Centro [#]_.

Metadatos
~~~~~~~~~

Los metadatos proporcionan la información mínima necesaria para identificar un recurso, pudiendo incluir información descriptiva sobre el contexto, calidad y condición o característica del dato [#]_. Puede resultar algo complejo de entender ya que podemos reducir su definición a "son datos que describen otros datos". 

Para aportar algo de claridad a esta definición aplicaré el concepto de "metadato" tomando como ejemplo una biblioteca. En este contexto, el conjunto de datos estaría formado por los libros y el conjunto de metadatos se correspondería a las fichas asociadas a cada libro. Este ejemplo de "metadato" es algo ambigüo ya que se presenta de una forma física, no digital. Actualmente, como veremos más adelante, estas "fichas" se encuentran en formato digital haciendo uso de lenguajes como XML o RDF.

.. image:: ../_static/images/ejemploMetadatos.png
   :alt: Ejemplo de metadatos.
   :scale: 80%
   :align: center


Esquema de metadatos
^^^^^^^^^^^^^^^^^^^^

Antes de introducir metadatos en cualquier catálogo, es necesario indicar como van a estar organizados. Para llevar a cabo esta tarea debemos definir un esquema, también llamado modelo o estándar.

Cada esquema está formado por un conjunto de campos de diferentes tipos, los cuales siguen una estructura jerárquica en forma de árbol. 

.. image:: ../_static/images/diagramacampos.png
   :alt: Modelo básico de un esquema de metadatos.
   :scale: 80%
   :align: center

En el diagrama superior podemos observar el **modelo básico** de cualquier esquema de metadatos:

    - **Ontología**: es la raíz del esquema. Su función es agrupar los demás campos en una única unidad temática. Puede tener tres tipos de descendientes: Clase, Referencia o Metadato.
    - **Definición de Clase**: define una clase o subclase dentro de una ontología determinada, creando así una jerarquía de clases.

        - **Atributo**: define un atributo para una determinada clase existente en la ontología.
    - **Conjunto de Referencia**: define un conjunto de valores que pueden ser instanciados en el Atributo de una Clase o en el Metadato de un recurso.

        - **Valor**: define el contenido de cada valor existente en un conjunto de referencia.
    - **Metadato de Recurso**: define el metadato de un recurso determinado. Además, puede ser descendiente de otro metadato a modo de especificación.

Cuando se define un atributo o un metadato se debe indicar, además, el tipo de contenido que va a adquirir, es decir, señalar que vamos a introducir texto plano, coordenadas, una fecha, un enlace, etc.

CIDOC-CRM
^^^^^^^^^   
**CIDOC** **C**\ onceptual **R**\ eference **M**\ odel (CRM) [#]_ es una ontología que ofrece definiciones y una estructura formal para describir conceptos implícitos y explícitos, así como las relaciones utilizadas en documentación sobre patrimonio cultural. CIDOC define un marco semántico en el cual se puede incluir cualquier tipo de información sobre patrimonio cultural.

ACDM
^^^^
El **A**\ RIADNE **C**\ atalogue **D**\ ata **M**\ odel es el modelo de datos utilizado por el catálogo de ARIADNE. Sirve para describir los recursos arqueológicos publicados por los participantes del proyecto. El uso de ACDM posibilita el descubrimiento, acceso e integración de los citados recursos. Para formalizar este modelo, se ha utilizado como base la ontología CIDOC CRM, la cual se adapta correctamente al dominio arqueológico.

PEM
^^^
El modelo *PEM* (\ **P**\ ARTHENOS **E**\ ntities **M**\ odel) es un modelo de datos desarrollado en el proyecto PARTHENOS [#]_ que extiende el modelo CIDOC-CRM. Está diseñado para ser lo suficientemente flexible como para mapear los diferentes tipos de esquemas de metadatos utilizados en todas las disciplinas académicas de manera uniforme.

AO-Cat
^^^^^^
La ontología *AO-Cat* (\ **A**\ RIADNE **O**\ ntology - **C**\ atalog) deriva del modelo de datos ACDM, empleado en el proyecto antiguo ARIADNE para modelar recursos arqueológicos, y del modelo PEM, utilizado para modelar
recursos gestionados por una determinada infraestructura de investigación. Se podría decir que AO-Cat es una contracción del modelo ACDM impulsada por la conceptualización subyacente al PEM. Además, AO-Cat hereda del modelo PEM su estrecha relación con el modelo CIDOC-CRM, el cual sirve para representar cualquier aspecto relacionado con recursos arqueológicos.

.. image:: ../_static/images/diagramaDeClasesAOCAT.png
   :alt: Diagrama de clases para la ontología AO-CAT.
   :scale: 40%
   :align: center


DLO - Document Like Object
^^^^^^^^^^^^^^^^^^^^^^^^^^

La expresión DLO (Document Like Object) apareció durante el desarrollo del esquema de metadatos Dublin Core. Esta alude a todos aquellos documentos existentes en la red (ficheros de texto, imagen, video, audio, etc.) y es utilizada para referirse a una unidad documental o al documento digital mínimo, que forma parte de una colección digital, al cual se le atribuyen metadatos para su descripción y recuperación [#]_.

Colección digital
^^^^^^^^^^^^^^^^^

Una colección digital está compuesta por un conjunto de objetos de información (DLO) digitales que han sido seleccionados y organizados para ser accedidos de forma remota a través de la Web.

Interoperabilidad
^^^^^^^^^^^^^^^^^

La interoperabilidad es la capacidad que tiene un sistema o producto de compartir datos y posibilitar el intercambio de información y conocimiento entre ellos [#]_.


Conceptos teóricos relativos al desarrollo de la infraestructura
----------------------------------------------------------------

Omeka
-----
Omeka es la plataforma software sobre la que he trabajado para desarrollar la infraestructura software propuesta. Es un proyecto del Roy Rosenzweig Center for History and New Media [#]_, creadores del también conocido gestor Zotero [#]_. Es una plataforma gratuita, flexible y de código abierto especializada en la gestión y publicación de colecciones digitales de bibliotecas, museos, archivos, etc. 

LAMP
----
Las siglas LAMP son utilizadas para describir infraestructuras software que hacen uso de cuatro herramientas específicas:

- **L**\ inux como sistema operativo.
- **A**\ pache como servidor web.
- **M**\ ysql o **M**\ ariaDB como gestor de base de datos.
- **P**\ HP como lenguaje de programación.

La infraestructura software desarrollada se corresponde con esta definición.

Dublin Core
-----------
*Dublin Core* es un esquema de metadatos elaborado por la *DCMI* [#]_ (*Dublin Core Metadata Initiative*), organización cuya misión principal es facilitar la compartición de recursos on-line por medio del desarrollo de un modelo de metadatos "base", capaz de proporcionar información descriptiva básica sobre cualquier recurso, sin importar el formato de origen, área de especialización u origen cultural. Dispone de 15 elementos descriptivos, los cuales pueden ser repetidos, aparecer en cualquier orden y no tienen por qué existir (son opcionales).

Dublin Core Extended
--------------------
Dado que el modelo *Dublin Core* puede resultar algo escueto, se presenta el esquema *Dublin Core Extended*, el cual cuenta con los elementos descriptivos básicos y, además, añade una serie de elementos adicionales/complementarios [#]_ que satisfacen las necesidades que el modelo básico no cubre.
He optado por utilizar este esquema en la infraestructura software que he desarrollado ya que se adapta perfectamente a las necesidades del proyecto.

Geolocalización
---------------
La geolocalización es la capacidad para obtener la ubicación geográfica real de un objeto [#]_. Uno de los requisitos fundamentales de ARIADNEplus es que todos los datos introducidos en su plataforma han de estar geolocalizados, es decir, tienen que tener, al menos, un elemento descriptivo que indique la ubicación actual del objeto. 
Nuestra plataforma cuenta con el elemento "Spatial Coverage" del modelo "Dublin Core Extended" para cubrir este requisito.

WGS84
-----
El **W**\ orld **G**\ eodetic **S**\ ystem 84 (WSG84) es un sistema de coordenadas geográficas usado mundialmente para localizar cualquier punto de la Tierra [#]_.
En ARIADNEplus, todas aquellas ubicaciones señaladas a través de coordenadas geográficas deben utilizar este sistema.

Protocolo OAI-PMH
-----------------
El protocolo *Open Archive Initiative-Protocol for Metadata Harvesting* (OAI-PMH) tiene como objetivo desarrollar y promover estándares de interoperabilidad que faciliten la difusión eficiente de contenidos en Internet. Permite transmitir metadatos entre diferentes tipos de infraestructuras software (repositorios, gestores, etc.) siempre y cuando éstos se codifiquen en Dublin Core.
Una de las opciones de importación que ARIADNEplus aconseja es mediante la utilización de este protocolo. 

Estados de integración
----------------------


Conceptos teóricos relativos a la integración
=============================================

Integración continua
--------------------

Despliegue continuo
-------------------


.. References

.. [#] "Ariadne Project EU | Foundation." https://www.ariadne-eu.org/

.. [#] “ARIADNE PLUS – Ariadne infrastructure.” https://ariadne-infrastructure.eu/.

.. [#] “Sobre el CENIEH | CENIEH.” https://www.cenieh.es/sobre-el-cenieh.

.. [#] “CIDOC CRM.” http://www.cidoc-crm.org/.

.. [#] "PARTHENOS Project." https://www.parthenos-project.eu/

.. [#] Senso, José Antonio; Rosa Piñero, Alberto de la (2003). "El concepto de metadato. Algo más que descripción de recursos electrónicos". http://www.scielo.br/pdf/ci/v32n2/17038.pdf/

.. [#] "DLO." https://es.wikipedia.org/wiki/DLO

.. [#] "Interoperabilidad." https://administracionelectronica.gob.es/pae_Home/pae_Estrategias/pae_Interoperabilidad_Inicio.html

.. [#] "Roy Rosenzweig Center for History and New Media." https://rrchnm.org/

.. [#] "Zotero." https://www.zotero.org/

.. [#] "DCMI." https://www.dublincore.org/

.. [#] "DCMI Metadata Terms." http://dublincore.org/documents/dcmi-terms/

.. [#] "Wikipedia - Geolocalización." https://es.wikipedia.org/wiki/Geolocalizaci%C3%B3n

.. [#] "Wikipedia - WSG84" https://es.wikipedia.org/wiki/WGS84
