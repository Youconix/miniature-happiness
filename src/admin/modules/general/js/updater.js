function Updater() {
  this.address = "../modules/general/updater/";
}
Updater.prototype.init = function () {
  $('#admin_updates_checkupdate').click(function () {
    admin.show(updater.address + 'checkupdates', updater.checkupdates);
  });
}
Updater.prototype.checkupdates = function () {

}
Updater.prototype.performUpdate = function () {

}
Updater.prototype.performUpdateCallback = function () {

}
var updater = new Updater();