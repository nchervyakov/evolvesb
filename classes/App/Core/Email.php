<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 19.03.2015
 * Time: 17:10
 */


namespace App\Core;


class Email extends \PHPixie\Email
{
    /**
     * @param array|string $to
     * @param array|string $from
     * @param null $subject
     * @param string $message
     * @param bool $html
     * @param string $config
     * @return int
     * @throws \Exception
     */
    public function send($to, $from, $subject, $message, $html = false, $config = 'default')
    {
        // Create the message
        if (!($message instanceof \Swift_Message)) {
            $message = \Swift_Message::newInstance($subject, $message, $html ? 'text/html' : 'text/plain', 'utf-8');
        }

        //Normalize the input array
        if (is_string($to)) {
            //No name specified
            $to = array('to' => array($to));

        } else if (is_array($to) && is_string(key($to)) && is_string(current($to))) {
            //Single recepient with name
            $to = array('to' => array($to));

        } else if (is_array($to) && is_numeric(key($to))) {
            //Multiple recepients
            $to = array('to' => $to);
        }

        foreach ($to as $type => $set) {
            $type = strtolower($type);
            if (!in_array($type, array('to', 'cc', 'bcc'), true)) {
                throw new \Exception("You can only specify 'To', 'Cc' or 'Bcc' recepients. You attempted to specify {$type}.");
            }

            // Get method name
            $method = 'add' . ucfirst($type);
            foreach ($set as $recepient) {
                if (is_array($recepient)) {
                    $message->$method(key($recepient), current($recepient));
                } else {
                    $message->$method($recepient);
                }
            }
        }
        if ($from === null) {
            $from = $this->pixie->config->get("email.{$config}.sender");
        }

        if (is_array($from)) {
            $message->setFrom(key($from), current($from));
        } else {
            $message->setFrom($from);
        }

        return $this->mailer($config)->send($message);
    }
}