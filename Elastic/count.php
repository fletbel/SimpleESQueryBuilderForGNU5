<?php
/** 
 * @author     : 김중권
 * @date       : 2021-01-22
 * @brief      : 문서 카운트 출력
 */ 
class Elastic_count extends ElasticSearch
{
    /**
     * url
     *
     * @var string
     */
    public static $url;

    /**
     * 파라미터
     *
     * @var array
     */
    public static $params = [];

    // 아래는 setter
    /**
     * 파라미터로부터 url과 파라미터를 elastic_search에 변수로 입력
     *
     * @param array $searchParams
     *
     * @return void
     */
    public static function set($searchParams) {
        self::setUrl($searchParams);
        return true;
    }

    /**
     * 파라미터로부터 url을 elastic_search에 변수로 입력
     *
     * @param array $searchParams
     *
     * @return void
     */
    public static function setUrl($searchParams) {
        $indexName = "";
        if(empty($searchParams["index"])) {
            $indexName = "bbasak_board_*";
        } else {
            $indexName = str_replace("bbasak_board_", "", $searchParams["index"]);
            $indexName = str_replace("_*", "", $indexName);
            $indexName = "bbasak_board_" . $indexName . "_*";
            logMessage($indexName, __METHOD__, __LINE__);
        }
        $type       = isset($searchParams["type"]) ? $searchParams["type"] : "";
        $type = "/" . $type;
        self::$url = $indexName . $type . "_count";
        return true;
    }

    // 아래는 getter
    /**
     * get header
     *
     * @return array
     */
    public static function header() {
        return array(
            'Content-Type: application/json; charset=utf-8'
        );
    }
    /**
     * get params
     *
     * @return array
     */
    public static function params() {
        return self::$params;
    }
}