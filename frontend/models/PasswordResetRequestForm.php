<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            [
                'email', 'required', 'message' => Yii::t('app', 'Пошта толтырылмаған!'),
            ],
            [
                'email', 'email', 'message' => Yii::t('app', 'Пошта example@mail.com форматында болуы тиіс!'),
            ],
            ['email', 'exist',
                'targetClass' => '\common\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'Бұл пошта тіркелмеген!'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }
        
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user]
            )
            ->setFrom(['beibarys.mukhammedyarov@alumni.nu.edu.kz' => Yii::t('app', Yii::$app->name)])
            ->setTo($this->email)
            ->setSubject(Yii::t('app', Yii::$app->name))
            ->send();
    }
}
