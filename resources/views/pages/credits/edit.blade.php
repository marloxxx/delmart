@extends('layouts.master')
@section('title', 'Create Pulsa')
@section('page', 'Pulsa')
@section('breadcrumb')
    <!--begin::Breadcrumb-->
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1">
        <!--begin::Item-->
        <li class="breadcrumb-item text-muted">Pulsa</li>
        <!--end::Item-->
        <!--begin::Item-->
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-200 w-5px h-2px"></span>
        </li>
        <!--end::Item-->
        <!--begin::Item-->
        <li class="breadcrumb-item text-dark">Tambah Pulsa</li>
        <!--end::Item-->
    </ul>
    <!--end::Breadcrumb-->
@endsection
@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-xxl">
                <form id="kt_ecommerce_edit_credit_form" class="form d-flex flex-column flex-lg-row"
                    data-kt-redirect="{{ route('credits.index') }}" action="{{ route('credits.update', $credit->id) }}"
                    method="PUT">
                    <!--begin::Main column-->
                    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                        <!--begin::General options-->
                        <div class="card card-flush py-4">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>General</h2>
                                </div>
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!--begin::Input group-->
                                <div class="mb-10 fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Provider</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <select name="provider" class="form-select form-select-solid" data-control="select2"
                                        data-placeholder="Select a provider">
                                        <option></option>
                                        <option value="Telkomsel" {{ $credit->provider == 'Telkomsel' ? 'selected' : '' }}>
                                            Telkomsel</option>
                                        <option value="Indosat" {{ $credit->provider == 'Indosat' ? 'selected' : '' }}>
                                            Indosat</option>
                                        <option value="XL" {{ $credit->provider == 'XL' ? 'selected' : '' }}>XL</option>
                                        <option value="Tri" {{ $credit->provider == 'Tri' ? 'selected' : '' }}>Tri
                                        </option>
                                        <option value="Smartfren" {{ $credit->provider == 'Smartfren' ? 'selected' : '' }}>
                                            Smartfren</option>
                                        <option value="Axis" {{ $credit->provider == 'Axis' ? 'selected' : '' }}>Axis
                                        </option>
                                        <option value="Three" {{ $credit->provider == 'Three' ? 'selected' : '' }}>Three
                                        </option>
                                    </select>
                                    <!--end::Input-->
                                    <!--begin::Description-->
                                    <div class="text-muted fs-7">Select a provider for the category.
                                    </div>
                                    <!--end::Description-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="mb-10 fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Nominal</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="nominal" class="form-control form-control-solid"
                                        placeholder="Enter nominal" value="{{ $credit->nominal }}" />
                                    <!--end::Input-->
                                    <!--begin::Description-->
                                    <div class="text-muted fs-7">Enter the nominal for the category.
                                    </div>
                                    <!--end::Description-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="mb-10 fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Harga</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="price" class="form-control form-control-solid"
                                        placeholder="Enter price" value="{{ $credit->price }}" />
                                    <!--end::Input-->
                                    <!--begin::Description-->
                                    <div class="text-muted fs-7">Enter the price for the category.
                                    </div>
                                    <!--end::Description-->
                                </div>
                                <!--end::Card header-->
                            </div>
                            <!--end::General options-->
                            <div class="d-flex justify-content-end">
                                <!--begin::Button-->
                                <a href="{{ route('credits.index') }}}" id="kt_ecommerce_edit_product_cancel"
                                    class="btn btn-light me-5">Cancel</a>
                                <!--end::Button-->
                                <!--begin::Button-->
                                <button type="submit" id="kt_ecommerce_edit_category_submit" class="btn btn-primary"
                                    data-kt-element="submit">
                                    <span class="indicator-label">Save Changes</span>
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                                <!--end::Button-->
                            </div>
                        </div>
                        <!--end::Main column-->
                </form>
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
@endsection
@section('scripts')
    <script>
        "use strict";
        // Class definition
        const KTFormControls = function() {
            // Base elements
            const formEl = $('#kt_ecommerce_edit_category_form');
            return {
                onSubmit: function() {
                    const btn = formEl.find('[data-kt-element="submit"]');
                    const action = formEl.attr('action');
                    const method = formEl.attr('method');
                    const data = new FormData(formEl[0]);
                    $.ajax({
                        url: action,
                        method: method,
                        data: data,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            btn.attr("data-kt-indicator", "on");
                            btn.prop("disabled", true);
                        },
                        success: function(response) {
                            if (response.status == 'error') {
                                Swal.fire({
                                    text: response.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                });
                                return;
                            } else {
                                Swal.fire({
                                    text: response.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function(result) {
                                    if (result.isConfirmed) {
                                        window.location.href = response
                                            .redirect;
                                    }
                                });
                            }
                        },
                        error: function(data) {
                            Swal.fire({
                                text: "Sorry, we couldn't update your data.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary"
                                }
                            });
                        }
                    }).done(function() {
                        btn.removeAttr("data-kt-indicator");
                        btn.prop("disabled", false);
                    });
                }
            };
        }();

        // on submit form
        $(document).on('submit', '#kt_ecommerce_edit_credit_form', function(e) {
            e.preventDefault();
            KTFormControls.onSubmit();
        });
    </script>
@endsection
