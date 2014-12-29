<article class="article1">
    <?=$content?>

    <?php if (isset($byline)) : ?>
        <footer class="byline">
            <img class='byline-img' src='<?=$this->url->asset("img/byline-img.jpg")?>' alt='Bild på mig'/> 
            <?=$byline?>
        </footer>
    <?php endif; ?>
</article> 