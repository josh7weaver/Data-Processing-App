<?php namespace DataStaging\Mailers;

use Mail;

abstract class Mailer{

	protected function sendTo($email, $subject, $view, $data=[])
	{
		Mail::send( $view, $data, function($message) use($email, $subject)
		{
			$message->to( $email )
					->subject( $subject );
		});
	}

}