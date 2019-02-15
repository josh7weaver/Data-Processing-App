<?php namespace DataStaging\Mailers;

class ExampleMailer extends Mailer{

	/**
	 * reportErrors
	 * @param  string 	$email  - admin email to send to
	 * @param  array 	$data 	- numerical array of errors
	 * @return [type]        [description]
	 */
	public function reportErrors( $email, $errors )
	{
		$errorCount = count($errors);

		$subject = "DataStaging Errors";
		$view = 'emails.admin.notify';
		$data = [
			'title' => "There were $errorCount errors",
			'errors' => $errors
		];

		return $this->sendTo( $email, $subject, $view, $data);
	}
}