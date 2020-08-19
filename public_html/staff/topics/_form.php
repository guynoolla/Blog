<?php if (!isset($_SESSION['user_id'])) exit('Silent is golden!') ?>

<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
  
  <?php echo display_errors($topic->errors) ?>
  
  <?php if(isset($topic->id)): ?>
    <input type="hidden" name="topic[id]" value="<?php echo $topic->id ?>">
  <?php endif; ?>
  
  <div class="input-group">
    <label>Name</label>
    <input type="text" name="topic[name]" value="<?php echo h($topic->name) ?>" class="text-input">
  </div>
  <div class="input-group">
    <label>Description</label>
    <textarea class="text-input" name="topic[description]" id="description"><?php echo h($topic->description) ?></textarea>
  </div>
  <div class="input-group">
    <button type="submit" name="submit_button" class="btn" >
    <?php echo (!isset($topic->id) ? 'New' : 'Update') ?> Topic
    </button>
  </div>
</form>