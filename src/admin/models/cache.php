<?php
//à
require_once(DIR_APLI_ADMIN . '/models/Model.php');
use Illuminate\Support\Facades\Session;

class cache extends model 
{
	function __construct() {
		return;
	}

	// Al crear o editar una instancia
	function backup($inst_id) {
		global $dbh;
		require_once (DIR_APLI.'/objects/MainObject.php');
		$idiomes=$this->busca_idiomes();
		$mainobject=new MainObject();

		if($idiomes) {
			foreach($idiomes as $idioma) {
				$cache_xml=$mainobject->getInstanceInfo($inst_id, $idioma, 'cache', '', 'D',Session::get('rol_id'));

				$sql_cache='insert omp_instances_backup (inst_id,language,xml_cache,date,user) values ('.$inst_id.', "'.$idioma.'", "'.mysql_real_escape_string($cache_xml, $dbh).'", now(),'.Session::get('user_id').');';
				$ret_cache=parent::insert_one($sql_cache);
				if (!$ret_cache) echo mysql_error($dbh).chr(13).chr(10).chr(13).chr(10);
			}
		}
		
		return 1;
	}
	
	//Només per resetejar TOTA la cache, handle with care!
	function deleteCache() {
		$sql_delete='delete from omp_instances_cache';
		parent::execute($sql_delete);
		$sql='alter table omp_instances_cache auto_increment=1;';
		parent::execute($sql);
		
		return 1;
	}
	
	function deleteCacheIncomplete($num) {
		$sql_delete='delete from omp_instances_cache where xml_cache_r="" or xml_cache_d=""';
		parent::execute($sql_delete);
		$sql='SELECT COUNT(*) AS conta, inst_id FROM omp_instances_cache GROUP BY inst_id HAVING conta != 3 ORDER BY inst_id;';
		$instances=parent::get_data($sql);
		foreach ($instances as $inst_id) {
			$sql_delete2='delete from omp_instances_cache where inst_id = '.$inst_id['inst_id'].';';
			parent::execute($sql_delete2);
		}
	}
	
	//Al crear o editar una instancia
	function updateCache($inst_id,$backup='N') {
		global $dbh;
		require_once (DIR_APLI.'/objects/MainObject.php');
		$idiomes=$this->busca_idiomes();
		$mainobject=new MainObject();

		if($idiomes) {
			foreach($idiomes as $idioma) {
				$cache_xml_r=$mainobject->getInstanceInfo($inst_id, $idioma, '%cache_name%', '%cache_relid%', 'R',Session::get('rol_id'));
				$cache_xml_d=$mainobject->getInstanceInfo($inst_id, $idioma, '%cache_name%', '%cache_relid%', 'D',Session::get('rol_id'));
				
				// nou codi cache a memcache, apons 20130919
				if (class_exists('Memcache')) {
					$mc=new Memcache;
					$memcacheAvailable=$mc->connect('localhost', 11211);
                }
				else {
					$memcacheAvailable=FALSE;
                }

				if ($memcacheAvailable) {
					$key=DIR_APLI.':'.$inst_id.':'.$idioma.':';
					$mc->set($key.'R', $cache_xml_r, MEMCACHE_COMPRESSED, 3600);
					$mc->set($key.'D', $cache_xml_d, MEMCACHE_COMPRESSED, 3600);
				}
				// fi nou codi cache a memcache
				
				$search_field=$this->search_texts($inst_id,$idioma);

				$sql_cache='insert omp_instances_cache (inst_id,language,xml_cache_r,xml_cache_d,search_field) values ('.$inst_id.', "'.$idioma.'", "'.mysql_real_escape_string($cache_xml_r,$dbh).'", "'.mysql_real_escape_string($cache_xml_d,$dbh).'", "'.mysql_real_escape_string($search_field,$dbh).'");';
				$ret_cache=parent::insert_one($sql_cache);
				if (!$ret_cache) {
					$sql_cache='update omp_instances_cache set xml_cache_r="'.mysql_real_escape_string($cache_xml_r,$dbh).'",xml_cache_d="'.mysql_real_escape_string($cache_xml_d,$dbh).'",search_field="'.mysql_real_escape_string($search_field,$dbh).'" where inst_id='.$inst_id.' and language="'.$idioma.'";';
					$ret_cache=parent::update_one($sql_cache);
					if (!$ret_cache) echo $sql_cache.chr(10).chr(13).chr(10).chr(13).chr(10).chr(13).chr(10).chr(13).chr(10).chr(13).chr(10).chr(13);
				}
			}
		}
		if ($backup=='Y') $this->backup($inst_id);
		return 1;
	}
	
	function eraseCache() {
		$this->deleteCache();
		return html_message_ok(getMessage('info_erase_cache'));
	}
	
	function cleanCache() {
		$idiomes = count($this->busca_idiomes);
		$this->deleteCacheIncomplete($idiomes);
		
		return html_message_ok(getMessage('info_clean_cache'));
	}
	
	function regenerateCacheWithout() {
		$instances=$this->getAllInstances();
		$return='';
		foreach ($instances as $inst_id) $this->updateCache($inst_id,'N');

		return html_message_ok(getMessage('info_regenerate_cache'));
	}
	
	//Despres d'una carrega massiva
	function regenerateCache() {
		$this->deleteCache();
		$instances=$this->getAllInstances();
		$return='';
		foreach ($instances as $inst_id) $this->updateCache($inst_id,'N');

		return html_message_ok(getMessage('info_regenerate_cache'));
	}
	
	function busca_idiomes() {
		$sql ='select language as lang from omp_attributes where language!="ALL" group by language';
		$ret=parent::get_data($sql);
		if($ret) {
			$idiomes=array();
			foreach($ret as $row) $idiomes[]=$row['lang'];

			return $idiomes;
		}
	}
	
	function getAllInstances() {
		$sql='SELECT id FROM omp_instances 
		WHERE id NOT IN (SELECT inst_id FROM omp_instances_cache)
		ORDER BY id;';
		$ret=parent::get_data($sql);

		$inst_id=array();
		foreach($ret as $row) $inst_id[]=$row['id'];

		return $inst_id;
	}
	
	function search_texts($inst_id,$idioma) {
		$sql='select * 
		from omp_values v
		, omp_attributes a
		where inst_id='.$inst_id.'
		and a.id=v.atri_id
		and (a.type="S" or a.type="T" or a.type="A")
		and (a.language="ALL" or a.language="'.$idioma.'")';

		$ret=parent::get_data($sql);

		$texts='';
		if (is_array($ret)) foreach($ret as $row) $texts.=$row['text_val'].' ';
		else $texts='';

		return $texts;
	}
}
?>
