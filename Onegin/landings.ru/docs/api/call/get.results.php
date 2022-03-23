<?

if(!$limit) $limit = 10000;

// пагинация
if(is_array($result) and isset($result[0]) and is_array($result[0]) and isset($result[0]['total'])){
	$total = (int)$result[0]['total'];
	array_shift($result);
}
if(!is_null(core()->resultTotal)) $total = core()->resultTotal;
if(!isset($total)) $total = (int)dbh()->query('SELECT FOUND_ROWS()')->fetchColumn();

if(!isset($offset)) $offset = 0;
$nextOffset = $offset + $limit;
// /пагинация

$response = new stdClass();
$response->result = $result;

if(core()->errors) $response->errors = core()->errors;
if(core()->messages) $response->messages = core()->messages;

if(isset($nextOffset) and $nextOffset < $total){
	$response->nextOffset = $nextOffset;
	$response->total = $total;
}

if(core()->resultMeta) $response->meta = core()->resultMeta;

echo json_encode($response, JSON_UNESCAPED_UNICODE);

return;

if(!isset($result)) return;