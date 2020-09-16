<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Comment</title>
</head>
<body>
<p>
    {{ $comment->comment }}
</p>
 
---
<p>Replied by: {{ $realtor->f_name }} {{ $realtor->l_name }}</p>
 
<p>Title: {{ $ticket->title }}</p>
<p>Ticket ID: {{ $ticket->ticket_id }}</p>
<p>Status: {{ $ticket->status }}</p>
 
<p>
    You can view the ticket at any time at {{ url('tickets/'. $ticket->ticket_id) }}
</p>
 
</body>
</html>