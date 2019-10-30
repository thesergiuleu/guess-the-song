<?php

namespace App;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Song
 *
 * @property int $id
 * @property string $spotify_song_id
 * @property string|null $artist
 * @property string|null $name
 * @property string|null $album
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Song newModelQuery()
 * @method static Builder|Song newQuery()
 * @method static Builder|Song query()
 * @method static Builder|Song whereAlbum($value)
 * @method static Builder|Song whereArtist($value)
 * @method static Builder|Song whereCreatedAt($value)
 * @method static Builder|Song whereId($value)
 * @method static Builder|Song whereName($value)
 * @method static Builder|Song whereSpotifySongId($value)
 * @method static Builder|Song whereUpdatedAt($value)
 * @mixin Eloquent
 * @property string $preview_url
 * @property string $spotify_url
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Song wherePreviewUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Song whereSpotifyUrl($value)
 */
class Song extends Model
{
    protected $fillable = ['spotify_song_id', 'artist', 'name', 'album'];

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_songs', 'song_id', 'user_id');
    }
}
