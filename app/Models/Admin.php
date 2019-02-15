<?php namespace DataStaging\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Laravel\Socialite\Contracts\User;

class Admin extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

    protected $table = "admins";

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['google_id', 'name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];


    public function findByEmailOrCreate(User $userData)
    {
        $email = $userData->getEmail();
        $google_id = $userData->getId();
        $values = [
            'name' => $userData->getName(),
        ];

        return static::updateOrCreate(compact('email', 'google_id'), $values);
    }

//    private function firstName($fullname)
//    {
//        return explode(' ', $fullname)[0];
//    }
//
//    private function lastName($fullname)
//    {
//        return explode(' ', $fullname)[1];
//    }
}
