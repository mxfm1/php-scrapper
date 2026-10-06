<?php

require_once 'vendor/autoload.php';
require_once 'offerParser.php';
require 'store.php'; 

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

// =========================================================================
// CONFIGURACIÓN Y VARIABLES GENERALES
// =========================================================================
$baseUrl           = 'https://www.bne.cl/data/ofertas/buscarListas';
$resultadosPorPagina = 50;
$regionObjetivo    = 375;
$comunaObjetivo    = 1041;
$archivoSalida     = 'ofertas_empleo_quinta_region.xlsx';
$OFERTAS_BNE = "OFERTAS_BNE";
$OFERTAS_OMIL = "OFERTAS_OMIL";

// Instancias iniciales
$Client = new Client();
$excel  = new ReporteExcel($archivoSalida);

// Configura las palabras clave aquí o déjalo vacío/null para realizar una búsqueda global
$keywordsInput = ['conserje']; // Ejemplos: ['aseo'], [], null

// =========================================================================
// FUNCIONES DE BÚSQUEDA Y PROCESAMIENTO
// =========================================================================

/**
 * Función principal para iniciar la obtención de empleos.
 */
function obtenerEmpleos($keywords = null) {
    global $excel;

    echo "================ INICIANDO BÚSQUEDA =================\n";

    // Convertir string separado por comas en array si es necesario
    if (is_string($keywords)) {
        $keywords = array_filter(array_map('trim', explode(',', $keywords)));
    }

    if (!empty($keywords) && is_array($keywords)) {
        foreach ($keywords as $keyword) {
            echo "Buscando por Palabra Clave: " . $keyword . "\n";
            procesarBusqueda($keyword);
        }
    } else {
        echo "Sin palabras clave definidas. Realizando búsqueda general...\n";
        procesarBusqueda(null);
    }

    $archivoGuardado = $excel->guardar();
    echo "=====================================================\n";
    echo "Archivo Excel generado correctamente: " . $archivoGuardado . "\n";
}

/**
 * Realiza la paginación y extracción de ofertas según la palabra clave recibida.
 */
function procesarBusqueda(?string $keyword = null) {
    global $Client, $excel, $baseUrl, $regionObjetivo, $comunaObjetivo, $resultadosPorPagina,$OFERTAS_BNE, $OFERTAS_OMIL;

    try {
        // Build base query parameters
        $queryParams = [
            'mostrar'               => 'empleo',
            'idRegion'              => $regionObjetivo,
            'idComuna'              => $comunaObjetivo,
            'numResultadosPorPagina' => $resultadosPorPagina,
            'clasificarYPaginar'    => 'true',
            'numPaginaRecuperar'    => 1
        ];

        if (!empty($keyword)) {
            $queryParams['textoLibre'] = $keyword;
        }

        // 1. Primera petición para obtener total de páginas
        $urlInicial = $baseUrl . '?' . http_build_query($queryParams);
        $response = $Client->get($urlInicial);
        $data = json_decode($response->getBody(), true);

        if (!isset($data['paginaOfertas'])) {
            echo "No se encontraron resultados para el criterio actual.\n";
            return;
        }

        $numeroPages = $data['paginaOfertas']['numPaginasTotal'] ?? 0;
        echo "Total de páginas encontradas: " . $numeroPages . "\n";
        echo "-----------------------------------------------------\n";

        // 2. Recorrer las páginas
        for ($page = 1; $page <= $numeroPages; $page++) {
            echo "--- Procesando Página: " . $page . " de " . $numeroPages . " ---\n";

            $queryParams['numPaginaRecuperar'] = $page;
            $urlPage = $baseUrl . '?' . http_build_query($queryParams);
            
            $response = $Client->get($urlPage);
            $pageData = json_decode($response->getBody(), true);

            $offersInPage = $pageData['paginaOfertas']['resultados'] ?? [];
            echo "Ofertas encontradas en esta página: " . count($offersInPage) . "\n";

            // 3. Procesar cada oferta individual
            foreach ($offersInPage as $job_offer) {
                $codigoOferta = $job_offer['codigo'];
                echo "-> Procesando Oferta: [" . $codigoOferta . "] - " . $job_offer['titulo'] . "\n";

                try {
                    $urlDetalle = "https://bne.cl/oferta/" . $codigoOferta;
                    $responseDetail = $Client->get($urlDetalle);
                    $jobOfferDetail = $responseDetail->getBody();

                    $Crawler = new Crawler((string) $jobOfferDetail);
                    $offerMapper = OfferParser::parse($Crawler);

                    $validar = $excel->validarEntryNuevo($codigoOferta,$OFERTAS_OMIL);
                    if ($validar == false) {
                        continue;
                    }

                    
                    $excel->agregarOferta(
                        $OFERTAS_OMIL,
                        $codigoOferta, 
                        $offerMapper['empresa'], 
                        $offerMapper['actividad_economica'], 
                        $offerMapper['logo_url'],
                        $offerMapper['titulo'],
                        $offerMapper['descripcion'],
                        $offerMapper['region'],
                        $offerMapper['remuneracion'],
                        $offerMapper['jornada'],
                        $offerMapper['fecha_inicio'],
                        $offerMapper['fecha_termino'],
                        $offerMapper['nivel_educacional'],
                        $offerMapper['experiencia'],
                        $offerMapper['tipo_contrato'],
                        $offerMapper['nivel_cargo'],
                        $offerMapper['origen_oferta'],
                        $offerMapper['es_practica']
                    );

                } catch (\Exception $e) {
                    echo "Error al obtener el detalle de la oferta " . $codigoOferta . ": " . $e->getMessage() . "\n";
                }

                usleep(500000); // 0.5 segundos de pausa entre ofertas
            }

            usleep(300000); // 0.3 segundos de pausa entre páginas
        }

    } catch (\Exception $e) {
        echo "Error en la ejecución de la búsqueda: " . $e->getMessage() . "\n";
    }
}

// =========================================================================
// EJECUCIÓN
// =========================================================================

// Llama a la función pasándole el array/string de keywords o déjalo vacío
obtenerEmpleos($keywordsInput);