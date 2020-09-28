import $ from 'jquery';

console.log("admin.js is running...");

const admin = $(".adminContentJS");

if ($("table a[data-delete]").length || $("a.btn.btn-danger").length) {
  appendModalToBody();

  admin.find("table a[data-delete], a.btn.btn-danger").on("click", e => {
    e.preventDefault();
  
    const link = $(e.target);
    const data = parseCsvDash(link.data("delete"));
    const urlToPost = link.attr("href").split("?")[0];
    let title = "";
    let body = "";
  
    if (data.table == "users") {
      title = "Delete User";
      body = `<p>Are you sure you want to delete the user
        <strong class="font-weight-bold">${data.username}</strong>?<br>
        If user posts exists they also will be permanently deleted!</p>`;
    
      } else if (data.table == "topics") {
      title = "Delete Topic";
      body = `<p>Are you sure you want to delete the topic <strong class="font-weight-bold">${data.name}</strong>?<br>
      If there are posts under this topic you can't delete it, unless you delete those posts first!</p>`;
    
    } else if (data.table == "posts") {
      title = "Delete Post";
      body = `<p>Are you sure you want to delete the post <strong class="font-weight-bold">${data.title}</strong>?<br>
      This post will be permanently deleted!</p>`;
    }
  
    currentModal(title, body, data, urlToPost);
  });
}

function currentModal(title, body, data, urlToPost) {
  const modal = $("#deleteModal");
  modal.on("show.bs.modal", e => {
    modal.find(".modal-title").text(title);
    modal.find(".modal-body").html(body);
    modal.find("form").attr("action", urlToPost);
    modal.find("form input[name='table']").val(data.table);
    modal.find("form button[name='delete']").val(data.id);
  })
  modal.modal();
}

function appendModalToBody() {
  const modal = `<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="deleteModalTitle"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="padding:.7rem 1rem;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body alert alert-warning mb-0 py-4"></div>
        <div class="modal-footer">
          <button id="modalCancelBtn" type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <form action="" method="post">
            <input type="hidden" name="table" value="">
            <button class="btn btn-danger btn-md delete" name="delete" value="">Delete</button>
          </form>
        </div>
      </div>
    </div>
  </div>`;

  $("body").append(modal);
}

function parseCsvDash(str) {
  const arr = str.split(",");
  const obj = {};
  arr.forEach(elem => {
    const prop = elem.split("-")[0];
    const value = elem.split("-")[1];
    return obj[prop] = value;
  })
  return obj;
}