<?

$result = 0;

// Контроль ошибок БД
// после перевода всех методов на объект dbh() все ошибки должны отлавливаться исключительно в нем
$db_errorCode = dbh()->dbh->errorCode();
$db_errorInfo = dbh()->dbh->errorInfo();

$db_errorText = ($db_errorCode and $db_errorCode != '00000')?'<br/>DB Error: '.$db_errorInfo[2].' ('.$db_errorCode.')':'';
if($db_errorText) core()->error($db_errorText);

if($Exception->getMessage()) core()->error($Exception->getMessage(), NULL, $Exception->getCode());