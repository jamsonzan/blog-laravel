<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Deleted_article
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $head
 * @property string $body
 * @property int $public
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deleted_article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deleted_article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deleted_article query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deleted_article whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deleted_article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deleted_article whereHead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deleted_article whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deleted_article wherePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deleted_article whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deleted_article whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deleted_article whereUserId($value)
 * @mixin \Eloquent
 */
class Deleted_article extends Model
{
    //
}
