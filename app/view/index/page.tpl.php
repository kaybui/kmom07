<br>
  <div class="myButton">
<a href="<?=$this->url->create('question/add') ?>">Ställ en fråga</a></li>

  </div>

<article class="article1">

<?=$content?>

<?php if(isset($byline)) : ?>
<footer class="byline">
<?=$byline?>

</footer>
<?php endif; ?>

</article>
