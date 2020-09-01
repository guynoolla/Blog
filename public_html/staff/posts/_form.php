<?php
// CHECK ACCESS
if (!isset($_SESSION['user_id'])) {
  exit('Silent is golden!');
}

require_once('../../../src/initialize.php');

$topics = App\Classes\Topic::findAll();

$auth_user_id = $session->getUserId();

// Isset of post ID means that Form is in the context of post editing
// if not then it is in the context of post creating!
$edit = isset($post->id) ? true : false;

?>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data" class="py-3">
  <?php
    if (!$edit) echo '<legend class="text-center">New Post</legend>';
    else echo '<legend class="text-center">Update Post</legend>'
  ?>

  <?php echo display_errors($post->errors) ?>

  <?php if($edit): ?>
    <input type="hidden" name="post[id]" value="<?php echo $post->id ?>">
  <?php endif; ?>
  <?php if(isset($post->user_id) && $post->user_id): ?>
    <input type="hidden" name="post[user_id]" value="<?php echo $post->user_id ?>">
  <?php else: ?>
    <input type="hidden" name="post[user_id]" value="<?php echo $auth_user_id ?>">
  <?php endif; ?>  

  <div class="form-group">
    <label for="exampleInputEmail1">Title</label>
    <input type="text" name="post[title]" value="<?php echo h($post->title) ?>" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
  </div>
  <div class="form-group">
    <label for="postBody">Content</label>
    <ul class="list-group list-group-horizontal-md" style="margin-bottom:1px">
      <li class="list-group-item bg-muted-lk">
        <span class="font-weight-bold text-secondary">HTML</span>
        <?php
          $tags = explode('><', $post->allowable_tags);
          $tags = implode('> <', $tags);
        ?>
        <span class="h5 text-muted"><?php echo h($tags) ?></span>
      </li>
      <li class="list-group-item font-weight-bold bg-muted-lk flex-grow-1 d-none d-md-block">
        <a href="https://www.youtube.com" class="text-muted">YouTube</a>
      </li>
      <li class="list-group-item font-weight-bold bg-muted-lk flex-grow-1 d-none d-md-block">
        <a href="https://vimeo.com" class="text-muted">Vimeo</a>
      </li>
    </ul>
    <textarea name="post[body]" value="<?php $post->body ?>" class="form-control" id="postBody" rows="10"><?php echo $post->body ?></textarea>
    <small id="bodyHelp" class="form-text text-muted">External links are not allowed excerpt YouTube and Vimeo video links.</small>
  </div>

  <div class="form-group">
    <label for="image">Select Post Image</label>
    <?php if ($edit && $post->image): ?>
      <div class="d-flex align-items-bottom">
        <h5>Image: <?php echo $post->image ?></h5>
        <img class="ml-auto" id="postImage" src="<?php echo url_for('/assets/images/' . $post->image) ?>" style="width:200px;height:auto;">
      </div>
    <?php endif; ?>
    <input type="file" name="image" class="form-control-file" id="image">
  </div>
  <div class="form-group mt-4">
    <select class="form-control" name="post[topic_id]">
      <?php if (!$edit): ?>
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
  <div class="custom-control custom-switch mt-4">
    <input name="post[published]" type="checkbox" class="custom-control-input" id="publishSwitch"<?php echo ($post->published == '1' ? ' checked' : '') ?>>
    <?php if ($post->published == '' || $post->published == '0'): ?>
      <label class="custom-control-label" for="publishSwitch">Publish</label>
    <?php else: ?>
      <label class="custom-control-label" for="publishSwitch">Published</label>
    <?php endif; ?>
    <small id="switchHelp" class="form-text small-nicer-lk">If it is not switched post will be saved as draft</small>
  </div>

  <?php
    if ($session->isAdmin() && (!$post->published && !$post->proved)): ?>
      <button type="submit" class="btn btn-danger ml-2 float-right">Delete</button><?php
    endif;
  ?>
  <button type="submit" class="btn btn-primary float-right">Save</button>
</form>