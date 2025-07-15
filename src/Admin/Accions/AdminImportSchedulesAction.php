<?php

namespace Omatech\Editora\Admin\Accions;

use Doctrine\DBAL\DriverManager;
use Illuminate\Support\Facades\DB;
use Omatech\Editora\Loader\Loader;
use Omatech\Editora\Admin\Models\Instances;

class AdminImportSchedulesAction extends AuthController
{
    /** @var Loader $loader */
    private $loader;
    private $dbConnection;
    private $classes;
    private $instances;
    private $relations;
    private $relationInstances;

    public function render()
    {
        $file = $_FILES['file_schedules']['name'];

        $file_name = explode(".", $_FILES['file_schedules']['name']);
        $extension_file = end($file_name);
        $count_rows = 0;
        $rowsByCourse = [];

        if (INST_PERM || Session::get('rol_id')==1 || $security->buscaAccessTotal($params) || $security->getAccess2($params)|| $security->getAccess('insertable', $params)) {
            $instances = new Instances;

            $params=get_params_info();
            $params['p_mode']='V';
            $params['param1']=4;

            $p_mode = 'I';
            $title=EDITORA_NAME;

            $menu = $this->loadMenu($instances, $params);
        }

        if ($extension_file == 'csv') {

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            $reader->setDelimiter(',');
            $objPHPExcel = $reader->load($_FILES['file_schedules']['tmp_name']);

            $conn = config("database.connections.mysql");
            $connection_params = array(
                'dbname' => $conn['database'],
                'user' => $conn['username'],
                'password' => (isset($conn['password']) ? $conn['password'] : ''),
                'host' => $conn['host'],
                'driver' => 'pdo_mysql',
                'charset' => 'utf8'
            );
            $conn_to = DriverManager::getConnection($connection_params);

            $this->loader = new Loader($conn_to);

            $this->dbConnection = DB::connection('mysql');
            $this->classes = $this->dbConnection->table('omp_classes')->whereIn('name',
            ['TableGroupBlock', 'TabBlock', 'TableBlock', 'TableRow', 'TableCell'])->pluck('id', 'name')->all();

            $this->relations = $this->dbConnection->table('omp_relations')->whereIn('name',
            ['TabsList', 'TablesList', 'TableRowsList', 'TableCellsList'])->pluck('id', 'name')->all();

            $this->relationInstances = $this->dbConnection->table('omp_relation_instances')
            ->whereIn('rel_id', array_values($this->relations))->get();

            $this->instances = $this->dbConnection->table('omp_instances')
            ->whereIn('class_id', array_values($this->classes))->orderBy('key_fields', 'desc')->get();

            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                foreach ($worksheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(true);

                    if ($row->getRowIndex() == 1) {
                        foreach ($cellIterator as $key => $cell) {
                            $attributes[$key] = $cell->getValue();
                        }
                    } else {
                        $fieldsOfRow = [];
                        foreach ($cellIterator as $keyCell => $cell) {
                            $fieldsOfRow[$attributes[$keyCell]] = $cell->getValue();
                        }

                        $rowsByCourse[$fieldsOfRow['id']][$fieldsOfRow['curs']][] = $fieldsOfRow;

                        $count_rows++;
                    }
                }
            }

            $this->importSchedules($rowsByCourse);

        }

        $viewData['title'] = EDITORA_NAME;
        $viewData['message'] = '';
        $viewData['p_mode'] = 'I';
        $viewData['body_class'] = 'edit-view';
        $viewData = array_merge($viewData, $menu);
        $viewData['count_rows'] = $count_rows;
        return response()->view('editora::pages.import_schedules', $viewData);
    }

    private function importSchedules($rowsByCourse)
    {
        foreach ($rowsByCourse as $id => $courses) {
            $this->removeChildsOfTableGroup($id);

            foreach (array_reverse($courses) as $key => $rows) {
                $this->createTab($id, $key, $rows);
            }
        }
    }

    private function createTab($id, $curs, $rows)
    {
        $tab = $this->loader->insertInstanceWithExternalID(
            $this->classes['TabBlock'],
            'Tab_' . $curs,
            null,
            $this->classes['TabBlock'],
            ['title_' . config('editora.defaultLanguage') => $curs]
        );

        $this->loader->insertRelationInstance(
            $this->relations['TabsList'],
            $id,
            $tab
        );

        $table = $this->createTable($tab, $curs, $rows);
    }

    private function createTable($tabId, $curs, $rows)
    {
        $table = $this->loader->insertInstanceWithExternalID(
            $this->classes['TableBlock'],
            'Taula_' . $curs,
            null,
            $this->classes['TableBlock'],
            []
        );

        $this->loader->insertRelationInstance(
            $this->relations['TablesList'],
            $tabId,
            $table
        );

        $key = count($rows);
        foreach (array_reverse($rows) as $row) {
            $this->createRow($table, $key, $row);
            $key--;
        }
    }

    private function createRow($table, $key, $row)
    {
        $tableRow = $this->loader->insertInstanceWithExternalID(
            $this->classes['TableRow'],
            'Fila_' . $key,
            null,
            $this->classes['TableRow'],
            ['title_' . config('editora.defaultLanguage') => 'Fila ' . $key]
        );

        $this->loader->insertRelationInstance(
            $this->relations['TableRowsList'],
            $table,
            $tableRow
        );

        $fields = ['curs', 'grup', 'horari', 'dies', 'professor'];

        $key = count($fields);

        foreach (array_reverse($fields) as $values) {
            if (isset($row[$values])) {
                $value = $row[$values];
            } else {
                $value = '';
            }
            $this->createCell($tableRow, $key, $value);
            $key--;
        }
    }

    private function createCell($tableRow, $key, $value)
    {
        $tableCell = $this->loader->insertInstanceWithExternalID(
            $this->classes['TableCell'],
            'Cel·la_' . $key,
            null,
            $this->classes['TableCell'],
            ['title_' . config('editora.defaultLanguage') => $value]
        );

        $this->loader->insertRelationInstance(
            $this->relations['TableCellsList'],
            $tableRow,
            $tableCell
        );
    }

    private function removeInstances($instancesToDelete)
    {
        foreach ($instancesToDelete as $instanceId) {
            $this->loader->deleteInstance($instanceId);
        }
    }

    private function removeChildsOfTableGroup($id)
    {
        $instance = $this->instances->where('id', $id)->first();

        if (!$instance) {
            return;
        }

        $instancesToDelete = [];
        $haveRelationInstances = true;
        $ids = [$id];

        while ($haveRelationInstances) {
            $relationInstances = $this->relationInstances->whereIn('parent_inst_id', $ids)
                ->whereIn('rel_id', array_values($this->relations))->pluck('child_inst_id')->toArray();

            if (!$relationInstances) {
                $haveRelationInstances = false;
            } else {
                $ids = $relationInstances;
                $instancesToDelete = array_merge($instancesToDelete, $relationInstances);
            }
        }

        $this->removeInstances($instancesToDelete);
    }
}
