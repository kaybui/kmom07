  <br>
  <div class="tiny_tri_navbar">
      <a href="<?=$this->url->create('users/login') ?>">Logga in</a>   
      <a href="<?=$this->url->create('users/logout') ?>">Logga ut</a>  
      <a href="<?=$this->url->create('users/add') ?>">Skapa profil</a>  
      <a href="<?=$this->url->create('users/setup') ?>">Återställ</a> 

  </div>

<h1><?=$title?></h1>

<table style='width: 100%; text-align: left;'>

<tr>
    <th></th>
    <th><?='Användarnamn'?></th>
    <th><?='Namn'?></th>
    <th><?='Email'?></th>

</tr> 

<?php foreach ($users as $user) : ?>
<tr>
    <td><img src='http://www.gravatar.com/avatar/<?=md5(strtolower(trim($user->email)))?>.jpg?s=40' alt=''></td>
    <td><a href="<?=$this->url->create('users/id/' . $user->id) ?>"><?=$user->acronym?></a></td>
    <td><?=$user->name?></td>
    <td><?=$user->email?></td>
</tr> 
<?php endforeach; ?>

</table>
