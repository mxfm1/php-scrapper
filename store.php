<?php
// store.php

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ReporteExcel {
    private $spreadsheet;
    private $nombreArchivo;

    public function __construct($nombreArchivo = 'ofertas_locales.xlsx') {
        $this->nombreArchivo = $nombreArchivo;

        if (file_exists($this->nombreArchivo)) {
            $this->spreadsheet = IOFactory::load($this->nombreArchivo);
        } else {
            $this->spreadsheet = new Spreadsheet();
            // Eliminamos la hoja en blanco por defecto
            $this->spreadsheet->removeSheetByIndex(0);
        }
    }

    /**
     * Obtiene una hoja por su nombre. Si no existe, la crea de forma segura.
     */
    private function obtenerOCrearHoja($nombreOrigen) {
        if (empty($nombreOrigen)) {
            $nombreOrigen = 'OFERTAS_BNE'; 
        }

        $nombreOrigen = strtoupper(trim($nombreOrigen));

        // Buscar si la pestaña ya existe en el libro actual
        $hoja = $this->spreadsheet->getSheetByName($nombreOrigen);

        // SI NO EXISTE, LA CREAMOS AQUÍ DE FORMA SEGURA
        if ($hoja === null) {
            $hoja = $this->spreadsheet->createSheet();
            $hoja->setTitle($nombreOrigen);
            $this->crearEncabezados($hoja);
        }

        return $hoja;
    }

    /**
     * Define las 17 columnas correspondientes en la pestaña indicada
     */
    private function crearEncabezados($hoja) {
        $encabezados = [
            'A1' => 'Código', 'B1' => 'Empresa', 'C1' => 'Actividad', 'D1' => 'Logo',
            'E1' => 'Título', 'F1' => 'Descripción', 'G1' => 'Región', 'H1' => 'Remuneración',
            'I1' => 'Jornada', 'J1' => 'Fecha Inicio', 'K1' => 'Fecha Término', 
            'L1' => 'Nivel Educacional', 'M1' => 'Experiencia', 'N1' => 'Tipo Contrato',
            'O1' => 'Nivel Cargo', 'P1' => 'Origen Oferta', 'Q1' => 'Es Práctica'
        ];

        foreach ($encabezados as $celda => $texto) {
            $hoja->setCellValue($celda, $texto);
        }
    }

    /**
     * Valida si un código ya existe en la pestaña especificada
     */
    public function validarEntryNuevo($codigo, $origen) {
        // Garantizamos que $hoja nunca sea null llamando al método reparado
        $hoja = $this->obtenerOCrearHoja($origen);
        
        $highestRow = $hoja->getHighestRow();

        for ($fila = 1; $fila <= $highestRow; $fila++) {
            $valorCelda = $hoja->getCell('A' . $fila)->getValue();
            if ($valorCelda == $codigo) {
                return false; // Ya existe, saltar
            }
        }
        return true; // Es nuevo, proceder a guardar
    }

    /**
     * Recibe los 17 parámetros desde el scraper, limpia caracteres y guarda
     */
    public function agregarOferta($origen, $codigo, $empresa, $actividad, $logo, $titulo, 
                                  $descripcion, $region, $remuneracion, $jornada, $fecha_inicio, 
                                  $fecha_termino, $nivel_educacional, $experiencia, $tipo_contrato, 
                                  $nivel_cargo, $origen_oferta, $es_practica) {
        
        $hoja = $this->obtenerOCrearHoja($origen);

        $ultimaFila = $hoja->getHighestRow();
        
        // Determinar la siguiente fila disponible de forma precisa
        $nuevaFila = ($ultimaFila == 1 && $hoja->getCell('A1')->getValue() === 'Código' && $hoja->getCell('A2')->getValue() === null) 
            ? 2 
            : $ultimaFila + 1;

        // Inserción de datos con decodificación de entidades HTML (&ntilde;, &aacute;, etc.)
        $hoja->setCellValue('A' . $nuevaFila, html_entity_decode($codigo, ENT_QUOTES, 'UTF-8'));
        $hoja->setCellValue('B' . $nuevaFila, html_entity_decode($empresa, ENT_QUOTES, 'UTF-8'));
        $hoja->setCellValue('C' . $nuevaFila, html_entity_decode($actividad, ENT_QUOTES, 'UTF-8'));
        $hoja->setCellValue('D' . $nuevaFila, $logo); 
        $hoja->setCellValue('E' . $nuevaFila, html_entity_decode($titulo, ENT_QUOTES, 'UTF-8'));
        $hoja->setCellValue('F' . $nuevaFila, html_entity_decode($descripcion, ENT_QUOTES, 'UTF-8'));
        $hoja->setCellValue('G' . $nuevaFila, html_entity_decode($region, ENT_QUOTES, 'UTF-8'));
        $hoja->setCellValue('H' . $nuevaFila, html_entity_decode($remuneracion, ENT_QUOTES, 'UTF-8'));
        $hoja->setCellValue('I' . $nuevaFila, html_entity_decode($jornada, ENT_QUOTES, 'UTF-8'));
        $hoja->setCellValue('J' . $nuevaFila, html_entity_decode($fecha_inicio, ENT_QUOTES, 'UTF-8'));
        $hoja->setCellValue('K' . $nuevaFila, html_entity_decode($fecha_termino, ENT_QUOTES, 'UTF-8'));
        $hoja->setCellValue('L' . $nuevaFila, html_entity_decode($nivel_educacional, ENT_QUOTES, 'UTF-8'));
        $hoja->setCellValue('M' . $nuevaFila, html_entity_decode($experiencia, ENT_QUOTES, 'UTF-8'));
        $hoja->setCellValue('N' . $nuevaFila, html_entity_decode($tipo_contrato, ENT_QUOTES, 'UTF-8'));
        $hoja->setCellValue('O' . $nuevaFila, html_entity_decode($nivel_cargo, ENT_QUOTES, 'UTF-8'));
        $hoja->setCellValue('P' . $nuevaFila, html_entity_decode($origen_oferta, ENT_QUOTES, 'UTF-8'));
        $hoja->setCellValue('Q' . $nuevaFila, html_entity_decode($es_practica, ENT_QUOTES, 'UTF-8'));
    }

    public function guardar() {
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($this->nombreArchivo);
        return $this->nombreArchivo;
    }
}