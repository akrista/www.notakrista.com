<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Support\Config;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $teams = Config::teamsEnabled();
        $registrar = resolve(PermissionRegistrar::class);

        /**
         * See `docs/prerequisites.md` for suggested lengths on 'name' and 'guard_name' if "1071 Specified key was too long" errors are encountered.
         */
        Schema::create(Config::permissionsTable(), static function (Blueprint $table): void {
            $table->uuid('id')->primary(); // permission id
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        /**
         * See `docs/prerequisites.md` for suggested lengths on 'name' and 'guard_name' if "1071 Specified key was too long" errors are encountered.
         */
        Schema::create(Config::rolesTable(), static function (Blueprint $table) use ($teams): void {
            $table->uuid('id')->primary(); // role id
            if ($teams || config('permission.testing')) { // permission.testing is a fix for sqlite testing
                $table->unsignedBigInteger(Config::teamForeignKey())->nullable();
                $table->index(Config::teamForeignKey(), 'roles_team_foreign_key_index');
            }

            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            if ($teams || config('permission.testing')) {
                $table->unique([Config::teamForeignKey(), 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });

        Schema::create(Config::modelHasPermissionsTable(), static function (Blueprint $table) use ($teams, $registrar): void {
            $table->uuid($registrar->pivotPermission);

            $table->string('model_type');
            $table->uuid(Config::morphKey());
            $table->index([Config::morphKey(), 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign($registrar->pivotPermission)
                ->references('id') // permission id
                ->on(Config::permissionsTable())
                ->cascadeOnDelete();
            if ($teams) {
                $table->unsignedBigInteger(Config::teamForeignKey());
                $table->index(Config::teamForeignKey(), 'model_has_permissions_team_foreign_key_index');

                $table->primary(
                    [Config::teamForeignKey(), $registrar->pivotPermission, Config::morphKey(), 'model_type'],
                    'model_has_permissions_permission_model_type_primary'
                );
            } else {
                $table->primary(
                    [$registrar->pivotPermission, Config::morphKey(), 'model_type'],
                    'model_has_permissions_permission_model_type_primary'
                );
            }
        });

        Schema::create(Config::modelHasRolesTable(), static function (Blueprint $table) use ($teams, $registrar): void {
            $table->uuid($registrar->pivotRole);

            $table->string('model_type');
            $table->uuid(Config::morphKey());
            $table->index([Config::morphKey(), 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign($registrar->pivotRole)
                ->references('id') // role id
                ->on(Config::rolesTable())
                ->cascadeOnDelete();
            if ($teams) {
                $table->unsignedBigInteger(Config::teamForeignKey());
                $table->index(Config::teamForeignKey(), 'model_has_roles_team_foreign_key_index');

                $table->primary(
                    [Config::teamForeignKey(), $registrar->pivotRole, Config::morphKey(), 'model_type'],
                    'model_has_roles_role_model_type_primary'
                );
            } else {
                $table->primary(
                    [$registrar->pivotRole, Config::morphKey(), 'model_type'],
                    'model_has_roles_role_model_type_primary'
                );
            }
        });

        Schema::create(Config::roleHasPermissionsTable(), static function (Blueprint $table) use ($registrar): void {
            $table->uuid($registrar->pivotPermission);
            $table->uuid($registrar->pivotRole);

            $table->foreign($registrar->pivotPermission)
                ->references('id') // permission id
                ->on(Config::permissionsTable())
                ->cascadeOnDelete();

            $table->foreign($registrar->pivotRole)
                ->references('id') // role id
                ->on(Config::rolesTable())
                ->cascadeOnDelete();

            $table->primary([$registrar->pivotPermission, $registrar->pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });

        $registrar->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Config::roleHasPermissionsTable());
        Schema::dropIfExists(Config::modelHasRolesTable());
        Schema::dropIfExists(Config::modelHasPermissionsTable());
        Schema::dropIfExists(Config::rolesTable());
        Schema::dropIfExists(Config::permissionsTable());
    }
};
