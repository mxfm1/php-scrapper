<?php
// OfferParser.php

use Symfony\Component\DomCrawler\Crawler;

class OfferParser {
    public static function parse(Crawler $Crawler): array {
        $data = [];

        // TITULO Y DESCRIPCION
        $offerTitle = $Crawler->filter('h1')->text();
        $data['titulo'] = mb_convert_encoding($offerTitle, 'UTF-8', 'UTF-8');
        
        $offerDescription = $Crawler->filter('.panel-body p')->text();
        $data['descripcion'] = mb_convert_encoding($offerDescription, 'UTF-8', 'UTF-8');

        // 1. Obtener la Empresa
        $empresaNodo = $Crawler->filter('.panel-body strong:contains("Empresa:")');
        if ($empresaNodo->count() > 0) {
            $empresa = $empresaNodo->siblings()->filter('span')->text();
            $data['empresa'] = trim(mb_convert_encoding($empresa, 'UTF-8', 'UTF-8'));
        } else {
            $data['empresa'] = "No especifica";
        }

        // 2. Obtener la Actividad Económica
        $actividadNodo = $Crawler->filter('.panel-body strong:contains("Actividad econ")');
        if ($actividadNodo->count() > 0) {
            $actividadEconomica = $actividadNodo->siblings()->filter('span')->text();
            $data['actividad_economica'] = trim(mb_convert_encoding($actividadEconomica, 'UTF-8', 'UTF-8'));
        } else {
            $data['actividad_economica'] = "No especifica";
        }

        // 3. Obtener la URL del Logo
        $logoNodo = $Crawler->filter('.panel-body img.imagen-logo');
        if ($logoNodo->count() > 0) {
            $logoUrlParcial = $logoNodo->attr('src');
            $data['logo_url'] = "https://www.bne.cl" . $logoUrlParcial;
        } else {
            $data['logo_url'] = "No tiene logo";
        }

        // 4. Ubicación, Fechas, Sueldo y Jornada
        $data['region'] = trim($Crawler->filter('.panel-body .fa-map-marker')->nextAll()->text());
        $data['fecha_inicio'] = trim($Crawler->filter('.panel-body .fa-calendar')->eq(0)->nextAll()->text());
        $data['fecha_termino'] = trim($Crawler->filter('.panel-body .fa-calendar')->eq(1)->nextAll()->text());
        $data['remuneracion'] = trim(preg_replace('/\s+/', ' ', $Crawler->filter('.panel-body .fa-dollar')->nextAll()->text()));
        $data['jornada'] = trim($Crawler->filter('.panel-body .fa-clock-o')->nextAll()->text());

        // 5. Requisitos solicitados
        $nivelEduNodo = $Crawler->filter('.panel-body strong:contains("Nivel educacional:")');
        if ($nivelEduNodo->count() > 0) {
            $nivelEducacional = trim($nivelEduNodo->nextAll()->text());
            $data['nivel_educacional'] = empty($nivelEducacional) ? "No especifica" : $nivelEducacional;
        } else {
            $data['nivel_educacional'] = "No especifica";
        }

        $experienciaNodo = $Crawler->filter('.panel-body strong:contains("Experiencia:")');
        if ($experienciaNodo->count() > 0) {
            $experiencia = $experienciaNodo->siblings()->filter('span')->text();
            $experiencia = preg_replace('/\s+/', ' ', $experiencia);
            $experiencia = trim(mb_convert_encoding($experiencia, 'UTF-8', 'UTF-8')); 
            $data['experiencia'] = str_replace('aos', 'años', $experiencia);
        } else {
            $data['experiencia'] = "No especifica";
        }

        // 6. Características del cargo
        $contratoNodo = $Crawler->filter('.panel-body strong:contains("Tipo de contrato:")');
        if ($contratoNodo->count() > 0) {
            $tipoContrato = $contratoNodo->siblings()->filter('span')->text();
            $data['tipo_contrato'] = trim(mb_convert_encoding($tipoContrato, 'UTF-8', 'UTF-8'));
        } else {
            $data['tipo_contrato'] = "No especifica";
        }

        $cargoNodo = $Crawler->filter('.panel-body strong:contains("Nivel de Cargo ofrecido:")');
        if ($cargoNodo->count() > 0) {
            $nivelCargo = $cargoNodo->siblings()->filter('span')->text();
            $data['nivel_cargo'] = trim(mb_convert_encoding($nivelCargo, 'UTF-8', 'UTF-8'));
        } else {
            $data['nivel_cargo'] = "No especifica";
        }

        $origenNodo = $Crawler->filter('.panel-body strong:contains("Origen de la Oferta:")');
        if ($origenNodo->count() > 0) {
            $origenOferta = $origenNodo->siblings()->filter('span')->text();
            $data['origen_oferta'] = trim(mb_convert_encoding($origenOferta, 'UTF-8', 'UTF-8'));
        } else {
            $data['origen_oferta'] = "No especifica";
        }

        $practicaNodo = $Crawler->filter('.panel-body strong:contains("Oferta de tipo pr")'); 
        if ($practicaNodo->count() > 0) {
            $esPractica = $practicaNodo->siblings()->filter('label')->text();
            $data['es_practica'] = trim(mb_convert_encoding($esPractica, 'UTF-8', 'UTF-8'));
        } else {
            $data['es_practica'] = "No especifica";
        }

        return $data; // Ahora el arreglo va completamente lleno
    }
}
