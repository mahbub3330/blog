<?php

namespace App\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function add($post){
        $user = Auth::user();
        $isfavorite = $user->favorite_posts()->where('post_id',$post)->count();

        if ($isfavorite == 0){
            $user->favorite_posts()->attach($post);
            Toastr::success('Post Successfully Added to your favorite list : ','success');
            return redirect()->back();
        }else{
            $user->favorite_posts()->detach($post);
            Toastr::success('Post Successfully Removed to your favorite list : ','success');
            return redirect()->back();
        }


    }
}
