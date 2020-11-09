import $ from 'jquery';

class PostStatus {

  constructor() {
    this.table = $(".loadContentJS table");
    this.pathname = window.location.pathname;
    this.postId;
    this.actions = ['approve', 'disapprove', 'publish', 'unpublish'];
    this.action;
    this.events();

    this.actionsClickHandler = () => {
      this.table = $(".loadContentJS table");
      this.events();
    }
  }

  events() {
    this.table.find('td a[data-key="fst_col"]')
        .on("click", this.actionHandler.bind(this))
    this.table.find('td a[data-key="snd_col"]')
        .on("click", this.actionHandler.bind(this))
  }

  actionHandler(e) {
    this.action = $(e.target).attr("data-cmd");
    
    if (this.actions.find(value => value == this.action)) {
      e.preventDefault();

      this.table.find("td[data-pid] a")
          .removeClass("status-updated status-updated--wave main-green main-blue");

      this.postId = $(e.target).attr("data-pid");
  
      this.requestServer({
        key: $(e.target).attr("data-key"),
        cmd: this.action,
        pid: this.postId,
        pathname: this.pathname
      });
    }
  }

  requestServer(params) {
    $.ajax({
      url: server.baseUrl + '/staff/xhr_post_status.php',
      type: 'GET',
      data: params,
      success: res => {
        console.log("RES J", res);
        const data = JSON.parse(res);

        if (data[0] == "success") {
          this.updateActionButton("fst_col", data[1]);
          this.updateActionButton("snd_col", data[2]);
          this.updatePostStatus(data[3]);
        } else {
          console.log('data j err', data)
        }
      },
      error: res => console.log("Err ->", res)
    })
  }

  updateActionButton(column, td) {
    this.table
      .find(`td a[data-key="${column}"][data-pid="${this.postId}"]`)
      .parent() // parent of a is td
      .replaceWith(td);

    this.table
      .find(`td a[data-key="${column}"][data-pid="${this.postId}"]`)
      .on("click", this.actionHandler.bind(this));
  }

  updatePostStatus(td) {
    this.table
        .find(`td[data-pid="${this.postId}"]`)
        .replaceWith(td);

    this.table.find(`td[data-pid="${this.postId}"] a`)
        .addClass("status-updated");

    let color = this.actionColor();
    color = color != "" ? ` ${color}` : "";
      
    this.table.find(`td[data-pid="${this.postId}"] a`)
        .addClass(`status-updated--wave${color}`);
  }

  actionColor() {
    switch(this.action) {
      case "approve":
              return "main-green"
      case "disapprove":
      case "publish":
              return "main-blue"
      default:
              return ""
    }
  }
}

export default PostStatus;