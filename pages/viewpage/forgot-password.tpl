<section class="loginSection">
	<div class="container-fluid">
		<div class="row loginMain">
		    <div class='col-md-6 col-md-offset-3 top30'>
		    {nocache}
                    
                        {if $ERROR neq ''}
                        
    					    <div class="alert alert-danger  err-msg">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close"></a>
                                {$ERROR}
                			</div>
                			
            			{else}
            			
            			    {if $SUCCESS neq ''}
            			    
    						    <div class="alert alert-success ">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close"></a>
                                    {$SUCCESS}
                    			</div>
                    			
                    		{/if}
                    		
    				    {/if}
    				    
				    {/nocache}
				    </div>
			<div class="loginBlock">
				<h3>Forgot Password</h3>
				<div class="loginBody">
					<form role="form" id="forgot_pwd" action='{_service("forgotPassword","generateUserToken")}' method="post">
					    
						<div class="form-group">
							 <input id="email" type="email" class="form-control" autocomplete="off" name="email" placeholder="Email Address*" required="required">
						</div>
					
						<div class="text-center">
							<button type="submit" class="btn btn-block">Submit</button>
							
						</div>
						
					</form>
				</div>
			</div>
		</div>	
	</div>
</section>