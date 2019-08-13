<?php

namespace Omatech\Editora\Admin\Accions;


use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\statictext;

use Omatech\Editora\Loader\Loader;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use SKAgarwal\GoogleApi\PlacesApi;

class AdminListClassImport extends AuthController
{
    public function render()
    {
        $file = $_FILES['file_class']['name'];

        $file_name = explode(".", $_FILES['file_class']['name']);
        $extension_file = end($file_name);

        //$rows = Excel::load('storage\\exports\\'. $file_name)->get();

        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES['file_class']['tmp_name']);

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

        $batch_id = time();

        $id_class = explode("_", $file_name[0]);
        $id_class = $id_class[0];

        $name_class = explode('_', $file_name[0]);
        $name_class = end($name_class);

        $niceurls = array();

        $count_rows = 0;

        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

            $values = '';
            $count = 0;

            $have_niceurl = false;

            foreach ($worksheet->getRowIterator() as $row) {


                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(true); // Loop all cells, even if it is not set

                //Fila 1 => noms atributes A => nom_intern
                if ($row->getRowIndex() == 1) {
                    foreach ($cellIterator as $key => $cell) {
                        $attributes[$key] = $cell->getValue();
                    }

                } else {

                    $instance = array();
                    foreach ($cellIterator as $key => $cell) {

                        $type_attribute = explode('#', $attributes[$key]);

                        //S, U, K, T, Y
                        $excel_type_attribute = $type_attribute[0];

                        //nom_intern, titol, niceurl, text, ...
                        $excel_attribute = $type_attribute[1];

                        $value = $cell->getValue();
                        $value_attr = $cell->getValue();


                        if ( !empty($value) ) {

                            switch ($excel_type_attribute) {

                                case 'B':
                                    $instance[$excel_attribute] = $value;
                                    break;

                                case 'D':
                                    break;

                                case 'E':
                                    //date
                                    break;

                                case 'F':
                                    //file
                                    break;

                                case 'H':
                                    break;

                                case 'I':
                                    //imatge

                                    //$instance[$excel_attribute] = $value;
                                    break;

                                case 'K':
                                    $instance[$excel_attribute] = $value;
                                    break;

                                case 'L':
                                    //lookup
                                    break;

                                case 'M':
                                    //map
                                    $maps = $this->searchAddressOnGoogle($value);
                                    if(isset($maps['geometry']) && isset($maps['geometry']['location']) && isset($maps['geometry']['location']['lat']) && isset($maps['geometry']['location']['lng']) ){
                                        $instance[$excel_attribute] = $maps['geometry']['location']['lat'].':'.$maps['geometry']['location']['lng'].'@'.$value;
                                    }

                                    break;

                                case 'N':
                                    break;

                                case 'O':
                                    //color?
                                    break;

                                case 'R':
                                    break;

                                case 'S':

                                    if (strcmp($excel_attribute, 'nom_intern') == 0) {

                                        $inst_id = 1;
                                        $count = 0;

                                        while ($inst_id != -1) {
                                            $inst_id = $loader->getInstIDFromNomIntern($name_class, $value);
                                            if ($inst_id != -1) {
                                                $value = $value_attr . '-' . $count;
                                            }
                                            $count++;
                                        }
                                        $instance[$excel_attribute] = $value;

                                    } else {
                                        $instance[$excel_attribute] = $value;
                                    }

                                    break;

                                case 'T':

                                    $instance[$excel_attribute] = $value;
                                    break;

                                case 'U':
                                    //url

                                    $instance[$excel_attribute] = $value;
                                    break;

                                case 'Y':
                                    //youtube you:sdf
                                    break;

                                case 'Z':

                                    if (!empty($value)) {
                                        $value = str_replace(".","-",$value);
                                        $value = str_replace(",","-",$value);
                                        $value = str_replace(" ","-",$value);
                                        $value = str_replace("-----","-",$value);
                                        $value = str_replace("----","-",$value);
                                        $value = str_replace("---","-",$value);
                                        $value = str_replace("--","-",$value);
                                        if(strcmp(substr("value", -1) == '-' ) == 0  ){
                                            $value = substr($value, 0, -1);
                                        }

                                        $langs = $loader->getLanguagesFromAttributes();

                                        $count = 0;
                                        $end = false;
                                        $search = false;

                                        while ($end == false) {

                                            //languages: 'all', 'ca', 'es', 'en'
                                            foreach ($langs as $lang) {

                                                $nice_url = $loader->clean_url($value);

                                                if ($search == false) {
                                                    if ($loader->existsURLNice($nice_url, $lang['language'])) {
                                                        $search = true;
                                                    } else {
                                                        $search = false;
                                                    }
                                                }
                                            }

                                            if ($search == false) {
                                                $end = true;
                                                $have_niceurl = true;
                                            } else {
                                                $value = $value_attr . '-' . $count;
                                                $count++;
                                                $search = false;
                                            }

                                        }

                                        $instance[$excel_attribute] = $loader->clean_url($value);
                                        $niceurls[$excel_attribute] = $loader->clean_url($value);
                                    }

                                    break;

                            }
                        }


                    }
                    if( !empty($instance) ){
                        $inst_id = $loader->insertInstanceWithExternalID($id_class, $instance['nom_intern'], '', $batch_id, $instance, 'P');

                        if (isset($inst_id) && !empty($inst_id) && $have_niceurl == true && isset($niceurls) && !empty($niceurls)) {
                            foreach ($niceurls as $name_atr => $urlnice) {

                                $lang = explode('_', $name_atr);
                                $lang = end($lang);

                                if (!empty($urlnice)) {

                                    $result = $loader->insertUrlNice($urlnice, $inst_id, $lang);
                                }
                            }
                        }

                        if (isset($inst_id) && !empty($inst_id)) {
                            $count_rows++;

                        }
                    }
                }
            }

        }

        $viewData['message'] = 'Good!';
        $viewData['count_rows'] = $count_rows;
        return response()->view('editora::pages.list_classes_export', $viewData);

    }



    public function searchAddressOnGoogle($address)

    {
        $key = env('GOOGLE_API_KEY');
        $googlePlaces = new PlacesApi($key);
        $response = $googlePlaces->placeAutocomplete($address);

        if ($response['status'] !== 'ZERO_RESULTS') {

            $placeId = $response->first()[0]['place_id'];
            $response = $googlePlaces->placeDetails($placeId)['result'];

        return $response;

            $componentAddress = [
                'street_number' => 'short_name',
                'route' => 'long_name',
                'locality' => 'long_name',
                'administrative_area_level_1' => 'long_name',
                'administrative_area_level_2' => 'long_name',
                'country' => 'long_name',
                'postal_code' => 'short_name'
            ];
            $address = $response['address_components'];
            for ($i = 0; $i < count($address); $i++) {
                $addressType = $address[$i]['types'][0];
                if ($componentAddress[$addressType]) {
                    $val = $address[$i][$componentAddress[$addressType]];
                    $componentAddress[$addressType] = $val;
                }
            }
            return [
                'address' => $componentAddress,
                'fullAddress' => $response['formatted_address']
            ];
        }
        return false;

    }
}