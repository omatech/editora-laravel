<?php

namespace Omatech\Editora\Admin\Accions;


use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\statictext;

use Omatech\Editora\Loader\Loader;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

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

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(0, 1 , 'S#nom_intern');


        foreach ($attributes as $key=>$attribute){
            //Nomes camps strings per ara
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($key+1, 1 , $attribute['type'].'#'.$attribute['name']);
        }

        $writer = new Xlsx($spreadsheet);

        ob_end_clean();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }
}