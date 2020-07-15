========================================
Conclusiones y Líneas de trabajo futuras
========================================

En este último apartado, se exponen las conclusiones extraídas tras un breve análisis objetivo del trabajo realizado. Además, se proponen nuevas perspectivas para las posibles líneas de trabajo futuras.

Conclusiones
------------
A continuación se listan las conclusiones más relevantes que se han obtenido tras finalizar el proyecto.

- En cuanto a los objetivos generales del proyecto, considero que se han cumplido ambos. Los operarios del *CENIEH* cuentan con una aplicación que les facilita el proceso de integración de sus datos en *ARIADNEplus* y, además, se ha conseguido integrar una de las colecciones propuestas haciendo uso de esta aplicación.
- Durante la fase de investigación del proyecto, se han aprendido multitud de técnicas y conocimientos nuevos relacionados con la creación, implementación y gestión de metadatos.
- El ser parte de un proyecto internacional como *ARIADNEplus* me ha permitido conocer nuevos métodos de trabajo como, por ejemplo, la utilización de entornos de investigación virtuales (*VREs*). Gracias a estos he podido comunicarme con los demás socios del proyecto, utilizar servicios y herramientas comunes, y compartir recursos digitales de todo tipo.
- En la parte de desarrollo del proyecto se han aplicado la mayoría de los conocimientos adquiridos durante el grado. Asímismo, se han utilizado otras materias que han requerido un estudio especial como *PHP*, *Zend Framework*, *Hooking*, etc.
- El desarrollo de *plugins* o complementos para la adaptación de la aplicación propuesta ha supuesto una experiencia totalmente nueva que me ha permitido conocer cómo funcionan este tipo de aplicaciones.
- En el proyecto se han aplicado técnicas de integración continua que han permitido agilizar muchas de las tareas involucradas en su desarrollo, afectando positivamente a la calidad del código y a la depuración de errores.

Líneas de trabajo futuras
-------------------------
Se pueden tomar dos caminos distintos para mejorar la aplicación propuesta:

1. Desarrollar nuevos complementos (*plugins*) que añadan nuevas funcionalidades.
2. Extender la funcionalidad de los complementos propuestos en este proyecto.

A continuación se exponen las funcionalidades que pueden resultar interesantes añadir en la plataforma.

- Dar soporte a los periodos temporales que pudieran aparecer dentro del metadato "*Temporal Coverage*".

   - Representar gráficamente a todos los periodos dentro de una linea temporal.
   - Sugerir periodos temporales del cliente *PeriodO* a la hora de rellenar el metadato. De esta manera, se podrá enriquecer dicho metadato en la fase de integración correspondiente.

En cuanto a las posibles mejoras de los complementos:

- Complemento *ARIADNEplus Tracking*: introducir nuevas funciones en alguna de las fases de los *tickets*.

   - *Fase 1*: Poder editar los ítems desde la misma ventana, sin necesidad de desplazarse al gestor de ítems.
   - *Fase 3*: Previsualizar la colección de *periodO* indicada por el usuario y poder adjuntar el fichero de definición de mapeo desde la misma ventana.
   - *Fase 4*: Previsualizar los ítems publicados en el portal fantasma de *ARIADNEplus* a partir del enlace *SPARQL*.

- Complemento *Geolocation*: introducir localizaciones con áreas poligonales (hasta ahora solo se pueden simples o rectangulares).
- Complemento *Bulk Metadata Editor*: introducir nuevas acciones de edición como, por ejemplo, poder asignar más de dos valores a un mismo metadato.
- Complemento *OAI-PMH Harvester*: poder programar recolecciones de metadatos en determinados intervalos de tiempo.
- Complemento *AutoDublinCore*: dado que la localización de los datos es imprescindible, crear un sistema que en caso de que el metadato que se encarga de ello ("*Spatial Coverage*) se encuentre vacío, busque en el contenido de los demás metadatos (e.g. *Title*, *Description*, etc.) una localización y, en caso de encontrarla, actualizar el contenido del metadato con dicha localización.
- Traducir todos los complementos desarrollados en este proyecto a otros idiomas.
