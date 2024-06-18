<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;

use App\Models\Booking\Document;
use App\Models\Booking\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::orderBy('sort', 'asc')->get();

        return view('Booking.documents.index', compact('documents'));
    }

    public function create()
    {
        return view('Booking.documents.new');
    }

    public function insert()
    {
        $document = Document::create([
            'name' => request('name'),
            'title' => request('title'),
            'slug' => request('slug') == '' ? Str::slug(request('name')) : Str::slug(request('slug')),
            'content' => request('content'),
            'position' => request('position'),
            'popup' => request('popup'),
        ]);

        session()->flash('messages', 'Document added');

        return redirect(route('tenant.documents.show', ['id' => $document->id]));
    }

    public function show($id)
    {
        $document = Document::findOrFail($id);

        return view('Booking.documents.show', compact('id', 'document'));
    }

    public function update($id, Request $request)
    {
        DB::beginTransaction();

        $document = Document::find($id)->update([
            'name' => $request->name,
            'title' => $request->title,
            'slug' => $request->slug,
            'content' => $request->content,
            'position' => request('position'),
            'popup' => request('popup'),
        ]);

        DB::commit();

        session()->flash('messages', 'Document updated');

        return redirect('documents/'.$id);
    }

    public function delete($id)
    {
        Document::find($id)->delete();

        session()->flash('messages', 'Document deleted');

        return redirect('documents/');
    }

    public function page($slug)
    {
        $document = Document::where('slug', $slug)->firstOrFail();

        return view('Booking.document')->with(['document' => $document]);
    }

    public function APIshow(string $slug) : object
    {
        $document = Document::where('slug', $slug)->firstOrFail();

        return response()->json($document);
    }

    public function sort()
    {
        $sort = request('data');

        foreach ($sort as $pos => $id) {
            DB::table('documents')->where('id', $id)->update([
                'sort' => intVal($pos) + 1
            ]);
        }

        return response('OK');
    }
}
