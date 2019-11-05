<!DOCTYPE html>
<html>
<head>
	
	
</head>

<body>

<?php
Class fileString
{
	private $str;
	function __construct ($str)
	{
		
		if(strlen($str)!==0)
		{
			$this->str = $str;
		}
		else
		{
			throw new \Exception('Empty String');
		}
	}
	function getKey()
	{
		return explode("\t",$this->str)['0'];
	}
	
	function getValue()
	{
		return explode("\t",$this->str)['1'];
	}
	
	
}
Class FileSearcher
{
	private $file; 
	function __construct($fileName)
	{
		$this->file = fopen($fileName, "r");
	}
	 
	function getLeinght()
	{
		$linecount=0;
		while(!feof($this->file)){
		  $line = fgets($this->file, 4096);
		  $linecount = $linecount + substr_count($line, PHP_EOL);
		}
		return $linecount;
	}
	
	
	private function goToString(int $string)
	{
		fseek($this->file,0);  // seek to 0
		$i = 0;
		$bufcarac = 0;                    
		for($i = 1;$i<$string;$i++)
			{
			$ligne = fgets($this->file);
			$bufcarac += strlen($ligne);  
			}  
		fseek($this->file,$bufcarac);
		return ($bufcarac);
	}
		
	function getString(int $string)
	{
		$bufcarac = $this->goToString($string);
		return stream_get_line($this->file, $bufcarac, "\x0A");
	}
	function GetLastString()
	{
		return $this->getString($this->getLeinght());
	}
	static function firstUp($searchKey,$key)
	{
		$arrBefore = $arr = [$searchKey,$key];
		sort($arr);
		if($arrBefore['0']===$arr['1']){return true;}
		else if($arrBefore['0']===$arr['0']){return false;}
		else throw new \Exception('Something went wrong');
	}
	
	function binarySearch($searchKey, $start=0, $end)
	{ 
		if ($end < $start) //если стартовый ключ выше поискового
        return 'undef';
		$midle =  floor(($end + $start)/2); //ищем середину
		$nowString = New FileString($this->getString($midle));//получаем строку середины поскового диапазона
		$nowKey = $nowString->getKey();//ключ текущей строки
 
		
		if($searchKey==$nowKey)
			{
				return 'for key: '.$nowString->getKey().' found value: '.$nowString->getValue();
				
			}
		else if($this->firstUp($nowKey, $searchKey))
		{
			return FileSearcher::binarySearch($searchKey, $start, $midle-1);
		}
		else if($this->firstUp($searchKey, $nowKey))
		{
			return FileSearcher::binarySearch($searchKey, $midle+1, $end);
		}
	} 
} 
$fileName = 'binarSearch10gb.txt';
$searchKey = '02e6b5e578';
function search($fileName, $searchKey)
{
	$file = New FileSearcher($fileName);
	$file->getLeinght();
	$laststring = New FileString($file->getLastString());//получаем последнюю строку
	echo $file->binarySearch($searchKey, 0 ,$file->getLeinght());
}
search($fileName, $searchKey);
?>
</body>
</html>