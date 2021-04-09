<?

if ( ! function_exists('is_really_writable'))
{
    /**
     * 파일쓰기 가능한지 확인하는 함수 (ci )
     * 
	 * @param	string
	 * @return	bool
	 */
	function is_really_writable($file)
	{
		// If we're on a Unix server with safe_mode off we call is_writable
		if (DIRECTORY_SEPARATOR === '/' && (is_php('5.4') OR ! ini_get('safe_mode')))
		{
			return is_writable($file);
		}

		/* For Windows servers and safe_mode "on" installations we'll actually
		 * write a file then read it. Bah...
		 */
		if (is_dir($file))
		{
			$file = rtrim($file, '/').'/'.md5(mt_rand());
			if (($fp = @fopen($file, 'ab')) === FALSE)
			{
				return FALSE;
			}

			fclose($fp);
			@chmod($file, 0777);
			@unlink($file);
			return TRUE;
		}
		elseif ( ! is_file($file) OR ($fp = @fopen($file, 'ab')) === FALSE)
		{
			return FALSE;
		}

		fclose($fp);
		return TRUE;
	}
}


if ( ! function_exists('is_php'))
{
	/**
	 * Determines if the current version of PHP is equal to or greater than the supplied value
	 *
	 * @param	string
	 * @return	bool	TRUE if the current version is $version or higher
	 */
	function is_php($version)
	{
		static $_is_php;
		$version = (string) $version;

		if ( ! isset($_is_php[$version]))
		{
			$_is_php[$version] = version_compare(PHP_VERSION, $version, '>=');
		}

		return $_is_php[$version];
	}
}

if (!function_exists("returnElasticData")) {
    /**
     * - input $msg, $returnVal = 1, $success = true, $result="", $reason=""
     * - return array message
     * - code 1: 성공, 
     * - status true:성공, false:실패
     * - result 결과값, reason 실패이유
     * 
     * 
     */
    function returnElasticData($msg, $returnVal = 1, $success = true, $result="", $reason="")
    {
        $return = ['status'=> $success, 'code' => $returnVal, 'result'=>['message' => $msg]];
        if ($result != "") {
            $return['result']['data'] = $result;
        }
        if ($reason != "") {
            $return['result']['reason'] = $reason;
        }

        if(isset($_GET["requestType"]) && $_GET["requestType"] == "api") {
            return json_encode($return, JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode($return, JSON_UNESCAPED_UNICODE);
        }
        // exit;
    }
}

if (!function_exists("returnHeaderMessage")) {
    /**
     * returnElasticData를 header에 넣어서 리턴
     *
     * @param integer $httpStatusCode http 상태코드
     * @param string $msg 메세지
     * @param integer $returnVal 리턴 코드
     * @param boolean $success 통신 상태
     * @param string $result 결과값
     * @param string $reason 실패이유
     * @return void
     */
    function returnHeaderMessage($httpStatusCode = 200, $msg = "성공.", $returnVal = 1, $success = true, $result="", $reason="")
    {
        $return = ['status'=> $success, 'code' => $returnVal, 'result'=>['message' => $msg]];
        if ($result != "") {
            $return['result']['data'] = $result;
        }
        if ($reason != "") {
            $return['result']['reason'] = $reason;
        }
        $return = json_encode($return);
        
        http_response_code($httpStatusCode);
        header("returnElasticData:".$return);
        exit;
    }
}

if (!function_exists("returnStatusCode")) {
    /**
     * 상태코드 설정 후 die with 메세지
     *
     * @param integer $code
     * @param string $msg
     * @return void
     */
    function returnStatusCode($code = 200, $msg = "")
    {
        if(empty($code)) {
            die($msg);
        }
        http_response_code($code);
        die($msg);
    }
}

if (!function_exists("logMessage")) {
    /**
     * 상태코드 설정 후 die with 메세지
     *
     * @param integer $code
     * @param string $msg
     * @return void
     */
    function logMessage($msg, $method = "NULL", $line = "NULL", $threshold = "DEBUG")
    {
		ElasticSearch::log($msg, $threshold, $method, $line);
    }
}


if (!function_exists("getFullIndexName")) {
    /**
     * 그누보드 bo_table변수로부터 인덱스이름을 리턴함
     *
     * @param string $bo_table
     * @return string
     */
    function getFullIndexName($bo_table)
    {
        $indexName = "";
        switch ($bo_table) {
            case "bbasak_search_notice": //빠삭 추천 삭제 추가 2021.02.09 mj
            case "bbasak_search_board":
            case "test_bbasak_search_board"://테스트용 2021.03.24 mj
                $indexName = $bo_table;
                break;
            case "b001": case "p43": case "freesound": // 커뮤니티
                $indexName .= "bbasak_board_community_" . $bo_table;
                break;
            case "givemephone": case "com1": case "com26":  // 정보
                $indexName .= "bbasak_board_info_" . $bo_table;
                break;
            case "com21": case "com22": case "com23": case "com24": // 모임
                $indexName .= "bbasak_board_moim_" . $bo_table;
                break;
            case "bbasak1": case "bbasak2": case "bbasak3": case "p122": case "p123": case "rental":  // 빠삭
                $indexName .= "bbasak_board_bbasak_" . $bo_table;
                break;
            case "con3": case "con1":  // 상담
                $indexName .= "bbasak_board_consult_" . $bo_table;
                break;
            case "b002": case "com5": case "com6": case "com7": case "com8": case "com9": case "com10":
            case "com11": case "com12": case "car": case "com13": case "com14": case "com15": case "com16":
            case "com17": case "com18": case "com19": case "com20": case "travel":   // 이야기
                $indexName .= "bbasak_board_story_" . $bo_table;
                break;
            case "b003": case "com3": // 갤러리
                $indexName .= "bbasak_board_gallery_" . $bo_table;
                break;
            default: 
                $indexName = false; // 포함안된 경우 익셉션 처리
        }
        return $indexName;
    }
}


if (!function_exists("get_board_category")) {
    /**
     * 게시판 카테고리로부터 엘라스틱서치 boardGroup 파라미터 생성
     *
     * @author : 김중권
     * @date   : 2021-02-19 16:04:48
     *
     * @param string $bo_table
     *
     * @return string
     */
    function get_board_category($bo_table)
    {
        switch ($bo_table) {
            case "b001": case "p43": case "freesound":
                return array("name" => "커뮤니티", "boardGroupText" => "community");
            break;
            case "givemephone": case "com1": case "com26":
                return array("name" => "정보", "boardGroupText" => "info");
            break;
            case "com21": case "com22": case "com23": case "com24":
                return array("name" => "모임", "boardGroupText" => "moim");
            break;
            case "bbasak1": case "bbasak2": case "bbasak3": case "p122": case "p123": case "rental":
                return array("name" => "빠삭", "boardGroupText" => "bbasak");
            break;
            case "con3": case "con1":
                return array("name" => "상담", "boardGroupText" => "consult");
            break;
            case "b002": case "com5": case "com6": case "com7": case "com8": case "com9": case "com10":
            case "com11": case "com12": case "car": case "com13": case "com14": case "com15": case "com16":
            case "com17": case "com18": case "com19": case "com20": case "travel":
                return array("name" => "이야기", "boardGroupText" => "story");
            break;
            case "b003": case "com3":
                return array("name" => "갤러리", "boardGroupText" => "gallery");
            break;
            default:
                return array("name" => "", "boardGroupText" => "");
        }
    }
}