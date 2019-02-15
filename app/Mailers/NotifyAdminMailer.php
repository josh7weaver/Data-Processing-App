<?php namespace DataStaging\Mailers;

use Mail;

class NotifyAdminMailer implements BufferedLoggingMailer
{
	protected $mailer;
	protected $message;
	protected $from;
	protected $to;
	protected $subject;

	public function __construct($email = null)
	{
		$this->mailer = Mail::getSwiftMailer();
		$this->message = $this->mailer->createMessage();
		$this->from = 'admin@tolbookstores.com';
        $this->fromName = 'Eva :: DataStaging System';
		$this->to = is_null($email) ?  getenv('DATA_ERROR_EMAIL') : $email;
		$this->subject = "Errors";
	}

	public function getSwiftMailer()
	{
		return $this->mailer;
	}

	public function getMessage()
	{
		// body is blank bc it doesn't matter. 
		// It gets overwritten by the error log mesages in SwiftMailerHandler
		return $this->message
			->setFrom( $this->from , $this->fromName)
            ->setTo( $this->to )
            ->setSubject( $this->subject )
            ->setBody('','text/html');
	}
}