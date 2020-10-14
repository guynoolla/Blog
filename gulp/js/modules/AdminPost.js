import $ from 'jquery';

class AdminPost {

  constructor() {
    this.table = $(".loadContentJS table");
    
    this.events();
  }

  events() {
    this.table.find('td a[data-adm="own"]').on("click", this.ownPostHandle.bind(this))
  }

  ownPostHandle(e) {
    e.preventDefault();

    const tab = $(e.target).attr("data-tab")
    const cmd = $(e.target).attr("data-cmd")
    const pid = $(e.target).attr("data-pid")

    console.log('DATA ' + tab + ' ' + cmd + ' ' + pid);

    return false;
  }

}

export default AdminPost;