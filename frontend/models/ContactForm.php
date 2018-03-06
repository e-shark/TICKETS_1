<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
    public $name;
    public $email;
    public $subject;
    public $body;
    public $verifyCode;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules=
         [
            // name, email, subject and body are required
            [['name', 'email', 'subject', 'body'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
        ];
        if(Yii::$app->user->isGuest) $rules += ['verifyCode', 'captcha'];
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name'=>Yii::t('app','Name'),
            'subject'=>Yii::t('app','Subject'),
            'body'=>Yii::t('app','Body'),
            'verifyCode' => Yii::t('app','Verification Code'),
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param string $email the target email address
     * @return bool whether the email was sent
     */
    public function sendEmail($email)
    {
        try{
        $result = Yii::$app->mailer->compose()
            ->setTo($email)
            ->setFrom([$this->email => $this->name])
            ->setSubject($this->subject)
            ->setTextBody($this->body)
            ->send();
        }catch(\Exception $e){$mailererrortext = $e->getMessage();}
        $mailererrortext = mb_convert_encoding(mb_substr($mailererrortext,0,255),'UTF-8');
                
        //--- Write log to DB
        $emaillist = mb_substr( is_array( $email )?implode(";",$email):$email, 0, 255 );
        Yii::$app->db->createCommand()
            ->insert('emaillog',[
                    'emailto'=>$emaillist,
                    'emailfrom'=>$this->email,
                    'emailsubject'=>'Support: '.$this->subject,
                    'emailbody'=>'Support: '.$this->body,
                    'emaillength'=>strlen($this->body),
                    'emailresult'=>$result ? 'OK':'Error:'.$mailererrortext])
            ->execute();
        return $result;
    }
    
}
