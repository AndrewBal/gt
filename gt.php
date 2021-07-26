<?php 
require '../vendor/autoload.php';
include '../../modules/gmerchantcenter/fr.php';
$_MODULE_EN = $_MODULE;
include '../../modules/gmerchantcenter/translations/ru.php';
$_MODULE_RU = $_MODULE;



// пути к файлам перевода различных модулей
$modules = scandir('../../modules/');
$modules_dir = array();
foreach($modules as $value) {
  if($value !== '.htaccess' && $value !== 'index.php' && $value !== '.' && $value !== '..' ) {
    array_push($modules_dir, $value);
  }
}
// foreach($modules_dir as $module_name_dir) {
//   $path_module = '../../modules/' . $module_name_dir . '/translations/en.php';
//   print_r($path_module . '<br/>');
// }


 
//Google переводчик
use Google\Cloud\Translate\V2\TranslateClient;
function translatemodule($value) {
  $translate = new TranslateClient([
      'key' => 'AIzaSyDWVv5-VoRzj6i-wTQEAoWLdMrmxWATFqQ'
  ]);

  $value = $translate->translate($value, ['target' => 'ru']);
  $value = $value["text"];
  $value = html_entity_decode($value);
  $value = str_replace('&#39;', '', "$value");

   $update = "$value";
  return $update;

} 





 //сохраняем оригинал  перевода
 rename('../../modules/gmerchantcenter/translations/ru.php', '../../modules/gmerchantcenter/translations/ru-dev.php');

 
 //проверка на наличие сохраненного файла оригинала для дальнейшего его сравнения с новым файлом
 try {
  $file_compare = '../../modules/gmerchantcenter/translations/ru-dev.php';
  if (!file_exists($file_compare)) {
      throw new Exception ('Файла для сравнения нет');
  }
  include $file_compare;
  $_MODULE_MANUAL_RU = $_MODULE;

} catch(Exception $e) {
  echo $e->getMessage();
}
 


//переводим и создаем новый файл
$translated_file = '../../modules/gmerchantcenter/translations/ru.php';
  $fp = fopen($translated_file, "w"); // ("r" - считывать "w" - создавать "a" - добовлять к тексту)
 
  fwrite($fp, '<?php' . PHP_EOL . 'global $_MODULE;' . PHP_EOL . '$_MODULE = array();' . PHP_EOL);


      foreach($_MODULE_EN as $key => $value) {
   
        //$chr_en = "a-zA-Z0-9\s`~!@#$%^&*()_+-={}|:;<>?,.\/\"\'\\\[\]";   /^[$chr_en]+$/
        $chr_fr = "0-9A-Za-z\ \tàâçéèêëîïôûùüÿñæœ";
        if(preg_match("/^[$chr_fr]+$/", $value)) {
           $value = translatemodule($value);
        }
    
        
        $str = '$_MODULE[\'' . $key . '\'] = \'' . $value . '\';' . PHP_EOL;
        fwrite($fp, $str);
   

  }


   
  fclose($fp);
 
  //открываем новый файл  и сравниваем его с оригиналом
  //если в оригинале уже есть значение на русском, то сохраняем данное значение в новый файл + проверка на наличие украинского языка
  //$fp = fopen($translated_file, "w");
  //fwrite($fp, '<?php' . PHP_EOL . 'global $_MODULE;' . PHP_EOL . '$_MODULE = array();' . PHP_EOL);
  //foreach($_MODULE_EN as $key => $value) {

  //}
 



// echo '<pre>';
// print_r(array_intersect($_MODULE_EN, $_MODULE_UK));
// echo '</pre>';




// if (count($_MODULE_EN) !== count($_MODULE_UK)) {
//   foreach($_MODULE_UK as $key => $value) {
//     if(!array_key_exists($key, $_MODULE_EN)) {
//       print_r($key . "  ====  " . $value);
//       echo '<br>';  
//     }
//   }

//   foreach($_MODULE_EN as $key => $value) {
//     if(!array_key_exists($key, $_MODULE_UK)) {
//       print_r($key . "  ====  " . $value);
//       echo '<br>';
//     }
//   }
// } 
