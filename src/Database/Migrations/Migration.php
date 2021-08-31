<?php

namespace Litegram\Database\Migrations;

use Litegram\Plugins\Database;

class Migration
{
    /**
     * Создать таблицы.
     * Перед использованием User, Store, Statistics плагинов с драйвером database, необходимо применить миграцию.
     *
     * @return void
     */
    public static function up()
    {
        $schema = Database::schema();

        if (!$schema->hasTable('users')) {
            $schema->create('users', function ($table) {
                $table->bigInteger('id')->unique()->primary()->index();
                $table->text('fullname')->nullable();
                $table->text('firstname')->nullable();
                $table->string('lastname')->nullable();
                $table->string('username')->nullable();
                $table->string('locale')->default('en');
                $table->string('phone')->nullable();
                $table->string('nickname')->nullable();
                $table->string('emoji')->nullable();
                $table->string('role')->default('user')->nullable();
                $table->boolean('blocked')->default(0);
                $table->boolean('banned')->default(0);
                $table->text('ban_comment')->nullable();
                $table->bigInteger('ban_start')->nullable();
                $table->bigInteger('ban_end')->nullable();
                $table->string('source')->nullable();
                $table->string('version');
                $table->bigInteger('first_message');
                $table->bigInteger('last_message');
                $table->json('extra')->nullable();
                $table->text('note')->nullable();
            });
        }

        if (!$schema->hasTable('store')) {
            $schema->create('store', function ($table) {
                $table->text('name');
                $table->mediumText('value')->nullable();
            });
        }

        if (!$schema->hasTable('stats_users')) {
            $schema->create('stats_users', function ($table) {
                $table->id();
                $table->bigInteger('date')->nullable();
                $table->integer('count')->nullable();
            });
        }

        if (!$schema->hasTable('stats_messages')) {
            $schema->create('stats_messages', function ($table) {
                $table->id();
                $table->bigInteger('date')->nullable();
                $table->integer('count')->nullable();
            });
        }

        if (!$schema->hasTable('messages')) {
            $schema->create('messages', function ($table) {
                $table->id();
                $table->bigInteger('date')->nullable()->index();
                $table->bigInteger('user_id')->nullable();
                $table->string('type', 32)->nullable()->index();
                $table->mediumText('text')->nullable();
                $table->string('file_id', 255)->nullable();
            });
        }

        if (!$schema->hasTable('mailing')) {
            $schema->create('mailing', function ($table) {
                $table->id();
                $table->bigInteger('date');
                $table->text('text')->nullable();
                $table->string('locale', 12);
                $table->string('status', 32);
                $table->string('type', 48);
                $table->text('file')->nullable();
                $table->string('report')->nullable();
            });
        }
    }

    /**
     * Удалить созданные таблицы.
     *
     * @return void
     */
    public static function down()
    {
        $schema = Database::schema();

        $schema->dropIfExists('users');
        $schema->dropIfExists('store');
        $schema->dropIfExists('stats_users');
        $schema->dropIfExists('stats_messages');
        $schema->dropIfExists('messages');
        $schema->dropIfExists('mailing');
    }
}