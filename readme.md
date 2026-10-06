# Manual de Uso y Documentación: Extractor de Empleos BNE/OMIL

Este manual técnico explica el funcionamiento, arquitectura y uso del sistema automatizado de extracción (*scraping*) de ofertas laborales desde la Bolsa Nacional de Empleo (**BNE**) y su posterior almacenamiento estructurado en formato **Excel (.xlsx)** de manera local.

---

## 🛠️ Arquitectura del Sistema

El proyecto está modularizado en tres archivos principales bajo el paradigma de programación orientada a objetos (POO) y separación de responsabilidades:

1. **`scrapper.php` (Script Principal):** Orquesta el flujo de ejecución, maneja los parámetros de búsqueda, gestiona las llamadas de red por paginación y conecta el extractor con el almacenamiento.
2. **`OfferParser.php` (Mapeador / Extractor):** Utiliza componentes de Symfony para leer el árbol HTML (*DOM*) de la oferta y transformar la información cruda en un arreglo asociativo estandarizado.
3. **`store.php` (`ReporteExcel`):** Clase encargada de la persistencia de datos local empleando `PhpSpreadsheet`. Controla la creación de pestañas automatizadas, la validación de duplicados y la sanitización de caracteres especiales.

---

## 🚀 Guía de Uso Rápido

### Requisitos Previos
* **PHP 7.4** o superior instalado localmente.
* **Composer** instalado para la gestión de dependencias.
* Librerías requeridas en tu `composer.json`:
  ```json
  {
      "require": {
          "phpoffice/phpspreadsheet": "^1.29",
          "guzzlehttp/guzzle": "^7.0",
          "symfony/dom-crawler": "^5.0",
          "symfony/css-selector": "^5.0"
      }
  }
  ```

### Configuración de Búsqueda
Abre `scrapper.php` y localiza la variable `$keywordsInput` en la sección de **Configuración General**. Puedes modificarla según tus necesidades:

```php
// Búsqueda por palabras clave específicas
$keywordsInput = ['conductor', 'conserje']; 

// Búsqueda global (sin filtros de texto libre)
$keywordsInput = null; 
```

### Ejecución
Abre tu consola o terminal en la ruta raíz del proyecto y ejecuta:
```bash
php scrapper.php
```

---

## 📖 Documentación de Funciones y Clases

### 1. Script Principal (`scrapper.php`)

#### `obtenerEmpleos($keywords = null): void`
Punto de entrada de la ejecución.
* **Parámetros:** 
  * `array|string|null $keywords`: Criterios de texto libre para buscar puestos. Si se provee un *string* separado por comas, la función lo convierte en una matriz limpia automáticamente.
* **Flujo:** Evalúa el tipo de entrada, inicializa el ciclo de búsqueda y finalmente invoca al método de cierre de Excel para consolidar el archivo en el disco.

#### `procesarBusqueda(?string $keyword = null): void`
Consume la API interna de la BNE y gestiona las subpeticiones del detalle.
* **Parámetros:** `?string $keyword`: Palabra clave única a procesar.
* **Flujo:** 
  1. Realiza una petición `GET` preliminar con `GuzzleHttp\Client` para calcular el total de páginas (`numPaginasTotal`).
  2. Itera secuencialmente por cada página recolectando los códigos de oferta (`codigo`).
  3. Ejecuta una petición interna por cada puesto para descargar su código fuente HTML.
  4. Valida la existencia del registro en la base local (Excel) para omitir duplicados y evitar pérdidas de tiempo de red en el mapeo repetido.

---

### 2. Clase `OfferParser` (`OfferParser.php`)

#### `public static function parse(Crawler $Crawler): array`
Transforma la estructura HTML de la página de detalle en datos estructurados limpios.
* **Parámetros:** `Symfony\Component\DomCrawler\Crawler $Crawler`: Objeto Crawler cargado con el cuerpo HTML de la oferta laboral.
* **Retorno:** Un `array` asociativo mapeado con claves normalizadas (`titulo`, `descripcion`, `empresa`, `actividad_economica`, etc.).
* **Detalle Técnico:** Emplea filtros CSS avanzados (como el selector posicional o de contenido `:contains()`) aislando los nodos `strong` y sus adyacentes (`siblings()`). Incorpora `mb_convert_encoding` para mitigar corrupciones de string en las cadenas de texto del DOM.

---

### 3. Clase `ReporteExcel` (`store.php`)

Abstrae la manipulación compleja de libros y celdas de cálculo.

#### `public function __construct(string $nombreArchivo)`
Constructor de la clase. Instancia un libro en blanco (`Spreadsheet`) si el archivo de destino no existe en la ruta local. Si el reporte ya existe (ej. ejecuciones pasadas), invoca dinámicamente a `IOFactory::load()` permitiendo la persistencia incremental de datos (*Append*).

#### `public function validarEntryNuevo(string $codigo, string $origen): bool`
Mecanismo de control de redundancias.
* **Parámetros:** 
  * `string $codigo`: Código identificador único del empleo.
  * `string $origen`: Nombre de la pestaña de destino asignada (ej. `OFERTAS_BNE`).
* **Retorno:** Devuelve `true` si el identificador no existe en la columna A de dicha hoja, o `false` en caso de detectar coincidencia exacta.

#### `public function agregarOferta(string $origen, ...$campos): void`
Escritura física de datos en memoria celular.
* **Funcionamiento:** Resuelve dinámicamente el número de la última fila libre con `$hoja->getHighestRow()`. Convierte de manera rigurosa los caracteres extraños y códigos de escape web (`&ntilde;`, `&aacute;`, etc.) en carácteres latinos nativos legibles usando `html_entity_decode($texto, ENT_QUOTES, 'UTF-8')`.

---

## 🔒 Buenas Prácticas Incorporadas
* **Políticas de Retardo (Delays):** Incluye `usleep(500000)` (0.5 segundos) entre peticiones de ofertas y `usleep(300000)` entre páginas para mitigar bloqueos o denegaciones de servicio (IP rate limiting) por parte del servidor de la BNE.
* **Pestañas Parametrizadas:** Permite segmentar automáticamente el universo de datos en pestañas independientes (por ejemplo, separar las ofertas generales de la BNE de los convenios municipales OMIL) con solo ajustar la variable de origen, manteniendo la integridad dentro del mismo archivo maestro de salida.
