<?php
use Migrations\AbstractMigration;

class CreateUserTokens extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('user_tokens');
        $table->addColumn('user_id', 'integer')
            ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->addIndex(['user_id',]);
        $table->addColumn('token', 'string', [
            'limit' => 255,
        ])->addIndex(['token'], [
            'name' => 'USER_TOKEN_TOKEN_UNIQUE',
            'unique' => true,
        ]);
        $table->addColumn('expiry_at', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->create();
    }
}
