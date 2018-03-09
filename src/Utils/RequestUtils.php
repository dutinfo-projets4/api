<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;

class RequestUtils {

	public static function checkGET(Request $rq, Array $param) {
		return RequestUtils::checkMethod($rq, 'GET', $param);
	}

	public static function checkPOST(Request $rq, Array $param) {
		return RequestUtils::checkMethod($rq, 'POST', $param);
	}
	public static function checkPUT(Request $rq, Array $param) {
		return RequestUtils::checkMethod($rq, 'PUT', $param);
	}

	public static function checkDELETE(Request $rq, Array $param) {
		return RequestUtils::checkMethod($rq, 'DELETE', $param);
	}

	public static function checkPATCH(Request $rq, Array $param) {
		return RequestUtils::checkMethod($rq, 'PATCH', $param);
	}

	private static function checkMethod(Request $rq, string $method, Array $param) {
		if (!$rq->getMethod() == $method)
			return false;

		return RequestUtils::checkParam($rq, $param);
	}

	private static function checkParam(Request $rq, Array $param){
		$wrongParam = false;

		foreach($param as $p){
			$wrongParam = empty($rq->query->get($p));

			if ($wrongParam) break;
		}
		return !$wrongParam;
	}

}
