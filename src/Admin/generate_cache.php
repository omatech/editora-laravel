<?php
//à
echo '-- Starting '.__FILE__.' '.date('d/m/Y H:i:s')."\n";
	
class model
{
	function __construct()
	{ 
		return;
	}	

	function get_data($sql)
	{
		global $dbh;
		$ret = mysql_query($sql, $dbh);

		if (!$ret)
		{
			//debug('Error en mysql: '.mysql_error($dbh));
			echo 'Error en mysql: '.mysql_error($dbh); die();
			return false;
		}
		else
		{
			$arr=array();
			while ($row = mysql_fetch_array($ret, MYSQL_ASSOC))
			{
				array_push($arr, $row);
			}
			if (count($arr)>0)
				return $arr;
			else
				return false;
		}
	}

	function get_one($sql)
	{
		global $dbh;
		$ret = mysql_query($sql, $dbh);

		if (!$ret)
			return false;
		else
		{
			$row = mysql_fetch_array($ret, MYSQL_ASSOC);
			if ($row)
				return $row;
			else
				return false;
		}
	}

	function insert_one($sql)
	{
		global $dbh;
		$ret = mysql_query($sql, $dbh);

		if (!$ret)
			return false;
		else
		{
			$id = mysql_insert_id($dbh);
			if ($id)
				return $id;
			else
				return false;
		}  
	}

	function update_one($sql)
	{
		global $dbh;
		$ret = mysql_query($sql, $dbh);

		if (!$ret)
			return false;
		else
			return true;
	}  

	function execute ($sql)
	{
		global $dbh;
		$ret = mysql_query($sql, $dbh);
		$id = mysql_insert_id($dbh);
		return $id;
	}

	function escape ($string)
	{
		global $dbh;
		return mysql_real_escape_string($string, $dbh);
	}
}

	ini_set("memory_limit", "500M");
	set_time_limit(0);

	require_once($_SERVER['DOCUMENT_ROOT'].'/conf/ompinfo.php');
	
	$md = new model();
	
	$sql='select * 
	from omp_niceurl
	where cache_pending = "Y" and niceurl <> ""';

	$ret=$md->get_data($sql);

/*	
	$outputfile = "dl.html";
	echo file_get_contents($outputfile);
*/
    if (!$ret) 
	{
	  echo "nothing to do\n";
	  die;
	}
	
	foreach($ret as $row)
	{		
		$url_nice = $row['niceurl'];
		$lang = $row['language'];
		echo "generant cache per: $url_nice amb l'idioma: $lang\n";
		
		if($lang == "ALL")
		{
          $url = URL_APLI.'/'.$url_nice.'?regenera_cachel=6969';
		  $outputfile = DIR_APLI.DIR_CACHE.$url_nice.'.html.tmp';
		  generate_file($url, $outputfile);
		  
		  if($row['inst_id'] == 1)
		  {
			$url = URL_APLI.'/'.$url_nice.'?regenera_cachel=6969';
			$outputfile = DIR_APLI.DIR_CACHE.'index.html.tmp';
		    generate_file($url, $outputfile);
		  }

	      foreach ($array_langs as $lg)
		  {
		    $url = URL_APLI.'/'.$lg.'/'.$url_nice.'?regenera_cachel=6969';
		    $outputfile = DIR_APLI.DIR_CACHE.$lg.'/'.$url_nice.'.html.tmp';
		    generate_file($url, $outputfile);

			if($row['inst_id'] == 1)
			{
			  $url = URL_APLI.'/'.$url_nice.'?regenera_cachel=6969';
			  $outputfile = DIR_APLI.DIR_CACHE.$lg.'/'.'index.html.tmp';
		      generate_file($url, $outputfile);

			}
		  }
		}
		else
		{
			$url = URL_APLI.'/'.$lg.'/'.$url_nice.'?regenera_cachel=6969';
			$outputfile = DIR_APLI.DIR_CACHE.$lang.'/'.$url_nice.'.html.tmp';
		    generate_file($url, $outputfile);
		}

		$sql='update omp_niceurl set cache_pending = "N"
		where id = '.$row['id'];

//		$upd=$md->update_one($sql);
		echo "final generacio cache\n";
	}
	
	function generate_file ($url, $outputfile)
	{
		$finaloutputfile=substr($outputfile, 0, -4);
		$cmd = "wget -q \"$url\" -O $outputfile";
		exec($cmd);			
		$cmd = "mv $outputfile $finaloutputfile";
		exec($cmd);
		echo "generating $finaloutputfile \n";
	}
	
	
echo '-- Ending '.__FILE__.' '.date('d/m/Y H:i:s')."\n";
