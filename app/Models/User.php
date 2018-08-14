<?php

namespace App\Models;

use App\Models\Link\Social;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /******************************************************************************************************************/

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function social()
    {
        return $this->hasMany(Social::class);
    }

    /******************************************************************************************************************/

    /**
     * @param $service
     * @return bool
     */
    public function hasSocialLinked($service)
    {
        return (bool) $this->social->where('service', $service)->count();
    }

    /******************************************************************************************************************/
}
