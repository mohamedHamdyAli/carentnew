<?php

namespace App\Models;

use App\Traits\Uuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Vehicle
 *
 * @property string $id
 * @property string $user_id
 * @property string $country_id
 * @property string $state_id
 * @property string|null $category_id
 * @property string|null $brand_id
 * @property string|null $model_id
 * @property string|null $plate_number
 * @property string|null $manufacture_year
 * @property string|null $color
 * @property string|null $fuel
 * @property string|null $features
 * @property int|null $seat_count
 * @property int|null $rating
 * @property int $views
 * @property int $rented
 * @property int $active
 * @property string|null $inactive_message
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\VehicleFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle query()
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereFuel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereInactiveMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereManufactureYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle wherePlateNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereRented($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereSeatCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereViews($value)
 * @mixin \Eloquent
 */
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
        'active',
        'views',
        'owner',
        'country',
        'images',
        'fuel_type',
        'features',
        'pricing',
        'verified',
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
        'fuel_type',
        'features',
        'pricing',
        'verified',
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
        return $this->belongsTo(VehicleFeature::class);
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

    public function getDailyPriceAttribute()
    {
        return $this->VehiclePricing()->first()->daily_price;
    }

    public function getVerifiedAttribute()
    {
        return $this->verified_at !== null;
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
            $images[] = url(Storage::url($image->image));
        }
        return $images;
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
        return url(Storage::url($this->thumbnail));
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
}
