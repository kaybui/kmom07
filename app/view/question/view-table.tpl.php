
<?php if (is_array($question)) : ?>
  <div class='question'>
    <table class='CSSTableGenerator'>
      <tr>
        <th>Kommentar</th>
        <th>Links</th>
        <th>Edit</th>
        <th>Id</th>
      </tr>
      <?php foreach ($question as $tmp) : ?>
        <tr>

          <td>
            <h3><?= $tmp->name ?></h3>
            <p><?= $tmp->question ?></p>
          </td>

          <td>

            <a href=<?= $tmp->mail ?>><i class="fa fa-envelope-o"></i></a>
          </td>

          <td>
            <a href="<?=$this->url->create('question/add/' . $tmp->id) ?>"><i class="fa fa-edit"></i></a>
            <a href="<?=$this->url->create('question/remove-id/' . $tmp->id) ?>"><i class="fa fa-trash-o"></i></a>
          </td>

          <td>
            <span class="post-id"><?= $tmp->id ?></span>
          </td>

        </tr>


      <?php endforeach; ?>
    </table>
  </div>
<?php endif; ?>

<a href="<?=$this->url->create('question/add') ?>">[add question]</a>
<a href="<?=$this->url->create('question/setup') ?>">[delete all questions]</a>
