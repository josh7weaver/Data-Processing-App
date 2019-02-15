<?php namespace DataStaging\Mailers;

interface BufferedLoggingMailer
{
    public function getSwiftMailer();

    public function getMessage();
}