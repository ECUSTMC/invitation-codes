<?php

namespace InvitationCodes;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InvitationCodeController extends Controller
{
    public function list()
    {
        $free = DB::table('invitation_codes')->where('used_by', 0)->get();
        $used = DB::table('invitation_codes')->where('used_by', '<>', 0)->get();

        return view('InvitationCodes::codes', compact('free', 'used'));
    }
	/*
    public function generate(Request $request)
    {
        ['amount' => $amount] = $request->validate([
            'amount' => 'required|integer|min:1',
        ]);

        $records = Collection::times($amount)
            ->map(function () {
                return [
                    'code' => md5(Str::random()),
                    'generated_at' => Carbon::now(),
                ];
            })
            ->values()
            ->toArray();

        DB::table('invitation_codes')->insert($records);

        return back();
    }
    */
  	public function generate(Request $request)
    {
        ['description' => $description] = $request->validate([
            'description' => 'required',
        ]);

        DB::table('invitation_codes')->insert([
                    'code' => md5(Str::random()),
                    'generated_at' => Carbon::now(),
          			'description' => $description
        ]);

        return back();
    }
  
    public function edit(Request $request)
    {
      	$validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'description' => 'required',
        ]);
      
        if ($validator->fails()) {
            return response(['success' => false, 'error' => $errors->all()], 400);
        }
      
        ['id' => $id, 'description' => $description] = $request->all();

        DB::table('invitation_codes')
          ->where('id', $id)
          ->update([
              'description' => $description
        ]);
      
        return ['success' => true];
    }
}
