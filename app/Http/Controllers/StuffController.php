<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\Stuff;
use Illuminate\Http\Request;

class StuffController extends Controller
{
    public function index()
    {
        try {
            $data = Stuff::with('stuffStock', 'inboundStuff', 'lendings')->get();

            return ApiFormatter::sendResponse(200, 'Successfully Get All Stuff Data', $data);
        }catch(\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {

            $this->validate($request, [
                'name' => 'required',
                'category' => 'required',
            ]);

            $data = Stuff::create([
                'name' => $request->name,
                'category' => $request->category,
            ]);

        return ApiFormatter::sendResponse(200, 'succes', $data);
    } catch(\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
         }
   }

    public function show(Stuff $stuff)
    {
        try {
            $data = Stuff::where('id', $id)->with('stuffStock', 'inboundStuff', 'lendings')->first();

            if (is_null($data)) {
                return ApiFormatter::sendResponse(400, 'bad request', 'Data not found!');
            }else {
                return ApiFormatter::sendResponse(200, 'succes', $data);
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function edit(Stuff $stuff)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'category' => 'required',
            ]);
    
            $checkProses = Stuff::where('id', $id)->update([
                'name' => $request->name,
                'category' => $request->category,
            ]);
    
            if ($checkProses) {
                $data = Stuff::find($id);
                return ApiFormatter::sendResponse(200, 'succes', $data);
            }else {
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal Mengubah data');
            } 
        } catch (\Experience $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }


    public function destroy($id)
    {
        try {
            $getStuff = Stuff::where('id' ,$id)->delete();
            
            return ApiFormatter::sendResponse(200, 'success', 'Data stuff berhasil di hapus!');
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request' , $err->getMessage());
        }
    }

    public function trash()
    {
        try {
            $data = Stuff::onlyTrashed()->get();

            return ApiFormatter::sendResponse(200, 'succes', $data);
        } catch(\Experience $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $checkProses = Stuff::onlyTrashed()->where('id', $id)->restore();

         if ($checkProses) {
            $data = Stuff::find($id);
            return ApiFormatter::sendResponse(200, 'succes', $data);
         } else {
            return ApiFormatter::sendResponse(400, 'bad request', 'Gagal mengembalikan data');
         }
    } catch (\Experience $err) {
        return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
    }
  }

  public function deletePermanent($id)
  {
    try {
        $checkProses = Stuff::onlyTrashed()->where('id', $id)->forceDelete();

        return ApiFormatter::sendResponse(200, 'succes', 'Berhasil menghapus permanent data stuff');
    } catch (\Experience $err) {
        return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
    }
  }

  public function __construct()
    {
    $this->middleware('auth:api');
    }
}
