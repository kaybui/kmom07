

  <br>
  <div class="tiny_tri_navbar">
      <a href="<?=$this->url->create('users/login') ?>">Logga in</a>   
      <a href="<?=$this->url->create('users/logout') ?>">Logga ut</a>  
      <a href="<?=$this->url->create('users/list') ?>">Alla användare</a>  
      <a href="<?=$this->url->create('users/add') ?>">Skapa profil</a>  
      <a href="<?=$this->url->create('users/setup') ?>">Återställ</a> 

  </div>

<article class="article1">

  <?=$content?>

  <?php if(isset($byline)) : ?>
    <footer class="byline">
      <?=$byline?>

    </footer>
  <?php endif; ?>

</article>
