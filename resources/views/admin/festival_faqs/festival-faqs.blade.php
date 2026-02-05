@extends('admin.layouts.layout')
@section('content')

<style>
    .invalid-feedback {
        display: inline;
    }
</style>

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4 subheader-solid " id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Festival Faqs</h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('festival-faqs', $festival->id) }}" class="text-muted"> Festival Faqs</a>
                        </li>
                    </ul>
                </div>
            </div>
            @include('admin.elements.quick_links')
        </div>
    </div>

    <div class="d-flex flex-column-fluid">
        <div class="container">

            {{-- Display Success Message --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('festival-faqs', $festival->id) }}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-1"></div>
                            <div class="col-xl-10">

                                <h3 class="mb-10 font-weight-bold text-dark">Add New FAQ</h3>

                                <div class="col-md-12" id="faq_listing">
                                    <div id="faq_row">
                                        <div class="form-group mb-3">
                                            <label for="question">Question <span class="text-danger">*</span></label>
                                            <input type="text" name="question[]" placeholder="Enter Question" class="form-control" required>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="answer">Answer <span class="text-danger">*</span></label>
                                            <textarea name="answer[]" placeholder="Enter Answer" class="form-control" rows="3" required></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label><br>
                                        <button type="button" class="btn btn-success" id="add_more"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between border-top mt-5 pt-10">
                    <div>
                        <button type="submit" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                            Submit
                        </button>
                    </div>
                </div>
            </form>

            <hr>

            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <th>Sr No</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Status</th>
                        <th>Action</th>
                    </thead>

                    <tbody>
                        @if(!empty($festivalFqs))
                            @foreach($festivalFqs as $index => $festivalFaq)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $festivalFaq->question ?? '' }}</td>
                                    <td>{{ $festivalFaq->answer ?? '' }}</td>
                                    <td>{{ $festivalFaq->status == 1 ? 'Active' : 'Inactive' }}</td>
                                    <td>
                                       
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $(document).on("click", "#add_more", function () {
            var row = $("#faq_row").clone();
            row.find("#add_more")
                .removeAttr("id")
                .removeClass("btn-success")
                .addClass("btn-danger delete_row")
                .html('<i class="fa fa-trash"></i>');
            $("#faq_listing").append(row);
        });

        $(document).on("click", ".delete_row", function () {
            $(this).closest("#faq_row").remove();
        });
    });
</script>

@stop
