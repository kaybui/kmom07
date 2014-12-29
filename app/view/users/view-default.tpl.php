<h1><?=$title?></h1>

<p><?=$main?></p>

<p><a href='<?=$this->url->create('users/list')?>'>Back to list...</a></p>
<p><a href='<?=$this->url->create('users/delete/' . $id)?>'>Radera användare</a></p> 
