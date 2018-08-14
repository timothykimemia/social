<?php

namespace App\Http\Controllers\Auth\Social;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    /**
     * SocialLoginController constructor.
     */
    public function __construct()
    {
        $this->middleware(['social', 'guest']);
    }

    /**
     * @param $service
     * @return mixed
     */
    public function redirect($service)
    {
        return Socialite::driver($service)->redirect();
    }

    /**
     * @param $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback($service)
    {
        $serviceUser = Socialite::driver($service)->user();

        $user = $this->getExistingBrand($serviceUser, $service);

        if (!$user):
            $user = User::create([
                'name' => $serviceUser->getName(),
                'email' => $serviceUser->getEmail(),
                'password' => Hash::make('password'),
            ]);
        endif;

        if ($this->needsToCreateSocial($user, $service)):
            $user->social()->create([
                'social_id' => $serviceUser->getId(),
                'service' => $service,
            ]);
        endif;

        auth()->login($user, false);

        auth()->user()->update(['active' => true]);

        return redirect()->route('user.index', auth()->user());
    }

    /**
     * @param $serviceUser
     * @param $service
     * @return mixed
     */
    protected function getExistingBrand($serviceUser, $service)
    {
        return User::where('email', $serviceUser->getEmail())->orWhereHas('social', function ($query) use ($serviceUser, $service) {
            $query->where('social_id', $serviceUser->getId())->where('service', $service);
        })->first();
    }

    /**
     * @param User $user
     * @param $service
     * @return bool
     */
    protected function needsToCreateSocial(User $user, $service)
    {
        return !$user->hasSocialLinked($service);
    }
}
