<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
return;
error_reporting(1);
/**
 * <p>
 * Class UPLOAD_FILES.<br>
 *
 * This class has the purpose of ease and secure the upload of files.<br>
 *
 * It executes several tests on the uploaded files and the upload directory
 * to validate and secure the upload. It checks the file name extension,
 * type, size and directory permissions.<br>
 *
 * It may also create the uploading directory structure, generate a unique
 * random name for the uploaded file, upload multiples files, and keep track
 * of the upload success or failure.<br>
 *
 * @author		Marcos Thiago <fabismt@yahoo.com.br>
 * @version 	1.0
 * @copyright	Freeware
 * @package 	UPLOAD_FILES
 * @example 	example.php
 * @since 		06/2004
**/
//require_once 'Archive/Zip.php';

class UPLOAD_FILES
{
	/**
 	* Receives the original name of the uploaded file.
 	*
 	* @var 		string
 	* @access	private
	**/
	var $name;

	/**
 	* Receives the temporary path and name of the uploaded file.
 	*
 	* @var 		string
 	* @access	private
	**/
	var $tmp_name;

	/**
 	* Receives the error of the uploaded file.
 	*
 	* @var 		string
 	* @access	private
	**/
	var $error;

	/**
 	* Store the size of the uploaded file.
 	*
 	* @var 		string
 	* @access	private
	**/
	var $size;

	/**
 	* Keep track of the files successfully uploaded.
 	*
 	* @var 		array
 	* @access	private
 	* @see 		moveFileToDestination()
	**/
	var $succeed_files_track;

	/**
 	* Keep track of the files which fail the upload.
 	*
 	* @var 		array
 	* @access	private
 	* @see 		moveFileToDestination()
	**/
	var $fail_files_track;

	/**
 	* Keep the index of the succeed_files_track array.
 	*
 	* @var 		int
 	* @access	private
 	* @see 		moveFileToDestination()
 	**/
	var $succeed_track_index;

	/**
 	* Keep the index of the fail_files_track array.
 	*
 	* @var 		int
 	* @access	private
 	* @see 		moveFileToDestination()
 	**/
	var $fail_track_index;

	/**
 	* Receives an array with the supported file extensions and types.
 	*
 	* @var 		array
 	* @access	private
 	* @see 		checkUploadConditions()
 	**/
	var $supported_extensions;

	/**
 	* Receives the destination directory for uploaded files.
 	*
 	* @var 		string
 	* @access	private
 	* @see 		checkUploadConditions(),createDiretoryStructure(),moveFileToDestination()
	**/
	var $dst_dir;

	/**
 	* Store the permission for the uploaded file.
 	* Default value is 0444(read only).
 	*
 	* @var 		string
 	* @access	private
 	* @see 		moveFileToDestination()
	**/
	var $file_perm;

	/**
 	* Store the field name of the uploaded file.
 	*
 	* @var 		string
 	* @access	private
 	* @see 		moveFileToDestination()
	**/
	var $fld_name;

	/**
 	* Store the file maximum size allowed in bytes.
 	* Default value is 40960 bytes (40 KB).
 	*
 	* @var 		int
 	* @access	private
 	* @see 		checkUploadConditions()
	**/
	var $max_file_size; //bytes.

	/**
 	* Store messages of upload status.
 	*
 	* @var 		array
 	* @access	private
 	* @see 		moveFileToDestination()
	**/
	var $msg;

	/**
 	* Store the error code of upload.
 	* It became the index for array msg.
 	*
 	* @var 		int
 	* @access	private
	**/
	var $error_type; // error code.

	/**
 	* If TRUE generate a random name to the file else maintain the original name.
 	* Default value is TRUE;
 	*
 	* @var 		boolean
 	* @access	private
 	* @see 		moveFileToDestination()
	**/
	var $randon_name;

	/**
 	* If TRUE replace a existent file, else mantain the original file.
 	* Default value is TRUE;
 	*
 	* @var 		boolean
 	* @access	private
 	* @see 		moveFileToDestination()
	**/
	var $replace;
	var $flagZIP;


	var $p_width = '';
	var $p_height = '';

	var $s_width = '';
	var $s_height = '';

	///////////////////////////////////////////////////////////////////
	/**
 	* Constructor
 	*
 	* @access	public
	**/
	function UPLOAD_FILES() {
		$this->name 								= "";
		$this->tmp_name 							= "";
		$this->error								= "";
		$this->size 								= "";
		$this->succeed_files_track					= array();
		$this->fail_files_track						= array();
		$this->succeed_track_index					= 0;
		$this->fail_track_index						= 0;
		$this->supported_extensions					= array();
		$this->dst_dir								= "";
		$this->file_perm							= "0644";
		$this->fld_name								= "";
		$this->max_file_size						= 4096000000000; //bytes.
		$this->msg									= array (
			"0" => "File uploaded successfully!",
			"1" => "Extension not allowed or wrong file type!",
			"2" => "File exceed size limit!",
			"3" => "Fail trying to create directory!",
			"4" => "Wrong directory permission!",
			"5" => "Unexpected failure!",
			"6" => "File not found!",
			"7" => "File already exists in directory!"
		);
		$this->error_type							= 0; // error code.
		$this->randon_name							= TRUE;
		$this->replace								= TRUE;
		$this->flagZIP								= FALSE;
		$this->force_move							= FALSE;

		$this->p_width								= '';
		$this->p_height								= '';
		$this->s_width								= '';
		$this->s_height								= '';
		$this->width								= '';
		$this->height								= '';

		$this->width_thumb							= '';
		$this->height_thumb							= '';
	}

	///////////////////////////////////////////////////////////////////
	/**
 	* Set value to class variables.
 	*
 	* @param 	string	$var
 	* @param 	string	$value
 	* @access	public
	**/
	function set($var,$value) {
		$this->$var = $value;
	}

	///////////////////////////////////////////////////////////////////
	/**
 	* Get value of class variables.
 	*
 	* @param 	string	$var
 	* @access	public
	**/
	function get($var) {
		return $this->$var;
	}

	///////////////////////////////////////////////////////////////////
	/**
 	* Generate a unique name to the uploaded file.
 	*
 	* @access	private
	**/
	function generateUniqueId() {
		return md5(uniqid(mt_rand(),TRUE));
	}

	///////////////////////////////////////////////////////////////////
	/**
 	* Generate the file unique name with extension.
 	*
 	* @access	private
 	* @return	string $dst_file_name
 	* @uses		generateUniqueId()
	**/
	function generateFileName() {
		$dst_file_name = $this->generateUniqueId();
		$arr = split("\.",$this->name);
		$dst_file_name .= ".".$arr[count($arr)-1];
		return $dst_file_name;
	}

	///////////////////////////////////////////////////////////////////
	/*
	* Generació del nom original amb codi numeric
	*	pasem el nom original
	* developer by Carlos Vazquez
	* copy Omatech.com
	*
	*/
 	function generateFileName2($file,$i=0) {
		if(file_exists($this->dst_dir."/".$file) && !$this->replace) {
			$trozos=explode(".",$file);
			array_pop($trozos);
			$nom=implode(".",$trozos);
			$trozos2=explode(".",$file);
			$ext=$trozos2[count($trozos2)-1];
			if(file_exists($this->dst_dir."/".$nom.".".$i.".".$ext)) {
				$dst_file_name=$this->generateFileName2($file,$i+1);
			}
			else {
				$dst_file_name=$nom.".".$i.".".$ext;
			}
		}
		else {
            if($this->replace){
                $trozos=explode(".",$file);
                array_pop($trozos);
                $nom=implode(".",$trozos);
                $trozos2=explode(".",$file);
                $ext=$trozos2[count($trozos2)-1];
                $dst_file_name=$nom.".".time().".".$ext;
            }else{
                $dst_file_name=$file;
            }
		}

		return $dst_file_name;
	}

	///////////////////////////////////////////////////////////////////
	/**
 	* Replace accents and special chars from file name.
 	*
 	* @access	private
 	* @return	string $string
	**/
	function fixFileName($string) {
		$string = strtr ( $string, "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ", "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn");
		for($i=0 ; $i < strlen($string); $i++) {
			if(!preg_match("([0-9A-Za-z_\.])",$string[$i])) {
				$string[$i] = "_";
			}
		}

		return $string;
	}

	///////////////////////////////////////////////////////////////////
	/**
 	* Check upload condictions for file and directory.
 	*
 	* @access	private
 	* @return	bool (true/false)
 	* @uses		createDiretoryStructure()
	**/
	function checkUploadConditions() {
		//-------- extension and type check -----------// de moment desactivo aixo pq nose si filtrar extensions
		/* $arr = split("\.",$this->name);
		$ext = $arr[count($arr)-1];
		$allow = TRUE;
		foreach($this->supported_extensions as $each_ext => $type)
			if(strtolower($each_ext) == strtolower($ext) && $this->type == $type) return TRUE;

		if(!$allow) {
			$this->error_type = 1;
			return FALSE;
		} */
		//----------------------------------------------//

		//------ size check ----------------------------//
		if($this->size > $this->max_file_size) {
			$this->error_type = 2;
			return FALSE;
		}
		//----------------------------------------------//


		//----- directory check ------------------------//
		if(!file_exists($this->dst_dir) && !$this->replace) {
			if(!$this->createDiretoryStructure()) {
				$this->error_type = 3;
				return FALSE;
			}
			else {
				return TRUE;
			}
		}
		elseif (!is_writable($this->dst_dir)){
			$this->error_type = 4;
			return FALSE;
		}
		else {
			return TRUE;
		}
		//----------------------------------------------//

	}

	function createDateFolder($path = null) {
		if(!file_exists($path)) {
			@mkdir($path,0755);
		}
	}
	///////////////////////////////////////////////////////////////////
	/**
 	* Try to create directory structure for upload.
 	*
 	* @access	private
 	* @return	bool (true/false)
	**/
	function createDiretoryStructure() {
		$arr = explode('\\',$this->dst_dir);
		$j=0;
		for($i=0; $i < count($arr); $i++)
			if($arr[$i]) $new_arr[$j++] = $arr[$i];
		//---------------------------------------------//

		$arr = $new_arr;
		$end = count($arr);

		$path="";
		for($i=0; $i < $end; $i++){
				if ($path=="") {
						$path=$arr[$i];
				} else {
						$path .= "\\".$arr[$i];
				}
			if(!file_exists($path)) {
				if(!@mkdir($path,0777)) {
					$fail = TRUE;
					break;
				}
			}
		}

		if($fail) return FALSE; else return TRUE;
	 }

	/////////////////////////////////////////////////////////////////////
	/**
 	* Chekeja si l'arxiu es zip
 	*
 	* @access	public
 	* @return	string si(return filename - el .zip)/no(return "")
	**/
	function checkZipType(){
		/* $arr = split("\.",$this->name);
		$ext = $arr[count($arr)-1];

		if(strtolower($ext) == 'zip') {
			echo "entro";
			$this->flagZIP = TRUE;
			$path="";
			for($i=0; $i < (count($arr)-1); $i++)
				$path .= $arr[$i];

			return $path."/";
		} */

		return "";
	}

	///////////////////////////////////////////////////////////////////
	/**
 	* Execute the upload.
 	* This is the main function that should be used outside this class.
 	* Also is responsable for keep track of succeed and failure uploads.
 	*
 	* @access	public
 	* @return	bool (true/false)
 	* uses 		checkUploadConditions(),generateFileName()
	**/
	function moveFileToDestination() {
		if(file_exists($this->tmp_name) && $this->dst_dir) {
			if($this->checkUploadConditions()) {
				$dst_file_name = $this->generateFileName2($this->fixFileName($this->name));
				$this->name = $dst_file_name;

				// La seguent linea afegida perque el nom real del fitxer pujat al servidor coincideixi amb el nom netejat. apons 20070830
				$full_destination_path = $this->dst_dir."/".$dst_file_name;
				if(file_exists($full_destination_path) && !$this->replace) {
					$this->error_type = 7;
				}
				else {
					if (GD_LIB) {
						// Tenim el GD activat
						$sizes = getimagesize($this->tmp_name);

						if ((isset($this->p_width) && $this->p_width!='' && $this->p_width!='0' && $this->p_width!=$sizes[0])
						|| (isset($this->p_height) && $this->p_height!='' && $this->p_height!='0' && $this->p_height!=$sizes[1])
						|| $this->force_move) {
							// S'ha de redimensionar, comprovem el tamany
							$size_array = getimagesize($this->tmp_name);
							if ($size_array[0]!=$this->p_width || $size_array[1]!=$this->p_height || $this->force_move) $new_img = new image($this->tmp_name, $dst_file_name);
						}

						if (isset($new_img) && $new_img->isImage()) {
							// Es una imatge, abans de redimensionar comprovem que no tingui el mateix tamany
							$this->error_type = 0;
							list($this->width_thumb, $this->height_thumb) = $new_img->size_width_height($this->width,$this->height,$this->s_width,$this->s_height,$this->p_width,$this->p_height);
							$new_img->save($full_destination_path);
							$this->error_type = 0;
						}
						else {
							if(@move_uploaded_file($this->tmp_name,$full_destination_path)) {
								@chmod ($this->dst_dir."/".$dst_file_name,$this->file_perm);
								$this->error_type = 0;
							}
							else {
								$this->error_type = 5;
							}
						}
					}
					else {
						if(@move_uploaded_file($this->tmp_name,$full_destination_path)) {
							@chmod ($this->dst_dir."/".$dst_file_name,$this->file_perm);
							$this->error_type = 0;
						}
						else {
							$this->error_type = 5;
						}
					}
				}
			}
			else {
				$this->error_type = 6;
			}

			if($this->error_type != 0) {
				$this->fail_files_track[$this->succeed_track_index]["file_name"] 						= $this->name;
				$this->fail_files_track[$this->succeed_track_index]["new_file_name"]					= $dst_file_name;
				$this->fail_files_track[$this->succeed_track_index]["destination_directory"]			= $this->dst_dir;
				$this->fail_files_track[$this->succeed_track_index]["field_name"]						= $this->fld_name;
				$this->fail_files_track[$this->succeed_track_index]["file_size"] 						= $this->size;
				$this->fail_files_track[$this->succeed_track_index]["file_type"] 						= $this->type;
				$this->fail_files_track[$this->succeed_track_index]["error_type"]						= $this->error_type;
				$this->fail_files_track[$this->succeed_track_index++]["msg"] 							= $this->msg[$this->error_type];
				return FALSE;
			}
			else {
				$this->succeed_files_track[$this->fail_track_index]["file_name"] 						= $this->name;
				$this->succeed_files_track[$this->fail_track_index]["new_file_name"]					= $dst_file_name;
				$this->succeed_files_track[$this->fail_track_index]["destination_directory"]			= $this->dst_dir;
				$this->succeed_files_track[$this->fail_track_index]["field_name"]						= $this->fld_name;
				$this->succeed_files_track[$this->fail_track_index]["file_size"] 						= $this->size;
				$this->succeed_files_track[$this->fail_track_index]["file_type"] 						= $this->type;
				$this->succeed_files_track[$this->fail_track_index]["error_type"]						= $this->error_type;
				$this->succeed_files_track[$this->fail_track_index++]["msg"] 							= $this->msg[$this->error_type];
				return TRUE;
			}
		}
	}
}


class image
{
	var $img;
	var $isImg;

	function image($imgfile, $originalName) {
		//detect image format
		$this->img["format"]=explode('.',$originalName);
		$this->img["format"]=strtoupper($this->img["format"][count($this->img["format"])-1]);
		$this->isImg = 1;
		if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
			//JPEG
			$this->img["format"]="JPEG";
			$this->img["src"] = ImageCreateFromJPEG ($imgfile);
		}
		elseif ($this->img["format"]=="PNG") {
			//PNG
			$this->img["format"]="PNG";
			$this->img["src"] = ImageCreateFromPNG ($imgfile);
		}
		elseif ($this->img["format"]=="GIF") {
			//GIF
			$this->img["format"]="GIF";
			$this->img["src"] = ImageCreateFromGIF ($imgfile);
		}
		elseif ($this->img["format"]=="WBMP") {
			//WBMP
			$this->img["format"]="WBMP";
			$this->img["src"] = ImageCreateFromWBMP ($imgfile);
		}
		else {
			//DEFAULT
			$this->isImg = 0;
		}

		@$this->img["lebar"] = imagesx($this->img["src"]);
		@$this->img["tinggi"] = imagesy($this->img["src"]);
		//default quality jpeg
		$this->img["quality"]=100;
	}

	function isImage () {
		return $this->isImg;
	}

	//Unificar la funció de les mides en una sola
	function size_width_height($width, $height, $s_width = null, $s_height = null, $d_width = null, $d_height = null) {
		if (isset($s_width) && $width && isset($s_height) && $height) {
			$this->img["src_width_start"]=$s_width;
			$this->img["src_height_start"]=$s_height;
			$this->img["lebar_thumb"]=$d_width;
			$this->img["tinggi_thumb"]=$d_height;
			$this->img["lebar"]=$width;
			$this->img["tinggi"]=$height;
		}
		elseif ($d_width!='' && $d_width!=0 && $d_height!='' && $d_height!=0) {
			$this->img["lebar_thumb"]=$d_width;
			//Si la imatge es més baixa de la que s'espera, no fem canvis, apliquem el aspect ratio i prou
			if ($this->img["tinggi"] >= $d_height) {
				$this->img["tinggi_thumb"]=$d_height;
				$this->img["src_width_start"]=0;
				$this->img["src_height_start"]=0;
			}
			//La imatge rebuda es més alta de la esperada. Calculem on hem de començar a tallar.
			else {
				$this->img["tinggi_thumb"]=$this->img["lebar"];
				$this->img["src_width_start"]=0;
				$this->img["src_height_start"]=($this->img["tinggi"] - $d_height)/2;
			}
		}
		elseif ($d_width!='' && $d_width!=0) {
			$this->img["lebar_thumb"]=$d_width;
			@$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
			$this->img["src_width_start"]=0;
			$this->img["src_height_start"]=0;
		}
		elseif ($d_height!='' && $d_height!=0) {
			$this->img["tinggi_thumb"]=$d_height;
			@$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
			$this->img["src_width_start"]=0;
			$this->img["src_height_start"]=0;
		}

		return array($this->img["lebar_thumb"], $this->img["tinggi_thumb"]);
	}

	function size_auto($size=100) {//size
		if ($this->img["lebar"]>=$this->img["tinggi"]) {
			$this->img["lebar_thumb"]=$size;
			@$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
		}
		else {
			$this->img["tinggi_thumb"]=$size;
			@$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
		}
	}

	function jpeg_quality($quality=100) {//jpeg quality
		$this->img["quality"]=$quality;
	}

	function show() {//show thumb
		@Header("Content-Type: image/".$this->img["format"]);

		/* change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function*/
		$this->img["des"] = ImageCreateTrueColor($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
		//Mira bé perquè sembla que no estem agafant bé el width i el height de destí (agafem el total de l'imatge?)
		@imagecopyresampled ($this->img["des"], $this->img["src"], 0, 0, $this->img["src_width_start"], $this->img["src_height_start"], floor($this->img["lebar_thumb"]), floor($this->img["tinggi_thumb"]), $this->img["lebar"], $this->img["tinggi"]);
		if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {//JPEG
			imageJPEG($this->img["des"],"",$this->img["quality"]);
		}
		elseif ($this->img["format"]=="PNG") {//PNG
			imagePNG($this->img["des"],"", 9, PNG_ALL_FILTERS);
		}
		elseif ($this->img["format"]=="GIF") {//GIF
			imageGIF($this->img["des"]);
		}
		elseif ($this->img["format"]=="WBMP") {//WBMP
			imageWBMP($this->img["des"]);
		}
	}

	function save($save="") {//save thumb
		if (empty($save)) $save=strtolower("./thumb.".$this->img["format"]);
		/* change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function*/

		$this->img["des"] = ImageCreateTrueColor($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);


        //***************** Eladio: MODified TO KEEP TRANSPARENCIES ******************
        $white = imagecolorallocate($this->img["des"], 255, 255, 255);
        imagefill($this->img["des"], 0, 0, $white);
        // Check for transparency
        if ($this->img["format"] == "PNG" || $this->img["format"] == "GIF") {
            $transparency = imagecolortransparent($this->img["src"]);
            if ($transparency >= 0) {

                $transparent_color = imagecolorsforindex($this->img["src"], $transparency);
                $transparency = imagecolorallocate($this->img["des"], $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($this->img["des"], 0, 0, $transparency);
                imagecolortransparent($this->img["des"], $transparency);


            }else  if ($this->img["format"] == "PNG") {
                imagealphablending($this->img["des"], false);
                $color = imagecolorallocatealpha($this->img["des"], 0, 0, 0, 127);
                imagefill($this->img["des"], 0, 0, $color);
                imagesavealpha($this->img["des"], true);
            }

        }
        //********************* END ************************

		@imagecopyresampled ($this->img["des"], $this->img["src"], 0, 0, $this->img["src_width_start"], $this->img["src_height_start"], floor($this->img["lebar_thumb"]), floor($this->img["tinggi_thumb"]), $this->img["lebar"], $this->img["tinggi"]);
		//@imagecopyresampled ($dst_r,$img_r,0,0,$x,$y,$twidth,$theight,$width,$height);

		unlink($save);
		if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {//JPEG
			imageJPEG($this->img["des"],$save,$this->img["quality"]);
		}
		elseif ($this->img["format"]=="PNG") {//PNG
			imagePNG($this->img["des"],$save, 9, PNG_ALL_FILTERS);
		}
		elseif ($this->img["format"]=="GIF") {//GIF
			imageGIF($this->img["des"],$save);
		}
		elseif ($this->img["format"]=="WBMP") {//WBMP
			imageWBMP($this->img["des"],$save);
		}
	}
}
?>
