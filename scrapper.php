<?php

require_once 'vendor/autoload.php';
require_once 'offerParser.php';

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

$Client = new Client();

$keywords= ['conserje','jardinero'];
$url = 'https://www.bne.cl/data/ofertas/buscarListas?mostrar=empleo&textoLibre=aseo&numPaginaRecuperar=1&numResultadosPorPagina=10&clasificarYPaginar=true';

// $response = $Client->get($url);
// $data = json_decode($response->getBody(), true);



// foreach ($keywords as $keyword) {
//     $url = "ofertas/buscarListas?mostrar=empleo&textoLibre=$keyword&numPaginaRecuperar=1&numResultadosPorPagina=10&clasificarYPaginar=true";

//     // redirecciona hacia el detalle de la vista
    
//     $response = $Client->get($url);
//     $data = json_decode($response->getBody(), true);
//     print_r($data);
// }

// try{
//     foreach($keywords as $keyword){
//         $url = "https://www.bne.cl/data/ofertas/buscarListas?mostrar=empleo&textoLibre=$keyword&numPaginaRecuperar=1&numResultadosPorPagina=10&clasificarYPaginar=true";

//         $response = $Client->get($url);
//         $data = json_decode($response->getBody(), true);
//         echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//         $numeroPages = $data['paginaOfertas']['numPaginasTotal'];
//         // $publicationEntries = $data['resultados'];
//         // echo $publicationEntries;
//         // echo $numeroPages;
//         // foreach($publicationEntries as $entry){
//         //     echo $entry['titulo'] . "\n";
//         //     echo $entry['descripcion'] . "\n";
//         //     echo $entry['codigo'] . "\n";
//         //     echo "--------------------------------------------------\n";
//         // }
//     }
// }catch(Exception $e){
//     echo "Error: " . $e->getMessage();
// }
// try {

//     $urlSufix = "https://www.bne.cl/data/ofertas/buscarListas?mostrar=empleo&textoLibre=";
//     foreach($keywords as $keyword){
//         echo "============== BUSQUEDA ACTUAL =================" . "\n";
//         echo "Palabra Clave: " . $keyword . "\n";
        
//         $url = $urlSufix . $keyword . "&numPaginaRecuperar=1&numResultadosPorPagina=10&clasificarYPaginar=true";
        
//         $response = $Client->get($url);
//         $data = json_decode($response->getBody(), true);

//         $numeroPages = $data['paginaOfertas']['numPaginasTotal'];
//         $publicationEntries = $data['paginaOfertas']['resultados'];
        
//         echo "Numero de paginas: " . $numeroPages . "\n";
//         echo "resultados obtenidos: " . count($publicationEntries) . "\n";
//         // echo "data obtenida" . json_encode($publicationEntries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
//         echo "===============================" . "\n";
//         for($counter = 0; $counter < $numeroPages; $counter++){
//             echo "numero de pag: " . $counter . "\n";
//             $url = $urlSufix . $keyword . "&numPaginaRecuperar=" . $counter . "&numResultadosPorPagina=10&clasificarYPaginar=true";
//             $response = $Client->get($url);
//             $data = json_decode($response->getBody(), true);
//             // por cada registro dentro de la pagina hacer la inserción en la tabal de excel
//             foreach($publicationEntries as $job_offer){
//                 $jobOfferDetail = $Client.get($job_offer['codigo']);
//                 echo '============ DETALLES OFERTA =================0';
//                 echo "TITULO OFERTA: " . $job_offer['titulo'] . "\n";
//                 echo "codigo de redirección por cada oferta" . $job_offer['codigo'] . "\n";
                
//             }
//             echo "Numero de paginas: " . $numeroPages . "\n";
//             echo "resultados obtenidos: " . count($publicationEntries) . "\n";
//             // echo "data obtenida" . json_encode($publicationEntries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
//             echo "===============================" . "\n";
//             usleep(300000);
//         }
        
//     }


//     $response = $Client->get($url);
//     $data = json_decode($response->getBody(), true); 

//     $detailURL = 'https://www.bne.cl/oferta/2026-117295';
//     $detailResponse = $Client->get($detailURL);
//     $detailBody = $detailResponse->getBody();

//     $Crawler = new Crawler($detailBody);
//     $offerMapper = OfferParser::parse($Crawler);

//     // echo json_encode($offerMapper, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

//      echo "------- DATOS DE CONTACTO ----------" ."\n";
//     echo "Empresa: " . $offerMapper['empresa'] . "\n";
//     echo "Actividad Económica: " . $offerMapper['actividad_economica'] . "\n";
//     echo "imagen de contacto: " . $offerMapper['logo_url'] . "\n";
//     echo "--------------------------------\n";

//     echo "------- DATOS DE LA OFERTA ----------" ."\n";
//     echo "Titulo oferta: " . $offerMapper['titulo'] . "\n";
//     echo "Descripción: " . substr($offerMapper['descripcion'], 0, 50) . "...\n";
//     echo "Región: " . $offerMapper['region'] . "\n";
//     echo "Rango Remuneración: " . $offerMapper['remuneracion'] . "\n";
//     echo "Jornada: " . $offerMapper['jornada'] . "\n";
//     echo "Fecha Inicio: " . $offerMapper['fecha_inicio'] . "\n";
//     echo "Fecha Término: " . $offerMapper['fecha_termino'] . "\n";
//     echo "===============================" . "\n";

//     echo "------- REQUISITOS SOLICITADOS ----------" ."\n";
//     echo "nivel educacional: " . $offerMapper['nivel_educacional'] . "\n";
//     echo "experiencia: " . $offerMapper['experiencia'] . "\n";
//     echo "===============================" . "\n";

//     echo "------- CARACTERISTICAS ----------\n";
//     echo "Tipo de Contrato: " . $offerMapper['tipo_contrato'] . "\n";
//     echo "Nivel de Cargo: " . $offerMapper['nivel_cargo'] . "\n";
//     echo "Origen Oferta: " . $offerMapper['origen_oferta'] . "\n";
//     echo "Práctica Profesional: " . $offerMapper['es_practica'] . "\n";
//     echo "----------------------------------\n";
// } catch(Exception $e) {
//     echo "Error: " . $e->getMessage();
// }

try {
    $urlSufix = "https://www.bne.cl/data/ofertas/buscarListas?mostrar=empleo&textoLibre=";

    foreach ($keywords as $keyword) {
        echo "============== INICIANDO BÚSQUEDA =================" . "\n";
        echo "Palabra Clave: " . $keyword . "\n";
        
        // 1. Primera petición solo para obtener el número total de páginas
        $urlInicial = $urlSufix . urlencode($keyword) . "&numPaginaRecuperar=1&numResultadosPorPagina=10&clasificarYPaginar=true";
        $response = $Client->get($urlInicial);
        $data = json_decode($response->getBody(), true);

        // Validamos que existan datos antes de continuar
        if (!isset($data['paginaOfertas'])) {
            echo "No se encontraron resultados para: " . $keyword . "\n";
            continue;
        }

        $numeroPages = $data['paginaOfertas']['numPaginasTotal'];
        echo "Total de páginas encontradas: " . $numeroPages . "\n";
        echo "==================================================" . "\n";

        // 2. Iteramos por cada una de las páginas (generalmente las APIs de paginación empiezan en 1 o 0, asumo 1 según tu URL inicial)
        for ($page = 1; $page <= $numeroPages; $page++) {
            echo "--- Procesando Página: " . $page . " de " . $numeroPages . " ---\n";
            
            $urlPage = $urlSufix . urlencode($keyword) . "&numPaginaRecuperar=" . $page . "&numResultadosPorPagina=10&clasificarYPaginar=true";
            $response = $Client->get($urlPage);
            $pageData = json_decode($response->getBody(), true);

            $offersInPage = $pageData['paginaOfertas']['resultados'] ?? [];
            echo "Ofertas encontradas en esta página: " . count($offersInPage) . "\n";

            // 3. Por cada oferta de la página actual, obtenemos su código y su detalle
            foreach ($offersInPage as $job_offer) {
                $codigoOferta = $job_offer['codigo'];
                echo "-> Procesando Oferta: [" . $codigoOferta . "] - " . $job_offer['titulo'] . "\n";

                try {
                    // Reemplaza esta URL por el endpoint real que use la BNE para ver el detalle mediante el código
                    $urlDetalle = "https://bne.cl/oferta/" . $codigoOferta; 
                    
                    $responseDetail = $Client->get($urlDetalle);
                    $jobOfferDetail = $responseDetail->getBody();
                    echo '=====DETALLE OFERTA===========' . "\n" ;
                    // ==========================================================
                    // AQUÍ ENTRALOS DATOS RELEVANTES ($jobOfferDetail) 
                    // Puedes proceder a insertarlos en tu Excel / Base de datos
                    // ==========================================================

                    $Crawler = new Crawler($jobOfferDetail);
                    $offerMapper = OfferParser::parse($Crawler);

                    echo "--- DATOS DE CONTACTO ---" . "\n";
                    echo "Empresa: " . $offerMapper['empresa'] . "\n";
                    echo "Actividad Económica: " . $offerMapper['actividad_economica'] . "\n";
                    echo "URL Logo: " . $offerMapper['logo_url'] . "\n";
                    echo "\n";

                    echo "--- DATOS DE LA OFERTA ---" . "\n";
                    echo "Título: " . $offerMapper['titulo'] . "\n";
                    echo "Descripción: " . $offerMapper['descripcion'] . "\n";
                    echo "Región: " . $offerMapper['region'] . "\n";
                    echo "Rango Remuneración: " . $offerMapper['remuneracion'] . "\n";
                    echo "Jornada: " . $offerMapper['jornada'] . "\n";
                    echo "Fecha Inicio: " . $offerMapper['fecha_inicio'] . "\n";
                    echo "Fecha Término: " . $offerMapper['fecha_termino'] . "\n";
                    echo "\n";

                    echo "--- REQUISITOS SOLICITADOS ---" . "\n";
                    echo "Nivel Educacional: " . $offerMapper['nivel_educacional'] . "\n";
                    echo "Experiencia: " . $offerMapper['experiencia'] . "\n";
                    echo "\n";

                    echo "--- CARACTERÍSTICAS ---" . "\n";
                    echo "Tipo de Contrato: " . $offerMapper['tipo_contrato'] . "\n";
                    echo "Nivel de Cargo: " . $offerMapper['nivel_cargo'] . "\n";
                    echo "Origen Oferta: " . $offerMapper['origen_oferta'] . "\n";
                    echo "Es Práctica: " . $offerMapper['es_practica'] . "\n";
                    echo "===========================\n";
                    
                } catch (\Exception $e) {
                    echo "Error al obtener el detalle de la oferta " . $codigoOferta . ": " . $e->getMessage() . "\n";
                }

                // Pausa corta entre ofertas para evitar bloqueos (Anti-bot WAF)
                usleep(500000); // 0.5 segundos
            }

            // Pausa entre páginas (300ms)
            usleep(300000); 
        }
    }
} catch (\Exception $e) {
    echo "Error general en el proceso: " . $e->getMessage() . "\n";
}
// Guarda el código fuente real en un archivo
// file_put_contents('estructura_bne.html', $html);

// echo "Estructura guardada. Abre 'estructura_bne.html' en tu editor para ver el árbol.";