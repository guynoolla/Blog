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

  <div class="form-group row pl-0">
    <div class="col-sm-6">
      <label>Post Format</label>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="post[format]" id="formatImage" value="image" <?php echo (($post->format == 'image' || !$edit) ? 'checked' : '') ?>>
        <label class="form-check-label" for="formatImage">Image</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="post[format]" id="formatVideo" value="video" <?php echo ($post->format == 'video' ? 'checked' : '') ?>>
        <label class="form-check-label" for="formatVideo">Video</label>
      </div>
      <?php
        $_format = $edit ? $post->format : "false";
        $_image = ((isset($post->image) && $post->image) ? $post->image : "false");
        $_video = ((isset($post->video) && $post->video) ? $post->video : "false");
        $post->videoSplitter();
      ?>
      <div class="preview preview-image mt-sm-4">
        <?php if ($session->isAdmin()): ?>
          <p><?php echo ($_image != 'false' ? $post->image : '') ?></p>
        <?php endif; ?>
      </div>
    </div>

    <div class="col-sm-6 media-preview" data-format="<?php echo $_format ?>">

      <div class="preview preview-image <?php // image format
                  echo $_format != 'image' ? 'd-none' : ''
      ?>" data-value="<?php echo $_image ?>">
        <img id="previewImage" class="ml-auto" src="<?php
          echo ($_image != 'false' ? url_for('/assets/images/' . $post->image) : '')
        ?>" style="width:100%;height:auto;">
      </div>

      <div class="preview preview-video mt-4 mt-sm-0 <?php // video format
                  echo $_format != 'video' ? 'd-none' : ''
      ?>" data-value="<?php echo $_video ?>">
        <div class="embed-responsive embed-responsive-16by9">
        
        <?php if ($post->video['source'] == 'youtube'): // youtube ?>
          <iframe id="previewVideo" class="embed-responsive-item" src="<?php
            echo ($_video != 'false' ? $post->video['embed'] : '') 
            ?>" frameborder="0" allowfullscreen>
          </iframe>
        
          <?php elseif ($post->video['source'] == 'vimeo'): // vimeo ?>
          <iframe id="previewVideo" class="embed-responsive-item" src="<?php
            echo ($_video != 'false' ? $post->video['embed'] : '') ?>"
            frameborder="0" webkitallowfullscreen mozallowfullscreen'
            allowfullscreen>
          </iframe>
        <?php endif; ?>
        
        </div><!-- embed-responsive -->
      </div>

    </div>
  </div>

  <div class="form-group row mt-2">
    <div class="col-sm-6">
      <input type="file" name="image" class="form-control-file" id="image" <?php echo ($post->format == 'video' ? 'disabled' : '') ?>>
      <small id="fileHelp" class="form-text small-nicer-lk">Image aspect ratio must be between 7x5 9x5)</small>
    </div>
    <div class="col-sm-6">
      <label for="video" class="mb-1">Video</label>
      <input type="text" name="post[video]" value="<?php echo (isset($post->video['url']) ? $post->video['url'] : '') ?>" class="form-control mt-1" id="video" placeholder="URL of Video" <?php echo (($post->format == 'image' || !$edit) ? 'disabled' : '') ?>>
    </div>
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