<?php

use yii\db\Migration;

/**
 * Class m190418_145622_init
 */
class m190418_145622_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('shop', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);

        $this->createTable('author', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);

        $this->createTable('book', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'issued_at' => $this->dateTime(),
        ]);

        $this->createTable('book_author', [
            'book_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
        ]);
        $this->addPrimaryKey('pk_book_author', 'book_author', ['book_id', 'author_id']);
        $this->addForeignKey('fk_book_author_author', 'book_author', 'author_id', 'author', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_book_author_book', 'book_author', 'book_id', 'book', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('book_destination', [
            'book_id' => $this->integer()->notNull(),
            'shop_id' => $this->integer()->notNull(),
        ]);
        $this->addPrimaryKey('pk_book_destination', 'book_destination', ['book_id', 'shop_id']);
        $this->addForeignKey('fk_book_destination_book', 'book_destination', 'book_id', 'book', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_book_destination_shop', 'book_destination', 'shop_id', 'shop', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_book_destination_book', 'book_destination');
        $this->dropForeignKey('fk_book_destination_shop', 'book_destination');
        $this->dropTable('book_destination');

        $this->dropForeignKey('fk_book_author_book', 'book_author');
        $this->dropForeignKey('fk_book_author_author', 'book_author');
        $this->dropTable('book_author');

        $this->dropTable('book');
        $this->dropTable('author');
        $this->dropTable('shop');
    }
}
