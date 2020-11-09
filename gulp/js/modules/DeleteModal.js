import $ from 'jquery';

class DeleteModal {

  constructor() {
    if (!server.isAdmin) return null;

    this.appendModalToBody();
    this.events();

    this.deleteModalHandler = () => {
      this.events();
    }
  }

  events() {
    if ($("table a[data-delete]").length || $("a.btn.btn-danger").length) {

      $(".adminContentJS").find("table a[data-delete], a.btn.btn-danger")
        .on("click", e =>
      {
        e.preventDefault();
      
        console.log("DeleteModal runs!");

        const link = $(e.target);
        const data = this.parseCsvDash(link.data("delete"));
        const urlToPost = link.attr("href").split("?")[0];
        let title = "";
        let body = "";
      
        if (data.table == "users") {
          title = "Delete User";
          body = `<p>Are you sure you want to delete the user
            <strong class="font-weight-bold">${data.username}</strong>?<br>
            If the user has posts you should delete those posts first.</p>`;
        
          } else if (data.table == "categories") {
          title = "Delete Category";
          body = `<p>Are you sure you want to delete the category
            <strong class="font-weight-bold">${data.name}</strong>?<br>
            It is unable to delete the category which has existing posts.</p>`;
        
        } else if (data.table == "posts") {
          title = "Delete Post";
          body = `<p>Are you sure you want to delete the post<br> <strong class="font-weight-bold">${data.title}</strong> ?<br>
          This post will be permanently deleted!</p>`;
        }
      
        this.currentModal("danger", title, body, data, urlToPost);
      });

    }
  }

  currentModal(type, title, body, data=false, urlToPost=false) {
    const modal = $("#dashboardModal");
  
    modal.on("show.bs.modal", e => {
      modal.find(".modal-title").text(title);
      modal.find(".modal-body").html(body);
      modal.find(".modal-body").addClass(`alert-${type}`);
      
      if (data !== false && urlToPost !== false) {
        modal.find("form").attr("action", urlToPost);
        modal.find("form input[name='table']").val(data.table);
        modal.find("form button[name='delete']").val(data.id);
      
      } else {
        modal.find("form").addClass("d-none");
        modal.find("#modalCancelBtn").addClass("d-none");
        modal.find("#modalOkBtn").removeClass("d-none");
      }
    })
  
    modal.modal();
  }

  appendModalToBody() {
    const modal = `<div class="modal fade" id="dashboardModal" tabindex="-1" role="dialog" aria-labelledby="dashboardModalTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header border-0">
            <h3 class="modal-title" id="dashboardModalTitle"></h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="padding:.7rem 1rem;">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body alert text-center mx-1 mx-sm-2 mx-md-3 mb-0 py-4"></div>
          <div class="modal-footer border-0">
            <button id="modalOkBtn" type="button" class="btn btn-primary btn-md my-3 d-none" data-dismiss="modal">Ok</button>
            <button id="modalCancelBtn" type="button" class="btn btn-primary btn-md" data-dismiss="modal">Cancel</button>
            <form action="" method="post" class="my-3">
              <input type="hidden" name="table" value="">
              <button class="btn btn-danger btn-md delete" name="delete" value="">Delete</button>
            </form>
          </div>
        </div>
      </div>
    </div>`;
  
    $("body").append(modal);
  }

  parseCsvDash(str) {
    const arr = str.split(",");
    const obj = {};
    arr.forEach(elem => {
      const prop = elem.split("-")[0];
      const value = elem.split("-")[1];
      return obj[prop] = value;
    })
    return obj;
  }

}

export default DeleteModal;