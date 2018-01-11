class Admin{
  constructor(adminContent){
    this.adminData = null;
    this.url = null;
    this.adminContent = adminContent;
  }  
  getPreUrl(){
    return'/router.php/admin/modules/';
  }
  init(){
    this.$menuWrapper = $('#menu_wrapper');
    $('#admin_menu_link').click(()=>{
      this.toggleMenu();
    });
  }
  toggleMenu(){
    if (this.$menuWrapper.css('display') === 'block') {
      this.hideMenu();
    }
    else {
      this.showMenu();
    }
  }
  hideMenu(callback){
    callback=callback||null;
    this.$menuWrapper.fadeOut(750,()=>{
      if (callback !== null) {
	callback();
      }
    });
  }
  showMenu(callback){
    callback=callback||null;
    this.$menuWrapper.fadeIn(750,()=>{
      if (callback !== null) {
	callback();
      }
    });
  }
}

var admin = new Admin();