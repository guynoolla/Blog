<?php if (!isset($_SESSION['user_id'])) exit('Silent is golden!') ?>

<?php $title = (!(isset($category->id)) ? 'New Category' : 'Update Category')  ?>
        
<h1 class="dashboard-headline mb-4">
  <?php echo $title ?>
  <div class="nav-btn back-btn-pos"><?php echo page_back_button() ?></div>
</h1>

<div class="row justify-content-left h-100">
  <div class="col col-md-10">

    <div class="py-2 my-4 rounded bg-white px-0 px-sm-4 px-lg-5">
      <form id="categoryEditForm" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

        <?php echo display_errors($category->errors); ?>
        
        <?php if(isset($category->id)): ?>
          <input type="hidden" name="category[id]" value="<?php echo $category->id ?>">
        <?php endif; ?>
        
        <?php $desc = $category->description ? $category->description : "" ?>

        <div class="form-group row mb-0 mx-0">
          <label for="email" class="col-sm-4 col-form-label pl-0">Name</label>
          <input type="text" class="col-sm-8 form-control" name="category[name]" value="<?php echo h($category->name) ?>" id="name" aria-describedby="emailHelp">
          <span class="errsum-name offset-sm-4 col-sm-8 text-danger field-validation-error"></span>
        </div>
        <div class="form-group row mb-0 mx-0">
          <label for="description" class="col-sm-4 col-form-label pl-0">Description</label>
          <textarea class="col-sm-8 form-control" name="category[description]" value="<?php
            echo h($desc) ?>" id="description" rows="5"><?php echo $desc
          ?></textarea>
          <span class="errsum-description offset-sm-4 col-sm-8 text-danger field-validation-error"></span>
        </div>

        <button type="submit" class="btn btn-primary float-right">Save</button>
      </form>
    </div>

  </div>
</div>
