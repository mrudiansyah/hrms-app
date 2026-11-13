<?php

namespace Tests\Feature;

use App\Mail\CandidateEmailVerification;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class CandidateOnboardingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.url' => 'http://localhost',
            'cache.default' => 'array',
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => false,
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');
        URL::forceRootUrl('http://localhost');
        URL::forceScheme('http');

        $this->createTables();
    }

    public function test_candidate_verifies_email_and_saves_all_profile_sections(): void
    {
        Mail::fake();
        $this->post(route('candidate.regions', ['level' => 'regency']), ['id' => '32'])
            ->assertOk()
            ->assertJsonFragment(['code' => '32.01', 'name' => 'Kabupaten Bandung']);
        $this->postJson(route('candidate.regions', ['level' => 'regency']), ['id' => '3%'])
            ->assertUnprocessable();
        DB::table('tb_cand_pin')->insert([
            'pin_access' => '4821',
            'created_by' => 1,
            'created_at' => now(),
            'expired_at' => now()->addHour(),
        ]);

        $this->post(route('candidate.send-verification'), [
            'pin_access' => '4821',
            'email_address' => 'candidate@example.test',
            'employee_name' => 'Test Candidate',
            'gender' => 'Perempuan',
        ])->assertRedirect(route('candidate.access'));

        $verificationUrl = '';
        Mail::assertSent(CandidateEmailVerification::class, function (CandidateEmailVerification $mail) use (&$verificationUrl) {
            $verificationUrl = $mail->verificationUrl;

            return true;
        });
        $this->assertNotSame('', $verificationUrl);

        $this->get($verificationUrl)->assertRedirect(route('candidate.profile'));
        $candidateId = DB::table('tb_cand_employees')->value('id');
        $this->assertNotNull(DB::table('tb_cand_employees')->where('id', $candidateId)->value('verified_at'));
        $this->get(route('candidate.profile'))
            ->assertOk()
            ->assertSee('Jawa Barat');

        $this->post(route('candidate.profile.save'), [
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1995-02-03',
            'agama' => 'Islam',
            'golongan_darah' => 'O',
            'ibu_kandung' => 'Test Ibu',
            'nomor_ktp' => '1234567890123456',
            'nomor_kk' => '6543210987654321',
            'nomor_npwp' => '',
            'kode_status' => 'TK/0',
            'nomor_bpjs_kes' => '12345',
            'nomor_bpjs_ket' => '',
            'nomor_rekening' => '987654321',
            'nama_bank' => 'Bank Test',
            'nomor_telepon' => '081234567890',
            'addresses' => [
                'ktp' => $this->address('Alamat KTP'),
                'domicile' => $this->address('Alamat domisili'),
            ],
            'educations' => [
                [
                    'level_education' => 'S1',
                    'institute' => 'Universitas Test',
                    'prodi' => 'Informatika',
                    'year' => '2013',
                    'graduate_year' => '2017',
                    'remark' => '',
                    'is_top' => '1',
                ],
            ],
            'emergency_contacts' => [
                [
                    'nama_keluarga' => 'Kontak Darurat',
                    'hubungan' => 'Saudara',
                    'nomor_kontak' => '081111111111',
                    'provinsi_kontak' => '32',
                    'kabupaten_kontak' => '32.01',
                    'kecamatan_kontak' => '32.01.01',
                    'kelurahan_kontak' => '32.01.01.2001',
                    'detail_kontak' => 'Jalan Test 1',
                ],
            ],
            'families' => [
                [
                    'nama_keluarga' => 'Test Anak',
                    'hubungan' => 'Anak ke-1',
                    'tanggal_lahir' => '2020-04-05',
                ],
            ],
        ])->assertRedirect(route('candidate.complete'));
        $this->get(route('candidate.complete'))->assertOk();

        $this->assertDatabaseHas('tb_cand_employees', [
            'id' => $candidateId,
            'updated_by' => 'Test Candidate',
        ]);
        $this->assertDatabaseHas('tb_cand_employee_detail', [
            'id_cand_employee' => $candidateId,
            'top_education' => 'S1',
            'prodi' => 'Informatika',
            'status_bpjs_kes' => 1,
            'status_bpjs_ket' => 0,
        ]);
        $this->assertDatabaseCount('tb_cand_addresses', 1);
        $this->assertDatabaseCount('tb_cand_domiciles', 1);
        $this->assertDatabaseHas('tb_cand_addresses', [
            'id_cand_employee' => $candidateId,
            'id_ktp' => '1234567890123456',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Kabupaten Bandung',
        ]);
        $this->assertDatabaseCount('tb_cand_educations', 1);
        $this->assertDatabaseCount('tb_cand_address_darurat', 1);
        $this->assertDatabaseCount('tb_cand_employee_family', 1);
    }

    public function test_inactive_pin_and_existing_user_email_are_rejected(): void
    {
        Mail::fake();
        DB::table('tb_cand_pin')->insert([
            'pin_access' => '4821',
            'created_by' => 1,
            'created_at' => now()->subHours(2),
            'expired_at' => now()->subHour(),
        ]);

        $payload = [
            'pin_access' => '4821',
            'email_address' => 'candidate@example.test',
            'employee_name' => 'Test Candidate',
            'gender' => 'Laki-laki',
        ];
        $this->post(route('candidate.send-verification'), $payload)
            ->assertSessionHasErrors('pin_access');

        DB::table('tb_cand_pin')->update(['expired_at' => now()->addHour()]);
        DB::table('users')->insert([
            'name' => 'Existing User',
            'email' => 'candidate@example.test',
            'password' => 'not-a-real-password',
        ]);

        $this->post(route('candidate.send-verification'), $payload)
            ->assertSessionHasErrors('email_address');
        Mail::assertNothingSent();
        $this->assertDatabaseCount('tb_cand_employees', 0);

        DB::table('tb_cand_employees')->insert([
            'email_address' => 'already-candidate@example.test',
            'employee_name' => 'Existing Candidate',
            'gender' => 'Laki-laki',
            'updated_by' => 'Existing Candidate',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $payload['email_address'] = 'already-candidate@example.test';
        $this->post(route('candidate.send-verification'), $payload)
            ->assertSessionHasErrors('email_address');
        Mail::assertNothingSent();
    }

    public function test_smtp_failure_returns_an_error_instead_of_waiting_for_the_php_timeout(): void
    {
        DB::table('tb_cand_pin')->insert([
            'pin_access' => '4821',
            'created_by' => 1,
            'created_at' => now(),
            'expired_at' => now()->addHour(),
        ]);
        Mail::shouldReceive('to')
            ->once()
            ->with('candidate@example.test')
            ->andThrow(new TransportException('SMTP unavailable'));

        $this->post(route('candidate.send-verification'), [
            'pin_access' => '4821',
            'email_address' => 'candidate@example.test',
            'employee_name' => 'Test Candidate',
            'gender' => 'Perempuan',
        ])->assertSessionHasErrors('email_address');

        $this->assertDatabaseCount('tb_cand_employees', 0);
    }

    public function test_hr_can_issue_a_pin_that_skips_email_verification(): void
    {
        Mail::fake();
        DB::table('tb_cand_pin')->insert([
            'pin_access' => '4821',
            'created_by' => 1,
            'created_at' => now(),
            'expired_at' => now()->addHour(),
            'skip_email_verification' => true,
        ]);

        $this->post(route('candidate.send-verification'), [
            'pin_access' => '4821',
            'email_address' => 'candidate@example.test',
            'employee_name' => 'Test Candidate',
            'gender' => 'Perempuan',
        ])->assertRedirect(route('candidate.profile'));

        $candidateId = DB::table('tb_cand_employees')->value('id');
        $this->assertDatabaseHas('tb_cand_employees', [
            'id' => $candidateId,
            'verified_at' => null,
        ]);
        $this->get(route('candidate.profile'))
            ->assertOk()
            ->assertSee('Email tidak diverifikasi (opsi skip dari HR)');
        Mail::assertNothingSent();
    }

    public function test_hr_access_can_generate_and_expire_a_pin(): void
    {
        $this->get(route('candidate.pins'))->assertRedirect('/login');

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('role_users', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
        });
        DB::table('roles')->insert(['name' => 'hr_access']);
        DB::table('roles')->insert(['name' => 'other_role']);
        $otherUserId = DB::table('users')->insertGetId([
            'name' => 'Other User',
            'email' => 'other@example.test',
            'password' => 'not-a-real-password',
        ]);
        DB::table('role_users')->insert([
            'user_id' => $otherUserId,
            'role_id' => DB::table('roles')->where('name', 'other_role')->value('id'),
        ]);
        $this->actingAs(User::findOrFail($otherUserId))
            ->get(route('candidate.pins'))
            ->assertForbidden();

        $userId = DB::table('users')->insertGetId([
            'name' => 'HR User',
            'email' => 'hr@example.test',
            'password' => 'not-a-real-password',
        ]);
        DB::table('role_users')->insert([
            'user_id' => $userId,
            'role_id' => DB::table('roles')->value('id'),
        ]);

        $this->actingAs(User::findOrFail($userId))
            ->post(route('candidate.pins.generate'))
            ->assertRedirect(route('candidate.pins'));

        $this->get(route('candidate.pins'))->assertOk();
        $pin = DB::table('tb_cand_pin')->first();
        $this->assertMatchesRegularExpression('/^\d{4}$/', $pin->pin_access);
        $this->assertSame((string) $userId, (string) $pin->created_by);
        $this->assertFalse((bool) $pin->skip_email_verification);
        $this->assertTrue(Carbon::parse($pin->expired_at)->isFuture());

        $this->actingAs(User::findOrFail($userId))
            ->post(route('candidate.pins.generate'), ['skip_email_verification' => '1'])
            ->assertRedirect(route('candidate.pins'));
        $skipPin = DB::table('tb_cand_pin')->where('skip_email_verification', true)->first();
        $this->assertNotNull($skipPin);

        $this->actingAs(User::findOrFail($userId))
            ->patch(route('candidate.pins.expire', $pin->id))
            ->assertRedirect(route('candidate.pins'));
        $this->assertFalse(Carbon::parse(DB::table('tb_cand_pin')->value('expired_at'))->isFuture());
    }

    private function address(string $detail): array
    {
        return [
            'provinsi' => '32',
            'kabupaten' => '32.01',
            'kecamatan' => '32.01.01',
            'kelurahan' => '32.01.01.2001',
            'detail' => $detail,
            'map_address' => '',
        ];
    }

    private function createTables(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('password');
        });
        Schema::create('tb_cand_pin', function (Blueprint $table) {
            $table->id();
            $table->string('pin_access', 4);
            $table->unsignedBigInteger('created_by');
            $table->dateTime('created_at');
            $table->dateTime('expired_at');
            $table->boolean('skip_email_verification')->default(false);
        });
        Schema::create('tb_cand_employees', function (Blueprint $table) {
            $table->id();
            $table->string('email_address');
            $table->string('employee_name');
            $table->string('gender');
            $table->string('updated_by');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('verified_at')->nullable();
            $table->unique('email_address', 'UX_tb_cand_employees_email_address');
        });
        Schema::create('tb_cand_employee_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cand_employee');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('agama');
            $table->string('golongan_darah');
            $table->string('ibu_kandung');
            $table->string('nomor_ktp');
            $table->string('nomor_kk');
            $table->string('nomor_npwp')->nullable();
            $table->string('kode_status');
            $table->string('nomor_bpjs_kes')->nullable();
            $table->string('nomor_bpjs_ket')->nullable();
            $table->string('nomor_rekening')->nullable();
            $table->string('nama_bank')->nullable();
            $table->string('nomor_telepon');
            $table->string('top_education');
            $table->string('prodi');
            $table->boolean('status_bpjs_kes');
            $table->boolean('status_bpjs_ket');
            $table->dateTime('updated_at');
        });
        foreach (['tb_cand_addresses', 'tb_cand_domiciles'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_cand_employee');
                $table->string('id_ktp');
                $table->string('provinsi');
                $table->string('kabupaten');
                $table->string('kecamatan');
                $table->string('kelurahan');
                $table->string('detail');
                $table->string('map_address')->nullable();
                $table->boolean('status');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
            });
        }
        Schema::create('tb_cand_address_darurat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cand_employee');
            $table->string('nama_keluarga');
            $table->string('hubungan');
            $table->string('nomor_kontak');
            $table->string('provinsi_kontak');
            $table->string('kabupaten_kontak');
            $table->string('kecamatan_kontak');
            $table->string('kelurahan_kontak');
            $table->string('detail_kontak');
            $table->boolean('status');
        });
        Schema::create('tb_cand_educations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cand_employee');
            $table->string('level_education');
            $table->string('institute');
            $table->string('prodi');
            $table->integer('year');
            $table->integer('graduate_year')->nullable();
            $table->string('remark')->nullable();
            $table->boolean('is_top');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        Schema::create('tb_cand_employee_family', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cand_employee');
            $table->string('nama_keluarga');
            $table->string('hubungan');
            $table->date('tanggal_lahir');
            $table->boolean('status');
        });
        Schema::create('tb_religion', function (Blueprint $table) {
            $table->id();
            $table->string('religion');
        });
        Schema::create('tb_pph21_status', function (Blueprint $table) {
            $table->id();
            $table->string('status_pajak');
        });
        Schema::create('wilayah_2020', function (Blueprint $table) {
            $table->string('kode', 13)->primary();
            $table->string('nama');
        });
        DB::table('tb_religion')->insert(['religion' => 'Islam']);
        DB::table('tb_pph21_status')->insert(['status_pajak' => 'TK/0']);
        DB::table('wilayah_2020')->insert([
            ['kode' => '32', 'nama' => 'Jawa Barat'],
            ['kode' => '32.01', 'nama' => 'Kabupaten Bandung'],
            ['kode' => '32.01.01', 'nama' => 'Coblong'],
            ['kode' => '32.01.01.2001', 'nama' => 'Dago'],
        ]);
    }
}
