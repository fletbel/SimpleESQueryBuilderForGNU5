<?php
/**
 * @author : 김중권
 * @date   : 2020-12-17 14:37:33
 *
 * 엘라스틱서치 파라미터 builder
 */
class Elastic_params extends ElasticSearch
{
    /**
     * 그누보드 게시판 prefix
     *
     * @var string
     */
    protected $gnuPrefix = "g5_write_";

    /**
     * bo_table 변수로부터 데이터베이스 테이블명 반환
     *
     * @param string $bo_table
     *
     * @return void
     */
    public function searchParams($searchParams) {
        if(isset($searchParams["query"])) { // 쿼리자체를 입력한 경우
            return json_encode($searchParams["query"], JSON_UNESCAPED_UNICODE);
        } else {
            $indexName  = isset($searchParams["index"])     ? $searchParams["index"]    : "";
            $field      = isset($searchParams["field"])     ? $searchParams["field"]    : "";
        }
    }
}