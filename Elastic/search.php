<?php
/**
 * @author : 김중권
 * @date   : 2020-12-17 14:37:33
 *
 * 엘라스틱서치 파라미터 builder
 * 
 * 검색어 관련 수정작업시 :
 * rest/board/search.php의 get parameter 
 *  -> Elastic_search::setParams 
 *  -> Elastic_query::mySetMethod (추가한 파라미터의 set 메소드 작성)
 *  -> Elastic_buildQuery에서 mySetMethod로 설정한 변수를 쿼리에 입력
 */
class Elastic_search extends ElasticSearch
{
    /**
     * 그누보드 게시판 prefix
     *
     * @var string
     */
    protected $gnuPrefix = "g5_write_";

    /**
     * 검색 url
     *
     * @var string
     */
    public static $url;

    /**
     * 검색 파라미터
     *
     * @var array
     */
    public static $params = [];

    // 아래는 setter
    /**
     * 검색 파라미터로부터 url과 파라미터를 elastic_search에 변수로 입력
     *
     * @param array $searchParams
     *
     * @return void
     */
    public static function set($searchParams) {
        self::setUrl($searchParams);
        self::setParams($searchParams);
        return true;
    }

    public static function setBasicSearch($query, $index){
        self::$url = $index."/". "_search";
        self::$params = $query;
    }
    
    /**
     * 검색 파라미터로부터 url을 elastic_search에 변수로 입력
     *
     * @param array $searchParams
     *
     * @return void
     */
    public static function setUrl($searchParams) {
        $indexName = "";
        
        // 2021.02.04 mj 수정 (bbasak_search_notice 추가)
        switch($searchParams["index"]){
            case "":
                $indexName = "bbasak_board_*";
                break;
            case "bbasak_search_board":
            case "bbasak_search_notice":
            case "bbasak_search_notice_v1":
                $indexName = $searchParams["index"];
                break;
            default:
                $indexName = get_board_category($searchParams["index"]);
                if( !empty($indexName["boardGroupText"]) ) { // 인덱스가 bo_table로 들어온 경우
                    $indexName = "bbasak_board_" . $indexName["boardGroupText"] . "_" . $searchParams["index"];
                } else {
                    $indexName = str_replace("bbasak_board_", "", $searchParams["index"]);
                    $indexName = str_replace("_*", "", $indexName);
                    $indexName = "bbasak_board_" . $indexName . "_*";
                }
                break;

        }
        // if(empty($searchParams["index"])) {
        //     $indexName = "bbasak_board_*";
        // } else if($searchParams["index"] != "bbasak_search_board") {
        //     $indexName = str_replace("bbasak_board_", "", $searchParams["index"]);
        //     $indexName = str_replace("_*", "", $indexName);
        //     $indexName = "bbasak_board_" . $indexName . "_*";
        // } else {
        //     $indexName = $searchParams["index"];
        // }
        $type       = isset($searchParams["type"]) ? $searchParams["type"] : "";
        $type = "/" . $type;

        self::$url = $indexName . $type . "_search";
        logMessage("Request Url::" . self::$url, __METHOD__, __LINE__);
        return true;
    }
    /**
     * 검색 파라미터로부터 source 검색조건(where 조건)을 elastic_search 변수에 입력.
     * 추가조건 설정하고싶을시 query.php에 set method를 생성하고 이 함수에서 set을 호출한 뒤,
     * buildQuery 메소드에서 set 된 값으로 build 하시면 됩니다.
     *
     * @param array $searchParams rest/board/search.php의 get parameter
     *
     * @return void
     */
    public static function setParams($searchParams) {
        $param = []; // json encode 전의 파라미터

        if(isset($searchParams["query"])) { // 쿼리자체를 입력한 경우
            // self::$params = $searchParams;
            exit;
        } else {
            // 검색테이블 설정
            Elastic_query::setSearchBoardTable($searchParams);
            // 검색필드
            Elastic_query::setSearchField($searchParams);

            // 검색 bool query 타입 지정
            Elastic_query::setBoolQueryType($searchParams);
            // 검색 단어 연산자 설정
            Elastic_query::setOperator($searchParams);
            // 검색어 설정
            Elastic_query::setSearchString($searchParams);
            // 검색어 analyzer 설정
            Elastic_query::setSearchAnalyzer($searchParams);

            // 검색날짜 설정
            Elastic_query::setSearchDate($searchParams);

            // 정렬 설정
            Elastic_query::setSort($searchParams);

            // 페이징 skip 설정
            Elastic_query::setSkip($searchParams);
            // 페이징 limit 설정
            Elastic_query::setLimit($searchParams);

            // 카테고리검색 ca_name
            Elastic_query::setCaName($searchParams);
            // 블라인드글 검색 wr_is_blind
            Elastic_query::setWrIsBlind($searchParams);
        
            // 문서개수 출력
            Elastic_query::setTotalHits();

            // 결과필드 필터링 설정
            Elastic_query::setResultField($searchParams);

            // 결과 텍스트 하이라이트 설정
            Elastic_query::setResultHighlight($searchParams);

            // 설정된 쿼리로부터 ES query 생성
            Elastic_query::buildQuery();

            // get ES query
            $param = Elastic_query::getQuery();
            self::$params = $param;
        }
    }
    
    // 아래는 getter
    /**
     * get header
     *
     * @return array
     */
    public static function header() {
        if(empty(self::$params)) { // 파라미터 있으면

        }
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