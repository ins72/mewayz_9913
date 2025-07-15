<?php

namespace App\Http\Controllers\Admin\Users;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class PostController extends Controller
{
    
    public function tree(Request $request){
        // Check if method exists first or abort

        if(!method_exists($this, $request->tree)) abort(404);

        // If passed, then we know the method exists and we can continue

        return $this->{$request->tree}($request);

        // That's all lol
    }

    public function login($request){

        $user = User::find($request->_id);

        if (!$user) {
            abort(404);
        }

        \Auth::login($user);


        return redirect()->route('dashboard-index')->with('success', __('Logged in successfully'));
    }

    public function edit($request){
        
        $user = User::find($request->_id);

        if (!$user) {
            abort(404);
        }

        // Validate the reuqest
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);

        // Update our user
        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = $request->status;
        $user->role  = $request->role;


        $settings = $user->settings;

        if (!empty($setting = $request->settings)) {
            foreach ($setting as $key => $value) {
                $settings[$key] = $value;
            }
        }
        
        $user->settings = $settings;


        if (!empty($request->password)) {
            $request->validate([
                'password' => 'min:6|required|confirmed',
            ]);


            $user->password = Hash::make($request->password);
        }


        $user->save();


        return back()->with('success', __('User Saved Successfully'));
    }

    // So you can have other method here like

    public function create($request){
        $validate = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'unique:users'],
            'password'  => ['required', 'string', 'min:6'],
        ]);

        $validate = $validate->validate();

        $array = [
            'username' => \Str::random(10),
            'name' => $request->name,
            'email' => $request->email,
            'status' => 1,
            'password' => Hash::make($request->password)
        ];

        $create = User::create($array);

        // Return Back
        return back()->with('success', __('User Created Successfully.'));
    }

    public function delete($request){

        if(auth()->user()->id == $request->_user){
            return back()->with('error', __('Cannot delete current user.'));
        }

        if(!$user = User::find($request->_user)) abort(404);
        $user->_delete();

        
        return back()->with('success', __('User removed successfully'));
    }

    // Etc
}
