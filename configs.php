<?php
/**
 * @author : 김중권
 * @date   : 2020-12-17
 *
 * 로드할 설정은 이곳에
 */

class Elastic_config
{
    public static $config = [
        "log_threshold" => "error"
    ];

    //2020.12.22 mj
    public static $board_type=[
        "b001"=>"community",
        "p43"=>"community",
        "freesound"=>"community",

        "givemephone"=>"info",
        "com1"=>"info",
        "com26"=>"info",

        "com21"=>"moim",
        "com22"=>"moim",
        "com23"=>"moim",
        "com24"=>"moim",

        "b002"=>"story",
        "com5"=>"story",
        "com6"=>"story",
        "com7"=>"story",
        "com8"=>"story",
        "com9"=>"story",
        "com10"=>"story",
        "com11"=>"story",
        "com12"=>"story",
        "car"=>"story",
        "com13"=>"story",
        "com14"=>"story",
        "com15"=>"story",
        "com16"=>"story",
        "com17"=>"story",
        "com18"=>"story",
        "com19"=>"story",
        "com20"=>"story",
        "travel"=>"story",

        "b003"=>"gallery",
        "com3"=>"gallery",
        
        "con1"=>"consult",
        "con3"=>"consult",

        "bbasak1"=>"bbasak",
        "bbasak2"=>"bbasak",
        "bbasak3"=>"bbasak",
        "p122"=>"bbasak",
        "p123"=>"bbasak",
        "rental"=>"bbasak",
    ];

    /* (빠삭)
    국내빠삭 - bbasak1
    해외빠삭 - bbasak2
    육아빠삭 - bbasak3
    휴대폰판매자 - p122
    통신판매자 - p123
    렌탈판매자 - rental

    (정보)
    폰살래답해줘 - givemephone
    제품구매후기 - com1
    맛집후기 - com26

    (커뮤니티)
    자유게시판 -b001
    카툰유머 -  p43
    소리공유 - freesound
    //디자인에는 가입인사,출첵게시판이 있음

    (갤러리)
    폰카갤러리 - b003
    디카갤러리 - com3

    (모임)
    지역모임 - com21
    os모임 - com22
    폰모델모임 - com23
    통신사모임 - com24

    (이야기)
    휴대폰이야기 - b002
    구매후기 - com5
    질문이야기 - com6
    제조사이야기 - com7
    생활체육이야기 - com8
    프로경기이야기 - com9
    낚시이야기 - com10
    등산이야기 - com11
    자전거이야기 - com12
    자동차 이야기 - car
    영화이야기 - com13
    책이야기 - com14
    공연이야기 - com15
    뮤직이야기 - com16
    건강이야기 - com17
    고민이야기 - com18
    공익나눔이야기 - com19
    부동산이야기 - com20
    #빠삭나눔 - nanum
    여행이야기 - travel

    (상담)
    라식라섹상담 - con3
    보험상담 - con1
    */
    public static $main_array = ['bbasak', 'info', 'community', 'gallery', 'moim', 'story', 'consult'];
    public static $board_array = [
        "bbasak" => array('bbasak1', 'bbasak2', 'bbasak3', 'p122', 'p123', 'rental'),
        "community" => array('b001', 'p43', 'freesound'),
        "info" => array('givemephone', 'com1', 'com26'),
        "noty" => array('p91', 'p92'),
        "moim" => array('com21', 'com22', 'com23', 'com24'),
        "consult" => array('con3', 'con1'),
        "story" => array(
            'b002', 'com5', 'com6', 'com7', 'com8', 'com9', 'com10', 'com11', 'com12', 
            'car', 'com13', 'com14', 'com15', 'com16', 'com17', 'com18', 
            'com19', 'com20', 'travel'
        ),
        "gallery" => array('b003', 'com3')
    ];

    public static $autocomplete = [
        "searchType" => "bs_keyword.txt,bs_keyword.chosung,bs_keyword.jamo,bs_keyword.eng",
        "operator" => "or",
        "index" => "bbasak_search_board"
    ];

    public static $notice = [
        "operator" => "and",
        "index" => "bbasak_search_notice",
        "searchType" => ["keywords"],               // ["wr_subject","keywords^2"],
        "highlight"=> ["wr_subject","keywords"],
        "name"=>"빠삭 추천"
    ];

    public static $curlErrCodes = [
        "1" => 'CURLE_UNSUPPORTED_PROTOCOL',
        "2" => 'CURLE_FAILED_INIT',
        "3" => 'CURLE_URL_MALFORMAT',
        "4" => 'CURLE_URL_MALFORMAT_USER',
        "5" => 'CURLE_COULDNT_RESOLVE_PROXY',
        "6" => 'CURLE_COULDNT_RESOLVE_HOST',
        "7" => 'CURLE_COULDNT_CONNECT',
        "8" => 'CURLE_FTP_WEIRD_SERVER_REPLY',
        "9" => 'CURLE_REMOTE_ACCESS_DENIED',
        "11" => 'CURLE_FTP_WEIRD_PASS_REPLY',
        "13" => 'CURLE_FTP_WEIRD_PASV_REPLY',
        "14"=>'CURLE_FTP_WEIRD_227_FORMAT',
        "15" => 'CURLE_FTP_CANT_GET_HOST',
        "17" => 'CURLE_FTP_COULDNT_SET_TYPE',
        "18" => 'CURLE_PARTIAL_FILE',
        "19" => 'CURLE_FTP_COULDNT_RETR_FILE',
        "21" => 'CURLE_QUOTE_ERROR',
        "22" => 'CURLE_HTTP_RETURNED_ERROR',
        "23" => 'CURLE_WRITE_ERROR',
        "25" => 'CURLE_UPLOAD_FAILED',
        "26" => 'CURLE_READ_ERROR',
        "27" => 'CURLE_OUT_OF_MEMORY',
        "28" => 'CURLE_OPERATION_TIMEDOUT',
        "30" => 'CURLE_FTP_PORT_FAILED',
        "31" => 'CURLE_FTP_COULDNT_USE_REST',
        "33" => 'CURLE_RANGE_ERROR',
        "34" => 'CURLE_HTTP_POST_ERROR',
        "35" => 'CURLE_SSL_CONNECT_ERROR',
        "36" => 'CURLE_BAD_DOWNLOAD_RESUME',
        "37" => 'CURLE_FILE_COULDNT_READ_FILE',
        "38" => 'CURLE_LDAP_CANNOT_BIND',
        "39" => 'CURLE_LDAP_SEARCH_FAILED',
        "41" => 'CURLE_FUNCTION_NOT_FOUND',
        "42" => 'CURLE_ABORTED_BY_CALLBACK',
        "43" => 'CURLE_BAD_FUNCTION_ARGUMENT',
        "45" => 'CURLE_INTERFACE_FAILED',
        "47" => 'CURLE_TOO_MANY_REDIRECTS',
        "48" => 'CURLE_UNKNOWN_TELNET_OPTION',
        "49" => 'CURLE_TELNET_OPTION_SYNTAX',
        "51" => 'CURLE_PEER_FAILED_VERIFICATION',
        "52" => 'CURLE_GOT_NOTHING',
        "53" => 'CURLE_SSL_ENGINE_NOTFOUND',
        "54" => 'CURLE_SSL_ENGINE_SETFAILED',
        "55" => 'CURLE_SEND_ERROR',
        "56" => 'CURLE_RECV_ERROR',
        "58" => 'CURLE_SSL_CERTPROBLEM',
        "59" => 'CURLE_SSL_CIPHER',
        "60" => 'CURLE_SSL_CACERT',
        "61" => 'CURLE_BAD_CONTENT_ENCODING',
        "62" => 'CURLE_LDAP_INVALID_URL',
        "63" => 'CURLE_FILESIZE_EXCEEDED',
        "64" => 'CURLE_USE_SSL_FAILED',
        "65" => 'CURLE_SEND_FAIL_REWIND',
        "66" => 'CURLE_SSL_ENGINE_INITFAILED',
        "67" => 'CURLE_LOGIN_DENIED',
        "68" => 'CURLE_TFTP_NOTFOUND',
        "69" => 'CURLE_TFTP_PERM',
        "70" => 'CURLE_REMOTE_DISK_FULL',
        "71" => 'CURLE_TFTP_ILLEGAL',
        "72" => 'CURLE_TFTP_UNKNOWNID',
        "73" => 'CURLE_REMOTE_FILE_EXISTS',
        "74" => 'CURLE_TFTP_NOSUCHUSER',
        "75" => 'CURLE_CONV_FAILED',
        "76" => 'CURLE_CONV_REQD',
        "77" => 'CURLE_SSL_CACERT_BADFILE',
        "78" => 'CURLE_REMOTE_FILE_NOT_FOUND',
        "79" => 'CURLE_SSH',
        "80" => 'CURLE_SSL_SHUTDOWN_FAILED',
        "81" => 'CURLE_AGAIN',
        "82" => 'CURLE_SSL_CRL_BADFILE',
        "83" => 'CURLE_SSL_ISSUER_ERROR',
        "84" => 'CURLE_FTP_PRET_FAILED',
        "84" => 'CURLE_FTP_PRET_FAILED',
        "85" => 'CURLE_RTSP_CSEQ_ERROR',
        "86" => 'CURLE_RTSP_SESSION_ERROR',
        "87" => 'CURLE_FTP_BAD_FILE_LIST',
        "88" => 'CURLE_CHUNK_FAILED'
    ];
}
