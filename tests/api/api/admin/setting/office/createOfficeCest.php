<?php

namespace tests\api\admin\setting\office;

use tests\api\ApiTester;

class createOfficeCest
{
    public function createOfficeViaAPI(ApiTester $I)
    {
        $request=[
            "name"          => "CodeException Tested ".time(),
            "address1"      => "Ấp Tân An, Xã Khánh Thành",
            "email"         => "dev@gmail.com",
            "status"        => 1,
            "description"   => "Đây là ghi chú được tạo từ CodeExcetion"
        ];
        $I->haveHttpHeader("Content-Type", "application/x-www-form-urlencoded");
        $I->sendPost("admin/setting/office/form/create", $request);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status' => true]);
        $I->seeResponseContainsJson([
            'office' => $request
          ]);
    }
}
