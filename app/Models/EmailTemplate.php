<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;


class EmailTemplate extends Model
{
    protected $fillable = ['user_id', 'template_id', 'trigger_days'];
    // use HasFactory;

    static function getEmailTemplateData(int $user_id = 0, int $email_template = 0)
    {
        $q = EmailTemplate::with('getTemplates')
            ->orderBy('trigger_days');

        if( $user_id > 0 )
            $q = $q->where('user_id', $user_id);

        if( $email_template > 0 )
            $q = $q->where('id', $email_template);

        return $q->get()->toArray();
    }

    public function getTemplates()
    {
        return $this->BelongsTo(Template::class, 'template_id');
    }
}
