<?php
  // CHECK ACCESS
  if (!isset($_SESSION['user_id'])) exit('Silent is golden!');

  $topics = App\Classes\Topic::findAll();

  $auth_user_id = $session->getUserId();

  // Isset of post ID means that Form is in the context of post editing
  // if not then it is in the context of post creating!
  $edit = isset($post->id) ? true : false;
?>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">

  <?php echo display_errors($post->errors) ?>

  <?php if($edit): ?>
    <input type="hidden" name="post[id]" value="<?php echo $post->id ?>">
  <?php endif; ?>
  <?php if(isset($post->user_id) && $post->user_id): ?>
    <input type="hidden" name="post[user_id]" value="<?php echo $post->user_id ?>">
  <?php else: ?>
    <input type="hidden" name="post[user_id]" value="<?php echo $auth_user_id ?>">
  <?php endif; ?>

  <div class="input-group">
    <label>Title</label>
    <input type="text" name="post[title]" value="<?php echo h($post->title) ?>" class="text-input">
  </div>
  <div class="input-group">
    <label>Content</label>
    <div class="edit-panel">
      <div class="box">Html Tags <span><?php echo h($post->allowable_tags) ?></span></div>
      <div class="box">Youtube videos</div>
    </div>
    <textarea class="text-input" name="post[body]" id="body" rows="10"
      ><?php echo $post->body ?></textarea>
  </div>
  <div class="input-group">
    
    <?php if ($edit && $post->image): ?>
      <div style="display:flex;align-items:flex-end;">
        <img id="postImage" src="<?php echo url_for('/assets/images/' . $post->image) ?>" style="padding-bottom:1em;">
        <p>Image: <?php echo $post->image ?></p>
      </div>
    <?php endif; ?>
    <input type="file" name="image" class="file-input">
  </div>
  <div class="input-group">
    <label>Topic</label>
    <select class="text-input" name="post[topic_id]">
      <?php if(!$edit): ?>
        <option value="">Select topic</option>
      <?php endif; ?>
      <?php foreach($topics as $topic): ?>
        <option value="<?php echo $topic->id ?>"
          <?php echo (($topic->id == $post->topic_id) ? 'selected' : '' ) ?>
        >
          <?php echo h($topic->name) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="input-group">
    <label>
      <input type="checkbox" name="post[published]" <?php echo ($post->published == '1' ? 'checked' : '') ?>>
        <?php 
          echo (($post->published == '' || $post->published == '0')
              ? 'Publish <i>( leave blank to save as draft )</i>'
              : 'Published');
        ?>
    </label>
  </div>
  
  <div class="input-group">
    <button type="submit" name="submit_button" class="btn">
      Save Post
    </button>
  </div>
</form>

<p>To appear in blog page posts have to be proved by site administrator.</p>
