<?php

namespace Litegram\Database;

use Litegram\Modules\Database;

class Migration
{
    /**
     * Создать таблицы.
     * Перед использованием User, Store, Statistics модулей, необходимо применить миграцию.
     *
     * @return void
     */
    public static function up()
    {
        $schema = Database::schema();

        if (!$schema->hasTable('users')) {
            $schema->create('users', function ($table) {
                $table->id();
                $table->bigInteger('user_id')->unique();
                $table->boolean('active');
                $table->text('fullname')->nullable();
                $table->text('firstname')->nullable();
                $table->string('lastname')->nullable();
                $table->string('username')->nullable();
                $table->string('lang', 3);
                $table->string('role')->nullable();
                $table->string('nickname')->nullable();
                $table->string('emoji')->nullable();
                $table->boolean('banned');
                $table->text('ban_comment')->nullable();
                $table->bigInteger('ban_date_from')->nullable();
                $table->bigInteger('ban_date_to')->nullable();
                $table->string('source')->nullable();
                $table->string('version');
                $table->bigInteger('first_message');
                $table->bigInteger('last_message');
                $table->mediumText('note')->nullable();
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
                $table->bigInteger('date')->nullable();
                $table->bigInteger('user_id')->nullable();
                $table->string('type', 32)->nullable();
                $table->mediumText('text')->nullable();
                $table->string('file_id', 255)->nullable();
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
    }
}