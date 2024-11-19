<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateCompanyTest extends TestCase
{
    public function test_01_create_company(): void
    {
        // clear database untuk keperluhan automate testing
        Company::truncate();
        Employee::truncate();
        User::where('email', '!=', 'root@mail.com')->forceDelete();
        DB::statement('ALTER TABLE users AUTO_INCREMENT = 2');
        
        // proses login untuk mendapatkan token : login sebagai supr admin 
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'root@mail.com',
            'password' => 'nananina12345',
        ]);
        
        $loginResponse->assertStatus(200);
        $result = $loginResponse->json('data');
        
        // sample data untuk keperluhan create company
        $dataTestingCompany = [
            [
                "company_name" => "galang corps",
                "company_email" => "galangcorps@mail.com",
                "company_phone" => "081226163475",
                "email" => "galanggg@mail.com",
                "name" => "galang",
                "phone" => "081226163475",
                "address" => "DIY, Indonesia",
                'password' => 'nananina12345',
            ],
            [
                "company_name" => "pt galang g",
                "company_email" => "randomy@mail.com",
                "company_phone" => "081226163488",
                "email" => "galanggf@mail.com",
                "name" => "fernan",
                "phone" => "081226563475",
                "address" => "DKI Jakarta, Indonesia",
                'password' => 'nananina12345',
            ],
            [
                "company_name" => "com test failed",
                "company_email" => "testfaild@mail.com",
                "company_phone" => "081226542488",
                "email" => "testfaild@mail.com",
                "name" => "testfaild",
                "phone" => "081227453475",
                "address" => "DKI Jakarta, Indonesia",
                'password' => 'nananina12345',
            ]
        ];
        
        $Authorization = [
            'Authorization' => 'Bearer ' . $result['token'], 
        ];
        
        // [1] testing create company tanpa token akses, hasil testing harusnya gagal dengan code error 401!! 
        $createcompany_failed_01 = $this->postJson('/api/company/store', [
                'name' => 'test manager',
                'company_name' => 'test company',
            ]);
        $createcompany_failed_01->assertStatus(401);
        
        // [2] testing create company dengan beberapa field tidak di sertakan untuk mengecek validasi field reqiuired
        $data_test_submit = $dataTestingCompany[0];
        unset($data_test_submit['name']);
        unset($data_test_submit['company_name']);
        $createcompany_failed_02 = $this->postJson(
            '/api/company/store', 
            $data_test_submit,
            $Authorization
        );
        $createcompany_failed_02->assertStatus(422);
        
        // [3] testing create company dengan input email tidak valid
        $data_test_submit = $dataTestingCompany[0];
        $data_test_submit['email'] = "testemail.com";
        $createcompany_failed_03 = $this->postJson(
            '/api/company/store', 
            $data_test_submit,
            $Authorization
        );
        $createcompany_failed_03->assertStatus(422);
        // print("    asdas");
        
        // [4] testing create company dengan input valid, ekspektasi berhasil di create
        $data_test_submit = $dataTestingCompany[0];
        $createcompany_failed_04 = $this->postJson(
            '/api/company/store', 
            $data_test_submit,
            $Authorization
        );
        $createcompany_failed_04->assertStatus(200);
        
        // [5] testing create company dengan input valid, ekspektasi failed karena input yang digunakan sama dengan sebelumnya 
        $data_test_submit = $dataTestingCompany[0];
        $createcompany_failed_05 = $this->postJson(
            '/api/company/store', 
            $data_test_submit,
            $Authorization
        );
        $createcompany_failed_05->assertStatus(422);
        
        // [6] testing create company dengan input valid, ekspektasi berhasik return 200 karena input berbeda dari sebelumnya
        $data_test_submit = $dataTestingCompany[1];
        $createcompany_failed_06 = $this->postJson(
            '/api/company/store', 
            $data_test_submit,
            $Authorization
        );
        $createcompany_failed_06->assertStatus(200);
        
        // [7] test get list company, dengan ekspektasi gagal karena tidak menggunakan token akses
        $get_list_company = $this->get('/api/employee');
        $get_list_company->assertStatus(401);
        
        // [8] test get list company, dengan ekspektasi berhasil
        $get_list_company = $this->get('/api/employee', $Authorization);
        $get_list_company->assertStatus(200);
        
        // [9] test create company dengan akun role manager, ekspektasi gagal karena hanya super admin yang dapat create company
        // proses login untuk mendapatkan token : login sebagai manager company [0]
        $loginResponse_o2 = $this->postJson('/api/login', [
            'email' => $dataTestingCompany[0]['email'],
            'password' => $dataTestingCompany[0]['password'],
        ]);
        
        $loginResponse_o2->assertStatus(200);
        $result = $loginResponse_o2->json('data');
        $Authorization_02 = [
            'Authorization' => 'Bearer ' . $result['token'], 
        ];
        
        $data_test_submit = $dataTestingCompany[2];
        $createcompany_failed_04 = $this->postJson(
            '/api/company/store', 
            $data_test_submit,
            $Authorization_02
        );
        $createcompany_failed_04->assertStatus(422);
        
        
        // $createResponse = $this->postJson('/api/create-endpoint', [
        //     'name' => 'Test Item',
        //     'description' => 'This is a test description',
        // ], [
        //     'Authorization' => 'Bearer ' . $token, // Tambahkan token dalam header
        // ]);

        // // Assert respons create
        // $createResponse->assertStatus(201); // Sesuaikan status
        // $createResponse->assertJson([
        //     'success' => true,
        //     'data' => [
        //         'name' => 'Test Item',
        //         'description' => 'This is a test description',
        //     ],
        // ]);
    }
}
