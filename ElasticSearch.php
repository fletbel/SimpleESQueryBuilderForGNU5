<?
/**
 * @author : 김중권
 * @date   : 2020-12-17
 *
 * php 엘라스틱서치
 */
define("ELASTIC_ROOT", dirname(__FILE__) . '/');
try {
    include_once ELASTIC_ROOT . "Autoloader.php";
} catch (Exception $e) {
    die($e->getMessage()); // 익셉션 발생시 에러출력후 종료
}

class ElasticSearch {
    /**
     * curl 에러 메세지
     *
     * @var $errorMsg
     */
    protected $errorMsg;
    /**
     * 마지막 결과값
     *
     * @var $lastResult
     */
    protected $lastResult;

    /**
     * 기본 호출 url
     *
     * @var array
     */
    protected $__staticUrl = [
        "showPluginList" => "_nodes/plugins?pretty"
    ];

    /**
     * curl 통신으로 elasticsearch 호출하는 함수
     *
     * @param string $url
     *
     * @return array
     */
    protected function _call($url = "", $callType = "") {
        /** 변수선언 */
        $result = ""; // curl result
        $ch     = ""; // curi init param
        $type   = "";
        
        // calltype이 set 되어져 호출되었으면 this->call type, 아닌 경우 인풋 $calltype 파라미터로
        $url = empty($url) ? ELASTIC_URL ."/" . Elastic_search::$url : ELASTIC_URL ."/" . $url;
        logMessage($url, __METHOD__, __LINE__);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   
        // 호출 타입별 메소드타입을 구분해줘야 할 때 
        if($callType == "insert") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        } else if ($callType == "search") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HTTPHEADER, Elastic_search::header());
            if (!empty(Elastic_search::params())) { // 파라미터가 있는 경우
                $param = json_encode(Elastic_search::params(), JSON_UNESCAPED_UNICODE);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
            }
        } else if($callType == "delete"){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_HTTPHEADER, Elastic_search::header());
        }
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); // 콘솔에서 -u username:password
        curl_setopt($ch, CURLOPT_FAILONERROR , true);
        curl_setopt($ch, CURLOPT_USERPWD, ELASTIC_USER);
        try {        
            $result = curl_exec($ch);
            $errNo = curl_errno($ch);
            if ($errNo) {
                $this->errorMsg = curl_error($ch);
                curl_close($ch);
                throw Elastic_exception::errorHandlerCallback(
                    ELASTIC_CURL_ERROR_CODE, 
                    $this->errorMsg . " ERR NO::" . $errNo . " MSG::" . Elastic_config::$curlErrCodes[$errNo],
                    __FILE__,
                    __LINE__,
                    __METHOD__
                );
                return false;
            } else {
                curl_close($ch);
            }
            
        } catch (Elastic_exception $e) {
            logMessage($e->getMessage(), __METHOD__, __LINE__, "error");
            // return error
            return returnElasticData($e->getMessage(), $e->getCode(), false);
        }
        
        if($callType == "insert") {
            logMessage(json_encode(Elastic_search::params(), JSON_UNESCAPED_UNICODE), __METHOD__, __LINE__);
            $result = json_decode("[" . $result . "]", true);
            return returnElasticData($result, 1, true);
        } else if ($callType == "search") {
            logMessage(json_encode(Elastic_search::params(), JSON_UNESCAPED_UNICODE), __METHOD__, __LINE__);
            $result = json_decode("[" . $result . "]", true);
            return returnElasticData($result, 1, true);
        } else if($callType == "delete"){
            $result = json_decode("[" . $result . "]", true);
            $success = $result[0]["_shards"]["successful"];
            logMessage($success . ", " . json_encode($result), __METHOD__, __LINE__);
            return returnElasticData($result, 1, true);
        }
    }
    
    public function getEsQuery() {
        return json_encode(Elastic_search::params(), JSON_UNESCAPED_UNICODE);
    }
    /**
     * 마지막 결과값 가져오는 함수
     *
     * @param string $type
     *
     * @return mixed
     */
    public function getResult($type = "array") {
        return $this->lastResult[$type];
    }

    /** 필요한 메소드는 아래에 작성 */

    /**
     * elasticsearch 호출 함수
     * _call과 다른방식으로 호출하는 방법이 생긴 경우, 
     * 이 public call 함수에 분기로 작성하거나 _call을 다른함수로 대체하기 위함
     * 
     * @param string $url
     *
     * @return array
     */
    public function call($url, $callType) {
        return $this->_call($url, $callType);
    }

    /**
     * 검색
     *
     * @param array $searchParams 검색 데이터
     *
     * @return array
     */
    public function search($searchParams) {
        // Elastic_search 클래스에서 파라미터를 set
        Elastic_search::set($searchParams);
        $this->url = Elastic_search::$url;
        $this->printHeader();
        return $this->_call($this->url, __FUNCTION__);
    }

    /**
     * 문서 개수와 페이징 변수 리턴
     *
     * @param array $countParams 카운트 쿼리
     *
     * @return array
     */
    public function count($countParams) {
        Elastic_count::set($countParams);
        $this->url = Elastic_count::$url;
        $this->printHeader();
        return $this->_call($this->url, __FUNCTION__);
    }
    /**
     * 자동완성?
     * 
     * @param array $autocompleteParams 자동완성 데이터
     *
     * @return array
     * 
     * @date    : 2021-01-22
     * @content : search에서 자동완성 인덱스를 검색하므로 이 메소드 안쓰게 됨
     */
    public function autocomplete($autocompleteParams) {
        // Elastic_autocomplete 클래스에서 파라미터를 set
        Elastic_autocomplete::set($autocompleteParams);
        $this->url = Elastic_autocomplete::$url;
        $this->printHeader();
        return $this->_call($this->url, __FUNCTION__);
    }

    public function searchBasic($query, $index) {
        // Elastic_search 클래스에서 파라미터를 set
        Elastic_search::setBasicSearch($query, $index);
        $this->url =Elastic_search::$url;
        $this->printHeader();
        return $this->_call($this->url, "search");
    }
    
    public function insert() {

    }

    public function replace() {

    }

    /**
     * 삭제
     *
     * @param array $searchParams 검색 데이터
     *
     * @return array
     */
    public function delete($deleteParams) {
        $setStatus = Elastic_delete::set($deleteParams);
        if(!$setStatus) {
            return returnElasticData("Wrong Input Parameter", -7, false);
        }

        $this->url = Elastic_delete::$url;
        $this->printHeader();
        return $this->_call($this->url, __FUNCTION__);
    }
    
    /*
        configs.php $board_type에 define된 index id 찾기
        2020.12.22 mj
    */
    public function findIndex($bo_table="b001"){
        $board_type = Elastic_config::$board_type; // 예 : b001 == community

        $exists = array_key_exists ($bo_table, $board_type);
        
        if($exists){
            $key = $board_type[$bo_table];
            return $key;
        }
        
        return "";
    }

    // b001 -> bbasak_board_community_b001 , use_static_name == ture인 경우 넣은 값 그대로
    public function get_full_index_name($bo_table, $use_static_name=false){

        if($use_static_name){
            return $bo_table;
        }

        $prefix = 'bbasak_board_';
        $arr = Elastic_config::$board_array;
        $index_prefiex = $this->get_key_arrays($arr, $prefix.$bo_table);

        $middle = strlen($index_prefix) > -1 ? $index_prefiex."_" : "";
        
        $result_index = $prefix.$middle.$bo_table;

        return $result_index;
    }

    //다중 array에서 key를 찾음. 2021.01.12 mj (config에 있는 데이터 추적)
    protected function get_key_arrays($array, $find_value){
        foreach($array as $keys=>$values){
            foreach($values as $key=>$value){
                if($tb_name === $value){
                    return $keys;
                }
            }
        
        }
        return "";
    }

    /*
        검색 관련한 g5_write_* 테이블의 record 삭제시 반드시 호출해야 합니다.
        2020.12.22 mj
    */
    public function deleteRecord($params){

        $index = $params["index"];
        $id = $params["wr_id"];
        $url = $index."/_doc/".$id;

        //$url = $params["bo_table"]."/_doc/".$params["wr_id"];
        
        $this->url = $url;  //Elastic_search::$url;
        $this->printHeader();
        echo json_encode( $this->url);
        //return $this->_call($this->url, "DELETE");
        return $this->_call($this->url, "DELETE");
        /*
        if(!empty($wr_id)){
            $index = $this->findIndex($bo_table, $wr_id);
            //DELETE /twitter/_doc/1

            if(!empty($index)){
                $index = "bbasak_board_".$index;



            }else{
                json_return(false, "elastic search에 해당 인덱스는 존재하지 않습니다.");
            }
            

        }
        else{
            json_return(false, "id값이 없습니다.");
        }
        */
    }

    /**
     * 마지막오류메세지 출력
     *
     * @return string
     */
    public function getLastError() {
        return $this->errorMsg;
    }

    /**
     * 플러그인 목록 출력
     *
     * @return string
     */
    public function showPluginList() {
        $url = $this->__staticUrl["showPluginList"];
        return $this->call($url);
    }

    /**
     * 로깅 함수
     *
     * @param string $msg 로그 메세지
     * @param string $threshold threshold
     * @param string $method 호출 메소드
     * @param string $line 발생 라인
     *
     * @return void
     */
    public static function log($msg, $threshold = "DEBUG", $method = "NULL", $line = "NULL") {
        $ElasticLog = new Elastic_log();
        $threshold = strtoupper($threshold); // upper이어야 매핑이 됨

        if($ElasticLog->getlogType() == "file") {
            $msg .= " AT METHOD::" . $method . ", LINE::" . $line;
            $ElasticLog->write_log($threshold, $msg);
        } else { // 로그방식을 파일외의 다른것으로 이용시 constant에 define값 변경 후 이곳에 추가

        }
    }

    /**
     * 출력 결과 utf-8 설정
     *
     * @return void
     */
    public function printHeader() {
        header("Content-type: text/html; charset=utf-8");
    }

}