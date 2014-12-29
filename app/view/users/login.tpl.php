<br>
<h1>Inloggning</h1>

<p>  
    <?php if(isset($loggedOn) && $loggedOn == true): ?>

     <a href="<?=$this->url->create('users/id/' . $id) ?>">Din profil</a>
    <?php endif; ?>
</p>

<?=$form?>
