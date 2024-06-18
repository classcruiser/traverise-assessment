<?php

namespace App\Http\Controllers\Panel;

use App\Events\TenantCreated;
use App\Events\TenantDeleted;
use App\Http\Controllers\Controller;
use App\Http\Requests\FormTenantRequest;
use App\Mail\TenantVerificationLink;
use App\Models\Booking\User as UserTenant;
use App\Models\Tenant;
use App\Services\TenantService;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    use ApiResponse;

    /**
     * list tenants
     *
     * @return Response
     */
    public function index()
    {
        $tenants = Tenant::with(['domains' => function ($query) {
            return $query->select(['id', 'domain', 'tenant_id']);
        }]);

        $tenants = $tenants->paginate(25);
        $total_tenants = $tenants->total();

        return view('tenants.index', compact('tenants', 'total_tenants'));
    }

    /**
     * show the tenant details
     *
     * @param App\Models\Tenant $tenant
     *
     * @return Response;
     */
    public function show(Tenant $tenant)
    {
        $tenant->load('domains');

        $countries = DB::table('country_codes')->orderBy('country_name', 'asc')->get();

        $super = UserTenant::where('tenant_id', $tenant->id)->where('is_super', 1)->first();

        return view('tenants.show', compact('tenant', 'countries', 'super'));
    }

    /**
     * create new tenant
     *
     * @return Illuminate\Http\View;
     */
    public function create()
    {
        $countries = DB::table('country_codes')->orderBy('country_name', 'asc')->get();

        $uid = Str::random(32);

        return view('tenants.new', compact('countries', 'uid'));
    }

    /**
     * create new tenant and dispatch job to create domain
     *
     * @param App\Http\Requests\FormTenantRequest $request
     *
     * @return Response
     */
    public function store(FormTenantRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $tenant = Tenant::create($request->except('is_active'));

            if (request()->has('is_active')) {
                $tenant->update(['is_active' => 1]);
            }

            $super = [
                'email' => request('super_email'),
                'password' => Hash::make(request('password'))
            ];

            TenantCreated::dispatch($tenant, $super);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

        DB::commit();

        session()->flash('message', 'Tenant added');

        return redirect(route('tenants'));
    }

    /**
     * update tenant
     *
     * @param Illuminate\Http\Request $request
     * @param App\Models\Tenant $tenant
     *
     * @return Response;
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->except(['is_active']), [
            'first_name' => 'required',
            'country' => 'required',
            'phone' => 'required',
            'email' => [
                'required',
                Rule::unique('tenants')->ignore($tenant->id)
            ]
        ]);

        $data = $validator->valid();

        DB::beginTransaction();

        try {
            $tenant->update($data);

            $tenant->update(['is_active' => request()->has('is_active')]);

            if (request('password') != '') {
                $super = UserTenant::where('is_super', 1)->where('tenant_id', $tenant->id)->first();
                $super->update(['password' => Hash::make(request('password'))]);
            }
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }

        DB::commit();

        session()->flash('message', 'Tenant updated');

        return redirect(route('tenants.show', $tenant));
    }

    /**
     * delete tenant - will delete all records
     *
     * @return string
     */
    public function delete(Tenant $tenant): string
    {
        TenantDeleted::dispatch($tenant);

        $tenant->delete();

        session()->flash('message', 'Tenant deleted');

        return redirect(route('tenants'));
    }

    /**
     * restore deleted tenants
     *
     * @return string
     */
    public function restore(string $id): string
    {
        $tenant = Tenant::where('id', $id)->withTrashed()->restore();

        return $this->successResponse('OK');
    }

    /**
     * check domain availability
     *
     * @return array
     */
    public function domainChecker(): array
    {
        $blacklist = [
            'panel',
            'admin',
            'auth',
            'traverise',
        ];

        $check = Tenant::where('id', request('domain'))->count() || in_array(request('domain'), $blacklist);

        return [
            'status' => $check > 0 ? 'FAIL' : 'SUCCESS',
            'html' => $check > 0 ? '<span class="text-red-600">Domain is not available</span>' : '<span class="text-green-600">Available</span>'
        ];
    }
}
