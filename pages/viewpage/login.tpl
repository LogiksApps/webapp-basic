<div class="container">
    <div class="row">
        <div class='col-md-6 col-md-offset-3 top30'>
          {if $ERROR_MSG}
            <div class='alert alert-danger'>{$ERROR_MSG}</div>
          {/if}
        </div>
        <div class="col-md-4 col-md-offset-4">
            <div id='login-dp' class="login-panel panel panel-default">
                <div class="panel-heading">
                    <!--<h3 class="panel-title">SILK <small>Welcome to our place!</small></h3>-->
                    <div class="row-fluid user-row">
                        <img src="{loadMedia('logos/logo.png')}" class="img-responsive" alt="Conxole Admin" />
                    </div>
                </div>
                <div class="panel-body">
                    {if getConfig("ALLOW_SOCIAL_LOGIN")}
                    Login via
                    <div class="social-buttons">
                      <a href="#" class="btn btn-fb"><i class="fa fa-facebook"></i> Facebook</a>
                      <a href="#" class="btn btn-tw"><i class="fa fa-twitter"></i> Twitter</a>
                      <a href="#" class="btn btn-gw"><i class="fa fa-google"></i> Google</a>
                    </div>
                    or
                    {/if}        
                    <form role="form" action="{_service('auth')}" method="POST">
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="E-mail" name="userid" type="text" autofocus>
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="Password" name="password" type="password" value="">
                                {if getConfig("ALLOW_PASSWORD_RECOVER")}
                                <div class="help-block text-right"><a href="" class="forgetLink">Forget the password ?</a></div>
                                {/if}
                            </div>
                            <!-- Change this to a button or input when using this as a form -->
                            <button type="submit" class="btn btn-lg btn-primary btn-block">Login</button>
                            <a href="{_link('forgot-password')}" class="forgetLink">Forgot Password?</a>
                            {if getConfig("ALLOW_PERSISTENT_LOGIN")}
                            <div class="checkbox">
                                <label>
                                    <input name="remember" type="checkbox" value="remember_me">Keep me logged in
                                </label>
                            </div>
                            {/if}
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
if(top!=window) top.location.reload();
</script>