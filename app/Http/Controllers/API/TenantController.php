<?php

namespace App\Http\Controllers\API;

use App\Events\TenantCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\FormTenantRequest;
use App\Models\Tenant;
use App\Services\TenantService;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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

        $total_tenants = $tenants->count();

        $tenants = $tenants->paginate(25);

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

        return $this->successResponse($tenant->toArray());
    }

    /**
     * create new tenant
     * 
     * @return Illuminate\Http\View;
     */
    public function create()
    {
        $countries = DB::table('country_codes')->orderBy('country_name', 'asc')->get();

        return view('tenants.new', compact('countries'));
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
            $tenant = Tenant::create($request->all());

            TenantCreated::dispatch($tenant);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors();
        }

        DB::commit();

        return $this->successResponse(collect($tenant)->merge(['domains' => $tenant->domains])->toArray());
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
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
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse($e->getMessage(), 500);
        }

        DB::commit();

        return $this->successResponse(collect($tenant)->merge(['domains' => $tenant->domains])->toArray());
    }

    /**
     * delete tenant - will delete all records 
     * 
     * @return string
     */
    public function destroy(Tenant $tenant) : string
    {
        $tenant->delete();

        return $this->successresponse('OK');
    }

    /**
     * restore deleted tenants
     * 
     * @return string
     */
    public function restore($id) : string
    {
        $tenant = Tenant::where('id', $id)->withTrashed()->restore();

        return $this->successResponse('OK');
    }
}
