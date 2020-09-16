<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>New Message</title>
</head>
<body>
<p>
Heads up {{ ucfirst($realtor->f_name) }}. A message has been opened for your property listing. The details of your ticket are shown below:
</p>
 
<p>Title: {{ $ticket->title }}</p>
<p>Status: {{ $ticket->status }}</p>
 
<p>
You can view the message at any time at {{ url('tickets/'. $ticket->ticket_id) }}
</p>
 
</body>
</html>