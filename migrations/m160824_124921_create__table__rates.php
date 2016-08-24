<?php

use yii\db\Migration;

class m160824_124921_create__table__rates extends Migration
{
    public function up()
    {
        $this->createTable('rates', [
            'id' => $this->primaryKey(),
            'name' => $this->char(15)->notNull(),
            'value' => $this->float('10,4')->defaultValue(null),
            'date' => $this->timestamp()
        ]);
    }

    public function down()
    {
        $this->dropTable('rates');
    }
}
