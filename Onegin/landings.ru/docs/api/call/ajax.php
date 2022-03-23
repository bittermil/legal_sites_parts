<?

define('IS_AJAX', true);

function setRequestData(){
	$CONTENT_TYPE = '';
	if(isset($_SERVER['HTTP_CONTENT_TYPE'])) $CONTENT_TYPE = $_SERVER['HTTP_CONTENT_TYPE'];
	if(isset($_SERVER['CONTENT_TYPE'])) $CONTENT_TYPE = $_SERVER['CONTENT_TYPE'];

	$CONTENT_TYPE = preg_replace('/;.*/', '', $CONTENT_TYPE);

	switch($CONTENT_TYPE){
		case 'application/json':
			$requestData = file_get_contents('php://input');
			if($requestData) $requestData = json_decode($requestData, true);

			if(is_null($requestData)){
				$result = [
					'result' => NULL,
					'errors' =>  [
						[
							'code' => 200,
							'string' => "Invalid Content-Type: $CONTENT_TYPE (post-data must be of the type json-string, object or http_query-string given)",
							'detail' => ''
						]
					]
				];

				echo json_encode($result);
				exit();
			}

			if(count($_POST) == 0){
				$_POST = $requestData;
				$_REQUEST = array_merge($_REQUEST, $_POST);
			}

			break;
	}
}

setRequestData();