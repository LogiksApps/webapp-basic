<section class="loginSection">
	<div class="container-fluid">
		<div class="row loginMain">
		    <div class='col-md-6 col-md-offset-3 top30'>
		    
                    
                       {if $SUCCESSMSG neq ''}
                	   
            			    <div class="alert alert-success  err-msg">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close"></a>
                				{$SUCCESSMSG} click <a href="{_link('login')}">here</a> to login
                			</div>
                			
                             {/if}
                                {if !empty($ERRORMSG)}
                                
                	                <div class="alert alert-danger fade in err-msg1">
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close"></a>
                				        {$ERRORMSG}
                			        </div>
                			        
                                {/if}
    				    
				    
				    </div>
		    
			<div class="loginBlock">
				<h3>Reset Password</h3>
				<div class="loginBody">
				    <!--<form id="reset" method="post" action='{_service("forgotPassword","resetPassword")}&token={$TOKEN_VAL}'>-->
				       <form id="reset" method="post" action='{_service("forgotPassword","resetPassword")}'>
					    	<div class="form-group">
						  <input id="security_code" type="text" class="form-control" name="security_code" placeholder="Enter Security Code *" required="required">
						    
						</div>
						<div class="form-group">
							 <input id="newpassword" type="password" class="form-control" name="password" placeholder="New Password*" required="required">
                                       <input type="hidden" name="token" id="token" value="{$TOKEN_VAL}">
            	                        <input type="hidden" name="sub" id="sub" value="{$ID}">
            	                         <input type="hidden" name="secure" id="secure" value="{$CODE_VAL}">
            	                        
						</div>
						<div class="form-group">
						      <input id="password_confirm" type="password" class="form-control" name="password_confirm" placeholder="Retype Password*" required="required">
						    
						</div>
					
						<div class="text-center">
								<button type="submit" class="btn btn-block">Reset Password</button>
							
						</div>
						
					</form>
				</div>
				
				
				
			</div>
			
			 
		</div>	
	</div>
</section>