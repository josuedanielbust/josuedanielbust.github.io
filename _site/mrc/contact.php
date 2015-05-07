<?php
	ob_start();
	include( dirname(__FILE__) .'/conf.php' );
	include( dirname(__FILE__) .'/lib/limonade.php' );
	include( dirname(__FILE__) .'/lib/formvalidator.php' );
	include( dirname(__FILE__) .'/lib/class.phpmailer.php' );

	dispatch('/', create_function('', 'return render("form.html.php", NULL, array("errors" => array(), "values" => array()));') );
	dispatch('/thanks', create_function('', 'return render("thanks.html.php");') );
	dispatch('/js', create_function('', 'return js("scf.js.php");') );
	dispatch_post('/post', 'process_post');
	run();
	ob_end_flush();

	function process_post()
	{
		global $conf;

		// validate form
		$validator = new FormValidator();
		$validator->addValidation("name", "req", "Por favor ingresa tu nombre");
		$validator->addValidation("email", "req", "Por favor ingresa tu email");
		$validator->addValidation("email", "email", "Por favor ingresa un email valido");
		$validator->addValidation("message", "req", "Por favor ingresa tu mensaje");

		$values = array(
			'name'		=> $_POST['name'],
			'email'		=> $_POST['email'],
			'message'	=> $_POST['message'],
			);

		if( $validator->ValidateForm() )
		{
		// send email
			$mail = new PHPMailer();
			$mail->From       = $conf['email_sent_from'];
			$mail->FromName   = $conf['email_sent_from_name'];
			$mail->Subject    = $conf['email_subject'];
			$mail->WordWrap   = 50; // some nice default value

			$mail->Body = $values['message'];
			$mail->AddReplyTo( $values['email'] );
			$mail->AddAddress( $conf['email_to'] );
			if( ! $mail->Send() )
			{
				// do something to handle email errors if needed
			}

		// show thank you
			redirect_to('thanks#contact');
		}
		else
		{
			$errors = $validator->GetErrors();
			return render('form.html.php', NULL, array('errors' => $errors, 'values' => $values));
		}
	}
?>