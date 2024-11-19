<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CrudEmployeeTest extends TestCase
{
    protected array $dataTestingEmployee; 

    protected function setUp(): void
    {
        parent::setUp();

        $this->dataTestingEmployee = [
            // MANAGER
            [
                "email" => "galanggg@mail.com",
                "name" => "galang",
                "phone" => "081226163475",
                "address" => "DIY, Indonesia",
                'password' => 'nananina12345',
            ],
            [
                "email" => "galanggf@mail.com",
                "name" => "fernan",
                "phone" => "081226563475",
                "address" => "DKI Jakarta, Indonesia",
                'password' => 'nananina12345',
            ],
            // EMPLOYEE COMP 01
            [
                "email" => "employeee_01_01@mail.com",
                "name" => "employeee_01_01",
                "phone" => "08342521266",
                "address" => "DKI Jakarta, Indonesia",
                'password' => 'nananina12345',
            ],
            [
                "email" => "employeee_01_02@mail.com",
                "name" => "employeee_01_02",
                "phone" => "08342521277",
                "address" => "DKI Jakarta, Indonesia",
                'password' => 'nananina12345',
            ],
            // EMPLOYEE COMP 02
            [
                "email" => "employeee_02_01@mail.com",
                "name" => "employeee_02_01",
                "phone" => "08342441266",
                "address" => "DKI Jakarta, Indonesia",
                'password' => 'nananina12345',
            ],
            [
                "email" => "employeee_02_02@mail.com",
                "name" => "employeee_02_02",
                "phone" => "08355521277",
                "address" => "DKI Jakarta, Indonesia",
                'password' => 'nananina12345',
            ],
        ];
    }
    
    public function test_01_create_employee(): void
    {
        
        // sample data untuk keperluhan create employee
        $dataTestingEmployee = $this->dataTestingEmployee;
        
        // ranah root
        $loginResponse = $this->postJson('/api/login', [
            'email' => "root@mail.com",
            'password' => $dataTestingEmployee[0]['password'],
        ]);
        
        $loginResponse->assertStatus(200);
        $result = $loginResponse->json('data');
        
        $Authorization = [
            'Authorization' => 'Bearer ' . $result['token'], 
        ];
        
        // [00] testing create employee dengan data yang valid ekspektasi gagal, karena yang memiliki akses untuk create employee hanya manager! 
        $data_post = $dataTestingEmployee[2];
        $createcompany_failed_00 = $this->postJson(
            '/api/employee', 
            $data_post, 
            $Authorization
        );
        $createcompany_failed_00->assertStatus(422);
        
        // ranah compan 01
        $loginResponse = $this->postJson('/api/login', [
            'email' => $dataTestingEmployee[0]['email'],
            'password' => $dataTestingEmployee[0]['password'],
        ]);
        
        $loginResponse->assertStatus(200);
        $result = $loginResponse->json('data');
        
        $Authorization = [
            'Authorization' => 'Bearer ' . $result['token'], 
        ];
        
        // [1] testing create employee dengan ekspektasi invalid karena tanpa token akses! 
        $createcompany_failed_01 = $this->postJson('/api/employee', [
                'name' => 'test manager',
                'email' => 'testemail@mail.com',
            ]);
        $createcompany_failed_01->assertStatus(401);
        
        // [2] testing create employee dengan data yang telah terdaftar, ekspektasi failed karena required uniq! 
        $data_post = $dataTestingEmployee[1];
        $createcompany_failed_02 = $this->postJson(
            '/api/employee', 
            $data_post, 
            $Authorization
        );
        $createcompany_failed_02->assertStatus(422);
        
        // [3] testing create employee dengan data yang valid ekspektasi berhasil! 
        $data_post = $dataTestingEmployee[2];
        $createcompany_failed_03 = $this->postJson(
            '/api/employee', 
            $data_post, 
            $Authorization
        );
        $createcompany_failed_03->assertStatus(200);
        
        // [4] testing create employee dengan data yang valid ekspektasi berhasil! 
        $data_post = $dataTestingEmployee[3];
        $createcompany_failed_04 = $this->postJson(
            '/api/employee', 
            $data_post, 
            $Authorization
        );
        $createcompany_failed_04->assertStatus(200);
        
        
        
        // TESTING HARUSNYA GAGAL KETIKA MENCOBA CREATE EMPLOYEE DENGAN AKUN EMPLOYEE
        $loginResponse = $this->postJson('/api/login', [
            'email' => $dataTestingEmployee[2]['email'],
            'password' => $dataTestingEmployee[2]['password'],
        ]);
        
        $loginResponse->assertStatus(200);
        $result = $loginResponse->json('data');
        
        $Authorization = [
            'Authorization' => 'Bearer ' . $result['token'], 
        ];
        
        $data_post = $dataTestingEmployee[4];
        $createcompany_failed_001 = $this->postJson(
            '/api/employee', 
            $data_post, 
            $Authorization
        );
        $createcompany_failed_001->assertStatus(422);
        
        
        // ranah compan 02
        $loginResponse = $this->postJson('/api/login', [
            'email' => $dataTestingEmployee[1]['email'],
            'password' => $dataTestingEmployee[1]['password'],
        ]);
        
        $loginResponse->assertStatus(200);
        $result = $loginResponse->json('data');
        
        $Authorization = [
            'Authorization' => 'Bearer ' . $result['token'], 
        ];
        
        // [5] testing create employee dengan data yang valid ekspektasi gagal, karena email + no hp employee telah terdaftar di company 01! 
        $data_post = $dataTestingEmployee[3];
        $createcompany_failed_05 = $this->postJson(
            '/api/employee', 
            $data_post, 
            $Authorization
        );
        $createcompany_failed_05->assertStatus(422);
        
        // [6] testing create employee dengan data email yang invalid ekspektasi gagal! 
        $data_post = $dataTestingEmployee[4];
        $data_post['email'] = "emploemplomail.com";
        $data_post['phone'] = "0234";
        $createcompany_failed_06 = $this->postJson(
            '/api/employee', 
            $data_post, 
            $Authorization
        );
        $createcompany_failed_06->assertStatus(422);
        
        
        
        // [7] testing create employee dengan data yang valid ekspektasi berhasil! 
        $data_post = $dataTestingEmployee[4];
        $createcompany_failed_07 = $this->postJson(
            '/api/employee', 
            $data_post, 
            $Authorization
        );
        $createcompany_failed_07->assertStatus(200);
        
        // [8] testing create employee dengan data yang valid ekspektasi berhasil! 
        $data_post = $dataTestingEmployee[5];
        $createcompany_failed_08 = $this->postJson(
            '/api/employee', 
            $data_post, 
            $Authorization
        );
        $createcompany_failed_08->assertStatus(200);
        
    }
    
    public function test_02_update_employee(): void
    {
        // sample data untuk keperluhan update employee
        $dataTestingEmployee    = $this->dataTestingEmployee;
        
        // ranah compan 01
        $loginResponse = $this->postJson('/api/login', [
            'email' => $dataTestingEmployee[0]['email'],
            'password' => $dataTestingEmployee[0]['password'],
        ]);
        
        $loginResponse->assertStatus(200);
        $result = $loginResponse->json('data');
        
        $Authorization = [
            'Authorization' => 'Bearer ' . $result['token'], 
        ];
        
        // [1] testing update employee dengan ekspektasi invalid karena tidak menggunakan token akses! 
        $data_post = $dataTestingEmployee[4];
        $createcompany_failed_01 = $this->putJson('/api/employee/6', $data_post);
        $createcompany_failed_01->assertStatus(401);
        
        // [2] testing update employee dengan ekspektasi invalid karena employe tidak berada di company yang sama dengan manager yg melakukan update! 
        $data_post = $dataTestingEmployee[4];
        $data_post['name'] .= "_UPDATED";
        $createcompany_failed_02 = $this->putJson(
            '/api/employee/5', 
            $data_post, 
            $Authorization
        );
        $createcompany_failed_02->assertStatus(422);
        
        // [3] testing update employee dengan ekspektasi BERHASIL! 
        $data_post = $dataTestingEmployee[2];
        $data_post['name'] .= "_UPDATED";
        $data_post['address'] .= "_UPDATED";
        $createcompany_failed_03 = $this->putJson(
            '/api/employee/3', 
            $data_post, 
            $Authorization
        );
        $createcompany_failed_03->assertStatus(200);
        
        
        // ranah compan 02
        $loginResponse = $this->postJson('/api/login', [
            'email' => $dataTestingEmployee[1]['email'],
            'password' => $dataTestingEmployee[1]['password'],
        ]);
        
        $loginResponse->assertStatus(200);
        $result = $loginResponse->json('data');
        
        $Authorization = [
            'Authorization' => 'Bearer ' . $result['token'], 
        ];
        
        // [4] testing update employee dengan ekspektasi invalid karena data employee yg digunakan telah terdaftar! 
        $data_post = $dataTestingEmployee[5];
        $data_post['name'] .= "_UPDATED";
        $createcompany_failed_04 = $this->putJson(
            '/api/employee/5', 
            $data_post, 
            $Authorization
        );
        $createcompany_failed_04->assertStatus(422);
        
        // [5] testing update employee dengan ekspektasi berhasil! 
        $data_post = $dataTestingEmployee[5];
        $data_post['name'] .= "_UPDATED";
        $createcompany_failed_05 = $this->putJson(
            '/api/employee/6', 
            $data_post, 
            $Authorization
        );
        $createcompany_failed_05->assertStatus(200);
        
        // TESTING HARUSNYA GAGAL KETIKA MENCOBA UPDATE EMPLOYEE DENGAN AKUN EMPLOYEE
        $loginResponse = $this->postJson('/api/login', [
            'email' => $dataTestingEmployee[4]['email'],
            'password' => $dataTestingEmployee[4]['password'],
        ]);
        
        $loginResponse->assertStatus(200);
        $result = $loginResponse->json('data');
        
        $Authorization = [
            'Authorization' => 'Bearer ' . $result['token'], 
        ];
        $data_post = $dataTestingEmployee[5];
        $data_post['name'] .= "_UPDATED";
        $createcompany_failed_05 = $this->putJson(
            '/api/employee/6', 
            $data_post, 
            $Authorization
        );
        $createcompany_failed_05->assertStatus(422);
    }
    
    public function test_03_get_paginate_employee(): void
    {
        // sample data untuk keperluhan update employee
        $dataTestingEmployee    = $this->dataTestingEmployee;
        
        // ranah compan 01
        $loginResponse = $this->postJson('/api/login', [
            'email' => $dataTestingEmployee[0]['email'],
            'password' => $dataTestingEmployee[0]['password'],
        ]);
        
        $loginResponse->assertStatus(200);
        $result = $loginResponse->json('data');
        
        $Authorization = [
            'Authorization' => 'Bearer ' . $result['token'], 
        ];
        
        // [1] testing get list paginate employee dengan ekspektasi berhasil! 
        $createcompany_failed_01 = $this->get('/api/employee/', $Authorization);
        $createcompany_failed_01->assertStatus(200);
        
        // [2] testing get list paginate employee, dengean penerapan fungsi search dengan ekspektasi berhasil! 
        $createcompany_failed_02 = $this->get('/api/employee/?search=employeee_01', $Authorization);
        $createcompany_failed_02->assertStatus(200);
        
        // [3] testing get list paginate employee, dengean penerapan fungsi sorting by create date & name dengan ekspektasi berhasil! 
        $createcompany_failed_03a = $this->get('/api/employee/?sort_by=created_at&sort_direction=ASC', $Authorization);
        $createcompany_failed_03a->assertStatus(200);
        
        $createcompany_failed_03b = $this->get('/api/employee/?sort_by=name&sort_direction=DESC', $Authorization);
        $createcompany_failed_03b->assertStatus(200);
        
    }
    
    public function test_04_get_detail_employee(): void
    {
        // sample data untuk keperluhan update employee
        $dataTestingEmployee    = $this->dataTestingEmployee;
        
        // ranah compan 01
        $loginResponse = $this->postJson('/api/login', [
            'email' => $dataTestingEmployee[0]['email'],
            'password' => $dataTestingEmployee[0]['password'],
        ]);
        
        $loginResponse->assertStatus(200);
        $result = $loginResponse->json('data');
        
        $Authorization = [
            'Authorization' => 'Bearer ' . $result['token'], 
        ];
        
        // [1] testing get detail employee dengan ekspektasi gagal, karena tidak menggunakan token akses! 
        $createcompany_failed_01 = $this->get('/api/employee/3');
        $createcompany_failed_01->assertStatus(401);
        
        // [2] testing get detail employee dengan ekspektasi berhasil! 
        $createcompany_failed_02 = $this->get('/api/employee/3', $Authorization);
        $createcompany_failed_02->assertStatus(200);
        
        // [3] testing get detail employee, dengan ekspektasi gagal karena employee berada di compnay yang berbeda dengan manager! 
        $createcompany_failed_03 = $this->get('/api/employee/5', $Authorization);
        $createcompany_failed_03->assertStatus(404);
        
        // ranah compan 02
        $loginResponse = $this->postJson('/api/login', [
            'email' => $dataTestingEmployee[1]['email'],
            'password' => $dataTestingEmployee[1]['password'],
        ]);
        
        $loginResponse->assertStatus(200);
        $result = $loginResponse->json('data');
        
        $Authorization = [
            'Authorization' => 'Bearer ' . $result['token'], 
        ];
        
        // [4] testing get detail employee dengan ekspektasi berhasil! 
        $createcompany_failed_04 = $this->get('/api/employee/5', $Authorization);
        $createcompany_failed_04->assertStatus(200);
        
    }
    
    public function test_05_delete_employee(): void
    {
        // sample data untuk keperluhan update employee
        $dataTestingEmployee    = $this->dataTestingEmployee;
        
        // ranah compan 01
        $loginResponse = $this->postJson('/api/login', [
            'email' => $dataTestingEmployee[0]['email'],
            'password' => $dataTestingEmployee[0]['password'],
        ]);
        
        $loginResponse->assertStatus(200);
        $result = $loginResponse->json('data');
        
        $Authorization = [
            'Authorization' => 'Bearer ' . $result['token'], 
        ];
        
        // [1] testing delete employee dengan ekspektasi gagal, karena tidak menggunakan token akses! 
        $createcompany_failed_01 = $this->delete('/api/employee/4');
        $createcompany_failed_01->assertStatus(401);
        
        // [2] testing delete employee dengan ekspektasi berhasil! 
        $createcompany_failed_02 = $this->delete('/api/employee/4', [], $Authorization);
        $createcompany_failed_02->assertStatus(200);
        
        // [3] testing delete employee, dengan ekspektasi gagal karena employee berada di compnay yang berbeda dengan manager! 
        $createcompany_failed_03 = $this->delete('/api/employee/5', [], $Authorization);
        $createcompany_failed_03->assertStatus(404);
        
        // TESTING HARUSNYA GAGAL JIKA INGIN DELETE DARI AKUN EMPLOYEE
        $loginResponse = $this->postJson('/api/login', [
            'email' => $dataTestingEmployee[4]['email'],
            'password' => $dataTestingEmployee[4]['password'],
        ]);
        
        $loginResponse->assertStatus(200);
        $result = $loginResponse->json('data');
        
        $Authorization = [
            'Authorization' => 'Bearer ' . $result['token'], 
        ];
        
        $createcompany_failed_00 = $this->delete('/api/employee/5', [], $Authorization);
        $createcompany_failed_00->assertStatus(422);
        
        
        // ranah compan 02
        $loginResponse = $this->postJson('/api/login', [
            'email' => $dataTestingEmployee[1]['email'],
            'password' => $dataTestingEmployee[1]['password'],
        ]);
        
        $loginResponse->assertStatus(200);
        $result = $loginResponse->json('data');
        
        $Authorization = [
            'Authorization' => 'Bearer ' . $result['token'], 
        ];
        
        // [4] testing delete employee dengan ekspektasi berhasil!
        $createcompany_failed_04 = $this->delete('/api/employee/5', [], $Authorization);
        $createcompany_failed_04->assertStatus(200);
        
    }
    
    public function test_06_profile_user(): void
    {
        // sample data untuk keperluhan update employee
        $dataTestingEmployee    = $this->dataTestingEmployee;
        
        // ranah compan 01
        $loginResponse = $this->postJson('/api/login', [
            'email' => $dataTestingEmployee[0]['email'],
            'password' => $dataTestingEmployee[0]['password'],
        ]);
        
        $loginResponse->assertStatus(200);
        $result = $loginResponse->json('data');
        
        $Authorization = [
            'Authorization' => 'Bearer ' . $result['token'], 
        ];
        
        // [1] testing get detail profile manager
        $createcompany_failed_01 = $this->get('/api/profile', $Authorization);
        $createcompany_failed_01->assertStatus(200);
        
        // [2] testing update profile manager
        $data_post = $dataTestingEmployee[0];
        $data_post['name'] .= "_UPDATED";
        $data_post['address'] .= "_UPDATED";
        $createcompany_failed_02 = $this->putJson(
            '/api/profile', 
            $data_post, 
            $Authorization
        );
        $createcompany_failed_02->assertStatus(200);
        
        
        // ranah employee
        $loginResponse = $this->postJson('/api/login', [
            'email' => $dataTestingEmployee[2]['email'],
            'password' => $dataTestingEmployee[2]['password'],
        ]);
        
        $loginResponse->assertStatus(200);
        $result = $loginResponse->json('data');
        
        $Authorization = [
            'Authorization' => 'Bearer ' . $result['token'], 
        ];
        
        // [3] testing get detail profile manager
        $createcompany_failed_03 = $this->get('/api/profile', $Authorization);
        $createcompany_failed_03->assertStatus(200);
        
        // [4] testing update profile manager
        $data_post = $dataTestingEmployee[2];
        $data_post['name'] .= "_un_UPDATED";
        $data_post['address'] .= "_un_UPDATED";
        $createcompany_failed_04 = $this->putJson(
            '/api/profile', 
            $data_post, 
            $Authorization
        );
        $createcompany_failed_04->assertStatus(422);
        
    }
}
