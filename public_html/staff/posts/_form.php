<?php
// CHECK ACCESS
if (!isset($_SESSION['user_id'])) {
  exit('Silent is golden!');
}

require_once('../../../src/initialize.php');

$categories = App\Classes\Category::findAll();

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
    <div class="back-btn-pos"><?php echo page_back_button() ?></div>  
  </h1>
<?php endif;

?>
<form id="postEditForm" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data" class="py-3">

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
    <span class="errsum-title text-danger field-validation-error"></span>
  </div>
  <div class="form-group">
    <label for="meta_desc">Meta Description for SEO</label>
    <input type="text" name="post[meta_desc]" value="<?php echo ($post->meta_desc ? h($post->meta_desc) : '') ?>" class="form-control" id="meta_desc" aria-describedby="emailHelp">
    <span class="errsum-meta_desc text-danger field-validation-error"></span>
  </div>
  <div class="form-group">
    <label for="postBody">Content</label>
    <ul class="post-edit-panel" style="margin-bottom:1px">
      <li class="panel-item allowable">
        <?php
          $tags = explode('><', $post->allowable_tags);
          $tags = str_replace('<', '', str_replace('>', '', $tags));
          $tags = implode(', ', $tags);
        ?>
        <span><span><?php echo h($tags) ?></span></span>
      </li>
      <li class="panel-item image dropzoneBtnJS">
        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="image" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-icon svg-inline--fa fa-image fa-w-16 fa-5x"><path fill="currentColor" d="M464 448H48c-26.51 0-48-21.49-48-48V112c0-26.51 21.49-48 48-48h416c26.51 0 48 21.49 48 48v288c0 26.51-21.49 48-48 48zM112 120c-30.928 0-56 25.072-56 56s25.072 56 56 56 56-25.072 56-56-25.072-56-56-56zM64 384h384V272l-87.515-87.515c-4.686-4.686-12.284-4.686-16.971 0L208 320l-55.515-55.515c-4.686-4.686-12.284-4.686-16.971 0L64 336v48z" class="svg-icon"></path></svg>
      </li>
      <li class="panel-item video"><span>YouTube <span class="pipe">|</span> Vimeo</span></li>
      <li class="panel-item dropzone-area">
        <span class="dropzone-area-hint">Drop files here or click to upload<span>
      </li>
    </ul>
    <textarea name="post[body]" value="<?php $post->body ?>" class="form-control" id="body" rows="10"><?php echo $post->body ?></textarea>
    <small>External links are not allowed excerpt YouTube and Vimeo video links.</small>
    <span class="errsum-body text-danger field-validation-error"></span>
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
        $post->videoSplitter();
        $format_v = ($post->format != '') ? $post->format : 'false';
        $image_v = ($post->image != '') ? 'true' : 'false';
        $video_v = ($post->video != '') ? 'true' : 'false';
      ?>
    </div>
 
    <div class="col-sm-6 media-preview" data-format="<?php echo $format_v ?>">

      <div class="preview preview-image <?php // Image format output
        echo $format_v != 'image' ? 'd-none' : '' ?>" data-value="<?php
      echo $image_v ?>">
        <img id="previewImage" class="ml-auto" src="<?php
          echo ($image_v != 'false' ? url_for('/assets/images/' . $post->image) : '')
        ?>" style="width:100%;height:auto;">
      </div>

      <?php //dd($post->video, 1, 'Look') ?>

      <div class="preview preview-video <?php // Video format output
        echo $format_v != 'video' ? 'd-none' : '' ?>" data-value="<?php
      echo $video_v ?>">

        <?php if ($video_v != 'false'): ?>
        <div class="embed-responsive embed-responsive-16by9">
          
          <?php if ($post->video['provider'] == 'youtube'): ?>

          <iframe id="previewVideo" class="embed-responsive-item" src="<?php
            echo $post->video['embed'] ?>" frameborder="0" allowfullscreen>
          </iframe>
        
          <?php elseif ($post->video['provider'] == 'vimeo'): ?>

          <iframe id="previewVideo" class="embed-responsive-item" src="<?php
            echo $post->video['embed'] ?>" frameborder="0" webkitallowfullscreen
            mozallowfullscreen allowfullscreen>
          </iframe>

          <?php endif; ?>
          
        </div><!-- embed-responsive -->
        <?php endif; ?>

      </div>

    </div>
  </div>

  <div class="form-group row">
    <div class="col-sm-6">
      <label for="image">Image</label>
      <input type="file" name="image" class="form-control-file rounded p-1" id="image" <?php echo ($post->format == 'video' ? 'disabled' : '') ?>>
      <small id="fileHelp" class="form-text small-nicer-lk">Image aspect ratio must be between 7x5 9x5)</small>
      <span class="errsum-image text-danger field-validation-error"></span>
    </div>
    <div class="col-sm-6">
      <label for="video">Video</label>
      <input type="text" name="post[video]" class="form-control mt-1" id="video" value="<?php echo (isset($post->video['url']) ? $post->video['url'] : '') ?>" placeholder="URL of Video" <?php echo (($post->format == 'image' || !$edit) ? 'disabled' : '') ?>>
      <span class="errsum-video text-danger field-validation-error"></span>
    </div>
  </div>

  <div class="form-group mt-4">
    <label for="category">Category</label>
    <select class="form-control" name="post[category_id]" id="category">
      <?php if (!$edit): ?>
        <option value="0">Select category</option>
      <?php endif; ?>
      <?php foreach($categories as $category): ?>
        <option value="<?php echo $category->id ?>"
          <?php echo (($category->id == $post->category_id) ? 'selected' : '' ) ?>
        >
          <?php echo h($category->name) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <span class="errsum-category text-danger field-validation-error"></span>
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
      if (!$post->published && !$post->approved):
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