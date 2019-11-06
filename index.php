<?php
//Класс с помощью которого мы работаем с передаваемым файлом - содержит методы определения последней строки, метод бин поиска и тд.
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
	
	
	private function goToString($string)
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
		
	function getString($string)
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
		else throw new \Exception('Ошибка');
	}
	//Функция бинарного поиска 
	function binarySearch($searchKey, $start=0, $end)
	{ 

		if ($end < $start) //если стартовый ключ выше поискового
        return 'Не найдено';
		$midle =  floor(($end + $start)/2); //поиск середину
		$nowString = New FileString($this->getString($midle));//получаем строку середины поскового диапазона
		
		$nowKey = $nowString->getKey();

		
		if($searchKey==$nowKey)
			{
				return 'Для ключа: '.$nowString->getKey().' значение: '.$nowString->getValue();
				
			}
		//В зависимости от результата поиск идет в 1 части или во второй 
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
			throw new \Exception('Пустая строка');
		}
	}
	function getKey()
	//Функция определяет ключ строки (те первый элемент до табуляции) 
	{
		return explode("\t",$this->str)['0'];
	}
	//Функция находит значение после табуляции в строке  
	function getValue()
	{
		return explode("\t",$this->str)['1'];
	}
	
	
}

//На вход функции search подаются ключ, по которому будет происходить поиск и имя файла, в котором будет происходить поиск 

$fileName = 'file.txt';
$searchKey = 'D1';
function search($fileName, $searchKey)
{
	$file = New FileSearcher($fileName);
	$file->getLeinght();
	$laststring = New FileString($file->getLastString());
	echo $file->binarySearch($searchKey, 0 ,$file->getLeinght());
}
search($fileName, $searchKey);
?>
