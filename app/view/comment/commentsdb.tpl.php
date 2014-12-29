
<?php if (is_array($comments)) : ?>
<div class='comments'>
  <table class='CSSTableGenerator'>
        <tr>
            <th>Kommentar</th>
            <th>Links</th>
            <th>Edit</th>
            <th>Id</th>
        </tr>
        <?php foreach ($comments as $comment) : ?>
              <tr>

                <td>
                  <h3><?= $comment->name ?></h3>
                  <p><?= $comment->comment ?></p>
                </td>

                <td>
                  <a href=<?= $comment->web ?>><i class="fa fa-globe"></i></a>
                  <a href=<?= $comment->mail ?>><i class="fa fa-envelope-o"></i></a>
                </td>

                <td>
                  <a href="<?=$this->url->create('comment/add/' . $comment->id) ?>"><i class="fa fa-edit"></i></a>
                  <a href="<?=$this->url->create('comment/remove-id/' . $comment->id) ?>"><i class="fa fa-trash-o"></i></a>
                </td>

                <td>
                  <span class="post-id"><?= $comment->id ?></span>
                </td>

              </tr>


        <?php endforeach; ?>
</table>
</div>
<?php endif; ?>

<a href="<?=$this->url->create('comment/add') ?>">[add question]</a>
<a href="<?=$this->url->create('comment/setup') ?>">[delete all questions]</a>
