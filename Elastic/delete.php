<?php
/**
 * @author : 김중권
 * @date   : 2020-12-17 14:37:33
 *
 * 엘라스틱서치 삭제
 */
class Elastic_delete extends ElasticSearch
{
    /**
     * 삭제 url
     *
     * @var string
     */
    public static $url;

    /**
     * 삭제 파라미터
     *
     * @var array
     */
    public static $params = [];

    // 아래는 setter
    /**
     * 삭제 파라미터로부터 url과 파라미터를 elastic_search에 변수로 입력
     *
     * @param array $deleteParams
     *
     * @return void
     */
    public static function set($deleteParams) {
        $setUrlStatus = self::setUrl($deleteParams);
        if($setUrlStatus == false) {
            return false;
        }
        return true;
    }

    /**
     * 삭제 파라미터로부터 url을 elastic_search에 변수로 입력
     *
     * @param array $deleteParams
     *
     * @return void
     */
    public static function setUrl($deleteParams) {
        if(empty($deleteParams["index"]) || empty($deleteParams["id"])) {
            return false;
        }

        // 문자가 들어온 경우 오류로 리턴처리
        if(!is_numeric($deleteParams["id"])) {
            return false;
        }

        $indexName = getFullIndexName($deleteParams["index"]);
        if($indexName == false) {
            return false;
        }

        self::$url = $indexName . "/_doc/" . $deleteParams["id"];
        logMessage("Request Url::" . self::$url, __METHOD__, __LINE__);
        return true;
    }
}