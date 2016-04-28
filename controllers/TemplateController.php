<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */
 
namespace lowbase\document\controllers;

use lowbase\document\models\FieldSearch;
use Yii;
use lowbase\document\models\Template;
use lowbase\document\models\TemplateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
//use yii\filters\AccessControl;

/**
 * Шаблоны документов
 *
 * Абсолютные пути Views использованы, чтобы при наследовании
 * происходила связь с отображениями модуля родителя.
 *
 * Class TemplateController
 * @package lowbase\document\controllers
 */
class TemplateController extends Controller
{
    public function behaviors()
    {
        return [
// Ограничение доступа к операциям, связанным с шаблонами
// Активировать при подключении пользователей и разделений прав
//            'access' => [
//                'class' => AccessControl::className(),
//                'only' => ['index', 'view', 'create', 'update', 'delete', 'multidelete'],
//                'rules' => [
//                ],
//            ],
        ];
    }

    /**
     * Менеджер шаблонов (список таблицей)
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('@vendor/lowbase/yii2-document/views/template/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Просмотр шаблона (карточка шаблона)
     * @param $id - ID шаблона
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('@vendor/lowbase/yii2-document/views/template/view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Создание шаблона
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Template();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('document', 'Новый шаблон создан.'));
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('@vendor/lowbase/yii2-document/views/template/create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Редактирование шаблона
     * @param $id - ID шаблона
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $searchModel = new FieldSearch();
        $searchModel->template_id = $model->id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('document', 'Шаблон отредактирован.'));
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('@vendor/lowbase/yii2-document/views/template/update', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Удаление шаблона
     * @param $id - ID шаблона
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->getSession()->setFlash('success', Yii::t('document', 'Шаблон удален.'));

        return $this->redirect(['index']);
    }

    /**
     * Множественное удаление шаблонов
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
            Yii::$app->getSession()->setFlash('success', Yii::t('document', 'Шаблоны удалены.'));
        }
        return true;
    }

    /**
     * Поиск модели шаблона по ID
     * @param integer $id
     * @return Template the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Template::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('document', 'Запрашиваемая страница не найдена.'));
        }
    }
}
