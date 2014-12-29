 <br>
  <div class="myButton">
    
    <a href="<?=$this->url->create('question/answer/' . $question->id) ?>">Svara på frågan</a>
    </ul>
  </div>

<?php if (isset($question)): ?>
  <h1>Fråga: <?=$question->title ?></h1>
  <div class="question">


    <div class="author-bar ">
      <?php $gravatar = 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($question->mail))) . '.jpg?s=30'; ?>

      <p class="right"><a href="<?=$this->url->create('users/id/' . $id) ?>"><?=$question->name?></a>

        <a href=<?= $question->mail ?>><i class="fa fa-envelope-o"></i></a></p>

        <img class="gravatar-small" src=<?=$gravatar ?>>
      </div>
      <p><?=$question->question ?></p>
      <?php
      $tags = explode("\n", str_replace(' ', '', $question->tags));
      ?>
      <div class="tags">
        <?php foreach($tags as $tag): ?>
          <div class="tag">
            <a href="<?=$this->url->create('question/tagId/' . $tag) ?>"><?=$tag ?></a>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="myButton1"><a class="right" href="<?=$this->url->create('question/comment/' . $tmp->id) ?>">Kommentera</a> </div>
    </div>
      <div class="comments">
        <?php if(isset($comments)): ?>
          <?php foreach($comments as $comment): ?>
            <?php
            $date1 = $comment->posted;
            $date2 = date('Y-m-d H:i:s');
            $ts1 = strtotime($date1);
            $ts2 = strtotime($date2);
            $seconds_diff = $ts2 - $ts1;
            if ($seconds_diff < 86400) {
              $date = ' idag';
            } else {
              $date = $seconds_diff / 86400;
              $date = round($date, 1);
              $date .= " dagar sedan";
            }
            ?>
            <?php
            foreach($commentators as $commentator) {
              if ($commentator->user == $comment->user) {
                $commentatorMail = $commentator->email;
              }
            }
            ?>
            <?php $realComment = strip_tags($comment->comment, '<a><i><b><strong><em>'); ?>
            <?php $gravatar = 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($commentatorMail))) . '.jpg?s=17'; ?>
            <div class="comment">
              <p><img class="gravatar" src=<?=$gravatar ?>>  <?=$realComment; ?> -<span class="timestamp"><?=$date ?></span></p>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>



  <?php endif; ?>


  <div class="answers">
    <?php if (isset($answers)): ?>
      <h5>Svar:</h5>
      <?php foreach($answers as $answer): ?>
        <?php
        $thisUser = $answer->user;
        $thisAnswer = $answer->answer;
        ?>
        <?php
        foreach($contributors as $contributor) {
          if ($contributor->user == $thisUser) {
            $contributorName = $contributor->user;
            $contributorId = $contributor->id;
            $contributorMail = $contributor->email;
          }
        }
        ?>

        <div class="answer">
          <div class="profile right">
            <?php $gravatar = 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($contributorMail))) . '.jpg?s=50'; ?>
            <p class="right"><a href="<?=$this->url->create('users/id/' . $contributorId) ?>"><?=$contributorName?></a>
              <a href=<?= $contributorMail ?>><i class="fa fa-envelope-o"></i></a></p>
              <img class="gravatar" src=<?=$gravatar ?>>
            </div>

            <p>Svar: <?=$thisAnswer ?></p>

          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php if (isset($form)): ?>
        <div class="answerForm">
          <p><?=$form ?></p>
        </div>
      <?php endif; ?>
    </div>
