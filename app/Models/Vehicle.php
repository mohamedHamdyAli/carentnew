<?php

namespace App\Models;

use App\Traits\Uuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Vehicle extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'user_id',
        'country_id',
        'state_id',
        'category_id',
        'brand_id',
        'model_id',
        'thumbnail',
        'plate_number',
        'manufacture_year',
        'color',
        'fuel_type_id',
        'seat_count',
        'rating',
        'views',
        'rented',
        'active',
        'verified_at',
    ];

    protected $casts = [
        'rating' => 'float',
        'active' => 'boolean',
        'verified' => 'boolean',
    ];

    protected $hidden = [
        'user_id',
        'user',
        'country_id',
        'state_id',
        'category_id',
        'brand_id',
        'model_id',
        'thumbnail',
        'plate_number',
        'manufacture_year',
        'thumbnail_url',
        'color',
        'fuel_type_id',
        'rented',
        'views',
        'owner',
        'country',
        'images',
        'features',
        'pricing',
        'rating',
        'rating_count',
        'status',
        'verified',
        'vehicle_features',
        'status',
        'verified_at',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'owner',
        'country',
        'state',
        'category',
        'thumbnail_url',
        'images',
        'daily_price',
        'brand',
        'model',
        'vehicle_features',
        'fuel_type',
        'features',
        'pricing',
        'verified',
        'status',
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function Country()
    {
        return $this->belongsTo(Country::class);
    }

    public function State()
    {
        return $this->belongsTo(State::class);
    }

    public function Category()
    {
        return $this->belongsTo(Category::class);
    }

    public function Brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function BrandModel()
    {
        return $this->belongsTo(BrandModel::class, 'model_id');
    }

    public function VehicleFeatures()
    {
        return $this->hasMany(VehicleFeature::class);
    }

    public function getVehicleFeaturesAttribute()
    {
        $features = [];
        foreach ($this->VehicleFeatures()->get() as $feature) {
            $features[] = $feature->feature->id;
        }
        return $features;
    }

    public function VehicleImages()
    {
        return $this->hasMany(VehicleImage::class);
    }

    public function VehicleLicense()
    {
        return $this->hasOne(VehicleLicense::class);
    }

    public function VehicleInsurance()
    {
        return $this->hasOne(VehicleInsurance::class);
    }

    public function VehiclePricing()
    {
        return $this->hasOne(VehiclePricing::class);
    }

    public function VehicleVerification()
    {
        return $this->hasOne(VehicleVerification::class);
    }

    public function getDailyPriceAttribute()
    {
        return $this->VehiclePricing()->first()->daily_price ?? null;
    }

    public function getVerifiedAttribute()
    {
        return $this->verified_at !== null;
    }

    public function getStatusAttribute()
    {
        return $this->VehicleVerification()->first()->status ?? null;
    }

    public function Features()
    {
        return $this->hasManyThrough(Feature::class, VehicleFeature::class, 'vehicle_id', 'id', 'id', 'feature_id');
    }

    public function FuelType()
    {
        return $this->belongsTo(FuelType::class);
    }

    public function getImagesAttribute()
    {
        $data = $this->VehicleImages()->orderBy('display_order')->get();
        $images = [];
        foreach ($data as $image) {
            $images[] = url(Storage::url($image->image), [], true);
        }
        return $images;
    }

    public function Orders()
    {
        return $this->hasMany(Order::class, 'vehicle_id');
    }

    public function getPricingAttribute()
    {
        return $this->VehiclePricing()->first();
    }

    public function getOwnerAttribute()
    {
        return $this->user()->first()->name;
    }

    public function getCountryAttribute()
    {
        return $this->country()->first()->name;
    }

    public function getStateAttribute()
    {
        return $this->state()->first()->name;
    }

    public function getCategoryAttribute()
    {
        return $this->category()->first()->name;
    }

    public function getBrandAttribute()
    {
        return $this->brand()->first()->name;
    }

    public function getModelAttribute()
    {
        return $this->BrandModel()->first()->name;
    }

    public function getFuelTypeAttribute()
    {
        return $this->fuelType()->first()->name;
    }

    public function getThumbnailUrlAttribute()
    {
        return url(Storage::url($this->thumbnail, [], true));
    }

    public function getFeaturesAttribute()
    {
        $data = $this->Features()->get();
        $features = [];
        foreach ($data as $feature) {
            $features[] = $feature->name;
        }
        return $features;
    }

    public function syncVehicleFeatures($features)
    {
        $this->VehicleFeatures()->whereNotIn('feature_id', $features)->delete();
        foreach ($features as $feature) {
            $this->VehicleFeatures()->updateOrCreate([
                'vehicle_id' => $this->id,
                'feature_id' => $feature,
            ]);
        }
    }

    public function updateThumbnail($thumbnailId)
    {
        $thumbnail = TempFile::where('id', $thumbnailId)->first();
        $newThumbnailLocation = 'public/vehicles/' . $this->id . '/thumbnail/' . Carbon::now()->timestamp . '_' . $thumbnail->name;
        Storage::move($thumbnail->path, $newThumbnailLocation);
        $this->thumbnail = $newThumbnailLocation;
        $this->save();
    }

    public function addVehicleImages($images)
    {
        $counter = 0;
        foreach ($images as $image) {
            $tempFile = TempFile::where('id', $image)->first();
            $newImageLocation = 'public/vehicles/' . $this->id . '/images/' . Carbon::now()->timestamp . '_' . $tempFile->name;
            Storage::move($tempFile->path, $newImageLocation);
            VehicleImage::create([
                'vehicle_id' => $this->id,
                'display_order' => $counter,
                'image' => $newImageLocation,
            ]);
            $counter++;
        }
    }

    // if vehicle has insurance
    public function hasInsurance()
    {
        $insurance = $this->VehicleInsurance()->first();
        return $insurance !== null && $insurance->expire_at > Carbon::now() && $insurance->image;
    }

    // if vehicle has license
    public function hasLicense()
    {
        $license = $this->VehicleLicense()->first();
        return $license !== null && $license->expire_at > Carbon::now() && $license->front_image && $license->back_image;
    }

    public function isVerified()
    {
        return $this->verified_at !== null;
    }
}
