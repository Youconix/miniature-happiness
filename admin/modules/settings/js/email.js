function SettingsEmail(){
    this.address = '../../admin/modules/settings/email/';
}
SettingsEmail.prototype.init = function(){
    $('#smtp_active').click(function(){
        if( $(this).is(':checked') ){
            $('#smtp_settings').show();
        }
        else {
            $('#smtp_settings').hide();
        }
    });
    $('#settings_email_save').click(function(){ settingsEmail.emailSave(); });
}
SettingsEmail.prototype.emailSave = function(){
    var data = {
            'email_name' : $('#email_name').val(),'email_email' : $('#email_email').val(),'smtp_host' : $('#smtp_host').val(),
            'smtp_username' : $('#smtp_username').val(),'smtp_password' : $('#smtp_password').val(),'smtp_port' : $('#smtp_port').val(),
            'email_admin_name' : $('#email_admin_name').val(),'email_admin_email' : $('#email_admin_email').val()
    };
    
    if( !$('#smtp_active').is(':checked') ){
        $('#smtp_host').removeAttr('required');
        $('#smtp_username').removeAttr('required');
        $('#smtp_password').removeAttr('required');
        $('#smtp_port').removeAttr('required');     
    }
    else {
        $('#smtp_host').attr('required',true);
        $('#smtp_username').attr('required',true);
        $('#smtp_password').attr('required',true);
        $('#smtp_port').attr('required',true);
        data['smtp_active'] = 1;
    }
    
    var fields = new Array('email_name','email_email','smtp_host','smtp_username','smtp_password','smtp_port','email_admin_name','email_admin_email');
    if( !validation.html5ValidationArray(fields) ){
        return;
    }
    
    $.post(settingsEmail.address+'showemail',data);
    
    $('#notice').html(languageAdmin.admin_settings_saved);
}
var settingsEmail = new SettingsEmail();