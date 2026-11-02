<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Loader\Loader;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminListClassExport extends AuthController
{
    public function render()
    {
        $filename = addslashes($_REQUEST['id']) . '_' . addslashes($_REQUEST['name']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $conn = config("database.connections.mysql");
        $connection_params = array(
            'dbname' => $conn['database'],
            'user' => $conn['username'],
            'password' => (isset($conn['password']) ? $conn['password'] : ''),
            'host' => $conn['host'],
            'driver' => 'pdo_mysql',
            'charset' => 'utf8'
        );
        $conn_to = \Doctrine\DBAL\DriverManager::getConnection($connection_params);

        $loader = new Loader($conn_to);
        $attributes = $loader->getAllAttributesInClass(addslashes($_REQUEST['id']));

        foreach ($attributes ?? [] as $key => $attribute) {
            //Nomes camps strings per ara
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($key + 1) . '1', $attribute['type'].'#'.$attribute['name']);
        }

        $writer = new Xlsx($spreadsheet);

        $this->discardBufferedOutput();

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * The admin bootstrap has already echoed stray bytes by the time an action
     * renders (the message catalogues start with a BOM); a download must begin
     * at byte 0 or the spreadsheet is corrupt.
     */
    private function discardBufferedOutput(): void
    {
        if (ob_get_length()) {
            ob_clean();
        }
    }
}
