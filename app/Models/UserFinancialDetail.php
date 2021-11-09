<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserFinancialDetail
 *
 * @property int $id
 * @property string $user_id
 * @property string $country_id
 * @property string $state_id
 * @property string $bank
 * @property string|null $branch
 * @property string $bank_account_number
 * @property string|null $swift_code
 * @property int $active
 * @property string|null $inactive_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserFinancialDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserFinancialDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserFinancialDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserFinancialDetail whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFinancialDetail whereBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFinancialDetail whereBankAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFinancialDetail whereBranch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFinancialDetail whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFinancialDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFinancialDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFinancialDetail whereInactiveMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFinancialDetail whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFinancialDetail whereSwiftCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFinancialDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFinancialDetail whereUserId($value)
 * @mixin \Eloquent
 */
class UserFinancialDetail extends Model
{
    use HasFactory;
}
