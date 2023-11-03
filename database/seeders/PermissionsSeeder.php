<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[
            \Spatie\Permission\PermissionRegistrar::class
        ]->forgetCachedPermissions();

        // create permissions
        $arrayOfPermissionNames = [
            // Posts
            "access vendors",
            "create vendors",
            "update vendors",
            "delete vendors",
            "access vendor details",
            // Users
            "access users",
            "create users",
            "update users",
            "delete users",
            // Events
            "access events",
            "create events",
            "update events",
            "delete events",
            "access event details",
            "favorite events",
            // Venues
            "access venues",
            "create venues",
            "update venues",
            "delete venues",
            // Functions
            "access functions",
            "create functions",
            "update functions",
            "delete functions",
            "complete functions",
            // Notifications
            "access notifications",
            // Clients
            "access clients",
            "create clients",
            "update clients",
            "delete clients",
            // Tasks
            "access tasks",
            "create tasks",
            "update tasks",
            "delete tasks",
            "asign tasks",
            "complete tasks",
            //Celebrations
            "access celebrations",
            //Charts
            "access charts",
        ];
        $permissions = collect($arrayOfPermissionNames)->map(function (
            $permission
        ) {
            return ["name" => $permission, "guard_name" => "web"];
        });

        Permission::insert($permissions->toArray());

        // create role & give it permissions
        Role::create(["name" => "admin"])->givePermissionTo(Permission::all());
        Role::create(["name" => "editor"])->givePermissionTo(['access functions',"update functions"]);

        // Assign roles to users (in this case for user id -> 1 & 2)
        User::find(1)->assignRole('admin');
        User::find(2)->assignRole('editor');
    }
}
