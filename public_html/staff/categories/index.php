<?php
use App\Classes\Category;
use App\Classes\Pagination;

require_once('../../../src/initialize.php');

// Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAdmin()) redirect_to(url_for('index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Admin


$current_page = $_GET['page'] ?? 1;
$per_page = DASHBOARD_PER_PAGE;
$total_count = Category::countAll();
$pagination = new Pagination($current_page, $per_page, $total_count);

$categories = Category::find($per_page, $pagination->offset());

$page_title = 'Categories';
include SHARED_PATH . '/staff_header.php';
include '../_common-html-render.php';

?>
<div class="row">
  <aside class="sidebar col-lg-3">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>

  <main class="main col-lg-9">
    <div class="main-content adminContentJS">

      <h1 class="dashboard-headline">
        <?php echo $page_title ?>
        <div class="back-btn-pos"><?php echo page_back_button() ?></div>
        <a
          class="btn btn-outline-primary rounded-0 btn-md"
          href="<?php echo url_for('staff/categories/create.php') ?>"
          style="position:absolute;bottom:0;left:0;"
        >New Category</a>
      </h1>

      <?php
      if (empty($categories)):
        echo tableIsEmpty();
        
      else: ?>
        <?php echo tableSearchForm() ?>

        <div class="loadContentJS" data-access="admin_category">
          <table class="table table-striped table-bordered table-hover table-light <?php echo TABLE_SIZE ?>">
            <thead class="bg-muted-lk text-muted">
              <tr>
                <th scope="col">#</th>
                <th scope="col"><a href="#name" class="click-load" data-access="admin_category" data-value="asc" data-type="name_order">Name</a></th>
                <th scope="col">Description</th>
                <th scope="col"><a href="#created" class="click-load" data-access="admin_category" data-value="asc" data-type="date_order">Created</a></th>
                <th scope="colgroup" colspan="2">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($categories as $key => $category): ?>
                <tr>
                  <th scope="row"><?php echo $key + 1 ?></th>
                  <td><span class="h5"><?php echo $category->name ?></span></td>
                  <td><?php echo $category->description ?></td>
                  <td><a href="#ondate" class="click-load h5" data-type="date" data-value="<?php echo $category->created_at ?>" data-access="admin_category"><?php echo date('M j, Y', strtotime($category->created_at)) ?></span></td>
                  <td scope="colgroup" colspan="1">
                    <a class="btn-lk btn-lk--secondary" href="<?php echo url_for('/staff/categories/edit.php?id=' . $category->id) ?>">
                      Edit
                    </a>
                  </td>
                  <td scope="colgroup" colspan="1">
                    <?php
                      $data = no_gaps_between("
                        table-categories,
                        id-{$category->id},
                        name-{$category->name}
                      ")
                    ?>
                    <a data-delete="<?php echo $data ?>" class="btn-lk btn-lk--danger"
                      href="<?php echo url_for('staff/delete.php?table=categories&id=' . $category->id)
                    ?>">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <?php
            $url = url_for('staff/categories/index.php');
            echo $pagination->pageLinks($url);
          ?>
        </div>
  
      <?php endif; ?>

    </div>
  </main>
</div><!-- row -->

<?php include SHARED_PATH . '/staff_footer.php'; ?>