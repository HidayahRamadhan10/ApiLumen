<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\Lending;
use Illuminate\Http\Request;
use App\Models\Stuff;
use App\Models\StuffStock;

class LendingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $getLending = Lending::with('stuff', 'user')->get();

            return ApiFormatter::sendResponse(200, 'Succesfully Get All Lending Data', $getLending);
        }catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, $err->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'stuff_id' => 'required',
                'date_time' => 'required',
                'name' => 'required',
                'user_id' => 'required',
                'notes' => 'required',
                'total_stuff' => 'required',
            ]);

            $createLending = Lending::create([
                'stuff_id' => $request->stuff_id,
                'date_time' => $request->date_time,
                'name' => $request->name,
                'user_id' => $request->user_id,
                'notes' => $request->notes,
                'total_stuff' => $request->total_stuff,
            ]);

            $getStuffStock = StuffStock::where('stuff_id', $request->stuff_id)->first();
            $updateStock = $getStuffStock->update([
                'total_available' => $getStuffStock['total_available'] - 
                $request->total_stuff,
            ]);
             
            return ApiFormatter::sendResponse(200, 'Successfully Create A Lending Data', $createLending);
        }catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, $err->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Lending  $lending
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $getLending = Lending::where('id', $id)->with('stuff', 'user', 'restoration')->first();

            if(!$getLending) {
                return ApiFormatter::sendResponse(404, false, 'Data Lending Not Found');
            } else{
                return ApiFormatter::sendResponse(200, true, 'Succesfully Get A Lending Data', $getLending);
            }
        }catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, false, $err->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Lending  $lending
     * @return \Illuminate\Http\Response
     */
    public function edit(Lending $lending)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lending  $lending
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $getLending = Lending::find($id);

            if ($getLending) {
                $this->validate($request, [
                'stuff_id' => 'required',
                'date_time' => 'required',
                'name' => 'required',
                'user_id' => 'required',
                'notes' => 'required',
                'total_stuff' => 'required',
                ]);
                $getStuffStock = StuffStock::where('stuff_id', $request->stuff_id)
                ->first();
                $getCurrentStock = StuffStock::where('stuff_id', $getLending['stuff_id'])
                ->first();
    
                if ($request->stuff_id == $getCurrentStock['stuff_id']){
                    $updateStock = $getCurrentStock->update([
                        'total_available' => $getCurrentStock['total_available'] +  $getLending['total_stuff'] - $request->total_stuff,
                    ]);
                }else{
                    $updateStock = $getCurrentStock->update([
                        'total_available' => $getCurrentStock['total_available'] + 
                        $getLending['total_stuff']
                    ]);
                    $updateStock = $getCurrentStock->update([
                        'total_available' => $getStuffStock['total_available'] - 
                        $request['total_stuff']
                    ]);
                }
    
                $updateLending = $getLending->update([
                    'stuff_id' => $request->stuff_id,
                    'date_time'=> $request->date_time,
                    'name'=> $request->name,
                    'user_id'=>$request->user_id,
                    'notes'=>$request->notes,
                    'total_stuff'=> $request->total_stuff
                ]);
    
                $getUpdateLending = Lending::where('id', $id)->with('stuff', 'user',
                'restorations')->first();
    
                return ApiFormatter::sendResponse(200, 'success', $getUpdateLending);
            }
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, $err->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lending  $lending
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $getLending = Lending::where('id' ,$id)->delete();
            
            return ApiFormatter::sendResponse(200, 'success', 'Data Lending berhasil di hapus!');
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request' , $err->getMessage());
        }
    }

    public function recycleBin()
    {
        try {

            $lendingDeleted = Lending::onlyTrashed()->get();

            if (!$lendingDeleted) {
                return ApiFormatter::sendResponse(404, false, 'Deletd Data Lending Doesnt Exists');
            } else {
                return ApiFormatter::sendResponse(200, true, 'Successfully Get Delete All Lending Data', $lendingDeleted);
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {

            $getLending = Lending::onlyTrashed()->where('id', $id);

            if (!$getLending) {
                return ApiFormatter::sendResponse(404, false, 'Restored Data Lending Doesnt Exists');
            } else {
                $restoreLending = $getLending->restore();

                if ($restoreLending) {
                    $getRestore = Lending::find($id);
                    $addStock = StuffStock::where('stuff_id', $getRestore['stuff_id'])->first();
                    $updateStock = $addStock->update([
                        'total_available' => $addStock['total_available'] - $getRestore['total_stuff'],
                    ]);

                    return ApiFormatter::sendResponse(200, true, 'Successfully Restore A Deleted Lending Data', $getRestore);
                }
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function deletepermanent($id)
    {
        try {

            $getLending = Lending::onlyTrashed()->where('id', $id);

            if (!$getLending) {
                return ApiFormatter::sendResponse(404, false, 'Data Lending for Permanent Delete Doesnt Exists');
            } else {
                $forceStuff = $getLending->forceDelete();

                if ($forceStuff) {
                    return ApiFormatter::sendResponse(200, true, 'Successfully Permanent Delete A Lending Data');
                }
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, false, $e->getMessage());
        }
    }
    
    public function __construct()
    {
    $this->middleware('auth:api');
    }
}
