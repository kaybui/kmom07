<div class='comment-form'>
  <form method="post">
    <input type="hidden" name="redirect" value="<?=$url?>" />
    <input type="hidden" name="commentId" value="<?=$id?>" />
    <fieldset>
      <!-- <legend>Lägg till kommentar</legend> -->
      <p><label>Kommentar:<br/><textarea rows="1" cols="40" onfocus="this.rows=10;" style="resize: none;" name='content'><?=$content?></textarea></label></p>
      <p><label>Namn:<br/><input type='text' name='name' value='<?=$name?>'/></label></p>
      <p><label>Tags:<br/><input type='text' name='web' value='<?=$web?>'/></label></p>
      <p><label>Email:<br/><input type='text' name='mail' value='<?=$mail?>'/></label></p>
      <p class=buttons>
        <input type='submit' name='doCreate' value='Edit' onClick="this.form.action = '<?=$this->url->create('comment/doEdit')?>'"/>
      </p>
      <output><?=$output?></output>
    </fieldset>
  </form>
</div>
