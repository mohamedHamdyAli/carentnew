<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\IdentityDocument
 *
 * @property string $id
 * @property string $user_id
 * @property string $front_image
 * @property string $back_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|IdentityDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IdentityDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IdentityDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder|IdentityDocument whereBackImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IdentityDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IdentityDocument whereFrontImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IdentityDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IdentityDocument whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IdentityDocument whereUserId($value)
 * @mixin \Eloquent
 */
class IdentityDocument extends Model
{
    use Uuid;
}
