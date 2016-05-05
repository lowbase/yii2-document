<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\document\controllers;

use lowbase\document\models\Template;
use Yii;
use lowbase\document\models\Field;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Дополнительные поля шаблона
 *
 * Абсолютные пути Views использованы, чтобы при наследовании
 * происходила связь с отображениями модуля родителя.
 *
 * Class FieldController
 * @package lowbase\document\controllers
 */
class FieldController extends Controller
{
    public function behaviors()
    {
        return [
// Ограничение доступа к операциям, связанным с шаблонами
// Активировать при подключении пользователей и разделений прав
//            'access' => [
//                'class' => AccessControl::className(),
//                'only' => ['create', 'update', 'delete', 'multidelete'],
//                'rules' => [
//                ],
//            ],
        ];
    }

    /**
     * Создание нового поля шаблона
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Field();
        $model->template_id = Yii::$app->request->get('template_id');
        $model->min = 0;
        $model->max = 1;
        $template = Template::findOne($model->template_id);
        if ($template == null) {
            throw new NotFoundHttpException(Yii::t('document', 'Запрашиваемая страница не найдена'));
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('document', 'Новый поле создано.'));
            return $this->redirect(['template/update', 'id' => $model->template_id]);
        } else {
            return $this->render('@vendor/lowbase/yii2-document/views/field/create', [
                'model' => $model,
                'template' => $template,
            ]);
        }
    }

    /**
     * Редактирование поля шаблона
     * @param $id - ID поля
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('document', 'Поле отредактировано.'));
            return $this->redirect(['template/update', 'id' => $model->template_id]);
        } else {
            return $this->render('@vendor/lowbase/yii2-document/views/field/update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Удаление дополнительного поля
     * @param $id - ID поля
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $template_id = $model->template_id;
        $model->delete();
        Yii::$app->getSession()->setFlash('success', Yii::t('document', 'Поле удалено.'));

        return $this->redirect(['template/update', 'id' => $template_id]);
    }

    /**
     * Поиск модели (поле шаблона по ID)
     * @param integer $id - ID поля шаблона
     * @return Field the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Field::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('document', 'Запрашиваемая страница не найдена'));
        }
    }

    /**
     * Множественное удаление полей
     * @return bool
     * @throws NotFoundHttpException
     */
    public function actionMultidelete()
    {
        $models = Yii::$app->request->post('keys');
        if ($models) {
            foreach ($models as $id) {
                $this->findModel($id)->delete();
            }
            Yii::$app->getSession()->setFlash('success', Yii::t('document', 'Поля удалены.'));
        }
        return true;
    }
}
