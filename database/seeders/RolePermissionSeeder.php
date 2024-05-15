<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'create_user']);
        Permission::create(['name' => 'read_user']);
        Permission::create(['name' => 'update_user']);
        Permission::create(['name' => 'delete_user']);

        Permission::create(['name' => 'create_grade']);
        Permission::create(['name' => 'read_grade']);
        Permission::create(['name' => 'update_grade']);
        Permission::create(['name' => 'delete_grade']);

        Permission::create(['name' => 'create_program']);
        Permission::create(['name' => 'read_program']);
        Permission::create(['name' => 'update_program']);
        Permission::create(['name' => 'delete_program']);

        Permission::create(['name' => 'create_task']);
        Permission::create(['name' => 'read_task']);
        Permission::create(['name' => 'update_task']);
        Permission::create(['name' => 'delete_task']);

        Permission::create(['name' => 'create_album']);
        Permission::create(['name' => 'read_album']);
        Permission::create(['name' => 'update_album']);
        Permission::create(['name' => 'delete_album']);

        Permission::create(['name' => 'create_report']);
        Permission::create(['name' => 'read_report']);
        Permission::create(['name' => 'update_report']);
        Permission::create(['name' => 'delete_report']);

        Role::create(['name' => 'teacher']);
        $roleTeacher  = Role::findByName('teacher');
        $roleTeacher->givePermissionTo('create_user');
        $roleTeacher->givePermissionTo('read_user');
        $roleTeacher->givePermissionTo('update_user');
        $roleTeacher->givePermissionTo('delete_user');

        $roleTeacher->givePermissionTo('create_grade');
        $roleTeacher->givePermissionTo('read_grade');
        $roleTeacher->givePermissionTo('update_grade');
        $roleTeacher->givePermissionTo('delete_grade');

        $roleTeacher->givePermissionTo('create_program');
        $roleTeacher->givePermissionTo('read_program');
        $roleTeacher->givePermissionTo('update_program');
        $roleTeacher->givePermissionTo('delete_program');

        $roleTeacher->givePermissionTo('create_task');
        $roleTeacher->givePermissionTo('read_task');
        $roleTeacher->givePermissionTo('update_task');
        $roleTeacher->givePermissionTo('delete_task');

        $roleTeacher->givePermissionTo('create_album');
        $roleTeacher->givePermissionTo('read_album');
        $roleTeacher->givePermissionTo('update_album');
        $roleTeacher->givePermissionTo('delete_album');

        $roleTeacher->givePermissionTo('create_report');
        $roleTeacher->givePermissionTo('read_report');
        $roleTeacher->givePermissionTo('update_report');
        $roleTeacher->givePermissionTo('delete_report');

        Role::create(['name' => 'student']);
        $roleStudent  = Role::findByName('student');
        $roleStudent->givePermissionTo('read_user');
        $roleStudent->givePermissionTo('read_grade');
        $roleStudent->givePermissionTo('read_program');
        $roleStudent->givePermissionTo('read_task');
        $roleStudent->givePermissionTo('read_album');   
        $roleStudent->givePermissionTo('read_report');
    }
}
