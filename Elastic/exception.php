<?
/**
 * 엘라스틱서치 exception
 */
class Elastic_exception extends Exception
{
    /**
     * 에러 핸들러
     *
     * @param mixed $code
     * @param mixed $string
     * @param mixed $file
     * @param mixed $line
     * @param mixed $context
     */
    public static function errorHandlerCallback($code, $string, $file ="", $line ="", $context = "")
    {
        logMessage($string, $file, $line, "error");
        $params = json_encode(Elastic_search::params(), JSON_UNESCAPED_UNICODE);
        $e = new self($string . " PARAM::" . $params, $code);
        $e->line = $line;
        $e->file = $file;
        throw $e;
    }
}
