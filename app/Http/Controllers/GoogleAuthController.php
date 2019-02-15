<?php namespace DataStaging\Http\Controllers;

use DataStaging\AuthenticateUser;
use Illuminate\Http\Request;
use Auth;

class GoogleAuthController extends Controller {

    public function login(AuthenticateUser $authenticator, Request $request)
    {
        return $authenticator->execute($request->has('code'), $this);
	}

    public function loggedInSuccessfully()
    {
        return redirect('/division');
    }

    public function loginErrorInvalidEmail($email)
    {
        return redirect('/')
                ->withMessage(
                    "$email is not a valid Tree of Life email address. ".
                    "Email must be @tolbookstores.com"
                );
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
