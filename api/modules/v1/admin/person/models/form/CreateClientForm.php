<?php

namespace api\modules\v1\admin\person\models\form;

use Yii;
use yii\base\Model;
use api\modules\v1\admin\person\models\Contact;
use api\modules\v1\admin\person\models\ContactCustomer;
use api\modules\v1\admin\person\models\Customer;

class CreateClientForm extends Model
{
    public $name;
    public $phone;
    public $email;
    public $address_1;

    public function saveClient()
    {
        $customer = new Customer([
            "status" => Customer::STATUS_ACTIVE,
            "owner_id" => Yii::$app->user->getId()
        ]);
        $customer->load($this->getAttributes());
        $customer->save(false);
        $contact = new Contact([
            "status" => Contact::STATUS_ACTIVE
        ]);
        $contact->load($this->getAttributes());
        $contact->save(false);
        $contactCustomer = new ContactCustomer([
            "customer_id" => $customer->id,
            "contact_id" => $contact->id
        ]);
        $contactCustomer->save(false);
    }

    public function rules(): array
    {
        return [
            [['name', 'phone', 'email', 'address_1'], 'required'],
            [['phone'], 'integer'],
            [['email'], 'email'],
        ];
    }

    public function formName(): string
    {
        return "";
    }
}
