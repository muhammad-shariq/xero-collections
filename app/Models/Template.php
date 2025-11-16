<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'document', 'email_name', 'email_from', 'email_subject', 'email_message'];
}
