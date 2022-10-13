var password = document.getElementById("new"), confirm_password = document.getElementById("conf_pwd");
password.onchange = validatePassword;
confirm_password.onkeyup = validatePassword;
function validatePassword(){
    if(password.value != confirm_password.value) {
      confirm_password.setCustomValidity("Passwords Don't Match");
    } else {
      confirm_password.setCustomValidity('');
    }
}
$(function(){
    $(".alert-success").fadeOut(6000);
    $(".alert-danger").fadeOut(6000);
});
