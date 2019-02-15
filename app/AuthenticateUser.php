<?php namespace DataStaging;

use DataStaging\Models\Admin;
use Illuminate\Contracts\Auth\Guard;
use Laravel\Socialite\Contracts\Factory as Socialite;

class AuthenticateUser {

    private $user, $socialite, $auth;

    public function __construct(Admin $user, Socialite $socialite, Guard $auth){

        $this->user = $user;
        $this->socialite = $socialite;
        $this->auth = $auth;
    }

    /**
     * @param $authenticated
     * @param $listener
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute($authenticated, $listener)
    {
        // If the user is not authenticated with google, send them to google
        if( !$authenticated ){
            return $this->redirectToGoogleForAuth();
        }

        // User is authenticated, so fetch data
        $userData = $this->fetchUserData();
        $email = $userData->getEmail();

        // if the users email is not a TOL email (@tolbookstores.com) email, ERROR
        if( !$this->isEmailValid($email) ){
            return $listener->loginErrorInvalidEmail($email);
        }

        // Authenticated and validated, persist and log them in.
        $user = $this->user->findByEmailOrCreate( $userData );
        $this->auth->login($user, true);

        return $listener->loggedInSuccessfully();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function redirectToGoogleForAuth()
    {
        return $this->socialite->driver('google')->redirect();
    }

    /**
     * Get user's data from Google
     * @return \Laravel\Socialite\Contracts\User
     */
    private function fetchUserData()
    {
        //User {#219 ▼
        //+token: "ya29.GQHMnBA2w1oOPwSD3F5G-T1Yogg9KJv6U6PE2UhbKRH4812CU-cRxkHo_ATD0BdaoUuePgKvU37mbg"
        //+id: "102464113359753568905"
        //+nickname: null
        //+name: "Joshua Weaver"
        //+email: "jweaver@tolbookstores.com"
        //+avatar: "https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg"
        //+"user": array:11 [▼
        //    "id" => "102464113359753568905"
        //    "email" => "jweaver@tolbookstores.com"
        //    "verified_email" => true
        //    "name" => "Joshua Weaver"
        //    "given_name" => "Joshua"
        //    "family_name" => "Weaver"
        //    "link" => "https://plus.google.com/102464113359753568905"
        //    "picture" => "https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg"
        //    "gender" => "other"
        //    "locale" => "en"
        //    "hd" => "tolbookstores.com"
        //  ]
        //}
        return $this->socialite->driver('google')->stateless()->user();
    }

    /**
     * @param $email
     * @return bool
     */
    private function isEmailValid($email)
    {
//        dd($this->getTld($email));
        return 'tolbookstores.com' == $this->getTld($email);
    }

    private function getTld($email)
    {
        return last(explode('@', $email));
    }
}
