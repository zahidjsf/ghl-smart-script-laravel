<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helper\CRM;


class CrmToken extends Model
{
    use HasFactory;

    protected $table = 'oauth';
    public $timestamps = false;

    public function urefresh(): bool
    {
        $is_refresh = false;
        try {
                list($is_refresh, $token) = CRM::getRefreshToken($this->user_id, $this, true);
                // list($is_refresh, $token) = CRM::getRefreshToken($this->user_id, $this, true);
                //\Log::info("Token refreshed successfully. New token:". $token);
        } catch (\Exception $e) {
            return 500;
        }
        return $is_refresh;
    }


}
