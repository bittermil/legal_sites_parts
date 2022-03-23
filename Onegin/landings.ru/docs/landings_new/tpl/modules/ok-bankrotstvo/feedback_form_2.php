<div class="form" method="post" enctype="multipart/form-data">
	<div class="title">Задать вопрос</div>
	<input name="email"  type="text"  placeholder="<?=(!$landing->authOk)?'Email':($landing->userEmail)?>" value="<?=(!$landing->authOk)?'':($landing->userEmail)?>" readonly>
	<input  name="phone"  type="text"  placeholder="Моб. тел., пример: +79304225846" <?=(!$landing->authOk)?'readonly':''?>>
	<textarea name="message" placeholder="Напишите ваш вопрос" <?=(!$landing->authOk)?'readonly':''?>></textarea>

	<div class="buttons">
		<input type="file" id="file1259" name="file" class="<?=(!$landing->authOk)?'open-pay':''?> attached" multiple/>
		<label for="file1259" class="file-label"></label>
		<button class="<?=(!$landing->authOk)?'open-pay':'button'?> blue">Отправить</button>
		<div class="file_to_send <?=(!$landing->authOk)?'open-pay':''?>"></div>
	</div>
	<div class="remove_files_to_send">Удалить файл (<span></span>)</div>
</div>

<div class="note">∗ К отправке допускается не более одного файла за раз формата .zip или .rar (вложите необходимые документы в папку, заархивируйте её и
направьте нам).</div>