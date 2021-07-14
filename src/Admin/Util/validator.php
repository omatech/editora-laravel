<?php

	/*****************************************************************
	FunctionName	:isEmail
	Input			: String
	OutPut			: True/ False
	Description 	: Returns false if invalid mail-id, else true
	******************************************************************/
	function isEmail($sEMail)
	{
		$nLen=strlen(trim($sEMail));
		
		if($nLen==0)
			return FALSE;

		$sFirstChr=$sEMail[0];
		$sLastChr=$sEMail[$nLen-1];
		/***** email id should not start or end with @,.,- ***/
		if(is_integer(strpos("@.-",$sFirstChr)) || is_integer(strpos("@.-",$sLastChr)))
		{
			return FALSE;
		}

		/***** email id should not start or end with number ***/
		if (is_integer($sFirstChr) || is_integer($sLastChr))
		{
			return FALSE;
		}

		/**** check for 2 '..','--','__','@@'in mail Id ***/
		if((is_integer(strpos(trim($sEMail),".."))) || (is_integer(strpos(trim($sEMail),"--"))) || (is_integer(strpos(trim($sEMail),"__"))) || (is_integer(strpos(trim($sEMail),"@@"))))
		{
			return FALSE;
		}
		
		$sValidChar="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.-_@";

		for ($n=0;$n<strlen($sEMail);$n++)
		{
			$sCharToCheck="";
			/***** Check for valid caharacters *******/
			if (!is_integer(strpos($sValidChar,$sEMail[$n])))
			{
				return FALSE;
			}
			
			/***** Check if more than 1 @ are there *******/
			if (substr_count(trim($sEMail),"@")!=1)
			{
				return FALSE;
			}

			/***** Check if more atleast 1 . *******/
			if (substr_count(trim($sEMail),".")==0)
			{
				return FALSE;
			}

			/***** Check if any of the follow. string is there *******/
			$sCharToCheck=$sPrevChar.trim($sEMail[$n]);
			switch($sCharToCheck) {
				case '.@' :
				case '@.' :
				case '-@' :
				case '@-' :
				case '_@' :
				case '@_' :
						return FALSE;
						break;
			}

			$sPrevChar=trim($sEMail[$n]);
		}

		return TRUE;
	}

	/*****************************************************************
	FunctionName :isDoubleByte
	Input :String
	OutPut :0/1
	Description :If char not a double byte then returns false
	******************************************************************/
	function isDoubleByte($sChkStr)
	{
	     for ($i=0;$i<mb_strlen($sChkStr);$i++)
	     {
	         if(mb_substr_count($sChkStr,"　")==mb_strlen($sChkStr))
	         {
	            return FALSE;
	         }

	         $sStr=mb_substr($sChkStr,$i, 1); 
	         if(strlen($sStr)<2)
	         {
	             return FALSE;
	         }
	     }

	     return TRUE;
	}

	/*****************************************************************
	FunctionName :isDate
	Input :Date String (YYYY/MM/DD format)
	OutPut :error/1
	Description :If Valid date returns 1 else error
	******************************************************************/
	function isDate($sDate) {
		if (trim($sDate)=="") {
		return FALSE;
		}

		$sDate = preg_split ('/[\/.-]/', $sDate);
		if (isset($sDate[2])) {
			$tmp = explode(' ', $sDate[2]);
		}
		else return FALSE;
		
		if (!@checkdate($sDate[1],$sDate[0],$tmp[0])) {
			return FALSE;
		}
		else {
			return TRUE;
		}
	}

	/*****************************************************************************************
	FunctionName  : IsHiragana
	Input         : String 
	OutPut        : Returns True If string contains hiragana characters else False 
	Explanation   : Used to check if the input string contains hiragana characters 
	*****************************************************************************************/
	Function IsHiragana($sStr)
	{	
		$sAllowedChars ="あいうえおぁぃぅぇぉかきくけこがぎぐげごさしすせそざじずぜぞたちつってとだぢづでどはひふへほばびぶべぼぱぴぷぺぽまみむめもなにぬねのらりるれろやゆよゃゅょわをん";

		If(mb_strlen($sStr) > 0 )
		{
			for ($i=0;$i<mb_strlen($sStr);$i++)
			{
				$sStr1= mb_substr($sStr,$i,1); 
				if(!is_integer(mb_strpos($sAllowedChars,$sStr1)))
				{
					return FALSE;
				}
			}
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	} 
	
	/*****************************************************************************************
	FunctionName  : IsKatakana
	Input         : String 
	OutPut        : Returns True If string contains katakana characters else False
	Explanation   : Used to check whether the input string contains katakana characters or not.
	*****************************************************************************************/
	Function IsKatakana($sStr)
	{	
		$sAllowedChars="アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワンヲガギグゲゴザジズゼゾダヂヅデドバビブベボパピプペポァィゥェォヵヶッャュょヮ";

		If(mb_strlen($sStr) > 0 )
		{
			for ($i=0;$i<mb_strlen($sStr);$i++)
			{
				$sStr1=mb_substr($sStr,$i,1); 

				if(!is_integer(mb_strpos($sAllowedChars,$sStr1)))
				{
					return FALSE;
				}
			}
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	} 

	/*****************************************************************************************
	FunctionName  : isKanji
	Input         : String 
	OutPut        : Returns True If string is Kanji else False
	Explanation   : Used to check whether the input string is Kanji
	*****************************************************************************************/
	Function isKanji($sStr)
	{
		$sAllowedChars ="ー０１２３４５６７８９ー＿‘〜！＠＃＄％＾＆＊（）＝＋「｛」｝；’”：＜、＞。？・ABCDEFGHTJKLMNOPQRSTUVWXYZ　";
		If(mb_strlen($sStr) > 0 )
		{
			if($this->IsKatakana($sStr1)==TRUE)
			{
				return FALSE;
			}

			if($this->IsHiragana($sStr1)==TRUE)
			{
				return FALSE;
			}

			if($this->isDoubleByte($sStr1)==FALSE)
			{
				return FALSE;
			}

			for ($i=0;$i<mb_strlen($sStr);$i++)
			{
				$sStr1= mb_substr($sStr,$i,1);

				if(is_integer(strpos($sAllowedChars,$sStr1)))
				{
					return FALSE;
				}
			}
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/*****************************************************************************************
	FunctionName  : isAlphaNum
	Input         : String 
	OutPut        : Returns True If string is Alphanumeric and False not Alphanumeric
	Explanation   : Used to check whether the input string is Alphanumeric or not
	*****************************************************************************************/
	function isAlphaNum($sStr) 
	{ 
		if(ereg("^[[:alnum:]]+$", $sStr))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/*****************************************************************************************
	FunctionName  : isNum
	Input         : String 
	OutPut        : Returns True If string is numeric and False not numeric
	Explanation   : Used to check whether the input string is numeric or not
	*****************************************************************************************/
	function isNum($sStr) 
	{ 
		if(ereg("^[[:digit:]]+$", $sStr))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/*****************************************************************************************
	FunctionName  : getLength
	Input         : String 
	OutPut        : Returns length of the string passsed to this function
	Explanation   : Used to calcualte length of the string
	*****************************************************************************************/
	function getLength($str)
	{
		$len = 0;
		for ($i=0;$i<strlen($str);$i++)
		{
			if (substr($str,$i,1) <= 255 )
			{
			    $len += 1;
			}
			else
			{
				$len += 2;
			}
		}
		return $len;
	}
?>
