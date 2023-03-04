<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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

    public function acceptDocument(Request $request, Document $document)
    {
        $document->status = Document::ACCEPTED;
        $document->verified_by = $request->user()->id;
        $document->update();
        return $this->respondSuccess(['document' => $document->refresh()], 'Document verified successfully');
    }

    public function declineDocument(Request $request, Document $document)
    {
        $document->status = Document::DECLINED;
        $document->verified_by = $request->user()->id;
        $document->reason = $request->reason;
        $document->update();
        return $this->respondSuccess(['document' => $document->refresh()], 'Document declined successfully');
    }
}
