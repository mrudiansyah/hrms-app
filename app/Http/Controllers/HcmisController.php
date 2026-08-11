<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HcmisService;
use Illuminate\Support\Facades\Cache;

class HcmisController extends Controller
{
    protected $hcmis;

    public function __construct(HcmisService $hcmis)
    {
        $this->hcmis = $hcmis;
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        $resp = $this->hcmis->login($data['email'], $data['password']);
        // if login returns access_token, store it in cache for reuse
        if (is_array($resp) && isset($resp['access_token'])) {
            $this->hcmis->setToken($resp['access_token']);
        }
        return response()->json($resp);
    }

    public function employeesDatatables(Request $request)
    {
        $payload = $this->ensureCredentials($request->all());
        $payload['page'] = $payload['page'] ?? 1;
        $payload['rows'] = $payload['rows'] ?? 100;

        $resp = $this->hcmis->get('/api/employees/datatables', $payload);
        return response()->json($resp);
    }

    public function employeesShow(Request $request)
    {
        $payload = $this->ensureCredentials($request->all());
        $resp = $this->hcmis->post('/api/employees/show', $payload);
        return response()->json($resp);
    }

    public function employeesStore(Request $request)
    {
        $payload = $this->ensureCredentials($request->all());
        $resp = $this->hcmis->post('/api/employees/store', $payload);
        return response()->json($resp);
    }

    public function employeesUpdate(Request $request)
    {
        $payload = $this->ensureCredentials($request->all());
        $resp = $this->hcmis->put('/api/employees/update', $payload);
        return response()->json($resp);
    }

    public function employeesDelete(Request $request)
    {
        $payload = $this->ensureCredentials($request->all());
        $resp = $this->hcmis->delete('/api/employees/delete', $payload);
        return response()->json($resp);
    }

    private function ensureCredentials(array $payload): array
    {
        if (empty($payload['username']) && config('hcmis.username')) {
            $payload['username'] = config('hcmis.username');
        }
        if (empty($payload['password']) && config('hcmis.password')) {
            $payload['password'] = config('hcmis.password');
        }
        if (empty($payload['company_code']) && config('hcmis.company_code')) {
            $payload['company_code'] = config('hcmis.company_code');
        }
        if (empty($payload['org_code']) && config('hcmis.org_code')) {
            $payload['org_code'] = config('hcmis.org_code');
        }
        return $payload;
    }
}
