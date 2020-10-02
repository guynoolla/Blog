<?php
use App\Classes\User;
use App\Classes\Pagination;

require_once '../../src/initialize.php';

$target = $_POST['target'] ?? "";

if (!$session->isAdmin()) {
  exit;
}

switch($target) {
  case 'admin_user_by_search':
  case 'admin_user_by_user_type':
  case 'admin_user_by_date':
        admin_user_data($_POST['data']);
  default:
    exit(json_encode(['target' => 'error']));
}

function admin_user_data($data) {
  global $session;
  
  parse_str($data, $params);
  $type = $params['type'] ?? "";
  $value = $params['value'] ?? "";

  if ($type == 'search') {
    
    if ($value != "") {  // search
      $total_count = User::countAll([
        ["( username LIKE '%?%' )", $value]
      ]);

    } elseif ($value == "") { // default
      $total_count = User::countAll();
    }

  } else if ($type == 'user_type') {

    $total_count = User::countAll([
      'user_type' => $value
    ]);

  } elseif ($type == 'date') {

    $created = date('Y-m-d', strtotime($value));
    $date_next = date('Y-m-d', strtotime('+ 1 day', strtotime($value)));
    $total_count = User::countAll([
      ["( created_at >= '?' AND created_at < '?' )", $created, $date_next]
    ]);

  }

  $current_page = $params['page'] ?? 1;
  $per_page = DASHBOARD_PER_PAGE;
  $pagination = new Pagination($current_page, $per_page, $total_count);
  
  $sql = "SELECT u.*, COUNT(p.user_id) AS posted,";
  $sql .= " SUM(if (p.approved = '1', 1, 0)) AS approved";
  $sql .= " FROM `users` AS u LEFT JOIN `posts` AS p";
  $sql .= " ON u.id = p.user_id";
  if ($type == 'search' && $value != "") {
    $sql .= " WHERE u.username LIKE '%{$value}%'";
  } else if ($type == 'user_type') {
    $sql .= " WHERE u.user_type = '{$value}'";
  } else if ($type == 'date') {
    $sql .= " WHERE u.created_at >= '{$created}' AND u.created_at < '{$date_next}'";
  }
  $sql .= " GROUP BY u.id ORDER BY u.username";
  $sql .= " LIMIT {$per_page} OFFSET {$pagination->offset()}";

  $users = User::findBySql($sql);

  ob_start();

  ?><table class="table table-striped table-bordered table-hover table-light table-md">
    <thead class="bg-muted-lk text-muted">
      <tr>
        <th scope="col">#</th>
        <th scope="col">Username</th>
        <th scope="col">Email</th>
        <th scope="col">Type</th>
        <th scope="col">Since</th>
        <th scope="col">Posted</th>
        <th scope="colgroup" colspan="2">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($users as $key => $user): ?>
      <tr data-user="<?php echo $user->id ?>">
        <th scope="row"><?php echo $key + 1 ?></th>
        <td><a href="#"><?php echo $user->username ?></a></td>
        <td><a href="mailto: <?php echo $user->email ?>" class="<?php echo ($user->email_confirmed ? 'text-success' : '') ?>"><?php echo $user->email ?></a></td>
        <td><a href="#user-type" data-type="user_type" data-value="<?php echo $user->user_type ?>" data-access="admin_user" class="click-load"><?php echo $user->user_type ?></a></td>
        <td><a href="#ondate" data-type="date" data-value="<?php echo $user->created_at ?>" data-access="admin_user" class="click-load"><?php echo date('M j, Y', strtotime($user->created_at)) ?></a></td>
        <td><?php echo $user->posted ?> - <span class="text-success font-weight-bold">
          <?php echo $user->approved ?></span>
        </td>
        <td scope="colgroup" colspan="1">
          <a class="btn-lk btn-lk--secondary" href="<?php echo url_for('/staff/users/edit.php?id=' . $user->id) ?>">
            Edit
          </a>
        </td>
        <td scope="colgroup" colspan="1">
          <?php $data = no_gaps_between("
            table-users,
            id-{$user->id},
            username-{$user->username}
          ") ?>
          <a data-delete="<?php echo $data ?>" class="btn-lk btn-lk--danger"
            href="<?php echo url_for('staff/delete.php?table=users&id=' . $user->id)
          ?>">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table><?php

  $output = ob_get_contents();
  ob_end_clean();

  $pag = [
    'total_count' => $total_count,
    'html' => $pagination->pageLinks($params['pathname'])
  ];

  if ($output) {
    exit(json_encode(['success', $output, $pag]));
  } else {
    exit(json_encode(['failed']));
  }
}