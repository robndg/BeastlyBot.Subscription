<?php
 
namespace App\Mailers;
 
use App\Ticket;
use App\User;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\Log;
 
class AppMailer
{
    protected $mailer;
    protected $fromAddress = 'team@beastly.app';
    protected $fromName = 'Beastly Support (no-reply)';
    protected $to;
    protected $subject;
    protected $view;
    protected $data = [];
 
    /**
     * AppMailer constructor.
     * @param $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }
 
    public function sendTicketInformation($user, Ticket $ticket)
    {

        $this->to = "robndg@me.com";//$user->email;
 
        $this->subject = "Message Sent! [$ticket->title] [ID: $ticket->ticket_id]";
 
        $this->view = 'emails.ticket_info';
 
        $this->data = compact('user', 'ticket');
 
        return $this->deliver();
    }

    public function sendTicketInformationToAdmin($realtor, Ticket $ticket, $user)
    {
      
        if($user->email != NULL && $user->name != NULL){
            $this->fromAddress = $user->email;
            $this->fromName = $user->name;
        }

        $this->to = $realtor->email;
 
        $this->subject = "$ticket->title [ID: $ticket->ticket_id]";
 
        $this->view = 'emails.ticket_info_realtor';
 
        $this->data = compact('realtor', 'ticket', 'user');
 
        return $this->deliver();
    }
 
    public function sendTicketComments($ticketOwner, $user, Ticket $ticket, $comment)
    {

        /*if($user->realtor_id != NULL){
            $realtor = Realtor::where('id', $user->realtor_id)->first();
            $this->fromAddress = $realtor->email;
            $this->fromName = $realtor->f_name . ' [Beastly Support]';
        }*/

        $this->to = $ticketOwner->email;
 
        $this->subject = "RE: $ticket->title [ID: $ticket->ticket_id]";
 
        $this->view = 'emails.ticket_comments';
 
        $this->data = compact('ticketOwner', 'user', 'ticket', 'comment');
 
        return $this->deliver();
    }

    public function sendTicketCommentsToRealtor($ticketOwner, $realtor, Ticket $ticket, $comment)
    {

        if($ticketOwner->email != NULL && $ticketOwner->name != NULL){
            $this->fromAddress = $ticketOwner->email;
            $this->fromName = $ticketOwner->name;
        }

        $this->to = $realtor->email; // realtor(user)
 
        $this->subject = "RE: $ticket->title [ID: $ticket->ticket_id]";
 
        $this->view = 'emails.ticket_comments_realtor';
 
        $this->data = compact('ticketOwner', 'realtor', 'ticket', 'comment');
 
        return $this->deliver();
    }
 
    public function sendTicketStatusNotification($ticketOwner, Ticket $ticket)
    {
        $this->to = $ticketOwner->email;
        $this->subject = "RE: $ticket->title [ID: $ticket->ticket_id]";
        $this->view = 'emails.ticket_status';
        $this->data = compact('ticketOwner', 'ticket');
 
        return $this->deliver();
    }
 
    public function deliver()
    {
        $this->mailer->send($this->view, $this->data, function($message){
 
            $message->from($this->fromAddress, $this->fromName)
                    ->to($this->to)->subject($this->subject);
 
        });
    }
}