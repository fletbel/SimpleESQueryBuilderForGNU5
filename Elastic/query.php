<?php
/**
 * @author : 김중권
 * @date   : 2020-12-17 14:37:33
 *
 * 엘라스틱서치 쿼리스트링 builder
 */
class Elastic_query extends ElasticSearch
{
    /**
     * set 되어진 파라미터로부터 설정된 elastic query의 배열
     *
     * @var array
     */
    protected static $query = array();

    /**
     * bo_table:검색테이블
     *
     * @var string
     */
    protected static $boardTable;

    /**
     * query_string 검색필드
     *
     * @var string
     */
    protected static $searchFields;

    /**
     * query_string 검색어
     *
     * @var string
     */
    protected static $searchWord;

    /**
     * query_string 검색날짜
     *
     * @var array
     */
    protected static $searchDate;

    /**
     * query_string 정렬
     *
     * @var array
     */
    protected static $sort;

    /**
     * 검색날짜 필드명
     *
     * @var string
     */
    protected static $dateKeyword = "wr_datetime";

    /**
     * 검색결과로 나올 필드 (select)
     *
     * @var array
     */
    protected static $resultField;

    /**
     * 페이징변수 skip
     *
     * @var array
     */
    protected static $skip;
    /**
     * 페이징변수 limit
     *
     * @var array
     */
    protected static $limit;

    /**
     * 게시판 접두어, 코멘트게시판 접미어
     *
     * @var string
     */
    protected static $bo_table_prefix = "g5_write_";
    protected static $bo_table_cmt_postfix = "_cmt";

    /**
     * 검색단어연산자
     *
     * @var array
     */
    protected static $operator;

    /**
     * 검색 bool query 타입
     *
     * @var array
     */
    protected static $boolQueryType;
    
    /**
     * 검색 bool query string
     *
     * @var array
     */
    protected static $queryString = array();

    /**
     * 검색어 tokenizing 실행 할 analyzer 지정
     *
     * @var array
     */
    protected static $analyzer;

    /**
     * 검색 테이블명
     *
     * @var string
     */
    protected static $bo_table;

    /**
     * 하이라이트 on / off 플래그값
     *
     * @var string
     */
    protected static $highlight;

    /**
     * 하이라이트 태그 앞부분
     *
     * @var string
     */
    protected static $preHighlightTag;

    /**
     * 하이라이트 태그 뒷부분
     *
     * @var string
     */
    protected static $postHighlightTag;

    /**
     * 하이라이트 순서
     *
     * @var string
     */
    protected static $highlightOrder;

    /**
     * 카테고리 타입
     *
     * @var string
     */
    protected static $caName;

    /**
     * 블라인드글 타입 enum (N, Y)
     *
     * @var string
     */
    protected static $wrIsBlind;

    /**
     * 검색결과 필드 설정
     *
     * @param array $searchParams
     *
     * @return void
     */
    public static function setSearchBoard($searchParams) {
        $fields = array();
        if(!empty($searchParams["bo_table"])) {
            self::$bo_table = $searchParams["bo_table"];
        }
    }
    /**
     * 검색대상 필드 설정
     *
     * @param array $searchParams
     *
     * @return void
     */
    public static function setSearchBoardTable($searchParams) {
        if(!empty($searchParams["bo_table"])) {
            self::$boardTable = $searchParams["bo_table"];
        } 
    }

    /**
     * 검색대상 필드 설정
     *
     * @param array $searchParams
     *
     * @return void
     */
    public static function setSearchField($searchParams) {
        if(!empty($searchParams["searchType"])) {
            self::$searchFields = $searchParams["searchType"];
        } else {
            self::$searchFields = ["wr_content", "wr_subject"];
        }
        self::$queryString["fields"] = self::$searchFields;
    }

    /**
     * query_string 검색어 설정
     *
     * @param array $searchParams
     *
     * @return void
     */
    public static function setSearchString($searchParams) {
        $query = $searchParams["searchWord"]; // 기본 검색어
        self::$searchWord = $searchParams["searchWord"];
        // 반드시 포함
        if (!empty($searchParams["searchWordRequired"])) {
            foreach ($searchParams["searchWordRequired"] as $row) {
                $row = trim($row);
                if (!empty($row)) { // 사용자가 공백을 잘못 입력하여 loop가 도는 경우 처리
                    if (empty($query) || (trim($query) == "")) {
                        $query = $row;
                    } else {
                        $query .= " AND $row";
                    }
                }
            }
        }

        // 정확하게 일치
        if (!empty($searchParams["searchWordEquals"])) {
            foreach ($searchParams["searchWordEquals"] as $row) {
                $row = trim($row);
                if (!empty($row)) { // 사용자가 공백을 잘못 입력하여 loop가 도는 경우 처리
                    if (empty($query) || (trim($query) == "")) {
                        $query = "\"$row\"";
                    } else {
                        $query .= " AND \"$row\"";
                    }
                }
            }
        }

        $exceptQuery = "";
        // 제외
        if (!empty($searchParams["searchWordExcepts"])) {
            foreach ($searchParams["searchWordExcepts"] as $row) {
                $row = trim($row);
                if (empty($exceptQuery)) { // 첫 번째 입력이면 AND붙이지 않음
                    $exceptQuery .= " NOT (" . $row;
                } else { 
                    $exceptQuery .= " AND" . $row;
                }
            }
            $exceptQuery .= ")"; // NOT 시작괄호 닫음
            $query .= $exceptQuery;
            unset($exceptQuery);
        }

        //2021.01.14 mj 영문 조회 포함
        if(!empty($searchParams["engWord"])){
            $query .= " OR ".$searchParams["engWord"];
        }

        //2021.02.08 kjk 한글 조회 포함
        if(!empty($searchParams["korWord"])){
            $query .= " OR ".$searchParams["korWord"];
        }

        // 검색어 최종 입력
        // array_push(self::$queryString, array("query" => $query));
        self::$queryString["query"] = $query;
    }
    
    /**
     * query_string 검색날짜 설정
     *
     * @param array $searchParams
     *
     * @return void
     */
    public static function setSearchDate($searchParams) {
        $dateSearch = array();
        if(!empty($searchParams["dateStart"])) { // 기간시작 옵션이 존재하면
            $dateSearch["gte"] = $searchParams["dateStart"];
        }
        if(!empty($searchParams["dateEnd"])) { // 기간끝 옵션이 존재하면
            $dateSearch["lte"] = $searchParams["dateEnd"];
        }

        self::$searchDate = $dateSearch;
    }

    /**
     * 검색결과 필드 설정
     *
     * @param array $searchParams
     *
     * @return void
     */
    public static function setResultField($searchParams) {
        if(!empty($searchParams["resultField"])) {
            self::$query["_source"] = $searchParams["resultField"];
        }
    }

    /**
     * 검색결과 텍스트 하이라이트 설정
     *
     * @param array $searchParams
     *
     * @return void
     */
    public static function setResultHighlight($searchParams) {
        if($searchParams["highlight"] == true) {
            $highlightFields = array();
            $highlightFieldsArray = $searchParams["highlightFields"];
            foreach($highlightFieldsArray as $row) {
                array_push(
                    $highlightFields, 
                    array(
                        $row => array(
                            "fragment_size" => 40,
                            "number_of_fragments" => 10,
                            "options" => array(
                                "return_offsets" => true
                            )
                        )
                    )
                );
            }
            self::$highlight = array(
                "pre_tags" => array( 0 => $searchParams["preHighlight"]),
                "post_tags" => array( 0 => $searchParams["postHighlight"]),
                "fields" => $highlightFields,
                "order" => $searchParams["highlightOrder"]
            );
            self::$query["highlight"] = self::$highlight;
        }
    }

    /**
     * 검색시 디폴트 쿼리연산자(띄어쓰기를 구분자로하는 단어를 잘라서 or 혹은 and의 연산자로 검색)
     *
     * @param array $searchParams
     *
     * @return void
     */
    public static function setOperator($searchParams) {
        if (empty($searchParams["operator"])) {
            self::$operator = "and";
        } else {
            self::$operator = $searchParams["operator"];
        }
        // array_push(self::$queryString, array("default_operator" => self::$operator));
        self::$queryString["default_operator"] = self::$operator;
    }
    
    /**
     * 검색시 디폴트 쿼리연산자(띄어쓰기를 구분자로하는 단어를 잘라서 or 혹은 and의 연산자로 검색)
     *
     * @param array $searchParams
     *
     * @return void
     */
    public static function setSearchAnalyzer($searchParams) {
        if (!empty($searchParams["analyzer"])) {
            self::$analyzer = $searchParams["analyzer"];
            self::$queryString["analyzer"] = self::$analyzer;
        } 
    }

    /**
     * 검색시 bool query의 타입 설정. (should, must)
     *
     * @param array $searchParams
     *
     * @return void
     */
    public static function setBoolQueryType($searchParams) {
        if (empty($searchParams["boolQueryType"])) {
            self::$boolQueryType = "must";
        } else {
            self::$boolQueryType = $searchParams["boolQueryType"];
        }
    }

    /**
     * query_string 정렬 설정
     *
     * @param array $searchParams
     *
     * @return void
     */
    public static function setSort($searchParams) {
        if(!empty($searchParams["sortWord"])) { // 정렬타입이 존재하면
            $searchParams["sortType"] = empty($searchParams["sortType"]) ? "desc" : $searchParams["sortType"];
            self::$query["sort"] = array(
                $searchParams["sortWord"] => $searchParams["sortType"]
            );
        }
    }
    
    /**
     * 페이징 skip 변수 설정
     *
     * @param array $searchParams
     *
     * @return void
     */
    public static function setSkip($searchParams) {
        $skip = 0;
        if(!empty($searchParams["skip"])) { // skip 변수가 있으면
            if($searchParams["skip"] == "NaN") {
                $searchParams["skip"] = 0;
            }
            if(!is_numeric($searchParams["skip"])) {
                logMessage($searchParams["skip"], __METHOD__, __LINE__, SET_CUSTOM_DEBUG_THRESHOLD);
                throw Elastic_exception::errorHandlerCallback(
                    ELASTIC_WRONG_PARAMETER_EXCEPTION_CODE, 
                    "Wrong parameter exception :: searchParams['skip']",
                    __FILE__,
                    __LINE__,
                    __METHOD__
                );
            }
            self::$query["from"] = $searchParams["skip"];
        }
    }

    /**
     * 페이징 limit 변수 설정
     *
     * @param array $searchParams
     *
     * @return void
     */
    public static function setLimit($searchParams) {
        if(!empty($searchParams["limit"])) { // limit 변수가 없으면
            if($searchParams["limit"] == "NaN") {
                $searchParams["limit"] = 5;
            }
            if(!is_numeric($searchParams["limit"])) {
                throw Elastic_exception::errorHandlerCallback(
                    ELASTIC_WRONG_PARAMETER_EXCEPTION_CODE, 
                    "Wrong parameter exception :: searchParams['limit']",
                    __FILE__,
                    __LINE__,
                    __METHOD__
                );
            }
            self::$query["size"] = $searchParams["limit"];
        }
    }

    /**
     * 카테고리 검색 기능
     *
     * @param array $caName
     *
     * @return void
     */
    public static function setCaName($searchParams) {
        if(!empty($searchParams["ca_name"])) { // ca_name 변수가 있으면
            self::$caName = $searchParams["ca_name"];
        }
    }

    /**
     * 블라인드 글 검색 기능
     *
     * @param array $searchParams
     *
     * @return void
     */
    public static function setWrIsBlind($searchParams) {
        if(!empty($searchParams["wr_is_blind"])) {
            $wrIsBlind = $searchParams["wr_is_blind"];

            if(gettype($wrIsBlind) !== "string") {
                return false;
            }
            $wrIsBlind = strtoupper($wrIsBlind);

            if($wrIsBlind === "Y" || $wrIsBlind === "N") {
                self::$wrIsBlind = $wrIsBlind;
            }
        }
    }
    /**
     * 페이징을 위한 문서 개수 출력
     *
     * @return void
     */
    public static function setTotalHits() {
        self::$query["track_total_hits"] = true;
    }


    /** bool query builder */
    public static function getBoolQueryString($query, $fields = []) {
        return array(
            "query_string" => array(
                "query" => $query,
                "fields" => $fields
            )
        );
    }
    /** end of bool query builder */

    /**
     * set 된 검색어, 정렬, 검색필드, 검색날짜로 부터 검색쿼리를 생성
     *
     * @return bool
     */
    public static function buildQuery() {
        $tempArray = array();
        $query = array();
        $match = "";

        // 카테고리가 비어있고 블라인드가 존재
        if(empty(self::$caName) && !empty(self::$wrIsBlind)) {
            $match = '[
                {
                    "match":{
                        "wr_is_blind": {
                            "query":"' . self::$wrIsBlind . '",
                            "operator" : "and"
                        }
                    }
                }
            ]';
            $query = json_decode($match, true);
        } elseif(!empty(self::$caName) && empty(self::$wrIsBlind)) { // 카테고리만 존재하는 경우
            $match = '[
                {
                    "match":{
                        "ca_name": {
                            "query":"' . self::$caName . '",
                            "operator" : "and"
                        }
                    }
                }
            ]';
            $query = json_decode($match, true);
        } elseif(!empty(self::$caName) && !empty(self::$wrIsBlind)) { // 카테고리, 블라인드 둘 다 존재하면
            $match = '[
                {
                    "match":{
                        "wr_is_blind": {
                            "query":"' . self::$wrIsBlind . '",
                            "operator" : "and"
                        }
                    }
                },
                {
                    "match":{
                        "ca_name": {
                            "query":"' . self::$caName . '",
                            "operator" : "and"
                        }
                    }
                }
            ]';
            $query = json_decode($match, true);
        } else { // 둘 다 비어있으면 skip
        }
       
        if(self::$boolQueryType == "must") {
            
            // 검색날짜
            if (!empty(self::$searchDate)) {
                $tempArray["range"] = array(self::$dateKeyword => self::$searchDate);
            }
            // 검색테이블
            if (!empty(self::$boardTable)) {
                $tempArray["term"] = array("bo_table" => self::$boardTable);
            }

            if(!empty($tempArray)) {
                array_push($query, $tempArray);
            }
            
            if(!empty(self::$searchWord)) {
                $tempArray = array("query_string" => self::$queryString);
                if(!empty($tempArray)) {
                    array_push($query, $tempArray);
                }
            }
            
            self::$query["query"]["bool"]["must"] = $query;
        } else {
            // 검색날짜
            if (!empty(self::$searchDate)) {
                $tempArray["range"] = array(self::$dateKeyword => self::$searchDate);
            }
            // 검색테이블
            if (!empty(self::$boardTable)) {
                $tempArray["term"] = array("bo_table" => self::$boardTable);
            }

            if(!empty($tempArray)) {
                array_push($query, $tempArray);
            }

            self::$query["query"]["bool"]["must"] = $query;

            if(!empty(self::$searchWord)) {
                $tempArray = array("query_string" => self::$queryString);
                if(!empty($tempArray)) {
                    array_push($query, $tempArray);
                }
            }
            
            self::$query["query"]["bool"]["should"] = $query;
            logMessage("should self::searchDate::" . self::$searchDate);
        }
        // // 검색어
        // ${self::$boolQueryType."Array"}[0]["query_string"] = self::$queryString;

        // // 검색어가 없거나 공백일시 검색조건은 아예 빼버림
        // if(!empty(self::$searchWord) && !empty($shouldArray)) {
        //     self::$query["query"]["bool"]["should"] = $shouldArray;
        // }
        logMessage("ES Request Query:: " . json_encode(self::$query), __METHOD__, __LINE__);
        return true;
    }

    /**
     * set 된 검색어, 정렬, 검색필드, 검색날짜로 부터 검색쿼리를 생성
     *
     * @return bool
     */
    public static function buildAutocompleteQuery() {
        $mustArray = array();
        $shouldArray = array();

        // 검색날짜
        if (!empty(self::$searchDate) && !empty(self::$dateKeyword)) {
            array_push($mustArray, array("range" => array(self::$dateKeyword => self::$searchDate)));
        }

        // 검색어
        ${self::$boolQueryType."Array"}[0]["query_string"] = self::$queryString;
        // array_push($queryStringArr, self::$queryString);
        // array_push(${self::$boolQueryType."Array"}, self::$queryString);

        if(!empty($shouldArray)) {
            self::$query["query"]["bool"]["should"] = $shouldArray;
        }
        if(!empty($mustArray)) {
            self::$query["query"]["bool"]["must"] = $mustArray;
        }
        return true;
    }
    /**
     * 생성된 쿼리를 리턴
     *
     * @return void
     */
    public static function getQuery() {
        return self::$query;
    }
}