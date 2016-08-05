<?php
use yii\db\Migration;

class m000000_000000_create_photo_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('photo',
            [
                'id'            => $this->primaryKey(),
                'file'          => $this->string(255)->notNull(),
                'file_small'    => $this->string(255)->notNull(),
                'type'          => $this->string(32)->notNull(),
                'object_id'     => $this->integer(),
                'user_id'       => $this->integer(),
                'deleted'       => $this->boolean()->notNull()->defaultValue(false),
                'created_at'    => $this->integer(),
                'updated_at'    => $this->integer(),
            ]
        );

        $this->addForeignKey('photo_user_fk', '{{%photo}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('photo');
    }
}
