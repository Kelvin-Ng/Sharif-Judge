<?php
/**
 * Sharif Judge online judge
 * @file login.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<?php echo form_open('login/lost') ?>
<form method="post" action="">
	<div class="box login">
		<div class="judge_logo">
			<a href="#"><img src="<?php echo site_url("images/logo.png") ?>"/></a>
		</div>
		<div class="login_form">
			<div class="login1">
				<p>
					<label for="email">Email</label><br/>
					<input type="text" name="email" class="sharif_input" value="<?php echo set_value('email'); ?>"/>
					<?php echo form_error('email','<div class="error">','</div>'); ?>
				</p>
				<?php if ($sent): ?>
					<div class="ok">We sent you an email containing a link to change your password.</div>
				<?php endif ?>
			</div>
			<div class="login2">
				<p style="margin:0;">
					<?php echo anchor("login","Login") ?>
					<input type="submit" value="Get New Password" id="sharif_submit"/>
				</p>
			</div>
		</div>
	</div>
</form>