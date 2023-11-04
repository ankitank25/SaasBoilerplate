<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpaceRole extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'space_id',
        'name',
        'description',
        'abilities',
    ];

    public static function getPredefineRoleResources(string $roleName)
    {
        $permissions = [];
        $role = config("access.roles");
        if(isset($role[$roleName])) {
            foreach($role[$roleName] as $section => $resources) {
                if(is_array($resources)) {
                    foreach($resources as $area => $resource) {
                        foreach($resource as $item) {
                            $permissions[] = $section."-".$area."-".$item;
                        }
                    }
                }
            }
        }

        return $permissions;
    }
}
