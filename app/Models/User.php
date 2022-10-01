<?php

namespace App\Models;

use App\Http\Common\Auth\PrivilegeManager;
use App\Http\Common\Auth\RoleManager;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Uuid, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'default_address_id',
        'email_verified_at',
        'phone_verified_at',
        'balance',
        'reward_points',
        'verified_at',
        'approved_at',
        'is_active',
        'fcm',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'default_address_id',
        'email_verified_at',
        'phone_verified_at',
        'balance',
        'reward_points',
        'verified_at',
        'approved_at',
        'driver_license_verified_at',
        'identity_document_verified_at',
        'default_address',
        'is_active',
        'roles',
        'privileges',
        'deleted_at',
        'created_at',
        'updated_at',
        'fcm',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $appends = [
        'default_address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'verified_at'       => 'datetime',
        'approved_at'       => 'datetime',
        'is_active'         => 'boolean',
        'balance'           => 'float',
        'reward_points'     => 'integer',
    ];

    /**
     * Route notifications for the Vonage channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForVonage($notification)
    {
        return $this->phone;
    }

    /**
     * @comment get user addresses.
     *
     * @return object
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function UserRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * @comment get user default address.
     *
     * @return object|null
     */
    public function getDefaultAddressAttribute()
    {
        return Address::where('id', $this->default_address_id)->first() ?? null;
    }

    /**
     * @comment get user roles
     *
     * @return object
     */
    public function roles()
    {
        return $this->hasMany(UserRole::class);
    }

    public function allRoles()
    {
        return $this->hasManyThrough(Role::class, UserRole::class, 'user_id', 'id', 'id', 'role_id');
    }

    /**
     * @comment check if user has role
     *
     * @return boolean
     */
    public function hasRole($role)
    {
        $role_id = Role::where('key', $role)->first()?->id;
        return $this->roles()->where('role_id', $role_id)->first() ? true : false;
    }

    /**
     * @comment get user privileges
     *
     * @return object
     */
    public function privileges()
    {
        return $this->hasMany(UserPrivilege::class);
    }

    /**
     * @comment get user balance transactions
     *
     * @return boolean
     */
    public function balanceTransactions()
    {
        return $this->hasMany(BalanceTransaction::class);
    }

    /**
     * @comment check if user has privilege
     *
     * @return boolean
     */
    public function hasPrivilege($privilege)
    {
        $privilege_id = Privilege::where('key', $privilege)->first()?->id;
        return $this->privileges()->where('privilege_id', $privilege_id)->first() ? true : false;
    }

    /**
     * @comment get user vehicles
     *
     * @return object
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function BankAccount()
    {
        return $this->hasOne(BankAccount::class);
    }

    /**
     * @comment check if user email is verified
     *
     * @return boolean
     */
    public function isEmailVerified()
    {
        return $this->email_verified_at !== null ? true : false;
    }

    /**
     * @comment check if user phone is verified
     *
     * @return boolean
     */
    public function isPhoneVerified()
    {
        return $this->phone_verified_at !== null ? true : false;
    }

    /**
     * @comment check if user driver license is verified
     *
     * @return boolean
     */
    public function isDriverLicenseVerified()
    {
        return $this->driver_license_verified_at !== null ? true : false;
    }

    /**
     * @comment check if user identity document is verified
     *
     * @return boolean
     */
    public function isIdentityDocumentVerified()
    {
        return $this->identity_document_verified_at !== null ? true : false;
    }

    /**
     * @comment get approval requests
     *
     * @return object
     */
    public function approvalRequests()
    {
        return $this->hasMany(ApprovalRequest::class);
    }

    /**
     * @comment get approval status
     *
     * @return boolean
     */
    public function isApproved()
    {
        return $this->approved_at !== null ? true : false;
    }

    /**
     * @comment get approval status
     *
     * @return boolean
     */
    public function isVerified()
    {
        return $this->verified_at !== null ? true : false;
    }

    /**
     * @comment get active status
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * @comment assign role to user
     * @param string $role
     * @return void
     */
    public function assignRole($role)
    {
        $roleManger = new RoleManager($this);
        return $roleManger->assign($role);
    }

    public function grantPrivilege($privilege)
    {
        $privilegeManger = new PrivilegeManager($this);
        return $privilegeManger->grant($privilege);
    }

    public function ownerApplications()
    {
        return $this->hasMany(OwnerApplication::class);
    }

    public function renterApplications()
    {
        return $this->hasMany(RenterApplication::class);
    }

    public function ownerApplication()
    {
        return $this->ownerApplications();
    }

    public function renterApplication()
    {
        return $this->renterApplications();
    }
}
