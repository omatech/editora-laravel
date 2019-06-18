<?php

namespace Omatech\Editora\Admin\Models;

class statictext extends model 
{


	
	public function get_static_text_keys(){
		$sql = 'select distinct text_key from omp_static_text;';
        $result = parent::get_data($sql);
		$statictext = array();
		foreach($result as $item){
			array_push($statictext, $item['text_key']);
		}
		return $statictext;
	}
	
	
	public function get_static_text_lang($lang){
		$sql = 'select * from omp_static_text where language="'.$lang.'";';
		$result = parent::get_data($sql);
		$messages = array();
		
		if($result){
			foreach($result as $item){
				array_push($messages, $item);
			}	
		}
		
		return $messages;
	}

	public function get_one_static_text($key, $lang){
		$sql = 'select * from omp_static_text where language="'.$lang.'" and text_key="'.$key.'"';
		
		$ret = parent::get_one($sql);
        if(!$ret){
	        return null;
	    }else{
		    return $ret;
	    }
	}
	
	public function get_static_text_languages(){
		$sql = "select distinct language from omp_attributes where language not in ('ALL');";
		$result = parent::get_data($sql);
		
		$langs = array();
		foreach($result as $item){
			$langs[$item['language']] = $item['language'];
		}
		return $langs;
	}
	
	public function set_static_text($values, $key){
		$langs = $this->get_static_text_languages();
		foreach($langs as $lang){
			$val = $values['lang_'.$lang];
			if($this->get_one_static_text($key, $lang)!=null){
				$sql = 'UPDATE omp_static_text SET text_value = "'.parent::escape($values['lang_'.$lang]).'"
					 WHERE text_key="'.$key.'" AND language = "'.$lang.'" ;';
					parent::update_one($sql);
					echo($sql);
			}else{
				$sql = 'INSERT INTO omp_static_text (text_key, language, text_value) 
					VALUES ("'.$key.'", "'.$lang.'", "'.parent::escape($val).'" );';
					parent::insert_one($sql);
			}
		}
		return true;
	}
}
