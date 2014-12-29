<h2>Frågor</h2>

<?php if (is_array($comments)) : ?>

<div class='comments'>
  <table class='CSSTableGenerator'>
        <tr>
            <th>Comments</th>
            <th>Links</th>
            <th>Id</th>
            <th>Edit</th>
        </tr>
<?php foreach ($comments as $id => $comment) : ?>

<?php
$data = $comment['content'];
$name = $comment['name'];
$web = $comment['web'];
$mail = $comment['mail'];
$timestamp = $comment['timestamp'];
?>
    <tr>
    <td>
      <h3 class="comment"><?=$name?></h3>
      <p><small><?=(date("Y-m-d",$timestamp))?></small></p>
      <p><?=$data?></p>
    </td>
    <td><p><a href=<?=$web?>>webpage</a>
    <a href="mailto:"<?=$mail?>><img src="../webroot/img/mail.png" height="20px" alt="mail"></a></p></td>
    <td><p><?=$id?></p></td>
    <td>
      <form method="post" action="#">
                    <input type=hidden name="redirect" value="<?=$this->url->getFullUrl()?>" />
                    <input type="hidden" name="commentId" value="<?=$id?>" />
                    <input type="submit" name="edit" value="Editera" onclick="this.form.action = '<?=$this->url->create('comment/edit')?>'" />
                    <input type="submit" name="doRemoveOne" value="Ta bort" onclick="this.form.action = '<?=$this->url->create('comment/remove')?>'"  />
                </form>
      </td>
    </tr>

<?php endforeach; ?>
</table>
</div>
<?php endif; ?>
