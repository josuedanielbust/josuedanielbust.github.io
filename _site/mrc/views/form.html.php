<form method="post" action="<?php echo url_for('post'); ?>" name="quick-contact" id="quick-contact" class="pure-form pure-form-stacked">
	<fieldset>
		<div class="form-group"<?php if( isset($errors['name']) ){echo ' class="error"';}; ?>>
			<input type="text" id="name" placeholder="Name" name="name" value="<?php if( isset($values['name']) ){echo $values['name'];}; ?>" required>
			<?php if( isset($errors['name']) ){echo '<span class="error">' . $errors['name'] . '</span>';}; ?>
		</div>
		<div class="form-group"<?php if( isset($errors['email']) ){echo ' class="error"';}; ?>>
			<input type="email" id="email" placeholder="Email" name="email" value="<?php if( isset($values['email']) ){echo $values['email'];}; ?>" required>
			<?php if( isset($errors['email']) ){echo '<span class="error">' . $errors['email'] . '</span>';}; ?>
		</div>
		<div class="form-group"<?php if( isset($errors['message']) ){echo ' class="error"';}; ?>>
			<textarea type="text" id="message" placeholder="Mensaje" rows="3" name="message" value="" style="width:493px;" required><?php if( isset($values['message']) ){echo $values['message'];}; ?></textarea>
			<?php if( isset($errors['message']) ){echo '<span class="error">' . $errors['message'] . '</span>';}; ?>
		</div>
		<div class="form-group" style="text-align:center; padding-top:1em;">
			<button type="submit" id="send" value="Send" class="pure-button">Enviar</button>
		</div>
	</fieldset>
</form>