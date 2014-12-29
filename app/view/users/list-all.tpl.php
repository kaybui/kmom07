<h1><?=$title?></h1>

<table style='width: 100%; text-align: left;'>
<div id="flash">
  
</div>
<tr>
    <th><?='Id'?></th>
    <th><?='Acronym'?></th>
    <th><?='Name'?></th>
    <th><?='Email'?></th>
    <th><?='Active'?></th>
    <th><?='Deleted'?></th>
</tr>

<?php foreach ($users as $user) : ?>
<tr>
    <td><?=$user->id?></td>
    <td><a href="<?=$this->url->create('users/id/' . $user->id) ?>"><?=$user->acronym?></a></td>
    <td><?=$user->name?></td>
    <td><?=$user->email?></td>
    <td><?=isset($user->active) ? 'Y' : 'N'?></td>
    <td><?=isset($user->deleted) ? 'Y' : 'N'?></td>
    <td><a href="<?=$this->url->create('users/soft-delete/' . $user->id) ?>">[trash]</a></td>
    <td><a href="<?=$this->url->create('users/soft-undelete/' . $user->id) ?>">[untrash]</a></td>
    <td><a href="<?=$this->url->create('users/delete/' . $user->id) ?>">[remove]</a></td>
    <td><a href="<?=$this->url->create('users/deactivate/' . $user->id) ?>">[deactivate]</a></td>
    <td><a href="<?=$this->url->create('users/activate/' . $user->id) ?>">[activate]</a></td>
    <td><a href="<?=$this->url->create('users/update/' . $user->id) ?>">[update]</a></td>
</tr>
<?php endforeach; ?>

</table>

<p><a href='<?=$this->url->create('users/list')?>'>Full list...</a></p>
