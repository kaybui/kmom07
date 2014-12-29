
<?php
$thisUser = $user->getProperties();
$acronym = $thisUser['acronym'];
$id = $thisUser['id'];
$email = $thisUser['email'];
$name = $thisUser['name'];
$active = $thisUser['active'];
$gravatar = 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($user->email))) . '.jpg?s=200';
?>

<div class="title-bar cf">
<h1 class="username"><?=$acronym?></h1>
</div>
<div class="user-wrapper cf">
  <div class="gravatar">
    <img src='http://www.gravatar.com/avatar/<?=md5(strtolower(trim($user->email)))?>.jpg?s=150' alt=''>
  </div>
  <div class="user-info">
    <ul style="list-style: none">
      <li><span class="profile-label">Namn:</span> <?=$name ?></li>
      <li><span class="profile-label">Senast inloggad:</span> <?=$active ?></li>


      <p>  
        <?php if(isset($loggedOn) && $loggedOn == true): ?>
          <a href="<?=$this->url->create('users/update/' . $id) ?>">Redigera</a></li>
        <?php endif; ?>
      </p>

    </ul>
  </div>
</div>

<br>

<?php if(isset($questions)): ?>
<div class="cf">
  <div class="user-activity">
    <h2 class="title-bar"><?=$acronym ?>s frågor</h2>
  <?php $questions = array_reverse($questions); ?>
    <?php foreach($questions as $question): ?>

      <?php
          $date1 = $question->timestamp;
          $date2 = date('Y-m-d H:i:s');
          $ts1 = strtotime($date1);
          $ts2 = strtotime($date2);
          $seconds_diff = $ts2 - $ts1;
          if ($seconds_diff < 43200) {
            $date = ' idag';
          } else {
            $date = $seconds_diff / 86400;
            $date = round($date, 0);
            $date .= " dagar sedan";
          }
      ?>
      <div class="firstPageQuestions">

        <p>
          <i class="fa fa-question"></i>  <a href="<?=$this->url->create('question/id/' . $question->id) ?>"><?=$question->title?></a>
          <span class="timestamp">- <?=$date ?></span>
        </p>
      </div>
    <?php endforeach; ?>
</div>
</div>
<?php endif; ?>


<?php if(isset($answers) && isset($answerdQuestions)): ?>
<div class="cf">
  <div class="user-activity">
      <h2 class="title-bar"><?=$acronym ?>s svar</h2>
  <?php $answerdQuestions = array_reverse($answerdQuestions); ?>

  <?php foreach($answerdQuestions as $answer): ?>
    <div class="firstPageQuestions">
      <?php
      $realAnswer = strip_tags($answer->answer);
      ?>

      <p>
        <i class="fa fa-exclamation"></i><a href="<?=$this->url->create('question/id/' . $answer->questionID) ?>"><?=$answer->title?></a>
        <span class="timestamp">Svar: <?=$realAnswer?> </span></p>
    </div>
  <?php endforeach; ?>
</div>
</div>
<?php endif; ?>
