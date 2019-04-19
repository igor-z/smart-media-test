<?php

/**
 * @var $this yii\web\View
 * @var DataProviderInterface $dataProvider
 * @var BookSearch $searchModel
 */

use app\models\BookSearch;
use yii\data\DataProviderInterface;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

$this->title = 'Index';
?>
<div class="site-index">
	<?php $form = ActiveForm::begin([
		'id' => 'books-filter',
		'method' => 'GET',
		'enableClientValidation' => false,
		'errorCssClass' => '',
	])?>
		<div class="row">
			<div class="col-sm-4">
				<?=$form->field($searchModel, 'bookId')
					->error(false)
					->label('Книги')
					->dropDownList($searchModel->getBookList(), [
	                    'multiple' => true,
	                    'size' => 6,
	                ])?>
			</div>

			<div class="col-sm-4">
                <?=$form->field($searchModel, 'authorId')
                    ->error(false)
	                ->label('Авторы')
	                ->dropDownList($searchModel->getAuthorList(), [
	                    'multiple' => true,
	                    'size' => 6,
	                ])?>
			</div>

			<div class="col-sm-4">
                <?=$form->field($searchModel, 'shopId')
                    ->error(false)
	                ->label('Магазины')
	                ->dropDownList($searchModel->getShopList(), [
	                    'multiple' => true,
	                    'size' => 6,
	                ])?>
			</div>
		</div>
	<?php ActiveForm::end()?>

	<div class="row">
		<h2>Книги</h2>
		<?php Pjax::begin([
			'id' => 'books-pjax',
			'enablePushState' => false,
			'enableReplaceState' => false,
		])?>
			<?php foreach ($dataProvider->getModels() as $book):?>
				<div class="col-sm-2">
					<div>Название: <?=Html::encode($book['name'])?></div>
					<div>Авторы: <?=Html::encode(implode(', ', array_column($book['authors'], 'name')))?></div>
					<div>Год выпуска: <?=Yii::$app->formatter->asDate($book['issued_at'], 'Y')?></div>
					<div>Магазины: <?=Html::encode(implode(', ', array_column($book['shops'], 'name')))?></div>

					<div>Фильтры: <?=Html::encode(implode(', ', json_decode($book['used_filter'], true)))?></div>
				</div>
			<?php endforeach;?>
		<?php Pjax::end()?>
	</div>
</div>
