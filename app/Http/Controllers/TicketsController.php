<?php

namespace App\Http\Controllers;

use App\Category;
use App\Ticket;
use App\User;
use App\AlertHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mailers\AppMailer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\InvalidRequestException;

class TicketsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sort = "created_at";
        $srt = "DESC";
        $tickets = Ticket::orderBy($sort, $srt)->paginate(10);
 
        return view('tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
 
        return view('slide.ticket.slide-ticket-create', compact('categories'));
    }

    public function createWithId($requestId)
    {

        $categories = Category::all();

        $user = Auth::user()->first();
        $fav_list = $user->favorites_list;
        $fav_list = collect($fav_list);
        $fav_list = $fav_list->toArray();

        $favorites = Listing::where('settings', '<', 3)->whereIn('id', $fav_list)->get();

        $prop = Listing::where('settings', '<', 3)->where('id', $requestId)->first();
        $photos = Photos::where('enabled', 1)->get();
 
        return view('tickets.create', ['favorites' => $favorites], compact('categories'))->with('type', 1)->with('prop', $prop)->with('photos', $photos);
        
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, AppMailer $mailer)
    {
        $this->validate($request, [
            'title' => 'required',
            'category' => 'required',
            'message' => 'required'
        ]);

        $category_input = $request->input('category');
        $category = Category::where('id', $category_input)->first();

        $ticket = new Ticket([
            'title' => $request->input('title'),
            'user_id' => Auth::user()->id,
            'ticket_id' => strtoupper(Str::random(10)),
            'category_id' => $category->id,
            'priority' => $category->priority,
            'message' => $request->input('message'),
            'status' => "Open"
        ]);
 
        $ticket->save();
 
       // $mailer->sendTicketInformation(Auth::user(), $ticket); 
 
        $tickets = Ticket::where('user_id', Auth::user()->id)->paginate(10);

        return response()->json(['success' => false, 'msg' => 'Message Sent!']);
 
        //return view('tickets.user_tickets', compact('tickets'))->with("status", "Your message has been sent!");
        

    }

    public function userTickets()
    {
        $sort = "created_at";
        $srt = "DESC";
        $tickets = Ticket::where('user_id', Auth::user()->id)->orderBy($sort, $srt)->paginate(10);

       // $tickets = Ticket::where('category_id',1)->whereIn('selected_id', Listing::where('realtor', 2)->get()->pluck('id')->toArray())->paginate(10); //Auth::user()->id
 
        return view('slide.ticket.slide-tickets-list', compact('tickets'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($ticket_id)
    {
        $ticket = Ticket::where('ticket_id', $ticket_id)->firstOrFail();

        $photos = NULL;
        $selected = NULL;
        if($ticket->category_id == 1 && $ticket->selected_id != 0){
            $selected = Listing::where('id',$ticket->selected_id)->first();
            $photos = Photos::where('gallery', 3)->orderBy('order')->get();
        }
        return view('tickets.show', compact('ticket'))->with('selected', $selected)->with('photos', $photos);
    }

    public function close($ticket_id, AppMailer $mailer)
    {
        $ticket = Ticket::where('ticket_id', $ticket_id)->firstOrFail();
 
        $ticket->status = "Closed";
 
        $ticket->save();
 
        $ticketOwner = $ticket->user;
 
        //$mailer->sendTicketStatusNotification($ticketOwner, $ticket);
 
        return redirect()->back()->with("status", "The ticket has been closed.");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
