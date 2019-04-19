<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\Expression as DbExpression;
use yii\db\Query;
use yii\validators\InlineValidator;

class BookSearch extends Model
{
    public $bookId;
    public $shopId;
    public $authorId;

    public function rules()
    {
        return [
            ['bookId', 'each', 'rule' => ['in', 'range' => array_keys($this->getBookList())]],
            ['shopId', 'each', 'rule' => ['in', 'range' => array_keys($this->getShopList())]],
            ['authorId', 'each', 'rule' => ['in', 'range' => array_keys($this->getAuthorList())]],
            [['authorId', 'bookId', 'shopId'], function ($attribute, $params, InlineValidator $validator) {
                if (!$this->authorId && !$this->shopId && !$this->bookId) {
                    $validator->addError($this, $attribute, 'Хотя бы один из фильтров должен быть выбран');
                }
            }, 'skipOnEmpty' => false],
        ];
    }

    public function getBookList()
    {
        return Book::find()
            ->select(['name', 'id'])
            ->indexBy('id')
            ->column();
    }

    public function getShopList()
    {
        return Shop::find()
            ->select(['name', 'id'])
            ->indexBy('id')
            ->column();
    }

    public function getAuthorList()
    {
        return Author::find()
            ->select(['name', 'id'])
            ->indexBy('id')
            ->column();
    }

    /**
     * @return DataProviderInterface
     */
    public function search() : DataProviderInterface
    {
        if (!$this->validate()) {
            $dataProvider = new ActiveDataProvider([
                'query' => (new Query())
                    ->select('*')
                    ->from('book')
                    ->andWhere('false')
            ]);
            return $dataProvider;
        }

        $query = null;
        if ($this->bookId)
            $query = $this->filterByBookId($query);
        if ($this->authorId)
            $query = $this->filterByAuthor($query);
        if ($this->shopId)
            $query = $this->filterByShop($query);

        $query = Book::find()
            ->select([new DbExpression('JSON_ARRAYAGG(used_filter) as used_filter'), 'book.name', 'book.id', 'book.issued_at'])
            ->from(['book' => $query])
            ->with(['authors', 'shops'])
            ->groupBy(['book.id', 'book.name', 'book.issued_at'])
            ->asArray();

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        return $dataProvider;
    }

    /**
     * @param Query|null $query
     * @return Query
     */
    private function filterByBookId($query)
    {
        if ($this->bookId) {
            $innerQuery = (new Query())
                ->select(['book.id', 'book.name', 'book.issued_at', new DbExpression('"Книги" AS used_filter')])
                ->from('book')
                ->where([
                    'id' => $this->bookId
                ]);

            if ($query)
                $query->union($innerQuery);
            else
                $query = $innerQuery;
        }

        return $query;
    }

    /**
     * @param Query|null $query
     * @return Query
     */
    private function filterByAuthor($query)
    {
        if ($this->authorId) {
            $innerQuery = (new Query())
                ->select(['book.id', 'book.name', 'book.issued_at', new DbExpression('"Авторы" AS used_filter')])
                ->from('book')
                ->innerJoin('book_author', '`book_author`.`book_id` = `book`.`id`')
                ->where([
                    'author_id' => $this->authorId
                ])
                ->groupBy(['book.id', 'book.name', 'book.issued_at', 'used_filter']);

            if ($query)
                $query->union($innerQuery);
            else
                $query = $innerQuery;
        }

        return $query;
    }

    /**
     * @param Query|null $query
     * @return Query
     */
    private function filterByShop($query)
    {
        if ($this->shopId) {
            $innerQuery = (new Query())
                ->select(['book.id', 'book.name', 'book.issued_at', new DbExpression('"Магазины" AS used_filter')])
                ->from('book')
                ->innerJoin('book_destination', '`book_destination`.`book_id` = `book`.`id`')
                ->where([
                    'shop_id' => $this->shopId
                ])
                ->groupBy(['book.id', 'book.name', 'book.issued_at', 'used_filter']);

            if ($query)
                $query->union($innerQuery);
            else
                $query = $innerQuery;
        }

        return $query;
    }
}