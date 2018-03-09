<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;

class RequestUtils {

	public static function checkPUT(Request $rq, Array $param) {
		if (!$rq->getMethod() == 'PUT')
			return false;

		return RequestUtils::checkParam($rq, $param);
	}

	public static function checkPOST(Request $rq, Array $param) {
		if (!$rq->getMethod() == 'POST')
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
