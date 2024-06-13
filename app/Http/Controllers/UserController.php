<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        try {
            // ambil data yang mau ditampilkan
            $data = User::all()->toArray();

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
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
            // validasi
            $this->validate($request, [
                'username' => 'required|unique:users',
                'email' => 'required|unique:users',
                'password' => 'required',
                'role' => 'required'
            ]);

            // proses tambah data
            // NamaModel::create(['column' => $request->name_or_key, ])
            $createUser = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role, 
            ]);
            
            return ApiFormatter::sendResponse(200, true, 'succesfully create A user Data', $createUser);
        } catch (\Exception $err){
            return ApiFormatter::sendResponse(400, false, $err->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response    
     */
    public function show($id)
    {
        try {
            $data = User::where('id', $id)->first();

            if (is_null($data)) {
                return ApiFormatter::sendResponse(400, 'bad request', 'Data not found!');
            } else {
                return ApiFormatter::sendResponse(200, 'success', $data);
            } 
        } catch (\Exception $err) {
            return ApiFormatter::senResponse(400, 'bad request', $err->getMessage());
        }
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
     * Update the specified resource lin storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'username' => 'required',
                'email' => 'required|email|min:3',
                'role' => 'required|in:admin , staff'
            ]);

            if ($request->password) {
                $checkProses = User::where('id', $id)->update([
                    'username' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => $request->role
                ]);
            } else{
                $checkProses = User::where('id', $id)->update([
                    'username' => $request->name,
                    'email' => $request->email,
                    'role' => $request->role
                ]);
            }

            if ($checkProses) {
                $data = User::find($id);
                return ApiFormatter::sendResponse(200, 'success', $data);
            } else {
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal mengubah data!');
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $checkProses = User::where('id', $id)->delete();

            return ApiFormatter::sendResponse(200, 'success', 'Data stuff berhasil dihapus!');
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function trash()
    {
        try {
            // onlyTrashed: mencari data yang deletes_at nya BUKAN null
            $data = User::onlyTrashed()->get();

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $checkProses = User::onlyTrashed()->where('id', $id)->restore();

            if ($checkProses) {
                $data = User::find(id);
                return ApiFormatter::sendResponse(200, 'success', $data);
            } else {
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal mengembalikan data!');
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function permanentDelete($id)
    {
        try{
            $cekPermanentDelete = User::onlyTrashed()->where('id', $id)->forceDelete();

            if ($cekPermanentDelete) {
                return
                ApiFormatter::sendResponse(200, 'success','Berhasil menghapus data secara permanent' );
            }
        } catch (\Exception $err) {
            return
            ApiFormatter::sendResponse(400,'bad_request', $err->getMessage());
        }

    }

    public function login(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required',
                'password' => 'required|min:8',
            ], [
                'email.required' => 'Email Harus Diisi',
                'password.required' => 'Password Harus Diisi',
                'password.min' => 'Password Minimal 8 Karakter'
            ]);

            $user = User::where('email', $request->email)->first(); //mencari dan mendapatkan data user berdasarkan email

            if (!$user) {
                return ApiFormatter::sendResponse(400, false, 'Login Failed! User Doesnt Exists');
            } else {
                $isValid = Hash::check($request->password, $user->password);

                if (!$isValid) {
                    return ApiFormatter::sendResponse(400, false, 'Login Failed! Password Doesnt Match');
                }else {
                    $generateToken = bin2hex(random_bytes(40));
                    //bin2hex Mengonversi bilangan biner ke heksadesimal.

                    $user->update([
                        'token' => $generateToken
                    ]);
                    return ApiFormatter::sendResponse(200, 'Login Succesfully', $user);
                }
            }
       }catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, false, $err->getMessage());
       }
    }

    public function logout(Request $request)
    {
        try{
            $this->validate($request, [
                'email' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            
            if (!$user) {
                return ApiFormatter::sendResponse(400, 'Login Failed! User Doesnt Exists');
            } else {
                if (!$user->token) {
                    return ApiFormatter::sendResponse(400,'Logout Failed! User Doenst Login Sciene');
                } else {
                    $logout = $user->update(['token' => null]);

                    if ($logout) {
                        return ApiFormatter::sendResponse(200, 'Logout Succesfully');
                    }
                }
            }
        }catch (\Exception $err) {
        return ApiFormatter::sendResponse(400, $err->getMessage());
    }
  }

  public function __construct()
    {
    $this->middleware('auth:api');
    }
}