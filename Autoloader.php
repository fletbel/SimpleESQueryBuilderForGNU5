<?
/**
 * @author : 김중권
 * @date   : 2020-12-17
 *
 * 엘라스틱서치 로더
 */
include_once ELASTIC_ROOT . "/constant.php"; // 상수 include(constants로 명명하면 vs코드 문제인지 Constants로 저장되는 문제가있어 s뺏음)
include_once ELASTIC_ROOT . "/helper.php"; // 헬퍼함수 include
include_once ELASTIC_ROOT . "/configs.php"; // 설정파일 include

ElasticAutoloader::register();
Elastic_shared_str::buildCharacterSets();

class ElasticAutoloader
{
    public static function register()
    {
        if (function_exists('__autoload')) {
            spl_autoload_register('__autoload');
        }
        return spl_autoload_register(array('ElasticAutoloader', 'load'), true, true);
    }

    public static function load($pClassName)
    {
        if ((class_exists($pClassName, false)) || (strpos($pClassName, 'Elastic') !== 0)) {
            return false;
        }

        $pClassFilePath = ELASTIC_ROOT . str_replace('_', DIRECTORY_SEPARATOR, $pClassName) . '.php';
        if ((file_exists($pClassFilePath) === false) || (is_readable($pClassFilePath) === false)) {
            return false;
        }

        // require($pClassFilePath);
        include_once $pClassFilePath;
    }
}
