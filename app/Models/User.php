<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use jeremykenedy\LaravelRoles\Traits\HasRoleAndPermission;
use Laravel\Sanctum\HasApiTokens;
use Modules\IncidentReporting\Entities\AccusedType;
use Modules\IncidentReporting\Entities\AffiliationDesignation;
use Modules\IncidentReporting\Entities\BannedOrganization;
use Modules\IncidentReporting\Entities\Closure;
use Modules\IncidentReporting\Entities\DepartmentTypeIncidentType;
use Modules\IncidentReporting\Entities\Evidence;
use Modules\IncidentReporting\Entities\EvidenceType;
use Modules\IncidentReporting\Entities\GadgetType;
use Modules\IncidentReporting\Entities\Incident;
use Modules\IncidentReporting\Entities\IncidentComplainant;
use Modules\IncidentReporting\Entities\IncidentMotive;
use Modules\IncidentReporting\Entities\IncidentPerson;
use Modules\IncidentReporting\Entities\IncidentSource;
use Modules\IncidentReporting\Entities\IncidentStatus;
use Modules\IncidentReporting\Entities\IncidentStatusHistory;
use Modules\IncidentReporting\Entities\IncidentType;
use Modules\IncidentReporting\Entities\Mobility;
use Modules\IncidentReporting\Entities\MobilityType;
use Modules\IncidentReporting\Entities\Person;
use Modules\IncidentReporting\Entities\Place;
use Modules\IncidentReporting\Entities\PoiAffiliation;
use Modules\IncidentReporting\Entities\PoiAttachment;
use Modules\IncidentReporting\Entities\PoiAttachmentType;
use Modules\IncidentReporting\Entities\PoiStatusHistory;
use Modules\IncidentReporting\Entities\PoliceStation;
use Modules\IncidentReporting\Entities\Priority;
use Modules\IncidentReporting\Entities\Recovery;
use Modules\IncidentReporting\Entities\RecoveryType;
use Modules\IncidentReporting\Entities\Religion;
use Modules\IncidentReporting\Entities\Spectrum;
use Modules\IncidentReporting\Entities\SpectrumAssignment;
use Modules\IncidentReporting\Entities\VehicleBrand;
use Modules\IncidentReporting\Entities\VehicleType;
use Modules\Settings\Entities\Company;
use Modules\Settings\Entities\MyPermission;
use Modules\Settings\Entities\MyRole;
use Modules\Settings\Entities\Section;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Modules\Dengue\Entities\TeamMember;
use AhmedAliraqi\LaravelMediaUploader\Entities\Concerns\HasUploader;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;




class User extends Authenticatable implements JWTSubject, HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia, HasUploader;
    use HasRoleAndPermission;
    use SoftDeletes;



    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('PROFILE_PICTURE');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'cnic',
        'username',
        'department_id',
        'password',
        'company_id',
        'section_id',
        'parent_id',
        'contact_number',
        'device_token',
        'otp',
        'otp_time',
        'description',
        'is_hod',
        'district_id',
        'tehsil_id',

    ];

    protected $connection = "mysql";

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = ['roles'];


    protected $appends = ['roles_array', 'permissions_array'];

    public function getRolesArrayAttribute()
    {
        return $roles = $this->roles()->with('permissions');
    }

    public function getPermissionsArrayAttribute()
    {
        //get roles persmission
        return $roles = $this->permissions()?->pluck('slug');
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    // user and permission many to many
    public function permissions()
    {
        return $this->belongsToMany(MyPermission::class, 'permission_user', 'user_id', 'permission_id')->withTimestamps();
    }



    // user and roles many to many
    public function roles()
    {
        return $this->belongsToMany(MyRole::class, 'role_user', 'user_id', 'role_id')->withTimestamps();
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function userDepartment()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }








    public function firebaseNotifications()
    {
        return $this->morphMany(\Modules\Settings\Entities\FirebaseNotification::class, 'morphable');
    }


}
