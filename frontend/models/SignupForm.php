<?php

namespace frontend\models;

use common\models\File;
use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required', 'message' => Yii::t('app', 'ЖСН толтырылмаған!')],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('app', 'Бұл ЖСН бос емес!')],
            ['username', 'match', 'pattern' => '/^\d{12}$/', 'message' => Yii::t('app', 'ЖСН 12 саннан тұруы тиіс!')],

            ['email', 'trim'],
            ['email', 'required', 'message' => Yii::t('app', 'Пошта толтырылмаған!')],
            ['email', 'email', 'message' => Yii::t('app', 'Пошта example@mail.com форматында болуы тиіс!')],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('app', 'Бұл пошта бос емес!')],

            ['password', 'required', 'message' => Yii::t('app', 'Құпия сөз толтырылмаған!')],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength'], 'tooShort' => Yii::t('app', 'Құпия сөз кемінде 8 таңба болуы тиіс!')],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup($teacher)
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->save();

        $teacher->user_id = $user->id;
        $teacher->save();

        $receipt = new File();
        $receipt->teacher_id = $teacher->id;
        $receipt->type = 'receipt';
        $receipt->save();

        return true;
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user, $teacher)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user, 'teacher' => $teacher]
            )
            ->setFrom(['beibarys.mukhammedyarov@alumni.nu.edu.kz' => Yii::t('app', Yii::$app->name)])
            ->setTo($this->email)
            ->setSubject(Yii::t('app', Yii::$app->name))
            ->send();
    }
}
