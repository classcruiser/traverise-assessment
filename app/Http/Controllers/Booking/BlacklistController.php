<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Booking\Blacklist;

class BlacklistController extends Controller
{
  /**
   * BLACKLIST INDEX
   * 
   * @param none
   * 
   * @return Illuminate\Http\View
   */
  public function index()
  {
    $blacklist = Blacklist::orderBy('fname', 'asc')->get();

    return view('Booking.blacklist.index', compact('blacklist'));
  }

  /**
   * INSERT NEW ENTRY
   * 
   * @param Object $request
   * 
   * @return Illuminate\Http\Redirect
   */
  public function insert(Request $request)
  {
    Blacklist::create($request->all([
      'fname',
      'lname',
      'email',
      'notes'
    ]));

    session()->flash('messages', 'Entry added');

    return redirect('/blacklist');
  }

  /**
   * SHOW BLACKLIST
   * 
   * @param Integer $id
   * 
   * @return Illuminate\Http\View
   */
  public function show($id)
  {
    $blacklist = Blacklist::find($id);

    return response($blacklist);
  }

  /**
   * UPDATE EXISTING ENTRY
   * 
   * @param Integer $id
   * @param Object $request
   * 
   * @return Illuminate\Http\Redirect
   */
  public function update(Request $request)
  {
    $id = request('id');
    $blacklist = Blacklist::find($id);
    
    $blacklist->update($request->only([
      'fname', 'lname', 'notes', 'email'
    ]));

    session()->flash('messages', 'Blacklist updated');
    
    return redirect('/blacklist');
  }

  /**
   * DELETE ENTRY
   * 
   * @param Integer $id
   * 
   * @return Illuminate\Http\Redirect
   */
  public function remove($id)
  {
    $blacklist = Blacklist::find($id);

    $blacklist->delete();

    session()->flash('messages', 'Entry deleted');

    return redirect('/blacklist');
  }
}
