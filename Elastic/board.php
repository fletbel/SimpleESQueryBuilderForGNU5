<?php
/**
 * @author : 김중권
 * @date   : 2020-12-17 14:37:33
 *
 * 엘라스틱서치 게시판
 */
class Elastic_board extends ElasticSearch
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
    public function getBbasakTableName($bo_table) {
        return $this->gnuPrefix . $bo_table;
    }
}