<?php if (!isset($_SESSION['user_id'])) exit('Silent is golden!') ?>

<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" class="py-3">

  <?php
    if (!isset($topic->id)) echo '<legend>New Topic</legend>';
    else echo '<legend>Update Topic</legend>';

    echo display_errors($topic->errors);
  ?>
  
  <?php if(isset($topic->id)): ?>
    <input type="hidden" name="topic[id]" value="<?php echo $topic->id ?>">
  <?php endif; ?>
  
  <div class="form-group">
    <label for="exampleInputEmail1">Name</label>
    <input type="text" name="topic[name]" value="<?php echo h($topic->name) ?>" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
  </div>
  <div class="form-group">
    <label for="exampleFormControlTextarea1">Description</label>
    <textarea name="topic[description]" value="<?php echo h($topic->description) ?>" class="form-control" id="exampleFormControlTextarea1" rows="5">
      <?php echo $topic->description ?>
    </textarea>
  </div>

  <button type="submit" class="btn btn-primary float-right">Save</button>
</form>