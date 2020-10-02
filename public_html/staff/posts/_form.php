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

if (!$edit): ?>
  <h1 class="dashboard-headline">
    New Post
    <div class="back-btn-pos"><?php echo page_back_button() ?></div>  
  </h1>
<?php else: ?>
  <h1 class="dashboard-headline">
    Post Update
    <div class="back-btn-pos"><?php page_back_button() ?></div>  
  </h1>
<?php endif;

?>
<form id="editPostForm" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data" class="py-3">

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
    <label for="title">Title</label>
    <input type="text" name="post[title]" value="<?php echo h($post->title) ?>" class="form-control" id="title" aria-describedby="emailHelp">
  </div>
  <div class="form-group">
    <label for="meta_desc">Meta Description for SEO</label>
    <input type="text" name="post[meta_desc]" value="<?php echo ($post->meta_desc ? h($post->meta_desc) : '') ?>" class="form-control" id="meta_desc" aria-describedby="emailHelp">
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
        <span class="text-muted">YouTube</span>
      </li>
      <li class="list-group-item font-weight-bold bg-muted-lk flex-grow-1 d-none d-md-block">
        <span class="text-muted">Vimeo</span>
      </li>
    </ul>
    <textarea name="post[body]" value="<?php $post->body ?>" class="form-control" id="postBody" rows="10"><?php echo $post->body ?></textarea>
    <small id="bodyHelp" class="form-text text-muted">External links are not allowed excerpt YouTube and Vimeo video links.</small>
  </div>

  <div class="form-group">
    <label>Post Format</label>
    <div class="form-check">
      <input class="form-check-input" type="radio" name="post[format]" id="format1" value="image" <?php echo (($post->format == 'image' || !$edit) ? 'checked' : '') ?>>
      <label class="form-check-label" for="format1">
        Image
      </label>
    </div>
    <div class="form-check">
      <input class="form-check-input" type="radio" name="post[format]" id="format2" value="video" <?php echo ($post->format == 'video' ? 'checked' : '') ?>>
      <label class="form-check-label" for="format2">
        Video
      </label>
    </div>
  </div>

  <?php $post->videoSplitter(); ?>

  <div class="form-group mt-2">
    <?php if ($edit && $post->image): ?>
      <div class="d-flex align-items-bottom">
        <h5>Image: <?php echo $post->image ?></h5>
        <img class="ml-auto" id="postImage" src="<?php echo url_for('/assets/images/' . $post->image) ?>" style="width:200px;height:auto;">
      </div>
    <?php endif; ?>
    <input type="file" name="image" class="form-control-file" id="image" <?php echo ($post->format == 'video' ? 'disabled' : '') ?>>
    <small id="fileHelp" class="form-text small-nicer-lk">Image aspect ratio (width x height) must be between 7x5 9x5)</small>
    <input type="text" name="post[video]" value="<?php echo (isset($post->video['url']) ? $post->video['url'] : '') ?>" class="form-control mt-1" id="video" placeholder="URL of Video" <?php echo (($post->format == 'image' || !$edit) ? 'disabled' : '') ?>>
  </div>
  <div class="form-group mt-4">
    <label for="topic">Topic</label>
    <select class="form-control" name="post[topic_id]" id="topic">
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
    <?php if ($post->published == '0'): ?>
      <label class="custom-control-label" for="publishSwitch">Publish</label>
    <?php else: ?>
      <label class="custom-control-label" for="publishSwitch">Published</label>
    <?php endif; ?>
    <small id="switchHelp" class="form-text small-nicer-lk">If it is not switched post will be saved as draft</small>
  </div>

  <?php
    if ($edit) {
      if ($session->isAdmin() && (!$post->published && !$post->approved)):
        $data = no_gaps_between("
          table-posts,
          id-{$post->id},
          title-{$post->title}
        ");
        ?><a data-delete="<?php echo $data ?>" href="<?php echo url_for('staff/delete.php?table=posts&id=' . $post->id) ?>" class="btn btn-danger ml-2 float-right">Delete</a><?php
      endif;
    }
  ?>
  <button type="submit" name="create" class="btn btn-primary float-right">Save</button>
</form>